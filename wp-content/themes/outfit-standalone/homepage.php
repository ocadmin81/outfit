<?php
/**
 * Template name: Perfit Home
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiera
 * @since classiera 1.0
 */
$loggedIn = is_user_logged_in();
global $current_user, $user_id;
global $redux_demo;
global $paged, $wp_query, $wp, $post;
global $catObj;
$catslugs = ['clothing', 'toys', 'strollers'];

$currentUser = $current_user = wp_get_current_user();
$userId = $user_id = $currentUser->ID;

$perPage = 5;

get_header(); ?>

<section class="user-pages section-gray-bg wish-page">
	<div class="wrap ad-page user-page">
		<?php
		//if (!$loggedIn) {
			foreach ($catslugs as $c) {
				//$catObj = get_category_by_slug($c);
				//if (false !== $catObj) {
				//	get_template_part('templates/catprod_home');
				//}
				echo do_shortcode('[catprod cat_slug="clothing"]');
			} //foreach ($catslugs as $c)
		//}
		//else {

		//}
		?>
	</div><!--container-->
</section><!--user-pages section-gray-bg-->



<?php get_footer(); ?>