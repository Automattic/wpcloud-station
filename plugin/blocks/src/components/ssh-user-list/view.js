( ( wpcloud ) => {
	const updateSshUserInputs = ( sshUserRow ) => {
		const sshUserInputs = sshUserRow.querySelectorAll(
			'form input[name=ssh_user]'
		);
		const sshUserName = sshUserRow.dataset.siteSshUser;
		sshUserInputs.forEach( ( input ) => {
			input.value = sshUserName;
		});

	};

	const sshUserList = document.querySelector(
		'.wp-block-wpcloud-ssh-user-list'
	);

	sshUserList
		.querySelectorAll(
			'.wpcloud-block-ssh-user-list__row:not([style*="display:none"])'
		)
		.forEach( updateSshUserInputs );

	function onSshUserAdded( { user } ) {
		const newRow = sshUserList
			.querySelector(
				'.wpcloud-block-ssh-user-list__row[style*="display:none"]'
			)
			.cloneNode( true );
		newRow.dataset.siteSshUser = user;
		newRow.querySelector(
			'.wpcloud-block-site-detail__value'
		).textContent = user;
		updateSshUserInputs( newRow );
		newRow.style.display = 'flex';
		sshUserList.appendChild(newRow);

		newRow.querySelectorAll('.wpcloud-block-form').forEach(wpcloud.bindFormHandler);
		newRow.querySelectorAll('.wpcloud-block-button').forEach(wpcloud.bindButtonHandler);
		newRow.classList.add('wpcloud-block-ssh-user-list__row--new');
	}

	function onSshUserRemove( result, form ) {
		if ( ! result.success ) {
			alert( result.message ); // eslint-disable-line no-alert, no-undef
			return;
		}

		const row = form.closest( '.wpcloud-block-ssh-user-list__row' );

		row.ontransitionend = row.remove;

		row.classList.add( 'wpcloud-hide' );
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

	// Disable the confirmation dialog for removing SSH users.
	wpcloud.hooks.addFilter(
		'wpcloud_form_should_submit_site_ssh_user_remove',
		'wpcloud',
		() => true
	);
} )( window.wpcloud );
