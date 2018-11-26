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
												$postLocations[] = '['.$loc1->getLatitude().','.$loc1->getLongitude().']';
											}
											$loc2 = OutfitLocation::createFromJSON(get_post_meta($post->ID, POST_META_LOCATION_2, true));
											if (null !== $loc2) {
												$postLocations[] = '['.$loc2->getLatitude().','.$loc2->getLongitude().']';
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
										<div id="outfit_main_map" style="width:100%; height:600px;">
											<script type="text/javascript">
												jQuery(document).ready(function(){
													var addressPoints = [
														<?php foreach ($products as $p){ ?>
														{
															id: <?php echo $p['id'] ?>,
															title: <?php echo $p['title'] ?>,
															author_id: <?php echo $p['author_id'] ?>,
															author_name: <?php echo $p['author_name'] ?>,
															price: <?php echo $p['price'] ?>,
															thumb_img_url: <?php echo $p['thumb_img_url'] ?>,
															author_img_url: <?php echo $p['author_img_url'] ?>,
															brand: <?php echo $p['brand'] ?>,
															locations: [<?php implode(', ', $p['locations']) ?>]
														}
                                                        <?php } ?>
													];
													var mapopts;
													if(window.matchMedia("(max-width: 1024px)").matches){
														var mapopts =  {
															dragging:false,
															tap:false,
														};
													};
													var map = L.map('classiera_main_map', mapopts).setView([0,0],1);
													map.dragging.disable;
													map.scrollWheelZoom.disable();
													var roadMutant = L.gridLayer.googleMutant({
														<?php if($classieraMAPStyle){?>styles: <?php echo wp_kses_post($classieraMAPStyle); ?>,<?php }?>
														maxZoom: 13,
														type:'roadmap'
													}).addTo(map);
													var markers = L.markerClusterGroup({
														spiderfyOnMaxZoom: true,
														showCoverageOnHover: true,
														zoomToBoundsOnClick: true,
														maxClusterRadius: 10
													});
													markers.on('clusterclick', function(e) {
														map.setView(e.latlng, 13);
													});
													var markerArray = [];
													for (var i = 0; i < addressPoints.length; i++){
														var a = addressPoints[i];
														var newicon = new L.Icon({iconUrl: a[3],
															iconSize: [36, 51], // size of the icon
															iconAnchor: [20, 10], // point of the icon which will correspond to marker's location
															popupAnchor: [0, 0] // point from which the popup should open relative to the iconAnchor
														});
														var title = a[2];
														var marker = L.marker(new L.LatLng(a[0], a[1]));
														marker.setIcon(newicon);
														marker.bindPopup(title, {minWidth:"400"});
														marker.title = title;
														//marker.on('click', function(e) {
														//map.setView(e.latlng, 13);

														//});
														markers.addLayer(marker);
														markerArray.push(marker);
														if(i==addressPoints.length-1){//this is the case when all the markers would be added to array
															var group = L.featureGroup(markerArray); //add markers array to featureGroup
															map.fitBounds(group.getBounds());
														}
													}
													var circle;
													map.addLayer(markers);
													function getLocation(){
														if(navigator.geolocation){
															navigator.geolocation.getCurrentPosition(showPosition);
														}else{
															x.innerHTML = "Geolocation is not supported by this browser.";
														}
													}
													function showPosition(position){
														jQuery('#latitude').val(position.coords.latitude);
														jQuery('#longitude').val(position.coords.longitude);
														var latitude = jQuery('#latitude').val();
														var longitude = jQuery('#longitude').val();
														map.setView([latitude,longitude],13);
														circle = new L.circle([latitude, longitude], {radius: 2500}).addTo(map);
													}
													jQuery('#getLocation').on('click', function(e){
														e.preventDefault();
														getLocation();
													});
													//Search on MAP//
													var geocoder;
													function initialize(){
														geocoder = new google.maps.Geocoder();
													}
													jQuery("#classiera_map_address").autocomplete({
														//This bit uses the geocoder to fetch address values
														source: function(request, response){
															geocoder = new google.maps.Geocoder();
															geocoder.geocode( {'address': request.term }, function(results, status) {
																response(jQuery.map(results, function(item) {
																	return {
																		label:  item.formatted_address,
																		value: item.formatted_address,
																		latitude: item.geometry.location.lat(),
																		longitude: item.geometry.location.lng()
																	}
																}));
															})
														},
														//This bit is executed upon selection of an address
														select: function(event, ui) {
															jQuery("#latitude").val(ui.item.latitude);
															jQuery("#longitude").val(ui.item.longitude);
															var latitude = jQuery('#latitude').val();
															var longitude = jQuery('#longitude').val();
															map.setView([latitude,longitude],10);
														}
													});
													//Search on MAP//
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