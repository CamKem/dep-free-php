<section>
    <h2>User Dashboard</h2>
    <div class="standard-container">
        <!-- TODO: Add link to admin if the user has the role of admin -->
        <p>Welcome, <?= $user->username; ?>!</p>
        <p>Here are some of your recent orders:</p>
        <ul>
            <?php foreach ($user->orders as $order): ?>
                <li>
                    <a href="<?= route('orders.show', ['order' => $order->id]) ?>">
                        Order #<?= $order->id; ?> - <?= $order->created_at->format('Y-m-d'); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
