<?php

namespace App\Jobs;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\GoogleCalendar\Event;

class CreateEvent implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Event $event;
    public Schedule $schedule;
    public int|null $mainRecurrenceScheduleId;

    /**
     * Create a new job instance.
     */
    public function __construct(Event $event, Schedule $schedule, int|null $mainRecurrenceScheduleId = null) {
        $this->event = $event;
        $this->schedule = $schedule;
        $this->mainRecurrenceScheduleId = $mainRecurrenceScheduleId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        \Log::info('Starting CreatEvent Job');

        $eventId = null;
        try {
            $createdEvent = $this->event->save();
            $eventId = $createdEvent->id;

            $this->schedule->event_id = $eventId;

            if ($this->mainRecurrenceScheduleId) {
                $this->schedule->recurrence_id =
                    ($this->schedule->id === $this->mainRecurrenceScheduleId) ? $eventId : Schedule::find($this->mainRecurrenceScheduleId)->event_id;
            }

            $this->schedule->save();

            \Log::info('CreatEvent Done -> eventId = ' . $eventId . ' / mainRecurrenceScheduleId = ' . $this->mainRecurrenceScheduleId . ' / schedule id = ' . $this->schedule->id . ' / schedule recurrence id = ' . $this->schedule->recurrence_id);
        } catch (\Exception $e) {
            if ($eventId) Event::find($eventId)->delete();
            // throw $e;
            \Log::info('CreatEvent Job Error - ' . $e->getMessage());
        }
    }
}
