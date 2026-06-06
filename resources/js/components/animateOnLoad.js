export default () => ({
    visible: false,
    init() {
        setTimeout(() => this.visible = true, 100);
    }
})