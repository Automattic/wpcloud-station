( ( wpcloud ) => {
	/**
	 * Handle the response from the site alias add form.
	 * @param {Object} result - The response from the server.
	 */
	function onSiteAliasAdd( result ) {
		if ( ! result.success ) {
			alert( result.message ); // eslint-disable-line no-alert, no-undef
			return;
		}
		// @TODO: do something with the success ?
		const newAliasInput = document.querySelector(
			'.wpcloud-block-site-alias-form-add input[name=site_alias]'
		);
		if ( newAliasInput ) {
			newAliasInput.value = '';
		}
		wpcloud.hooks.doAction( 'wpcloud_alias_added', result.site_alias );
	}

	wpcloud.hooks.addAction(
		'wpcloud_form_response_site_alias_add',
		'site_alias_add',
		onSiteAliasAdd
	);
} )( window.wpcloud );
