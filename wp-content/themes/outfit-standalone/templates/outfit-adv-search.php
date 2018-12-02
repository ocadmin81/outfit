<?php

global $redux_demo;
global $catId, $outfitMainCat;
$filterBy = fetch_category_custom_fields(get_category($outfitMainCat));

/* filters */
$ageGroups = outfit_get_list_of_age_groups();
$colors = outfit_get_list_of_colors();

$conditions = outfit_get_list_of_conditions();
$writers = outfit_get_list_of_writers();
$characters = outfit_get_list_of_characters();
$brands = getBrandsByCategory($outfitMainCat);

$postColor = '';
$postAgeGroup = '';
$postBrand = '';
$postCondition = '';
$postWriter = '';
$postCharacter = '';

global $currentUserId;
$searchPrefAge = '';
$searchPrefLocation = null;

if ($currentUserId) {
	$searchPrefAge = get_user_meta($currentUserId, USER_META_SEARCH_PREF_AGE, true);
	$searchPrefLocation = get_user_meta($currentUserId, USER_META_SEARCH_PREF_LOCATION, true);
}

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
					<?php
					foreach ($ageGroups as $c): ?>
						<option value="<?php echo $c->term_id; ?>"
							<?php echo ($c->term_id == $postAgeGroup? 'selected' : ''); ?>>
							<?php esc_html_e($c->name); ?></option>
					<?php endforeach; ?>
				</select>				
			</div>
			<!--Age Groups-->
			<?php } ?>

			<!--Locations-->
			<div class="inner-search-box ab">
				<div class="inner-search-heading"><?php esc_html_e( 'אזור', 'classiera' ); ?></div>
				<select id="address" name="address" class="form-control form-control-sm">					
					<?php
					foreach ($ageGroups as $c): ?>
						<option value="<?php echo $c->term_id; ?>"
							<?php echo ($c->term_id == $postAgeGroup? 'selected' : ''); ?>>
							<?php esc_html_e($c->name); ?></option>
					<?php endforeach; ?>
				</select>	

			</div>

			<!--Locations-->

			<?php if ($filterBy && $filterBy->catFilterByBrand) { ?>
				<!--Brands-->
				<div class="inner-search-box">
					<div class="inner-search-heading"><?php esc_html_e( 'מותגים', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
					<?php
					foreach ($brands as $i => $c): ?>
						<div class="checkbox">
							<input type="checkbox" id="<?php echo esc_attr('brand_'.$i); ?>" name="postBrand[]" value="<?php echo $c->term_id; ?>">
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
						foreach ($colors as $i => $c): ?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('color_'.$i); ?>" name="postColor[]" value="<?php echo $c->term_id; ?>">
								<label for="<?php echo esc_attr('color_'.$i); ?>" style="background: <?php echo esc_attr(get_term_meta($c->term_id, 'color', true)); ?>" title="<?php esc_html_e($c->name); ?>"><?php esc_html_e($c->name); ?></label>
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
					foreach ($priceRanges as $i => $range): ?>
						<div class="checkbox">
							<input type="checkbox" id="<?php echo esc_attr('price_range_'.$i); ?>" name="priceRange[]"
								   value="<?php echo $range['min'].'_'.$range['max'] ?>">
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
						foreach ($conditions as $i => $c): ?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('condition_'.$i); ?>" name="postCondition[]" value="<?php echo $c->term_id; ?>">
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
						foreach ($writers as $i => $c): ?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('writer_'.$i); ?>" name="postWriter[]" value="<?php echo $c->term_id; ?>">
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
					divh5 class="inner-search-heading"><?php esc_html_e( 'Character', 'outfit-standalone' ); ?></div>
					<div class="inner-addon right-addon">
						<?php
						foreach ($characters as $i => $c): ?>
							<div class="checkbox">
								<input type="checkbox" id="<?php echo esc_attr('character_'.$i); ?>" name="postCharacter[]" value="<?php echo $c->term_id; ?>">
								<label for="<?php echo esc_attr('character_'.$i); ?>"><?php esc_html_e($c->name); ?></label>
							</div>
						<?php endforeach; ?>
					</div>

				</div>
				<!--Characters-->
			<?php } ?>

			<button type="submit" name="search" class="btn btn-primary sharp btn-sm btn-style-one btn-block" style="display:none;" value="<?php esc_html_e( 'Search', 'classiera') ?>"><?php esc_html_e( 'Search', 'classiera') ?></button>
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