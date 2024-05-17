<?php
declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

wp_enqueue_script( 'user-profile' );

function print_users_select( WPCloud_Site $wpcloud_site, array $filter = array() ): void {
	$users = array_reduce( get_users($filter) , function ( $users, $user ) {
		$users[$user->ID] = $user->display_name;
		return $users;
	}, array() );
	print_form_select_row( 'Owner', 'owner_id', $users, $wpcloud_site->owner_id );
}

function print_form_input_row( string $label, string $name, ?string $value, string $description = '' ): void {
	?>
	<tr class="wpcloud_row">
		<th scope="row">
			<label for="<?php echo "$name" ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		</th>
		<td>
			<input type="text" id="<?php echo "$name" ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"><span class="description"><?php echo $description ?></span>
		</td>
	</tr>
	<?php
}

function print_form_select_row( string $label, string $name, array $options, mixed $value ): void {
	?>
	<tr class="wpcloud_row">
		<th scope="row">
			<label for="<?php echo "$name" ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		</th>
		<td>
			<select id="<?php echo "$name" ?>" name="<?php echo esc_attr( $name ); ?>">
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
			wp_nonce_field( 'wpcloud-admin-site-form' );
			?>
			<table class="form-table" role="presentation">
				<tbody>
					<input type="hidden" name="action" value="<?php echo $wpcloud_site->id ? 'edit' : 'create' ?>">
					<input type="hidden" name="wpcloud_site[id]" value="<?php echo esc_attr( $wpcloud_site->id ); ?>" >
					<input type="hidden" name="wpcloud_site[post_type]" value="wpcloud_site" >
					<input type="hidden" name="wpcloud_site[status]" value="draft" >
					<?php print_form_input_row( 'Name', 'wpcloud_site[post_title]', $wpcloud_site->name ); ?>
					<?php print_users_select ($wpcloud_site ); ?>
					<tr class="form-field form-required user-pass1-wrap">
						<th scope="row">
							<label for="pass1">
								<?php _e( 'Password' ); ?>
								<span class="description hide-if-js"><?php _e( '(required)' ); ?></span>
							</label>
						</th>
						<td>
							<input type="hidden" value=" " /><!-- #24364 workaround -->
							<button type="button" class="button wp-generate-pw hide-if-no-js"><?php _e( 'Generate password' ); ?></button>
							<div class="wp-pwd">
								<?php $initial_password = wp_generate_password( 24 ); ?>
								<div class="password-input-wrapper">
									<input type="password" name="pass1" id="pass1" class="regular-text" autocomplete="new-password" spellcheck="false" data-reveal="1" data-pw="<?php echo esc_attr( $initial_password ); ?>" aria-describedby="pass-strength-result" />
									<div style="display:none" id="pass-strength-result" aria-live="polite"></div>
								</div>
								<button type="button" class="button wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Hide password' ); ?>">
									<span class="dashicons dashicons-hidden" aria-hidden="true"></span>
									<span class="text"><?php _e( 'Hide' ); ?></span>
								</button>
							</div>
						</td>
					</tr>
					<tr class="pw-weak">
						<th><?php _e( 'Confirm Password' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="pw_weak" class="pw-checkbox" />
								<?php _e( 'Confirm use of weak password' ); ?>
							</label>
						</td>
					</tr>
					<?php print_form_select_row( 'PHP Version', 'wpcloud_site[meta_input][php_version]', wpcloud_client_php_versions_available( true ), $wpcloud_site->php_version ); ?>
					<?php print_form_select_row( 'Datacenter', 'wpcloud_site[meta_input][data_center]', wpcloud_client_data_centers_available( true ), $wpcloud_site->data_center ); ?>
					</tbody>
				</table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Add Site">
		</p>

</div>
