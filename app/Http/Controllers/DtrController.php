<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DtrService;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class DtrController extends Controller
{

    public function __construct(protected DtrService $dtrService) {}

    public function index()
    {
        return Inertia::render('Home');
    }
    

    public function getEmployeeAndSchedules(Request $request)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('home');
        }

        $employeeID = $request->input('employeeID');
        
        if (empty($employeeID)) {
            throw ValidationException::withMessages([
                'employeeID' => 'Employee ID is required.',
            ]);
        }

        $employee = $this->dtrService->checkEmployee($employeeID);
        if (!$employee) {
            throw ValidationException::withMessages([
                'employeeID' => 'Employee not found.',
            ]);
        }

        $schedules = $this->dtrService->getEmployeeSchedules($request);
        if (!$schedules) {
            throw ValidationException::withMessages([
                'employeeID' => 'No schedules found for employee.',
            ]);
        }

        return Inertia::render('Home', [
            'employeeData' => $employee,
            'schedules' => $schedules,
        ]);
    }

    public function addDtr(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string',
            'dtrDate' => 'required|string',
            'type' => 'required|string|in:login,logout',
            'timestamp' => 'nullable|numeric|min:0'
        ]);

        $employeeID = $request->input('employee_id');
        $dtrDate = $request->input('dtrDate');
        $type = $request->input('type');
        $timestamp = $request->input('timestamp');

        $exactTime = $timestamp ? \Carbon\Carbon::createFromTimestampMs($timestamp, config('app.timezone', 'Asia/Manila')) : null;

        if ($exactTime) {
            $diffInMinutes = $exactTime->diffInMinutes(now(config('app.timezone', 'Asia/Manila')));
            // If the client's timestamp is more than 5 minutes off from the true server time
            if ($diffInMinutes > 5) {
                // Ignore client timestamp and use backend server time instead
                $exactTime = null; 
            }
        }

        $result = $this->dtrService->logDTR($employeeID, $dtrDate, $type, $exactTime);

        if (!$result) {
            $errorMessage = 'You are already logged in.';
            
            // Re-fetch schedules to reflect the true existing login
            $request->merge(['employeeID' => $employeeID]); // Simulate getEmployeeSchedules request
            $schedules = $this->dtrService->getEmployeeSchedules($request);
            
            return redirect()->route('home')->withErrors([
                'employee_id' => $errorMessage,
            ])->with('schedules', $schedules);
        }

        return Inertia::render('Home', [
            'success' => $result['type'] === 'login'
                ? 'You have logged in successfully.'
                : 'You have logged out successfully.',
        ]);
    }
}
