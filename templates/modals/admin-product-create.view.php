<script type="module">
    import Modal from '/scripts/modal.js';
    import FormValidator from "/scripts/validation.js";

    document.addEventListener('openModal', (event) => {
        if (event.detail.action === 'product-create') {
            let modal = new Modal('product-create', event.detail.form);
            modal.openModal();

            const imagePlaceholder = document.getElementById('image-placeholder');
            const imageInput = document.getElementById('image');

            imagePlaceholder.addEventListener('click', () => {
                imageInput.click();
            });

            imageInput.addEventListener('change', () => {
                const file = imageInput.files[0];
                if (!file) return;

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

                        const imagePreview = document.createElement('img');
                        imagePreview.src = '/' + data.filePath;
                        const imageName = data.filePath.split('/').pop();
                        imagePreview.alt = imageName;
                        imagePreview.classList.add('image-preview');

                        imagePlaceholder.innerHTML = '';
                        imagePlaceholder.appendChild(imagePreview);

                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.value = imageName;
                        hiddenInput.name = 'image';
                        document.getElementById('create-product-form').appendChild(hiddenInput);

                        const flashEvent = new CustomEvent('flashToggle', {
                            bubbles: true,
                            detail: {message: data.message}
                        });
                        document.dispatchEvent(flashEvent);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        }
    });

    window.onload = () => new FormValidator('create-product-form');
</script>

<style>
    .placeholder {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: auto;
        background-color: #f0f0f0;
        border: 2px dashed #ccc;
        border-radius: 10px;
        font-size: 18px;
        padding: var(--default-padding);
        margin: var(--small-margin) 0;
        color: #ccc;
        text-align: center;
        line-height: 1.2;
        font-family: Arial, sans-serif;
        cursor: pointer;
        position: relative;
    }

    .placeholder img {
        max-width: 75%;
        max-height: 75%;
        border-radius: var(--large-radius);
    }

    .placeholder svg {
        width: 50px;
        height: 50px;
        fill: #ccc;
    }

    .placeholder input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 1;
    }

    .placeholder:hover {
        border-color: #999;
        color: #999;
    }

    .placeholder:hover svg {
        fill: #999;
    }

</style>

<div id="product-create-modal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2 class="general-heading">Create Product</h2>
        <div class="modal-form">
            <form id="create-product-form"
                  action="<?= route('admin.products.store') ?>"
                  method="post">

                <div class="placeholder" id="image-placeholder">
                    <svg id="image-placeholder-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-7H7v-2h4V7h2v4h4v2h-4v4h-2v-4z"/>
                    </svg>
                    <div id="image-placeholder-text">Click to Upload<br>Product Image</div>
                    <input type="file" id="image" title="Image" name="image" accept="image/*" data-validate="true" hidden>
                </div>
                <p class="error-message">
                    <?= error('image') ?>
                </p>

                <label for="username">Name</label>
                <input type="text"
                       name="name"
                       title="Name"
                       id="name"
                       value="<?= old('name') ?>"
                       placeholder="Enter a name"
                       data-validate="true"
                       autocomplete="new-name"
                >
                <p class="error-message">
                    <?= error('name') ?>
                </p>

                <label for="price">Price</label>
                <input type="number"
                       name="price"
                       step="0.01"
                       title="Price"
                       id="price"
                       value="<?= old('price') ?>"
                       placeholder="Enter a price"
                       data-validate="true"
                       autocomplete="new-price"
                >
                <p class="error-message" id="price-error">
                    <?= error('price') ?>
                </p>

                <label for="category_id">Category:</label>
                <select name="category_id"
                        id="category_id"
                        title="Category"
                        data-validate="true"
                >
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category->id ?>">
                            <?= ucwords($category->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="error-message" id="category_id-error">
                    <?= error('category_id') ?>
                </p>

                <label for="description">Description</label>
                <textarea name="description"
                          title="Description"
                          id="description"
                          placeholder="Enter a description"
                          data-validate="true"
                          autocomplete="new-description"
                ><?= old('description') ?></textarea>
                <p class="error-message" id="description-error">
                    <?= error('description') ?>
                </p>

                <p class="modal-text">
                    Are you sure you want to create this product?
                </p>
                <button type="submit" class="confirm-button">
                    Create Product
                </button>
            </form>
        </div>
    </div>
</div>