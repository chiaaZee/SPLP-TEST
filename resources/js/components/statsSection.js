export default (initialStats) => ({
    stats: {
        katalog: '0',
        endpoint: '0',
        total_request: '0'
    },
    targetStats: initialStats || { katalog: 0, endpoint: 0, total_request: 0 },
    hasAnimated: false,

    init() {
        // Use Intersection Observer to trigger animation when visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.hasAnimated) {
                    this.startAnimation();
                    this.hasAnimated = true;
                    observer.disconnect(); // Stop observing once animated
                }
            });
        }, { threshold: 0.5 }); // Trigger when 50% of the element is visible

        observer.observe(this.$el);
    },

    startAnimation() {
        this.animateValue('katalog', this.targetStats.katalog);
        this.animateValue('endpoint', this.targetStats.endpoint, true);
        this.animateValue('total_request', this.targetStats.total_request);
    },

    formatNumber(value) {
        if (value >= 1000000) {
            return (value / 1000000).toFixed(1) + 'M';
        }
        if (value >= 1000) {
            return (value / 1000).toFixed(1) + 'k';
        }
        return value.toString();
    },

    animateValue(property, end, addPlus = false) {
        let start = 0;
        let duration = 2000; // 2 seconds
        let startTime = null;
        let finalValue = parseInt(end);

        const step = (timestamp) => {
            if (!startTime) startTime = timestamp;
            const progress = Math.min((timestamp - startTime) / duration, 1);

            // Ease out quart
            const easeProgress = 1 - Math.pow(1 - progress, 4);

            let current = Math.floor(easeProgress * (finalValue - start) + start);

            let formatted = this.formatNumber(current);
            if (addPlus && current > 0) formatted += '+';

            this.stats[property] = formatted;

            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                // Ensure final value is exact and formatted
                let finalFormatted = this.formatNumber(finalValue);
                if (addPlus) finalFormatted += '+';
                this.stats[property] = finalFormatted;
            }
        };

        window.requestAnimationFrame(step);
    }
})
