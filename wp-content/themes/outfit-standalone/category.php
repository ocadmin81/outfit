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
global $outfitMainCat;

$catId = get_queried_object_id();
$thisCategory = get_category($catId);

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$perPage = 20;

global $currentUserFavoriteAds;
$currentUserFavoriteAds = outfit_authors_all_favorite($currentUserId);

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
$subCategories = getSubCategories($outfitMainCat);

$products = array();

?>

<!-- page content -->
<section class="inner-page-content border-bottom top-pad-50">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-lg-9">
				<!-- advertisement -->
				<section class="classiera-advertisement advertisement-v7 section-pad border-bottom">
					<div class="tab-divs">
						<div class="view-head">
							<div class="container">
								<div class="row">
									<div class="col-md-12">
										<div class="tab-button">
											<ul class="nav nav-tabs" role="tablist">
												<li role="presentation" class="active">
													<a href="#grid" aria-controls="grid" role="tab" data-toggle="tab">
														<?php esc_html_e( 'Products', 'outfit-standalone' ); ?>
													</a>
												</li>
												<li role="presentation">
													<a href="#map" aria-controls="map" role="tab" data-toggle="tab">
														<?php esc_html_e( 'Map', 'outfit-standalone' ); ?>
													</a>
												</li>
											</ul><!--nav nav-tabs-->
										</div><!--tab-button-->
									</div><!--col-lg-6 col-sm-8-->
								</div><!--row-->
							</div><!--container-->
						</div><!--view-head-->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane fade in active" id="grid">
								<div class="container">
									<div class="row">
										<script type="text/javascript">

										</script>
										<!-- Posts-->
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

											$products[] = array(
												'id' => $post->ID,
												'author_id' => $post->post_author,
												'author_name' => esc_attr(getAuthorFullName($postAuthorId)),
												'title' => get_the_title(),
												'price' => outfit_format_post_price(get_post_meta($post->ID, POST_META_PRICE, true)),
												'thumb_img_url' => esc_url(outfit_get_post_thumb_url($post->ID)),
												'author_img_url' => esc_url(outfit_get_user_picture($postAuthorId, 50)),
												'brand' => esc_attr(implode(', ', getPostTermNames($post->ID, 'brands'))),
												'locations' => $postLocations
											);
										endwhile;
										?>
										<!-- Posts-->

									</div><!--row-->
									<?php outfit_pagination(); ?>
								</div><!--container-->
								<?php wp_reset_query(); ?>
							</div><!--tabpanel ALL-->
							<div role="tabpanel" class="tab-pane fade" id="map">
								<div class="container">
									<div class="row">
										<textarea style="display: none;" id="current-address-points"><?php echo json_encode($products); ?></textarea>

										<div id="outfit_main_map" style="width:100%; height:600px;">

											<script type="text/javascript">
												// Initialize and add the map
												var map;
												function initMap(lat, long) {
													var point = {lat: lat, lng: long};
													// The map, centered at Uluru
													map = new google.maps.Map(
														document.getElementById('outfit_main_map'), {zoom: 8, center: point});
													var addressPoints = jQuery.parseJSON(jQuery('#current-address-points').text());
													var markerArray = [];
													for (var i = 0; i < addressPoints.length; i++){
														var a = addressPoints[i];
														for (var j = 0; j < a.locations.length; j++) {
															var coords = a.locations[j];
															var position = {lat: parseFloat(coords[0]), lng: parseFloat(coords[1])};
															var marker = new google.maps.Marker({position: position, map: map});
														}
													}
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
					</div><!--tab-divs-->
				</section>
				<!-- advertisement -->
			</div><!--col-md-8-->
			<div class="col-md-4 col-lg-3">
				<aside class="sidebar">
					<div class="row">
						<!--subcategory-->
						<div class="col-lg-12 col-md-12 col-sm-6 match-height">
							<div class="widget-box">
								<div class="widget-title">
									<h4>
										<i class="" style=""></i>
										<?php echo esc_html($thisCategory->name); ?>
									</h4>
								</div>
								<div class="widget-content">
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

						<div class="col-lg-12 col-md-12 col-sm-6 match-height">
							<div class="widget-box">
								<?php get_template_part( 'templates/outfit-adv-search' );?>
							</div>
						</div>
					</div><!--row-->
				</aside>
			</div><!--row-->
		</div><!--row-->
	</div><!--container-->
</section>	
<!-- page content -->
<?php get_footer(); ?>