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

    public function getSchedules(Request $request)
    {
        $employeeID = $request->input('employeeID');
        $timezone = $request->input('timezone');

        $employeeData = $this->dtrService->checkEmployee($employeeID, $timezone);
        if (!$employeeData) {
            return response()->json([
                'success' => false,
                'message' => "Employee not found",
            ], 404);
        }

        $schedules = $this->dtrService->getSchedules($employeeID, $timezone);
        if (!$schedules) {
            return response()->json([
                'success' => false,
                'message' => "No schedules found for Employee ID: {$employeeID}",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'employeeData' => $employeeData,
            'schedules' => $schedules
        ]);
    }


    public function addDtr(Request $request)
    {
        $employeeID = $request->input('employee_id');
        $dtrDate = $request->input('dtrDate');
        $this->dtrService->logDTR($employeeID, $dtrDate);
        return redirect()->back()->with('success', 'DTR successfully added for today.');
    }
}
