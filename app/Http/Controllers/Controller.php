<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

abstract class Controller
{
    // ADDED: view data for a stall's QR code, shared by the admin QR page and the staff QR tab.
    // The QR holds a signed evaluation link, so students can only open a stall's form by scanning it.
    // The signature covers only the path and query (not the host), and the host is the site the page
    // is viewed on, so a code made on localhost works locally and one made on the live site works there.
    protected function stallQrData(object $stall): array
    {
        $url = url(URL::signedRoute('student.evaluation', ['stall' => $stall->id], null, false));

        return [
            'stall' => $stall,
            'url' => $url,
            'qr' => (new QRCode)->render($url),
            'fileName' => Str::slug($stall->name) . '-qr',
        ];
    }

    // ADDED: evaluation activity trend (with Month & Year filtering), moved here from
    // AdminController::dashboard and StaffController::dashboard, which held identical copies.
    // Pass a stall id to limit it to one stall, as the staff dashboard did.
    // CHANGED: days and months are counted in Philippine time, as everywhere else in the app (created_at is
    // stored in UTC, so an evaluation before 8 AM used to land on the previous day). The rows are grouped in
    // PHP, which replaces the per-driver SQL; the year list runs from the first evaluation's year to this year.
    // ADDED: $first, the first evaluation's created_at, when the caller already has it; false makes it a query here.
    protected function activityTrend(Request $request, ?int $stallId = null, string|null|false $first = false): array
    {
        $tz = 'Asia/Manila';
        $now = now($tz);
        $evaluations = fn () => DB::table('stall_evaluations')->when($stallId, fn ($q) => $q->where('stall_id', $stallId));

        if ($first === false) {
            $first = $evaluations()->min('created_at');
        }
        $firstYear = $first ? Carbon::parse($first, 'UTC')->timezone($tz)->year : $now->year;
        $availableYears = range($now->year, min($firstYear, $now->year));

        $selectedYear = (int) $request->get('activity_year', $now->year);
        $selectedMonth = $request->get('activity_month', '30_days');

        if ($selectedMonth === 'all') {
            $start = Carbon::create($selectedYear, 1, 1, 0, 0, 0, $tz);
            $end = $start->copy()->addYear();
            $activityPeriodLabel = "Full Year {$selectedYear}";
        } elseif (is_numeric($selectedMonth) && (int) $selectedMonth >= 1 && (int) $selectedMonth <= 12) {
            $start = Carbon::create($selectedYear, (int) $selectedMonth, 1, 0, 0, 0, $tz);
            $end = $start->copy()->addMonth();
            $activityPeriodLabel = $start->format('F Y');
        } else {
            $selectedMonth = '30_days';
            $start = $now->copy()->subDays(29)->startOfDay();
            $end = $now->copy()->addDay()->startOfDay();
            $activityPeriodLabel = 'Last 30 Days';
        }

        // One bucket per month for the full year, else one per day; every bucket starts at zero.
        $format = $selectedMonth === 'all' ? 'M' : 'M d';
        $counts = [];
        for ($d = $start->copy(); $d < $end; $selectedMonth === 'all' ? $d->addMonth() : $d->addDay()) {
            $counts[$d->format($format)] = 0;
        }

        // ponytail: loads one created_at per evaluation in the range; fine at campus scale, group in SQL with AT TIME ZONE if it grows.
        $times = $evaluations()
            ->where('created_at', '>=', $start->copy()->utc())
            ->where('created_at', '<', $end->copy()->utc())
            ->pluck('created_at');
        foreach ($times as $at) {
            $counts[Carbon::parse($at, 'UTC')->timezone($tz)->format($format)]++;
        }

        $trendDates = array_keys($counts);
        $trendCounts = array_values($counts);
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
