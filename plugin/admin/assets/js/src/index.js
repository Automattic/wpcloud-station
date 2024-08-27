
(function ( { wp, wpcloud } ) {
	if (!wp || !wpcloud ) {
		return;
	}
	if (!wpcloud.apiConnected) {
		wp.data.dispatch('core/notices').createNotice(
			'error',
			'You are not connected to the WP Cloud API. Station requires a valid API connection to function properly.', // Text string to display.
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
})( window );
