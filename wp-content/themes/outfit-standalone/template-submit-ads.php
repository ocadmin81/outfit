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
	global $redux_demo; 
	$login = $redux_demo['login'];
	wp_redirect( $login ); exit;
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

$current_user = wp_get_current_user();
$userID = $current_user->ID;
$cUserCheck = current_user_can( 'administrator' );
$role = $current_user->roles;
$currentRole = $role[0];
$imageLimit = 5;

$categories = outfit_get_main_cats(true);
$ageGroups = outfit_get_list_of_age_groups();
$colors = outfit_get_list_of_colors();
$brands = array();
$conditions = outfit_get_list_of_conditions();

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
			$outfitSubCat = $_POST['postSubcategory'];
			$outfitSubSubCat = $_POST['postSubsubcategory'];
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
				'post_content' => strip_tags($_POST['postContent'], '<h1><h2><h3><strong><b><ul><ol><li><i><a><blockquote><center><embed><iframe><pre><table><tbody><tr><td><video><br>'),
				'post-type' => OUTFIT_AD_POST_TYPE,
				'post_category' => array($outfitMainCat, $outfitSubCat, $outfitSubSubCat),
				'comment_status' => 'open',
				'ping_status' => 'open',
				'post_status' => $postStatus
			);

			$postId = wp_insert_post($post_information);

			$postPrice = trim($_POST['postPrice']);
			$postPhone = trim($_POST['postPhone']);
			$postAddress = trim($_POST['postAddress']);
			$googleLat = $_POST['latitude'];
			$googleLong = $_POST['longitude'];
		}
		//If Its posting featured image//
		/*if ( isset($_FILES['upload_attachment']) ) {
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
						$attachment_id = outfit_insert_attachment($file, $post_id);
						if($count == $featuredimg){
							set_post_thumbnail( $post_id, $attachment_id );
						}
						$count++;
					}

				}
			}
		}*/
	}
}


if(isset($_POST['postTitle'])){
	if(trim($_POST['postTitle']) != '' && $_POST['outfit-main-cat-field'] != ''){
		if(isset($_POST['submitted']) && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {
			
			if(empty($_POST['postTitle'])){
				$postTitleError =  esc_html__( 'Please enter a title.', 'outfit-standalone' );
				$hasError = true;
			}else{
				$postTitle = trim($_POST['postTitle']);
			}
			if(empty($_POST['classiera-main-cat-field'])){
				$catError = esc_html__( 'Please select a category', 'outfit-standalone' );
				$hasError = true;
			}
			
			//Image Count check//
			$userIMGCount = $_POST['image-count'];
			$files = $_FILES['upload_attachment'];
			$count = $files['name'];
			$filenumber = count($count);			
			if($filenumber > $userIMGCount){
				$imageError = esc_html__( 'You selected Images Count is exceeded', 'outfit-standalone' );
				$hasError = true;
			}
			//Image Count check//

			if($hasError != true && !empty($_POST['classiera_post_type']) || isset($_POST['regular-ads-enable'])) {
				$classieraPostType = $_POST['classiera_post_type'];
				//Set Post Status//
				if(is_super_admin() ){
					$postStatus = 'publish';
				}elseif(!is_super_admin()){
					if($redux_demo['post-options-on'] == 1){
						$postStatus = 'private';
					}else{
						$postStatus = 'publish';
					}
					if($classieraPostType == 'payperpost'){
						$postStatus = 'pending';
					}
				}
				//Set Post Status//
				//Check Category//
				$classieraMainCat = $_POST['classiera-main-cat-field'];
				$classieraChildCat = $_POST['classiera-sub-cat-field'];
				$classieraThirdCat = $_POST['classiera_third_cat'];
				if(empty($classieraThirdCat)){
					$classieraCategory = $classieraChildCat;
				}else{
					$classieraCategory = $classieraThirdCat;
				}
				if(empty($classieraCategory)){
					$classieraCategory = $classieraMainCat;
				}
				//Check Category//
				//Setup Post Data//
				$post_information = array(
					'post_title' => esc_attr(strip_tags($_POST['postTitle'])),			
					'post_content' => strip_tags($_POST['postContent'], '<h1><h2><h3><strong><b><ul><ol><li><i><a><blockquote><center><embed><iframe><pre><table><tbody><tr><td><video><br>'),
					'post-type' => 'post',
					'post_category' => array($classieraMainCat, $classieraChildCat, $classieraThirdCat),
					'tags_input'    => explode(',', $_POST['post_tags']),
					'tax_input' => array(
					'location' => $_POST['post_location'],
					),
					'comment_status' => 'open',
					'ping_status' => 'open',
					'post_status' => $postStatus
				);

				$post_id = wp_insert_post($post_information);
				
				//Setup Price//
				$postMultiTag = $_POST['post_currency_tag'];
				$post_price = trim($_POST['post_price']);
				$post_old_price = trim($_POST['post_old_price']);
				
				/*Check If Latitude is OFF */
				$googleLat = $_POST['latitude'];
				if(empty($googleLat)){
					$latitude = $classieraLatitude;
				}else{
					$latitude = $googleLat;
				}
				/*Check If longitude is OFF */
				$googleLong = $_POST['longitude'];
				if(empty($googleLong)){
					$longitude = $classieraLongitude;
				}else{
					$longitude = $googleLong;
				}
				
				$featuredIMG = $_POST['classiera_featured_img'];
				$itemCondition = $_POST['item-condition'];		
				$catID = $classieraCategory.'custom_field';		
				$custom_fields = $_POST[$catID];
				/*If We are using CSC Plugin*/
				
				/*Get Country Name*/
				if(isset($_POST['post_location'])){
					$postLo = $_POST['post_location'];
					$allCountry = get_posts( array( 'include' => $postLo, 'post_type' => 'countries', 'posts_per_page' => -1, 'suppress_filters' => 0, 'orderby'=>'post__in' ) );
					foreach( $allCountry as $country_post ){
						$postCounty = $country_post->post_title;
					}
				}				
				$poststate = $_POST['post_state'];
				$postCity = $_POST['post_city'];
				
				/*If We are using CSC Plugin*/
				if(isset($_POST['post_category_type'])){
					update_post_meta($post_id, 'post_category_type', esc_attr( $_POST['post_category_type'] ) );
				}	
				if(isset($_POST['classiera_sub_fields'])){
					$classiera_sub_fields = $_POST['classiera_sub_fields'];
					update_post_meta($post_id, 'classiera_sub_fields', $classiera_sub_fields);
				}
				if(isset($_POST['classiera_CF_Front_end'])){
					$classiera_CF_Front_end = $_POST['classiera_CF_Front_end'];
					update_post_meta($post_id, 'classiera_CF_Front_end', $classiera_CF_Front_end);
				}
				update_post_meta($post_id, 'custom_field', $custom_fields);
				
				update_post_meta($post_id, 'post_currency_tag', $postMultiTag, $allowed);
				update_post_meta($post_id, 'post_price', $post_price, $allowed);
				update_post_meta($post_id, 'post_old_price', $post_old_price, $allowed);
				
				update_post_meta($post_id, 'post_perent_cat', $classieraMainCat, $allowed);
				update_post_meta($post_id, 'post_child_cat', $classieraChildCat, $allowed);				
				update_post_meta($post_id, 'post_inner_cat', $classieraThirdCat, $allowed);
				
				if(isset($_POST['post_phone'])){
					update_post_meta($post_id, 'post_phone', $_POST['post_phone'], $allowed);
				}
				update_post_meta($post_id, 'classiera_ads_type', $_POST['classiera_ads_type'], $allowed);
				if(isset($_POST['seller'])){
					update_post_meta($post_id, 'seller', $_POST['seller'], $allowed);
				}

				update_post_meta($post_id, 'post_location', wp_kses($postCounty, $allowed));
				
				update_post_meta($post_id, 'post_state', wp_kses($poststate, $allowed));
				update_post_meta($post_id, 'post_city', wp_kses($postCity, $allowed));

				update_post_meta($post_id, 'post_latitude', wp_kses($latitude, $allowed));

				update_post_meta($post_id, 'post_longitude', wp_kses($longitude, $allowed));

				update_post_meta($post_id, 'post_address', wp_kses($_POST['address'], $allowed));
				if(isset($_POST['video'])){
					update_post_meta($post_id, 'post_video', $_POST['video'], $allowed);
				}
				update_post_meta($post_id, 'featured_img', $featuredIMG, $allowed);
				
				if(isset($_POST['item-condition'])){
					update_post_meta($post_id, 'item-condition', $itemCondition, $allowed);
				}
				update_post_meta($post_id, 'classiera_post_type', $_POST['classiera_post_type'], $allowed);
				
				if(isset($_POST['pay_per_post_product_id'])){
					update_post_meta($post_id, 'pay_per_post_product_id', $_POST['pay_per_post_product_id'], $allowed);
				}
				if(isset($_POST['days_to_expire'])){
					update_post_meta($post_id, 'days_to_expire', $_POST['days_to_expire'], $allowed);
				}
				if($classieraPostType == 'payperpost'){
					$permalink = $classieraProfileURL;
				}else{
					$permalink = get_permalink( $post_id );
				}
				//If Its posting featured image//
				if(trim($_POST['classiera_post_type']) != 'classiera_regular'){
					if($_POST['classiera_post_type'] == 'payperpost'){
						//Do Nothing on Pay Per Post//
					}elseif($_POST['classiera_post_type'] == 'classiera_regular_with_plan'){
						//Regular Ads Posting with Plans//
						$classieraPlanID = trim($_POST['regular_plan_id']);
						global $wpdb;
						$current_user = wp_get_current_user();
						$userID = $current_user->ID;
						$result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}classiera_plans WHERE id = $classieraPlanID" );
						if($result){
							$tablename = $wpdb->prefix . 'classiera_plans';
							foreach ( $result as $info ){
								$newRegularUsed = $info->regular_used +1;
								$update_data = array('regular_used' => $newRegularUsed);
								$where = array('id' => $classieraPlanID);
								$update_format = array('%s');
								$wpdb->update($tablename, $update_data, $where, $update_format);
							}
						}
					}else{
						//Featured Post with Plan Ads//
						$featurePlanID = trim($_POST['classiera_post_type']);
						global $wpdb;
						$current_user = wp_get_current_user();
						$userID = $current_user->ID;
						$result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}classiera_plans WHERE id = $featurePlanID" );
						if ($result){
							$featuredADS = 0;
							$tablename = $wpdb->prefix . 'classiera_plans';
							foreach ( $result as $info ){
								$totalAds = $info->ads;
								if (is_numeric($totalAds)){
									$totalAds = $info->ads;
									$usedAds = $info->used;
									$infoDays = $info->days;
								}								
								if($totalAds == 'unlimited'){
									$availableADS = 'unlimited';
								}else{
									$availableADS = $totalAds-$usedAds;
								}								
								if($usedAds < $totalAds && $availableADS != "0" || $totalAds == 'unlimited'){
									global $wpdb;
									$newUsed = $info->used +1;
									$update_data = array('used' => $newUsed);
									$where = array('id' => $featurePlanID);
									$update_format = array('%s');
									$wpdb->update($tablename, $update_data, $where, $update_format);
									update_post_meta($post_id, 'post_price_plan_id', $featurePlanID );

									$dateActivation = date('m/d/Y H:i:s');
									update_post_meta($post_id, 'post_price_plan_activation_date', $dateActivation );		
									
									$daysToExpire = $infoDays;
									$dateExpiration_Normal = date("m/d/Y H:i:s", strtotime("+ ".$daysToExpire." days"));
									update_post_meta($post_id, 'post_price_plan_expiration_date_normal', $dateExpiration_Normal );



									$dateExpiration = strtotime(date("m/d/Y H:i:s", strtotime("+ ".$daysToExpire." days")));
									update_post_meta($post_id, 'post_price_plan_expiration_date', $dateExpiration );
									update_post_meta($post_id, 'featured_post', "1" );
								}
							}
						}
					}
				}
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
								$featuredimg = $_POST['classiera_featured_img'];
								if($count == $featuredimg){
									$attachment_id = classiera_insert_attachment($file,$post_id);
									set_post_thumbnail( $post_id, $attachment_id );
								}else{
									$attachment_id = classiera_insert_attachment($file,$post_id);
								}								
								$count++;
							}
							
						}						
					}/*Foreach*/
				}					
				wp_redirect($permalink); exit();
			}
		}
	}else{
		if(trim($_POST['postTitle']) === '') {
			$postTitleError = esc_html__( 'Please enter a title.', 'outfit-standalone' );	
			$hasError = true;
		}
		if($_POST['classiera-main-cat-field'] === '-1') {
			$catError = esc_html__( 'Please select a category.', 'outfit-standalone' );
			$hasError = true;
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
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Photos for your ad', 'outfit-standalone') ?> :</label>
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
										<!--Imageloop-->
										<?php
										for ($i = 0; $i < $imageLimit; $i++){
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
										<input type="hidden" name="classiera_featured_img" id="classiera_featured_img" value="0">
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
									<input id="title" data-minlength="5" name="postTitle" type="text" class="form-control form-control-md" placeholder="<?php esc_html_e('Ad Title Goes here', 'outfit-standalone') ?>" required>

								</div>
							</div><!-- /Ad title-->

							<div class="form-group">
								<label class="col-sm-3 text-left flip" for="title"><?php esc_html_e('Price', 'outfit-standalone') ?> : <span>*</span></label>
								<div class="col-sm-9">
									<input id="price" data-minlength="1" name="postPrice" type="text" class="form-control form-control-md" placeholder="<?php esc_html_e('Price', 'outfit-standalone') ?>" required>

								</div>
							</div><!-- /Ad price-->

							<div class="form-group post-cat-container">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Category', 'outfit-standalone') ?> : </label>
								<div class="col-sm-9">
									<select id="category" name="postCategory" class="form-control form-control-md category-select">
										<option value="" selected><?php esc_html_e('Select Category', 'outfit-standalone'); ?></option>
										<?php
										foreach ($categories as $c): ?>
											<?php $c = fetch_category_custom_fields($c); ?>
											<option value="<?php echo $c->term_id; ?>"
													data-color-enabled="<?php echo ($c->catFilterByColor? '1' : '0'); ?>"
													data-brand-enabled="<?php echo ($c->catFilterByBrand? '1' : '0'); ?>"
													data-age-enabled="<?php echo ($c->catFilterByAge? '1' : '0'); ?>"
													data-condition-enabled="<?php echo ($c->catFilterByCondition? '1' : '0'); ?>"
												><?php esc_html_e($c->name); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div><!-- /Ad Category-->

							<div class="form-group post-sub-cat-container" style="display: none;">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Sub Category', 'outfit-standalone') ?> : </label>
								<div class="col-sm-9">
									<select id="subcategory" name="postSubcategory" class="form-control form-control-md">
										<option value=""><?php esc_html_e('Select Sub Category', 'outfit-standalone'); ?></option>

									</select>
								</div>
							</div><!-- /Ad Sub Category-->

							<div class="form-group post-colors-container" style="display: none;">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Color', 'outfit-standalone') ?> : </label>
								<div class="col-sm-9">
									<select id="color" name="postColor" class="form-control form-control-md" multiple>
										<option value=""><?php esc_html_e('Select Colors', 'outfit-standalone'); ?></option>
										<?php
										foreach ($colors as $c): ?>
											<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div><!-- /Ad Colors-->

							<div class="form-group post-age-groups-container" style="display: none;">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Age Group', 'outfit-standalone') ?> : </label>
								<div class="col-sm-9">
									<select id="ageGroup" name="postAgeGroup" class="form-control form-control-md" multiple>
										<option value=""><?php esc_html_e('Select Age Groups', 'outfit-standalone'); ?></option>
										<?php
										foreach ($ageGroups as $c): ?>
											<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div><!-- /Ad Age Groups-->

							<div class="form-group post-brands-container" style="display: none;">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Brand', 'outfit-standalone') ?> : </label>
								<div class="col-sm-9">
									<select id="brand" name="postBrand" class="form-control form-control-md" multiple>
										<option value=""><?php esc_html_e('Select Brands', 'outfit-standalone'); ?></option>
										<?php
										foreach ($brands as $c): ?>
											<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div><!-- /Ad Brands-->

							<div class="form-group post-conditions-container" style="display: none;">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Condition', 'outfit-standalone') ?> : </label>
								<div class="col-sm-9">
									<select id="condition" name="postCondition" class="form-control form-control-md">
										<option value=""><?php esc_html_e('Select Condition', 'outfit-standalone'); ?></option>
										<?php
										foreach ($conditions as $c): ?>
											<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div><!-- /Ad Conditions-->

							<div class="form-group">
								<label class="col-sm-3 text-left flip" for="description"><?php esc_html_e('Ad description', 'outfit-standalone') ?> : <span>*</span></label>
								<div class="col-sm-9">
									<textarea name="postContent" id="description" class="form-control" data-error="<?php esc_html_e('Write description', 'outfit-standalone') ?>" required></textarea>
									<div class="help-block with-errors"></div>
								</div>
							</div><!--Ad description-->

							<div class="form-group">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Your Phone/Mobile', 'outfit-standalone') ?> :</label>
								<div class="col-sm-9">
									<input type="text" id="phone" name="postPhone" class="form-control form-control-md" placeholder="<?php esc_html_e('Enter your phone number or Mobile number', 'outfit-standalone') ?>">
								</div>
							</div>


							<!--Address-->
							<div class="form-group">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Primary address', 'classiera'); ?> : <span>*</span></label>
								<div class="col-sm-9">
									<input class="address" id="address" type="text" name="address" class="form-control form-control-md" placeholder="<?php esc_html_e('Address or City', 'classiera') ?>" required>
									<input class="latitude" type="hidden" id="latitude" name="latitude">
									<input class="longitude" type="hidden" id="longitude" name="longitude">
									<input class="locality" type="hidden" id="locality" name="locality">
									<input class="aal3" type="hidden" id="aal3" name="aal3">
									<input class="aal2" type="hidden" id="aal2" name="aal2">
									<input class="aal1" type="hidden" id="aal1" name="aal1">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Secondary address', 'classiera'); ?> : <span>*</span></label>
								<div class="col-sm-9">
									<input class="address" id="address_2" type="text" name="address_2" class="form-control form-control-md" placeholder="<?php esc_html_e('Address or City', 'classiera') ?>" required>
									<input class="latitude" type="hidden" id="latitude_2" name="latitude_2">
									<input class="longitude" type="hidden" id="longitude_2" name="longitude_2">
									<input class="locality" type="hidden" id="locality_2" name="locality_2">
									<input class="aal3" type="hidden" id="aal3_2" name="aal3_2">
									<input class="aal2" type="hidden" id="aal2_2" name="aal2_2">
									<input class="aal1" type="hidden" id="aal1_2" name="aal1_2">
								</div>
							</div>

							<!--/Address-->

						</div><!---form-main-section post-detail-->



						<div class="row">
                            <div class="col-sm-9">
                                <div class="help-block terms-use">
                                    <?php esc_html_e('By clicking "Publish Ad", you agree to our', 'outfit-standalone') ?> <a href="<?php echo esc_url($termsandcondition); ?>" target="_blank"><?php esc_html_e('Terms of Use', 'outfit-standalone') ?></a> <?php esc_html_e('and acknowledge that you are the rightful owner of this item', 'outfit-standalone') ?>
                                </div>
                            </div>
                        </div>
						<div class="form-main-section">
                            <div class="col-sm-4">
								<?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
								<input type="hidden" name="submitted" id="submitted" value="true">
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