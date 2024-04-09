<?php
declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

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
					<?php print_users_select ($wpcloud_site ); ?>
					<?php print_form_input_row( 'Name', 'wpcloud_site[post_title]', $wpcloud_site->name, '.' . wpcloud_site_get_default_domain() ); ?>
					<?php print_form_select_row( 'PHP Version', 'wpcloud_site[meta_input][php_version]', WPCLOUD_PHP_VERSIONS, $wpcloud_site->php_version ); ?>
					<?php print_form_select_row( 'Datacenter', 'wpcloud_site[meta_input][data_center]', WPCLOUD_DATA_CENTERS, $wpcloud_site->data_center ); ?>
					</tbody>
				</table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Add Site">
		</p>

</div>
