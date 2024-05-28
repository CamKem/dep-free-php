<script type="module">
    import FormValidator from '/scripts/validation.js';
    import {CardHandler, Progress} from "/scripts/checkout.js";

    window.onload = () => {
        const circles = document.querySelectorAll('.progress .circle');
        const bars = document.querySelectorAll('.progress .bar');
        const sections = Array.from(document.querySelectorAll('.checkout-form-container fieldset'));
        const nextButtons = Array.from(document.querySelectorAll('.next'));
        const prevButtons = Array.from(document.querySelectorAll('.prev'));
        const validator = new FormValidator('checkout-form', false);
        new Progress(circles, bars, prevButtons, nextButtons, sections, validator)
        const cardNumberInputs = Array.from(document.querySelectorAll('.card-segment'));
        const expiryDateInput = document.getElementById('expiry_date');
        new CardHandler(cardNumberInputs, expiryDateInput);

        function generateDetails(inputs) {
            return inputs.reduce((acc, input) => {
                if (!input.id.includes('card-number-')) {
                    acc += `<p class="confirmation-detail"><span class="title">${input.title}:</span><span title="value">${input.value}</span></p>`;
                }
                return acc;
            }, '');
        }

        window.addEventListener('summary', () => {
            const shippingInfo = document.getElementById('shipping-info');
            const paymentInfo = document.getElementById('payment-info');

            const shippingInputs = Array.from(document.querySelectorAll('#step-1 input'));
            const paymentInputs = Array.from(document.querySelectorAll('#step-2 input'));

            shippingInfo.innerHTML = generateDetails(shippingInputs);
            paymentInfo.innerHTML = generateDetails(paymentInputs);
        });
    };
</script>
<section>
    <h2>Checkout</h2>
    <?php if ($cart->count() === 0): ?>
        <div class="flex-center">
            <p>No items are in your cart</p>
            <a href="<?= route('products.index') ?>" class="standard-link">
                Back to shop
            </a>
        </div>
    <?php else: ?>
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
            <div class="order-summary card">
                <h3 class="general-heading">Order Summary</h3>
                <?php $total = 0; ?>
                <?php foreach ($cart->toArray() as $index => $item): ?>
                    <div class="order-item">
                        <a href="<?= route('products.show', [
                            'category' => $item['category']['slug'],
                            'product' => $item['slug'],
                        ]) ?>"
                           class="general-link"
                        >
                            <?= ucwords($item['name']) ?></a>
                        <div style="display: flex; justify-content: space-between;">
                            <p>Quantity: <?= $item['quantity'] ?></p>
                            <p>Price: $<?= $item['price'] ?></p>
                            <p>Total:
                                $<?php
                                $total += $item['price'] * $item['quantity'];
                                echo number_format($total, 2);
                                ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <p class="order-detail">Subtotal:
                    $<?= number_format($total, 2) ?></p>
                <p class="order-detail">
                    Shipping: $<?= number_format($shipping, 2) ?>
                </p>
                <p class="order-detail">
                    Tax: $<?= number_format($total * $taxRate, 2) ?>
                </p>
                <p class="order-total">Order Total:
                    <?php $orderTotal = $total + $shipping + ($total * $taxRate); ?>
                    $<?= number_format($orderTotal, 2) ?></p>
            </div>

            <div class="checkout-form-container card">
                <form action="<?= route('orders.store') ?>"
                      method="post"
                      id="checkout-form"
                      class="checkout-form"
                >
                    <fieldset id="step-1">
                        <h3 class="general-heading">Shipping Information</h3>
                        <label for="name">Full Name:
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               title="Full Name"
                               autocomplete="name"
                               placeholder="Full Name"
                               data-validate=true
                        >
                        <p class="error-message"></p>

                        <label for="address">Address:
                            <input type="text"
                                   id="address"
                                   name="address"
                                   autocomplete="address-line1"
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
                                <label for="state">State:</label>
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
                                <label for="post_code">Post Code:</label>
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
                            >Back to cart</a>
                            <button type="button" class="btn next">Next</button>
                        </div>
                    </fieldset>
                    <fieldset id="step-2" class="hidden">
                        <h3 class="general-heading">Payment Information</h3>
                        <label for="card_name">Name on Card:</label>
                        <input type="text"
                               id="card_name"
                               name="card_name"
                               title="Cardholder"
                               placeholder="Cardholder Name"
                               data-validate=true>
                        <p class="error-message"></p>

                        <label for="card_number">Card Number:</label>
                        <input type="hidden"
                               name="card_number"
                               title="Card Number"
                               id="card_number"
                        >
                        <div class="card-number" id="card_number">
                            <input
                                    type="text"
                                    class="card-segment"
                                    id="card-number-1"
                                    title="Card Number"
                                    maxlength="4"
                                    inputmode="numeric"
                                    pattern="\d{4}"
                                    autocomplete="cc-number"
                                    placeholder="####"
                                    data-validate=true
                            >
                            <input
                                    type="text"
                                    class="card-segment"
                                    id="card-number-2"
                                    title="Card Number"
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
                                    title="Card Number"
                                    maxlength="4"
                                    inputmode="numeric"
                                    pattern="\d{4}"
                                    placeholder="####"
                                    data-validate=true
                            >
                            <input type="text"
                                   class="card-segment"
                                   id="card-number-4"
                                   title="Card Number"
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
                                <label for="expiry_date">
                                    Expiry Date:
                                </label>
                                <input type="text"
                                       id="expiry_date"
                                       title="Expiry"
                                       name="expiry_date"
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
                                <input type="text"
                                       id="cvv"
                                       title="CVV"
                                       name="cvv"
                                       inputmode="numeric"
                                       maxlength="3"
                                       pattern="[0-9]{3}"
                                       placeholder="CVV"
                                       data-validate=true
                                >
                                <p class="error-message"></p>
                            </div>
                        </div>

                        <label for="contact_number">
                            Contact Number:
                            <input type="tel"
                                   id="contact_number"
                                   name="contact_number"
                                   title="Contact Number"
                                   inputmode="numeric"
                                   pattern="[0-9]{10}"
                                   placeholder="Contact Number"
                                   data-validate=true
                            >
                        </label>
                        <p class="error-message"></p>

                        <div class="form-bottom">
                            <button type="button" class="btn prev">Previous
                            </button>
                            <button type="button" class="btn next">Next</button>
                        </div>
                    </fieldset>
                    <fieldset id="step-3" class="hidden">
                        <h3 class="general-heading">Order Confirmation</h3>
                        <h4 class="confirmation-title">Shipping Info:</h4>
                        <div id="shipping-info"></div>
                        <h4 class="confirmation-title">Payment Info:</h4>
                        <div id="payment-info"></div>
                        <div class="form-bottom">
                            <button type="button" class="btn prev">Previous
                            </button>
                            <button type="submit" class="btn">Place Order
                            </button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    <?php endif; ?>
</section>