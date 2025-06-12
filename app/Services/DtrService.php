<?php

namespace App\Services;

use App\Repositories\DtrRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\UserRepository;

class DtrService
{
    protected $dtrRepository;
    protected $scheduleRepository;
    protected $userRepository;

    public function __construct(DtrRepository $dtrRepository, ScheduleRepository $scheduleRepository, UserRepository $userRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
        $this->dtrRepository = $dtrRepository;
        $this->userRepository = $userRepository;
    }

    public function checkEmployee($employeeID, $timezone)
    {
        $employee = $this->userRepository->checkEmployee($employeeID);

        if (!$employee) {
            return;
        }

        $schedules = $this->scheduleRepository->getLastFiveSchedule($employeeID, $timezone);

        $employeeDtr = $this->dtrRepository->getByEmployeeId($employeeID);

        return [
            'employee' => $employee,         // actual employee info
            'schedules' => $schedules,       // last 5 schedules
            'dtr' => $employeeDtr            // daily time records
        ];
    }


    public function checkLatestDTR(string $employeeID, $schedDate)
    {
        if (!$this->dtrRepository->checkDtrExists($employeeID, $schedDate)) {
            $this->dtrRepository->storeDtr([
                'user_id' => '111',
                'employee_id' => $employeeID,
                'name' => 'lad',
                'department' => 'ITDEV',
                'date' => date('Y-m-d'),
                'time_in' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
            ]);
            return true;
        };
    }
}
