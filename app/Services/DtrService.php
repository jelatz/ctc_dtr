<?php

namespace App\Services;

use App\Repositories\DtrRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\UserRepository;
use App\Traits\ScheduleHelper;
use App\Jobs\SyncDtrToQisJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DtrService
{
    use ScheduleHelper;

    public function __construct(
        protected DtrRepository $dtrRepository,
        protected ScheduleRepository $scheduleRepository,
        protected UserRepository $userRepository
    ) {}

    /**
     * Check if employee exists.
     */
    public function checkEmployee(string $employeeID)
    {
        try {
            $qisUser = DB::connection('mysql_qis')
                ->table('EmployeeInfo')
                ->where('InterEmployeeID', $employeeID)
                ->first();
        } catch (\Exception $e) {
            Log::warning("QIS Database connection failed for checkEmployee. Error: " . $e->getMessage());
            throw \Illuminate\Validation\ValidationException::withMessages([
                'employeeID' => 'Unable to connect to the external QIS database. Please try again later.',
            ]);
        }

        $localUser = $this->userRepository->checkEmployee($employeeID);
        if (!$localUser) return false;

        $chrisBaseURL = "http://172.20.60.241/CHRIS/";

        return [
            'employee_id' => $localUser->employee_id,
            'name'        => $localUser->name,
            'photo'       => ($qisUser && $qisUser->PhotoFilename)
                ? $chrisBaseURL . ltrim($qisUser->PhotoFilename, '/')
                : asset('images/default-prof-pic.png'),
        ];
    }

    /**
     * Get last 5 schedules based on computed schedule date.
     */
    public function getEmployeeSchedules(Request $request)
    {
        $employeeID = $request->input('employeeID');

        $today = Carbon::today('Asia/Manila')->toDateString();
        $yesterday = Carbon::yesterday('Asia/Manila')->toDateString();

        if (!$this->scheduleRepository->getScheduleByDate($employeeID, $today)) {
            return false;
        }

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
 public function logDTR(string $employeeID, string $dtrDate, string $type, ?\Carbon\Carbon $exactTime = null)
{
    $existingDtr = $this->dtrRepository->checkDtrExists($employeeID, $dtrDate);
    $nowTime = $exactTime ?? Carbon::now('Asia/Manila');

    // If login and already has time_in, stop immediately to avoid QIS dtr_data updates
    if ($type === 'login' && $existingDtr && $existingDtr->time_in) {
        // Optional: Still record to dtr_actual for audit trail if desired
        DB::connection('mysql_qis')->table('dtr_actual')->insert([
            'logtype'     => strtoupper($type),
            'logdatetime' => $nowTime,
            'employeeno'  => $employeeID,
        ]);
        return false; 
    }

    // Store local log
    $this->dtrRepository->storeLogs([
        'employee_id' => $employeeID,
        'dtr_date'    => $dtrDate,
        'type'        => $type,
    ]);

    // Insert to legacy raw logs
    DB::connection('mysql_qis')->table('dtr_actual')->insert([
        'logtype'     => strtoupper($type),
        'logdatetime' => $nowTime,
        'employeeno'  => $employeeID,
    ]);

    // Handle time in / time out logic
    if (!$existingDtr) {
        $actualDatetime = "{$nowTime->toDateString()} {$nowTime->format('H:i:s')}";

        if ($type === 'login') {
            $this->createTimeIn($employeeID, $dtrDate, $actualDatetime);
        } else {
            $this->createTimeOutOnly($employeeID, $dtrDate, $actualDatetime);
        }
        SyncDtrToQisJob::dispatch($employeeID, $dtrDate, $type, $actualDatetime, $nowTime);
    } else {
        if ($type === 'logout') {
            $actualDatetime = "{$nowTime->toDateString()} {$nowTime->format('H:i:s')}";
            $this->updateTimeOut($employeeID, $dtrDate, $actualDatetime);
            SyncDtrToQisJob::dispatch($employeeID, $dtrDate, $type, $actualDatetime, $nowTime);
        } else if ($type === 'login' && empty($existingDtr->time_in)) {
            $actualDatetime = "{$nowTime->toDateString()} {$nowTime->format('H:i:s')}";
            $this->updateTimeIn($employeeID, $dtrDate, $actualDatetime);
            SyncDtrToQisJob::dispatch($employeeID, $dtrDate, $type, $actualDatetime, $nowTime);
        }
    }

    return [
        'status' => 'success',
        'type'   => $type,
    ];
}

    /**
     * Create new DTR entry (time in).
     */
    private function createTimeIn(string $employeeID, string $dtrDate, string $actualDatetime)
    {
        return $this->dtrRepository->storeDtr([
            'employee_id' => $employeeID,
            'dtr_date'    => $dtrDate,
            'time_in'     => $actualDatetime,
            'time_out'    => null,
        ]);
    }

    /**
     * Create new DTR entry (time out only).
     */
    private function createTimeOutOnly(string $employeeID, string $dtrDate, string $actualDatetime)
    {
        return $this->dtrRepository->storeDtr([
            'employee_id' => $employeeID,
            'dtr_date'    => $dtrDate,
            'time_in'     => null,
            'time_out'    => $actualDatetime,
        ]);
    }

    /**
     * Update DTR entry (time in).
     */
    private function updateTimeIn(string $employeeID, string $dtrDate, string $actualDatetime)
    {
        return $this->dtrRepository->updateDtr([
            'employee_id' => $employeeID,
            'dtr_date'    => $dtrDate,
            'time_in'     => $actualDatetime,
        ]);
    }

    /**
     * Update DTR entry (time out).
     */
    private function updateTimeOut(string $employeeID, string $dtrDate, string $actualDatetime)
    {
        return $this->dtrRepository->updateDtr([
            'employee_id' => $employeeID,
            'dtr_date'    => $dtrDate,
            'time_out'    => $actualDatetime,
        ]);
    }

    }