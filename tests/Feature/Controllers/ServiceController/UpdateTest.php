<?php

use App\Models\Service;
use App\Models\Team;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->updateServicesPermission = Permission::create(['name' => 'edit services']);

    $this->admUser = User::factory()->create();
    $this->admUser->givePermissionTo($this->updateServicesPermission);

    $this->validData = [
        'name' => 'Serviço de teste',
        'price' => 150,
        'team' => Team::factory()->create()->id,
        'user' => $this->admUser->id,
    ];
});

it('requires authentication', function () {
    put(route('services.update', Service::factory()->create()))->assertRedirect(route('login'));
});

it('requires create services permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->put(route('services.update', Service::factory()->create()))->assertForbidden();
});

it('can update a service', function () {
    $service = Service::factory()->create([
        'name' => 'Serviço de testes',
        'price' => 450,
    ]);

    $newName = 'Seviço novo de teste';

    actingAs($this->admUser)->put(route('services.update', $service), [...$this->validData, 'name' => $newName]);

    assertDatabaseHas(Service::class, [
        'id' => $service->id,
        'name' => $newName,
    ]);
});