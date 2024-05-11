<section>
    <h2>Contact Us</h2>
    <script type="module">
        import FormValidator from './scripts/validation.js';
        window.onload = () => new FormValidator('contact-form');
    </script>
    <div class="flex-center">
        <form method="POST" action="/contact" id="contact-form"
              class="flex-center">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
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

            <div class="mail-list">
                <label for="mailing_list"><span>Join mailing list</span></label>
                <input type="checkbox"
                       aria-label="Join mailing list"
                       id="mailing_list"
                       name="mailing_list"
                       data-validate=false
                >
            </div>

            <button id="submit" type="submit">Send</button>

        </form>
    </div>
</section>

<style>
    .flex-center {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    #contact-form {
        align-items: flex-start;
        padding: 2rem;
        background-color: #f4f4f4; /* Light grey background */
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        width: 100%;
        max-width: 400px;
    }

    input[type="text"],
    input[type="tel"],
    input[type="email"],
    textarea {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button {
        background-color: #f26722; /* Orange button to match the header */
        color: white;
        padding: 10px 50px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #e55d1b; /* Slightly darker on hover */
    }

    .error-message {
        color: #cc0000; /* Red color for errors */
        font-size: 0.8rem;
        margin-bottom: 10px;
        height: 1rem; /* Keep consistent height even if no error */
    }

    /* Styling for the checkbox */
    .mail-list {
        display: flex;
        place-self: end;
        gap: 10px;
        margin-bottom: 10px;
    }

    input[type="checkbox"] {
        accent-color: #f26722; /* Orange accent for checkbox */
    }
</style>