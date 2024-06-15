export default class Modal {
    constructor(action, form) {
        this.action = action;
        this.form = form
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
        if (!this.form) return;
        this.form.addEventListener('keydown', (event) => {
            event.preventDefault();
            if (event.key === 'Enter') {
                this.confirmAction();
            }
        })
    }

    confirmAction() {
        let confirm = new CustomEvent('confirmed', {
            bubbles: false,
            detail: {action: this.action},
        });
        this.form.dispatchEvent(confirm);
        return this.closeModal();
    }

    closeOnOutsideClick(event) {
        if (event.target === this.modal) {
            this.closeModal();
        }
    }
}