<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Handle global AJAX instant search query.
     */
    public function query(Request $request)
    {
        $q = trim($request->input('q'));
        
        if (strlen($q) < 2) {
            return response()->json([
                'employees' => [],
                'announcements' => [],
                'payrolls' => []
            ]);
        }

        $currentUser = Auth::user();

        // 1. Query Employees matching the search query within the same company (Admins see all, Employees only see self)
        if ($currentUser->role === 'admin') {
            $employees = Employee::where('company_name', $currentUser->company_name)
                ->where(function($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%")
                          ->orWhere('department', 'like', "%{$q}%")
                          ->orWhere('designation', 'like', "%{$q}%")
                          ->orWhere('employee_id', 'like', "%{$q}%");
                })
                ->limit(5)
                ->get();
        } else {
            $employees = Employee::where('company_name', $currentUser->company_name)
                ->where('id', $currentUser->employee_id)
                ->where(function($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%")
                          ->orWhere('department', 'like', "%{$q}%")
                          ->orWhere('designation', 'like', "%{$q}%")
                          ->orWhere('employee_id', 'like', "%{$q}%");
                })
                ->limit(1)
                ->get();
        }

        // 2. Query Broadcast announcements matching the query within the same company
        $announcements = Message::where('is_broadcast', true)
            ->where('message', 'like', "%{$q}%")
            ->whereHas('sender', function($query) use ($currentUser) {
                $query->where('company_name', $currentUser->company_name);
            })
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Parse month name to numerical format for YYYY-MM database matching
        $qLower = strtolower($q);
        $monthQueryNum = null;
        $monthsMap = [
            'january'   => '01', 'jan' => '01',
            'february'  => '02', 'feb' => '02',
            'march'     => '03', 'mar' => '03',
            'april'     => '04', 'apr' => '04',
            'may'       => '05',
            'june'      => '06', 'jun' => '06',
            'july'      => '07', 'jul' => '07',
            'august'    => '08', 'aug' => '08',
            'september' => '09', 'sep' => '09',
            'october'   => '10', 'oct' => '10',
            'november'  => '11', 'nov' => '11',
            'december'  => '12', 'dec' => '12',
        ];

        foreach ($monthsMap as $name => $num) {
            if (str_contains($qLower, $name)) {
                $monthQueryNum = $num;
                break;
            }
        }

        // 3. Query Salary Slips / Payroll matching months, amounts, status
        if ($currentUser->role === 'employee') {
            // Employees only search their own personal payroll list
            $payrolls = Payroll::where('employee_id', $currentUser->employee_id)
                ->where(function($query) use ($q, $monthQueryNum) {
                    $query->where('month', 'like', "%{$q}%")
                          ->orWhere('status', 'like', "%{$q}%")
                          ->orWhere('net_salary', 'like', "%{$q}%")
                          ->orWhere('basic_salary', 'like', "%{$q}%")
                          ->orWhere('bonus', 'like', "%{$q}%")
                          ->orWhere('deductions', 'like', "%{$q}%");
                          
                    if ($monthQueryNum) {
                        $query->orWhere('month', 'like', "%-{$monthQueryNum}%");
                    }
                })
                ->limit(5)
                ->get();
        } else {
            // Admins can search across all company employee payrolls
            $payrolls = Payroll::whereHas('employee', function($query) use ($currentUser) {
                $query->where('company_name', $currentUser->company_name);
            })
            ->where(function($query) use ($q, $monthQueryNum) {
                $query->where('month', 'like', "%{$q}%")
                      ->orWhere('status', 'like', "%{$q}%")
                      ->orWhere('net_salary', 'like', "%{$q}%")
                      ->orWhereHas('employee', function($subQuery) use ($q) {
                          $subQuery->where('name', 'like', "%{$q}%");
                      });
                      
                if ($monthQueryNum) {
                    $query->orWhere('month', 'like', "%-{$monthQueryNum}%");
                }
            })
            ->with('employee')
            ->limit(5)
            ->get();
        }

        // Format employee list to include deep link permissions
        $formattedEmployees = $employees->map(function($emp) use ($currentUser) {
            return [
                'id' => $emp->id,
                'name' => $emp->name,
                'email' => $emp->email,
                'designation' => $emp->designation,
                'department' => $emp->department,
                'url' => $currentUser->role === 'admin' 
                    ? route('employees.show', $emp->id) 
                    : '#'
            ];
        });

        // Format announcement list to snippet
        $formattedAnnouncements = $announcements->map(function($msg) {
            return [
                'id' => $msg->id,
                'sender' => $msg->sender->name,
                'sender_role' => $msg->sender->role === 'admin' ? 'Admin' : 'Employee',
                'snippet' => strlen($msg->message) > 40 ? substr($msg->message, 0, 40) . '...' : $msg->message,
                'created_at' => $msg->created_at->format('M d'),
                'url' => route('chat.index')
            ];
        });

        // Format salary slips list
        $formattedPayrolls = $payrolls->map(function($pay) use ($currentUser) {
            return [
                'id' => $pay->id,
                'employee_name' => $pay->employee->name ?? 'N/A',
                'month' => $pay->month,
                'net_salary' => number_format($pay->net_salary, 2),
                'currency' => $currentUser->currency_symbol,
                'status' => ucfirst($pay->status),
                'url' => route('payroll.slip', $pay->id)
            ];
        });

        return response()->json([
            'employees' => $formattedEmployees,
            'announcements' => $formattedAnnouncements,
            'payrolls' => $formattedPayrolls
        ]);
    }
}
