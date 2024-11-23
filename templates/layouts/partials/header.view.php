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
            <?= add('layouts.partials.mobile-nav') ?>

            <?= add('layouts.partials.desktop-nav') ?>

            <div class="top-bar-right header-text">
                <?php if (auth()->check()): ?>
                    <a href="<?= route('logout') ?>" aria-label="Logout"
                       class="desktop-only login-link">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <span>Logout</span>
                    </a>
                <?php else: ?>
                    <a href="<?= route('login.index') ?>" aria-label="Login"
                       class="desktop-only login-link">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <span>Login</span>
                    </a>
                <?php endif; ?>
                <a class="cart-view" aria-label="View Cart Link"
                   href="<?= route('cart.show') ?>">
                    <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                    <span>View Cart</span>
                </a>
                <a class="cart-item-count" aria-label="Items in cart"
                   href="<?= route('cart.show') ?>">
                    <span><?= count(session()->get('cart', [])) ?> items</span>
                </a>
            </div>
        </div>
    </div>

    <div class="content-container search-logo-section">
        <section class="logo-container">
            <h1 class="sr-only">Sports Warehouse</h1>
            <a href="/" aria-label="Sports Warehouse Home" class="logo">
                <img src="/images/sports-warehouse-logo.svg"
                     alt="Sports Warehouse Logo">
            </a>
        </section>

        <form class="search-form" action="<?= route('products.index') ?>"
              method="get">
            <label for="search-bar" class="sr-only">Search products</label>
            <input type="text" name="search" id="search-bar"
                <?php
                if (request()->getUri() === route('products.index')) {
                    echo 'value="' . request()->get('search') . '"';
                }
                ?>
                   placeholder="Search products">
            <div class="search-button-group">
                <script type="module">
                    import VoiceSearch from '/scripts/voiceSearch.js';

                    const conditions = [
                        'brave' in navigator,
                        window.location.href.includes('localhost'),
                        window.location.protocol !== 'https:',
                        (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)),
                    ];

                    if (conditions.some(condition => condition === true)) {
                        document.getElementById('voice-search-button').style.display = 'none';
                    } else {
                        new VoiceSearch(
                            document.getElementById('search-bar'),
                            document.getElementById('voice-search-button')
                        );
                    }
                </script>
                <button type="button"
                        aria-label="Voice Search"
                        title="Voice Search"
                        id="voice-search-button"
                >
                    <i class="fas fa-microphone search-icon"
                       aria-hidden="true"
                    ></i>
                    <span class="tooltip">Click to search by voice</span>
                </button>
                <button type="submit" id="search-button">
                    <i class="fas fa-search search-icon" aria-hidden="true"></i>
                </button>
            </div>
        </form>
    </div>

    <nav class="category-nav content-container" aria-label="Product categories">
        <ul>
            <?php
            // get the active categories to make it styled on active state
            $active = request()->get('category');
            foreach ($categories as $category) {
                echo "<a aria-label=\"{$category->name}\"
                         href=\"" . route('categories.show', ['category' => $category->slug]) . "\"
                         class=\"" . ($active === $category->slug ? 'category-active' : '') . "\"
                      >
                          <li class=\"" . ($active === $category->slug ? 'category-active' : '') . "\">
                              {$category->name}
                          </li>
                      </a>";
            }
            ?>
        </ul>
    </nav>
</header>