<script type="module">
    import FormValidator from './scripts/validation.js';

    window.onload = () => new FormValidator('password-reset-form');
</script>
<section>
    <h2>Reset Password</h2>
    <div class="standard-container flex-center">
        <form action="<?= route('password.reset.update', $token) ?>"
              method="post"
              id="password-reset-form"
              class="flex-center user-form"
        >
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <label for="password">New Password:</label>
            <input type="password"
                   id="password"
                   name="password"
                   placeholder="Enter your new password"
                   data-validate=true
                   value="<?= old('password') ?>"
            >
            <p class="error-message">
                <?= error('password') ?>
            </p>
            <button type="submit" class="button-padding">Reset Password</button>
        </form>
</section>