<?php

global $redux_demo;
global $catId, $outfitMainCat, $subCategories, $thisCategory;
global $searchLocations, $searchLocationsStr;

outfit_strip_slashes_from_input();

$filterBy = false;
$filterByGender = false;

if (null !== $thisCategory) {
	$filterBy = fetch_category_custom_fields(get_category($thisCategory->term_id));
}
else if ($outfitMainCat) {
	$filterBy = fetch_category_custom_fields(get_category($outfitMainCat));
}

/* filters */
$ageGroups = outfit_get_list_of_age_groups();
$colors = outfit_get_list_of_colors();

$conditions = outfit_get_list_of_conditions();
$writers = outfit_get_list_of_writers();
$characters = outfit_get_list_of_characters();
$brands = getBrandsByCategory($outfitMainCat);
$genders = outfit_get_list_of_genders();
$languages = outfit_get_list_of_languages();

$shoeSizes = outfit_get_list_of_shoe_sizes();
$maternitySizes = outfit_get_list_of_maternity_sizes();
$bicycleSizes = outfit_get_list_of_bicycle_sizes();


global $currentUserId;
$searchPrefAge = '';
$searchPrefLocation = null;

if ($currentUserId) {
	$searchPrefAge = get_user_meta($currentUserId, USER_META_SEARCH_PREF_AGE, true);
	$searchPrefLocation = OutfitLocation::createFromJSON(get_user_meta($currentUserId, USER_META_SEARCH_PREF_LOCATION, true));
}

$postColor = getRequestMultiple('postColor', true);
$postAgeGroup = getRequestMultiple('postAgeGroup', true);
$postBrand = getRequestMultiple('postBrand', true);
$postCondition = getRequestMultiple('postCondition', true);
$postWriter = getRequestMultiple('postWriter', true);
$postCharacter = getRequestMultiple('postCharacter', true);
$postGender = getRequestMultiple('postGender', true);
$postLanguage = getRequestMultiple('postLanguage', true);
$postShoeSize = getRequestMultiple('postShoeSize', true);
$postMaternitySize = getRequestMultiple('postMaternitySize', true);
$postBicycleSize = getRequestMultiple('postBicycleSize', true);

$postKeyword = getRequestInput('s', '');
$postLocation = null;
$postPrice = getRequestMultiple('priceRange', []);

if (!isset($_REQUEST['postAgeGroup'])) {
	$postAgeGroup = array($searchPrefAge);
}

// post primary location
if (!isset($_REQUEST['address'])) {
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
	$postAddress = trim(getRequestInput('address'));
	$postLatitude = getRequestInput('latitude');
	$postLongitude = getRequestInput('longitude');
	$postLocality = getRequestInput('locality');
	$postArea1 = getRequestInput('aal1');
	$postArea2 = getRequestInput('aal2');
	$postArea3 = getRequestInput('aal3');

	$postLocation = new OutfitLocation($postAddress, $postLongitude, $postLatitude, [
		'locality' => $postLocality,
		'aal3' => $postArea3,
		'aal2' => $postArea2,
		'aal1' => $postArea1
	]);
}


//var_dump($_REQUEST['address']);
//var_dump(get_query_var('address'));
?>
<!--SearchForm-->
<form id="advanced-search-form" method="post" action="<?php echo home_url(); ?>">
	<div class="search-form">
		<input type="hidden" id="paged" name="paged" value="1">
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
				<?php
				if (!empty($catId)) {
					$liveAgeGroups = outfit_filter_live_terms($catId, 'age_groups', $ageGroups);
				}
				else {
					$liveAgeGroups = $ageGroups;
				}
				?>
			<!--Age Groups-->
			<div class="inner-search-box ab">
				<div class="inner-search-heading"><?php esc_html_e( 'גיל', 'outfit-standalone' ); ?></div>
				<select id="ageGroup" name="postAgeGroup" class="form-control form-control-sm">					
					<option value=""><?php esc_html_e( 'בחרו גיל', 'outfit-standalone' ); ?></option>
					<?php
					foreach ($liveAgeGroups as $c): ?>
						<option value="<?php echo $c->term_id; ?>"
							<?php echo (in_array($c->term_id, $postAgeGroup)? 'selected' : ''); ?>>
							<?php esc_html_e($c->name); ?></option>
					<?php endforeach; ?>
				</select>				
			</div>	
			<!--<div><a class="clear-age-filter" href="javascript:void(0)">clear age</a></div>-->
			<!--Age Groups-->
			<?php } //if ($filterBy && $filterBy->catFilterByAge) ?>

			<!--Locations-->
			<div class="inner-search-box ab address">
				<div class="inner-search-heading"><?php esc_html_e( 'איזור', 'outfit-standalone' ); ?></div>

				<textarea style="display: none" id="locations" name="locations"><?php echo $searchLocationsStr; ?></textarea>

				<div class="inner-addon right-addon post_sub_loc">
					<input id="address" type="text" class="address form-control form-control-md" value=""
						   placeholder="<?php count($searchLocations) > 0? esc_html_e('הקלד אזור נוסף', 'outfit-standalone') : esc_html_e('עיר או יישוב', 'outfit-standalone') ?>">
					<input class="latitude" type="hidden" id="latitude" value="">
					<input class="longitude" type="hidden" id="longitude" value="">
					<input class="locality" type="hidden" id="locality" value="">
					<input class="aal3" type="hidden" id="aal3" value="">
					<input class="aal2" type="hidden" id="aal2" value="">
					<input class="aal1" type="hidden" id="aal1" value="">
				</div>

				<div class="">
					<?php foreach ($searchLocations as $i => $l): ?>
					<div class="address-label tag label label-info" style="display: inline-block;">
						<span><?php echo esc_html(wp_trim_words($l->getAddress(), 4)); ?></span>
						<a><i data-location-index="<?php echo $i; ?>" class="remove fa fa-times"></i></a>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<!--<div><a class="clear-address-filter" href="javascript:void(0)">clear address</a></div>-->
			<!--Locations-->

			<?php if (is_user_logged_in()) { ?>
			<div class="inner-search-box save-pre">
				<span style="display:none" class="save-done"><?php esc_html_e('ההגדרות נשמרו', 'outfit-standalone') ?></span>
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
				<?php
				if (!empty($catId)) {
					$livebrands = outfit_filter_live_terms($catId, 'brands', $brands);
				}
				else {
					$livebrands = $brands;
				}
				?>

				<!--Brands-->
				<?php if(count($livebrands)): ?>
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'מותגים', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
					<?php
					foreach ($livebrands as $i => $c):
						$checked = in_array($c->term_id, $postBrand)? 'checked' : '';
						?>
						<div class="checkbox">
							<input type="checkbox" id="<?php echo esc_attr('brand_'.$i); ?>" name="postBrand[]" value="<?php echo $c->term_id; ?>" <?php echo $checked?>>
							<label for="<?php echo esc_attr('brand_'.$i); ?>"><?php echo esc_html($c->name); ?></label>
						</div>
					<?php endforeach; ?>
					</div>

				</div>
				<?php endif; ?>
				<!--Brands-->
			<?php } ?>

			<?php if ($filterBy && $filterBy->catFilterByColor) { ?>
				<?php
				if (!empty($catId)) {
					$livecolors = outfit_filter_live_terms($catId, 'colors', $colors);
				}
				else {
					$livecolors = $colors;
				}
				?>
				<!--Colors-->
				<?php if (count($livecolors)): ?>
				<div class="inner-search-box color">
					<div class="inner-search-heading"><?php esc_html_e( 'צבע', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($livecolors as $i => $c):
							$checked = in_array($c->term_id, $postColor)? 'checked' : '';
							$attId = get_term_meta($c->term_id, 'image', true);
							$imgSrcArr = [];
							if (isset($attId['ID'])) {
								wp_get_attachment_image_src($attId['ID']);
							}

							if (!empty($imgSrcArr)) {
								$bg = 'url('.$imgSrcArr[0].')';
							}
							else {
								$bg = get_term_meta($c->term_id, 'color', true);
							}
						?>
							<div class="checkbox <?php if(in_array($c->term_id, $postColor)): ?>active<?php endif; ?>">
								<input type="checkbox" id="<?php echo esc_attr('color_'.$i); ?>" name="postColor[]" value="<?php echo $c->term_id; ?>" <?php echo $checked?>>
								<label for="<?php echo esc_attr('color_'.$i); ?>" style="background: <?php echo esc_attr($bg); ?>" title="<?php esc_html_e($c->name); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>

				</div>
				<?php endif; ?>
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
				<?php
				if (!empty($catId)) {
					$liveconditions = outfit_filter_live_terms($catId, 'conditions', $conditions);
				}
				else {
					$liveconditions = $conditions;
				}
				?>
				<!--Conditions-->
				<?php if(count($liveconditions)): ?>
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'מצב פריט', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($liveconditions as $i => $c):
							$checked = in_array($c->term_id, $postCondition)? 'checked' : '';
						?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('condition_'.$i); ?>" name="postCondition[]" value="<?php echo $c->term_id; ?>" <?php echo $checked; ?>>
								<label for="<?php echo esc_attr('condition_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
				<!--Conditions-->
			<?php } ?>

			<?php if ($filterBy && $filterBy->catFilterByWriter) { ?>
				<?php
				if (!empty($catId)) {
					$livewriters = outfit_filter_live_terms($catId, 'writers', $writers);
				}
				else {
					$livewriters = $writers;
				}
				?>
				<?php if(count($livewriters)): ?>
				<!--Writers-->
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'סופר', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($livewriters as $i => $c):
							$checked = in_array($c->term_id, $postWriter)? 'checked' : '';
						?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('writer_'.$i); ?>" name="postWriter[]" value="<?php echo $c->term_id; ?>" <?php echo $checked; ?>>
								<label for="<?php echo esc_attr('writer_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
				<!--Writers-->
			<?php } ?>

			<?php if ($filterBy && $filterBy->catFilterByCharacter) { ?>
				<?php
				if (!empty($catId)) {
					$livecharacters = outfit_filter_live_terms($catId, 'characters', $characters);
				}
				else {
					$livecharacters = $characters;
				}
				?>
				<?php if(count($livecharacters)): ?>
				<!--Characters-->
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'נושא', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($livecharacters as $i => $c):
							$checked = in_array($c->term_id, $postCharacter)? 'checked' : '';
						?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('character_'.$i); ?>" name="postCharacter[]" value="<?php echo $c->term_id; ?>" <?php echo $checked; ?>>
								<label for="<?php echo esc_attr('character_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
				<!--Characters-->
			<?php } ?>

			<?php if ($filterByGender && $filterBy && $filterBy->catFilterByGender) { ?>
				<?php
				if (!empty($catId)) {
					$livegenders = outfit_filter_live_terms($catId, 'genders', $genders);
				}
				else {
					$livegenders = $genders;
				}
				?>
				<?php if(count($livegenders)): ?>
				<!--Genders-->
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'מין', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($livegenders as $i => $c):
							$checked = in_array($c->term_id, $postGender)? 'checked' : '';
							?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('gender_'.$i); ?>" name="postGender[]" value="<?php echo $c->term_id; ?>" <?php echo $checked; ?>>
								<label for="<?php echo esc_attr('gender_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
				<!--Genders-->
			<?php } ?>

			<?php if ($filterBy && $filterBy->catFilterByLanguage) { ?>
				<?php
				//var_dump($languages);
				if (!empty($catId)) {
					$livelanguages = outfit_filter_live_terms($catId, 'languages', $languages);
				}
				else {
					$livelanguages = $languages;
				}
				?>
				<?php if(count($livelanguages)): ?>
				<!--Languages-->
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'שפה', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($livelanguages as $i => $c):
							$checked = in_array($c->term_id, $postLanguage)? 'checked' : '';
							?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('language_'.$i); ?>" name="postLanguage[]" value="<?php echo $c->term_id; ?>" <?php echo $checked; ?>>
								<label for="<?php echo esc_attr('language_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
				<!--Languages-->
			<?php } ?>

			<?php if ($filterBy && $filterBy->catFilterByShoeSize) { ?>
				<?php
				if (!empty($catId)) {
					$live = outfit_filter_live_terms($catId, 'shoe_sizes', $shoeSizes);
				}
				else {
					$live = $shoeSizes;
				}
				?>
				<?php if(count($live)): ?>
				<!--Shoe Sizes-->
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'מידה', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($live as $i => $c):
							$checked = in_array($c->term_id, $postShoeSize)? 'checked' : '';
							?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('shoesize_'.$i); ?>" name="postShoeSize[]" value="<?php echo $c->term_id; ?>" <?php echo $checked; ?>>
								<label for="<?php echo esc_attr('shoesize_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
				<!--Shoe Sizes-->
			<?php } ?>

			<?php if ($filterBy && $filterBy->catFilterByMaternitySize) { ?>
				<!-- if ($filterBy && $filterBy->catFilterByMaternitySize) -->
				<?php
				if (!empty($catId)) {
					$live = outfit_filter_live_terms($catId, 'maternity_sizes', $maternitySizes);
				}
				else {
					$live = $maternitySizes;
				}
				?>
				<!--Maternity Sizes cat id <?php echo $catId; ?>-->
				<?php if(count($live)): ?>
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'מידה', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($live as $i => $c):
							$checked = in_array($c->term_id, $postMaternitySize)? 'checked' : '';
							?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('maternitysize_'.$i); ?>" name="postMaternitySize[]" value="<?php echo $c->term_id; ?>" <?php echo $checked; ?>>
								<label for="<?php echo esc_attr('maternitysize_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
				<!--Maternity Sizes-->
			<?php } ?>

			<?php if ($filterBy && $filterBy->catFilterByBicycleSize) { ?>
				<?php
				if (!empty($catId)) {
					$live = outfit_filter_live_terms($catId, 'bicycle_sizes', $bicycleSizes);
				}
				else {
					$live = $bicycleSizes;
				}
				?>
				<?php if(count($live)): ?>
				<!--Bicycle Sizes-->
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'מידה', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($live as $i => $c):
							$checked = in_array($c->term_id, $postBicycleSize)? 'checked' : '';
							?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('bicyclesize_'.$i); ?>" name="postBicycleSize[]" value="<?php echo $c->term_id; ?>" <?php echo $checked; ?>>
								<label for="<?php echo esc_attr('bicyclesize_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
				<!--Bicycle Sizes-->
			<?php } ?>

			<input type="hidden" name="cat_id" value="<?php echo $catId; ?>">
			<input type="hidden" name="search">
			<input type="hidden" name="outfit_ad">
			<button style="display: none;" type="submit" name="search" class="btn btn-primary sharp btn-sm btn-style-one btn-block" value="<?php esc_html_e( 'Search', 'outfit-standalone') ?>"><?php esc_html_e( 'Search', 'outfit-standalone') ?></button>
		</div><!--innerSearch-->
	</div><!--search-form-->
</form>
<!--SearchForm-->

<script type="text/javascript">
	jQuery(document).ready(function(){

		var locations = [];
		var locationsJson = jQuery('#locations').val();

		if (locationsJson != '') {
			locations = JSON.parse(locationsJson);
		}

		if (locations.length >=5) {
			jQuery('#address').hide();
		}

		jQuery('.inner-search-box').on("change", "input:not(.address), select", function(){
			jQuery(this).closest('form').submit();
		});
		jQuery('.inner-search-box').on("addresschange", "input.address", function(event, param){
			locations.push(JSON.stringify(param));
			jQuery('#locations').val(JSON.stringify(locations));
			jQuery(this).closest('form').submit();
		});
		jQuery('.address-label i.remove').on("click", function () {
			var index = jQuery(this).attr('data-location-index');
			if (index >= 0 && index < locations.length) {
				locations.splice(index, 1);
			}
			jQuery('#locations').val(JSON.stringify(locations));
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

		var savingPrefs = false;

		jQuery('#advanced-search-form').on('submit', function(event) {
			if (savingPrefs) return false;
			return true;
		});

		/* save search prefs */
		jQuery('#outfit_save_search_prefs').on('click', function(event){
			event.preventDefault();
			var $btn = jQuery(this);
			$btn.addClass('saving');
			var age = (jQuery('#ageGroup')? jQuery('#ageGroup').val() : '');
			var locations = jQuery('#locations').val();
			var data = {
				'action': 'outfit_save_search_filters',
				'age': age,
				'locations': locations
			};
			jQuery.ajax({
				url: ajaxurl, //AJAX file path - admin_url('admin-ajax.php')
				type: "POST",
				data: data,
				success: function(data){
					jQuery('#outfit_save_search_prefs').text(jQuery('.save-done').text());
					$btn.removeClass('saving');
				}
			});
		});
	});
</script>
