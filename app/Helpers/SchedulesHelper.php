<?php

namespace App\Helpers;

use App\Enums\ScheduleStatus;
use App\Enums\ScheduleStatusColor;
use App\Jobs\CreateEvent;
use App\Jobs\UpdateEvent;
use App\Models\Customer;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class SchedulesHelper {
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

    public static function updateSchedule(Schedule $schedule, mixed $updateData, int|null $submitType = null, int $index = null): \Illuminate\Http\RedirectResponse|bool {
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
}