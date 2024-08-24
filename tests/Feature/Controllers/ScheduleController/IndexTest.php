<?php

use App\Helpers\SchedulesHelper;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->seeSchedulesPermission = Permission::create(['name' => 'see schedules']);
});

it('requires authentication', function () {
    get(route('schedules.index'))->assertRedirect(route('login'));
});

it('requires see schedules permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->get(route('schedules.index'))->assertForbidden();
});

it('should return the correct component', function () {
    $user = User::factory()->create()->givePermissionTo($this->seeSchedulesPermission);

    actingAs($user)->get(route('schedules.index'))->assertComponent('Schedules/Index');
});

it('passes schedules to the view (kinda bugged rn)', function () {
    $user = User::factory()->create()->givePermissionTo($this->seeSchedulesPermission);

    Schedule::factory(1)->create();
    $schedules = Schedule::with(['customer', 'service'])->get();

    actingAs($user)->get(route('schedules.index'))->assertHasResource('schedules', ScheduleResource::collection($schedules));
});