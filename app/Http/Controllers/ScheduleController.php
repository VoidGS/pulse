<?php

namespace App\Http\Controllers;

use App\Enums\ScheduleStatus;
use App\Enums\ScheduleStatusColor;
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
use Spatie\GoogleCalendar\Event;

class ScheduleController extends Controller {
    public function __construct() {
        $this->authorizeResource(Schedule::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
        return inertia('Schedules/Index', [
            'schedules' => fn () => ScheduleResource::collection(Schedule::with(['customer', 'service'])->where('active', true)->orderBy('start_date', 'asc')->get()),
            'customers' => fn () => CustomerResource::collection(Customer::with(['guardians', 'discounts'])->where('active', true)->get()),
            'services' => fn () => ServiceResource::collection(Service::with(['user', 'team'])->where('active', true)->get()),
        ]);
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
        try {
            DB::beginTransaction();


        } catch (\Exception $e) {
            DB::rollBack();

            // throw $e;
            return to_route('schedules.index')->toastDanger('Ocorreu um erro no servidor.');
        }

        $data = $request->validate([
            'scheduleDate' => ['required', 'date'],
            'customerId' => ['required', 'numeric', 'exists:customers,id'],
            'serviceId' => ['required', 'numeric', 'exists:services,id'],
            'hasRecurrence' => ['required', 'boolean'],
        ]);

        $event = new Event();
        $event->name = "Agendamento " . Customer::find($data['customerId'])->name;
        $event->startDateTime = Carbon::createFromDate($data['scheduleDate']);
        $event->endDateTime = Carbon::createFromDate($data['scheduleDate'])->addHour();
        $event->setColorId(ScheduleStatusColor::PENDENTE->value);
        $newEvent = $event->save();

        $schedule = new Schedule();
        $schedule->customer_id = $data['customerId'];
        $schedule->service_id = $data['serviceId'];
        $schedule->start_date = Carbon::createFromDate($data['scheduleDate'])->setTimezone('America/Sao_Paulo');
        $schedule->end_date = Carbon::createFromDate($data['scheduleDate'])->addHour()->setTimezone('America/Sao_Paulo');
        $schedule->status = ScheduleStatus::PENDENTE;
        $schedule->event_id = $newEvent->id;
        if ($data['hasRecurrence']) {
            $schedule->recurrence_id = $newEvent->id;
        }
        $schedule->save();

        if ($data['hasRecurrence']) {
            SchedulesHelper::generateFutureSchedulesRecurrence($schedule);
        }

        DB::commit();

        return to_route('schedules.index')->toastSuccess('Agendamento cadastrado com sucesso!');
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
        $data = $request->validate([
            'scheduleDate' => ['required', 'date'],
            'customerId' => ['required', 'numeric'],
            'serviceId' => ['required', 'numeric'],
            'status' => ['required', 'string'],
            'hasRecurrence' => ['required', 'boolean'],
        ]);

        $event = Event::find($schedule->event_id);

        if ($event->status == 'cancelled') {
            return to_route('schedules.index')->toastDanger('Este agendamento não existe mais no Google Agenda. É recomendado que inative o agendamento.');
        }

        $statusName = strtoupper($data['status']);
        $eventColodId = constant("\App\Enums\ScheduleStatusColor::{$statusName}")->value;

        $event->setColorId($eventColodId);
        $event->update([
            'startDateTime' => Carbon::createFromDate($data['scheduleDate']),
            'endDateTime' => Carbon::createFromDate($data['scheduleDate'])->addHour(),
        ]);

        $updateData = [
            'customer_id' => $data['customerId'],
            'service_id' => $data['serviceId'],
            'start_date' => Carbon::createFromDate($data['scheduleDate'])->setTimezone('America/Sao_Paulo'),
            'end_date' => Carbon::createFromDate($data['scheduleDate'])->addHour()->setTimezone('America/Sao_Paulo'),
            'status' => ScheduleStatus::from($data['status'])->value,
        ];
        $schedule->update($updateData);

        return to_route('schedules.index')->toastSuccess('Agendamento editado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule) {
        $event = Event::find($schedule->event_id);

        if ($event->status !== 'cancelled') {
            $event->delete();
        }

        $schedule->update(['active' => false]);

        return to_route('schedules.index')->toastSuccess('Agendamento inativado com sucesso!');
    }
}
