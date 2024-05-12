<section>
    <h2><?= $category->name ?></h2>
    <?php if (empty($products->toArray())) : ?>
        <p>No products found.</p>
    <?php else : ?>
        <?= add('products.partials.grid', [
                'products' => $products,
                'category' => $category,
            ]) ?>
    <?php endif; ?>
</section>

<style>
    p {
        text-align: center;
    }
</style>