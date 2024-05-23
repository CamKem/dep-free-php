<section>
    <h2>Contact Us</h2>
    <script type="module">
        import FormValidator from './scripts/validation.js';

        window.onload = () => new FormValidator('contact-form');
    </script>
    <div class="flex-center row" style="gap: 20px">
        <div class="desktop-only flex-center">
            <h3 class="general-heading">Visit our store</h3>
            <p class="general-text" style="text-align: left">Our premises are
                located at:</p>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3319.205896257385!2d151.0995178757116!3d-33.70362077329031!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6b12a7843e5429b3%3A0xf017d68f9f32ca0!2sWestfield%20Hornsby!5e0!3m2!1sen!2sau!4v1694053001433!5m2!1sen!2sau"
                    width="400" height="370" style="border:0;"
                    allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Google Map Result of TipToe Soles Headquarter"
                    id="Google Map Result of Tip Toe Soles Headquarter"
                    class="map"></iframe>
        </div>
        <div class="flex-center">
            <form method="POST" action="/contact" id="contact-form"
                  aria-label="Contact form"
                  class="user-form">
                <input type="hidden" name="csrf_token"
                       value="<?= csrf_token() ?>">
                <label for="first_name"><span>First name</span></label>
                <input type="text"
                       name="first_name"
                       id="first_name"
                       placeholder="Your first name"
                       data-validate=true
                >
                <p class="error-message"></p>

                <label for="last_name"><span>Last name</span></label>
                <input type="text"
                       id="last_name"
                       name="last_name"
                       placeholder="Your last name"
                       data-validate=true
                >
                <p class="error-message"></p>

                <label for="contact"><span>Contact number</span></label>
                <input type="tel"
                       id="contact"
                       name="contact"
                       placeholder="Your contact number"
                       data-validate=true
                >
                <p class="error-message"></p>

                <label for="email"><span>Email address</span></label>
                <input type="email"
                       id="email"
                       name="email"
                       placeholder="Your email address"
                       data-validate=true
                >
                <p class="error-message"></p>

                <textarea id="message"
                          aria-label="Message"
                          name="message"
                          rows="5"
                          placeholder="Please write a message here..."
                          data-validate=false
                ></textarea>

                <div class="flex-center row" style="justify-content: space-between">
                    <button id="submit" type="submit">Send</button>
                    <div class="flex-center row standard-gap">
                        <label for="mailing_list">Join the mailing list:</label>
                        <input type="checkbox"
                               aria-label="Join mailing list"
                               id="mailing_list"
                               name="mailing_list"
                               data-validate=false
                        >
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>