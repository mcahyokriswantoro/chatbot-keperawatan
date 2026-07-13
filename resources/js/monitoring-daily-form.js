document.addEventListener('alpine:init', () => {
    Alpine.data('monitoringDailyForm', (config = {}) => ({
        hasSelfManagement: config.hasSelfManagement ?? false,
        sections: {
            complaints: false,
            medication: false,
            selfManagement: ! (config.hasSelfManagement ?? false),
        },

        init() {
            this.checkMedication();
        },

        handleSection(event) {
            const { section, complete } = event.detail ?? {};

            if (section in this.sections) {
                this.sections[section] = Boolean(complete);
            }
        },

        checkMedication() {
            const cards = this.$root.querySelectorAll('[data-medication-card]');
            let complete = cards.length > 0;
            cards.forEach((card) => {
                if (! card.querySelector('input[type=radio]:checked')) {
                    complete = false;
                }
            });
            this.sections.medication = complete;
        },
    }));
});
