<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function print_form_input_row( $label, $name, $value ) {
	?>
	<tr class="wpcloud_row">
		<th scope="row">
			<label for="wpcloud_api_key">
				<?php echo esc_html( $label ); ?>
			</label>
		</th>
		<td>
			<input type="text" id="wpcloud_api_key" name="wpcloud_site[<?php echo esc_attr( $name ); ?>]" value="<?php echo esc_attr( $value ); ?>">
		</td>
	</tr>
	<?php
}

function print_form_select_row($label, $name, $options, $value) {
	?>
	<tr class="wpcloud_row">
		<th scope="row">
			<label for="wpcloud_api_key">
				<?php echo esc_html( $label ); ?>
			</label>
		</th>
		<td>
			<select id="wpcloud_api_key" name="wpcloud_site[<?php echo esc_attr( $name ); ?>]">
				<?php
				foreach ( $options as $option_value => $option_label ) {
					?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $option_value, $value ); ?>>
						<?php echo esc_html( $option_label ); ?>
					</option>
					<?php
				}
				?>
			</select>
		</td>
	</tr>
	<?php
}

?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="<?php echo menu_page_url( 'wpcloud', false ) ; ?>" method="post">
			<?php
			wp_nonce_field( 'wpcloud-edit-site');
			?>
			<table class="form-table" role="presentation">
				<tbody>
					<input type="hidden" name="wpcloud_site[id]" value="<?php echo esc_attr( $wpcloud_site['id'] ); ?>" >
					<?php print_form_input_row( 'Site Name', 'site_name', $wpcloud_site['site_name'] ); ?>
					<?php print_form_select_row( 'PHP Version', 'php_version', WP_CLOUD_PHP_VERSIONS, $wpcloud_site['php_version'] ); ?>
					<?php print_form_select_row( 'Data Center', 'data_center', WP_CLOUD_DATA_CENTERS, $wpcloud_site['php_version'] ); ?>
					</tbody>
				</table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Add Site">
		</p>

</div>