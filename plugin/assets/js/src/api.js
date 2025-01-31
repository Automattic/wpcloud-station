import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

const stationApi = window.wpcloudStationApi;
apiFetch.use(apiFetch.createNonceMiddleware(stationApi.nonce));


async function handler( nextOptions ) {
	const { url, path, data, parse = true, ...remainingOptions } = nextOptions;
	let { body, headers } = nextOptions;

	headers = { Accept: 'application/json, */*;q=0.1', ...headers };

	// The `data` property is a shorthand for sending a JSON body.
	if ( data ) {
		body = JSON.stringify( data );
		headers[ 'Content-Type' ] = 'application/json';
	}

	const response = await window.fetch(url || path,
		{
			credentials: 'include',
			...remainingOptions,
			body,
			headers,
		}
	);

	const json = await response.json();
	if (!response.ok) {
		return Promise.reject({ response, ok: false, data: json, headers: response.headers });
	};

	return Promise.resolve({ data: json, headers: response.headers, ok: response.ok, response });
}

apiFetch.setFetchHandler(handler);

// expose the configured apiFetch for non station api calls
export const configuredApiFetch = apiFetch;

function path( endpoint, query, version = 'v1' ) {
	const slashRoute = endpoint.startsWith('/') ? endpoint : '/' + endpoint;
	let path = `/wpcloud-station/${version}${slashRoute}`;
	if (query) {
		path = addQueryArgs(path, query);
	}
	return path;
}

async function call(options) {
	const { method = 'GET', endpoint } = options;
	options.path = path(endpoint, options.query, options.version);
	wpcloud.hooks.doAction('wpcloud_station_api_call', options);
	wpcloud.hooks.doAction(`wpcloud_station_api_call_${method}_${endpoint}`, options);

	//try {
	const response = apiFetch(options);
	response.then(
		({ data }) => {
			wpcloud.hooks.doAction(`wpcloud_station_api_response_${method}_${endpoint}`, data);
			wpcloud.hooks.doAction('wpcloud_station_api_response', data);
		},
		({ response }) => {
			wpcloud.hooks.doAction(`wpcloud_station_api_error_${method}_${endpoint}`, response);
			wpcloud.hooks.doAction('wpcloud_station_api_error', response);
		}
	);

	return response;
}

export default {
	async get(endpoint, options) {
		return call({ endpoint, ...options });
	},

	async post(endpoint, options) {
		const { data, form } = options;
		if (form) {
			data = {
				...(new FormData(form)),
				...(data || {}),
			};
		}
		return call({ method: 'POST', endpoint, data, ...options });
	},

	async put(endpoint, options) {
		const { data, form } = options;
		if (form) {
			data = {
				...(new FormData(form)),
				...(data || {}),
			};
		}
		return call({ method: 'PUT', endpoint, query, data, ...options});
	},

	async delete(endpoint, { query, version = 'v1', data = {}, form }) {
		if (form) {
			data = {
				...(new FormData(form)),
				...data,
			};
		}
		return call({ method: 'DELETE', endpoint, query, version, data });
	},
	call
}