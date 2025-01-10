(() => {
	const windowPath = window.location.pathname.replace(/\/$/, '');
	document.querySelectorAll('.wp-block-wpcloud-nav a').forEach((link) => {
		if (link.href?.includes(windowPath)) {
			link.classList.add('contrast');
			link.classList.add('current');
			link.classList.remove('secondary');
			link.onclick = (e) => e.preventDefault() && false;
		}
	});
})();