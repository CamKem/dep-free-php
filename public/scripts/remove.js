export default class RemoveManager {
    constructor(formName) {
        this.formName = formName;
        this.removeConfirmations = [];
        this.handleRemove()
    }

    handleRemove() {
        document.addEventListener('DOMContentLoaded', () => {
            let removeForms = Array.from(document.getElementsByName(this.formName));
            removeForms.forEach((removeForm) => {
                    const removeConfirmation = new RemoveConfirmation(removeForm);
                    this.removeConfirmations.push(removeConfirmation);
                }
            )
        })
    }

    /* Note: This method is for if we decide to use fetch to remove items from the cart */
    removeConfirmation(removeForm) {
        const index = this.removeConfirmations.findIndex(rc => rc.removeForm === removeForm);
        if (index > -1) {
            this.removeConfirmations.splice(index, 1);
        }
    }

}

class RemoveConfirmation {
    constructor(removeForm) {
        this.removeForm = removeForm;
        this.handleRemove()
    }

    handleRemove() {
        this.openModalOnSubmit();
        this.handleEnter();
        this.submitOnConfirm();
    }

    openModalOnSubmit() {
        this.removeForm.addEventListener('submit', (event) => {
            event.preventDefault();
            this.createEvent('openModal', 'open');
        });
    }

    submitOnConfirm() {
        document.addEventListener('confirmed', (event) => {
            if (event.detail.action === 'remove' || 'delete') {
                this.removeForm.submit();
            }
        });
    }

    handleEnter() {
        this.removeForm.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                this.createEvent('confirmed', 'remove');
            }
        })
    }

    createEvent(name, action) {
        let event = new CustomEvent(name, {
            bubbles: true,
            detail: {action: action}
        });
        this.removeForm.dispatchEvent(event);
    }
}