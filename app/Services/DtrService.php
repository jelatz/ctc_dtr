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

    public function checkEmployee($employeeID)
    {
        $employee = $this->userRepository->checkEmployee($employeeID);

        if (!$employee) {
            return false;
        }

        return $employee;
    }

    public function getEmployeeSchedules(Request $request)
    {

        $setSchedule = $this->scheduleRepository->getScheduleByDate($request->input('employeeID'), Carbon::today()->toDateString());

        if (!$setSchedule) {
            return false;
        }

        $schedules = $this->scheduleRepository->getLastFiveSchedule(
            $request->input('employeeID'),
            Carbon::yesterday()->toDateString()
        );

        // Check yesterday and today's schedules
        $yesterday = $this->scheduleRepository->getScheduleByDate(
            $request->input('employeeID'),
            Carbon::yesterday()->toDateString()
        );

        $today = $this->scheduleRepository->getScheduleByDate(
            $request->input('employeeID'),
            Carbon::today()->toDateString()
        );

        if ($yesterday && date('Y-m-d', strtotime($yesterday->sched_end)) > date('Y-m-d', strtotime($yesterday->sched_start))) {
            $sched_date = $yesterday->sched_date;
            if (now()->greaterThan(Carbon::parse($yesterday->sched_end)->addHours(6))) {
                $sched_date = Carbon::now()->toDateString();
            } else {
                $sched_date = $yesterday->sched_date;
            }
        } else {
            $sched_date = Carbon::today()->toDateString();
            $sched_date = $today->sched_date;
            if (now()->greaterThan(Carbon::parse($today->sched_end)->addHours(6))) {
                $sched_date = Carbon::now()->tomorrow()->toDateString();
            } else {
                $sched_date = $today->sched_date;
            }
        }

        $schedules = $this->scheduleRepository->getLastFiveSchedule(
            $request->input('employeeID'),
            $sched_date
        );
        if (!$schedules) {
            return false;
        }
        return $schedules;
    }

    public function logDTR(string $employeeID, string $dtrDate)
    {
        $existingDtr = $this->dtrRepository->checkDtrExists($employeeID, $dtrDate);
        $nowTime = now()->addMinutes(5)->format('H:i:s');

        if (!$existingDtr) {
            return $this->dtrRepository->storeDtr([
                'employee_id' => $employeeID,
                'dtr_date' => $dtrDate,
                'time_in' => "{$dtrDate} {$nowTime}",
                'time_out' => null,
            ]);
        }

        if (!$existingDtr->time_out && $existingDtr->time_in) {
            $dateToday = now()->toDateString();
            return $this->dtrRepository->updateDtr([
                'employee_id' => $employeeID,
                'dtr_date' => $dateToday,
                'time_out' => "{$dateToday} {$nowTime}",
            ]);
        }

        // If the DTR already exists store in logs and throw an error
        if ($this->dtrRepository->storeLogs([
            'employee_id' => $employeeID,
            'dtr_date' => now()->toDateString()
        ])) {
            return false;
        }
    }
}
