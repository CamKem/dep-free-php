<section>
    <h2>Login</h2>
    <script type="module">
        import FormValidator from './scripts/validation.js';
        window.onload = () => new FormValidator('registration-form');
    </script>
    <div class="flex-center">
        <form method="POST" action="<?= route('register.store') ?>"
              id="registration-form"
              class="flex-center user-form">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <label for="username"><span>Username</span></label>
            <input type="text"
                   id="username"
                   name="username"
                   value="<?= old('username') ?>"
                   placeholder="Your username"
                   data-validate=true
            >
            <p class="error-message">
                <?= errors('username') ?>
            </p>

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
    </div>
</section>