<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DtrService;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class DtrController extends Controller
{
    protected $dtrService;

    public function __construct(DtrService $dtrService)
    {
        $this->dtrService = $dtrService;
    }

    public function index()
    {
        return Inertia::render('Home');
    }

    public function getEmployeeAndSchedules(Request $request)
    {
        $employeeID = $request->input('employeeID');

        $employee = $this->dtrService->checkEmployee($employeeID);
        if (!$employee) {
            throw ValidationException::withMessages([
                'employeeID' => 'Employee not found.'
            ]);
        }
        $schedules = $this->dtrService->getEmployeeSchedules($request);

        if (!$schedules) {
            throw ValidationException::withMessages([
                'employeeID' => 'No schedules found for employee.'
            ]);
        }

        return redirect()->route('home')->with([
            'employeeData' => $employee,
            'schedules' => $schedules
        ]);
    }

    public function addDtr(Request $request)
    {

        $employeeID = $request->input('employee_id');
        $dtrDate = $request->input('dtrDate');
        $type = $request->input('type');

        $result = $this->dtrService->logDTR($employeeID, $dtrDate, $type);
        if (!$result) {
            throw ValidationException::withMessages([
                'employeeID' => 'You already have logged in for today\'s shift. This action will be logged incase of overtime application'
            ]);
        }

        if ($result['type'] === 'login') {
            return redirect()->route('home')->with([
                'success' => 'You have logged in successfully.'
            ]);
        } else {
            return redirect()->route('home')->with([
                'success' => 'You have logged out successfully.'
            ]);
        }
    }
}
