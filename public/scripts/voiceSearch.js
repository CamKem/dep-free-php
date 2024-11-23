export default class VoiceSearch {

    constructor(searchInput, searchButton) {
        this.searchInput = searchInput;
        this.isRecognizing = false;
        this.searchButton = searchButton;
        this.recognition = null;

        this.init();
    }

    init() {
        if (window.SpeechRecognition || window.webkitSpeechRecognition) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();
            this.recognition.continuous = false;
            this.recognition.interimResults = false;
            this.recognition.lang = 'en-US';

            this.setupListeners();

            this.recognition.onstart = () => {
                this.isRecognizing = true;
                this.triggerFlashMessage('Listening...');
            };

            this.recognition.onspeechend = () => {
                this.recognition.stop();
                this.isRecognizing = false;
            };

            this.recognition.onresult = (event) => {
                this.searchInput.value = event.results[0][0].transcript;
                this.searchInput.form.submit();
            };

            this.recognition.onerror = (event) => {
                this.triggerFlashMessage(`Something went wrong with speech recognition`);
                this.triggerFlashMessage(`Error: ${event.error}`);
                this.isRecognizing = false;
            };
        } else {
            this.triggerFlashMessage('Speech recognition is not supported in this browser.');
        }
    }

    setupListeners() {
        if (this.searchButton) {
            this.searchButton.addEventListener('click', () => this.start());
        }
    }

    start() {
        if (this.recognition && !this.isRecognizing) {
            this.isRecognizing = true;
            this.recognition.start();
        }
    }

    triggerFlashMessage(message) {
        const event = new CustomEvent('flashToggle', {
            detail: { message }
        });
        document.dispatchEvent(event);
    }
}