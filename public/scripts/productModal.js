import Modal from '/scripts/modal.js';
import FormValidator from "/scripts/validation.js";

export default class ProductModal {
    constructor(action, placeholderSelector) {
        this.action = action;
        //this.form = document.getElementById(action);
        this.modal = new Modal(action);
        this.validator = new FormValidator(action);
        this.form = this.modal.modal.querySelector('form');
        this.placeholder = this.form.querySelector('#image-placeholder');
        this.init();
    }

    init() {
        document.addEventListener('openModal', (event) => {
            if (event.detail.action === this.action) {
                event.stopPropagation();
                this.modal.openModal();
                // set the focus to the first input field
                this.handleImageUpload();
                this.handleEnterKey();
                this.handleSubmit();
            }
        });
    }

    handleImageUpload() {
        const imageInput = this.placeholder.querySelector('input[type="file"]');
        this.placeholder.addEventListener('click', () => {
            imageInput.click();
        });

        imageInput.addEventListener('change', (event) => {
            const file = imageInput.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('image', file);

                fetch('/admin/products/image', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.filePath) {
                            const flashEvent = new CustomEvent('flashToggle', {
                                bubbles: true,
                                detail: {message: data.message}
                            });
                            document.dispatchEvent(flashEvent);
                            return;
                        }

                        // find the correct image preview using the form as a reference
                        const image = this.form.querySelector('.image-preview');

                        if (image) {
                            image.src = '/' + data.filePath;
                            const name = data.filePath.split('/').pop();
                            image.alt = name;
                            image.dataset.image = name;

                            document.dispatchEvent(new CustomEvent('flashToggle', {
                                bubbles: true,
                                detail: {message: data.message}
                            }));
                        }
                    })
            }
        });
    }

    handleEnterKey() {
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && this.modal) {
                this.runSubmit(event);
            }
        });
    }

    handleSubmit() {
        this.form.addEventListener('submit', (event) => {
            this.runSubmit(event);
        });
    }

    runSubmit(event) {
        this.validator.validateForm(event);
        const image = this.form.querySelector('.image-preview');
        if (image && this.validator.errors.length === 0) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'image';
            input.value = image.dataset.image;
            this.form.appendChild(input);
            this.form.submit();
        } else {
            this.modal.openModal();
        }
    }

}