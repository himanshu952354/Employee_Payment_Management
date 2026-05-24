<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $query = Employee::where('company_name', auth()->user()->company_name);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        // Filter by Department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('employee_id', 'asc')->paginate(10);
        
        // Departments list for filter dropdown
        $admin = auth()->user();
        $departments = ($admin && $admin->role === 'admin' && is_array($admin->departments) && count($admin->departments) > 0)
            ? $admin->departments
            : ['Engineering', 'Marketing', 'HR', 'Finance', 'Sales'];

        return view('employees.index', compact('employees', 'departments'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'department' => 'required|string',
            'designation' => 'required|string',
            'salary' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'join_date' => 'required|date',
            'password' => 'required|string|min:6',
        ]);

        // Generate unique employee ID e.g., EMP-1008
        $lastEmp = Employee::orderBy('employee_id', 'desc')->first();
        if ($lastEmp && preg_match('/EMP-(\d+)/', $lastEmp->employee_id, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
            $employeeId = 'EMP-' . $nextNumber;
        } else {
            $employeeId = 'EMP-1001';
        }

        $employee = Employee::create(array_merge(
            $request->all(),
            [
                'employee_id' => $employeeId,
                'status' => 'Active',
                'company_name' => auth()->user()->company_name,
            ]
        ));

        // Auto-provision User login credentials for the employee
        User::create([
            'name' => $employee->name,
            'email' => $employee->email,
            'password' => $request->password, // Custom password, auto-hashed by model cast!
            'role' => 'employee',
            'company_name' => $employee->company_name,
            'employee_id' => $employee->id,
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee registered successfully with ID: ' . $employeeId . ' and secure User credentials provisioned.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        if ($employee->company_name !== auth()->user()->company_name) {
            abort(403, 'Unauthorized access to employee dossier.');
        }

        $payrolls = $employee->payrolls()->orderBy('month', 'desc')->get();
        
        // Get attendance statistics for this employee
        $attendanceStats = [
            'present' => $employee->attendances()->where('status', 'Present')->count(),
            'absent' => $employee->attendances()->where('status', 'Absent')->count(),
            'leave' => $employee->attendances()->where('status', 'Leave')->count(),
        ];
        
        $totalDays = array_sum($attendanceStats);
        $attendanceRate = $totalDays > 0 ? round(($attendanceStats['present'] / $totalDays) * 100) : 0;

        $admin = auth()->user();
        $departments = ($admin && $admin->role === 'admin' && is_array($admin->departments) && count($admin->departments) > 0)
            ? $admin->departments
            : ['Engineering', 'Marketing', 'HR', 'Finance', 'Sales'];

        return view('employees.show', compact('employee', 'payrolls', 'attendanceStats', 'attendanceRate', 'departments'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee)
    {
        if ($employee->company_name !== auth()->user()->company_name) {
            abort(403, 'Unauthorized access.');
        }

        $userId = User::where('employee_id', $employee->id)->value('id');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id . '|unique:users,email,' . ($userId ?? 'NULL'),
            'phone' => 'nullable|string|max:20',
            'department' => 'required|string',
            'designation' => 'required|string',
            'salary' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'join_date' => 'required|date',
            'status' => 'required|string|in:Active,Inactive',
            'password' => 'nullable|string|min:6',
        ]);

        $employee->update($request->all());

        // Synchronize linked User account details
        $user = User::where('employee_id', $employee->id)->first();
        if ($user) {
            $user->name = $employee->name;
            $user->email = $employee->email;
            if ($request->filled('password')) {
                $user->password = $request->password;
            }
            $user->save();
        }

        return redirect()->route('employees.show', $employee->id)->with('success', 'Employee details updated successfully and User credentials synchronized.');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee)
    {
        if ($employee->company_name !== auth()->user()->company_name) {
            abort(403, 'Unauthorized access.');
        }

        // Remove linked User login credentials
        User::where('employee_id', $employee->id)->delete();

        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee record and matching User credentials removed successfully.');
    }
}
