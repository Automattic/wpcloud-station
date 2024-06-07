((wpcloud) => {

	// Bind clicks to any action buttons
	const buttons = document.querySelectorAll('.wp-block-wpcloud-button');

	function bindClick(button) {
		const action = button.dataset.wpcloudAction;

		if (action) {
			button.addEventListener('click', (event) => {
				const type = button.attributes.type?.value;

				// Let submit buttons emit the event and let the form handle it.
				if (type !== 'submit') {
					event.preventDefault();
					event.stopPropagation();
				}
				wpcloud.hooks.doAction(action, button);
			});
		}
	}
	wpcloud.bindButtonHandler = bindClick;
	buttons.forEach(bindClick);

	async function refreshHref( e ) {
		const link = e.currentTarget;
		const button = link.closest( '.wp-block-wpcloud-button[data-refresh-rate]' );
		const refreshRate = link.dataset.refreshRate || 15000;
		link.dataset.refresh = '';
		setTimeout( () => {
			link.dataset.refresh = 'true';
		}, refreshRate );

		const formData = new FormData();
		formData.append( 'action', 'wpcloud_refresh_link' );
		formData.append( 'site_id', button.dataset.siteId );
		formData.append( '_wpnonce', button.dataset.nonce );
		formData.append( 'site_detail', button.dataset.wpcloudDetail );
		const response = await fetch(
			'/wp-admin/admin-ajax.php',
			{
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams( formData ).toString(),
			}
		);

		const result = await response.json();
		if ( ! result.success ) {
			// @TODO: Show error message
			return false;
		}

		link.href = result.data.url;
	}

 document
		.querySelectorAll( '.wp-block-wpcloud-button[data-refresh-rate] a' )
		.forEach( async ( link ) => {
			const refreshRate = link.dataset.refreshRate || 15000;
			setTimeout( () => {
				link.dataset.refresh = 'true';
			}, refreshRate );
			link.addEventListener( 'click', async ( e ) => {
				if ( link.dataset.refresh ) {
					e.preventDefault();
					await refreshHref( e );
					link.click();
				}
				return true;
			} );

			link.addEventListener( 'contextmenu', async ( e ) => {
				if ( link.dataset.refresh ) {
					await refreshHref( e );
				}
			} );
		} );

})(window.wpcloud);