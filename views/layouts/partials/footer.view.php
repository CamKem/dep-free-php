<footer class="footer">
    <div class="footer-top">
        <div class="content-container">
            <nav aria-label="Footer navigation">
                <h1>Site navigation</h1>
                <ul class="footer-nav">
                    <li><a aria-label="Home" href="<?= route('home') ?>">Home</a></li>
                    <li><a aria-label="About" href="<?= route('about') ?>">About</a></li>
                    <li><a aria-label="Contact" href="<?= route('contact.index') ?>">Contact</a></li>
                    <li><a aria-label="VProducts" href="<?= route('products.index') ?>">Products</a></li>
                    <?php if (auth()->check()) : ?>
                        <li><a aria-label="Dashboard" href="<?= route('dashboard.index') ?>">Dashboard</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <nav class="desktop-only" aria-label="Product categories">
                <h1>Product categories</h1>
                <ul class="footer-nav">
                    <?php
                    foreach ($categories as $category) {
                        echo "<li><a aria-label=\"{$category['name']}\" href=\"" . route('categories.show', ['category' => $category['slug']]) . "\">{$category['name']}</a></li>";
                    }
                    ?>
                </ul>
            </nav>

            <section class="contact-info">
                <h1>Contact Sports Warehouse</h1>
                <div class="contact-info-icons">
                    <a aria-label="facebook link" href="https://facebook.com">
                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        <span>Facebook</span>
                    </a>
                    <a aria-label="twitter link" href="https://twitter.com">
                        <i class="fab fa-twitter" aria-hidden="true"></i>
                        <span>Twitter</span>
                    </a>
                    <a aria-label="instagram link" href="https://instagram.com">
                        <i class="fab fa-instagram" aria-hidden="true"></i>
                        <span>Instagram</span>
                    </a>
                </div>
            </section>
        </div>
    </div>

    <div class="footer-bottom">
        <small class="copyright-text">
            <span>&copy; Copyright 2024 Sports Warehouse</span>
            <span>All rights reserved.</span>
            <span>Website made by Awesomesauce Design & Cameron Kemshal-Bell.</span>
        </small>
    </div>
</footer>