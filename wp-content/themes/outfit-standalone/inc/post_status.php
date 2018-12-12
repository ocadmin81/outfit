<?php

function outfit_custom_status_creation(){
    register_post_status( 'sold', array(
        'label'                     => _x( 'Sold', 'post' ),
        'label_count'               => _n_noop( 'Sold <span class="count">(%s)</span>', 'Sold <span class="count">(%s)</span>'),
        'public'                    => true,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true
    ));
}
add_action( 'init', 'outfit_custom_status_creation' );

function outfit_custom_status_add_in_quick_edit() {
    echo "<script>
        jQuery(document).ready( function() {
            jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"sold\">Sold</option>' );
        });
        </script>";
}
add_action('admin_footer-edit.php','outfit_custom_status_add_in_quick_edit');
function outfit_custom_status_add_in_post_page() {
    echo "<script>
        jQuery(document).ready( function() {
            jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"sold\">Sold</option>' );
        });
        </script>";
}
add_action('admin_footer-post.php', 'outfit_custom_status_add_in_post_page');
add_action('admin_footer-post-new.php', 'outfit_custom_status_add_in_post_page');