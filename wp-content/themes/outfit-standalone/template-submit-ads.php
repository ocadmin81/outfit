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
$postContent = '';
$hasError ='';
$allowed ='';
global $redux_demo;
global $wpdb;

$current_user = wp_get_current_user();
$userID = $current_user->ID;
$cUserCheck = current_user_can( 'administrator' );
$role = $current_user->roles;
$currentRole = $role[0];
$imageLimit = 5;

$categories = outfit_get_main_cats(true);

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
											<option value="<?php echo $c->term_id; ?>"><?php esc_html_e($c->name); ?></option>
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

							<div class="form-group">
								<label class="col-sm-3 text-left flip" for="description"><?php esc_html_e('Ad description', 'outfit-standalone') ?> : <span>*</span></label>
								<div class="col-sm-9">
									<textarea name="postContent" id="description" class="form-control" data-error="<?php esc_html_e('Write description', 'outfit-standalone') ?>" required></textarea>
									<div class="help-block with-errors"></div>
								</div>
							</div><!--Ad description-->
							<div class="form-group">
								<label class="col-sm-3 text-left flip"><?php esc_html_e('Keywords', 'outfit-standalone') ?> : </label>
								<div class="col-sm-9">
									<div class="form-inline row">
										<div class="col-sm-12">
											<div class="input-group">
												<div class="input-group-addon"><i class="fa fa-tags"></i></div>
												<input type="text" name="post_tags" class="form-control form-control-md" placeholder="<?php esc_html_e('enter keywords for better search..!', 'outfit-standalone') ?>">
											</div>
										</div>
									</div>
									<div class="help-block"><?php esc_html_e('Keywords Example : ads, car, cat, business', 'outfit-standalone') ?></div>
								</div>
							</div><!--Ad Tags-->


							<!--ContactPhone-->
							<?php $classieraAskingPhone = $redux_demo['phoneon'];?>
							<?php if($classieraAskingPhone == 1){?>
								<div class="form-group">
									<label class="col-sm-3 text-left flip"><?php esc_html_e('Your Phone/Mobile', 'outfit-standalone') ?> :</label>
									<div class="col-sm-9">
										<div class="form-inline row">
											<div class="col-sm-12">
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-mobile-alt"></i>
													</div>
													<input type="text" name="post_phone" class="form-control form-control-md" placeholder="<?php esc_html_e('Enter your phone number or Mobile number', 'outfit-standalone') ?>">
												</div>
											</div>
										</div>
										<div class="help-block"><?php esc_html_e('Its Not required, but if you will put phone here then it will show publicly', 'outfit-standalone') ?></div>
									</div>
								</div>
							<?php } ?>
							<!--ContactPhone-->
							<?php
							$adpostCondition= $redux_demo['adpost-condition'];
							if($adpostCondition == 1){
								?>
								<div class="form-group">
									<label class="col-sm-3 text-left flip"><?php esc_html_e('Item Condition', 'outfit-standalone') ?> : <span>*</span></label>
									<div class="col-sm-9">
										<div class="radio">
											<input id="new" type="radio" name="item-condition" value="<?php esc_html_e('new', 'outfit-standalone') ?>" name="item-condition" checked>
											<label for="new"><?php esc_html_e('Brand New', 'outfit-standalone') ?></label>
											<input id="used" type="radio" name="item-condition" value="<?php esc_html_e('used', 'outfit-standalone') ?>" name="item-condition">
											<label for="used"><?php esc_html_e('Used', 'outfit-standalone') ?></label>
										</div>
									</div>
								</div><!--Item condition-->
							<?php } ?>
						</div><!---form-main-section post-detail-->
						<!--Category-->
						<div class="form-main-section classiera-post-cat">
							<div class="classiera-post-main-cat">
								<h4 class="classiera-post-inner-heading">
									<?php esc_html_e('Select a Category', 'outfit-standalone') ?> :
								</h4>
								<ul class="list-unstyled list-inline">
									<?php

									foreach ($categories as $category){
										//print_r($category);
										$tag = $category->term_id;
										$classieraCatFields = get_option(MY_CATEGORY_FIELDS);
										if (isset($classieraCatFields[$tag])){
											$classieraCatIconCode = $classieraCatFields[$tag]['category_icon_code'];
											$classieraCatIcoIMG = $classieraCatFields[$tag]['your_image_url'];
											$classieraCatIconClr = $classieraCatFields[$tag]['category_icon_color'];
										}
										if(empty($classieraCatIconClr)){
											$iconColor = $primaryColor;
										}else{
											$iconColor = $classieraCatIconClr;
										}
										$category_icon = stripslashes($classieraCatIconCode);
										?>
										<li class="match-height">
											<a href="#" id="<?php echo esc_attr( $tag ); ?>" class="border">
												<?php
												if($classieraIconsStyle == 'icon'){
													?>
													<i class="<?php echo esc_html( $category_icon ); ?>" style="color:<?php echo esc_html( $iconColor ); ?>;"></i>
													<?php
												}elseif($classieraIconsStyle == 'img'){
													?>
													<img src="<?php echo esc_url( $classieraCatIcoIMG ); ?>" alt="<?php echo esc_html(get_cat_name( $catName )); ?>">
													<?php
												}
												?>
												<span><?php echo esc_html(get_cat_name( $tag )); ?></span>
											</a>
										</li>
										<?php
									}
									?>
								</ul><!--list-unstyled-->
								<input class="classiera-main-cat-field" name="classiera-main-cat-field" type="hidden" value="">
							</div><!--classiera-post-main-cat-->
							<div class="classiera-post-sub-cat">
								<h4 class="classiera-post-inner-heading">
									<?php esc_html_e('Select a Sub Category', 'outfit-standalone') ?> :
								</h4>
								<ul class="list-unstyled classieraSubReturn">
								</ul>
								<input class="classiera-sub-cat-field" name="classiera-sub-cat-field" type="hidden" value="">
							</div><!--classiera-post-sub-cat-->
							<!--ThirdLevel-->
							<div class="classiera_third_level_cat">
								<h4 class="classiera-post-inner-heading">
									<?php esc_html_e('Select a Sub Category', 'outfit-standalone') ?> :
								</h4>
								<ul class="list-unstyled classieraSubthird">
								</ul>
								<input class="classiera_third_cat" name="classiera_third_cat" type="hidden" value="">
							</div>
							<!--ThirdLevel-->
						</div>
						<!-- /Category-->

						<!-- extra fields -->
						<div class="classieraExtraFields" style="display:none;"></div>
						<!-- extra fields -->

						<!-- post location -->
						<?php
						$classiera_ad_location_remove = $redux_demo['classiera_ad_location_remove'];
						if($classiera_ad_location_remove == 1){
						?>
						<div class="form-main-section post-location">
							<h4 class="text-uppercase border-bottom"><?php esc_html_e('Ad Location', 'outfit-standalone') ?> :</h4>
							<?php 
							$args = array(
								'post_type' => 'countries',
								'posts_per_page'   => -1,
								'orderby'          => 'title',
								'order'            => 'ASC',
								'post_status'      => 'publish',
								'suppress_filters' => false 
							);
							$country_posts = get_posts($args);
							if(!empty($country_posts)){
							?>
							<!--Select Country-->
							<div class="form-group">
                                <label class="col-sm-3 text-left flip"><?php esc_html_e('Select Country', 'outfit-standalone') ?>: <span>*</span></label>
                                <div class="col-sm-6">
                                    <div class="inner-addon right-addon">
                                        <i class="form-icon right-form-icon fa fa-angle-down"></i>
                                        <select name="post_location" id="post_location" class="form-control form-control-md">
                                            <option value="-1" selected disabled><?php esc_html_e('Select Country', 'outfit-standalone'); ?></option>
                                            <?php 
											foreach( $country_posts as $country_post ){
												?>
												<option value="<?php echo esc_attr( $country_post->ID ); ?>">
													<?php echo esc_html( $country_post->post_title ); ?>
												</option>
												<?php
											}
											?>
                                        </select>
                                    </div>
                                </div>
                            </div>
							<?php } ?>
							<!--Select Country-->	
							<!--Select States-->
							<?php 
							$locationsStateOn = $redux_demo['location_states_on'];
							if($locationsStateOn == 1){
							?>
							<div class="form-group">
                                <label class="col-sm-3 text-left flip"><?php esc_html_e('Select State', 'outfit-standalone') ?>: <span>*</span></label>
                                <div class="col-sm-6">
                                    <div class="inner-addon right-addon">
                                        <i class="form-icon right-form-icon fa fa-angle-down"></i>
										<select name="post_state" id="post_state" class="selectState form-control form-control-md" required>
											<option value=""><?php esc_html_e('Select State', 'outfit-standalone'); ?></option>
										</select>
                                    </div>
                                </div>
                            </div>
							<?php } ?>
							<!--Select States-->
							<!--Select City-->
							<?php 
							$locationsCityOn= $redux_demo['location_city_on'];
							if($locationsCityOn == 1){
							?>
							<div class="form-group">
                                <label class="col-sm-3 text-left flip"><?php esc_html_e('Select City', 'outfit-standalone'); ?>: <span>*</span></label>
                                <div class="col-sm-6">
                                    <div class="inner-addon right-addon">
                                        <i class="form-icon right-form-icon fa fa-angle-down"></i>
										<select name="post_city" id="post_city" class="selectCity form-control form-control-md" required>
											<option value=""><?php esc_html_e('Select City', 'outfit-standalone'); ?></option>
										</select>
                                    </div>
                                </div>
                            </div>
							<?php } ?>
							<!--Select City-->
							<!--Address-->
							<?php if($classieraAddress == 1){?>
							<div class="form-group">
                                <label class="col-sm-3 text-left flip"><?php esc_html_e('Address', 'outfit-standalone'); ?> : <span>*</span></label>
                                <div class="col-sm-9">
                                    <input id="address" type="text" name="address" class="form-control form-control-md" placeholder="<?php esc_html_e('Address or City', 'outfit-standalone') ?>" required>
                                </div>
                            </div>
							<?php } ?>
							<!--Address-->
							<!--Google Value-->
							<div class="form-group">
								<?php 
									$googleFieldsOn = $redux_demo['google-lat-long']; 
									if($googleFieldsOn == 1){
								?>
                                <label class="col-sm-3 text-left flip"><?php esc_html_e('Set Latitude & Longitude', 'outfit-standalone') ?> : <span>*</span></label>
									<?php } ?>
                                <div class="col-sm-9">
								<?php 
									$googleFieldsOn = $redux_demo['google-lat-long']; 
									if($googleFieldsOn == 1){
								?>
                                    <div class="form-inline row">
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fas fa-map-marker-alt"></i></div>
                                                <input type="text" name="latitude" id="latitude" class="form-control form-control-md" placeholder="<?php esc_html_e('Latitude', 'outfit-standalone') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fas fa-map-marker-alt"></i></div>
                                                <input type="text" name="longitude" id="longitude" class="form-control form-control-md" placeholder="<?php esc_html_e('Longitude', 'outfit-standalone') ?>" required>
                                            </div>
                                        </div>
                                    </div>
									<?php }else{ ?>
										<input type="hidden" id="latitude" name="latitude">
										<input type="hidden" id="longitude" name="longitude">
									<?php } ?>
									<?php 
								$googleMapadPost = $redux_demo['google-map-adpost']; 
								if($googleMapadPost == 1){
								?>
                                    <div id="post-map" class="submitMAp">
                                        <div id="map-canvas"></div>
										<script type="text/javascript">
										jQuery(document).ready(function($) {
										var geocoder;
										var map;
										var marker;
										var geocoder = new google.maps.Geocoder();
										function geocodePosition(pos) {
											geocoder.geocode({
											latLng: pos
										}, function(responses) {
									    if (responses && responses.length > 0) {
									      updateMarkerAddress(responses[0].formatted_address);
									    } else {
									      updateMarkerAddress('Cannot determine address at this location.');
									    }

									  });

									}

									function updateMarkerPosition(latLng) {
									  jQuery('#latitude').val(latLng.lat());
									  jQuery('#longitude').val(latLng.lng());
									}



									function updateMarkerAddress(str) {
									  jQuery('#address').val(str);
									}



									function initialize() {
									  var latlng = new google.maps.LatLng(0, 0);
									  var mapOptions = {
									    zoom: 2,
									    center: latlng,
										draggable: true
									  }

									  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
									  geocoder = new google.maps.Geocoder();
									  marker = new google.maps.Marker({
									  	position: latlng,
									    map: map,
									    draggable: true
									  });
									  // Add dragging event listeners.
									  google.maps.event.addListener(marker, 'dragstart', function() {
									    updateMarkerAddress('Dragging...');
									  });									  

									  google.maps.event.addListener(marker, 'drag', function() {
									    updateMarkerPosition(marker.getPosition());
									  });									  

									  google.maps.event.addListener(marker, 'dragend', function() {
									    geocodePosition(marker.getPosition());
									  });
									}



									google.maps.event.addDomListener(window, 'load', initialize);
									jQuery(document).ready(function() {							         

									  initialize();									          

									  jQuery(function() {
									    jQuery("#address").autocomplete({
									      //This bit uses the geocoder to fetch address values
									      source: function(request, response) {
									        geocoder.geocode( {'address': request.term }, function(results, status) {
									          response(jQuery.map(results, function(item) {
									            return {
									              label:  item.formatted_address,
									              value: item.formatted_address,
									              latitude: item.geometry.location.lat(),
									              longitude: item.geometry.location.lng()
									            }

									          }));

									        })

									      },

									      //This bit is executed upon selection of an address

									      select: function(event, ui) {
									        jQuery("#latitude").val(ui.item.latitude);
									        jQuery("#longitude").val(ui.item.longitude);
									        var location = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
									        marker.setPosition(location);
									        map.setZoom(16);
									        map.setCenter(location);
									      }

									    });

									  });

									  

									  //Add listener to marker for reverse geocoding
									  google.maps.event.addListener(marker, 'drag', function() {
									    geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
									      if (status == google.maps.GeocoderStatus.OK) {
									        if (results[0]) {
									          jQuery('#address').val(results[0].formatted_address);
									          jQuery('#latitude').val(marker.getPosition().lat());
									          jQuery('#longitude').val(marker.getPosition().lng());
									        }

									      }
									    });
									  });

									});
								});
										</script>
                                    </div>
								<?php } ?>
                                </div>
                            </div>
							<!--Google Value-->
						</div>
						<?php } ?>
						<!-- post location -->
						<!-- seller information without login-->
						<?php if( !is_user_logged_in()){?>
						<div class="form-main-section seller">
                            <h4 class="text-uppercase border-bottom"><?php esc_html_e('Seller Information', 'outfit-standalone') ?> :</h4>
                            <div class="form-group">
                                <label class="col-sm-3 text-left flip"><?php esc_html_e('Your Are', 'outfit-standalone') ?> : <span>*</span></label>
                                <div class="col-sm-9">
                                    <div class="radio">
                                        <input id="individual" type="radio" name="seller" checked>
                                        <label for="individual"><?php esc_html_e('Individual', 'outfit-standalone') ?></label>
                                        <input id="dealer" type="radio" name="seller">
                                        <label for="dealer"><?php esc_html_e('Dealer', 'outfit-standalone') ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 text-left flip"><?php esc_html_e('Your Name', 'outfit-standalone') ?>: <span>*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" name="user_name" class="form-control form-control-md" placeholder="<?php esc_html_e('Enter Your Name', 'outfit-standalone') ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 text-left flip"><?php esc_html_e('Your Email', 'outfit-standalone') ?> : <span>*</span></label>
                                <div class="col-sm-6">
                                    <input type="email" name="user_email" class="form-control form-control-md" placeholder="<?php esc_html_e('Enter your email', 'outfit-standalone') ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 text-left flip"><?php esc_html_e('Your Phone or Mobile No', 'outfit-standalone') ?> :<span>*</span></label>
                                <div class="col-sm-6">
                                    <input type="tel" name="user_phone" class="form-control form-control-md" placeholder="<?php esc_html_e('Enter your Mobile or Phone number', 'outfit-standalone') ?>">
                                </div>
                            </div>
                        </div>
						<?php }?>
						<!-- seller information without login -->
						<!--Select Ads Type-->
						<?php 
						$totalAds = '';
						$usedAds = '';
						$availableADS = '';
						$planCount = 0;						
						$regular_ads = $redux_demo['regular-ads'];
						$classieraRegularAdsDays = $redux_demo['ad_expiry'];
						$current_user = wp_get_current_user();
						$userID = $current_user->ID;
						$result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}classiera_plans WHERE user_id = $userID ORDER BY id DESC" );
						?>
						<div class="form-main-section post-type">
                            <h4 class="text-uppercase border-bottom"><?php esc_html_e('Select Ad Post Type', 'outfit-standalone') ?> :</h4>
                            <p class="help-block"><?php esc_html_e('Select an Option to make your ad featured or regular', 'outfit-standalone') ?></p>
                            <div class="form-group">
							<!--Regular Ad with plans-->
							<?php							
							if($postLimitOn == true && $countPosts >= $regularCount && $currentRole != "administrator"){
								if(!empty($result)){
									$count = 0;
									foreach( $result as $info ){
										$totalRegularAds = $info->regular_ads;
										$usedRegularAds = $info->regular_used;
										$regularID = $info->id;
										$availableRegularADS = $totalRegularAds-$usedRegularAds;
										$planName = $info->plan_name;
										if($availableRegularADS != 0){
											?>
											<div class="col-sm-4 col-md-3 col-lg-3">
												<div class="post-type-box">
													<h3 class="text-uppercase">
														<?php esc_html_e('Regular with ', 'outfit-standalone') ?>
														<?php echo esc_html($planName); ?>
													</h3>
													<p>
													<?php esc_html_e('Available Regular ads ', 'outfit-standalone') ?> :
													<?php echo esc_attr($availableRegularADS); ?>
													</p>
													<p>
													<?php esc_html_e('Used Regular ads', 'outfit-standalone') ?> : 
													<?php echo esc_attr($usedRegularAds); ?>
													</p>
													<div class="radio">
														<input id="regularPlan<?php echo esc_attr($regularID); ?>" class="classieraGetID" type="radio" name="classiera_post_type" value="classiera_regular_with_plan" data-regular-id="<?php echo esc_attr($info->id); ?>">
														<label for="regularPlan<?php echo esc_attr($regularID); ?>">
															<?php esc_html_e('Select', 'outfit-standalone') ?>
														</label>
													</div><!--radio-->
												</div><!--post-type-box-->
											</div><!--col-sm-4-->
											<?php
										}
									}
								}
							}else{
								if($regular_ads == 1){
								?>
								<!--Regular Ad-->
								<div class="col-sm-4 col-md-3 col-lg-3 active-post-type">
                                    <div class="post-type-box">
                                        <h3 class="text-uppercase"><?php esc_html_e('Regular', 'outfit-standalone') ?></h3>
                                        <p><?php esc_html_e('For', 'outfit-standalone') ?>&nbsp;<?php echo esc_attr($classieraRegularAdsDays); ?>&nbsp;<?php esc_html_e('days', 'outfit-standalone') ?></p>
                                        <div class="radio">
                                            <input id="regular" type="radio" name="classiera_post_type" value="classiera_regular" checked>
                                            <label for="regular"><?php esc_html_e('Select', 'outfit-standalone') ?></label>
                                        </div>
										<input type="hidden" name="regular-ads-enable" value=""  >
                                    </div>
                                </div>
								<!--Regular Ad-->
								<?php
								}
							}
							?>
							<!--Regular Ad with plans-->
							<?php
								if(!empty($result)){
									foreach ( $result as $info ) {
										//print_r($info);
										$premiumID = $info->id;
										$name = $info->plan_name;
										$totalAds = $info->ads;
										$usedAds = $info->used;
										if($totalAds == 'unlimited'){
											$name = esc_html__( 'Unlimited for Admin Only', 'outfit-standalone' );
											$availableADS = 'unlimited';
										}else{
											$availableADS = $totalAds-$usedAds;
										}
										
										if($availableADS != 0 || $totalAds == 'unlimited'){
										?>
											<div class="col-sm-4 col-md-3 col-lg-3">
												<div class="post-type-box">
													<h3 class="text-uppercase">						
														<?php echo esc_html($name); ?>
													</h3>
													<p><?php esc_html_e('Total Ads Available', 'outfit-standalone') ?> : <?php echo esc_attr($availableADS); ?></p>
													<p><?php esc_html_e('Used Ads with this Plan', 'outfit-standalone') ?> : <?php echo esc_attr($usedAds); ?></p>
													<div class="radio">
														<input id="featured<?php echo esc_attr($premiumID); ?>" type="radio" name="classiera_post_type" value="<?php echo esc_attr($info->id); ?>">
														<label for="featured<?php echo esc_attr($premiumID); ?>">
															<?php esc_html_e('Select', 'outfit-standalone') ?>
														</label>
													</div>
												</div>
											</div>
										<?php
										}										
									}
								}
							?>	
								<!--Pay Per Post Per Category Base-->
								<div class="col-sm-4 col-md-3 col-lg-3 classieraPayPerPost">
									<div class="post-type-box">
										<h3 class="text-uppercase">
											<?php esc_html_e('Featured Ad', 'outfit-standalone') ?>
										</h3>	
										<p class="classieraPPP"></p>
										<div class="radio">
											<input id="payperpost" type="radio" name="classiera_post_type" value="payperpost">
											<label for="payperpost">
											<?php esc_html_e('select', 'outfit-standalone') ?>
											</label>
										</div>										
									</div>
								</div>
								<!--Pay Per Post Per Category Base-->
                            </div>
                        </div>
						<!--Select Ads Type-->
						<?php 
						$featured_plans = $redux_demo['featured_plans'];
						if(!empty($featured_plans)){
							if($availableADS == "0" || empty($result)){
						?>
						<div class="row">
                            <div class="col-sm-9">
                                <div class="help-block terms-use">
                                    <?php esc_html_e('Currently you have no active plan for featured ads. You must purchase a', 'outfit-standalone') ?> <strong><a href="<?php echo esc_url($featured_plans); ?>" target="_blank"><?php esc_html_e('Featured Pricing Plan', 'outfit-standalone') ?></a></strong> <?php esc_html_e('to be able to publish a Featured Ad.', 'outfit-standalone') ?>
                                </div>
                            </div>
                        </div>
						<?php }} ?>
						<div class="row">
                            <div class="col-sm-9">
                                <div class="help-block terms-use">
                                    <?php esc_html_e('By clicking "Publish Ad", you agree to our', 'outfit-standalone') ?> <a href="<?php echo esc_url($termsandcondition); ?>" target="_blank"><?php esc_html_e('Terms of Use', 'outfit-standalone') ?></a> <?php esc_html_e('and acknowledge that you are the rightful owner of this item', 'outfit-standalone') ?>
                                </div>
                            </div>
                        </div>
						<div class="form-main-section">
                            <div class="col-sm-4">
								<input type="hidden" class="regular_plan_id" name="regular_plan_id" value="">
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
	<img src="<?php echo get_template_directory_uri().'/images/loader180.gif' ?>">
</div>
<?php get_footer(); ?>