((wpcloud) => {

	// Bind clicks to any action buttons
	const buttons = document.querySelectorAll('.wp-block-wpcloud-button');
	buttons.forEach((button) => {
		const action = button.dataset.wpcloudAction;

		if (action) {
			button.addEventListener('click', (event) => {
				const type = button.attributes.type?.value;

				// Let submit buttons emit the event and let the form handle it.
				if (type !== 'submit') {
					event.preventDefault();
					event.stopPropagation();
				}
				wpcloud.hooks.doAction(action, button);
			});
		}
	});

})(window.wpcloud);