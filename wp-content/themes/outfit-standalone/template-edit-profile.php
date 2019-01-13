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
	wp_redirect( outfit_get_page_url('login') );
	exit;
}

global $userId, $userIdentity, $userLevel;
global $redux_demo;
global $current_user;
wp_get_current_user();
$currentUser = $current_user;
$userId = $currentUser->ID;
$userInfo = get_userdata($userId);

$userName = $userInfo->display_name;

$userFirstName = get_user_meta($userId, USER_META_FIRSTNAME, true);
$userLastName = get_user_meta($userId, USER_META_LASTNAME, true);

//$userEmail = get_user_meta($userId, USER_META_EMAIL, true);
$userEmail = $userInfo->user_email;
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

// post third location
$postLocation3 = OutfitLocation::toAssoc(get_user_meta($userId, USER_META_THIRD_ADDRESS, true));
$postThirdAddress = '';
$postThirdLatitude = '';
$postThirdLongitude = '';
$postThirdLocality = $postThirdArea1 = $postThirdArea2 = $postThirdArea3 = '';
if (null !== $postLocation3 && isset($postLocation3['address'])) {
	$postThirdAddress = $postLocation3['address'];
	$postThirdLatitude = $postLocation3['latitude'];
	$postThirdLongitude = $postLocation3['longitude'];
	$postThirdLocality = $postLocation3['locality'];
	$postThirdArea1 = $postLocation3['aal1'];
	$postThirdArea2 = $postLocation3['aal2'];
	$postThirdArea3 = $postLocation3['aal3'];
}

$brands = getListOfAllBrands();

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
outfit_strip_slashes_from_input();
if ($userId) {
	if ($_POST) {
		$message =  esc_html__( 'הפרופיל שלך עודכן בהצלחה.', 'outfit-standalone' );

		$userFirstName = $_POST['first_name'];
		$userLastName = $_POST['last_name'];
		$userPhone = $_POST['phone'];
		$userEmail = $_POST['email'];
		$userPreferredHours = $_POST['preferredhours'];
		$userAbout = $_POST['desc'];

		update_user_meta($userId, USER_META_FIRSTNAME, $userFirstName);
		update_user_meta($userId, USER_META_LASTNAME, $userLastName);
		update_user_meta($userId, USER_META_PHONE, $userPhone);
		update_user_meta($userId, USER_META_EMAIL, $userEmail);
		update_user_meta($userId, USER_META_PREFERRED_HOURS, $userPreferredHours);
		update_user_meta($userId, USER_META_ABOUT, $userAbout);

		$postAddress = trim(getPostInput('address'));
		$postLatitude = getPostInput('latitude');
		$postLongitude = getPostInput('longitude');
		$postLocality = getPostInput('locality');
		$postArea1 = getPostInput('aal1');
		$postArea2 = getPostInput('aal2');
		$postArea3 = getPostInput('aal3');

		$location = new OutfitLocation($postAddress, $postLongitude, $postLatitude, [
			'locality' => $postLocality,
			'aal3' => $postArea3,
			'aal2' => $postArea2,
			'aal1' => $postArea1
		]);

		if ($location->isValid()) {

			update_user_meta($userId, USER_META_PRIMARY_ADDRESS, $location->toString());
		}

		// post secondary location
		$postSecAddress = trim(getPostInput('address_2'));
		$postSecLatitude = getPostInput('latitude_2');
		$postSecLongitude = getPostInput('longitude_2');
		$postSecLocality = getPostInput('locality_2');
		$postSecArea1 = getPostInput('aal1_2');
		$postSecArea2 = getPostInput('aal2_2');
		$postSecArea3 = getPostInput('aal3_2');

		if ($postSecAddress) {
			$location2 = new OutfitLocation($postSecAddress, $postSecLongitude, $postSecLatitude, [
				'locality' => $postSecLocality,
				'aal3' => $postSecArea3,
				'aal2' => $postSecArea2,
				'aal1' => $postSecArea1
			]);

			if ($location2->isValid()) {

				update_user_meta($userId, USER_META_SECONDARY_ADDRESS, $location2->toString());
			}
		}

		// post third location
		$postThirdAddress = trim(getPostInput('address_3'));
		$postThirdLatitude = getPostInput('latitude_3');
		$postThirdLongitude = getPostInput('longitude_3');
		$postThirdLocality = getPostInput('locality_3');
		$postThirdArea1 = getPostInput('aal1_3');
		$postThirdArea2 = getPostInput('aal2_3');
		$postThirdArea3 = getPostInput('aal3_3');

		if ($postThirdAddress) {
			$location3 = new OutfitLocation($postThirdAddress, $postThirdLongitude, $postThirdLatitude, [
				'locality' => $postThirdLocality,
				'aal3' => $postThirdArea3,
				'aal2' => $postThirdArea2,
				'aal1' => $postThirdArea1
			]);

			if ($location3->isValid()) {

				update_user_meta($userId, USER_META_THIRD_ADDRESS, $location3->toString());
			}
		}

		$birthdays = getPostMultiple('birthdays');
		$validBirthdays = array();

		foreach ($birthdays as $b) {
			if (validateDateInput($b, 'd/m/Y')) {
				$validBirthdays[] = $b;
			}
		}
		updateUserKidsBirthdays($userId, $validBirthdays);
		$userKidsBirthdays = getUserKidsBirthdays($userId);
		$birthday1 = (isset($userKidsBirthdays[0])? $userKidsBirthdays[0] : '');
		$birthday2 = (isset($userKidsBirthdays[1])? $userKidsBirthdays[1] : '');
		$birthday3 = (isset($userKidsBirthdays[2])? $userKidsBirthdays[2] : '');

		// user brands
		$userFavBrands = getPostMultiple('favbrands');
		updateUserFavoriteBrands($userId, $userFavBrands);

		$password = $_POST['confirm'];
		$confirm_password = $_POST['confirm2'];

		if($password){

			if (strlen($password) < 5 || strlen($password) > 25) {
				$message =  esc_html__( 'סיסמא חייבת להיות בין 5-20 תווים.', 'outfit-standalone' );
			}
			else if ($password != $confirm_password) {
				$message =  esc_html__( 'הסיסמאות לא שוות', 'outfit-standalone' );
			}
			else {
				wp_set_password( $password, $userId );
				$message =  esc_html__( 'הפרופיל שלך עודכן בהצלחה.', 'outfit-standalone' );
			}
		}
	}

	/*ImageUploading*/
	if ( isset($_FILES['upload_attachment']) ) {
		$count = '0';
		$files = $_FILES['upload_attachment'];
		foreach ($files['name'] as $key => $value) {
			if ($files['name'][$key]) {
				$file = array(
					'name'     => $files['name'][$key],
					'type'     => $files['type'][$key],
					'tmp_name' => $files['tmp_name'][$key],
					'error'    => $files['error'][$key],
					'size'     => $files['size'][$key]
				);
				$_FILES = array("upload_attachment" => $file);
				foreach ($_FILES as $file => $array) {
					if (($newupload = outfit_insert_userIMG($file)) !== false) {
						$count++;
						$profileImage = $newupload;
						if (!empty($profileImage)) {
							update_user_meta($userId, USER_META_AVATAR_URL, $profileImage);
						}
					}
				}
			}
		}/*Foreach*/
	}
	$outfitAuthorThumb = outfit_get_user_picture($userId, 130);


get_header();

?>
<!-- user pages -->
<section class="user-pages section-gray-bg">
	<div class="wrap ad-page user-page">
		<div class="row">
			<div class="col-lg-3 col-md-4">
				<?php get_template_part( 'templates/profile/profile-sidebar' );?>
			</div><!--col-lg-3-->
			<div class="col-lg-9 col-md-8 user-content-height">
				<?php if($_POST && isset($message)){?>
					<div class="alert alert-success" role="alert">
						<?php echo esc_html( $message ); ?>
					</div>
				<?php } ?>
				<div class="user-detail-section section-bg-white">
					<div class="user-ads user-profile-settings">
						<form data-toggle="validator" role="form" method="POST" id="primaryProfileForm" action="" enctype="multipart/form-data">
							<!-- upload avatar -->								
							<h1 class="user-detail-section-heading text-uppercase border-bottom"><?php esc_html_e("פרטים אישיים", 'outfit-standalone') ?></h1>
							<div class="media">
								<div class="uploadImage">
									<img class="media-object img-circle author-avatar" src="<?php echo esc_url( $outfitAuthorThumb ); ?>" alt="<?php echo esc_attr( $userName ); ?>">
									<input class="criteria-image-url" id="your_image_url" type="text" size="36" name="your_author_image_url" style="display: none;" value="" />
								</div>
								<div class="media-upload">
									<h5 class="media-heading text-uppercase">
										<?php esc_html_e( 'עדכנו תמונת פרופיל', 'outfit-standalone' ); ?>
									</h5>
									<div class="choose-image">
										<input type="file" id="file-1" name="upload_attachment[]" class="inputfile inputfile-1 author-UP" data-multiple-caption="{count} files selected" multiple />
										<label for="file-1" class="upload-author-image">
											<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/camera-small.png" />
											<span><?php esc_html_e( 'עדכן תמונה', 'outfit-standalone' ); ?></span>
										</label>

									</div>
								</div>
							</div><!-- /.upload avatar -->
							<div class="form-inline row">
								<div class="form-group col-sm-6">
									<label for="first-name"><?php esc_html_e( 'שם פרטי', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="first-name" name="first_name" class="form-control form-control-md"
											   placeholder="<?php esc_html_e( 'עדכן שם פרטי', 'outfit-standalone' ); ?>"
											   value="<?php echo esc_attr( $userFirstName ); ?>">
									</div>
								</div><!--Firstname-->
								<div class="form-group col-sm-6">
									<label for="last-name"><?php esc_html_e( 'שם משפחה', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="last-name" name="last_name" class="form-control form-control-sm"
											   placeholder="<?php esc_html_e( 'עדכן שם משפחה', 'outfit-standalone' ); ?>"
											   value="<?php echo esc_attr( $userLastName); ?>">
									</div>
								</div><!--Last name-->
							</div>
							<!-- user basic information -->
							<!-- Contact Details -->
							<h4 class="user-detail-section-heading text-uppercase">
								<?php esc_html_e( 'פרטי התקשרות', 'outfit-standalone' ); ?>
							</h4>
							<div class="form-inline row">
								<div class="form-group col-sm-6">
									<label for="email"><?php esc_html_e( 'אימייל', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<input type="text" id="email" name="email" class="form-control form-control-sm"
											   placeholder="<?php esc_html_e( '', 'outfit-standalone' ); ?>"
											   value="<?php echo sanitize_email( $userEmail ); ?>">
										<input type="hidden" name="current_email" value="<?php echo sanitize_email( $userEmail ); ?>">
									</div>
								</div><!--Your Email-->							
								<div class="form-group col-sm-6">
									<label for="phone"><?php esc_html_e( 'טלפון', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<input type="tel" id="phone" class="form-control form-control-sm"
											   placeholder="<?php esc_html_e( '', 'outfit-standalone' ); ?>"
											   name="phone" value="<?php echo esc_html( $userPhone ); ?>">
									</div>
								</div><!--Phone Number-->
								<div class="form-group col-sm-12">
									<label for="preferredhours"><?php esc_html_e( 'שעות מועדפות ליצירת קשר', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<input type="text" name="preferredhours" id="preferredhours" class="form-control form-control-sm" value="<?php echo esc_html( $userPreferredHours ); ?>" placeholder="<?php esc_html_e( '', 'outfit-standalone' ); ?>">
									</div>
								</div><!-- preferred time -->
							</div>
							<!-- Contact Details -->

							<!-- Addresses -->
							<h4 class="user-detail-section-heading text-uppercase">
								<?php esc_html_e( 'אזורי הגעה', 'outfit-standalone' ); ?>
							</h4>
							<div class="ex"><?php echo do_shortcode("[do_widget id=text-10]"); ?></div>
							<!--Address-->
							<div class="form-inline row">
							<div class="form-group col-sm-6">
								<label class="text-left flip"><?php esc_html_e('אזור עיקרי', 'outfit-standalone'); ?></label>
								<div class="inner-addon">
									<input id="address" type="text" name="address" class="form-control form-control-sm address" value="<?php echo esc_html($postAddress); ?>" placeholder="<?php esc_html_e('כתובת מלאה', 'outfit-standalone') ?>">
									<input class="latitude" type="hidden" id="latitude" name="latitude" value="<?php echo esc_html($postLatitude); ?>">
									<input class="longitude" type="hidden" id="longitude" name="longitude" value="<?php echo esc_html($postLongitude); ?>">
									<input class="locality" type="hidden" id="locality" name="locality" value="<?php echo esc_html($postLocality); ?>">
									<input class="aal3" type="hidden" id="aal3" name="aal3" value="<?php echo esc_html($postArea3); ?>">
									<input class="aal2" type="hidden" id="aal2" name="aal2" value="<?php echo esc_html($postArea2); ?>">
									<input class="aal1" type="hidden" id="aal1" name="aal1" value="<?php echo esc_html($postArea1); ?>">
								</div>
								<div><small><?php echo do_shortcode("[do_widget id=text-21]"); ?></small></div>
							</div>

							<div class="form-group col-sm-6">
								<label class="text-left flip"><?php esc_html_e('איזורי הגעה נוספים ( לא חובה)', 'outfit-standalone'); ?> : </label>
								<div class="inner-addon">

									<input id="address_2" type="text" name="address_2" class="address form-control form-control-sm" value="<?php echo esc_html($postSecAddress); ?>" placeholder="<?php esc_html_e('', 'outfit-standalone') ?>">
									<input class="latitude" type="hidden" id="latitude_2" name="latitude_2" value="<?php echo esc_html($postSecLatitude); ?>">
									<input class="longitude" type="hidden" id="longitude_2" name="longitude_2" value="<?php echo esc_html($postSecLongitude); ?>">
									<input class="locality" type="hidden" id="locality_2" name="locality_2" value="<?php echo esc_html($postSecLocality); ?>">
									<input class="aal3" type="hidden" id="aal3_2" name="aal3_2" value="<?php echo esc_html($postSecArea3); ?>">
									<input class="aal2" type="hidden" id="aal2_2" name="aal2_2" value="<?php echo esc_html($postSecArea2); ?>">
									<input class="aal1" type="hidden" id="aal1_2" name="aal1_2" value="<?php echo esc_html($postSecArea1); ?>">
								</div>
							</div>

							<div class="form-group col-sm-6">

								<div class="inner-addon" id="plus-location-hidden">

									<input id="address_3" type="text" name="address_3" class="address form-control form-control-sm" value="<?php echo esc_html($postThirdAddress); ?>" placeholder="<?php esc_html_e('', 'outfit-standalone') ?>">
									<input class="latitude" type="hidden" id="latitude_3" name="latitude_3" value="<?php echo esc_html($postThirdLatitude); ?>">
									<input class="longitude" type="hidden" id="longitude_3" name="longitude_3" value="<?php echo esc_html($postThirdLongitude); ?>">
									<input class="locality" type="hidden" id="locality_3" name="locality_3" value="<?php echo esc_html($postThirdLocality); ?>">
									<input class="aal3" type="hidden" id="aal3_3" name="aal3_3" value="<?php echo esc_html($postThirdArea3); ?>">
									<input class="aal2" type="hidden" id="aal2_3" name="aal2_3" value="<?php echo esc_html($postThirdArea2); ?>">
									<input class="aal1" type="hidden" id="aal1_3" name="aal1_3" value="<?php echo esc_html($postThirdArea1); ?>">
								</div>
							</div>
							</div>
							<!--/Address-->

							<!-- Kids Birthdays -->
							<h4 class="user-detail-section-heading text-uppercase">
								<?php esc_html_e( 'תאריך לידה של הילדים', 'outfit-standalone' ); ?>
							</h4>
							<div class="form-inline form-group date-section row">
								<div class="inner-addon col-sm-4">
									<input type="text"
										   class="form-control form-control-sm date-desktop"
										   id="birthday1"
										   name="birthdays[]"
										   value="<?php echo esc_attr($birthday1); ?>"
										   title="יש להשתמש בפורמט תאריך DD/MM/YYYY">
								</div>
								<div class="inner-addon col-sm-4">
									<input type="text"
										   class="form-control form-control-sm date-desktop"
										   id="birthday2"
										   name="birthdays[]"
										   value="<?php echo esc_attr($birthday2); ?>"
										   title="יש להשתמש בפורמט תאריך DD/MM/YYYY">
								</div>
								<div class="inner-addon col-sm-4">
									<input type="text"
										   class="form-control form-control-sm date-desktop"
										   id="birthday3"
										   name="birthdays[]"
										   value="<?php echo esc_attr($birthday3); ?>"
										   title="יש להשתמש בפורמט תאריך DD/MM/YYYY">
								</div>
							</div>
							<!-- Kids Birthdays -->

							<!-- About me -->
							<h4 class="user-detail-section-heading text-uppercase">
								<?php esc_html_e( 'תספרו לנו עליכם', 'outfit-standalone' ); ?>
							</h4>
							<div class="form-inline row">
								<div class="form-group col-sm-6">
									<label for="favbrands"><?php esc_html_e('מותגים אהובים', 'outfit-standalone') ?> </label>
									<div class="">
										<div style="display: none">
											<?php //var_dump($brands); ?>
										</div>
										<select id="favbrands" name="favbrands[]" class="form-control form-control-md" multiple>
											<option value=""><?php esc_html_e('בחרו מותגים מהרשימה', 'outfit-standalone'); ?></option>
											<?php
											foreach ($brands as $c): ?>
												<option value="<?php echo $c->term_id; ?>"
													<?php echo (in_array($c->term_id, $userFavBrands)? 'selected' : ''); ?>>
													<?php echo esc_html($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Brands-->
								<div class="form-group col-sm-12">
									<label for="bio"><?php esc_html_e( 'כמה מילים עליי', 'outfit-standalone' ); ?></label>
									<div class="inner-addon">
										<textarea name="desc" id="bio" placeholder="<?php esc_html_e( 'אמא לשניים, אוהבת עיצוב / אני אבא לשניים, אוהב את אמא...', 'outfit-standalone' ); ?>"><?php echo esc_html( $userAbout ); ?></textarea>
									</div>
								</div><!--biography-->
							</div>
							<!-- About me -->

							<!-- Update your Password -->
							<h4 class="user-detail-section-heading text-uppercase"><?php esc_html_e( 'שינוי סיסמא', 'outfit-standalone' ); ?></h4>
							<div class="ex"><p><?php esc_html_e( 'אם ברצונך לשנות סיסמא הכנס חדשה, אחרת אשאר ריק', 'outfit-standalone' ); ?></p></div>
							<div class="form-inline row" style="display:none;">
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
								<div class="form-group col-sm-6">
									<label for="new-pass">
										<?php esc_html_e( 'סיסמא חדשה', 'outfit-standalone' ); ?>
									</label>
									<div class="inner-addon">
										<input type="password" name="confirm" data-minlength="5" class="form-control form-control-sm" placeholder="<?php esc_html_e( '', 'outfit-standalone' ); ?>" id="new-pass" data-error="<?php esc_html_e( 'סיסמא חובה', 'outfit-standalone' ); ?>">
										<div class="help-block">
											<?php esc_html_e( '5 תווים מינימום', 'outfit-standalone' ); ?>
										</div>
									</div>
								</div>
								<div class="form-group col-sm-6">
									<label for="re-enter">
										<?php esc_html_e( 'הקדלת סיסמא שוב', 'outfit-standalone' ); ?>
									</label>
									<div class="inner-addon">
										<input type="password" id="re-enter" name="confirm2" class="form-control form-control-sm sharp-edge" placeholder="<?php esc_html_e( '', 'outfit-standalone' ); ?>" data-match="#new-pass" data-match-error="<?php esc_html_e( 'אופס, זה לא מתאים', 'outfit-standalone' ); ?>">
										<div class="help-block with-errors"></div>
									</div>
								</div>								
							</div><!--Enter New Password-->							
							<?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
							<input type="hidden" name="submitted" id="submitted" value="true" />
							<button type="submit" name="op" value="update_profile" class="btn btn-primary sharp btn-sm btn-style-one"><?php esc_html_e('שמירת שינויים', 'outfit-standalone') ?></button>
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

<?php } // if ($userId) ?>
<?php get_footer(); ?>