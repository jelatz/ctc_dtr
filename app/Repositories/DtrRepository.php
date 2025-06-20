<?php

namespace App\Repositories;

use App\Models\Dtr;
use Exception;
use Illuminate\Support\Facades\Log;
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
            $dtr = DTR::where('employee_id', $data['employee_id'])->latest();

            if (!$dtr) {
                throw new Exception("DTR not found for employee ID: {$data['employee_id']}");
            }

            $dtr->update(['time_out' => $data['time_out']]);
            return $dtr;
        });
    }

    public function checkDtrExists(string $employeeID, $dtrDate = null)
    {
        $dtrDate = $dtrDate ?: now()->toDateString();
        return Dtr::where('employee_id', $employeeID)
            ->whereDate('time_in', $dtrDate)
            ->latest()
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
