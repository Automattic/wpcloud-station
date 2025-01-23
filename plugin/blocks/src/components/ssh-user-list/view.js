/**
 * Wpcloud SSH User List Block.
 */
((wpcloud) => {
	const updateSshUserInputs = (sshUserRow) => {
		const sshUserInputs = sshUserRow.querySelectorAll(
			'form input[name=ssh_user]'
		);
		const sshUserName = sshUserRow.dataset.siteSshUser;
		sshUserInputs.forEach( ( input ) => {
			input.value = sshUserName;
		} );

	};

	const sshUserList = document.querySelector(
		'.wp-block-wpcloud-ssh-user-list'
	);

	sshUserList
		.querySelectorAll(
			'.wpcloud-block-ssh-user-list__row:not([style*="display:none"])'
		)
		.forEach( updateSshUserInputs );

	function onSshUserAdded({ user }) {
		const newRow = sshUserList
			.querySelector(
				'.wpcloud-block-ssh-user-list__row[style*="display:none"]'
			)
			.cloneNode(true);
		newRow.dataset.siteSshUser = user;
		newRow.querySelector(
			'.wpcloud-block-site-detail__value'
		).textContent = user;

		updateSshUserInputs(newRow);
		newRow.style.display = 'flex';
		sshUserList.appendChild(newRow);

		newRow.querySelectorAll('.wpcloud-block-form').forEach(wpcloud.bindFormHandler);
		newRow.querySelectorAll('.wpcloud-block-button').forEach(wpcloud.bindButtonHandler);
		newRow.classList.add('wpcloud-block-ssh-user-list__row--new');
	}

	function onSshUserRemove(result, form) {
		if (!result.success) {
			return;
		}

		const row = form.closest('.wpcloud-block-ssh-user-list__row');

		row.ontransitionend = row.remove;

		row.classList.add('wpcloud-hide');
	}

	function onSshUserInitUpdate(button) {
		const sshUser = button.closest(
			'.wpcloud-block-ssh-user-list__row'
		);

		const sshUserName = sshUser.dataset.siteSshUser;

		const details = button.closest('details');
		details && ( details.open = false );

		const form = document.querySelector('.wpcloud-form-ssh-user');

		if (!form) {
			return;
		}

		const nameInput = form.querySelector('input[name="user"]');
		nameInput.value = sshUserName;
		nameInput.dataset.wasWritable = true;
		nameInput.readOnly = true;

		// Clear the other inputs on the form
		const inputs = form.querySelectorAll('input[type="password"], textarea');
		inputs.forEach( input => input.value = '' );

		const submit = form.querySelector('button[type="submit"]');
		submit.querySelector('.wpcloud-block-button__label').textContent =
			'Update';

		const wpCloudAction = form.querySelector(
			'input[name="wpcloud_action"]'
		);
		wpCloudAction.value = 'site_ssh_user_update';

		// scroll to the form
		wpcloud.scrollTo(form).andHighlight('input[name="user"]');

		// show any hidden reset buttons
		const resetButtons = form.querySelectorAll('button[type="reset"]');
		resetButtons.forEach((button) => {
			button.classList.remove('hidden');
		});
	}

	wpcloud.hooks.addAction(
		'wpcloud_form_response_site_ssh_user_remove',
		'site_ssh_user_add',
		onSshUserRemove
	);

	wpcloud.hooks.addAction(
		'wpcloud_site_ssh_user_added',
		'site_ssh_user_added',
		onSshUserAdded
	);

	wpcloud.hooks.addAction(
		'wpcloud_ssh_user_update_button_click',
		'wpcloud',
		onSshUserInitUpdate
	);


	// Disable the confirmation dialog for removing SSH users.
	wpcloud.hooks.addFilter(
		'wpcloud_form_should_submit_site_ssh_user_remove',
		'wpcloud',
		() => true
	);

} )( window.wpcloud );
