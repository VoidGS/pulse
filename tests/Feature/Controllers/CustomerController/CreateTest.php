<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->createCustomersPermission = Permission::create(['name' => 'create customers']);
});

it('requires authentication', function () {
    get(route('customers.create'))->assertRedirect(route('login'));
});

it('requires create customers permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->get(route('customers.create'))->assertForbidden();
});

it('return the correct component', function () {
    $user = User::factory()->create()->givePermissionTo($this->createCustomersPermission);

    actingAs($user)->get(route('customers.create'))->assertComponent('Customers/Create');
});