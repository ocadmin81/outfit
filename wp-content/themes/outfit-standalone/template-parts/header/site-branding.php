<?php
/**
 * Displays header site branding
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */
$userFirstName = do_shortcode('[userfirstname]');
?>
<div class="site-branding">
	<div class="wrap">
		<div class="right header-right">
			<div class="publish"><a href="<?php echo get_site_url(); ?>/submit_ad"><?php _e( "פרסום מודעה", 'outfit-standalone' ); ?></a></div>
			<div class="favourites hidden-xs">
				<a href="/favorite_ads">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/heart.png" />
					<span class="count">0</span>
				</a>
			</div>
			<div class="login hidden-xs">
				<?php if(is_user_logged_in()): ?>					
					<a href="<?php echo get_site_url(); ?>/profile_settings"><?php _e( "היי, ", 'outfit-standalone' ); ?><?php echo $userFirstName; ?></a>
				<?php else: ?>
					<a href="<?php echo get_site_url(); ?>/login"><?php _e( "היי, התחבר/י", 'outfit-standalone' ); ?></a>					
				<?php endif; ?>
			</div>
		</div>
		<?php the_custom_logo(); ?>
		<div class="left header-left"><?php get_search_form(); ?></div>

		<?php if ( ( twentyseventeen_is_frontpage() || ( is_home() && is_front_page() ) ) && ! has_nav_menu( 'top' ) ) : ?>
		<a href="#content" class="menu-scroll-down"><?php echo twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ); ?><span class="screen-reader-text"><?php _e( 'Scroll down to content', 'outfit-standalone' ); ?></span></a>
	<?php endif; ?>

	</div><!-- .wrap -->
</div><!-- .site-branding -->
