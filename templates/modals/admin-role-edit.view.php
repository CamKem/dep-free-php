<script type="module">
    import Modal from '/scripts/modal.js';
    import FormValidator from "/scripts/validation.js";

    document.addEventListener('openModal', (event) => {
        if (event.detail.action === 'edit-<?= $role->id ?>') {
            let modal = new Modal('role-edit-<?= $role->id ?>', event.detail.form);
            modal.openModal();
        }
    });

    new FormValidator('edit-role-<?= $role->id ?>-form');
</script>

<div id="role-edit-<?= $role->id ?>-modal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2 class="general-heading">Edit Role</h2>
        <div class="modal-form">
            <form id="edit-role-<?= $role->id ?>-form"
                  action="<?= route('admin.roles.update', ['id' => $role->id]) ?>"
                  method="post">
                <input type="hidden" name="_method" value="put">
                <label for="name">Name</label>
                <input type="text"
                       name="name"
                       title="name"
                       id="name"
                       placeholder="Enter a name"
                       data-validate="true"
                       value="<?= old('name', $role->name) ?>"
                       autocomplete="edit-role"
                >
                <p class="error-message" id="name-error">
                    <?= error('name') ?>
                </p>

                <label for="description">Description</label>
                <textarea name="description"
                          title="Description"
                          id="description"
                          placeholder="Enter a description"
                          data-validate="true"
                          rows="5"
                          autocomplete="edit-description"
                ><?= old('description', $role->description) ?></textarea>
                <p class="error-message" id="description-error">
                    <?= error('description') ?>
                </p>

                <p class="modal-text">
                    Are you sure you want to edit this role?
                </p>
                <button type="submit" class="confirm-button">
                    Edit Role
                </button>
            </form>
        </div>
    </div>
</div>