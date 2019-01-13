<?php

/**
 *
 */
class OutfitInit
{
    public static function init() {

        self::create_tables();
        self::setup_pages();
    }

    private static function create_tables() {

        global $wpdb;

        $sql2 = ("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}author_follower (

        id int(11) NOT NULL AUTO_INCREMENT,

        author_id int(11) NOT NULL,

        follower_id int(11) NOT NULL,

        PRIMARY KEY (id)

    ) ENGINE=InnoDB AUTO_INCREMENT=1;");

        $wpdb->query($sql2);

        $sql = ("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}author_favorite (

        id int(11) NOT NULL AUTO_INCREMENT,

        author_id int(11) NOT NULL,

        post_id int(11) NOT NULL,

        PRIMARY KEY (id)

    ) ENGINE=InnoDB AUTO_INCREMENT=1;");

        $wpdb->query($sql);
    }

    private static function setup_pages() {

        $pages = self::get_pages();
        foreach ($pages as $p) {
            self::create_page_if_null($p['slug'], $p['template']);
        }
    }

    public static function get_pages() {

        $pages = array(

            'login' => array(
                'slug' => 'login',
                'template' => 'template-login-signup.php'
            ),
            'profile_settings' => array(
                'slug' => 'profile_settings',
                'template' => 'template-edit-profile.php'
            ),
            'submit_ad' => array(
                'slug' => 'submit_ad',
                'template' => 'template-submit-ads.php'
            ),
            'edit_ad' => array(
                'slug' => 'edit_ad',
                'template' => 'template-edit-ads.php'
            ),
            'favorite_ads' => array(
                'slug' => 'favorite_ads',
                'template' => 'template-favorite.php'
            ),
            'follow' => array(
                'slug' => 'follow',
                'template' => 'template-follow.php'
            ),
            'user_all_ads' => array(
                'slug' => 'user_all_ads',
                'template' => 'template-user-all-ads.php'
            ),
            'advsearch' => array(
                'slug' => 'advsearch',
                'template' => 'custom-search.php'
            )
            /*'' => array(
                'slug' => '',
                'template' => ''
            )*/
        );
        return $pages;
    }

    private static function create_page_if_null($target, $template) {

        $page = get_page_by_title($target);
        if ($page == NULL) {
            self::create_page_fly($target, $template);
        }
        else {
            if ($template != get_post_meta( $page->ID, '_wp_page_template', true )) {
                update_post_meta( $page->ID, '_wp_page_template', $template );
            }
        }
    }

    private static function create_page_fly($target, $template) {

        wp_insert_post( array(
            'post_title'     => $target,
            'post_type'      => 'page',
            'post_name'      => $target,
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
            'post_content'   => '',
            'post_status'    => 'publish',
            'post_author'    => 1,
            // Assign page template
            'page_template'  => $template
        ) );
    }
}