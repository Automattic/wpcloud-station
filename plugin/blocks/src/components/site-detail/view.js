(() => {
	const refreshLinks = document.querySelectorAll('.wpcloud-block-site-detail.refresh-link a');

	async function refreshHref(e) {
		const link = e.currentTarget;
		const refreshRate = link.dataset.refreshRate || 15000;
		link.dataset.refresh = '';
		setTimeout(() => {
			link.dataset.refresh = 'true';
		}, refreshRate);

		const formData = new FormData();
		formData.append('action', 'wpcloud_refresh_link');
		formData.append('site_id', link.dataset.siteId);
		formData.append('_wpnonce', link.dataset.nonce);
		formData.append('site_detail', link.dataset.siteDetail);
		const response = await fetch(
			'http://localhost:8888/wp-admin/admin-ajax.php',
			{
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams(formData).toString(),
			}
		);

		const result = await response.json();
		if (!result.success) {
			// @TODO: Show error message
			return false;
		}

		link.href = result.data.url;
	}

	refreshLinks.forEach(async (link) => {
		const refreshRate = link.dataset.refreshRate || 15000;
		setTimeout(() => {
			link.dataset.refresh = 'true';
		}, refreshRate);
		link.addEventListener('click', async (e) => {
			if (link.dataset.refresh) {
				e.preventDefault();
				await refreshHref(e);
				link.click();
			}
			return true;
		});

		link.addEventListener('contextmenu', async (e) => {
			if (link.dataset.refresh) {
				await refreshHref(e);
			}
		});
	});

})();