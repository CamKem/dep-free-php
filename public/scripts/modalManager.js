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
        this.submitOnConfirm();
    }

    openModalOnSubmit() {
        this.form.addEventListener('submit', (event) => {
            if (event.target === this.form) {
                event.preventDefault();
                this.createEvent('openModal', this.action, this.form);
            }
        });
    }

    submitOnConfirm() {
        this.form.addEventListener('confirmed', (event) => {
            if (event.detail.action === this.action) {
                this.form.submit();
            }
        });
    }

    createEvent(name, action, form) {
        let event = new CustomEvent(name, {
            bubbles: true,
            detail: {action: action, form: form}
        });
        this.form.dispatchEvent(event);
    }

}