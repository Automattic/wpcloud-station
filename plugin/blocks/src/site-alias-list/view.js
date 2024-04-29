( ( wpcloud ) => {
	const aliasList = document.querySelector(
		'.wpcloud-block-site-alias-list'
	);

	function onSiteAliasRemove( result, form ) {
		if ( ! result.success ) {
			//@TODO: update how error is handled here
			alert( result.message ); // eslint-disable-line no-alert, no-undef
			return;
		}

		form.ontransitionend = () => {
			form.remove();
		};

		form.classList.add( 'wpcloud-hide' );
	}

	function onSiteAliasAdded( alias ) {
		const newForm = aliasList.querySelector( 'form' ).cloneNode( true );
		const anchor = document.createElement( 'a' );
		anchor.href = `https://${ alias }`;
		anchor.textContent = alias;
		newForm
			.querySelector( '.wpcloud-block-site-detail__value' )
			.appendChild( anchor );

		newForm.querySelector( 'input[name=site_alias]' ).value = alias;
		aliasList.appendChild( newForm );
		newForm.style.display = 'flex';
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
} )( window.wpcloud );
