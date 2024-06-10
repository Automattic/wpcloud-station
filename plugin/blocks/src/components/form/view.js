((wpcloud) => {

	async function submitFormData(form, data) {
		form.classList.add('is-loading');
		form.classList.remove('is-error');



		// make sure the hidden input is in the form data
		form.querySelectorAll('input[type="hidden"]').forEach((input) => {
			data[input.name] = input.value;
		});

		data.action = 'wpcloud_form_submit';
		// override redirect if a ref query string is present
		const queryString = window.location.search;
		const urlParams = new URLSearchParams(queryString);
		const redirect = urlParams.get('ref');
		if (redirect) {
			data.redirect = redirect;
		}

		// let other scripts know that the form is about to be submitted
		let confirmed;
		confirmed = wpcloud.hooks.applyFilters(
			`wpcloud_form_should_submit_${data.wpcloud_action}`,
			confirmed,
			data
		);

		if (confirmed === undefined) {
			confirmed = wpcloud.hooks.applyFilters(
				'wpcloud_form_should_submit',
				confirmed,
				data
			);
		}

		if (confirmed === false) {
			button.removeAttribute('disabled');
			form.classList.remove('is-loading');
			return;
		}

		const action = data.wpcloud_action;
		wpcloud.hooks.doAction('wpcloud_form_submit', form, action);
		if (action) {
			wpcloud.hooks.doAction(`wpcloud_form_submit_${action}`, form, action);
		}
		try {
			const response = await fetch(
				'/wp-admin/admin-ajax.php',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: new URLSearchParams(data).toString(),
				}
			);

			const result = await response.json();

			wpcloud.hooks.doAction(
				'wpcloud_form_response',
				result.data,
				form
			);
			wpcloud.hooks.doAction(
				`wpcloud_form_response_${result.data.action}`,
				result.data,
				form
			);

			if (response.ok && result?.data?.redirect) {
				if (result.data.redirect === 'reload') {
					window.location.reload();
					return;
				}
				window.location = result.data.redirect;
			}

			button && button.removeAttribute('disabled');
			form.classList.remove('is-loading');

			if (!response.ok) {
				form.classList.add('is-error');
			}
		} catch (error) {
			/* eslint-disable no-console */
			console.error(error);
		}
	}


	wpcloud.bindFormHandler = (form) => {
		const button = form.querySelector('button[type="submit"]');

		form.addEventListener('submit', async (e) => {
			e.preventDefault();
			button && button.setAttribute('disabled', 'disabled');

			const data = Object.fromEntries(new FormData(form));

			await submitFormData(form, data);
		});
	};

	// Fill in any missing hidden inputs closest data attribute
	document.querySelectorAll( 'form.wpcloud-block-form' ).forEach( ( form ) => {
		const emptyHiddenInputs = form.querySelectorAll( 'input[type="hidden"][value=""]' );
		emptyHiddenInputs.forEach( ( input ) => {
			const dataName = `data-${input.name}`.replace(/_/g, '-');
			const closestData = input.closest( `[${dataName}]` );
			if ( closestData ) {
				input.value = closestData.getAttribute( dataName );
			}
		});
	} );

	// Bind form handlers to all forms with the `data-ajax` attribute
	document
		.querySelectorAll( 'form.wpcloud-block-form[data-ajax]' )
		.forEach(wpcloud.bindFormHandler);


	// Default handler for destructive actions
	// if `confirmed` is defined then we can assume some other script has already handled the confirmation.
	wpcloud.hooks.addFilter(
		'wpcloud_form_should_submit',
		'wpcloud',
		( confirmed, data ) => {
			if ( confirmed !== undefined ) {
				return confirmed;
			}

			if ( data?.wpcloud_action?.match( /delete|remove/ ) ) {
				return confirm( 'Are you sure you want to delete this item?' );
			}
		},
		20
	);
} )( window.wpcloud );
