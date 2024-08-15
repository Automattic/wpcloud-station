( ( wpcloud ) => {
	wpcloud.hooks.addAction(
		'wpcloud_form_response_site_alias_add',
		'site_alias_add',
		(result) => {
			if (!result.success && !result.needsVerification) {
				return;
			}
			const form = document.querySelector(
				'.wpcloud-site-alias-add--expanding-section'
			);
			wpcloud.hooks.doAction('wpcloud_expanding_section_toggle', form);
		}
	);

} )( window.wpcloud );
