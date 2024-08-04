<?php

namespace App\Helpers;

use App\Enums\ScheduleStatus;
use App\Enums\ScheduleStatusColor;
use App\Http\Resources\ScheduleResource;
use App\Jobs\CreateEvent;
use App\Jobs\InactivateEvent;
use App\Jobs\UpdateEvent;
use App\Models\Customer;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class SchedulesHelper {
    /**
     * @throws \Exception
     */
    public static function verifySchedulesConflict(mixed $item, Schedule|null $schedule = null): void {
        $hasRecurrence = ($item['hasRecurrence']);
        $service = Service::find($item['service_id']);

        if (!$hasRecurrence) {
            $startDate = Carbon::createFromDate($item['start_date'])->setTimezone('America/Sao_Paulo');
            $endDate = Carbon::createFromDate($item['start_date'])->addMinutes($service->duration)->setTimezone('America/Sao_Paulo');
            self::checkValidScheduleDate($startDate, $endDate, $service->id, $schedule);
            return;
        }

        $arrSchedule = [];
        $loopCount = 0;
        if ($schedule) {
            $arrSchedule = Schedule::where(['recurrence_id' => $schedule->recurrence_id, 'active' => true])
                ->whereNotNull('recurrence_id')
                ->orderBy('start_date')
                ->get();

            $loopCount = count($arrSchedule);
        }

        if ($loopCount < 2) {
            $loopCount = 5;
        }

        $i = 0;
        while ($i < $loopCount) {
            $startDate = Carbon::createFromDate($item['start_date'])->addWeeks($i)->setTimezone('America/Sao_Paulo');
            $endDate = Carbon::createFromDate($item['start_date'])->addMinutes($service->duration)->addWeeks($i)->setTimezone('America/Sao_Paulo');

            $itemSchedule = isset($arrSchedule[$i]) ? $arrSchedule[$i] : null;
            self::checkValidScheduleDate($startDate, $endDate, $service->id, $itemSchedule);

            $i++;
        }
        return;
    }

    /**
     * @throws \Exception
     */
    public static function checkValidScheduleDate(Carbon $startDate, Carbon $endDate, int $serviceId, Schedule|null $schedule = null): void {
        $scheduleId = $schedule?->id;

        $conflictingSchedules = Schedule::where(['service_id' => $serviceId, 'active' => true])
            ->where('id', '!=', $scheduleId)
            ->where(function ($query) use ($schedule) {
                if ($schedule) {
                    $query->where('recurrence_id', '!=', $schedule->recurrence_id)
                        ->orWhereNull('recurrence_id');
                }
            })
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate]);
                })
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<', $startDate)
                        ->where('end_date', '>', $endDate);
                });
            })
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('end_date', '!=', $startDate)
                    ->where('start_date', '!=', $endDate);
            })
            ->exists();

        if ($conflictingSchedules) {
            throw new \Exception('O agendamento conflita com outro agendamento existente', 600);
        }
    }

    /**
     * @throws \Exception
     */
    public static function generateFutureSchedulesRecurrence(Schedule|null $schedule = null, int|null $mainRecurrenceScheduleId = null): void {
        $schedules = [];
        if (!$schedule) {
            $schedules = Schedule::distinct()
                ->where(['active' => true])
                ->whereNotNull('recurrence_id')
                ->orderBy('end_date', 'desc')
                ->get()
                ->groupBy('recurrence_id');
        } else {
            $schedules[] = $schedule;
        }

        $i = 1;
        while ($i <= 4) {
            foreach ($schedules as $item) {
                self::createSchedule($item, true, $i, $mainRecurrenceScheduleId);
            }

            $i++;
        }
    }

    /**
     * @throws \Exception
     */
    public static function createSchedule(mixed $item, bool $fromRecurrenceFunc = false, int|null $index = null, int|null $mainRecurrenceScheduleId = null): void {
        $customer = Customer::find($item['customer_id']);
        $service = Service::find($item['service_id']);
        $mainRecurrenceId = $mainRecurrenceScheduleId;

        if ($fromRecurrenceFunc) {
            $startDate = Carbon::createFromDate($item['start_date'])->addWeeks($index);
            $endDate = Carbon::createFromDate($item['end_date'])->addWeeks($index);
            $recurrenceId = $item['recurrence_id'];
        } else {
            $startDate = Carbon::createFromDate($item['start_date']);
            $endDate = Carbon::createFromDate($item['start_date'])->addMinutes($service->duration);
            self::verifySchedulesConflict($item);
        }

        $recurrenceEvent = new Event();
        $recurrenceEvent->name = "Agendamento " . $customer->name;
        $recurrenceEvent->startDateTime = $startDate;
        $recurrenceEvent->endDateTime = $endDate;
        $recurrenceEvent->setColorId(ScheduleStatusColor::PENDENTE->value);

        $recurrenceSchedule = new Schedule();
        $recurrenceSchedule->customer_id = $item['customer_id'];
        $recurrenceSchedule->service_id = $item['service_id'];
        $recurrenceSchedule->start_date = $startDate->setTimezone('America/Sao_Paulo');
        $recurrenceSchedule->end_date = $endDate->setTimezone('America/Sao_Paulo');
        $recurrenceSchedule->status = ScheduleStatus::PENDENTE;
        $recurrenceSchedule->save();

        if (!$mainRecurrenceId && !$fromRecurrenceFunc && $item['hasRecurrence']) {
            $mainRecurrenceId = $recurrenceSchedule->id;
        }

        CreateEvent::dispatchAfterResponse($recurrenceEvent, $recurrenceSchedule, $mainRecurrenceId);

        if (!$fromRecurrenceFunc && $item['hasRecurrence']) {
            self::generateFutureSchedulesRecurrence($recurrenceSchedule, $mainRecurrenceId);
        }
    }

    /**
     * @throws \Exception
     */
    public static function updateSchedule(Schedule $schedule, mixed $updateData, int|null $submitType = null, int $index = null): \Illuminate\Http\RedirectResponse|bool {
        if (!$index) {
            $item = $updateData;
            $item['service_id'] = $item['serviceId'];
            $item['start_date'] = $item['scheduleDate'];
            self::verifySchedulesConflict($item, $schedule);
        }

        if ($submitType === 1 && $updateData['hasRecurrence']) {
            self::updateAllSchedulesFromThisRecurrence($schedule, $updateData);
        }

        $service = Service::find($updateData['serviceId']);
        $startDate = Carbon::createFromDate($updateData['scheduleDate']);
        $endDate = Carbon::createFromDate($updateData['scheduleDate'])->addMinutes($service->duration);
        $shouldRemoveRecurrence = !$updateData['hasRecurrence'] && $schedule->recurrence_id;

        if ($index) {
            $startDate = $index > 0 ? Carbon::createFromDate($updateData['scheduleDate'])->addWeeks($index) :
                Carbon::createFromDate($updateData['scheduleDate'])->subWeeks($index * -1);
            $endDate = $index > 0 ? Carbon::createFromDate($updateData['scheduleDate'])->addWeeks($index)->addMinutes($service->duration) :
                Carbon::createFromDate($updateData['scheduleDate'])->subWeeks($index * -1);
        }

        $updateSchedule = [
            'customer_id' => $updateData['customerId'],
            'service_id' => $updateData['serviceId'],
            'start_date' => $startDate->setTimezone('America/Sao_Paulo'),
            'end_date' => $endDate->setTimezone('America/Sao_Paulo'),
            'status' => ScheduleStatus::tryFrom($updateData['status'])->value,
        ];
        $schedule->update($updateSchedule);

        if ($shouldRemoveRecurrence) {
            self::removeRecurrence($schedule);
            $schedule->update(['recurrence_id' => null]);
        }

        UpdateEvent::dispatchAfterResponse($schedule, $startDate, $endDate, $shouldRemoveRecurrence);

        if ($updateData['hasRecurrence'] && !$schedule->recurrence_id) {
            self::generateFutureSchedulesRecurrence($schedule, $schedule->id);
            $schedule->update(['recurrence_id' => $schedule->event_id]);
        }

        if ($submitType === 2) {
            self::updateNextSchedules($schedule, $updateData);
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public static function updateNextSchedules(Schedule $schedule, mixed $updateData): void {
        $nextSchedules = Schedule::where(['recurrence_id' => $schedule->recurrence_id, 'active' => true])
            ->whereDate('start_date', '>=', Carbon::createFromDate($updateData['scheduleDate']))
            ->where('event_id', '<>', $schedule->event_id)
            ->orderBy('start_date')
            ->get();

        foreach ($nextSchedules as $key => $item) {
            self::updateSchedule($item, $updateData, null, $key + 1);
        }
    }

    /**
     * @throws \Exception
     */
    public static function updateAllSchedulesFromThisRecurrence(Schedule $schedule, mixed $updateData): void {
        $nextSchedules = Schedule::where(['recurrence_id' => $schedule->recurrence_id, 'active' => true])
            ->whereDate('start_date', '>=', Carbon::createFromDate($schedule->start_date))
            ->where('event_id', '<>', $schedule->event_id)
            ->orderBy('start_date')
            ->get();

        $previousSchedules = Schedule::where(['recurrence_id' => $schedule->recurrence_id, 'active' => true])
            ->whereDate('start_date', '<', Carbon::createFromDate($schedule->start_date))
            ->where('event_id', '<>', $schedule->event_id)
            ->orderBy('start_date', 'desc')
            ->get();

        foreach ($nextSchedules as $nextKey => $nextItem) {
            self::updateSchedule($nextItem, $updateData, null, $nextKey + 1);
        }

        foreach ($previousSchedules as $previousKey => $previousItem) {
            self::updateSchedule($previousItem, $updateData, null, ($previousKey + 1) * -1);
        }
    }

    public static function removeRecurrence(Schedule $schedule): void {
        $childSchedules = Schedule::where(['recurrence_id' => $schedule->recurrence_id, 'active' => true])
            ->where('event_id', '<>', $schedule->event_id)
            ->get();

        foreach ($childSchedules as $item) {
            $item->update(['active' => false]);
        }
    }

    public static function inactivateSchedule(Schedule $schedule): void {
        $schedule->update(['active' => false]);
        InactivateEvent::dispatchAfterResponse($schedule->event_id);
    }

    public static function filterSchedules(mixed $filterOptions): \Illuminate\Http\Resources\Json\AnonymousResourceCollection {
        $scheduleQuery = Schedule::with(['customer', 'service']);

        if (isset($filterOptions['scheduleDate'])) $scheduleQuery->whereDay('start_date', Carbon::createFromDate($filterOptions['scheduleDate'])->day);
        if (isset($filterOptions['customerId'])) $scheduleQuery->where('customer_id', $filterOptions['customerId']);
        if (isset($filterOptions['serviceId'])) $scheduleQuery->where('service_id', $filterOptions['serviceId']);
        if (isset($filterOptions['status'])) $scheduleQuery->where('status', $filterOptions['status']);

        $scheduleQuery->where(['active' => true]);

        return ScheduleResource::collection($scheduleQuery->orderBy('start_date', 'asc')->limit(50)->get());
    }
}