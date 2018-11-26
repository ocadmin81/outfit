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

function outfit_get_list_of_characters() {

    return outfit_get_list_of_filters('characters');
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

function getSubCategories($catId) {
    if (!outfit_category_exists($catId)) {
        return array();
    }
    return get_categories('child_of='.$catId.'&hide_empty=0');
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
