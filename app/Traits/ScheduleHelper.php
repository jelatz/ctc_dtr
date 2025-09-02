<?php

namespace App\Traits;

use Carbon\Carbon;

trait ScheduleHelper
{
    /**
     * Decide which schedule date should be used.
     */
    public function determineScheduleDate($scheduleRepository, string $employeeID, string $yesterday, string $today): string
    {
        $yesterdaySched = $scheduleRepository->getScheduleByDate($employeeID, $yesterday);
        $todaySched     = $scheduleRepository->getScheduleByDate($employeeID, $today);

        if ($yesterdaySched && $this->isOvernightShift($yesterdaySched)) {
            return now()->greaterThan(Carbon::parse($yesterdaySched->sched_end)->addHours(6))
                ? Carbon::now()->toDateString()
                : $yesterdaySched->sched_date;
        }

        if ($todaySched) {
            return now()->greaterThan(Carbon::parse($todaySched->sched_end)->addHours(6))
                ? Carbon::tomorrow()->toDateString()
                : $todaySched->sched_date;
        }

        return $today;
    }

    /**
     * Check if schedule crosses midnight.
     */
    public function isOvernightShift($schedule): bool
    {
        return date('Y-m-d', strtotime($schedule->sched_end)) > date('Y-m-d', strtotime($schedule->sched_start));
    }
}
