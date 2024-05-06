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
                    <li><a aria-label="Login" href="#">Login</a></li>
                    <li><a aria-label="Home" href="#">Home</a></li>
                    <li><a aria-label="About SW" href="#">About SW</a></li>
                    <li><a aria-label="Contact Us" href="#">Contact Us</a></li>
                    <li><a aria-label="View Products" href="#">View Products</a></li>
                </ul>
            </nav>

            <nav class="desktop-only desktop-nav">
                <ul>
                    <li><a aria-label="Home" href="#">Home</a></li>
                    <li><a aria-label="About SW" href="#">About SW</a></li>
                    <li><a aria-label="Contact Us" href="#">Contact Us</a></li>
                    <li><a aria-label="View Products" href="#">View Products</a></li>
                </ul>
            </nav>

            <div class="top-bar-right">
                <a href="#" aria-label="Login" class="desktop-only login-link">
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
                <img src="./assets/images/sports-warehouse-logo.svg" alt="Sports Warehouse Logo">
            </a>
        </section>

        <form class="search-form" action="/">
            <label for="search-bar" class="sr-only">Search products</label>
            <input type="text" id="search-bar" placeholder="Search products">
            <button type="submit" name="website search button" id="search-button">
                <i class="fas fa-search" aria-hidden="true"></i>
            </button>
        </form>
    </div>

    <nav class="category-nav content-container" aria-label="Product categories">
        <ul>
            <li><a aria-label="Shoes" href="#">Shoes</a></li>
            <li><a aria-label="Helmets" href="#">Helmets</a></li>
            <li><a aria-label="Pants" href="#">Pants</a></li>
            <li><a aria-label="Balls" href="#">Balls</a></li>
            <li><a aria-label="Tops" href="#">Tops</a></li>
            <li><a aria-label="Equipment" href="#">Equipment</a></li>
            <li><a aria-label="Training Gear" href="#">Training Gear</a></li>
        </ul>
    </nav>
</header>