<script type="module">
    import Modal from '/scripts/modal.js';
    import FormValidator from "/scripts/validation.js";

    document.addEventListener('openModal', (event) => {
        if (event.detail.action === 'category-create') {
            let modal = new Modal('category-create', event.detail.form);
            modal.openModal();
        }
    });
    window.onload = () => new FormValidator('create-category-form');
</script>

<div id="category-create-modal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2 class="general-heading">Create a New Category</h2>
        <div class="modal-form">
            <form id="create-category-form"
                  action="<?= route('admin.categories.store') ?>"
                  method="post">
                <label for="name">Name</label>
                <input type="text"
                       name="name"
                       title="name"
                       id="name"
                       value="<?= old('name') ?>"
                       placeholder="Enter a name"
                       data-validate="true"
                       autocomplete="new-category"
                >
                <p class="error-message">
                    <?= error('name') ?>
                </p>
                <label for="status">Status:</label>
                <select name="status"
                        id="category-status"
                        title="status"
                        data-validate="true"
                >
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= $status ?>"
                            <?= old('status') === $status ? 'selected' : '' ?>
                        ><?= ucwords($status) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="modal-text">
                    Are you sure you want to create this category?
                </p>
                <button type="submit" class="confirm-button">
                    Create Category
                </button>
            </form>
        </div>
    </div>
</div>