((wpcloud) => {
	/**
	 * Handle the response from the site alias add form.
	 * @param {Object} result - The response from the server.
	 */
	function onSiteSshUserAdd( result, form ) {
		if ( ! result.success ) {
			alert( result.message ); // eslint-disable-line no-alert, no-undef
			return;
		}
		wpcloud.hooks.doAction( 'wpcloud_site_ssh_user_added', result.user );
	}
	wpcloud.hooks.addAction(
		'wpcloud_form_response_site_ssh_user_add',
		'site_ssh_user_add',
		onSiteSshUserAdd
	);

	function onSiteSshUserUpdate( result, form ) {
		if ( ! result.success ) {
			alert( result.message ); // eslint-disable-line no-alert, no-undef
			return;
		}
		// Close the form section.
		const section = form.closest(
			'.wpcloud-ssh-user-form--expanding-section'
		);
		wpcloud.hooks.doAction(
			'wpcloud_expanding_section_toggle',
			section
		);
	}
	wpcloud.hooks.addAction(
		'wpcloud_form_response_site_ssh_user_update',
		'site_ssh_user_update',
		onSiteSshUserUpdate
	);
} )( window.wpcloud );
