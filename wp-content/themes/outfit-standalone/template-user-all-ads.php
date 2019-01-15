<?php
/**
 * Template name: Favorite Ads
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiera
 * @since classiera 1.0
 */
if ( !is_user_logged_in() ) {
	wp_redirect( outfit_get_page_url('login') );
	exit;
}
global $current_user, $user_id;
global $redux_demo;
global $paged, $wp_query, $wp, $post;

wp_get_current_user();
$currentUser = $current_user;
$userId = $user_id = $currentUser->ID;

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$perPage = 20;

$pagepermalink = get_permalink($post->ID);
$currentPageId = $post->ID;

if(isset($_GET['delete_id'])){
	$deleteId = $_GET['delete_id'];
	$deletePost = get_post($deleteId);
	if ($deletePost && ($deletePost->post_author == $userId || current_user_can('administrator'))) {
		wp_trash_post($deleteId);
	}
}

if(isset($_GET['sold_id'])){
	$soldId = $_GET['sold_id'];
	$soldPost = get_post($soldId);
	if ($soldPost && ($soldPost->post_author == $userId || current_user_can('administrator'))) {
		wp_update_post(array(
			'ID' => $soldId,
			'post_status' => 'sold'
		));
	}
}

$args = array(
	'post_type' => OUTFIT_AD_POST_TYPE,
	'author' => $userId,
	'post_status' => array( 'publish', 'pending', 'sold' ),
	'posts_per_page' => $perPage,
	'paged' => $paged
);

get_header(); ?>

<section class="user-pages section-gray-bg">
	<div class="wrap ad-page user-page">
		<div class="row">
			<div class="col-lg-3 col-md-4">
				<?php get_template_part( 'templates/profile/profile-sidebar' );?>
			</div><!--col-lg-3-->
			<div class="col-lg-9 col-md-8 user-content-height">
				<?php if($_POST){?>
					<div class="alert alert-success" role="alert">
						<?php echo esc_html( $message ); ?>
					</div>
				<?php } ?>
				<div class="user-detail-section section-bg-white">
					<div class="user-ads favorite-ads">
						<h1 class="user-detail-section-heading text-uppercase <?php if(!$wp_query->have_posts()): ?>center<?php endif; ?>"><?php esc_html_e("המודעות שלי", 'outfit-standalone') ?></h1>
						<div class="my-ads products">
							<div class="row">
								<?php
								$wp_query= null;
								// The Query
								$wp_query = new WP_Query( $args );
								?>
								<?php if($wp_query->have_posts()): ?>
									<?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
										<div style="display: none">
											<?php var_dump($post) ?>
										</div>
										<div class="col-lg-4 col-md-4 col-sm-6 item">
											<div class="classiera-box-div classiera-box-div-v1">
												<figure class="clearfix">

													<div class="premium-img">
														<?php if ($post->post_status == 'sold') { ?>
															<div class="sold">
																<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/SOLD.png" />
															</div>
														<?php //} else if ($post->post_status == 'pending') { ?>
															<!--<div class="pending" style="position: absolute; top: 0; z-index: 10;">
																<p>Pending</p>
															</div>-->
														<?php } ?>
														<a href="<?php the_permalink(); ?>">
															<?php
															$postPrice = get_post_meta($post->ID, POST_META_PRICE, true);
															$postAuthorId = $post->post_author;
															$postAuthorName = getAuthorFullName($postAuthorId);
															$editPostUrl = outfit_edit_ad_url($post->ID);
															//global $wp_rewrite;
															//$deletePostUrl = ($wp_rewrite->permalink_structure == '')? $pagepermalink."&delete_id=".$post->ID : $pagepermalink."?delete_id=".$post->ID;
															//$soldPostUrl = ($wp_rewrite->permalink_structure == '')? $pagepermalink."&sold_id=".$post->ID : $pagepermalink."?sold_id=".$post->ID;
															$deletePostUrl = outfit_get_page_permalink($currentPageId, 'delete_id='.$post->ID);
															$soldPostUrl = outfit_get_page_permalink($currentPageId, 'sold_id='.$post->ID);

															//$authorAvatarUrl = outfit_get_user_picture($postAuthorId, 50);
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
														<div class="cat-wish-brand">
															<div class="cat-wish">&nbsp;</div>
															<div class="ad-brand"><?php echo esc_attr($postBrand); ?></div>
														</div>
													</div><!--premium-img-->
													<div class="remove-post-button post-rmv">
														<a href="javascript:void(0)">
															<span class="remove-post">&nbsp;</span>
															<input type="hidden" name="" value="<?php echo esc_attr($post->ID); ?>">
														</a>
													</div><!--remove-post-button-->
													<div class="remove-post-buttons" style="display: none;">
														<div class="post-action-button-sold">
														<a class="" href="<?php echo esc_url($soldPostUrl) ?>"
														   data-title="<?php echo esc_html_e( 'האם לסמן את המוצר כ"נמכר" ולהסירו מהקטגוריה?', 'outfit-standalone' ); ?>"
														   data-content="<?php //echo esc_html_e( 'פה יכול להיות הסבר על התהליך ואיפה המוצר יוכל להופיע או לא', 'outfit-standalone' ); ?>">
															<span class="remove-post">
																<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/remove-check.png" />
																<?php esc_html_e('סמן כנמכר', 'outfit-standalone'); ?>
															</span>
															<input type="hidden" name="" value="<?php echo esc_attr($post->ID); ?>">
														</a>
														</div>
														<div class="post-action-button-remove">
														<a class="" href="<?php echo esc_url($deletePostUrl) ?>"
														   data-title="<?php echo esc_html_e( 'בטוח שתרצו להסיר את המוצר?', 'outfit-standalone' ); ?>"
														   data-content="<?php echo esc_html_e( 'ברגע שתלחצו על כן, המוצר שפירסמתם יירד מהמערכת, האם אתה בטוח?', 'outfit-standalone' ); ?>">
															<span class="remove-post">
																<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/remove-close.png" />
																<?php esc_html_e('מחיקת הודעה', 'outfit-standalone'); ?>
															</span>
															<input type="hidden" name="" value="<?php echo esc_attr($post->ID); ?>">
														</a>
														</div>
													</div><!--remove-post-button-->
													<div class="edit-price">
														<div class="edit"><a href="<?php echo esc_url($editPostUrl); ?>"><?php echo esc_html_e( 'עריכה', 'outfit-standalone' ); ?></a></div>
														<?php //if(!empty($postPrice)){?>
															<div class="price">
															<span class="">
																<?php
																if(is_numeric($postPrice)){
																	echo '&#8362; ' .  $postPrice;
																}else{
																	echo esc_attr( $postPrice );
																}
																?>
															</span>
															</div>
														<?php //} ?>													
													</div>

												</figure>
											</div><!--classiera-box-div-->
										</div><!--col-lg-4-->
									<?php endwhile; ?>
								<?php else: ?>
									<div class="no-items">
										<?php echo do_shortcode("[do_widget id=text-13]"); ?>
									</div>
								<?php endif; ?>								
							</div>
						</div>
							<?php outfit_pagination(); ?>
							<?php wp_reset_query(); ?>
					</div><!--user-ads user-profile-settings-->
				</div><!--user-detail-section-->
			</div><!--col-lg-9-->
		</div><!--row-->
	</div><!--container-->
</section><!--user-pages section-gray-bg-->

	<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="remove-post-modal">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel"></h4>
					<p id="myModalText"></p>
				</div>
				<div class="modal-footer">
					<input type="hidden" value="" id="remove-post-modal-data">
					<button type="button" class="btn btn-yes" id="remove-post-modal-yes"><?php echo esc_html_e( 'כן', 'outfit-standalone' ); ?></button>
					<button type="button" class="btn btn-no" id="remove-post-modal-no"><?php echo esc_html_e( 'לא', 'outfit-standalone' ); ?></button>
				</div>
			</div>
		</div>
	</div>

<?php get_footer(); ?>