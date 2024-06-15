<script type="module">
    import ModalManager from "/scripts/modalManager.js";

    new ModalManager('admin-product-create', 'product-create');
    new ModalManager('delete-form', 'delete');

    if (<?= session()->has('open-create-modal') ? 'true' : 'false' ?>) {
        console.log('open modal');
        window.onload = () => {
            // TODO: attempt to get this working.
            const createModalTrigger = document.getElementById('admin-product-create');
            console.log(createModalTrigger);
            createModalTrigger.click();
        };
        const createModalTrigger = document.getElementById('admin-product-create');
        console.log(createModalTrigger);
        createModalTrigger.click();
        document.querySelector('form[name="admin-product-create"]').dispatchEvent(new Event('submit'));
    }
</script>
<?= add('modals.confirmation', ['action' => 'delete']) ?>
<?= add('modals.admin-product-create', ['categories' => $categories]) ?>
<section>
    <div class="admin-form-actions">
        <form name="admin-product-create">
            <button>Create product</button>
        </form>
        <form class="search-form" action="<?= route('admin.products.index') ?>"
              method="get">
            <label for="search-bar" class="sr-only">Search products</label>
            <input type="text" name="search" id="search-bar"
                   value="<?= request()->get('search') ?>"
                   placeholder="Search products">
            <button type="submit" id="search-button">
                <i class="fas fa-search" aria-hidden="true"></i>
            </button>
        </form>
    </div>
    <?php if ($products->isEmpty()): ?>
        <p class="text-section">No products are currently found.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead class="admin-table-heading">
            <tr class="admin-heading-row">
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Featured</th>
                <th>Category</th>
                <th>Orders</th>
                <th>Updated</th>
                <th style="width: 100px">Actions</th>
            </tr>
            </thead>
            <?php foreach ($products as $product): ?>
                <tr class="admin-table-row">
                    <td>
                        <img src="/images/products/<?= $product->image ?>"
                             alt="<?= $product->name ?>"
                             class="itemImg">
                    </td>
                    <td>
                        <a class="general-link"
                           href="<?= route('products.show', [
                               'category' => $product->category->slug,
                               'product' => $product->slug
                           ]) ?>">
                            <?= ucwords($product->name) ?>
                        </a>
                    </td>
                    <td>
                        <div class="flex-center align-start"><?php
                            if ($product->sale_price) {
                                echo "<span class='original-price-group'><del>\${$product->price}</del></span>" . PHP_EOL;
                                echo "<span class='price-sale'>\${$product->sale_price}</span>" . PHP_EOL;
                            } else {
                                echo "<span class='price'>\${$product->price}</span>" . PHP_EOL;
                            }
                            ?></div>
                    </td>
                    <td><?= $product->featured ? 'Yes' : 'No' ?></td>
                    <td><?= $product->category->name ?></td>
                    <td><?= $product->orders_count ?></td>
                    <td><?= date('d M Y', strtotime($product->updated_at)) ?></td>
                    <td>
                        <div class="form-buttons">
                            <script type="module">
                                import ModalManager
                                    from "/scripts/modalManager.js";

                                new ModalManager('product-edit-<?= $product->id ?>', 'product-edit-<?= $product->id ?>');
                            </script>
                            <?= add('modals.admin-product-edit', compact('product', 'categories')) ?>
                            <form name="product-edit-<?= $product->id ?>"
                                  id="product-edit-<?= $product->id ?>"
                            >
                                <button>Edit</button>
                            </form>
                            <form method="post"
                                  id="delete-form-<?= $product->id ?>"
                                  name="delete-form"
                                  action="<?= route('admin.products.destroy', ['id' => $product->id]) ?>">
                                <input type="hidden" name="_method"
                                       value="DELETE">
                                <button type="submit" class="delete-button">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($products->links() && count($products->links()) > 1): ?>
                <tfoot>
                <tr>
                    <td colspan="8">
                        <?= add('layouts.partials.pagination', ['items' => $products]) ?>
                    </td>
                </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    <?php endif; ?>
</section>