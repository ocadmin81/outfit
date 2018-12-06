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
$authorName = getAuthorFullName($authorId);
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
	if (!empty($currentUserId) && !empty($_POST['author_id'])) {
		outfit_insert_author_follower(intval($_POST['author_id']), $currentUserId);
	}
}
else if (isset($_POST['unfollow'])) {
	if (!empty($currentUserId) && !empty($_POST['author_id'])) {
		outfit_delete_author_follower(intval($_POST['author_id']), $currentUserId);
	}
}

if (isset($_POST['favorite'])) {
	if (!empty($currentUserId)) {
		outfit_insert_author_favorite($currentUserId, $_POST['post_id']);
	}
}
else if (isset($_POST['unfavorite'])) {
	if (!empty($currentUserId)) {
		outfit_delete_author_favorite($currentUserId, $_POST['post_id']);
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
<section class="author-box author-page">
	<div class="wrap author-box-bg">
		<div class="row">
			<div class="col-lg-12">
				<div class="row no-gutter removeMargin border-bottom author-first-row">
					<div class="col-lg-7 col-sm-7">
						<div class="author-info">
							<div class="media">
								<div class="media-img">
									<img class="media-object" src="<?php echo esc_url($authorAvatarUrl); ?>" alt="<?php echo get_the_author_meta('display_name', $userId ); ?>">
								</div><!---->
								<div class="media-body">
									<h5 class="media-heading text-uppercase">
										<?php echo esc_attr($authorName); ?>
									</h5>
									<div class="desc"><?php echo esc_html($authorAbout); ?></div>
									<div>
										<?php if (!empty($currentUserId) && $currentUserId != $authorId) { ?>
											<?php
											//global $wpdb;
											//$q = "SELECT * FROM {$wpdb->prefix}author_follower WHERE follower_id = $currentUserId AND author_id = $authorId";
											//$results = $wpdb->get_results( $q );
											//print_r($results);
											?>
											<form method="post" class="classiera_follow_user">
												<input type="hidden" name="author_id" value="<?php echo esc_attr($authorId); ?>"/>
												<?php if (!outfit_is_favorite_author($authorId, $currentUserId)) { ?>
													<button type="submit" name="follow"><span><?php esc_html_e( 'הוספה למוכרים אהובים', 'outfit-standalone' ); ?></span></button>
												<?php } else { ?>
													<button type="submit" name="unfollow"><span><?php esc_html_e( 'הסרה ממוכרים אהובים', 'outfit-standalone' ); ?></span></button>
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
									<h5 class="text-uppercase"><?php esc_html_e('נקודות איסוף', 'outfit-standalone') ?></h5>
									<ul class="list-unstyled c-detail">
										<li>
											<span>
												<?php if (!empty($postAddress)) { ?>
													<?php echo esc_html($postAddress);?>
												<?php } ?>
											</span>
										</li>
										<li>
											<span>
												<?php if (!empty($postSecAddress)) { ?>
													<?php echo esc_html($postSecAddress);?>
												<?php } ?>
											</span>
										</li>
									</ul>
								</div>							
								<div class="col-md-6 col-sm-12">
									<h5 class="text-uppercase"><?php esc_html_e('ליצירת קשר עם המוכר/ת', 'outfit-standalone') ?></h5>
									<ul class="list-unstyled p-detail">
										<?php if(!empty($authorPhone)){?>
											<li>
												<a href="tel:<?php echo esc_html($authorPhone);?>"><span class="a-phone"><?php echo esc_html($authorPhone);?></span></a>
											</li>
										<?php } ?>
										<?php if(!empty($authorPreferredHours)){?>
											<li class="s-time">
												<span><?php esc_html_e('*שעה נוחה להתקשרות', 'outfit-standalone') ?>:</span>
											</li>
											<li>
												<span><?php echo esc_html($authorPreferredHours);?></span>
											</li>
										<?php } ?>
									</ul>
								</div>
							</div>
						</div>


					</div><!--col-lg-5 col-sm-5-->
				</div><!--row-->
			</div><!--col-lg-12-->
		</div><!--row-->
	</div><!--container border author-box-bg-->
		<div class="wrap author-box-bg seller-ads products">
			<div class="view-head">
				<div class="row">
					<div class="col-md-12">
						<h5><?php esc_html_e( 'Products', 'outfit-standalone' ); ?></h5>
					</div>
				</div><!--row-->
			</div><!--view-head-->
			<div class="tab-content">
				<div class="row">
				<?php foreach ( $authorPosts as $post ) : setup_postdata( $post ); ?>
					<?php get_template_part('templates/loops/product'); ?>
				<?php
				endforeach;
				wp_reset_postdata();
				?>
				</div><!--row-->			
				<?php wp_reset_query(); ?>
			</div><!--tab-content-->
		</div>
</section><!--inner-page-content-->


<?php get_footer(); ?>