<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Http\Resources\ServiceResource;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Http\Request;

class CustomerController extends Controller {
    public function __construct() {
        $this->authorizeResource(Customer::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
        return inertia('Customers/Index', [
            'customers' => fn () => CustomerResource::collection(Customer::where('active', true)->get()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        return inertia('Customers/Create', [
            'services' => fn () => ServiceResource::collection(Service::where('active', true)->get())
        ]);
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
    public function show(Customer $customer) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer) {
        //
    }
}
