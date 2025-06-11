<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DtrService;
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
        $employeeData = $this->dtrService->checkEmployee($employeeID);

        if (!$employeeData['employee']) {
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
        if ($this->dtrService->checkLatestDTR($employeeID)) {
            return redirect()->back()->with('success', 'DTR successfully added for today.');
        }
        return redirect()->back()->withErrors(['error' => 'DTR already exists for today.']);
    }
}
