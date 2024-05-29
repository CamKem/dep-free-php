<script type="module">
    import FormValidator from '/scripts/validation.js';
    window.onload = () => new FormValidator('password-reset-form');
</script>
<section>
    <h2>Reset Password</h2>
    <div class="standard-container flex-center">
        <form action="<?= route('password.reset.store') ?>"
              method="post"
              id="password-reset-form"
              class="flex-center user-form"
        >
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <label for="email">Email:</label>
            <input type="email"
                   id="email"
                   name="email"
                   title="Email"
                   value="<?= old('email') ?>"
                   placeholder="Enter your email address"
                   data-validate=true
            >
            <p class="error-message">
                <?= error('email') ?>
            </p>
            <button type="submit" class="button-padding">Send Password Reset
            </button>
        </form>
    </div>
</section>