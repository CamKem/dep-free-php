<script type="module">
    import Modal from '/scripts/modal.js';
    let modal = new Modal('<?= $action ?>');
    document.addEventListener('openModal', (event) => {
        if (event.detail.action === 'open') {
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