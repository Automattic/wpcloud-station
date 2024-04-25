
((wpcloud) => {

	const aliasList = document.querySelector('.wpcloud-block-site-alias-list');
	const removeForm = document.querySelector('.wpcloud-block-site-alias-form-remove');
	const makePrimaryButtons = document.querySelectorAll('.wpcloud-block-site-alias-make-primary');
	const newAliasInput = document.querySelector('.wpcloud-block-site-alias-form-add input[name=site_alias]');

	function bindMakePrimaryButton(button) {
		if (!button) {
			return;
		}
		button.addEventListener('click', (e) => {
			e.preventDefault();
			const form = e.target.closest('form');
			updateAliasFormAction( form, 'site_alias_make_primary' );
			form.dispatchEvent(new Event('submit'));
		});
	}
	makePrimaryButtons.forEach(bindMakePrimaryButton);

	function updateAliasFormAction(form, action) {
	 	form.querySelector('input[name=wpcloud_action]').value = action;
	}

	function siteAliasAddResponse(result) {
		if (!result.success) {
			alert(result.message);
			return;
		}
		const newAlias = removeForm.cloneNode(true);
		newAlias.querySelector('.wpcloud-block-site-detail__value').textContent = result.site_alias;
		newAlias.querySelector('input[name=site_alias]').value = result.site_alias;
		newAlias.dataset.siteAlias = result.site_alias;
		newAlias.removeAttribute('style');
		aliasList.appendChild(newAlias);
		bindMakePrimaryButton( newAlias.querySelector('.wpcloud-block-site-alias-make-primary'));
		wpcloud.bindFormHandler(newAlias);
		newAliasInput.value = '';
	}

	function siteAliasRemoveResponse(result) {
		if (!result.success) {
			alert(result.message);
			return;
		}
		const removed = aliasList.querySelector(`[data-site-alias="${result.site_alias}"]`);
		removed.ontransitionend = () => {
			removed.remove();
		}
		removed.style.opacity = 0;
	}

	function siteAliasMakePrimaryResponse( result, form ) {
		updateAliasFormAction(form, 'site_alias_remove');
		if (!result.success) {
			alert(result.message);
			return;
		}
		const primary = aliasList.querySelector('.wpcloud-block-site-alias-list__item--primary');
		const primaryValue = primary.querySelector('.wpcloud-block-site-detail__value');
		console.log(form);
		const oldAliasValueInput = form.querySelector('.wpcloud-block-site-detail__value');
		const oldPrimary = primaryValue.textContent;
		const newPrimary = result.site_alias;

		const removeFormInput = form.querySelector('input[name=site_alias]');
		removeFormInput.value = oldPrimary;
		removeFormInput.name = 'site_alias';

		[[primaryValue, newPrimary], [oldAliasValueInput, oldPrimary]].forEach(val => {
			console.log(val);
			const [el, newText] = val;
			console.log(el);
			el.ontransitionend = () => {
				console.log('transitionend', el, newText)
				el.textContent = newText;
				el.classList.remove('wpcloud-hide');
				el.ontransitionend = null;
			}
		});

		primaryValue.classList.add('wpcloud-hide');
		oldAliasValueInput.classList.add('wpcloud-hide');





	}

	wpcloud.hooks.addAction('wpcloud_form_response', 'site_alias_add', ( result, form ) => {

		if (result?.action === 'site_alias_add') {
			return siteAliasAddResponse(result);
		}
		if (result?.action === 'site_alias_remove') {
			return siteAliasRemoveResponse(result);
		}
		if (result?.action === 'site_alias_make_primary') {
			return siteAliasMakePrimaryResponse( result, form );
		}
	});

	})(window.wpcloud);