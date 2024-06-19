<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;

class CreateSchedulesRecurrence extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-schedules-recurrence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates the future schedules recurrence if schedule is active.';

    /**
     * Execute the console command.
     */
    public function handle(): void {
        $this->info("app:create-schedules-recurrence running at " . now());

        Service::factory()->create();
    }
}
