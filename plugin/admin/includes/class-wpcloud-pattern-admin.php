<?php
/**
 * WP Cloud Pattern Admin
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

/**
 * Class for managing patterns in the admin.
 */
class WPCloud_Pattern_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_patterns_page' ) );
		add_action( 'admin_post_wpcloud_update_patterns', array( $this, 'handle_update_patterns' ) );
	}

	/**
	 * Add the patterns page to the admin menu.
	 */
	public function add_patterns_page() {
		add_submenu_page(
			'wpcloud',
			__( 'Patterns', 'wpcloud' ),
			__( 'Patterns', 'wpcloud' ),
			'manage_options',
			'wpcloud_patterns',
			array( $this, 'render_patterns_page' )
		);
	}

	/**
	 * Render the patterns page.
	 */
	public function render_patterns_page() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Get all patterns from the database.
		$patterns = $this->get_patterns();

		// Get all pattern files from the patterns directory.
		$pattern_files = $this->get_pattern_files();

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<div class="notice notice-info">
				<p><?php esc_html_e( 'This page allows you to manage patterns in your WordPress site. You can update patterns from JSON files in the plugin/patterns directory.', 'wpcloud' ); ?></p>
			</div>

			<h2><?php esc_html_e( 'Update Patterns', 'wpcloud' ); ?></h2>
			<p><?php esc_html_e( 'Click the button below to update patterns from JSON files in the plugin/patterns directory.', 'wpcloud' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'wpcloud_update_patterns', 'wpcloud_patterns_nonce' ); ?>
				<input type="hidden" name="action" value="wpcloud_update_patterns">
				<p>
					<label>
						<input type="checkbox" name="force_update" value="1">
						<?php esc_html_e( 'Force update all patterns, even if they haven\'t changed.', 'wpcloud' ); ?>
					</label>
				</p>
				<?php submit_button( __( 'Update Patterns', 'wpcloud' ), 'primary', 'submit', false ); ?>
			</form>

			<h2><?php esc_html_e( 'Pattern Files', 'wpcloud' ); ?></h2>
			<?php if ( empty( $pattern_files ) ) : ?>
				<p><?php esc_html_e( 'No pattern files found in the plugin/patterns directory.', 'wpcloud' ); ?></p>
			<?php else : ?>
				<p><?php echo esc_html( sprintf( __( 'Found %d pattern files in the plugin/patterns directory:', 'wpcloud' ), count( $pattern_files ) ) ); ?></p>
				<ul>
					<?php foreach ( $pattern_files as $pattern_file ) : ?>
						<li><?php echo esc_html( $pattern_file ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<h2><?php esc_html_e( 'Registered Patterns', 'wpcloud' ); ?></h2>
			<?php if ( empty( $patterns ) ) : ?>
				<p><?php esc_html_e( 'No patterns found in the database.', 'wpcloud' ); ?></p>
			<?php else : ?>
				<p><?php echo esc_html( sprintf( __( 'Found %d patterns in the database:', 'wpcloud' ), count( $patterns ) ) ); ?></p>
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'ID', 'wpcloud' ); ?></th>
							<th><?php esc_html_e( 'Title', 'wpcloud' ); ?></th>
							<th><?php esc_html_e( 'Slug', 'wpcloud' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'wpcloud' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $patterns as $pattern ) : ?>
							<tr>
								<td><?php echo esc_html( $pattern->ID ); ?></td>
								<td><?php echo esc_html( $pattern->post_title ); ?></td>
								<td><?php echo esc_html( $pattern->post_name ); ?></td>
								<td>
									<a href="<?php echo esc_url( get_edit_post_link( $pattern->ID ) ); ?>"><?php esc_html_e( 'Edit', 'wpcloud' ); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Handle the update patterns form submission.
	 */
	public function handle_update_patterns() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wpcloud' ) );
		}

		// Verify nonce.
		if ( ! isset( $_POST['wpcloud_patterns_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpcloud_patterns_nonce'] ) ), 'wpcloud_update_patterns' ) ) {
			wp_die( esc_html__( 'Invalid nonce.', 'wpcloud' ) );
		}

		// Get the force update option.
		$force = isset( $_POST['force_update'] ) && '1' === $_POST['force_update'];

		// Load the pattern updater.
		require_once plugin_dir_path( dirname( __DIR__ ) ) . 'includes/class-wpcloud-pattern-updater.php';
		$updater = new WPCloud_Pattern_Updater();

		// Update patterns.
		$results = $updater->update_patterns( $force );

		// Set up admin notices.
		if ( ! empty( $results['updated'] ) ) {
			$message = sprintf(
				/* translators: %d: Number of patterns updated. */
				_n(
					'Updated %d pattern.',
					'Updated %d patterns.',
					count( $results['updated'] ),
					'wpcloud'
				),
				count( $results['updated'] )
			);
			add_settings_error( 'wpcloud_patterns', 'wpcloud_patterns_updated', $message, 'success' );
		}

		if ( ! empty( $results['created'] ) ) {
			$message = sprintf(
				/* translators: %d: Number of patterns created. */
				_n(
					'Created %d pattern.',
					'Created %d patterns.',
					count( $results['created'] ),
					'wpcloud'
				),
				count( $results['created'] )
			);
			add_settings_error( 'wpcloud_patterns', 'wpcloud_patterns_created', $message, 'success' );
		}

		if ( ! empty( $results['skipped'] ) ) {
			$message = sprintf(
				/* translators: %d: Number of patterns skipped. */
				_n(
					'Skipped %d pattern (no changes).',
					'Skipped %d patterns (no changes).',
					count( $results['skipped'] ),
					'wpcloud'
				),
				count( $results['skipped'] )
			);
			add_settings_error( 'wpcloud_patterns', 'wpcloud_patterns_skipped', $message, 'info' );
		}

		if ( ! empty( $results['errors'] ) ) {
			$message = sprintf(
				/* translators: %d: Number of errors. */
				_n(
					'Encountered %d error.',
					'Encountered %d errors.',
					count( $results['errors'] ),
					'wpcloud'
				),
				count( $results['errors'] )
			);
			add_settings_error( 'wpcloud_patterns', 'wpcloud_patterns_errors', $message, 'error' );
		}

		// Redirect back to the patterns page.
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => 'wpcloud_patterns',
					'updated' => '1',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Get all patterns from the database.
	 *
	 * @return array Array of pattern posts.
	 */
	private function get_patterns() {
		$args = array(
			'post_type'      => 'wp_block',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		$query = new WP_Query( $args );

		return $query->posts;
	}

	/**
	 * Get all pattern files from the patterns directory.
	 *
	 * @return array Array of pattern filenames.
	 */
	private function get_pattern_files() {
		$patterns_dir = plugin_dir_path( dirname( __DIR__ ) ) . 'patterns';

		// Check if patterns directory exists.
		if ( ! file_exists( $patterns_dir ) || ! is_dir( $patterns_dir ) ) {
			return array();
		}

		// Get all JSON files in the patterns directory.
		$pattern_files = glob( $patterns_dir . '/*.json' );
		if ( empty( $pattern_files ) ) {
			return array();
		}

		// Extract filenames.
		$filenames = array();
		foreach ( $pattern_files as $pattern_file ) {
			$filenames[] = basename( $pattern_file );
		}

		return $filenames;
	}
}
