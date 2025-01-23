
const formMessages = document.querySelectorAll('.wpcloud-form-message');

formMessages.forEach((formMessage) => {
	const { wpcloudAction, messageType } = formMessage.dataset;

	wpcloud.hooks.addAction(
		`wpcloud_form_response_${wpcloudAction}`,
		'wpcloud',
		(response) => {
			const message = response.message;
			const success = response.success;
			if (messageType === 'success' && success) {
				formMessage.classList.remove('hidden');
			}
			if (messageType === 'error' && !success) {
				formMessage.classList.remove('hidden');
			}
		}
	);

	// clear out any existing messages
	wpcloud.hooks.addAction(
		`wpcloud_form_submit_${wpcloudAction}`,
		'wpcloud',
		() => {
			formMessage.classList.add('hidden');
		}
	);
});