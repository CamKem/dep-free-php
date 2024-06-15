<script type="module">
    import ModalManager from '/scripts/modalManager.js';

    new ModalManager('remove-role-form', 'remove');
    new ModalManager('admin-user-delete', 'delete');
    new ModalManager('admin-user-edit', 'user-edit');

    document.addEventListener('DOMContentLoaded', () => {
        if (<?= session()->has('open-user-edit-modal') ? 'true' : 'false'  ?> === true) {
            console.log('open modal');
            document.dispatchEvent(new CustomEvent('openModal', {
                bubbles: true,
                detail: { action: 'user-edit' }
            }));
        }
    });

</script>
<?= add('modals.confirmation', ['action' => 'delete']) ?>
<?= add('modals.confirmation', ['action' => 'remove']) ?>
<?= add('modals.admin-user-edit', [
    'user' => $user,
    'roles' => $roles,
]) ?>
<div class="standard-container">
    <div class="admin-form-actions">
        <form name="admin-user-edit" method="post">
            <button>Edit User</button>
        </form>
        <form method="post"
              id="delete-user-<?= $user->id ?>"
              name="admin-user-delete"
              action="<?= route('admin.users.destroy', ['id' => $user->id]) ?>">
            <input type="hidden" name="_method"
                   value="DELETE">
            <button class="delete-button">Delete User</button>
        </form>
    </div>
    <div class="content-form">
        <h3 class="content__heading__top">
            <i class="fa-solid fa-user"></i>
            User Details
        </h3>
        <div class="content__details">
            <p><strong>Username:</strong> <?= $user->username ?></p>
            <p><strong>Email:</strong> <?= $user->email ?></p>
            <p>
                <strong>Joined:</strong> <?= date('d M Y', strtotime($user->created_at)) ?>
            </p>
        </div>
        <?php if ($user->roles && !$user->roles->isEmpty()): ?>
            <div class="content__details">
                <h3 class="content__heading__middle">
                    <i class="fa-solid fa-universal-access"></i>
                    Users Roles
                </h3>
                <ul class="content__list">
                    <?php foreach ($user->roles as $role): ?>
                        <li><p><?= $role->name ?></p>
                            <form method="post"
                                  id="remove-role-form-<?= $user->id ?>"
                                  name="remove-role-form"
                                  action="<?= route('admin.users.update', ['id' => $user->id]) ?>">
                                <input type="hidden" name="role_id"
                                       value="<?= $role->id ?>">
                                <input type="hidden" name="remove_role"
                                       value="1">
                                <input type="hidden" name="_method" value="PUT">
                                <button type="submit" class="delete-button">
                                    Remove Role
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (!$orders->isEmpty()): ?>
            <div class="content__details">
                <h3 class="content__heading__middle">
                    <i class="fa-solid fa-cart-shopping"></i>
                    Users Orders
                </h3>
                <ul class="content__list">
                    <?php foreach ($orders as $order): ?>
                        <li>
                            <p><a class="general-link"
                                  href="<?= route('admin.orders.show', ['id' => $order->id]) ?>">
                                    Order #<?= $order->id ?>
                                    - <?= date('d M Y', strtotime($order->created_at)) ?>
                                </a></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php if ($orders->links() && count($orders->links()) > 1): ?>
                <?= add('layouts.partials.pagination', ['items' => $orders]) ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>