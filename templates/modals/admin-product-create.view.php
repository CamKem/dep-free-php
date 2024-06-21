<script type="module">
    import ProductModal from '/scripts/productModal.js';

    window.addEventListener('DOMContentLoaded', () => {
        const action = 'product-create'
        const placeholderSelector = `#image-placeholder[data-image="empty"]`;
        new ProductModal(action, placeholderSelector);
    });
</script>


<div id="product-create-modal" class="modal">
    <div class="modal-content wide">
        <span class="close-button">&times;</span>
        <h2 class="general-heading">Create Product</h2>
        <div class="modal-form">

            <form id="product-create"
                  action="<?= route('admin.products.store') ?>"
                  method="post"
                  class="product-form"
            >
                <input type="hidden" name="csrf_token"
                       value="<?= csrf_token() ?>">

                <div class="column-1">
                    <div class="placeholder"
                         id="image-placeholder"
                         data-image="empty"
                    >
                        <img src="/images/products/<?= old('image', 'default.svg') ?>"
                             alt="<?= old('name') ?>"
                             class="image-preview"
                             data-image="<?= old('image', 'default.svg') ?>"
                        >
                        <!-- todo: remove the name on this hidden tag, so there is no change of it being submitted and causing a conflict -->
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
                    <button type="submit" class="confirm-button" id="confirm-product-create">
                        Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>