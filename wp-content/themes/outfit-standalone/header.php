<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */
?><!DOCTYPE html>
<?php $server_uri = $_SERVER['REQUEST_URI']; ?>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php if (strpos($server_uri, 'author') !== false): ?>
		<meta name="robots" content="NOINDEX,NOFOLLOW" />
	<?php endif; ?>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-WX2VHGV');</script>
	<!-- End Google Tag Manager -->

<link rel="profile" href="http://gmpg.org/xfn/11">
<link href="https://fonts.googleapis.com/css?family=Assistant:300,400,600,700&amp;subset=hebrew" rel="stylesheet">
	<?php
	$pageTitle = '';
	if (isset($_REQUEST['bpg'])) {
		$postBrand = getRequestMultiple('postBrand', true);
		if (count($postBrand) > 0) {
			$term = get_term($postBrand[0], 'brands');
			if (!is_wp_error($term)) {
				$pageTitle = $term->name;
			}
		}
	}
	else if (isset($_GET['cs']) && !empty($_GET['cs'])) {
		$pageTitle = 'תוצאות חיפוש עבור ' . '"' . $_GET['cs'] . '"';
	}
	else if (is_author()) {
		$author = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
		if ($author) {
			$pageTitle = getAuthorFullName($author->ID);
		}
		else {
			$pageTitle = wp_get_document_title();
		}
	}
	else {
		$pageTitle = wp_get_document_title();
	}
	?>
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title><?php echo esc_attr($pageTitle); ?></title>
	<?php endif; ?>
<?php wp_head(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri()."/rtl3.css" ?>" type="text/css" >
</head>

<body <?php body_class(); ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WX2VHGV"
				  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentyseventeen' ); ?></a>

	<header id="masthead" class="site-header" role="banner">

		<?php get_template_part( 'template-parts/header/header', 'image' ); ?>

		<?php if ( has_nav_menu( 'top' ) ) : ?>
			<div class="navigation-top">
				<div class="wrap">
					<?php get_template_part( 'template-parts/navigation/navigation', 'top' ); ?>
				</div><!-- .wrap -->
			</div><!-- .navigation-top -->
		<?php endif; ?>

	</header><!-- #masthead -->
	<div class="overlay-nav"></div>
	<div class="site-content-contain">
		<div id="content" class="site-content">
