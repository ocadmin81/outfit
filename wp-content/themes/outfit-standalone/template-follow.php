<?php
/**
 * Template name: Favorite Sellers
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

wp_get_current_user();
$currentUser = $current_user;
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

$pagepermalink = get_permalink($post->ID);

$favoritearray = outfit_authors_all_favorite_sellers($userId);

get_header(); ?>

<section class="user-pages section-gray-bg fa-sellers">
	<div class="wrap ad-page user-page user-follow">
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
					<div class="user-prods favorite-ads">
						<h1 class="user-detail-section-heading text-uppercase <?php if(!$favoritearray): ?>center<?php endif; ?>"><?php esc_html_e("מוכרים אהובים", 'outfit-standalone') ?></h1>
						<div class="my-prods">
							<div class="row">
								<?php if($favoritearray): ?>
									<?php foreach ($favoritearray as $aid): ?>
										<?php
										if (empty($aid) || !outfit_is_user_exists($aid)) continue;
										global $wp_rewrite;
										$unfollowUrl = ($wp_rewrite->permalink_structure == '')? $pagepermalink."&unfollow_id=".$aid : $pagepermalink."?unfollow_id=".$aid;
										$authorName = getAuthorFullName($aid);
										?>
										<div class="col-lg-4 col-md-4 col-sm-6 item">
											<div class="classiera-box-div classiera-box-div-v1">
												<figure class="clearfix">

													<div class="remove-post-button">
														<a href="<?php echo esc_url($unfollowUrl) ?>">
														<span class="remove-post">&nbsp;</span>
														<input type="hidden" name="" value="<?php echo esc_attr($post->ID); ?>">
													</div><!--remove-post-button-->
													<div class="premium-img">
														<a href="<?php echo outfit_get_author_posts_url($aid); ?>">
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
															<a href="<?php echo outfit_get_author_posts_url( $aid ); ?>"><?php echo esc_attr($authorName); ?></a></span>
													</div>

												</figure>
											</div><!--classiera-box-div-->
										</div><!--col-lg-4-->
									<?php endforeach; ?>
								<?php else: ?>
									<div class="no-items">
										<?php echo do_shortcode("[do_widget id=text-14]"); ?>
									</div>
								<?php endif; ?>										
							</div>
						</div>
					</div><!--user-prods user-profile-settings-->
				</div><!--user-detail-section-->
			</div><!--col-lg-9-->
		</div><!--row-->
	</div><!--container-->
</section><!--user-pages section-gray-bg-->



<?php get_footer(); ?>