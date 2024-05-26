class Checkout {
    constructor(totalSteps, cart) {
        this.currentStep = 1;
        this.totalSteps = totalSteps;
        this.cart = cart;
        this.nextButton = document.getElementById('next-button');
        this.form = document.getElementById('checkout-form');
        this.circles = document.querySelectorAll('.checkout-progress .circles .circle');
        this.orderSummary = document.querySelector('.order-summary');
    }

    updateProgressBar() {
        this.circles[this.currentStep - 1].classList.add('active');
        if (this.currentStep > 1) {
            this.circles[this.currentStep - 2].classList.remove('active');
            this.circles[this.currentStep - 2].classList.add('done');
        }
    }

    updateOrderSummary() {
        // Update the order summary based on the current state of the cart
        // This will depend on the structure of your cart and how you want to display the order summary
    }

    handleFormSubmission() {
        this.form.addEventListener('submit', (event) => {
            event.preventDefault();
            // Handle form submission
            // This will depend on how you want to handle form submission
        });
    }

    handleNextButtonClick() {
        this.nextButton.addEventListener('click', () => {
            // Hide current step
            document.getElementById('step' + this.currentStep).style.display = 'none';

            // Show next step
            this.currentStep++;
            document.getElementById('step' + this.currentStep).style.display = 'block';

            // Update the progress bar
            this.updateProgressBar();

            // Update the order summary
            this.updateOrderSummary();

            // If we're on the last step, change the button to a submit button
            if (this.currentStep === this.totalSteps) {
                this.nextButton.type = 'submit';
                this.nextButton.textContent = 'Submit';
            }
        });
    }

    init() {
        this.handleNextButtonClick();
        this.handleFormSubmission();
    }
}