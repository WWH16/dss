<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class Controller
{
    // ADDED: evaluation activity trend (with Month & Year filtering), moved here from
    // AdminController::dashboard and StaffController::dashboard, which held identical copies.
    // Pass a stall id to limit it to one stall, as the staff dashboard did.
    protected function activityTrend(Request $request, ?int $stallId = null): array
    {
        $driver = DB::connection()->getDriverName();
        $yearSql = match ($driver) {
            'sqlite' => "DISTINCT strftime('%Y', created_at) as year",
            'pgsql'  => "DISTINCT CAST(EXTRACT(YEAR FROM created_at) AS INTEGER) as year",
            default  => "DISTINCT YEAR(created_at) as year",
        };
        $availableYears = DB::table('stall_evaluations')
            ->when($stallId, fn ($q) => $q->where('stall_id', $stallId))
            ->selectRaw($yearSql)
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($y) => (int) $y)
            ->toArray();
        if (empty($availableYears)) {
            $availableYears = [(int)date('Y')];
        }
        if (!in_array((int)date('Y'), $availableYears)) {
            array_unshift($availableYears, (int)date('Y'));
        }

        $selectedYear = (int)$request->get('activity_year', $availableYears[0] ?? date('Y'));
        $selectedMonth = $request->get('activity_month', '30_days');

        $trendDates = [];
        $trendCounts = [];

        if ($selectedMonth === 'all') {
            // Full Year: Monthly aggregations (Jan - Dec)
            $monthSql = match ($driver) {
                'sqlite' => "strftime('%m', created_at) as m, COUNT(*) as count",
                'pgsql'  => "CAST(EXTRACT(MONTH FROM created_at) AS INTEGER) as m, COUNT(*) as count",
                default  => "MONTH(created_at) as m, COUNT(*) as count",
            };
            $monthGroup = match ($driver) {
                'sqlite' => "strftime('%m', created_at)",
                'pgsql'  => "CAST(EXTRACT(MONTH FROM created_at) AS INTEGER)",
                default  => "MONTH(created_at)",
            };
            $evalTrend = DB::table('stall_evaluations')
                ->when($stallId, fn ($q) => $q->where('stall_id', $stallId))
                ->selectRaw($monthSql)
                ->whereYear('created_at', $selectedYear)
                ->groupByRaw($monthGroup)
                ->get()
                ->keyBy(fn ($row) => (int) $row->m);

            for ($m = 1; $m <= 12; $m++) {
                $trendDates[] = date('M', mktime(0, 0, 0, $m, 1));
                $trendCounts[] = isset($evalTrend[$m]) ? (int)$evalTrend[$m]->count : 0;
            }
            $activityPeriodLabel = "Full Year {$selectedYear}";
        } elseif (is_numeric($selectedMonth) && (int)$selectedMonth >= 1 && (int)$selectedMonth <= 12) {
            // Specific Month: Day-by-Day (Day 1 to Days in Month)
            $m = (int)$selectedMonth;
            $daysInMonth = (int) date('t', mktime(0, 0, 0, $m, 1, $selectedYear));

            $daySql = match ($driver) {
                'sqlite' => "strftime('%d', created_at) as d, COUNT(*) as count",
                'pgsql'  => "CAST(EXTRACT(DAY FROM created_at) AS INTEGER) as d, COUNT(*) as count",
                default  => "DAY(created_at) as d, COUNT(*) as count",
            };
            $dayGroup = match ($driver) {
                'sqlite' => "strftime('%d', created_at)",
                'pgsql'  => "CAST(EXTRACT(DAY FROM created_at) AS INTEGER)",
                default  => "DAY(created_at)",
            };
            $evalTrend = DB::table('stall_evaluations')
                ->when($stallId, fn ($q) => $q->where('stall_id', $stallId))
                ->selectRaw($daySql)
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $m)
                ->groupByRaw($dayGroup)
                ->get()
                ->keyBy(fn ($row) => (int) $row->d);

            $monthShort = date('M', mktime(0, 0, 0, $m, 1));
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $trendDates[] = sprintf('%s %02d', $monthShort, $d);
                $trendCounts[] = isset($evalTrend[$d]) ? (int)$evalTrend[$d]->count : 0;
            }
            $activityPeriodLabel = date('F Y', mktime(0, 0, 0, $m, 1, $selectedYear));
        } else {
            // Default: Rolling Last 30 Days
            $selectedMonth = '30_days';
            $dateSql = match ($driver) {
                'pgsql'  => "CAST(created_at AS DATE) as date, COUNT(*) as count",
                default  => "DATE(created_at) as date, COUNT(*) as count",
            };
            $dateGroup = match ($driver) {
                'pgsql'  => "CAST(created_at AS DATE)",
                default  => "DATE(created_at)",
            };
            $evalTrend = DB::table('stall_evaluations')
                ->when($stallId, fn ($q) => $q->where('stall_id', $stallId))
                ->selectRaw($dateSql)
                ->where('created_at', '>=', now()->subDays(29)->startOfDay())
                ->groupByRaw($dateGroup)
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            for ($i = 29; $i >= 0; $i--) {
                $d = now()->subDays($i)->format('Y-m-d');
                $trendDates[] = now()->subDays($i)->format('M d');
                $trendCounts[] = isset($evalTrend[$d]) ? (int) $evalTrend[$d]->count : 0;
            }
            $activityPeriodLabel = 'Last 30 Days';
        }
        $activityTotalCount = array_sum($trendCounts);

        return compact(
            'availableYears',
            'selectedYear',
            'selectedMonth',
            'trendDates',
            'trendCounts',
            'activityPeriodLabel',
            'activityTotalCount',
        );
    }
}
