<section>
    <h2>Shopping Cart</h2>
    <!-- TODO: set up the shopping cart functionality -->
    <?php if ($cart->count() === 0): ?>
        <p>Your cart is empty</p>
    <?php else: ?>
        <table class="cart">
            <tbody>
            <?php foreach ($cart->toArray() as $item): ?>
                <tr>
                    <td class="cart-item-info">
                        <h4 class="general-heading" style="font-size: 18px">
                            <a href="<?= route('products.show', [
                                'category' => $item['category']['slug'],
                                'product' => $item['slug']
                            ]) ?>">
                        </h4>
                    </td>
                </tr>
                <tr class="cart-item">
                    <td>
                        <img class="cart-item-image" src="/images/products/<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
                    </td>
                    <td class="price">
                        <strong>$<?= $item['price'] ?></strong>
                    </td>
                    <td class="quantity">
                        <label for="quantity-<?= $item['id'] ?>" class="sr-only">Quantity</label>
                        <input type="text" id="quantity-<?= $item['id'] ?>" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="10">
                    </td>
                    <td>
                        <form action="<?= route('cart.destroy') ?>" method="post">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <button type="submit" aria-label="remove from cart">Remove from Cart</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <form action="<?= route('cart.destroy') ?>" method="post">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="all" value="true">
            <button type="submit" aria-label="clear cart">Clear Cart</button>
        </form>
    <?php endif; ?>
</section>
