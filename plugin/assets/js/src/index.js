/**
 * WordPress dependencies
 */
import { createHooks } from '@wordpress/hooks';

/**
 * Local dependencies
 */

import form from '@wpcloud/utils/form';
import api, { configuredApiFetch as apiFetch } from '@wpcloud/utils/api';

window.wpcloud = window.wpcloud || {};
wpcloud.hooks = wpcloud.hooks || createHooks();

wpcloud.form = form;
wpcloud.stationApi = api;
wpcloud.apiFetch = apiFetch;

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

// set up the station api fetch.
// @deprecated TODO remove
const stationApi = window.wpcloudStationApi;
apiFetch.use(apiFetch.createNonceMiddleware(stationApi.nonce));

wpcloud.stationFetch = () => {
	console.warn('wpcloud.stationFetch is deprecated. Use wpcloud.stationApi.get or wpcloud.stationApi.post instead.');
}

wpcloud.renderTemplate = (template, data = {}) => {
	const site = wpcloud.site ?? {};
		const templateData = {
		...data,
		...site
	}
	const render = new Function('templateData', `return \`${template}\`;`);

	try {
		return render(templateData);
	} catch (error) {
		console.error(error);
		return template;
	}
}