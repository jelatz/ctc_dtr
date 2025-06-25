<?php

namespace App\Services;

use App\Repositories\DtrRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public function checkEmployee(string $employeeID, string $timezone): ?array
    {
        $employee = $this->fetchFromQisApi(['check_employee' => $employeeID]);

        if (!$employee || empty($employee['success'])) {
            Log::error("Employee not found in QIS API for ID: {$employeeID}. Response: " . json_encode($employee));
            return null;
        }

        $addOneDay = $this->checkDtr($employeeID);
        $schedules = $this->getSchedules($employeeID, $timezone, $addOneDay);

        return ['schedules' => $schedules];
    }

    public function checkDtr(string $employeeID): bool
    {
        $dtrData = $this->fetchFromQisApi(['check_dtr' => $employeeID]);

        if (!$dtrData || empty($dtrData['success'])) {
            Log::error("DTR not found in QIS API for ID: {$employeeID}. Response: " . json_encode($dtrData));
            return false;
        }

        return !empty($dtrData['time_in']) && !empty($dtrData['time_out']);
    }

    public function getSchedules(string $employeeID, $timezone, $addOneDay)
    {
        $scheduleData = $this->fetchFromQisApi(['check_schedule' => $employeeID, 'timezone' => $timezone, 'add_one_day' => $addOneDay]);

        if (!$scheduleData || empty($scheduleData['success'])) {
            Log::error("Schedules not found in QIS API for ID: {$employeeID}. Response: " . json_encode($scheduleData));
            return null;
        }

        return $scheduleData['schedules'] ?? [];
    }

    public function logDTR(string $employeeID, string $dtrDate): bool
    {
        $existingDtr = $this->dtrRepository->checkDtrExists($employeeID, $dtrDate);
        $nowTime = now()->format('H:i:s');

        if (!$existingDtr) {
            $this->dtrRepository->storeDtr([
                'employee_id' => $employeeID,
                'dtr_date' => $dtrDate,
                'time_in' => "{$dtrDate} {$nowTime}",
                'time_out' => null,
            ]);
            return true;
        }

        if (!$existingDtr->time_out && $existingDtr->time_in) {
            $this->dtrRepository->updateDtr([
                'employee_id' => $employeeID,
                'dtr_date' => $dtrDate,
                'time_out' => "{$dtrDate} {$nowTime}",
            ]);
            return true;
        }

        return false;
    }

    /**
     * Send GET request to QIS API and return decoded JSON.
     */
    private function fetchFromQisApi(array $params): ?array
    {
        $params['api_key'] = env('QIS_API_KEY');
        $url = env('QIS_API_URL');

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => env('QIS_API_KEY'),
            ])->get($url, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error("Failed to contact QIS API. Error: {$e->getMessage()}");
            return null;
        }
    }
}
