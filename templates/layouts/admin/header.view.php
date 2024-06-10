<header class="admin-header">
    <div class="logo">
        <a href="/" aria-label="Sports Warehouse Home" class="logo">
            <img src="/images/sports-warehouse-logo.svg"
                 alt="Sports Warehouse Logo">
        </a>
    </div>
    <script type="module">
        import { MenuToggle } from '/scripts/menuToggle.js';
        window.onload = () => {
            new MenuToggle('menu-icon', 'menu-nav');
        }
    </script>
    <div class="menu-icon-container">
        <a id="menu-icon">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </a>
    </div>
    <?= add('layouts.admin.menu-nav') ?>
</header>