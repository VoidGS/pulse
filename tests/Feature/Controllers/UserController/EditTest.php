<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->createUsersPermission = Permission::create(['name' => 'create users']);
    $this->editUsersPermission = Permission::create(['name' => 'edit users']);
});

it('requires authentication', function () {
    get(route('users.edit', User::factory()->create()))->assertRedirect(route('login'));
});

it('requires edit users permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->get(route('users.edit', User::factory()->create()))->assertForbidden();
});

it('return the correct component', function () {
    $user = User::factory()->create()->givePermissionTo($this->editUsersPermission);
    $editUser = User::factory()->create();
    $editUser->load('teams');
    $editUser->load('ownedTeams');

    actingAs($user)->get(route('users.edit', $editUser))->assertComponent('Users/Edit');
});

it('passes user to the edit', function () {
    $user = User::factory()->create()->givePermissionTo($this->editUsersPermission);
    $editUser = User::factory()->create();
    $editUser->load('teams');
    $editUser->load('ownedTeams');

    actingAs($user)->get(route('users.edit', $editUser))->assertHasResource('user', UserResource::make($editUser));
});