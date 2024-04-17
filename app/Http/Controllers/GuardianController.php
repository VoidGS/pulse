<?php

namespace App\Http\Controllers;

use App\Http\Resources\GuardianResource;
use App\Models\Guardian;
use Illuminate\Http\Request;

class GuardianController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $cpfFilter = $request->query('cpf');

        return GuardianResource::collection(Guardian::where('cpf', 'LIKE', "%{$cpfFilter}%")->limit(5)->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Guardian $customerGuardian) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guardian $customerGuardian) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guardian $customerGuardian) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guardian $customerGuardian) {
        //
    }
}
