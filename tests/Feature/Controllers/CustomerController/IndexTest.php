<?php

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
   $this->seeCustomersPermission = Permission::create(['name' => 'see customers']);
});

it('requires authentication', function () {
    get(route('customers.index'))->assertRedirect(route('login'));
});

it('requires see customers permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->get(route('customers.index'))->assertForbidden();
});

it('should return the correct component', function () {
    $user = User::factory()->create()->givePermissionTo($this->seeCustomersPermission);

    actingAs($user)->get(route('customers.index'))->assertComponent('Customers/Index');
});

it('passes customers to the view', function () {
    $user = User::factory()->create()->givePermissionTo($this->seeCustomersPermission);

    $customers = Customer::factory(5)->create();

    actingAs($user)->get(route('customers.index'))->assertHasResource('customers', CustomerResource::collection($customers));
});