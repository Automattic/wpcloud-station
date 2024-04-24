
wpcloud.bindFormHandler = (form) => {
	const button = form.querySelector( 'button[type="submit"]' );

	form.addEventListener( 'submit', async ( e ) => {
		e.preventDefault();
		button.setAttribute( 'disabled', 'disabled' );
		form.classList.add( 'is-loading' );
		form.classList.remove( 'is-error' );

		const formData = Object.fromEntries(
			new FormData( form ).entries()
		);
		formData.action = 'wpcloud_form_submit';

		try {
			const response = await fetch(
				'http://localhost:8888/wp-admin/admin-ajax.php',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: new URLSearchParams( formData ).toString(),
				}
			);
			wpcloud.hooks.doAction( 'wpcloud_form_submit', form );
			const result = await response.json();

			wpcloud.hooks.doAction( 'wpcloud_form_response', result.data );

			if ( response.ok && result?.data?.redirect ) {
				window.location = result.data.redirect;
			}

			button.removeAttribute( 'disabled' );
			form.classList.remove( 'is-loading' );

			if ( ! response.ok ) {
				form.classList.add( 'is-error' );
			}
		} catch (error) {
			console.error( error );
		}
	} );
}

document
	.querySelectorAll( 'form.wpcloud-block-form[data-ajax]' )
	.forEach( wpcloud.bindFormHandler );