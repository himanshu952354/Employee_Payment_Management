<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    // ── Auto UUID ──────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Payroll $payroll) {
            if (empty($payroll->uuid)) {
                $payroll->uuid = (string) Str::uuid();
            }
        });
    }
}
