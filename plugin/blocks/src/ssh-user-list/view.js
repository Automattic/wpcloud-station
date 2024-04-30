((wpcloud) => {

	const sshUserList = document.querySelector('.wpcloud-block-site-ssh-user-list');

	function onSshUserAdded( { user } ) {
		const newForm = sshUserList.querySelector('form').cloneNode(true);
		newForm.querySelector(
			'.wpcloud-block-site-detail__value'
		).textContent = user;
		newForm.querySelector('input[name=site_ssh_user]').value = user;
		wpcloud.bindFormHandler(newForm);

		sshUserList.appendChild(newForm);
		newForm.style.display = 'flex';
	}

	function onSshUserRemove(result, form) {
		console.log(result, form);
		if (!result.success) {
			alert(result.message); // eslint-disable-line no-alert, no-undef
			return;
		}

		form.ontransitionend = () => {
			form.remove();
		};

		form.classList.add( 'wpcloud-hide' );
	}

	console.log('registering hooks for ssh user list view');
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

})(window.wpcloud);
