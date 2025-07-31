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
    }

    public function getEmployeeSchedules(Request $request) {}

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
