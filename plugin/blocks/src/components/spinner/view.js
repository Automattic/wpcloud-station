((wpcloud) => {
	wpcloud.hooks.addAction('wpcloud_spinner_show', 'wpcloud', (spinner) => {
		spinner.style.display = 'block';
	});
	wpcloud.hooks.addAction('wpcloud_spinner_hide', 'wpcloud', (spinner) => {
		spinner.style.display = 'none';
	});
})(window.wpcloud || (window.wpcloud = {}));