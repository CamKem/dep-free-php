<section>
    <h2><?= $category->name ?></h2>
    <?php if (empty($products->toArray())): ?>
        <p class="text-center">No products found.</p>
    <?php else : ?>
        <?= add('products.partials.grid', compact('products', 'category')) ?>
    <?php endif; ?>
</section>