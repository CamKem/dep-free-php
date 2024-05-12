<header class="header">
    <div class="top-bar">
        <div class="content-container">
            <div class="mobile-only menu-icon-container">
                <a href="#"
                   aria-label="Open menu"
                   class="menu-icon"
                >
                    <i class="fas fa-bars" aria-hidden="true"></i>
                </a>
                <span class="menu-text">Menu</span>
            </div>
            <nav class="hidden mobile-only mobile-nav">
                <ul>
                    <li><a aria-label="Login" href="<?= route('login.index') ?>">Login</a></li>
                    <li><a aria-label="Home" href="<?= route('home') ?>">Home</a></li>
                    <li><a aria-label="About SW" href="<?= route('about') ?>">About SW</a></li>
                    <li><a aria-label="Contact Us" href="<?= route('contact.index') ?>">Contact Us</a></li>
                    <li><a aria-label="View Products" href="<?= route('products.index') ?>">View Products</a></li>
                </ul>
            </nav>

            <nav class="desktop-only desktop-nav">
                <ul>
                    <li><a aria-label="Home" href="<?= route('home') ?>">Home</a></li>
                    <li><a aria-label="About SW" href="<?= route('about') ?>">About SW</a></li>
                    <li><a aria-label="Contact Us" href="<?= route('contact.index') ?>">Contact Us</a></li>
                    <li><a aria-label="View Products" href="<?= route('products.index') ?>">View Products</a></li>
                </ul>
            </nav>

            <div class="top-bar-right">
                <a href="<?= route('login.index') ?>" aria-label="Login" class="desktop-only login-link">
                    <i class="fas fa-lock" aria-hidden="true"></i>
                    <span>Login</span>
                </a>
                <a class="cart-view" aria-label="View Cart Link" href="#">
                    <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                    <span>View Cart</span>
                </a>
                <a class="cart-item-count" aria-label="Items in cart" href="#">
                    <span>0 items</span>
                </a>
            </div>
        </div>
    </div>

    <div class="content-container search-logo-section">
        <section class="logo-container">
            <h1 class="sr-only">Sports Warehouse</h1>
            <a href="/" aria-label="Sports Warehouse Home" class="logo">
                <img src="/images/sports-warehouse-logo.svg" alt="Sports Warehouse Logo">
            </a>
        </section>

        <form class="search-form" action="<?= route('products.index')?>" method="get">
            <label for="search-bar" class="sr-only">Search products</label>
            <input type="text" name="search" id="search-bar" placeholder="Search products">
            <button type="submit" id="search-button">
                <i class="fas fa-search" aria-hidden="true"></i>
            </button>
        </form>
    </div>

    <nav class="category-nav content-container" aria-label="Product categories">
        <ul>
            <?php
            foreach ($categories as $category) {
                echo "<li><a aria-label=\"{$category['name']}\" href=\"" . route('categories.show', ['category' => $category['slug']]) . "\">{$category['name']}</a></li>";
            }
            ?>
        </ul>
    </nav>
</header>