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

	wpcloud.hooks.addAction(
		'wpcloud_site_ssh_user_added',
		'site_ssh_user_added',
		onSshUserAdded
	);

})(window.wpcloud);
