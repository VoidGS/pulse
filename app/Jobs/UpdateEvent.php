<?php

namespace App\Jobs;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\GoogleCalendar\Event;

class UpdateEvent implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Event|null $event = null;
    public Schedule|null $schedule = null;
    public Carbon $startDate;
    public Carbon $endDate;
    public bool $shouldRemoveRecurrence = false;

    /**
     * Create a new job instance.
     */
    public function __construct(Event|Schedule $item, Carbon $startDate, Carbon $endDate, bool $shouldRemoveRecurrence = false) {
        if ($item instanceof Event) {
            $this->event = $item;
        } else {
            $this->schedule = $item;
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->shouldRemoveRecurrence = $shouldRemoveRecurrence;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        \Log::info('Starting UpdateEvent Job');

        if ($this->schedule) {
            \Log::info('Searching Event - ' . $this->schedule->event_id);
            $searchEvent = Event::find($this->schedule->event_id);

            if ($searchEvent->status == 'cancelled') {
                \Log::info('Event not found, starting to create it');

                $newEvent = new Event();
                $this->event = $newEvent;
            } else {
                \Log::info('Event found');
                $this->event = $searchEvent;
            }

            $statusName = strtoupper($this->schedule->status->value);
            $eventColorId = constant("\App\Enums\ScheduleStatusColor::{$statusName}")->value;

            $this->event->name = "Agendamento " . $this->schedule->customer->name;
            $this->event->startDateTime = $this->startDate;
            $this->event->endDateTime = $this->endDate;
            $this->event->setColorId($eventColorId);
        }

        $saveEvent = $this->event->save();
        \Log::info('Event saved -> id = ' . $saveEvent->id);

        if ($this->schedule) {
            $recurrenceId = null;

            if ($this->schedule->recurrence_id && !$this->shouldRemoveRecurrence) {
                $mainRecurrence = $this->schedule->recurrence_id === $this->schedule->event_id;
                $recurrenceId = $mainRecurrence ? $saveEvent->id : $this->schedule->recurrence_id;

                if ($mainRecurrence) {
                    \Log::info('Updating childRecurrences');
                    $childCount = $this->updateChildRecurrences($this->schedule, $recurrenceId);
                    \Log::info('Updated ' . $childCount . ' child recurrences to recurrence_id = ' . $recurrenceId);
                }
            }

            if ($this->shouldRemoveRecurrence) {
                $this->removeRecurrence($this->schedule);
            }

            $this->schedule->update(['event_id' => $saveEvent->id, 'recurrence_id' => $recurrenceId]);
            \Log::info('Schedule updated -> event_id = ' . $saveEvent->id . ' / recurrence_id = ' . $recurrenceId);
        }
    }

    public function updateChildRecurrences(Schedule $schedule, string $recurrenceId): int {
        $childSchedules = Schedule::where(['recurrence_id' => $schedule->recurrence_id, 'active' => true])
            ->where('event_id', '<>', $schedule->event_id)
            ->get();

        foreach ($childSchedules as $item) {
            $item->update(['recurrence_id' => $recurrenceId]);
        }

        return count($childSchedules);
    }

    public function removeRecurrence(Schedule $schedule): void {
        $childSchedules = Schedule::where(['recurrence_id' => $schedule->recurrence_id])
            ->where('event_id', '<>', $schedule->event_id)
            ->get();

        foreach ($childSchedules as $item) {
            $event = Event::find($item->event_id);
            $event->delete();
            $item->delete();
        }
    }
}
