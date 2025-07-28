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

    public function index()
    {
        return Inertia::render('Home');
    }

    public function getEmployeeAndSchedules(Request $request)
    {
        $result = $this->dtrService->getEmployeeSchedules($request);

        return to_route('home')->with([
            'employeeData' => $result['employeeData'],
            'schedules' => $result['schedules'],
            'showModal' => true,
        ]);
    }

    public function addDtr(Request $request)
    {
        $employeeID = $request->input('employee_id');
        $dtrDate = $request->input('dtrDate');

        $result = $this->dtrService->logDTR($employeeID, $dtrDate);
        // 'message' => "You already have logged in for today's shift. Data has been stored in logs."

        if ($result && !$result['success'] && $result['error_type'] === 'dtr_exists') {
            return redirect()->back()->with([
                'error' => "You already have logged in for today's shift. Data has been stored in logs."
            ]);
        }

        return redirect()->back()->with([
            'success' => 'DTR confirmed successfully!'
        ]);
    }
}
