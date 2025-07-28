<?php

namespace App\Repositories;

use App\Models\Schedule;
use Exception;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class ScheduleRepository
{
    public function getLastFiveSchedule(string $employeeID, $date)
    {
        try {
            // $localToday = Carbon::now($timezone);
            $dateString = $date ?? now()->toDateString();

            $schedules = Schedule::with('user')
                ->where('employee_id', $employeeID)
                ->where('sched_date', '<=', $dateString)
                ->orderByDesc('sched_date')
                ->orderByDesc('start_time')
                ->limit(5)
                ->get();

            // Attach the correct DTR based on employee_id + time_in = sched_date
            $schedules->each(function ($schedule) {
                $schedule->setRelation('dtr', $schedule->dtr()->where('dtr_date', $schedule->sched_date)->first());
            });

            return $schedules;
        } catch (Exception $e) {
            Log::error("Failed to fetch schedules for employee: {$employeeID}. Error: " . $e->getMessage());
            return collect();
        }
    }

    public function getScheduleByDate(string $employeeID, $date)
    {
        try {
            $schedule = Schedule::with('user')->where('employee_id', $employeeID)->where('sched_date', $date)->orderBy('sched_date', 'desc')->first();
            return $schedule;
        } catch (Exception $e) {
            Log::error("Failed to fetch current schedule for employee: {$employeeID}. Error: " . $e->getMessage());
            return null;
        }
    }
}
