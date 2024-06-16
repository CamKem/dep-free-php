<script type="module">
    import ProductModal from '/scripts/productModal.js';

    window.addEventListener('DOMContentLoaded', () => {
        const action = 'product-edit-<?= $product->id ?>';
        const placeholderSelector = `#image-placeholder[data-image="<?= $product->id ?>"]`;
        new ProductModal(action, placeholderSelector);
    });
</script>

<div id="product-edit-<?= $product->id ?>-modal" class="modal">
    <div class="modal-content wide">
        <span class="close-button">&times;</span>
        <h2 class="general-heading">Edit Product</h2>
        <div class="modal-form">

            <form id="product-edit-<?= $product->id ?>"
                  action="<?= route('admin.products.update', ['id' => $product->id]) ?>"
                  method="post"
                  class="product-form"
            >
                <input type="hidden" name="csrf_token"
                       value="<?= csrf_token() ?>">
                <input type="hidden" name="_method" value="PUT">
                <div class="column-1">
                    <div class="placeholder"
                         id="image-placeholder"
                         data-image="<?= $product->id ?>"
                    >
                        <img src="/images/products/<?= $product->image ?>"
                             alt="<?= $product->name ?>"
                             id="image-preview"
                             class="image-preview"
                             data-image="<?= $product->image ?>"
                        >
                        <input type="file" id="image" title="Image" name="image"
                               accept="image/*" data-validate="true" hidden
                        >
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
                              autocomplete="new-description"
                    ><?= $product->description ?></textarea>
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
                           value="<?= $product->name ?>"
                           placeholder="Enter a name"
                           data-validate="true"
                           autocomplete="new-name"
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
                           value="<?= $product->price ?>"
                           placeholder="Enter a price"
                           data-validate="true"
                           autocomplete="price"
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
                           value="<?= $product->sale_price ?>"
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
                            <option value="<?= $category->id ?>"
                                <?= $product->category->id === $category->id ? 'selected' : '' ?>
                            >
                                <?= ucwords($category->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="error-message" id="category_id-error">
                        <?= error('category_id') ?>
                    </p>

                    <div class="featured-checkbox">
                        <label for="featured">Featured:</label>
                        <input type="checkbox" name="featured" id="featured"
                            <?= $product->featured ? 'checked' : '' ?>
                               value="1">
                    </div>
                </div>

                <div class="product-form-bottom">
                    <p class="modal-text">
                        Are you sure you want to edit this product?
                    </p>
                    <button type="submit" class="confirm-button" id="confirm-product-edit">
                        Edit Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>