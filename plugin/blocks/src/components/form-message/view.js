
const formMessages = document.querySelectorAll('.wpcloud-form-message');

formMessages.forEach((formMessage) => {
	const { wpcloudAction, messageType, messageTemplate } = formMessage.dataset;

	wpcloud.hooks.addAction(
		`wpcloud_form_response_${wpcloudAction}`,
		'wpcloud',
		(response) => {
			const render = new Function('response', `return \`${messageTemplate}\`;`);
			const p = formMessage.querySelector('p');

			const renderTemplate = () => {
				try {
					p.innerHTML = render(response);
				} catch (error) {
					console.error(error);
					p.innerHTML = messageTemplate;
				}
			}

			const success = response.success;
			if (messageType === 'success' && success) {
				renderTemplate();
				formMessage.classList.remove('display-none');
			}
			if (messageType === 'error' && !success) {
				renderTemplate();
				formMessage.classList.remove('display-none');
			}
		}
	);

	// clear out any existing messages
	wpcloud.hooks.addAction(
		`wpcloud_form_submit_${wpcloudAction}`,
		'wpcloud',
		() => {
			formMessage.classList.add('display-none');
		}
	);

	// bind the close element
	const dismiss = formMessage.querySelector('.dismiss');
	if (dismiss) {
		dismiss.addEventListener('click', () => {
			formMessage.classList.add('display-none');
		});
	}

	// bind reset event
	wpcloud.hooks.addAction(
		'wpcloud_form_reset',
		'wpcloud',
		() => {
			formMessage.classList.add('display-none');
		}
	)
});
