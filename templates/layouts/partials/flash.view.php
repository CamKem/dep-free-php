<script type="module">
    import FlashManager from "/scripts/flash.js";
    new FlashManager(<?= json_encode((array) session()->get('flash-message', [])) ?>);
</script>