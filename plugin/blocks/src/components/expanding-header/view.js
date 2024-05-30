((wpcloud) => {
	wpcloud.hooks.addAction('wpcloud_expanding_section_toggle', 'wpcloud', (target) => {
		const section = target.closest('.wpcloud-block-expanding-section');
		const wrapper = section.querySelector('.wpcloud-block-expanding-section__header-wrapper');
		if (wrapper.classList.contains('hide-on-open')) {
			wrapper.classList.toggle('is-open');
		}
	});

})( window.wpcloud );