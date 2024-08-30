(() => {

	const form = document.querySelector('.wpcloud_block_form__log_download');

	function switchFilterOptions() {

	}

	let type = form.querySelector('.wpcloud-block-form-input__input[name="type"]')?.value;
	if (type) {
		type = 'error' === type ? 'site' : 'error';
		const filters = form.querySelectorAll(`[name^="filter-${type}"]`);
		filters.forEach((filter) => {
			filter.closest('.wpcloud-block-form--input').classList.add('display-none');
		});
	}

	form.querySelector('.wpcloud-block-form-input__input[name="type"]').addEventListener('change', () => {
		const filters = form.querySelectorAll(`[name^="filter-"]`);
		filters.forEach((filter) => {
			filter.value = '';
			filter.closest('.wpcloud-block-form--input').classList.toggle('display-none');
		});
	});

	function addUserTimezoneToForm(form) {
		if (!form || form.querySelector('input[name="timezone"]')) {
			return;
		}
		const date = new Date();
		const offsetMin = date.getTimezoneOffset();
		const hours = `0${Math.abs( offsetMin ) / 60 ^ 0 }`.slice(-2);
		const minutes = `0${offsetMin % 60}`.slice(-2);
		const sign = offsetMin < 0 ? '+' : '-';
		const offset = `${sign}${hours}${minutes}`;
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


	form.querySelectorAll('.wpcloud-block-form-input__input[name="log_end"]').forEach( addDatetimeLocalValue );
	form.querySelectorAll('.wpcloud-block-form-input__input[name="log_start"]').forEach((input) => {
		const date = new Date();
		date.setDate(date.getDate() - 7);
		addDatetimeLocalValue(input, date);
	});
})();