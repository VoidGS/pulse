<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;

beforeEach(function () {
    $this->deleteUsersPermission = Permission::create(['name' => 'delete users']);
});

it('requires authentication', function () {
    delete(route('users.destroy', User::factory()->create()))->assertRedirect(route('login'));
});

it('requires delete users permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->delete(route('users.destroy', User::factory()->create()))->assertForbidden();
});

it('can delete a user', function () {
    $targetUser = User::factory()->create();
    $user = User::factory()->create()->givePermissionTo($this->deleteUsersPermission);

    actingAs($user)->delete(route('users.destroy', $targetUser));

    assertDatabaseHas(User::class, [
        'id' => $targetUser->id,
        'active' => false
    ]);
});