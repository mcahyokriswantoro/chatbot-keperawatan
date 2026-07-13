document.addEventListener('alpine:init', () => {
    window.Alpine.data('adminConsultationChat', (config = {}) => ({
        messages: Array.isArray(config.initialMessages) ? [...config.initialMessages] : [],
        draft: '',
        sending: false,
        pollTimer: null,
        lastId: 0,

        init() {
            this.syncLastId();
            this.$nextTick(() => this.scrollToBottom());
            this.pollTimer = setInterval(() => this.fetchMessages(), 4000);
        },

        syncLastId() {
            this.lastId = this.messages.reduce((max, msg) => Math.max(max, msg.id || 0), 0);
        },

        mergeMessages(incoming) {
            if (! Array.isArray(incoming) || incoming.length === 0) {
                return;
            }

            const known = new Set(this.messages.map((m) => m.id));
            let added = false;

            incoming.forEach((msg) => {
                if (! known.has(msg.id)) {
                    this.messages.push(msg);
                    added = true;
                }
            });

            if (added) {
                this.syncLastId();
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        async fetchMessages() {
            try {
                const url = new URL(config.messagesUrl, window.location.origin);
                if (this.lastId > 0) {
                    url.searchParams.set('after', String(this.lastId));
                }

                const response = await fetch(url.toString(), {
                    headers: { Accept: 'application/json' },
                });

                if (! response.ok) {
                    return;
                }

                const data = await response.json();
                this.mergeMessages(data.messages || []);
            } catch {
                // ignore
            }
        },

        scrollToBottom() {
            const list = this.$refs.messageList;
            if (list) {
                list.scrollTop = list.scrollHeight;
            }
        },

        async sendReply() {
            const text = this.draft.trim();
            if (! text || this.sending) {
                return;
            }

            this.sending = true;

            try {
                const response = await fetch(config.replyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': config.csrf,
                    },
                    body: JSON.stringify({ message: text }),
                });

                const payload = await response.json().catch(() => ({}));

                if (! response.ok) {
                    throw new Error(payload.message || 'Gagal mengirim balasan.');
                }

                this.draft = '';
                if (payload.message) {
                    this.mergeMessages([payload.message]);
                }
            } catch (error) {
                alert(error.message || 'Gagal mengirim balasan.');
            } finally {
                this.sending = false;
            }
        },
    }));
});
