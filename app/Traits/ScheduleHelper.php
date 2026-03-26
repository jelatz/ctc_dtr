<?php

namespace App\Traits;

use Carbon\Carbon;

trait ScheduleHelper
{
    /**
     * Decide which schedule date should be used for a given employee and moment.
     *
     * Handles four scenarios:
     *  1. Yesterday was a dayoff (even with overnight times) → overnight window is ignored;
     *     resolve against today's schedule or fall back to $today.
     *  2. Yesterday had a real overnight working/holiday shift AND today's shift has started
     *     → today's shift takes priority, use today's sched_date.
     *  3. Yesterday had a real overnight shift and today's shift has NOT started yet
     *     → still on yesterday's overnight window (with 6-hour grace after sched_end).
     *  4. No overnight overlap → resolve against today's schedule or fall back to $today.
     */
    public function determineScheduleDate($scheduleRepository, string $employeeID, string $yesterday, string $today): string
    {
        $now            = Carbon::now('Asia/Manila');
        $yesterdaySched = $scheduleRepository->getScheduleByDate($employeeID, $yesterday);
        $todaySched     = $scheduleRepository->getScheduleByDate($employeeID, $today);

        // Overnight shift from yesterday is still potentially active —
        // but ONLY when yesterday was an actual working/holiday shift, NOT a dayoff.
        // A dayoff schedule carries no active shift even if its time window crosses midnight.
        if ($yesterdaySched && !$this->isDayOff($yesterdaySched) && $this->isOvernightShift($yesterdaySched)) {
            $overnightEnd = Carbon::parse($yesterdaySched->sched_end, 'Asia/Manila');

            // If today's schedule also exists AND now >= today's sched_start,
            // the employee has transitioned into today's shift (schedules overlap).
            // Today's schedule takes priority in this case.
            if ($todaySched) {
                $todayStart = Carbon::parse($todaySched->sched_start, 'Asia/Manila');
                if ($now->greaterThanOrEqualTo($todayStart)) {
                    return $this->resolveTodayScheduleDate($now, $todaySched);
                }
            }

            // Still within the overnight shift window (or 6-hour grace after it ends).
            return $now->greaterThan($overnightEnd->copy()->addHours(6))
                ? $now->toDateString()
                : $yesterdaySched->sched_date;
        }

        if ($todaySched) {
            return $this->resolveTodayScheduleDate($now, $todaySched);
        }

        return $today;
    }

    /**
     * Resolve the schedule date for a today-based schedule record.
     * If we are more than 6 hours past sched_end, the shift has long ended → return tomorrow.
     */
    private function resolveTodayScheduleDate(Carbon $now, $todaySched): string
    {
        return $now->greaterThan(Carbon::parse($todaySched->sched_end, 'Asia/Manila')->addHours(6))
            ? Carbon::tomorrow('Asia/Manila')->toDateString()
            : $todaySched->sched_date;
    }

    /**
     * Check if schedule crosses midnight.
     */
    public function isOvernightShift($schedule): bool
    {
        return Carbon::parse($schedule->sched_end, 'Asia/Manila')->toDateString()
             > Carbon::parse($schedule->sched_start, 'Asia/Manila')->toDateString();
    }

    /**
     * Check if a schedule record is a day-off.
     * Day-off schedules carry no active shift, even if the time window crosses midnight.
     */
    public function isDayOff($schedule): bool
    {
        return isset($schedule->day_type) && $schedule->day_type === 'dayoff';
    }
}
