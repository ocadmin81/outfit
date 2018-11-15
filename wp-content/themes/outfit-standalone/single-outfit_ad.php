<?php
/**
 *
 * The template for displaying all single outfit ads
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	<section class="inner-page-content single-post-page">
		<div class="container">

			<?php
			$postPhone = get_post_meta($post->ID, POST_META_PHONE, true);
			$postPrice = get_post_meta($post->ID, POST_META_PRICE, true);
			$postPreferredHours = get_post_meta($post->ID, POST_META_PREFERRED_HOURS, true);
			$postLocation = json_decode(get_post_meta($post->ID, POST_META_LOCATION, true), true);
			$postAddress = '';
			if (null !== $postLocation && isset($postLocation['address'])) {
				$postAddress = $postLocation['address'];
			}
			?>
			<?php if ( get_post_status ( $post->ID ) == 'pending' ) {?>
				<div class="alert alert-info" role="alert">
					<p>
						<strong><?php esc_html_e('Congratulation!', 'outfit-standalone') ?></strong> <?php esc_html_e('Your Ad has submitted and pending for review.', 'outfit-standalone') ?>
					</p>
				</div>
			<?php } ?>
			<div class="row">
				<div class="col-md-3 col-sm-12">

				</div>
				<div class="col-md-4 col-sm-12">
					<h4 class="text-uppercase">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						<?php
						if (
						($post->post_author == $current_user->ID && get_post_status ( $post->ID ) == 'publish') ||
						current_user_can('administrator')
						):
						?>
							<a href="<?php echo esc_url($edit_post); ?>" class="edit-post btn btn-sm btn-default">
								<i class="far fa-edit"></i>
								<?php esc_html_e( 'Edit Post', 'classiera' ); ?>
							</a>
						<?php
						endif;
						?>
						<!--Edit Ads Button-->
					</h4>
					<div class="post-price">
						<h4>
							<?php
							if(is_numeric($postPrice)){
								echo '&#8362; ' . $postPrice;
							} else {
								echo esc_attr($postPrice);
							}
							?>
						</h4>
					</div>
				</div>
				<div class="col-md-5 col-sm-12">
					<!-- single post carousel-->
					<?php
					$attachments = get_children(array('post_parent' => $post->ID,
							'post_status' => 'inherit',
							'post_type' => 'attachment',
							'post_mime_type' => 'image',
							'order' => 'ASC',
							'orderby' => 'menu_order ID'
						)
					);
					?>
					<?php if ( has_post_thumbnail() || !empty($attachments)){?>
						<div id="single-post-carousel" class="carousel slide single-carousel" data-ride="carousel" data-interval="3000">

							<!-- Wrapper for slides -->
							<div class="carousel-inner" role="listbox">
								<?php
								if(empty($attachments)){
									if ( has_post_thumbnail()){
										$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
										?>
										<div class="item active">
											<img class="img-responsive" src="<?php echo esc_url($image[0]); ?>" alt="<?php the_title(); ?>">
										</div>
										<?php
									}else{
										$image = get_template_directory_uri().'/assets/images/nothumb.png';
										?>
										<div class="item active">
											<img class="img-responsive" src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
										</div>
										<?php
									}
								}else{
									$count = 1;
									foreach($attachments as $att_id => $attachment){
										$full_img_url = wp_get_attachment_url($attachment->ID);
										?>
										<div class="item <?php if($count == 1){ echo "active"; }?>">
											<img class="img-responsive" src="<?php echo esc_url($full_img_url); ?>" alt="<?php the_title(); ?>">
										</div>
										<?php
										$count++;
									}
								}
								?>
							</div>
							<!-- slides number -->
							<div class="num">
								<i class="fa fa-camera"></i>
								<span class="init-num"><?php esc_html_e('1', 'classiera') ?></span>
								<span><?php esc_html_e('of', 'classiera') ?></span>
								<span class="total-num"></span>
							</div>
							<!-- Left and right controls -->
							<div class="single-post-carousel-controls">
								<a class="left carousel-control" href="#single-post-carousel" role="button" data-slide="prev">
									<span class="fa fa-chevron-left" aria-hidden="true"></span>
									<span class="sr-only"><?php esc_html_e('Previous', 'classiera') ?></span>
								</a>
								<a class="right carousel-control" href="#single-post-carousel" role="button" data-slide="next">
									<span class="fa fa-chevron-right" aria-hidden="true"></span>
									<span class="sr-only"><?php esc_html_e('Next', 'classiera') ?></span>
								</a>
							</div>
							<!-- Left and right controls -->
						</div>
					<?php } ?>
					<!-- single post carousel-->
				</div>
			</div>
		</div>
	</section>

<?php endwhile; ?>
<?php get_footer();