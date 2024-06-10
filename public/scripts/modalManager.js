export default class ModalManager {
    constructor(formName, action) {
        this.formName = formName;
        this.action = action;
        this.modals = [];
        this.initManager();
    }

    initManager() {
        document.addEventListener('DOMContentLoaded', () => {
            let forms = Array.from(document.getElementsByName(this.formName));
            forms.forEach((form) => {
                    const modal = new ModalHandler(form, this.action);
                    this.modals.push(modal);
                }
            )
        })
    }

}

class ModalHandler {
    constructor(form, action) {
        this.form = form;
        this.action = action;
        this.handleModal()
    }

    handleModal() {
        this.openModalOnSubmit();
        this.handleEnter();
        this.submitOnConfirm();
    }

    openModalOnSubmit() {
        this.form.addEventListener('submit', (event) => {
            event.preventDefault();
            this.createEvent('openModal', this.action);
        });
    }

    submitOnConfirm() {
        document.addEventListener('confirmed', (event) => {
            if (event.detail.action === this.action) {
                this.form.submit();
            }
        });
    }

    handleEnter() {
        this.form.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                this.createEvent('confirmed', this.action);
            }
        })
    }

    createEvent(name, action) {
        let event = new CustomEvent(name, {
            bubbles: true,
            detail: {action: action}
        });
        this.form.dispatchEvent(event);
    }
}