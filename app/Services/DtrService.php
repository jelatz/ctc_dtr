<?php

namespace App\Services;

use App\Models\Dtr;

class DtrService
{
    public function checkEmployee($employeeID)
    {
        return Dtr::where('employee_id', $employeeID)->first();
    }
}
