<?php
/**
 * Template name: Profile Page
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiera
 * @since classiera
 */
global $redux_demo;
global $current_user;
$currentUserId = '';
$author = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
wp_get_current_user();
$currentUserId = $current_user->ID;
$authorId = $author->ID;
$authorName = get_the_author_meta('display_name', $authorId );
if (empty($authorName)) {
	$authorName = get_the_author_meta('user_nicename', $authorId );
}
if (empty($authorName)) {
	$authorName = get_the_author_meta('user_login', $authorId );
}
$authorAvatarUrl = get_user_meta($authorId, USER_META_AVATAR_URL, true);
$authorAvatarUrl = outfit_get_profile_img($authorAvatarUrl);
if (empty($authorAvatarUrl)) {
	outfit_get_avatar_url($authorId, 150);
}
$authorEmail = get_the_author_meta(USER_META_EMAIL, $authorId);
$authorPhone = get_the_author_meta(USER_META_PHONE, $authorId);
$authorAbout = get_the_author_meta(USER_META_ABOUT, $authorId);
$authorPreferredHours = get_the_author_meta(USER_META_PREFERRED_HOURS, $postAuthorId);

// post location
$postLocation = OutfitLocation::toAssoc(get_the_author_meta($authorId, USER_META_PRIMARY_ADDRESS, true));
$postAddress = '';
if (null !== $postLocation && isset($postLocation['address'])) {
	$postAddress = $postLocation['address'];
}
// post secondary location
$postLocation2 = OutfitLocation::toAssoc(get_post_meta($authorId, USER_META_SECONDARY_ADDRESS, true));
$postSecAddress = '';
if (null !== $postLocation2 && isset($postLocation2['address'])) {
	$postSecAddress = $postLocation2['address'];
}

if (isset($_POST['follow'])) {
	if (!empty($currentUserId)) {
		outfit_insert_author_follower($_POST['author_id'], $currentUserId);
	}
}
else if (isset($_POST['unfavorite'])) {
	if (!empty($currentUserId)) {
		outfit_delete_author_follower($_POST['author_id'], $currentUserId);
	}
}

get_header();


?>
<section class="author-box">
	<div class="container border author-box-bg">
		<div class="row">
			<div class="col-lg-12">
				<div class="row no-gutter removeMargin border-bottom author-first-row">
					<div class="col-lg-7 col-sm-7">
						<div class="author-info">
							<div class="media">
								<div class="media-left">
									<img class="media-object" src="<?php echo esc_url($authorAvatarUrl); ?>" alt="<?php echo get_the_author_meta('display_name', $userId ); ?>">
								</div><!--media-left-->
								<div class="media-body">
									<h5 class="media-heading text-uppercase">
										<?php echo esc_attr($authorName); ?>
									</h5>
									<div><?php echo esc_html($authorAbout); ?></div>
									<div>
										<?php if (!empty($userId) && $userId != $authorId) { ?>
											<form method="post" class="classiera_follow_user">
												<input type="hidden" name="author_id" value="<?php echo esc_attr($author_id); ?>"/>
												<?php if (!outfit_is_favorite_author($authorId, $userId)) { ?>
													<input type="submit" name="follow" value="<?php esc_html_e( 'Follow', 'outfit-standalone' ); ?>" />
												<?php } else { ?>
													<input type="submit" name="unfollow" value="<?php esc_html_e( 'Remove from favorites', 'outfit-standalone' ); ?>" />
												<?php } ?>
											</form>
											<div class="clearfix"></div>

										<?php } ?>
									</div>
								</div><!--media-body-->
							</div><!--media-->
						</div><!--author-info-->
					</div><!--col-lg-7-->
					<div class="col-lg-5 col-sm-5">
						<div class="author-contact-details">
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<h5 class="text-uppercase"><?php esc_html_e('Contact Details', 'outfit-standalone') ?> :</h5>
									<ul class="list-unstyled fa-ul c-detail">
										<?php if(!empty($authorPhone)){?>
											<li>
												<span class=""><?php echo esc_html($authorPhone);?></span>
											</li>
										<?php } ?>
										<?php if(!empty($authorPreferredHours)){?>
											<li>
												<span><?php esc_html_e('Best time to call', 'outfit-standalone') ?>:</span>
											<span>
												<?php echo esc_html($authorPreferredHours);?>
											</span>
											</li>
										<?php } ?>
									</ul>
								</div>
								<div class="col-md-6 col-sm-12">
									<h5 class="text-uppercase"><?php esc_html_e('Collect points', 'outfit-standalone') ?> :</h5>
									<ul class="list-unstyled fa-ul c-detail">
										<li>
										<span>
											<?php if (!empty($postAddress)) { ?>
												<?php echo esc_html($postAddress);?>
											<?php } ?>
										</span><br>
										<span>
											<?php if (!empty($postSecAddress)) { ?>
												<?php echo esc_html($postSecAddress);?>
											<?php } ?>
										</span>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="author-contact-details">
							<h5 class="text-uppercase"><?php esc_html_e('Contact Details', 'classiera') ?></h5>
							<div class="contact-detail-row">
								<div class="contact-detail-col">
									<?php $userPhone = get_the_author_meta('phone', $user_ID); ?>
									<?php if(!empty($userPhone)){?>
										<span>
                                        <i class="fa fa-phone-square"></i>
                                        <a href="tel:<?php echo esc_html($userPhone); ?>">
											<?php echo esc_html($userPhone); ?>
										</a>
                                    </span>
									<?php } ?>
								</div><!--contact-detail-col-->
								<div class="contact-detail-col">
									<?php $userWebsite = get_the_author_meta('user_url', $user_ID); ?>
									<?php if(!empty($userWebsite)){?>
										<span>
                                        <i class="fa fa-globe"></i>
                                        <a href="<?php echo esc_url($userWebsite); ?>">
											<?php echo esc_url($userWebsite); ?>
										</a>
                                    </span>
									<?php } ?>
								</div><!--contact-detail-col-->
							</div><!--contact-detail-row-->
							<div class="contact-detail-row">
								<div class="contact-detail-col">
									<?php $userMobile = get_the_author_meta('phone2', $user_ID); ?>
									<?php if(!empty($userMobile)){ ?>
										<span>
                                        <i class="fa fa-mobile-alt"></i>
										<a href="tel:<?php echo esc_html($userMobile); ?>">
											<?php echo esc_html($userMobile); ?>
										</a>
                                    </span>
									<?php } ?>
								</div><!--contact-detail-col-->
								<div class="contact-detail-col">
									<?php if(!empty($userEmail)){?>
										<span>
                                        <i class="fa fa-envelope"></i>
                                        <a href="mailto:<?php echo sanitize_email($userEmail); ?>">
											<?php echo sanitize_email($userEmail); ?>
										</a>
                                    </span>
									<?php } ?>
								</div><!--contact-detail-col-->
							</div><!--contact-detail-row-->
						</div><!--author-contact-details-->

					</div><!--col-lg-5 col-sm-5-->
				</div><!--row-->
			</div><!--col-lg-12-->
		</div><!--row-->
	</div><!--container border author-box-bg-->
</section><!--author-box-->
<?php if($classieraAuthorStyle == 'fullwidth'){?>
<section class="inner-page-content border-bottom">
	<section class="classiera-advertisement advertisement-v1">
		<div class="tab-divs section-light-bg">
			<div class="view-head">
				<div class="container">
					<div class="row">
						<div class="col-lg-6 col-sm-6 col-xs-6">
                            <div class="total-post">
                                <p><?php esc_html_e( 'Total ads', 'classiera' ); ?>: 
									<span>
									<?php echo count_user_posts($user_ID);?>&nbsp;
									<?php esc_html_e( 'Ads Posted', 'classiera' ); ?>
									</span>
								</p>
                            </div><!--total-post-->
                        </div><!--col-lg-6 col-sm-6 col-xs-6-->
						<div class="col-lg-6 col-sm-6 col-xs-6">
                            <div class="view-as text-right flip">
                                <span><?php esc_html_e( 'View As', 'classiera' ); ?>:</span>
                                <a id="grid" class="grid btn btn-sm sharp outline <?php if($classieraAdsView == 'grid'){ echo "active"; }?>" href="#"><i class="fa fa-th"></i></a>
                                <a id="list" class="list btn btn-sm sharp outline <?php if($classieraAdsView == 'list'){ echo "active"; }?>" href="#"><i class="fa fa-bars"></i></a>
                            </div><!--view-as text-right flip-->
                        </div><!--col-lg-6 col-sm-6 col-xs-6-->
					</div><!--row-->
				</div><!--container-->
			</div><!--view-head-->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane fade in active" id="all">
					<div class="container">
						<div class="row">
						<?php 
							global $paged, $wp_query, $wp;
							$args = wp_parse_args($wp->matched_query);
							if ( !empty ( $args['paged'] ) && 0 == $paged ){
								$wp_query->set('paged', $args['paged']);
								$paged = $args['paged'];
							}
							$cat_id = get_cat_ID(single_cat_title('', false));
							$temp = $wp_query;
							$wp_query= null;
							$wp_query = new WP_Query();
							$wp_query->query('post_type=post&posts_per_page=12&paged='.$paged.'&cat='.$cat_id.'&author='.$user_ID);
						while ($wp_query->have_posts()) : $wp_query->the_post();
							get_template_part( 'templates/classiera-loops/loop-lime');
						endwhile; 
						?>
						</div><!--row-->
						<?php
						if( function_exists('classiera_pagination') ){
							classiera_pagination();
						  }
						?>
					</div><!--container-->
					
				</div><!--tabpanel-->
				
				<?php wp_reset_query(); ?>
			</div><!--tab-content-->
		</div><!--tab-divs section-light-bg-->
	</section><!--classiera-advertisement advertisement-v1-->
</section><!--inner-page-content-->
<?php }elseif($classieraAuthorStyle == 'sidebar'){?>
<section class="inner-page-content border-bottom top-pad-50">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-lg-9">
				<section class="classiera-advertisement advertisement-v1">
					<div class="tab-divs section-light-bg">
						<div class="view-head">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-6 col-xs-6">
                                        <div class="total-post">
                                            <p><?php esc_html_e( 'Total ads', 'classiera' ); ?>: 
												<span>
												<?php echo count_user_posts($user_ID);?>&nbsp;
												<?php esc_html_e( 'Ads Posted', 'classiera' ); ?>
												</span>
											</p>
                                        </div><!--total-post-->
                                    </div><!--col-lg-6 col-sm-6 col-xs-6-->
                                    <div class="col-lg-6 col-sm-6 col-xs-6">
                                        <div class="view-as text-right flip">
                                            <span><?php esc_html_e( 'View As', 'classiera' ); ?>:</span>
                                            <a id="grid" class="grid btn btn-sm sharp outline <?php if($classieraAdsView == 'grid'){ echo "active"; }?>" href="#"><i class="fa fa-th"></i></a>
                                            <a id="list" class="list btn btn-sm sharp outline <?php if($classieraAdsView == 'list'){ echo "active"; }?>" href="#"><i class="fa fa-bars"></i></a>
                                        </div><!--view-as text-right flip-->
                                    </div><!--col-lg-6 col-sm-6 col-xs-6-->
                                </div><!--row-->
                            </div><!--container-->
                        </div><!--view-head-->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane fade in active" id="all">
								<div class="container">
									<div class="row">
									<?php 
									global $paged, $wp_query, $wp;
									$args = wp_parse_args($wp->matched_query);
									if ( !empty ( $args['paged'] ) && 0 == $paged ){
										$wp_query->set('paged', $args['paged']);
										$paged = $args['paged'];
									}
									$cat_id = get_cat_ID(single_cat_title('', false));
									$temp = $wp_query;
									$wp_query= null;
									$wp_query = new WP_Query();
									$wp_query->query('post_type=post&posts_per_page=12&paged='.$paged.'&cat='.$cat_id.'&author='.$user_ID);
									while ($wp_query->have_posts()) : $wp_query->the_post();
										get_template_part( 'templates/classiera-loops/loop-lime');
									endwhile; 
									?>
									</div><!--row-->
								</div><!--container-->
							</div><!--tabpanel-->
						</div><!--tab-content-->
					</div><!--tab-divs section-light-bg-->
				</section><!--classiera-advertisement advertisement-v1-->
				<?php
				  if ( function_exists('classiera_pagination') ){
					classiera_pagination();
				  }
				?>
				<?php wp_reset_query(); ?>
			</div><!--col-md-8 col-lg-9-->
			<!--Sidebar-->
			<div class="col-md-4 col-lg-3">
				<aside class="sidebar">
					<div class="row">
						<?php get_sidebar('pages'); ?>
					</div>
				</aside>
			</div>
			<!--Sidebar-->
		</div><!--row-->
	</div><!--container-->
</section>
<?php } ?>
<!-- Company Section Start-->
<?php 
	global $redux_demo; 
	$classieraCompany = $redux_demo['partners-on'];
	$classieraPartnersStyle = $redux_demo['classiera_partners_style'];
	if($classieraCompany == 1){
		if($classieraPartnersStyle == 1){
			get_template_part('templates/members/memberv1');
		}elseif($classieraPartnersStyle == 2){
			get_template_part('templates/members/memberv2');
		}elseif($classieraPartnersStyle == 3){
			get_template_part('templates/members/memberv3');
		}elseif($classieraPartnersStyle == 4){
			get_template_part('templates/members/memberv4');
		}elseif($classieraPartnersStyle == 5){
			get_template_part('templates/members/memberv5');
		}elseif($classieraPartnersStyle == 6){
			get_template_part('templates/members/memberv6');
		}
	}
?>
<!-- Company Section End-->	
<?php get_footer(); ?>