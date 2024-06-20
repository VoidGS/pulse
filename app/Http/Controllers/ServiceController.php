<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceResource;
use App\Http\Resources\TeamResource;
use App\Http\Resources\UserResource;
use App\Models\Service;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceController extends Controller {
    public function __construct() {
        $this->authorizeResource(Service::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
        return inertia('Services/Index', [
            'services' => fn () => ServiceResource::collection(Service::with(['user', 'team'])->where('active', true)->get()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        return inertia('Services/Create', [
            'teams' => fn () => TeamResource::collection(Team::where('active', true)->get()),
            'users' => fn () => UserResource::collection(User::where('active', true)->get()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999'],
            'duration' => ['required', 'integer', 'min:0', 'max:99'],
            'team' => ['required', 'numeric'],
            'user' => ['required', 'numeric'],
        ]);

        Service::create([
            'name' => $request->name,
            'price' => $request->price,
            'duration' => $request->duration,
            'team_id' => $request->team,
            'user_id' => $request->user,
        ]);

        return to_route('services.index')->toastSuccess('Serviço cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service) {
        $service->load('user');
        $service->load('team');

        return inertia('Services/Edit', [
            'service' => fn () => ServiceResource::make($service),
            'teams' => fn () => TeamResource::collection(Team::where('active', true)->get()),
            'users' => fn () => UserResource::collection(User::where('active', true)->get()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service) {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999'],
            'duration' => ['required', 'integer', 'min:0', 'max:99'],
            'team' => ['required', 'numeric'],
            'user' => ['required', 'numeric'],
        ]);

        $dataUpdate = [
            'name' => $data['name'],
            'price' => $data['price'],
            'duration' => $data['duration'],
            'team_id' => $data['team'],
            'user_id' => $data['user']
        ];

        $service->update($dataUpdate);

        return to_route('services.index')->toastSuccess('Serviço atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service) {
        $service->active = false;
        $service->save();

        return to_route('services.index')->toastSuccess('Serviço inativado com sucesso!');
    }
}
