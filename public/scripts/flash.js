export default class Flash {
    constructor(text) {
        this.flash = this.createFlashMessage();
        this.flashText = text;
        if (this.flashText !== '') {
            this.flashIn(this.flashText);
            this.flashOut();
        }
        document.addEventListener('flashToggle', (event) => {
            if (event.detail.message) {
                this.flashIn(event.detail.message);
                this.flashOut();
            }
        });
    }

    createFlashMessage() {
        const flash = document.createElement('div');
        flash.id = 'flash';
        flash.className = 'hidden flash-message';
        document.body.appendChild(flash);
        return flash;
    }

    flashIn(text) {
        this.flash.innerHTML = '';
        this.flash.innerHTML = text;
        this.flash.classList.remove('hidden');
        this.flash.classList.add('slide-in');
    }

    flashOut() {
        const handleAnimationEnd = () => {
            this.flash.classList.remove('slide-out');
            this.flash.classList.add('hidden');
            this.flash.removeEventListener('animationend', handleAnimationEnd);
        }
        setTimeout(() => {
            this.flash.addEventListener('animationend', handleAnimationEnd);
            this.flash.classList.remove('slide-in');
            this.flash.classList.add('slide-out');
        }, 3500);
    }
}