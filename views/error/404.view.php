<?= add('layouts/partials/head') ?>
<?= add('layouts/partials/header') ?>
<?= add('layouts/partials/nav') ?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold">Sorry. Page Not Found.</h1>

        <p class="mt-4">
            <a href="<?= route('home') ?>" class="text-blue-500 underline">Go back home.</a>
        </p>
    </div>
</main>

<?= add('layouts/partials/brands') ?>
<?= add('layouts/partials/footer') ?>
<?= add('layouts/partials/bottom') ?>
