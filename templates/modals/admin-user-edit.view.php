<script type="module">
    import Modal from '/scripts/modal.js';
    import FormValidator from "/scripts/validation.js";

    document.addEventListener('openModal', (event) => {
        console.log(event.detail);
        if (event.detail.action === 'edit') {
            // if the event target is this specific modal
            let modal = new Modal('user-edit', event.detail.form);
            modal.openModal();
        }
    });
    window.onload = () => new FormValidator('user-edit-form');
</script>

<div id="user-edit-modal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2 class="general-heading">Edit User</h2>
        <div class="modal-form">
            <form id="user-edit-form"
                  action="<?= route('admin.users.update', ['id' => $user->id]) ?>"
                  method="post"
            >
                <input type="hidden" name="_method" value="PUT">
                <label for="username">Username</label>
                <input type="text"
                       name="username"
                       title="Username"
                       id="username"
                       value="<?= old('username', $user->username) ?? '' ?>"
                       data-validate="true"
                       autocomplete="new-username"
                >
                <p class="error-message" id="username-error">
                    <?= error('username') ?>
                </p>
                <label for="email">Email</label>
                <input type="email"
                       title="Email"
                       name="email"
                       id="email"
                       value="<?= old('email',$user->email) ?? '' ?>"
                       data-validate="true"
                       autocomplete="new-email"
                >
                <p class="error-message" id="email-error"></p>
                <label for="password">Password</label>
                <input type="password"
                       title="Password"
                       name="password"
                       value="<?= old('password') ?>"
                       id="password"
                       autocomplete="new-password"
                >
                <p class="error-message" id="password-error">
                    <?= error('password') ?>
                </p>
                <label for="roles">Roles</label>
                <select name="roles[]" id="roles" multiple
                        title="Roles"
                        data-validate="true"
                        autocomplete="new-roles"
                        style="height: <?= 48 * $roles->count() ?>px"
                >
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role->id ?>"
                            <?php if ($user->roles->contains($role->id)): ?>
                                selected
                            <?php endif; ?>
                        >
                            <?= $role->name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="error-message" id="roles-error">
                    <?= error('roles') ?>
                </p>
                <p class="modal-text">
                    Are you sure you want to edit this user?
                </p>
                <button type="submit" class="confirm-button">
                    Edit User
                </button>
            </form>
        </div>
    </div>
</div>