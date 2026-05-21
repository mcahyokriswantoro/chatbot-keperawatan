document.addEventListener('alpine:init', () => {
    Alpine.data('screeningIdentityForm', (config = {}) => ({
        provinces: config.provinces ?? [],
        regencies: [],
        districts: [],
        provinceKode: config.old?.province_kode ?? '',
        regencyKode: config.old?.regency_kode ?? '',
        districtKode: config.old?.district_kode ?? '',
        loadingRegencies: false,
        loadingDistricts: false,

        init() {
            this.updateAge();

            if (this.provinceKode) {
                this.loadRegencies(this.provinceKode, true);
            }
        },

        async loadRegencies(provinceKode, keepSelection = false) {
            if (! provinceKode) {
                this.regencies = [];
                this.districts = [];
                this.regencyKode = '';
                this.districtKode = '';

                return;
            }

            if (! keepSelection) {
                this.regencyKode = '';
                this.districtKode = '';
                this.districts = [];
            }

            this.loadingRegencies = true;

            try {
                this.regencies = await this.fetchChildren(provinceKode);
            } finally {
                this.loadingRegencies = false;
            }

            if (keepSelection && this.regencyKode) {
                await this.loadDistricts(this.regencyKode, true);
            }
        },

        async loadDistricts(regencyKode, keepSelection = false) {
            if (! regencyKode) {
                this.districts = [];
                this.districtKode = '';

                return;
            }

            if (! keepSelection) {
                this.districtKode = '';
            }

            this.loadingDistricts = true;

            try {
                this.districts = await this.fetchChildren(regencyKode);
            } finally {
                this.loadingDistricts = false;
            }
        },

        onProvinceChange() {
            this.loadRegencies(this.provinceKode);
        },

        onRegencyChange() {
            this.loadDistricts(this.regencyKode);
        },

        async fetchChildren(parentKode) {
            const res = await fetch(`/api/wilayah/children?parent=${encodeURIComponent(parentKode)}`, {
                headers: { Accept: 'application/json' },
            });

            if (! res.ok) {
                return [];
            }

            return res.json();
        },

        updateAge() {
            const dob = this.$refs.dob?.value;
            const display = this.$refs.ageDisplay;
            if (! dob || ! display) {
                return;
            }

            const birth = new Date(dob + 'T00:00:00');
            const today = new Date();
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();

            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }

            display.value = age >= 0 ? `${age} tahun` : '';
        },
    }));
});
