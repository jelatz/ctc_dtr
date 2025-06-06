<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DtrService;

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
        $employee = $this->dtrService->checkEmployee($employeeID);

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $employee,
        ]);
    }
}
