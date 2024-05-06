<?= add('layouts/partials/head') ?>
<?= add('layouts/partials/header') ?>


    <main class="content-container">

        <?= $content ?>

        <section class="desktop-only hero">
            <h2 class="sr-only">Hero Section</h2>
            <div class="hero-content">
                <span>View our brand-new range of</span>
                <strong>Sports balls</strong>
                <a href="#" class="shop-now">Shop now</a>
            </div>
            <div class="slider-indicator">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </section>

        <section>
            <h2>Featured products</h2>
            <ul class="product-grid">
                <li>
                    <a href="#"
                       class="product-link"
                       aria-label="Adidas Euro 16 Top Soccer Ball"
                    >
                        <article class="product-card">
                            <img src="./assets/images/productImages/adidasEuro16TopSoccerBall.jpg"
                                 alt="Adidas Euro 16 Top Soccer Ball">
                            <p class="price">
                                <strong class="price-sale">$34.95</strong>
                                <span class="original-price-group">
                                    <span class="was-text">WAS</span>
                                    <del>$46.00</del>
                                </span>
                            </p>
                            <h3 class="short-desc">Adidas Euro16 Top Soccer Ball</h3>
                        </article>
                    </a>
                </li>
                <li>
                    <a href="#"
                       class="product-link"
                       aria-label="Protec Classic Skate Helmet"
                    >
                        <article class="product-card">
                            <img src="./assets/images/productImages/ProtecClassicSkateHelmet.png"
                                 alt="Protec Classic Skate Helmet">
                            <p class="price">
                                <strong>$70.00</strong>
                            </p>
                            <h3 class="short-desc">Protec Classic Skate Helmet</h3>
                        </article>
                    </a>
                </li>
                <li>
                    <a href="#"
                       class="product-link"
                       aria-label="Nike Sport 600ml Water Bottle"
                    >
                        <article class="product-card">
                            <img src="./assets/images/productImages/NikeSport600mlWaterBottle.png"
                                 alt="Nike Sport 600ml Water Bottle">
                            <p class="price">
                                <strong class="price-sale">$15.00</strong>
                                <span class="original-price-group">
                                    <span class="was-text">WAS</span><del>$17.50</del>
                                </span>
                            </p>
                            <h3 class="short-desc">Nike Sport 600ml Water Bottle</h3>
                        </article>
                    </a>
                </li>
                <li>
                    <a href="#"
                       class="product-link"
                       aria-label="Sting Arma Plus Boxing Gloves"
                    >
                        <article class="product-card">
                            <img src="./assets/images/productImages/StingArmaPlusBoxingGloves.png"
                                 alt="Sting Arma Plus Boxing Gloves">
                            <p class="price">
                                <strong>$79.95</strong>
                            </p>
                            <h3 class="short-desc">Sting Arma Plus Boxing Gloves</h3>
                        </article>
                    </a>
                </li>
                <li>
                    <a href="#"
                       class="product-link"
                       aria-label="Asics Gel Lethal Tigreor 8 IT"
                    >
                        <article class="product-card">
                            <img src="./assets/images/productImages/AsicsGelLethalTigreor8ITMens.jpg"
                                 alt="Asics Gel Lethal Tigreor 8 IT Men's">
                            <p class="price">
                                <strong class="price-sale">$15.00</strong>
                                <span class="original-price-group">
                                    <span class="was-text">WAS</span><del>$17.50</del>
                                </span>
                            </p>
                            <h3 class="short-desc">Asics Gel Lethal Tigreor 8 IT Men's</h3>
                        </article>
                    </a>
                </li>
            </ul>
        </section>

        <?= add('layouts/partials/brands') ?>

    </main>
<?= add('layouts/partials/footer') ?>
<?= add('layouts/partials/bottom') ?>