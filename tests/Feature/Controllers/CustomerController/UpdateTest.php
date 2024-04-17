<?php

use App\Models\Customer;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->editCustomersPermission = Permission::create(['name' => 'edit customers']);

    $this->admUser = User::factory()->create();
    $this->admUser->givePermissionTo($this->editCustomersPermission);

    $this->validData = [
        'name' => 'Silvia Carla',
        'cpf' => '23662435020',
        'birthdate' => '2012-07-30',
        'phone' => '(11) 97654-8976',
        'email' => 'silvia@test.com'
    ];
});

it('requires authentication', function () {
    $customer = Customer::factory()->create();

    put(route('customers.update', $customer->id))->assertRedirect(route('login'));
});

it('requires edit customers permission', function () {
    $badUser = User::factory()->create();
    $customer = Customer::factory()->create();

    actingAs($badUser)->put(route('customers.update', $customer->id))->assertForbidden();
});

it('can update a customer', function () {
    $customer = Customer::factory()->create($this->validData);

    $newName = 'Daniel Lopes';

    actingAs($this->admUser)->put(route('customers.update', $customer->id), [...$this->validData, 'name' => $newName]);

    assertDatabaseHas(Customer::class, [
        'id' => $customer->id,
        'name' => $newName
    ]);
});