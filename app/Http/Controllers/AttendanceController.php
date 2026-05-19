<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display attendance sheet for a specific date.
     */
    public function index(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date)->toDateString() : Carbon::now()->toDateString();
        
        $employees = Employee::where('company_name', auth()->user()->company_name)
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();
        
        // Fetch already marked attendance for this date
        $markedAttendance = Attendance::where('date', $date)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->keyBy('employee_id');

        return view('attendance.index', compact('employees', 'date', 'markedAttendance'));
    }

    /**
     * Save daily attendance roster.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'attendance' => 'required|array',
            'attendance.*' => 'required|string|in:Present,Absent,Leave',
        ]);

        $date = Carbon::parse($request->date)->toDateString();
        $companyEmployeeIds = Employee::where('company_name', auth()->user()->company_name)->pluck('id')->toArray();

        foreach ($request->attendance as $employeeId => $status) {
            if (in_array($employeeId, $companyEmployeeIds)) {
                Attendance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'date' => $date,
                    ],
                    [
                        'status' => $status,
                    ]
                );
            }
        }

        return redirect()->route('attendance.index', ['date' => $date])
            ->with('success', 'Attendance roster updated successfully for ' . Carbon::parse($date)->format('M d, Y') . '.');
    }
}
