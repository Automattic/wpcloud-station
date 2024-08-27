
(function (wp, wpcloud ) {
	if ( !wp || !wp.data || !wpcloud ) {
		return;
	}
	if (!wpcloud.apiConnected) {
		wp.data.dispatch('core/notices').createNotice(
			'warning',
			'You are not connected to the WP Cloud API. Station requires a valid API connection to function properly.',
			{
				isDismissible: false,
				actions: [
					{
						url: '/wp-admin/admin.php?page=wpcloud_admin_settings',
						label: 'WP Cloud Settings',
					},
				],
			}
		);
	}
} )( window.wp, window.wpcloud );