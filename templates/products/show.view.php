<section>
    <h2>Product Details</h2>

    <div class="standard-container flex-center">
        <div class="flex-center row">
            <div class="product-details-image">
                <img src="/images/products/<?= $product->image ?>" alt="<?= $product->name ?>">
            </div>
            <div class="product-details-info">
                <div class="general-heading"><?= $product->name ?></div>
                <p class="price">
                    <?php if ($product->sale_price): ?>
                        <strong class="price-sale">$<?= $product->sale_price ?></strong>
                        <span class="original-price-group">
                                    <span class="was-text">WAS</span>
                                    <del>$<?= $product->price ?></del>
                                </span>
                    <?php else: ?>
                        <strong>$<?= $product->price ?></strong>
                    <?php endif; ?>
                </p>
                <p class="product-details-description"><?= $product->description ?></p>
                <form action="<?= route('cart.store') ?>" method="post">
                    <input type="hidden" name="product_id" value="<?= $product->id ?>">
                    <div class="quantity-selector">
                        <label for="quantity">Quantity:</label>
                        <button type="button" onclick="changeQuantity(-1)" aria-label="Decrease quantity">-</button>
                        <input type="text" id="quantity" name="quantity" value="1" min="1" max="10">
                        <button type="button" onclick="changeQuantity(1)" aria-label="Increase quantity">+</button>
                    </div>
                    <button type="submit" aria-label="add to cart">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</section>