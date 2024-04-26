<?php

function wpcloud_dashboard_get_assets_url( $url ) {
	return plugins_url( '/assets/' . $url, dirname( dirname( dirname( __FILE__ ) ) ) );
}

if ( ! function_exists( 'wpcloud_dashboard_list_site_card' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function wpcloud_dashboard_list_site_card( WPCLOUD_Site $wpcloud_site, WP_Post $site ) {
		$site_url = $wpcloud_site->domain;
		$site_name = $site->post_name;
		$view_url = get_the_permalink( $site );

		if ( empty( $site_name ) ) {
			$site_name = $site_url;
		}

		// @TODO get real site thumbnail
		$site_thumbnail = wpcloud_dashboard_get_assets_url( '/images/Gravatar_filled_' . $site->ID % 5 . '.png' );

		$ex_link = wpcloud_dashboard_get_assets_url( '/images/external-link.svg' );

		$site_card = <<<SITE
		<div class="site-card">
			<img src="$site_thumbnail" />
			<h2 class="site-title">
				<a target="_blank" href="$view_url">$site_name</a>
			</h2>
			<h3 class="site-url">
				<a href="https://$site_url" target="_blank"><span>$site_url</span><img src="$ex_link"/></a>
			</h3>
		</div>
		SITE;

		return $site_card; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

if ( ! function_exists( 'wpcloud_dashboard_performance_excerpt' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function wpcloud_dashboard_list_performance() {

		// if ( get_post_status() !== 'publish' ) {
		// 	echo '<span class="wpcloud-performance"><span>–</span></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		// 	return;
		// }

		// @TODO: get real performance data
		$mobile = rand( 80, 100 );
		$mobile_trend = rand( -1, 2 ) > 0 ? 'up' : 'down';
		$mobile_icon = wpcloud_dashboard_get_assets_url( 'images/phone_android_24px.svg' );
		$mobile_trend_icon = wpcloud_dashboard_get_assets_url( 'images/trending_' . $mobile_trend . '_24px.svg' );
		$desktop = rand( 80, 100 );
		$desktop_trend = rand( -1, 2 ) > 0 ? 'up' : 'down';
		$desktop_icon = wpcloud_dashboard_get_assets_url( 'images/laptop_24px.svg' );
		$desktop_trend_icon = wpcloud_dashboard_get_assets_url( 'images/trending_' . $desktop_trend . '_24px.svg' );

		$perf = <<<PERF
		<span class="wpcloud-performance">
			<span class="wpcloud-performance-mobile">
				<img src="$mobile_icon" alt="mobile-trends" />
				<span> $mobile </span>
				<img src="$mobile_trend_icon" alt="trending $mobile_trend"/>
			</span>
			<span class="wpcloud-performance-desktop">
				<img src="$desktop_icon" alt="desktop-trends" />
				<span> $desktop </span>
				<img src="$desktop_trend_icon" alt="trending $desktop_trend"/>
			</span>
		</span>

		PERF;

		return $perf; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

if ( ! function_exists( 'wpcloud_dashboard_list_owner' ) ) :
	function wpcloud_dashboard_list_owner( WPCLOUD_Site $wpcloud_site ) {
		$owner = get_user_by( 'id', $wpcloud_site->owner_id );
		$link  = get_edit_user_link( $wpcloud_site->owner_id );

		return '<span class="site-owner"><a href=" ' . $link . ' ">' . $owner->display_name . '</a></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

if ( ! function_exists( 'wpcloud_dashboard_wp_admin_button' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function wpcloud_dashboard_wp_admin_button( WPCLOUD_Site $wpcloud_site ) {
		$wp_icon = wpcloud_dashboard_get_assets_url( '/images/wordpress.svg' );
		$ex_link = wpcloud_dashboard_get_assets_url( '/images/external-link.svg' );
		return '<a class="wpcloud-list-wpadmin-button" target="_blank" href="https://' . $wpcloud_site->domain . '/wp-admin" class="button"><img src="'. $wp_icon . '" /><span>WP Admin</span><img src="' . $ex_link . '"/></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

if ( ! function_exists( 'wpcloud_dashboard_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function wpcloud_dashboard_list_created_on( WP_Post $post ) {
		$time_string = '<time class="entry-date created" datetime="%1$s">%2$s</time>';
		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C, $post ) ),
			esc_html( get_the_date('Y/m/d', $post) ),
		);

		return '<span class="posted-on">' . $time_string . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;
