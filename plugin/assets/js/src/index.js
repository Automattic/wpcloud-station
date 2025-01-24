import { createHooks } from '@wordpress/hooks';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';


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

// set up the station api fetch.
const stationApi = window.wpcloudStationApi;
apiFetch.use(apiFetch.createNonceMiddleware(stationApi.nonce));

wpcloud.stationFetch = async (route, queryParams = null, version = 'v1') => {
	const slashRoute = route.startsWith('/') ? route : '/' + route;
	let path = `/wpcloud-station/${version}${slashRoute}`;
	if (queryParams) {
		path = addQueryArgs(path, queryParams);
	}
	const response = apiFetch({ path });
	response.then((data) => {
		wpcloud.hooks.doAction(`wpcloud_station_fetch_${route}`, data);
		wpcloud.hooks.doAction('wpcloud_station_fetch', data);
	});
	return response;
}