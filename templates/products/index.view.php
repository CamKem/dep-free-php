<section>
    <h2>Products</h2>
    <?php if (empty($products->toArray())) : ?>
        <p class="text-center">No products found.</p>
    <?php else : ?>
        <?= add('products.partials.grid', ['products' => $products]) ?>
    <?php endif; ?>
</section>