<section>
    <h2>Order</h2>
    <div class="standard-container">
        <div class="order">
            <div class="order__details">
                <h3 class="order__heading__top">
                Order Details</h3>
                <p><strong>Order ID:</strong> <?= $order->id ?></p>
                <p><strong>Order Status:</strong> <?= $order->status ?></p>
                <p><strong>Order Date:</strong> <?= $order->purchase_date ?></p>
                <p><strong>Shipping Cost:</strong>
                    $<?= number_format($shipping, 2) ?></p>
                <p><strong>Order Tax:</strong>
                    $<?= number_format(($order->total * $tax), 2) ?></p>
                <?php $total = $order->total += $shipping + ($order->total * $tax); ?>
                <p><strong>Order Total:</strong>
                    $<?= number_format($total, 2) ?></p>
            </div>
            <h3 class="order__heading__middle">Products</h3>
            <div class="order__products">
                <ul class="flex-center">

                    <?php foreach ($order->products as $index => $item): ?>
                        <li class="items">
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
                                    <?= dd($item->category) ?>
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
                                    <label for="quantity">
                                        <input type="number" class="qty"
                                               value="<?= $item->quantity ?>"/>
                                    </label>
                                    <p id="price" class="price">
                                        <span>x </span>
                                        <span>$<?= $item->price ?></span>
                                    </p>
                                </div>
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
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>