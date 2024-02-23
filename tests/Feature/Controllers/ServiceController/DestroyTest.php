<?php

use App\Models\Service;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;

beforeEach(function () {
    $this->deleteServicesPermission = Permission::create(['name' => 'delete services']);
});

it('requires authentication', function () {
    delete(route('services.destroy', Service::factory()->create()))->assertRedirect(route('login'));
});

it('requires delete services permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->delete(route('services.destroy', Service::factory()->create()))->assertForbidden();
});

it('can delete a service', function () {
    $service = Service::factory()->create();
    $user = User::factory()->create()->givePermissionTo($this->deleteServicesPermission);

    actingAs($user)->delete(route('services.destroy', $service));

    assertDatabaseHas(Service::class, [
        'id' => $service->id,
        'active' => false
    ]);
});