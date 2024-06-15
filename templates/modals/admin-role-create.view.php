<script type="module">
    import Modal from '/scripts/modal.js';
    import FormValidator from "/scripts/validation.js";

    document.addEventListener('openModal', (event) => {
        if (event.detail.action === 'role-create') {
            let modal = new Modal('role-create', event.detail.form);
            modal.openModal();
        }
    });

    new FormValidator('create-role-form');
</script>

<div id="role-create-modal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2 class="general-heading">Create Role</h2>
        <div class="modal-form">
            <form id="create-role-form"
                  action="<?= route('admin.roles.store') ?>"
                  method="post">
                <label for="name">Name</label>
                <input type="text"
                       name="name"
                       title="name"
                       id="name"
                       placeholder="Enter a name"
                       data-validate="true"
                       autocomplete="create-role"
                >
                <p class="error-message" id="name-error"></p>

                <label for="description">Description</label>
                <textarea name="description"
                          title="Description"
                          id="description"
                          placeholder="Enter a description"
                          data-validate="true"
                          rows="5"
                          autocomplete="create-description"
                ></textarea>
                <p class="error-message" id="description-error"></p>

                <p class="modal-text">
                    Are you sure you want to create this role?
                </p>
                <button type="submit" class="confirm-button">
                    Create Role
                </button>
            </form>
        </div>
    </div>
</div>