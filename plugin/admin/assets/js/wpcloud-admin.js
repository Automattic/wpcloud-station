
(() => {

	wp.domReady(() => {
		window.location.href.includes('nav-menus.php') && initMenuImageUpload();
	})

	function initMenuImageUpload() {
		// handle menu item image upload
		const openMediaButtons = document.querySelectorAll('.wpcloud-menu-image-upload');
		const useAvatarCb = document.querySelectorAll('.wpcloud-menu-image-input--avatar');
		const settingsRadios = document.querySelectorAll('.wpcloud-menu-item-setting');
		const itemList = document.getElementById('menu-to-edit');
		let frame;

		openMediaButtons.forEach( btn => btn.addEventListener('click', e =>  openMedia(btn.dataset.itemId)));
		useAvatarCb.forEach(cb => cb.addEventListener('change', e => toggleUseAvatar(cb, cb.dataset.itemId)));
		settingsRadios.forEach(radio => radio.addEventListener('change', e => settingChange(radio, radio.dataset.itemId)));

		itemList.addEventListener('DOMNodeInserted', (e) => {
			const id = e.target?.id?.split('-').pop();
			if ( ! id ) {
				return;
			}
			const button = e.target.querySelector(`[data-item-id="${id}"].wpcloud-menu-image-upload`);
			button?.addEventListener('click', e => openMedia(id));

			const avatarCb = e.target.querySelector(`[data-item-id="${id}"].wpcloud-menu-image-input--avatar`);
			avatarCb?.addEventListener('change', e => toggleUseAvatar(avatarCb, id));

			const settingsRadios = e.target.querySelectorAll(`[data-item-id="${id}"].wpcloud-menu-item-setting`);
			settingsRadios.forEach(radio => radio.addEventListener('change', e => settingChange(e.target, id)));
		});

		const settingChange = (setting, itemId) => {
			const value = setting.value;
			const container = document.getElementById('wpcloud-menu-image-' + itemId);
			const preview = container.querySelector('.wpcloud-menu-image-preview-row');
			const linkPlaceholder = preview.querySelector('span');
			console.log(linkPlaceholder)
			switch (value) {
				case 'before':
					preview.style.flexDirection = 'row';
					linkPlaceholder.style.display = 'inline-block';
					break;
				case 'after':
					preview.style.flexDirection = 'row-reverse';
					linkPlaceholder.style.display = 'inline-block';
					break;
				case 'replace':
					linkPlaceholder.style.display = 'none';
					break;
			}
		}

		const setPreviewUrl = (itemId, url) => {
			const container = document.getElementById('wpcloud-menu-image-' + itemId);
			const preview = container.querySelector('.wpcloud-menu-image-preview');
			const img = preview.querySelector('img');

			if (! url) {
				preview.classList.add('hidden');
				img.src = '';
				return;
			}

			'avatar' === url ? img.src = img.dataset.avatarPlaceholder : img.src = url;
			preview.classList.remove('hidden');
		}

		const showSettings = (itemId) => {
			const container = document.getElementById('wpcloud-menu-image-' + itemId);
			const settings = container.querySelector('.wpcloud-menu-image-settings');
			console.log('show settings', itemId, settings);

			if ( ! settings.classList.contains('hidden') ) {
				return;
			}
			settings.classList.remove('hidden');
			const isChecked = settings.querySelector(':checked');
			if (! isChecked) {
				settings.querySelector('.wpcloud-menu-item-setting--before').checked = true;
			}
		}

		const hideSettings = (itemId) => {
			const container = document.getElementById('wpcloud-menu-image-' + itemId);
			const settings = container.querySelector('.wpcloud-menu-image-settings');
			settings.classList.add('hidden');
		}

		const clearImage = (itemId) => {
			const container = document.getElementById('wpcloud-menu-image-' + itemId);
			const input = container.querySelector('.wpcloud-menu-image-input');

			input.value = '';

			hideSettings(itemId);
			setPreviewUrl(itemId,'');
		}

		const toggleUseAvatar = (avatarButton, itemId) => {
			if ( ! avatarButton.checked ) {
				clearImage(itemId);
				setPreviewUrl(itemId, '');
				return;
			}
			setPreviewUrl(itemId, 'avatar');
			showSettings(itemId);
		}


		const openMedia = (itemId) => {
			console.log('open media', itemId);
			if (!itemId) {
				return;
			}

			const container = document.getElementById('wpcloud-menu-image-' + itemId);
			const input = container.querySelector('.wpcloud-menu-image-input');

			frame = wp.media({
				title: 'Select or upload menu item image',
				button: {
					text: 'Use this image'
				},
				library: {
					type: 'image'
				},
				multiple: false
			});

			frame.on('select', () => {
				const attachment = frame.state().get('selection').first().toJSON();
				input.value = attachment.url;

				setPreviewUrl(itemId, attachment.url);
				showSettings(itemId);
				container.querySelector('.wpcloud-menu-image-input--avatar').checked = false;
			});

			frame.open();
		}
	};
} )();