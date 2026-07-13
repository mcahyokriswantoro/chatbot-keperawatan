<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

final class AppTimezone
{
    public static function name(): string
    {
        return (string) config('app.timezone', 'Asia/Jakarta');
    }

    public static function offset(): string
    {
        return now()->format('P');
    }

    /**
     * Tanggal aktivitas monitoring: recorded_at (kalender) atau created_at di zona app.
     */
    public static function monitoringActivityDateSql(): string
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $offset = self::offset();

            return "COALESCE(recorded_at, DATE(CONVERT_TZ(created_at, '+00:00', '{$offset}')))";
        }

        return 'DATE(COALESCE(recorded_at, created_at))';
    }

    /**
     * Tanggal dari timestamp UTC (mis. created_at skrining) di zona app.
     */
    public static function sqlDateFromUtcTimestamp(string $column): string
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $offset = self::offset();

            return "DATE(CONVERT_TZ({$column}, '+00:00', '{$offset}'))";
        }

        return "DATE({$column})";
    }
}
