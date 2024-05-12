<section>
    <h2>Products</h2>
    <?php if (empty($products->toArray())) : ?>
        <p>No products found.</p>
    <?php else : ?>
        <?= add('products.partials.grid', ['products' => $products]) ?>
    <?php endif; ?>
</section>

<style>
    p {
        text-align: center;
    }
</style>