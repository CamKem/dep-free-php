<?= add('layouts/partials/head', ['title' => $title]) ?>
<?= add('layouts/partials/header', ['categories' => $categories]) ?>
    <main class="content-container">

        <?= $content ?>

        <?= add('layouts/partials/brands') ?>

    </main>
<?= add('layouts/partials/footer', ['categories' => $categories]) ?>
<?= add('layouts/partials/bottom') ?>