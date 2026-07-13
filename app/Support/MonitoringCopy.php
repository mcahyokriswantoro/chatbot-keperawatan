<?php

namespace App\Support;

class MonitoringCopy
{
    public static function severityHint(): string
    {
        return 'Pilihan: Tidak ada, Ringan, Sedang, Berat · Skor: 0, 1, 2, 3 · Total skor = jumlah semua gejala.';
    }

    public static function complaintsSectionSubtitle(string $label): string
    {
        return "Mari kita catat keluhan {$label} hari ini. Untuk setiap pertanyaan, pilih: Tidak ada, Ringan, Sedang, atau Berat.";
    }

    public static function complaintsIntro(string $label): string
    {
        return "Halo! Mari kita catat keluhan {$label} hari ini. Saya akan menanyakan beberapa gejala satu per satu. Untuk setiap gejala, pilih: Tidak ada, Ringan, Sedang, atau Berat. Siap? Klik Lanjut untuk mulai.";
    }

    public static function selfManagementSectionSubtitle(string $risk): string
    {
        return "Mari evaluasi Self management Anda (risiko {$risk}). Untuk setiap pertanyaan, pilih: Tidak atau Ya.";
    }

    public static function relapseSectionSubtitle(): string
    {
        return 'Dalam bulan ini, seberapa sering kondisi Anda memburuk? Pilih salah satu jawaban di bawah.';
    }

    public static function selfManagementIntro(string $risk): string
    {
        return "Mari evaluasi Self management Anda (risiko {$risk}). Jawab jujur ya — ini membantu kami memantau perkembangan Anda.";
    }

    public static function selfManagementPrompt(string $item): string
    {
        $item = trim(rtrim($item, '.'));

        if (str_contains($item, '?')) {
            return $item;
        }

        if (str_starts_with($item, 'Keluarga ')) {
            return 'Apakah '.lcfirst($item).'?';
        }

        if (str_starts_with($item, 'Tidak ')) {
            return 'Apakah hari ini Anda '.$item.'?';
        }

        return 'Apakah hari ini Anda '.lcfirst($item).'?';
    }
}
