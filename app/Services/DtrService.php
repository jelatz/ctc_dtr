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

        $dtrData = $this->dtrRepository->checkDtrExists($employeeID);
        $addOneDay = $dtrData && $dtrData->time_in && $dtrData->time_out;
        $schedules = $this->scheduleRepository->getLastFiveSchedule($employeeID, $timezone, $addOneDay);

        return [
            'schedules' => $schedules,
        ];
    }



    public function logDTR(string $employeeID, $dtrDate)
    {
        $hasDtrData = $this->dtrRepository->checkDtrExists($employeeID, $dtrDate);
        if (!$hasDtrData) {
            $this->dtrRepository->storeDtr([
                'employee_id' => $employeeID,
                'dtr_date' => $dtrDate,
                'time_in' => $dtrDate . ' ' . now()->format('H:i:s'),
                'time_out' => null,
            ]);
            return true;
        }

        if ($hasDtrData->time_in) {
            $this->dtrRepository->updateDtr([
                'employee_id' => $employeeID,
                'dtr_date' => $dtrDate,
                'time_out' => $dtrDate . ' ' . now()->format('H:i:s'),
            ]);
            return true;
        }
    }
}
