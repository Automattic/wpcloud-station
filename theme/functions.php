<?php
/**
 * WP Cloud Dashboard functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WP_Cloud_Dashboard
 */

if ( ! defined( 'WPCLOUD_DASHBOARD_VERSION' ) ) {
	define( 'WPCLOUD_DASHBOARD_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function wpcloud_dashboard_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on WP Cloud Dashboard, use a find and replace
		* to change 'wpcloud-dashboard' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'wpcloud-dashboard', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary', 'wpcloud-dashboard' ),
			'site'    => esc_html__( 'Site', 'wpcloud-dashboard' ),
			'footer' => esc_html__( 'Footer', 'wpcloud-dashboard' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'wpcloud_dashboard_custom_background_args',
			array(
				'default-color' => '1A1515',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'wpcloud_dashboard_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function wpcloud_dashboard_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'wpcloud_dashboard_content_width', 640 );
}
add_action( 'after_setup_theme', 'wpcloud_dashboard_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function wpcloud_dashboard_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'wpcloud-dashboard' ),
			'id'            => 'primary-sidebar',
			'description'   => esc_html__( 'Add widgets here.', 'wpcloud-dashboard' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'wpcloud_dashboard_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function wpcloud_dashboard_scripts() {
	wp_enqueue_style( 'wpcloud-dashboard-style', get_stylesheet_uri(), array(), WPCLOUD_DASHBOARD_VERSION );
	wp_style_add_data( 'wpcloud-dashboard-style', 'rtl', 'replace' );

	wp_enqueue_script( 'wpcloud-dashboard-navigation', get_template_directory_uri() . '/js/navigation.js', array(), WPCLOUD_DASHBOARD_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'wpcloud_dashboard_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

add_action('init', 'wpcloud_start_session', 1);
function wpcloud_start_session() {
	session_start();
}

function wpcloud_dashboard_add_notice( $message, $type = 'success' ) {
	$_SESSION['notices'][] = array(
		'message' => $message,
		'type'    => $type,
	);
	session_write_close();
}

function wpcloud_dashboard_notices() {
	if ( isset( $_SESSION['notices'] ) ) {
		$notices = $_SESSION['notices'];
		foreach ( $notices as $notice ) {
			?>
			<div class="notice notice-<?php echo esc_attr( $notice['type'] ); ?> is-dismissible">
				<p><?php echo esc_html( $notice['message'] ); ?></p>
			</div>
			<?php
		}

	}
	session_unset();
}

add_action('wp_logout','wpcloud_end_session');
add_action('wp_login','wpcloud_end_session');
add_action('end_session_action','wpcloud_end_session');

function wpcloud_end_session() {
	session_destroy ();
}
