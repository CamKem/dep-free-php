<?php if (session()->has('flash-message')): ?>
    <div class="flash-message slide-in">
        <?= session()->get('flash-message') ?>
    </div>
    <script>
        setTimeout(() => {
            let flashMessage = document.querySelector('.flash-message');
            if (flashMessage) {
                flashMessage.classList.remove('slide-in');
                flashMessage.classList.add('slide-out');
                flashMessage.addEventListener('animationend', () => {
                    flashMessage.remove();
                });
            }
        }, 3000);
    </script>
<?php endif; ?>
<?php session()->remove('flash-message'); ?>