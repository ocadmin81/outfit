<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.2
 */

?>

		</div><!-- #content -->

		<footer id="colophon" class="site-footer" role="contentinfo">
			<div class="footer-newsletter">
				<div class="wrap">
					<div class="sign-form">
						<?php echo do_shortcode('[mc4wp_form id="100"]'); ?>
					</div>
					<?php if ( has_nav_menu( 'social' ) ) : ?>
						<nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Social Links Menu', 'twentyseventeen' ); ?>">
							<?php
								wp_nav_menu( array(
									'theme_location' => 'social',
									'menu_class'     => 'social-links-menu',
									'depth'          => 1,
								) );
							?>
						</nav><!-- .social-navigation -->
					<?php endif; ?>					
				</div>
			</div>
			<div class="wrap">
				<?php
				get_template_part( 'template-parts/footer/footer', 'widgets' );

				//get_template_part( 'template-parts/footer/site', 'info' );
				?>
			</div><!-- .wrap -->
		</footer><!-- #colophon -->
	</div><!-- .site-content-contain -->
</div><!-- #page -->
<?php wp_footer(); ?>

</body>
</html>
