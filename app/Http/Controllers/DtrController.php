<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Schedule;

class DtrController extends Controller
{
    public function getEmployeeAndSchedules(Request $request)
    {
        $request->validate([
            'employeeID' => 'required|string|exists:users,employee_id',
        ], [
            'employeeID.exists' => 'Employee ID not found in the system.',
        ]);

        $employeeId = $request->employeeID;

        // Get employee data
        $employee = User::where('employee_id', $employeeId)->first();

        if (!$employee) {
            return back()->withErrors([
                'employeeID' => 'Employee not found.',
            ]);
        }

        // Get last 5 schedules
        $schedules = Schedule::where('employee_id', $employeeId)
            ->with('dtr') // Assuming you have a dtr relationship
            ->orderBy('sched_date', 'desc')
            ->limit(5)
            ->get();

        // Return to Home page with data
        return Inertia::render('Home', [
            'employeeData' => $employee,
            'schedules' => $schedules,
        ]);
    }

    public function addDtr(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string|exists:users,employee_id',
            'dtrDate' => 'required|date',
            'type' => 'required|in:login,logout',
        ]);

        $employeeId = $request->employee_id;
        $dtrDate = $request->dtrDate;
        $type = $request->type;

        try {
            // Your DTR logic here
            // Example:
            $dtr = \App\Models\Dtr::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'dtr_date' => $dtrDate,
                ],
                [
                    $type === 'login' ? 'time_in' : 'time_out' => now(),
                ]
            );

            return redirect()->route('home')->with('flash', [
                'success' => ucfirst($type) . ' recorded successfully!',
            ]);

        } catch (\Exception $e) {
            return back()->withErrors([
                'employeeID' => 'Failed to record ' . $type . '. Please try again.',
            ]);
        }
    }
}