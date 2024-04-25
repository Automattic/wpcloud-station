
((wpcloud) => {

	const aliasList = document.querySelector('.wpcloud-block-site-alias-list');
	const removeForm = document.querySelector('.wpcloud-block-site-alias-form-remove');

	const newAliasInput = document.querySelector('.wpcloud-block-site-alias-form-add input[name=site_alias]');

	wpcloud.hooks.addAction('wpcloud_form_response', 'site_alias_add', (result) => {
		if (result?.action === 'site_alias_add') {
			if (result.success) {
				const newAlias = removeForm.cloneNode(true);
				newAlias.querySelector('.wpcloud-block-site-detail__value').textContent = result.site_alias;
				newAlias.querySelector('input[name=site_alias]').value = result.site_alias;
				newAlias.dataset.siteAlias = result.site_alias;
				newAlias.removeAttribute('style');
				aliasList.appendChild(newAlias);

				wpcloud.bindFormHandler(newAlias);
				newAliasInput.value = '';

			} else {
				alert(result.message);
			}
		}
	});

	wpcloud.hooks.addAction('wpcloud_form_response', 'site_alias_remove', (result) => {
		if (result?.action === 'site_alias_remove') {
			wpcloud.fadeOut(aliasList.querySelector(`[data-site-alias="${result.site_alias}"]`));
		}
	});

	})(window.wpcloud);