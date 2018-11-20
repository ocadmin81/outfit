<?php 
	global $redux_demo;
	global $post;

	$postPrice = get_post_meta($post->ID, POST_META_PRICE, true);
	$postAuthorId = $post->post_author;
	$postAuthorName = get_the_author_meta('display_name', $postAuthorId );
	if (empty($postAuthorName)) {
		$postAuthorName = get_the_author_meta('user_nicename', $postAuthorId );
	}
	if (empty($postAuthorName)) {
		$postAuthorName = get_the_author_meta('user_login', $postAuthorId );
	}
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
				<?php if(!empty($postPrice)){?>
				<span class="">
					<span class="price-text">
						<?php
						if(is_numeric($postPrice)){
							echo '&#8362; ' .  $postPrice;
						}else{
							echo esc_attr( $postPrice );
						}
						?>
					</span>
				</span>
				<?php } ?>
				<span>
					<img class="" src="<?php echo esc_url($authorAvatarUrl); ?>" alt="<?php echo esc_attr($postAuthorName); ?>">
				</span>
				<span><a href="<?php echo get_author_posts_url( $postAuthorId ); ?>"><?php echo esc_attr($postAuthorName); ?></a></span>
			</div>

		</figure>
	</div><!--classiera-box-div-->
</div><!--col-lg-4-->