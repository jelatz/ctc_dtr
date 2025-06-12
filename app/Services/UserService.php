<?php

namespace App\Services;

use App\Repositories\UserRepository;;

use App\Repositories\ScheduleRepository;

class UserService
{
    protected $userRepository;
    protected $scheduleRepository;

    public function __construct(UserRepository $userRepository, ScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
        $this->userRepository = $userRepository;
    }

    public function checkEmployee(string $employeeID)
    {
        return $this->userRepository->checkEmployee($employeeID);
    }
}
