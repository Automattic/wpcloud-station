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
	 * Setup the site .
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
			$this->log( 'Setting up the site name: ' . $site_name );
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

		// Create add site page.
		$core_pages = array(
			'add-site' => array(
				'post_title'    => 'Add Site',
				'post_content'  => '<!-- wp:pattern {"slug":"wpcloud-station/form-add-site"} /-->',
				'post_category' => array( $wpcloud_core_cat->term_id, get_category_by_slug( WPCLOUD_CATEGORY_PRIVATE )->term_id ),
			),
		);
		$query      = new WP_Query(
			array(
				'cat'           => $wpcloud_core_cat->term_id,
				'post_type'     => 'any',
				'post_name__in' => array_keys( $core_pages ),
			)
		);
		$post_names = array_map( fn( $post ) => $post->post_name, $query->get_posts() );

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
		$as_lower = $this->get_persistent_data( strtolower( $name ) );
		if ( $as_lower ) {
			return $as_lower;
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
		if ( ! $this->apd ) {
			$this->apd = new Atomic_Persistent_Data();
		}

		if ( ! $key ) {
			return $this->apd;
		}

		if ( ! isset( $this->apd->$key ) ) {
			return '';
		}

		// Try parsing any json.
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
