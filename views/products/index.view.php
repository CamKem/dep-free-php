<section>
    <h2>Products</h2>
    <?php if (empty($products->toArray())) : ?>
        <p style="text-align: center">No products found.</p>
    <?php else : ?>
        <?= add('products.partials.grid', ['products' => $products]) ?>
    <?php endif; ?>
</section>