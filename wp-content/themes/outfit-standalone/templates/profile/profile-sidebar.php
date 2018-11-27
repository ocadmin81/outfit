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
	require_once 'Mobile_Detect_side.php';
	$detect = new Mobile_Detect_side;
?>
<?php if ($detect->isMobile()): ?>
	<div class="user-menu-heading">
		<?php if(is_page_template( 'template-edit-profile.php' )): ?>
			<span class="user"><?php esc_html_e("פרטים אישיים", 'outfit-standalone') ?></span>
		<?php elseif(is_page_template( 'template-user-all-ads.php' )): ?>
			<span class="ads"><?php esc_html_e("המודעות שלי", 'outfit-standalone') ?></span>
		<?php elseif(is_page_template( 'template-favorite.php' )): ?>
			<span class="wish"><?php esc_html_e("WISHLIST", 'outfit-standalone') ?></span>
		<?php elseif(is_page_template( 'template-follow.php' )): ?>
			<span class="seller"><?php esc_html_e("מוכרים אהובים", 'outfit-standalone') ?></span>
		<?php else: ?>
			<span class="user"><?php esc_html_e("פרטים אישיים", 'outfit-standalone') ?></span>
		<?php endif; ?>
	</div>
<?php endif; ?>
<aside id="sideBarAffix" class="section-bg-white affix-top <?php if ($detect->isMobile()): ?>mobile-user-menu<?php endif; ?>">
	<ul class="user-page-list list-unstyled">
		<li class="user <?php if(is_page_template( 'template-edit-profile.php' )){echo "active";}?>">
			<a href="<?php echo esc_url( $outfitProfile ); ?>">
				<span><?php esc_html_e("פרטים אישיים", 'outfit-standalone') ?></span>
			</a>
		</li><!--About-->
		<li class="ads <?php if(is_page_template( 'template-user-all-ads.php' )){echo "active";}?>">
			<a href="<?php echo esc_url( $outfitAllAds ); ?>">
				<span><?php esc_html_e("המודעות שלי", 'outfit-standalone') ?></span>				
			</a>
		</li><!--My Ads-->
		<li class="wish <?php if(is_page_template( 'template-favorite.php' )){echo "active";}?>">
			<a href="<?php echo esc_url( $outfitUserFavourite ); ?>">
				<?php esc_html_e("WISHLIST", 'outfit-standalone') ?></span>
			</a>
		</li><!-- Wishlist -->
		<li class="seller <?php if(is_page_template( 'template-follow.php' )){echo "active";}?>">
			<a href="<?php echo esc_url( $outfitFollowerPage ); ?>">
				<span><?php esc_html_e("מוכרים אהובים", 'outfit-standalone') ?></span>
			</a>
		</li><!--Follower-->
		<li class="logout">
			<a href="<?php echo wp_logout_url(get_option('siteurl')); ?>">
				<span><?php esc_html_e("התנתק", 'outfit-standalone') ?></span>
			</a>
		</li><!--Logout-->
	</ul><!--user-page-list-->
</aside><!--sideBarAffix-->