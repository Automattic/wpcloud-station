(() => {

	function addUserTimezoneToForm(form) {
		if (!form || form.querySelector('input[name="timezone"]')) {
			return;
		}
		const date = new Date();
		const offsetMin = date.getTimezoneOffset();
		const hours = `0${offsetMin / 6 ^ 0}`.slice(-2);
		const minutes = `0${offsetMin % 60}`.slice(-2);
		const offset = `${hours}:${minutes}`;
		const timeZoneInput = document.createElement('input');
		timeZoneInput.type = 'hidden';
		timeZoneInput.name = 'timezone';
		timeZoneInput.value = offset;
		form.appendChild(timeZoneInput);
	}


	function addDatetimeLocalValue(input, date) {
		if (input.value) {
			return;
		}
		if ( ! ( date instanceof Date ) ) {
			date = new Date();
		}
		input.value = date.toISOString().split('T')[0] + 'T' + date.toTimeString().split(':').slice(0,2).join(':');
		const form = input.closest('form');
		addUserTimezoneToForm(form);
	}


	document.querySelectorAll('.wpcloud-block-form-input__input[name="log_end"]').forEach( addDatetimeLocalValue );
	document.querySelectorAll('.wpcloud-block-form-input__input[name="log_start"]').forEach((input) => {
		const date = new Date();
		date.setDate(date.getDate() - 28);
		addDatetimeLocalValue(input, date);
	});
})();