((wpcloud) => {

	wpcloud.hooks.addAction('wpcloud_expanding_section_toggle', 'wpcloud', (button) => {
		const section = button.closest('.wpcloud-block-expanding-section');
		console.log(section);
		const isClosed = section.classList.contains('is-closed');
		section.classList.toggle('is-closed', !isClosed);
	});

})( window.wpcloud );