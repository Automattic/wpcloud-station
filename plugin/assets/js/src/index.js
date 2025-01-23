import { createHooks } from '@wordpress/hooks';

window.wpcloud = window.wpcloud || {};
wpcloud.hooks = wpcloud.hooks || createHooks();

wpcloud.hooks.addAction('all', 'wpcloud', (hookName, ...args) => {
	console.log('Hook:', hookName, 'Args:', args);
});

wpcloud.scrollTo = (element) => {
	element.scrollIntoView({ behavior: "smooth", block: "center" });

	const animate = (name) => (...targets) => {
		const toAnimate = targets.map((selector) => element.querySelector(selector));

		setTimeout(() => {
			toAnimate.forEach((el) => {
				el.classList.add(name);

				el.addEventListener("animationend", () => {
					el.classList.remove(name);
				}, { once: true });
			})
		}, 500);
	}

	return { andPulse: animate("pulse"), andHighlight: animate("highlight") };
	}