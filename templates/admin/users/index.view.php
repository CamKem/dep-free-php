<section>
    <div class="admin-form-actions">
        <a href="<?= route('admin.users.create') ?>"><button>Create User</button></a>
        <form class="search-form" action="<?= route('admin.users.index') ?>"
              method="get">
            <label for="search-bar" class="sr-only">Search users</label>
            <input type="text" name="search" id="search-bar"
                <?php
                if (request()->getUri() === route('products.index')) {
                    echo 'value="' . request()->get('search') . '"';
                }
                ?>
                   placeholder="Search products">
            <button type="submit" id="search-button">
                <i class="fas fa-search" aria-hidden="true"></i>
            </button>
        </form>
    </div>
    <?= dd($users) ?>
    <?php if ($users->isEmpty()): ?>
        <p class="text-section">You have no users yet.</p>
    <?php else: ?>
        <div class="content-form">
            <p class="content-form-heading">List of our users</p>
            <table style="width: 100%;">
                <thead class="content-form-heading" style="grid-template-columns: 1fr 2fr 2fr 1fr 1fr 1fr;">
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <?php foreach ($users as $user): ?>
                <tr>
                        <td><?= $user->username; ?></td>
                        <td><?= $user->email; ?></td>
                    <td><?= $user->created_at; ?></td>
                    <td class="form-buttons">
                        <button>
                            <a href="<?= route('admin.users.edit', ['id' => $user->id]) ?>">
                                Edit
                            </a>
                        </button>
                        <form method="post"
                              id="delete-form"
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
                <?php endforeach; ?>
                <tfoot>
                <tr>
                    <td colspan="6">
                        pagination
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</section>