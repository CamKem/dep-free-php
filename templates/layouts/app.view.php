<?= add('layouts.partials.head', ['title' => $title]) ?>
    <div class="site-wrapper">
        <?= add('layouts.partials.header', ['categories' => session()->get('categories')]) ?>
        <main class="content-container">

            {{ slot }}

            <?= add('layouts.partials.brands') ?>

        </main>
        <?= add('layouts.partials.footer', ['categories' => session()->get('categories')]) ?>
    </div>
<?= add('layouts.partials.flash') ?>
<?= add('layouts.partials.bottom') ?>