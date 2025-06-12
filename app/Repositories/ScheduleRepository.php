<?php

namespace App\Repositories;

use App\Models\Schedule;
use Exception;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class ScheduleRepository
{
    public function getLastFiveSchedule(string $employeeID, string $timezone = 'UTC')
    {
        try {
            // Get today's date in the user's local timezone
            $localToday = Carbon::now($timezone)->toDateString();

            // Filter using this local date
            return Schedule::where('employee_id', $employeeID)
                ->where('sched_date', '<=', $localToday)
                ->orderByDesc('sched_date')
                ->orderByDesc('sched_start')
                ->limit(5)
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to fetch schedules for employee: {$employeeID}. Error: " . $e->getMessage());
            return collect();
        }
    }
}
