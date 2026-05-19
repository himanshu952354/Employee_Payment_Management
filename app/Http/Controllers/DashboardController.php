<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dynamic dashboard (bifurcated by role).
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Employee Portal Bifurcation
        if ($user->role === 'employee') {
            $employee = Employee::find($user->employee_id);
            if (!$employee) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Linked employee profile not found in directory.');
            }

            // Attendance Stats
            $totalDays = Attendance::where('employee_id', $employee->id)->count();
            $present = Attendance::where('employee_id', $employee->id)->where('status', 'Present')->count();
            $absent = Attendance::where('employee_id', $employee->id)->where('status', 'Absent')->count();
            $leave = Attendance::where('employee_id', $employee->id)->where('status', 'Leave')->count();

            $attendanceRate = $totalDays > 0 ? round(($present / $totalDays) * 100) : 0;
            $attendanceStats = ['present' => $present, 'absent' => $absent, 'leave' => $leave];

            // Personal Payroll Billing History
            $payrolls = Payroll::where('employee_id', $employee->id)
                ->orderBy('month', 'desc')
                ->get();

            return view('employee.dashboard', compact('employee', 'attendanceRate', 'attendanceStats', 'payrolls'));
        }

        // 2. Administrative Boss Portal
        $currentMonth = Carbon::now()->format('Y-m');
        $currentMonthLabel = Carbon::now()->format('F Y');
        $companyName = $user->company_name;
        $companyEmployeeIds = Employee::where('company_name', $companyName)->pluck('id');

        // Metric Cards
        $totalEmployees = Employee::where('company_name', $companyName)->where('status', 'Active')->count();
        
        $totalPaidThisMonth = Payroll::where('month', $currentMonth)
            ->whereIn('employee_id', $companyEmployeeIds)
            ->where('status', 'Paid')
            ->sum('net_salary');

        $totalPendingThisMonth = Payroll::where('month', $currentMonth)
            ->whereIn('employee_id', $companyEmployeeIds)
            ->where('status', 'Pending')
            ->sum('net_salary');

        // Latest attendance date and rate
        $latestAttendanceDate = Attendance::whereIn('employee_id', $companyEmployeeIds)->orderBy('date', 'desc')->value('date');
        $latestAttendanceDate = $latestAttendanceDate ? Carbon::parse($latestAttendanceDate) : null;
        $attendanceRate = 0;
        
        if ($latestAttendanceDate) {
            $latestAttendanceDateStr = $latestAttendanceDate->toDateString();
            $totalAttendance = Attendance::where('date', $latestAttendanceDateStr)
                ->whereIn('employee_id', $companyEmployeeIds)
                ->count();
            if ($totalAttendance > 0) {
                $presentCount = Attendance::where('date', $latestAttendanceDateStr)
                    ->whereIn('employee_id', $companyEmployeeIds)
                    ->where('status', 'Present')
                    ->count();
                $attendanceRate = round(($presentCount / $totalAttendance) * 100);
            }
        }

        // Chart data: Payout trends (last 6 months)
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $chartLabels[] = $date->format('M Y');
            
            $monthSum = Payroll::where('month', $monthKey)
                ->whereIn('employee_id', $companyEmployeeIds)
                ->where('status', 'Paid')
                ->sum('net_salary');
            $chartData[] = (float) $monthSum;
        }

        // Department breakdown for doughnut chart
        $departments = Employee::where('company_name', $companyName)
            ->select('department')
            ->selectRaw('count(*) as count')
            ->groupBy('department')
            ->get();
        
        $deptLabels = $departments->pluck('department')->toArray();
        $deptCounts = $departments->pluck('count')->toArray();

        // Recent Transactions
        $recentTransactions = Transaction::whereHas('payroll.employee', function($q) use ($companyName) {
                $q->where('company_name', $companyName);
            })
            ->with('payroll.employee')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalEmployees',
            'totalPaidThisMonth',
            'totalPendingThisMonth',
            'attendanceRate',
            'latestAttendanceDate',
            'currentMonthLabel',
            'chartLabels',
            'chartData',
            'deptLabels',
            'deptCounts',
            'recentTransactions'
        ));
    }
}
