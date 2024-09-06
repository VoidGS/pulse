<?php

namespace App\Http\Controllers;

use App\Helpers\DashboardHelper;
use App\Models\Schedule;
use Illuminate\Http\Request;

class DashboardController extends Controller {
    public function index() {
        return inertia('Dashboard', [
            'chartData' => DashboardHelper::getSchedulesChartArray(),
            'projection' => DashboardHelper::getSchedulesMonthlyProjection(),
            'newCustomers' => DashboardHelper::getMonthlyNewCustomers()
        ]);
    }
}
