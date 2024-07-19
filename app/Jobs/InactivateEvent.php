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

class InactivateEvent implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $eventId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $eventId) {
        $this->eventId = $eventId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        \Log::info('Starting InactivateEvent Job');

        $event = Event::find($this->eventId);

        if ($event->status !== 'cancelled') {
            \Log::info('Inactivating event [' . $this->eventId . ']...');
            $event->delete();
            \Log::info('Event [' . $this->eventId . '] was successfully inactivated!');
        } else {
            \Log::info('Event [' . $this->eventId . '] is already cancelled');
        }

        \Log::info('Done InactivateEvent Job');
    }
}
