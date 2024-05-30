((wpcloud) => {
	document.querySelectorAll('.wpcloud-block-expanding-section.click-to-toggle').forEach((section) => {
		section.addEventListener('click', (event) => {
			event.preventDefault();
			wpcloud.hooks.doAction('wpcloud_expanding_section_toggle', section);
		});
	});
})(window.wpcloud);