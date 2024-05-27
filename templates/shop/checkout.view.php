<script type="module">
    import FormValidator from '/scripts/validation.js';
    import Progress from '/scripts/progress.js';

    window.onload = () => {
        const circles = document.querySelectorAll('.progress .circle');
        const bars = document.querySelectorAll('.progress .bar');
        const sections = Array.from(document.querySelectorAll('.checkout-form-container fieldset'));
        const nextButtons = Array.from(document.querySelectorAll('.next'));
        const prevButtons = Array.from(document.querySelectorAll('.prev'));
        const validator = new FormValidator('checkout-form');
        window.progress = new Progress(circles, bars, prevButtons, nextButtons, sections, validator);
        window.progress.setUp();
        window.progress.forward();
    };
</script>
<section>
    <h2>Checkout</h2>
    <div class="checkout-container">
        <div class="progress-wrapper">
            <div class="progress">
                <div class="circle done">
                    <span class="label">1</span>
                    <span class="title">Address</span>
                </div>
                <span class="bar done"></span>
                <div class="circle active">
                    <span class="label">2</span>
                    <span class="title">Payment</span>
                </div>
                <span class="bar"></span>
                <div class="circle">
                    <span class="label">3</span>
                    <span class="title">Process</span>
                </div>
            </div>
        </div>
        <div class="order-summary">
            <h2>Order Summary</h2>
            <?php $total = 0; ?>
            <?php foreach ($cart->toArray() as $index => $item): ?>
                <div class="order-item">
                    <p><?= ucwords($item['name']) ?></p>
                    <p>Quantity: <?= $item['quantity'] ?></p>
                    <p>Price: $<?= $item['price'] ?></p>
                    <p>Total:
                        $<?php
                        $total += number_format($item['price'] * $item['quantity'], 2);
                        echo $total;
                        ?>
                    </p>
                </div>
            <?php endforeach; ?>
            <p class="order-total">Order Total:
                $<?= number_format($total, 2) ?></p>
        </div>

        <div class="checkout-form-container">
            <form action="<?= route('orders.store') ?>"
                  method="post"
                  id="checkout-form"
                  class="checkout-form"
            >
                <fieldset id="step-1">
                    <h3>Shipping Information</h3>
                    <label for="name">Full Name:
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           title="Full Name"
                           placeholder="Full Name"
                           data-validate=true
                    >
                    <p class="error-message"></p>

                    <label for="address">Address:
                        <input type="text"
                               id="address"
                               name="address"
                               title="Street Address"
                               placeholder="Street Address"
                               data-validate=true
                        >
                    </label>
                    <p class="error-message"></p>

                    <label for="city">City:
                        <input type="text"
                               id="city"
                               name="city"
                               title="City"
                               placeholder="City"
                               data-validate=true
                        >
                    </label>
                    <p class="error-message"></p>

                    <div class="form-bottom">
                        <div class="flex-center align-start">
                            <label for="state">State:
                            </label>
                            <input type="text"
                                   id="state"
                                   name="state"
                                   title="State"
                                   placeholder="State"
                                   data-validate=true
                            >
                            <p class="error-message"></p>
                        </div>
                        <div class="flex-center align-start">
                            <label for="post_code">
                                Post Code:
                            </label>
                            <input type="text"
                                   id="post_code"
                                   name="post_code"
                                   title="Post Code"
                                   placeholder="Post Code"
                                   data-validate=true
                            >
                            <p class="error-message"></p>
                        </div>
                    </div>

                    <div class="form-bottom">
                        <a href="<?= route('cart.show') ?>"
                           class="btn prev"
                        >
                            Back to cart
                        </a>
                        <button type="button" class="btn next">Next</button>
                    </div>
                </fieldset>
                <fieldset id="step-2" class="hidden">
                    <h3>Payment Information</h3>
                    <label for="cardName">Name on Card</label>
                    <input type="text" id="cardName"
                           name="cardName"
                           title="Cardholder Name"
                           placeholder="Cardholder Name"
                           data-validate=true>
                    <p class="error-message"></p>

                    <label for="cardNumber">Card Number</label>
                    <div class="card-number" id="cardNumber">
                        <input
                                type="text"
                                class="card-segment"
                                id="card-number-1"
                                maxlength="4"
                                inputmode="numeric"
                                pattern="\d{4}"
                                placeholder="####"
                                data-validate=true
                        >
                        <input
                                type="text"
                                class="card-segment"
                                id="card-number-2"
                                maxlength="4"
                                inputmode="numeric"
                                pattern="\d{4}"
                                placeholder="####"
                                data-validate=true
                        >
                        <input
                                type="text"
                                class="card-segment"
                                id="card-number-3"
                                maxlength="4"
                                inputmode="numeric"
                                pattern="\d{4}"
                                placeholder="####"
                                data-validate=true
                        >
                        <input type="text"
                               class="card-segment"
                               id="card-number-4"
                               maxlength="4"
                               inputmode="numeric"
                               pattern="\d{4}"
                               placeholder="####"
                               data-validate=true
                        >
                    </div>
                    <p class="error-message"></p>

                    <div class="form-bottom">
                        <div class="flex-center align-start">
                            <label for="expDate">
                                Expiry Date:
                            </label>
                            <input type="text"
                                   id="expDate"
                                   title="Expiry"
                                   name="expDate"
                                   inputmode="numeric"
                                   pattern="(?:0[1-9]|1[0-2])\/[0-9]{2}"
                                   placeholder="MM/YY"
                                   data-validate=true
                            >
                            <p class="error-message"></p>
                        </div>
                        <div class="flex-center align-start">
                            <label for="cvv">CVV:
                            </label>
                            <input type="number"
                                   id="cvv"
                                   title="CVV"
                                   name="cvv"
                                   inputmode="numeric"
                                   pattern="[0-9]{3}"
                                   placeholder="CVV"
                                   data-validate=true
                            >
                            <p class="error-message"></p>
                        </div>
                    </div>

                    <div class="form-bottom">
                        <button type="button" class="btn prev">Previous</button>
                        <button type="button" class="btn next">Next</button>
                    </div>
                </fieldset>
                <fieldset id="step-3" class="hidden">
                    <h3>Order Confirmation</h3>
                    <p>Shipping Info:</p>
                    <p id="shipping-info"></p>
                    <p>Payment Info:</p>
                    <p id="payment-info"></p>
                    <div class="form-bottom">
                        <button type="button" class="btn prev">Previous</button>
                        <button type="submit" class="btn">Place Order</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</section>