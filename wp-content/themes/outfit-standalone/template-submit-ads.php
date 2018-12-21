<?php
/**
 * Template name: Submit Ad
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiera
 * @since classiera 1.0
 */

if ( !is_user_logged_in() ) {
	wp_redirect( outfit_get_page_url('login') );
	exit;
}
$postTitleError = '';
$postPriceError = '';
$catError = '';
$imageError = '';
$postContent = '';
$hasError = '';
$allowed = '';
global $redux_demo;
global $wpdb;
global $current_user;

wp_get_current_user();
$userID = $userId = $current_user->ID;
$cUserCheck = current_user_can( 'administrator' );
$role = $current_user->roles;
$currentRole = $role[0];
$imageLimit = 4;

$categories = outfit_get_main_cats(true);
$ageGroups = outfit_get_list_of_age_groups();
$colors = outfit_get_list_of_colors();
$brands = array();
$conditions = outfit_get_list_of_conditions();
$writers = outfit_get_list_of_writers();
$characters = outfit_get_list_of_characters();
$genders = outfit_get_list_of_genders();
$languages = outfit_get_list_of_languages();
$shoeSizes = outfit_get_list_of_shoe_sizes();
$maternitySizes = outfit_get_list_of_maternity_sizes();
$bicycleSizes = outfit_get_list_of_bicycle_sizes();

$userPhone = get_user_meta($userId, USER_META_PHONE, true);
$userPreferredHours = get_user_meta($userId, USER_META_PREFERRED_HOURS, true);
$userPrimaryLocation = OutfitLocation::createFromJSON(get_user_meta($userId, USER_META_PRIMARY_ADDRESS, true));
$userSecondaryLocation = OutfitLocation::createFromJSON(get_user_meta($userId, USER_META_SECONDARY_ADDRESS, true));
$userThirdLocation = OutfitLocation::createFromJSON(get_user_meta($userId, USER_META_THIRD_ADDRESS, true));

$termsandcondition = '/תנאי-שימוש-באתר';

/*
 * upload_attachment[]
 * postTitle
 * postPrice
 * postCategory
 * postSubcategory
 * postColor
 * postAgeGroup
 * postBrand
 * postCondition
 * postWriter
 * postCharacter
 * postContent
 * postPhone
 * address, latitude, longitude, locality, aal3, aal2, aal1
 *
 */
if(isset( $_POST['postTitle'] )) {

	if(
		isset($_POST['submitted']) &&
		isset($_POST['post_nonce_field']) &&
		wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')
	) {
		// this is a submitted form
		$postTitle = trim($_POST['postTitle']);
		if (empty($postTitle)) {
			$postTitleError =  esc_html__( 'נא להכניס כותרת', 'outfit-standalone' );
			$hasError = true;
		}
		if (empty($_POST['postCategory'])) {
			$catError = esc_html__( 'נא לבחור קטגוריה', 'outfit-standalone' );
			$hasError = true;
		}
		//Image Count check//
		$files = $_FILES['upload_attachment'];
		$count = count($files['name']);
		if($count == 0){
			$imageError = esc_html__( 'יש לעלות תמונה אחת לפחות', 'outfit-standalone' );
			$hasError = true;
		}
		//Image Count check//

		if ($hasError != true) {

			//Set Post Status//
			if (is_super_admin() ) {
				$postStatus = 'publish';
			} else {
				$postStatus = 'pending';
			}
			//Set Post Status//

			//Check Category//
			$outfitMainCat = $_POST['postCategory'];
			$outfitSubCat = getPostInput('postSubcategory');
			$outfitSubSubCat = getPostInput('postSubsubcategory');
			$outfitCategory = $outfitMainCat;
			if (!empty($outfitSubSubCat)) {
				$outfitCategory = $outfitSubSubCat;
			}
			else if (!empty($outfitSubCat)) {
				$outfitCategory = $outfitSubCat;
			}
			//Check Category//

			//Setup Post Data//
			$postInfo = array(
				'post_title' => esc_attr(strip_tags($_POST['postTitle'])),
				'post_content' => strip_tags(getPostInput('postContent'), '<h1><h2><h3><strong><b><ul><ol><li><i><a><blockquote><center><embed><iframe><pre><table><tbody><tr><td><video><br>'),
				'post_type' => OUTFIT_AD_POST_TYPE,
				'post_category' => array($outfitMainCat, $outfitSubCat, $outfitSubSubCat),
				'comment_status' => 'open',
				'ping_status' => 'open',
				'post_status' => $postStatus
			);

			$postId = wp_insert_post($postInfo);

			$postPrice = trim(getPostInput('postPrice'));
			$postPhone = trim(getPostInput('postPhone'));
			$postPreferredHours = trim(getPostInput('postPreferredHours'));

			// post primary location
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
				update_post_meta($postId, POST_META_LOCATION, $location->toString());
				update_post_meta($postId, POST_META_LOCALITY_TAG, $postLocality);
				update_post_meta($postId, POST_META_AREA1_TAG, $postArea1);
				update_post_meta($postId, POST_META_AREA2_TAG, $postArea2);
				update_post_meta($postId, POST_META_AREA3_TAG, $postArea3);
			}

			// post secondary location
			$postSecAddress = trim(getPostInput('address_2'));
			$postSecLatitude = getPostInput('latitude_2');
			$postSecLongitude = getPostInput('longitude_2');
			$postSecLocality = getPostInput('locality_2');
			$postSecArea1 = getPostInput('aal1_2');
			$postSecArea2 = getPostInput('aal2_2');
			$postSecArea3 = getPostInput('aal3_2');

			$location2 = null;
			if ($postSecAddress) {
				$location2 = new OutfitLocation($postSecAddress, $postSecLongitude, $postSecLatitude, [
					'locality' => $postSecLocality,
					'aal3' => $postSecArea3,
					'aal2' => $postSecArea2,
					'aal1' => $postSecArea1
				]);

				if ($location2->isValid()) {
					update_post_meta($postId, POST_META_LOCATION_2, $location2->toString());
					update_post_meta($postId, POST_META_LOCALITY_TAG, $postSecLocality);
					update_post_meta($postId, POST_META_AREA1_TAG, $postSecArea1);
					update_post_meta($postId, POST_META_AREA2_TAG, $postSecArea2);
					update_post_meta($postId, POST_META_AREA3_TAG, $postSecArea3);
				}
			}

			// post third optional location
			$postThirdAddress = trim(getPostInput('address_3'));
			$postThirdLatitude = getPostInput('latitude_3');
			$postThirdLongitude = getPostInput('longitude_3');
			$postThirdLocality = getPostInput('locality_3');
			$postThirdArea1 = getPostInput('aal1_3');
			$postThirdArea2 = getPostInput('aal2_3');
			$postThirdArea3 = getPostInput('aal3_3');

			$location3 = null;
			if ($postThirdAddress) {
				$location3 = new OutfitLocation($postThirdAddress, $postThirdLongitude, $postThirdLatitude, [
					'locality' => $postThirdLocality,
					'aal3' => $postThirdArea3,
					'aal2' => $postThirdArea2,
					'aal1' => $postThirdArea1
				]);

				if ($location3->isValid()) {
					update_post_meta($postId, POST_META_LOCATION_3, $location3->toString());
					update_post_meta($postId, POST_META_LOCALITY_TAG, $postThirdLocality);
					update_post_meta($postId, POST_META_AREA1_TAG, $postThirdArea1);
					update_post_meta($postId, POST_META_AREA2_TAG, $postThirdArea2);
					update_post_meta($postId, POST_META_AREA3_TAG, $postThirdArea3);
				}
			}

			// outfit post status
			update_post_meta($postId, POST_META_OUTFIT_STATUS, OUTFIT_AD_STATUS_ACTIVE);

			// post price
			update_post_meta($postId, POST_META_PHONE, $postPhone);

			//post phone
			update_post_meta($postId, POST_META_PRICE, $postPrice);

			//post preferred hours
			update_post_meta($postId, POST_META_PREFERRED_HOURS, $postPreferredHours);

			// post color
			$postColor = getPostMultiple('postColor');
			setPostColors($postId, $postColor);

			// post age group
			$postAgeGroup = getPostMultiple('postAgeGroup');
			setPostAgeGroups($postId, $postAgeGroup);

			// post brand
			$postBrand = getPostMultiple('postBrand');
			setPostBrands($postId, $postBrand);

			// post condition
			$postCondition = getPostInput('postCondition');
			if (!empty($postCondition)) {
				setPostCondition($postId, $postCondition);
			}

			// post writers
			$postWriter = getPostMultiple('postWriter');
			setPostWriters($postId, $postWriter);

			// post characters
			$postCharacter = getPostMultiple('postCharacter');
			setPostCharacters($postId, $postCharacter);

			// post languages
			$postLanguage = getPostMultiple('postLanguage');
			setPostLanguages($postId, $postLanguage);

			// post genders
			$postGender = getPostMultiple('postGender');
			setPostGenders($postId, $postGender);

			// post shoe sizes
			$postShoeSize = getPostMultiple('postShoeSize');
			setPostShoeSizes($postId, $postShoeSize);

			// post maternity sizes
			$postMaternitySize = getPostMultiple('postMaternitySize');
			setPostMaternitySizes($postId, $postMaternitySize);

			// post bicycle sizes
			$postBicycleSize = getPostMultiple('postBicycleSize');
			setPostBicycleSizes($postId, $postBicycleSize);

			//If Its posting featured image//
			if ( isset($_FILES['upload_attachment']) ) {
                $count = 0;
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

                        foreach ($_FILES as $file => $array){
                            $featuredimg = $_POST['outfit_featured_img'];
                            $attachmentId = outfit_insert_attachment($file, $postId);
                            if($count == $featuredimg){
                                set_post_thumbnail( $postId, $attachmentId );
                            }
                            $count++;
                        }

                    }
                }
            }
			if (isset($_POST['postSavePrefs'])) {

				// save user prefs
				if (!empty($postPhone)) {
					update_user_meta($userId, USER_META_PHONE, $postPhone);
				}
				if (!empty($postPreferredHours)) {
					update_user_meta($userId, USER_META_PREFERRED_HOURS, $postPreferredHours);
				}
				if ($location->isValid()) {
					update_user_meta($userId, USER_META_PRIMARY_ADDRESS, $location->toString());
				}
				if (null !== $location2 && $location2->isValid()) {
					update_user_meta($userId, USER_META_SECONDARY_ADDRESS, $location2->toString());
				}
			}

			wp_redirect( outfit_get_thank_you_link() );
			exit;
		}
	}
}

get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<?php 
	$page = get_post($post->ID);
	$current_page_id = $page->ID;
?>
<section class="user-pages">
	<div class="top-blog">		
		<h1 class="page-title"><?php esc_html_e('התחילו למכור', 'outfit-standalone') ?></h1>
		<div class="taxonomy-description"><?php echo do_shortcode("[do_widget id=text-8]"); ?></div>		
	</div>
	<div class="wrap ad-page">
		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-12 ad-tips">
				<div class="form-group reg-text">
					<?php echo do_shortcode("[do_widget id=text-9]"); ?>
				</div>				
			</div>
			<div class="col-lg-7 col-md-7 col-sm-12 user-content-height ad-form">

				<div class="submit-post section-bg-white">
					<form class="form-horizontal" action="" role="form" id="primaryPostForm" method="POST" data-toggle="validator" enctype="multipart/form-data">
						<div class="form-main-section post-detail">
						<!-- add photos and media -->
							<div class="form-group media">
								<label class="text-left flip"><?php esc_html_e('הוסיפו תמונה(ות)', 'outfit-standalone') ?> <span>*</span></label>
								<div class="item">
									<!-- HTML heavily inspired by http://blueimp.github.io/jQuery-File-Upload/ -->
									<div id="mydropzone" class="classiera-image-upload clearfix" data-maxfile="<?php echo esc_attr( $imageLimit ); ?>">
										<!--Imageloop-->
										<?php
										for ($i = 0; $i < $imageLimit; $i++){
											?>
											<div class="classiera-image-box" id="index-<?php echo $i; ?>">
												<div class="classiera-upload-box">
													<input name="image-count" type="hidden" value="<?php echo esc_attr( $imageLimit ); ?>" />
													<input class="classiera-input-file imgInp" id="imgInp<?php echo esc_attr( $i ); ?>" type="file" name="upload_attachment[]">
													<label class="img-label" for="imgInp<?php echo esc_attr( $i ); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/upload-icon.png" /></label>
													<div class="classiera-image-preview">
														<img class="my-image" src=""/>
														<span class="remove-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/post_image_remove.png" /></span>
													</div>
												</div>
											</div>
										<?php } ?>
										<input type="hidden" name="outfit_featured_img" id="outfit_featured_img" value="0">
										<!--Imageloop-->
									</div>

								</div>
							</div>
						<!-- add photos and media -->						

							<div class="form-group">
								<label class="text-left flip" for="title"><?php esc_html_e('מה אתם מוכרים?', 'outfit-standalone') ?> <span>*</span></label>
								<div class="item">
									<input id="title" data-minlength="2" name="postTitle" type="text" class="form-control form-control-md" required>

								</div>
							</div><!-- /Ad title-->

							<div class="form-group">
								<label class="text-left flip" for="title"><?php esc_html_e('מחיר מבוקש', 'outfit-standalone') ?> <span>*</span></label>
								<div class="item">
									<input id="price" data-minlength="1" name="postPrice" placeholder="<?php esc_html_e('מספרים בלבד', 'outfit-standalone') ?>" type="number" class="form-control form-control-md" required>									
								</div>
							</div><!-- /Ad price-->
							
							<div class="ad-categories">
								<div class="form-group post-cat-container">
									<label class="text-left flip"><?php esc_html_e('קטגוריה', 'outfit-standalone') ?> <span>*</span> </label>
									<div class="item">
										<select id="category" name="postCategory" class="reg form-control select2-container form-control-md category-select" required>
											<option value="" selected><?php esc_html_e('בחירת קטגוריה', 'outfit-standalone'); ?></option>
											<?php
											foreach ($categories as $c): ?>
												<?php $c = fetch_category_custom_fields($c); ?>
												<option value="<?php echo $c->term_id; ?>"
														data-color-enabled="<?php echo ($c->catFilterByColor? '1' : '0'); ?>"
														data-brand-enabled="<?php echo ($c->catFilterByBrand? '1' : '0'); ?>"
														data-writer-enabled="<?php echo ($c->catFilterByWriter? '1' : '0'); ?>"
														data-character-enabled="<?php echo ($c->catFilterByCharacter? '1' : '0'); ?>"
														data-age-enabled="<?php echo ($c->catFilterByAge? '1' : '0'); ?>"
														data-condition-enabled="<?php echo ($c->catFilterByCondition? '1' : '0'); ?>"
														data-gender-enabled="<?php echo ($c->catFilterByGender? '1' : '0'); ?>"
														data-language-enabled="<?php echo ($c->catFilterByLanguage? '1' : '0'); ?>"
														data-shoesize-enabled="<?php echo ($c->catFilterByShoeSize? '1' : '0'); ?>"
														data-maternitysize-enabled="<?php echo ($c->catFilterByMaternitySize? '1' : '0'); ?>"
														data-bicyclesize-enabled="<?php echo ($c->catFilterByBicycleSize? '1' : '0'); ?>"
													><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Category-->

								<div class="form-group post-sub-cat-container toggle-required" style="display: none;">
									<label class="text-left flip"><?php esc_html_e('תת קטגוריה', 'outfit-standalone') ?> </label>
									<div class="item">
										<select id="subcategory" name="postSubcategory" class="reg form-control form-control-md"></select>
									</div>
								</div><!-- /Ad Sub Category-->

								<div class="form-group post-colors-container" style="display: none;">
									<label class="text-left flip"><?php esc_html_e('צבע', 'outfit-standalone') ?> <span>*</span></label>
									<div class="item">
										<select id="color" name="postColor[]" class="reg form-control form-control-md">	
											<option value="" selected><?php esc_html_e('בחירת צבע', 'outfit-standalone'); ?></option>
											<?php
											foreach ($colors as $c): ?>
												<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Colors-->

								<div class="form-group post-age-groups-container toggle-required" style="display: none;">
									<label class="text-left flip"><?php esc_html_e('קבוצות גיל', 'outfit-standalone') ?> <span>*</span> </label>
									<div class="item">
										<select id="ageGroup" name="postAgeGroup[]" class="reg ageGroup form-control form-control-md" multiple>		
										<!--<option value=""><?php //esc_html_e('בחירת קבוצת גיל', 'outfit-standalone'); ?></option>-->
											<?php
											foreach ($ageGroups as $c): ?>
												<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Age Groups-->

								<div class="form-group post-brands-container toggle-required" style="display: none;">
									<label class="text-left flip"><?php esc_html_e('מותג', 'outfit-standalone') ?> <span>*</span> </label>
									<div class="item">
										<select id="brand" name="postBrand[]" class="brand-select reg form-control form-control-md">
										<option value="" selected><?php esc_html_e('בחירת מותג', 'outfit-standalone'); ?></option>
											<?php
											foreach ($brands as $c): ?>
												<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Brands-->

								<div class="form-group post-conditions-container toggle-required" style="display: none;">
									<label class="text-left flip"><?php esc_html_e('מצב הפריט', 'outfit-standalone') ?> <span>*</span> </label>
									<div class="item">
										<select id="condition" name="postCondition" class="reg form-control form-control-md">	
											<option value="" selected><?php esc_html_e('בחירת מצב פריט', 'outfit-standalone'); ?></option>
											<?php
											foreach ($conditions as $c): ?>
												<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Conditions-->

								<div class="form-group post-writers-container toggle-required" style="display: none;">
									<label class="text-left flip"><?php esc_html_e('סופר', 'outfit-standalone') ?> <span>*</span> </label>
									<div class="item">
										<select id="writer" name="postWriter[]" class="reg form-control form-control-md">	
											<option value="" selected><?php esc_html_e('בחירת סופר', 'outfit-standalone'); ?></option>
											<?php
											foreach ($writers as $c): ?>
												<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Book Writers-->

								<div class="form-group post-characters-container toggle-required" style="display: none;">
									<label class="text-left flip"><?php esc_html_e('דמות', 'outfit-standalone') ?> <span>*</span> </label>
									<div class="item">
										<select id="character" name="postCharacter[]" class="reg form-control form-control-md">	
											<option value="" selected><?php esc_html_e('בחירת דמות', 'outfit-standalone'); ?></option>
											<?php
											foreach ($characters as $c): ?>
												<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Characters-->

								<div class="form-group post-genders-container toggle-required" style="display: none;">
									<label class="text-left flip"><?php esc_html_e('מין', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="gender" name="postGender[]" class="reg form-control form-control-md">
											<option value="" selected><?php esc_html_e('בחירת מין', 'outfit-standalone'); ?></option>
											<?php
											foreach ($genders as $c): ?>
												<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Genders-->

								<div class="form-group post-languages-container toggle-required" style="display: none;">
									<label class="text-left flip"><?php esc_html_e('שפה', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="language" name="postLanguage[]" class="reg form-control form-control-md">
											<option value="" selected><?php esc_html_e('בחירת שפה', 'outfit-standalone'); ?></option>
											<?php
											foreach ($languages as $c): ?>
												<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Languages-->

								<div class="form-group post-shoe-sizes-container toggle-required" style="display: none;">
									<label class="text-left flip"><?php esc_html_e('מידת נעליים', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="shoeSize" name="postShoeSize[]" class="reg form-control form-control-md">
											<option value="" selected></option>
											<?php
											foreach ($shoeSizes as $c): ?>
												<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Shoe Sizes-->

								<div class="form-group post-maternity-sizes-container toggle-required" style="display: none;">
									<label class="text-left flip"><?php esc_html_e('מידה', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="maternitySize" name="postMaternitySize[]" class="reg form-control form-control-md">
											<option value="" selected></option>
											<?php
											foreach ($maternitySizes as $c): ?>
												<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Maternity Sizes-->

								<div class="form-group post-bicycle-sizes-container toggle-required" style="display: none;">
									<label class="text-left flip"><?php esc_html_e('מידה', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="bicycleSize" name="postBicycleSize[]" class="reg form-control form-control-md">
											<option value="" selected></option>
											<?php
											foreach ($bicycleSizes as $c): ?>
												<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Bicycle Sizes-->

							</div>
							<div class="form-group">
								<label class="text-left flip" for="description"><?php esc_html_e('תיאור הפריט', 'outfit-standalone') ?> </label>
								<div class="item">
									<textarea name="postContent" id="description" class="form-control" data-error="<?php esc_html_e('תיאור הפריט', 'outfit-standalone') ?>"></textarea>
									<div class="help-block with-errors"></div>
								</div>
							</div><!--Ad description-->

							<!--Address-->
							<div class="form-group">
								<label class="text-left flip"><?php esc_html_e('נקודת איסוף עיקרית', 'outfit-standalone'); ?> <span>*</span></label>
								<div class="item">
									<input class="address form-control form-control-md" id="address" type="text"
										   name="address" placeholder="<?php esc_html_e('כתובת או עיר', 'outfit-standalone') ?>"
										   value="<?php echo (null != $userPrimaryLocation? $userPrimaryLocation->getAddress() : ''); ?>" required>
									<input class="latitude" type="hidden" id="latitude" name="latitude"
										   value="<?php echo (null != $userPrimaryLocation? $userPrimaryLocation->getLatitude() : ''); ?>">
									<input class="longitude" type="hidden" id="longitude" name="longitude"
										   value="<?php echo (null != $userPrimaryLocation? $userPrimaryLocation->getLongitude() : ''); ?>">
									<input class="locality" type="hidden" id="locality" name="locality"
										   value="<?php echo (null != $userPrimaryLocation? $userPrimaryLocation->getLocality() : ''); ?>">
									<input class="aal3" type="hidden" id="aal3" name="aal3"
										   value="<?php echo (null != $userPrimaryLocation? $userPrimaryLocation->getAal3() : ''); ?>">
									<input class="aal2" type="hidden" id="aal2" name="aal2"
										   value="<?php echo (null != $userPrimaryLocation? $userPrimaryLocation->getAal2() : ''); ?>">
									<input class="aal1" type="hidden" id="aal1" name="aal1"
										   value="<?php echo (null != $userPrimaryLocation? $userPrimaryLocation->getAal1() : ''); ?>">
								</div>
							</div>

							<div class="form-group">
								<label class="text-left flip"><?php esc_html_e('נקודת איסוף נוספת (לא חובה)', 'outfit-standalone'); ?> </label>
								<div class="item">
									<input class="address form-control form-control-md" id="address_2" type="text"
										   name="address_2" placeholder="<?php esc_html_e('כתובת או עיר', 'outfit-standalone') ?>"
										   value="<?php echo (null != $userSecondaryLocation? $userSecondaryLocation->getAddress() : ''); ?>">
									<input class="latitude" type="hidden" id="latitude_2" name="latitude_2"
										   value="<?php echo (null != $userSecondaryLocation? $userSecondaryLocation->getLatitude() : ''); ?>">
									<input class="longitude" type="hidden" id="longitude_2" name="longitude_2"
										   value="<?php echo (null != $userSecondaryLocation? $userSecondaryLocation->getLongitude() : ''); ?>">
									<input class="locality" type="hidden" id="locality_2" name="locality_2"
										   value="<?php echo (null != $userSecondaryLocation? $userSecondaryLocation->getLocality() : ''); ?>">
									<input class="aal3" type="hidden" id="aal3_2" name="aal3_2"
										   value="<?php echo (null != $userSecondaryLocation? $userSecondaryLocation->getAal3() : ''); ?>">
									<input class="aal2" type="hidden" id="aal2_2" name="aal2_2"
										   value="<?php echo (null != $userSecondaryLocation? $userSecondaryLocation->getAal2() : ''); ?>">
									<input class="aal1" type="hidden" id="aal1_2" name="aal1_2"
										   value="<?php echo (null != $userSecondaryLocation? $userSecondaryLocation->getAal1() : ''); ?>">
								</div>
							</div>

							<div class="form-group">
								<label class="text-left flip"><?php esc_html_e('נקודת איסוף נוספת (לא חובה)', 'outfit-standalone'); ?> </label>
								<div class="item">
									<input class="address form-control form-control-md" id="address_3" type="text"
										   name="address_3" placeholder="<?php esc_html_e('כתובת או עיר', 'outfit-standalone') ?>"
										   value="<?php echo (null != $userThirdLocation? $userThirdLocation->getAddress() : ''); ?>">
									<input class="latitude" type="hidden" id="latitude_3" name="latitude_3"
										   value="<?php echo (null != $userThirdLocation? $userThirdLocation->getLatitude() : ''); ?>">
									<input class="longitude" type="hidden" id="longitude_3" name="longitude_3"
										   value="<?php echo (null != $userThirdLocation? $userThirdLocation->getLongitude() : ''); ?>">
									<input class="locality" type="hidden" id="locality_3" name="locality_3"
										   value="<?php echo (null != $userThirdLocation? $userThirdLocation->getLocality() : ''); ?>">
									<input class="aal3" type="hidden" id="aal3_3" name="aal3_3"
										   value="<?php echo (null != $userThirdLocation? $userThirdLocation->getAal3() : ''); ?>">
									<input class="aal2" type="hidden" id="aal2_3" name="aal2_3"
										   value="<?php echo (null != $userThirdLocation? $userThirdLocation->getAal2() : ''); ?>">
									<input class="aal1" type="hidden" id="aal1_3" name="aal1_3"
										   value="<?php echo (null != $userThirdLocation? $userThirdLocation->getAal1() : ''); ?>">
								</div>
							</div>

							<!--/Address-->

							<div class="form-group">
								<label class="text-left flip"><?php esc_html_e('טלפון ליצירת קשר', 'outfit-standalone') ?> <span>*</span> </label>
								<div class="item">
									<input type="text" id="phone" name="postPhone" class="form-control form-control-md"
										   placeholder="<?php //esc_html_e('Enter your phone number or Mobile number', 'outfit-standalone') ?>"
										   value="<?php echo esc_html($userPhone); ?>" required>
								</div>
							</div>

							<div class="form-group">
								<label class="text-left flip"><?php esc_html_e('שעה נוחה להתקשרות', 'outfit-standalone') ?></label>
								<div class="item">
									<input type="text" id="preferred_hours" name="postPreferredHours" class="form-control form-control-md"
										   placeholder="<?php esc_html_e('בין 8:00 ל 12:00, לא בשבת', 'outfit-standalone') ?>"
										   value="<?php echo esc_html($userPreferredHours); ?>">
								</div>
							</div>						


							<div class="row">
								<div class="item">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" name="postSavePrefs" id="postSavePrefs">
										<label class="form-check-label" for="postSavePrefs">
											<?php esc_html_e('שמרו את הפרטים שלי באתר לפעמים הבאות', 'outfit-standalone') ?>
										</label>
									</div>
								</div>
							</div>
						</div><!---form-main-section post-detail-->							
						<div class="row">
							<div class="item">
								<div class="form-check checkbox">
									<input type="checkbox" class="form-check-input" name="postAgreeToTerms" id="postAgreeToTerms" data-validate="true" data-error="<?php esc_html_e('יש להסכים לתנאי השימוש', 'outfit-standalone') ?>" required>
									<label class="form-check-label" for="postAgreeToTerms">										
										<?php esc_html_e('קראתי ואני מסכימ/ה', 'outfit-standalone') ?> <a href="<?php echo esc_url( $termsandcondition ); ?>" target="_blank"><?php esc_html_e('לתנאי השימוש', 'outfit-standalone') ?></a>
									</label>
									<div class="left-side help-block with-errors" style="color: red;padding-right: 15px;"></div>
								</div>
							</div>
						</div>
						<div class="form-main-btn">
                            <div class="item">
								<?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
								<input type="hidden" name="submitted" id="submitted" value="true">
                                <button class="post-submit btn btn-primary sharp btn-md btn-style-one btn-block" type="submit" name="op" value="Publish Ad"><?php esc_html_e('שליחה', 'outfit-standalone') ?></button>
                            </div>
                        </div>
					</form>
				</div><!--submit-post-->

			</div><!--col-lg-9 col-md-8 user-content-heigh-->
		</div><!--row-->
	</div><!--container-->
</section><!--user-pages-->
<?php endwhile; ?>
<div class="loader_submit_form">
	<img src="<?php echo get_template_directory_uri().'/assets/images/loader-thin.gif' ?>">
</div>
<?php get_footer(); ?>