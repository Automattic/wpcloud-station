((wpcloud) => {
	wpcloud.hooks.addAction('wpcloud_show_spinner', 'wpcloud', (spinner) => {
		spinner.style.display = 'block';
	});
	wpcloud.hooks.addAction('wpcloud_hide_spinner', 'wpcloud', (spinner) => {
		spinner.style.display = 'none';
	});
})(window.wpcloud || (window.wpcloud = {}));