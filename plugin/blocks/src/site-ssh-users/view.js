((wpcloud) => {
		wpcloud.hooks.addAction(
		'wpcloud_ssh_user_update_button_click',
		'wpcloud',
		(button) => {
			console.log(button);

			const sshUserName = button.closest('.wpcloud-block-ssh-user').dataset.siteSshUser;

			const sshUserContainer = button.closest('.wpcloud-block-ssh-users');

			const form = sshUserContainer.querySelector('.wpcloud-block-form');
			console.log(sshUserName);

			/*
			1. Update the form with the SSH user name.
			2. Change the form action to 'site_ssh_user_update'.
			3. Change submit button label to 'Update'.
			4. Open up the expandable section.
			5. Scroll into into the section view.
			*/

			// Open the form section.
			const openFormButton = sshUserContainer.querySelector('[data-wpcloud-action="wpcloud_expanding_section_toggle"]');
			wpcloud.hooks.doAction('wpcloud_expanding_section_toggle', openFormButton);
		}
	)

})(window.wpcloud)