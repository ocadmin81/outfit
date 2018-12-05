<?php
/**
 * Template name: Perfit Home
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiera
 * @since classiera 1.0
 */
$loggedIn = is_user_logged_in();
global $current_user, $user_id;
global $redux_demo;
global $paged, $wp_query, $wp, $post;
global $catObj;
$catslugs = [
	'clothing' => 'Shop for clothes',
	'toys' => 'Shop for toys',
	'strollers' => 'Shop for strollers'
];

$currentUser = $current_user = wp_get_current_user();
$userId = $user_id = $currentUser->ID;

$postsRows = array();

if ($loggedIn) {
	/*$catSlugsKeys = array_keys($catslugs);
	$catSlugsIndex = 0;*/
	/*if (empty($recentCatsPosts)) {
		if ($catSlugsIndex < count($catSlugsKeys)) {
			$postsRows[] = outfit_get_category_posts($catSlugsKeys[$catSlugsIndex], 5);
			$catSlugsIndex++;
		}
	}
	else {

	}*/
	$postsRows[] = $recentCatsPosts = outfit_get_recent_cats_posts($currentUser->ID, 5);

	$searchPrefAge = get_user_meta($userId, USER_META_SEARCH_PREF_AGE, true);
	$searchPrefLocation = OutfitLocation::createFromJSON(
		get_user_meta($userId, USER_META_SEARCH_PREF_LOCATION, true));

	$postsByAgeAndLocation = array();
	if (null != $searchPrefLocation || !empty($searchPrefAge)) {
		$postsByAgeAndLocation = outfit_get_posts_by_age_and_location($searchPrefAge, $searchPrefLocation, 5);
	}
	$postsRows[] = $postsByAgeAndLocation;
	$postsRows[] = $favSellersPosts = outfit_get_posts_of_favorite_sellers($currentUser->ID, 5);
	$postsRows[] = $wishlist = outfit_get_wishlist_posts($currentUser->ID, 5);
	$postsRows[] = $recentPosts = outfit_get_recent_products($currentUser->ID, 20);

	$catSlugsKeys = array_keys($catslugs);
	$catSlugsIndex = 0;
	$catSlugsCount = count($catSlugsKeys);
	foreach ($postsRows as $i => $postRow) {
		if (empty($postRow) && $catSlugsIndex < $catSlugsCount) {
			$postRows[$i] = outfit_get_category_posts($catSlugsKeys[$catSlugsIndex], 5);
			$catSlugsIndex++;
		}
	}
}
else {
	foreach ($catslugs as $catslug) {
		$postsRows[] = outfit_get_category_posts($catslug, 5);
	}
}

foreach ($postsRows as $i => $postRow) { ?>

	<?php if (count($postRow) > 0) { ?>
		<div class="row home-cat-row">
			<div class="col-md-12 user-content-height">
				<div class="user-detail-section section-bg-white">
					<div class="user-ads favorite-ads">
						<div class="my-ads products">
							<div class="row">
								<?php foreach ( $postRow as $post ) : setup_postdata( $post ); ?>
									<div class="col-lg-4 col-md-4 col-sm-6 item">
										<div class="classiera-box-div classiera-box-div-v1">
											<figure class="clearfix">

												<div class="premium-img">
													<a href="<?php the_permalink(); ?>">
														<?php
														$postPrice = get_post_meta($post->ID, POST_META_PRICE, true);
														$postAuthorId = $post->post_author;
														$postAuthorName = getAuthorFullName($postAuthorId);

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
													<div class="ad-brand"><?php echo esc_attr($postBrand); ?></div>
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
								<?php endforeach; ?>
							</div>
							<?php wp_reset_postdata(); ?>
						</div>
					</div><!--user-ads user-profile-settings-->
				</div><!--user-detail-section-->
			</div><!--col-lg-9-->
		</div><!--row-->
	<?php } //if ($wp_query->have_posts()) ?>

<?php } // foreach ($postsRows as $i => $postRow) ?>



