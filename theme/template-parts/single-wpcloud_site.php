<?php
/**
 * Template part for displaying post
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WP_Cloud_Dashboard
 */

$cloud_site = WPCLOUD_Site::from_post( get_post(),true );
$site_details = $cloud_site->details;
$site_url = get_the_title();
$site_name = get_the_title();
$view_url = get_the_permalink();
$site_name = get_post_meta( get_the_ID(), 'name', true );
$site_thumbnail = get_theme_file_uri( '/assets/Gravatar_filled_' . get_the_ID() % 5 . '.png' );
$ex_link = get_theme_file_uri( '/assets/external-link.svg' );
$datacenter = $site_details['datacenter'];
$php_version = $site_details['php_version'];
$wordpress_version = $site_details['wp_version'];
$wp_icon = get_theme_file_uri( '/assets/wordpress.svg' );

if ( empty( $site_name ) ) {
	$site_name = $site_url;
}
?>

<img class="site-thumbnail" src="<?php echo $site_thumbnail; ?>" />
<h2 class="site-title">
	<a href="<?php echo $site_url; ?>"><?php echo $site_name; ?></a>
	<a class="wpcloud-site-details-wpadmin button button--wp" target="_blank" href="https://<?php echo $site_url; ?>/wp-admin"><img src="<?php echo $wp_icon; ?>" /><span>WP Admin</span><img src="<?php echo $ex_link; ?>"/></a>
</h2>
<h3 class="site-url">
	<a href="https://<?php echo $site_url; ?>" target="_blank"><span><?php echo $site_url; ?></span><img src="<?php echo $ex_link; ?>"/></a>
</h3>

<nav class="wpcloud-site-details-nav menu nav-menu">
	<ul>
		<li class="wpcloud-nav-sites menu-item current-menu-item"><a href="#site-information" data-id="site-information">Information</a></li>
		<li class="wpcloud-nav-collaborators menu-item"><a href="#site-actions" data-id="site-actions">Actions</a></li>
		<li class="wpcloud-nav-sites menu-item"><a href="#site-domains" data-id="site-domains">Domains</a></li>
		<li class="wpcloud-nav-usage menu-item"><a href="#site-performance" data-id="site-performance">Performance</a></li>
		<li class="wpcloud-nav-sites menu-item"><a href="#site-connections" data-id="site-connections">Connections</a></li>
		<li class="wpcloud-nav-sites menu-item"><a href="#site-data" data-id="site-data">Data</a></li>
		<li class="wpcloud-nav-sites menu-item"><a href="#site-plugins" data-id="site-plugins">Plugins</a></li>
		<li class="wpcloud-nav-sites menu-item"><a href="#site-logs" data-id="site-logs">Logs</a></li>
	</ul>
</nav>

<div class="wpcloud-site-details-content">
	<div id="site-information" class="wpcloud-site-details-section active">
		<h3>Site Name</h3>
		<p><?php echo $site_name; ?></p>
		<h3>Primary Domain</h3>
		<p><?php echo $site_url; ?></p>
		<h3>Datacenter</h3>
		<p><?php echo $datacenter; ?></p>
		<h3>PHP Version</h3>
		<select>
			<?php
			foreach ( WPCLOUD_PHP_VERSIONS as $option_value => $option_label ) {
				?>
				<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $option_value, $php_version ); ?>>
					<?php echo esc_html( $option_label ); ?>
				</option>
				<?php
			}
			?>
		</select>
		<h3>WordPress Version</h3>
		<select>
			<option>Previous Version (6.4)</option>
			<option selected>Current Stable (6.5)</option>
			<option>Beta Version</option>
		</select>
	</div>
	<div id="site-actions" class="wpcloud-site-details-section">Actions</div>
	<div id="site-domains" class="wpcloud-site-details-section">Domains</div>
	<div id="site-performance" class="wpcloud-site-details-section">Performance</div>
	<div id="site-connections" class="wpcloud-site-details-section">Connections</div>
	<div id="site-data" class="wpcloud-site-details-section">Data</div>
	<div id="site-plugins" class="wpcloud-site-details-section">Plugins</div>
	<div id="site-logs" class="wpcloud-site-details-section">Logs</div>
</div>

<script type="text/javascript">
	document.querySelectorAll('nav.wpcloud-site-details-nav a')
		.forEach(e => e.addEventListener('click', _ => change(e.dataset.id)));

	function change(n) {
		n = n.replace('#', '');
		let panels = document.querySelectorAll('.wpcloud-site-details-content div');
		panels.forEach(p => p.classList.remove('active'));
		document.getElementById(n).classList.add('active');

		// let links = document.querySelectorAll('.wpcloud-site-details-nav li');
		// links.forEach(p => p.classList.remove('current-menu-item'));
	}

	if (location.hash) {
		change(location.hash)
	}
</script>
