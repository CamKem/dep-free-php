<script type="module">
    import {RemoveConfirmation, Cart} from '/scripts/cart.js'

    new Cart(<?= $taxRate ?>, <?= $shipping ?>)
    new RemoveConfirmation()
</script>
<?= add('modals.confirmation', ['action' => 'remove']) ?>
<section>
    <h2>Shopping Cart</h2>
    <?php if ($cart->count() === 0): ?>
        <p class="general-text">Your cart is empty</p>
    <?php else: ?>
        <?php $total = 0; ?>
        <div class="wrap cf">
            <div class="cart">
                <ul class="cartWrap">
                    <?php foreach ($cart->toArray() as $index => $item): ?>
                        <li class="items <?= $index % 2 === 0 ? '' : 'even' ?>">
                            <div class="infoWrap">
                                <div class="cartSection">
                                    <img src="/images/products/<?= $item['image'] ?>"
                                         alt="<?= $item['name'] ?>"
                                         class="itemImg">
                                    <p class="itemNumber">
                                        #QUE-007544-00<?= $index ?>
                                    </p>
                                    <h3>
                                        <a href="<?= route('products.show', [
                                            'category' => $item['category']['slug'],
                                            'product' => $item['slug']
                                        ]) ?>">
                                            <?= $item['name'] ?>
                                        </a>
                                    </h3>

                                    <p>
                                        <input type="hidden" name="product_id"
                                               value="<?= $item['id'] ?>">
                                        <input type="text" class="qty"
                                               value="<?= $item['quantity'] ?>"/>
                                    </p>
                                    <p id="price" class="price">
                                        <span>x </span>
                                        <span>$<?= $item['price'] ?></span>
                                    </p>

                                    <p class="stockStatus">In Stock</p>
                                    <p class="stockStatus out">Stock Out</p>
                                </div>

                                <div class="prodTotal cartSection"
                                     id="prodTotal">
                                    <?php
                                    $line = ($item['price'] * $item['quantity']);
                                    $total += $line;
                                    ?>
                                    <p class="line-price" id="line-price">
                                        <span>$<?= number_format($line, 2) ?></span>
                                    </p>
                                </div>
                                <div class="cartSection removeWrap">
                                    <form action="<?= route('cart.destroy') ?>"
                                          method="post"
                                          id="remove-form"
                                    >
                                        <input type="hidden" name="_method"
                                               value="DELETE">
                                        <input type="hidden" name="product_id"
                                               value="<?= $item['id'] ?>">
                                        <button type="submit"
                                                class="remove"
                                                aria-label="remove from cart">
                                            <i class="fas fa-xmark"
                                               aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="promoCode"><label for="promo">Have A Promo Code?</label>
                <input type="text" name="promo" placeholder="Enter Code"/>
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
                        <a href="#" class="btn continue">Checkout</a>
                    </li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php
// TODO: Add the following styles to the main.css file
//  make sure the cart styling is consistent with the rest of the site
//  and that im happy with the design
?>

<style>
    .cf:before,
    .cf:after {
        content: " ";
        display: table;
    }

    .cf:after {
        clear: both;
    }

    .cf {
        width: 95%;
        margin: 0 auto;
        zoom: 1;
    }

    .items {
        display: block;
        width: 100%;
        vertical-align: middle;
        padding: 1.5em;
    }

    .items.even {
        background: var(--very-light-grey-color);
    }

    .items .infoWrap {
        display: table;
        width: 100%;
    }

    .items .infoWrap .cartSection {
        display: table-cell;
        vertical-align: middle;
    }

    .items .infoWrap .cartSection .itemNumber {
        font-size: .75em;
        color: var(--grey-color);
        margin-bottom: .5em;
    }

    .items .infoWrap .cartSection h3 {
        font-size: 1em;
        font-family: 'Montserrat', sans-serif;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .025em;
    }

    .items .infoWrap .cartSection p {
        display: inline-block;
        font-size: .85em;
        color: var(--grey-color);
        font-family: 'Montserrat', sans-serif;
    }

    .items .infoWrap .cartSection p.stockStatus {
        color: #82CA9C;
        font-weight: bold;
        padding: .5em 0 0 1em;
        text-transform: uppercase;
    }

    .items .infoWrap .cartSection p.stockStatus.out {
        color: var(--button-orange-color);
    }

    .items .infoWrap .cartSection input.qty {
        width: 2em;
        text-align: center;
        font-size: 1em;
        padding: .25em;
        margin: 1em .5em 0 0;
    }

    .items .infoWrap .cartSection .itemImg {
        width: 8em;
        display: inline;
        padding-right: 1em;
    }

    .promoCode {
        border: 2px solid #efefef;
        float: left;
        width: 35%;
        margin-top: 8px;
        padding: 2%;
    }

    .promoCode label {
        display: block;
        width: 100%;
        font-style: italic;
        font-size: 1.15em;
        margin-bottom: .5em;
        letter-spacing: -.025em;
    }

    .promoCode input {
        width: 85%;
        font-size: 1em;
        padding: .5em;
        float: left;
        border: 1px solid #dadada;
    }

    .promoCode input:active,
    .promoCode input:focus {
        outline: 0;
    }

    .promoCode a.btn {
        float: left;
        width: 15%;
        padding: .7em 0;
        margin-top: 5px;
        border-radius: 0 1em 1em 0;
        text-align: center;
        border: 1px solid #82ca9c;
    }

    .promoCode a.btn:hover {
        border: 1px solid var(--button-orange-color);
        background: var(--button-orange-color);
    }

    .btn {
        text-decoration: none;
        font-family: 'Montserrat', sans-serif;
        letter-spacing: -.015em;
        font-size: 1em;
        padding: 1em 3em;
        color: var(--white-color);
        background: #82ca9c;
        font-weight: bold;
        border-radius: 50px;
        float: right;
        text-align: right;
        transition: all .25s linear;
    }

    .btn:after {
        content: "\276f";
        padding: .5em;
        position: relative;
        right: 0;
        transition: all .15s linear;
    }

    .btn:hover,
    .btn:focus,
    .btn:active {
        background: #f69679;
    }

    .btn:hover:after,
    .btn:focus:after,
    .btn:active:after {
        right: -10px;
    }

    .promoCode .btn {
        font-size: .85em;
        padding: .5em 2em;
    }

    .subtotal {
        float: right;
        width: 35%;
    }

    .subtotal .totalRow {
        padding: .5em;
        text-align: right;
    }

    .subtotal .totalRow.final {
        font-size: 1.25em;
        font-weight: bold;
    }

    .subtotal .totalRow span {
        display: inline-block;
        padding: 0 0 0 1em;
        text-align: right;
    }

    .subtotal .totalRow .label {
        font-family: 'Montserrat', sans-serif;
        font-size: .85em;
        text-transform: uppercase;
        color: var(--grey-color)
    }

    .subtotal .totalRow .value {
        letter-spacing: -.025em;
        width: 35%;
    }

    @media only screen and (max-width: 39.375em) {
        .wrap {
            width: 98%;
            padding: 2% 0;
        }

        .items .cartSection {
            width: 90%;
            display: block;
            float: left;
        }

        .items .cartSection.prodTotal,
        .items .cartSection.removeWrap {
            display: none;
        }

        .items .cartSection .itemImg {
            width: 25%;
        }

        .promoCode,
        .subtotal {
            width: 100%;
        }

        a.btn.continue {
            width: 100%;
            text-align: center;
        }
    }
</style>