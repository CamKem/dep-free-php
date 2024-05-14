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