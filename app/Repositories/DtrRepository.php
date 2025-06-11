<?php

namespace App\Repositories;

use App\Models\Dtr;
use Exception;
use Illuminate\Support\Facades\Log;

class DtrRepository
{
    public function getByEmployeeId(string $employeeID)
    {
        return Dtr::where('employee_id', $employeeID)->first();
    }

    public function storeDtr(array $data)
    {
        return Dtr::create($data);
    }

    public function checkDtrExists(string $employeeID)
    {
        return Dtr::where('employee_id', $employeeID)
            ->latest()
            ->where('date', '2025-06-12')
            ->first();
    }
}
