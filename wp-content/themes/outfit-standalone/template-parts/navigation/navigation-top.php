<?php
/**
 * Displays top navigation
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.2
 */

?>
<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Top Menu', 'twentyseventeen' ); ?>">
	<button class="menu-toggle" aria-controls="top-menu" aria-expanded="false">
		<img class="m-close" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/menu-m.png" />
		<img class="m-open" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/menu-m-x.png" />
	</button>
	<div class="m-menu-top hidden-lg hidden-md hidden-sm">
		<div class="favourites">
			<a href="#">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/heart.png" />
				<span class="count">0</span>
			</a>
		</div>
		<div class="login">
			<?php if(is_user_logged_in()): ?>	
				<a href="<?php echo get_site_url(); ?>/profile_settings"><?php _e( "החשבון שלי", 'outfit-standalone' ); ?></a>
			<?php else: ?>
				<a href="<?php echo get_site_url(); ?>/login"><?php _e( "היי, התחבר/י", 'twentyseventeen' ); ?></a>
			<?php endif; ?>
		</div>	
	</div>
	<?php wp_nav_menu( array(
		'theme_location' => 'top',
		'menu_id'        => 'top-menu',
	) ); ?>

	<?php if ( ( twentyseventeen_is_frontpage() || ( is_home() && is_front_page() ) ) && has_custom_header() ) : ?>
		<a href="#content" class="menu-scroll-down"><?php echo twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ); ?><span class="screen-reader-text"><?php _e( 'Scroll down to content', 'twentyseventeen' ); ?></span></a>
	<?php endif; ?>
</nav><!-- #site-navigation -->
