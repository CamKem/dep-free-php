<script type="module">
    import Modal from '/scripts/modal.js';
    document.addEventListener('openModal', (event) => {
        if (event.detail.action === '<?= $action ?>') {
            let modal = new Modal('<?= $action ?>', event.detail.form);
            modal.openModal();
        }
    });
</script>

<div id="<?= $action ?>-modal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2 class="general-heading">Confirm <?= ucfirst($action) ?></h2>
        <p class="modal-text">Are you sure you want to <?= $action ?> this item?</p>
        <button class="confirm-button" id="confirm-<?= $action ?>">Yes, <?= ucfirst($action) ?>!</button>
    </div>
</div>