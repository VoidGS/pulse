<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->createServicesPermission = Permission::create(['name' => 'create services']);
});

it('requires authentication', function () {
   get(route('services.create'))->assertRedirect(route('login'));
});

it('requires create services permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->get(route('services.create'))->assertForbidden();
});

it('return the correct component', function () {
    $user = User::factory()->create()->givePermissionTo($this->createServicesPermission);

    actingAs($user)
        ->get(route('services.create'))
        ->assertComponent('Services/Create');
});