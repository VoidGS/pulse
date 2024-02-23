<?php

use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->createServicesPermission = Permission::create(['name' => 'create services']);
    $this->editServicesPermission = Permission::create(['name' => 'edit services']);

    $this->admUser = User::factory()->create();
    $this->admUser->givePermissionTo($this->createServicesPermission);
    $this->admUser->givePermissionTo($this->editServicesPermission);
});

it('requires authentication', function () {
    $service = Service::factory()->create();

    get(route('services.edit', $service))->assertRedirect(route('login'));
});

it('requires edit services permission', function () {
    $badUser = User::factory()->create();
    $service = Service::factory()->create();

    actingAs($badUser)->get(route('services.edit', $service))->assertForbidden();
});

it('return the correct component', function () {
    $user = User::factory()->create()->givePermissionTo($this->editServicesPermission);
    $service = Service::factory()->create();

    actingAs($user)
        ->get(route('services.edit', $service))
        ->assertComponent('Services/Edit');
});

it('passes service to the edit', function () {
    $user = User::factory()->create()->givePermissionTo($this->editServicesPermission);

    $service = Service::factory()->create();
    $service->load('user');
    $service->load('team');

    actingAs($user)->get(route('services.edit', $service))->assertHasResource('service', ServiceResource::make($service));
});