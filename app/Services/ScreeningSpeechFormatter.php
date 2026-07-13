<?php

namespace App\Services;

class ScreeningSpeechFormatter
{
    public function format(string $text): string
    {
        $text = $this->normalize($text);

        if ($text === '') {
            return '';
        }

        if (str_starts_with($text, 'Panduan')) {
            $text = 'Halo. Berikut panduan kesehatan untuk Anda. ... '.$text;
        } elseif (str_starts_with($text, 'Hasil skrining')) {
            $text = 'Halo. Berikut ringkasan hasil skrining Anda. ... '.$text;
        }

        // Jeda antar kalimat.
        $text = preg_replace('/\.\s+/', '. ... ', $text) ?? $text;

        // Jeda lebih panjang sebelum poin/section edukasi.
        $text = preg_replace(
            '/\.\s\.\.\.\s+(Lakukan|Jaga|Terapkan|Hindari|Catat|Pertahankan|Perbanyak|Konsumsi|Kurangi|Kelola|Perhatikan|Skrining)/u',
            '. ... ... $1',
            $text
        ) ?? $text;

        // Hindari titik berlebihan.
        $text = preg_replace('/(\.\s\.\.\.){3,}/', '. ... ... ', $text) ?? $text;
        $text = preg_replace('/\s{2,}/', ' ', $text) ?? $text;

        return trim($text);
    }

    protected function normalize(string $text): string
    {
        return trim(preg_replace('/\s+/', ' ', str_replace(
            ["\r\n", "\n", "\r"],
            ' ',
            preg_replace([
                '/\bIGD\b/i',
                '/\bSpO₂\b/i',
                '/\bRA\b/',
                '/\bDM\b/',
                '/\bHT\b/',
            ], [
                'I G D',
                'saturasi oksigen',
                'artritis reumatoid',
                'diabetes melitus',
                'hipertensi',
            ], $text) ?? $text
        )) ?? '');
    }
}
