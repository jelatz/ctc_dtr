<?php

namespace App\Services;

use App\Repositories\ScheduleRepository;

class ScheduleService
{
    protected $scheduleRepository;

    public function __construct(ScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
    }

    public function checkEmployee($employeeID)
    {
        return $this->scheduleRepository->getByEmployeeId($employeeID);
    }
}
