export function toggleMobileMenu() {
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
}

export class MenuToggle {
    constructor(toggle, menu) {
        this.menu = document.getElementById(menu);
        this.toggle = document.getElementById(toggle);
        this.icon = this.toggle.querySelector('i');
        this.menuOpen = false;
        this.init();
    }

    init() {
        this.toggle.addEventListener('click', (event) => {
            event.preventDefault();
            this.toggleMenu();
        });
        this.menu.addEventListener('click', this.closeOnOutsideClick.bind(this));
    }

    toggleMenu() {
        this.menuOpen ? this.closeMenu() : this.openMenu();
    }

    openMenu() {
        this.menuOpen = true;
        this.icon.classList.remove('fa-bars');
        this.icon.classList.add('fa-times');
        this.menu.classList.remove('hidden');
        this.menu.classList.add('fixed');
    }

    closeMenu() {
        this.menuOpen = false;
        this.icon.classList.remove('fa-times');
        this.icon.classList.add('fa-bars');
        this.menu.classList.remove('fixed');
        this.menu.classList.add('hidden');
    }

    closeOnOutsideClick(event) {
        console.log(event.target);
        if (this.menuOpen && event.target === this.menu) {
            this.toggleMenu();
        }
    }
}