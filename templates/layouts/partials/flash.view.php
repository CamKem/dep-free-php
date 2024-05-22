<script type="module">
    import FlashManager from "/scripts/flash.js";

    new FlashManager('<?= session()->get('flash-message') ?>');
</script>