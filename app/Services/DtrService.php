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
            return [
                'success' => false,
                'error_type' => 'employee_not_found',
                'message' => "Employee ID {$employeeID} not found."
            ];
        }

        $yesterday = $this->scheduleRepository->getScheduleByDate($employeeID, now()->subDay()->toDateString());
        $today = $this->scheduleRepository->getScheduleByDate($employeeID, now()->toDateString());

        if (!$yesterday || !$today) {
            return [
                'success' => false,
                'error_type' => 'schedule_not_found',
                'message' => "Schedule not found for employee {$employeeID} on yesterday or today."
            ];
        }

        $isGraveyard = false;

        $yesterdaySchedStartDate = date('Y-m-d', strtotime($yesterday->sched_start));
        $yesterdaySchedEndDate = date('Y-m-d', strtotime($yesterday->sched_end));

        if ($yesterdaySchedEndDate > $yesterdaySchedStartDate) {
            $isGraveyard = true;
        }
        $schedules = $this->scheduleRepository->getLastFiveSchedule($employeeID, $isGraveyard);
        return [
            'success' => true,
            'employeeData' => $employee,
            'schedules' => $schedules
        ];
    }

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

        return ([
            'success' => false,
            'message' => `You already have logged in for today's shift`
        ]);
    }
}
