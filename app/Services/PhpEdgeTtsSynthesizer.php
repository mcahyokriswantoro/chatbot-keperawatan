<?php



namespace App\Services;



use Afaya\EdgeTTS\Service\EdgeTTS;

use RuntimeException;

use Throwable;



class PhpEdgeTtsSynthesizer

{

    public function synthesize(

        string $text,

        string $voice,

        string $rate,

        string $pitch,

        string $volume,

    ): string {

        try {

            $tts = new EdgeTTS;

            $tts->synthesize($text, $voice, [

                'rate' => $rate,

                'pitch' => $pitch,

                'volume' => $volume,

            ]);



            $bytes = $tts->toRaw();

        } catch (Throwable $exception) {

            throw new RuntimeException(

                'TTS PHP gagal: '.$exception->getMessage(),

                0,

                $exception,

            );

        }



        if ($bytes === '') {

            throw new RuntimeException('TTS PHP menghasilkan audio kosong.');

        }



        return $bytes;

    }

}

