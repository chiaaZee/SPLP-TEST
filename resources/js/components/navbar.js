export default () => ({
    isMenuOpen: false,
    scrolled: false,

    navLinks: [
        { name: 'Beranda', id: '#beranda' }, 
        { name: 'Tentang', id: '#tentang' }, 
        { name: 'Fitur', id: '#fitur' }, 
        { name: 'Kontak', id: '#kontak' }
    ],

    init() {
        window.addEventListener('scroll', () => {
            this.scrolled = window.pageYOffset > 20;
        });
    },

    toggleMenu() {
        this.isMenuOpen = !this.isMenuOpen;
    },
    
    closeMenu() {
        this.isMenuOpen = false;
    }
})