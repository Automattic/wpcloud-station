<?php

function wpcloud_nav_menu_item_custom_fields( $item_id ) {
	$image_url = get_post_meta( $item_id, 'wpcloud_menu_item_image', true );
	$setting = get_post_meta( $item_id, 'wpcloud_menu_item_image_setting', true ) ?? 'before';
	$is_before = $setting === 'before' ? 'checked' : '';
	$is_after = $setting === 'after' ? 'checked' : '';
	$is_replace = $setting === 'replace' ? 'checked' : '';
	$is_avatar = $image_url === 'avatar' ? 'checked' : '';
	$button_text = $image_url ? __( 'Replace Image', 'wpcloud' ) : __( 'Add Image', 'wpcloud' );

	$avatar_placeholder = plugin_dir_url( __DIR__ ) . 'admin/assets/img/gravatar.png';
	$preview_url = $is_avatar ? $avatar_placeholder : $image_url;
	?>
	<div style="clear: both; display:flex; flex-direction: column;" class="wpcloud-menu-image" id="wpcloud-menu-image-<?php echo $item_id ?>">
	<label> <?php _e( 'Menu Image', 'wpcloud' ); ?> </label>
		<input type="hidden"  class="wpcloud-menu-image-input" name="menu_item_image[<?php echo $item_id ?>]" value="<?php echo $image_url ;?>" />
		<div style="padding-top: 10px">
	    <button type="button" class="wpcloud-menu-image-upload" data-item-id="<?php echo $item_id ?>" ><?php echo $button_text ?></button>
			<label style="padding-left: 10px" >
				<input class="wpcloud-menu-image-input--avatar" type="checkbox" name="menu-item-image-avatar[<?php echo $item_id ?>]" value="avatar" <?php echo $is_avatar ?> data-item-id="<?php echo $item_id ?>" />
				<span><?php _e( 'Use Profile Image', 'wpcloud' ); ?></span>
			</label>
		</div>
		<div class="wpcloud-menu-image-preview <?php echo $image_url ? '' : 'hidden'; ?> " >
				<p><?php _e( 'Image Preview', 'wpcloud' ); ?></p>
				<div class="wpcloud-menu-image-preview-row" style="display: flex; gap: 5px; justify-content: left;">
					<img src="<?php echo $preview_url; ?>" data-avatar-placeholder="<?php echo $avatar_placeholder ?>" width="32" height="32">
					<span style="display:inline-block; background: #8080806b; border-radius: 2px; height: 32px; width: 180px;"></span>
				</div>
		</div>
		<div class="wpcloud-menu-image-settings <?php echo $image_url ? '' : 'hidden' ?> " style="padding-bottom: 10px; padding-top:10px;">
				<label>
					<input class="wpcloud-menu-item-setting wpcloud-menu-item-setting--before" data-item-id="<?php echo $item_id ?>" type="radio" name="menu-item-image-setting[<?php echo $item_id; ?>]" value="before" <?php echo $is_before ?> />
					<span><?php _e( 'Before', 'wpcloud' ); ?></span>
				</label>
				<label>
					<input class="wpcloud-menu-item-setting wpcloud-menu-item-setting--after" data-item-id="<?php echo $item_id ?>" type="radio" name="menu-item-image-setting[<?php echo $item_id; ?>]" value="after" <?php echo $is_after ?> />
					<span><?php _e( 'After', 'wpcloud' ); ?></span>
				</label>
				<label>
					<input class="wpcloud-menu-item-setting wpcloud-menu-item-setting--replace" data-item-id="<?php echo $item_id ?>" type="radio" name="menu-item-image-setting[<?php echo $item_id; ?>]" value="replace" <? echo $is_replace ?> />
					<span><?php _e( 'Image only', 'wpcloud' ); ?></span>
				</label>
				<br />
				<br />
				<a href="#" class="wpcloud-menu-image-delete" class="button"><?php _e( 'Delete menu image', 'wpcloud' ); ?></a>
			</div>
		</div>
	<?php
	wp_enqueue_media();
}

add_action( 'wp_nav_menu_item_custom_fields', 'wpcloud_nav_menu_item_custom_fields', 10, 1);

add_action( 'wp_update_nav_menu_item', 'wpcloud_update_nav_menu_item', 10, 2 );
function wpcloud_update_nav_menu_item( $menu_id, $menu_item_db_id ) {

		$menu_image = sanitize_text_field($_POST['menu_item_image'][$menu_item_db_id] ?? '');
		$use_avatar = sanitize_text_field($_POST['menu-item-image-avatar'][$menu_item_db_id] ?? null );
		$image_setting = sanitize_text_field($_POST['menu-item-image-setting'][$menu_item_db_id] ?? 'before');
		if ( $menu_image || $use_avatar ) {
			update_post_meta( $menu_item_db_id, 'wpcloud_menu_item_image', $menu_image ?? 'avatar' );
			update_post_meta( $menu_item_db_id, 'wpcloud_menu_item_image_setting', $image_setting );
		}
}

add_action( 'nav_menu_css_class', 'wpcloud_add_custom_class', 10, 2 );
function wpcloud_add_custom_class( $classes, $item ) {
	$image_setting = get_post_meta( $item->ID, 'wpcloud_menu_item_image_setting', true );
	$classes[] = 'wpcloud-menu-img';
	switch ( $image_setting ) {
		case 'before':
		case 'after':
			$classes[] = 'wpcloud-menu-img-after';
			break;
		case 'replace':
			$classes[] = 'wpcloud-menu-img-replace';
			break;
		default:
			$classes[] = 'wpcloud-menu-img-before';
			break;
	}
	return $classes;
}

function wpcloud_nav_menu_add_image($title, $item ) {
	$image_url = get_post_meta( $item->ID, 'wpcloud_menu_item_image', true );
	$image_setting = get_post_meta( $item->ID, 'wpcloud_menu_item_image_setting', true );

	if ( ! $image_url ) {
		return $title;
	}

	if ( 'avatar' === $image_url ) {
		$image_url = get_avatar_url( get_current_user_id(), [ 'size' => 32 ] );
	}

	$img = '<img src="' . $image_url . '" alt="' . $item->title . '" />';

	switch ( $image_setting ) {
		case 'before':
			return $img . $title;
			break;
		case 'after':
			return $title . $img;
			break;
		case 'replace':
			return $img;
			break;
	}
	return $title;

}
add_filter( 'nav_menu_item_title', 'wpcloud_nav_menu_add_image', 10, 2 );