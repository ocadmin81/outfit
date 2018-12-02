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

if (isset($_GET['follow_id'])) {
	if (!empty($userId)) {
		outfit_insert_author_follower(intval($_GET['follow_id']), $userId);
	}
}
else if (isset($_GET['unfollow_id'])) {
	if (!empty($userId)) {
		outfit_delete_author_follower(intval($_GET['unfollow_id']), $userId);
	}
}

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$perPage = 20;

$favoritearray = outfit_authors_all_favorite_sellers($userId);



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
							<?php esc_html_e("מוכרים אהובים", 'outfit-standalone') ?>
						</h1>
						<div class="my-ads">
							<div class="row">
								<?php foreach ($favoritearray as $aid): ?>
									<?php
									if (empty($aid) || !outfit_is_user_exists($aid)) continue;
									global $wp_rewrite;
									$unfollowUrl = ($wp_rewrite->permalink_structure == '')? $pagepermalink."&unfollow_id=".$post->ID : $pagepermalink."?unfollow_id=".$post->ID;
									$authorName = getAuthorFullName($aid);
									?>
									<div class="col-lg-4 col-md-4 col-sm-6 match-height item">
										<div class="classiera-box-div classiera-box-div-v1">
											<figure class="clearfix">

												<div class="remove-post-button">
													<a href="<?php echo esc_url($unfollowUrl) ?>"
													<span class="remove-post"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/post_image_remove.png" /></span>
													<input type="hidden" name="" value="<?php echo esc_attr($post->ID); ?>">
												</div><!--remove-post-button-->
												<div class="premium-img">
													<a href="<?php echo get_author_posts_url($aid); ?>">
														<?php
														$authorAvatarUrl = outfit_get_user_picture($aid);
														if( !empty($authorAvatarUrl)){ ?>
															<img class="img-responsive" src="<?php echo esc_url( $authorAvatarUrl ); ?>" alt="<?php echo esc_attr($postAuthorName); ?>">
															<?php
														}else{
															?>
															<img class="img-responsive" src="<?php echo get_template_directory_uri() . '/assets/images/nothumb.png' ?>" alt="No Thumb"/>
															<?php
														}
														?>
													</a>

												</div><!--premium-img-->
												<div>
													<span>
														<a href="<?php echo get_author_posts_url( $aid ); ?>"><?php echo esc_attr($authorName); ?></a></span>
												</div>

											</figure>
										</div><!--classiera-box-div-->
									</div><!--col-lg-4-->
								<?php endforeach; ?>
							</div>
						</div>
					</div><!--user-ads user-profile-settings-->
				</div><!--user-detail-section-->
			</div><!--col-lg-9-->
		</div><!--row-->
	</div><!--container-->
</section><!--user-pages section-gray-bg-->



<?php get_footer(); ?>