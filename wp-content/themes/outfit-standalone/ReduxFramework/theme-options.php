<?php
    /**
     * ReduxFramework Sample Config File
     * For full documentation, please visit: http://docs.reduxframework.com/
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }


    // This is your option name where all the Redux data is stored.
    $opt_name = "redux_demo";

    // This line is only for altering the demo. Can be easily removed.
    $opt_name = apply_filters( 'redux_demo/opt_name', $opt_name );

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        // TYPICAL -> Change these values as you need/desire
        'opt_name'             => $opt_name,
        // This is where your data is stored in the database and also becomes your global variable name.
        'display_name'         => $theme->get( 'Name' ),
        // Name that appears at the top of your panel
        'display_version'      => $theme->get( 'Version' ),
        // Version that appears at the top of your panel
        'menu_type'            => 'menu',
        //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
        'allow_sub_menu'       => true,
        // Show the sections below the admin menu item or not
        'menu_title'           => __( 'Outfit Options', 'outfit-standalone' ),
        'page_title'           => __( 'Outfit Options', 'outfit-standalone' ),
        // You will need to generate a Google API key to use this feature.
        // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
        'google_api_key'       => '',
        'admin_bar'            => true,
        // Show the panel pages on the admin bar
        'admin_bar_icon'       => 'dashicons-portfolio',
        // Choose an icon for the admin bar menu
        'admin_bar_priority'   => 50,
        // Choose an priority for the admin bar menu
        'global_variable'      => '',
        // Set a different name for your global variable other than the opt_name
        'dev_mode'             => false,
        // Show the time the page took to load, etc
        'update_notice'        => false,
        // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
        'customizer'           => true,
        // Enable basic customizer support
        //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
        //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

        // OPTIONAL -> Give you extra features
        'page_priority'        => null,
        // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
        'page_parent'          => 'themes.php',
        // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
        'page_permissions'     => 'manage_options',
        // Permissions needed to access the options panel.
        'menu_icon'            => '',
        // Specify a custom URL to an icon
        'last_tab'             => '',
        // Force your panel to always open to a specific tab (by id)
        'page_icon'            => 'icon-themes',
        // Icon displayed in the admin panel next to your menu_title
        'page_slug'            => '',
        // Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
        'save_defaults'        => true,
        // On load save the defaults to DB before user clicks save or not
        'default_show'         => false,
        // If true, shows the default value next to each field that is not the default value.
        'default_mark'         => '',
        // What to print by the field's title if the value shown is default. Suggested: *
        'show_import_export'   => true,
        // Shows the Import/Export panel when not used as a field.

        // CAREFUL -> These options are for advanced use only
        'transient_time'       => 60 * MINUTE_IN_SECONDS,
        'output'               => true,
        // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
        'output_tag'           => true,
        // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
        // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

        // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
        'database'             => '',
        // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
        'use_cdn'              => true,
        // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.

        // HINTS
        'hints'                => array(
            'icon'          => 'el el-question-sign',
            'icon_position' => 'right',
            'icon_color'    => 'lightgray',
            'icon_size'     => 'normal',
            'tip_style'     => array(
                'color'   => 'red',
                'shadow'  => true,
                'rounded' => false,
                'style'   => '',
            ),
            'tip_position'  => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect'    => array(
                'show' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'mouseover',
                ),
                'hide' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'click mouseleave',
                ),
            ),
        )
    );

    // Panel Intro text -> before the form
    if ( ! isset( $args['global_variable'] ) || $args['global_variable'] !== false ) {
        if ( ! empty( $args['global_variable'] ) ) {
            $v = $args['global_variable'];
        } else {
            $v = str_replace( '-', '_', $args['opt_name'] );
        }
        $args['intro_text'] = sprintf( __( 'Welcome To Outfit WordPress Theme Options Panel', 'outfit-standalone' ), $v );
    } else {
        $args['intro_text'] = __( 'Welcome To Outfit WordPress Theme Options Panel', 'outfit-standalone' );
    }

    // Add content after the form.
    $args['footer_text'] = __( 'Thanks for using Outfit Options Panel.', 'outfit-standalone' );

    Redux::setArgs( $opt_name, $args );

    /*
     * ---> END ARGUMENTS
     */


    /*
     *
     * ---> START SECTIONS
     *
     */

    /*

        As of Redux 3.5+, there is an extensive API. This API can be used in a mix/match mode allowing for

     */
    /*Redux::setSection( $opt_name, array(
        'title'            => __( 'Pages', 'outfit-standalone' ),
        'id'               => 'pages',
        'customizer_width' => '500px',
        'icon'             => 'el-icon-website',
		'fields'     => array(
			array(
				'id'=>'login',
				'type' => 'text',
				'title' => __('Login Page URL', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting Login template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'register',
				'type' => 'text',
				'title' => __('Register Page URL', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting Register template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'profile',
				'type' => 'text',
				'title' => __('Profile Page URL', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting Profile template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'edit',
				'type' => 'text',
				'title' => __('Profile Settings Page URL', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting Profile template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'new_post',
				'type' => 'text',
				'title' => __('Submit Ad Page URL', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting Submit template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'edit_post',
				'type' => 'text',
				'title' => __('Edit Ad Page URL', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting Edit Ad template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'featured_plans',
				'type' => 'text',
				'title' => __('Pricing Plans Page URL', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting Pricing Plan template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'all-cat-page-link',
				'type' => 'text',
				'title' => __('All Categories Page URL', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting All Categories template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'all-ads-page-link',
				'type' => 'text',
				'title' => __('All Ads Page URL', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting All Ads template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'all-ads',
				'type' => 'text',
				'title' => __('Single User All Ads', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting Single User All Ads template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'outfit_single_user_plans',
				'type' => 'text',
				'title' => __('Single User Plans', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting Single User All Plans template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'outfit_inbox_page_url',
				'type' => 'text',
				'title' => __('Inbox Page URL', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting inbox template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'all-favourite',
				'type' => 'text',
				'title' => __('Single User All Favorite', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting Favorite Ads template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'outfit_user_follow',
				'type' => 'text',
				'title' => __('Single User All Follower', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting Follow template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'reset',
				'type' => 'text',
				'title' => __('Reset Password Page URL', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Create page by selecting Reset Password Page template and insert url here.', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'termsandcondition',
				'type' => 'text',
				'title' => __('Terms And Conditions URL', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('This Link will be shown at registration page', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'outfit_cart_url',
				'type' => 'text',
				'title' => __('WooCommerece Cart URL', 'outfit-standalone'),
				'subtitle' => __('This must be an URL.', 'outfit-standalone'),
				'desc' => __('Please Put Woo Commerce URL here', 'outfit-standalone'),
				'validate' => 'url',
				'default' => ''
			),
		)
    ) );*/

    Redux::setSection( $opt_name, array(
        'title'            => __( 'Google API', 'outfit-standalone' ),
        'id'               => 'google_api',
        'customizer_width' => '500px',
        'icon'             => 'el el-map-marker',
        'fields'     => array(
            array(
                'id'=>'google_api_key',
                'type' => 'text',
                'title' => __('Google API Key', 'outfit-standalone'),
                'subtitle' => __('To use the Maps JavaScript API, you must get an API key.', 'outfit-standalone'),
                'desc' => __('The API key is used to track API requests associated with your project for quota, usage, and billing.', 'outfit-standalone'),
                'default' => ''
            )
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'            => __( 'Email Settings', 'outfit-standalone' ),
        'id'               => 'email_settings',
        'customizer_width' => '500px',
        'icon'             => 'fas fa-envelope',
        'fields'     => array(
            array(
                'id'=>'admin_email',
                'type' => 'email',
                'title' => __('Admin Email for Notifications', 'outfit-standalone'),
                'default' => ''
            ),
            array(
                'id'=>'new_user_title',
                'type' => 'text',
                'title' => __('New User Email Title', 'outfit-standalone'),
                'default' => 'כיף שהצטרפת אלינו!'
            ),
            array(
                'id'=>'new_user_text1',
                'type' => 'textarea',
                'title' => __('New User Email Paragraph 1', 'outfit-standalone'),
                'default' => 'העלו את המודעה הראשונה ותתחילו למכור.'
            ),
            array(
                'id'=>'new_user_text2',
                'type' => 'textarea',
                'title' => __('New User Email Paragraph 2', 'outfit-standalone'),
                'default' => 'הוסיפו פרטים לפרופיל האישי.'.'כך נכיר אותך ונדע לייעל את החיפוש לך באתר.'
            ),
            array(
                'id'=>'new_user_text3',
                'type' => 'textarea',
                'title' => __('New User Email Paragraph 3', 'outfit-standalone'),
                'default' => 'שתפו את האתר עם הורים כמוך.'.'גלו להם על המקום החדש לקנות ולמכור פרטי ילדים.'
            ),
            array(
                'id'=>'published_ad_notification_title',
                'type' => 'text',
                'title' => __('Published Ad Notification Title', 'outfit-standalone'),
                'default' => 'מושלם ! המודעה שלך אושרה.'
            ),
            array(
                'id'=>'published_ad_notification_text',
                'type' => 'textarea',
                'title' => __('Published Ad Notification Text', 'outfit-standalone'),
                'default' => 'אנחנו שמחים להודיע לך שהמודעה שלך אושרה והיא מופיעה באתר.
לפעמים (ולא לעיתים קרובות) אנחנו משנים דברים קטנים במודעה לפני פרסומה, בעיקר כדי שיהיה קל למחפשים אחרים למצוא אותה בקלות.
היכנס לראות כיצד פורסמה מודעתך כאן:'
            ),
            array(
                'id'=>'password_reset_title',
                'type' => 'text',
                'title' => __('Password Reset Email Title', 'outfit-standalone'),
                'default' => 'הסיסמה השתנתה.'
            ),
        )
    ) );

    /*
     * <--- END SECTIONS
     */


    /*
     *
     * YOU MUST PREFIX THE FUNCTIONS BELOW AND ACTION FUNCTION CALLS OR ANY OTHER CONFIG MAY OVERRIDE YOUR CODE.
     *
     */

    /*
    *
    * --> Action hook examples
    *
    */

    // If Redux is running as a plugin, this will remove the demo notice and links
	if( function_exists( 'remove_demo' ) ) {
		add_action( 'redux/loaded', 'remove_demo' );
	}
    // Function to test the compiler hook and demo CSS output.
    // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
    //add_filter('redux/options/' . $opt_name . '/compiler', 'compiler_action', 10, 3);

    // Change the arguments after they've been declared, but before the panel is created
    //add_filter('redux/options/' . $opt_name . '/outfit-maskirkol', 'change_arguments' );

    // Change the default value of a field after it's been set, but before it's been useds
    //add_filter('redux/options/' . $opt_name . '/defaults', 'change_defaults' );

    // Dynamically add a section. Can be also used to modify sections/fields
    //add_filter('redux/options/' . $opt_name . '/sections', 'dynamic_section');

    /**
     * This is a test function that will let you see when the compiler hook occurs.
     * It only runs if a field    set with compiler=>true is changed.
     * */
    if ( ! function_exists( 'compiler_action' ) ) {
        function compiler_action( $options, $css, $changed_values ) {
            echo '<h1>The compiler hook has run!</h1>';
            echo "<pre>";
            print_r( $changed_values ); // Values that have changed since the last save
            echo "</pre>";
            //print_r($options); //Option values
            //print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )
        }
    }

    /**
     * Custom function for the callback validation referenced above
     * */
    if ( ! function_exists( 'redux_validate_callback_function' ) ) {
        function redux_validate_callback_function( $field, $value, $existing_value ) {
            $error   = false;
            $warning = false;

            //do your validation
            if ( $value == 1 ) {
                $error = true;
                $value = $existing_value;
            } elseif ( $value == 2 ) {
                $warning = true;
                $value   = $existing_value;
            }

            $return['value'] = $value;

            if ( $error == true ) {
                $return['error'] = $field;
                $field['msg']    = 'your custom error message';
            }

            if ( $warning == true ) {
                $return['warning'] = $field;
                $field['msg']      = 'your custom warning message';
            }

            return $return;
        }
    }

    /**
     * Custom function for the callback referenced above
     */
    if ( ! function_exists( 'redux_my_custom_field' ) ) {
        function redux_my_custom_field( $field, $value ) {
            print_r( $field );
            echo '<br/>';
            print_r( $value );
        }
    }

    /**
     * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
     * Simply include this function in the child themes functions.php file.
     * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
     * so you must use get_template_directory_uri() if you want to use any of the built in icons
     * */
    if ( ! function_exists( 'dynamic_section' ) ) {
        function dynamic_section( $sections ) {
            //$sections = array();
            $sections[] = array(
                'title'  => __( 'Section via hook', 'outfit-standalone' ),
                'desc'   => __( '<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'outfit-standalone' ),
                'icon'   => 'el el-paper-clip',
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array()
            );

            return $sections;
        }
    }

    /**
     * Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.
     * */
    if ( ! function_exists( 'change_arguments' ) ) {
        function change_arguments( $args ) {
            //$args['dev_mode'] = true;

            return $args;
        }
    }

    /**
     * Filter hook for filtering the default value of any given field. Very useful in development mode.
     * */
    if ( ! function_exists( 'change_defaults' ) ) {
        function change_defaults( $defaults ) {
            $defaults['str_replace'] = 'Testing filter hook!';

            return $defaults;
        }
    }