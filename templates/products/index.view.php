<section>
    <h2>Products</h2>
    <?php if ($products->isEmpty()) : ?>
        <p class="text-section">No products found.</p>
    <?php else : ?>
        <?= add('products.partials.grid', ['products' => $products]) ?>
    <?php endif; ?>
</section>