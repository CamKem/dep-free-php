<script type="module">
    import ModalManager from "/scripts/modalManager.js";

    new ModalManager('delete-form', 'delete');
    new ModalManager('admin-category-create', 'category-create');
</script>
<?= add('modals.confirmation', ['action' => 'delete']) ?>
<?= add('modals.admin-category-create', compact('statuses')) ?>
<section>
    <div class="admin-form-actions">
        <form name="admin-category-create">
            <button>Create category</button>
        </form>
        <form class="search-form"
              action="<?= route('admin.categories.index') ?>"
              method="get">
            <label for="search-bar" class="sr-only">Search categories</label>
            <input type="text" name="search" id="search-bar"
                   value="<?= request()->get('search') ?>"
                   placeholder="Search categories">
            <button type="submit" id="product-search-button">
                <i class="fas fa-search" aria-hidden="true"></i>
            </button>
        </form>
    </div>
    <?php if ($categories->isEmpty()): ?>
        <p class="text-section">No categories have been found.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead class="admin-table-heading">
            <tr class="admin-heading-row">
                <th>Name</th>
                <th>Slug</th>
                <th>Products</th>
                <th>Status</th>
                <th>Updated</th>
                <th style="width: 100px">Actions</th>
            </tr>
            </thead>
            <?php foreach ($categories as $category): ?>
                <tr class="admin-table-row">
                    <td><?= $category->name; ?></td>
                    <td><?= $category->slug; ?></td>
                    <td>
                        <a class="general-link"
                           href="<?= route('admin.products.index', ['category' => $category->id]) ?>"
                        >
                            <?= $category->products_count ?>
                        </a>
                    </td>
                    <td><?= $category->status ?></td>
                    <td><?= date('d M Y', strtotime($category->updated_at)) ?></td>
                    <td class="form-buttons">
                        <script type="module">
                            import ModalManager from "/scripts/modalManager.js";

                            new ModalManager('category-edit-<?= $category->id ?>', 'edit-<?= $category->id ?>');
                        </script>
                        <?= add('modals.admin-category-edit', compact('category', 'statuses')) ?>
                        <form name="category-edit-<?= $category->id ?>"
                              id="category-edit-<?= $category->id ?>"
                        >
                            <button>Edit</button>
                        </form>
                        <form method="post"
                              id="delete-form-<?= $category->id ?>"
                              name="delete-form"
                              action="<?= route('admin.categories.destroy', ['id' => $category->id]) ?>">
                            <input type="hidden" name="_method"
                                   value="DELETE">
                            <button type="submit" class="delete-button">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                </a>
            <?php endforeach; ?>
            <?php if ($categories->links() && count($categories->links()) > 1): ?>
                <tfoot>
                <tr>
                    <td colspan="6">
                        <?= add('layouts.partials.pagination', ['items' => $categories]) ?>
                    </td>
                </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    <?php endif; ?>
</section>