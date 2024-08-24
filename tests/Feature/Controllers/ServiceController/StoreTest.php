<?php

use App\Models\Service;
use App\Models\Team;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->createServicesPermission = Permission::create(['name' => 'create services']);

    $this->validData = [
        'name' => 'Serviço de teste',
        'price' => 50,
        'duration' => 60,
        'team' => Team::factory()->create()->id,
        'user' => User::factory()->create()->id,
    ];
});

it('requires authentication', function () {
    post(route('services.store'))->assertRedirect(route('login'));
});

it('requires create services permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->post(route('services.store'))->assertForbidden();
});

it('requires valid data', function ($badData, $errors) {
    $user = User::factory()->create()->givePermissionTo($this->createServicesPermission);

    actingAs($user)
        ->post(route('services.store', [...$this->validData, ...$badData]))
        ->assertInvalid($errors);
})->with([
    [['name' => null], 'name'],
    [['name' => 'a'], 'name'],
    [['name' => str_repeat('a', 256)], 'name'],
    [['price' => null], 'price'],
    [['price' => 'abc'], 'price'],
    [['price' => '100000'], 'price'],
    [['price' => 100000], 'price'],
    [['team' => null], 'team'],
    [['team' => str_repeat('a', 7)], 'team'],
    [['user' => null], 'user'],
    [['user' => str_repeat('a', 7)], 'user'],
]);

it('store a service', function () {
    $user = User::factory()->create()->givePermissionTo($this->createServicesPermission);

    actingAs($user)->post(route('services.store', $this->validData));

    assertDatabaseHas(Service::class, [
        'name' => $this->validData['name'],
        'price' => $this->validData['price'],
        'duration' => $this->validData['duration'],
        'team_id' => $this->validData['team'],
        'user_id' => $this->validData['user'],
    ]);
});

