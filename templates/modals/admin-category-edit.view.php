<script type="module">
    import Modal from '/scripts/modal.js';
    import FormValidator from "/scripts/validation.js";

    document.addEventListener('openModal', (event) => {
        if (event.detail.action === 'edit-<?= $category->id ?>') {
            let modal = new Modal('category-edit-<?= $category->id ?>', event.detail.form);
            modal.openModal();
        }
    });


    window.onload = () => new FormValidator('edit-category-<?= $category->id ?>-form');
</script>

<div id="category-edit-<?= $category->id ?>-modal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2 class="general-heading">Create a New Category</h2>
        <div class="modal-form">
            <form id="edit-category-<?= $category->id ?>-form"
                  action="<?= route('admin.categories.update', ['id' => $category->id]) ?>"
                  method="post">
                <input type="hidden" name="_method" value="put">
                <label for="name">Name</label>
                <input type="text"
                       name="name"
                       title="name"
                       id="name"
                       placeholder="Enter a name"
                       data-validate="true"
                       value="<?= $category->name ?>"
                       autocomplete="edit-category"
                >
                <p class="error-message" id="name-error"></p>
                <label for="status">Status:</label>
                <select name="status"
                        id="order-status"
                        title="status"
                        data-validate="true"
                >
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= $status ?>"
                            <?= $category->status === $status ? 'selected' : '' ?>
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