<section>
    <h2>Login</h2>
    <script type="module">
        import FormValidator from './scripts/validation.js';
        window.onload = () => new FormValidator('login-form');
    </script>
    <div class="flex-center">
        <form method="POST" action="<?= route('login.store') ?>" id="login-form"
              class="flex-center user-form">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <label for="email"><span>Email address</span></label>
            <input type="email"
                   id="email"
                   name="email"
                     value="<?= old('email') ?>"
                   placeholder="Your email address"
                   data-validate=true
            >
            <p class="error-message">
                <?= errors('email') ?>
            </p>

            <label for="password"><span>Password</span></label>
            <input type="password"
                   id="password"
                   name="password"
                   value="<?= old('password') ?>"
                   placeholder="Enter your password"
                   data-validate=true
            >
            <p class="error-message">
                <?= errors('password') ?>
            </p>

            <button class="button-padding" id="submit" type="submit">Login</button>

        </form>
        <p class="general-text">Don't have an account? <a class="standard-link" href="<?= route('register.index') ?>">Register</a></p>
    </div>
</section>