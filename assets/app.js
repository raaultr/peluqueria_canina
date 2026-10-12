import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('click', (event) => {
	const toggle = event.target.closest('[data-password-toggle]');
	if (!toggle) {
		return;
	}

	const input = document.getElementById(toggle.getAttribute('aria-controls'));
	if (!(input instanceof HTMLInputElement)) {
		return;
	}

	const isVisible = input.type === 'password';
	input.type = isVisible ? 'text' : 'password';
	toggle.setAttribute('aria-pressed', String(isVisible));
	toggle.setAttribute('aria-label', isVisible ? 'Ocultar contraseña' : 'Mostrar contraseña');
});
