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
                <ul class="cart flex-center">
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
<!--                <ul>-->
<!--                    --><?php //foreach ($order->products->toArray() as $product) : ?>
<!--                        <li style="list-style-type: disc;">-->
<!--                            <a style="color: #0079A6;"-->
<!--                               href="--><?php //= route('products.show', ['product' => $product['slug']]) ?><!--">-->
<!--                                --><?php //= $product['name'] ?>
<!--                            </a>-->
<!--                        </li>-->
<!--                    --><?php //endforeach; ?>
<!--                </ul>-->
            </div>
        </div>
    </div>
</section>

<style>
    .order {
        background-color: var(--very-light-grey-color);
        border-radius: var(--medium-radius);
        border: 1px solid var(--light-grey-color);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .order__products {
        margin-bottom: var(--large-margin);
        padding: var(--default-padding);
    }

    .order__heading__top {
        font-size: var(--font-default);
        color: var(--grey-color);
        margin-bottom: var(--small-margin);
        background-color: var(--light-grey-color);
        padding: var(--small-padding);
        border-top-left-radius: var(--medium-radius);
        border-top-right-radius: var(--medium-radius);
    }

    .order__heading__middle {
        font-size: var(--font-default);
        color: var(--grey-color);
        margin-bottom: var(--small-margin);
        background-color: var(--light-grey-color);
        padding: var(--small-padding);
    }

    .order__details p, .order__products p {
        font-size: var(--font-small);
        color: var(--mid-grey-color);
        margin-bottom: var(--small-margin);
        padding-left: var(--default-padding);
    }

    .order__products ul {
        list-style-type: disc;
        margin-left: var(--default-padding);
    }

    .order__products ul li {
        margin-bottom: var(--small-margin);
    }

    .order__products ul li a {
        color: var(--blue-color);
        font-weight: bold;
        text-decoration: none;
    }

    .order__products ul li a:hover {
        color: var(--dark-blue-color);
        text-decoration: underline;
    }
</style>