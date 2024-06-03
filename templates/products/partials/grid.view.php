<ul class="product-grid">
    <?php foreach ($products->toArray() as $product): ?>
        <li>
<!--            --><?php //= dd($product) ?>
            <a href="<?= route('products.show', [
                    'category' => $product['category'][0]['slug'] ?? $category->slug,
                    'product' => $product['slug']]) ?>"
               class="product-link"
               aria-label="<?= $product['name'] ?>"
            >
                <article class="product-card">
                    <img src="/images/products/<?= $product['image'] ?>"
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
                    <h3 class="short-desc"><?= $product['name'] ?></h3>
                </article>
            </a>
        </li>
    <?php endforeach; ?>
</ul>