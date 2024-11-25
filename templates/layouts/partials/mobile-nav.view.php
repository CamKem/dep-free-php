<nav class="hidden mobile-only mobile-nav">
    <ul>
        <?php if (auth()->check()): ?>
        <li>
            <a aria-label="Dashboard"
               href="<?= route('dashboard.index') ?>">
                <span>Welcome, <?= auth()->user()->username ?></span>
            </a>
        </li>
        <li class="mobile-nav-item">
            <a aria-label="Logout" href="<?= route('logout') ?>">
                <span>Logout</span>
            </a>
        </li>
        <?php else: ?>
        <li class="mobile-nav-item">
            <a aria-label="Login"
               href="<?= route('login.index') ?>">
                <span>Login</span>
            </a>
        </li>
        <?php endif; ?>
        <li class="mobile-nav-item">
            <a aria-label="Home" href="<?= route('home') ?>">Home</a></li>
        <li class="mobile-nav-item">
            <a aria-label="View Products"
               href="<?= route('products.index') ?>">
                <span>Products</span>
            </a>
        </li>
        <li class="mobile-nav-item">
            <a aria-label="About SW" href="<?= route('about') ?>">
                <span>About</span>
            </a>
        </li>
        <li class="mobile-nav-item">
            <a aria-label="Contact Us"
               href="<?= route('contact.index') ?>">
                <span>Contact</span>
            </a>
        </li>
        <li class="mobile-nav-item">
            <a aria-label="Subscribe to the mailing list"
               href="<?= route('subscribe.index') ?>">
                <span>Subscribe</span>
            </a>
        </li>
    </ul>
</nav>