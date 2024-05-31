( ( wpcloud ) => {
	wpcloud.bindFormHandler = ( form ) => {
		const button = form.querySelector( 'button[type="submit"]' );

		form.addEventListener( 'submit', async ( e ) => {
			e.preventDefault();
			button.setAttribute( 'disabled', 'disabled' );
			form.classList.add( 'is-loading' );
			form.classList.remove( 'is-error' );

			const formData = Object.fromEntries(
				new FormData( form ).entries()
			);
			formData.action = 'wpcloud_form_submit';

			// override redirect if a ref query string is present
			const queryString = window.location.search;
			const urlParams = new URLSearchParams( queryString );
			const redirect = urlParams.get( 'ref' );
			if ( redirect ) {
				formData.redirect = redirect;
			}

			// let other scripts know that the form is about to be submitted
			let confirmed;
			confirmed = wpcloud.hooks.applyFilters(
				`wpcloud_form_should_submit_${ formData.wpcloud_action }`,
				confirmed,
				formData
			);

			if ( confirmed === undefined ) {
				confirmed = wpcloud.hooks.applyFilters(
					'wpcloud_form_should_submit',
					confirmed,
					formData
				);
			}

			if ( confirmed === false ) {
				button.removeAttribute( 'disabled' );
				form.classList.remove( 'is-loading' );
				return;
			}

			wpcloud.hooks.doAction( 'wpcloud_form_submit', form );
			try {
				const response = await fetch(
					'/wp-admin/admin-ajax.php',
					{
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded',
						},
						body: new URLSearchParams( formData ).toString(),
					}
				);

				const result = await response.json();

				wpcloud.hooks.doAction(
					'wpcloud_form_response',
					result.data,
					form
				);
				wpcloud.hooks.doAction(
					`wpcloud_form_response_${ result.data.action }`,
					result.data,
					form
				);

				if ( response.ok && result?.data?.redirect ) {
					if ( result.data.redirect === 'reload' ) {
						window.location.reload();
						return;
					}
					window.location = result.data.redirect;
				}

				button.removeAttribute( 'disabled' );
				form.classList.remove( 'is-loading' );

				if ( ! response.ok ) {
					form.classList.add( 'is-error' );
				}
			} catch ( error ) {
				/* eslint-disable no-console */
				console.error( error );
			}
		} );
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
