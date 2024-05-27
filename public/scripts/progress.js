export default class Progress {
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
                    });
                }
            }
        });
        this.nextButtons[0].addEventListener('submit', (event) => {
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