<script type="module">
    import ModalManager from '/scripts/modalManager.js';

    new ModalManager('admin-order-delete', 'delete');
</script>
<?= add('modals.confirmation', ['action' => 'delete']) ?>
<div class="standard-container">
    <div class="admin-form-actions">
        <script>
            window.onload = function () {
                const status = document.getElementById('order-status');
                status.addEventListener('change', function () {
                    document.getElementById('order-status-<?= $order->id ?>').submit();
                });
            };
        </script>
        <form method="post"
              class="status-form"
              id="order-status-<?= $order->id ?>"
              action="<?= route('admin.orders.update', ['id' => $order->id]) ?>">
            <input type="hidden" name="_method"
                   value="PUT">
            <label for="status">Update Status:</label>
            <select name="status" id="order-status">
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= $status ?>"
                        <?= $order->status === $status ? 'selected' : '' ?>
                    ><?= ucwords($status) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <form method="post"
              id="delete-order-<?= $order->id ?>"
              name="admin-order-delete"
              action="<?= route('admin.orders.destroy', ['id' => $order->id]) ?>">
            <input type="hidden" name="_method"
                   value="DELETE">
            <button class="delete-button">Delete order</button>
        </form>
    </div>
    <div class="order">
        <div class="content__details">
            <h3 class="content__heading__top">
                Order Details
            </h3>
            <p><strong>Order ID:</strong> <?= $order->id ?></p>
            <p><strong>Order Status:</strong> <?= $order->status ?></p>
            <p><strong>Order Date:</strong> <?= $order->purchase_date ?></p>
            <p><strong>Shipping Cost:</strong>
                $<?= number_format($shipping, 2) ?></p>
            <p><strong>Order Tax:</strong>
                $<?= number_format(($tax * $order->total), 2) ?></p>
            <p><strong>Order Total:</strong>
                $<?= number_format(($order->total + $shipping + ($order->total * $tax)), 2) ?>
            </p>
        </div>
        <h3 class="content__heading__middle">Products</h3>
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
                                <label for="quantity">
                                    <?php $quantity = $item->toArray()['pivot']['quantity'] ?>
                                    <span class="qty"><?= $quantity ?></span>
                                </label>
                                <p id="price" class="price">
                                    <span>x </span>
                                    <span>$<?= $item->price ?></span>
                                </p>
                            </div>
                        </div>

                        <div class="prodTotal cartSection"
                             id="prodTotal">
                            <p class="line-price" id="line-price">
                                <span>$<?= number_format(($quantity * $item->price), 2) ?></span>
                            </p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>