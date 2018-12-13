<?php

global $redux_demo;
global $catId, $outfitMainCat, $subCategories;
$filterBy = fetch_category_custom_fields(get_category($outfitMainCat));

/* filters */
$ageGroups = outfit_get_list_of_age_groups();
$colors = outfit_get_list_of_colors();

$conditions = outfit_get_list_of_conditions();
$writers = outfit_get_list_of_writers();
$characters = outfit_get_list_of_characters();
$brands = getBrandsByCategory($outfitMainCat);
$genders = outfit_get_list_of_genders();
$languages = outfit_get_list_of_languages();


global $currentUserId;
$searchPrefAge = '';
$searchPrefLocation = null;

if ($currentUserId) {
	$searchPrefAge = get_user_meta($currentUserId, USER_META_SEARCH_PREF_AGE, true);
	$searchPrefLocation = OutfitLocation::createFromJSON(get_user_meta($currentUserId, USER_META_SEARCH_PREF_LOCATION, true));
}

$postColor = getGetMultiple('postColor', true);
$postAgeGroup = getGetMultiple('postAgeGroup', true);
$postBrand = getGetMultiple('postBrand', true);
$postCondition = getGetMultiple('postCondition', true);
$postWriter = getGetMultiple('postWriter', true);
$postCharacter = getGetMultiple('postCharacter', true);
$postGender = getGetMultiple('postGender', true);
$postLanguage = getGetMultiple('postLanguage', true);
$postKeyword = getGetInput('s', '');
$postLocation = null;
$postPrice = getGetMultiple('priceRange', []);

if (!isset($_GET['postAgeGroup'])) {
	$postAgeGroup = array($searchPrefAge);
}

// post primary location
if (!isset($_GET['address'])) {
	if (null != $searchPrefLocation) {
		$postAddress = $searchPrefLocation->getAddress();
		$postLatitude = $searchPrefLocation->getLatitude();
		$postLongitude = $searchPrefLocation->getLongitude();
		$postLocality = $searchPrefLocation->getLocality();
		$postArea1 = $searchPrefLocation->getAal1();
		$postArea2 = $searchPrefLocation->getAal2();
		$postArea3 = $searchPrefLocation->getAal3();
	}
}
else {
	$postAddress = trim(getGetInput('address'));
	$postLatitude = getGetInput('latitude');
	$postLongitude = getGetInput('longitude');
	$postLocality = getGetInput('locality');
	$postArea1 = getGetInput('aal1');
	$postArea2 = getGetInput('aal2');
	$postArea3 = getGetInput('aal3');

	$postLocation = new OutfitLocation($postAddress, $postLongitude, $postLatitude, [
		'locality' => $postLocality,
		'aal3' => $postArea3,
		'aal2' => $postArea2,
		'aal1' => $postArea1
	]);
}


//var_dump($_GET['address']);
//var_dump(get_query_var('address'));
?>
<!--SearchForm-->
<form method="get" action="<?php echo home_url(); ?>">
	<div class="search-form">
		<div class="search-form-main-heading" style="display:none;">
			<a href="#innerSearch" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="innerSearch">
				<i class="fa fa-search"></i>
				<?php esc_html_e( 'Filter results', 'outfit-standalone' ); ?>
			</a>
		</div><!--search-form-main-heading-->
		<div class="filter-close">
			<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/menu-m-x.png" />
		</div>
		<div id="innerSearch" class="classiera__inner">

			<?php if ($filterBy && $filterBy->catFilterByAge) { ?>
			<!--Age Groups-->
			<div class="inner-search-box ab">
				<div class="inner-search-heading"><?php esc_html_e( 'גיל', 'outfit-standalone' ); ?></div>
				<select id="ageGroup" name="postAgeGroup" class="form-control form-control-sm">					
					<option value=""><?php esc_html_e( 'בחרו גיל', 'outfit-standalone' ); ?></option>
					<?php
					foreach ($ageGroups as $c): ?>
						<option value="<?php echo $c->term_id; ?>"
							<?php echo (in_array($c->term_id, $postAgeGroup)? 'selected' : ''); ?>>
							<?php esc_html_e($c->name); ?></option>
					<?php endforeach; ?>
				</select>				
			</div>			
			<!--Age Groups-->
			<?php } ?>

			<!--Locations-->
			<div class="inner-search-box ab address">
				<div class="inner-search-heading"><?php esc_html_e( 'איזור', 'classiera' ); ?></div>
				<div class="inner-addon right-addon post_sub_loc">
					<input id="address" type="text" name="address" class="address form-control form-control-md" value="<?php echo esc_html($postAddress); ?>" placeholder="<?php esc_html_e('עיר או יישוב', 'outfit-standalone') ?>">
					<input class="latitude" type="hidden" id="latitude" name="latitude" value="<?php echo esc_html($postLatitude); ?>">
					<input class="longitude" type="hidden" id="longitude" name="longitude" value="<?php echo esc_html($postLongitude); ?>">
					<input class="locality" type="hidden" id="locality" name="locality" value="<?php echo esc_html($postLocality); ?>">
					<input class="aal3" type="hidden" id="aal3" name="aal3" value="<?php echo esc_html($postArea3); ?>">
					<input class="aal2" type="hidden" id="aal2" name="aal2" value="<?php echo esc_html($postArea2); ?>">
					<input class="aal1" type="hidden" id="aal1" name="aal1" value="<?php echo esc_html($postArea1); ?>">
				</div>				
			</div>

			<!--Locations-->

			<?php if (is_user_logged_in()) { ?>
			<div class="inner-search-box save-pre">
				<a id="outfit_save_search_prefs" href="javascript:void(0)"><?php esc_html_e('שמור את ההעדפות שלי', 'outfit-standalone') ?></a>
			</div>
			<?php } else { ?>
			<div class="inner-search-box save-pre">
				<a id="login_to_save_search_prefs" href="<?php echo esc_url(outfit_get_page_url('login')); ?>"><?php esc_html_e('התחבר/י לשמירת העדפה', 'outfit-standalone') ?></a>
			</div>
			<?php } ?>
			<?php if (count($subCategories)) { ?>
			<!--Categories-->
			<div class="inner-search-box">
				<div class="inner-search-heading"><?php esc_html_e( 'קטגוריות', 'outfit-standalone' ); ?></div>
				<div class="inner-addon right-addon">
					<?php
					foreach ($subCategories as $sub): ?>
						<div class="link">							
							<a href="<?php echo get_category_link($sub->term_id); ?>?outfit_ad"><?php esc_html_e($sub->name); ?></a>
						</div>
					<?php endforeach; ?>
				</div>

			</div>
			<!--Categories-->
			<?php } ?>
			<?php if ($filterBy && $filterBy->catFilterByBrand) { ?>
				<!--Brands-->
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'מותגים', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
					<?php
					foreach ($brands as $i => $c):
						$checked = in_array($c->term_id, $postBrand)? 'checked' : '';
						?>
						<div class="checkbox">
							<input type="checkbox" id="<?php echo esc_attr('brand_'.$i); ?>" name="postBrand[]" value="<?php echo $c->term_id; ?>" <?php echo $checked?>>
							<label for="<?php echo esc_attr('brand_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
						</div>
					<?php endforeach; ?>
					</div>

				</div>
				<!--Brands-->
			<?php } ?>

			<?php if ($filterBy && $filterBy->catFilterByColor) { ?>
				<!--Colors-->
				<div class="inner-search-box color">
					<div class="inner-search-heading"><?php esc_html_e( 'צבע', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($colors as $i => $c):
							$checked = in_array($c->term_id, $postColor)? 'checked' : '';
							$attId = get_term_meta($c->term_id, 'image', true);
							$imgSrcArr = wp_get_attachment_image_src($attId['ID']);

							if (!empty($imgSrcArr)) {
								$bg = 'url('.$imgSrcArr[0].')';
							}
							else {
								$bg = get_term_meta($c->term_id, 'color', true);
							}
						?>
							<div style="display: none">
								<?php //var_dump($imgSrcArr); ?>
							</div>
							<div class="checkbox <?php if(in_array($c->term_id, $postColor)): ?>active<?php endif; ?>">
								<input type="checkbox" id="<?php echo esc_attr('color_'.$i); ?>" name="postColor[]" value="<?php echo $c->term_id; ?>" <?php echo $checked?>>
								<label for="<?php echo esc_attr('color_'.$i); ?>" style="background: <?php echo esc_attr($bg); ?>" title="<?php esc_html_e($c->name); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>

				</div>
				<!--Colors-->
			<?php } ?>

			<!--Price-->
			<div class="inner-search-box">
				<div class="inner-search-heading"><?php esc_html_e( 'מחיר', 'outfit-standalone' ); ?></div>
				<div class="inner-addon right-addon">
					<?php
					$priceRanges = array(
						array(
							'min' => 0,
							'max' => 20
						),
						array(
							'min' => 20,
							'max' => 50
						),
						array(
							'min' => 50,
							'max' => 100
						),
						array(
							'min' => 100,
							'max' => 200
						),
						array(
							'min' => 200,
							'max' => 1000
						)
					);
					foreach ($priceRanges as $i => $range):
						$checked = in_array($range['min'].'_'.$range['max'], $postPrice)? 'checked' : '';
						?>
					<div class="checkbox">
							<input type="checkbox" id="<?php echo esc_attr('price_range_'.$i); ?>" name="priceRange[]"
								   value="<?php echo $range['min'].'_'.$range['max'] ?>" <?php echo $checked?>>
							<label for="<?php echo esc_attr('price_range_'.$i); ?>"><?php echo (outfit_format_price_range_label($priceRanges, $i, true)); ?></label>
						</div>
					<?php endforeach; ?>
				</div>

			</div>
			<!--Price-->

			<?php if ($filterBy && $filterBy->catFilterByCondition) { ?>
				<!--Conditions-->
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'מצב פריט', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($conditions as $i => $c):
							$checked = in_array($c->term_id, $postCondition)? 'checked' : '';
						?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('condition_'.$i); ?>" name="postCondition[]" value="<?php echo $c->term_id; ?>" <?php echo $checked; ?>>
								<label for="<?php echo esc_attr('condition_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>

				</div>
				<!--Conditions-->
			<?php } ?>

			<?php if ($filterBy && $filterBy->catFilterByWriter) { ?>
				<!--Writers-->
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'סופר', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($writers as $i => $c):
							$checked = in_array($c->term_id, $postWriter)? 'checked' : '';
						?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('writer_'.$i); ?>" name="postWriter[]" value="<?php echo $c->term_id; ?>" <?php echo $checked; ?>>
								<label for="<?php echo esc_attr('writer_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>

				</div>
				<!--Writers-->
			<?php } ?>

			<?php if ($filterBy && $filterBy->catFilterByCharacter) { ?>
				<!--Characters-->
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'דמות', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($characters as $i => $c):
							$checked = in_array($c->term_id, $postCharacter)? 'checked' : '';
						?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('character_'.$i); ?>" name="postCharacter[]" value="<?php echo $c->term_id; ?>" <?php echo $checked; ?>>
								<label for="<?php echo esc_attr('character_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>

				</div>
				<!--Characters-->
			<?php } ?>

			<?php if ($filterBy && $filterBy->catFilterByGender) { ?>
				<!--Genders-->
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'מין', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($genders as $i => $c):
							$checked = in_array($c->term_id, $postGender)? 'checked' : '';
							?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('gender_'.$i); ?>" name="postGender[]" value="<?php echo $c->term_id; ?>" <?php echo $checked; ?>>
								<label for="<?php echo esc_attr('gender_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>

				</div>
				<!--Genders-->
			<?php } ?>

			<?php if ($filterBy && $filterBy->catFilterByLanguage) { ?>
				<!--Languages-->
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'מין', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($languages as $i => $c):
							$checked = in_array($c->term_id, $postLanguage)? 'checked' : '';
							?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('language_'.$i); ?>" name="postLanguage[]" value="<?php echo $c->term_id; ?>" <?php echo $checked; ?>>
								<label for="<?php echo esc_attr('language_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>

				</div>
				<!--Languages-->
			<?php } ?>

			<input type="hidden" name="cat_id" value="<?php echo $catId; ?>">
			<input type="hidden" name="search">
			<input type="hidden" name="outfit_ad">
			<button style="display: none;" type="submit" name="search" class="btn btn-primary sharp btn-sm btn-style-one btn-block" value="<?php esc_html_e( 'Search', 'classiera') ?>"><?php esc_html_e( 'Search', 'classiera') ?></button>
		</div><!--innerSearch-->
	</div><!--search-form-->
</form>
<!--SearchForm-->

<script type="text/javascript">
	jQuery(document).ready(function(){

		jQuery('.inner-search-box').on("change", "input:not(.address), select", function(){
			jQuery(this).closest('form').submit();
		});
		jQuery('.inner-search-box').on("addresschange", "input.address", function(event){
			jQuery(this).closest('form').submit();
		});
		jQuery('.clear-address-filter').on("click", function() {
			var box = jQuery(this).closest('.inner-search-box');
			box.find(".latitude").val('');
			box.find(".longitude").val('');
			box.find(".locality").val('');
			box.find(".aal3").val('');
			box.find(".aal2").val('');
			box.find(".aal1").val('');
			box.find("input.address").val('');
			jQuery(this).closest('form').submit();
		});
		jQuery('.clear-age-filter').on("click", function() {
			jQuery('#ageGroup').val('').trigger('change');
			//jQuery(this).closest('form').submit();
		});
	});
</script>