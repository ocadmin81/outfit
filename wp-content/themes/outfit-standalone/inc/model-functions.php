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
            'parent' => 0
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