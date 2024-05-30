( ( wpcloud ) => {
	const aliasList = document.querySelector(
		'.wpcloud-block-site-alias-list'
	);

	// Find each row in the alias list and get the data-site-alias attribute
	aliasList
		.querySelectorAll(
			'.wpcloud-block-site-alias-list--row:not([style*="display:none"]'
		)
		.forEach( ( row ) => {
			const alias = row.dataset.siteAlias;
			if ( ! alias ) {
				// eslint-disable-next-line no-console
				console.error(
					'Missing data-site-alias attribute on alias list row'
				);
				return;
			}

			row.querySelectorAll( 'input[name=site_alias]' ).forEach(
				( input ) => ( input.value = alias )
			);
		} );

	function onSiteAliasRemove( result, form ) {
		if ( ! result.success ) {
			//@TODO: update how error is handled here
			alert( result.message ); // eslint-disable-line no-alert, no-undef
			return;
		}

		const row = form.closest( '.wpcloud-block-site-alias-list--row' );

		row.ontransitionend = () => {
			row.remove();
		};

		row.classList.add( 'wpcloud-hide' );
	}

	function onSiteAliasAdded( alias ) {
		const newRow = aliasList
			.querySelector(
				'.wpcloud-block-site-alias-list--row[style*="display:none"]'
			)
			.cloneNode( true );

		newRow.dataset.siteAlias = alias;

		newRow.querySelector(
			'.wpcloud-block-site-detail__value'
		).textContent = alias;

		// Set up the new forms
		newRow.querySelectorAll( 'form' ).forEach( ( form ) => {
			form.querySelector( 'input[name=site_alias]' ).value = alias;
			wpcloud.bindFormHandler( form );
		} );

		aliasList.appendChild( newRow );
		newRow.classList.add( 'wpcloud-hide' );
		newRow.style.display = 'flex';
		newRow.ontransitionend = () => {
			newRow.classList.remove( 'wpcloud-hide' );
			newRow.ontransitionend = null;
		};
	}

	function updateTextOnTransitionEnd( el, text ) {
		return () => {
			const a = el.querySelector( 'a' );
			a.href = `https://${ text }`;
			a.textContent = text;
			el.classList.remove( 'wpcloud-hide' );
			el.ontransitionend = null;
		};
	}

	function onSiteAliasMakePrimary( result, form ) {
		if ( ! result.success ) {
			alert( result.message ); // eslint-disable-line no-alert, no-undef
			return;
		}

		const newPrimary = result.site_alias;
		const alias = form.closest( '.wpcloud-block-site-alias-list--row' );
		const primary = aliasList.querySelector(
			'.wpcloud-block-site-alias-list__item--primary'
		);
		const oldPrimary = primary.dataset.domainName;

		// swap the data sets
		primary.dataset.domainName = newPrimary;
		alias.dataset.siteAlias = oldPrimary;

		// update alias form inputs
		alias
			.querySelectorAll( 'input[name=site_alias]' )
			.forEach( ( input ) => ( input.value = oldPrimary ) );

		// swap the values
		const primaryValueNode = primary.querySelector(
			'.wpcloud-block-site-detail__value'
		);
		primaryValueNode.ontransitionend = updateTextOnTransitionEnd(
			primaryValueNode,
			newPrimary
		);

		const aliasValueNode = alias.querySelector(
			'.wpcloud-block-site-detail__value'
		);
		aliasValueNode.ontransitionend = updateTextOnTransitionEnd(
			aliasValueNode,
			oldPrimary
		);

		primaryValueNode.classList.add( 'wpcloud-hide' );
		aliasValueNode.classList.add( 'wpcloud-hide' );
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

	// Disable the default destructive confirmation prompt.
	wpcloud.hooks.addFilter(
		'wpcloud_form_should_submit_site_alias_remove',
		'wpcloud',
		() => true
	);
} )( window.wpcloud );
