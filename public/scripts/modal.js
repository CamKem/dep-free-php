export default class Modal {
    constructor(action) {
        this.action = action;
        let elementId = action + "-confirmation-modal";
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
    }

    closeModal() {
        this.modal.style.display = "none";
    }

    confirmAction() {
        let confirm = new CustomEvent('confirmed', {
            bubbles: true,
            detail: {
                action: this.action
            },
        });
        this.modal.dispatchEvent(confirm);
        return this.closeModal();
    }

    closeOnOutsideClick(event) {
        if (event.target === this.modal) {
            this.closeModal();
        }
    }
}