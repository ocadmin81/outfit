<?php
/**
 * Displays header site branding
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>
<div class="site-branding">
	<div class="wrap">
		<div class="right header-right">
			<div class="publish"><a href="#"><?php _e( "פרסום מודעה", 'twentyseventeen' ); ?></a></div>
			<div class="favourites">
				<a href="#">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/heart.png" />
					<span class="count">0</span>
				</a>
			</div>
			<div class="login"><a href="#"><?php _e( "היי, התחבר/י", 'twentyseventeen' ); ?></a></div>
		</div>
		<?php the_custom_logo(); ?>
		<div class="left header-left"><?php get_search_form(); ?></div>

		<?php if ( ( twentyseventeen_is_frontpage() || ( is_home() && is_front_page() ) ) && ! has_nav_menu( 'top' ) ) : ?>
		<a href="#content" class="menu-scroll-down"><?php echo twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ); ?><span class="screen-reader-text"><?php _e( 'Scroll down to content', 'twentyseventeen' ); ?></span></a>
	<?php endif; ?>

	</div><!-- .wrap -->
</div><!-- .site-branding -->
