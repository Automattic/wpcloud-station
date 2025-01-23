(() => {
	const windowPath = window.location.pathname.replace(/\/$/, '');
	if (!windowPath) {
		return;
	}
	document.querySelectorAll('.wp-block-wpcloud-nav a').forEach((link) => {
		if (link.href?.endsWith(windowPath)) {
			link.classList.add('contrast');
			link.classList.add('current');
			link.classList.remove('secondary');
			link.onclick = (e) => e.preventDefault() && false;
		}
	});
})();