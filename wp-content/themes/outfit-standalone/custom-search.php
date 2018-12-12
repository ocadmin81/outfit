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

get_header(); 
?>
<?php
global $redux_demo;
global $current_user;
$currentUserId = '';
global $allowed_html;
global $catId;
global $paged, $wp_query, $wp, $post;
global $outfitMainCat, $subCategories;

$catId = '';
$subCategories = array();
$thisCategory = null;
$outfitMainCat = '';

if (isset($_GET['cat_id']) && !empty($_GET['cat_id'])) {
	$catId = intval($_GET['cat_id']);
	$thisCategory = get_category($catId);
	if (is_wp_error($thisCategory)) {
		$thisCategory = null;
	}
}

if (null !== $thisCategory) {
	$subCategories = getSubCategories($catId);
	$outfitMainCat = outfit_get_cat_ancestors($catId);

	if (false === $outfitMainCat) {
		$outfitMainCat = $catId;
	}
	else {
		$outfitMainCat = end($outfitMainCat);
	}
}
else {
	$subCategories = outfit_get_main_cats(true);
}

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$perPage = 20;

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

$args = array(
	'post_type' => OUTFIT_AD_POST_TYPE,
	'post_status' => 'publish',
	'posts_per_page' => $perPage,
	'paged' => $paged
);

if ($thisCategory) {
	$args['cat'] = $thisCategory->term_id;
}

$products = array();

$postColor = getGetMultiple('postColor', true);
$postAgeGroup = getGetMultiple('postAgeGroup', true);;
$postBrand = getGetMultiple('postBrand', true);;
$postCondition = getGetMultiple('postCondition', true);;
$postWriter = getGetMultiple('postWriter', true);;
$postCharacter = getGetMultiple('postCharacter', true);;
$postKeyword = getGetInput('s', '');
$postLocation = null;
$postPrice = getGetMultiple('priceRange', true);

// post primary location
$postAddress = trim(getGetInput('address'));
$postLatitude = getGetInput('latitude');
$postLongitude = getGetInput('longitude');
$postLocality = getGetInput('locality');
$postArea1 = getGetInput('aal1');
$postArea2 = getGetInput('aal2');
$postArea3 = getGetInput('aal3');

$postLocation = new OutfitLocation($postAddress, $postLongitude, $postLatitude, [
	'locality' => $postLocality,
	'aal3' => $postArea3,
	'aal2' => $postArea2,
	'aal1' => $postArea1
]);

if (!empty($postKeyword)) {
	$args['s'] = $postKeyword;
}

$taxQuery = array();

if (!empty($postColor)) {
	$taxQuery[] = array(
		'taxonomy' => 'colors',
		'field'    => 'term_id',
		'terms'    => $postColor,
		'operator' => 'IN'
	);
}

if (!empty($postAgeGroup)) {
	$taxQuery[] = array(
		'taxonomy' => 'age_groups',
		'field'    => 'term_id',
		'terms'    => $postAgeGroup,
		'operator' => 'IN'
	);
}

if (!empty($postBrand)) {
	$taxQuery[] = array(
		'taxonomy' => 'brands',
		'field'    => 'term_id',
		'terms'    => $postBrand,
		'operator' => 'IN'
	);
}

if (!empty($postCondition)) {
	$taxQuery[] = array(
		'taxonomy' => 'conditions',
		'field'    => 'term_id',
		'terms'    => $postCondition,
		'operator' => 'IN'
	);
}

if (!empty($postWriter)) {
	$taxQuery[] = array(
		'taxonomy' => 'writers',
		'field'    => 'term_id',
		'terms'    => $postWriter,
		'operator' => 'IN'
	);
}

if (!empty($postCharacter)) {
	$taxQuery[] = array(
		'taxonomy' => 'characters',
		'field'    => 'term_id',
		'terms'    => $postCharacter,
		'operator' => 'IN'
	);
}

if (!empty($taxQuery)) {
	$args['tax_query'] = $taxQuery;
}

$metaQuery = array();
$metaQueryPrice = array();

if (!empty($postPrice)) {
	foreach ($postPrice as $range) {
		list($min, $max) = explode('_', $range);

		if (is_numeric($min) && is_numeric($max)) {
			$min = intval($min);
			$max = intval($max);
			if ($max > $min) {
				$metaQueryPrice[] = array(
					'key' => POST_META_PRICE,
					'value' => array($min, $max),
					'compare' => 'BETWEEN',
					'type' => 'NUMERIC'
				);
			}
		}
		else if (!is_numeric($max)) {
			$min = intval($min);
			$metaQueryPrice[] = array(
				'key' => POST_META_PRICE,
				'value' => $min,
				'compare' => '>',
				'type' => 'NUMERIC'
			);
		}
	}
	if (!empty($metaQueryPrice)) {
		$metaQueryPrice['relation'] = 'OR';
		$metaQuery[] = $metaQueryPrice;
	}
}

$locationSearchKey = $postLocation->suggestSearchKey();
$searchBy = $postLocation->getLastSearchKeyBy();

$metaQueryLocation = array();
if ($locationSearchKey && $searchBy) {

	$metaKey = '';
	if ($searchBy == OutfitLocation::LOCALITY) {
		$metaKey = POST_META_LOCALITY_TAG;
	}
	else if ($searchBy == OutfitLocation::AREA3) {
		$metaKey = POST_META_AREA3_TAG;
	}
	else if ($searchBy == OutfitLocation::AREA2) {
		$metaKey = POST_META_AREA2_TAG;
	}
	else if ($searchBy == OutfitLocation::AREA1) {
		$metaKey = POST_META_AREA1_TAG;
	}
	$metaQueryLocation = array(
		'key' => $metaKey,
		'value' => $locationSearchKey,
		'compare' => '='
	);
	$metaQuery[] = $metaQueryLocation;
}
if (!empty($metaQuery)) {
	$args['meta_query'] = $metaQuery;
}

?>

<!-- page content -->
<section class="inner-page-content top-pad-50">
	<div class="wrap">		
			<div class="view-head cat-tabs">
					<div class="row">
						<div class="col-md-4 col-lg-3">&nbsp;</div>
						<div class="col-md-8 col-lg-9">
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
				<div class="col-md-4 col-lg-3">
					<aside class="sidebar cat-sidebar">
						<div class="row">
							<!--subcategory-->
							<div class="col-lg-12 col-md-12 col-sm-6">
								<div class="widget-box">
									<div class="widget-title"><?php esc_html_e( 'סינון לפי', 'outfit-standalone' ); ?></div>
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

							<div class="col-lg-12 col-md-12 col-sm-6">
								<div class="widget-box">
									<?php get_template_part( 'templates/outfit-adv-search' );?>
								</div>
							</div>
						</div><!--row-->
					</aside>
				</div><!--row-->		
				<div class="col-md-8 col-lg-9">
					<!-- advertisement -->
					<section class="classiera-advertisement advertisement-v7 section-pad">
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane cat-tab-content products fade in active" id="grid">
										<div class="row">
											<script type="text/javascript">

											</script>
											<!-- Posts-->
											<h1 class="cat-title"><?php echo esc_html($thisCategory->name); ?></h1>
											<?php

											$wp_query= null;
											$wp_query = new WP_Query($args);
											while ($wp_query->have_posts()) : $wp_query->the_post();
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

												$pThumb = esc_url(outfit_get_post_thumb_url($post->ID));
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
													'thumb_img_url' => $pThumb,
													'author_img_url' => $authorAvatarUrl,
													'brand' => $pBrand,
													'locations' => $postLocations,
													'content' => '<div class="classiera_map_div"><a href="'.get_the_permalink().'">'
														.'<img class="classiera_map_div__img" src="'.$pThumb.'" alt="images">'
														.'<div class="classiera_map_div__body">'
														.'<h5 class="classiera_map_div__heading">'.get_the_title().'</h5>'
														.'<p class="classiera_map_div__price"><span>'.$pPrice.'</span></p></div></a></div>'
														//.'<p class="classiera_map_div__cat">'.__( "Brand", 'outfit-standalone').' : '.$pBrand.'</p></div></a>'
														.'<div class="classiera_map_div">'
														.'<a href="'.get_author_posts_url( $post->post_author ).'">'
														.'<img style="height: 30px;" src="'.esc_url($authorAvatarUrl).'" alt="'.esc_attr($pAuthorName).'">'
														.esc_attr($pAuthorName)
														.'</a></div>'
												);
											endwhile;
											?>
											<!-- Posts-->

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

													function initMap(lat, long) {
														var point = {lat: lat, lng: long};

														map = new google.maps.Map(
															document.getElementById('outfit_main_map'), {zoom: 7, center: point});

														loadAddressPoints();

													}

													function closeAllInfowindows() {
														for (var i = 0; i < infowindows.length; i++) {
															infowindows[i].close();
														}
													}

													function loadAddressPoints() {
														var products = jQuery.parseJSON(jQuery('#current-address-points').text());


														for (var i = 0; i < products.length; i++){
															var a = products[i];

															for (var j = 0; j < a.locations.length; j++) {
																var infowindow = new google.maps.InfoWindow({
																	content: a.content
																});
																infowindows.push(infowindow);

																var coords = a.locations[j];
																var position = {lat: parseFloat(coords[0]), lng: parseFloat(coords[1])};
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