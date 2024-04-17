<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerDiscountResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\GuardianResource;
use App\Http\Resources\ServiceResource;
use App\Models\Customer;
use App\Models\CustomerDiscount;
use App\Models\Guardian;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            'services' => fn () => ServiceResource::collection(Service::where('active', true)->get()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'cpf' => ['required_unless:hasGuardians,true', 'cpf', 'unique:customers,cpf'],
            'birthdate' => ['required', 'date'],
            'phone' => ['required_unless:hasGuardians,true', 'celular_com_ddd', 'unique:customers,phone'],
            'email' => ['required_unless:hasGuardians,true', 'email', 'unique:customers,email'],
            'hasGuardians' => ['boolean'],
            'guardians.*.id' => ['integer', 'exists:guardians,id'],
            'guardians.*.name' => ['required_if_accepted:hasGuardians', 'string', 'min:2', 'max:255'],
            'guardians.*.cpf' => ['required_if_accepted:hasGuardians', 'cpf', 'unique:guardians,cpf'],
            'guardians.*.birthdate' => ['required_if_accepted:hasGuardians', 'date'],
            'guardians.*.phone' => ['required_if_accepted:hasGuardians', 'celular_com_ddd'],
            'guardians.*.email' => ['required_if_accepted:hasGuardians', 'email'],
            'hasDiscounts' => ['boolean'],
            'discounts.*.service' => ['required_if_accepted:hasDiscounts', 'numeric', 'exists:services,id'],
            'discounts.*.discount' => ['required_if_accepted:hasDiscounts', 'numeric', 'min:1', 'max:100'],
        ]);

        try {
            DB::beginTransaction();

            $customer = Customer::create([
                'name' => $request->name,
                'cpf' => preg_replace('/\D+/', '', $request->cpf),
                'birthdate' => $request->birthdate,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            if ($request->hasGuardians && $request->guardians) {
                foreach ($request->guardians as $guardian) {
                    if (empty($guardian)) {
                        break;
                    }

                    if (isset($guardian['id'])) {
                        $guardianEntity = Guardian::find($guardian['id']);
                    } else {
                        $guardianEntity = Guardian::create([
                            'name' => $guardian['name'],
                            'cpf' => preg_replace('/\D+/', '', $guardian['cpf']),
                            'birthdate' => $guardian['birthdate'],
                            'phone' => $guardian['phone'],
                            'email' => $guardian['email'],
                        ]);
                    }

                    $customer->guardians()->attach($guardianEntity);
                }
            }

            if ($request->hasDiscounts && $request->discounts) {
                foreach ($request->discounts as $discount) {
                    CustomerDiscount::create([
                        'customer_id' => $customer->id,
                        'service_id' => $discount['service'],
                        'discount' => $discount['discount'],
                    ]);
                }
            }

            DB::commit();

            return to_route('customers.index')->toastSuccess('Cliente cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            // throw $e;
            return to_route('customers.create')->toastDanger('Ocorreu um erro.');
        }
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
        return inertia('Customers/Edit', [
            'customer' => fn () => CustomerResource::make($customer),
            'guardians' => fn () => GuardianResource::collection($customer->guardians()->where('active', true)->get()),
            'discounts' => fn () => CustomerDiscountResource::collection($customer->discounts()->where('active', true)->get()),
            'services' => fn () => ServiceResource::collection(Service::where('active', true)->get()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer) {
        $guardians = $request->get('guardians');

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'cpf' => ['required_unless:hasGuardians,true', 'cpf', Rule::unique('customers')->ignore($customer->id)],
            'birthdate' => ['required', 'date'],
            'phone' => ['required_unless:hasGuardians,true', 'celular_com_ddd', Rule::unique('customers')->ignore($customer->id)],
            'email' => ['required_unless:hasGuardians,true', 'email', Rule::unique('customers')->ignore($customer->id)],
            'hasGuardians' => ['boolean'],
            'guardians.*.id' => ['integer', 'exists:guardians,id'],
            'guardians.*.name' => ['required_if_accepted:hasGuardians', 'string', 'min:2', 'max:255'],
            'guardians.*.cpf' => [
                'required_if_accepted:hasGuardians',
                'cpf',
                function ($attribute, $value, $validator) use ($guardians) {
                    $index = explode('.', $attribute)[1];

                    $guardian = $guardians[$index];
                    $updatingGuardianId = ($guardian['id'] ?? null);

                    // Check if the CPF is unique (excluding the updating guardian)
                    $query = DB::table('guardians')->where('cpf', $value)->whereNotIn('id', [$updatingGuardianId]);

                    return !$query->exists();
                },
            ],
            'guardians.*.birthdate' => ['required_if_accepted:hasGuardians', 'date'],
            'guardians.*.phone' => ['required_if_accepted:hasGuardians', 'celular_com_ddd'],
            'guardians.*.email' => ['required_if_accepted:hasGuardians', 'email'],
            'hasDiscounts' => ['boolean'],
            'discounts.*.id' => ['integer', 'exists:customer_discounts,id'],
            'discounts.*.service' => ['required_if_accepted:hasDiscounts', 'numeric', 'exists:services,id'],
            'discounts.*.discount' => ['required_if_accepted:hasDiscounts', 'numeric', 'min:1', 'max:100'],
        ]);

        try {
            $customer->update($data);

            $guardianIds = [];
            if (isset($data['guardians'])) {
                foreach ($data['guardians'] as $guardian) {
                    if (isset($guardian['id'])) {
                        $guardianIds[] = $guardian['id'];
                    } else {
                        $guardianEntity = Guardian::create([
                            'name' => $guardian['name'],
                            'cpf' => preg_replace('/\D+/', '', $guardian['cpf']),
                            'birthdate' => $guardian['birthdate'],
                            'phone' => $guardian['phone'],
                            'email' => $guardian['email'],
                        ]);

                        $guardianIds[] = $guardianEntity['id'];
                    }
                }
            }
            $customer->guardians()->sync($guardianIds);

            $actualDiscounts = $customer->discounts()->where(['active' => true])->get();
            $requestDiscounts = isset($data['discounts']) ? collect($data['discounts']) : [];

            if (count($requestDiscounts) > 0) {
                $requestDiscountsIds = [];
                $deleteDiscountsArr = [];

                foreach ($requestDiscounts as $discount) {
                    if (isset($discount['id'])) {
                        $requestDiscountsIds[] = $discount['id'];

                        if ($actualDiscounts->contains('id', $discount['id'])) {
                            CustomerDiscount::find($discount['id'])->update($discount);
                        }
                    } else {
                        CustomerDiscount::create([
                            'customer_id' => $customer->id,
                            'service_id' => $discount['service'],
                            'discount' => $discount['discount'],
                        ]);
                    }
                }

                $requestDiscountsIds = collect($requestDiscountsIds);
                $deleteDiscountsArr = $actualDiscounts->filter(function ($item) use ($requestDiscountsIds) {
                    return $requestDiscountsIds->doesntContain($item->id);
                });

                if ($deleteDiscountsArr->count() > 0) {
                    $deleteDiscountsArr->each(function ($item) {
                        CustomerDiscount::find($item->id)->update(['active' => false]);
                    });
                }
            } else {
                if ($actualDiscounts->count() > 0) {
                    $customer->discounts()->update(['active' => false]);
                }
            }

            return to_route('customers.index')->toastSuccess('Cliente editado com sucesso!');
        } catch (\Exception $e) {
            // throw $e;
            return to_route('customers.edit', $customer->id)->toastDanger('Ocorreu um erro.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer) {
        $customer->active = false;
        $customer->save();

        return to_route('customers.index')->toastSuccess('Cliente inativado com sucesso!');
    }
}
