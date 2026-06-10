<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'gender',
        'phone',
        'date_of_birth',
        'age',
        'weight',
        'height',
        'address',
        'occupation',
        'profile_photo',
        'password',
        'is_admin',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'weight' => 'decimal:1',
            'height' => 'decimal:1',
        ];
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isFemale(): bool
    {
        return in_array($this->gender, ['perempuan', 'female', 'P'], true);
    }

    public function profilePhotoUrl(): string
    {
        if ($this->profile_photo && Storage::disk('public')->exists($this->profile_photo)) {
            return Storage::disk('public')->url($this->profile_photo);
        }

        return asset($this->isFemale() ? 'images/avatars/female.svg' : 'images/avatars/male.svg');
    }

    public function genderLabel(): ?string
    {
        return match ($this->gender) {
            'male', 'laki-laki', 'L' => 'Laki-laki',
            'female', 'perempuan', 'P' => 'Perempuan',
            default => $this->gender ? ucfirst($this->gender) : null,
        };
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
