<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\DtrService;
use App\Services\UserService;
use Inertia\Inertia;

class DtrController extends Controller
{
    protected $dtrService;

    public function __construct(DtrService $dtrService)
    {
        $this->dtrService = $dtrService;
    }

    public function checkEmployee(Request $request)
    {
        $employeeID = $request->input('employeeID');
        $timezone = $request->input('timezone');

        // dd($employeeID, $timezone);

        $employeeData = $this->dtrService->checkEmployee($employeeID, $timezone);

        if (!$employeeData || !$employeeData['employee']) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'employeeData' => $employeeData,
        ]);
    }


    public function addDtr(Request $request)
    {
        $employeeID = $request->input('employee_id');
        $schedDate = $request->input('sched_date');
        if ($this->dtrService->checkLatestDTR($employeeID, $schedDate)) {
            return redirect()->back()->with('success', 'DTR successfully added for today.');
        }
        return redirect()->back()->withErrors(['error' => 'DTR already exists for today.']);
    }
}
