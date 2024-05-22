((wpcloud) => {

	const onCopyToClipboard = async (e) => {
		const detail = e.currentTarget.closest('.wpcloud-block-site-detail');
		const value = detail.querySelector('.wpcloud-block-site-detail__value');
		const listValue = value.querySelector('.wpcloud_block_site_detail__value__list');

		let text = '';
		if (listValue) {
			listValue.querySelectorAll('li').forEach((li) => {
				text += li.innerText + '\n';
			});
		} else {
			text = value.innerText;
		}
		try {
			await navigator.clipboard.writeText(text);
			alert('Copied to clipboard');
  	} catch (error) {
    	console.error(error.message);
  	}
	}

	// Bind the onCopyToClipboard function to the wpcloud object
	// for dynamically created elements
	wpcloud.copyToClipboard = wpcloud?.onCopyToClipboard || onCopyToClipboard;

	document.querySelectorAll('.wpcloud-block-site-detail .copy-to-clipboard').forEach((element) => {
		element.addEventListener('click', wpcloud.copyToClipboard);
	});

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

	document.querySelectorAll('.wpcloud-block-site-detail.refresh-link a').forEach(async (link) => {
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

})(window.wpcloud);