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
    global $redux_demo;
    $login = '/login';//$redux_demo['login'];
    wp_redirect( $login ); exit;
}
?>
<h3>profile</h3>
<a href="/profile-settings">Edit profile</a>