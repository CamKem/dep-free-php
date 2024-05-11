<?= add('layouts/partials/head', ['title' => $title]) ?>
<?= add('layouts/partials/header', ['categories' => session()->get('categories')]) ?>
    <main class="content-container">

        <?= $content ?>

        <?= add('layouts/partials/brands') ?>

    </main>
<?= add('layouts/partials/footer', ['categories' => session()->get('categories')]) ?>
<?= add('layouts/partials/bottom') ?>