<nav class="desktop-only desktop-nav header-text">
    <ul>
        <li>
            <a aria-label="Home" href="<?= route('home') ?>">Home</a>
        </li>
        <li>
            <a aria-label="About" href="<?= route('about') ?>">About</a>
        </li>
        <li>
            <a aria-label="Contact"
               href="<?= route('contact.index') ?>">Contact</a>
        </li>
        <li>
            <a aria-label="Products" href="<?= route('products.index') ?>">Products</a>
        </li>
        <?php if (auth()->check()): ?>
            <li>
                <a aria-label="Dashboard" href="<?= route('dashboard.index') ?>">Dashboard</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>