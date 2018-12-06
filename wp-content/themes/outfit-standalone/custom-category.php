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
global $allowed_html;
global $catId;
global $paged, $wp_query, $wp, $post;
global $outfitMainCat, $subCategories;

$catId = get_queried_object_id();
$thisCategory = get_category($catId);

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$perPage = 20;

$pagepermalink = get_permalink($post->ID);
global $currPageId;
$currPageId = $post->ID;

global $currentUserFavoriteAds, $currentUserId;
$current_user = wp_get_current_user();
$currentUserId = $current_user->ID;

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

$outfitMainCat = outfit_get_cat_ancestors($catId);

if (false === $outfitMainCat) {
	$outfitMainCat = $catId;
}
else {
	$outfitMainCat = end($outfitMainCat);
}
//$subCategories = getSubCategories($outfitMainCat);
$subCategories = getSubCategories($catId);
$products = array();

require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;
?>

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
									<div class="widget-title"><?php esc_html_e( 'סינון לפי', 'outfit-standalone' ); ?></div>
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
												$products[] = array(
													'id' => $post->ID,
													'author_id' => $post->post_author,
													'author_name' => esc_attr(getAuthorFullName($postAuthorId)),
													'title' => get_the_title(),
													'price' => $pPrice,
													'thumb_img_url' => $pThumb,
													'author_img_url' => esc_url(outfit_get_user_picture($postAuthorId, 50)),
													'brand' => $pBrand,
													'locations' => $postLocations,
													'content' => '<a class="classiera_map_div" href="'.get_the_permalink().'">'
														.'<img class="classiera_map_div__img" src="'.$pThumb.'" alt="images">'
														.'<div class="classiera_map_div__body">'
														.'<p class="classiera_map_div__price">'.__( "Price", 'outfit-standalone').' : <span>'.$pPrice.'</span></p>'
														.'<h5 class="classiera_map_div__heading">'.get_the_title().'</h5>'
														.'<p class="classiera_map_div__cat">'.__( "Brand", 'outfit-standalone').' : '.$pBrand.'</p></div></a>'
												);
											endwhile;
											?>
											<!-- Posts-->

										</div><!--row-->
										<?php outfit_pagination(); ?>
									<?php wp_reset_query(); ?>
								</div><!--tabpanel ALL-->
								<div role="tabpanel" class="tab-pane map-tab fade" id="map">
									<div class="container-not">
										<div class="row">
											<textarea style="display: none;" id="current-address-points"><?php echo json_encode($products); ?></textarea>

											<div id="outfit_main_map" style="width:100%; height:800px;">

												<script type="text/javascript">
													// Initialize and add the map
													var map;
													function initMap(lat, long) {
														var point = {lat: lat, lng: long};

														map = new google.maps.Map(
															document.getElementById('outfit_main_map'), {zoom: 7, center: point});

														loadAddressPoints();

													}

													function loadAddressPoints() {
														var products = jQuery.parseJSON(jQuery('#current-address-points').text());
														var markers = [];
														for (var i = 0; i < products.length; i++){
															var a = products[i];
															var infowindow = new google.maps.InfoWindow({
																content: a.content
															});
															for (var j = 0; j < a.locations.length; j++) {
																var coords = a.locations[j];
																var position = {lat: parseFloat(coords[0]), lng: parseFloat(coords[1])};
																var marker = new google.maps.Marker({position: position, map: map, title: a.title});
																marker.addListener('click', function() {
																	infowindow.open(map, marker);
																});
																//markers.push(marker);
															}
														}
														//var bounds = new google.maps.LatLngBounds();
														//for (i = 0; i < markers.length; i++) {
														//	bounds.extend(markers[i].getPosition());
														//}
														//map.fitBounds(bounds);
													}

													jQuery(document).ready(function() {
														google.maps.event.addDomListener(window, 'load', function(){
															initMap(31.0461, 34.8516);
														});
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