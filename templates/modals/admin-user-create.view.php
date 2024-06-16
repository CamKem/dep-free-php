<script type="module">
    import Modal from '/scripts/modal.js';
    import FormValidator from "/scripts/validation.js";

    document.addEventListener('openModal', (event) => {
        if (event.detail.action === 'user-create') {
            let modal = new Modal('user-create', event.detail.form);
            modal.openModal();
        }
    });
    window.onload = () => new FormValidator('create-user-form');
</script>

<div id="user-create-modal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2 class="general-heading">Create User</h2>
        <div class="modal-form">
            <form id="create-user-form"
                  action="<?= route('admin.users.store') ?>"
                  method="post">
                <label for="username">Username</label>
                <input type="text"
                       name="username"
                       title="Username"
                       id="username"
                       value="<?= old('username') ?>"
                       placeholder="Enter a username"
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
                       value="<?= old('email') ?>"
                       placeholder="Enter an email address"
                       data-validate="true"
                       autocomplete="new-email"
                >
                <p class="error-message" id="email-error">
                    <?= error('email') ?>
                </p>
                <label for="password">Password</label>
                <input type="password"
                       title="Password"
                       name="password"
                       id="password"
                       placeholder="Enter a password"
                       data-validate="true"
                       autocomplete="new-password"
                >
                <p class="error-message" id="password-error">
                    <?= error('password') ?>
                </p>
                <p class="modal-text">
                    Are you sure you want to create this user?
                </p>
                <button type="submit" class="confirm-button">
                    Create User
                </button>
            </form>
        </div>
    </div>
</div>