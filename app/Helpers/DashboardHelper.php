<?php

namespace App\Helpers;

use App\Models\Customer;
use App\Models\Schedule;
use Carbon\Carbon;

class DashboardHelper {
    public static function getSchedulesChartArray() {
        $months = collect();
        for ($i = 0; $i < 12; $i++) {
            $months->push([
                'month' => now()->subMonths($i)->format('M Y'),
                'month_number' => now()->subMonths($i)->format('m'),
            ]);
        }
        $months = $months->reverse();

        $chartData = Schedule::selectRaw("
            TO_CHAR(DATE_TRUNC('month', start_date), 'Mon YYYY') as month,
            TO_CHAR(start_date, 'MM') as month_number,
            teams.name as team,
            COUNT(*) as count
        ")
            ->join('services', 'schedules.service_id', '=', 'services.id')
            ->join('teams', 'services.team_id', '=', 'teams.id')
            ->where('schedules.status', 'finalizado')
            ->where('schedules.active', true)
            ->where('schedules.start_date', '>=', now()->subMonths(12))
            ->groupBy('month', 'month_number', 'team')
            ->orderBy('month_number', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->month;
            });

        return $months->map(function ($month) use ($chartData) {
            $items = $chartData->get($month['month'], collect());

            $total = $items->sum('count');
            $teamsCount = $items->groupBy('team')->mapWithKeys(function ($teamItem, $team) {
                return [$team => $teamItem->sum('count')];
            });

            return array_merge(
                ['name' => $month['month'], 'Total' => $total],
                $teamsCount->toArray()
            );
        })->values()->toArray();
    }

    public static function getSchedulesMonthlyProjection() {
        $projectionValue = 0;
        $schedules = Schedule::with('service')
            ->where(['active' => true])
            ->where('status', '<>', 'cancelado')
            ->where('status', '<>', 'faltou')
            ->get();

        foreach ($schedules as $schedule) {
            $projectionValue += $schedule->service->price;
        }

        return $projectionValue;
    }

    public static function getMonthlyNewCustomers() {
        return Customer::where('active', true)->whereMonth('created_at', Carbon::today()->month)->count();
    }
}