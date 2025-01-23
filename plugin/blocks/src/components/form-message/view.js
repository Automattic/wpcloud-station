
const formMessages = document.querySelectorAll('.wpcloud-form-message');

formMessages.forEach((formMessage) => {
	const { wpcloudAction, messageType, messageTemplate } = formMessage.dataset;

	wpcloud.hooks.addAction(
		`wpcloud_form_response_${wpcloudAction}`,
		'wpcloud',
		(response) => {
			const render = new Function('response', `return \`${messageTemplate}\`;`);
			const p = formMessage.querySelector('p');
			try {
				p.innerHTML = render(response);
			} catch (error) {
				console.error(error);
				p.innerHTML = messageTemplate;
			}

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

	// bind the close element
	const dismiss = formMessage.querySelector('.dismiss');
	if (dismiss) {
		dismiss.addEventListener('click', () => {
			formMessage.classList.add('hidden');
		});
	}
});