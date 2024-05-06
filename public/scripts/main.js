(function toggleMobileMenu() {
    const mobileNav = document.querySelector('.mobile-nav');
    const toggle = document.querySelector('.menu-icon');
    const menuIcon = document.querySelector('.menu-icon > i');
    const menuText = document.querySelector('.menu-text');

    toggle.addEventListener('click', function (event) {
        event.preventDefault();

        switch (mobileNav.classList.contains('hidden')) {
            case true:
                menuIcon.classList.remove('fa-bars');
                menuIcon.classList.add('fa-times');
                menuText.classList.add('hidden');
                mobileNav.classList.remove('hidden');
                break;
            case false:
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
                menuText.classList.remove('hidden');
                mobileNav.classList.add('hidden');
                break;
        }
    });
})();