<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin'])]
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
            'is_admin' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function screeningSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ScreeningSession::class);
    }

    public function healthMonitorings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HealthMonitoring::class);
    }

    public function selfManagementLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SelfManagementLog::class);
    }
}
