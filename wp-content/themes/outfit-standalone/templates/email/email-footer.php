		<?php 
			global $redux_demo;
			$outfitLogo = $redux_demo['outfit_email_logo']['url'];
			$outfitCopyRight = $redux_demo['footer_copyright'];
			$outfitFacebook = $redux_demo['facebook-link'];
			$outfitTwitter = $redux_demo['twitter-link'];
			$outfitDribbble = $redux_demo['dribbble-link'];
			$outfitFlickr = $redux_demo['flickr-link'];
			$outfitGithub = $redux_demo['github-link'];
			$outfitPinterest = $redux_demo['pinterest-link'];	
			$outfitYouTube = $redux_demo['youtube-link'];
			$outfitGoogle = $redux_demo['google-plus-link'];
			$outfitLinkedin = $redux_demo['linkedin-link'];
			$outfitInstagram = $redux_demo['instagram-link'];
			$outfitVimeo = $redux_demo['vimeo-link'];
		?>
			<div class="outfit-email-footer" style="padding: 50px 0; background: #232323; text-align: center;">
				<img style="margin-bottom: 40px;" src="<?php echo esc_url($outfitLogo); ?>" alt="<?php bloginfo( 'name' ); ?>">
				<div style="text-align: center;">
					<?php if(!empty($outfitFacebook)){?>
					<?php $facebookIMG = get_template_directory_uri() . '/images/social/facebook.png'; ?>
					<a class="classiera-email-social-icon" href="<?php echo esc_url($outfitFacebook); ?>" style="background: #444444; height: 40px; width: 40px; text-align: center; display: inline-block; line-height:40px;">
						<img src="<?php echo esc_url($facebookIMG); ?>" style="width:14px;" alt="facebook">
					</a>
					<?php } ?>
					<?php if(!empty($outfitTwitter)){?>
					<?php $twiiterIMG = get_template_directory_uri() . '/images/social/twiiter.png'; ?>
					<a class="classiera-email-social-icon" href="<?php echo esc_url($outfitTwitter); ?>" style="background: #444444; height: 40px; width: 40px; text-align: center; display: inline-block; line-height:40px;">
						<img src="<?php echo esc_url($twiiterIMG); ?>" style="width:14px;" alt="twitter">
					</a>
					<?php } ?>
					<?php if(!empty($outfitGoogle)){?>
					<?php $googleIMG = get_template_directory_uri() . '/images/social/google-plus.png'; ?>
					<a class="classiera-email-social-icon" href="<?php echo esc_url($outfitGoogle); ?>" style="background: #444444; height: 40px; width: 40px; text-align: center; display: inline-block; line-height:40px;">
						<img src="<?php echo esc_url($googleIMG); ?>" style="width:14px;" alt="google">
					</a>
					<?php } ?>
					<?php if(!empty($outfitPinterest)){?>
					<?php $pinterestIMG = get_template_directory_uri() . '/images/social/pinterest.png'; ?>
					<a class="classiera-email-social-icon" href="<?php echo esc_url($outfitPinterest); ?>" style="background: #444444; height: 40px; width: 40px; text-align: center; display: inline-block; line-height:40px;">
						<img src="<?php echo esc_url($pinterestIMG); ?>" style="width:14px;" alt="pinterest">
					</a>
					<?php } ?>
					<?php if(!empty($outfitInstagram)){?>
					<?php $instagramIMG = get_template_directory_uri() . '/images/social/instagram.png'; ?>
					<a class="classiera-email-social-icon" href="<?php echo esc_url($outfitInstagram); ?>" style="background: #444444; height: 40px; width: 40px; text-align: center; display: inline-block; line-height:40px;">
						<img src="<?php echo esc_url($instagramIMG); ?>" style="width:14px;" alt="instagram">
					</a>
					<?php } ?>
					<?php if(!empty($outfitLinkedin)){?>
					<?php $linkedinIMG = get_template_directory_uri() . '/images/social/linkedin.png'; ?>
					<a class="classiera-email-social-icon" href="<?php echo esc_url($outfitLinkedin); ?>" style="background: #444444; height: 40px; width: 40px; text-align: center; display: inline-block; line-height:40px;">
						<img src="<?php echo esc_url($linkedinIMG); ?>" style="width:14px;" alt="linkedin">
					</a>
					<?php } ?>
				</div>
			</div><!--outfit-email-footer-->
			<div class="cassiera-footer-bottom" style="padding: 10px 0; text-align: center; background: #303030;">
				<p style="font-family: 'Lato', sans-serif; font-size:14px; color: #8e8e8e">
				<?php echo wp_kses($outfitCopyRight, $allowed_html); ?>
				</p>
			</div><!--outfit-footer-bottom-->
		</div>
	</body>
</html>