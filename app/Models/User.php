<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'company_name', 'employee_id', 'departments', 'currency'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'departments' => 'array',
        ];
    }

    /**
     * Get associated employee dossier if this user is registered as an employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Resolve the currency symbol dynamically for admin or linked employee.
     */
    public function getCurrencySymbolAttribute()
    {
        if ($this->role === 'admin') {
            return $this->currency ?? '$';
        }

        // For employees, resolve from their company admin user
        return cache()->remember("company_currency_{$this->company_name}", 3600, function () {
            return self::where('role', 'admin')
                ->where('company_name', $this->company_name)
                ->value('currency') ?? '$';
        });
    }
}
