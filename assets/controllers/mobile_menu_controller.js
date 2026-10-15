import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['menu', 'toggle', 'backdrop', 'closeButton'];

    connect() {
        this.setOpen(false);
    }

    disconnect() {
        document.body.classList.remove('overflow-hidden');
    }

    toggle() {
        const isOpen = this.toggleTarget.getAttribute('aria-expanded') !== 'true';
        this.setOpen(isOpen);
    }

    close() {
        this.setOpen(false);
    }

    closeOnEscape() {
        if (this.toggleTarget.getAttribute('aria-expanded') !== 'true') {
            return;
        }

        this.setOpen(false);
        this.toggleTarget.focus();
    }

    closeOnNavigation(event) {
        if (event.target.closest('a')) {
            this.setOpen(false);
        }
    }

    setOpen(isOpen) {
        this.menuTarget.classList.toggle('-translate-x-full', !isOpen);
        this.menuTarget.classList.toggle('invisible', !isOpen);
        this.backdropTarget.classList.toggle('hidden', !isOpen);
        this.toggleTarget.classList.toggle('hidden', isOpen);
        this.closeButtonTarget.classList.toggle('hidden', !isOpen);
        this.toggleTarget.setAttribute('aria-expanded', String(isOpen));
        document.body.classList.toggle('overflow-hidden', isOpen);
    }
}