((wpcloud) => {
	document.querySelectorAll('.wpcloud-block-expanding-section.click-to-toggle').forEach((section) => {
		section.addEventListener('click', (event) => {
			const selection = document.getSelection();
			if (selection.type === 'Range') {
				return;
			}
			event.preventDefault();
			section.classList.toggle('section-toggled');
			wpcloud.hooks.doAction('wpcloud_expanding_section_toggle', section);
		});
	});
})(window.wpcloud);