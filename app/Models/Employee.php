<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    // ── Auto UUID ──────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        // Assign a stable UUID before creating
        static::creating(function (Employee $employee) {
            if (empty($employee->uuid)) {
                $employee->uuid = (string) Str::uuid();
            }
        });
    }
}
