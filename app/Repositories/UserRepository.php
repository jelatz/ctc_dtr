<?php

namespace App\Repositories;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class UserRepository
{

    public function checkEmployee(string $employeeID)
    {
        return User::select('employee_id', 'name')->where('employee_id', $employeeID)->first();
    }
}
