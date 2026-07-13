document.addEventListener('alpine:init', () => {
    Alpine.data('monitoringComplaintsChat', (config) => ({
        diseaseLabel: config.diseaseLabel,
        intro: config.intro ?? '',
        symptoms: config.symptoms,
        options: config.options,
        step: 0,
        started: false,
        introDone: false,
        finished: false,
        messages: [],
        answers: { ...(config.oldAnswers ?? {}) },
        _msgSeq: 2,

        init() {
            if (this.answeredCount() > 0) {
                this.restoreFromAnswers();
            }

            this.$nextTick(() => {
                if (this.finished) {
                    this.$dispatch('monitoring-daily-section', { section: 'complaints', complete: true });
                }
            });
        },

        nextMsgId() {
            this._msgSeq += 1;

            return this._msgSeq;
        },

        pushMessage(role, text) {
            this.messages.push({
                id: this.nextMsgId(),
                role,
                text,
            });
        },

        get currentSymptom() {
            return this.symptoms[this.step] ?? null;
        },

        get progressLabel() {
            if (! this.started) {
                return 'Siap memulai';
            }
            if (! this.introDone) {
                return 'Pengantar';
            }
            if (this.finished) {
                return 'Keluhan selesai';
            }

            return `Gejala ${this.step + 1} dari ${this.symptoms.length}`;
        },

        get progress() {
            if (this.symptoms.length === 0) {
                return 100;
            }
            if (! this.started || ! this.introDone) {
                return 0;
            }
            if (this.finished) {
                return 100;
            }

            return Math.round((this.step / this.symptoms.length) * 100);
        },

        get totalScore() {
            const scoreMap = Object.fromEntries(this.options.map((o) => [o.value, o.score ?? 0]));

            return this.symptoms.reduce(
                (sum, symptom) => sum + (scoreMap[this.answers[symptom.key]] ?? 0),
                0,
            );
        },

        answeredCount() {
            return this.symptoms.filter((s) => this.answers[s.key]).length;
        },

        start() {
            this.started = true;
            this.introDone = false;
            this.step = 0;
            this.finished = false;
            this.answers = {};
            this.messages = [];
            this._msgSeq = 0;
            this.pushMessage('bot', this.intro);
            this.$nextTick(() => this.scrollToBottom());
        },

        continueAfterIntro() {
            this.introDone = true;
            this.askCurrent();
        },

        askCurrent() {
            const symptom = this.currentSymptom;
            if (! symptom) {
                return;
            }

            this.pushMessage('bot', `• ${symptom.label}`);
            this.$nextTick(() => this.scrollToBottom());
        },

        selectOption(option) {
            const symptom = this.currentSymptom;
            if (! symptom || this.finished) {
                return;
            }

            this.answers[symptom.key] = option.value;
            this.pushMessage('user', option.label);

            this.step++;

            if (this.step >= this.symptoms.length) {
                this.finished = true;
                this.$dispatch('monitoring-daily-section', { section: 'complaints', complete: true });
                this.pushMessage(
                    'bot',
                    `Terima kasih! Total skor keluhan hari ini: ${this.totalScore}. Tekan tombol Simpan keluhan di bawah untuk menyimpan.`,
                );
            } else {
                this.askCurrent();
            }

            this.$nextTick(() => this.scrollToBottom());
        },

        restoreFromAnswers() {
            this.started = true;
            this.introDone = true;
            this.finished = false;
            this.step = 0;

            for (const symptom of this.symptoms) {
                const value = this.answers[symptom.key];
                if (! value) {
                    break;
                }

                this.pushMessage('bot', `• ${symptom.label}`);

                const label = this.options.find((o) => o.value === value)?.label ?? value;
                this.pushMessage('user', label);
                this.step++;
            }

            if (this.step >= this.symptoms.length) {
                this.finished = true;
                this.$dispatch('monitoring-daily-section', { section: 'complaints', complete: true });
                this.pushMessage(
                    'bot',
                    `Total skor keluhan hari ini: ${this.totalScore}. Silakan lanjut isi bagian di bawah.`,
                );
            } else if (this.step > 0) {
                this.askCurrent();
            }
        },

        scrollToBottom() {
            const list = this.$refs.messageList;
            if (list) {
                list.scrollTop = list.scrollHeight;
            }
        },
    }));
});
