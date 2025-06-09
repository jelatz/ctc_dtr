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

    public function addDtr(array $data)
    {
        $employeeID = $data['employee_id']; // or 'employee_id'
        $userID = $data['user_id'];
        $date = date('Y-m-d');
        dd($data);
        $existingDtr = $this->dtrRepository->checkDtrExists($employeeID, $date);

        $time = date('H:i:s');

        if ($existingDtr) {
            $existingDtr->time_out = $time;
            $existingDtr->save();
            return $existingDtr;
        } else {
            return $this->dtrRepository->storeDtr([
                'employee_id' => $employeeID,
                'user_id' => $userID,
                'date' => $date,
                'time_in' => $time,
            ]);
        }
    }
}
