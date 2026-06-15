/**
 * Text-to-Speech untuk hasil skrining (Web Speech API, suara disesuaikan gender).
 * Pause/resume memakai antrian kalimat; event onend diabaikan saat jeda/stop.
 */

let voicesCache = [];
let activePlayer = null;
let playbackState = 'idle';
let ignoreUtteranceEvents = false;

/** @type {{ chunks: string[], index: number, gender: string|null, player: Element|null } | null} */
let session = null;

function loadVoices() {
    if (!('speechSynthesis' in window)) {
        return;
    }
    voicesCache = window.speechSynthesis.getVoices();
}

if ('speechSynthesis' in window) {
    loadVoices();
    window.speechSynthesis.onvoiceschanged = loadVoices;
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

function pickVoice(gender) {
    const preferred = normalizeGender(gender);
    const voices = voicesCache.length ? voicesCache : window.speechSynthesis?.getVoices?.() ?? [];
    const indonesian = voices.filter((voice) => voice.lang?.toLowerCase().startsWith('id'));

    const pool = indonesian.length ? indonesian : voices;

    const isFemale = (voice) => /female|woman|perempuan|zira|samantha|hazel|yuna|siti/i.test(`${voice.name} ${voice.voiceURI}`);
    const isMale = (voice) => /male|man|laki|david|daniel|andika|google indonesia/i.test(`${voice.name} ${voice.voiceURI}`) && !isFemale(voice);

    if (preferred === 'female') {
        return pool.find(isFemale) ?? pool.find((v) => !isMale(v)) ?? pool[0] ?? null;
    }

    if (preferred === 'male') {
        return pool.find(isMale) ?? pool[0] ?? null;
    }

    return pool[0] ?? null;
}

function splitTextIntoChunks(text) {
    const normalized = String(text || '')
        .replace(/\r\n/g, '\n')
        .replace(/\n+/g, '. ')
        .replace(/\s+/g, ' ')
        .trim();

    if (!normalized) {
        return [];
    }

    const parts = normalized
        .split(/(?<=[.!?])\s+/)
        .map((part) => part.trim())
        .filter(Boolean);

    return parts.length ? parts : [normalized];
}

export function getTtsState() {
    if (!('speechSynthesis' in window)) {
        return 'unsupported';
    }

    return playbackState;
}

function notifyState() {
    document.dispatchEvent(new CustomEvent('screening-tts:state', { detail: playbackState }));

    document.querySelectorAll('[data-screening-tts-player]').forEach((player) => {
        player.dataset.ttsState = playbackState;
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

function resetPlayback() {
    playbackState = 'idle';
    session = null;
    activePlayer = null;
    cancelSpeechSafely();
    notifyState();
}

function finishPlayback() {
    playbackState = 'idle';
    session = null;
    activePlayer = null;
    notifyState();
}

function createUtterance(text, gender) {
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'id-ID';
    utterance.rate = 0.92;
    utterance.pitch = normalizeGender(gender) === 'female' ? 1.05 : 0.95;

    const voice = pickVoice(gender);
    if (voice) {
        utterance.voice = voice;
    }

    return utterance;
}

function speakCurrentChunk() {
    if (!session || playbackState !== 'speaking' || !('speechSynthesis' in window)) {
        return;
    }

    if (session.index >= session.chunks.length) {
        finishPlayback();
        return;
    }

    const utterance = createUtterance(session.chunks[session.index], session.gender);

    utterance.onend = () => {
        if (ignoreUtteranceEvents || !session || playbackState !== 'speaking') {
            return;
        }

        session.index += 1;

        if (session.index >= session.chunks.length) {
            finishPlayback();
            return;
        }

        speakCurrentChunk();
    };

    utterance.onerror = () => {
        if (ignoreUtteranceEvents || playbackState !== 'speaking') {
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

    return activePlayer === null && session !== null;
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
    const isOwner = activePlayer === null ? session !== null : activePlayer === player;

    const playBtn = player.querySelector('[data-tts-action="play"]');
    const pauseBtn = player.querySelector('[data-tts-action="pause"]');
    const resumeBtn = player.querySelector('[data-tts-action="resume"]');
    const stopBtn = player.querySelector('[data-tts-action="stop"]');

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
    if (playbackState !== 'speaking' || !session) {
        return;
    }

    playbackState = 'paused';
    cancelSpeechSafely();
    notifyState();
}

export function resumeScreeningResult() {
    if (playbackState !== 'paused' || !session) {
        return;
    }

    playbackState = 'speaking';
    notifyState();
    speakCurrentChunk();
}

export function togglePauseScreeningResult() {
    if (playbackState === 'paused') {
        resumeScreeningResult();
    } else {
        pauseScreeningResult();
    }
}

export function speakScreeningResult(text, gender = null, playerEl = null) {
    if (!('speechSynthesis' in window)) {
        window.alert('Peramban Anda tidak mendukung pembacaan suara.');
        return;
    }

    const chunks = splitTextIntoChunks(text);
    if (!chunks.length) {
        return;
    }

    resetPlayback();

    session = {
        chunks,
        index: 0,
        gender: gender ?? null,
        player: playerEl ?? null,
    };
    activePlayer = playerEl ?? null;
    playbackState = 'speaking';
    notifyState();
    speakCurrentChunk();
}

export function bindScreeningTtsPlayers() {
    document.querySelectorAll('[data-screening-tts-player]').forEach((player) => {
        if (player.dataset.ttsPlayerBound === '1') {
            updatePlayerButtons(player);
            return;
        }

        player.dataset.ttsPlayerBound = '1';
        player.dataset.ttsState = playbackState;

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
            if (isPlayerActive(player) && (playbackState === 'speaking' || playbackState === 'paused')) {
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

document.addEventListener('DOMContentLoaded', bindScreeningTtsPlayers);
