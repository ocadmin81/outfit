<?php
/**
 * Template name: Profile Page
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiera
 * @since classiera
 */
global $redux_demo;
global $current_user;
$currentUserId = '';
$author = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
wp_get_current_user();
$currentUserId = $current_user->ID;
$authorId = $author->ID;
$authorName = get_the_author_meta('display_name', $authorId );
if (empty($authorName)) {
	$authorName = get_the_author_meta('user_nicename', $authorId );
}
if (empty($authorName)) {
	$authorName = get_the_author_meta('user_login', $authorId );
}
$authorAvatarUrl = outfit_get_user_picture($authorId, 130);

$authorEmail = get_the_author_meta('user_email', $authorId);
$authorPhone = get_the_author_meta(USER_META_PHONE, $authorId);
$authorAbout = get_the_author_meta(USER_META_ABOUT, $authorId);
$authorPreferredHours = get_the_author_meta(USER_META_PREFERRED_HOURS, $authorId);

// post location
$postLocation = OutfitLocation::toAssoc(get_the_author_meta(USER_META_PRIMARY_ADDRESS, $authorId));
$postAddress = '';
if (null !== $postLocation && isset($postLocation['address'])) {
	$postAddress = $postLocation['address'];
}
// post secondary location
$postLocation2 = OutfitLocation::toAssoc(get_post_meta(USER_META_SECONDARY_ADDRESS, $authorId));
$postSecAddress = '';
if (null !== $postLocation2 && isset($postLocation2['address'])) {
	$postSecAddress = $postLocation2['address'];
}

if (isset($_POST['follow'])) {
	if (!empty($currentUserId)) {
		outfit_insert_author_follower($_POST['author_id'], $currentUserId);
	}
}
else if (isset($_POST['unfavorite'])) {
	if (!empty($currentUserId)) {
		outfit_delete_author_follower($_POST['author_id'], $currentUserId);
	}
}

global $paged;
global $post;
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$perPage = 10;

global $currentUserFavoriteAds;
$currentUserFavoriteAds = outfit_authors_all_favorite($currentUserId);

$authorPosts = getOutfitAdsByAuthor($authorId, $paged, $perPage);

get_header();


?>
<section class="author-box">
	<div class="container border author-box-bg">
		<div class="row">
			<div class="col-lg-12">
				<div class="row no-gutter removeMargin border-bottom author-first-row">
					<div class="col-lg-7 col-sm-7">
						<div class="author-info">
							<div class="media">
								<div class="">
									<img class="media-object" src="<?php echo esc_url($authorAvatarUrl); ?>" alt="<?php echo get_the_author_meta('display_name', $userId ); ?>">
								</div><!---->
								<div class="media-body">
									<h5 class="media-heading text-uppercase">
										<?php echo esc_attr($authorName); ?>
									</h5>
									<div><?php echo esc_html($authorAbout); ?></div>
									<div>
										<?php if (!empty($userId) && $userId != $authorId) { ?>
											<form method="post" class="classiera_follow_user">
												<input type="hidden" name="author_id" value="<?php echo esc_attr($author_id); ?>"/>
												<?php if (!outfit_is_favorite_author($authorId, $userId)) { ?>
													<input type="submit" name="follow" value="<?php esc_html_e( 'Follow', 'outfit-standalone' ); ?>" />
												<?php } else { ?>
													<input type="submit" name="unfollow" value="<?php esc_html_e( 'Remove from favorites', 'outfit-standalone' ); ?>" />
												<?php } ?>
											</form>
											<div class="clearfix"></div>

										<?php } ?>
									</div>
								</div><!--media-body-->
							</div><!--media-->
						</div><!--author-info-->
					</div><!--col-lg-7-->
					<div class="col-lg-5 col-sm-5">
						<div class="author-contact-details">
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<h5 class="text-uppercase"><?php esc_html_e('Contact Details', 'outfit-standalone') ?> :</h5>
									<ul class="list-unstyled fa-ul c-detail">
										<?php if(!empty($authorPhone)){?>
											<li>
												<span class=""><?php echo esc_html($authorPhone);?></span>
											</li>
										<?php } ?>
										<?php if(!empty($authorPreferredHours)){?>
											<li>
												<span><?php esc_html_e('Best time to call', 'outfit-standalone') ?>:</span>
											<span>
												<?php echo esc_html($authorPreferredHours);?>
											</span>
											</li>
										<?php } ?>
									</ul>
								</div>
								<div class="col-md-6 col-sm-12">
									<h5 class="text-uppercase"><?php esc_html_e('Collect points', 'outfit-standalone') ?> :</h5>
									<ul class="list-unstyled fa-ul c-detail">
										<li>
										<span>
											<?php if (!empty($postAddress)) { ?>
												<?php echo esc_html($postAddress);?>
											<?php } ?>
										</span><br>
										<span>
											<?php if (!empty($postSecAddress)) { ?>
												<?php echo esc_html($postSecAddress);?>
											<?php } ?>
										</span>
										</li>
									</ul>
								</div>
							</div>
						</div>


					</div><!--col-lg-5 col-sm-5-->
				</div><!--row-->
			</div><!--col-lg-12-->
		</div><!--row-->
	</div><!--container border author-box-bg-->
</section><!--author-box-->

<section class="inner-page-content border-bottom">
	<section class="classiera-advertisement advertisement-v1">
		<div class="section-light-bg">
			<div class="view-head">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
                            <h5><?php esc_html_e( 'Products', 'outfit-standalone' ); ?></h5>
                        </div>
					</div><!--row-->
				</div><!--container-->
			</div><!--view-head-->
			<div class="tab-content">
				<div class="container">
					<div class="row">
					<?php foreach ( $authorPosts as $post ) : setup_postdata( $post ); ?>
						<?php get_template_part('templates/loops/product'); ?>
					<?php
					endforeach;
					wp_reset_postdata();
					?>
					</div><!--row-->

				</div><!--container-->
					

				
				<?php wp_reset_query(); ?>
			</div><!--tab-content-->
		</div><!--tab-divs section-light-bg-->
	</section><!--classiera-advertisement advertisement-v1-->
</section><!--inner-page-content-->


<?php get_footer(); ?>