<?= add('layouts.partials.head', ['title' => $title]) ?>
<?= add('layouts.partials.header', ['categories' => session()->get('categories')]) ?>
    <main class="content-container">

        <!-- This is the placeholder where the main content will be rendered-->
        {{ slot }}

        <?= add('layouts.partials.brands') ?>

    </main>
<?= add('layouts.partials.footer', ['categories' => session()->get('categories')]) ?>
<?= add('layouts.partials.flash') ?>
<?= add('layouts.partials.bottom') ?>

<?php
echo 'Memory usage now: ' . round((memory_get_usage() / 1024), 2) . "KB \n";
echo 'Peak memory usage: ' . round((memory_get_peak_usage() / 1024), 2) . " KB\n";
?>
