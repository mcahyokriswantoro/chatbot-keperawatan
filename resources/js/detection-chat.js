import {
    getTtsState,
    pauseScreeningResult,
    resumeScreeningResult,
    speakScreeningResult,
    stopScreeningResult,
} from './screening-tts';

document.addEventListener('alpine:init', () => {
    Alpine.data('screeningChat', (config) => ({
        config,
        messages: [],
        currentStep: -1,
        isTyping: false,
        answers: {},
        multiSelected: [],
        textInput: '',
        finished: false,
        showInput: false,
        isEmergency: false,
        emergencySymptoms: [],
        totalScore: null,
        maxScore: null,
        scoreRows: [],
        hasilKategori: null,
        risikoLabel: null,
        hasWarningSigns: false,
        ttsState: 'idle',

        get activeSelfManagement() {
            if (!this.config.self_management || !this.hasilKategori) {
                return null;
            }

            return this.config.self_management[this.hasilKategori] ?? null;
        },

        get totalQuestions() {
            return this.config.questions.length;
        },

        get progress() {
            if (this.finished) return 100;
            if (this.currentStep < 0) return 0;
            return Math.round(((this.currentStep + 1) / this.totalQuestions) * 100);
        },

        get progressLabel() {
            if (this.finished) return 'Selesai';
            if (this.currentStep < 0) return 'Memulai';
            return `Pertanyaan ${this.currentStep + 1} dari ${this.totalQuestions}`;
        },

        init() {
            this.$nextTick(() => this.scrollToBottom());
            setTimeout(() => this.showWelcome(), 400);

            document.addEventListener('screening-tts:state', (event) => {
                this.ttsState = event.detail ?? getTtsState();
            });
        },

        get ttsActive() {
            return this.ttsState === 'speaking' || this.ttsState === 'paused';
        },

        scrollToBottom() {
            const el = this.$refs.messageList;
            if (el) {
                el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
            }
        },

        async botSay(text, delay = 900) {
            this.isTyping = true;
            this.scrollToBottom();
            await this.wait(delay);
            this.isTyping = false;
            this.messages.push({
                id: Date.now() + Math.random(),
                role: 'bot',
                text,
                time: this.now(),
            });
            this.$nextTick(() => this.scrollToBottom());
        },

        userSay(text) {
            this.messages.push({
                id: Date.now() + Math.random(),
                role: 'user',
                text,
                time: this.now(),
            });
            this.$nextTick(() => this.scrollToBottom());
        },

        now() {
            return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        },

        wait(ms) {
            return new Promise((resolve) => setTimeout(resolve, ms));
        },

        async showWelcome() {
            await this.botSay(this.config.welcome);
            this.showQuickReplies(this.config.start_options);
        },

        showQuickReplies(options) {
            this.activeOptions = options;
            this.showInput = false;
            this.multiSelected = [];
        },

        activeOptions: [],

        itemScore(question, answerValue) {
            if (!this.config.scoring || !question?.score_ya) {
                return 0;
            }

            return answerValue === 'ya' ? question.score_ya : 0;
        },

        async selectOption(option) {
            this.activeOptions = [];

            if (this.currentStep < 0) {
                this.userSay(option.label);
                if (option.value === 'later') {
                    await this.wait(300);
                    await this.botSay('Baik, kapan saja Anda siap silakan kembali ke halaman ini. Semoga sehat selalu! 🙏');
                    return;
                }
                this.currentStep = 0;
                await this.askQuestion(0);
                return;
            }

            const question = this.config.questions[this.currentStep];
            const score = this.itemScore(question, option.value);
            this.answers[question.id] = option.value;
            this.answers[`${question.id}_score`] = score;

            this.userSay(option.label);
            await this.nextQuestion();
        },

        toggleMulti(value, label) {
            if (value === 'tidak_ada') {
                this.multiSelected = ['tidak_ada'];
                return;
            }
            this.multiSelected = this.multiSelected.filter((v) => v !== 'tidak_ada');
            const idx = this.multiSelected.indexOf(value);
            if (idx >= 0) {
                this.multiSelected.splice(idx, 1);
            } else {
                this.multiSelected.push(value);
            }
        },

        isMultiSelected(value) {
            return this.multiSelected.includes(value);
        },

        async submitMulti() {
            if (this.multiSelected.length === 0) return;
            const question = this.config.questions[this.currentStep];
            const labels = question.options
                .filter((o) => this.multiSelected.includes(o.value))
                .map((o) => o.label);
            this.activeOptions = [];
            this.userSay(labels.join(', '));
            this.answers[question.id] = [...this.multiSelected];
            this.multiSelected = [];
            await this.nextQuestion();
        },

        async submitText() {
            const text = this.textInput.trim();
            const question = this.config.questions[this.currentStep];
            this.userSay(text || 'Tidak ada');
            this.answers[question.id] = text || '-';
            this.textInput = '';
            this.showInput = false;
            await this.nextQuestion();
        },

        async askQuestion(index) {
            const question = this.config.questions[index];
            const prefix = question.prompt_prefix ?? this.config.question_prefix ?? '';
            let prompt;

            if (question.no && prefix) {
                prompt = `${question.no}. ${prefix} ${question.text}?`;
            } else if (question.no) {
                prompt = `${question.no}. ${question.text}?`;
            } else if (prefix) {
                prompt = `${prefix} ${question.text}?`;
            } else {
                prompt = question.text;
            }

            await this.botSay(prompt);

            if (question.type === 'choice') {
                this.showQuickReplies(question.options);
            } else if (question.type === 'multi') {
                this.activeOptions = question.options;
                this.showInput = false;
            } else if (question.type === 'text') {
                this.activeOptions = [];
                this.showInput = true;
                this.$nextTick(() => this.$refs.textInput?.focus());
            }
        },

        async nextQuestion() {
            await this.wait(500);
            this.currentStep++;

            const symptoms = this.answers.symptoms;
            const skipDuration = Array.isArray(symptoms) && symptoms.includes('tidak_ada');
            if (
                skipDuration
                && this.config.questions[this.currentStep]?.id === 'duration'
            ) {
                this.answers.duration = 'none';
                this.currentStep++;
            }

            if (this.currentStep >= this.config.questions.length) {
                await this.showResult();
                return;
            }

            await this.askQuestion(this.currentStep);
        },

        getScoreRows() {
            const items = this.config.scoring_items ?? this.config.questions;

            return items.map((item) => {
                const jawaban = this.answers[item.id] ?? '';
                const skorDidapat = this.itemScore(
                    { score_ya: item.score_ya },
                    jawaban,
                );

                return {
                    no: item.no,
                    text: item.text,
                    jawaban: jawaban === 'ya' ? 'Ya' : jawaban === 'tidak' ? 'Tidak' : '-',
                    skor_ya: item.score_ya ?? 0,
                    skor_didapat: skorDidapat,
                };
            });
        },

        calculateTotalScore() {
            if (!this.config.scoring) {
                return { total: 0, max: 0 };
            }

            const rows = this.getScoreRows();
            const total = rows.reduce((sum, row) => sum + row.skor_didapat, 0);
            const max = this.config.max_score
                ?? rows.reduce((sum, row) => sum + row.skor_ya, 0);

            return { total, max };
        },

        hasWarningSignsFromAnswers() {
            const ids = this.config.warning_sign_ids ?? [];

            return ids.some((id) => this.answers[id] === 'ya');
        },

        standardRisikoKategori(total) {
            if (total >= 9) return 'Tinggi';
            if (total >= 5) return 'Sedang';

            return 'Rendah';
        },

        hasilKategoriFromScore(total) {
            if (this.config.disease === 'dhf') {
                const hasWarning = this.hasWarningSignsFromAnswers();

                if (hasWarning || total >= 9) return 'Tinggi';
                if (total >= 5) return 'Sedang';

                return 'Rendah';
            }

            if (this.config.disease === 'ppok') {
                return this.standardRisikoKategori(total);
            }

            if (['penyakit_ginjal', 'stroke', 'jantung_koroner', 'diabetes_melitus', 'hipertensi'].includes(this.config.disease)) {
                if (total >= 11) return 'Tinggi';
                if (total >= 6) return 'Sedang';

                return 'Rendah';
            }

            if (total >= 11) return 'Tinggi';
            if (total >= 6) return 'Sedang';

            return 'Rendah';
        },

        risikoLabelFromKategori(hasilKategori) {
            return {
                Tinggi: 'Risiko Tinggi',
                Sedang: 'Risiko Sedang',
                Rendah: 'Risiko Rendah',
            }[hasilKategori] ?? hasilKategori;
        },

        async showResult() {
            this.finished = true;
            this.activeOptions = [];
            this.showInput = false;

            if (this.config.scoring) {
                const { total, max } = this.calculateTotalScore();
                this.totalScore = total;
                this.maxScore = max;
                this.scoreRows = this.getScoreRows();
                this.hasWarningSigns = this.hasWarningSignsFromAnswers();
                this.hasilKategori = this.hasilKategoriFromScore(total);
                this.risikoLabel = this.risikoLabelFromKategori(this.hasilKategori);
                this.answers._total_score = total;
                this.answers._max_score = max;
                this.answers._hasil_kategori = this.hasilKategori;
                this.answers._risiko_label = this.risikoLabel;
                this.answers._has_warning_signs = this.hasWarningSigns;
            }

            const summary = this.buildSummary();
            await this.saveScreening(summary);

            if (this.config.suppress_emergency) {
                this.isEmergency = false;
            }

            if (this.isEmergency && !this.config.suppress_emergency) {
                await this.botSay(
                    '⚠️ PERINGATAN DARURAT: Gejala yang Anda laporkan memerlukan penanganan segera. Segera hubungi layanan darurat (119) atau kunjungi IGD terdekat.'
                );
            }

            if (this.config.scoring && this.totalScore !== null) {
                const label = this.risikoLabel ?? this.risikoLabelFromKategori(this.hasilKategori) ?? this.hasilKategori;
                let scoreMsg = `📊 Jumlah skor Anda: ${this.totalScore} dari ${this.maxScore}. Klasifikasi: ${label}.`;

                if (this.hasWarningSigns && !this.config.suppress_emergency) {
                    scoreMsg += ' Terdapat tanda peringatan (warning signs) — segera ke fasilitas kesehatan.';
                }

                await this.botSay(scoreMsg);

                if (this.activeSelfManagement) {
                    await this.botSay(
                        `📋 Berikut panduan self-management untuk ${this.activeSelfManagement.label}. Lihat detail di kartu hasil di bawah.`
                    );
                }
            }

            await this.botSay(this.config.result.message);

            this.messages.push({
                id: 'result-' + Date.now(),
                role: 'bot',
                text: summary,
                time: this.now(),
                isResult: true,
            });
            this.$nextTick(() => this.scrollToBottom());
        },

        async saveScreening(summary) {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            try {
                const res = await fetch('/api/screening', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({
                        disease: this.config.disease,
                        answers: this.answers,
                        summary,
                        screening_identity_id: this.config.screening_identity_id ?? null,
                    }),
                });
                if (res.ok) {
                    const data = await res.json();
                    this.isEmergency = data.is_emergency;
                    this.emergencySymptoms = data.emergency_symptoms ?? [];
                    if (data.total_score !== undefined) {
                        this.totalScore = data.total_score;
                        this.maxScore = data.max_score;
                    }
                    if (data.hasil_kategori) {
                        this.hasilKategori = data.hasil_kategori;
                    }
                    if (data.risiko_label) {
                        this.risikoLabel = data.risiko_label;
                    }
                    if (this.config.suppress_emergency) {
                        this.isEmergency = false;
                    }
                }
            } catch {
                // offline or server error — screening still shown locally
            }
        },

        buildSummary() {
            const label = this.config.disease_label ?? 'Kesehatan';

            if (this.config.scoring) {
                const rows = this.getScoreRows();
                const displayLabel = this.risikoLabel ?? this.hasilKategori;
                const lines = [
                    `📋 Hasil Skrining: ${label}`,
                    '',
                    `⭐ JUMLAH NILAI AKHIR: ${this.totalScore} / ${this.maxScore}`,
                    `📌 KLASIFIKASI: ${displayLabel}`,
                ];

                if (this.hasWarningSigns) {
                    lines.push('⚠️ Terdapat tanda peringatan (warning signs)');
                }

                lines.push('');

                rows.forEach((row) => {
                    lines.push(`${row.no}. ${row.text} — Jawaban: ${row.jawaban}`);
                });

                return lines.join('\n');
            }

            const lines = [`📋 Ringkasan Skrining: ${label}\n`];

            this.config.questions.forEach((q) => {
                const answer = this.answers[q.id];
                let display = '-';

                if (Array.isArray(answer)) {
                    display = q.options
                        .filter((o) => answer.includes(o.value))
                        .map((o) => o.label)
                        .join(', ');
                } else if (q.type === 'choice' || q.type === 'multi') {
                    const opt = q.options?.find((o) => o.value === answer);
                    display = opt?.label ?? answer;
                } else {
                    display = answer;
                }

                const prefix = q.no ? `${q.no}. ` : '• ';
                lines.push(`${prefix}${q.text}: ${display}`);
            });

            return lines.join('\n');
        },

        buildSpeechText() {
            const parts = [];
            const label = this.config.disease_label ?? 'kesehatan';
            const riskLabel = this.risikoLabel ?? this.hasilKategori ?? '';

            parts.push(`Panduan self management ${label}`);

            if (riskLabel) {
                parts.push(`Tingkat risiko Anda ${String(riskLabel).replace(/^Risiko\s+/i, '')}`);
            }

            const guide = this.activeSelfManagement;
            if (guide) {
                if (guide.intro) {
                    parts.push(guide.intro);
                }

                (guide.sections ?? []).forEach((section) => {
                    parts.push(section.title);
                    (section.items ?? []).forEach((item) => parts.push(item));
                });
            }

            const emergency = this.config.self_management?.emergency;
            if (emergency && this.hasilKategori === 'Tinggi') {
                parts.push(emergency.title);
                (emergency.items ?? []).forEach((item) => parts.push(item));
            }

            if (this.isEmergency && !this.config.suppress_emergency) {
                parts.push('Segera ke fasilitas kesehatan atau IGD terdekat.');
            }

            return parts.filter(Boolean).join('. ');
        },

        listenToResult() {
            speakScreeningResult(this.buildSpeechText(), this.config.user_gender);
        },

        pauseTts() {
            pauseScreeningResult();
        },

        resumeTts() {
            resumeScreeningResult();
        },

        stopTts() {
            stopScreeningResult();
        },
    }));
});
