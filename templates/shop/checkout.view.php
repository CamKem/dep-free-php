<section>
    <h2>Checkout</h2>
    <div class="progress">
        <div class="circle done">
            <span class="label">1</span>
            <span class="title">Confirm</span>
        </div>
        <span class="bar done"></span>
        <div class="circle done">
            <span class="label">2</span>
            <span class="title">Address</span>
        </div>
        <span class="bar half"></span>
        <div class="circle active">
            <span class="label">3</span>
            <span class="title">Payment</span>
        </div>
        <span class="bar"></span>
        <div class="circle">
            <span class="label">5</span>
            <span class="title">Finish</span>
        </div>
    </div>
    <div class="checkout-progress">
        <ul class="circles">
            <li><div class="circle"></div></li>
            <li class="active"><div class="circle"></div></li>
            <li><div class="circle"></div></li>
            <li><div class="circle"></div></li>
        </ul>
        <ul class="labels">
            <li><span>Cart</span></li>
            <li><span>Shipping</span></li>
            <li><span>Payment</span></li>
            <li><span>Confirmation</span></li>
        </ul>
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
        <p class="order-total">Order Total: $<?= number_format($total, 2) ?></p>
    </div>
    <div class="checkout-form-container">
        <form action="<?= route('orders.store') ?>" method="post"
              class="checkout-form">
            <h3>Shipping Information</h3>
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>

            <label for="address">Address</label>
            <input type="text" id="address" name="address" required>

            <label for="city">City</label>
            <input type="text" id="city" name="city" required>

            <label for="state">State</label>
            <input type="text" id="state" name="state" required>

            <label for="zip">Post / ZIP Code</label>
            <input type="text" id="post_zip_code" name="post_zip_code" required>

            <h3>Payment Information</h3>
            <label for="cardName">Name on Card</label>
            <input type="text" id="cardName" name="cardName" required>

            <label for="cardNumber">Card Number</label>
            <input type="text" id="cardNumber" name="cardNumber" required>

            <label for="expDate">Expiration Date</label>
            <input type="text" id="expDate" name="expDate" required>

            <label for="cvv">CVV</label>
            <input type="text" id="cvv" name="cvv" required>

            <button type="submit" class="btn">Place Order</button>
        </form>
    </div>
</section>

<script>
    let i = 1;
    const circles = document.querySelectorAll('.progress .circle');
    const bars = document.querySelectorAll('.progress .bar');

    circles.forEach(function(circle) {
        circle.className = 'circle';
    });

    bars.forEach(function(bar) {
        bar.className = 'bar';
    });

    setInterval(function() {
        if (circles[i-1] !== undefined) {
            circles[i-1].classList.add('active');
        }
        if (circles[i-2] !== undefined) {
            circles[i-2].classList.remove('active');
            circles[i-2].classList.add('done');
            circles[i-2].querySelector('.label').innerHTML = '&#10003;';
        }
        if (bars[i-1] !== undefined) {
            bars[i-1].classList.add('active');
        }
        if (bars[i-2] !== undefined) {
            bars[i-2].classList.remove('active');
            bars[i-2].classList.add('done');
        }
        i++;
        if (i === 0) {
            bars.forEach(function(bar) {
                bar.className = 'bar';
            });
            circles.forEach(function(circle) {
                circle.className = 'circle';
            });
            i = 1;
        }
    }, 1000);
</script>