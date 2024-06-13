export default class FormValidator {
    constructor(formId, onSubmit = true) {
        this.form = document.getElementById(formId);
        this.fields = Array.from(this.form.querySelectorAll('input, textarea, select'));
        this.fields = this.fields.filter(field => !field.hasAttribute('hidden'));
        this.errors = [];
        this.submitted = false;
        this.debounceTimer = null;
        if (onSubmit) {
            this.form.addEventListener('submit', this.validateForm.bind(this));
        }
        this.form.addEventListener('focusin', this.removeInvalidClass.bind(this), true);
        this.form.addEventListener('input', this.debouncedValidation.bind(this));
    }

    validateFor(fields) {
        fields.forEach(field => {
            this.validateField(field);
        });
        return !this.errors.length;
    }

    debounce(func, delay) {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => func.apply(this), delay);
    }

    removeInvalidClass(arg) {
        const field = this.eventOrEl(arg);
        if (field.value) {
            if (field.classList.contains('invalid')) {
                field.classList.remove('invalid');
            }
        }
    }

    addInvalidClass(field) {
        if (!field.classList.contains('invalid')) {
            field.classList.add('invalid');
        }
    }

    debouncedValidation(event) {
        if (this.submitted) {
            this.debounce(this.validateField, 500, event);
            if (this.fields.includes(event.target)) {
                this.debounce(() => this.validateField(event.target), 300);
            }
        }
    }

    updateErrors(id, message) {
        const error = this.errors.find(error => error.id === id);
        if (error) {
            if (error.message !== message) {
                error.message = message;
            }
        } else {
            this.errors.push({id, message});
        }
    }

    updateAndDisplayErrors(id, message) {
        this.updateErrors(id, message);
        this.updateErrorElement(id, message);
    }

    updateErrorElement(id, message) {
        const field = this.fields.find(field => field.id === id);
        const el = field.nextElementSibling === null
            ? field.parentElement.nextElementSibling
            : field.nextElementSibling;
        el.textContent = '⚠ ' + message.charAt(0).toUpperCase() + message.slice(1);
    }

    clearErrors() {
        this.errors.forEach(error => {
            this.removeError(document.getElementById(error.id));
        });
    }

    removeError(field) {
        // remove the element with the id of the field + '-error'
        const error = document.getElementById(field.id + '-error');
        if (error) {
            error.textContent = '';
            this.removeInvalidClass(field);
            // remove the error from the array of errors
            this.errors = this.errors.filter(error => error.id !== field.id);
        }
    }

    eventOrEl(arg) {
        if (arg instanceof Event) {
            return arg.target;
        } else if (arg instanceof Element) {
            return arg
        }
        return null;
    }

    isValidateable(field) {
        return field.dataset.validate === 'true';
    }

    validateField(field) {
        field = this.eventOrEl(field);
        if (this.isValidateable(field)) {
            let errorMessage = null;
            if (!field.value) {
                this.addInvalidClass(field);
                errorMessage = `${field.title} is required`;
                return this.updateAndDisplayErrors(field.id, errorMessage);
            }
            if (field.tagName !== 'SELECT' || field.type !== 'number' || field.type !== 'checkbox' || field.type !== 'file') {
                if (field.value.length < 3) {
                    this.addInvalidClass(field);
                    errorMessage = `${field.title} must be at least 3 characters long`;
                    return this.updateAndDisplayErrors(field.id, errorMessage);
                }
            }
            if (field.type === 'file') {
                if (!field.files.length) {
                    this.addInvalidClass(field);
                    errorMessage = 'Please select a file';
                    return this.updateAndDisplayErrors(field.id, errorMessage);
                }
            }
            if (field.type === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(field.value)) {
                    this.addInvalidClass(field);
                    errorMessage = 'Please enter a valid email address'
                    return this.updateAndDisplayErrors(field.id, errorMessage);
                }
            }
            if (field.type === 'tel') {
                const phoneRegex = /^\d{10}$/;
                if (!phoneRegex.test(field.value)) {
                    this.addInvalidClass(field);
                    errorMessage = 'Please enter a valid phone number'
                    return this.updateAndDisplayErrors(field.id, errorMessage);
                }
            }
            if (field.type === 'checkbox') {
                if (!field.checked) {
                    this.addInvalidClass(field);
                    errorMessage = 'Please check this box if you want to proceed';
                    return this.updateAndDisplayErrors(field.id, errorMessage);
                }
            }
            if (field.type === 'number') {
                // number regex must be a number, allow up to 2 decimal places
                const numberRegex = /^\d+(\.\d{1,2})?$/;
                if (!numberRegex.test(field.value)) {
                    this.addInvalidClass(field);
                    errorMessage = 'Please enter a valid number'
                    return this.updateAndDisplayErrors(field.id, errorMessage);
                }
            }
            // add one for the field.id === 'postcode' condition
            if (field.id === 'post_code') {
                // postcode regex must be 4 numbers long without spaces or characters other than numbers
                const postcodeRegex = /^\d{4}$/;
                if (!postcodeRegex.test(field.value)) {
                    this.addInvalidClass(field);
                    errorMessage = 'Please enter valid post code'
                    return this.updateAndDisplayErrors(field.id, errorMessage);
                }
            }
            if (field.id === 'card_number') {
                const cardNumberRegex = /^\d{16}$/;
                if (!cardNumberRegex.test(field.value)) {
                    this.addInvalidClass(field);
                    errorMessage = 'Please enter a valid card number'
                    return this.updateAndDisplayErrors(field.id, errorMessage);
                }
            }
            // validate the credit card expiry date
            if (field.id === 'expiry_date') {
                const expiryDateRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
                if (!expiryDateRegex.test(field.value)) {
                    this.addInvalidClass(field);
                    errorMessage = 'Please enter a valid expiry date'
                    return this.updateAndDisplayErrors(field.id, errorMessage);
                }
            }
            // validate the credit card cvv
            if (field.id === 'cvv') {
                const cvvRegex = /^\d{3}$/;
                if (!cvvRegex.test(field.value)) {
                    this.addInvalidClass(field);
                    errorMessage = 'Please enter a valid cvv'
                    return this.updateAndDisplayErrors(field.id, errorMessage);
                }
            }
            // validate the credit card number with the luhn algorithm
            if (field.id.startsWith('card-number-1')) {
                // Concatenate the values of all the card number inputs
                let value = '';
                this.fields.forEach(field => {
                    if (field.id.startsWith('card-number-')) {
                        value += field.value;
                    }
                });

                // Check if the value is a valid number
                if (isNaN(value)) {
                    errorMessage = 'Card number should be a number';
                    return this.updateAndDisplayErrors(field.id, errorMessage);
                }

                // Check if the total length is 16
                if (value.length !== 16) {
                    errorMessage = 'Card number should be 16 digits';
                    return this.updateAndDisplayErrors(field.id, errorMessage);
                }
            }
            if (this.errors.some(error => error.id === field.id)) {
                return this.removeError(field);
            }
        }
    }

    validateForm(event) {
        this.submitted = true;
        this.fields.forEach(field => {
            this.validateField(field);
        });

        if (this.errors.length) {
           event.preventDefault();
        }
        if (!this.errors.length) {
            this.clearErrors();
            return true;
        }
    }

}