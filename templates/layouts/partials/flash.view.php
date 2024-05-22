<script type="module">
    import Flash from "/scripts/flash.js";

    new Flash('<?= session()->get('flash-message') ?>');
</script>