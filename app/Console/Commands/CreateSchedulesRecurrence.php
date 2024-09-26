<?php

namespace App\Console\Commands;

use App\Helpers\SchedulesHelper;
use Illuminate\Console\Command;

class CreateSchedulesRecurrence extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:generate-future-recurrence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates future recurrence schedules until the limit date.';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle(): void {
        $this->info("schedule:generate-future-recurrence running at: " . now());
        SchedulesHelper::generateFutureSchedulesRecurrence();
        $this->info("Future recurrence schedules generated successfully.");
    }
}
