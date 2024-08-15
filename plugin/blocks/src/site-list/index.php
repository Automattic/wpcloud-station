<?php
/**
 * WP Cloud Station Site List
 *
 * @package wpcloud
 */

add_filter(
	'wpcloud_site_list_query_args',
	function ( $query_args ) {

		if ( current_user_can( WPCLOUD_CAN_MANAGE_SITES ) ) {
			$owner_nicename = sanitize_text_field( wp_unslash( $_GET['owner'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $owner_nicename ) {
				$user_query = new WP_User_Query(
					array(
						'search'        => $owner_nicename,
						'search_fields' => array( 'user_nicename' ),
					)
				);

				$users = $user_query->get_results();
				$owner = array_shift( $users );

				if ( $owner ) {
					$query_args['author'] = $owner->ID;
				}
			}
		}

		return $query_args;
	}
);
