<?php
/**
 * WP Cloud Station
 *
 * @package wpcloud-client
 */

declare( strict_types = 1 );

/**
 * WP Cloud Station
 */
class WPCloud_Station {

	/**
	 * The persistent data.
	 *
	 * @var Atomic_Persistent_Data
	 */
	protected $apd;

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( class_exists( 'Atomic_Persistent_Data' ) ) {
			$this->apd = new Atomic_Persistent_Data();
		}
	}

	/**
	 * Setup the site.
	 *
	 * @param array $options The options.
	 *
	 * @return array
	 */
	public function setup( array $options ): array {
		$errors = array();

		// Setup the site name.
		$site_name = $options['site-name'] ?? $this->wp_cloud_client_name;
		if ( $site_name ) {
			update_option( 'blogname', $site_name );
		}

		// Setup the site logo.
		$attachment_id = $this->add_logo_attachment( $options['site-logo'] ?? '' );
		if ( is_wp_error( $attachment_id ) ) {
			$errors[] = $attachment_id;
		}
		set_theme_mod( 'custom_logo', $attachment_id );

		// Add the core category.
		$wpcloud_core_cat = get_category_by_slug( WPCLOUD_CATEGORY_CORE );
		if ( ! $wpcloud_core_cat ) {
			wpcloud_setup_categories();
		}

		// Create add site pages.
		$core_pages = array(
			'add-site'              => array(
				'post_title'    => 'Add Site',
				'post_content'  => '<!-- wp:pattern {"slug":"wpcloud-station/form-add-site"} /-->',
				'post_category' => array( $wpcloud_core_cat->term_id, get_category_by_slug( WPCLOUD_CATEGORY_PRIVATE )->term_id ),
			),

			'performance-dashboard' => array(
				'post_title'    => 'Performance Dashboard',
				'post_content'  => '<!-- wp:heading {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"0","right":"0"},"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<h2 class="wp-block-heading" style="margin-top:var(--wp--preset--spacing--20);margin-right:0;margin-bottom:var(--wp--preset--spacing--20);margin-left:0;padding-right:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">Performance Dashboard</h2>
<!-- /wp:heading --><!-- wp:pattern {"slug":"wpcloud-station/performance-dashboard"} /-->',
				'post_category' => array( $wpcloud_core_cat->term_id, get_category_by_slug( WPCLOUD_CATEGORY_PRIVATE )->term_id ),
				'page_template' => 'page-wide.html',
			),
		);
		$query          = new WP_Query(
			array(
				'cat'           => $wpcloud_core_cat->term_id,
				'post_type'     => 'any',
				'post_name__in' => array_keys( $core_pages ),
			)
		);
		$existing_posts = $query->get_posts();
		$post_names     = array_map( fn( $post ) => $post->post_name, $existing_posts );

		// First, update any existing pages with new templates.
		foreach ( $existing_posts as $post ) {
			$page_name = $post->post_name;
			if ( isset( $core_pages[ $page_name ]['page_template'] ) ) {
				update_post_meta( $post->ID, '_wp_page_template', $core_pages[ $page_name ]['page_template'] );
			}
		}

		// Then create any missing pages.
		foreach ( $core_pages as $page_name => $args ) {
			if ( ! in_array( $page_name, $post_names, true ) ) {
				$args    = wp_parse_args(
					$args,
					array(
						'post_title'  => $page_name,
						'post_status' => 'publish',
						'post_type'   => 'page',
					)
				);
				$page_id = wp_insert_post( $args );
				if ( is_wp_error( $page_id ) ) {
					$errors[] = $page_id;
				} elseif ( isset( $args['page_template'] ) ) {
					// Set page template if specified.
					update_post_meta( $page_id, '_wp_page_template', $args['page_template'] );
				}
			}
		}

		// Configure permalinks.
		global $wp_rewrite;
		$permalink_structure = '/%postname%/';
		update_option( 'permalink_structure', $permalink_structure );
		$wp_rewrite->set_permalink_structure( $permalink_structure );
		flush_rewrite_rules();

		return $errors;
	}

	/**
	 * Add getter to fetch persistent data.
	 *
	 * @param string $name The name.
	 * @return mixed The persistent data.
	 */
	public function __get( string $name ): mixed {
		$as_upper = $this->get_persistent_data( strtoupper( $name ) );
		if ( $as_upper ) {
			return $as_upper;
		}
		return $this->get_persistent_data( $name );
	}

	/**
	 *  Get the persistent data.
	 *
	 * @param string $key The key.
	 * @return mixed The persistent data or the value if a key is provided and the key exists.
	 */
	public function get_persistent_data( string $key = '' ): mixed {
		if ( ! class_exists( 'Atomic_Persistent_Data' ) ) {
			return '';
		}

		if ( ! $key ) {
			return $this->apd;
		}

		$value = $this->apd->$key;
		if ( ! is_string( $value ) ) {
			return '';
		}

		// Try parsing any JSON.
		$value = json_decode( $this->apd->$key, true );
		if ( json_last_error() === JSON_ERROR_NONE ) {
			return $value;
		}
		return $this->apd->$key;
	}

	/**
	 * Set the site logo.
	 *
	 * @param string $image_url The image URL.
	 * @return WP_Error|int
	 */
	private function add_logo_attachment( string $image_url ): WP_Error|int {
		if ( $image_url ) {
			// Try downloading the logo.
			return media_sideload_image( $image_url, 0, null, 'id' );
		}
		$logo_path        = plugin_dir_path( __DIR__ ) . 'assets/images/wpcloud.svg';
		$upload_dir       = wp_upload_dir();
		$logo_path_upload = trailingslashit( $upload_dir['basedir'] ) . 'wpcloud.svg';
		copy( $logo_path, $logo_path_upload );

		$attachment = array(
			'post_mime_type' => 'image/svg+xml',
			'post_title'     => 'WP Cloud Logo',
			'post_content'   => ' ',
			'post_status'    => 'inherit',
		);

		$attachment_id = wp_insert_attachment( $attachment, $logo_path_upload, 0 );
		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}
		return $attachment_id;
	}
}
