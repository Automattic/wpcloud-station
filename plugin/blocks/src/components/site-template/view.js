(() => {
	console.log('site-template view.js');
	document.querySelectorAll('.wpcloud-site-template tr, .wp-block-query .wp-block-wpcloud-site-card').forEach((row) => {
		row.addEventListener('click', (event) => {
			const targetLink = event.target.closest('a');
			if (targetLink) {
				return true;
			}
			const link = row.querySelector('.site-title a');
			if (link) {
				window.location = link.href;
			}
		});
	});
})()