<script type="module">
    import Modal from '/scripts/modal.js';
    import FormValidator from "/scripts/validation.js";

    document.addEventListener('openModal', (event) => {
        if (event.detail.action === 'product-create') {
            let modal = new Modal('product-create', event.detail.form);
            modal.openModal();

            const imagePlaceholder = document.getElementById('image-placeholder');
            const imageInput = document.querySelector('input[type="file"]');

            imagePlaceholder.addEventListener('click', () => {
                imageInput.click();
            });

            imageInput.addEventListener('change', () => {
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

                            const imagePreview = document.createElement('img');
                            imagePreview.src = '/' + data.filePath;
                            const imageName = data.filePath.split('/').pop();
                            imagePreview.alt = imageName;
                            imagePreview.dataset.image = imageName;
                            imagePreview.classList.add('image-preview');

                            imagePlaceholder.innerHTML = '';
                            imagePlaceholder.appendChild(imagePreview);

                            const flashEvent = new CustomEvent('flashToggle', {
                                bubbles: true,
                                detail: {message: data.message}
                            });
                            document.dispatchEvent(flashEvent);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }
            });
            // TODO extract this logic & the logic in the edit modal to a common class or function
            const form = document.getElementById('create-product-form');
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                // get the image element that is a child of the form
                const image = form.querySelector('.image-preview');
                // append a new hidden input element to the form
                if (image) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'image';
                    // get the image name from the dataset
                    input.value = image.dataset.image;
                    //
                    form.appendChild(input);
                    form.submit();
                }
            });

            if (<?= old('image', false) ? 'true' : 'false' ?> === true) {
                const form = document.getElementById('create-product-form');
                // create a new element in the form to hold the image
                if (form) {
                    // find the file input and set the dataset value to the old image
                    const image = form.querySelector('.image-preview');
                    image.dataset.image = '<?= old('image') ?>';
                }
            }
        }
    });

    window.onload = () => new FormValidator('create-product-form');
</script>


<div id="product-create-modal" class="modal">
    <div class="modal-content wide">
        <span class="close-button">&times;</span>
        <h2 class="general-heading">Create Product</h2>
        <div class="modal-form">

            <form id="create-product-form"
                  action="<?= route('admin.products.store') ?>"
                  method="post"
                  class="product-form"
            >
                <input type="hidden" name="csrf_token"
                       value="<?= csrf_token() ?>">

                <div class="column-1">
                    <div class="placeholder" id="image-placeholder">
                        <?php if (old('image') !== ''): ?>
                            <img src="/images/products/<?= old('image') ?>"
                                 alt="<?= old('name') ?>"
                                 class="image-preview">
                        <?php else: ?>
                            <svg id="image-placeholder-icon" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-7H7v-2h4V7h2v4h4v2h-4v4h-2v-4z"/>
                            </svg>
                            <div id="image-placeholder-text">
                                Click to Upload<br>
                                Product Image
                            </div>
                        <?php endif; ?>
                        <input type="file" id="image" title="Image" name="image"
                               accept="image/*"
                               hidden>
                    </div>
                    <p class="error-message" id="image-error">
                        <?= error('image') ?>
                    </p>

                    <label for="description">Description</label>
                    <textarea name="description"
                              title="Description"
                              id="description"
                              placeholder="Enter a description"
                              data-validate="true"
                              rows="5"
                              autocomplete="description"
                    ><?= old('description') ?></textarea>
                    <p class="error-message" id="description-error">
                        <?= error('description') ?>
                    </p>
                </div>

                <div class="column-2">
                    <label for="username">Name</label>
                    <input type="text"
                           name="name"
                           title="Name"
                           id="name"
                           value="<?= old('name') ?>"
                           placeholder="Enter a name"
                           data-validate="true"
                           autocomplete="product-name"
                    >
                    <p class="error-message" id="name-error">
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

                    <label for="sale_price">Sale Price</label>
                    <input type="number"
                           name="sale_price"
                           step="0.01"
                           title="Sale Price"
                           id="sale_price"
                           value="<?= old('sale_price') ?>"
                           placeholder="Enter a sale price"
                           autocomplete="sale-price"
                    >
                    <p class="error-message" id="sale_price-error">
                        <?= error('sale_price') ?>
                    </p>

                    <label for="category_id">Category:</label>
                    <select name="category_id"
                            id="category_id"
                            title="Category"
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

                    <div class="featured-checkbox">
                        <label for="featured">Featured:</label>
                        <input type="checkbox"
                               name="featured"
                               id="featured"
                               value="1"
                               <?= old('featured') ? 'checked' : '' ?>
                        >
                    </div>
                </div>

                <div class="product-form-bottom">
                    <p class="modal-text">
                        Are you sure you want to create this product?
                    </p>
                    <button type="submit" class="confirm-button">
                        Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>