export default class FlashManager {
    constructor(text) {
        this.flashMessages = [];
        if (text !== '') {
            const flashMessage = new FlashMessage(text, this);
            this.flashMessages.push(flashMessage);
        }
        document.addEventListener('flashToggle', (event) => {
            if (event.detail.message) {
                const flashMessage = new FlashMessage(event.detail.message, this);
                this.flashMessages.push(flashMessage);
            }
        });
    }

    removeMessage(message) {
        const index = this.flashMessages.indexOf(message);
        if (index > -1) {
            this.flashMessages.splice(index, 1);
        }
    }
}

class FlashMessage {
    constructor(text, manager) {
        this.manager = manager;
        this.flash = this.createFlashMessage();
        this.flash.innerHTML = text;
        this.flashIn();
        this.flashOut();
    }

    createFlashMessage() {
        const flash = document.createElement('div');
        flash.className = 'hidden flash-message';
        const index = this.manager.flashMessages.length;
        const position = index === 0 ? 20 : index * 55 + 20;
        flash.style.bottom = `${position}px`;
        document.body.appendChild(flash);
        return flash;
    }

    flashIn() {
        this.flash.classList.remove('hidden');
        this.flash.classList.add('slide-in');
    }

    flashOut() {
        const handleAnimationEnd = () => {
            this.flash.classList.remove('slide-out');
            this.flash.classList.add('hidden');
            this.flash.removeEventListener('animationend', handleAnimationEnd);
            this.manager.removeMessage(this);
        }
        setTimeout(() => {
            this.flash.addEventListener('animationend', handleAnimationEnd);
            this.flash.classList.remove('slide-in');
            this.flash.classList.add('slide-out');
        }, 3500);
    }
}