<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->createUsersPermission = Permission::create(['name' => 'create users']);
    $this->adminRole = Role::create(['name' => 'admin']);

    $this->validData = [
        'name' => 'Void',
        'email' => 'void@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => $this->adminRole->id,
    ];
});

it('requires authentication', function () {
    post(route('users.store'))->assertRedirect(route('login'));
});

it('requires create users permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->post(route('users.store'))->assertForbidden();
});

it('requires valid data', function ($badData, $errors) {
    $user = User::factory()->create()->givePermissionTo($this->createUsersPermission);

    $duplicatedEmailUser = User::factory()->create(['email' => 'duplicated@email.com']);

    actingAs($user)
        ->post(route('users.store', [...$this->validData, ...$badData]))
        ->assertInvalid($errors);
})->with([
    [['name' => null], 'name'],
    [['name' => str_repeat('a', 256)], 'name'],
    [['email' => null], 'email'],
    [['email' => true], 'email'],
    [['email' => 1], 'email'],
    [['email' => 1.5], 'email'],
    [['email' => 'duplicated@email.com'], 'email'],
    [['email' => str_repeat('a', 256)], 'email'],
    [['password' => null], 'password'],
    [['password' => true], 'password'],
    [['password' => 1], 'password'],
    [['password' => 1.5], 'password'],
    [['password' => str_repeat('a', 7)], 'password'],
]);

it('register a user', function () {
    $user = User::factory()->create()->givePermissionTo($this->createUsersPermission);

    actingAs($user)
        ->post(route('users.store', $this->validData));

    assertDatabaseHas(User::class, ['email' => $this->validData['email']]);
});