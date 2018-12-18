<?php

function outfit_get_main_cats($excludeDefault = false) {

    $exclude = array();
    if ($excludeDefault) {
        $exclude[] = get_option( 'default_category' );
    }
    $categories = get_terms('category', array(
            'hide_empty' => 0,
            'parent' => 0,
            'order'=> 'ASC',
            'exclude' => $exclude
        )
    );
    return $categories;
}

function outfit_get_cat_ancestors($childId) {

    $ancestors = array();
    $term = get_term($childId, 'category');
    while ( ! is_wp_error($term) && ! empty( $term->parent ) && ! in_array( $term->parent, $ancestors ) ) {
        $ancestors[] = (int) $term->parent;
        $term = get_term($term->parent, 'category');
    }
    if (empty($ancestors)) {
        return false;
    }
    return $ancestors;
}

function fetch_category_custom_fields($category) {

    $extraFields = get_option(MY_CATEGORY_FIELDS);
    $category->catFilterByColor = isset( $extraFields[$category->term_id]['category_filter_by_color'] ) ? $extraFields[$category->term_id]['category_filter_by_color'] : false;
    $category->catFilterByBrand = isset( $extraFields[$category->term_id]['category_filter_by_brand'] ) ? $extraFields[$category->term_id]['category_filter_by_brand'] : false;
    $category->catFilterByAge = isset( $extraFields[$category->term_id]['category_filter_by_age'] ) ? $extraFields[$category->term_id]['category_filter_by_age'] : false;
    $category->catFilterByCondition = isset( $extraFields[$category->term_id]['category_filter_by_condition'] ) ? $extraFields[$category->term_id]['category_filter_by_condition'] : false;
    $category->catFilterByWriter = isset( $extraFields[$category->term_id]['category_filter_by_writer'] ) ? $extraFields[$category->term_id]['category_filter_by_writer'] : false;
    $category->catFilterByCharacter = isset( $extraFields[$category->term_id]['category_filter_by_character'] ) ? $extraFields[$category->term_id]['category_filter_by_character'] : false;
    $category->catFilterByGender = isset( $extraFields[$category->term_id]['category_filter_by_gender'] ) ? $extraFields[$category->term_id]['category_filter_by_gender'] : false;
    $category->catFilterByLanguage = isset( $extraFields[$category->term_id]['category_filter_by_language'] ) ? $extraFields[$category->term_id]['category_filter_by_language'] : false;
    $category->catFilterByShoeSize = isset( $extraFields[$category->term_id]['category_filter_by_shoesize'] ) ? $extraFields[$category->term_id]['category_filter_by_shoesize'] : false;
    $category->catFilterByMaternitySize = isset( $extraFields[$category->term_id]['category_filter_by_maternitysize'] ) ? $extraFields[$category->term_id]['category_filter_by_maternitysize'] : false;
    $category->catFilterByBicycleSize = isset( $extraFields[$category->term_id]['category_filter_by_bicyclesize'] ) ? $extraFields[$category->term_id]['category_filter_by_bicyclesize'] : false;

    return $category;
}

function outfit_category_exists($termId) {

    $term = get_term_by( 'id', $termId, 'category' );
    if ( false !== $term ) {
        return true;
    }
    return false;
}

function outfit_get_list_of_age_groups() {

    $ageGroups = get_terms('age_groups', array(
            'hide_empty' => 0,
            'parent' => 0
        )
    );
    return $ageGroups;
}

function outfit_get_list_of_colors() {

    $colors = get_terms('colors', array(
            'hide_empty' => 0,
            'parent' => 0,
            'meta_key' => 'color'
        )
    );
    return $colors;
}

function outfit_get_list_of_conditions() {

    $conditions = get_terms('conditions', array(
            'hide_empty' => 0,
            'parent' => 0
        )
    );
    return $conditions;
}

function outfit_get_list_of_writers() {

    return outfit_get_list_of_filters('writers');
}

function outfit_get_list_of_genders() {

    return outfit_get_list_of_filters('genders');
}

function outfit_get_list_of_languages() {

    return outfit_get_list_of_filters('languages');
}

function outfit_get_list_of_characters() {

    return outfit_get_list_of_filters('characters');
}

function outfit_get_list_of_shoe_sizes() {
    return outfit_get_list_of_filters('shoe_sizes');
}

function outfit_get_list_of_maternity_sizes() {
    return outfit_get_list_of_filters('maternity_sizes');
}

function outfit_get_list_of_bicycle_sizes() {
    return outfit_get_list_of_filters('bicycle_sizes');
}

function outfit_get_list_of_filters($taxonomy) {

    $filters = get_terms($taxonomy, array(
            'hide_empty' => 0,
            'parent' => 0
        )
    );
    return $filters;
}

function outfit_get_list_of_brands($categoryId) {

    $brands = get_terms('brands', array(
            'hide_empty' => 0,
            'parent' => 0,
            'meta_query' => array(
                array(
                    'key'       => 'categories',
                    'value'     => $categoryId,
                    'compare'   => '='
                )
            )
        )
    );
    return $brands;
}

function outfit_get_list_of_terms_by_cat($categoryId, $taxonomy) {

    $terms = get_terms($taxonomy, array(
            'hide_empty' => 0,
            'parent' => 0,
            'meta_query' => array(
                array(
                    'key'       => 'categories',
                    'value'     => $categoryId,
                    'compare'   => '='
                )
            )
        )
    );
    return $terms;
}

function toArrayOfInts($array) {
    $new = array();
    foreach ($array as $k => $v) {
        $new[$k] = (int)$v;
    }
    return $new;
}

function setPostColors($postId, $termIds) {

    wp_set_object_terms($postId, toArrayOfInts($termIds), 'colors');
}

function setPostAgeGroups($postId, $termIds) {

    wp_set_object_terms($postId, toArrayOfInts($termIds), 'age_groups');
}

function setPostBrands($postId, $termIds) {

    wp_set_object_terms($postId, toArrayOfInts($termIds), 'brands');
}

function setPostCondition($postId, $termId) {

    wp_set_object_terms($postId, (int)$termId, 'conditions');
}

function setPostTerms($postId, $termIds, $taxonomy) {

    wp_set_object_terms($postId, toArrayOfInts($termIds), $taxonomy);
}

function setPostWriters($postId, $termIds) {

    setPostTerms($postId, $termIds, 'writers');
}

function setPostCharacters($postId, $termIds) {

    setPostTerms($postId, $termIds, 'characters');
}

function setPostLanguages($postId, $termIds) {

    setPostTerms($postId, $termIds, 'languages');
}

function setPostGenders($postId, $termIds) {

    setPostTerms($postId, $termIds, 'genders');
}

function setPostShoeSizes($postId, $termIds) {

    setPostTerms($postId, $termIds, 'shoe_sizes');
}

function setPostMaternitySizes($postId, $termIds) {

    setPostTerms($postId, $termIds, 'maternity_sizes');
}

function setPostBicycleSizes($postId, $termIds) {

    setPostTerms($postId, $termIds, 'bicycle_sizes');
}

function getPostTermIds($postId, $taxonomy) {
    $terms = wp_get_post_terms( $postId, $taxonomy );
    $res = array();
    foreach ($terms as $term) {
        $res[] = $term->term_id;
    }
    return $res;
}

function getPostTermNames($postId, $taxonomy) {
    $terms = wp_get_post_terms( $postId, $taxonomy );
    $res = array();
    foreach ($terms as $term) {
        $res[] = $term->name;
    }
    return $res;
}

function getPostColors($postId) {
    return getPostTermIds($postId, 'colors');
}

function getPostAgeGroups($postId) {
    return getPostTermIds($postId, 'age_groups');
}

function getPostBrands($postId) {
    return getPostTermIds($postId, 'brands');
}

function getPostConditions($postId) {
    return getPostTermIds($postId, 'conditions');
}

function getPostWriters($postId) {
    return getPostTermIds($postId, 'writers');
}

function getPostCharacters($postId) {
    return getPostTermIds($postId, 'characters');
}

function getPostGenders($postId) {
    return getPostTermIds($postId, 'genders');
}

function getPostLanguages($postId) {
    return getPostTermIds($postId, 'languages');
}

function getCategoryPath($postId) {

    $terms = get_the_category($postId);
    $res = array();

    foreach ($terms as $term) {
        if ($term->parent == 0) {
            $res[0] = $term->term_id;
            break;
        }
    }
    if (isset($res[0])) {
        foreach ($terms as $term) {
            if ($term->parent == $res[0]) {
                $res[1] = $term->term_id;
                break;
            }
        }
    }
    if (isset($res[1])) {
        foreach ($terms as $term) {
            if ($term->parent == $res[1]) {
                $res[2] = $term->term_id;
                break;
            }
        }
    }
    return $res;
}

function getSubCategories($catId, $direct = false) {
    if (!outfit_category_exists($catId)) {
        return array();
    }
    if (!$direct) {
        return get_categories('child_of=' . $catId . '&hide_empty=0');
    }
    return get_categories('parent=' . $catId . '&hide_empty=0');
}

function getBrandsByCategory($catId) {
    if (!outfit_category_exists($catId)) {
        return array();
    }
    return outfit_get_list_of_brands($catId);
}

function getOutfitAdsByAuthor($authorId, $paged, $perPage = 5) {

    $args = array(
        'paged' => $paged,
        'posts_per_page' => $perPage,
        'post_type' => array( OUTFIT_AD_POST_TYPE ),
        'author' => $authorId,
        'post_status' => 'publish',
        'order'                  => 'DESC',
        'orderby'                => 'modified',
        /*'meta_query'             => array(
            array(
                'key'     => POST_META_OUTFIT_STATUS,
                'value'   => OUTFIT_AD_STATUS_ACTIVE,
                'compare' => '='
            )
        )*/
    );

    return get_posts( $args );
}

function getUserKidsBirthdays($userId) {

    $userKidsBirthdays = get_user_meta($userId, USER_META_KIDS_BIRTHDAYS, true);
    if (empty($userKidsBirthdays)) {
        return array();
    }
    $userKidsBirthdays = explode('||', $userKidsBirthdays);
    return $userKidsBirthdays;
}

function updateUserKidsBirthdays($userId, $birthdays) {
    update_user_meta($userId, USER_META_KIDS_BIRTHDAYS, implode('||', $birthdays));
}

function getUserFavoriteBrands($userId) {

    $brands = get_user_meta($userId, USER_META_FAVORITE_BRANDS, true);
    if (empty($brands)) {
        return array();
    }
    $brands = explode('||', $brands);
    return $brands;
}

function updateUserFavoriteBrands($userId, $brands) {
    update_user_meta($userId, USER_META_FAVORITE_BRANDS, implode('||', $brands));
}

function getListOfAllBrands() {
    $bs = get_terms('brands', array(
            'hide_empty' => 0,
            'parent' => 0
        )
    );
    return $bs;
}

function validateDateInput($input) {
    $format = 'Y-m-d';
    $date = DateTime::createFromFormat($format, $input);
    return $date && DateTime::getLastErrors()['warning_count'] == 0 && DateTime::getLastErrors()['error_count'] == 0;
}

function getAuthorFullName($postAuthorId) {

    $firstName = get_the_author_meta(USER_META_FIRSTNAME, $postAuthorId);
    $lastName = get_the_author_meta(USER_META_LASTNAME, $postAuthorId);
    if (!empty($firstName) && !empty($lastName)) {
        return ($firstName . ' ' . $lastName);
    }
    $postAuthorName = get_the_author_meta('display_name', $postAuthorId );
    if (empty($postAuthorName)) {
        $postAuthorName = get_the_author_meta('user_nicename', $postAuthorId);
    }
    if (empty($postAuthorName)) {
        $postAuthorName = get_the_author_meta('user_login', $postAuthorId );
    }
    return $postAuthorName;
}

function getAuthorFullNameCat($postAuthorId) {

    $firstName = get_the_author_meta(USER_META_FIRSTNAME, $postAuthorId);
    $lastName = get_the_author_meta(USER_META_LASTNAME, $postAuthorId);
    if (!empty($firstName) && !empty($lastName)) {
        return ($firstName . ' ' . mb_substr($lastName, 0, 1,'UTF-8').'.');
    }
    $postAuthorName = get_the_author_meta('display_name', $postAuthorId );
    if (empty($postAuthorName)) {
        $postAuthorName = get_the_author_meta('user_nicename', $postAuthorId);
    }
    if (empty($postAuthorName)) {
        $postAuthorName = get_the_author_meta('user_login', $postAuthorId );
    }
    return $postAuthorName;
}

function outfit_is_user_exists($userId) {
    $user = get_userdata( $userId );
    if ( $user === false ) {
        return false;
    }
    return true;
}

function outfit_get_recent_products($userId, $count) {
    $rsnt = get_user_meta($userId, USER_META_LAST_PRODUCTS, true);
    $rsnt = explode(',', $rsnt);
    $recent = [];
    foreach ($rsnt as $r) {
        if (!empty($r)) {
            $recent[] = (int)$r;
        }
    }
    $sortedPosts = [];
    if (count($recent) > 0) {
        $args = array(
            'post_type' => OUTFIT_AD_POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => $count,
            'paged' => 1,
            'post__in' => $recent
        );
        $recentPosts = get_posts($args);

        $recentPostsWKeys = [];
        foreach ($recentPosts as $rp) {
            $recentPostsWKeys[intval($rp->ID)] = $rp;
        }


        foreach ($recent as $pid) {

            if (isset($recentPostsWKeys[$pid])) {
                $sortedPosts[] = $recentPostsWKeys[$pid];
            }
        }
    }
    return $sortedPosts;
}

function outfit_get_recent_cats_posts($userId, $count) {
    $rsnt = get_user_meta($userId, USER_META_LAST_CATEGORY, true);
    $rsnt = explode(',', $rsnt);
    $recent = [];

    foreach ($rsnt as $r) {
        if (!empty($r)) {
            $recent[] = (int)$r;
        }
    }
    $recentPosts = [];
    if (count($recent) > 0) {
        $args = array(
            'post_type' => OUTFIT_AD_POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => $count,
            'paged' => 1,
            'category__in' => $recent
        );
        $recentPosts = get_posts($args);

    }
    return $recentPosts;
}

/**
 * @param $ageTermId
 * @param OutfitLocation $location
 * @param $count
 * @return array
 */
function outfit_get_posts_by_age_and_location($ageTermId, $locations, $count) {
    $posts = array();

    if (empty($ageTermId) && empty($locations)) {
        return $posts;
    }

    $args = array(
        'post_type' => OUTFIT_AD_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => $count,
        'paged' => 1
    );
    if (!empty($ageTermId)) {
        $args['tax_query'] = [array(
            'taxonomy' => 'age_groups',
            'field'    => 'term_id',
            'terms'    => $ageTermId,
            'operator' => 'IN'
        )];
    }
    if (!empty($locations)) {

        $metaQueryLocation = array('relation' => 'OR');

        // search by locations
        foreach ($locations as $location) {
            $meta = $location->suggestMetaKeyValue();
            if (false !== $meta) {
                $metaQueryLocation[] = array(
                    'key' => $meta['meta_key'],
                    'value' => $meta['meta_value'],
                    'compare' => '='
                );
            }
        }

        $args['meta_query'] = $metaQueryLocation;

        //var_dump($args);
    }
    $posts = get_posts($args);
    return $posts;
}

function outfit_get_posts_of_favorite_sellers($userId, $count) {
    $posts = array();
    $args = array(
        'post_type' => OUTFIT_AD_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => $count,
        'paged' => 1
    );
    $favoritearray = outfit_authors_all_favorite_sellers($userId);
    if (count($favoritearray) == 0) {
        return $posts;
    }
    $args['author__in'] = $favoritearray;
    $posts = get_posts($args);
    return $posts;
}

function outfit_get_wishlist_posts($userId, $count) {
    $posts = array();
    $args = array(
        'post_type' => OUTFIT_AD_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => $count,
        'paged' => 1
    );
    $favoritearray = outfit_authors_all_favorite($userId);
    if (count($favoritearray) == 0) {
        return $posts;
    }
    $args['post__in'] = $favoritearray;
    $posts = get_posts($args);
    return $posts;
}

function outfit_get_category_posts($catslug, $count) {
    $posts = array();
    $catObj = get_category_by_slug($catslug);
    if (!$catObj) {
        return $posts;
    }
    $args = array(
        'post_type' => OUTFIT_AD_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => $count,
        'paged' => 1,
        'cat' => $catObj->term_id
    );

    $posts = get_posts($args);
    return $posts;
}

function outfit_get_category_posts_by_id($catId, $count) {

    $posts = array();
    if (empty($catId)) {
        return $posts;
    }
    $args = array(
        'post_type' => OUTFIT_AD_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => $count,
        'paged' => 1,
        'cat' => $catId
    );

    $posts = get_posts($args);
    return $posts;
}

function outfit_get_posts_count_by_term($categoryId, $taxonomy) {

    global $wpdb;
    $query = "
      SELECT COUNT(p.ID) as posts, tt1.term_id as term
      FROM $wpdb->posts AS p
      INNER JOIN $wpdb->term_relationships AS tr1 ON (p.ID = tr1.object_id)
      INNER JOIN $wpdb->term_taxonomy AS tt1 ON (tr1.term_taxonomy_id = tt1.term_taxonomy_id)
      INNER JOIN $wpdb->term_relationships AS tr2 ON (p.ID = tr2.object_id)
      INNER JOIN $wpdb->term_taxonomy AS tt2 ON (tr2.term_taxonomy_id = tt2.term_taxonomy_id)
      WHERE p.post_status = 'publish'
      AND p.post_type = '".OUTFIT_AD_POST_TYPE."'
      AND tt1.taxonomy = '$taxonomy'
      AND tt2.term_id = $categoryId
      GROUP BY tt1.term_id
    ";
    return $wpdb->get_results($query);
    //return $query;
}

function outfit_get_live_terms($categoryId, $taxonomy) {

    $res = outfit_get_posts_count_by_term($categoryId, $taxonomy);
    $terms = array();
    foreach ($res as $r) {
        $terms[] = $r->term;
    }
    return $terms;
}

function outfit_filter_live_terms($categoryId, $taxonomy, $terms) {
    $live = outfit_get_live_terms($categoryId, $taxonomy);
    $res = array();
    foreach ($terms as $term) {
        if (in_array($term->term_id, $live)) {
            $res[] = $term;
        }
    }
    return $res;
}