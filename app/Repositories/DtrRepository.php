<?php

namespace App\Repositories;

use App\Models\Dtr;
use Exception;
use Illuminate\Support\Facades\Log;

class DtrRepository
{
    public function getByEmployeeId(string $employeeID)
    {
        try {
            return Dtr::where('employee_id', $employeeID)->first();
        } catch (Exception $e) {
            Log::error("Failed to fetch DTR for employee: {$employeeID}. Error: " . $e->getMessage());
            return null;
        }
    }
}
