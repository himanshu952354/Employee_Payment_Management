<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MongoDBService;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Attendance;

class RestoreFromMongoDB extends Command
{
    protected $signature = 'db:restore-from-mongo';
    protected $description = 'Restore employees, payrolls and attendance from MongoDB Atlas into local SQLite on container startup.';

    public function handle()
    {
        $this->info('🔄 Starting MongoDB → SQLite restore...');

        try {
            $mongo = new MongoDBService();

            // ── 1. Restore Employees ──────────────────────────────────────
            $this->info('  → Restoring employees...');
            $employees = $mongo->selectCollection('employees')->find();
            $employeeMap = []; // mongo_uuid → local SQLite id

            foreach ($employees as $emp) {
                // Use our custom "uuid" field as a stable cross-environment key
                $uuid = $emp['uuid'] ?? null;

                $local = $uuid
                    ? Employee::where('uuid', $uuid)->first()
                    : Employee::where('email', $emp['email'] ?? '')->first();

                $data = [
                    'uuid'           => $uuid,
                    'employee_id'    => $emp['employee_id'] ?? null,
                    'name'           => $emp['name'] ?? '',
                    'email'          => $emp['email'] ?? '',
                    'phone'          => $emp['phone'] ?? null,
                    'department'     => $emp['department'] ?? null,
                    'designation'    => $emp['designation'] ?? null,
                    'salary'         => $emp['salary'] ?? 0,
                    'bank_name'      => $emp['bank_name'] ?? null,
                    'account_number' => $emp['account_number'] ?? null,
                    'join_date'      => $emp['join_date'] ?? null,
                    'status'         => $emp['status'] ?? 'Active',
                    'company_name'   => $emp['company_name'] ?? null,
                ];

                if ($local) {
                    $local->update($data);
                } else {
                    $local = Employee::create($data);
                }

                if ($uuid) {
                    $employeeMap[$uuid] = $local->id;
                }
            }
            $this->info('     ✔ ' . count($employees) . ' employee(s) restored.');

            // ── 2. Restore Payrolls ────────────────────────────────────────
            $this->info('  → Restoring payrolls...');
            $payrolls = $mongo->selectCollection('payrolls')->find();

            foreach ($payrolls as $pay) {
                $uuid       = $pay['uuid'] ?? null;
                $empUuid    = $pay['employee_uuid'] ?? null;
                $localEmpId = $empUuid ? ($employeeMap[$empUuid] ?? null) : null;

                if (!$localEmpId) {
                    continue; // skip orphaned payroll
                }

                $data = [
                    'uuid'         => $uuid,
                    'employee_id'  => $localEmpId,
                    'month'        => $pay['month'] ?? null,
                    'basic_salary' => $pay['basic_salary'] ?? 0,
                    'bonus'        => $pay['bonus'] ?? 0,
                    'deductions'   => $pay['deductions'] ?? 0,
                    'net_salary'   => $pay['net_salary'] ?? 0,
                    'status'       => $pay['status'] ?? 'Pending',
                    'processed_at' => $pay['processed_at'] ?? null,
                ];

                $local = $uuid ? Payroll::where('uuid', $uuid)->first() : null;
                if ($local) {
                    $local->update($data);
                } else {
                    Payroll::create($data);
                }
            }
            $this->info('     ✔ ' . count($payrolls) . ' payroll(s) restored.');

            // ── 3. Restore Attendance ──────────────────────────────────────
            $this->info('  → Restoring attendance records...');
            $records = $mongo->selectCollection('attendances')->find();

            foreach ($records as $rec) {
                $uuid       = $rec['uuid'] ?? null;
                $empUuid    = $rec['employee_uuid'] ?? null;
                $localEmpId = $empUuid ? ($employeeMap[$empUuid] ?? null) : null;

                if (!$localEmpId) {
                    continue;
                }

                $data = [
                    'uuid'        => $uuid,
                    'employee_id' => $localEmpId,
                    'date'        => $rec['date'] ?? null,
                    'status'      => $rec['status'] ?? 'Present',
                ];

                $local = $uuid ? Attendance::where('uuid', $uuid)->first() : null;
                if ($local) {
                    $local->update($data);
                } else {
                    Attendance::create($data);
                }
            }
            $this->info('     ✔ ' . count($records) . ' attendance record(s) restored.');

            $this->info('✅ MongoDB → SQLite restore complete!');
        } catch (\Exception $e) {
            $this->error('❌ Restore failed: ' . $e->getMessage());
        }

        return 0;
    }
}
