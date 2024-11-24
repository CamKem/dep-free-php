<section>
    <h2>Products</h2>
    <?php if ($shouldShowDidYouMean) : ?>
        <div class="standard-container mb-8">
            <p class="text-section">Did you mean <a href="<?= route('products.index', ['search' => $matched_product_words]) ?>" class="general-link"><?= implode(' ,', $matched_product_words) ?></a> instead of <strong class="general-link"><?= implode(' ,', $matched_search_words) ?></strong>?</p>
        </div>
    <?php endif; ?>
    <?php if ($products->isEmpty()) : ?>
        <div class="standard-container">
            <p class="text-section">No products found.</p>
        </div>
    <?php else : ?>
        <?= add('products.partials.grid', ['products' => $products]) ?>
    <?php endif; ?>
</section>