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
            return Dtr::create($data, []);
        });
    }

    public function checkDtrExists(string $employeeID)
    {
        return Dtr::where('employee_id', $employeeID)
            ->where('time_in', '!=', null)
            ->orWhere('time_out', '!=', null)
            ->latest()
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
