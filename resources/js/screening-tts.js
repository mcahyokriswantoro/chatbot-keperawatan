/**
 * Text-to-Speech untuk hasil skrining.
 * Default: Web Speech API peramban (instan, suara AI perangkat).
 * Opsional: neural Edge TTS via server jika screening-tts-client=0.
 */

let voicesCache = [];
let activePlayer = null;
let playbackState = 'idle';
let playbackMode = 'idle';
let ignoreUtteranceEvents = false;
let voicesReadyPromise = null;

/** @type {HTMLAudioElement|null} */
let neuralAudio = null;

/** @type {string|null} */
let neuralObjectUrl = null;

/** @type {{ stopped: boolean } | null} */
let neuralSession = null;

/** @type {AbortController|null} */
let neuralFetchAbort = null;

/** @type {{ chunks: string[], index: number, gender: string|null, player: Element|null } | null} */
let session = null;

const SILENT_MP3 = 'data:audio/mpeg;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA//tQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAWGluZwAAAA8AAAACAAADhAC7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7//////////////////////////////////////////////////////////////////8AAAAATGF2YzU4LjEzAAAAAAAAAAAAAAAAJAAAAAAAAAAAA4T/C/AAAAAAAAAAAAAAAAAAAA';

/** Maks tunggu API neural sebelum pakai suara peramban (HP lama / jaringan lambat). */
const NEURAL_FETCH_TIMEOUT_MS = 20000;

function preferClientTts() {
    const meta = document.querySelector('meta[name="screening-tts-client"]');

    return !meta || meta.getAttribute('content') !== '0';
}

function loadVoices() {
    if (!('speechSynthesis' in window)) {
        return [];
    }

    voicesCache = window.speechSynthesis.getVoices();

    return voicesCache;
}

function ensureVoicesLoaded() {
    if (!('speechSynthesis' in window)) {
        return Promise.resolve([]);
    }

    if (voicesCache.length) {
        return Promise.resolve(voicesCache);
    }

    if (voicesReadyPromise) {
        return voicesReadyPromise;
    }

    voicesReadyPromise = new Promise((resolve) => {
        const finish = () => {
            const voices = loadVoices();
            if (voices.length) {
                resolve(voices);
                return true;
            }

            return false;
        };

        if (finish()) {
            return;
        }

        const previousHandler = window.speechSynthesis.onvoiceschanged;

        window.speechSynthesis.onvoiceschanged = () => {
            if (typeof previousHandler === 'function') {
                previousHandler();
            }

            if (finish()) {
                window.speechSynthesis.onvoiceschanged = previousHandler ?? null;
            }
        };

        window.setTimeout(() => {
            resolve(loadVoices());
        }, 400);
    });

    return voicesReadyPromise;
}

if ('speechSynthesis' in window) {
    loadVoices();
    ensureVoicesLoaded();
}

function normalizeGender(gender) {
    const value = String(gender || '').toLowerCase();
    if (value.includes('perempuan') || value === 'female' || value === 'f') {
        return 'female';
    }
    if (value.includes('laki') || value === 'male' || value === 'm') {
        return 'male';
    }
    return 'neutral';
}

function scoreVoice(voice, preferred) {
    const blob = `${voice.name} ${voice.voiceURI}`.toLowerCase();
    const lang = (voice.lang || '').toLowerCase();
    let score = 0;

    if (lang === 'id-id') {
        score += 120;
    } else if (lang.startsWith('id')) {
        score += 100;
    } else if (lang.startsWith('ms')) {
        score += 35;
    }

    if (/natural|neural|premium|enhanced|online|wavenet|multilingual/i.test(blob)) {
        score += 70;
    }

    if (/google/i.test(blob) && lang.startsWith('id')) {
        score += 50;
    }

    if (/microsoft/i.test(blob) && lang.startsWith('id')) {
        score += 45;
    }

    if (/gedang|damayanti|siti/i.test(blob)) {
        score += 30;
    }

    if (/ardi|andika|riko/i.test(blob)) {
        score += 30;
    }

    if (preferred === 'female') {
        if (/female|woman|perempuan|gedang|damayanti|siti|zira|samantha|hazel/i.test(blob)) {
            score += 28;
        }
        if (/male|man|laki|ardi|andika|david|daniel/i.test(blob) && !/female|gedang|damayanti/i.test(blob)) {
            score -= 35;
        }
    } else if (preferred === 'male') {
        if (/male|man|laki|ardi|andika|david|daniel|riko/i.test(blob) && !/female|gedang|damayanti/i.test(blob)) {
            score += 28;
        }
        if (/female|woman|gedang|damayanti|siti/i.test(blob)) {
            score -= 25;
        }
    }

    if (voice.localService === false) {
        score += 12;
    }

    return score;
}

function pickVoice(gender) {
    const preferred = normalizeGender(gender);
    const voices = voicesCache.length ? voicesCache : window.speechSynthesis?.getVoices?.() ?? [];

    if (!voices.length) {
        return null;
    }

    return [...voices]
        .map((voice) => ({ voice, score: scoreVoice(voice, preferred) }))
        .sort((a, b) => b.score - a.score)[0]?.voice ?? null;
}

function normalizeSpeechText(text) {
    return String(text || '')
        .replace(/\r\n/g, '\n')
        .replace(/\n+/g, '. ')
        .replace(/\bIGD\b/gi, 'I G D')
        .replace(/\bSpO₂\b/gi, 'saturasi oksigen')
        .replace(/\bRA\b/g, 'artritis reumatoid')
        .replace(/\bDM\b/g, 'diabetes melitus')
        .replace(/\bHT\b/g, 'hipertensi')
        .replace(/\s*:\s*/g, ', ')
        .replace(/\s*;\s*/g, '. ')
        .replace(/\s+/g, ' ')
        .trim();
}

function formatSpeechText(text) {
    let normalized = normalizeSpeechText(text);

    if (!normalized) {
        return '';
    }

    if (normalized.startsWith('Panduan')) {
        normalized = `Halo. Berikut panduan kesehatan untuk Anda. ${normalized}`;
    } else if (normalized.startsWith('Hasil skrining')) {
        normalized = `Halo. Berikut ringkasan hasil skrining Anda. ${normalized}`;
    }

    return normalized.replace(/\s{2,}/g, ' ').trim();
}

/** @deprecated use normalizeSpeechText or formatSpeechText */
function prepareSpeechText(text) {
    return normalizeSpeechText(text);
}

function splitIntoSentences(text) {
    const sentences = [];
    let buffer = '';

    for (const char of text) {
        buffer += char;
        if (/[.!?]/.test(char)) {
            const trimmed = buffer.trim();
            if (trimmed) {
                sentences.push(trimmed);
            }
            buffer = '';
        }
    }

    const rest = buffer.trim();
    if (rest) {
        sentences.push(rest);
    }

    return sentences;
}

function isSlowOrLegacyDevice() {
    if (!window.fetch || typeof window.fetch !== 'function') {
        return true;
    }

    if (!window.URL?.createObjectURL) {
        return true;
    }

    const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
    if (connection?.saveData) {
        return true;
    }

    if (connection?.effectiveType === 'slow-2g' || connection?.effectiveType === '2g') {
        return true;
    }

    const ua = navigator.userAgent;

    if (/Android [1-8]\./i.test(ua)) {
        return true;
    }

    if (navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 2) {
        return true;
    }

    const iosVersion = ua.match(/OS (\d+)_/i);
    if (iosVersion && parseInt(iosVersion[1], 10) <= 12) {
        return true;
    }

    if (navigator.deviceMemory && navigator.deviceMemory <= 2) {
        return true;
    }

    return false;
}

function splitLongChunk(chunk, maxLength = 140) {
    if (chunk.length <= maxLength) {
        return [chunk];
    }

    const parts = chunk
        .split(/,\s+(?=[^,]{8,})/)
        .map((part) => part.trim())
        .filter(Boolean);

    if (parts.length <= 1) {
        return [chunk];
    }

    return parts;
}

function splitTextIntoChunks(text) {
    const normalized = formatSpeechText(text);

    if (!normalized) {
        return [];
    }

    const sentences = splitIntoSentences(normalized);

    const source = sentences.length ? sentences : [normalized];
    const chunks = [];

    source.forEach((sentence) => {
        splitLongChunk(sentence).forEach((part) => chunks.push(part));
    });

    return chunks.length ? chunks : [normalized];
}

function pauseBeforeNextChunk(chunk) {
    if (!chunk) {
        return 100;
    }

    if (chunk.length > 120) {
        return 180;
    }

    if (/[.!?]$/.test(chunk)) {
        return 140;
    }

    return 80;
}

export function getTtsState() {
    if (playbackState === 'loading') {
        return 'loading';
    }

    if (playbackState !== 'idle') {
        return playbackState;
    }

    if (!('speechSynthesis' in window)) {
        return 'unsupported';
    }

    return playbackState;
}

function notifyState() {
    document.dispatchEvent(new CustomEvent('screening-tts:state', {
        detail: {
            state: playbackState,
            mode: playbackMode,
        },
    }));

    document.querySelectorAll('[data-screening-tts-player]').forEach((player) => {
        player.dataset.ttsState = playbackState;
        player.dataset.ttsMode = playbackMode;
        updatePlayerButtons(player);
    });
}

function cancelSpeechSafely() {
    if (!('speechSynthesis' in window)) {
        return;
    }

    ignoreUtteranceEvents = true;
    window.speechSynthesis.cancel();

    window.setTimeout(() => {
        ignoreUtteranceEvents = false;
    }, 100);
}

function cleanupNeuralPlayback() {
    if (neuralAudio) {
        neuralAudio.pause();
        neuralAudio.removeAttribute('src');
        neuralAudio.load();
        neuralAudio.onended = null;
        neuralAudio.onerror = null;
        neuralAudio.onpause = null;
        neuralAudio = null;
    }

    if (neuralObjectUrl) {
        URL.revokeObjectURL(neuralObjectUrl);
        neuralObjectUrl = null;
    }
}

function resetPlayback() {
    playbackState = 'idle';
    playbackMode = 'idle';
    session = null;
    if (neuralSession) {
        neuralSession.stopped = true;
    }
    neuralSession = null;
    if (neuralFetchAbort) {
        neuralFetchAbort.abort();
        neuralFetchAbort = null;
    }
    activePlayer = null;
    cleanupNeuralPlayback();
    cancelSpeechSafely();
    notifyState();
}

function finishPlayback() {
    playbackState = 'idle';
    playbackMode = 'idle';
    session = null;
    neuralSession = null;
    activePlayer = null;
    cleanupNeuralPlayback();
    notifyState();
}

function createNeuralAudioElement() {
    const audio = new Audio();
    audio.setAttribute('playsinline', '');
    audio.preload = 'auto';
    return audio;
}

function unlockNeuralAudioDuringGesture() {
    cleanupNeuralPlayback();
    neuralAudio = createNeuralAudioElement();
    neuralAudio.src = SILENT_MP3;
    neuralAudio.volume = 0.01;
    return neuralAudio.play().catch(() => {});
}

async function fetchNeuralAudio(text, gender, signal = null) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
    const controller = signal ? null : new AbortController();
    const abortSignal = signal ?? controller.signal;
    const timeoutId = window.setTimeout(() => {
        if (controller) {
            controller.abort();
        }
    }, NEURAL_FETCH_TIMEOUT_MS);

    try {
        const response = await fetch('/api/screening-tts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'audio/mpeg',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                text: normalizeSpeechText(text),
                gender: gender ?? null,
            }),
            credentials: 'same-origin',
            signal: abortSignal,
        });

        if (!response.ok) {
            let message = 'Gagal memuat suara neural.';
            try {
                const payload = await response.json();
                if (payload?.message) {
                    message = payload.message;
                }
            } catch {
                // ignore JSON parse errors
            }
            throw new Error(message);
        }

        const contentType = response.headers.get('content-type') ?? '';
        if (!contentType.includes('audio')) {
            throw new Error('Respons suara tidak valid.');
        }

        const blob = await response.blob();

        if (!blob.size) {
            throw new Error('Audio kosong.');
        }

        return blob;
    } finally {
        window.clearTimeout(timeoutId);
    }
}

function attachNeuralHandlers(onEnded = null) {
    if (!neuralAudio) {
        return;
    }

    neuralAudio.onended = () => {
        if (playbackMode !== 'neural') {
            return;
        }

        if (typeof onEnded === 'function') {
            onEnded();
            return;
        }

        finishPlayback();
    };

    neuralAudio.onerror = () => {
        if (playbackMode !== 'neural') {
            return;
        }

        if (typeof onEnded === 'function') {
            onEnded();
            return;
        }

        finishPlayback();
    };
}

function playNeuralBlobAndWait(blob, playerEl) {
    return new Promise((resolve, reject) => {
        if (!neuralAudio) {
            neuralAudio = createNeuralAudioElement();
        }

        if (neuralObjectUrl) {
            URL.revokeObjectURL(neuralObjectUrl);
        }

        neuralObjectUrl = URL.createObjectURL(blob);
        neuralAudio.src = neuralObjectUrl;
        neuralAudio.currentTime = 0;
        neuralAudio.volume = 1;
        activePlayer = playerEl ?? null;
        playbackMode = 'neural';

        attachNeuralHandlers(() => resolve());

        neuralAudio.play().catch(reject);
    });
}

async function speakWithNeural(text, gender, playerEl) {
    neuralSession = { stopped: false };
    activePlayer = playerEl ?? null;
    playbackMode = 'neural';
    playbackState = 'loading';
    notifyState();

    neuralFetchAbort = new AbortController();

    let blob;
    try {
        blob = await fetchNeuralAudio(text, gender, neuralFetchAbort.signal);
    } catch (error) {
        if (neuralSession?.stopped || error?.name === 'AbortError') {
            return;
        }

        throw error;
    } finally {
        neuralFetchAbort = null;
    }

    if (!neuralSession || neuralSession.stopped) {
        return;
    }

    playbackState = 'speaking';
    notifyState();

    await playNeuralBlobAndWait(blob, playerEl);
    finishPlayback();
}

function createUtterance(text, gender) {
    const utterance = new SpeechSynthesisUtterance(text);
    const preferred = normalizeGender(gender);

    utterance.lang = 'id-ID';
    utterance.rate = 1.05;
    utterance.pitch = preferred === 'female' ? 0.98 : preferred === 'male' ? 0.94 : 1;
    utterance.volume = 1;

    const voice = pickVoice(gender);
    if (voice) {
        utterance.voice = voice;
        utterance.lang = voice.lang || 'id-ID';
    }

    return utterance;
}

function speakCurrentChunk() {
    if (!session || playbackState !== 'speaking' || playbackMode !== 'webspeech' || !('speechSynthesis' in window)) {
        return;
    }

    if (session.index >= session.chunks.length) {
        finishPlayback();
        return;
    }

    const chunk = session.chunks[session.index];
    const utterance = createUtterance(chunk, session.gender);

    utterance.onend = () => {
        if (ignoreUtteranceEvents || !session || playbackState !== 'speaking' || playbackMode !== 'webspeech') {
            return;
        }

        const finishedChunk = session.chunks[session.index];
        session.index += 1;

        if (session.index >= session.chunks.length) {
            finishPlayback();
            return;
        }

        window.setTimeout(() => speakCurrentChunk(), pauseBeforeNextChunk(finishedChunk));
    };

    utterance.onerror = () => {
        if (ignoreUtteranceEvents || playbackState !== 'speaking' || playbackMode !== 'webspeech') {
            return;
        }

        finishPlayback();
    };

    window.speechSynthesis.speak(utterance);
}

function setButtonEnabled(button, enabled) {
    if (!button) {
        return;
    }

    button.disabled = !enabled;
    button.setAttribute('aria-disabled', enabled ? 'false' : 'true');
}

function isPlayerActive(player) {
    if (activePlayer === player) {
        return true;
    }

    return activePlayer === null && (session !== null || playbackState === 'loading');
}

function getPlayerText(player) {
    const textEl = player.querySelector('[data-tts-text-content]');
    if (textEl) {
        return textEl.value ?? textEl.textContent ?? '';
    }

    return player.dataset.ttsText ?? '';
}

function updatePlayerButtons(player) {
    const state = playbackState;
    const isOwner = activePlayer === null ? session !== null || state === 'loading' : activePlayer === player;

    const playBtn = player.querySelector('[data-tts-action="play"]');
    const pauseBtn = player.querySelector('[data-tts-action="pause"]');
    const resumeBtn = player.querySelector('[data-tts-action="resume"]');
    const stopBtn = player.querySelector('[data-tts-action="stop"]');

    if (playBtn) {
        const labelEl = playBtn.querySelector('[data-tts-play-label]');
        const defaultLabel = labelEl?.dataset.ttsDefaultLabel ?? labelEl?.textContent?.trim() ?? 'Dengarkan Panduan';
        if (labelEl) {
            labelEl.dataset.ttsDefaultLabel = defaultLabel;
            labelEl.textContent = state === 'loading' && isOwner
                ? 'Menyiapkan suara...'
                : defaultLabel;
        }
    }

    if (state === 'loading' && isOwner) {
        setButtonEnabled(playBtn, false);
        setButtonEnabled(pauseBtn, false);
        setButtonEnabled(resumeBtn, false);
        setButtonEnabled(stopBtn, true);
        return;
    }

    if (state === 'idle' || !isOwner) {
        setButtonEnabled(playBtn, state === 'idle');
        setButtonEnabled(pauseBtn, false);
        setButtonEnabled(resumeBtn, false);
        setButtonEnabled(stopBtn, false);
        return;
    }

    setButtonEnabled(playBtn, false);
    setButtonEnabled(pauseBtn, state === 'speaking');
    setButtonEnabled(resumeBtn, state === 'paused');
    setButtonEnabled(stopBtn, state === 'speaking' || state === 'paused');
}

export function stopScreeningResult() {
    resetPlayback();
}

export function pauseScreeningResult() {
    if (playbackState !== 'speaking') {
        return;
    }

    if (playbackMode === 'neural' && neuralAudio) {
        neuralAudio.pause();
        playbackState = 'paused';
        notifyState();
        return;
    }

    if (session) {
        playbackState = 'paused';
        cancelSpeechSafely();
        notifyState();
    }
}

export function resumeScreeningResult() {
    if (playbackState !== 'paused') {
        return;
    }

    if (playbackMode === 'neural' && neuralAudio) {
        playbackState = 'speaking';
        notifyState();
        neuralAudio.play().catch(() => finishPlayback());
        return;
    }

    if (session) {
        playbackState = 'speaking';
        notifyState();
        speakCurrentChunk();
    }
}

export function togglePauseScreeningResult() {
    if (playbackState === 'paused') {
        resumeScreeningResult();
    } else {
        pauseScreeningResult();
    }
}

async function speakWithWebSpeech(text, gender, playerEl) {
    if (!('speechSynthesis' in window)) {
        window.alert('Peramban Anda tidak mendukung pembacaan suara.');
        return;
    }

    await ensureVoicesLoaded();

    const chunks = splitTextIntoChunks(text);
    if (!chunks.length) {
        return;
    }

    session = {
        chunks,
        index: 0,
        gender: gender ?? null,
        player: playerEl ?? null,
    };
    activePlayer = playerEl ?? null;
    playbackMode = 'webspeech';
    playbackState = 'speaking';
    notifyState();
    speakCurrentChunk();
}

export async function speakScreeningResult(text, gender = null, playerEl = null) {
    const prepared = normalizeSpeechText(text);

    if (!prepared) {
        return;
    }

    resetPlayback();

    if (preferClientTts()) {
        await speakWithWebSpeech(text, gender, playerEl);
        return;
    }

    if (isSlowOrLegacyDevice()) {
        activePlayer = playerEl ?? null;
        playbackState = 'loading';
        notifyState();
        await speakWithWebSpeech(text, gender, playerEl);
        return;
    }

    const unlockPromise = unlockNeuralAudioDuringGesture();

    activePlayer = playerEl ?? null;
    playbackState = 'loading';
    notifyState();

    let apiError = null;

    try {
        await unlockPromise;
        await speakWithNeural(text, gender, playerEl);
        return;
    } catch (error) {
        if (neuralSession?.stopped || error?.name === 'AbortError') {
            resetPlayback();
            return;
        }

        apiError = error instanceof Error ? error.message : 'Gagal memuat suara neural.';
    }

    cleanupNeuralPlayback();
    neuralSession = null;

    if (apiError) {
        await speakWithWebSpeech(text, gender, playerEl);
    }
}

export function bindScreeningTtsPlayers() {
    document.querySelectorAll('[data-screening-tts-player]').forEach((player) => {
        if (player.dataset.ttsPlayerBound === '1') {
            updatePlayerButtons(player);
            return;
        }

        player.dataset.ttsPlayerBound = '1';
        player.dataset.ttsState = playbackState;
        player.dataset.ttsMode = playbackMode;

        const gender = player.dataset.ttsGender ?? null;

        player.querySelector('[data-tts-action="play"]')?.addEventListener('click', () => {
            speakScreeningResult(getPlayerText(player), gender, player);
        });

        player.querySelector('[data-tts-action="pause"]')?.addEventListener('click', (event) => {
            event.preventDefault();
            if (isPlayerActive(player) && playbackState === 'speaking') {
                pauseScreeningResult();
            }
        });

        player.querySelector('[data-tts-action="resume"]')?.addEventListener('click', (event) => {
            event.preventDefault();
            if (isPlayerActive(player) && playbackState === 'paused') {
                resumeScreeningResult();
            }
        });

        player.querySelector('[data-tts-action="stop"]')?.addEventListener('click', (event) => {
            event.preventDefault();
            if (isPlayerActive(player) && (playbackState === 'speaking' || playbackState === 'paused' || playbackState === 'loading')) {
                stopScreeningResult();
            }
        });

        updatePlayerButtons(player);
    });
}

/** @deprecated use bindScreeningTtsPlayers */
export function bindScreeningTtsButtons() {
    bindScreeningTtsPlayers();
}

window.ScreeningTts = {
    speak: speakScreeningResult,
    pause: pauseScreeningResult,
    resume: resumeScreeningResult,
    togglePause: togglePauseScreeningResult,
    stop: stopScreeningResult,
    getState: getTtsState,
    bind: bindScreeningTtsPlayers,
};

document.addEventListener('DOMContentLoaded', () => {
    bindScreeningTtsPlayers();
    ensureVoicesLoaded();
});
