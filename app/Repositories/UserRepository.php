<?php

namespace App\Repositories;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class UserRepository
{

    public function checkEmployee(string $employeeID)
    {
        return User::where('employee_id', $employeeID)->first();
    }
}
