<section>
    <h2>Products</h2>
    <?php if ($shouldShowDidYouMean) : ?>
        <div class="standard-container mb-8">
            <p class="text-section">Did you mean
                <?php if (count($matched_product_words) === 1) : ?>
                    <a href="<?= route('products.index', ['search' => $matched_product_words]) ?>" class="general-link"><?= $matched_product_words[0] ?></a> instead of <strong class="general-link"><?= $matched_search_words[0] ?></strong>?
                <?php else : ?>
                        <?php foreach ($matched_product_words as $index => $matched_product_word) : ?>
                            <a href="<?= route('products.index', ['search' => $matched_product_word]) ?>" class="general-link"><?= $matched_product_word ?></a>
                            <?php if ($index < count($matched_product_words) - 1) : ?>
                                or,
                            <?php else : ?>
                                instead of <strong class="general-link"><?= implode(', ', $matched_search_words) ?></strong>?
                            <?php endif; ?>
                        <?php endforeach; ?>

                <?php endif; ?>
            </p>
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