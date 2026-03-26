<?php

namespace App\Repositories;

use App\Models\Dtr;
use App\Models\Logs;
use Exception;
use Illuminate\Support\Facades\DB;

class DtrRepository
{
    public function getByEmployeeId(string $employeeID)
    {
        return Dtr::where('employee_id', $employeeID)->first();
    }

    public function storeDtr(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Dtr::create($data);
        });
    }

    public function updateDtr(array $data)
    {
        return DB::transaction(function () use ($data) {
            $dtr = Dtr::where('employee_id', $data['employee_id'])
                ->where('dtr_date', $data['dtr_date'])
                ->first();

            if (!$dtr) {
                throw new Exception("DTR not found for employee ID: {$data['employee_id']}");
            }

            $updateData = [];
            if (isset($data['time_in'])) {
                $updateData['time_in'] = $data['time_in'];
            }
            if (isset($data['time_out'])) {
                $updateData['time_out'] = $data['time_out'];
            }

            $dtr->update($updateData);
            return $dtr;
        });
    }

    public function checkDtrExists(string $employeeID, $dtrDate = null)
    {
        return Dtr::where('employee_id', $employeeID)
            ->where('dtr_date', $dtrDate)
            ->latest()
            ->first();
    }

    public function storeLogs(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Logs::create($data);
        });
    }
}
