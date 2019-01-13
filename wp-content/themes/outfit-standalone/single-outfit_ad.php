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

global $redux_demo;
global $current_user;
global $post;
$postId = '';
$userId = '';
require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;

wp_get_current_user();
$userId = $current_user->ID;

$filterByGender = false;

?>

<?php //echo 'curr templ' . $GLOBALS['current_theme_template'] ?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php
	$postId = $mainPostId = $post->ID;
	/* save to recent products */
	if (!empty($userId)) {
		$recent = get_user_meta($userId, USER_META_LAST_PRODUCTS, true);
		$recent = explode(',', $recent);
		$newRecent = array();
		$newRecent[] = $postId;
		foreach ($recent as $rp) {
			if ($rp != $postId) {
				$newRecent[] = $rp;
			}
		}
		$newRecent = array_slice($newRecent, 0, 20);
		update_user_meta($userId, USER_META_LAST_PRODUCTS, implode(',', $newRecent));
	}

	global $wp_rewrite;
	$editPostUrl = outfit_edit_ad_url($postId);

	if (isset($_POST['favorite'])) {
		if (!empty($userId)) {
			outfit_insert_author_favorite($userId, $_POST['post_id']);
		}
		else {
			$loginUrl = outfit_login_url_back('', 'favorite', $_POST['post_id']);
			//echo 'login url' . $loginUrl;
			wp_redirect($loginUrl);
			exit;
		}
	}
	else if (isset($_POST['unfavorite'])) {
		if (!empty($userId)) {
			outfit_delete_author_favorite($userId, $_POST['post_id']);
		}
	}

	if (isset($_POST['follow'])) {
		if (!empty($userId)) {
			outfit_insert_author_follower($_POST['author_id'], $userId);
		}
	}
	else if (isset($_POST['unfollow'])) {
		if (!empty($userId)) {
			outfit_delete_author_follower($_POST['author_id'], $userId);
		}
	}

	global $currentUserFavoriteAds;
	$currentUserFavoriteAds = outfit_authors_all_favorite($userId);

	get_header();

	?>

	<section class="inner-page-content single-post-page single-post-ad">
		<div class="wrap">

			<?php
			/*
			 * categories
			 */
			$postCategories = getCategoryPath($post->ID);
			$comparePriceData = outfit_get_compare_price_data($postCategories);
			$outfitMainCat = (isset($postCategories[0])? $postCategories[0] : 0);
			$outfitSubCat = (isset($postCategories[1])? $postCategories[1] : 0);
			$outfitSubSubCat = (isset($postCategories[2])? $postCategories[2] : 0);
			$closestCat = false;
			$catsCount = count($postCategories);
			if ($catsCount > 0) {
				$closestCat = $postCategories[$catsCount-1];
			}
			$filterBy = false;
			if ($closestCat) {
				$filterBy = fetch_category_custom_fields(get_category($closestCat));
			}

			$postStatus = get_post_status($post->ID);
			$postPhone = get_post_meta($post->ID, POST_META_PHONE, true);
			$postPrice = get_post_meta($post->ID, POST_META_PRICE, true);
			$postPreferredHours = get_post_meta($post->ID, POST_META_PREFERRED_HOURS, true);
			// post location
			$postLocation = OutfitLocation::toAssoc(get_post_meta($post->ID, POST_META_LOCATION, true));
			$postAddress = '';
			$postLatitude = '';
			$postLongitude = '';
			$postLocality = $postArea1 = $postArea2 = $postArea3 = '';
			if (null !== $postLocation && isset($postLocation['address'])) {
				$postAddress = $postLocation['address'];
				$postLatitude = $postLocation['latitude'];
				$postLongitude = $postLocation['longitude'];
				$postLocality = $postLocation['locality'];
				$postArea1 = $postLocation['aal1'];
				$postArea2 = $postLocation['aal2'];
				$postArea3 = $postLocation['aal3'];
			}
			// post secondary location
			$postLocation2 = OutfitLocation::toAssoc(get_post_meta($post->ID, POST_META_LOCATION_2, true));
			$postSecAddress = '';
			$postSecLatitude = '';
			$postSecLongitude = '';
			$postSecLocality = $postSecArea1 = $postSecArea2 = $postSecArea3 = '';
			if (null !== $postLocation2 && isset($postLocation2['address'])) {
				$postSecAddress = $postLocation2['address'];
				$postSecLatitude = $postLocation2['latitude'];
				$postSecLongitude = $postLocation2['longitude'];
				$postSecLocality = $postLocation2['locality'];
				$postSecArea1 = $postLocation2['aal1'];
				$postSecArea2 = $postLocation2['aal2'];
				$postSecArea3 = $postLocation2['aal3'];
			}

			// post third location
			$postLocation3 = OutfitLocation::toAssoc(get_post_meta($post->ID, POST_META_LOCATION_3, true));

			$postThirdAddress = '';
			$postThirdLatitude = '';
			$postThirdLongitude = '';
			$postThirdLocality = $postThirdArea1 = $postThirdArea2 = $postThirdArea3 = '';
			if (null !== $postLocation3 && isset($postLocation3['address'])) {
				$postThirdAddress = $postLocation3['address'];
				//var_dump($postThirdAddress);
				$postThirdLatitude = $postLocation3['latitude'];
				$postThirdLongitude = $postLocation3['longitude'];
				$postThirdLocality = $postLocation3['locality'];
				$postThirdArea1 = $postLocation3['aal1'];
				$postThirdArea2 = $postLocation3['aal2'];
				$postThirdArea3 = $postLocation3['aal3'];
			}
			$postColor = getPostTermNames($post->ID, 'colors');
			$postAgeGroup = getPostTermNames($post->ID, 'age_groups');
			$postAgeGroupIds = getPostTermIds($post->ID, 'age_groups');
			$postBrand = getPostTermNames($post->ID, 'brands');
			$postBrandObjects = wp_get_post_terms($post->ID, 'brands');
			$postConditions = getPostTermNames($post->ID, 'conditions');
			$postCondition = (isset($postConditions[0])? $postConditions[0] : '');
			$postWriter = getPostTermNames($post->ID, 'writers');
			$postCharacter = getPostTermNames($post->ID, 'characters');
			$postGender = getPostTermNames($post->ID, 'genders');
			$postLanguage = getPostTermNames($post->ID, 'languages');
			$postShoeSize = getPostTermNames($post->ID, 'shoe_sizes');
			$postMaternitySize = getPostTermNames($post->ID, 'maternity_sizes');
			$postBicycleSize = getPostTermNames($post->ID, 'bicycle_sizes');

			/* post author */
			$postAuthorId = $post->post_author;
			$postAuthorName = getAuthorFullName($postAuthorId);
			$authorAvatarUrl = outfit_get_user_picture($postAuthorId, 50);
			$authorEmail = get_the_author_meta(USER_META_EMAIL, $postAuthorId);
			$authorPhone = get_the_author_meta(USER_META_PHONE, $postAuthorId);
			$authorPreferredHours = get_the_author_meta(USER_META_PREFERRED_HOURS, $postAuthorId);

			$authorPosts = getOutfitAdsByAuthor($postAuthorId, 1, 5);

			if(!empty( $postAgeGroupIds ) && ($filterBy && $filterBy->catFilterByAge)) {
				$categoryPosts = outfit_get_category_posts_by_id_and_age($outfitMainCat, 5, $postAgeGroupIds);
			}
			else {
				$categoryPosts = outfit_get_category_posts_by_id($outfitMainCat, 5);
			}

			?>
			<?php if ( get_post_status ( $post->ID ) == 'pending' ) {?>
				<div class="alert alert-info" role="alert">
					<p>
						<strong><?php //esc_html_e('ברכות!', 'outfit-standalone') ?></strong> <?php esc_html_e('המודעה שלך הועלתה ומחכה לאישור', 'outfit-standalone') ?>
					</p>
				</div>
			<?php } ?>
			<?php if ($detect->isMobile() && !$detect->isTablet()): ?>
				<div class="mobile-seller">
					<div class="seller-img-link">
						<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
							<?php if($authorAvatarUrl): ?>
								<img class="media-object" src="<?php echo esc_url($authorAvatarUrl); ?>" alt="<?php echo esc_attr($postAuthorName); ?>">
							<?php else: ?>
								<img class="media-object" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/user-placeholder.png" alt="<?php echo esc_attr($postAuthorName); ?>">
							<?php endif; ?>
							<span><?php echo esc_attr($postAuthorName); ?></span>
						</a>
					</div>
					<div class="seller-info">
						<div class="area seller-info-block">
							<div class="seller-icon"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.svg" /></div>
							<div class="seller-info-drop area-drop">
								<div class="widget-content widget-content-post collect-points">
									<div class="contact-details widget-content-post-area">
										<h5 class="text-uppercase"><?php esc_html_e('נקודות איסוף', 'outfit-standalone') ?></h5>
										<ul class="list-unstyled c-detail">
											<?php if (!empty($postAddress)) { ?>
												<li><span><?php echo esc_html($postAddress);?></span></li>
											<?php } ?>
											<?php if (!empty($postSecAddress)) { ?>
												<li><span><?php echo esc_html($postSecAddress);?></span></li>
											<?php } ?>
											<?php if (!empty($postThirdAddress)) { ?>
												<li><span><?php echo esc_html($postThirdAddress);?></span></li>
											<?php } ?>
										</ul>
									</div>
								</div><!--widget-content-->								
							</div>
						</div>
						<div class="phone seller-info-block">
							<div class="seller-icon"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/phone.svg" /></div>
							<div class="seller-info-drop area-phone area-drop">
								<div class="widget-content widget-content-post collect-details">
									<div class="contact-details widget-content-post-area">
										<h5 class="text-uppercase"><?php esc_html_e('ליצירת קשר עם המוכר/ת', 'outfit-standalone') ?></h5>
										<ul class="list-unstyled c-detail">
											<?php if(!empty($authorPhone)){?>
												<li class="authorPhone">
													<a href="tel:<?php echo esc_html($authorPhone); ?>"><span class=""><?php echo esc_html($authorPhone);?></a></span>
												</li>
											<?php } ?>
											<?php if(!empty($authorPreferredHours)){?>
												<li class="PreferredHours">
													<span class="title"><?php esc_html_e('*שעה נוחה להתקשרות', 'outfit-standalone') ?>:</span>
													<span>
														<?php echo esc_html($authorPreferredHours);?>
													</span>
												</li>
											<?php } ?>
										</ul>
									</div><!--contact-details-->
								</div>							
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<div class="row">
				<div class="col-md-5 col-sm-6 media-box">
					<!-- single post carousel-->
					<?php
					$attachments = get_children(array('post_parent' => $post->ID,
							'post_status' => 'inherit',
							'post_type' => 'attachment',
							'post_mime_type' => 'image',
							'order' => 'ASC',
							//'orderby' => 'menu_order ID'
						)
					);
					?>
					<?php if ( has_post_thumbnail() || !empty($attachments)){?>
						<div id="single-post-carousel" class="slide img-box">
							<div class="row">
								<!-- Wrapper for slides -->
								<?php if (!$detect->isMobile() || $detect->isTablet()): ?>
									<?php if(!empty($attachments)){ ?>
										<div class="col-md-2 col-sm-2 thumbs-img">
										<?php
										$count = 1;
										foreach($attachments as $att_id => $attachment){
											$full_img_url = wp_get_attachment_url($attachment->ID);
											?>
											<div class="item <?php if($count == 1){ echo "active"; }?>">
												<a href="<?php echo esc_url($full_img_url); ?>" class="fancybox">
													<img class="img-responsive" src="<?php echo esc_url($full_img_url); ?>" alt="<?php the_title(); ?>">
												</a>
											</div>
											<?php
											$count++;
										}
										?>
										</div>										
									<?php } ?>
									<div class="col-md-10 col-sm-10 main-img" role="listbox">
									<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
										<div class="item">
											<a href="<?php echo esc_url($image[0]); ?>" class="fancybox">
												<img class="img-responsive" src="<?php echo esc_url($image[0]); ?>" alt="<?php the_title(); ?>">
											</a>
										</div>
										<div class="wish-share">
											<?php //if (!empty($userId)): ?>
											<form method="post" class="fav-form clearfix">
												<input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>"/>
												<?php if (!outfit_is_favorite_post($userId, $post->ID)) { ?>
													<button type="submit" value="favorite" name="favorite" class="watch-later text-uppercase">
														<span><?php //esc_html_e( 'הוסף ל- WISHLIST', 'outfit-standalone' ); ?></span>
													</button>
												<?php } else { ?>
													<button type="submit" value="unfavorite" name="unfavorite" class="watch-later in-fa text-uppercase">
														<span><?php //esc_html_e( 'הסרה מה- WISHLIST', 'outfit-standalone' ); ?></span>
													</button>
												<?php } ?>
											</form>
											<?php //endif; ?>
											<div class="share">
												<ul>
													<li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_permalink(); ?>" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>													
													<li><a href="http://pinterest.com/pin/create/button/?url=<?php echo get_permalink(); ?>" target="_blank"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a></li>
													<li><a href="mailto:enteryour@addresshere.com?subject=<?php echo the_title(); ?>&body=<?php echo get_permalink(); ?>" target="_blank"><i class="fa fa-envelope" aria-hidden="true"></i></a></li>
												</ul>
											</div>
										</div>										
									</div>
								<?php else: ?>
									<?php if(!empty($attachments)){ ?>
										<div class="thumbs-img-m <?php if(count($attachments) > 1): ?>owl-carousel<?php endif; ?>">
											<?php
											$count = 1;
											foreach($attachments as $att_id => $attachment){
												$full_img_url = wp_get_attachment_url($attachment->ID);
												?>
												<div class="item <?php if($count == 1){ echo "active"; }?>">
													<a href="<?php echo esc_url($full_img_url); ?>" class="fancybox">
														<img class="img-responsive" src="<?php echo esc_url($full_img_url); ?>" alt="<?php the_title(); ?>">
													</a>
												</div>
												<?php
												$count++;
											}
											?>
										</div>										
									<?php } ?>
								<div class="wish-share mobile">
									<?php //if (!empty($userId)): ?>
										<form method="post" class="fav-form clearfix">
											<input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>"/>
											<?php if (!outfit_is_favorite_post($userId, $post->ID)) { ?>
											<button type="submit" value="favorite" name="favorite" class="watch-later text-uppercase">
												<span><?php //esc_html_e( 'הוסף ל- WISHLIST', 'outfit-standalone' ); ?></span>
											</button>
											<?php } else { ?>
											<button type="submit" value="unfavorite" name="unfavorite" class="watch-later in-fa text-uppercase">
												<span><?php //esc_html_e( 'הסרה מה- WISHLIST', 'outfit-standalone' ); ?></span>
											</button>
											<?php } ?>
										</form>
									<?php //endif; ?>								
								</div>									
								<?php endif; ?>
							</div>						
					<?php } ?>
					<!-- single post carousel-->
					</div>		
				</div>	
				<div class="col-md-4 col-sm-6 middle-ad-column">
					<h1 class="text-uppercase">
						<?php the_title(); ?>
						<!--Edit Ads Button-->
						<?php
						if (
						($post->post_author == $current_user->ID && get_post_status ( $post->ID ) == 'publish') ||
						current_user_can('administrator')
						):
						?>
							<a href="<?php echo esc_url($editPostUrl); ?>" class="edit-post btn btn-sm btn-default">
								<?php esc_html_e( 'עריכה', 'outfit-standalone' ); ?>
							</a>
						<?php
						endif;
						?>						
					</h1>
					<div class="post-price">
							<?php
							if(is_numeric($postPrice)){
								echo  number_format((float)$postPrice, 2, '.', '').' &#8362';
							} else {
								echo esc_attr($postPrice);
							}
							?>
					</div>
					<?php if ( !empty( get_the_content() ) ): ?>
						<div class="post-desc"><?php echo the_content(); ?></div>
					<?php endif; ?>
					<div class="post-details">
						<ul class="list-unstyled clearfix">
							<?php
							$otherBrandOnly = false;
							if (count($postBrandObjects) == 1 && strstr($postBrandObjects[0]->slug, 'other')) {
								$otherBrandOnly = true;
							}
							?>
							<?php if(!empty( $postBrandObjects ) && ($filterBy && $filterBy->catFilterByBrand) && !$otherBrandOnly): ?>
								<li>
									<strong><?php esc_html_e( 'מותג', 'outfit-standalone' ); ?></strong>
									<span class="pull-right flip">
										<?php foreach ($postBrandObjects as $termObj): ?>
											<?php if (!strstr($termObj->slug, 'other')) : ?>
											<a href="<?php echo outfit_get_brand_link($termObj->term_id) ?>"><?php echo esc_attr($termObj->name); ?></a>
											<?php endif; ?>
										<?php endforeach; ?>
									</span>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postAgeGroup ) && ($filterBy && $filterBy->catFilterByAge)): ?>
								<li>
									<strong><?php esc_html_e( 'גיל', 'outfit-standalone' ); ?></strong>
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postAgeGroup)); ?>
									</span>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postColor ) && ($filterBy && $filterBy->catFilterByColor)): ?>
								<li>
									<strong><?php esc_html_e( 'צבע', 'outfit-standalone' ); ?></strong>
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postColor)); ?>
									</span>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postWriter ) && ($filterBy && $filterBy->catFilterByWriter)): ?>
								<li>
									<strong><?php esc_html_e( 'סופר', 'outfit-standalone' ); ?></strong>
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postWriter)); ?>
									</span>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postCharacter ) && ($filterBy && $filterBy->catFilterByCharacter)): ?>
								<li>
									<strong><?php esc_html_e( 'נושא', 'outfit-standalone' ); ?></strong>
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postCharacter)); ?>
									</span>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postCondition ) && ($filterBy && $filterBy->catFilterByCondition)): ?>
								<li>
									<strong><?php esc_html_e( 'מצב הפריט', 'outfit-standalone' ); ?></strong>
									<span class="pull-right flip">
										<?php echo esc_attr($postCondition); ?>
									</span>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postGender ) && ($filterByGender && $filterBy && $filterBy->catFilterByGender)): ?>
								<li>
									<strong><?php esc_html_e( 'מין', 'outfit-standalone' ); ?></strong>
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postGender)); ?>
									</span>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postLanguage ) && ($filterBy && $filterBy->catFilterByLanguage)): ?>
								<li>
									<strong><?php esc_html_e( 'שפה', 'outfit-standalone' ); ?></strong>
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postLanguage)); ?>
									</span>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postShoeSize ) && ($filterBy && $filterBy->catFilterByShoeSize)): ?>
								<li>
									<strong><?php esc_html_e( 'מידה', 'outfit-standalone' ); ?></strong>
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postShoeSize)); ?>
									</span>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postMaternitySize ) && ($filterBy && $filterBy->catFilterByMaternitySize)): ?>
								<li>
									<strong><?php esc_html_e( 'מידה', 'outfit-standalone' ); ?></strong>
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postMaternitySize)); ?>
									</span>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postBicycleSize ) && ($filterBy && $filterBy->catFilterByBicycleSize)): ?>
								<li>
									<strong><?php esc_html_e( 'גודל', 'outfit-standalone' ); ?></strong>
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postBicycleSize)); ?>
									</span>
								</li>
							<?php endif; ?>
						</ul>						
						<?php if (false !== $comparePriceData): ?>
						<div class="price-compare">
							<span><?php echo esc_html('סקר שוק?'); ?></span>
							<a href="<?php echo esc_url($comparePriceData['link']) ?>" target="_blank"><?php echo esc_html($comparePriceData['text']); ?></a>
						</div>
						<?php endif; ?>
					</div>
					<?php if ($detect->isMobile() && !$detect->isTablet()): ?>
						<div class="wish-share mobile">
							<div class="whatsapp-share">
								<a title="<?php echo the_title(); ?>" class="whatsapp-share-image" href="whatsapp://send?text=<?php //echo $watsappmessage; ?> » <?php echo the_title(); ?> » <?php echo get_permalink(); ?>"><span><?php esc_html_e( 'שיתוף ב- WhatsApp', 'outfit-standalone' ); ?></span></a>
							</div>							
							<div class="share">
								<ul>
									<li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_permalink(); ?>" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>									
									<li><a href="http://pinterest.com/pin/create/button/?url=<?php echo get_permalink(); ?>" target="_blank"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a></li>
									<li><a href="mailto:enteryour@addresshere.com?subject=<?php echo the_title(); ?>&body=<?php echo get_permalink(); ?>" target="_blank"><i class="fa fa-envelope" aria-hidden="true"></i></a></li>
								</ul>
							</div>
						</div>	
					<?php endif; ?>
				</div>
				<?php if (!$detect->isMobile() || $detect->isTablet()): ?>
				<div class="col-md-3 col-sm-12 post-left-side">
					<div class="post-left">
						<div class="widget-box">
							<div class="widget-content widget-content-post">
								<div class="author-info widget-content-post-area">
									<div class="media">
										<div class="user-img">
											<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
											<?php if($authorAvatarUrl): ?>
												<img class="media-object" src="<?php echo esc_url($authorAvatarUrl); ?>" alt="<?php echo esc_attr($postAuthorName); ?>">
											<?php else: ?>
												<img class="media-object" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/user-placeholder.png" alt="<?php echo esc_attr($postAuthorName); ?>">
											<?php endif; ?>
											</a>
										</div><!--media-left-->
										<div class="media-body">
											<h5 class="media-heading text-uppercase">
												<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php echo esc_attr($postAuthorName); ?></a>
											</h5>
											<?php if (!empty($userId) && $userId != $postAuthorId) { ?>
												<form method="post" class="classiera_follow_user">
													<input type="hidden" name="author_id" value="<?php echo esc_attr($postAuthorId); ?>"/>
													<?php if (!outfit_is_favorite_author($postAuthorId, $userId)) { ?>
														<button type="submit" name="follow"><span><?php esc_html_e( 'הוספה למוכרים אהובים', 'outfit-standalone' ); ?></span></button>
													<?php } else { ?>
														<button type="submit" name="unfollow"><span><?php esc_html_e( 'הסרה ממוכרים אהובים', 'outfit-standalone' ); ?></span></button>
													<?php } ?>
													</form>
												<div class="clearfix"></div>

											<?php } ?>
										</div><!--media-body-->
									</div><!--media-->


								</div><!--author-info-->
							</div>
							<div class="widget-content widget-content-post collect-points">
								<div class="contact-details widget-content-post-area">
									<h5 class="text-uppercase"><?php esc_html_e('נקודות איסוף', 'outfit-standalone') ?></h5>
									<ul class="list-unstyled c-detail">
										<?php if (!empty($postAddress)) { ?>
											<li><span><?php echo esc_html($postAddress);?></span></li>
										<?php } ?>
										<?php if (!empty($postSecAddress)) { ?>
											<li><span><?php echo esc_html($postSecAddress);?></span></li>
										<?php } ?>
										<?php if (!empty($postThirdAddress)) { ?>
											<li><span><?php echo esc_html($postThirdAddress);?></span></li>
										<?php } ?>
									</ul>
								</div>
							</div><!--widget-content-->
							<div class="widget-content widget-content-post collect-details">
								<div class="contact-details widget-content-post-area">
									<h5 class="text-uppercase"><?php esc_html_e('ליצירת קשר עם המוכר/ת', 'outfit-standalone') ?></h5>
									<ul class="list-unstyled c-detail">
										<?php if(!empty($authorPhone)){?>
											<li class="authorPhone">
												<a href="tel:<?php echo esc_html($authorPhone); ?>"><span class=""><?php echo esc_html($authorPhone);?></a></span>
											</li>
										<?php } ?>
										<?php if(!empty($authorPreferredHours)){?>
											<li class="PreferredHours">
												<span class="title"><?php esc_html_e('*שעה נוחה להתקשרות', 'outfit-standalone') ?>:</span>
												<span>
													<?php echo esc_html($authorPreferredHours);?>
												</span>
											</li>
										<?php } ?>
									</ul>
								</div><!--contact-details-->
							</div><!--widget-content-->
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>


	</section>

	<?php if (count($authorPosts) > 1) { ?>
	<section class="author-box author-page">
		<div class="wrap author-box-bg seller-ads products">
			<div class="view-head">
				<div class="row">
					<div class="col-md-12">
						<h5><?php esc_html_e( 'More from this seller', 'outfit-standalone' ); ?></h5>
					</div>
				</div><!--row-->
			</div><!--view-head-->
			<div class="tab-content">
				<div class="row">
					<?php
					foreach ( $authorPosts as $post ) : setup_postdata( $post ); ?>
						<?php
						if ($post->ID != $mainPostId) {
							get_template_part('templates/loops/product');
						}
						?>
						<?php
					endforeach;
					wp_reset_postdata();
					?>
				</div><!--row-->
				<?php wp_reset_query(); ?>
				<div class="cat-link">
					<a href="<?php echo get_author_posts_url( $postAuthorId ); ?>">הצג עוד</a>
				</div>
			</div><!--tab-content-->
		</div>
	</section>
	<?php } ?>

	<?php if (count($categoryPosts) > 1) { ?>
		<section class="author-box author-page">
			<div class="wrap author-box-bg seller-ads products">
				<div class="view-head">
					<div class="row">
						<div class="col-md-12">
							<h5><?php esc_html_e( 'More items like this', 'outfit-standalone' ); ?></h5>
						</div>
					</div><!--row-->
				</div><!--view-head-->
				<div class="tab-content">
					<div class="row">
						<?php
						foreach ( $categoryPosts as $post ) : setup_postdata( $post ); ?>
							<?php
							if ($post->ID != $mainPostId) {
								get_template_part('templates/loops/product');
							}
							?>
							<?php
						endforeach;
						wp_reset_postdata();
						?>
					</div><!--row-->
					<?php wp_reset_query(); ?>
					<div class="cat-link">
						<a href="<?php echo esc_url(outfit_get_category_link($outfitMainCat)) ?>">הצג עוד</a>
					</div>
				</div><!--tab-content-->
			</div>
		</section>
	<?php } ?>

<?php endwhile; ?>
<?php get_footer();