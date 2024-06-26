(() => {
	const passwordToggle = document.querySelectorAll('.wpcloud-block-form-input--toggle-hidden');
	passwordToggle.forEach((toggle) => {
		toggle.addEventListener('click', () => {
			const input = toggle.closest('.wpcloud-block-form-input--password').querySelector('.wpcloud-block-form-input__input');
			if ('password' === input.type) {
				input.type = 'text';
				toggle.querySelector('.wpcloud-block-form-input--toggle-hidden--seen').style.display = 'none';
				toggle.querySelector('.wpcloud-block-form-input--toggle-hidden--unseen').style.display = 'block';
			} else {
				input.type = 'password';
				toggle.querySelector('.wpcloud-block-form-input--toggle-hidden--seen').style.display = 'block';
				toggle.querySelector('.wpcloud-block-form-input--toggle-hidden--unseen').style.display = 'none';
			}
		});
	});
})();