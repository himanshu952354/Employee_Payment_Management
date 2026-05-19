<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Services\MongoDBService;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'employee_id',
        'month',
        'basic_salary',
        'bonus',
        'deductions',
        'net_salary',
        'status',
        'processed_at',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'bonus'        => 'decimal:2',
        'deductions'   => 'decimal:2',
        'net_salary'   => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    // ── Auto UUID + MongoDB Sync ──────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Payroll $payroll) {
            if (empty($payroll->uuid)) {
                $payroll->uuid = (string) Str::uuid();
            }
        });

        static::saved(function (Payroll $payroll) {
            $payroll->syncToMongoDB();
        });

        static::deleted(function (Payroll $payroll) {
            try {
                $mongo = new MongoDBService();
                $mongo->selectCollection('payrolls')->deleteOne(['uuid' => $payroll->uuid]);
            } catch (\Exception $e) {
                \Log::error('MongoDB Payroll Delete Failed: ' . $e->getMessage());
            }
        });
    }

    public function syncToMongoDB(): void
    {
        try {
            $mongo    = new MongoDBService();
            $col      = $mongo->selectCollection('payrolls');
            $existing = $col->findOne(['uuid' => $this->uuid]);

            // Store the employee's UUID so the restore command can re-link records
            $employeeUuid = $this->employee?->uuid;

            $data = [
                'uuid'          => $this->uuid,
                'employee_uuid' => $employeeUuid,
                'employee_id'   => $this->employee_id,
                'month'         => $this->month,
                'basic_salary'  => (float) $this->basic_salary,
                'bonus'         => (float) $this->bonus,
                'deductions'    => (float) $this->deductions,
                'net_salary'    => (float) $this->net_salary,
                'status'        => $this->status,
                'processed_at'  => $this->processed_at?->toIso8601String(),
                'updated_at'    => now()->toIso8601String(),
            ];

            if ($existing) {
                $col->updateOne(['uuid' => $this->uuid], ['$set' => $data]);
            } else {
                $data['created_at'] = now()->toIso8601String();
                $col->insertOne($data);
            }
        } catch (\Exception $e) {
            \Log::error('MongoDB Payroll Sync Failed: ' . $e->getMessage());
        }
    }
}
