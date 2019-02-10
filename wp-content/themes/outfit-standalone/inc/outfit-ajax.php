<?php
/*==========================
 Add to favorites AJAX Function
 ===========================*/
add_action('wp_ajax_outfit_favorite_post', 'outfitAddToFavoritesLoggedIn');
add_action('wp_ajax_nopriv_outfit_favorite_post', 'outfitAddToFavoritesLoggedOut');//for users that are not logged in.
function outfitAddToFavoritesLoggedOut() {
    if (isset($_GET['post_id'])) {
        $loginUrl = outfit_login_url_back('', 'favorite', $_GET['post_id']);
        wp_send_json(['login' => $loginUrl]);
    }
    else {
        wp_send_json(['error' => 'no post id']);
    }
}
function outfitAddToFavoritesLoggedIn() {
    global $current_user;
    wp_get_current_user();
    $userId = $current_user->ID;
    if (isset($_GET['post_id']) && !empty($userId) && !empty($_GET['post_id'])) {
        outfit_insert_author_favorite($userId, $_GET['post_id']);
        wp_send_json(['result' => 'added', 'count' => outfit_wishlist_count_shortcode()]);
    }
    else {
        wp_send_json(['error' => 'no post id']);
    }
}
add_action('wp_ajax_outfit_unfavorite_post', 'outfitRemoveFromFavoritesLoggedIn');
add_action('wp_ajax_nopriv_outfit_unfavorite_post', 'outfitRemoveFromFavoritesLoggedIn');//for users that are not logged in.
function outfitRemoveFromFavoritesLoggedIn() {
    global $current_user;
    wp_get_current_user();
    $userId = $current_user->ID;
    if (isset($_GET['post_id']) && !empty($userId) && !empty($_GET['post_id'])) {
        outfit_delete_author_favorite($userId, $_GET['post_id']);
        wp_send_json(['result' => 'removed', 'count' => outfit_wishlist_count_shortcode()]);
    }
    else {
        wp_send_json(['error' => 'no post id']);
    }
}
/*==========================
 Select Sub categories AJAX Function
 ===========================*/
add_action('wp_ajax_outfitGetSubCatOnClick', 'outfitGetSubCatOnClick');
add_action('wp_ajax_nopriv_outfitGetSubCatOnClick', 'outfitGetSubCatOnClick');//for users that are not logged in.
function outfitGetSubCatOnClick()
{
    if (isset($_POST['mainCat'])) {
        $option = '';
        $mainCatID = $_POST['mainCat'];
        if (!outfit_category_exists($mainCatID)) {
            echo $option;
            wp_die('outfit_category_not_exist');
        }
        $cat_child = get_term_children($mainCatID, 'category');
        if (!empty($cat_child)) {
            $categories = get_categories('parent=' . $mainCatID . '&hide_empty=0');

            foreach ($categories as $cat) {
                $cat = fetch_category_custom_fields($cat);
                $option .= '<option value="' . $cat->term_id . '" ' .

                    'data-color-enabled="' . ($cat->catFilterByColor ? '1' : '0') . '" ' .
                    'data-brand-enabled="' . ($cat->catFilterByBrand ? '1' : '0') . '" ' .
                    'data-writer-enabled="' . ($cat->catFilterByWriter ? '1' : '0') . '" ' .
                    'data-character-enabled="' . ($cat->catFilterByCharacter ? '1' : '0') . '" ' .
                    'data-age-enabled="' . ($cat->catFilterByAge ? '1' : '0') . '" ' .
                    'data-condition-enabled="' . ($cat->catFilterByCondition ? '1' : '0') . '" ' .
                    'data-gender-enabled="' . ($cat->catFilterByGender ? '1' : '0') . '" ' .
                    'data-language-enabled="' . ($cat->catFilterByLanguage ? '1' : '0') . '" ' .
                    'data-shoesize-enabled="' . ($cat->catFilterByShoeSize ? '1' : '0') . '" ' .
                    'data-maternitysize-enabled="' . ($cat->catFilterByMaternitySize ? '1' : '0') . '" ' .
                    'data-bicyclesize-enabled="' . ($cat->catFilterByBicycleSize ? '1' : '0') . '" >';

                $option .= $cat->name;
                $option .= '</option>';
            }
            echo '<option value="">' . esc_html__("בחירת תת קטגוריה", "outfit-standalone") . '</option>' . $option;
            wp_die();
        } else {
            echo $option;
            wp_die();
        }
    } // end if
}

add_action('wp_ajax_outfitGetBrandsByCat', 'outfitGetBrandsByCat');
add_action('wp_ajax_nopriv_outfitGetBrandsByCat', 'outfitGetBrandsByCat');

function outfitGetBrandsByCat()
{

    if (isset($_POST['mainCat'])) {
        $option = '';
        $mainCatID = $_POST['mainCat'];
        if (!outfit_category_exists($mainCatID)) {
            echo $option;
            wp_die();
        }
        $brands = outfit_get_list_of_brands($mainCatID);
        if (!empty($brands)) {

            foreach ($brands as $brand) {
                $option .= '<option value="' . $brand->term_id . '">';
                $option .= $brand->name;
                $option .= '</option>';
            }
            echo '<option value="">' . esc_html__("בחירת מותג", "outfit-standalone") . '</option>' . $option;
            wp_die();
        } else {
            echo $option;
            wp_die();
        }
    } // end if
}

?>