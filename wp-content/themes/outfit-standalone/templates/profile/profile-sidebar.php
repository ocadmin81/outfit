<?php 
	global $redux_demo;

	$outfitAuthorName = '';
	$currentUser = wp_get_current_user();
	$userId = $currentUser->ID;
	$outfitAuthorEmail = $currentUser->user_email;
	$outfitAuthorName = $currentUser->display_name;
	if(empty($outfitAuthorName)){
		$outfitAuthorName = $currentUser->user_nicename;
	}
	if(empty($outfitAuthorName)){
		$outfitAuthorName = $currentUser->user_login;
	}
	$outfitAuthorThumb = get_user_meta($userId, "outfit_author_avatar_url", true);
	$outfitAuthorThumb = outfit_get_profile_img($outfitAuthorThumb);
	if(empty($outfitAuthorThumb)){
		$outfitAuthorThumb = outfit_get_avatar_url ($outfitAuthorEmail, $size = '150' );
	}	
	$outfitOnlineCheck = outfit_user_last_online($userId);
	$userRegistered = $currentUser->user_registered;
	$dateFormat = get_option( 'date_format' );
	$outfitRegDate = date_i18n( $dateFormat, strtotime($userRegistered) );
	$outfitProfile = $redux_demo['profile'];
	$outfitAllAds = $redux_demo['all-ads'];
	$outfitEditProfile = $redux_demo['edit'];
	$outfitPostAds = $redux_demo['new_post'];
	$outfitFollowerPage = $redux_demo['outfit_user_follow'];
	$outfitUserFavourite = $redux_demo['all-favourite'];
?>
<aside id="sideBarAffix" class="section-bg-white affix-top">
	<ul class="user-page-list list-unstyled">
		<li class="<?php if(is_page_template( 'template-edit-profile.php' )){echo "active";}?>">
			<a href="<?php echo esc_url( $outfitProfile ); ?>">
				<span>
					<i class="fa fa-user"></i>
					<?php esc_html_e("About Me", 'outfit-standalone') ?>
				</span>
			</a>
		</li><!--About-->
		<li class="<?php if(is_page_template( 'template-user-all-ads.php' )){echo "active";}?>">
			<a href="<?php echo esc_url( $outfitAllAds ); ?>">
				<span><i class="fa fa-suitcase"></i><?php esc_html_e("My Ads", 'outfit-standalone') ?></span>
				<span class="in-count pull-right flip"><?php echo count_user_posts($userId);?></span>
			</a>
		</li><!--My Ads-->
		<li class="<?php if(is_page_template( 'template-favorite.php' )){echo "active";}?>">
			<a href="<?php echo esc_url( $outfitUserFavourite ); ?>">
				<span><i class="fa fa-heart"></i><?php esc_html_e("Watch later Ads", 'outfit-standalone') ?></span>
				<span class="in-count pull-right flip">
					<?php
						$myarray = outfit_authors_all_favorite($userId);
						if(!empty($myarray)){
							$args = array(
							   'post_type' => 'post',
							   'post__in'      => $myarray
							);
						$wp_query = new WP_Query( $args );
						$favCount = 0;
						while ($wp_query->have_posts()) : $wp_query->the_post(); $favCount++;
						endwhile;						
						echo esc_attr( $favCount );
						wp_reset_query();
						}else{
							echo "0";
						}
					?>
				</span>
			</a>
		</li><!-- Wishlist -->
		<li class="<?php if(is_page_template( 'template-follow.php' )){echo "active";}?>">
			<a href="<?php echo esc_url( $outfitFollowerPage ); ?>">
				<span><i class="fa fa-users"></i><?php esc_html_e("Follower", 'outfit-standalone') ?></span>
			</a>
		</li><!--Follower-->
		<li>
			<a href="<?php echo wp_logout_url(get_option('siteurl')); ?>">
				<span><i class="fas fa-sign-out-alt"></i><?php esc_html_e("Logout", 'outfit-standalone') ?></span>
			</a>
		</li><!--Logout-->
	</ul><!--user-page-list-->
</aside><!--sideBarAffix-->