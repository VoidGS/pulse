<?php

use App\Models\Customer;
use App\Models\CustomerDiscount;
use App\Models\Guardian;
use App\Models\Service;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->createCustomersPermission = Permission::create(['name' => 'create customers']);

    $this->validMinimalData = [
        'name' => 'Pedro Henrique',
        'cpf' => '20119218054',
        'birthdate' => '2012-07-30',
        'phone' => '(99) 99999-9999',
        'email' => 'customer@test.com'
    ];

    $this->validFullData = [
        'name' => 'Camila Timbó',
        'birthdate' => '2012-07-30',
        'hasGuardians' => true,
        'guardians' => [
            0 => [
                'name' => 'Silvia Carla',
                'cpf' => '56697386088',
                'birthdate' => '2002-07-30',
                'phone' => '(99) 99999-9999',
                'email' => 'responsavel1@test.com'
            ]
        ],
        'hasDiscounts' => true,
        'discounts' => [
            0 => [
                'service' => Service::factory()->create()->id,
                'discount' => 50
            ]
        ]
    ];
});

it('requires authentication', function () {
    post(route('customers.store'))->assertRedirect(route('login'));
});

it('requires create customers permission', function () {
    $badUser = User::factory()->create();

    actingAs($badUser)->post(route('customers.store'))->assertForbidden();
});

it('requires valid data (full data)', function ($badData, $errors) {
    $user = User::factory()->create()->givePermissionTo($this->createCustomersPermission);

    actingAs($user)->post(route('customers.store', [...$this->validFullData, ...$badData]))->assertInvalid($errors);
})
    ->with([
    [['name' => null], 'name'],
    [['name' => 'a'], 'name'],
    [['name' => str_repeat('a', 256)], 'name'],
    [['birthdate' => null], 'birthdate'],
    [['birthdate' => fn () => new Date()], 'birthdate'],
    [['guardians' => [
        0 => [
            'name' => ''
        ]
    ]], 'guardians.0.name'],
    [['guardians' => [
        0 => [
            'name' => 'a'
        ]
    ]], 'guardians.0.name'],
    [['guardians' => [
        0 => [
            'name' => str_repeat('a', 256)
        ]
    ]], 'guardians.0.name'],
    [['guardians' => [
        0 => [
            'cpf' => ''
        ]
    ]], 'guardians.0.cpf'],
    [['guardians' => [
        0 => [
            'cpf' => '56697386087'
        ]
    ]], 'guardians.0.cpf'],
    [['guardians' => [
        0 => [
            'birthdate' => ''
        ]
    ]], 'guardians.0.birthdate'],
    [['guardians' => [
        0 => [
            'phone' => ''
        ]
    ]], 'guardians.0.phone'],
    [['guardians' => [
        0 => [
            'phone' => '123'
        ]
    ]], 'guardians.0.phone'],
    [['guardians' => [
        0 => [
            'email' => ''
        ]
    ]], 'guardians.0.email'],
    [['guardians' => [
        0 => [
            'email' => 'abc'
        ]
    ]], 'guardians.0.email'],
    [['discounts' => [
        0 => [
            'service' => '',
        ]
    ]], 'discounts.0.service'],
    [['discounts' => [
        0 => [
            'service' => 'wrong service',
        ]
    ]], 'discounts.0.service'],
    [['discounts' => [
        0 => [
            'discount' => ''
        ]
    ]], 'discounts.0.discount'],
    [['discounts' => [
        0 => [
            'discount' => 'a'
        ]
    ]], 'discounts.0.discount'],
]);

it('requires valid data (minimal data)', function ($badData, $errors) {
    $user = User::factory()->create()->givePermissionTo($this->createCustomersPermission);

    actingAs($user)->post(route('customers.store', [...$this->validFullData, ...$badData]))->assertInvalid($errors);
})
    ->with([
    [['name' => null], 'name'],
    [['name' => 'a'], 'name'],
    [['name' => str_repeat('a', 256)], 'name'],
    [['cpf' => ''], 'cpf'],
    [['cpf' => '56697386087'], 'cpf'],
    [['birthdate' => null], 'birthdate'],
    [['birthdate' => fn () => new Date()], 'birthdate'],
    [['phone' => ''], 'phone'],
    [['phone' => '123'], 'phone']
]);

it('store a customer with full data', function () {
    $user = User::factory()->create()->givePermissionTo($this->createCustomersPermission);

    actingAs($user)->post(route('customers.store', $this->validFullData));

    assertDatabaseHas(Customer::class, [
        'name' => $this->validFullData['name'],
        'birthdate' => $this->validFullData['birthdate'],
    ]);

    assertDatabaseHas(Guardian::class, [
        'name' => $this->validFullData['guardians'][0]['name'],
        'cpf' => $this->validFullData['guardians'][0]['cpf'],
        'birthdate' => $this->validFullData['guardians'][0]['birthdate'],
        'phone' => $this->validFullData['guardians'][0]['phone'],
        'email' => $this->validFullData['guardians'][0]['email']
    ]);

    assertDatabaseHas(CustomerDiscount::class, [
        'service_id' => $this->validFullData['discounts'][0]['service'],
        'discount' => $this->validFullData['discounts'][0]['discount']
    ]);
});

it('store a customer with minimal data', function () {
    $user = User::factory()->create()->givePermissionTo($this->createCustomersPermission);

    actingAs($user)->post(route('customers.store', $this->validMinimalData));

    assertDatabaseHas(Customer::class, [
        'name' => $this->validMinimalData['name'],
        'cpf' => $this->validMinimalData['cpf'],
        'birthdate' => $this->validMinimalData['birthdate'],
        'phone' => $this->validMinimalData['phone'],
        'email' => $this->validMinimalData['email']
    ]);
});