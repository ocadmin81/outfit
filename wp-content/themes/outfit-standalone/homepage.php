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

get_header();

echo do_shortcode('[homeprod]');



get_footer(); ?>