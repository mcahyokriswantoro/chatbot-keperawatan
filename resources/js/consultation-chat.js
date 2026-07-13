document.addEventListener('alpine:init', () => {
    window.Alpine.data('consultationChat', (config = {}) => ({
        messages: Array.isArray(config.initialMessages) ? [...config.initialMessages] : [],
        draft: '',
        sending: false,
        expired: false,
        pollTimer: null,
        lastId: 0,

        init() {
            this.syncLastId();
            this.$nextTick(() => this.scrollToBottom());
            this.startPolling();

            if (config.expiresAt) {
                const expires = new Date(config.expiresAt).getTime();
                if (Date.now() >= expires) {
                    this.expired = true;
                }
            }
        },

        destroy() {
            if (this.pollTimer) {
                clearInterval(this.pollTimer);
            }
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

        startPolling() {
            this.pollTimer = setInterval(() => this.fetchMessages(), 4000);
        },

        async fetchMessages() {
            if (this.expired) {
                return;
            }

            try {
                const url = new URL(config.messagesUrl, window.location.origin);
                if (this.lastId > 0) {
                    url.searchParams.set('after', String(this.lastId));
                }

                const response = await fetch(url.toString(), {
                    headers: { Accept: 'application/json' },
                });

                if (response.status === 402) {
                    this.expired = true;
                    return;
                }

                if (! response.ok) {
                    return;
                }

                const data = await response.json();
                this.mergeMessages(data.messages || []);

                if (data.expires_at) {
                    const expires = new Date(data.expires_at).getTime();
                    if (Date.now() >= expires) {
                        this.expired = true;
                    }
                }
            } catch {
                // ignore transient network errors
            }
        },

        scrollToBottom() {
            const list = this.$refs.messageList;
            if (list) {
                list.scrollTop = list.scrollHeight;
            }
        },

        async sendMessage() {
            const text = this.draft.trim();

            if (! text || this.sending || this.expired) {
                return;
            }

            this.sending = true;

            try {
                const response = await fetch(config.sendUrl, {
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
                    if (response.status === 402 && payload.checkout_url) {
                        window.location.href = payload.checkout_url;
                        return;
                    }

                    throw new Error(payload.message || 'Gagal mengirim pesan.');
                }

                this.draft = '';

                if (payload.message) {
                    this.mergeMessages([payload.message]);
                }
            } catch (error) {
                alert(error.message || 'Gagal mengirim pesan. Coba lagi.');
            } finally {
                this.sending = false;
                this.$nextTick(() => this.$refs.input?.focus());
            }
        },
    }));
});
