<?php
/**
 * Template name: Edit Profile
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage outfit-standalone
 * @since outfit-standalone
 */

if ( !is_user_logged_in() ) {
	global $redux_demo;
	$login = '/login';//$redux_demo['login'];
	wp_redirect( $login ); exit;
}

global $user_ID, $user_identity, $user_level;
global $redux_demo;

$profile = '/profile'; //$redux_demo['profile'];
$current_user = wp_get_current_user();
$user_ID = $current_user->ID;
$user_info = get_userdata($user_ID);

$page = get_post($post->ID);
$current_page_id = $page->ID;

get_header();

?>
<!-- user pages -->
<section class="user-pages section-gray-bg">
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-4">
				<?php get_template_part( 'templates/profile/userabout' );?>
			</div><!--col-lg-3-->
			<div class="col-lg-9 col-md-8 user-content-height">
				<?php if($_POST){?>
					<div class="alert alert-success" role="alert">
						<?php echo esc_html( $message ); ?>
					</div>
				<?php } ?>
				<div class="user-detail-section section-bg-white">
					<div class="user-ads user-profile-settings">
						<h4 class="user-detail-section-heading text-uppercase">
							<?php esc_html_e("Profile Settings", 'classiera') ?>
						</h4>
						<form data-toggle="validator" role="form" method="POST" id="primaryPostForm" action="" enctype="multipart/form-data">
							<!-- upload avatar -->
							<div class="media">
								<div class="media-left uploadImage">
									<?php
									$profileIMGID = get_user_meta($user_ID, "classify_author_avatar_url", true);
									$profileIMG = classiera_get_profile_img($profileIMGID);
									$authorName = get_the_author_meta('display_name', $user_ID);
									$author_verified = get_the_author_meta('author_verified', $user_ID);
									if(empty($profileIMG)){
										$profileIMG = classiera_get_avatar_url ( get_the_author_meta('user_email', $user_ID), $size = '130' );
										?>
										<img class="media-object img-circle author-avatar" src="<?php echo esc_url( $profileIMG ); ?>" alt="<?php echo esc_attr( $authorName ); ?>">
										<?php
									}else{ ?>
										<img class="media-object img-circle author-avatar" src="<?php echo esc_url( $profileIMG ); ?>" alt="<?php echo esc_attr( $authorName ); ?>">
									<?php } ?>
									<input class="criteria-image-url" id="your_image_url" type="text" size="36" name="your_author_image_url" style="display: none;" value="" />
								</div>
								<div class="media-body">
									<h5 class="media-heading text-uppercase">
										<?php esc_html_e( 'Update your Profile Photo', 'classiera' ); ?>
									</h5>
									<p><?php esc_html_e( 'Update your avatar manually,If the not set the default Gravatar will be the same as your login email/user account.', 'classiera' ); ?></p>
									<div class="choose-image">
										<input type="file" id="file-1" name="upload_attachment[]" class="inputfile inputfile-1 author-UP" data-multiple-caption="{count} files selected" multiple />
										<label for="file-1" class="upload-author-image"><i class="fa fa-camera"></i>
											<span><?php esc_html_e( 'Upload photo', 'classiera' ); ?></span>
										</label>
										<!-- verify profile btn-->
										<?php if($author_verified != 'verified'){ ?>
											<button class="classiera_verify_btn" data-toggle="modal" data-target="#verifyModal" type="button"> <i class="fa fa-check"></i> <?php esc_html_e( 'Verify your account', 'classiera' ); ?></button>
										<?php } ?>
										<!-- verify profile btn-->
									</div>
								</div>
							</div><!-- /.upload avatar -->
							<!-- user basic information -->
							<h4 class="user-detail-section-heading text-uppercase">
								<?php esc_html_e( 'Basic Information', 'classiera' ); ?>
							</h4>
							<div class="form-inline row form-inline-margin">
								<div class="form-group col-sm-5">
									<label for="first-name"><?php esc_html_e( 'First Name', 'classiera' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="first-name" name="first_name" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Update first Name..', 'classiera' ); ?>" value="<?php echo esc_attr( $user_info->first_name ); ?>">
									</div>
								</div><!--Firstname-->
								<div class="form-group col-sm-5">
									<label for="last-name"><?php esc_html_e( 'Last Name', 'classiera' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="last-name" name="last_name" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'update last Name', 'classiera' ); ?>" value="<?php echo esc_attr( $user_info->last_name ); ?>">
									</div>
								</div><!--Last name-->
								<div class="form-group col-sm-12">
									<label for="bio"><?php esc_html_e( 'Biography', 'classiera' ); ?></label>
									<div class="inner-addon">
										<textarea name="desc" id="bio" placeholder="<?php esc_html_e( 'enter your short info.', 'classiera' ); ?>"><?php echo esc_html( $user_info->description ); ?></textarea>
									</div>
								</div><!--biography-->
							</div>
							<!-- user basic information -->
							<!-- Contact Details -->
							<h4 class="user-detail-section-heading text-uppercase">
								<?php esc_html_e( 'Contact Details', 'classiera' ); ?>
							</h4>
							<div class="form-inline row form-inline-margin">
								<div class="form-group col-sm-5">
									<label for="phone"><?php esc_html_e( 'Phone Number', 'classiera' ); ?></label>
									<div class="inner-addon">
										<input type="tel" id="phone" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Your Phone No', 'classiera' ); ?>" name="phone" value="<?php echo esc_html( $user_info->phone ); ?>">
									</div>
								</div><!--Phone Number-->
								<div class="form-group col-sm-5">
									<label for="mobile"><?php esc_html_e( 'Mobile Number', 'classiera' ); ?></label>
									<div class="inner-addon">
										<input type="tel" id="mobile" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'update your mobile number', 'classiera' ); ?>" name="phone2" value="<?php echo esc_html( $user_info->phone2 ); ?>">
									</div>
								</div><!--Mobile Number-->
								<div class="form-group col-sm-5">
									<label for="website"><?php esc_html_e( 'Your Website', 'classiera' ); ?></label>
									<div class="inner-addon">
										<input type="url" id="website" name="website" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'enter your website link', 'classiera' ); ?>" value="<?php echo esc_url( $user_info->user_url ); ?>">
									</div>
								</div><!--Your Website-->
								<div class="form-group col-sm-5">
									<label for="email"><?php esc_html_e( 'Your Email', 'classiera' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="email" name="email" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'enter your email address', 'classiera' ); ?>" value="<?php echo sanitize_email( $user_info->user_email ); ?>">
										<input type="hidden" name="current_email" value="<?php echo sanitize_email( $user_info->user_email ); ?>">
									</div>
								</div><!--Your Email-->
								<div class="form-group col-sm-5">
									<label for="email"><?php esc_html_e( 'Your Country', 'classiera' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="country" name="country" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'enter your country', 'classiera' ); ?>" value="<?php echo esc_attr( $user_info->country ); ?>">
									</div>
								</div><!--Your Country-->
								<div class="form-group col-sm-5">
									<label for="email"><?php esc_html_e( 'Your State', 'classiera' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="state" name="state" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'enter your State', 'classiera' ); ?>" value="<?php echo esc_attr( $user_info->state ); ?>">
									</div>
								</div><!--Your State-->
								<div class="form-group col-sm-5">
									<label for="email"><?php esc_html_e( 'Your City', 'classiera' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="city" name="city" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'enter your City', 'classiera' ); ?>" value="<?php echo esc_attr( $user_info->city ); ?>">
									</div>
								</div><!--Your City-->
								<div class="form-group col-sm-5">
									<label for="email"><?php esc_html_e( 'Your Post Code', 'classiera' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="post_code" name="post_code" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'enter your Post Code', 'classiera' ); ?>" value="<?php echo esc_attr( $user_info->postcode ); ?>">
									</div>
								</div><!--Your Post Code-->
								<div class="form-group col-sm-12">
									<label for="address"><?php esc_html_e( 'Address', 'classiera' ); ?></label>
									<div class="inner-addon">
										<textarea name="address" id="address" placeholder="<?php esc_html_e( 'enter your address', 'classiera' ); ?>"><?php echo esc_html( $user_info->address ); ?></textarea>
									</div>
								</div><!--Address-->
							</div>
							<!-- Contact Details -->
							<!-- Social Profile Links -->
							<h4 class="user-detail-section-heading text-uppercase">
								<?php esc_html_e( 'Social Profile Links', 'classiera' ); ?>
							</h4>
							<div class="form-inline row form-inline-margin">
								<div class="form-group col-sm-5">
									<label for="facebook"><?php esc_html_e( 'Facebook', 'classiera' ); ?></label>
									<div class="inner-addon">
										<div class="input-group">
											<div class="input-group-addon input-group-addon-width-sm"><i class="fab fa-facebook-f"></i></div>
											<input type="url" id="facebook" name="facebook" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Enter your facebook url', 'classiera' ); ?>" value="<?php echo esc_url( $user_info->facebook ); ?>">
										</div>
									</div>
								</div><!--facebook-->
								<div class="form-group col-sm-5">
									<label for="twitter"><?php esc_html_e( 'Twitter', 'classiera' ); ?></label>
									<div class="inner-addon">
										<div class="input-group">
											<div class="input-group-addon input-group-addon-width-sm"><i class="fab fa-twitter"></i></div>
											<input type="url" id="twitter" name="twitter" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Enter your twitter url', 'classiera' ); ?>" value="<?php echo esc_url( $user_info->twitter ); ?>">
										</div>
									</div>
								</div><!--twitter-->
								<div class="form-group col-sm-5">
									<label for="googleplus"><?php esc_html_e( 'Google Plus', 'classiera' ); ?></label>
									<div class="inner-addon">
										<div class="input-group">
											<div class="input-group-addon input-group-addon-width-sm"><i class="fab fa-google-plus-g"></i></div>
											<input type="url" id="googleplus" name="google-plus" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Enter your Google Plus url', 'classiera' ); ?>" value="<?php echo esc_url( $user_info->googleplus); ?>">
										</div>
									</div>
								</div><!--Google Plus-->
								<div class="form-group col-sm-5">
									<label for="instagram"><?php esc_html_e( 'instagram', 'classiera' ); ?></label>
									<div class="inner-addon">
										<div class="input-group">
											<div class="input-group-addon input-group-addon-width-sm"><i class="fab fa-instagram"></i></div>
											<input type="url" id="instagram" name="instagram" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Enter your instagram url', 'classiera' ); ?>" value="<?php echo esc_url( $user_info->instagram ); ?>">
										</div>
									</div>
								</div><!--instagram-->
								<div class="form-group col-sm-5">
									<label for="pinterest"><?php esc_html_e( 'Pinterest', 'classiera' ); ?></label>
									<div class="inner-addon">
										<div class="input-group">
											<div class="input-group-addon input-group-addon-width-sm"><i class="fab fa-pinterest-p"></i></div>
											<input type="url" id="pinterest" name="pinterest" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Enter your Pinterest url', 'classiera' ); ?>" value="<?php echo esc_url( $user_info->pinterest ); ?>">
										</div>
									</div>
								</div><!--pinterest-->
								<div class="form-group col-sm-5">
									<label for="linkedin"><?php esc_html_e( 'Linkedin', 'classiera' ); ?></label>
									<div class="inner-addon">
										<div class="input-group">
											<div class="input-group-addon input-group-addon-width-sm"><i class="fab fa-linkedin"></i></div>
											<input type="url" id="linkedin" name="linkedin" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Enter your Linkedin url', 'classiera' ); ?>" value="<?php echo esc_url( $user_info->linkedin ); ?>">
										</div>
									</div>
								</div><!--linkedin-->
								<div class="form-group col-sm-5">
									<label for="vimeo"><?php esc_html_e( 'Vimeo', 'classiera' ); ?></label>
									<div class="inner-addon">
										<div class="input-group">
											<div class="input-group-addon input-group-addon-width-sm"><i class="fab fa-vimeo"></i></div>
											<input type="url" id="vimeo" name="vimeo" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Enter your vimeo url', 'classiera' ); ?>" value="<?php echo esc_url( $user_info->vimeo ); ?>">
										</div>
									</div>
								</div><!--vimeo-->
								<div class="form-group col-sm-5">
									<label for="youtube"><?php esc_html_e( 'Youtube', 'classiera' ); ?></label>
									<div class="inner-addon">
										<div class="input-group">
											<div class="input-group-addon input-group-addon-width-sm"><i class="fab fa-youtube"></i></div>
											<input type="url" id="youtube" name="youtube" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Enter your YouTube url', 'classiera' ); ?>" value="<?php echo esc_url( $user_info->youtube ); ?>">
										</div>
									</div>
								</div><!--youtube-->
							</div><!--form-inline-->
							<!-- Socail Profile Links -->
							<!-- Update your Password -->
							<h4 class="user-detail-section-heading text-uppercase"><?php esc_html_e( 'Update your Password', 'classiera' ); ?></h4>
							<div class="form-inline row">
								<div class="form-group col-sm-5">
									<label for="current-pass">
										<?php esc_html_e( 'Enter Current Password', 'classiera' ); ?>
									</label>
									<div class="inner-addon">
										<input type="password" id="current-pass" name="pwd" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Enter your current password', 'classiera' ); ?>">
									</div>
								</div>
							</div><!--currentpass-->
							<div class="form-inline row">
								<div class="form-group col-sm-5">
									<label for="new-pass">
										<?php esc_html_e( 'Enter New Password', 'classiera' ); ?>
									</label>
									<div class="inner-addon">
										<input type="password" name="confirm" data-minlength="5" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Enter Password', 'classiera' ); ?>" id="new-pass" data-error="<?php esc_html_e( 'Password required', 'classiera' ); ?>">
										<div class="help-block">
											<?php esc_html_e( 'Minimum of 5 characters', 'classiera' ); ?>
										</div>
									</div>
								</div>
							</div><!--Enter New Password-->
							<div class="form-inline row">
								<div class="form-group col-sm-5">
									<label for="re-enter">
										<?php esc_html_e( 'Re-enter New Password', 'classiera' ); ?>
									</label>
									<div class="inner-addon">
										<input type="password" id="re-enter" name="confirm2" class="form-control form-control-sm sharp-edge" placeholder="<?php esc_html_e( 'Re-enter New Password', 'classiera' ); ?>" data-match="#new-pass" data-match-error="<?php esc_html_e( 'Whoops, these dont match', 'classiera' ); ?>">
										<div class="help-block with-errors"></div>
									</div>
								</div>
							</div><!--Enter New Password-->
							<p><?php esc_html_e( 'If you would like to change the password type a new one. Otherwise leave this blank.', 'classiera' ); ?></p>
							<?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
							<input type="hidden" name="submitted" id="submitted" value="true" />
							<button type="submit" name="op" value="update_profile" class="btn btn-primary sharp btn-sm btn-style-one"><?php esc_html_e('Update Now', 'classiera') ?></button>
							<!-- Update your Password -->
						</form>
					</div><!--user-ads user-profile-settings-->
				</div><!--user-detail-section-->
			</div><!--col-lg-9-->
		</div><!--row-->
	</div><!--container-->
</section><!--user-pages section-gray-bg-->