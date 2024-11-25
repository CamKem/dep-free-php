<script type="module">
    import ModalManager from "/scripts/modalManager.js";

    new ModalManager('admin-role-create', 'role-create');
</script>
<?= add('modals.confirmation', ['action' => 'delete']) ?>
<?= add('modals.admin-role-create') ?>
<section>
    <div class="admin-form-actions">
        <form name="admin-role-create">
            <button>Create role</button>
        </form>
        <form class="search-form"
              action="<?= route('admin.roles.index') ?>"
              method="get">
            <label for="search-bar" class="sr-only">Search roles</label>
            <input type="text" name="search" id="search-bar"
                   value="<?= request()->get('search') ?>"
                   placeholder="Search roles">
            <button type="submit" id="product-search-button">
                <i class="fas fa-search" aria-hidden="true"></i>
            </button>
        </form>
    </div>
    <?php if ($roles->isEmpty()): ?>
        <p class="text-section">No roles have been found.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead class="admin-table-heading">
            <tr class="admin-heading-row">
                <th>Name</th>
                <th>Description</th>
                <th>Users</th>
                <th>Created</th>
                <th>Updated</th>
                <th style="width: 100px">Actions</th>
            </tr>
            </thead>
            <?php foreach ($roles as $role): ?>
                <tr class="admin-table-row">
                    <td><?= $role->name; ?></td>
                    <td><?= $role->description; ?></td>
                    <td>
                        <a class="general-link"
                           href="<?= route('admin.users.index', ['role' => $role->id]) ?>"
                        >
                        <?= $role->users_count ?>
                        </a>
                    </td>
                    <td><?= date('d M Y', strtotime($role->created_at)) ?></td>
                    <td><?= date('d M Y', strtotime($role->updated_at)) ?></td>
                    <td class="form-buttons">
                        <script type="module">
                            import ModalManager from "/scripts/modalManager.js";

                            new ModalManager('role-edit-<?= $role->id ?>', 'edit-<?= $role->id ?>');
                        </script>
                        <?= add('modals.admin-role-edit', compact('role')) ?>
                        <form name="role-edit-<?= $role->id ?>"
                              id="role-edit-<?= $role->id ?>"
                        >
                            <button>Edit</button>
                        </form>
                        <form method="post"
                              id="delete-form-<?= $role->id ?>"
                              name="delete-form"
                              action="<?= route('admin.roles.destroy', ['id' => $role->id]) ?>">
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
            <?php if ($roles->links() && count($roles->links()) > 1): ?>
                <tfoot>
                <tr>
                    <td colspan="6">
                        <?= add('layouts.partials.pagination', ['items' => $roles]) ?>
                    </td>
                </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    <?php endif; ?>
</section>