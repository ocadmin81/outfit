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
global $cat_id;
global $paged, $wp_query, $wp, $post;

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
	'cat' => $cat_id
);

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
										<!-- Posts-->
										<?php

										$wp_query= null;
										$wp_query = new WP_Query($args);
										while ($wp_query->have_posts()) : $wp_query->the_post();
											get_template_part( 'templates/loops/product');
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
						<?php 
						$cat_term_ID = $this_category->term_id;
						$cat_child = get_term_children( $cat_term_ID, 'category' );
						if (!empty($cat_child)) {
							$args = array(
								'type' => 'post',								
								'parent' => $cat_id,
								'orderby' => 'name',
								'order' => 'ASC',
								'hide_empty' => 0,
								'depth' => 1,
								'hierarchical' => 1,
								'taxonomy' => 'category',
								'pad_counts' => true 
							);
							$category = get_categories($args);
							if($category[0]->category_parent == 0){
									$tag = $category[0]->cat_ID;
									$category_icon_code = "";
									$category_icon_color = "";
									$your_image_url = "";
									$tag_extra_fields = get_option(MY_CATEGORY_FIELDS);
									if (isset($tag_extra_fields[$tag])) {
										$category_icon_code = $tag_extra_fields[$tag]['category_icon_code'];
										$category_icon_color = $tag_extra_fields[$tag]['category_icon_color'];
									}
								}else{
									$tag = $category[0]->category_parent;
									$tag_extra_fields = get_option(MY_CATEGORY_FIELDS);
									if (isset($tag_extra_fields[$tag])) {
										$category_icon_code = $tag_extra_fields[$tag]['category_icon_code'];
										$category_icon_color = $tag_extra_fields[$tag]['category_icon_color'];
									}
								}
								$category_icon = stripslashes($category_icon_code);
						?>
						<div class="col-lg-12 col-md-12 col-sm-6 match-height">
							<div class="widget-box">
								<div class="widget-title">
									<h4>
										<i class="<?php echo esc_html($category_icon); ?>" style="color:<?php echo esc_html($category_icon_color); ?>;"></i>
										<?php echo esc_html($catName); ?>
									</h4>
								</div>
								<div class="widget-content">
									<ul class="category">
									<?php
										foreach($category as $category) { 
									?>
										<li>
                                            <a href="<?php echo esc_url(get_category_link( $category->term_id ));?>">
                                                <i class="fa fa-angle-right"></i>
                                                <?php echo esc_html($category->name); ?>
                                                <span class="pull-right flip">
												<?php if($classieraPostCount == 1){?>
													(<?php echo esc_attr($category->count); ?>)
												<?php }else{ ?>
													&nbsp;
												<?php } ?>
												</span>
                                            </a>
                                        </li>
									<?php } ?>
									</ul>
								</div>
							</div>
						</div>
						<?php } ?>
						<!--subcategory-->


					</div><!--row-->
				</aside>
			</div><!--row-->
		</div><!--row-->
	</div><!--container-->
</section>	
<!-- page content -->
<?php get_footer(); ?>