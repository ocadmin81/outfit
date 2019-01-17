<?php
/**
 * Template name: Login / Register
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage outfit-standalone
 * @since outfit-standalone 0.1
 */

if (isset($_GET['back_to']) && !empty($_GET['back_to'])) {
	$redirectAfterSuccess = $_GET['back_to'];
}
else {
	//$redirectAfterSuccess = outfit_get_page_url('profile_settings');
	$redirectAfterSuccess = outfit_get_page_url('user_all_ads');
}

if ( is_user_logged_in() ) {

	wp_redirect( $redirectAfterSuccess );
	exit;

}

//global $user_ID, $username, $password, $remember;
global $redux_demo;
$login = outfit_get_page_url('login');
$outfitSocialLogin = $redux_demo['outfit_social_login'];
//$termsandcondition = $redux_demo['termsandcondition'];
$termsandcondition = '/תנאי-שימוש-באתר';
$outfitEmailVerify = $redux_demo['registor-email-verify'];
$outfitSocialLogin = $redux_demo['outfit_social_login'];
$rand1 = rand(0,9);
$rand2 = rand(0,9);
$rand_answer = $rand1 + $rand2;
global $resetSuccess;
$login_message = $register_message = $reset_message = '';

if(isset($_POST['op_outfit']) && isset($_POST['submit'])) {
	// login
	if($_POST['submit'] == 'Login') {
		$username = esc_sql(isset($_POST['username']) ? $_POST['username'] : '');
		$password = esc_sql(isset($_POST['password']) ? $_POST['password'] : '');
		$login_data = array();
		$login_data['user_login'] = $username;
		$login_data['user_password'] = $password;
		$login_data['remember'] = false;
		$user_verify = wp_signon( $login_data, false );
		if ( is_wp_error($user_verify) ) {
			$login_message = "שם משתמש או סיסמה שגויים. נא לנסות שוב";
		} else {

			if (isset($_GET['favorite'])) {
				$pid = (int) $_GET['favorite'];
				if (!empty($pid)) {
					outfit_insert_author_favorite($user_verify->ID, $pid);
				}
			}
			wp_redirect( $redirectAfterSuccess );
			exit;

		}
	}
	else if ($_POST['submit'] == 'Reset') {
		// First, make sure the email address is set
		if ( isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ) {

			// Next, sanitize the data
			$email_addr = trim( strip_tags( stripslashes( $_POST['email'] ) ) );

			$user = get_user_by( 'email', $email_addr );
			$user_ID = $user->ID;

			if( !empty($user_ID)) {

				$new_password = wp_generate_password( 12, false );

				if ( isset($new_password) ) {

					wp_set_password( $new_password, $user_ID );

					$reset_message = "בדוק את תיבת המייל שלך";

					$from = get_option('admin_email');
					$headers = 'From: '.$from . "\r\n";
					$subject = "Password reset!";
					$msg = "Reset password.\nYour login details\nNew Password: $new_password";
					/*if (function_exists('outfit_send_mail_with_headers')) {
                        outfit_send_mail_with_headers($email_addr, $subject, $msg, $headers);
                    }*/
					outfit_reset_password_email($new_password, $email_addr);

					$resetSuccess = 1;

				}

			} else {

				$reset_message = "אין שם משתמש עם כתובת מייל זה";

			} // end if/else

		} else {
			$reset_message = "כתבות מייל הוא שדה חובה";
		}
	}
	else if($_POST['submit'] == 'Register'){

		$register_message =  esc_html__( 'Registration successful.', 'outfit-standalone' );

		//$username = $wpdb->escape($_POST['username']);
		//$email = $wpdb->escape($_POST['email']);
		//$username = isset($_POST['username']) ? trim( wp_unslash( $_POST['username'] ) ) : '';
		$username = '';
		$email  = isset( $_POST['email']  ) ? trim( wp_unslash( $_POST['email'] ) ) : null;

		$firstname = isset( $_POST['firstname']  ) ? trim( wp_unslash( $_POST['firstname'] ) ) : '';
		$lastname = isset( $_POST['lastname']  ) ? trim( wp_unslash( $_POST['lastname'] ) ) : '';

		//$password = $wpdb->escape($_POST['password']);
		//$confirm_password = $wpdb->escape($_POST['confirm']);

		$password = isset( $_POST['password']  ) ? trim( $_POST['password'] ) : null;
		$confirm_password = isset( $_POST['confirm']  ) ? trim( $_POST['confirm'] ) : '';

		$agree = $wpdb->escape($_POST['agree']);

		$registerSuccess = 1;


		if(!empty($agree)) {

			/*if(empty($username)){
                $message =  esc_html__( 'User name should not be empty.', 'outfit-standalone' );
                $registerSuccess = 0;
            }
            elseif ( $username != sanitize_user( $username, true ) ) {
                $message =  esc_html__( 'The username you provided has invalid characters.', 'outfit-standalone' );
                $registerSuccess = 0;
            }
            else*/
			if(empty($firstname)){
				$register_message =  esc_html__( 'שם פרטי הוא שדה חובה', 'outfit-standalone' );
				$registerSuccess = 0;
			}
			elseif(empty($lastname)){
				$register_message =  esc_html__( 'שם משפחה הוא שדה חובה', 'outfit-standalone' );
				$registerSuccess = 0;
			}
			if ($registerSuccess) {
				if(isset($email)) {

					if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)){

						//wp_update_user( array ('ID' => $user_ID, 'user_email' => $email) ) ;
						$username = $email;

					}else {
						$register_message =  esc_html__( 'יש להכניס כתובת מייל תקינה', 'outfit-standalone' );
						$registerSuccess = 0;
					}

				}else{
					$registerSuccess = 0;
					$register_message =  esc_html__( 'יש להכניס כתובת מייל תקינה', 'outfit-standalone' );
				}
			}
			if ($registerSuccess) {
				/*If Admin Turn Of Email Verification then this code will work*/
				if($password){

					if (strlen($password) < 5 || strlen($password) > 15) {
						$register_message =  esc_html__( 'סיסמא חייבת להיות בין 5-15 תווים', 'outfit-standalone' );
						$registerSuccess = 0;

					}elseif(isset($password) && $password != $confirm_password) {

						$register_message =  esc_html__( 'הסיסמאות אינן תואמות', 'outfit-standalone' );

						$registerSuccess = 0;

					}elseif ( isset($password) && !empty($password) ) {

						wp_set_password( $password, $user_ID );
						$register_message =  esc_html__( 'נרשמת בהצלחה', 'outfit-standalone' );
						$registerSuccess = 1;

					}

				}
			}

			if ($registerSuccess) {
				$status = wp_create_user( $username, $password, $email );
				if ( is_wp_error($status) ) {
					$registerSuccess = 0;
					$register_message =  esc_html__( 'שם משתמש או כתובת מייל כבר קיים.', 'outfit-standalone' );
				}
			}

			if ( $registerSuccess ) {

				update_user_meta($status, USER_META_FIRSTNAME, $firstname);
				update_user_meta($status, USER_META_LASTNAME, $lastname);
				outfitUserNotification( $email, $password, $status );
				//global $redux_demo;
				//$newUsernotification = $redux_demo['newusernotification'];
				//if($newUsernotification == 1){
				//	outfitNewUserNotifiy($email, $username);
				//}
			}

			/*If Turn OFF Email verification*/
			if($registerSuccess == 1) {
				$login_data = array();
				$login_data['user_login'] = $username;
				$login_data['user_password'] = $password;
				$user_verify = wp_signon( $login_data, false );
				if (isset($_GET['favorite'])) {
					$pid = (int) $_GET['favorite'];
					if (!empty($pid)) {
						outfit_insert_author_favorite($user_verify->ID, $pid);
					}
				}
				wp_redirect( $redirectAfterSuccess );
				exit;

			}
		}else{
			$register_message =  esc_html__( 'יש לאשר את תנאי השימוש.', 'outfit-standalone' );
			$registerSuccess = 0;
		}
	}
}	

?>
<?php get_header(); ?>
<?php 
	$page = get_post($post->ID);
	$current_page_id = $page->ID;
	$isRegistration = (isset($_POST['submit']) && $_POST['submit'] == 'Register');
	$isReset = (isset($_POST['submit']) && $_POST['submit'] == 'Reset');
?>
<!-- page content -->
<img class="login-bg" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/login-bg.jpg" />
<section class="inner-page-content top-pad-50 login-page">
	<div class="login-register">
		<div class="wrap">
				<div class="outfit-login-register-heading text-center">
					<?php the_custom_logo(); ?>
				</div>		
				<div class="center-block">
					<div class="tabs">
						<div id="tab1" class="<?php echo ($isRegistration? '' : 'active') ?>"><?php esc_html_e('כניסה לחשבונך', 'outfit-standalone') ?></div>
						<div id="tab2" class="<?php echo ($isRegistration? 'active' : '') ?>"><?php esc_html_e('הצטרפות', 'outfit-standalone') ?></div>
					</div>

						<div class="login-section <?php echo ($isRegistration? '' : 'active') ?>">
							<div class="social-login-link">
								<a class="fb-login fb" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=facebook"
								   data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="facebook"
								   data-popupwidth="475" data-popupheight="175"><?php esc_html_e('כניסה באמצעות פייסבוק', 'outfit-standalone') ?></a>
								<?php if($outfitSocialLogin == 1){?>							
                                <!--Social Plugins-->								
								<?php if(class_exists('NextendSocialLogin', false)){ ?>
								<!--Nextend Facebook-->
									<a class="loginSocialbtn fb" href="<?php echo get_site_url(); ?>/wp-login.php?loginFacebook=1" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginFacebook=1&redirect='+window.location.href; return false;"><i class="fab fa-facebook-f"></i><?php esc_html_e('Login via Facebook', 'outfit-standalone') ?></a>
								<!--Nextend Twitter-->
									<a class="loginSocialbtn twitter" href="<?php echo get_site_url(); ?>/wp-login.php?loginTwitter=1" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginTwitter=1&redirect='+window.location.href; return false;"><i class="fab fa-twitter"></i><?php esc_html_e('Login via Twitter', 'outfit-standalone') ?></a>
								<!--Nextend Google-->
									<a class="loginSocialbtn google" href="<?php echo get_site_url(); ?>/wp-login.php?loginGoogle=1" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginGoogle=1&redirect='+window.location.href; return false;"><i class="fab fa-google-plus-g"></i><?php esc_html_e('Login via Google', 'outfit-standalone') ?></a>
									<?php
								}
								?>
								<!--AccessPress Socil Login-->
								<?php echo do_shortcode('[apsl-login-lite]'); ?>
								<?php //echo do_shortcode('[apsl-login]'); ?>
								<!--AccessPress Socil Login-->
                                <!--Social Plugins-->
								<?php } ?>
								<div class=" social-login-or">
                                    <span><?php esc_html_e('או', 'outfit-standalone') ?></span>
                                </div>								
                            </div><!--social-login-->							
							<form data-toggle="validator" role="form" method="POST" enctype="multipart/form-data">
								<?php if (!empty($login_message)) { ?>
								<div class="alert alert-danger" role="alert">
									<?php echo esc_html( $login_message ); ?>
								</div>
								<?php } ?>
								<div class="form-group">
                                    <div class="inner-addon left-addon">
                                        <input type="text" class="form-control form-control-md sharp-edge" name="username"
											   value="<?php echo esc_html(isset($username)? wp_unslash($username) : '') ?>"
											   placeholder="<?php esc_html_e( 'אימייל', 'outfit-standalone' ); ?>" data-error="<?php esc_html_e( 'זה שדה חובה', 'outfit-standalone' ); ?>" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div><!--username-->
								<div class="form-group">
                                    <div class="inner-addon left-addon">
                                        <input type="password" name="password" class="form-control form-control-md sharp-edge" placeholder="<?php esc_html_e( 'סיסמא', 'outfit-standalone' ); ?>" data-error="<?php esc_html_e( 'זה שדה חובה', 'outfit-standalone' ); ?>" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div><!--Password
								<div class="form-group">
                                    <div class="checkbox">
                                        <input type="checkbox" id="remember" name="rememberme" value="forever">
                                        <label for="remember"><?php esc_html_e( 'זכור אותי', 'outfit-standalone' ); ?></label>
                                    </div>
                                </div>--><!--remember-->
								<div class="form-group form-group-forgot">
									<a href="#"><?php esc_html_e( 'שכחת את סיסמתך?', 'outfit-standalone' ); ?></a>
								</div>
								<div class="form-group">
									<input type="hidden" name="submit" value="Login" id="submit" />
									<button class="btn btn-primary sharp btn-user btn-md btn-style-one" id="edit-submit" name="op_outfit" type="submit"><?php esc_html_e('כניסה', 'outfit-standalone') ?></button>
                                </div><!--loginbutton-->
								<div class="form-group form-group-reg-link">
									<a href="#"><?php esc_html_e( 'להצטרפות לחצו כאן', 'outfit-standalone' ); ?> >></a>
								</div>								
							</form>
						</div><!--col-lg-6-->
						<!--Register-->
						<div class="reg-section <?php echo ($isRegistration? 'active' : '') ?>">
							<div class="form-group reg-text">
								<?php echo do_shortcode("[do_widget id=text-7]"); ?>
							</div>
							<div class="social-login-link">
								<a class="fb-login fb" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=facebook"
								   data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="facebook"
								   data-popupwidth="475" data-popupheight="175"><?php esc_html_e('כניסה באמצעות פייסבוק', 'outfit-standalone') ?></a>
								<?php if($outfitSocialLogin == 1){?>
                                <!--Social Plugins-->								
								<?php if(class_exists('NextendSocialLogin', false)){ ?>
								<!--Nextend Facebook-->
									<a class="loginSocialbtn fb" href="<?php echo get_site_url(); ?>/wp-login.php?loginFacebook=1" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginFacebook=1&redirect='+window.location.href; return false;"><i class="fab fa-facebook-f"></i><?php esc_html_e('Login via Facebook', 'outfit-standalone') ?></a>
								<!--Nextend Twitter-->
									<a class="loginSocialbtn twitter" href="<?php echo get_site_url(); ?>/wp-login.php?loginTwitter=1" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginTwitter=1&redirect='+window.location.href; return false;"><i class="fab fa-twitter"></i><?php esc_html_e('Login via Twitter', 'outfit-standalone') ?></a>
								<!--Nextend Google-->
									<a class="loginSocialbtn google" href="<?php echo get_site_url(); ?>/wp-login.php?loginGoogle=1" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginGoogle=1&redirect='+window.location.href; return false;"><i class="fab fa-google-plus-g"></i><?php esc_html_e('Login via Google', 'outfit-standalone') ?></a>
									<?php
								}
								?>
								<!--AccessPress Socil Login-->
								<?php echo do_shortcode('[apsl-login-lite]'); ?>
								<?php //echo do_shortcode('[apsl-login]'); ?>
								<!--AccessPress Socil Login-->
                                <!--Social Plugins-->
								<?php } ?>
								<div class=" social-login-or">
                                    <span><?php esc_html_e('או', 'outfit-standalone') ?></span>
                                </div>								
                            </div><!--social-login-->						
                            <form data-toggle="validator" role="form" method="POST" enctype="multipart/form-data">
								<?php if (!empty($register_message)) { ?>
									<div class="alert alert-danger" role="alert">
										<?php echo esc_html( $register_message ); ?>
									</div>
								<?php } ?>
								<div class="form-group">
									<div class="inner-addon left-addon">
										<input type="text" name="firstname"
											   class="form-control form-control-md sharp-edge"
											   placeholder="<?php esc_html_e('שם פרטי', 'outfit-standalone') ?>"
											   data-error="<?php esc_html_e('זה שדה חובה', 'outfit-standalone') ?>"
											   value="<?php echo esc_attr( $firstname ); ?>"
											   required>
										<div class="help-block with-errors"></div>
									</div>
								</div><!--firstname-->
								<div class="form-group">
									<div class="inner-addon left-addon">
										<input type="text" name="lastname"
											   class="form-control form-control-md sharp-edge"
											   placeholder="<?php esc_html_e('שם משפחה', 'outfit-standalone') ?>"
											   data-error="<?php esc_html_e('זה שדה חובה', 'outfit-standalone') ?>"
											   value="<?php echo esc_attr( $lastname ); ?>"
											   required>
										<div class="help-block with-errors"></div>
									</div>
								</div><!--lastname-->
								<!--<div class="form-group">
                                    <div class="inner-addon left-addon">                                        
                                        <input type="text" name="username"
											   class="form-control form-control-md sharp-edge"
											   placeholder="<?php //esc_html_e('שם משתמש', 'outfit-standalone') ?>"
											   data-error="<?php //esc_html_e('זה שדה חובה', 'outfit-standalone') ?>"
											   value="<?php //echo esc_html( sanitize_user( $username, true ) ); ?>"
											   required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div><!--username-->
                                <div class="form-group">
                                    <div class="inner-addon left-addon">                                        
                                        <input type="email" name="email"
											   class="form-control form-control-md sharp-edge"
											   placeholder="<?php esc_html_e('אימייל', 'outfit-standalone') ?>"
											   data-error="<?php esc_html_e('זה שדה חובה', 'outfit-standalone') ?>"
											   value="<?php echo esc_attr( $email ); ?>"
											   required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div><!--Email Address-->
                                <div class="form-group">
                                    <div class="inner-addon left-addon">                                        
                                        <input type="password" name="password" data-minlength="5" class="form-control form-control-md sharp-edge" placeholder="<?php esc_html_e('סיסמא', 'outfit-standalone') ?>" id="registerPass" data-error="<?php esc_html_e('זה שדה חובה', 'outfit-standalone') ?>" required>
                                        <!--<div class="help-block"><?php //esc_html_e('Minimum of 5 characters.', 'outfit-standalone') ?></div>-->
                                    </div>
                                </div><!--Password-->
                                <div class="form-group">
                                    <div class="inner-addon left-addon">                                        
                                        <input type="password" name="confirm" class="form-control form-control-md sharp-edge" placeholder="<?php esc_html_e('סיסמא שוב', 'outfit-standalone') ?>" data-match="#registerPass" data-match-error="<?php esc_html_e('אופס, לא תואם לשדה סיסמא', 'outfit-standalone') ?>" required>
                                        <!--<div class="help-block with-errors"></div>-->
                                    </div>
                                </div><!--re-enter password-->
                                <div class="form-group">
                                    <div class="checkbox">
                                        <input type="checkbox" value="forever" name="agree" id="agree" data-error="<?php esc_html_e('יש להסכים לתנאי השימוש', 'outfit-standalone') ?>" required>
                                        <label for="agree"><?php esc_html_e('מסכימ/ה', 'outfit-standalone') ?> <a href="<?php echo esc_url( $termsandcondition ); ?>" target="_blank"><?php esc_html_e('לתנאי השימוש', 'outfit-standalone') ?></a></label>
                                        <div class="left-side help-block with-errors"></div>
                                    </div>
                                </div><!--Agreed-->
                                <div class="form-group">
									<input type="hidden" name="submit" value="Register" id="submit" />
									<button class="btn btn-primary sharp btn-md btn-style-one" id="edit-submit" name="op_outfit" type="submit"><?php esc_html_e('הצטרפות', 'outfit-standalone') ?></button>
                                </div><!--register button-->
                            </form>
                        </div>
						<!--Register-->
                        <div class="forgot-section <?php echo ($isReset? 'active' : '') ?>">
                            <div class="social-login-v2 forgot-title">
                                <h5 class="text-uppercase"><?php esc_html_e('איפוס סיסמא', 'outfit-standalone') ?></h5>
                            </div>
                            <form data-toggle="validator" role="form" method="POST" enctype="multipart/form-data">
								<?php if (!empty($reset_message)) { ?>
									<div class="alert alert-danger" role="alert">
										<?php echo esc_html( $reset_message ); ?>
									</div>
								<?php } ?>
								<div class="form-group">
                                    <div class="inner-addon left-addon">                                        
                                        <input type="email" name="email" class="form-control form-control-md sharp-edge" placeholder="<?php esc_html_e( 'אימייל', 'outfit-standalone' ); ?>" data-error="<?php esc_html_e( 'זה שדה חובה', 'outfit-standalone' ); ?>" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div><!--email-->
                                <div class="form-group">
									<input type="hidden" name="submit" value="Reset" id="submit" />
									<button class="btn btn-primary sharp btn-md btn-style-one" id="edit-submit" name="op_outfit" type="submit"><?php esc_html_e('קבל סיסמא', 'outfit-standalone') ?></button>
                                </div><!--Button-->
                            </form>
                        </div>
					<!--Reset Password-->
				</div><!--col-lg-10-->
		</div><!--container-->
	</div><!--Login-register-->
</section>
<!-- page content -->
<?php get_footer(); ?>
