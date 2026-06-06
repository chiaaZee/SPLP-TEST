export default () => ({
    active: 0,
    autoplay: null,

    slides: [
        {
            title: 'Seamless Integration',
            category: 'CONNECTED',
            color: 'bg-blue-600',
            icon: '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4\'></path>'
        },
        {
            title: 'Gov-Grade Security',
            category: 'SECURE',
            color: 'bg-rose-500',
            icon: '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z\'></path>'
        },
        {
            title: 'High Performance',
            category: 'FAST',
            color: 'bg-amber-500',
            icon: '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path>'
        },
        {
            title: 'Audit Ready',
            category: 'COMPLIANT',
            color: 'bg-emerald-500',
            icon: '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4\'></path>'
        }
    ],

    init() {
        this.startAutoplay();
    },

    startAutoplay() {
        this.autoplay = setInterval(() => {
            this.active = (this.active + 1) % this.slides.length
        }, 4000);
    },

    stopAutoplay() {
        clearInterval(this.autoplay);
    },

    goTo(index) {
        this.active = index;
        this.stopAutoplay();
        this.startAutoplay();
    }
})
