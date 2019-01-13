<?php
/**
 * Twenty Seventeen functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 */

define('OUTFIT_AD_POST_TYPE', 'outfit_ad');
define('OUTFIT_AD_STATUS_ACTIVE', 'outfit_active');
define('OUTFIT_AD_STATUS_SOLD', 'outfit_sold');

if ( ! function_exists('write_log')) {
	function write_log ( $log )  {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
}

function outfit_strip_slashes_from_input() {
	//if ( get_magic_quotes_gpc() ) {
	$_POST      = array_map( 'stripslashes_deep', $_POST );
	$_GET       = array_map( 'stripslashes_deep', $_GET );
	$_COOKIE    = array_map( 'stripslashes_deep', $_COOKIE );
	$_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );
	//}
}

/*==========================
 Requried some Files.
 ===========================*/
require get_template_directory() . '/inc/post_status.php';
require get_template_directory() . '/inc/constants.php';
require get_template_directory() . '/inc/outfit_init.php';
require get_template_directory() . '/inc/theme-support.php';
require get_template_directory() . '/inc/enque-styles-script.php';
require get_template_directory() . '/inc/user_status.php';
require get_template_directory() . '/inc/model-functions.php';
require get_template_directory() . '/inc/outfit-ajax.php';
require get_template_directory() . '/inc/outfit-location.php';
require get_template_directory() . '/templates/email/email_functions.php';
require_once('pagination.php');

/*==========================
 Display Global ajax URL
 ===========================*/
add_action('wp_head','outfit_ajaxURL');
function outfit_ajaxURL() {
	?>
	<script type="text/javascript">
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	</script>
	<?php
}

/*==========================
 Outfit : Add Redux Framework
 @return void
 ===========================*/
if ( !isset( $redux_demo ) && file_exists( get_template_directory() . '/ReduxFramework/theme-options.php' ) ) {
	require_once( get_template_directory() . '/ReduxFramework/theme-options.php' );
}

/**
 * Twenty Seventeen only works in WordPress 4.7 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.7-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function twentyseventeen_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed at WordPress.org. See: https://translate.wordpress.org/projects/wp-themes/twentyseventeen
	 * If you're building a theme based on Twenty Seventeen, use a find and replace
	 * to change 'twentyseventeen' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'twentyseventeen' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	//add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	add_image_size( 'twentyseventeen-featured-image', 2000, 1200, true );

	add_image_size( 'twentyseventeen-thumbnail-avatar', 100, 100, true );

	// Set the default content width.
	$GLOBALS['content_width'] = 525;

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'top'    => __( 'Top Menu', 'twentyseventeen' ),
		'social' => __( 'Social Links Menu', 'twentyseventeen' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
		'gallery',
		'audio',
	) );

	// Add theme support for Custom Logo.
	add_theme_support( 'custom-logo', array(
		'width'       => 250,
		'height'      => 250,
		'flex-width'  => true,
	) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, and column width.
 	 */
	add_editor_style( array( 'assets/css/editor-style.css', twentyseventeen_fonts_url() ) );

	// Define and register starter content to showcase the theme on new sites.
	$starter_content = array(
		'widgets' => array(
			// Place three core-defined widgets in the sidebar area.
			'sidebar-1' => array(
				'text_business_info',
				'search',
				'text_about',
			),

			// Add the core-defined business info widget to the footer 1 area.
			'sidebar-2' => array(
				'text_business_info',
			),

			// Put two core-defined widgets in the footer 2 area.
			'sidebar-3' => array(
				'text_about',
				'search',
			),
		),

		// Specify the core-defined pages to create and add custom thumbnails to some of them.
		'posts' => array(
			'home',
			'about' => array(
				'thumbnail' => '{{image-sandwich}}',
			),
			'contact' => array(
				'thumbnail' => '{{image-espresso}}',
			),
			'blog' => array(
				'thumbnail' => '{{image-coffee}}',
			),
			'homepage-section' => array(
				'thumbnail' => '{{image-espresso}}',
			),
		),

		// Create the custom image attachments used as post thumbnails for pages.
		'attachments' => array(
			'image-espresso' => array(
				'post_title' => _x( 'Espresso', 'Theme starter content', 'twentyseventeen' ),
				'file' => 'assets/images/espresso.jpg', // URL relative to the template directory.
			),
			'image-sandwich' => array(
				'post_title' => _x( 'Sandwich', 'Theme starter content', 'twentyseventeen' ),
				'file' => 'assets/images/sandwich.jpg',
			),
			'image-coffee' => array(
				'post_title' => _x( 'Coffee', 'Theme starter content', 'twentyseventeen' ),
				'file' => 'assets/images/coffee.jpg',
			),
		),

		// Default to a static front page and assign the front and posts pages.
		'options' => array(
			'show_on_front' => 'page',
			'page_on_front' => '{{home}}',
			'page_for_posts' => '{{blog}}',
		),

		// Set the front page section theme mods to the IDs of the core-registered pages.
		'theme_mods' => array(
			'panel_1' => '{{homepage-section}}',
			'panel_2' => '{{about}}',
			'panel_3' => '{{blog}}',
			'panel_4' => '{{contact}}',
		),

		// Set up nav menus for each of the two areas registered in the theme.
		'nav_menus' => array(
			// Assign a menu to the "top" location.
			'top' => array(
				'name' => __( 'Top Menu', 'twentyseventeen' ),
				'items' => array(
					'link_home', // Note that the core "home" page is actually a link in case a static front page is not used.
					'page_about',
					'page_blog',
					'page_contact',
				),
			),

			// Assign a menu to the "social" location.
			'social' => array(
				'name' => __( 'Social Links Menu', 'twentyseventeen' ),
				'items' => array(
					'link_yelp',
					'link_facebook',
					'link_twitter',
					'link_instagram',
					'link_email',
				),
			),
		),
	);

	/**
	 * Filters Twenty Seventeen array of starter content.
	 *
	 * @since Twenty Seventeen 1.1
	 *
	 * @param array $starter_content Array of starter content.
	 */
	$starter_content = apply_filters( 'twentyseventeen_starter_content', $starter_content );

	add_theme_support( 'starter-content', $starter_content );
}
add_action( 'after_setup_theme', 'twentyseventeen_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function twentyseventeen_content_width() {

	$content_width = $GLOBALS['content_width'];

	// Get layout.
	$page_layout = get_theme_mod( 'page_layout' );

	// Check if layout is one column.
	if ( 'one-column' === $page_layout ) {
		if ( twentyseventeen_is_frontpage() ) {
			$content_width = 644;
		} elseif ( is_page() ) {
			$content_width = 740;
		}
	}

	// Check if is single post and there is no sidebar.
	if ( is_single() && ! is_active_sidebar( 'sidebar-1' ) ) {
		$content_width = 740;
	}

	/**
	 * Filter Twenty Seventeen content width of the theme.
	 *
	 * @since Twenty Seventeen 1.0
	 *
	 * @param int $content_width Content width in pixels.
	 */
	$GLOBALS['content_width'] = apply_filters( 'twentyseventeen_content_width', $content_width );
}
add_action( 'template_redirect', 'twentyseventeen_content_width', 0 );

/**
 * Register custom fonts.
 */
function twentyseventeen_fonts_url() {
	$fonts_url = '';

	/*
	 * Translators: If there are characters in your language that are not
	 * supported by Libre Franklin, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$libre_franklin = _x( 'on', 'Libre Franklin font: on or off', 'twentyseventeen' );

	if ( 'off' !== $libre_franklin ) {
		$font_families = array();

		$font_families[] = 'Libre Franklin:300,300i,400,400i,600,600i,800,800i';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return esc_url_raw( $fonts_url );
}

/**
 * Add preconnect for Google Fonts.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param array  $urls           URLs to print for resource hints.
 * @param string $relation_type  The relation type the URLs are printed.
 * @return array $urls           URLs to print for resource hints.
 */
function twentyseventeen_resource_hints( $urls, $relation_type ) {
	if ( wp_style_is( 'twentyseventeen-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'twentyseventeen_resource_hints', 10, 2 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function twentyseventeen_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'twentyseventeen' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'twentyseventeen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 1', 'twentyseventeen' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Add widgets here to appear in your footer.', 'twentyseventeen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 2', 'twentyseventeen' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'Add widgets here to appear in your footer.', 'twentyseventeen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'twentyseventeen_widgets_init' );

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
 * a 'Continue reading' link.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $link Link to single post/page.
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function twentyseventeen_excerpt_more( $link ) {
	if ( is_admin() ) {
		return $link;
	}

	$link = sprintf( '<p class="link-more"><a href="%1$s" class="more-link">%2$s</a></p>',
		esc_url( get_permalink( get_the_ID() ) ),
		/* translators: %s: Name of current post */
		sprintf( __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'twentyseventeen' ), get_the_title( get_the_ID() ) )
	);
	return ' &hellip; ' . $link;
}
add_filter( 'excerpt_more', 'twentyseventeen_excerpt_more' );

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Twenty Seventeen 1.0
 */
function twentyseventeen_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'twentyseventeen_javascript_detection', 0 );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function twentyseventeen_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", get_bloginfo( 'pingback_url' ) );
	}
}
add_action( 'wp_head', 'twentyseventeen_pingback_header' );

/**
 * Display custom color CSS.
 */
function twentyseventeen_colors_css_wrap() {
	if ( 'custom' !== get_theme_mod( 'colorscheme' ) && ! is_customize_preview() ) {
		return;
	}

	require_once( get_parent_theme_file_path( '/inc/color-patterns.php' ) );
	$hue = absint( get_theme_mod( 'colorscheme_hue', 250 ) );
?>
	<style type="text/css" id="custom-theme-colors" <?php if ( is_customize_preview() ) { echo 'data-hue="' . $hue . '"'; } ?>>
		<?php echo twentyseventeen_custom_colors_css(); ?>
	</style>
<?php }
add_action( 'wp_head', 'twentyseventeen_colors_css_wrap' );

/**
 * Enqueue scripts and styles.
 */
function twentyseventeen_scripts() {
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'twentyseventeen-fonts', twentyseventeen_fonts_url(), array(), null );

	// Theme stylesheet.
	wp_enqueue_style( 'twentyseventeen-style', get_stylesheet_uri() );

	// Load the dark colorscheme.
	if ( 'dark' === get_theme_mod( 'colorscheme', 'light' ) || is_customize_preview() ) {
		wp_enqueue_style( 'twentyseventeen-colors-dark', get_theme_file_uri( '/assets/css/colors-dark.css' ), array( 'twentyseventeen-style' ), '1.0' );
	}

	// Load the Internet Explorer 9 specific stylesheet, to fix display issues in the Customizer.
	if ( is_customize_preview() ) {
		wp_enqueue_style( 'twentyseventeen-ie9', get_theme_file_uri( '/assets/css/ie9.css' ), array( 'twentyseventeen-style' ), '1.0' );
		wp_style_add_data( 'twentyseventeen-ie9', 'conditional', 'IE 9' );
	}

	// Load the Internet Explorer 8 specific stylesheet.
	wp_enqueue_style( 'twentyseventeen-ie8', get_theme_file_uri( '/assets/css/ie8.css' ), array( 'twentyseventeen-style' ), '1.0' );
	wp_style_add_data( 'twentyseventeen-ie8', 'conditional', 'lt IE 9' );

	// Load the html5 shiv.
	wp_enqueue_script( 'html5', get_theme_file_uri( '/assets/js/html5.js' ), array(), '3.7.3' );
	wp_script_add_data( 'html5', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'twentyseventeen-skip-link-focus-fix', get_theme_file_uri( '/assets/js/skip-link-focus-fix.js' ), array(), '1.0', true );

	$twentyseventeen_l10n = array(
		'quote'          => twentyseventeen_get_svg( array( 'icon' => 'quote-right' ) ),
	);

	if ( has_nav_menu( 'top' ) ) {
		wp_enqueue_script( 'twentyseventeen-navigation', get_theme_file_uri( '/assets/js/navigation.js' ), array( 'jquery' ), '1.0', true );
		$twentyseventeen_l10n['expand']         = __( 'Expand child menu', 'twentyseventeen' );
		$twentyseventeen_l10n['collapse']       = __( 'Collapse child menu', 'twentyseventeen' );
		$twentyseventeen_l10n['icon']           = twentyseventeen_get_svg( array( 'icon' => 'angle-down', 'fallback' => true ) );
	}

	wp_enqueue_script( 'twentyseventeen-global', get_theme_file_uri( '/assets/js/global.js' ), array( 'jquery' ), '1.0', true );

	wp_enqueue_script( 'jquery-scrollto', get_theme_file_uri( '/assets/js/jquery.scrollTo.js' ), array( 'jquery' ), '2.1.2', true );

	wp_localize_script( 'twentyseventeen-skip-link-focus-fix', 'twentyseventeenScreenReaderText', $twentyseventeen_l10n );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'twentyseventeen_scripts' );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for content images.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $sizes A source size value for use in a 'sizes' attribute.
 * @param array  $size  Image size. Accepts an array of width and height
 *                      values in pixels (in that order).
 * @return string A source size value for use in a content image 'sizes' attribute.
 */
function twentyseventeen_content_image_sizes_attr( $sizes, $size ) {
	$width = $size[0];

	if ( 740 <= $width ) {
		$sizes = '(max-width: 706px) 89vw, (max-width: 767px) 82vw, 740px';
	}

	if ( is_active_sidebar( 'sidebar-1' ) || is_archive() || is_search() || is_home() || is_page() ) {
		if ( ! ( is_page() && 'one-column' === get_theme_mod( 'page_options' ) ) && 767 <= $width ) {
			 $sizes = '(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px';
		}
	}

	return $sizes;
}
add_filter( 'wp_calculate_image_sizes', 'twentyseventeen_content_image_sizes_attr', 10, 2 );

/**
 * Filter the `sizes` value in the header image markup.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $html   The HTML image tag markup being filtered.
 * @param object $header The custom header object returned by 'get_custom_header()'.
 * @param array  $attr   Array of the attributes for the image tag.
 * @return string The filtered header image HTML.
 */
function twentyseventeen_header_image_tag( $html, $header, $attr ) {
	if ( isset( $attr['sizes'] ) ) {
		$html = str_replace( $attr['sizes'], '100vw', $html );
	}
	return $html;
}
add_filter( 'get_header_image_tag', 'twentyseventeen_header_image_tag', 10, 3 );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for post thumbnails.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param array $attr       Attributes for the image markup.
 * @param int   $attachment Image attachment ID.
 * @param array $size       Registered image size or flat array of height and width dimensions.
 * @return array The filtered attributes for the image markup.
 */
function twentyseventeen_post_thumbnail_sizes_attr( $attr, $attachment, $size ) {
	if ( is_archive() || is_search() || is_home() ) {
		$attr['sizes'] = '(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px';
	} else {
		$attr['sizes'] = '100vw';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'twentyseventeen_post_thumbnail_sizes_attr', 10, 3 );

/**
 * Use front-page.php when Front page displays is set to a static page.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $template front-page.php.
 *
 * @return string The template to be used: blank if is_home() is true (defaults to index.php), else $template.
 */
function twentyseventeen_front_page_template( $template ) {
	return is_home() ? '' : $template;
}
add_filter( 'frontpage_template',  'twentyseventeen_front_page_template' );

/**
 * Modifies tag cloud widget arguments to display all tags in the same font size
 * and use list format for better accessibility.
 *
 * @since Twenty Seventeen 1.4
 *
 * @param array $args Arguments for tag cloud widget.
 * @return array The filtered arguments for tag cloud widget.
 */
function twentyseventeen_widget_tag_cloud_args( $args ) {
	$args['largest']  = 1;
	$args['smallest'] = 1;
	$args['unit']     = 'em';
	$args['format']   = 'list';

	return $args;
}
add_filter( 'widget_tag_cloud_args', 'twentyseventeen_widget_tag_cloud_args' );

/**
 * Implement the Custom Header feature.
 */
require get_parent_theme_file_path( '/inc/custom-header.php' );

/**
 * Custom template tags for this theme.
 */
require get_parent_theme_file_path( '/inc/template-tags.php' );

/**
 * Additional features to allow styling of the templates.
 */
require get_parent_theme_file_path( '/inc/template-functions.php' );

/**
 * Customizer additions.
 */
require get_parent_theme_file_path( '/inc/customizer.php' );

/**
 * SVG icons functions and filters.
 */
require get_parent_theme_file_path( '/inc/icon-functions.php' );


/*==========================
 Upload User Profile Image and return attachment ID
 ===========================*/
function outfit_insert_userIMG($file_handler){
	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	$attach_id = media_handle_upload($file_handler, $post_id = null);
	if (is_wp_error($attach_id)) return false;
	return $attach_id;
}

/*==========================
 Outfit : Get Profile URL from attachment ID
 ===========================*/
function outfit_get_profile_img($attach_id){
	$sourceURL = wp_get_attachment_image_src($attach_id);
	$profileURL = $sourceURL[0];
	return $profileURL;
}

/*==========================
 Outfit : Get Avatar URL
 ===========================*/
function outfit_get_avatar_url($author_id, $size){
	$get_avatar = get_avatar( $author_id, $size );
	$matches = array();
	preg_match('/(?<!_)src=([\'"])?(.*?)\\1/',$get_avatar, $matches);
	return ( $matches[2] );
}

function outfit_get_user_picture($userId, $size=150) {
	$profileImgId = get_user_meta($userId, USER_META_AVATAR_URL, true);
	$profileImg = outfit_get_profile_img($profileImgId);
	if (empty($profileImg)) {
		$profileImg = outfit_get_avatar_url($userId, $size);
	}
	return $profileImg;
}

function outfit_get_wishlist_count($userId) {
	if (empty($userId)) return 0;
	//global $wpdb;
	//$prepared_statement = $wpdb->prepare("SELECT COUNT(DISTINCT post_id) FROM {$wpdb->prefix}author_favorite WHERE  author_id = %d", $userId);
	//$count = $wpdb->get_var($prepared_statement);
	//return $count;
	$args = array(
		'post_type' => OUTFIT_AD_POST_TYPE,
		'post_status' => array('publish','sold'),
		'posts_per_page' => -1,
	);
	$favoritearray = outfit_authors_all_favorite($userId);
	if (count($favoritearray) == 0) return 0;

	$args['post__in'] = $favoritearray;
	$wp_query = new WP_Query($args);
	$count = count($wp_query->get_posts());
	wp_reset_query();
	return $count;
}

function outfit_wishlist_count_shortcode($atts = [], $content = null) {
	global $current_user;
	wp_get_current_user();
	if (empty($current_user->ID)) return '0';
	return outfit_get_wishlist_count($current_user->ID);
}

add_shortcode('wishlistcount', 'outfit_wishlist_count_shortcode');

/*========================================
 Outfit : Get User Favorite Posts IDs
 =========================================*/
function outfit_authors_all_favorite($author_id) {
	global $wpdb;
	$prepared_statement = $wpdb->prepare("SELECT DISTINCT post_id FROM {$wpdb->prefix}author_favorite WHERE  author_id = %d", $author_id);
	$postids = $wpdb->get_col($prepared_statement);

	return $postids;
}

/*========================================
 Outfit : Get User Favorite Authors IDs
 =========================================*/
function outfit_authors_all_favorite_sellers($author_id) {
	global $wpdb;
	$prepared_statement = $wpdb->prepare("SELECT DISTINCT author_id FROM {$wpdb->prefix}author_follower WHERE  follower_id = %d", $author_id);
	$ids = $wpdb->get_col($prepared_statement);

	return $ids;
}

/*========================================
 Outfit : Add Follower
 =========================================*/
function outfit_insert_author_follower($author_id, $follower_id) {
	global $wpdb;
	$author_insert = ("INSERT into {$wpdb->prefix}author_follower (author_id,follower_id)value('".$author_id."','".$follower_id."')");
	$wpdb->query($author_insert);
}

/*========================================
 Outfit : Delete Follower
 =========================================*/
function outfit_delete_author_follower($author_id, $follower_id) {
	global $wpdb;
	$author_del = ("DELETE from {$wpdb->prefix}author_follower WHERE author_id = $author_id AND follower_id = $follower_id ");
	$wpdb->query($author_del);
}

/*========================================
 Outfit : Add Post to Favorites
 =========================================*/
function outfit_insert_author_favorite($author_id, $post_id) {
	global $wpdb;
	$author_insert = ("INSERT into {$wpdb->prefix}author_favorite (author_id,post_id)value('".$author_id."','".$post_id."')");
	$wpdb->query($author_insert);
}

/*========================================
 Outfit : Delete Post from Favorites
 =========================================*/
function outfit_delete_author_favorite($author_id, $post_id) {
	global $wpdb;
	$author_del = ("DELETE from {$wpdb->prefix}author_favorite WHERE author_id = $author_id AND post_id = $post_id ");
	$wpdb->query($author_del);
}

function outfit_is_favorite_post($author_id, $post_id) {
	global $wpdb;
	$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}author_favorite WHERE post_id = $post_id AND author_id = $author_id" );
	if (empty($results)) {
		return false;
	}
	return true;
}

function outfit_is_favorite_author($author_id, $follower_id) {
	global $wpdb;
	$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}author_follower WHERE follower_id = $follower_id AND author_id = $author_id" );
	if (empty($results)) {
		return false;
	}
	return true;
}

/*==========================
 Outfit : Create tables
 @since classiera 1.0
 ===========================*/
function outfit_authors_tbl_create() {
	/*global $wpdb;
	$sql2 = ("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}author_follower (

        id int(11) NOT NULL AUTO_INCREMENT,

        author_id int(11) NOT NULL,

        follower_id int(11) NOT NULL,

        PRIMARY KEY (id)

    ) ENGINE=InnoDB AUTO_INCREMENT=1;");
	$wpdb->query($sql2);
	$sql = ("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}author_favorite (

        id int(11) NOT NULL AUTO_INCREMENT,

        author_id int(11) NOT NULL,

        post_id int(11) NOT NULL,

        PRIMARY KEY (id)

    ) ENGINE=InnoDB AUTO_INCREMENT=1;");

	$wpdb->query($sql);*/
}

function outfit_init() {
	OutfitInit::init();
}

add_action( 'init', 'outfit_init', 1 );

// Category new fields (the form)
add_filter('add_category_form', 'outfit_my_category_fields');
add_filter('edit_category_form', 'outfit_my_category_fields');

// Update category fields
add_action( 'edited_category', 'outfit_update_my_category_fields', 10, 2 );
add_action( 'create_category', 'outfit_update_my_category_fields', 10, 2 );

// change comment form buttin text
add_filter('comment_form_defaults', 'wpsites_change_comment_form_submit_label');

/*==========================
 Function to display extra info on category admin
 ===========================*/
// the option name
define('MY_CATEGORY_FIELDS', 'my_category_fields_option');
// your fields (the form)
function outfit_my_category_fields($tag) {
	if (!($tag instanceof WP_Term)) return;
	$tag_extra_fields = get_option(MY_CATEGORY_FIELDS);
	$catFilterByColor = isset( $tag_extra_fields[$tag->term_id]['category_filter_by_color'] ) ? $tag_extra_fields[$tag->term_id]['category_filter_by_color'] : false;
	$catFilterByBrand = isset( $tag_extra_fields[$tag->term_id]['category_filter_by_brand'] ) ? $tag_extra_fields[$tag->term_id]['category_filter_by_brand'] : false;
	$catFilterByAge = isset( $tag_extra_fields[$tag->term_id]['category_filter_by_age'] ) ? $tag_extra_fields[$tag->term_id]['category_filter_by_age'] : false;
	$catFilterByCondition = isset( $tag_extra_fields[$tag->term_id]['category_filter_by_condition'] ) ? $tag_extra_fields[$tag->term_id]['category_filter_by_condition'] : false;
	$catFilterByWriter = isset( $tag_extra_fields[$tag->term_id]['category_filter_by_writer'] ) ? $tag_extra_fields[$tag->term_id]['category_filter_by_writer'] : false;
	$catFilterByCharacter = isset( $tag_extra_fields[$tag->term_id]['category_filter_by_character'] ) ? $tag_extra_fields[$tag->term_id]['category_filter_by_character'] : false;
	$catFilterByGender = isset( $tag_extra_fields[$tag->term_id]['category_filter_by_gender'] ) ? $tag_extra_fields[$tag->term_id]['category_filter_by_gender'] : false;
	$catFilterByLanguage = isset( $tag_extra_fields[$tag->term_id]['category_filter_by_language'] ) ? $tag_extra_fields[$tag->term_id]['category_filter_by_language'] : false;
	$catFilterByShoeSize = isset( $tag_extra_fields[$tag->term_id]['category_filter_by_shoesize'] ) ? $tag_extra_fields[$tag->term_id]['category_filter_by_shoesize'] : false;
	$catFilterByMaternitySize = isset( $tag_extra_fields[$tag->term_id]['category_filter_by_maternitysize'] ) ? $tag_extra_fields[$tag->term_id]['category_filter_by_maternitysize'] : false;
	$catFilterByBicycleSize = isset( $tag_extra_fields[$tag->term_id]['category_filter_by_bicyclesize'] ) ? $tag_extra_fields[$tag->term_id]['category_filter_by_bicyclesize'] : false;


	?>

	<div class="form-field">
		<table class="form-table">
			<tr class="form-field">
				<th scope="row" valign="top"><label for="category-page-slider"><?php esc_html_e( 'Filter By Color', 'outfit-standalone' ); ?></label></th>
				<td>

					<input id="category_filter_by_color" type="checkbox" name="category_filter_by_color"
						   value="1" <?php echo ($catFilterByColor? "checked" : "") ?> />
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="category-page-slider"><?php esc_html_e( 'Filter By Age', 'outfit-standalone' ); ?></label></th>
				<td>

					<input id="category_filter_by_age" type="checkbox" name="category_filter_by_age"
						   value="1" <?php echo ($catFilterByAge? "checked" : "") ?> />
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="category-page-slider"><?php esc_html_e( 'Filter By Brand', 'outfit-standalone' ); ?></label></th>
				<td>

					<input id="category_filter_by_brand" type="checkbox" name="category_filter_by_brand"
						   value="1" <?php echo ($catFilterByBrand? "checked" : "") ?> />
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="category-page-slider"><?php esc_html_e( 'Filter By Condition', 'outfit-standalone' ); ?></label></th>
				<td>

					<input id="category_filter_by_condition" type="checkbox" name="category_filter_by_condition"
						   value="1" <?php echo ($catFilterByCondition? "checked" : "") ?> />
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="category-page-slider"><?php esc_html_e( 'Filter By Writer', 'outfit-standalone' ); ?></label></th>
				<td>

					<input id="category_filter_by_writer" type="checkbox" name="category_filter_by_writer"
						   value="1" <?php echo ($catFilterByWriter? "checked" : "") ?> />
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="category-page-slider"><?php esc_html_e( 'Filter By Character', 'outfit-standalone' ); ?></label></th>
				<td>

					<input id="category_filter_by_character" type="checkbox" name="category_filter_by_character"
						   value="1" <?php echo ($catFilterByCharacter? "checked" : "") ?> />
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="category-page-slider"><?php esc_html_e( 'Filter By Gender', 'outfit-standalone' ); ?></label></th>
				<td>

					<input id="category_filter_by_gender" type="checkbox" name="category_filter_by_gender"
						   value="1" <?php echo ($catFilterByGender? "checked" : "") ?> />
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="category-page-slider"><?php esc_html_e( 'Filter By Language', 'outfit-standalone' ); ?></label></th>
				<td>

					<input id="category_filter_by_language" type="checkbox" name="category_filter_by_language"
						   value="1" <?php echo ($catFilterByLanguage? "checked" : "") ?> />
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="category-page-slider"><?php esc_html_e( 'Filter By Shoe Size', 'outfit-standalone' ); ?></label></th>
				<td>

					<input id="category_filter_by_shoesize" type="checkbox" name="category_filter_by_shoesize"
						   value="1" <?php echo ($catFilterByShoeSize? "checked" : "") ?> />
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="category-page-slider"><?php esc_html_e( 'Filter By Maternity Size', 'outfit-standalone' ); ?></label></th>
				<td>

					<input id="category_filter_by_maternitysize" type="checkbox" name="category_filter_by_maternitysize"
						   value="1" <?php echo ($catFilterByMaternitySize? "checked" : "") ?> />
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="category-page-slider"><?php esc_html_e( 'Filter By Bicycle Size', 'outfit-standalone' ); ?></label></th>
				<td>

					<input id="category_filter_by_bicyclesize" type="checkbox" name="category_filter_by_bicyclesize"
						   value="1" <?php echo ($catFilterByBicycleSize? "checked" : "") ?> />
				</td>
			</tr>
		</table>
	</div>

	<?php
}
/*==========================
 when the form gets submitted, and the category gets updated (in your case the option will get updated with the values of your custom fields above.
 ===========================*/
function outfit_update_my_category_fields($term_id) {
	if(isset($_POST['taxonomy'])){
		if($_POST['taxonomy'] == 'category'):
			$tag_extra_fields = get_option(MY_CATEGORY_FIELDS);
			$tag_extra_fields[$term_id]['category_filter_by_color'] = isset($_POST['category_filter_by_color'])? true : false;
			$tag_extra_fields[$term_id]['category_filter_by_age'] = isset($_POST['category_filter_by_age'])? true : false;
			$tag_extra_fields[$term_id]['category_filter_by_brand'] = isset($_POST['category_filter_by_brand'])? true : false;
			$tag_extra_fields[$term_id]['category_filter_by_writer'] = isset($_POST['category_filter_by_writer'])? true : false;
			$tag_extra_fields[$term_id]['category_filter_by_character'] = isset($_POST['category_filter_by_character'])? true : false;
			$tag_extra_fields[$term_id]['category_filter_by_condition'] = isset($_POST['category_filter_by_condition'])? true : false;
			$tag_extra_fields[$term_id]['category_filter_by_gender'] = isset($_POST['category_filter_by_gender'])? true : false;
			$tag_extra_fields[$term_id]['category_filter_by_language'] = isset($_POST['category_filter_by_language'])? true : false;
			$tag_extra_fields[$term_id]['category_filter_by_shoesize'] = isset($_POST['category_filter_by_shoesize'])? true : false;
			$tag_extra_fields[$term_id]['category_filter_by_maternitysize'] = isset($_POST['category_filter_by_maternitysize'])? true : false;
			$tag_extra_fields[$term_id]['category_filter_by_bicyclesize'] = isset($_POST['category_filter_by_bicyclesize'])? true : false;

			update_option(MY_CATEGORY_FIELDS, $tag_extra_fields);
		endif;
	}
}
/*==========================
 when a category is removed
 ===========================*/
add_filter('deleted_term_taxonomy', 'outfit_remove_my_category_fields');
function outfit_remove_my_category_fields($term_id) {
	if(isset($_POST['taxonomy'])){
		if($_POST['taxonomy'] == 'category'):
			$tag_extra_fields = get_option(MY_CATEGORY_FIELDS);
			unset($tag_extra_fields[$term_id]);
			update_option(MY_CATEGORY_FIELDS, $tag_extra_fields);
		endif;
	}
}
/*==========================
 Remove category from title
 ===========================*/
add_filter( 'get_the_archive_title', function ( $title ) {
    if( is_category() ) {
        $title = single_cat_title( '', false );
    }
    return $title;
});

/*==========================
 Insert attachments front end
 ===========================*/
function outfit_insert_attachment($file_handler,$post_id) {

	// check to make sure its a successful upload
	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');

	$overrides = array( 'test_form' => false, 'action' => 'outfit_ad_upload' );

	$attach_id = media_handle_upload( $file_handler, $post_id, array(), $overrides );

	//if ($setthumb) update_post_meta($post_id,'_thumbnail_id',$attach_id);
	return $attach_id;
}

add_filter('outfit_ad_upload_prefilter','outfit_ad_upload_prefilter');
function outfit_ad_upload_prefilter($file)
{

	$ext = pathinfo( $file['name'], PATHINFO_EXTENSION );

	$allowed_extensions = array('gif', 'jpg', 'jpeg', 'png');

	if ( !(in_array($ext, $allowed_extensions)) ) {
		$file['error'] = "The uploaded file is not supported. Please upload a valid image file";
	}

	return $file;
}

function getPostInput($key, $default = '') {

	if (isset($_POST[$key])) {
		return $_POST[$key];
	}
	else {
		return $default;
	}
}

function getPostMultiple($key) {

	if (isset($_POST[$key])) {
		if (!is_array($_POST[$key])) {
			return array($_POST[$key]);
		}
		return $_POST[$key];
	}
	return array();
}

function getGetMultiple($key, $clearEmptyValues=false) {

	$t = array();
	if (isset($_GET[$key])) {
		if (!is_array($_GET[$key])) {
			$t = array($_GET[$key]);
		}
		else {
			$t = $_GET[$key];
		}
	}
	if ($clearEmptyValues) {
		$r = array();
		foreach ($t as $k => $v) {
			if (!empty($v)) {
				$r[$k] = $v;
			}
		}
		return $r;
	}
	return $t;
}

function getRequestMultiple($key, $clearEmptyValues=false) {

	$requestValue = (isset($_GET[$key])? $_GET[$key] : (isset($_POST[$key])? $_POST[$key] : null));
	$t = array();
	if (isset($requestValue)) {
		if (!is_array($requestValue)) {
			$t = array($requestValue);
		}
		else {
			$t = $requestValue;
		}
	}
	if ($clearEmptyValues) {
		$r = array();
		foreach ($t as $k => $v) {
			if (!empty($v)) {
				$r[$k] = $v;
			}
		}
		return $r;
	}
	return $t;
}

function getGetInput($key, $default='') {

	if (isset($_GET[$key])) {
		return $_GET[$key];
	}
	return $default;
}

function getRequestInput($key, $default='') {

	if (isset($_GET[$key])) {
		return $_GET[$key];
	}
	else if (isset($_POST[$key])) {
		return $_POST[$key];
	}
	return $default;
}

/**
 * Decode Unicode Characters from \u0000 ASCII syntax.
 *
 * This algorithm was originally developed for the
 * Solar Framework by Paul M. Jones
 *
 * @link   http://solarphp.com/
 * @link   http://svn.solarphp.com/core/trunk/Solar/Json.php
 * @param  string $chrs
 * @return string
 */
function outfitDecodeUnicodeString($chrs)
{
	$delim       = substr($chrs, 0, 1);
	$utf8        = '';
	$strlen_chrs = strlen($chrs);

	for($i = 0; $i < $strlen_chrs; $i++) {

		$substr_chrs_c_2 = substr($chrs, $i, 2);
		$ord_chrs_c = ord($chrs[$i]);

		switch (true) {
			case preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $i, 6)):
				// single, escaped unicode character
				$utf16 = chr(hexdec(substr($chrs, ($i + 2), 2)))
					. chr(hexdec(substr($chrs, ($i + 4), 2)));
				$utf8 .= mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
				$i += 5;
				break;
			case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F):
				$utf8 .= $chrs{$i};
				break;
			case ($ord_chrs_c & 0xE0) == 0xC0:
				// characters U-00000080 - U-000007FF, mask 110XXXXX
				//see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$utf8 .= substr($chrs, $i, 2);
				++$i;
				break;
			case ($ord_chrs_c & 0xF0) == 0xE0:
				// characters U-00000800 - U-0000FFFF, mask 1110XXXX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$utf8 .= substr($chrs, $i, 3);
				$i += 2;
				break;
			case ($ord_chrs_c & 0xF8) == 0xF0:
				// characters U-00010000 - U-001FFFFF, mask 11110XXX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$utf8 .= substr($chrs, $i, 4);
				$i += 3;
				break;
			case ($ord_chrs_c & 0xFC) == 0xF8:
				// characters U-00200000 - U-03FFFFFF, mask 111110XX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$utf8 .= substr($chrs, $i, 5);
				$i += 4;
				break;
			case ($ord_chrs_c & 0xFE) == 0xFC:
				// characters U-04000000 - U-7FFFFFFF, mask 1111110X
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$utf8 .= substr($chrs, $i, 6);
				$i += 5;
				break;
		}
	}

	return $utf8;
}

function outfitEncodeUnicodeString($value)
{
	$strlen_var = strlen($value);
	$ascii = "";

	/**
	 * Iterate over every character in the string,
	 * escaping with a slash or encoding to UTF-8 where necessary
	 */
	for($i = 0; $i < $strlen_var; $i++) {
		$ord_var_c = ord($value[$i]);

		switch (true) {
			case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
				// characters U-00000000 - U-0000007F (same as ASCII)
				$ascii .= $value[$i];
				break;

			case (($ord_var_c & 0xE0) == 0xC0):
				// characters U-00000080 - U-000007FF, mask 110XXXXX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$char = pack('C*', $ord_var_c, ord($value[$i + 1]));
				$i += 1;
				$utf16 = mb_convert_encoding($char, 'UTF-16', 'UTF-8');
				$ascii .= sprintf('\u%04s', bin2hex($utf16));
				break;

			case (($ord_var_c & 0xF0) == 0xE0):
				// characters U-00000800 - U-0000FFFF, mask 1110XXXX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$char = pack('C*', $ord_var_c,
					ord($value[$i + 1]),
					ord($value[$i + 2]));
				$i += 2;
				$utf16 = mb_convert_encoding($char, 'UTF-16', 'UTF-8');
				$ascii .= sprintf('\u%04s', bin2hex($utf16));
				break;

			case (($ord_var_c & 0xF8) == 0xF0):
				// characters U-00010000 - U-001FFFFF, mask 11110XXX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$char = pack('C*', $ord_var_c,
					ord($value[$i + 1]),
					ord($value[$i + 2]),
					ord($value[$i + 3]));
				$i += 3;
				$utf16 = mb_convert_encoding($char, 'UTF-16', 'UTF-8');
				$ascii .= sprintf('\u%04s', bin2hex($utf16));
				break;

			case (($ord_var_c & 0xFC) == 0xF8):
				// characters U-00200000 - U-03FFFFFF, mask 111110XX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$char = pack('C*', $ord_var_c,
					ord($value[$i + 1]),
					ord($value[$i + 2]),
					ord($value[$i + 3]),
					ord($value[$i + 4]));
				$i += 4;
				$utf16 = mb_convert_encoding($char, 'UTF-16', 'UTF-8');
				$ascii .= sprintf('\u%04s', bin2hex($utf16));
				break;

			case (($ord_var_c & 0xFE) == 0xFC):
				// characters U-04000000 - U-7FFFFFFF, mask 1111110X
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$char = pack('C*', $ord_var_c,
					ord($value[$i + 1]),
					ord($value[$i + 2]),
					ord($value[$i + 3]),
					ord($value[$i + 4]),
					ord($value[$i + 5]));
				$i += 5;
				$utf16 = mb_convert_encoding($char, 'UTF-16', 'UTF-8');
				$ascii .= sprintf('\u%04s', bin2hex($utf16));
				break;
		}
	}

	return $ascii;
}

/*==========================
 Get Template URL with template name.
 Mostly Used for WPML
 ===========================*/
if (!function_exists('outfit_get_template_url')) {
	function outfit_get_template_url($templatename){
		$url = '';
		$archive_page = get_pages(
			array(
				'meta_key' => '_wp_page_template',
				'meta_value' => $templatename,
				'suppress_filters' => true,
			)
		);
		$pageID = $archive_page[0]->ID;
		$url = get_permalink($pageID);
		return $url;
	}
}

function outfit_get_page_url($outfit_page_name) {
	$url = '';
	$pages = OutfitInit::get_pages();
	if (isset($pages[$outfit_page_name])) {
		$page = $pages[$outfit_page_name];
		$url = outfit_get_template_url($page['template']);
	}
	return $url;
}

function outfit_edit_ad_url($postId) {
	$editPostUrl = outfit_get_page_url('edit_ad');
	global $wp_rewrite;
	if ($wp_rewrite->permalink_structure == ''){
		$editPostUrl .= "&post=".$postId;
	}else{
		$editPostUrl .= "?post=".$postId;
	}
	return $editPostUrl;
}

function outfit_get_category_link ($catId) {
	$catUrl = get_category_link($catId);
	global $wp_rewrite;
	if ($wp_rewrite->permalink_structure == ''){
		$catUrl .= "&outfit_ad";
	}else{
		$catUrl .= "?outfit_ad";
	}
	return $catUrl;
}

function outfit_login_url_back($back_to = '', $action = '', $post_id = '') {

	if (empty($back_to)) {
		$back_to = outfit_current_url();
	}
	$loginUrl = outfit_get_page_url('login');
	global $wp_rewrite;
	if ($wp_rewrite->permalink_structure == ''){
		$loginUrl .= "&back_to=".urlencode($back_to);
	}else{
		$loginUrl .= "?back_to=".urlencode($back_to);
	}
	if (!empty($action) && !empty($post_id)) {
		$loginUrl .= '&'.$action.'='.$post_id;
	}
	return $loginUrl;
}

function outfit_current_url() {
	$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

	$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	return $url; // full URL
}

function outfit_format_price_range_label($priceRanges, $i, $currencyAfter = true) {
	$currency = '&#8362';
	$res = '';
	$count = count($priceRanges);
	$d = ' ';
	if ($i+1 > $count) return $res;
	if ($i+1 < $count) {
		$res .= ($currencyAfter? $priceRanges[$i]['min'].$d.$currency : $currency.$d.$priceRanges[$i]['min']);
		$res .= '-'.($currencyAfter? $priceRanges[$i]['max'].$d.$currency : $currency.$d.$priceRanges[$i]['max']);
	}
	else {
		$res .= translate('מעל').' '.($currencyAfter? $priceRanges[$i]['min'].$d.$currency : $currency.$d.$priceRanges[$i]['min']);
	}
	return $res;
}

function outfit_format_post_price($price, $currencyAfter = true) {
	$currency = '&#8362';
	$res = '';
	if(!empty($price)) {
		if(is_numeric($price)){
			$res = ($currencyAfter? $price.' '.$currency : $currency.' '.$price);
		}else{
			$res = esc_attr( $price );
		}
	}
	return $res;
}

function outfit_get_post_thumb_url($postId) {
	if( has_post_thumbnail($postId)){
		$img = wp_get_attachment_image_src( get_post_thumbnail_id($postId));
		$url = $img[0];
	}else{
		$url = get_template_directory_uri() . '/assets/images/nothumb.png';
	}
	return $url;
}

function wpsites_change_comment_form_submit_label($arg) {
	$arg['label_submit'] = 'השארת תגובה';
	return $arg;
}

function posts_in_category($query){
	if ($query->is_category) {
		 $query->set('posts_per_archive_page', 20);
	}	
}
//add_filter('pre_get_posts', 'posts_in_category');
add_filter( 'template_include', 'outfit_template_check', 99 );
function outfit_template_check( $template ) {
	global $wp_query;
	if ( is_category() ){

		if ( isset($_GET['outfit_ad']) || isset($_POST['outfit_ad']) ){
			$new_template = get_template_directory() . '/custom-category.php';
			if ( !empty( $new_template ) ) {
				//status_header( 200 );
				//$wp_query->is_page = true;
				//$wp_query->is_404=false;
				return $new_template;
			}
		}
	}
	else if (isset($_GET['search']) || isset($_POST['search']) || isset($_GET['s']) || isset($_POST['s'])) {

		if ( isset($_GET['outfit_ad']) || isset($_POST['outfit_ad']) ){
			$new_template = get_template_directory() . '/custom-search.php';
			if ( !empty( $new_template ) ) {
				return $new_template;
			}
		}
	}
	return $template;

}

//adding parameter to menu links
add_filter( 'wp_get_nav_menu_items','nav_items', 11, 3 );
function nav_items( $items, $menu, $args ) 
{
    if( is_admin() )
        return $items;

    foreach( $items as $item ) 
    {
        $item->url .= '?outfit_ad';
    }
    return $items;
}

add_filter( 'template_include', 'var_template_include', 1000 );
function var_template_include( $t ){
	$GLOBALS['current_theme_template'] = basename($t);
	return $t;
}

function get_current_template( $echo = false ) {
	if( !isset( $GLOBALS['current_theme_template'] ) )
		return false;
	if( $echo )
		echo $GLOBALS['current_theme_template'];
	else
		return $GLOBALS['current_theme_template'];
}

function outfit_category_products_shortcode($atts = [], $content = null)
{
	ob_start();
	$atts = shortcode_atts(
		array(
			'cat_slug' => '',
			'count' => 5,
		), $atts, 'catprod' );
	$catSlug = $atts['cat_slug'];
	global $perPage;
	$perPage = (int) $atts['count'];
	$loggedIn = is_user_logged_in();
	global $current_user, $user_id;
	global $redux_demo;
	global $paged, $wp_query, $wp, $post;
	global $catObj;
	wp_get_current_user();
	$currentUser = $current_user;
	$userId = $user_id = $currentUser->ID;

	$catObj = get_category_by_slug($catSlug);
	if (false !== $catObj) {

		get_template_part('templates/catprod_home');

	}
	return ob_get_clean();
}
add_shortcode('catprod', 'outfit_category_products_shortcode');

function outfit_homepage_products_shortcode($atts = [], $content = null)
{
	ob_start();
	get_template_part('templates/homeprod');
	return ob_get_clean();
}
add_shortcode('homeprod', 'outfit_homepage_products_shortcode');

function outfit_user_firstname() {

	global $current_user;
	wp_get_current_user();
	$currentUser = $current_user;
	$userId = $currentUser->ID;
	if ($userId) {
		return esc_html(get_user_meta($userId, USER_META_FIRSTNAME, true));
	}
	return '';
}
add_shortcode('userfirstname', 'outfit_user_firstname');

function outfit_user_lastname() {

	global $current_user;
	wp_get_current_user();
	$currentUser = $current_user;
	$userId = $currentUser->ID;
	if ($userId) {
		return esc_html(get_user_meta($userId, USER_META_LASTNAME, true));
	}
	return '';
}
add_shortcode('userlastname', 'outfit_user_lastname');

add_action('wp_ajax_outfit_save_search_filters', 'outfit_save_search_filters');
add_action('wp_ajax_nopriv_outfit_save_search_filters', 'outfit_save_search_filters');//for users that are not logged in.

function outfit_save_search_filters() {

	if (!is_user_logged_in()) {
		wp_die();
	}
	global $current_user;
	wp_get_current_user();
	$currentUser = $current_user;
	$ageTermId = (isset($_POST['age']) && !empty($_POST['age']) ? intval($_POST['age']) : false);
	$searchLocations = outfit_get_search_locations();

	if ($ageTermId) {
		update_user_meta($currentUser->ID, USER_META_SEARCH_PREF_AGE, $ageTermId);
	}
	else {
		delete_user_meta($currentUser->ID, USER_META_SEARCH_PREF_AGE);
	}
	if (!empty($searchLocations)) {

		delete_user_meta($currentUser->ID, USER_META_SEARCH_PREF_LOCATION);
		foreach ($searchLocations as $l) {
			add_user_meta($currentUser->ID, USER_META_SEARCH_PREF_LOCATION, $l->toString());
		}
	}
	else {
		delete_user_meta($currentUser->ID, USER_META_SEARCH_PREF_LOCATION);
	}
	//echo 'Age = '.$ageTermId.'; Location address = '.($searchLocationsStr);
	wp_die();
}

function outfit_get_brand_link($termId) {
	$query = 'outfit_ad=1&search=1&postBrand='.$termId.'&bpg=1';
	$link = home_url('?' . $query );
	return $link;
}

add_action('after_setup_theme', 'outfit_remove_admin_bar');

function outfit_remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
		show_admin_bar(false);
	}
}

function outfit_get_thank_you_link() {
	return '/'.'תודה-שפירסמתם-אצלינו';
}

function outfit_show_pending_ad_to_author($query) {
	global $current_user;
	wp_get_current_user();
	$currentUserId = $current_user->ID;
	//echo get_current_template();
	//if ('single-outfit_ad.php' == get_current_template()) {
		if (isset($_GET['p']) && isset($_GET['post_type']) && $_GET['post_type'] == 'outfit_ad') {
			$p = get_post($_GET['p']);
			if ($p) {
				if (!is_admin() && $p->post_author == $currentUserId) {
					$query->set('post_status', array('publish','pending','sold','inherit'));
				}
			}
		}
	//}

}
add_action( 'pre_get_posts', 'outfit_show_pending_ad_to_author' );

function outfit_get_page_permalink($pageId, $queryString) {
	global $wp_rewrite;
	$pagepermalink = get_permalink($pageId);
	$link = ($wp_rewrite->permalink_structure == '')? $pagepermalink."&".$queryString : $pagepermalink."?".$queryString;
	return $link;
}

function outfit_get_add_favorites_link($pageId, $postId) {
	return outfit_get_page_permalink($pageId, 'favorite_id='.$postId);
}

function outfit_get_remove_favorites_link($pageId, $postId) {
	return outfit_get_page_permalink($pageId, 'unfavorite_id='.$postId);
}

function outfit_get_search_locations($str = '') {
	$locations = array();
	$locationStr = '';
	if (!empty($str)) {
		$locationStr = $str;
		//var_dump($locationStr);
	}
	else if (isset($_REQUEST['locations']) && !empty($_REQUEST['locations'])) {
		$locationStr = stripslashes_deep($_REQUEST['locations']);
		//var_dump($locationStr);
	}
	else {
		return $locations;
	}
	$locationsArr = json_decode($locationStr);
	//var_dump($locationsArr);
	if (null === $locationsArr || !is_array($locationsArr)) {
		return $locations;
	}
	foreach ($locationsArr as $jsonstr) {
		$l = OutfitLocation::createFromJSON($jsonstr);
		if (null !== $l && $l->isValid()) {
			$locations[] = $l;
		}
	}
	//var_dump($locations);
	return $locations;
}

function outfit_get_preferred_locations($userId) {
	$locations = array();
	$locationsArr = get_user_meta($userId, USER_META_SEARCH_PREF_LOCATION);
	foreach ($locationsArr as $jsonstr) {
		$l = OutfitLocation::createFromJSON($jsonstr);
		//var_dump($l);
		if (null !== $l && $l->isValid()) {
			$locations[] = $l;
		}
	}
	//var_dump($locationsArr);
	return $locations;
}

function outfit_author_archive( &$query ) {
	if ($query->is_author)
		$query->set( 'post_type', array( OUTFIT_AD_POST_TYPE ) );
}
add_action( 'pre_get_posts', 'outfit_author_archive' );

function outfit_change_title($title, $sep) {
	return get_current_template();
	/*if (isset($_REQUEST['bpg'])) {
		return 'brand page';
	}
	else if (isset($_REQUEST['s']) && isset($_REQUEST['search'])) {
		return 'search results';
	}*/
}

add_action( 'wp_title', 'outfit_change_title', 10, 3 );
// fix 404 pagination wordpress
/*function remove_page_from_query_string($query_string) {
	if (is_admin()) return $query_string;
	if ($query_string['name'] == 'page' && isset($query_string['page'])) {
		unset($query_string['name']);
		// 'page' in the query_string looks like '/2', so i'm spliting it out
		list($delim, $page_index) = split('/', $query_string['page']);
		$query_string['paged'] = $page_index;
	}
	return $query_string;
}

add_filter('request', 'remove_page_from_query_string');*/