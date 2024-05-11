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
    <?= add('products.partials.grid', ['products' => $products]) ?>
</section>