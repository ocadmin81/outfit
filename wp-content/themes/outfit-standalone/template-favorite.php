<?php
/**
 * Template name: Favorite Ads
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiera
 * @since classiera 1.0
 */
if ( !is_user_logged_in() ) {
	wp_redirect( outfit_get_page_url('login') );
	exit;
}
global $current_user, $user_id;
global $redux_demo;
global $paged, $wp_query, $wp, $post;

$currentUser = $current_user = wp_get_current_user();
$userId = $user_id = $currentUser->ID;

if (isset($_POST['favorite'])) {
	if (!empty($userId)) {
		outfit_insert_author_favorite($userId, $_POST['post_id']);
	}
}
else if (isset($_POST['unfavorite'])) {
	if (!empty($userId)) {
		outfit_delete_author_favorite($userId, $_POST['post_id']);
	}
}

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$perPage = 20;

$args = array(
	'post_type' => OUTFIT_AD_POST_TYPE,
	'post_status' => 'publish',
	'posts_per_page' => $perPage,
	'paged' => $paged
);

$wp_query= null;
$wp_query = new WP_Query();

$favoritearray = outfit_authors_all_favorite($userId);

$args['post__in'] = $favoritearray;

if(!empty($favoritearray)) {
	// The Query
	$wp_query = new WP_Query( $args );
}
get_header(); ?>
<section class="user-pages section-gray-bg wish-page">
	<div class="wrap ad-page user-page">
		<div class="row">
			<div class="col-lg-3 col-md-4">
				<?php get_template_part( 'templates/profile/profile-sidebar' );?>
			</div><!--col-lg-3-->
			<div class="col-lg-9 col-md-8 user-content-height">
				<?php if($_POST){?>
					<div class="alert alert-success" role="alert">
						<?php echo esc_html( $message ); ?>
					</div>
				<?php } ?>
				<div class="user-detail-section section-bg-white">
					<div class="user-ads favorite-ads">
						<h1 class="user-detail-section-heading text-uppercase"><?php esc_html_e("WISHLIST", 'outfit-standalone') ?></h1>
						<div class="my-ads products">
							<div class="row">
								<?php if($wp_query->have_posts()): ?>
									<?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
										<div class="col-lg-4 col-md-4 col-sm-6 item">
											<div class="classiera-box-div classiera-box-div-v1">
												<figure class="clearfix">

													<form method="post" class="fav-form clearfix">
														<input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>"/>
														<button type="submit" value="unfavorite" name="unfavorite" class="watch-later text-uppercase">
															<span><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/post_image_remove.png" /></span>
														</button>
													</form>
													<div class="premium-img">
														<a href="<?php the_permalink(); ?>">
															<?php
															$postPrice = get_post_meta($post->ID, POST_META_PRICE, true);
															$postAuthorId = $post->post_author;
															$postAuthorName = getAuthorFullNameCat($postAuthorId);

															$authorAvatarUrl = outfit_get_user_picture($postAuthorId, 50);
															$postBrand = implode(',', getPostTermNames($post->ID, 'brands'));
															if( has_post_thumbnail()){
																$imageurl = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'classiera-370');
																$thumb_id = get_post_thumbnail_id($post->ID);
																?>
																<img class="img-responsive" src="<?php echo esc_url( $imageurl[0] ); ?>" alt="<?php the_title() ?>">
																<?php
															}else{
																?>
																<img class="img-responsive" src="<?php echo get_template_directory_uri() . '/assets/images/nothumb.png' ?>" alt="No Thumb"/>
																<?php
															}
															?>
														</a>
														<div class="cat-wish-brand">
															<div class="cat-wish">&nbsp;</div>
															<div class="ad-brand"><?php echo esc_attr($postBrand); ?></div>
														</div>
													</div><!--premium-img-->
													<div class="au-price">
														<div class="au">
															<a href="<?php echo get_author_posts_url( $postAuthorId ); ?>">
																<img style="height: 30px;" class="" src="<?php echo esc_url($authorAvatarUrl); ?>" alt="<?php echo esc_attr($postAuthorName); ?>">
																<?php echo esc_attr($postAuthorName); ?>
															</a>
														</div>												
														<?php if(!empty($postPrice)){?>
															<div class="price">
																<span class="">
																	<?php
																	if(is_numeric($postPrice)){
																		echo '&#8362; ' .  $postPrice;
																	}else{
																		echo esc_attr( $postPrice );
																	}
																	?>
																</span>
															</div>
														<?php } ?>
													</div>

												</figure>
											</div><!--classiera-box-div-->
										</div><!--col-lg-4-->
									<?php endwhile; ?>
								<?php else: ?>
									<div class="no-items">
										<?php echo do_shortcode("[do_widget id=text-12]"); ?>
									</div>
								<?php endif; ?>
							</div>
							<?php outfit_pagination(); ?>
							<?php wp_reset_query(); ?>
						</div>
					</div><!--user-ads user-profile-settings-->
				</div><!--user-detail-section-->
			</div><!--col-lg-9-->
		</div><!--row-->
	</div><!--container-->
</section><!--user-pages section-gray-bg-->



<?php get_footer(); ?>