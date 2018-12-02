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

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$perPage = 20;

$pagepermalink = get_permalink($post->ID);

if(isset($_GET['delete_id'])){
	$deleteId = $_GET['delete_id'];
	$deletePost = get_post($deleteId);
	if ($deletePost && ($deletePost->post_author == $userId || current_user_can('administrator'))) {
		wp_trash_post($deleteId);
	}
}

$args = array(
	'post_type' => OUTFIT_AD_POST_TYPE,
	'author' => $userId,
	'post_status' => array( 'publish', 'pending' ),
	'posts_per_page' => $perPage,
	'paged' => $paged
);

$wp_query= null;
// The Query
$wp_query = new WP_Query( $args );
get_header(); ?>

<section class="user-pages section-gray-bg">
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
						<h1 class="user-detail-section-heading text-uppercase">
							<?php esc_html_e("המודעות שלי", 'outfit-standalone') ?>
						</h1>
						<div class="my-ads products">
							<div class="row">
								<?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
									<div class="col-lg-4 col-md-4 col-sm-6 item">
										<div class="classiera-box-div classiera-box-div-v1">
											<figure class="clearfix">

												<div class="premium-img">
													<a href="<?php the_permalink(); ?>">
														<?php
														$postPrice = get_post_meta($post->ID, POST_META_PRICE, true);
														$postAuthorId = $post->post_author;
														$postAuthorName = getAuthorFullName($postAuthorId);
														$editPostUrl = outfit_edit_ad_url($post->ID);
														global $wp_rewrite;
														$deletePostUrl = ($wp_rewrite->permalink_structure == '')? $pagepermalink."&delete_id=".$post->ID : $pagepermalink."?delete_id=".$post->ID;

														//$authorAvatarUrl = outfit_get_user_picture($postAuthorId, 50);
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
													<div class="ad-brand">castro<?php echo esc_attr($postBrand); ?></div>
												</div><!--premium-img-->
												<div class="remove-post-button">
													<a href="<?php echo esc_url($deletePostUrl) ?>">
														<span class="remove-post"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/post_image_remove.png" /></span>
														<input type="hidden" name="" value="<?php echo esc_attr($post->ID); ?>">
													</a>
												</div><!--remove-post-button-->												
												<div class="edit-price">
													<div class="edit"><a href="<?php echo esc_url($editPostUrl); ?>"><?php echo esc_html_e( 'עריכה', 'outfit-standalone' ); ?></a></div>
													<?php //if(!empty($postPrice)){?>
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
													<?php //} ?>													
												</div>

											</figure>
										</div><!--classiera-box-div-->
									</div><!--col-lg-4-->
								<?php endwhile; ?>
							</div>
						</div>
							<?php outfit_pagination(); ?>
							<?php wp_reset_query(); ?>
					</div><!--user-ads user-profile-settings-->
				</div><!--user-detail-section-->
			</div><!--col-lg-9-->
		</div><!--row-->
	</div><!--container-->
</section><!--user-pages section-gray-bg-->



<?php get_footer(); ?>