((wpcloud) => {

	// Bind clicks to any action buttons
	const buttons = document.querySelectorAll('.wp-block-wpcloud-button');
	buttons.forEach((button) => {
		const action = button.dataset.wpcloudAction;

		if (action) {
			button.addEventListener('click', (event) => {
				event.preventDefault();
				wpcloud.hooks.doAction(action, button);
			});
		}
	});

})(window.wpcloud);