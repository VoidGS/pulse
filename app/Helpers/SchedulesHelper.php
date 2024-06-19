<?php

namespace App\Helpers;

use App\Enums\ScheduleStatus;
use App\Enums\ScheduleStatusColor;
use App\Models\Customer;
use App\Models\Schedule;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class SchedulesHelper {
    public static function generateFutureSchedulesRecurrence(Schedule $schedule = null): void {
        $i = 1;
        while ($i <= 4) {
            $schedules = [];

            if (!$schedule) {
                $schedules = Schedule::distinct()->where(['active' => true])->whereNotNull('recurrence_id')->orderBy('end_date', 'desc');
            } else {
                $schedules[] = $schedule;
            }

            foreach ($schedules as $item) {
                $recurrenceEvent = new Event();
                $recurrenceEvent->name = "Agendamento " . Customer::find($item->customer_id)->name;
                $recurrenceEvent->startDateTime = Carbon::createFromDate($item->start_date)->addWeeks($i);
                $recurrenceEvent->endDateTime = Carbon::createFromDate($item->end_date)->addWeeks($i);
                $recurrenceEvent->setColorId(ScheduleStatusColor::PENDENTE->value);
                $newRecurrenceEvent = $recurrenceEvent->save();

                $recurrenceSchedule = new Schedule();
                $recurrenceSchedule->customer_id = $item->customer_id;
                $recurrenceSchedule->service_id = $item->service_id;
                $recurrenceSchedule->start_date = Carbon::createFromDate($item->start_date)->addWeeks($i)->setTimezone('America/Sao_Paulo');
                $recurrenceSchedule->end_date = Carbon::createFromDate($item->end_date)->addWeeks($i)->setTimezone('America/Sao_Paulo');
                $recurrenceSchedule->status = ScheduleStatus::PENDENTE;
                $recurrenceSchedule->event_id = $newRecurrenceEvent->id;
                $recurrenceSchedule->recurrence_id = $item->recurrence_id;
                $recurrenceSchedule->save();
            }

            $i++;
        }
    }
}