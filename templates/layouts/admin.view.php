<?= add('layouts.admin.head', ['title' => $title]) ?>
<?= add('layouts.admin.header') ?>

<div class="admin-breadcrumbs">
    <div class="breadcrumbs">
        <a class="breadcrumb__item" href="<?= route('admin.index') ?>">
            Admin
        </a>
        <span>></span>
        <?php if (isset($crumbs)): ?>
            <?php foreach ($crumbs as $name => $route): ?>
                <a class="breadcrumb__item"
                   href="<?= $route ?>"><?= $name ?></a>
                <?php if ($name !== array_key_last($crumbs)): ?>
                    <span>></span>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <a class="breadcrumb__item" href="<?= route(request()->route()->getName()) ?>">
                <?= $title ?>
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="admin-content">
    <div class="standard-container">
        {{ slot }}
    </div>
</div>
<?= add('layouts.partials.bottom-footer') ?>
<?= add('layouts.partials.flash') ?>
<?= add('layouts.partials.bottom') ?>

