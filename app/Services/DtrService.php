<?php

namespace App\Services;

use App\Repositories\DtrRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\UserRepository;
use App\Traits\ScheduleHelper; // <-- import your trait here
use Carbon\Carbon;
use Illuminate\Http\Request;

class DtrService
{
    use ScheduleHelper; // <-- use the trait

    protected DtrRepository $dtrRepository;
    protected ScheduleRepository $scheduleRepository;
    protected UserRepository $userRepository;

    public function __construct(
        DtrRepository $dtrRepository,
        ScheduleRepository $scheduleRepository,
        UserRepository $userRepository
    ) {
        $this->dtrRepository = $dtrRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Check if employee exists.
     */
    public function checkEmployee(string $employeeID)
    {
        return $this->userRepository->checkEmployee($employeeID) ?: false;
    }

    /**
     * Get last 5 schedules based on computed schedule date.
     */
    public function getEmployeeSchedules(Request $request)
    {
        $employeeID = $request->input('employeeID');

        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        // Ensure employee has a schedule today
        if (!$this->scheduleRepository->getScheduleByDate($employeeID, $today)) {
            return false;
        }

        // Use trait helper for schedule date computation
        $schedDate = $this->determineScheduleDate(
            $this->scheduleRepository,
            $employeeID,
            $yesterday,
            $today
        );

        $schedules = $this->scheduleRepository->getLastFiveSchedule($employeeID, $schedDate);

        return $schedules ?: false;
    }

    /**
     * Log time in/out or store in logs if already exists.
     */
    public function logDTR(string $employeeID, string $dtrDate, string $type)
    {
        $existingDtr = $this->dtrRepository->checkDtrExists($employeeID, $dtrDate);
        $nowTime = now()->addMinutes(5);

        // Validation rules
        if ($existingDtr && $type === 'login') {
            return false; // Already logged in
        }

        if ($existingDtr && $existingDtr->time_out && $type === 'logout') {
            return false; // Already logged out
        }

        // Store log first
        $this->dtrRepository->storeLogs([
            'employee_id' => $employeeID,
            'dtr_date'    => $dtrDate,
            'type'        => $type,
        ]);

        // Handle time in / time out
        if (!$existingDtr && $type === 'login') {
            $this->createTimeIn($employeeID, $dtrDate, $nowTime);
        } elseif ($existingDtr && $existingDtr->time_in && !$existingDtr->time_out && $type === 'logout') {
            $this->updateTimeOut($employeeID, $nowTime);
        }

        return [
            'status' => 'success',
            'type'   => $type,
        ];
    }



    /**
     * Create new DTR entry (time in).
     */
    private function createTimeIn(string $employeeID, string $dtrDate, Carbon $now)
    {
        return $this->dtrRepository->storeDtr([
            'employee_id' => $employeeID,
            'dtr_date'    => $dtrDate,
            'time_in'     => "{$dtrDate} {$now->format('H:i:s')}",
            'time_out'    => null,
        ]);
    }

    /**
     * Update DTR entry (time out).
     */
    private function updateTimeOut(string $employeeID, Carbon $now)
    {
        $dateToday = $now->toDateString();

        return $this->dtrRepository->updateDtr([
            'employee_id' => $employeeID,
            'dtr_date'    => $dateToday,
            'time_out'    => "{$dateToday} {$now->format('H:i:s')}",
        ]);
    }
}
