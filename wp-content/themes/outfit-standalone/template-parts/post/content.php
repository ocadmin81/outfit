<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.2
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if ( is_sticky() && is_home() ) :
		echo twentyseventeen_get_svg( array( 'icon' => 'thumb-tack' ) );
	endif;
	?>
	<?php if ( '' !== get_the_post_thumbnail() && ! is_single() ) : ?>
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'twentyseventeen-featured-image' ); ?>
			</a>
		</div><!-- .post-thumbnail -->
	<?php endif; ?>	
	<header class="entry-header">
		<?php
			if ( 'post' === get_post_type() && !is_single()) {
				echo '<div class="entry-meta">';
					echo twentyseventeen_posted_on();
					twentyseventeen_edit_link();
				echo '</div><!-- .entry-meta -->';
			};

			if ( is_single() ) {
				the_title( '<h1 class="entry-title">', '</h1>' );
			} elseif ( is_front_page() && is_home() ) {
				the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
			} else {
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			}
		?>
		<?php if ( 'post' === get_post_type() && is_single()): ?>
			<div class="entry-meta">
				<?php twentyseventeen_posted_on(); ?>
				<div class="post-share">
					<ul>
						<li class="mail"><a target="_blank" rel="noopener noreferrer nofollow" href="mailto:enteryour@addresshere.com?subject=<?php the_title(); ?>&body=<?php echo strip_tags(the_excerpt()); ?>"><i class="fa fa-envelope" aria-hidden="true"></i></a></li>
						<li class="pin"><a target="_blank" rel="noopener noreferrer nofollow" href="http://pinterest.com/pin/create/button/?url=<?php echo esc_url( get_permalink() ); ?>&media=<?php the_post_thumbnail_url() ?>&description=<?php echo strip_tags(the_excerpt()); ?>"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a></li>
						<li class="tweet"><a rel="noopener noreferrer nofollow" href="<?php echo 'http://twitter.com/home?status=' . the_title() . '+' . esc_url( get_permalink() ); ?>" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
						<li class="face"><a href="http://www.facebook.com/sharer.php?s=100&p[url]=<?php echo esc_url( get_permalink() ); ?>&p[images][0]=<?php the_post_thumbnail_url() ?>&p[title]=<?php the_title(); ?>&p[summary]=<?php echo strip_tags(the_excerpt()); ?>" rel="noopener noreferrer nofollow" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
					</ul>
				</div>
			</div>
		<?php endif; ?>
		
	</header><!-- .entry-header -->
	
	<div class="entry-content">
		<?php if ( is_single() ): ?>
			<?php
				the_content( sprintf(
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'twentyseventeen' ),
					get_the_title()
				) );
			?>
		<?php else: ?>
			<?php the_excerpt(); ?>
		<?php endif; ?>
		<?php
		/* translators: %s: Name of current post */

		wp_link_pages( array(
			'before'      => '<div class="page-links">' . __( 'Pages:', 'twentyseventeen' ),
			'after'       => '</div>',
			'link_before' => '<span class="page-number">',
			'link_after'  => '</span>',
		) );
		?>
	</div><!-- .entry-content -->
	<?php if ( !is_single() ): ?>
		<div class="to-article"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'הצג כתבה', 'outfit-standalone' ); ?></a></div>
	<?php endif; ?>
	<?php
	if ( is_single() ) {
		//twentyseventeen_entry_footer();
	}
	?>

</article><!-- #post-## -->
