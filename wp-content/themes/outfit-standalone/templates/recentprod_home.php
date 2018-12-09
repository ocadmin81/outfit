<?php

global $catObj;
global $current_user, $user_id;
global $redux_demo;
global $paged, $wp_query, $wp, $post;

$perPage = 5;

if (is_user_logged_in()) {

    wp_get_current_user();
    $currentUser = $current_user;
    $rsnt = get_user_meta($currentUser->ID, USER_META_LAST_PRODUCTS, true);
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
            'posts_per_page' => 5,
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

?>
<?php if (count($sortedPosts) > 0) { ?>
<div class="row home-cat-row">
<div class="col-md-12 user-content-height">
<div class="user-detail-section section-bg-white">
<div class="user-ads favorite-ads">
<div class="my-ads products">
    <div class="row">
        <?php foreach ( $sortedPosts as $post ) : setup_postdata( $post ); ?>
            <div class="col-lg-4 col-md-4 col-sm-6 item">
                <div class="classiera-box-div classiera-box-div-v1">
                    <figure class="clearfix">

                        <div class="premium-img">
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                $postPrice = get_post_meta($post->ID, POST_META_PRICE, true);
                                $postAuthorId = $post->post_author;
                                $postAuthorName = getAuthorFullName($postAuthorId);

                                $authorAvatarUrl = outfit_get_user_picture($postAuthorId, 50);
                                $postBrand = implode(',', getPostTermNames($post->ID, 'brands'));
                                if( has_post_thumbnail()){
                                    $imageurl = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'classiera-370');
                                    $thumb_id = get_post_thumbnail_id($post->ID);
                                    ?>
                                    <img class="img-responsive" src="<?php echo esc_url( $imageurl[0] ); ?>" alt="<?php the_title() ?>">
                                    <?php
                                }else{
                                    ?>
                                    <img class="img-responsive" src="<?php echo get_template_directory_uri() . '/assets/images/nothumb.png' ?>" alt="No Thumb"/>
                                    <?php
                                }
                                ?>
                            </a>
                            <div class="ad-brand"><?php echo esc_attr($postBrand); ?></div>
                        </div><!--premium-img-->
                        <div class="au-price">
                            <div class="au">
                                <a href="<?php echo get_author_posts_url( $postAuthorId ); ?>">
                                    <img style="height: 30px;" class="" src="<?php echo esc_url($authorAvatarUrl); ?>" alt="<?php echo esc_attr($postAuthorName); ?>">
                                    <?php echo esc_attr($postAuthorName); ?>
                                </a>
                            </div>
                            <?php if(!empty($postPrice)){?>
                                <div class="price">
															<span class="">
																<?php
                                                                if(is_numeric($postPrice)){
                                                                    echo '&#8362; ' .  $postPrice;
                                                                }else{
                                                                    echo esc_attr( $postPrice );
                                                                }
                                                                ?>
															</span>
                                </div>
                            <?php } ?>
                        </div>

                    </figure>
                </div><!--classiera-box-div-->
            </div><!--col-lg-4-->
        <?php endforeach; ?>
    </div>
    <?php wp_reset_postdata(); ?>
</div>
</div><!--user-ads user-profile-settings-->
</div><!--user-detail-section-->
</div><!--col-lg-9-->
</div><!--row-->
<?php } //if ($wp_query->have_posts()) ?>

<?php } //if (is_user_logged_in()) ?>
