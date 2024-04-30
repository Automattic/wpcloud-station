( ( wpcloud ) => {
	const aliasList = document.querySelector(
		'.wpcloud-block-site-alias-list'
	);

	const makePrimaryButtonQuery =
		'.wpcloud-block-form-site-alias--make-primary';

	function bindMakePrimaryButton( button ) {
		button.onclick = ( e ) => {
			e.preventDefault();
			const form = button.closest( 'form' );
			form.querySelector( 'input[name=wpcloud_action]' ).value =
				'site_alias_make_primary';
			form.dispatchEvent( new Event( 'submit' ) );
		};
	}

	aliasList
		.querySelectorAll( makePrimaryButtonQuery )
		.forEach( bindMakePrimaryButton );

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
		newForm.querySelector(
			'.wpcloud-block-site-detail__value'
		).textContent = alias;

		newForm.querySelector( 'input[name=site_alias]' ).value = alias;
		bindMakePrimaryButton(
			newForm.querySelector( makePrimaryButtonQuery )
		);
		wpcloud.bindFormHandler( newForm );

		aliasList.appendChild( newForm );
		newForm.style.display = 'flex';
	}

	function updateTextOnTransitionEnd( el, text ) {
		return () => {
			el.textContent = text;
			el.classList.remove( 'wpcloud-hide' );
			el.ontransitionend = null;
		};
	}

	function onSiteAliasMakePrimary( result, form ) {
		if ( ! result.success ) {
			alert( result.message ); // eslint-disable-line no-alert, no-undef
			return;
		}

		form.querySelector( 'input[name=wpcloud_action]' ).value =
			'site_alias_remove';

		const primary = aliasList.querySelector(
			'.wpcloud-block-site-alias-list__item--primary'
		);
		const primaryValue = primary.querySelector(
			'.wpcloud-block-site-detail__value'
		);

		const aliasValue = form.querySelector(
			'.wpcloud-block-site-detail__value'
		);

		const oldPrimary = primaryValue.textContent;
		const newPrimary = result.site_alias;

		const removeFormInput = form.querySelector( 'input[name=site_alias]' );
		removeFormInput.value = oldPrimary;
		removeFormInput.name = 'site_alias';

		primaryValue.ontransitionend = updateTextOnTransitionEnd(
			primaryValue,
			newPrimary
		);
		aliasValue.ontransitionend = updateTextOnTransitionEnd(
			aliasValue,
			oldPrimary
		);

		primaryValue.classList.add( 'wpcloud-hide' );
		aliasValue.classList.add( 'wpcloud-hide' );
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
	wpcloud.hooks.addFilter('wpcloud_form_should_submit_site_alias_remove', 'wpcloud', () => true );

} )( window.wpcloud );
