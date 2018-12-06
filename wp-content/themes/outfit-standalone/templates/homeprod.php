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
require_once 'Mobile_Detect_Prods.php';
$detect = new Mobile_Detect_Prods;
$count = 5;
if ($detect->isMobile() && !$detect->isTablet())
	$count = 4;
$catslugs = [
	'clothing' => 'Shop for clothes',
	'toys' => 'Shop for toys',
	'and-safety-chairs-strollers' => 'Shop for strollers'
];

$currentUser = $current_user = wp_get_current_user();
$userId = $user_id = $currentUser->ID;

$postsRows = array();

if ($loggedIn) {

	$postsRows[] = array(
		'title' => 'New in',
		'data' => $recentCatsPosts = outfit_get_recent_cats_posts($currentUser->ID, $count)
	);

	/*$postsRows[] = array(
		'title' => '',
		'data' =>
	);*/

	$searchPrefAge = get_user_meta($userId, USER_META_SEARCH_PREF_AGE, true);
	$searchPrefLocation = OutfitLocation::createFromJSON(
		get_user_meta($userId, USER_META_SEARCH_PREF_LOCATION, true));

	$postsByAgeAndLocation = array();
	if (null != $searchPrefLocation || !empty($searchPrefAge)) {
		$postsByAgeAndLocation = outfit_get_posts_by_age_and_location($searchPrefAge, $searchPrefLocation, $count5);
	}
	$postsRows[] = array(
		'title' => 'לפי גיל ואיזור',
		'data' => $postsByAgeAndLocation
	);
	$postsRows[] = array(
		'title' => 'חדש מאנשים שאת אוהבת',
		'data' => $favSellersPosts = outfit_get_posts_of_favorite_sellers($currentUser->ID,$count5)
	);
	$postsRows[] = array(
		'title' => 'אהבת אותם לאחרונה',
		'data' => $wishlist = outfit_get_wishlist_posts($currentUser->ID, $count)
	);
	$postsRows[] = array(
		'title' => 'צפית בהם לאחרונה',
		'data' => $recentPosts = outfit_get_recent_products($currentUser->ID, $count)
	);

	$catSlugsKeys = array_keys($catslugs);
	$catSlugsIndex = 0;
	$catSlugsCount = count($catSlugsKeys);
	foreach ($postsRows as $i => $postRow) {
		if (empty($postRow['data']) && $catSlugsIndex < $catSlugsCount) {
			$postRows[$i]['data'] = outfit_get_category_posts($catSlugsKeys[$catSlugsIndex], $count);
			$postRows[$i]['title'] = $catslugs[$catSlugsKeys[$catSlugsIndex]];
			$catSlugsIndex++;
		}
	}
}
else {
	foreach ($catslugs as $catslug => $title) {
		$postsRows[] = array(
			'title' => $title,
			'data' => outfit_get_category_posts($catslug, $count),
			'slug' => $catslug
		);
	}
}

foreach ($postsRows as $i => $postRow) { ?>

	<?php if (count($postRow['data']) > 0) { ?>
	<div class="home-cats <?php if(!$loggedIn): ?>guest<?php endif; ?>">
		<div class="elementor-widget-container">
			<div class="elementor-text-editor elementor-clearfix">
				<h3 style="text-align: center;"><?php echo esc_html($postRow['title']) ?></h3>
			</div>
		</div>
		<div class="row home-cat-row">
			<div class="col-md-12 user-content-height">
				<div class="user-detail-section section-bg-white">
					<div class="user-ads favorite-ads">
						<div class="my-ads products">
							<div class="row">
								<?php foreach ( $postRow['data'] as $post ) : setup_postdata( $post ); ?>
									<div class="col-lg-4 col-md-4 col-sm-6 item">
										<div class="classiera-box-div classiera-box-div-v1">
											<figure class="clearfix">

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
																	echo $postPrice. ' &#8362;';
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
			<?php if (!$loggedIn): ?>
				<div class="cat-link">
					<a href="category/<?php echo $postRow['slug']; ?>?outfit_ad">הצג עוד</a>
				</div>
			<?php endif; ?>		
	</div>
	<?php } //if ($wp_query->have_posts()) ?>

<?php } // foreach ($postsRows as $i => $postRow) ?>



