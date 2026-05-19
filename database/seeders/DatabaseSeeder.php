<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        User::create([
            'name' => 'Himanshu Shekhar',
            'email' => 'admin@payflow.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'company_name' => 'PayFlow Enterprise',
        ]);

        // 2. Create Mock Employees
        $employeesData = [
            [
                'employee_id' => 'EMP-1001',
                'name' => 'Himanshu Shekhar',
                'email' => 'himanshu@payflow.com',
                'phone' => '+91 98765 43210',
                'department' => 'Engineering',
                'designation' => 'Lead Software Architect',
                'salary' => 9500.00,
                'bank_name' => 'State Bank of India',
                'account_number' => 'SBI9876543210',
                'join_date' => '2024-01-15',
                'status' => 'Active',
            ],
            [
                'employee_id' => 'EMP-1002',
                'name' => 'MD Irfan',
                'email' => 'irfan@payflow.com',
                'phone' => '+91 98765 43211',
                'department' => 'Engineering',
                'designation' => 'Senior DevOps Engineer',
                'salary' => 8000.00,
                'bank_name' => 'HDFC Bank',
                'account_number' => 'HDF8765432109',
                'join_date' => '2024-03-10',
                'status' => 'Active',
            ],
            [
                'employee_id' => 'EMP-1003',
                'name' => 'Mannat',
                'email' => 'mannat@payflow.com',
                'phone' => '+91 98765 43212',
                'department' => 'Marketing',
                'designation' => 'Growth & Marketing Lead',
                'salary' => 7500.00,
                'bank_name' => 'ICICI Bank',
                'account_number' => 'ICI7654321098',
                'join_date' => '2024-06-01',
                'status' => 'Active',
            ],
            [
                'employee_id' => 'EMP-1004',
                'name' => 'Sophia Rodriguez',
                'email' => 'sophia@payflow.com',
                'phone' => '+1 (555) 234-5678',
                'department' => 'HR',
                'designation' => 'Human Resources Director',
                'salary' => 6500.00,
                'bank_name' => 'Chase Bank',
                'account_number' => 'CHS6543210987',
                'join_date' => '2024-08-20',
                'status' => 'Active',
            ],
            [
                'employee_id' => 'EMP-1005',
                'name' => 'Alex Chen',
                'email' => 'alex@payflow.com',
                'phone' => '+1 (555) 345-6789',
                'department' => 'Sales',
                'designation' => 'Enterprise Sales Lead',
                'salary' => 7200.00,
                'bank_name' => 'Bank of America',
                'account_number' => 'BOA5432109876',
                'join_date' => '2025-01-10',
                'status' => 'Active',
            ],
            [
                'employee_id' => 'EMP-1006',
                'name' => 'Sarah Jenkins',
                'email' => 'sarah@payflow.com',
                'phone' => '+1 (555) 456-7890',
                'department' => 'Finance',
                'designation' => 'Financial Analyst',
                'salary' => 6800.00,
                'bank_name' => 'Wells Fargo',
                'account_number' => 'WFG4321098765',
                'join_date' => '2025-02-15',
                'status' => 'Active',
            ],
            [
                'employee_id' => 'EMP-1007',
                'name' => 'Kabir Malhotra',
                'email' => 'kabir@payflow.com',
                'phone' => '+91 98765 43213',
                'department' => 'Marketing',
                'designation' => 'UI/UX Designer',
                'salary' => 5800.00,
                'bank_name' => 'Axis Bank',
                'account_number' => 'AXI3210987654',
                'join_date' => '2025-03-01',
                'status' => 'Active',
            ],
        ];

        $employees = [];
        foreach ($employeesData as $data) {
            $employee = Employee::create($data);
            $employees[] = $employee;

            // 2.5 Create corresponding Employee User Login
            User::create([
                'name' => $employee->name,
                'email' => $employee->email,
                'password' => Hash::make('password'),
                'role' => 'employee',
                'employee_id' => $employee->id,
            ]);
        }

        // 3. Create Attendances (April 2026 and May 2026)
        $startDate = Carbon::create(2026, 4, 1);
        $endDate = Carbon::create(2026, 5, 18);

        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            if ($currentDate->isWeekday()) {
                foreach ($employees as $employee) {
                    $rand = rand(1, 100);
                    if ($rand <= 92) {
                        $status = 'Present';
                    } elseif ($rand <= 96) {
                        $status = 'Leave';
                    } else {
                        $status = 'Absent';
                    }

                    Attendance::create([
                        'employee_id' => $employee->id,
                        'date' => $currentDate->toDateString(),
                        'status' => $status,
                    ]);
                }
            }
            $currentDate->addDay();
        }

        // 4. Create Payroll and Transactions for April 2026
        foreach ($employees as $employee) {
            $bonus = rand(150, 500);
            $deductions = rand(50, 150);
            $netSalary = $employee->salary + $bonus - $deductions;

            $payroll = Payroll::create([
                'employee_id' => $employee->id,
                'month' => '2026-04',
                'basic_salary' => $employee->salary,
                'bonus' => $bonus,
                'deductions' => $deductions,
                'net_salary' => $netSalary,
                'status' => 'Paid',
                'processed_at' => Carbon::create(2026, 4, 30, 10, 0, 0),
            ]);

            Transaction::create([
                'payroll_id' => $payroll->id,
                'transaction_number' => 'TXN-' . strtoupper(Str::random(12)),
                'amount' => $netSalary,
                'payment_method' => 'Bank Transfer',
                'status' => 'Success',
                'notes' => 'Salary payout for April 2026',
                'created_at' => Carbon::create(2026, 4, 30, 10, 5, 0),
            ]);
        }

        // 5. Create Payroll and Transactions for May 2026
        foreach ($employees as $index => $employee) {
            $bonus = rand(200, 600);
            $deductions = rand(80, 200);
            $netSalary = $employee->salary + $bonus - $deductions;

            if ($index < 4) {
                $payroll = Payroll::create([
                    'employee_id' => $employee->id,
                    'month' => '2026-05',
                    'basic_salary' => $employee->salary,
                    'bonus' => $bonus,
                    'deductions' => $deductions,
                    'net_salary' => $netSalary,
                    'status' => 'Paid',
                    'processed_at' => Carbon::create(2026, 5, 15, 11, 30, 0),
                ]);

                Transaction::create([
                    'payroll_id' => $payroll->id,
                    'transaction_number' => 'TXN-' . strtoupper(Str::random(12)),
                    'amount' => $netSalary,
                    'payment_method' => 'Bank Transfer',
                    'status' => 'Success',
                    'notes' => 'Salary payout for May 2026',
                    'created_at' => Carbon::create(2026, 5, 15, 11, 32, 0),
                ]);
            } elseif ($index == 4) {
                Payroll::create([
                    'employee_id' => $employee->id,
                    'month' => '2026-05',
                    'basic_salary' => $employee->salary,
                    'bonus' => $bonus,
                    'deductions' => $deductions,
                    'net_salary' => $netSalary,
                    'status' => 'Pending',
                    'processed_at' => null,
                ]);
            } elseif ($index == 5) {
                $payroll = Payroll::create([
                    'employee_id' => $employee->id,
                    'month' => '2026-05',
                    'basic_salary' => $employee->salary,
                    'bonus' => $bonus,
                    'deductions' => $deductions,
                    'net_salary' => $netSalary,
                    'status' => 'Failed',
                    'processed_at' => Carbon::create(2026, 5, 16, 14, 0, 0),
                ]);

                Transaction::create([
                    'payroll_id' => $payroll->id,
                    'transaction_number' => 'TXN-' . strtoupper(Str::random(12)),
                    'amount' => $netSalary,
                    'payment_method' => 'Bank Transfer',
                    'status' => 'Failed',
                    'notes' => 'Transaction failed by bank: Insufficient clearing funds or invalid routing',
                    'created_at' => Carbon::create(2026, 5, 16, 14, 1, 0),
                ]);
            }
        }
    }
}
