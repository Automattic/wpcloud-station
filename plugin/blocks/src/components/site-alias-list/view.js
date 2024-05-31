( ( wpcloud ) => {
	const aliasList = document.querySelector(
		'.wpcloud-block-site-alias-list'
	);
	const primary = aliasList.querySelector(
		'.wpcloud-block-site-alias-list__item--primary'
	);
	const primaryValueNode = primary.querySelector(
		'.wpcloud-block-site-detail__value'
	);

	function onSiteAliasRemove( result, form ) {
		if ( ! result.success ) {
			//@TODO: update how error is handled here
			alert( result.message ); // eslint-disable-line no-alert, no-undef
			return;
		}

		const row = form.closest( '.wpcloud-block-site-alias-list__row' );

		row.ontransitionend = () => {
			row.remove();
		};

		row.classList.add( 'wpcloud-hide' );
	}

	function onSiteAliasAdded( alias ) {
		const row = aliasList
			.querySelector(
				'.wpcloud-block-site-alias-list__row[style*="display:none"]'
			)
		if (!row) {
			return;
			}
		const newRow = row.cloneNode(true);

		newRow.dataset.siteAlias = alias;

		newRow.querySelector(
			'.wpcloud-block-site-detail__value'
		).textContent = alias;

		// Set up the new forms
		newRow.querySelectorAll( 'form' ).forEach( ( form ) => {
			form.querySelector( 'input[name=site_alias]' ).value = alias;
			wpcloud.bindFormHandler( form );
		} );

		// @TODO the new row does not have an anchor tag
		//newRow.querySelector( 'a' ).href = `https://${alias}`;
		aliasList.appendChild(newRow);
		// @TODO need to figure out the old fade effect for the new row
		//newRow.classList.add( 'wpcloud-hide' );
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
	}

	wpcloud.hooks.addAction(
		'wpcloud_form_response_site_alias_remove',
		'site_alias_remove',
		onSiteAliasRemove
	);

	wpcloud.hooks.addAction(
		'wpcloud_alias_added',
		'site_alias_list',
		onSiteAliasAdded
	);

	wpcloud.hooks.addAction(
		'wpcloud_form_response_site_alias_make_primary',
		'site_alias_make_primary',
		onSiteAliasMakePrimary
	);

	wpcloud.hooks.addAction(
		'wpcloud_button_alias_request_make_primary',
		'site_alias_list',
		(button) => {
			// find the alias attached to the button
			const aliasRow = button.closest('.wpcloud-block-site-alias-list__row');
			const alias = aliasRow.querySelector('.wpcloud-block-site-detail__value');
			alias.classList.toggle('is-pending');
			primaryValueNode.classList.toggle('is-pending');
		}
	);

	wpcloud.hooks.addAction(
		'wpcloud_button_alias_request_remove',
		'site_alias_list',
		(button) => {
			console.log('remove alias request');
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
} )( window.wpcloud );
