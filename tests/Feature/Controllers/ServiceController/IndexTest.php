<?php

use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\withoutExceptionHandling;

beforeEach(function () {
   $this->seeServicesPermission = Permission::create(['name' => 'see services']);
});

it('requires authentication', function () {
    get(route('services.index'))->assertRedirect(route('login'));
});

it('requires see services permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->get(route('services.index'))->assertForbidden();
});

it('should return the correct component', function () {
    $user = User::factory()->create()->givePermissionTo($this->seeServicesPermission);

    actingAs($user)->get(route('services.index'))->assertComponent('Services/Index');
});

it('passes services to the view', function () {
    $user = User::factory()->create()->givePermissionTo($this->seeServicesPermission);

    Service::factory(5)->create();
    $services = Service::with(['user', 'team'])->get();

    actingAs($user)->get(route('services.index'))->assertHasResource('services', ServiceResource::collection($services));
});