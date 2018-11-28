<?php
/**
 * Template name: Profile
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage outfit-standalone
 * @since outfit-standalone
 */

if ( !is_user_logged_in() ) {
    wp_redirect( outfit_get_page_url('login') );
    exit;
}

get_header();
?>
<h3>profile</h3>
<a href="/profile-settings">Edit profile</a>

<?php get_footer(); ?>