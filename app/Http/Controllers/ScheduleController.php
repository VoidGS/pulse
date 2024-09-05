<?php

namespace App\Http\Controllers;

use App\Helpers\SchedulesHelper;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\ScheduleResource;
use App\Http\Resources\ServiceResource;
use App\Models\Customer;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller {
    public function __construct() {
        $this->authorizeResource(Schedule::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
        return inertia('Schedules/Index', [
            'schedules' => fn () => SchedulesHelper::filterSchedules(['scheduleDate' => Carbon::now()->setTimezone('America/Sao_Paulo')]),
            'customers' => fn () => CustomerResource::collection(Customer::with(['guardians', 'discounts'])->where('active', true)->get()),
            'services' => fn () => ServiceResource::collection(Service::with(['user', 'team'])->where('active', true)->get()),
        ]);
    }

    public function filter(Request $request) {
        $data = $request->validate([
            'scheduleDate' => ['date'],
            'month' => ['numeric'],
            'year' => ['numeric'],
            'customerId' => ['numeric', 'exists:customers,id'],
            'serviceId' => ['numeric', 'exists:services,id'],
            'status' => ['string'],
        ]);

        $filterOptions = [];
        $filterOptions['scheduleDate'] = $request->query('scheduleDate');
        $filterOptions['month'] = $request->query('month');
        $filterOptions['year'] = $request->query('year');
        $filterOptions['customerId'] = $request->query('customerId');
        $filterOptions['serviceId'] = $request->query('serviceId');
        $filterOptions['status'] = $request->query('status');

        return SchedulesHelper::filterSchedules($filterOptions);
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
        $data = $request->validate([
            'scheduleDate' => ['required', 'date'],
            'customerId' => ['required', 'numeric', 'exists:customers,id'],
            'serviceId' => ['required', 'numeric', 'exists:services,id'],
            'hasRecurrence' => ['required', 'boolean'],
        ]);

        try {
            DB::beginTransaction();

            $item = [
                'start_date' => $data['scheduleDate'],
                'customer_id' => $data['customerId'],
                'service_id' => $data['serviceId'],
                'hasRecurrence' => $data['hasRecurrence']
            ];
            SchedulesHelper::createSchedule($item);

            DB::commit();

            return to_route('schedules.index')->toastSuccess('Agendamento cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            // throw $e;

            if ($e->getCode() == 600) {
                return to_route('schedules.index')->toastDanger($e->getMessage());
            }

            return to_route('schedules.index')->toastDanger('Ocorreu um erro no servidor.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule) {
        try {
            DB::beginTransaction();

            $data = $request->validate([
                'scheduleDate' => ['required', 'date'],
                'customerId' => ['required', 'numeric'],
                'serviceId' => ['required', 'numeric'],
                'status' => ['required', 'string'],
                'hasRecurrence' => ['required', 'boolean'],
                'submitType' => ['required', 'integer']
            ]);

            $submitType = $data['submitType'];
            SchedulesHelper::updateSchedule($schedule, $data, $submitType);

            DB::commit();

            return to_route('schedules.index')->toastSuccess('Agendamento editado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            // throw $e;

            if ($e->getCode() == 600) {
                return to_route('schedules.index')->toastDanger($e->getMessage());
            }

            return to_route('schedules.index')->toastDanger('Ocorreu um erro no servidor.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule) {
        try {
            DB::beginTransaction();

            SchedulesHelper::inactivateSchedule($schedule);

            DB::commit();

            return to_route('schedules.index')->toastSuccess('Agendamento inativado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            // throw $e;

            return to_route('schedules.index')->toastDanger('Ocorreu um erro no servidor.');
        }
    }
}
