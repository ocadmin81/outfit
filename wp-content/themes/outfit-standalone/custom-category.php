<?php
/**
 * The template for displaying Category pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiera
 * @since classiera 1.0
 */


?>
<?php
global $redux_demo;
global $current_user;
$currentUserId = '';
global $allowed_html;
global $catId;
global $paged, $wp_query, $wp, $post;
global $outfitMainCat, $subCategories;
global $thisCategory;
$thisCategory = null;

//status_header( 200 );
//$wp_query->is_page = true;
//$wp_query->is_404=false;

$catId = get_queried_object_id();
$thisCategory = get_category($catId);

$filterBy = false;
$filterByGender = false;

if (null !== $thisCategory) {
	$filterBy = fetch_category_custom_fields(get_category($thisCategory->term_id));
}

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$perPage = 20;

$pagepermalink = get_category_link($catId);//get_permalink($post->ID);
global $currPageId;
//$currPageId = $post->ID;

global $currentUserFavoriteAds, $currentUserId;
wp_get_current_user();
$currentUserId = $current_user->ID;

if (isset($_POST['favorite'])) {
	if (!empty($currentUserId)) {
		outfit_insert_author_favorite($currentUserId, $_POST['post_id']);
	}
	else {
		$loginUrl = outfit_login_url_back('', 'favorite', $_POST['post_id']);
		wp_redirect($loginUrl);
		exit;
	}
}
else if (isset($_POST['unfavorite'])) {
	if (!empty($currentUserId)) {
		outfit_delete_author_favorite($currentUserId, $_POST['post_id']);
	}
}

$currentUserFavoriteAds = outfit_authors_all_favorite($currentUserId);

/* save to recent products */
if ($currentUserId) {
	$recent = get_user_meta($currentUserId, USER_META_LAST_CATEGORY, true);
	$recent = explode(',', $recent);
	$newRecent = array();
	$newRecent[] = $catId;
	foreach ($recent as $rp) {
		if ($rp != $catId) {
			$newRecent[] = $rp;
		}
	}
	$newRecent = array_slice($newRecent, 0, 5);
	update_user_meta($currentUserId, USER_META_LAST_CATEGORY, implode(',', $newRecent));
}

$args = array(
	'post_type' => OUTFIT_AD_POST_TYPE,
	'post_status' => 'publish',
	'posts_per_page' => $perPage,
	'paged' => $paged,
	'cat' => $catId
);

// search locations
global $searchLocations, $searchLocationsStr;
$searchLocations = [];
$searchLocationsStr = '';


if ($currentUserId) {
	$searchPrefAge = get_user_meta($currentUserId, USER_META_SEARCH_PREF_AGE, true);
	$searchLocations = outfit_get_preferred_locations($currentUserId);

	//var_dump($searchLocations);
	if (!empty($searchPrefAge) && $filterBy && $filterBy->catFilterByAge) {
		$args['tax_query'] = [array(
			'taxonomy' => 'age_groups',
			'field'    => 'term_id',
			'terms'    => [(int)$searchPrefAge],
			'operator' => 'IN'
		)];
	}

	$arr = array();
	foreach ($searchLocations as $l) {
		$arr[] = $l->toString();
	}
	$searchLocationsStr = json_encode($arr);

	$metaQueryLocation = array();

	// search by locations
	foreach ($searchLocations as $location) {
		$meta = $location->suggestMetaKeyValue();
		if (false !== $meta) {
			$metaQueryLocation[] = array(
				'key' => $meta['meta_key'],
				'value' => $meta['meta_value'],
				'compare' => '='
			);
		}
	}

	if (count($metaQueryLocation)) {
		$metaQueryLocation['relation'] = 'OR';
	}

	if (!empty($metaQueryLocation)) {
		$args['meta_query'] = [$metaQueryLocation];
	}
}

$outfitMainCat = outfit_get_cat_ancestors($catId);

if (false === $outfitMainCat) {
	$outfitMainCat = $catId;
}
else {
	$outfitMainCat = end($outfitMainCat);
}
//$subCategories = getSubCategories($outfitMainCat);
$subCategories = getSubCategories($thisCategory->term_id, true);
$products = array();

require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;

get_header();
?>
<div style="display: none">
	<?php
	echo 'This category: '.$thisCategory->term_id;
	var_dump(get_permalink());
	?>
</div>
<!-- page content -->
<section class="inner-page-content top-pad-50 aaa">
	<div class="wrap">		
			<div class="view-head cat-tabs">
					<div class="row">
						<div class="col-md-4 col-sm-4 col-lg-3">&nbsp;</div>
						<div class="col-md-8 col-sm-8 col-lg-9">
							<div class="tab-button">
								<ul class="nav nav-tabs" role="tablist">
									<li role="presentation" class="p-tab active">
										<a href="#grid" aria-controls="grid" role="tab" data-toggle="tab">
											<span><?php esc_html_e( 'מוצרים', 'outfit-standalone' ); ?></span>
										</a>
									</li>
									<li role="presentation" class="m-tab">
										<a href="#map" aria-controls="map" role="tab" data-toggle="tab">
											<span><?php esc_html_e( 'מפה', 'outfit-standalone' ); ?></span>
										</a>
									</li>
								</ul><!--nav nav-tabs-->
							</div><!--tab-button-->
						</div><!--col-lg-6 col-sm-8-->
					</div><!--row-->
			</div><!--view-head-->	
			<div class="cat-content">
			<div class="row">
				<?php if ($detect->isMobile() && !$detect->isTablet()): ?>
					<h1 class="cat-title"><?php echo esc_html($thisCategory->name); ?></h1>
				<?php endif; ?>
				<div class="col-md-4 col-lg-3 col-sm-4">
					<aside class="sidebar cat-sidebar">
						<div class="row">
							<!--subcategory-->
							<div class="col-lg-12 col-md-12 col-sm-12">
								<div class="widget-box">
									<div class="widget-title title-desk"><?php esc_html_e( 'סינון לפי', 'outfit-standalone' ); ?></div>
									<div class="box-title-mobile">
										<div class="widget-title pop-form"><i class="sortbutton-icon fa fa-sliders" aria-hidden="true" data-raofz="18"></i> <?php esc_html_e( 'סינון לפי', 'outfit-standalone' ); ?></div>
										<div class="widget-title pop-map"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/addr.png" /><a href="#map" aria-controls="map" role="tab" data-toggle="tab"><?php esc_html_e( 'מפה', 'outfit-standalone' ); ?></a></div>
									</div>
									<div class="filter-fixed-title"><i class="sortbutton-icon fa fa-sliders" aria-hidden="true" data-raofz="18"></i><?php esc_html_e( 'סינון לפי', 'outfit-standalone' ); ?></div>
									<div class="widget-content" style="display:none;">
										<ul class="category">
										<?php
											foreach($subCategories as $sub) {
										?>
											<li>
												<a href="<?php echo esc_url(get_category_link( $sub->term_id ));?>">
													<i class="fa fa-angle-right"></i>
													<?php echo esc_html($sub->name); ?>

												</a>
											</li>
										<?php } ?>
										</ul>
									</div>
								</div>
							</div>

							<!--subcategory-->

							<div class="col-lg-12 col-md-12 col-sm-12">
								<div class="widget-box">
									<?php get_template_part( 'templates/outfit-adv-search' );?>
								</div>
							</div>
						</div><!--row-->
					</aside>
				</div><!--row-->		
				<div class="col-md-8 col-lg-9 col-sm-8">
					<!-- advertisement -->
					<section class="classiera-advertisement advertisement-v7 section-pad">
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane cat-tab-content products fade in active" id="grid">
										<div class="row">
											<script type="text/javascript">

											</script>
											<!-- Posts-->
											<?php if (!$detect->isMobile() || $detect->isTablet()): ?>
												<h1 class="cat-title"><?php echo esc_html($thisCategory->name); ?></h1>
											<?php endif; ?>
											<?php

											$countPosts = 0;
											$wp_query= null;
											$wp_query = new WP_Query($args);
											while ($wp_query->have_posts()) : $wp_query->the_post();
												$countPosts++;
												get_template_part( 'templates/loops/product');

												$postLocations = [];
												$loc1 = OutfitLocation::createFromJSON(get_post_meta($post->ID, POST_META_LOCATION, true));
												if (null !== $loc1) {
													//$postLocations[] = '['.$loc1->getLatitude().','.$loc1->getLongitude().']';
													$postLocations[] = array($loc1->getLatitude(), $loc1->getLongitude());
												}
												$loc2 = OutfitLocation::createFromJSON(get_post_meta($post->ID, POST_META_LOCATION_2, true));
												if (null !== $loc2) {
													//$postLocations[] = '['.$loc2->getLatitude().','.$loc2->getLongitude().']';
													$postLocations[] = array($loc2->getLatitude(), $loc2->getLongitude());
												}

												$pThumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
												$pPrice = outfit_format_post_price(get_post_meta($post->ID, POST_META_PRICE, true));
												$pBrand = esc_attr(implode(', ', getPostTermNames($post->ID, 'brands')));
												$pAuthorName = esc_attr(getAuthorFullName($post->post_author));
												$authorAvatarUrl = esc_url(outfit_get_user_picture($post->post_author, 50));
												$products[] = array(
													'id' => $post->ID,
													'author_id' => $post->post_author,
													'author_name' => $pAuthorName,
													'title' => get_the_title(),
													'price' => $pPrice,
													'thumb_img_url' => $pThumb[0],
													'author_img_url' => $authorAvatarUrl,
													'brand' => $pBrand,
													'locations' => $postLocations,
													'content' => '<div class="classiera_map_div img"><a href="'.get_the_permalink().'">'
														.'<img class="classiera_map_div__img" src="'.$pThumb[0].'" alt="images">'
														.'</a></div>'
														//.'<p class="classiera_map_div__cat">'.__( "Brand", 'outfit-standalone').' : '.$pBrand.'</p></div></a>'
														.'<div class="classiera_map_div content">'
														.'<a href="'.get_the_permalink().'"><h5 class="classiera_map_div__heading">'.get_the_title().'</a></h5>'
														.'<p class="classiera_map_div__price"><span>'.$pPrice.'</span></p>'
														.'<a href="'.get_author_posts_url( $post->post_author ).'">'
														.'<div class="map-author"><img style="height: 30px;" src="'.esc_url($authorAvatarUrl).'" alt="'.esc_attr($pAuthorName).'">'
														.esc_attr($pAuthorName)
														.'</a></div></div>'
												);
											endwhile;
											?>
											<!-- Posts-->
											<?php if(!$countPosts) { ?>
												<div class="col-xm-12">
													<div class="textwidget">
														<p>
															<img class="alignnone size-full wp-image-999 aligncenter"
																 src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/search_icon.png') ?>"
																 alt="" width="54" height="54">
														</p>
														<p style="text-align: center;"><strong>לא נמצאו מוצרים</strong></p>
														<p style="text-align: center;">
															לא נמצאו מוצרים התואמים את הסינון שלך,
															אנו ממליצים להוסיף איזורי איסוף נוספים או לשנות את הסינון.
														</p>
													</div>
												</div>
											<?php } //if(!$countPosts) ?>

										</div><!--row-->
										<?php outfit_pagination(); ?>
									<?php wp_reset_query(); ?>
								</div><!--tabpanel ALL-->
								<div role="tabpanel" class="tab-pane map-tab fade" id="map">
									<div class="filter-close-map"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/menu-m-x.png" /></div>
									<div class="container-not">
										<div class="row">
											<textarea style="display: none;" id="current-address-points"><?php echo json_encode($products); ?></textarea>

											<div id="outfit_main_map" style="width:100%; height:800px;">

												<script type="text/javascript">
													// Initialize and add the map
													var map;
													var infowindows = [];
													var mapMarkerUrl = "<?php echo esc_url(get_stylesheet_directory_uri().'/assets/images/map-marker.png') ?>";
													var topMarkerPoint = {lat: 0, lng: 0};
													var topLatitude = 0;

													function initMap(lat, long) {
														var point = {lat: lat, lng: long};

														map = new google.maps.Map(
															document.getElementById('outfit_main_map'), {zoom: 9, center: point});

														loadAddressPoints();

														if (topMarkerPoint.lat > 0) {
															map.setCenter(topMarkerPoint);
														}
													}

													function closeAllInfowindows() {
														for (var i = 0; i < infowindows.length; i++) {
															infowindows[i].close();
														}
													}

													function loadAddressPoints() {
														var products = <?php echo json_encode($products); ?>;//jQuery.parseJSON(jQuery('#current-address-points').text());


														for (var i = 0; i < products.length; i++){
															var a = products[i];

															for (var j = 0; j < a.locations.length; j++) {
																var infowindow = new google.maps.InfoWindow({
																	content: a.content
																});
																infowindows.push(infowindow);

																var coords = a.locations[j];
																var position = {lat: parseFloat(coords[0]), lng: parseFloat(coords[1])};
																if (position.lat > topLatitude) {
																	topLatitude = position.lat;
																	topMarkerPoint.lat = topLatitude;
																	topMarkerPoint.lng = position.lng;
																}
																var marker = new google.maps.Marker({position: position, map: map, title: a.title, icon: mapMarkerUrl});
																(function (m, iw) {
																	m.addListener('click', function() {
																		closeAllInfowindows();
																		iw.open(map, this);
																	});
																}(marker, infowindow));
															}
														}
													}

													jQuery(document).ready(function() {
														google.maps.event.addDomListener(window, 'load', function(){
															initMap(31.0461, 34.8516);
														});
														initMap(31.0461, 34.8516);
													});



												</script>
											</div>
										</div><!--row-->
									</div><!--container-->
								</div><!--tabpanel random-->

							</div><!--tab-content-->
					</section>
					<!-- advertisement -->
				</div><!--col-md-8-->
			</div><!--row-->
		</div>
	</div><!--container-->
</section>	
<!-- page content -->
<?php get_footer(); ?>