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



global $currentUserId;
$searchPrefAge = '';
$searchPrefLocation = null;

if ($currentUserId) {
	$searchPrefAge = get_user_meta($currentUserId, USER_META_SEARCH_PREF_AGE, true);
	$searchPrefLocation = OutfitLocation::createFromJSON(get_user_meta($currentUserId, USER_META_SEARCH_PREF_LOCATION, true));
}

$postColor = getGetMultiple('postColor', []);
$postAgeGroup = getGetMultiple('postAgeGroup', []);;
$postBrand = getGetMultiple('postBrand', []);;
$postCondition = getGetMultiple('postCondition', []);;
$postWriter = getGetMultiple('postWriter', []);;
$postCharacter = getGetMultiple('postCharacter', []);;
$postKeyword = getGetInput('s', '');
$postLocation = null;
$postPrice = getGetMultiple('priceRange', []);

if (!isset($_GET['postAgeGroup']) || empty($_GET['postAgeGroup'])) {
	$postAgeGroup = array($searchPrefAge);
}

// post primary location
if (!isset($_GET['address']) || empty($_GET['address'])) {
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

			<?php if (count($subCategories)) { ?>
			<!--Categories-->
			<!--<div class="inner-search-box">
				<h5 class="inner-search-heading"><i class="fas fa-map-marker-alt"></i>
					<?php //esc_html_e( 'Sub category', 'outfit-standalone' ); ?>
				</h5>
				<div class="inner-addon right-addon">
					<?php
					//foreach ($subCategories as $sub): ?>
						<div class="checkbox">
							<input type="checkbox" id="<?php //echo esc_attr('cat_'.$sub->term_id); ?>" name="category[]" value="<?php //echo $sub->term_id; ?>">
							<label for="<?php //echo esc_attr('cat_'.$sub->term_id); ?>"><?php //esc_html_e($sub->name); ?></label>
						</div>
					<?php //endforeach; ?>
				</div>

			</div>-->
			<!--Categories-->
			<?php } ?>

			<?php if ($filterBy && $filterBy->catFilterByAge) { ?>
			<!--Age Groups-->
			<div class="inner-search-box ab">
				<div class="inner-search-heading"><?php esc_html_e( 'גיל', 'outfit-standalone' ); ?></div>
				<select id="ageGroup" name="postAgeGroup" class="form-control form-control-sm">					
					<option value=""><?php esc_html_e( 'גיל', 'outfit-standalone' ); ?></option>
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
			<div class="inner-search-box">
				<h5 class="inner-search-heading"><i class="fas fa-map-marker-alt"></i>
				<?php esc_html_e( 'Location', 'classiera' ); ?>
				</h5>
				<div class="inner-addon right-addon post_sub_loc">
					<input id="address" type="text" name="address" class="address form-control form-control-md" value="<?php echo esc_html($postAddress); ?>" placeholder="<?php esc_html_e('Address or City', 'outfit-standalone') ?>">
					<input class="latitude" type="hidden" id="latitude" name="latitude" value="<?php echo esc_html($postLatitude); ?>">
					<input class="longitude" type="hidden" id="longitude" name="longitude" value="<?php echo esc_html($postLongitude); ?>">
					<input class="locality" type="hidden" id="locality" name="locality" value="<?php echo esc_html($postLocality); ?>">
					<input class="aal3" type="hidden" id="aal3" name="aal3" value="<?php echo esc_html($postArea3); ?>">
					<input class="aal2" type="hidden" id="aal2" name="aal2" value="<?php echo esc_html($postArea2); ?>">
					<input class="aal1" type="hidden" id="aal1" name="aal1" value="<?php echo esc_html($postArea1); ?>">
				</div>

			</div>

			<!--Locations-->

			<div class="inner-search-box">
				<a id="outfit_save_search_prefs" href="javascript:void(0)"><?php esc_html_e('Save Search Preferences', 'outfit-standalone') ?></a>
			</div>

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
						?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('color_'.$i); ?>" name="postColor[]" value="<?php echo $c->term_id; ?>" <?php echo $checked?>>
								<label for="<?php echo esc_attr('color_'.$i); ?>" style="color: <?php echo esc_attr(get_term_meta($c->term_id, 'color', true)); ?>"><?php esc_html_e($c->name); ?></label>
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
							<label for="<?php echo esc_attr('price_range_'.$i); ?>"><?php echo (outfit_format_price_range_label($priceRanges, $i)); ?></label>
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
					<div class="inner-search-heading"><?php esc_html_e( 'Writer', 'outfit-standalone' ); ?></div>
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
					<div class="inner-search-heading"><?php esc_html_e( 'Character', 'outfit-standalone' ); ?></div>
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

		jQuery('.inner-search-box').on("change", "input, select", function(){
			jQuery(this).closest('form').submit();
		});
	});
</script>