<script type="module">
    import FormValidator from '/scripts/validation.js';

    window.onload = () => {
        new FormValidator("subscribe-form");
    }
</script>
<section>
    <h2>Subscribe to our newsletter</h2>
    <div class="flex-center" style="gap: var(--large-gap)">
        <p class="general-text">Subscribe to our newsletter to receive the latest news and updates.</p>
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
            <button id="submit" type="submit">Subscribe</button>
        </form>
    </div>
</section>