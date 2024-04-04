<?php
/**
 * WP Cloud Site .
 *
 * @package wpcloud-dashboard
 */
declare( strict_types = 1 );

require_once  plugin_dir_path( __FILE__ ) . 'wpcloud-client.php';

class WPCLOUD_Site {

	private static $initial_status = 'draft';

	public $id;
	public $name;
	public $php_version;
	public $data_center;
	public $status;
	public $owner_id;
	public $domain;

	public function __construct(string $domain = '') {
		$this->id = 0;
		$this->name = '';
		$this->php_version = '';
		$this->data_center = '';
		$this->status = '';
		$this->owner_id = 0;

		if ( ! $domain ) {
			$this->domain = self::get_default_domain();
		} else {
			$this->domain = $domain;
		}
	}

	public static function get_default_domain(string $sub_domain = ''): string {
		$settings = get_option( 'wpcloud_settings' );
		$domain = $settings['wpcloud_domain'] ?? '';
		if ( $sub_domain && $domain ) {
			$domain = $sub_domain . '.' . $domain;
		}
		return $domain;
	}

	public static function register_post_type(): void {
			register_post_type( 'wpcloud_site',
				array(
				'labels'	=> array(
					'name'			=> __( 'Sites', 'wpcloud' ),
					'singular_name'	=> __( 'Site', 'wpcloud' ),
					'add_new' => 'Add New Site',
				),
				'public'	=> true,
				'has_archive' => true,
				'show_in_rest' => true,
				'show_in_ ui' => false,
				'show_in_menu' => false,
				'capabilities' => array(
					'wpcloud_add_site' => true,
				),
			)
		);
	}

	/**
	 * Create a new WPCLOUD_Site from a WP_Post object.
	 * @param WP_Post $post
	 * @return WPCLOUD_Site
	 */
	public static function from_post( WP_Post $post ): self {
		$site = new self();
		$site->id = $post->ID;
		$site->name = $post->post_title;
		$site->php_version = get_post_meta( $post->ID, 'php_version', true );
		$site->data_center = get_post_meta( $post->ID, 'data_center', true );
		$site->status = $post->post_status;
		$site->owner_id = $post->post_author;
		return $site;
	}

	/**
	 * Create a new WPCLOUD_Site from WP Cloud.
	 * @param string $id
	 * @return WPCLOUD_Site
	 */
	public static function from_client(string $id): self {
		//@TODO: call the wpcloud API to get the site details.
		return new self();
	}

	public static function create(string $name, string $php_version, string $data_center, ?string $owner_id): mixed {
		// Set up the site info
		$status = apply_filters( WPCLOUD_INITIAL_SITE_STATUS, self::$initial_status );
		$post_details = array(
			'post_type' => 'wpcloud_site',
			'post_title' => $name,
			'post_status' => $status,
			'comment_status'=> 'closed',
		);

		// Check if the user is allowed to create a site.
		if ( !$owner_id) {
			$owner_id = get_current_user_id();
		} else {
			$post_details['post_author'] = $owner_id;
		}

		$should_create = apply_filters( WPCLOUD_SHOULD_CREATE_SITE, true, $owner_id );
		if ( !$should_create ) {
			return new WP_Error( 'forbidden', __( 'Site creation is disabled.' ) );
		}

		// @TODO validate name for
		$pattern = "/^(?!-)(?!.*--)[A-Za-z0-9-]{1,63}(?<!-)$/";
		if ( ! preg_match( $pattern, $name ) ) {
			return new WP_Error( 'forbidden', __( 'Invalid site name.' ) );
		}

		// Create the site CPT and set the meta data.
		$site_id = wp_insert_post( $post_details );
		if ( is_wp_error( $site_id ) ) {
			return $site_id;
		}

		//We only really need these for the initial creation so we can show the user what they just created.
		update_post_meta( $site_id, 'php_version', $php_version );
		update_post_meta( $site_id, 'data_center', $data_center );

		$domain = self::get_default_domain( $name );
		$admin =  get_user_by( 'id', $owner_id );

		$data = array(
			'php_version' => $php_version,
			'geo_affinity' => $data_center,
		);

		$result = wpcloud_client_site_create(
			domain: $domain,
			admin_user: $admin->user_login,
			admin_email: $admin->user_email,
			data: $data
		);

		if ( is_wp_error( $result ) ) {
			wp_delete_post( $site_id );
			return $result;
		}

		do_action( WPCLOUD_ACTION_SITE_CREATED, $site_id, $owner_id, 'wp-admin' );

		return self::from_post( get_post( $site_id ) );
	}

	public static function find_all(?string $user_id, array $query = array() ): array {
		if ( ! $user_id && ! current_user_can( WPCLOUD_CAN_MANAGE_SITES ) ) {
			throw new Exception( 'Unauthorized to view all sites.');
		}

		$defaults = array(
			'post_type' => 'wpcloud_site',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		);

		$query = wp_parse_args( $query, $defaults );

		if ( $user_id ) {
			$query['author'] = $user_id;
		} else

		$results = new WP_Query( $query );

		return array_map( self::class . '::from_post', $results->posts );
	}
}