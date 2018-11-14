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
	global $redux_demo; 
	$login = $redux_demo['login'];
	wp_redirect( $login ); exit;
}

$current_user = wp_get_current_user();
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
$hasError = '';
$allowed = '';
global $redux_demo;
global $wpdb;

$cUserCheck = current_user_can( 'administrator' );
$role = $current_user->roles;
$currentRole = $role[0];
$imageLimit = 5;

$categories = outfit_get_main_cats(true);
$ageGroups = outfit_get_list_of_age_groups();
$colors = outfit_get_list_of_colors();
$brands = array();
$conditions = outfit_get_list_of_conditions();
$writers = outfit_get_list_of_writers();
$characters = outfit_get_list_of_characters();

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
if ($outfitMainCat) {
	$subCategories = getSubCategories($outfitMainCat);
	$brands = getBrandsByCategory($outfitMainCat);
}
$filterBy = null;

$postStatus = get_post_status($postId);
$postPhone = get_post_meta($post->ID, POST_META_PHONE, true);
$postPrice = get_post_meta($post->ID, POST_META_PRICE, true);
$postPreferredHours = get_post_meta($post->ID, POST_META_PREFERRED_HOURS, true);
// post location
$postLocation = json_decode(get_post_meta($post->ID, POST_META_LOCATION, true), true);
$postAddress = '';
$postLatitude = '';
$postLongitude = '';
$postLocality = $postArea1 = $postArea2 = $postArea3 = '';
if (null !== $postLocation && isset($postLocation['address'])) {
	$postAddress = urldecode($postLocation['address']);
	$postLatitude = $postLocation['latitude'];
	$postLongitude = $postLocation['longitude'];
	$postLocality = $postLocation['locality'];
	$postArea1 = $postLocation['aal1'];
	$postArea2 = $postLocation['aal2'];
	$postArea3 = $postLocation['aal3'];
}
// post secondary location
$postLocation2 = json_decode(get_post_meta($post->ID, POST_META_LOCATION_2, true), true);
$postSecAddress = '';
$postSecLatitude = '';
$postSecLongitude = '';
$postSecLocality = $postSecArea1 = $postSecArea2 = $postSecArea3 = '';
if (null !== $postLocation2 && isset($postLocation2['address'])) {
	$postSecAddress = urldecode($postLocation2['address']);
	$postSecLatitude = $postLocation2['latitude'];
	$postSecLongitude = $postLocation2['longitude'];
	$postSecLocality = $postLocation2['locality'];
	$postSecArea1 = $postLocation2['aal1'];
	$postSecArea2 = $postLocation2['aal2'];
	$postSecArea3 = $postLocation2['aal3'];
}
$postColor = getPostColors($postId);
$postAgeGroup = getPostAgeGroups($postId);
$postBrand = getPostBrands($postId);
$postConditions = getPostConditions($postId);
$postCondition = (isset($postConditions[0])? $postConditions[0] : '');
$postWriter = getPostWriters($postId);
$postCharacter = getPostCharacters($postId);
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
			$postTitleError =  esc_html__( 'Please enter a title.', 'outfit-standalone' );
			$hasError = true;
		}
		if (empty($_POST['postCategory'])) {
			$catError = esc_html__( 'Please select a category', 'outfit-standalone' );
			$hasError = true;
		}
		//Image Count check//
		$files = $_FILES['upload_attachment'];
		$count = count($files['name']);
		if($count == 0){
			$imageError = esc_html__( 'Please, upload at least one image', 'outfit-standalone' );
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

			// post location
			$postPrice = trim(getPostInput('postPrice'));
			$postPhone = trim(getPostInput('postPhone'));
			$postPreferredHours = trim(getPostInput('postPreferredHours'));
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
					update_post_meta($postId, POST_META_LOCATION_2, $location2->toString());
				}
			}

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
			}

			wp_redirect(get_permalink( $postId )); exit();
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
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h4 class="text-uppercase border-bottom"><?php esc_html_e('MAKE NEW AD', 'outfit-standalone') ?></h4>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-3 col-md-4">
				<p>some stuff</p>
			</div><!--col-lg-3 col-md-4-->
			<div class="col-lg-9 col-md-8 user-content-height">

				<div class="submit-post section-bg-white">
					<form class="form-horizontal" action="" role="form" id="primaryPostForm" method="POST" data-toggle="validator" enctype="multipart/form-data">

						<!-- add photos and media -->
						<div class="form-main-section media-detail">

							<div class="form-group">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Photos for your ad', 'outfit-standalone') ?> : <span>*</span></label>
								<div class="col-sm-9">
									<div class="classiera-dropzone-heading">
										<i class="classiera-dropzone-heading-text fa fa-cloud-upload-alt" aria-hidden="true"></i>
										<div class="classiera-dropzone-heading-text">
											<p><?php esc_html_e('Select files to Upload', 'outfit-standalone') ?></p>
											<p><?php esc_html_e('You can add multiple images. Ads With photo get 50% more Responses', 'outfit-standalone') ?></p>
											<p class="limitIMG"><?php esc_html_e('You can upload', 'outfit-standalone') ?>&nbsp;<?php echo esc_attr( $imageLimit ); ?>&nbsp;<?php esc_html_e('Images maximum.', 'outfit-standalone') ?></p>
										</div>
									</div>
									<!-- HTML heavily inspired by http://blueimp.github.io/jQuery-File-Upload/ -->
									<div id="mydropzone" class="classiera-image-upload clearfix" data-maxfile="<?php echo esc_attr( $imageLimit ); ?>">
										<!--PreviousImages-->
										<?php
										$imageCount = 0;
										$imgargs = array(
											'post_parent' => $current_post,
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
												<div id="<?php echo esc_attr($attachment_ID); ?>" class="edit-post-image-block">
													<img class="edit-post-image" src="<?php echo esc_url($full_img_url);?>" />
													<div class="remove-edit-post-image">
														<i class="fas fa-minus-square"></i>
														<span class="remImage"><?php esc_html_e('Remove', 'outfit-standalone');?></span>
														<input type="hidden" name="" value="<?php echo esc_attr($attachment_ID); ?>">
													</div><!--remove-edit-post-image-->
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
											<div class="classiera-image-box">
												<div class="classiera-upload-box">
													<input name="image-count" type="hidden" value="<?php echo esc_attr( $imageLimit ); ?>" />
													<input class="classiera-input-file imgInp" id="imgInp<?php echo esc_attr( $i ); ?>" type="file" name="upload_attachment[]">
													<label class="img-label" for="imgInp<?php echo esc_attr( $i ); ?>"><i class="fas fa-plus-square"></i></label>
													<div class="classiera-image-preview">
														<img class="my-image" src=""/>
														<span class="remove-img"><i class="fas fa-times-circle"></i></span>
													</div>
												</div>
											</div>
										<?php } ?>
										<input type="hidden" name="outfit_featured_img" id="outfit_featured_img" value="0">
										<!--Imageloop-->
									</div>

								</div>
							</div>
						</div>
						<!-- add photos and media -->

						<div class="form-main-section post-detail">

							<div class="form-group">
								<label class="col-sm-3 text-left flip" for="title"><?php esc_html_e('Ad title', 'outfit-standalone') ?> : <span>*</span></label>
								<div class="col-sm-9">
									<input id="title" data-minlength="5" name="postTitle" type="text" class="form-control form-control-md" value="<?php echo esc_html($postTitle); ?>" placeholder="<?php esc_html_e('Ad Title Goes here', 'outfit-standalone') ?>" required>

								</div>
							</div><!-- /Ad title-->

							<div class="form-group">
								<label class="col-sm-3 text-left flip" for="title"><?php esc_html_e('Price', 'outfit-standalone') ?> : <span>*</span></label>
								<div class="col-sm-9">
									<input id="price" data-minlength="1" name="postPrice" type="text" class="form-control form-control-md" value="<?php echo esc_html($postPrice); ?>" placeholder="<?php esc_html_e('Price', 'outfit-standalone') ?>" required>

								</div>
							</div><!-- /Ad price-->

							<div class="form-group post-cat-container">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Category', 'outfit-standalone') ?> : <span>*</span> </label>
								<div class="col-sm-9">
									<select id="category" name="postCategory" class="form-control form-control-md category-select" required>
										<option value="" selected><?php esc_html_e('Select Category', 'outfit-standalone'); ?></option>
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
													<?php if ($outfitMainCat && $c->term_id == $outfitMainCat) {
														$filterBy = $c;
														echo 'selected';
													} ?>
												><?php esc_html_e($c->name); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div><!-- /Ad Category-->

							<div class="form-group post-sub-cat-container" style="<?php echo (count($subCategories)? '' : 'display: none;'); ?>">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Sub Category', 'outfit-standalone') ?> : </label>
								<div class="col-sm-9">
									<select id="subcategory" name="postSubcategory" class="form-control form-control-md">
										<option value=""><?php esc_html_e('Select Sub Category', 'outfit-standalone'); ?></option>
										<?php foreach ($subCategories as $c): ?>
											<option value="<?php echo $c->term_id; ?>"
												<?php if ($outfitSubCat && $c->term_id == $outfitSubCat) {
													echo 'selected';
												} ?>
												><?php esc_html_e($c->name); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div><!-- /Ad Sub Category-->

							<div class="form-group post-colors-container"
								style="<?php echo (($filterBy && $filterBy->catFilterByColor)? '' : 'display: none;'); ?>">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Color', 'outfit-standalone') ?> : <span>*</span> </label>
								<div class="col-sm-9">
									<select id="color" name="postColor[]" class="form-control form-control-md" multiple
										<?php echo (($filterBy && $filterBy->catFilterByColor)? 'required' : ''); ?>>
										<option value=""><?php esc_html_e('Select Colors', 'outfit-standalone'); ?></option>
										<?php
										foreach ($colors as $c): ?>
											<option value="<?php echo $c->term_id; ?>"
												<?php echo (in_array($c->term_id, $postColor)? 'selected' : ''); ?>>
												<?php esc_html_e($c->name); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div><!-- /Ad Colors-->

							<div class="form-group post-age-groups-container"
								 style="<?php echo (($filterBy && $filterBy->catFilterByAge)? '' : 'display: none;'); ?>">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Age Group', 'outfit-standalone') ?> : <span>*</span> </label>
								<div class="col-sm-9">
									<select id="ageGroup" name="postAgeGroup[]" class="form-control form-control-md" multiple
										<?php echo (($filterBy && $filterBy->catFilterByAge)? 'required' : ''); ?>>
										<option value=""><?php esc_html_e('Select Age Groups', 'outfit-standalone'); ?></option>
										<?php
										foreach ($ageGroups as $c): ?>
											<option value="<?php echo $c->term_id; ?>"
												<?php echo (in_array($c->term_id, $postAgeGroup)? 'selected' : ''); ?>>
												<?php esc_html_e($c->name); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div><!-- /Ad Age Groups-->

							<div class="form-group post-brands-container"
								 style="<?php echo (($filterBy && $filterBy->catFilterByBrand)? '' : 'display: none;'); ?>">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Brand', 'outfit-standalone') ?> : <span>*</span> </label>
								<div class="col-sm-9">
									<select id="brand" name="postBrand[]" class="form-control form-control-md" multiple
										<?php echo (($filterBy && $filterBy->catFilterByBrand)? 'required' : ''); ?>>
										<option value=""><?php esc_html_e('Select Brands', 'outfit-standalone'); ?></option>
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
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Condition', 'outfit-standalone') ?> : <span>*</span> </label>
								<div class="col-sm-9">
									<select id="condition" name="postCondition" class="form-control form-control-md"
										<?php echo (($filterBy && $filterBy->catFilterByCondition)? 'required' : ''); ?>>
										<option value=""><?php esc_html_e('Select Condition', 'outfit-standalone'); ?></option>
										<?php
										foreach ($conditions as $c): ?>
											<option value="<?php echo $c->term_id; ?>"
												<?php echo ($c->term_id == $postCondition? 'selected' : ''); ?>>
												<?php esc_html_e($c->name); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div><!-- /Ad Conditions-->

							<div class="form-group post-writers-container"
								 style="<?php echo (($filterBy && $filterBy->catFilterByWriter)? '' : 'display: none;'); ?>">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Writer', 'outfit-standalone') ?> : <span>*</span> </label>
								<div class="col-sm-9">
									<select id="writer" name="postWriter[]" class="form-control form-control-md" multiple
										<?php echo (($filterBy && $filterBy->catFilterByWriter)? 'required' : ''); ?>>
										<option value=""><?php esc_html_e('Select Writers', 'outfit-standalone'); ?></option>
										<?php
										foreach ($writers as $c): ?>
											<option value="<?php echo $c->term_id; ?>"
												<?php echo (in_array($c->term_id, $postWriter)? 'selected' : ''); ?>>
												<?php esc_html_e($c->name); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div><!-- /Ad Book Writers-->

							<div class="form-group post-characters-container"
								 style="<?php echo (($filterBy && $filterBy->catFilterByCharacter)? '' : 'display: none;'); ?>">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Character', 'outfit-standalone') ?> : <span>*</span> </label>
								<div class="col-sm-9">
									<select id="character" name="postCharacter[]" class="form-control form-control-md" multiple
										<?php echo (($filterBy && $filterBy->catFilterByCharacter)? 'required' : ''); ?>>
										<option value=""><?php esc_html_e('Select Characters', 'outfit-standalone'); ?></option>
										<?php
										foreach ($characters as $c): ?>
											<option value="<?php echo $c->term_id; ?>"
												<?php echo (in_array($c->term_id, $postCharacter)? 'selected' : ''); ?>>
												<?php esc_html_e($c->name); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div><!-- /Ad Characters-->

							<div class="form-group">
								<label class="col-sm-3 text-left flip" for="description"><?php esc_html_e('Ad description', 'outfit-standalone') ?> : </label>
								<div class="col-sm-9">
									<textarea name="postContent" id="description" class="form-control" data-error="<?php esc_html_e('Write description', 'outfit-standalone') ?>"><?php echo esc_html($postContent);?></textarea>
									<div class="help-block with-errors"></div>
								</div>
							</div><!--Ad description-->

							<!--Address-->
							<div class="form-group">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Primary address', 'outfit-standalone'); ?> : <span>*</span></label>
								<div class="col-sm-9">
									<p><?php var_dump($postLocation) ?></p>
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

							<!--/Address-->

							<div class="form-group">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Your Phone/Mobile', 'outfit-standalone') ?> : <span>*</span> </label>
								<div class="col-sm-9">
									<input type="text" id="phone" name="postPhone" class="form-control form-control-md" value="<?php echo esc_html($postPhone); ?>" placeholder="<?php esc_html_e('Enter your phone number or Mobile number', 'outfit-standalone') ?>" required>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Preferred hours', 'outfit-standalone') ?> :</label>
								<div class="col-sm-9">
									<input type="text" id="preferred_hours" name="postPreferredHours" class="form-control form-control-md" value="<?php echo esc_html($postPreferredHours); ?>" placeholder="<?php esc_html_e('Enter your preferred hours to contact', 'outfit-standalone') ?>">
								</div>
							</div>

						</div><!---form-main-section post-detail-->


						<div class="row">
                            <div class="col-sm-9">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" name="postSavePrefs" id="postSavePrefs">
									<label class="form-check-label" for="postSavePrefs">
										<?php esc_html_e('Save preferences for future use', 'outfit-standalone') ?>
									</label>
								</div>
                            </div>
                        </div>
						<div class="row">
							<div class="col-sm-9">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" name="postAgreeToTerms" id="postAgreeToTerms" required>
									<label class="form-check-label" for="postAgreeToTerms">
										<?php esc_html_e('I agree to terms', 'outfit-standalone') ?>
									</label>
								</div>
							</div>
						</div>
						<div class="form-main-section">
                            <div class="col-sm-4">
								<?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
								<input type="hidden" name="submitted" id="submitted" value="true">
								<input type="hidden" name="post_ID" id="post_ID" value="<?php esc_html($postId); ?>">
                                <button class="post-submit btn btn-primary sharp btn-md btn-style-one btn-block" type="submit" name="op" value="Publish Ad"><?php esc_html_e('Publish Ad', 'outfit-standalone') ?></button>
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
	<img src="<?php echo get_template_directory_uri().'/assets/images/loader180.gif' ?>">
</div>
<?php get_footer(); ?>