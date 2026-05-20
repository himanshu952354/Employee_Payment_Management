<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'employee_id',
        'date',
        'status',
    ];

    protected $casts = [
        'date' => 'string',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // ── Auto UUID ──────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Attendance $attendance) {
            if (empty($attendance->uuid)) {
                $attendance->uuid = (string) Str::uuid();
            }
        });
    }
}
