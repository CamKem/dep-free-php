<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <p>Hello. Welcome to the contact page.</p>
    </div>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <p>Send us a message</p>
        <form method="POST" action="/contact">
            <label>
                <input type="hidden" name="csrf_token"
                       value="<?= $csrfToken ?>">
                <input type="text" name="name" placeholder="Your name">
            </label>
            <label>
                <input type="email" name="email" placeholder="Your email">
            </label>
            <label>
                <textarea name="message" placeholder="Your message"></textarea>
            </label>
            <button type="submit">Send</button>
        </form>
    </div>
</main>
