<nav class="desktop-only desktop-nav header-text">
    <ul>
        <li>
            <a aria-label="Home" href="<?= route('home') ?>">Home</a>
        </li>
        <li>
            <a aria-label="Products" href="<?= route('products.index') ?>">Products</a>
        </li>
        <li>
            <a aria-label="About" href="<?= route('about') ?>">About</a>
        </li>
        <li>
            <a aria-label="Contact"
               href="<?= route('contact.index') ?>">Contact</a>
        </li>
        <li>
            <a aria-label="Subscribe"
               href="<?= route('subscribe.index') ?>">Subscribe</a>
        </li>
        <?php if (auth()->check()): ?>
            <li style="position: relative;">
                <a aria-label="Dashboard"
                   href="<?= route('dashboard.index') ?>">Dashboard</a>
                <?php if (auth()->user()->isAdmin()): ?>
                    <span class="admin-badge">Admin</span>
                <?php endif; ?>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<style>
    .admin-badge {
        position: absolute;
        top: 0;
        bottom 0;
        right: -45px;
        font-size: 8px;
        background-color: var(--mid-blue-color);
        color: var(--white-color);
        padding: 0.25rem 0.5rem;
        border-radius: 5px;
    }
</style>