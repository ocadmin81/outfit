<?php 

	$outfitAllAds = outfit_get_page_url('user_all_ads');
	$outfitEditProfile = outfit_get_page_url('profile_settings');
	$outfitFollowerPage = outfit_get_page_url('follow');
	$outfitUserFavorite = outfit_get_page_url('favorite_ads');
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
			<a href="<?php echo esc_url( $outfitEditProfile ); ?>">
				<span><?php esc_html_e("פרטים אישיים", 'outfit-standalone') ?></span>
			</a>
		</li><!--About-->
		<li class="ads <?php if(is_page_template( 'template-user-all-ads.php' )){echo "active";}?>">
			<a href="<?php echo esc_url( $outfitAllAds ); ?>">
				<span><?php esc_html_e("המודעות שלי", 'outfit-standalone') ?></span>				
			</a>
		</li><!--My Ads-->
		<li class="wish <?php if(is_page_template( 'template-favorite.php' )){echo "active";}?>">
			<a href="<?php echo esc_url( $outfitUserFavorite ); ?>">
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