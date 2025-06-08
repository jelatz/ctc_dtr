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
        $data = [
            'employee_id' => $request->input('employeeID'),
            'user_id' => $request->input('userID'), // Assuming userID is passed in the request
            'date' => date('Y-m-d'),
            'time_in' => date('H:i:s'), // Or 'time_out' depending on logic
        ];

        $insertDtr = $this->dtrService->addDtr($data);

        if (!$insertDtr) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add DTR',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'DTR added successfully',
            'data' => $insertDtr,
        ]);
    }
}
