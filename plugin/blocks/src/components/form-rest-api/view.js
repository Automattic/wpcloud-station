((wpcloud) => {
	/*
	document.querySelectorAll('form.wpcloud-block-form').forEach((form) => {
		// Fill in any missing hidden inputs closest data attribute
		const emptyHiddenInputs = form.querySelectorAll( 'input[type="hidden"][value=""]' );
		emptyHiddenInputs.forEach( ( input ) => {
			const dataName = `data-${input.name}`.replace(/_/g, '-');
			const closestData = input.closest( `[${dataName}]` );
			if ( closestData ) {
				input.value = closestData.getAttribute( dataName );
			}
		});

		form.addEventListener('reset', () => {
			wpcloud.hooks.doAction('wpcloud_form_reset', form);
			resetForm(form);
		});

		const resetButtons = form.querySelectorAll('button[type="reset"]');
		// Show any reset buttons if they exist on a dirty form
		form.addEventListener('input', () => {
			resetButtons.forEach((button) => {
				button.classList.remove('display-none');
			});
		});

	} );
*/
	// Bind form handlers to all forms with the `data-ajax` attribute
	document
		.querySelectorAll( '[data-rest-api-endpoint]' )
		.forEach(wpcloud.form.bindHandlers);

	wpcloud.hooks.addAction(
		'wpcloud_form_on_submit',
		'wpcloud',
		async (trigger) => {
			if (trigger.getAttribute('disabled')) {
				return;
			}
			wpcloud.form.toggleButton(trigger);
			const form = trigger.closest('form');
			await wpcloud.form.submitForm(form);
		}
	)
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
