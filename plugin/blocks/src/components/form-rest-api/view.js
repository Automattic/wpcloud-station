((wpcloud) => {
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
