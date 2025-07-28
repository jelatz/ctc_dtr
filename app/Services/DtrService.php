<?php

namespace App\Services;

use App\Repositories\DtrRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;



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

    public function getEmployeeSchedules(Request $request)
    {
        $errorMessage = ['employeeID.exists' => 'Employee ID not found.'];
        $data = $request->validate([
            'employeeID' => 'required|exists:users,employee_id',
        ], $errorMessage);

        $employeeID = $data['employeeID'];

        $employee = $this->userRepository->checkEmployee($employeeID);
        if (!$employee) {
            return [
                'success' => false,
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

        $yesterdaySchedStartDate = date('Y-m-d', strtotime($yesterday->sched_start));
        $yesterdaySchedEndDate = date('Y-m-d', strtotime($yesterday->sched_end));

        $date = now()->toDateString();

        if ($yesterdaySchedEndDate > $yesterdaySchedStartDate) {
            $date = now()->subDay()->toDateString();
            if (now()->greaterThan(Carbon::parse($yesterday->sched_end)->addHours(6))) {
                $date = now()->toDateString();
            }
        }
    
        if(now()->greaterThan(Carbon::parse($today->sched_end)->addHours(6))){
            $date = now()->addDay()->toDateString();
        }

        dd($date);

        $schedules = $this->scheduleRepository->getLastFiveSchedule($employeeID, $date);
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
            return [
                'success' => true,
                'message' => 'DTR logged in successfully.',
            ];
        }

        if (!$existingDtr->time_out && $existingDtr->time_in) {
            $dateToday = now()->toDateString();
            $this->dtrRepository->updateDtr([
                'employee_id' => $employeeID,
                'dtr_date' => $dateToday,
                'time_out' => "{$dateToday} {$nowTime}",
            ]);
            return [
                'success' => true,
                'message' => 'Time out logged successfully.',
            ];
        }

        // If the DTR already exists store in logs and throw an error
        $this->dtrRepository->storeLogs([
            'employee_id' => $employeeID,
            'dtr_date' => now()->toDateString()
        ]);

        return [
            'success' => false,
            'error_type' => 'dtr_exists',
        ];
    }
}
