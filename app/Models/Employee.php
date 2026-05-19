<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Services\MongoDBService;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'employee_id',
        'name',
        'email',
        'phone',
        'department',
        'designation',
        'salary',
        'bank_name',
        'account_number',
        'join_date',
        'status',
        'company_name',
    ];

    protected $casts = [
        'join_date' => 'date',
        'salary'    => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    // ── Auto UUID + MongoDB Sync ──────────────────────────────────────────

    protected static function booted(): void
    {
        // Assign a stable UUID before creating so MongoDB can identify the record
        static::creating(function (Employee $employee) {
            if (empty($employee->uuid)) {
                $employee->uuid = (string) Str::uuid();
            }
        });

        // Push to MongoDB whenever an employee is created or updated
        static::saved(function (Employee $employee) {
            $employee->syncToMongoDB();
        });

        // Remove from MongoDB when deleted
        static::deleted(function (Employee $employee) {
            try {
                $mongo = new MongoDBService();
                $mongo->selectCollection('employees')->deleteOne(['uuid' => $employee->uuid]);
            } catch (\Exception $e) {
                \Log::error('MongoDB Employee Delete Failed: ' . $e->getMessage());
            }
        });
    }

    public function syncToMongoDB(): void
    {
        try {
            $mongo   = new MongoDBService();
            $col     = $mongo->selectCollection('employees');
            $existing = $col->findOne(['uuid' => $this->uuid]);

            $data = [
                'uuid'           => $this->uuid,
                'employee_id'    => $this->employee_id,
                'name'           => $this->name,
                'email'          => $this->email,
                'phone'          => $this->phone,
                'department'     => $this->department,
                'designation'    => $this->designation,
                'salary'         => (float) $this->salary,
                'bank_name'      => $this->bank_name,
                'account_number' => $this->account_number,
                'join_date'      => $this->join_date?->toDateString(),
                'status'         => $this->status,
                'company_name'   => $this->company_name,
                'updated_at'     => now()->toIso8601String(),
            ];

            if ($existing) {
                $col->updateOne(['uuid' => $this->uuid], ['$set' => $data]);
            } else {
                $data['created_at'] = now()->toIso8601String();
                $col->insertOne($data);
            }
        } catch (\Exception $e) {
            \Log::error('MongoDB Employee Sync Failed: ' . $e->getMessage());
        }
    }
}
