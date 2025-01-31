((wpcloud) => {
	wpcloud.hooks.addAction('wpcloud_spinner_show', 'wpcloud', (spinner) => {
		spinner.classList.remove('visibility-none');
	});
	wpcloud.hooks.addAction('wpcloud_spinner_hide', 'wpcloud', (spinner) => {
		spinner.classList.add('visibility-none');
	});
})(window.wpcloud || (window.wpcloud = {}));