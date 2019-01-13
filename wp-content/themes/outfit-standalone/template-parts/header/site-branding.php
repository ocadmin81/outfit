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
					<span class="count"><?php echo do_shortcode('[wishlistcount]'); ?></span>
				</a>
			</div>
			<div class="login hidden-xs">
				<?php $outfitAllAds = outfit_get_page_url('user_all_ads'); ?>
				<?php if(is_user_logged_in()): ?>					
					<a href="<?php echo esc_url( $outfitAllAds ); ?>"><?php _e( "היי, ", 'outfit-standalone' ); ?><?php echo $userFirstName; ?></a>
				<?php else: ?>
					<a href="<?php echo get_site_url(); ?>/login"><?php _e( "היי, התחבר/י", 'outfit-standalone' ); ?></a>					
				<?php endif; ?>
			</div>
		</div>
		<?php the_custom_logo(); ?>
		<div class="left header-left">
			<?php get_search_form(); ?>	
			<div class="favourites hidden-lg hidden-md hidden-sm">
				<a href="/favorite_ads">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/like.svg" />
					<span class="count"><?php echo do_shortcode('[wishlistcount]'); ?></span>
				</a>
			</div>			
			<div class="login hidden-lg hidden-md hidden-sm">
				<?php $outfitAllAds = outfit_get_page_url('user_all_ads'); ?>
				<?php if(is_user_logged_in()): ?>	
					<a href="<?php echo esc_url( $outfitAllAds ); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/avatar.svg" /></a>
				<?php else: ?>
					<a href="<?php echo get_site_url(); ?>/login"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/avatar.svg" /></a>
				<?php endif; ?>
			</div>							
		</div>
		<?php if ( ( twentyseventeen_is_frontpage() || ( is_home() && is_front_page() ) ) && ! has_nav_menu( 'top' ) ) : ?>
		<a href="#content" class="menu-scroll-down"><?php echo twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ); ?><span class="screen-reader-text"><?php _e( 'Scroll down to content', 'outfit-standalone' ); ?></span></a>
	<?php endif; ?>

	</div><!-- .wrap -->
</div><!-- .site-branding -->
