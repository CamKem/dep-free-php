<?= add('layouts.admin.head', ['title' => $title]) ?>
<?= add('layouts.admin.header') ?>

<div class="admin-breadcrumbs">
    <div class="breadcrumbs">
        <a class="breadcrumb__item" href="<?= route('admin.index') ?>">Admin</a>
        <span>></span>
        <a class="breadcrumb__item" href="<?= route(request()->route()->getName()) ?>"><?= $title ?></a>
    </div>
</div>

<div class="admin-content">
<!--    <h2>--><?php //= $title ?><!--</h2>-->
    <div class="standard-container">
        {{ slot }}
    </div>
</div>
<?= add('layouts.partials.bottom-footer') ?>
<?= add('layouts.partials.flash') ?>
<?= add('layouts.partials.bottom') ?>

