<?php

namespace App\Services;

use App\Repositories\DtrRepository;
use App\Repositories\ScheduleRepository;

class DtrService
{
    protected $dtrRepository;
    protected $scheduleRepository;

    public function __construct(DtrRepository $dtrRepository, ScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
        $this->dtrRepository = $dtrRepository;
    }

    public function checkEmployee($employeeID)
    {
        $schedules = $this->scheduleRepository->getLastFiveSchedule($employeeID);
        $employee = $this->dtrRepository->getByEmployeeId($employeeID);

        return [
            'employee' => $employee,
            'schedules' => $schedules
        ];
    }

    public function checkLatestDTR(string $employeeID)
    {
        if (!$this->dtrRepository->checkDtrExists($employeeID)) {
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
