import { createHooks } from '@wordpress/hooks';

window.wpcloud = window.wpcloud || {};
wpcloud.hooks = wpcloud.hooks || createHooks();

wpcloud.fadeOut = (element, duration = 1000) => {
	if ( element.classList.contains('no-fade-out')) {
		return;
	}
	element.style.transition = `opacity ${duration}ms`;
	element.style.opacity = 0;
	setTimeout(() => {
		element.style.display = 'none';
	}, duration);
}