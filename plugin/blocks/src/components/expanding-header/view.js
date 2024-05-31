((wpcloud) => {
	wpcloud.hooks.addAction('wpcloud_expanding_section_toggle', 'wpcloud', (target) => {
		const section = target.closest('.wpcloud-block-expanding-section');
		const wrapper = section.querySelector('.wpcloud-block-expanding-section__header-wrapper');
		wrapper.classList.toggle('is-open');
		section.classList.toggle('is-open');
	});

})( window.wpcloud );