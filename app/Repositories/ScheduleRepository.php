<?php

namespace App\Repositories;

use App\Models\Schedule;
use Exception;
use Illuminate\Support\Facades\Log;

class ScheduleRepository
{
    public function getLastFiveSchedule(string $employeeID)
    {
        try {
            return Schedule::where('employee_id', $employeeID)
                ->orderBy('sched_date', 'desc')
                ->limit(5)
                ->get();
        } catch (Exception $e) {
            Log::error("Failed to fetch schedules for employee: {$employeeID}. Error: " . $e->getMessage());
            return collect(); // Return empty collection instead of null
        }
    }
}
