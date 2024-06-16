export default class Modal {
    constructor(action, form = null) {
        this.action = action;
        this.form = form ? form : action;
        let elementId = action + "-modal";
        this.modal = document.getElementById(elementId);

        if (this.modal) {
            this.modal.addEventListener('click', this.closeOnOutsideClick.bind(this));

            this.closeButton = this.modal.getElementsByClassName("close-button");
            if (this.closeButton.length) {
                this.closeButton[0].addEventListener('click', this.closeModal.bind(this));
            }
            this.confirmButton = this.modal.querySelector("#confirm-" + action);
            if (this.confirmButton) {
                this.confirmButton.addEventListener('click', this.confirmAction.bind(this));
            }
            this.cancelButton = this.modal.querySelector("#cancel-" + action);
            if (this.cancelButton) {
                this.cancelButton.addEventListener('click', this.closeModal.bind(this));
            }
        }
    }

    openModal() {
        this.modal.style.display = "block";
        this.handleEnter();
    }

    closeModal() {
        this.modal.style.display = "none";
    }

    handleEnter() {
        if (typeof this.form !== 'string') {
            this.form.addEventListener('keydown', (event) => {
                event.preventDefault();
                if (event.key === 'Enter') {
                    this.confirmAction();
                }
            })
        }
    }

    confirmAction() {
        if (typeof this.form !== 'string') {
            this.form.dispatchEvent(new CustomEvent('confirmed', {
                bubbles: true,
                detail: {action: this.action},
            }));
        }
        return this.closeModal();
    }

    closeOnOutsideClick(event) {
        if (event.target === this.modal) {
            this.closeModal();
        }
    }
}