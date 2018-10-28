<?php
/**
 * Enqueues scripts and styles for front end.
 *
 * @since outfit 0.1
 *
 * @return void
 */
function outfit_scripts_styles(){

	// Load google maps js
	global $redux_demo;
	$googleApiKey = $redux_demo['google_api_key'];
	$mapLang = 'he';
	$mapRegion = 'IL';

	//Load Script
	wp_enqueue_script('jquery.min', get_template_directory_uri() . '/assets/js/jquery.min.js', 'jquery', '', true);
	wp_enqueue_script('bootstrap.min', get_template_directory_uri() . '/assets/js/bootstrap.min.js', 'jquery', '', true);
	wp_enqueue_script('bootstrap-dropdownhover', get_template_directory_uri() . '/assets/js/bootstrap-dropdownhover.js', 'jquery', '', true);
	wp_enqueue_script('validator.min', get_template_directory_uri() . '/assets/js/validator.min.js', 'jquery', '', true);
	wp_enqueue_script('owl.carousel.min', get_template_directory_uri() . '/assets/js/owl.carousel.min.js', 'jquery', '', true);
	wp_enqueue_script('jquery.matchHeight', get_template_directory_uri() . '/assets/js/jquery.matchHeight.js', 'jquery', '', true);
	wp_enqueue_script('infinitescroll', get_template_directory_uri() . '/assets/js/infinitescroll.js', 'jquery', '', true);
	wp_enqueue_script('select2.min', get_template_directory_uri() . '/assets/js/select2.min.js', 'jquery', '', true);
	wp_enqueue_script('slick.min', get_template_directory_uri() . '/assets/js/slick.min.js', 'jquery', '', true);
	wp_enqueue_script('outfit', get_template_directory_uri() . '/assets/js/outfit.js', 'jquery', '', true);
	wp_enqueue_script('jquery-ui', get_template_directory_uri() . '/assets/js/jquery-ui.min.js', 'jquery', '', true);

	wp_enqueue_script( 'outfit-google-maps-script', 'https://maps.googleapis.com/maps/api/js?key='.$googleApiKey.'&language='.$mapLang.'&region='.$mapRegion.'&v=3.exp', array( 'jquery' ), '2014-07-18', true );
	if( is_page_template('template-submit-ads.php') || is_page_template('template-edit-post.php')) {
		/* add javascript */
		wp_enqueue_script('getlocation-script', get_template_directory_uri() . '/assets/js/getlocation-script.js', 'jquery', '', true);
		//wp_enqueue_script('select2.min', get_template_directory_uri() . '/js/select2.min.js', 'jquery', '', true);
		//wp_enqueue_style( 'select2.min', get_template_directory_uri() . '/css/select2.min.css', array(), '1' );
	}

	wp_enqueue_style( 'select2.min', get_template_directory_uri() . '/assets/css/select2.min.css', array(), '1' );
	wp_enqueue_style( 'jquery-ui', get_template_directory_uri() . '/assets/css/jquery-ui.min.css', array(), '1' );
	
    // Load Classiera CSS
    wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.css', array(), '1' );
    wp_enqueue_style( 'animate.min', get_template_directory_uri() . '/assets/css/animate.min.css', array(), '1' );
    wp_enqueue_style( 'bootstrap-dropdownhover.min', get_template_directory_uri() . '/assets/css/bootstrap-dropdownhover.min.css', array(), '1' );
	wp_enqueue_style( 'outfit-components', get_template_directory_uri() . '/assets/css/outfit-components.css', array(), '1' );
	wp_enqueue_style( 'outfit', get_template_directory_uri() . '/assets/css/outfit.css', array(), '1' );
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.css', array(), '1' );
	wp_enqueue_style( 'material-design-iconic-font', get_template_directory_uri() . '/assets/css/material-design-iconic-font.css', array(), '1' );
	wp_enqueue_style( 'owl.carousel.min', get_template_directory_uri() . '/assets/css/owl.carousel.min.css', array(), '1' );
	wp_enqueue_style( 'owl.theme.default.min', get_template_directory_uri() . '/assets/css/owl.theme.default.min.css', array(), '1' );
	wp_enqueue_style( 'responsive', get_template_directory_uri() . '/assets/css/responsive.css', array(), '1' );
	wp_enqueue_style( 'outfit-map', get_template_directory_uri() . '/assets/css/outfit-map.css', array(), '1' );
    wp_enqueue_style( 'bootstrap-slider', get_template_directory_uri() . '/assets/css/bootstrap-slider.css', array(), '1' );

	if(is_rtl()){
		wp_enqueue_style( 'bootstrap-rtl', get_template_directory_uri() . '/assets/css/bootstrap-rtl.css', array(), '1' );
	}

	if(is_admin_bar_showing()) echo "<style type=\"text/css\">.navbar-fixed-top { margin-top: 28px; } </style>";

}