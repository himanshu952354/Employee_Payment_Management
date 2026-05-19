<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Services\MongoDBService;

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

    // ── Auto UUID + MongoDB Sync ──────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Attendance $attendance) {
            if (empty($attendance->uuid)) {
                $attendance->uuid = (string) Str::uuid();
            }
        });

        static::saved(function (Attendance $attendance) {
            $attendance->syncToMongoDB();
        });

        static::deleted(function (Attendance $attendance) {
            try {
                $mongo = new MongoDBService();
                $mongo->selectCollection('attendances')->deleteOne(['uuid' => $attendance->uuid]);
            } catch (\Exception $e) {
                \Log::error('MongoDB Attendance Delete Failed: ' . $e->getMessage());
            }
        });
    }

    public function syncToMongoDB(): void
    {
        try {
            $mongo    = new MongoDBService();
            $col      = $mongo->selectCollection('attendances');
            $existing = $col->findOne(['uuid' => $this->uuid]);

            $employeeUuid = $this->employee?->uuid;

            $data = [
                'uuid'          => $this->uuid,
                'employee_uuid' => $employeeUuid,
                'employee_id'   => $this->employee_id,
                'date'          => $this->date,
                'status'        => $this->status,
                'updated_at'    => now()->toIso8601String(),
            ];

            if ($existing) {
                $col->updateOne(['uuid' => $this->uuid], ['$set' => $data]);
            } else {
                $data['created_at'] = now()->toIso8601String();
                $col->insertOne($data);
            }
        } catch (\Exception $e) {
            \Log::error('MongoDB Attendance Sync Failed: ' . $e->getMessage());
        }
    }
}
