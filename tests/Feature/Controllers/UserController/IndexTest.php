<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use function Pest\Laravel\get;
use function Pest\Laravel\withoutExceptionHandling;

it('requires authentication', function () {
    get(route('users.index'))->assertRedirect(route('login'));
});

it('should return the correct component', function () {
    $this->actingAs($user = User::factory()->create());
    get(route('users.index'))->assertComponent('Users/Index');
});

it('passes users to the view', function () {
    $users = User::factory(5)->create();

    $this->actingAs($user = User::first());
    get(route('users.index'))->assertHasPaginatedResource('users', UserResource::collection($users));
});