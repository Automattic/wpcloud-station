((wpcloud) => {


	wpcloud.hooks.addAction(
		'wpcloud_ssh_user_update_button_click',
		'wpcloud',
		(button) => {
			const sshUser = button.closest('.wpcloud-block-ssh-user-list__row');

			const sshUserName = sshUser.dataset.siteSshUser;

			const sshUserContainer = button.closest('.wpcloud-block-ssh-users');

			const form = sshUserContainer.querySelector('.wpcloud-block-form');
			const nameInput = form.querySelector('input[name="user"]');
			nameInput.value = sshUserName;

			const submit = form.querySelector('button[type="submit"]');
			submit.querySelector('.wpcloud-block-button__label').textContent = 'Update';

			const wpCloudAction = form.querySelector('input[name="wpcloud_action"]');
			wpCloudAction.value = 'wpcloud_ssh_user_update';

			// Open the form section if it's not open already.
			const section = form.closest('.wpcloud-ssh-user-form--expanding-section');
			if (!section.classList.contains('is-open')) {
				const openFormButton = sshUserContainer.querySelector('[data-wpcloud-action="wpcloud_expanding_section_toggle"]');
				wpcloud.hooks.doAction('wpcloud_expanding_section_toggle', openFormButton);
			}
		}
	);

	wpcloud.hooks.addAction(
		'wpcloud_expanding_section_toggle_end',
		'wpcloud',
		(isOpen, section) => {
			if (isOpen) {
				return;
			}
			const sshUserSection = section.closest('.wpcloud-ssh-user-form--expanding-section');
			if (!sshUserSection) {
				return;
			}
			clearForm(sshUserSection.querySelector('.wpcloud-block-form'));
		}
	);

	// @TODO this is probably general enough to move to the form view.js
	function clearForm(form) {
		form.querySelectorAll('input').forEach((input) => {

			if (input.type === 'hidden') {
				return;
			}
			input.value = '';
		});

		const originalLabel = form.dataset.originalLabel;
		if (originalLabel) {
			const submit = form.querySelector('button[type="submit"]');
			submit.querySelector('.wpcloud-block-button__label').textContent = originalLabel;
		}

		const originalAction = form.dataset.originalAction;
		if ( originalAction ) {
			const wpCloudAction = form.querySelector('input[name="wpcloud_action"]');
			wpCloudAction.value = form.dataset.originalAction;
		}
	}

})(window.wpcloud)