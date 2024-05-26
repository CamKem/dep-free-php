<script type="module">
    import FormValidator from './scripts/validation.js';

    // window.onload = () =>
    window.validator = new FormValidator('checkout-form');
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
                    <span class="title">Finish</span>
                </div>
            </div>
        </div>
        <div class="order-summary">
            <h2>Order Summary</h2>
            <!-- Loop through the cart items and display them -->
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
            <!-- Display the total order price -->
            <p class="order-total">Order Total:
                $<?= number_format($total, 2) ?></p>
        </div>
        <div class="checkout-form-container">
            <form action="<?= route('orders.store') ?>"
                  method="post"
                  id="checkout-form"
                  class="checkout-form"
            >
                <fieldset id="step-1" class="active">
                    <h3>Shipping Information</h3>
                    <label for="name">Full Name:
                        <input type="text"
                               id="name"
                               name="name"
                               placeholder="Full Name"
                               data-validate=true
                        >
                        <p class="error-message"></p>
                    </label>

                    <label for="address">Address:
                        <input type="text"
                               id="address"
                               name="address"
                               placeholder="Address"
                               data-validate=true
                        >
                        <p class="error-message"></p>
                    </label>

                    <label for="city">City:
                        <input type="text"
                               id="city"
                               name="city"
                               placeholder="City"
                               data-validate=true
                        >
                        <p class="error-message"></p>
                    </label>
                    <div class="form-bottom">
                        <label for="state">State:
                            <input type="text" id="state" name="state"
                                   placeholder="State"
                                   data-validate=true
                            >
                            <p class="error-message"></p>
                        </label>
                        <label for="post_zip_code">
                            Post / ZIP Code
                            <input type="text" id="post_zip_code"
                                   name="post_zip_code"
                                   placeholder="Post / ZIP Code"
                                   data-validate=true
                            >
                            <p class="error-message"></p>
                        </label>
                    </div>
                    <div class="form-bottom">
                        <!--                    <button type="button" class="btn prev">Back to cart</button>-->
                        <button type="button" class="btn next">Next</button>
                    </div>
                </fieldset>
                <fieldset id="step-2" class="hidden">
                    <h3>Payment Information</h3>
                    <label for="cardName">Name on Card</label>
                    <input type="text" id="cardName" name="cardName"
                           placeholder="Cardholder Name"
                           data-validate=true>
                    <p class="error-message"></p>

                    <label for="cardNumber">Card Number</label>
                    <input type="text" id="cardNumber" name="cardNumber"
                           placeholder="Card Number"
                           data-validate=true>
                    <p class="error-message"></p>

                    <div class="form-bottom">
                        <label for="expDate">
                            Expiration Date:
                            <input type="text" id="expDate" name="expDate"
                                   placeholder="Expiry Date"
                                   data-validate=true>
                            <p class="error-message"></p>
                        </label>
                        <label for="cvv">CVV:
                            <input type="text" id="cvv" name="cvv"
                                   placeholder="CVV"
                                   data-validate=true>
                            <p class="error-message"></p>
                        </label>
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

<script>
    // Get all sections
    const sections = Array.from(document.querySelectorAll('.checkout-form-container fieldset'));

    console.log(sections);

    // Add event listeners to all Next buttons
    const nextButtons = Array.from(document.querySelectorAll('.next'));
    console.log(nextButtons);
    nextButtons.forEach((button, index) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            // run the validator on the current section
            // Validate the form fields in the current section
            const inputs = sections[index].querySelectorAll('input');
            let isValid = window.validator.validateFor(inputs);

            // If the form fields are valid, hide the current section and show the next one
            if (isValid) {
                sections[index].style.display = 'none';
                sections[index + 1].style.display = 'block';
            }
        });
    });

    // Add event listeners to all Previous buttons
    const prevButtons = Array.from(document.querySelectorAll('.prev'));
    prevButtons.forEach((button, index) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            sections[index + 1].style.display = 'none';
            sections[index].style.display = 'block';
        });
    });
</script>

<script>
    let i = 1;
    const circles = document.querySelectorAll('.progress .circle');
    const bars = document.querySelectorAll('.progress .bar');

    circles.forEach(function (circle) {
        circle.className = 'circle';
    });

    bars.forEach(function (bar) {
        bar.className = 'bar';
    });

    setInterval(function () {
        if (circles[i - 1] !== undefined) {
            circles[i - 1].classList.add('active');
        }
        if (circles[i - 2] !== undefined) {
            circles[i - 2].classList.remove('active');
            circles[i - 2].classList.add('done');
            circles[i - 2].querySelector('.label').innerHTML = '&#10003;';
        }
        if (bars[i - 1] !== undefined) {
            bars[i - 1].classList.add('active');
        }
        if (bars[i - 2] !== undefined) {
            bars[i - 2].classList.remove('active');
            bars[i - 2].classList.add('done');
        }
        i++;
        if (i === 0) {
            bars.forEach(function (bar) {
                bar.className = 'bar';
            });
            circles.forEach(function (circle) {
                circle.className = 'circle';
            });
            i = 1;
        }
    }, 1000);
</script>