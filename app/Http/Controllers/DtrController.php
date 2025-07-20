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

    public function index()
    {
        return Inertia::render('Home');
    }

    public function getEmployeeAndSchedules(Request $request)
    {
        $errorMessage = ['employeeID.exists' => 'Employee ID not found.'];
        $data = $request->validate([
            'employeeID' => 'required|exists:users,employee_id',
        ], $errorMessage);

        $employeeID = $data['employeeID'];

        $result = $this->dtrService->getEmployeeSchedules($employeeID);

        return to_route('home')->with([
            'employeeData' => $result['employeeData'],
            'schedules' => $result['schedules'],
            'employeeID' => $employeeID,
            'showModal' => true,
        ]);
    }

    public function addDtr(Request $request)
    {
        $employeeID = $request->input('employee_id');
        $dtrDate = $request->input('dtrDate');

        $result = $this->dtrService->logDTR($employeeID, $dtrDate);

        if (!$result['success']) {
            return redirect()->back()->with([
                'error' => "You already have logged in for today's shift"
            ]);
        }

        return redirect()->back()->with([
            'success' => 'DTR confirmed successfully!'
        ]);
    }
}
