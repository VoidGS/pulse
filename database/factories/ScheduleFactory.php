<?php

namespace Database\Factories;

use App\Enums\ScheduleStatus;
use App\Models\Customer;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        $startDate = $this->faker->dateTimeBetween('today', 'today +22 hours');
        $service = Service::factory();
        $customer = Customer::factory();

        return [
            'start_date' => Carbon::createFromDate($startDate),
            'end_date' => Carbon::createFromDate($startDate)->addMinutes(60),
            'service_id' => $service,
            'customer_id' => $customer,
            'status' => ScheduleStatus::PENDENTE
        ];
    }
}
