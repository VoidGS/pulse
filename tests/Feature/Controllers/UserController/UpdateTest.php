<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->updateUsersPermission = Permission::create(['name' => 'edit users']);
    $this->createUsersPermission = Permission::create(['name' => 'create users']);
    $this->adminRole = Role::create(['name' => 'admin']);

    $this->admUser = User::factory()->create();
    $this->admUser->givePermissionTo($this->updateUsersPermission);

    $this->validData = [
        'name' => 'Camila',
        'email' => 'camila@test.com',
        'role' => $this->adminRole->name
    ];
});

it('requires authentication', function () {
    put(route('users.update', User::factory()->create()))->assertRedirect(route('login'));
});

it('requires edit users permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->put(route('users.update', User::factory()->create()))->assertForbidden();
});

it('can update a user', function () {
    $user = User::factory()->create([
        'name' => $this->validData['name'],
        'email' => $this->validData['email'],
    ]);

    $newEmail = 'camila@new.com';

    actingAs($this->admUser)->put(route('users.update', $user), [...$this->validData, 'email' => $newEmail]);

    assertDatabaseHas(User::class, [
        'id' => $user->id,
        'email' => $newEmail
    ]);
});