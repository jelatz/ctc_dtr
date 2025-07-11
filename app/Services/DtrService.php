<?php

namespace App\Services;

use App\Repositories\DtrRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DtrService
{
    protected $dtrRepository;
    protected $scheduleRepository;
    protected $userRepository;

    public function __construct(
        DtrRepository $dtrRepository,
        ScheduleRepository $scheduleRepository,
        UserRepository $userRepository
    ) {
        $this->dtrRepository = $dtrRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->userRepository = $userRepository;
    }

    public function checkEmployee(string $employeeID)
    {
        $employee = $this->userRepository->checkEmployee($employeeID);

        if (!$employee) {
            Log::error("Employee not found in local database for ID: {$employeeID}");
            return null;
        };

        return $employee;
    }

    public function getEmployeeSchedules(string $employeeID, string $timezone)
    {
        $employee = $this->userRepository->checkEmployee($employeeID);

        if (!$employee) {
            Log::error("Employee not found in local database for ID: {$employeeID}");
            return null;
        }

        // Get yesterday and today's schedule (assumes getCurrentSchedule returns 2 entries)
        $schedules = $this->scheduleRepository->getCurrentSchedule($employeeID); // should return sorted by date DESC

        if (!$schedules || count($schedules) < 2) {
            Log::error('Not enough schedules found for employee ID: ' . $employeeID);
            return null;
        }

        $todaySchedule = $schedules[0];       // today
        $yesterdaySchedule = $schedules[1];   // yesterday

        // Check if yesterday's schedule crosses midnight
        $yesterdayStart = date('Y-m-d', strtotime($yesterdaySchedule->sched_start));
        $yesterdayEnd   = date('Y-m-d', strtotime($yesterdaySchedule->sched_end));
        if ($yesterdayEnd > $yesterdayStart) {
            // Normal Shift
            dd("yes");
        } else {
            dd("no");
            // Graveyard Shift
        }

        // You can also apply the same logic to today's schedule if needed
        $todayStart = strtotime($todaySchedule->sched_date . ' ' . $todaySchedule->sched_start);
        $todayEnd   = strtotime($todaySchedule->sched_date . ' ' . $todaySchedule->sched_end);

        if ($todayEnd < $todayStart) {
            Log::info("Today's shift crosses midnight (graveyard) for employee ID: {$employeeID}");
            // Graveyard logic
        } else {
            Log::info("Today's shift is a regular daytime shift for employee ID: {$employeeID}");
            // Day shift logic
        }

        return [
            'success' => true,
            'employeeData' => $employee,
            'schedules' => [
                'today' => $todaySchedule,
                'yesterday' => $yesterdaySchedule
            ]
        ];
    }



    // public function getSchedules(string $employeeID, string $timezone, bool $addOneDay = false)
    // {
    //     $currentSchedule = $this->scheduleRepository->getCurrentSchedule($employeeID);
    //     if ($currentSchedule) {
    //         $timeIn = date('Y-m-d', strtotime($currentSchedule->sched_start));
    //         $timeOut = date('Y-m-d', strtotime($currentSchedule->sched_end));
    //         if ($timeOut > $timeIn) {
    //             dd("yes");
    //         }
    //     }
    //     // check latest dtr data if there's login / logout
    //     $dtrData = $this->dtrRepository->checkDtrExists($employeeID);
    //     $addOneDay = $dtrData && $dtrData->time_in && $dtrData->time_out ? true : false;

    //     $schedules = $this->scheduleRepository->getLastFiveSchedule($employeeID, $timezone, $addOneDay);

    //     if ($schedules->isEmpty()) {
    //         Log::error("No schedules found for employee ID: {$employeeID}");
    //         return [];
    //     }

    //     return $schedules;
    // }

    public function logDTR(string $employeeID, string $dtrDate)
    {
        $existingDtr = $this->dtrRepository->checkDtrExists($employeeID, $dtrDate);
        $nowTime = now()->addMinutes(5)->format('H:i:s');

        if (!$existingDtr) {
            $this->dtrRepository->storeDtr([
                'employee_id' => $employeeID,
                'dtr_date' => $dtrDate,
                'time_in' => "{$dtrDate} {$nowTime}",
                'time_out' => null,
            ]);
            return true;
        }

        if (!$existingDtr->time_out && $existingDtr->time_in) {
            $dateToday = now()->toDateString();
            $this->dtrRepository->updateDtr([
                'employee_id' => $employeeID,
                'dtr_date' => $dateToday,
                'time_out' => "{$dateToday} {$nowTime}",
            ]);
            return true;
        }
        return false;
    }
}
