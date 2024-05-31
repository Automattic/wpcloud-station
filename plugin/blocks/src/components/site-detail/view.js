( ( wpcloud ) => {
	const onCopyToClipboard = async ( e ) => {
		const detail = e.currentTarget.closest('.wpcloud-block-site-detail');
		const isObscured = detail.querySelector('.is-obscured');
		if (isObscured) {
			return true;
		}
		const value = detail.querySelector(
			'.wpcloud-block-site-detail__value'
		);
		const listValue = value.querySelector(
			'.wpcloud_block_site_detail__value__list'
		);

		let text = '';
		if ( listValue ) {
			listValue.querySelectorAll( 'li' ).forEach( ( li ) => {
				text += li.innerText + '\n';
			} );
		} else {
			text = value.innerText;
		}
		try {
			await navigator.clipboard.writeText( text );
			alert( 'Copied to clipboard' );
		} catch ( error ) {
			console.error( error.message );
		}
	};

	// Bind the onCopyToClipboard function to the wpcloud object
	// for dynamically created elements
	wpcloud.copyToClipboard = wpcloud?.onCopyToClipboard || onCopyToClipboard;

	const siteDetails = document.querySelectorAll( '.wpcloud-block-site-detail' );

	siteDetails.forEach((siteDetail) => {
		siteDetail.querySelectorAll('.wpcloud-copy-to-clipboard')
			.forEach((element) => {
				element.addEventListener('click', wpcloud.copyToClipboard);
			});
	});

	siteDetails.forEach((siteDetail) => {
		siteDetail.querySelectorAll('.is-obscured').forEach((element) => {
			const revealButton = element.querySelector('.wpcloud-reveal-value');
			revealButton.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();
				element.classList.toggle('is-obscured');
			});
		})
	});
} )( window.wpcloud );
