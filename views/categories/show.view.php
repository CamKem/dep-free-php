<section>
    <h2><?= $category->name ?></h2>
    <?php if (empty($products)) : ?>
        <p>No products found.</p>
    <?php else : ?>
        <?= add('products.partials.grid', [
                'products' => $products,
                'category' => $category,
            ]) ?>
    <?php endif; ?>
</section>