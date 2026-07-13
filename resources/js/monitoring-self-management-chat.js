document.addEventListener('alpine:init', () => {
    Alpine.data('monitoringSelfManagementChat', (config) => ({
        riskLevel: config.riskLevel,
        items: config.items,
        options: config.options,
        step: 0,
        started: false,
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
                    this.$dispatch('monitoring-daily-section', { section: 'selfManagement', complete: true });
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

        get currentItem() {
            return this.items[this.step] ?? null;
        },

        get progressLabel() {
            if (! this.started) {
                return 'Siap memulai';
            }
            if (this.finished) {
                return 'Evaluasi selesai';
            }

            return `Pertanyaan ${this.step + 1} dari ${this.items.length}`;
        },

        get progress() {
            if (this.items.length === 0) {
                return 100;
            }
            if (! this.started) {
                return 0;
            }
            if (this.finished) {
                return 100;
            }

            return Math.round((this.step / this.items.length) * 100);
        },

        get totalPercent() {
            const scoreMap = Object.fromEntries(this.options.map((o) => [o.value, o.score ?? 0]));
            const total = this.items.reduce(
                (sum, item) => sum + (scoreMap[this.answers[item.index]] ?? 0),
                0,
            );
            const max = this.items.length * 2;

            return max === 0 ? 0 : Math.round((total / max) * 1000) / 10;
        },

        answeredCount() {
            return this.items.filter((item) => this.answers[item.index]).length;
        },

        start() {
            this.started = true;
            this.step = 0;
            this.finished = false;
            this.answers = {};
            this.messages = [];
            this._msgSeq = 0;
            this.askCurrent();
        },

        askCurrent() {
            const item = this.currentItem;
            if (! item) {
                return;
            }

            this.pushMessage('bot', item.question);
            this.$nextTick(() => this.scrollToBottom());
        },

        selectOption(option) {
            const item = this.currentItem;
            if (! item || this.finished) {
                return;
            }

            this.answers[item.index] = option.value;
            this.pushMessage('user', option.label);

            this.step++;

            if (this.step >= this.items.length) {
                this.finished = true;
                this.$dispatch('monitoring-daily-section', { section: 'selfManagement', complete: true });
                this.pushMessage(
                    'bot',
                    `Terima kasih! Self management hari ini: ${this.totalPercent}%. Silakan simpan catatan hari ini.`,
                );
            } else {
                this.askCurrent();
            }

            this.$nextTick(() => this.scrollToBottom());
        },

        restoreFromAnswers() {
            this.started = true;
            this.finished = false;
            this.step = 0;

            for (const item of this.items) {
                const value = this.answers[item.index];
                if (! value) {
                    break;
                }

                this.pushMessage('bot', item.question);

                const label = this.options.find((o) => o.value === value)?.label ?? value;
                this.pushMessage('user', label);
                this.step++;
            }

            if (this.step >= this.items.length) {
                this.finished = true;
                this.$dispatch('monitoring-daily-section', { section: 'selfManagement', complete: true });
                this.pushMessage(
                    'bot',
                    `Self management hari ini: ${this.totalPercent}%. Silakan simpan catatan hari ini.`,
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
