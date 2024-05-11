<section class="desktop-only hero">
    <h2 class="sr-only">Hero Section</h2>
    <div class="hero-content">
        <span>View our brand-new range of</span>
        <strong>Sports balls</strong>
        <a aria-label="Shop Now" href="<?= route('categories.show', ['category' => 'balls']) ?>" class="shop-now">Shop now</a>
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
        <?php foreach ($products->toArray() as $product): ?>
            <li>
                <a href="<?= route('products.show', [
                        'category' => $product['category']['slug'],
                        'product' => $product['slug']]
                ) ?>"
                   class="product-link"
                   aria-label="<?= $product['name'] ?>"
                >
                    <article class="product-card">
                        <img src="./images/products/<?= $product['image'] ?>"
                             alt="<?= $product['name'] ?>">
                        <p class="price">
                            <?php if ($product['sale_price']): ?>
                                <strong class="price-sale">$<?= $product['sale_price'] ?></strong>
                                <span class="original-price-group">
                                    <span class="was-text">WAS</span>
                                    <del>$<?= $product['price'] ?></del>
                                </span>
                            <?php else: ?>
                                <strong>$<?= $product['price'] ?></strong>
                            <?php endif; ?>
                        </p>
                        <h3 class="short-desc"><?= substr($product['description'], 0, 30) ?>...</h3>
                    </article>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>