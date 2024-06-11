<script type="module">
    import ModalManager from "/scripts/modalManager.js";

    new ModalManager('admin-users-create', 'user-create');
    new ModalManager('delete-form', 'delete');
</script>
<?= add('modals.confirmation', ['action' => 'delete']) ?>
<?= add('modals.admin-user-create') ?>
<section>
    <div class="admin-form-actions">
        <form name="admin-users-create">
            <button></button>
        </form>
        <form class="search-form" action="<?= route('admin.users.index') ?>"
              method="get">
            <label for="search-bar" class="sr-only">Search users</label>
            <input type="text" name="search" id="search-bar"
                   value="<?= request()->get('search') ?>"
                   placeholder="Search products">
            <button type="submit" id="search-button">
                <i class="fas fa-search" aria-hidden="true"></i>
            </button>
        </form>
    </div>
    <?php if ($orders->isEmpty()): ?>
        <p class="text-section">No orders are currently found available.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead class="admin-table-heading">
            <tr class="admin-heading-row">
                <th>Order ID</th>
                <th>User</th>
                <th>Products</th>
                <th>Total</th>
                <th>Date</th>
                <th style="width: 100px">Actions</th>
            </tr>
            </thead>
            <?php foreach ($orders as $order): ?>
                <tr class="admin-table-row">
                    <td>
                        <a class="general-link"
                           href="<?= route('admin.orders.show', ['id' => $order->id]) ?>">
                            <?= $order->id; ?>
                        </a>
                    </td>
                    <td><?= $order->user->username ?></td>
                    <td><?= $order->products->count() ?></td>
                    <td>$<?= $order->total ?></td>
                    <td><?= date('d M Y', strtotime($order->created_at)) ?></td>
                    <td class="form-buttons">
                        <form action="<?= route('admin.orders.show', ['id' => $order->id]) ?>"
                              name="user-edit"
                              method="get">
                            <button>Edit</button>
                        </form>
                        <form method="post"
                              id="delete-form-<?= $order->id ?>"
                              name="delete-form"
                              action="<?= route('admin.orders.destroy', ['id' => $order->id]) ?>">
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
            <?php if ($orders->links() && count($orders->links()) > 1): ?>
                <tfoot>
                <tr>
                    <td colspan="6">
                        <?= add('layouts.partials.pagination', ['items' => $orders]) ?>
                    </td>
                </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    <?php endif; ?>
</section>