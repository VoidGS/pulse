<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {
   get(route('users.create'))->assertRedirect(route('login'));
});

it('requires an administrator user', function () {
    $badUser = User::factory()->create([
        'is_administrator' => false,
    ]);

    actingAs($badUser)->get(route('users.create'))->assertForbidden();
});

it('return the correct component', function () {
    actingAs(User::factory()->create(['is_administrator' => true]))
        ->get(route('users.create'))
        ->assertComponent('Auth/Register');
});