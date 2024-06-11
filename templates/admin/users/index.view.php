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
            <button>Create User</button>
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
    <?php if ($users->isEmpty()): ?>
        <p class="text-section">You have no users found.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead class="admin-table-heading">
            <tr class="admin-heading-row">
                <th>Username</th>
                <th>Email</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
            </thead>
            <?php foreach ($users as $user): ?>
                <tr class="admin-table-row">
                    <td>
                        <a class="general-link"
                           href="<?= route('admin.users.show', ['id' => $user->id]) ?>">
                            <?= $user->username; ?>
                        </a>
                    </td>
                    <td><?= $user->email; ?></td>
                    <td><?= date('d M Y', strtotime($user->created_at)) ?></td>
                    <td class="form-buttons">
                        <form action="<?= route('admin.users.show', ['id' => $user->id]) ?>"
                              name="user-edit"
                              method="get">
                            <button>Edit</button>
                        </form>
                        <form method="post"
                              id="delete-form-<?= $user->id ?>"
                              name="delete-form"
                              action="<?= route('admin.users.destroy', ['id' => $user->id]) ?>">
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
            <?php if ($users->links() && count($users->links()) > 1): ?>
                <tfoot>
                <tr>
                    <td colspan="6">
                        <div class="row-between">
                            <a class="pagination-link"
                                <?php if ($users->previousPageUrl()): ?>
                                    disabled
                                <?php endif; ?>
                               href="<?= $users->previousPageUrl() ?>"
                            >
                                Previous
                            </a>
                            <?php foreach ($users->links() as $index => $link): ?>
                                <a class="pagination-link" href="<?= $link ?>">
                                    <?= $index + 1 ?>
                                </a>
                            <?php endforeach; ?>
                            <a class="pagination-link"
                                <?php if ($users->nextPageUrl()): ?>
                                    disabled
                                <?php endif; ?>
                               href="<?= $users->nextPageUrl() ?>"
                            >
                                Next
                            </a>
                        </div>
                    </td>
                </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    <?php endif; ?>
</section>