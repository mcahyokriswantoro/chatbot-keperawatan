<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use InvalidArgumentException;

class AdminAccessService
{
    public function grantByEmail(string $email): User
    {
        $email = Str::lower(trim($email));

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            throw new InvalidArgumentException('Email belum terdaftar. Pengguna harus daftar akun dulu.');
        }

        $updates = [];

        if (! $user->isAdmin()) {
            $updates['is_admin'] = true;
        }

        if ($user->email_verified_at === null) {
            $updates['email_verified_at'] = now();
        }

        if ($updates !== []) {
            $user->update($updates);
        }

        return $user->fresh();
    }

    public function revoke(User $user, User $actor): void
    {
        if (! $user->isAdmin()) {
            throw new InvalidArgumentException('Pengguna ini bukan admin.');
        }

        if ($user->is($actor)) {
            throw new InvalidArgumentException('Anda tidak bisa mencabut akses admin diri sendiri.');
        }

        if (User::query()->where('is_admin', true)->count() <= 1) {
            throw new InvalidArgumentException('Minimal harus ada satu admin aktif.');
        }

        $user->update(['is_admin' => false]);
    }

    public function grantProviderAccess(string $email, string $providerKey): User
    {
        $email = Str::lower(trim($email));

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            throw new InvalidArgumentException('Email belum terdaftar. Pengguna harus daftar akun dulu.');
        }

        if ($user->isAdmin()) {
            throw new InvalidArgumentException('Pengguna ini adalah Super Admin dan sudah memiliki semua akses.');
        }

        $user->update([
            'provider_key' => $providerKey,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        return $user->fresh();
    }

    public function revokeProviderAccess(User $user): void
    {
        $user->update(['provider_key' => null]);
    }
}
