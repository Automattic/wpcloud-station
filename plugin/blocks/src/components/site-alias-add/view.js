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


	wpcloud.hooks.addFilter(
		'wpcloud_form_data_request_txt_verification',
		'request_txt_verification',
		(data, form) => {
			const alias = form.closest('[data-site-alias]')
				?.dataset.siteAlias;
			data.domain_name = alias;
			return data;
		}
	)

	wpcloud.hooks.addAction(
		'wpcloud_form_response_request_txt_verification',
		'request_txt_verification',
		(result, form) => {
			if (!result.success) {
				alert(result.message); // eslint-disable-line no-alert, no-undef
				return;
			}
			console.log(form);
			const code = result.code;
			const actions = form.closest('.wpcloud-alias-actions');
			const detail = actions.querySelector('.site-alias-verification-code');
			detail.dataset.clipboardPattern = code;

			actions.querySelector('.wpcloud-copy-to-clipboard')?.addEventListener('click', wpcloud.copyToClipboard);

			const value = detail.querySelector('.wpcloud-block-site-detail__value');
			value.textContent = `Verification code`;

			detail.classList.remove('display-none');
		}
	);
} )( window.wpcloud );
