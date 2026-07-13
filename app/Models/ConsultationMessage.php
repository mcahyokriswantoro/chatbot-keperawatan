<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class ConsultationMessage extends Model
{
    public const SENDER_USER = 'user';

    public const SENDER_PROVIDER = 'provider';

    public const SENDER_SYSTEM = 'system';

    protected $fillable = [
        'consultation_order_id',
        'user_id',
        'provider_key',
        'sender_type',
        'sender_user_id',
        'body',
        'read_by_user_at',
        'read_by_provider_at',
        'notified_provider',
    ];

    protected function casts(): array
    {
        return [
            'read_by_user_at' => 'datetime',
            'read_by_provider_at' => 'datetime',
            'notified_provider' => 'boolean',
        ];
    }

    public static function tableReady(): bool
    {
        return Schema::hasTable('consultation_messages');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(ConsultationOrder::class, 'consultation_order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function isFromUser(): bool
    {
        return $this->sender_type === self::SENDER_USER;
    }

    public function isFromProvider(): bool
    {
        return $this->sender_type === self::SENDER_PROVIDER;
    }

    /**
     * @return array<string, mixed>
     */
    public function toChatArray(): array
    {
        return [
            'id' => $this->id,
            'role' => match ($this->sender_type) {
                self::SENDER_USER => 'user',
                self::SENDER_SYSTEM => 'system',
                default => 'provider',
            },
            'text' => $this->body,
            'sender_name' => $this->isFromProvider()
                ? ($this->sender?->name ?? 'Tenaga kesehatan')
                : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
