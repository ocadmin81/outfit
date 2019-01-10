<?php
/**
 * Template name: Edit Ad
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
global $current_user;
wp_get_current_user();
$userId = $current_user->ID;

$authorized = false;
$postFound = false;
$postAuthorId = 0;

if ( isset( $_GET['post'] ) )
	$postId = (int) $_GET['post'];
elseif ( isset( $_POST['post_ID'] ) )
	$postId = (int) $_POST['post_ID'];
else
	$postId = 0;

$post = null;

if ( $postId ) {
	$post = get_post( $postId );
	if ( $post ) {
		if ($post->post_type == OUTFIT_AD_POST_TYPE) {
			$postFound = true;
		}
	}
}
if (!$postFound) {
	wp_redirect( home_url() );
	exit;
}
$postAuthorId = get_post_field('post_author', $postId);
if(current_user_can('administrator') || $postAuthorId == $userId) {
	$authorized = true;
}
if (!$authorized) {
	wp_redirect( home_url() ); exit;
}
$postTitleError = '';
$postPriceError = '';
$catError = '';
$imageError = '';
$postContent = '';
$hasError = false;
$allowed = '';
global $redux_demo;
global $wpdb;

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

$termsandcondition = '/תנאי-שימוש-באתר';
/*
 * post data
 * */
$postTitle = $post->post_title;
$postContent = $post->post_content;
/*
 * categories
 */
$postCategories = getCategoryPath($postId);
$outfitMainCat = (isset($postCategories[0])? $postCategories[0] : 0);
$outfitSubCat = (isset($postCategories[1])? $postCategories[1] : 0);
$outfitSubSubCat = (isset($postCategories[2])? $postCategories[2] : 0);
$subCategories = array();
$subSubCategories = array();
if ($outfitMainCat) {
	$subCategories = getSubCategories($outfitMainCat);
	$brands = getBrandsByCategory($outfitMainCat);
	if ($outfitSubCat) {
		$subSubCategories = getSubCategories($outfitSubCat);
	}
}
$filterBy = null;

$postStatus = get_post_status($postId);
$postPhone = get_post_meta($post->ID, POST_META_PHONE, true);
$postPrice = get_post_meta($post->ID, POST_META_PRICE, true);
$postPreferredHours = get_post_meta($post->ID, POST_META_PREFERRED_HOURS, true);
$featuredimg = get_post_meta($post->ID, POST_META_FEATURED_IMAGE, true);
// post location
$postLocation = OutfitLocation::toAssoc(get_post_meta($post->ID, POST_META_LOCATION, true));
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
// post second location
$postLocation2 = OutfitLocation::toAssoc(get_post_meta($post->ID, POST_META_LOCATION_2, true));
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
$postLocation3 = OutfitLocation::toAssoc(get_post_meta($post->ID, POST_META_LOCATION_3, true));
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

$postColor = getPostColors($postId);
$postAgeGroup = getPostAgeGroups($postId);
$postBrand = getPostBrands($postId);
$postConditions = getPostConditions($postId);
$postCondition = (isset($postConditions[0])? $postConditions[0] : '');
$postWriter = getPostWriters($postId);
$postCharacter = getPostCharacters($postId);
$postGender = getPostGenders($postId);
$postLanguage = getPostLanguages($postId);
$postShoeSize = getPostShoeSizes($postId);
$postMaternitySize = getPostMaternitySizes($postId);
$postBicycleSize = getPostBicycleSizes($postId);
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
outfit_strip_slashes_from_input();
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
		$imgargs = array(
			'post_parent' => $postId,
			'post_status' => 'inherit',
			'post_type'   => 'attachment',
			'post_mime_type'   => 'image',
			'order' => 'ASC',
			'orderby' => 'menu_order ID',
		);
		$attachments = get_children($imgargs);
		$count = count($attachments);

		if(!$count && (!isset($_FILES['upload_attachment']) || array_of_empties($_FILES['upload_attachment']['name']))) {
			$imageError = esc_html__( 'יש לעלות תמונה אחת לפחות', 'outfit-standalone' );
			$hasError = true;
		}
		//Image Count check//

		if ($hasError === false) {

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
			$outfitSubSubCat = getPostInput('postSubSubcategory');
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
				'ID' => $postId,
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

			// post location
			delete_post_meta($postId, POST_META_LOCATION);
			delete_post_meta($postId, POST_META_LOCATION_2);
			delete_post_meta($postId, POST_META_LOCALITY_TAG);
			delete_post_meta($postId, POST_META_AREA1_TAG);
			delete_post_meta($postId, POST_META_AREA2_TAG);
			delete_post_meta($postId, POST_META_AREA3_TAG);

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

				add_post_meta($postId, POST_META_LOCATION, $location->toString());
				add_post_meta($postId, POST_META_LOCALITY_TAG, $postLocality);
				add_post_meta($postId, POST_META_AREA1_TAG, $postArea1);
				add_post_meta($postId, POST_META_AREA2_TAG, $postArea2);
				add_post_meta($postId, POST_META_AREA3_TAG, $postArea3);
			}

			// post second optional location
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

					add_post_meta($postId, POST_META_LOCATION_2, $location2->toString());
					add_post_meta($postId, POST_META_LOCALITY_TAG, $postSecLocality);
					add_post_meta($postId, POST_META_AREA1_TAG, $postSecArea1);
					add_post_meta($postId, POST_META_AREA2_TAG, $postSecArea2);
					add_post_meta($postId, POST_META_AREA3_TAG, $postSecArea3);
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

			if ($postThirdAddress) {
				$location3 = new OutfitLocation($postThirdAddress, $postThirdLongitude, $postThirdLatitude, [
					'locality' => $postThirdLocality,
					'aal3' => $postThirdArea3,
					'aal2' => $postThirdArea2,
					'aal1' => $postThirdArea1
				]);

				if ($location3->isValid()) {

					add_post_meta($postId, POST_META_LOCATION_3, $location3->toString());
					add_post_meta($postId, POST_META_LOCALITY_TAG, $postThirdLocality);
					add_post_meta($postId, POST_META_AREA1_TAG, $postThirdArea1);
					add_post_meta($postId, POST_META_AREA2_TAG, $postThirdArea2);
					add_post_meta($postId, POST_META_AREA3_TAG, $postThirdArea3);
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

				if (null !== $location3 && $location3->isValid()) {
					update_user_meta($userId, USER_META_THIRD_ADDRESS, $location3->toString());
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
<section class="user-pages section-gray-bg">
	<span class="hidden" id="removeImg"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/post_image_remove.png" /></span>
	<span class="hidden" id="addImg"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/upload-icon.png" /></span>
	<div class="wrap ad-page">
		<div class="row">
			<div class="col-lg-3 col-md-4">
				<?php get_template_part( 'templates/profile/profile-sidebar' );?>
			</div><!--col-lg-3 col-md-4-->
			<div class="col-lg-9 col-md-8 ad-form user-content-height edit">
				<div class="submit-post section-bg-white">
					<h1 class="text-uppercase border-bottom"><?php esc_html_e('עריכת מודעה', 'outfit-standalone') ?></h1>		
					<form class="form-horizontal" action="" role="form" id="primaryPostForm" method="POST" data-toggle="validator" enctype="multipart/form-data">

						<!-- add photos and media -->
						<div class="form-main-section media-detail">

							<div class="form-group">
								<label class="text-left flip"><?php esc_html_e('הוסיפו תמונה(ות)', 'outfit-standalone') ?> : <span>*</span></label>
								<div class="item">
									<!-- HTML heavily inspired by http://blueimp.github.io/jQuery-File-Upload/ -->
									<div id="mydropzone" class="classiera-image-upload clearfix" data-maxfile="<?php echo esc_attr( $imageLimit ); ?>">
										<!--PreviousImages-->
										<?php
										$imageCount = 0;
										$imgargs = array(
											'post_parent' => $postId,
											'post_status' => 'inherit',
											'post_type'   => 'attachment',
											'post_mime_type'   => 'image',
											'order' => 'ASC',
											'orderby' => 'menu_order ID',
										);
										$attachments = get_children($imgargs);
										if($attachments){
											foreach($attachments as $att_id => $attachment){
												$attachment_ID = $attachment->ID;
												$full_img_url = wp_get_attachment_url($attachment->ID);
												?>
												<div id="<?php echo esc_attr($attachment_ID); ?>" class="edit-post-image-block classiera-image-box edit">
													<div class="classiera-upload-box">
														<div class="classiera-image-preview" style="display:block;">
															<img class="edit-post-image" src="<?php echo esc_url($full_img_url);?>" />
															<div class="remove-edit-post-image">
																<span class="remove-img remImage"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/post_image_remove.png" /></span>															
																<input type="hidden" name="" value="<?php echo esc_attr($attachment_ID); ?>">
															</div><!--remove-edit-post-image-->
														</div>
													</div>
												</div>
												<?php $imageCount++;?>
											<?php }?>
										<?php }?>
										<!--PreviousImages-->
										<!--Imageloop-->
										<?php
										$imageCounter = $imageLimit-$imageCount;
										for ($i = 0; $i < $imageCounter; $i++){
											?>
											<div class="classiera-image-box edit">
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

									<?php if ($imageError) { ?>
									<div class="left-side help-block with-errors" style="color: red;padding-right: 15px;"><?php echo esc_html($imageError); ?></div>
									<?php } ?>
								</div>
							</div>
						<!-- add photos and media -->

							<div class="form-group">
								<label class="text-left flip" for="title"><?php esc_html_e('מה אתם מוכרים?', 'outfit-standalone') ?><span>*</span></label>
								<div class="item">
									<input id="title" data-minlength="2" name="postTitle" type="text" class="form-control form-control-md" value="<?php echo esc_html($postTitle); ?>" placeholder="<?php //esc_html_e('Ad Title Goes here', 'outfit-standalone') ?>" required>

								</div>
							</div><!-- /Ad title-->

							<div class="form-group">
								<label class="text-left flip" for="title"><?php esc_html_e('מחיר מבוקש', 'outfit-standalone') ?><span>*</span></label>
								<div class="item">
									<input id="price" data-minlength="1" name="postPrice" placeholder="<?php esc_html_e('מספרים בלבד', 'outfit-standalone') ?>" type="number" class="form-control form-control-md" value="<?php echo esc_html($postPrice); ?>" placeholder="<?php //esc_html_e('Price', 'outfit-standalone') ?>" required>

								</div>
							</div><!-- /Ad price-->
							<div class="ad-categories">
								<div class="form-group post-cat-container perfit-cats">
									<label class="text-left flip"><?php esc_html_e('קטגוריה', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="category" name="postCategory" class="reg form-control form-control-md category-select" required>
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
														<?php if ($outfitMainCat && $c->term_id == $outfitMainCat) {
															$filterBy = $c;
															echo 'selected';
														} ?>
													><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Category-->

								<div class="form-group post-sub-cat-container perfit-cats toggle-required" style="<?php echo (count($subCategories)? '' : 'display: none;'); ?>">
									<label class="text-left flip"><?php esc_html_e('תת קטגוריה', 'outfit-standalone') ?></label>
									<div class="item">
										<select id="subcategory" name="postSubcategory" class="reg form-control form-control-md">
											<option value=""><?php esc_html_e('בחירת תת קטגוריה', 'outfit-standalone'); ?></option>
											<?php foreach ($subCategories as $c): ?>
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
													<?php if ($outfitSubCat && $c->term_id == $outfitSubCat) {
														$filterBy = $c;
														echo 'selected';
													} ?>
													><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Sub Category-->

								<div class="form-group post-sub-sub-cat-container perfit-cats toggle-required" style="<?php echo (count($subSubCategories)? '' : 'display: none;'); ?>">
									<label class="text-left flip"><?php esc_html_e('תת קטגוריה', 'outfit-standalone') ?></label>
									<div class="item">
										<select id="subsubcategory" name="postSubSubcategory" class="reg form-control form-control-md">
											<option value=""><?php esc_html_e('בחירת תת קטגוריה', 'outfit-standalone'); ?></option>
											<?php foreach ($subSubCategories as $c): ?>
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
													<?php if ($outfitSubSubCat && $c->term_id == $outfitSubSubCat) {
														$filterBy = $c;
														echo 'selected';
													} ?>
													><?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Sub Category-->

								<div class="form-group post-colors-container toggle-required"
									style="<?php echo (($filterBy && $filterBy->catFilterByColor)? '' : 'display: none;'); ?>">
									<label class="text-left flip"><?php esc_html_e('צבע', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="color" name="postColor[]" class="reg form-control form-control-md"
											<?php echo (($filterBy && $filterBy->catFilterByColor)? 'required' : ''); ?>>											
											<?php
											foreach ($colors as $c): ?>
												<option value="<?php echo $c->term_id; ?>"
													<?php echo (in_array($c->term_id, $postColor)? 'selected' : ''); ?>>
													<?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Colors-->

								<div class="form-group post-age-groups-container toggle-required"
									 style="<?php echo (($filterBy && $filterBy->catFilterByAge)? '' : 'display: none;'); ?>">
									<label class="text-left flip"><?php esc_html_e('קבוצות גיל', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="ageGroup" name="postAgeGroup[]" class="reg ageGroup form-control form-control-md"
											<?php echo (($filterBy && $filterBy->catFilterByAge)? 'required' : ''); ?> multiple>
											<?php
											foreach ($ageGroups as $c): ?>
												<option value="<?php echo $c->term_id; ?>"
													<?php echo (in_array($c->term_id, $postAgeGroup)? 'selected' : ''); ?>>
													<?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Age Groups-->

								<div class="form-group post-brands-container toggle-required"
									 style="<?php echo (($filterBy && $filterBy->catFilterByBrand)? '' : 'display: none;'); ?>">
									<label class="text-left flip"><?php esc_html_e('מותג', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="brand" name="postBrand[]" class="brand-select reg form-control form-control-md"
											<?php echo (($filterBy && $filterBy->catFilterByBrand)? 'required' : ''); ?>>
											<?php
											foreach ($brands as $c): ?>
												<option value="<?php echo $c->term_id; ?>"
													<?php echo (in_array($c->term_id, $postBrand)? 'selected' : ''); ?>>
													<?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Brands-->

								<div class="form-group post-conditions-container"
									 style="<?php echo (($filterBy && $filterBy->catFilterByCondition)? '' : 'display: none;'); ?>">
									<label class="text-left flip"><?php esc_html_e('מצב הפריט', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="condition" name="postCondition" class="reg form-control form-control-md"
											<?php echo (($filterBy && $filterBy->catFilterByCondition)? 'required' : ''); ?>>
											<option></option>
											<?php
											foreach ($conditions as $c): ?>
												<option value="<?php echo $c->term_id; ?>"
													<?php echo ($c->term_id == $postCondition? 'selected' : ''); ?>>
													<?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Conditions-->

								<div class="form-group post-writers-container toggle-required"
									 style="<?php echo (($filterBy && $filterBy->catFilterByWriter)? '' : 'display: none;'); ?>">
									<label class="text-left flip"><?php esc_html_e('כותכ', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="writer" name="postWriter[]" class="reg form-control form-control-md"
											<?php echo (($filterBy && $filterBy->catFilterByWriter)? 'required' : ''); ?>>	
											<option></option>
											<?php
											foreach ($writers as $c): ?>
												<option value="<?php echo $c->term_id; ?>"
													<?php echo (in_array($c->term_id, $postWriter)? 'selected' : ''); ?>>
													<?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Book Writers-->

								<div class="form-group post-characters-container toggle-required"
									 style="<?php echo (($filterBy && $filterBy->catFilterByCharacter)? '' : 'display: none;'); ?>">
									<label class="text-left flip"><?php esc_html_e('Character', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="character" name="postCharacter[]" class="reg form-control form-control-md"
											<?php echo (($filterBy && $filterBy->catFilterByCharacter)? 'required' : ''); ?>>
											<option value=""><?php esc_html_e('Select Characters', 'outfit-standalone'); ?></option>
											<option></option>
											<?php
											foreach ($characters as $c): ?>
												<option value="<?php echo $c->term_id; ?>"
													<?php echo (in_array($c->term_id, $postCharacter)? 'selected' : ''); ?>>
													<?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Characters-->

								<div class="form-group post-genders-container toggle-required"
									 style="<?php echo (($filterBy && $filterBy->catFilterByGender)? '' : 'display: none;'); ?>">
									<label class="text-left flip"><?php esc_html_e('מין', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="gender" name="postGender[]" class="reg form-control form-control-md"
											<?php echo (($filterBy && $filterBy->catFilterByGender)? 'required' : ''); ?>>
											<option></option>
											<?php
											foreach ($genders as $c): ?>
												<option value="<?php echo $c->term_id; ?>"
													<?php echo (in_array($c->term_id, $postGender)? 'selected' : ''); ?>>
													<?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Genders-->

								<div class="form-group post-languages-container toggle-required"
									 style="<?php echo (($filterBy && $filterBy->catFilterByLanguage)? '' : 'display: none;'); ?>">
									<label class="text-left flip"><?php esc_html_e('שפה', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="language" name="postLanguage[]" class="reg form-control form-control-md"
											<?php echo (($filterBy && $filterBy->catFilterByLanguage)? 'required' : ''); ?>>
											<option></option>
											<?php
											foreach ($languages as $c): ?>
												<option value="<?php echo $c->term_id; ?>"
													<?php echo (in_array($c->term_id, $postLanguage)? 'selected' : ''); ?>>
													<?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Languages-->

								<div class="form-group post-shoe-sizes-container toggle-required"
									 style="<?php echo (($filterBy && $filterBy->catFilterByShoeSize)? '' : 'display: none;'); ?>">
									<label class="text-left flip"><?php esc_html_e('מידת נעליים', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="shoeSize" name="postShoeSize[]" class="reg form-control form-control-md"
											<?php echo (($filterBy && $filterBy->catFilterByShoeSize)? 'required' : ''); ?>>
											<option></option>
											<?php
											foreach ($shoeSizes as $c): ?>
												<option value="<?php echo $c->term_id; ?>"
													<?php echo (in_array($c->term_id, $postShoeSize)? 'selected' : ''); ?>>
													<?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Shoe Sizes-->

								<div class="form-group post-maternity-sizes-container toggle-required"
									 style="<?php echo (($filterBy && $filterBy->catFilterByMaternitySize)? '' : 'display: none;'); ?>">
									<label class="text-left flip"><?php esc_html_e('מידה', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="maternitySize" name="postMaternitySize[]" class="reg form-control form-control-md"
											<?php echo (($filterBy && $filterBy->catFilterByMaternitySize)? 'required' : ''); ?>>
											<option></option>
											<?php
											foreach ($maternitySizes as $c): ?>
												<option value="<?php echo $c->term_id; ?>"
													<?php echo (in_array($c->term_id, $postMaternitySize)? 'selected' : ''); ?>>
													<?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Maternity Sizes-->

								<div class="form-group post-bicycle-sizes-container toggle-required"
									 style="<?php echo (($filterBy && $filterBy->catFilterByBicycleSize)? '' : 'display: none;'); ?>">
									<label class="text-left flip"><?php esc_html_e('מידה', 'outfit-standalone') ?><span>*</span> </label>
									<div class="item">
										<select id="bicycleSize" name="postBicycleSize[]" class="reg form-control form-control-md"
											<?php echo (($filterBy && $filterBy->catFilterByBicycleSize)? 'required' : ''); ?>>
											<option></option>
											<?php
											foreach ($bicycleSizes as $c): ?>
												<option value="<?php echo $c->term_id; ?>"
													<?php echo (in_array($c->term_id, $postBicycleSize)? 'selected' : ''); ?>>
													<?php esc_html_e($c->name); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div><!-- /Ad Bicycle Sizes-->

							</div>
							<div class="form-group">
								<label class="text-left flip" for="description"><?php esc_html_e('תיאור הפריט', 'outfit-standalone') ?> : </label>
								<div class="item">
									<textarea name="postContent" id="description" class="form-control" data-error="<?php esc_html_e('Write description', 'outfit-standalone') ?>"><?php echo esc_html($postContent);?></textarea>
									<div class="help-block with-errors"></div>
								</div>
							</div><!--Ad description-->

							<!--Address-->
							<div class="form-group">
								<label class="text-left flip"><?php esc_html_e('נקודת איסוף עיקרית', 'outfit-standalone'); ?><span>*</span></label>
								<div class="item">
									<input id="address" type="text" name="address" class="address form-control form-control-md" value="<?php echo esc_html($postAddress); ?>" placeholder="<?php esc_html_e('כתובת או עיר', 'outfit-standalone') ?>" required>
									<input class="latitude" type="hidden" id="latitude" name="latitude" value="<?php echo esc_html($postLatitude); ?>">
									<input class="longitude" type="hidden" id="longitude" name="longitude" value="<?php echo esc_html($postLongitude); ?>">
									<input class="locality" type="hidden" id="locality" name="locality" value="<?php echo esc_html($postLocality); ?>">
									<input class="aal3" type="hidden" id="aal3" name="aal3" value="<?php echo esc_html($postArea3); ?>">
									<input class="aal2" type="hidden" id="aal2" name="aal2" value="<?php echo esc_html($postArea2); ?>">
									<input class="aal1" type="hidden" id="aal1" name="aal1" value="<?php echo esc_html($postArea1); ?>">
								</div>
							</div>

							<div class="form-group">
								<label class="text-left flip"><?php esc_html_e('נקודת איסוף נוספת (לא חובה)', 'outfit-standalone'); ?> : </label>
								<div class="item">
									<input id="address_2" type="text" name="address_2" class="address form-control form-control-md" value="<?php echo esc_html($postSecAddress); ?>" placeholder="<?php esc_html_e('כתובת או עיר	', 'outfit-standalone') ?>">
									<input class="latitude" type="hidden" id="latitude_2" name="latitude_2" value="<?php echo esc_html($postSecLatitude); ?>">
									<input class="longitude" type="hidden" id="longitude_2" name="longitude_2" value="<?php echo esc_html($postSecLongitude); ?>">
									<input class="locality" type="hidden" id="locality_2" name="locality_2" value="<?php echo esc_html($postSecLocality); ?>">
									<input class="aal3" type="hidden" id="aal3_2" name="aal3_2" value="<?php echo esc_html($postSecArea3); ?>">
									<input class="aal2" type="hidden" id="aal2_2" name="aal2_2" value="<?php echo esc_html($postSecArea2); ?>">
									<input class="aal1" type="hidden" id="aal1_2" name="aal1_2" value="<?php echo esc_html($postSecArea1); ?>">
								</div>
							</div>

							<div class="form-group">
								<label class="text-left flip"><?php esc_html_e('נקודת איסוף נוספת (לא חובה)', 'outfit-standalone'); ?> : </label>
								<div class="item">
									<input id="address_3" type="text" name="address_3" class="address form-control form-control-md" value="<?php echo esc_html($postThirdAddress); ?>" placeholder="<?php esc_html_e('כתובת או עיר	', 'outfit-standalone') ?>">
									<input class="latitude" type="hidden" id="latitude_3" name="latitude_3" value="<?php echo esc_html($postThirdLatitude); ?>">
									<input class="longitude" type="hidden" id="longitude_3" name="longitude_3" value="<?php echo esc_html($postThirdLongitude); ?>">
									<input class="locality" type="hidden" id="locality_3" name="locality_3" value="<?php echo esc_html($postThirdLocality); ?>">
									<input class="aal3" type="hidden" id="aal3_3" name="aal3_3" value="<?php echo esc_html($postThirdArea3); ?>">
									<input class="aal2" type="hidden" id="aal2_3" name="aal2_3" value="<?php echo esc_html($postThirdArea2); ?>">
									<input class="aal1" type="hidden" id="aal1_3" name="aal1_3" value="<?php echo esc_html($postThirdArea1); ?>">
								</div>
							</div>

							<!--/Address-->

							<div class="form-group">
								<label class="text-left flip"><?php esc_html_e('טלפון ליצירת קשר', 'outfit-standalone') ?> : <span>*</span> </label>
								<div class="item">
									<input type="text" id="phone" name="postPhone" class="form-control form-control-md" value="<?php echo esc_html($postPhone); ?>" placeholder="<?php //esc_html_e('Enter your phone number or Mobile number', 'outfit-standalone') ?>" required>
								</div>
							</div>

							<div class="form-group">
								<label class="text-left flip"><?php esc_html_e('שעה נוחה להתקשרות', 'outfit-standalone') ?> :</label>
								<div class="item">
									<input type="text" id="preferred_hours" name="postPreferredHours" placeholder="<?php esc_html_e('בין 8:00 ל 12:00, לא בשבת', 'outfit-standalone') ?>" class="form-control form-control-md" value="<?php echo esc_html($postPreferredHours); ?>" placeholder="<?php //esc_html_e('Enter your preferred hours to contact', 'outfit-standalone') ?>">
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
									<input type="checkbox" class="form-check-input" name="postAgreeToTerms" id="postAgreeToTerms" data-error="<?php esc_html_e('יש להסכים לתנאי השימוש', 'outfit-standalone') ?>" required>
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
								<input type="hidden" name="post_ID" id="post_ID" value="<?php esc_html($postId); ?>">
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