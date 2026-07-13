<?php



namespace App\Services;



use Illuminate\Contracts\Process\ProcessResult;

use Illuminate\Support\Facades\Process;

use Illuminate\Support\Str;

use RuntimeException;



class ScreeningTtsService

{

    public function __construct(

        private PhpEdgeTtsSynthesizer $phpTts,

    ) {}



    public function voiceForGender(?string $gender): string

    {

        $voices = config('screening_tts.voices', []);



        if (config('screening_tts.use_presenter_voice', true)) {

            return (string) ($voices['presenter'] ?? 'id-ID-GadisNeural');

        }



        $value = Str::lower((string) $gender);



        if (str_contains($value, 'laki') || $value === 'male' || $value === 'm') {

            return (string) ($voices['male'] ?? 'id-ID-ArdiNeural');

        }



        return (string) ($voices['female'] ?? 'id-ID-GadisNeural');

    }



    public function synthesize(string $text, ?string $gender = null): string

    {

        $text = trim($text);



        if ($text === '') {

            throw new RuntimeException('Teks kosong.');

        }



        if (strlen($text) > 8000) {

            $text = substr($text, 0, 8000);

        }



        $text = app(ScreeningSpeechFormatter::class)->format($text);



        if ($text === '') {

            throw new RuntimeException('Teks kosong.');

        }



        $voice = $this->voiceForGender($gender);

        $prosody = config('screening_tts.prosody', []);

        $rate = (string) ($prosody['rate'] ?? '-14%');

        $pitch = (string) ($prosody['pitch'] ?? '-2Hz');

        $volume = (string) ($prosody['volume'] ?? '+0%');



        return match ($this->resolveDriver()) {

            'php' => $this->phpTts->synthesize($text, $voice, $rate, $pitch, $volume),

            'node' => $this->synthesizeViaNode($text, $voice, $rate, $pitch, $volume),

            default => $this->synthesizeAuto($text, $voice, $rate, $pitch, $volume),

        };

    }



    protected function resolveDriver(): string

    {

        $driver = Str::lower((string) config('screening_tts.driver', 'auto'));



        return in_array($driver, ['auto', 'php', 'node'], true) ? $driver : 'auto';

    }



    protected function synthesizeAuto(

        string $text,

        string $voice,

        string $rate,

        string $pitch,

        string $volume,

    ): string {

        if (PHP_OS_FAMILY === 'Windows' && $this->canUseNodeDriver()) {

            return $this->synthesizeViaNode($text, $voice, $rate, $pitch, $volume);

        }



        return $this->phpTts->synthesize($text, $voice, $rate, $pitch, $volume);

    }



    protected function canUseNodeDriver(): bool

    {

        if ($this->isShellFunctionDisabled('proc_open') && $this->isShellFunctionDisabled('popen')) {

            return false;

        }



        return is_file(base_path('scripts/synthesize-tts.mjs'))

            && is_dir(base_path('node_modules/edge-tts-universal'));

    }



    protected function isShellFunctionDisabled(string $function): bool

    {

        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));



        return in_array($function, $disabled, true);

    }



    protected function synthesizeViaNode(

        string $text,

        string $voice,

        string $rate,

        string $pitch,

        string $volume,

    ): string {

        $outputPath = storage_path('app/tts/'.Str::uuid()->toString().'.mp3');

        $outputDir = dirname($outputPath);



        if (! is_dir($outputDir)) {

            mkdir($outputDir, 0755, true);

        }



        $timeout = (int) config('screening_tts.timeout_seconds', 180);

        $result = $this->runNodeSynthesis($text, $voice, $outputPath, $rate, $pitch, $volume, $timeout);



        if (! $result->successful() || ! is_file($outputPath)) {

            @unlink($outputPath);



            $message = trim($result->errorOutput() ?: $result->output() ?: 'Gagal membuat audio.');

            if (str_contains($message, 'CSPRNG')) {

                $message = 'Node.js gagal dijalankan (CSPRNG). Pasang Node.js 20 LTS atau restart komputer, lalu coba lagi.';

            }



            throw new RuntimeException($message);

        }



        $bytes = file_get_contents($outputPath) ?: '';

        @unlink($outputPath);



        if ($bytes === '') {

            throw new RuntimeException('Audio kosong.');

        }



        return $bytes;

    }



    /**

     * @return list<string>

     */

    protected function nodeSynthesisCommand(

        string $text,

        string $voice,

        string $outputPath,

        string $rate,

        string $pitch,

        string $volume,

    ): array {

        if (PHP_OS_FAMILY === 'Windows') {

            return [

                base_path('scripts/synthesize-tts.cmd'),

                $text,

                $voice,

                $outputPath,

                $rate,

                $pitch,

                $volume,

            ];

        }



        $nodeBinary = (string) config('screening_tts.node_binary', env('NODE_BINARY', 'node'));

        if ($nodeBinary !== 'node' && ! is_file($nodeBinary)) {

            $nodeBinary = 'node';

        }



        return [

            $nodeBinary,

            base_path('scripts/synthesize-tts.mjs'),

            $text,

            $voice,

            $outputPath,

            $rate,

            $pitch,

            $volume,

        ];

    }



    protected function runNodeSynthesis(

        string $text,

        string $voice,

        string $outputPath,

        string $rate,

        string $pitch,

        string $volume,

        int $timeout,

    ): ProcessResult {

        $env = $this->processEnvironment();

        $nodeBinary = (string) config('screening_tts.node_binary', env('NODE_BINARY', 'node'));

        if ($nodeBinary !== 'node' && is_file($nodeBinary)) {

            $env['NODE_BINARY'] = $nodeBinary;

        }



        return Process::timeout($timeout)

            ->path(base_path())

            ->env($env)

            ->run($this->nodeSynthesisCommand($text, $voice, $outputPath, $rate, $pitch, $volume));

    }



    /**

     * @return array<string, string>

     */

    protected function processEnvironment(): array

    {

        $env = [];



        foreach (array_merge($_ENV, $_SERVER) as $key => $value) {

            if (! is_string($key) || ! is_string($value) || $value === '') {

                continue;

            }



            if (! preg_match('/^[A-Z_][A-Z0-9_]*$/', $key)) {

                continue;

            }



            $env[$key] = $value;

        }



        if (PHP_OS_FAMILY === 'Windows') {

            $systemRoot = $env['SYSTEMROOT'] ?? $env['SystemRoot'] ?? 'C:\\Windows';

            $env['SYSTEMROOT'] = $systemRoot;

            $env['SystemRoot'] = $systemRoot;

            $env['WINDIR'] = $env['WINDIR'] ?? $systemRoot;

            $env['ComSpec'] = $env['ComSpec'] ?? 'C:\\Windows\\system32\\cmd.exe';

            $env['TEMP'] = $env['TEMP'] ?? sys_get_temp_dir();

            $env['TMP'] = $env['TMP'] ?? $env['TEMP'];

        }



        return $env;

    }

}

