((wpcloud) => {

	const group = document.querySelector('.wpcloud-group-defensive-mode');
	if (!group) {
		return;
	}

	const statusValue = group.querySelector('[data-site-detail="defensive_mode"] .wpcloud-block-site-detail__value');

	const status = statusValue ? statusValue.textContent : 'Unknown';
	const disableButton = group.querySelector('[data-original-action="defensive_mode_disable"] button');
	const updateInput = group.querySelector('[data-original-action="defensive_mode_update"] .wpcloud-block-form-input__input');

	if (updateInput) {
		updateInput.value = '';
	}


	disableButton.disabled = 'disabled' === status.toLowerCase();

	wpcloud.hooks.addAction(
		'wpcloud_form_response_defensive_mode_update',
		'wpcloud',
		(response) => {
			if (response.success) {
				disableButton.disabled = false;
				statusValue.textContent = response.ddosUntil || 'Unknown';
				updateInput.value = '';
			}
		}
	);

	wpcloud.hooks.addAction(
		'wpcloud_form_response_defensive_mode_disable',
		'wpcloud',
		(response) => {
			if (response.success) {
				statusValue.textContent = 'Disabled';
			}
		}
	);



})(window.wpcloud);