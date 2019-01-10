<?php
/**
 * Template name: Perfit Home Products
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 */
$loggedIn = is_user_logged_in();
global $current_user, $user_id;
global $redux_demo;
global $paged, $wp_query, $wp, $post;
global $catObj;
require_once 'Mobile_Detect_Prods.php';
$detect = new Mobile_Detect_Prods;
$count = 5;
$isMobile = ($detect->isMobile() && !$detect->isTablet());
if ($isMobile)
	$count = 4;
$catslugs = [
	'clothing' => 'Shop for clothes',
	'toys' => 'Shop for toys',
	'stroller' => 'Shop for strollers'
];

wp_get_current_user();
$currentUser = $current_user;
$currentUserId = $userId = $user_id = $currentUser->ID;

if (isset($_POST['favorite'])) {
	if ($loggedIn) {
		outfit_insert_author_favorite($currentUserId, $_POST['post_id']);
	}
	else {
		$loginUrl = outfit_login_url_back('', 'favorite', $_POST['post_id']);

		?>
		<script type="text/javascript">
			window.location.href = '<?php echo $loginUrl; ?>';
		</script>
		<?php

		//wp_redirect($loginUrl);
		exit();
	}
}
else if (isset($_POST['unfavorite'])) {
	if (!empty($currentUserId)) {
		outfit_delete_author_favorite($currentUserId, $_POST['post_id']);
	}
}

$currentUserFavoriteAds = outfit_authors_all_favorite($userId);

$postsRows = array();

if ($loggedIn) {

	//$postsRows[] = array(
	//	'title' => 'New in',
	//	'data' => $recentCatsPosts = outfit_get_recent_cats_posts($currentUser->ID, $count)
	//);

	/*$postsRows[] = array(
		'title' => '',
		'data' =>
	);*/

	$searchPrefAge = get_user_meta($userId, USER_META_SEARCH_PREF_AGE, true);
	$searchPrefLocations = outfit_get_preferred_locations($userId);

	//var_dump($searchPrefLocations);

	$postsByAgeAndLocation = array();
	if (!empty($searchPrefLocations) || !empty($searchPrefAge)) {
		$postsByAgeAndLocation = outfit_get_posts_by_age_and_location($searchPrefAge, $searchPrefLocations, $count);
	}
	$postsRows[] = array(
		'title' => 'New items to love',
		'data' => $postsByAgeAndLocation
	);
	$postsRows[] = array(
		'title' => 'From your favorite sellers',
		'data' => $favSellersPosts = outfit_get_posts_of_favorite_sellers($currentUser->ID,$count)
	);
	$postsRows[] = array(
		'title' => 'Your latest loved ones',
		'data' => $wishlist = outfit_get_wishlist_posts($currentUser->ID, $count)
	);
	$postsRows[] = array(
		'title' => 'Seen lately',
		'data' => $recentPosts = outfit_get_recent_products($currentUser->ID, $count)
	);

	$catSlugsKeys = array_keys($catslugs);
	$catSlugsIndex = 0;
	$catSlugsCount = count($catSlugsKeys);

	foreach ($postsRows as $i => $postRow) {
		if (count($postRow['data']) == 0 && ($catSlugsIndex < $catSlugsCount)) {
			$postsRows[$i]['data'] = outfit_get_category_posts($catSlugsKeys[$catSlugsIndex], $count);
			$postsRows[$i]['title'] = $catslugs[$catSlugsKeys[$catSlugsIndex]];
			$postsRows[$i]['slug'] = $catSlugsKeys[$catSlugsIndex];
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
								<?php
								$postsInRow = count($postRow['data']);
								if ($isMobile && $postsInRow > 1 && ($postsInRow % 2) == 1) {
									$lastIndex = $postsInRow - 2;
								}
								else {
									$lastIndex = $postsInRow - 1;
								}

								foreach ( $postRow['data'] as $postIndex => $post ) : setup_postdata( $post );
									if ($postIndex > $lastIndex) break;
								?>
									<div class="col-lg-4 col-md-4 col-sm-6 item">
										<div class="classiera-box-div classiera-box-div-v1">
											<figure class="clearfix">

												<div class="premium-img">
													<a href="<?php the_permalink(); ?>">
														<?php
														$postPrice = get_post_meta($post->ID, POST_META_PRICE, true);
														$postAuthorId = $post->post_author;
														$postAuthorName = getAuthorFullNameCat($postAuthorId);
														$postAuthorNameTitle = getAuthorFullName($postAuthorId);

														$authorAvatarUrl = outfit_get_user_picture($postAuthorId, 50);
														$postBrand = implode(',', getPostTermNames($post->ID, 'brands'));
														$postBrandObjects = wp_get_post_terms($post->ID, 'brands');
														$isFavorite = in_array($post->ID, $currentUserFavoriteAds);
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
														<div class="cat-wish">
															<?php //if ($isFavorite) { ?>
																<!--<i class="fa fa-heart" aria-hidden="true"></i>-->
															<?php //} else { ?>
																<!--<i class="fa fa-heart-o" aria-hidden="true"></i>-->
															<?php //} ?>
														
															<?php //if (!empty($userId)): ?>
																<form method="post" class="fav-form clearfix">
																	<input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>"/>
																	<?php if (!$isFavorite) { ?>
																		<button type="submit" value="favorite" name="favorite" class="watch-later text-uppercase"><span></span></button>
																	<?php } else { ?>
																		<button type="submit" value="unfavorite" name="unfavorite" class="watch-later text-uppercase in-wish"><span></span></button>
																	<?php } ?>
																</form>
															<?php //endif; ?>	
														</div>				
														<div class="cat-brand">
															<?php foreach ($postBrandObjects as $termObj): ?>
																<?php if (!strstr($termObj->slug, 'other')) : ?>
																<a href="<?php echo outfit_get_brand_link($termObj->term_id) ?>"><?php echo esc_attr($termObj->name); ?></a>
																<?php endif; ?>
															<?php endforeach; ?>
														</div>
													</div>
												</div><!--premium-img-->
												<div class="au-price">
													<div class="au">
														<a href="<?php echo get_author_posts_url( $postAuthorId ); ?>">
															<img style="height: 30px;" class="" src="<?php echo esc_url($authorAvatarUrl); ?>" alt="<?php echo esc_attr($postAuthorNameTitle); ?>">
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



