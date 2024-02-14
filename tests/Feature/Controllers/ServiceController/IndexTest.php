<?php

use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\withoutExceptionHandling;

it('requires authentication', function () {
    get(route('services.index'))->assertRedirect(route('login'));
});

it('should return the correct component', function () {
    $this->actingAs($user = User::factory()->create());
    get(route('services.index'))->assertComponent('Services/Index');
});

it('passes services to the view', function () {
    $services = Service::factory(5)->create();

    actingAs(User::factory()->create());
    get(route('services.index'))->assertHasResource('services', ServiceResource::collection($services));
});