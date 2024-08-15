( ( wpcloud ) => {
	/**
	 * Handle the response from the site alias add form.
	 * @param {Object} result - The response from the server.
	 */
	function onSiteAliasAdd(result) {

		if ( !result.success && !result.needsVerification ) {
				alert( result.message ); // eslint-disable-line no-alert, no-undef
				return;
		}

		const newAliasInput = document.querySelector(
			'.wpcloud-block-form--site-alias-add input[name=site_alias]'
		);
		if ( newAliasInput ) {
			newAliasInput.value = '';
		}
		wpcloud.hooks.doAction( 'wpcloud_alias_added', result.site_alias, result.needsVerification || false );
	}

	wpcloud.hooks.addAction(
		'wpcloud_form_response_site_alias_add',
		'site_alias_add',
		onSiteAliasAdd
	);
} )( window.wpcloud );
