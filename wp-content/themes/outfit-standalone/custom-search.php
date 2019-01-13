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
global $thisCategory;

status_header( 200 );
$wp_query->is_page = true;
$wp_query->is_404=false;

require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;

$catId = '';
$subCategories = array();
$thisCategory = null;
$outfitMainCat = '';

if (isset($_REQUEST['cat_id']) && !empty($_REQUEST['cat_id'])) {
	$catId = intval($_REQUEST['cat_id']);
	$thisCategory = get_category($catId);
	if (is_wp_error($thisCategory)) {
		$thisCategory = null;
	}
}

if (null !== $thisCategory) {
	$subCategories = getSubCategories($catId, true);
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

// search locations
global $searchLocations, $searchLocationsStr;
$searchLocations = outfit_get_search_locations();
//var_dump($searchLocations);
$searchLocationsStr = '';
$arr = array();
foreach ($searchLocations as $l) {
	$arr[] = $l->toString();
}
$searchLocationsStr = json_encode($arr);

outfit_strip_slashes_from_input();

$postColor = getRequestMultiple('postColor', true);
$postAgeGroup = getRequestMultiple('postAgeGroup', true);;
$postBrand = getRequestMultiple('postBrand', true);;
$postCondition = getRequestMultiple('postCondition', true);;
$postWriter = getRequestMultiple('postWriter', true);;
$postCharacter = getRequestMultiple('postCharacter', true);;
$postKeyword = getGetInput('s', '');
$postPrice = getRequestMultiple('priceRange', true);
$postGender = getRequestMultiple('postGender', true);
$postLanguage = getRequestMultiple('postLanguage', true);
$postShoeSize = getRequestMultiple('postShoeSize', true);
$postMaternitySize = getRequestMultiple('postMaternitySize', true);
$postBicycleSize = getRequestMultiple('postBicycleSize', true);

$pageTitle = '';
if (isset($_REQUEST['bpg']) && count($postBrand) > 0) {
	$term = get_term($postBrand[0], 'brands');
	if (!is_wp_error($term)) {
		$pageTitle = $term->name;
	}
}
else if (!empty($postKeyword)) {
	$pageTitle = $postKeyword;
}



if (!empty($postKeyword)) {
	$args['s'] = $postKeyword;
}

$taxQuery = array();

if (!empty($postGender)) {
	$taxQuery[] = array(
		'taxonomy' => 'genders',
		'field'    => 'term_id',
		'terms'    => $postGender,
		'operator' => 'IN'
	);
}

if (!empty($postLanguage)) {
	$taxQuery[] = array(
		'taxonomy' => 'languages',
		'field'    => 'term_id',
		'terms'    => $postLanguage,
		'operator' => 'IN'
	);
}

if (!empty($postShoeSize)) {
	$taxQuery[] = array(
		'taxonomy' => 'shoe_sizes',
		'field'    => 'term_id',
		'terms'    => $postShoeSize,
		'operator' => 'IN'
	);
}

if (!empty($postMaternitySize)) {
	$taxQuery[] = array(
		'taxonomy' => 'maternity_sizes',
		'field'    => 'term_id',
		'terms'    => $postMaternitySize,
		'operator' => 'IN'
	);
}

if (!empty($postBicycleSize)) {
	$taxQuery[] = array(
		'taxonomy' => 'bicycle_sizes',
		'field'    => 'term_id',
		'terms'    => $postBicycleSize,
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

if (!empty($postColor)) {
	$taxQuery[] = array(
		'taxonomy' => 'colors',
		'field'    => 'term_id',
		'terms'    => $postColor,
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

$metaQuery[] = $metaQueryLocation;

if (!empty($metaQuery)) {
	$args['meta_query'] = $metaQuery;
}

$inSearch = isset($_GET["s"]);
?>

<!-- page content -->
<section class="inner-page-content top-pad-50">
	<div class="wrap">	
			<?php if(!$inSearch): ?>
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
			<?php endif; ?>
			<div class="cat-content">
			<div class="row">
				<?php if(!$inSearch): ?>
					<?php if ($detect->isMobile() && !$detect->isTablet()): ?>
						<h1 class="cat-title"><?php echo esc_html($pageTitle); ?></h1>
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
				<?php endif; ?>
				<div class="col-md-8 col-lg-9 col-sm-8 product-list">
					<!-- advertisement -->
					<section class="classiera-advertisement advertisement-v7 section-pad">
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane cat-tab-content products fade in active" id="grid">
										<div class="row">
											<script type="text/javascript">

											</script>
											<!-- Posts-->
											<?php if(!$inSearch): ?>
												<?php if (!$detect->isMobile() || $detect->isTablet()): ?>
													<h1 class="cat-title"><?php echo esc_html($pageTitle); ?></h1>
												<?php endif; ?>
											<?php endif; ?>
											<?php
											$countPosts = 0;
											$wp_query= null;
											$wp_query = new WP_Query($args);											
											while ($wp_query->have_posts()) : $wp_query->the_post();
												get_template_part( 'templates/loops/product');
												$countPosts++;
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
											<?php if($inSearch) { ?>
											<div class="search-no-items">
												<?php echo do_shortcode("[do_widget id=text-17]"); ?>
												<?php get_search_form(); ?>
											</div>
											<?php } else { ?>
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
											<?php } ?>
										<?php } ?>
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