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

        return [
            'schedules' => $schedules,
        ];
    }


    public function logDTR(string $employeeID)
    {
        $dtr = $this->dtrRepository->checkDtrExists($employeeID);
        if ($dtr && $dtr->time_in) {
            return $this->dtrRepository->storeDtr([$employeeID]);
        }

        if ($dtr) {

            $this->dtrRepository->storeDtr([
                'employee_id' => $employeeID,
                'time_in' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
            ]);
            return true;
        }

        return false;
    }
}
