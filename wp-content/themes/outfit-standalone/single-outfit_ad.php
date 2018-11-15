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
global $post;
$postId = '';
$currentUser = wp_get_current_user();
$editPostUrl = $redux_demo['edit_post'];
if(function_exists('icl_object_id')) {
	$templateEditAd = 'template-edit-ads.php';
	$editPostUrl = outfit_get_template_url($templateEditAd);
}
$postId = $post->ID;
global $wp_rewrite;
if ($wp_rewrite->permalink_structure == ''){
	$editPostUrl .= "&post=".$postId;
}else{
	$editPostUrl .= "?post=".$postId;
}

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	<section class="inner-page-content single-post-page">
		<div class="container">

			<?php
			/*
			 * categories
			 */
			$postCategories = getCategoryPath($post->ID);
			$outfitMainCat = (isset($postCategories[0])? $postCategories[0] : 0);
			$outfitSubCat = (isset($postCategories[1])? $postCategories[1] : 0);
			$outfitSubSubCat = (isset($postCategories[2])? $postCategories[2] : 0);
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
			$postColor = getPostTermNames($post->ID, 'colors');
			$postAgeGroup = getPostTermNames($post->ID, 'age_groups');
			$postBrand = getPostTermNames($post->ID, 'brands');
			$postConditions = getPostTermNames($post->ID, 'conditions');
			$postCondition = (isset($postConditions[0])? $postConditions[0] : '');
			$postWriter = getPostTermNames($post->ID, 'writers');
			$postCharacter = getPostTermNames($post->ID, 'characters');
			?>
			<?php if ( get_post_status ( $post->ID ) == 'pending' ) {?>
				<div class="alert alert-info" role="alert">
					<p>
						<strong><?php esc_html_e('Congratulation!', 'outfit-standalone') ?></strong> <?php esc_html_e('Your Ad has submitted and pending for review.', 'outfit-standalone') ?>
					</p>
				</div>
			<?php } ?>
			<div class="row">
				<div class="col-md-3 col-sm-12">

				</div>
				<div class="col-md-4 col-sm-12">
					<h4 class="text-uppercase">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						<?php
						if (
						($post->post_author == $currentUser->ID && get_post_status ( $post->ID ) == 'publish') ||
						current_user_can('administrator')
						):
						?>
							<a href="<?php echo esc_url($editPostUrl); ?>" class="edit-post btn btn-sm btn-default">
								<i class="far fa-edit"></i>
								<?php esc_html_e( 'Edit Post', 'outfit-standalone' ); ?>
							</a>
						<?php
						endif;
						?>
						<!--Edit Ads Button-->
					</h4>
					<div class="post-price">
						<h4>
							<?php
							if(is_numeric($postPrice)){
								echo '&#8362; ' . $postPrice;
							} else {
								echo esc_attr($postPrice);
							}
							?>
						</h4>
					</div>
					<div><?php echo the_content(); ?></div>
					<div class="post-details">
						<ul class="list-unstyled clearfix">
							<?php if(!empty( $postBrand )): ?>
								<li>
									<p><?php esc_html_e( 'Brand', 'outfit-standalone' ); ?>:
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postBrand)); ?>
									</span>
									</p>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postAgeGroup )): ?>
								<li>
									<p><?php esc_html_e( 'Age Group', 'outfit-standalone' ); ?>:
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postAgeGroup)); ?>
									</span>
									</p>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postColor )): ?>
								<li>
									<p><?php esc_html_e( 'Color', 'outfit-standalone' ); ?>:
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postColor)); ?>
									</span>
									</p>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postWriter )): ?>
								<li>
									<p><?php esc_html_e( 'Writer', 'outfit-standalone' ); ?>:
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postWriter)); ?>
									</span>
									</p>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postCharacter )): ?>
								<li>
									<p><?php esc_html_e( 'Character', 'outfit-standalone' ); ?>:
									<span class="pull-right flip">
										<?php echo esc_attr(join(',', $postCharacter)); ?>
									</span>
									</p>
								</li>
							<?php endif; ?>
							<?php if(!empty( $postCondition )): ?>
								<li>
									<p><?php esc_html_e( 'Condition', 'outfit-standalone' ); ?>:
									<span class="pull-right flip">
										<?php echo esc_attr($postCondition); ?>
									</span>
									</p>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
				<div class="col-md-5 col-sm-12">
					<!-- single post carousel-->
					<?php
					$attachments = get_children(array('post_parent' => $post->ID,
							'post_status' => 'inherit',
							'post_type' => 'attachment',
							'post_mime_type' => 'image',
							'order' => 'ASC',
							'orderby' => 'menu_order ID'
						)
					);
					?>
					<?php if ( has_post_thumbnail() || !empty($attachments)){?>
						<div id="single-post-carousel" class="carousel slide single-carousel" data-ride="carousel" data-interval="3000">

							<!-- Wrapper for slides -->
							<div class="carousel-inner" role="listbox">
								<?php
								if(empty($attachments)){
									if ( has_post_thumbnail()){
										$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
										?>
										<div class="item active">
											<img class="img-responsive" src="<?php echo esc_url($image[0]); ?>" alt="<?php the_title(); ?>">
										</div>
										<?php
									}else{
										$image = get_template_directory_uri().'/assets/images/nothumb.png';
										?>
										<div class="item active">
											<img class="img-responsive" src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
										</div>
										<?php
									}
								}else{
									$count = 1;
									foreach($attachments as $att_id => $attachment){
										$full_img_url = wp_get_attachment_url($attachment->ID);
										?>
										<div class="item <?php if($count == 1){ echo "active"; }?>">
											<img class="img-responsive" src="<?php echo esc_url($full_img_url); ?>" alt="<?php the_title(); ?>">
										</div>
										<?php
										$count++;
									}
								}
								?>
							</div>
							<!-- slides number -->
							<div class="num">
								<i class="fa fa-camera"></i>
								<span class="init-num"><?php esc_html_e('1', 'classiera') ?></span>
								<span><?php esc_html_e('of', 'classiera') ?></span>
								<span class="total-num"></span>
							</div>
							<!-- Left and right controls -->
							<div class="single-post-carousel-controls">
								<a class="left carousel-control" href="#single-post-carousel" role="button" data-slide="prev">
									<span class="fa fa-chevron-left" aria-hidden="true"></span>
									<span class="sr-only"><?php esc_html_e('Previous', 'classiera') ?></span>
								</a>
								<a class="right carousel-control" href="#single-post-carousel" role="button" data-slide="next">
									<span class="fa fa-chevron-right" aria-hidden="true"></span>
									<span class="sr-only"><?php esc_html_e('Next', 'classiera') ?></span>
								</a>
							</div>
							<!-- Left and right controls -->
						</div>
					<?php } ?>
					<!-- single post carousel-->
				</div>
			</div>
		</div>
	</section>

<?php endwhile; ?>
<?php get_footer();