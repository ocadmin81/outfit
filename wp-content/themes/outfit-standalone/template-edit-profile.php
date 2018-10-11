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
	$login = $redux_demo['login'];
	wp_redirect( $login ); exit;
}

global $userId, $userIdentity, $userLevel;
global $redux_demo;

$profile = $redux_demo['profile'];
$currentUser = wp_get_current_user();
$userID = $currentUser->ID;
$userInfo = get_userdata($userId);

$outfitAuthorThumb = get_user_meta($userId, "outfit_author_avatar_url", true);
$outfitAuthorThumb = outfit_get_profile_img($outfitAuthorThumb);

$authorName = get_the_author_meta('display_name', $userId);
$author_verified = get_the_author_meta('author_verified', $userId);


$page = get_post($post->ID);
$currentPageId = $page->ID;

get_header();

?>
<!-- user pages -->
<section class="user-pages section-gray-bg">
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-4">
				<?php get_template_part( 'templates/profile/profile-sidebar' );?>
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
							<?php esc_html_e("Basic Information", 'outfit-standalone') ?>
						</h4>
						<form data-toggle="validator" role="form" method="POST" id="primaryPostForm" action="" enctype="multipart/form-data">
							<!-- upload avatar -->
							<div class="media">
								<div class="media-left uploadImage">
									<?php

									if(empty($outfitAuthorThumb)){
										$outfitAuthorThumb = outfit_get_avatar_url ( get_the_author_meta('user_email', $userId), $size = '130' );
										?>
										<img class="media-object img-circle author-avatar" src="<?php echo esc_url( $outfitAuthorThumb ); ?>" alt="<?php echo esc_attr( $authorName ); ?>">
										<?php
									}else{ ?>
										<img class="media-object img-circle author-avatar" src="<?php echo esc_url( $outfitAuthorThumb ); ?>" alt="<?php echo esc_attr( $authorName ); ?>">
									<?php } ?>
									<input class="criteria-image-url" id="your_image_url" type="text" size="36" name="your_author_image_url" style="display: none;" value="" />
								</div>
								<div class="media-body">
									<h5 class="media-heading text-uppercase">
										<?php esc_html_e( 'Update your Profile Photo', 'outfit-standalone' ); ?>
									</h5>
									<div class="choose-image">
										<input type="file" id="file-1" name="upload_attachment[]" class="inputfile inputfile-1 author-UP" data-multiple-caption="{count} files selected" multiple />
										<label for="file-1" class="upload-author-image"><i class="fa fa-camera"></i>
											<span><?php esc_html_e( 'Upload photo', 'outfit-standalone' ); ?></span>
										</label>
										<!-- verify profile btn-->
										<?php if($author_verified != 'verified'){ ?>
											<button class="classiera_verify_btn" data-toggle="modal" data-target="#verifyModal" type="button"> <i class="fa fa-check"></i> <?php esc_html_e( 'Verify your account', 'outfit-standalone' ); ?></button>
										<?php } ?>
										<!-- verify profile btn-->
									</div>
								</div>
							</div><!-- /.upload avatar -->
							<div class="form-inline row form-inline-margin">
								<div class="form-group col-sm-5">
									<label for="first-name"><?php esc_html_e( 'First Name', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="first-name" name="first_name" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Update first Name..', 'outfit-standalone' ); ?>" value="<?php echo esc_attr( $userInfo->first_name ); ?>">
									</div>
								</div><!--Firstname-->
								<div class="form-group col-sm-5">
									<label for="last-name"><?php esc_html_e( 'Last Name', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="last-name" name="last_name" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'update last Name', 'outfit-standalone' ); ?>" value="<?php echo esc_attr( $userInfo->last_name ); ?>">
									</div>
								</div><!--Last name-->
								<div class="form-group col-sm-12">
									<label for="bio"><?php esc_html_e( 'Biography', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<textarea name="desc" id="bio" placeholder="<?php esc_html_e( 'enter your short info.', 'outfit-standalone' ); ?>"><?php echo esc_html( $userInfo->description ); ?></textarea>
									</div>
								</div><!--biography-->
							</div>
							<!-- user basic information -->
							<!-- Contact Details -->
							<h4 class="user-detail-section-heading text-uppercase">
								<?php esc_html_e( 'Contact Details', 'outfit-standalone' ); ?>
							</h4>
							<div class="form-inline row form-inline-margin">
								<div class="form-group col-sm-5">
									<label for="phone"><?php esc_html_e( 'Phone Number', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<input type="tel" id="phone" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Your Phone No', 'outfit-standalone' ); ?>" name="phone" value="<?php echo esc_html( $userInfo->phone ); ?>">
									</div>
								</div><!--Phone Number-->
								<div class="form-group col-sm-5">
									<label for="email"><?php esc_html_e( 'Your Email', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="email" name="email" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'enter your email address', 'outfit-standalone' ); ?>" value="<?php echo sanitize_email( $userInfo->user_email ); ?>">
										<input type="hidden" name="current_email" value="<?php echo sanitize_email( $userInfo->user_email ); ?>">
									</div>
								</div><!--Your Email-->
								<div class="form-group col-sm-12">
									<label for="address"><?php esc_html_e( 'Primary Address', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<textarea name="address1" id="address1" placeholder="<?php esc_html_e( 'enter your primary address', 'outfit-standalone' ); ?>"><?php echo esc_html( $userInfo->address1 ); ?></textarea>
									</div>
								</div><!--Primary Address-->
								<div class="form-group col-sm-12">
									<label for="address"><?php esc_html_e( 'Secondary Address', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<textarea name="address2" id="address2" placeholder="<?php esc_html_e( 'enter your secondary address', 'outfit-standalone' ); ?>"><?php echo esc_html( $userInfo->address2 ); ?></textarea>
									</div>
								</div><!--Secondary Address-->
							</div>
							<!-- Contact Details -->

							<!-- Update your Password -->
							<h4 class="user-detail-section-heading text-uppercase"><?php esc_html_e( 'Update your Password', 'outfit-standalone' ); ?></h4>
							<div class="form-inline row">
								<div class="form-group col-sm-5">
									<label for="current-pass">
										<?php esc_html_e( 'Enter Current Password', 'outfit-standalone' ); ?>
									</label>
									<div class="inner-addon">
										<input type="password" id="current-pass" name="pwd" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Enter your current password', 'outfit-standalone' ); ?>">
									</div>
								</div>
							</div><!--currentpass-->
							<div class="form-inline row">
								<div class="form-group col-sm-5">
									<label for="new-pass">
										<?php esc_html_e( 'Enter New Password', 'outfit-standalone' ); ?>
									</label>
									<div class="inner-addon">
										<input type="password" name="confirm" data-minlength="5" class="form-control form-control-sm" placeholder="<?php esc_html_e( 'Enter Password', 'outfit-standalone' ); ?>" id="new-pass" data-error="<?php esc_html_e( 'Password required', 'outfit-standalone' ); ?>">
										<div class="help-block">
											<?php esc_html_e( 'Minimum of 5 characters', 'outfit-standalone' ); ?>
										</div>
									</div>
								</div>
							</div><!--Enter New Password-->
							<div class="form-inline row">
								<div class="form-group col-sm-5">
									<label for="re-enter">
										<?php esc_html_e( 'Re-enter New Password', 'outfit-standalone' ); ?>
									</label>
									<div class="inner-addon">
										<input type="password" id="re-enter" name="confirm2" class="form-control form-control-sm sharp-edge" placeholder="<?php esc_html_e( 'Re-enter New Password', 'outfit-standalone' ); ?>" data-match="#new-pass" data-match-error="<?php esc_html_e( 'Whoops, these dont match', 'outfit-standalone' ); ?>">
										<div class="help-block with-errors"></div>
									</div>
								</div>
							</div><!--Enter New Password-->
							<p><?php esc_html_e( 'If you would like to change the password type a new one. Otherwise leave this blank.', 'outfit-standalone' ); ?></p>
							<?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
							<input type="hidden" name="submitted" id="submitted" value="true" />
							<button type="submit" name="op" value="update_profile" class="btn btn-primary sharp btn-sm btn-style-one"><?php esc_html_e('Update Now', 'outfit-standalone') ?></button>
							<!-- Update your Password -->
						</form>
					</div><!--user-ads user-profile-settings-->
				</div><!--user-detail-section-->
			</div><!--col-lg-9-->
		</div><!--row-->
	</div><!--container-->
</section><!--user-pages section-gray-bg-->
	<!--Verify Profile Modal-->
	<div class="modal fade" id="verifyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<?php
		$authorEmail = $userInfo->user_email;
		$dbcode = get_the_author_meta('author_vcode', $userId);
		$outfitVerifyCode = md5($authorEmail);
		?>
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title text-uppercase text-center" id="myModalLabel">
						<?php esc_html_e('Verify Your Account Now', 'outfit-standalone') ?>
					</h4>
					<p class="text-uppercase text-center">
						<?php esc_html_e('Following these Steps', 'outfit-standalone') ?>
					</p>
				</div><!--modal-header-->
				<div class="modal-body">
					<form method="post" class="form-inline row classiera_get_code_form">
						<span class="classiera--loader"><img src="<?php echo get_template_directory_uri().'/assets/images/loader.gif' ?>" alt="classiera loader"></span>
						<div class="form-group col-sm-9">
							<input type="text" class="form-control verify_email" placeholder="<?php esc_html_e('example@email.com', 'outfit-standalone') ?>" value="<?php echo sanitize_email( $authorEmail ); ?>" disabled>
							<input type="hidden" value="<?php echo esc_html( $outfitVerifyCode ); ?>" name="" class="verify_code">
							<input type="hidden" value="<?php echo esc_attr( $userId ); ?>" name="" class="verify_user_id">
						</div>
						<div class="form-group col-sm-3">
							<button type="submit" class="btn btn-primary sharp btn-sm btn-style-one verify_get_code">
								<?php esc_html_e('Get verification Code', 'outfit-standalone') ?>
							</button>
						</div>
					</form><!--classiera_get_code_form-->
					<form method="post" class="form-inline row classiera_verify_form" <?php if($dbcode){ ?>style="display:block;" <?php } ?>>
						<span class="classiera--loader"><img src="<?php echo get_template_directory_uri().'/images/loader.gif' ?>" alt="classiera loader"></span>
						<h5 class="text-center text-uppercase">
							<?php esc_html_e('Check your email inbox and paste code below', 'outfit-standalone') ?>
						</h5>
						<div class="form-group col-sm-9">
							<input type="text" class="form-control verification_code" placeholder="<?php esc_html_e('Enter your verified code', 'outfit-standalone') ?>" value="" required>
							<input type="hidden" value="<?php echo esc_attr( $userId ); ?>" name="" class="verify_user_id">
							<input type="hidden" value="<?php echo sanitize_email( $authorEmail ); ?>" name="" class="verify_email">
						</div>
						<div class="form-group col-sm-3">
							<button type="submit" class="btn btn-primary sharp btn-sm btn-style-one verify_code_btn">
								<?php esc_html_e('Verify Now', 'outfit-standalone') ?>
							</button>
						</div>
					</form><!--classiera_verify_form-->
					<div class="classiera_verify_congrats text-center">

					</div><!--classiera_verify_congrats-->
				</div><!--modal-body-->
			</div><!--modal-content-->
		</div><!--modal-dialog modal-lg-->
	</div><!--modal fade-->
	<!--Verify Profile Modal-->
<?php get_footer(); ?>