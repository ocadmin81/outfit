<?php 
	global $redux_demo;
	global $post;
	global $currentUserFavoriteAds;

	$postPrice = get_post_meta($post->ID, POST_META_PRICE, true);
	$postAuthorId = $post->post_author;
	$postAuthorName = getAuthorFullName($postAuthorId);

	$authorAvatarUrl = outfit_get_user_picture($postAuthorId, 50);
	$postBrand = implode(',', getPostTermNames($post->ID, 'brands'));

	$isFavorite = in_array($post->ID, $currentUserFavoriteAds);
?>
<div class="col-lg-4 col-md-4 col-sm-6 match-height item">
	<div class="classiera-box-div classiera-box-div-v1">
		<figure class="clearfix">
			<div class="premium-img">
				<a href="<?php the_permalink(); ?>">
					<?php
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

			</div><!--premium-img-->
			<div>
				<?php if ($isFavorite) { ?>
					<i class="fa fa-heart" aria-hidden="true"></i>
				<?php } else { ?>
					<i class="fa fa-heart-o" aria-hidden="true"></i>
				<?php } ?>
			</div>
			<div>
				<?php echo esc_attr($postBrand); ?>
			</div>
			<div>
				<?php if(!empty($postPrice)){?>
				<div class="">
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
				<?php } ?>
				<span>
					<img style="height: 30px;" class="" src="<?php echo esc_url($authorAvatarUrl); ?>" alt="<?php echo esc_attr($postAuthorName); ?>">
				</span>
				<span><a href="<?php echo get_author_posts_url( $postAuthorId ); ?>"><?php echo esc_attr($postAuthorName); ?></a></span>
			</div>

		</figure>
	</div><!--classiera-box-div-->
</div><!--col-lg-4-->