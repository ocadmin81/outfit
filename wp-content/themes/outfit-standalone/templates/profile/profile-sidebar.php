<?php 

	$outfitAllAds = outfit_get_page_url('user_all_ads');
	$outfitEditProfile = outfit_get_page_url('profile_settings');
	$outfitFollowerPage = outfit_get_page_url('follow');
	$outfitUserFavorite = outfit_get_page_url('favorite_ads');
	require_once 'Mobile_Detect_side.php';
	$detect = new Mobile_Detect_side;
	$path = $_SERVER['REQUEST_URI'];
?>
<?php if ($detect->isMobile()): ?>
	<div class="user-menu-heading" style="display:none;">
		<?php if($path == '/profile-settings/'): ?>
			<span class="user"><?php esc_html_e("פרטים אישיים", 'outfit-standalone') ?></span>
		<?php elseif($path == '/user_all_ads/'): ?>
			<span class="ads"><?php esc_html_e("המודעות שלי", 'outfit-standalone') ?></span>
		<?php elseif($path == '/favorite_ads/'): ?>
			<span class="wish"><?php esc_html_e("WISHLIST", 'outfit-standalone') ?></span>
		<?php elseif($path == '/follow/'): ?>
			<span class="seller"><?php esc_html_e("מוכרים אהובים", 'outfit-standalone') ?></span>
		<?php else: ?>
			<span class="user"><?php esc_html_e("פרטים אישיים", 'outfit-standalone') ?></span>
		<?php endif; ?>
	</div>
<?php endif; ?>
<aside id="sideBarAffix-not" class="section-bg-white affix-top <?php if ($detect->isMobile()): ?>mobile-user-menu<?php endif; ?>">
	<ul class="user-page-list list-unstyled">
		<li class="ads <?php if($path == '/user_all_ads/'){echo "active";}?>">
			<a href="<?php echo esc_url( $outfitAllAds ); ?>">
				<span><?php esc_html_e("המודעות שלי", 'outfit-standalone') ?></span>				
			</a>
		</li><!--My Ads-->	
		<li class="user <?php if($path == '/profile-settings/'){echo "active";}?>">
			<a href="<?php echo esc_url( $outfitEditProfile ); ?>">
				<span><?php esc_html_e("פרטים אישיים", 'outfit-standalone') ?></span>
			</a>
		</li><!--About-->
		<li class="wish <?php if($path == '/favorite_ads/'){echo "active";}?>">
			<a href="<?php echo esc_url( $outfitUserFavorite ); ?>">
				<?php esc_html_e("WISHLIST", 'outfit-standalone') ?></span>
			</a>
		</li><!-- Wishlist -->
		<li class="seller <?php if($path == '/follow/'){echo "active";}?>">
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