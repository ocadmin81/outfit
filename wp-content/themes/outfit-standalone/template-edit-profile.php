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

$currentUser = wp_get_current_user();
$userId = $currentUser->ID;
$userInfo = get_userdata($userId);

$outfitAuthorThumb = get_user_meta($userId, USER_META_AVATAR_URL, true);
$outfitAuthorThumb = outfit_get_profile_img($outfitAuthorThumb);
if (empty($outfitAuthorThumb)) {
	$outfitAuthorThumb = outfit_get_avatar_url($userId, 150);
}

$userName = $userInfo->display_name;

$userFirstName = get_user_meta($userId, USER_META_FIRSTNAME, true);
$userLastName = get_user_meta($userId, USER_META_LASTNAME, true);

$userEmail = get_user_meta($userId, USER_META_EMAIL, true);
$userPhone = get_user_meta($userId, USER_META_PHONE, true);
$userAbout = get_user_meta($userId, USER_META_ABOUT, true);
$userPreferredHours = get_user_meta($userId, USER_META_PREFERRED_HOURS, true);
$userKidsBirthdays = getUserKidsBirthdays($userId);
$birthday1 = (isset($userKidsBirthdays[0])? $userKidsBirthdays[0] : '');
$birthday2 = (isset($userKidsBirthdays[1])? $userKidsBirthdays[1] : '');
$birthday3 = (isset($userKidsBirthdays[2])? $userKidsBirthdays[2] : '');
$userFavBrands = getUserFavoriteBrands($userId);

// post location
$postLocation = OutfitLocation::toAssoc(get_user_meta($userId, USER_META_PRIMARY_ADDRESS, true));
$postAddress = '';
$postLatitude = '';
$postLongitude = '';
$postLocality = $postArea1 = $postArea2 = $postArea3 = '';
if (null !== $postLocation && isset($postLocation['address'])) {
	$postAddress = $postLocation['address'];
	$postLatitude = $postLocation['latitude'];
	$postLongitude = $postLocation['longitude'];
	$postLocality = $postLocation['locality'];
	$postArea1 = $postLocation['aal1'];
	$postArea2 = $postLocation['aal2'];
	$postArea3 = $postLocation['aal3'];
}
// post secondary location
$postLocation2 = OutfitLocation::toAssoc(get_user_meta($userId, USER_META_SECONDARY_ADDRESS, true));
$postSecAddress = '';
$postSecLatitude = '';
$postSecLongitude = '';
$postSecLocality = $postSecArea1 = $postSecArea2 = $postSecArea3 = '';
if (null !== $postLocation2 && isset($postLocation2['address'])) {
	$postSecAddress = $postLocation2['address'];
	$postSecLatitude = $postLocation2['latitude'];
	$postSecLongitude = $postLocation2['longitude'];
	$postSecLocality = $postLocation2['locality'];
	$postSecArea1 = $postLocation2['aal1'];
	$postSecArea2 = $postLocation2['aal2'];
	$postSecArea3 = $postLocation2['aal3'];
}

$page = get_page($post->ID);
$currentPageId = $page->ID;

/*
 * profile image
 * first name
 * last name
 * email
 * phone
 * preferred hours
 * primary address
 * secondary address
 * kids birthdays
 * about
 * favorite brands
 * password change
 */

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
						<form data-toggle="validator" role="form" method="POST" id="primaryPostForm" action="" enctype="multipart/form-data">
							<!-- upload avatar -->
							<h4 class="user-detail-section-heading text-uppercase">
								<?php esc_html_e("Basic Information", 'outfit-standalone') ?>
							</h4>
							<div class="media">
								<div class="media-left uploadImage">
									<img class="media-object img-circle author-avatar" src="<?php echo esc_url( $outfitAuthorThumb ); ?>" alt="<?php echo esc_attr( $authorName ); ?>">
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

									</div>
								</div>
							</div><!-- /.upload avatar -->
							<div class="form-inline row form-inline-margin">
								<div class="form-group col-sm-5">
									<label for="first-name"><?php esc_html_e( 'First Name', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="first-name" name="first_name" class="form-control form-control-sm"
											   placeholder="<?php esc_html_e( 'Update first Name..', 'outfit-standalone' ); ?>"
											   value="<?php echo esc_attr( $userFirstName ); ?>">
									</div>
								</div><!--Firstname-->
								<div class="form-group col-sm-5">
									<label for="last-name"><?php esc_html_e( 'Last Name', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="last-name" name="last_name" class="form-control form-control-sm"
											   placeholder="<?php esc_html_e( 'update last Name', 'outfit-standalone' ); ?>"
											   value="<?php echo esc_attr( $userLastName); ?>">
									</div>
								</div><!--Last name-->
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
										<input type="tel" id="phone" class="form-control form-control-sm"
											   placeholder="<?php esc_html_e( 'Your Phone No', 'outfit-standalone' ); ?>"
											   name="phone" value="<?php echo esc_html( $userPhone ); ?>">
									</div>
								</div><!--Phone Number-->
								<div class="form-group col-sm-5">
									<label for="email"><?php esc_html_e( 'Your Email', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="email" name="email" class="form-control form-control-sm"
											   placeholder="<?php esc_html_e( 'enter your email address', 'outfit-standalone' ); ?>"
											   value="<?php echo sanitize_email( $userEmail ); ?>">
										<input type="hidden" name="current_email" value="<?php echo sanitize_email( $userEmail ); ?>">
									</div>
								</div><!--Your Email-->
								<div class="form-group col-sm-12">
									<label for="preferredhours"><?php esc_html_e( 'Preferred time to call', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<textarea name="preferredhours" id="preferredhours"
												  placeholder="<?php esc_html_e( 'enter your preferred time.', 'outfit-standalone' ); ?>"><?php echo esc_html( $userPreferredHours ); ?></textarea>
									</div>
								</div><!-- preferred time -->
							</div>
							<!-- Contact Details -->

							<!-- Addresses -->
							<h4 class="user-detail-section-heading text-uppercase">
								<?php esc_html_e( 'Collect points', 'outfit-standalone' ); ?>
							</h4>
							<!--Address-->
							<div class="form-inline row form-inline-margin">
							<div class="form-group">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Primary address', 'outfit-standalone'); ?> : <span>*</span></label>
								<div class="col-sm-9">
									<input class="address" id="address" type="text" name="address" class="form-control form-control-md" value="<?php echo esc_html($postAddress); ?>" placeholder="<?php esc_html_e('Address or City', 'outfit-standalone') ?>" required>
									<input class="latitude" type="hidden" id="latitude" name="latitude" value="<?php echo esc_html($postLatitude); ?>">
									<input class="longitude" type="hidden" id="longitude" name="longitude" value="<?php echo esc_html($postLongitude); ?>">
									<input class="locality" type="hidden" id="locality" name="locality" value="<?php echo esc_html($postLocality); ?>">
									<input class="aal3" type="hidden" id="aal3" name="aal3" value="<?php echo esc_html($postArea3); ?>">
									<input class="aal2" type="hidden" id="aal2" name="aal2" value="<?php echo esc_html($postArea2); ?>">
									<input class="aal1" type="hidden" id="aal1" name="aal1" value="<?php echo esc_html($postArea1); ?>">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Secondary address', 'outfit-standalone'); ?> : </label>
								<div class="col-sm-9">

									<input class="address" id="address_2" type="text" name="address_2" class="form-control form-control-md" value="<?php echo esc_html($postSecAddress); ?>" placeholder="<?php esc_html_e('Address or City', 'outfit-standalone') ?>">
									<input class="latitude" type="hidden" id="latitude_2" name="latitude_2" value="<?php echo esc_html($postSecLatitude); ?>">
									<input class="longitude" type="hidden" id="longitude_2" name="longitude_2" value="<?php echo esc_html($postSecLongitude); ?>">
									<input class="locality" type="hidden" id="locality_2" name="locality_2" value="<?php echo esc_html($postSecLocality); ?>">
									<input class="aal3" type="hidden" id="aal3_2" name="aal3_2" value="<?php echo esc_html($postSecArea3); ?>">
									<input class="aal2" type="hidden" id="aal2_2" name="aal2_2" value="<?php echo esc_html($postSecArea2); ?>">
									<input class="aal1" type="hidden" id="aal1_2" name="aal1_2" value="<?php echo esc_html($postSecArea1); ?>">
								</div>
							</div>
							</div>
							<!--/Address-->

							<!-- Kids Birthdays -->
							<h4 class="user-detail-section-heading text-uppercase">
								<?php esc_html_e( 'Kids birthdays', 'outfit-standalone' ); ?>
							</h4>
							<div class="form-inline row form-inline-margin">
								<div class="form-group col-sm-4">
									<input type="date"
										   class="form-control form-control-sm"
										   id="birthday1"
										   name="birthdays[]"
										   value="<?php esc_attr($birthday1); ?>"
										   title="Please use DD.MM.YYYY as the date format."
										   pattern="(3[01]|[21][0-9]|0[1-9]).(1[0-2]|0[1-9]).(19[0-9][0-9]|20[0-9][0-9])">
								</div>
								<div class="form-group col-sm-4">
									<input type="date"
										   class="form-control form-control-sm"
										   id="birthday2"
										   name="birthdays[]"
										   value="<?php esc_attr($birthday2); ?>"
										   title="Please use DD.MM.YYYY as the date format."
										   pattern="(3[01]|[21][0-9]|0[1-9]).(1[0-2]|0[1-9]).(19[0-9][0-9]|20[0-9][0-9])">
								</div>
								<div class="form-group col-sm-4">
									<input type="date"
										   class="form-control form-control-sm"
										   id="birthday3"
										   name="birthdays[]"
										   value="<?php esc_attr($birthday3); ?>"
										   title="Please use DD.MM.YYYY as the date format."
										   pattern="(3[01]|[21][0-9]|0[1-9]).(1[0-2]|0[1-9]).(19[0-9][0-9]|20[0-9][0-9])">
								</div>
							</div>
							<!-- Kids Birthdays -->

							<!-- About me -->
							<h4 class="user-detail-section-heading text-uppercase">
								<?php esc_html_e( 'About me', 'outfit-standalone' ); ?>
							</h4>
							<div class="form-inline row form-inline-margin">

								<div class="form-group col-sm-12">
									<label for="bio"><?php esc_html_e( 'Biography', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<textarea name="desc" id="bio" placeholder="<?php esc_html_e( 'enter your short info.', 'outfit-standalone' ); ?>"><?php echo esc_html( $userAbout ); ?></textarea>
									</div>
								</div><!--biography-->
							</div>
							<!-- About me -->

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