<nav class="hidden mobile-only mobile-nav">
    <ul>
        <?php if (auth()->check()): ?>
            <a aria-label="Dashboard"
               href="<?= route('dashboard.index') ?>">
                <li>Welcome, <?= auth()->user()->username ?></li>
            </a>
            <a aria-label="Logout" href="<?= route('logout') ?>">
                <li class="mobile-nav-login">Logout</li>
            </a>
        <?php else: ?>
            <a aria-label="Login"
               href="<?= route('login.index') ?>">
                <li class="mobile-nav-login">Login</li>
            </a>
        <?php endif; ?>
        <a aria-label="Home" href="<?= route('home') ?>">
            <li class="mobile-nav-item">Home</li>
        </a>
        <a aria-label="About SW" href="<?= route('about') ?>">
            <li class="mobile-nav-item">About SW</li>
        </a>
        <a aria-label="Contact Us"
           href="<?= route('contact.index') ?>">
            <li class="mobile-nav-item">Contact Us</li>
        </a>
        <a aria-label="View Products"
           href="<?= route('products.index') ?>">
            <li class="mobile-nav-item">View Products</li>
        </a>
    </ul>
</nav>