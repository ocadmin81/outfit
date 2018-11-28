<?php 
	global $redux_demo;
	global $post;
	global $currentUserFavoriteAds;

	$postPrice = get_post_meta($post->ID, POST_META_PRICE, true);
	$postAuthorId = $post->post_author;
	$postAuthorName = get_the_author_meta('display_name', $postAuthorId );
	if (empty($postAuthorName)) {
		$postAuthorName = get_the_author_meta('user_nicename', $postAuthorId );
	}
	if (empty($postAuthorName)) {
		$postAuthorName = get_the_author_meta('user_login', $postAuthorId );
	}
	$authorAvatarUrl = outfit_get_user_picture($postAuthorId, 50);
	$postBrand = implode(',', getPostTermNames($post->ID, 'brands'));

	$isFavorite = in_array($post->ID, $currentUserFavoriteAds);
?>
<div class="col-lg-3 col-md-4 col-sm-6 col-sms-6 item">
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
				<div class="cat-wish-brand">
					<div class="cat-wish">
						<?php //if ($isFavorite) { ?>
							<!--<i class="fa fa-heart" aria-hidden="true"></i>-->
						<?php //} else { ?>
							<!--<i class="fa fa-heart-o" aria-hidden="true"></i>-->
						<?php //} ?>
					
						<?php //if (!empty($userId)): ?>
							<form method="post" class="fav-form clearfix">
								<input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>"/>
								<?php if (isFavorite) { ?>
									<button type="submit" value="favorite" name="favorite" class="watch-later text-uppercase"><span></span></button>
								<?php } else { ?>
									<button type="submit" value="unfavorite" name="unfavorite" class="watch-later text-uppercase in-wish"><span></span></button>
								<?php } ?>
							</form>
						<?php //endif; ?>	
					</div>				
					<div class="cat-brand"><a href=""><?php echo esc_attr($postBrand); ?></a></div>
				</div>
			</div><!--premium-img-->
			<div class="au-price">
				<div class="au">
					<a href="<?php echo get_author_posts_url( $postAuthorId ); ?>">
						<img style="height: 30px;" class="" src="<?php echo esc_url($authorAvatarUrl); ?>" alt="<?php echo esc_attr($postAuthorName); ?>">
						<?php echo esc_attr($postAuthorName); ?>
					</a>
				</div>				
				<?php //if(!empty($postPrice)){?>
				<div class="price">
					<span class="">						
						<?php
						if(is_numeric($postPrice)){
							echo $postPrice.' &#8362;';
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