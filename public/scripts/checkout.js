export class Progress {
    constructor(circles, bars, prevButtons, nextButtons, sections, validator) {
        this.circles = circles;
        this.bars = bars;
        this.nextButtons = nextButtons;
        this.prevButtons = prevButtons;
        this.sections = sections;
        this.validator = validator;
        this.currentStep = 0;
        this.setUp();
    }

    setUp() {
        this.circles.forEach(function (circle) {
            circle.className = 'circle';
        });

        this.bars.forEach(function (bar) {
            bar.className = 'bar';
        });

        this.setUpButtons();
        this.setUpEnterHandler();
        this.forward();
    }

    setUpButtons() {
        this.nextButtons.forEach((button, index) => {
            if (button) {
                if (button.tagName === 'BUTTON') {
                    button.addEventListener('click', () => {
                        const inputs = this.sections[index].querySelectorAll('input');
                        let isValid = this.validator.validateFor(inputs);
                        if (isValid) {
                            this.sections[index].classList.add('hidden');
                            this.sections[index + 1].classList.remove('hidden');
                            this.forward();
                        }
                        if (index === this.sections.length - 2) {
                            const event = new Event('summary');
                            window.dispatchEvent(event);
                        }
                    });
                }
            }
        });

        this.nextButtons[0].form.addEventListener('submit', (event) => {
            event.preventDefault();
            this.forward();
            setTimeout(() => {
                event.target.submit();
            }, 800);
        });

        this.prevButtons.forEach((button, index) => {
            if (button && button.tagName === 'BUTTON') {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    this.sections[index].classList.add('hidden');
                    this.sections[index - 1].classList.remove('hidden');
                    this.back();
                });
            }
        });
    }

    setUpEnterHandler() {
        this.sections.forEach((section, index) => {
            section.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    // if the last section is reached, submit the form
                    if (index === this.sections.length - 1) {
                        this.forward();
                        setTimeout(() => {
                            this.nextButtons[0].click();
                        }, 800);
                    } else {
                        this.nextButtons[index].click();
                    }
                }
            });
        });
    }

    forward() {
        if (this.circles[this.currentStep]) {
            this.circles[this.currentStep].classList.add('active');
        }
        if (this.circles[this.currentStep - 1]) {
            this.circles[this.currentStep - 1].classList.remove('active');
            this.circles[this.currentStep - 1].classList.add('done');
            this.circles[this.currentStep - 1].querySelector('.label').innerHTML = '&#10003;';
        }
        if (this.bars[this.currentStep]) {
            this.bars[this.currentStep].classList.add('active');
        }
        if (this.bars[this.currentStep - 1]) {
            this.bars[this.currentStep - 1].classList.remove('active');
            this.bars[this.currentStep - 1].classList.add('done');
        }
        if (this.currentStep === this.circles.length - 1) {
            this.circles[this.currentStep].classList.add('active');
        }
        this.currentStep++;
    }

    back() {
        if (this.currentStep > 0) {
            this.currentStep--;
            if (this.circles[this.currentStep]) {
                this.circles[this.currentStep].classList.remove('done');
                this.circles[this.currentStep].querySelector('.label').innerHTML = this.currentStep + 1;
            }
            if (this.bars[this.currentStep]) {
                this.bars[this.currentStep].classList.remove('done');
            }
            if (this.circles[this.currentStep - 1]) {
                this.circles[this.currentStep - 1].classList.add('active');
            }
        }
    }
}

export class CardHandler {
    constructor(cardNumberInputs, expiryDateInput) {
        this.inputs = cardNumberInputs;
        this.expiryDateInput = expiryDateInput;
        this.fullCardNumberInput = document.getElementById('card_number');
        this.setUp();
    }

    setUp() {
        this.inputs.forEach((input, index) => {
            input.addEventListener('input', (event) => {
                // Get the value of the input field and remove any whitespace
                const value = event.target.value.replace(/\s/g, '');
                // If the card number is less than 16 digits, return
                if (value.length === 16) {
                    // Split the card number into 4 segments
                    const segments = value.match(/.{1,4}/g) || [];
                    // Fill the input fields with the segments
                    for (let i = 1; i <= 4; i++) {
                        const input = document.getElementById(`card-number-${i}`);
                        input.value = segments[i - 1] || '';
                    }
                }
                if (event.target.value.length >= 4 && index < this.inputs.length - 1) {
                    this.inputs[index + 1].focus();
                    this.inputs[index + 1].select();
                }
            });
            input.addEventListener('focus', () => {
                input.select();
            });
        });

        this.expiryDateInput.addEventListener('input', (event) => {
            // Get the value of the input field
            let value = event.target.value;
            // If the value is 2 characters long and does not yet contain a slash
            if (value.length === 2 && !value.includes('/')) {
                // Append a slash to the value
                event.target.value += '/';
            }
            // If the value is 3 characters long and ends with a slash
            else if (value.length === 3 && value.endsWith('/')) {
                // Remove the slash
                event.target.value = value.slice(0, -1);
            }
        });

        this.inputs.forEach(input => {
            input.addEventListener('input', () => {
                this.fullCardNumberInput.value = this.inputs.map(input => input.value).join('');
            });
        });
    }
}