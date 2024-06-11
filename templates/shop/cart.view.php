<script type="module">
    import Cart from '/scripts/cart.js';
    import ModalManager from "/scripts/modalManager.js";

    new Cart(<?= $taxRate ?>, <?= $shipping ?>);
    new ModalManager('remove-form', 'remove');
</script>
<?= add('modals.confirmation', ['action' => 'remove']) ?>
<section>
    <h2>Shopping Cart</h2>
    <?php if ($cart->count() === 0): ?>
        <div class="standard-container">
            <p class="text-section">Your cart is empty</p>
        </div>
    <?php else: ?>
        <?php $total = 0; ?>
        <div class="wrap cf">
            <ul class="cart flex-center">
                <?php foreach ($cart as $index => $item): ?>
                    <li class="items <?= $index % 2 === 0 ? '' : 'even' ?>">
                        <div class="cartSection product-details-section">
                            <img src="/images/products/<?= $item->image ?>"
                                 alt="<?= $item->name ?>"
                                 class="itemImg">
                            <p class="itemNumber">
                                #QUE-007544-00<?= $index ?>
                            </p>
                        </div>

                        <div class="cartSection product-title-section">
                            <h3>
                                <a href="<?= route('products.show', [
                                    'category' => $item->category->slug,
                                    'product' => $item->slug
                                ]) ?>">
                                    <?= ucwords($item->name) ?>
                                </a>
                            </h3>
                        </div>

                        <div class="cartSection quantity-price-section">
                            <div>
                                <input type="hidden" name="product_id"
                                       value="<?= $item->id ?>">
                                <input type="number" class="qty"
                                       value="<?= $item->quantity ?>"/>
                                <p id="price" class="price">
                                    <span>x </span>
                                    <span>$<?= $item->price ?></span>
                                </p>
                            </div>

                            <p class="stockStatus">In Stock</p>
                            <!-- <p class="stockStatus out">Stock Out</p> -->
                        </div>

                        <div class="prodTotal cartSection"
                             id="prodTotal">
                            <?php
                            $line = ($item->price * $item->quantity);
                            $total += $line;
                            ?>
                            <p class="line-price" id="line-price">
                                <span>$<?= number_format($line, 2) ?></span>
                            </p>
                        </div>
                        <div class="cartSection removeWrap">
                            <form action="<?= route('cart.destroy') ?>"
                                  method="post"
                                  name="remove-form"
                                  id="remove-form"
                            >
                                <input type="hidden" name="_method"
                                       value="DELETE">
                                <input type="hidden" name="product_id"
                                       value="<?= $item->id ?>">
                                <button type="submit"
                                        class="remove"
                                        aria-label="remove from cart">
                                    <i class="fas fa-xmark"
                                       aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="promoCode"><label for="promo">Have A Promo Code?</label>
                <label for="promo" aria-label="promo code input field">
                    <input type="text" name="promo" placeholder="Enter Code"/>
                </label>
                <a href="#" class="btn"></a>
            </div>

            <div class="subtotal cf" id="cart-totals">
                <ul>
                    <li class="totalRow"><span
                                class="label">Subtotal</span>
                        <span id="subTotalPrice"
                              class="value">$<?= number_format($total, 2) ?></span>
                    </li>

                    <li class="totalRow">
                        <span class="label">Shipping</span>
                        <span id="shippingPrice"
                              class="value">$<?= number_format($shipping, 2) ?></span>
                    </li>

                    <li class="totalRow">
                        <span class="label">Tax</span>
                        <?php $tax = $total * $taxRate ?>
                        <span id="taxPrice"
                              class="value">$<?= number_format($tax, 2) ?></span>
                    </li>
                    <li class="totalRow final">
                        <span class="label">Total</span>
                        <span id="totalPrice"
                              class="value">$<?= number_format(($total + $tax + $shipping), 2) ?></span>
                    </li>
                    <li class="totalRow">
                        <a href="<?= route('checkout') ?>" class="btn continue">Checkout</a>
                    </li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</section>