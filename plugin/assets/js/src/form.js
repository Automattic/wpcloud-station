
function toggleButton(button) {
	if (!button) {
		return;
	}
	const disabled = button.getAttribute('disabled');
	const spinner = button.querySelector('.wpcloud-block-button__spinner');
	if (disabled) {
		button.removeAttribute('disabled');
		if (spinner) {
			spinner.classList.add('visibility-none');
			button.querySelector('.wpcloud-block-button__label').classList.remove('visibility-hidden');
		}
	} else {
		button.setAttribute('disabled', 'disabled');
		if (spinner) {
			spinner.classList.remove('visibility-none');
			button.querySelector('.wpcloud-block-button__label').classList.add('visibility-hidden');
		}
	}
}

function isConfirmed(data, action) {
	let confirmed;

	if (action) {
		confirmed = wpcloud.hooks.applyFilters(
			`wpcloud_form_should_submit_${action}`,
			confirmed,
			data
		);
	}

	if (confirmed === undefined) {
		confirmed = wpcloud.hooks.applyFilters(
			'wpcloud_form_should_submit',
			confirmed,
			data
		);
	}

	return confirmed !== false;
}

function maybeRedirect({ redirect }) {
	if (redirect) {
		if (redirect === 'reload') {
			window.location.reload();
		}
		window.location = redirect;
	}
}

function resetOnResponse(form, { resetInputs }) {
	toggleButton(form.querySelector('button[type="submit"]'));
	form.classList.remove('is-loading');
	if (resetInputs) {
		resetForm(form);
	}
}

function onError(form, action, response) {
	resetOnResponse(form, { resetInputs: false });
	form.classList.add('is-error');
	wpcloud.hooks.doAction( 'wpcloud_form_response_error', response, form );
	wpcloud.hooks.doAction(`wpcloud_form_response_${action}`, response, form );
}

function bindHandlers(form) {

	form.addEventListener('submit', async (e) => {
		const button = form.querySelector('button[type="submit"]');
		e.preventDefault();
		toggleButton(button);
		await onSubmit(form);
	});

	// Bind submit on change inputs
	form.querySelectorAll('.submit-on-change').forEach((input) => {
		if (input.type === 'text') {
			return;
		}

		input.addEventListener('change', () => {
			const data = { [input.name]: input.value };
			submit(form, { data });
		});
	});
}

async function onSubmit(form) {
	form.classList.add('is-loading');
	form.classList.remove('is-error');

	let formData = Object.fromEntries(new FormData(form));
	// skip submit on change inputs
	form.querySelectorAll('.submit-on-change').forEach((input) => {
		delete formData[input.name];
	});

	const { wpcloudAction, resetOnSuccess } = form.dataset;
	const action = wpcloudAction || form.action;

	// override redirect if a ref query string is present
	const queryString = window.location.search;
	const urlParams = new URLSearchParams(queryString);
	const redirect = urlParams.get('ref');
	if (redirect) {
		formData.redirect = redirect;
	}

	if (!wpcloud.form.isConfirmed(formData, action)) {
		toggleButton(button);
		form.classList.remove('is-loading');
		return;
	}


	wpcloud.hooks.doAction('wpcloud_form_submit', form, formData);
	if (action) {
		wpcloud.hooks.doAction(`wpcloud_form_submit_${action}`, form, action );
		formData = wpcloud.hooks.applyFilters(`wpcloud_form_data_${action}`, formData );
	}

	try {
		const result = await submit(form, { data: formData, foo: 'bar' });
		const { response, headers, data } = result;

		const contentType = headers.get('Content-Type');

		if (contentType.includes('octet-stream')) {
			await saveDownload(response);
			data = { action: 'download' };
		}

		wpcloud.hooks.doAction('wpcloud_form_response', response, form );
		if (action) {
			wpcloud.hooks.doAction(`wpcloud_form_response_${action}`, response, form );
		}

		resetOnResponse(form, { resetInputs: resetOnSuccess });
		maybeRedirect(data);
	} catch ({ data:response }) {
		return onError(form, action, response );
	}
}

async function submit(form, o) {
	const { data, ...options } = o;
	// Check if submitting to the rest api
	const { restApiEndpoint, useStationApi, stationRestApiVersion: version } = form.dataset;
	if (useStationApi) {
		const endpoint = restApiEndpoint || form.action;
		const apiOptions = { data, version, ...options };
		return wpcloud.stationApi.post(endpoint, apiOptions);
	}
	if (restApiEndpoint) {
		return wpcloud.apiFetch({data, ...options});
	}

	// Otherwise, submit to the admin-ajax.php
	return fetch(
		'/wp-admin/admin-ajax.php',
		{
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: new URLSearchParams(data).toString(),
		}
	);
}

export default {
	toggleButton,
	bindHandlers,
	isConfirmed,
	submit
}
