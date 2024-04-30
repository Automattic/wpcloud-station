
( ( wpcloud ) => {
	/**
	 * Handle the response from the site alias add form.
	 * @param {Object} result - The response from the server.
	 */
	function onSiteSshUserAdd( result ) {
		if ( ! result.success ) {
			alert( result.message ); // eslint-disable-line no-alert, no-undef
			return;
		}
		// @TODO: clear the ssh add user form

		wpcloud.hooks.doAction( 'wpcloud_site_ssh_user_added', result.user );
	}

	wpcloud.hooks.addAction(
		'wpcloud_form_response_site_ssh_user_add',
		'site_ssh_user_add',
		onSiteSshUserAdd
	);
} )( window.wpcloud );
