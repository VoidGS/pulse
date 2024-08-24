<?php

use App\Models\Customer;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutExceptionHandling;

beforeEach(function () {
    $this->createSchedulesPermission = Permission::create(['name' => 'create schedules']);

    $this->validCustomer = Customer::factory()->create();
    $this->validService = Service::factory()->create();

    $this->validData = [
        'scheduleDate' => Carbon::today('America/Sao_Paulo')->hour(18)->toISOString(),
        'customerId' => $this->validCustomer->id,
        'serviceId' => $this->validService->id,
        'hasRecurrence' => false
    ];

    $this->validRecurrenceData = [
        'scheduleDate' => Carbon::today('America/Sao_Paulo')->hour(16)->toISOString(),
        'customerId' => $this->validCustomer->id,
        'serviceId' => $this->validService->id,
        'hasRecurrence' => true
    ];
});

it('requires authentication', function () {
    post(route('schedules.store'))->assertRedirect(route('login'));
});

it('requires create schedules permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->post(route('schedules.store'))->assertForbidden();
});

it('requires valid data', function ($badData, $errors) {
    $user = User::factory()->create()->givePermissionTo($this->createSchedulesPermission);

    actingAs($user)
        ->post(route('schedules.store', [...$this->validData, ...$badData]))
        ->assertInvalid();
})->with([
    [['scheduleDate' => null], 'scheduleDate'],
    [['scheduleDate' => 'a'], 'scheduleDate'],
    [['customerId' => null], 'customerId'],
    [['customerId' => 'a'], 'customerId'],
    [['customerId' => 786], 'customerId'],
    [['serviceId' => null], 'serviceId'],
    [['serviceId' => 'a'], 'serviceId'],
    [['serviceId' => 786], 'serviceId'],
    [['hasRecurrence' => null], 'hasRecurrence'],
]);

it('store a schedule', function () {
    $user = User::factory()->create()->givePermissionTo($this->createSchedulesPermission);

    actingAs($user)->post(route('schedules.store', $this->validData));

    assertDatabaseHas(Schedule::class, [
        'start_date' => Carbon::createFromDate($this->validData['scheduleDate'])->setTimezone('America/Sao_Paulo')->toDateTimeString(),
        'end_date' => Carbon::createFromDate($this->validData['scheduleDate'])->addMinutes($this->validService->duration)->setTimezone('America/Sao_Paulo')->toDateTimeString(),
        'customer_id' => $this->validCustomer->id,
        'service_id' => $this->validService->id,
        'recurrence_id' => null
    ]);
});

it('store a recurrence schedule', function () {
    $user = User::factory()->create()->givePermissionTo($this->createSchedulesPermission);

    actingAs($user)->post(route('schedules.store', $this->validRecurrenceData));

    $scheduleSearch = [
        'start_date' => Carbon::createFromDate($this->validRecurrenceData['scheduleDate'])->setTimezone('America/Sao_Paulo')->toDateTimeString(),
        'end_date' => Carbon::createFromDate($this->validRecurrenceData['scheduleDate'])->addMinutes($this->validService->duration)->setTimezone('America/Sao_Paulo')->toDateTimeString(),
        'customer_id' => $this->validCustomer->id,
        'service_id' => $this->validService->id
    ];
    assertDatabaseHas(Schedule::class, $scheduleSearch);
    assertDatabaseCount(Schedule::class, 5);
});

it('dont let schedules coincide', function () {
    $user = User::factory()->create()->givePermissionTo($this->createSchedulesPermission);

    withoutExceptionHandling();

    actingAs($user)->post(route('schedules.store', $this->validData));
    actingAs($user)->post(route('schedules.store', $this->validData))->assertFlash('O agendamento conflita com outro agendamento existente');
});

