<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
   $this->seeUsersPermission = Permission::create(['name' => 'see users']);
});

it('requires authentication', function () {
    get(route('users.index'))->assertRedirect(route('login'));
});

it('requires see users permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->get(route('users.index'))->assertForbidden();
});

it('should return the correct component', function () {
    $user = User::factory()->create()->givePermissionTo($this->seeUsersPermission);

    actingAs($user);
    get(route('users.index'))->assertComponent('Users/Index');
});

it('passes users to the view', function () {
    User::factory(5)->create();
    $users = User::with(['teams', 'ownedTeams'])->get();
    $user = User::first()->givePermissionTo($this->seeUsersPermission);

    actingAs($user);
    get(route('users.index'))->assertHasResource('users', UserResource::collection($users));
});