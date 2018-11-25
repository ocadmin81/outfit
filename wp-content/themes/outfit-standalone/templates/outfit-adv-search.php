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

?>
<!--SearchForm-->
<form method="get" action="<?php echo home_url(); ?>">
	<div class="search-form">
		<div class="search-form-main-heading">
			<a href="#innerSearch" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="innerSearch">
				<i class="fa fa-search"></i>
				<?php esc_html_e( 'Filter results', 'outfit-standalone' ); ?>
			</a>
		</div><!--search-form-main-heading-->
		<div id="innerSearch" class="collapse in classiera__inner">

			<?php if ($filterBy && $filterBy->catFilterByAge) { ?>
			<!--Age Groups-->
			<div class="inner-search-box">
				<h5 class="inner-search-heading"><i class="fas fa-map-marker-alt"></i>
					<?php esc_html_e( 'Age', 'outfit-standalone' ); ?>
				</h5>
				<div class="inner-addon right-addon">
					<i class="right-addon form-icon fa fa-sort"></i>
					<select id="ageGroup" name="postAgeGroup" class="form-control form-control-sm">
						<option value=""><?php esc_html_e('Select Age Groups', 'outfit-standalone'); ?></option>
						<?php
						foreach ($ageGroups as $c): ?>
							<option value="<?php echo $c->term_id; ?>"
								<?php echo ($c->term_id == $postAgeGroup? 'selected' : ''); ?>>
								<?php esc_html_e($c->name); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

			</div>
			<!--Age Groups-->
			<?php } ?>

			<!--Locations-->
			<div class="inner-search-box">
				<h5 class="inner-search-heading"><i class="fas fa-map-marker-alt"></i>
				<?php esc_html_e( 'Location', 'classiera' ); ?>
				</h5>
				<div class="inner-addon right-addon post_sub_loc">
					<i class="right-addon form-icon fa fa-sort"></i>
					<select name="post_city" class="form-control form-control-sm" id="post_city">
						<option value=""><?php esc_html_e('Select City', 'classiera'); ?></option>
					</select>
				</div>

			</div>

			<!--Locations-->

			<button type="submit" name="search" class="btn btn-primary sharp btn-sm btn-style-one btn-block" value="<?php esc_html_e( 'Search', 'classiera') ?>"><?php esc_html_e( 'Search', 'classiera') ?></button>
		</div><!--innerSearch-->
	</div><!--search-form-->
</form>
<!--SearchForm-->