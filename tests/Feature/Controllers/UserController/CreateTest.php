<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->createUsersPermission = $createUsers = Permission::create(['name' => 'create users']);
});

it('requires authentication', function () {
   get(route('users.create'))->assertRedirect(route('login'));
});

it('requires create users permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->get(route('users.create'))->assertForbidden();
});

it('return the correct component', function () {
    $user = User::factory()->create()->givePermissionTo($this->createUsersPermission);

    actingAs($user)
        ->get(route('users.create'))
        ->assertComponent('Users/Create');
});