<?php
/**
 * Register theme support for languages, menus, post-thumbnails, post-formats etc.
 *
 * @package WordPress
 * @subpackage outfit
 * @since outfit 0.1
 */

if ( ! function_exists( 'outfit_theme_support' ) ) :
function outfit_theme_support() {

	// Load scripts and styles
	add_action( 'wp_enqueue_scripts', 'outfit_scripts_styles' );
}

add_action( 'after_setup_theme', 'outfit_theme_support' );
endif;
?>