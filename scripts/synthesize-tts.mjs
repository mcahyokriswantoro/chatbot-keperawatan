import { writeFileSync } from 'node:fs';
import { UniversalEdgeTTS } from 'edge-tts-universal';

const text = process.argv[2] ?? '';
const voice = process.argv[3] ?? 'id-ID-GadisNeural';
const outputPath = process.argv[4] ?? '';
const rate = process.argv[5] ?? '-2%';
const pitch = process.argv[6] ?? '-2Hz';
const volume = process.argv[7] ?? '+0%';

if (!text.trim() || !outputPath) {
    console.error('Usage: node synthesize-tts.mjs "<text>" <voice> <output.mp3> [rate] [pitch] [volume]');
    process.exit(1);
}

const tts = new UniversalEdgeTTS(text, voice, {
    rate,
    volume,
    pitch,
});

const result = await tts.synthesize();
const audioBuffer = Buffer.from(await result.audio.arrayBuffer());

writeFileSync(outputPath, audioBuffer);
