<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="wrap">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<form action="options.php" method="post">
						<?php
						settings_fields( 'wpcloud' );
						do_settings_sections( 'wpcloud' );
						submit_button( 'Save Settings' );
						?>
				</form>
		</div>