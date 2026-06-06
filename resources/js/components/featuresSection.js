export default () => ({
    active: 0,
    isExpanded: false,
    autoplay: null,

    slides: [
        {
            id: 0,
            title: 'Unified Integration',
            desc: 'Satu sambungan untuk semua aplikasi daerah.',
            details: 'Menyatukan pulau-pulau data yang terpisah. Hubungkan sistem kependudukan, perizinan, dan keuangan daerah dalam satu jalur integrasi yang mulus.',
            icon: '<path stroke=\'currentColor\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4\'></path>',
            color: 'bg-blue-500',
            textColor: 'text-blue-500',
            lightColor: 'bg-blue-50',
            badge: 'CONNECTED'
        },
        {
            id: 1,
            title: 'Cyber Security',
            desc: 'Standar keamanan siber tingkat tinggi (SPBE).',
            details: 'Proteksi berlapis dengan Enkripsi AES-256 dan Autentikasi OAuth2. Menjaga kedaulatan data pemerintah dari akses ilegal dan serangan siber.',
            icon: '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z\'></path>',
            color: 'bg-rose-500',
            textColor: 'text-rose-500',
            lightColor: 'bg-rose-50',
            badge: 'SECURE'
        },
        {
            id: 2,
            title: 'High Availability',
            desc: 'Layanan publik online 24/7 tanpa henti.',
            details: 'API Gateway cerdas yang mengatur lalu lintas data otomatis. Mencegah server down saat lonjakan pengguna demi kepuasan masyarakat.',
            icon: '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path>',
            color: 'bg-amber-500',
            textColor: 'text-amber-500',
            lightColor: 'bg-amber-50',
            badge: 'RELIABLE'
        },
        {
            id: 3,
            title: 'Data Compliance',
            desc: 'Transparansi penuh sesuai regulasi Satu Data.',
            details: 'Pencatatan jejak digital (Audit Trail) yang tidak bisa dimanipulasi. Memudahkan pelaporan kinerja dan audit kepatuhan SPBE secara real-time.',
            icon: '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4\'></path>',
            color: 'bg-emerald-500',
            textColor: 'text-emerald-500',
            lightColor: 'bg-emerald-50',
            badge: 'AUDITABLE'
        }
    ],

    init() {
        this.startAutoplay();
    },

    startAutoplay() {
        this.autoplay = setInterval(() => {
            if (!this.isExpanded) {
                this.active = (this.active + 1) % this.slides.length;
            }
        }, 5000);
    },

    stopAutoplay() {
        clearInterval(this.autoplay);
    },

    goTo(index) {
        if (this.active === index && !this.isExpanded) {
            this.isExpanded = true;
        } else {
            this.active = index;
            this.isExpanded = false;
        }
    },

    next() {
        this.isExpanded = false;
        if (this.active < this.slides.length - 1) {
            this.active++;
        } else {
            this.active = 0;
        }
        this.stopAutoplay();
    },

    prev() {
        this.isExpanded = false;
        if (this.active > 0) {
            this.active--;
        }
        this.stopAutoplay();
    },

    closeCard() {
        this.isExpanded = false;
    }
})
