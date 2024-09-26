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
        $hasRecurrence = $item['hasRecurrence'];
        $service = Service::find($item['service_id']);
        $startDate = Carbon::parse($item['start_date'])->setTimezone('America/Sao_Paulo');
        $endDate = $startDate->copy()->addMinutes($service->duration);

        if (!$hasRecurrence) {
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

        // Definir o loop com base no número de semanas até o limite da recorrência
        if ($loopCount < 2) {
            $loopCount = self::getWeeksUntilLimit($item['start_date']);
        }

        for ($i = 0; $i < $loopCount; $i++) {
            $startDate = Carbon::parse($item['start_date'])->addWeeks($i)->setTimezone('America/Sao_Paulo');
            $endDate = $startDate->copy()->addMinutes($service->duration);
            $itemSchedule = $arrSchedule[$i] ?? null;

            self::checkValidScheduleDate($startDate, $endDate, $service->id, $itemSchedule);
        }
    }

    /**
     * Verifica quantas semanas até o limite de dois meses à frente.
     */
    private static function getWeeksUntilLimit(string $startDate): int {
        $start = Carbon::parse($startDate);
        $limitDate = Carbon::now()->addMonths(2)->endOfWeek();
        return $start->diffInWeeks($limitDate);
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
            ->exists();

        if ($conflictingSchedules) {
            throw new \Exception('O agendamento conflita com outro agendamento existente', 600);
        }
    }

    /**
     * @throws \Exception
     */
    public static function generateFutureSchedulesRecurrence(Schedule|null $schedule = null, int|null $mainRecurrenceScheduleId = null, string|null $recurrenceString = null): void {
        $currentDate = Carbon::now();
        $limitDate = $currentDate->copy()->startOfMonth()->addMonths(2)->endOfWeek();

        // Buscar agendamentos com recorrência
        $schedules = $schedule ? collect([$schedule]) : self::getActiveRecurringSchedules();

        foreach ($schedules as $scheduleGroup) {
            $lastScheduleDate = self::getLastScheduleDate($scheduleGroup);
            $nextScheduleIndex = 1;
            $recurrenceId = $recurrenceString ?? $scheduleGroup->recurrence_id;

            self::generateSchedulesUntilLimit($scheduleGroup, $lastScheduleDate, $limitDate, $mainRecurrenceScheduleId, $nextScheduleIndex, $recurrenceId);
        }
    }

    private static function getActiveRecurringSchedules(): \Illuminate\Support\Collection {
        return Schedule::where('active', true)
            ->whereNotNull('recurrence_id')
            ->orderBy('end_date', 'desc')
            ->get()
            ->unique('recurrence_id');
    }

    private static function getLastScheduleDate($scheduleGroup) {
        return $scheduleGroup->max('end_date');
    }

    /**
     * @throws \Exception
     */
    private static function generateSchedulesUntilLimit($scheduleGroup, $lastScheduleDate, $limitDate, $mainRecurrenceScheduleId, $index, string|null $recurrenceString = null): void {
        $nextScheduleDate = Carbon::createFromDate($scheduleGroup->start_date)->copy()->addWeek();

        while ($nextScheduleDate->lessThanOrEqualTo($limitDate)) {
            self::createSchedule($scheduleGroup, true, $index, $mainRecurrenceScheduleId, $recurrenceString);
            $nextScheduleDate->addWeek();
            $index++;
        }
    }

    /**
     * @throws \Exception
     */
    public static function createSchedule(mixed $item, bool $fromRecurrenceFunc = false, int|null $index = null, int|null $mainRecurrenceScheduleId = null, string|null $recurrenceString = null): void {
        $customer = Customer::find($item['customer_id']);
        $service = Service::find($item['service_id']);

        if ($fromRecurrenceFunc) {
            $startDate = Carbon::parse($item['start_date'])->addWeeks($index);
            $endDate = Carbon::parse($item['end_date'])->addWeeks($index);
        } else {
            $startDate = Carbon::parse($item['start_date']);
            $endDate = $startDate->copy()->addMinutes($service->duration);
            self::verifySchedulesConflict($item);
        }

        if (!$mainRecurrenceScheduleId && !$fromRecurrenceFunc && $item['hasRecurrence']) {
            $recurrenceString = "PLACEHOLDER_" . uniqid();
        }

        $recurrenceEvent = self::createEvent($customer->name, $startDate, $endDate);
        $recurrenceSchedule = self::createScheduleRecord($item['customer_id'], $item['service_id'], $startDate, $endDate, $recurrenceString);

        if (!$mainRecurrenceScheduleId && !$fromRecurrenceFunc && $item['hasRecurrence']) {
            $mainRecurrenceScheduleId = $recurrenceSchedule->id;
        }

        CreateEvent::dispatchAfterResponse($recurrenceEvent, $recurrenceSchedule, $mainRecurrenceScheduleId);

        if (!$fromRecurrenceFunc && $item['hasRecurrence']) {
            self::generateFutureSchedulesRecurrence($recurrenceSchedule, $mainRecurrenceScheduleId, $recurrenceString);
        }
    }

    private static function createEvent(string $customerName, Carbon $startDate, Carbon $endDate): Event {
        $event = new Event();
        $event->name = "Agendamento " . $customerName;
        $event->startDateTime = $startDate;
        $event->endDateTime = $endDate;
        $event->setColorId(ScheduleStatusColor::PENDENTE->value);

        return $event;
    }

    private static function createScheduleRecord(int $customerId, int $serviceId, Carbon $startDate, Carbon $endDate, string|null $recurrenceString = null): Schedule {
        $schedule = new Schedule();
        $schedule->customer_id = $customerId;
        $schedule->service_id = $serviceId;
        $schedule->start_date = $startDate->setTimezone('America/Sao_Paulo');
        $schedule->end_date = $endDate->setTimezone('America/Sao_Paulo');
        $schedule->recurrence_id = $recurrenceString;
        $schedule->status = ScheduleStatus::PENDENTE;
        $schedule->save();

        return $schedule;
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

        if (isset($filterOptions['scheduleDate'])) {
            $scheduleQuery->whereDate('start_date', Carbon::createFromDate($filterOptions['scheduleDate']));
        }
        if (isset($filterOptions['month'])) {
            $scheduleQuery->whereMonth('start_date', $filterOptions['month']);
        }
        if (isset($filterOptions['year'])) {
            $scheduleQuery->whereYear('start_date', $filterOptions['year']);
        }
        if (isset($filterOptions['customerId'])) {
            $scheduleQuery->where('customer_id', $filterOptions['customerId']);
        }
        if (isset($filterOptions['serviceId'])) {
            $scheduleQuery->where('service_id', $filterOptions['serviceId']);
        }
        if (isset($filterOptions['status'])) {
            $scheduleQuery->where('status', $filterOptions['status']);
        }

        $scheduleQuery->where(['active' => true]);

        return ScheduleResource::collection($scheduleQuery->orderBy('start_date', 'asc')->limit(50)->get());
    }
}