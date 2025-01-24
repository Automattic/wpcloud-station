((wpcloud) => {
	const demo_domain = 'wpcloudstation.dev';
	const aliasList = document.querySelector(
		'.wpcloud-block-site-alias-list'
	);
	const primary = aliasList.querySelector(
		'.wpcloud-block-site-alias-list__item--primary'
	);
	const primaryRow = aliasList.querySelector(
		'.wpcloud-block-site-alias-list__row--primary'
	);
	const primaryValueNode = primary.querySelector(
		'.wpcloud-block-site-detail__value'
	);

	// hide the ability to make the demo domain primary
	aliasList
		.querySelector(`[data-site-alias$="${demo_domain}"] [data-original-action="site_alias_make_primary"]`)
		?.classList.add('display-none')

	aliasList.querySelectorAll( '[data-a' ).forEach( wpcloud.bindFormHandler );

	function onSiteAliasRemove( result, form ) {
		if ( ! result.success ) {
			return;
		}

		const row = form.closest( '.wpcloud-block-site-alias-list__row' );
		row.style.transition = 'transform 0.5s ease';
		row.style.transform = 'scaleY(0)';
		row.ontransitionend = () => {
			row.remove();
		};
	}

	async function onSiteAliasAdded({ site_alias: alias, needsVerification }) {

		const row = aliasList
			.querySelector(
				'.wpcloud-block-site-alias-list__row[style*="display:none"]'
		);

		if (!row) {
			return;
			}
		const newRow = row.cloneNode(true);

		newRow.dataset.siteAlias = alias;

		newRow.querySelector(
			'.wpcloud-block-site-detail__value'
		).textContent = alias;

		// Set up the new forms
		newRow.querySelectorAll('form').forEach((form) => {
			const inputs = form.querySelectorAll('input[name=site_alias]');
			inputs.forEach((input) => {
				input.value = alias;
			});
			wpcloud.bindFormHandler(form);
		});

		if (needsVerification) {
			['.wpcloud-block-form-request_txt_verification', '.alias-warning'].forEach((selector) => {
				newRow.querySelector(selector)?.classList.remove('display-none');
			});

			// Backwards compatibility with the old form flow.
			// If the form is not there go ahead and fetch the verification code.
			if (!newRow.querySelector('.wpcloud-block-form-request_txt_verification')) {

				const { verification, success, domain } = await wpcloud.stationFetch('/domains/txt-verification', { domain: alias });
				wpcloud.hooks.doAction('wpcloud_display_message_request_txt_verification', { success, domain, verification });

				if (success) {
					const detail = newRow.querySelector('.site-alias-verification-code');
					detail.dataset.clipboardPattern = verification;

					newRow.querySelector('.wpcloud-copy-to-clipboard')?.addEventListener('click', wpcloud.copyToClipboard);

					const value = detail.querySelector('.wpcloud-block-site-detail__value');
					value.textContent = `Verification code`;

					detail.classList.remove('display-none');
				}
			}
		}

		const aliasValueNode = newRow.querySelector('.wpcloud-block-site-detail__value');
		const anchor = document.createElement('a');
		anchor.href = `https://${alias}`;
		anchor.textContent = alias;
		aliasValueNode.textContent = '';
		aliasValueNode.appendChild(anchor);
		aliasList.appendChild(newRow);

		newRow.style.display = 'flex';
		newRow.classList.add( 'wpcloud-block-site-alias-list__row--new' );

		newRow.ontransitionend = () => {
			newRow.classList.remove( 'wpcloud-hide' );
			newRow.ontransitionend = null;
		};
	}

	function onSiteAliasMakePrimary( result, form ) {
		if ( ! result.success ) {
			alert( result.message ); // eslint-disable-line no-alert, no-undef
			return;
		}

		const newPrimary = result.site_alias;
		const alias = form.closest('.wpcloud-block-site-alias-list__row');

		const oldPrimary = primary.dataset.domainName;

		// swap the data sets
		primary.dataset.domainName = newPrimary;
		alias.dataset.siteAlias = oldPrimary;

		// update alias form inputs
		alias
			.querySelectorAll( 'input[name=site_alias]' )
			.forEach( ( input ) => ( input.value = oldPrimary ) );

		const aliasValueNode = alias.querySelector(
			'.wpcloud-block-site-detail__value'
		);

		primaryValueNode.classList.remove('is-pending');
		aliasValueNode.classList.remove('is-pending');

		const primaryAnchor = primaryValueNode.querySelector('a');
		primaryAnchor.href = `https://${newPrimary}`;
		primaryAnchor.textContent = newPrimary;

		const aliasAnchor = aliasValueNode.querySelector('a');
		aliasAnchor.href = `https://${oldPrimary}`;
		aliasAnchor.textContent = oldPrimary;

		// Check ssl on the domains
		[ primaryRow.querySelector('.wpcloud-block-form-retry-ssl'), alias.querySelector('.wpcloud-block-form-retry-ssl') ].forEach( verifySSL );
	}

	wpcloud.hooks.addAction(
		'wpcloud_form_response_site_alias_remove',
		'site_alias_remove',
		onSiteAliasRemove
	);

	wpcloud.hooks.addAction(
		'wpcloud_form_response_site_alias_add',
		'site_alias_list',
		onSiteAliasAdded
	);

	wpcloud.hooks.addAction(
		'wpcloud_form_response_site_alias_make_primary',
		'site_alias_make_primary',
		onSiteAliasMakePrimary
	);

	function setPending( form, action ) {
		const aliasRow = form.closest('.wpcloud-block-site-alias-list__row');
		aliasRow.classList.toggle('is-pending');

		if (action === 'site_alias_make_primary') {
			primaryValueNode.classList.toggle('is-pending');
		}

		const details = form.closest('details');
		details && ( details.open = false );
	}
	wpcloud.hooks.addAction(
		'wpcloud_form_submit_site_alias_make_primary',
		'site_alias_list',
		setPending
	);

	wpcloud.hooks.addAction(
		'wpcloud_form_submit_site_alias_remove',
		'site_alias_list',
		setPending
	)

	wpcloud.hooks.addAction(
		'wpcloud_button_alias_request_remove',
		'site_alias_list',
		(button) => {
			const aliasRow = button.closest('.wpcloud-block-site-alias-list__row');
			const alias = aliasRow.querySelector('.wpcloud-block-site-detail__value');
			alias.classList.toggle('is-pending');
		}
	);

	// Disable the default destructive confirmation prompt.
	wpcloud.hooks.addFilter(
		'wpcloud_form_should_submit_site_alias_remove',
		'wpcloud',
		() => true
	);

	function getDomainFromSSLForm(form) {
		if (!form) {
			return;
		}
		let domain = form.closest('.wpcloud-block-site-alias-list__row')
			?.querySelector('[data-domain-name]')
			?.dataset.domainName;
		if (!domain) {
			domain = form.closest('[data-site-alias]')
				?.dataset.siteAlias;
		}
		return domain;
	}

	async function verifySSL(form) {
		const domain = getDomainFromSSLForm(form);
		if ( ! domain || domain.includes( demo_domain ) ) {
			return;
		}
		const { success, valid } = await wpcloud.stationFetch('/domains/ssl-status', { domain });

		if ( success && ! valid ) {
			form.classList.remove('display-none');
			form.closest('.wpcloud-block-site-alias-list__row').querySelector('.alias-warning')?.classList.remove('display-none');
		} else {
			form.classList.add( 'display-none' );
		}
	}
	// SSL Retry
	wpcloud.hooks.addFilter(
		'wpcloud_form_data_retry_ssl',
		'wpcloud',
		( data, form ) => {
			data.domain_name = getDomainFromSSLForm( form );
			return data;
		}
	);

	wpcloud.hooks.addAction(
		'wpcloud_form_response_retry_ssl',
		'wpcloud',
		(result, form) => {
			if ( result.success ) {

				const button = form.querySelector('.wpcloud-block-button__label');
				if ( button ) {
					button.innerText = 'Queued';
				}
				setTimeout(() => {
					form.classList.add('wpcloud-hide');
				}, 1000);
				return;
			}
		}
	);

	// Check the status of the existing domains.
	aliasList.querySelectorAll('.wpcloud-block-form-retry-ssl').forEach( verifySSL );


})(window.wpcloud);
