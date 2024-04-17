<?php

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->editCustomersPermission = Permission::create(['name' => 'edit customers']);

    $this->admUser = User::factory()->create();
    $this->admUser->givePermissionTo($this->editCustomersPermission);
});

it('requires authentication', function () {
    $customer = Customer::factory()->create();

    get(route('customers.edit', $customer->id))->assertRedirect(route('login'));
});

it('requires edit customers permission', function () {
    $badUser = User::factory()->create();
    $customer = Customer::factory()->create();

    actingAs($badUser)->get(route('customers.edit', $customer->id))->assertForbidden();
});

it('returns the correct component', function () {
    $customer = Customer::factory()->create();

    actingAs($this->admUser)
        ->get(route('customers.edit', $customer->id))
        ->assertComponent('Customers/Edit');
});

it('passes the customer to the edit', function () {
    $customer = Customer::factory()->create();

    actingAs($this->admUser)
        ->get(route('customers.edit', $customer->id))
        ->assertHasResource('customer', CustomerResource::make($customer));
});