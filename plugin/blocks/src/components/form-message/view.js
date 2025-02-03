
const formMessages = document.querySelectorAll('.wpcloud-form-message');

export function renderMessage(template, data) {
	let result = '';
	for (const key in data) {
		const render = new Function(key, `return \`${template}\`;`);
		result += render(data[key]);
	}

	return result;
}

formMessages.forEach((formMessage) => {
	const { wpcloudAction, messageType, messageTemplate } = formMessage.dataset;

	function renderAndShowMessage(response) {
		// handle both admin-ajax and REST API responses
		const success = response.success || response.ok;
		response = response.data || response;
		const p = formMessage.querySelector('p');

		const renderTemplate = () => {
			try {
				p.innerHTML = renderMessage(messageTemplate, { response });
			} catch (error) {
				console.error(error);
				p.innerHTML = messageTemplate;
			}
		}

		if (messageType === 'success' && success) {
			renderTemplate();
			formMessage.classList.remove('display-none');
		}
		if (messageType === 'error' && !success) {
			renderTemplate();
			formMessage.classList.remove('display-none');
		}
	}

	wpcloud.hooks.addAction(
		`wpcloud_form_response_${wpcloudAction}`,
		'wpcloud',
		renderAndShowMessage
	);

	wpcloud.hooks.addAction(
		`wpcloud_display_message_${wpcloudAction}`,
		'wpcloud',
		renderAndShowMessage
	);

	// clear out any existing messages
	wpcloud.hooks.addAction(
		`wpcloud_form_response`,
		'wpcloud',
		() => {
			if (formMessage.classList.contains('wpcloud-form-message--dismissable')) {
				formMessage.classList.add('display-none');
			}
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
