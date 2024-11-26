<script type="module">
    import FormValidator from '/scripts/validation.js';

    window.onload = () => {
        new FormValidator("subscribe-form");
    }
</script>
<section>
    <h2>Subscribe to our newsletter</h2>
    <div class="subscribe">
        <?php
        $action = session()->get('action');
        ?>
        <?php if ($action !== null) : ?>
            <p class="alert <?= $action ?>">
                <span class="close-alert" aria-label="Close alert" onclick="this.parentElement.style.display = 'none';">&times;</span>
                <?= $action === 'success' ? 'You have been subscribed to our mailing list.' : 'Failed to subscribe to the newsletter, please try again.' ?>
            </p>
        <?php endif; ?>
        <p class="text-section">Subscribe to our newsletter to receive the latest news and updates.</p>
        <div class="standard-container">
        <form method="POST"
              action="<?= route('subscribe.store') ?>"
              id="subscribe-form"
              aria-label="Subscribe form"
              class="user-form"
        >
            <input type="hidden" name="csrf_token"
                   value="<?= csrf_token() ?>">
            <label for="email"><span>Email address</span></label>
            <input type="email"
                   id="email"
                   name="email"
                   value="<?= old('email', auth()->user()->email ?? '') ?>"
                   title="Email address"
                   placeholder="Your email address"
                   data-validate="true"
            >
            <p class="error-message" id="email-error">
                <?= error('email') ?>
            </p>
            <div class="form-bottom">
                <button id="submit" type="submit">Subscribe</button>
            </div>
        </form>
        </div>
    </div>
</section>