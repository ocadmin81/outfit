<?php
/*==========================
 Outfit Email Filter
 1- Filter content type.
 2- Filter Name
 3- Filter Email
 ===========================*/
add_filter ("wp_mail_content_type", "outfit_mail_content_type");
function outfit_mail_content_type() {
	return "text/html";
}
add_filter('wp_mail_from_name', 'outfit_blog_name_from_name');
function outfit_blog_name_from_name($name = '') {
    return get_bloginfo('name');
}
add_filter ("wp_mail_from", "outfit_mail_from");
function outfit_mail_from() {
	$sendemail =  get_bloginfo('admin_email');
	return $sendemail;
}
if(!function_exists('outfit_new_ad_email')) {
	add_action( 'transition_post_status', 'outfit_new_ad_email', 10, 3 );
	function outfit_new_ad_email( $new_status, $old_status, $post ){
		if($new_status == 'pending' && ($old_status == 'new' || $old_status == 'publish') && $post->post_type == 'outfit_ad'){
			$post = get_post($post->ID);
			$author = get_userdata($post->post_author);
			global $redux_demo, $email_subject;
			$email_subject = 'הודעה חדשה ממתינה לאישור';
			$admin_email = $redux_demo['admin_email'];
			if (empty($admin_email)) return;
			ob_start();
			include(TEMPLATEPATH . '/templates/email/email-header.php');
			?>

			<div class="classiera-email-content" style="padding: 20px 0; width:100%; margin:0 auto;">
				<h1><?php echo esc_html($email_subject); ?>
				</h1>
				<?php
				$link_start = '<a href="'.add_query_arg('email', 'admin', get_permalink($post->ID)).'">';
				$link_end = '</a>';
				$text = $link_start.esc_html($post->post_title).$link_end;
				?>
				<p style="">
					<?php echo $text; ?>
				</p>
			</div>
			<?php
			include(TEMPLATEPATH . '/templates/email/email-footer.php');
			$message = ob_get_contents();
			write_log($message);
			ob_end_clean();
			wp_mail($admin_email, $email_subject, $message);
			//wp_mail("milla@originalconcepts.co.il", $email_subject, $message, ["From: Perfit <$admin_email>"]);
		}
	}
}
/*==========================
 Email template which sent When Email is Published
 ===========================*/	
if(!function_exists('outfit_publish_post_email')) { 
	add_action( 'transition_post_status', 'outfit_publish_post_email', 10, 3 );
	function outfit_publish_post_email( $new_status, $old_status, $post ){
		if($new_status == 'publish' && $old_status != 'publish' && $post->post_type == 'outfit_ad'){
			$post = get_post($post->ID);
			$author = get_userdata($post->post_author);
			global $redux_demo, $email_subject;
			$email_subject = $redux_demo['published_ad_notification_title'];
			$author_email = $author->user_email;
			ob_start();
			include(TEMPLATEPATH . '/templates/email/email-header.php');
			?>

			<div class="classiera-email-content" style="padding: 20px 0; width:100%; margin:0 auto;">
				<?php

				$texts = [
					[
						'text' => 'מושלם! המודעה שלך אושרה',
						'link' => ''
					],
					[
						'text' => '*אנחנו שמחים שבחרתם לפרסם אצלינו*',
						'link' => ''
					],
					[
						'text' => '*כל מודעה שעולה אצלינו עוברת בדיקה*',
						'link' => ''
					],
					[
						'text' => 'לפעמים (ולא לעיתים קרובות)
אנחנו משנים דברים קטנים
במודעה לפני פרסומה
כדי שיהיה קל למחפשים אחרים למצוא אותה בקלות
',
						'link' => ''
					],
					[
						'text' => '*הכנסו לראות כיצד פורסמה מודעתכם*',
						'link' => ''
					]
				];

				?>

				<?php foreach ($texts as $i => $entry) { ?>
					<?php if ($i == 0) { ?>
						<p style="">
							<span style="font-size: 22px;"><?php echo $entry['text']; ?></span>
						</p>
						<p style="">
							<img style="height: 22px; line-height: 22px; margin-top: -7px; display:inline-block;vertical-align: middle;"
								 src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/love.png">
						</p>
					<?php } else if ($i < (count($texts)-1)) { ?>
						<p style="">
							<span style=""><?php echo $entry['text']; ?></span>
						</p>
					<?php } else { ?>
						<p style="">
							<span style="font-size: 24px;"><?php echo $entry['text']; ?></span>
						</p>
					<?php } ?>
				<?php } ?>
				<p style="">
					<a href="<?php echo esc_url(add_query_arg('email', 'seller', get_permalink($post->ID))); ?>">
						<img style="width: 100%; padding: 0 15px;" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/email_img2.jpg">
					</a>
				</p>
			</div>

			<?php
			include(TEMPLATEPATH . '/templates/email/email-footer.php');	
			$message = ob_get_contents();
			write_log($message);
			ob_end_clean();
			wp_mail($author_email, $email_subject, $message);
			//wp_mail("milla@originalconcepts.co.il", $email_subject, $message);
		}    
	}
}
/*==========================
 Email template New User Registration Function
 ===========================*/
if (!function_exists('outfitUserNotification')) {
	function outfitUserNotification($email, $password, $userId)
	{
		$blog_title = get_bloginfo('name');
		$blog_url = esc_url(home_url());
		$adminEmail = get_bloginfo('admin_email');
		global $redux_demo, $email_subject;
		$email_subject = $redux_demo['new_user_title'];

		ob_start();
		include(get_template_directory() . '/templates/email/email-header.php');

		?>

		<div class="classiera-email-content" style="padding: 20px 0; width:100%; margin:0 auto;">
			<?php

			$texts = [
				[
					'text' => 'העלו את המודעה הראשונה שלכם',
					'link' => outfit_get_page_url('submit_ad')
				],
				[
					'text' => '*הוסיפו פרטים לפרופיל שלכם*',
					'link' => get_author_posts_url($userId)
				],
				[
					'text' => '*החיפוש והפרסום יותאם לכם באופן אישי*',
					'link' => ''
				],
				[
					'text' => '*הפיצו את הבשורה*',
					'link' => ''
				],
				[
					'text' => '*שתפו את האתר החדש עם הורים כמוכם*',
					'link' => ''
				]
			];

			?>

			<?php foreach ($texts as $i => $entry) { ?>
				<p style="">
					<?php if ($i == 0) { ?>
						<img style="height: 22px; line-height: 22px; margin-top: -7px; display:inline-block;vertical-align: middle;"
							 src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/love.png">
						<span style="font-size: 22px;"><?php echo $entry['text']; ?></span>
						<img style="height: 22px; line-height: 22px; margin-top: -7px; display:inline-block;vertical-align: middle;"
							 src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/love.png">
					<?php } else { ?>
						<span style=""><?php echo $entry['text']; ?></span>
					<?php } ?>
				</p>
			<?php } ?>
			<p style="">
				<a href="<?php echo esc_url(outfit_get_page_url('submit_ad')); ?>">
					<img style="width: 100%; padding: 0 15px;" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/email_img1.jpg">
				</a>
			</p>
		</div>
		<?php
		include(TEMPLATEPATH . '/templates/email/email-footer.php');
		$message = ob_get_contents();
		ob_end_clean();

		wp_mail($email, $email_subject, $message);
		//wp_mail("milla@originalconcepts.co.il", $email_subject, $message);

	}
}
/*==========================
 Email to Admin On New User Registration
 ===========================*/
if(!function_exists('outfitNewUserNotifiy')) { 
	function outfitNewUserNotifiy($email, $username){
		$blog_title = get_bloginfo('name');
		$blog_url = esc_url( home_url() ) ;
		$adminEmail =  get_bloginfo('admin_email');
		global $redux_demo;
		$outfitEmailIMG = $redux_demo['outfit_email_header_img']['url'];
		
		$email_subject = "New User Has been Registered On ".$blog_title;
		
		ob_start();	
		include(get_template_directory() . '/templates/email/email-header.php');
		
		?>
		<div class="classiera-email-welcome" style="direction: rtl;padding: 50px 0; background: url('<?php echo esc_url($outfitEmailIMG); ?>'); background-size: cover; background-image:url('<?php echo esc_url($outfitEmailIMG); ?>'); background-repeat:repeat-x;">
			<h4 style="font-size:18px; color: #232323; text-align: center; font-family: 'Ubuntu', sans-serif; font-weight: normal; text-transform: uppercase;"><?php esc_html_e( 'New User has been Registered !', 'outfit-standalone' ); ?></h4>
			<span class="email-seprator" style="width:100px; height: 2px; background: #b6d91a; margin: 0 auto; display: block;"></span>
			<h3 style="font-family: 'Ubuntu', sans-serif; font-size:24px; text-align: center; text-transform: uppercase;">
				<?php esc_html_e( 'Hello Admin, New User Registred on', 'outfit-standalone' ); ?>, <?php echo esc_html($blog_title) ?>
			</h3>
		</div>
		<div class="classiera-email-content" style="padding: 50px 0; width:600px; margin:0 auto;">
			<p style="">
				<?php esc_html_e( 'Hello, New User has been Registred on', 'outfit-standalone' ); ?>, <?php echo esc_html($blog_title) ?>. <?php esc_html_e( 'By using this email', 'outfit-standalone' ); ?> <?php echo sanitize_email($email); ?>!
			</p>
			<p>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #232323;direction: rtl;"><?php esc_html_e( 'His User name is:', 'outfit-standalone' ); ?> : </span>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #0d7cb0;direction: rtl;">
					<?php echo esc_attr($username); ?>
				</span>
			</p>
		</div>	
		<?php
		
		include(get_template_directory() . '/templates/email/email-footer.php');
		
		$message = ob_get_contents();
		ob_end_clean();
		if( function_exists('outfit_send_wp_mail')){
			outfit_send_wp_mail($adminEmail, $email_subject, $message);
		}
	}
}

/*==========================
	Rejected Post Status Function
 ===========================*/
if(!function_exists('outfitRejectedPost')) {
	function outfitRejectedPost( $new_status, $old_status, $post ){
		if ($new_status == 'rejected'){		
			$author = get_userdata($post->post_author);		
			$author_email = $author->user_email;
			$author_display = $author->user_login;
			$blog_title = get_bloginfo('name');
			global $redux_demo;
			$outfitEmailIMG = $redux_demo['outfit_email_header_img']['url'];
			$email_subject = esc_html__( 'Your Ad is Rejected..!', 'outfit-standalone' );
			$adminEmail =  get_bloginfo('admin_email');	
			ob_start();
			include(TEMPLATEPATH . '/templates/email/email-header.php');
			?>
			<div class="classiera-email-welcome" style="direction: rtl;padding: 50px 0; background: url('<?php echo esc_url($outfitEmailIMG); ?>'); background-size: cover; background-image:url('<?php echo esc_url($outfitEmailIMG); ?>'); background-repeat:repeat-x;">
				<h4 style="font-size:18px; color: #232323; text-align: center; font-family: 'Ubuntu', sans-serif; font-weight: normal; text-transform: uppercase;"><?php echo esc_html($email_subject); ?></h4>
				<span class="email-seprator" style="width:100px; height: 2px; background: #b6d91a; margin: 0 auto; display: block;"></span>
				<h3 style="font-family: 'Ubuntu', sans-serif; font-size:24px; text-align: center; text-transform: uppercase;">
					<?php esc_html_e( 'Hello', 'outfit-standalone' ); ?>, <?php echo esc_attr($author_display); ?>
				</h3>
			</div>
			<div class="classiera-email-content" style="direction: rtl;padding: 50px 0; width:600px; margin:0 auto;">
				<p style="">
					<?php esc_html_e( 'We want to inform you, your ad is rejected, which you have posts on', 'outfit-standalone' ); ?> &nbsp;<?php echo get_bloginfo('name'); ?>!
				</p>
				<p style=""><?php esc_html_e( 'Please visit your Dashboard to see post status, For more information contact with website admin at this email.', 'outfit-standalone' ); ?> <a href="mailto:<?php echo sanitize_email($adminEmail); ?>"><?php echo sanitize_email($adminEmail); ?></a> </p>
			</div>
			<?php
			include(TEMPLATEPATH . '/templates/email/email-footer.php');
			$message = ob_get_contents();
			ob_end_clean();		
			
			if( function_exists('outfit_send_wp_mail')){
				outfit_send_wp_mail($author_email, $email_subject, $message);
			}
		}
	}
	//add_action(  'transition_post_status',  'outfitRejectedPost', 10, 3 );
}
/*==========================
	Email to Post Author
 ===========================*/
if(!function_exists('contactToAuthor')) { 
	function contactToAuthor($emailTo, $subject, $name, $email, $comments, $headers, $outfitPostTitle, $outfitPostURL) {	

		$blog_title = get_bloginfo('name');
		$blog_url = esc_url( home_url() ) ;
		$adminEmail =  get_bloginfo('admin_email');
		global $redux_demo;
		$outfitEmailIMG = $redux_demo['outfit_email_header_img']['url'];
		
		$email_subject = $subject;
		
		ob_start();	
		include(get_template_directory() . '/templates/email/email-header.php');
		
		?>
		<div class="classiera-email-welcome" style="direction: rtl;padding: 50px 0; background: url('<?php echo esc_attr($outfitEmailIMG); ?>'); background-size: cover; background-image:url('<?php echo esc_attr($outfitEmailIMG); ?>'); background-repeat:repeat-x;">
			<h4 style="font-size:18px; color: #232323; text-align: center; font-family: 'Ubuntu', sans-serif; font-weight: normal; text-transform: uppercase;"><?php echo esc_attr($email_subject); ?></h4>
			<span class="email-seprator" style="width:100px; height: 2px; background: #b6d91a; margin: 0 auto; display: block;"></span>
			<h3 style="font-family: 'Ubuntu', sans-serif; font-size:24px; text-align: center; text-transform: uppercase;">
				<?php esc_html_e( 'You have received email from', 'outfit-standalone' ); ?>, <?php echo esc_attr($name); ?>
			</h3>
		</div>
		<div class="classiera-email-content" style="direction: rtl;padding: 50px 0; width:600px; margin:0 auto;">
			<h3 style="font-family: 'Ubuntu', sans-serif; font-size:24px; font-weight: normal; text-transform: capitalize;">
				<?php esc_html_e( 'Your have received this email from', 'outfit-standalone' ); ?>
			</h3>
			<p><?php echo esc_html($comments); ?></p>
			<p>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #232323;"><?php esc_html_e( 'Sender Name', 'outfit-standalone' ); ?> : </span>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #0d7cb0;">
				<?php echo esc_attr($name);?></span>
			</p>
			<p>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #232323;"><?php esc_html_e( 'Sender Email', 'outfit-standalone' ); ?> : </span>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #0d7cb0;">
				<?php echo sanitize_email($email);?></span>
			</p>
			<p>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #232323;"><?php esc_html_e( 'Your Post Title', 'outfit-standalone' ); ?> : </span>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #0d7cb0;">
				<?php echo esc_html($outfitPostTitle);?></span>
			</p>
			<p>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #232323;"><?php esc_html_e( 'Your Post URL', 'outfit-standalone' ); ?> : </span>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #0d7cb0;"><?php echo esc_url($outfitPostURL);?></span>
			</p>
		</div>	
		<?php
		
		include(get_template_directory() . '/templates/email/email-footer.php');
		
		$message = ob_get_contents();
		ob_end_clean();
		
		if (function_exists('outfit_send_mail_with_headers')) {
			outfit_send_mail_with_headers($emailTo, $email_subject, $message, $headers);
		}
	}
}
/*==========================
	Reset Password email
 ===========================*/	
if(!function_exists('outfit_reset_password_email')) {
	function outfit_reset_password_email($new_password, $userEmail ){
		$blog_title = get_bloginfo('name');
		$blog_url = esc_url( home_url() ) ;
		$emailTo = $userEmail;
		$adminEmail =  get_bloginfo('admin_email');
		global $redux_demo;
		$email_subject = esc_html__( 'Password Reset', 'outfit-standalone' );
		
		ob_start();
		include(get_template_directory() . '/templates/email/email-header.php');
		?>

		<div class="classiera-email-content" style="padding: 20px 0; width:100%; margin:0 auto;">
			<h1><?php echo esc_html($redux_demo['password_reset_title']); ?>
			</h1>
			<p style="">
				<?php echo 'הסיסמה החדשה שלך: '; ?>
				<?php echo esc_attr($new_password); ?>
			</p>
		</div>

		<?php
		include(get_template_directory() . '/templates/email/email-footer.php');
		$message = ob_get_contents();
		ob_end_clean();	

		wp_mail($emailTo, $email_subject, $message, ["From: $blog_title <$adminEmail>"]);
		//wp_mail("milla@originalconcepts.co.il", $email_subject, $message, ["From: $blog_title <$adminEmail>"]);
	}	
}

/*==========================
	Report Ad to Admin
 ===========================*/
if(!function_exists('outfit_reportAdtoAdmin')) {
	function outfit_reportAdtoAdmin($message, $outfitPostTitle, $outfitPostURL){
		$blog_title = get_bloginfo('name');
		$blog_url = esc_url( home_url() ) ;	
		$adminEmail =  get_bloginfo('admin_email');
		global $redux_demo;
		$outfitEmailIMG = $redux_demo['outfit_email_header_img']['url'];
		$email_subject = esc_html__( 'Report Ad Notification!', 'outfit-standalone' );
		
		ob_start();
		include(get_template_directory() . '/templates/email/email-header.php');
		?>
		<div class="classiera-email-welcome" style="direction: rtl;padding: 50px 0; background: url('<?php echo esc_url($outfitEmailIMG); ?>'); background-size: cover; background-image:url('<?php echo esc_url($outfitEmailIMG); ?>'); background-repeat:repeat-x;">
			<h4 style="font-size:18px; color: #232323; text-align: center; font-family: 'Ubuntu', sans-serif; font-weight: normal; text-transform: uppercase;"><?php echo esc_html($email_subject); ?></h4>
			<span class="email-seprator" style="width:100px; height: 2px; background: #b6d91a; margin: 0 auto; display: block;"></span>
			<h3 style="font-family: 'Ubuntu', sans-serif; font-size:24px; text-align: center; text-transform: uppercase;">
				<?php esc_html_e( 'Hello Admin, DMCA/Copyright', 'outfit-standalone' ); ?>
			</h3>
		</div>
		<div class="classiera-email-content" style="direction: rtl;padding: 50px 0; width:600px; margin:0 auto;">
			<p style="">
				<?php esc_html_e( 'Hi Someone Report an Ad which is posted on your website.', 'outfit-standalone' ); ?>
			</p>
			<p>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #232323;">
					<?php esc_html_e( 'Post Title Is', 'outfit-standalone' ); ?> : 
				</span>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #0d7cb0;">
					<?php echo esc_html($outfitPostTitle); ?>
				</span>
			</p>
			<p>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #232323;">
					<?php esc_html_e( 'Post Link', 'outfit-standalone' ); ?> : 
				</span>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #0d7cb0;">
					<?php echo esc_url($outfitPostURL); ?>
				</span>
			</p>
			<p style="">
				<?php echo esc_html($message); ?>
			</p>
		</div>
		<?php
		include(get_template_directory() . '/templates/email/email-footer.php');
		$emilbody = ob_get_contents();
		ob_end_clean();
		if( function_exists('outfit_send_wp_mail')){
			outfit_send_wp_mail($adminEmail, $email_subject, $emilbody);
		}
	}	
}
/*==========================
	Contact us Page email
 ===========================*/
if(!function_exists('outfit_contact_us_page')) {
	function outfit_contact_us_page($name, $email, $submitMobile, $emailTo, $subject, $comments){
		$blog_title = get_bloginfo('name');
		$blog_url = esc_url( home_url() ) ;	
		$adminEmail =  $emailTo;
		global $redux_demo;
		$headers = 'From <'.$adminEmail.'>' . "\r\n" . 'Reply-To: ' . $email;
		$outfitEmailIMG = $redux_demo['outfit_email_header_img']['url'];
		$email_subject = $subject;
		
		ob_start();
		include(get_template_directory() . '/templates/email/email-header.php');
		?>
		<div class="classiera-email-welcome" style="direction: rtl;padding: 50px 0; background: url('<?php echo esc_url($outfitEmailIMG); ?>'); background-size: cover; background-image:url('<?php echo esc_url($outfitEmailIMG); ?>'); background-repeat:repeat-x;">
			<h4 style="font-size:18px; color: #232323; text-align: center; font-family: 'Ubuntu', sans-serif; font-weight: normal; text-transform: uppercase;"><?php echo esc_html($email_subject); ?></h4>
			<span class="email-seprator" style="width:100px; height: 2px; background: #b6d91a; margin: 0 auto; display: block;"></span>
		</div>
		<div class="classiera-email-content" style="direction: rtl;padding: 50px 0; width:600px; margin:0 auto;">
			<p style="">
				<?php echo esc_html($comments); ?>
			</p>
			<p>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #232323;">
					<?php esc_html_e( 'Sender Name', 'outfit-standalone' ); ?> : <?php echo esc_attr($name); ?>
				</span>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #0d7cb0;">
					<?php esc_html_e( 'Sender Email', 'outfit-standalone' ); ?> : <?php echo sanitize_email($email); ?>
				</span>
				<span style="font-family: 'Ubuntu', sans-serif; font-size: 14px; color: #0d7cb0;">
					<?php esc_html_e( 'Sender Phone', 'outfit-standalone' ); ?> : <?php echo esc_attr($submitMobile); ?>
				</span>
			</p>		
		</div>
		<?php
		include(get_template_directory() . '/templates/email/email-footer.php');
		$emilbody = ob_get_contents();
		ob_end_clean();
		if (function_exists('outfit_send_mail_with_headers')) {
			outfit_send_mail_with_headers($adminEmail, $email_subject, $emilbody, $headers);
		}
	}
}
?>
