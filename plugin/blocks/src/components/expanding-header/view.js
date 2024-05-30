((wpcloud) => {

	wpcloud.hooks.addAction('wpcloud_expanding_section_toggle', 'wpcloud', ( button ) => {
		const section = button.closest('.wpcloud-block-expanding-section');
		const wrapper = section.querySelector('.wpcloud-block-expanding-section__header-wrapper');
		if (wrapper.classList.contains('hide-on-open')) {
			wrapper.classList.toggle('is-open');
		}
	});

})( window.wpcloud );