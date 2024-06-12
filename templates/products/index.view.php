<section>
    <h2>Products</h2>
    <?php if ($products->isEmpty()) : ?>
        <div class="standard-container">
            <p class="text-section">No products found.</p>
        </div>
    <?php else : ?>
        <?= add('products.partials.grid', ['products' => $products]) ?>
    <?php endif; ?>
</section>