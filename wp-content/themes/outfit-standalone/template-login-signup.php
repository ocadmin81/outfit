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

if ( is_user_logged_in() ) { 

	global $redux_demo; 
	$profile = $redux_demo['profile'];
	wp_redirect( $profile ); exit;

}

global $user_ID, $username, $password, $remember;

//We shall SQL escape all inputs
$username = esc_sql(isset($_REQUEST['username']) ? $_REQUEST['username'] : '');
$password = esc_sql(isset($_REQUEST['password']) ? $_REQUEST['password'] : '');
$remember = esc_sql(isset($_REQUEST['rememberme']) ? $_REQUEST['rememberme'] : '');
	
if($remember) $remember = "true";
else $remember = "false";
$login_data = array();
$login_data['user_login'] = $username;
$login_data['user_password'] = $password;
$login_data['remember'] = $remember;
$user_verify = wp_signon( $login_data, false ); 
//wp_signon is a wordpress function which authenticates a user. It accepts user info parameters as an array.
if(isset($_POST['op_outfit']) ){
	if($_POST['submit'] == 'Login'){
		if ( is_wp_error($user_verify) ) {
			$UserError = "Invalid username or password. Please try again!";
		} else {

			global $redux_demo; 
			$profile = $redux_demo['profile'];
			wp_redirect( $profile ); exit;

		}
	}
}	
global $redux_demo; 
$login = $redux_demo['login'];
$outfitSocialLogin = $redux_demo['outfit_social_login'];
$termsandcondition = $redux_demo['termsandcondition'];
$outfitEmailVerify = $redux_demo['registor-email-verify'];
$outfitSocialLogin = $redux_demo['outfit_social_login'];
$rand1 = rand(0,9);
$rand2 = rand(0,9);
$rand_answer = $rand1 + $rand2;


global $resetSuccess;

if (!$user_ID){
	if(isset($_POST['op_outfit']) ){
		if($_POST['submit'] == 'Reset'){

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

						$message = "Check your email for new password.";

						$from = get_option('admin_email');
						$headers = 'From: '.$from . "\r\n";
						$subject = "Password reset!";
						$msg = "Reset password.\nYour login details\nNew Password: $new_password";
						if (function_exists('outfit_send_mail_with_headers')) {
							outfit_send_mail_with_headers($email_addr, $subject, $msg, $headers);
						}

						$resetSuccess = 1;

					}

				} else {

					$message = "There is no user available for this email.";

				} // end if/else

			} else {
				$message = "Email should not be empty.";
			}

		}
		if($_POST['submit'] == 'Register'){
			
			$message =  esc_html__( 'Registration successful.', 'outfit-standalone' );

			$username = $wpdb->escape($_POST['username']);

			$email = $wpdb->escape($_POST['email']);

			$password = $wpdb->escape($_POST['password']);

			$confirm_password = $wpdb->escape($_POST['confirm']);
			
			$remember = $wpdb->escape($_POST['remember']);

			$registerSuccess = 1;


			if(!empty($remember)) {			
				
				if(empty($username)){					
					$message =  esc_html__( 'User name should not be empty.', 'outfit-standalone' );
					$registerSuccess = 0;
				}

				if(isset($email)) {

					if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)){ 

						wp_update_user( array ('ID' => $user_ID, 'user_email' => $email) ) ;

					}else { 				 
						$message =  esc_html__( 'Please enter a valid email.', 'outfit-standalone' );
						$registerSuccess = 0;
					}				

				}else{
					$registerSuccess = 0;
					$message =  esc_html__( 'Please enter a valid email.', 'outfit-standalone' );
				}
				/*If Admin Turn Of Email Verification then this code will work*/
				if($password){

					if (strlen($password) < 5 || strlen($password) > 15) {						
						$message =  esc_html__( 'Password must be 5 to 15 characters in length.', 'outfit-standalone' );
						$registerSuccess = 0;
						
					}elseif(isset($password) && $password != $confirm_password) {
						
						$message =  esc_html__( 'Password Mismatch', 'outfit-standalone' );

						$registerSuccess = 0;

					}elseif ( isset($password) && !empty($password) ) {

						$update = wp_set_password( $password, $user_ID );						
						$message =  esc_html__( 'Registration successful', 'outfit-standalone' );
						$registerSuccess = 1;

					}

				}
				
				$status = wp_create_user( $username, $password, $email );
				if ( is_wp_error($status) ) {
					$registerSuccess = 0;
					
					$message =  esc_html__( 'Username or E-mail already exists. Please try another one.', 'outfit-standalone' );
				}else{					
					outfitUserNotification( $email, $password, $username );			
					global $redux_demo; 
					$newUsernotification = $redux_demo['newusernotification'];	
					if($newUsernotification == 1){
						outfitNewUserNotifiy($email, $username);	
					}
					$registerSuccess = 1;
				}
				
				/*If Turn OFF Email verification*/
				if($registerSuccess == 1) {
					$login_data = array();
					$login_data['user_login'] = $username;
					$login_data['user_password'] = $password;
					$user_verify = wp_signon( $login_data, false );
					global $redux_demo; 
					$profile = $redux_demo['profile'];
					wp_redirect( $profile ); exit;
					
				}
			}else{			
				$message =  esc_html__( 'You Must Need to Agree With Terms And Conditions.', 'outfit-standalone' );
				$registerSuccess = 0;
			}
		}
	}
}
?>
<?php get_header(); ?>
<?php 
	$page = get_post($post->ID);
	$current_page_id = $page->ID;
?>
<!-- page content -->
<img class="login-bg" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/login-bg.jpg" />
<section class="inner-page-content top-pad-50 login-page">
	<div class="login-register">
		<div class="wrap">
				<div class="center-block">
					<div class="outfit-login-register-heading text-center">
						<?php the_custom_logo(); ?>
					</div>
					<div class="tabs">
						<div id="tab1"><?php esc_html_e('Enter your Account', 'outfit-standalone') ?></div>
						<div id="tab2"><?php esc_html_e('Join', 'outfit-standalone') ?></div>
					</div>
						<div class="login-section">
							<a class="loginSocialbtn fb" href="<?php echo get_site_url(); ?>/wp-login.php?loginFacebook=1" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginFacebook=1&redirect='+window.location.href; return false;"><i class="fab fa-facebook-f"></i><?php esc_html_e('Login via Facebook', 'outfit-standalone') ?></a>
							<?php if($outfitSocialLogin == 1){?>
							<div class="social-login border-bottom">
                                <h5 class="text-uppercase">
									<?php esc_html_e('Login or Signup With Social Media', 'outfit-standalone') ?>
								</h5>
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
								<div class=" social-login-or">
                                    <span><?php esc_html_e('OR', 'outfit-standalone') ?></span>
                                </div>
                            </div><!--social-login-->
							<?php } ?>
							<form data-toggle="validator" role="form" method="POST" enctype="multipart/form-data">
								<div class="form-group">
                                    <div class="inner-addon left-addon">
                                        <i class="left-addon form-icon fa fa-user"></i>
                                        <input type="text" class="form-control form-control-md sharp-edge" name="username" placeholder="<?php esc_html_e( 'Email', 'outfit-standalone' ); ?>" data-error="<?php esc_html_e( 'Username is required', 'outfit-standalone' ); ?>" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div><!--username-->
								<div class="form-group">
                                    <div class="inner-addon left-addon">
                                        <i class="left-addon form-icon fa fa-lock"></i>
                                        <input type="password" name="password" class="form-control form-control-md sharp-edge" placeholder="<?php esc_html_e( 'Enter Password', 'outfit-standalone' ); ?>" data-error="<?php esc_html_e( 'Password required', 'outfit-standalone' ); ?>" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div><!--Password-->
								<div class="form-group">
                                    <div class="checkbox">
                                        <input type="checkbox" id="remember" name="rememberme" value="forever">
                                        <label for="remember"><?php esc_html_e( 'Remember me', 'outfit-standalone' ); ?></label>
                                    </div>
                                </div><!--remember-->
								<div class="form-group">
									<input type="hidden" name="submit" value="Login" id="submit" />
									<button class="btn btn-primary sharp btn-md btn-style-one" id="edit-submit" name="op_outfit" type="submit"><?php esc_html_e('LOGIN NOW', 'outfit-standalone') ?></button>
                                </div><!--loginbutton-->
							</form>
						</div><!--col-lg-6-->
						<!--Register-->
						<div class="reg-section">
                            <div class="social-login-v2">
                                <h5 class="text-uppercase"><?php esc_html_e('Register an Account', 'outfit-standalone') ?></h5>
                            </div>
							<?php if($_POST){?>
							<?php if($registerSuccess == 0){?>
							<div class="alert alert-danger" role="alert">
							  <strong><?php esc_html_e('Oh snap!', 'outfit-standalone') ?></strong> <?php echo esc_html( $message ); ?>
							</div>
							<?php } ?>
							<?php } ?>
                            <form data-toggle="validator" role="form" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <div class="inner-addon left-addon">
                                        <i class="left-addon form-icon fa fa-user"></i>
                                        <input type="text" name="username" class="form-control form-control-md sharp-edge" placeholder="<?php esc_html_e('Enter username', 'outfit-standalone') ?>" data-error="<?php esc_html_e('username required', 'outfit-standalone') ?>" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div><!--username-->
                                <div class="form-group">
                                    <div class="inner-addon left-addon">
                                        <i class="left-addon form-icon fas fa-envelope"></i>
                                        <input type="email" name="email" class="form-control form-control-md sharp-edge" placeholder="<?php esc_html_e('Enter email address', 'outfit-standalone') ?>" data-error="<?php esc_html_e('email required', 'outfit-standalone') ?>" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div><!--Email Address-->
                                <div class="form-group">
                                    <div class="inner-addon left-addon">
                                        <i class="left-addon form-icon fa fa-lock"></i>
                                        <input type="password" name="password" data-minlength="5" class="form-control form-control-md sharp-edge" placeholder="<?php esc_html_e('Enter password', 'outfit-standalone') ?>" id="registerPass" data-error="<?php esc_html_e('password required', 'outfit-standalone') ?>" required>
                                        <div class="help-block"><?php esc_html_e('Minimum of 5 characters.', 'outfit-standalone') ?></div>
                                    </div>
                                </div><!--Password-->
                                <div class="form-group">
                                    <div class="inner-addon left-addon">
                                        <i class="left-addon form-icon fa fa-lock"></i>
                                        <input type="password" name="confirm" class="form-control form-control-md sharp-edge" placeholder="<?php esc_html_e('re-enter password', 'outfit-standalone') ?>" data-match="#registerPass" data-match-error="<?php esc_html_e('Whoops, these dont match', 'outfit-standalone') ?>" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div><!--re-enter password-->
                                <div class="form-group">
                                    <div class="checkbox">
                                        <input type="checkbox" value="forever" name="remember" id="agree" data-error="<?php esc_html_e('you must need to agree with our terms and condition', 'outfit-standalone') ?>" required>
                                        <label for="agree"><?php esc_html_e('Agree to', 'outfit-standalone') ?> <a href="<?php echo esc_url( $termsandcondition ); ?>" target="_blank"><?php esc_html_e('terms & Condition', 'outfit-standalone') ?></a></label>
                                        <div class="left-side help-block with-errors"></div>
                                    </div>
                                </div><!--Agreed-->
                                <div class="form-group">
									<input type="hidden" name="submit" value="Register" id="submit" />
									<button class="btn btn-primary sharp btn-md btn-style-one" id="edit-submit" name="op_outfit" type="submit"><?php esc_html_e('Register', 'outfit-standalone') ?></button>
                                </div><!--register button-->
                            </form>
                        </div>
						<!--Register-->
                        <div class="forgot-section">
                            <div class="social-login-v2">
                                <h5 class="text-uppercase"><?php esc_html_e('Reset Your Password', 'outfit-standalone') ?></h5>
                            </div>
                            <form data-toggle="validator" role="form" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <div class="inner-addon left-addon">
                                        <i class="left-addon form-icon fas fa-envelope"></i>
                                        <input type="email" name="email" class="form-control form-control-md sharp-edge" placeholder="<?php esc_html_e( 'enter email address', 'outfit-standalone' ); ?>" data-error="<?php esc_html_e( 'email required', 'outfit-standalone' ); ?>" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div><!--email-->
                                <div class="form-group">
									<input type="hidden" name="submit" value="Reset" id="submit" />
									<button class="btn btn-primary sharp btn-md btn-style-one" id="edit-submit" name="op_outfit" type="submit"><?php esc_html_e('Get password', 'outfit-standalone') ?></button>
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