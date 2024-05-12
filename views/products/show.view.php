<section>
    <h2>Product Details</h2>
    <div class="product flex-center standard-container">
        <img src="/images/products/<?= $product->image ?>" alt="<?= $product->name ?>">
        <h3 class="general-heading"><?= $product->name ?></h3>
        <p class="general-text" style="text-align: left"><?= $product->description ?></p>
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
        <p class="general-text">Category: <?= $product->category_name ?></p>

        <form action="" method="post" id="add-to-cart-form" class="flex-center">
            <div class="quantity-selector">
                <label for="quantity">Quantity:</label>
                <button type="button" onclick="changeQuantity(-1)" aria-label="Decrease quantity">-</button>
                <input type="text" id="quantity" name="quantity" value="1" min="1" max="10" class="quantity-input">
                <button type="button" onclick="changeQuantity(1)" aria-label="Increase quantity">+</button>
            </div>
            <button type="submit" class="button-padding">Add to Cart</button>
        </form>
    </div>
</section>

<style>
    .quantity-selector {
        margin: 20px 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quantity-selector label {
        margin-right: 10px;
    }

    .quantity-selector input {
        width: 50px;
        height: 30px;
        text-align: center;
    }

    .quantity-selector button {
        width: 30px;
        height: 30px;
        margin: 5px 5px 10px;
        font-size: 16px;
        line-height: 1;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 4px;
        place-content: center;
        place-items: center;
        place-self: center;
    }
</style>

<script>
    function changeQuantity(change) {
        const quantityInput = document.getElementById('quantity');
        const currentQuantity = parseInt(quantityInput.value);
        let newQuantity = currentQuantity + change;
        if (newQuantity < 1) newQuantity = 1;
        if (newQuantity > 10) newQuantity = 10;
        quantityInput.value = newQuantity;
    }
</script>