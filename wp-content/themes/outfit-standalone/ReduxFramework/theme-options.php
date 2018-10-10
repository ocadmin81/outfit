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
        'menu_title'           => __( 'Outfit Options', 'classiera' ),
        'page_title'           => __( 'Outfit Options', 'classiera' ),
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


	



    Redux::setSection( $opt_name, array(
        'title'            => __( 'Submit Ads - Edit Ads', 'classiera' ),
        'id'               => 'classiera_submit_ad',
		'subsection' => true,
        'customizer_width' => '500px',
        'icon'             => 'el el-arrow-up',
		'desc' => __('From here you can manage your Submit Ad / Post Ad form.', 'classiera'),
		'fields'     => array(
			array(
				'id' => 'post-options-on',
				'type' => 'switch',
				'title' => __('Post moderation', 'classiera'),
				'subtitle' => __('Post moderation', 'classiera'),
				'default' => 1,
			),
			array(
				'id' => 'post-options-edit-on',
				'type' => 'switch',
				'title' => __('Post moderation On every edit post', 'classiera'),
				'subtitle' => __('Post moderation On every edit post', 'classiera'),
				'default' => 1,
			),
			
			array(
				'id' => 'phoneon',
				'type' => 'switch',
				'title' => __('Asking Phone Number on Post New Ads', 'classiera'),
				'subtitle' => __('If you dont want to ask phone number then just Turn OFF this.', 'classiera'),
				'default' => 1,
            ),
			array(
				'id' => 'classiera_ads_type',
				'type' => 'switch',
				'title' => __('Ads Type', 'classiera'),
				'subtitle' => __('Turn On/OFF Ads Type', 'classiera'),
				'desc' => __('Ads type: Buy, Sell, Rent, Hire', 'classiera'),
				'default' => 1,
            ),
			array(
				'id' => 'classiera_ads_type_show',
				'type' => 'checkbox',
				'title' => __('Which Ads type you want to use?', 'classiera'),
				'desc' => __('From here you need to select which ads type you want to show.(Buy, Sell, Rent, Hire)', 'classiera'),
				'options' => array(
					'1' => __('I want to sell?', 'classiera'),
					'2' => __('I want to buy?', 'classiera'),
					'3' => __('I want to rent?', 'classiera'),
					'4' => __('I want to hire?', 'classiera'),
					'5' => __('Lost and found?', 'classiera'),
					'6' => __('I give for free.', 'classiera'),
					'7' => __('I am an event', 'classiera'),
					'8' => __('Professional service.', 'classiera'),
				),
				'default' => array(
					'1' => '1',
					'2' => '1',
					'3' => '1',
					'4' => '1',
					'5' => '1',
					'6' => '1',
					'7' => '1',
					'8' => '1',
				),
			),
			array(
				'id' => 'adpost-condition',
				'type' => 'switch',
				'title' => __('Item Condition', 'classiera'),
				'subtitle' => __('Turn On/OFF Item Condition from Ads Post', 'classiera'),
				'desc' => __('If You dont want to use Item Condition at Submit Page Then Turn OFF this Option.', 'classiera'),
				'default' => 1,
            ),
			array(
				'id' => 'location_states_on',
				'type' => 'switch',
				'title' => __('Locations States On/OFF', 'classiera'),
				'subtitle' => __('Locations States On/OFF', 'classiera'),
				'desc' => __('This option will only work when you are not using City dropdown.', 'classiera'),
				'default' => 1,
            ),			
			array(
				'id' => 'location_city_on',
				'type' => 'switch',
				'title' => __('Locations City On/OFF', 'classiera'),
				'subtitle' => __('Locations City On/OFF', 'classiera'),
				'desc' => __('If you dont want to use city dropdown then turn off this .', 'classiera'),
				'default' => 1,
            ),
			array(
				'id' => 'classiera_address_field_on',
				'type' => 'switch',
				'title' => __('Address Field On/OFF', 'classiera'),
				'subtitle' => __('Address Field On/OFF', 'classiera'),
				'desc' => __('If you dont want to use Address Field then turn off this .', 'classiera'),
				'default' => 1,
            ),
			array(
				'id' => 'google-lat-long',
				'type' => 'switch',
				'title' => __('Latitude and Longitude', 'classiera'),
				'subtitle' => __('Turn On/OFF Latitude and Longitude from Ads Post', 'classiera'),
				'desc' => __('If You dont want user put Latitude and Longitude while posting ads then just turn OFF this option.', 'classiera'),
				'default' => 1,
            ),
			array(
				'id' => 'google-map-adpost',
				'type' => 'switch',
				'title' => __('Google MAP on Post Ads', 'classiera'),
				'subtitle' => __('Turn On/OFF Google MAP from Ads Post', 'classiera'),
				'desc' => __('If You want to hide Google MAP from Submit Ads Page And Single Ads Page Then Turn OFF this Option.', 'classiera'),
				'default' => 1,
            ),
			array(
				'id' => 'classiera_ad_location_remove',
				'type' => 'switch',
				'title' => __('Ad Location Section', 'classiera'),
				'subtitle' => __('Ad Location On/OFF', 'classiera'),
				'desc' => __('If you want remove Ad Locations section completely then Turn Off this option, It will remove Country, States,City, Address, Google Latitude, Google Longitude and Google MAP Option.', 'classiera'),
				'default' => 1,
            ),
			array(
				'id' => 'classiera_video_postads',
				'type' => 'switch',
				'title' => __('Video Box on Post Ads', 'classiera'),
				'subtitle' => __('Turn On/OFF Video Box on Post Ads', 'classiera'),
				'desc' => __('If you dont want to allow users to add video iframe or link in ads then just turn off this option', 'classiera'),
				'default' => 1,
            ),			
			array(
				'id' => 'regularpriceon',
				'type' => 'switch',
				'title' => __('Regular Price Tab on Post New Ads', 'classiera'),
				'subtitle' => __('Regular Price Tab on Post New Ads', 'classiera'),
				'default' => 1,
            ),
			array(
				'id' => 'classiera_sale_price_off',
				'type' => 'switch',
				'title' => __('Price section', 'classiera'),
				'subtitle' => __('Price Tab on Post New Ads', 'classiera'),
				'desc' => __('If you want to hide price section completely then please turn off this option.', 'classiera'),
				'default' => 1,
            ),
			array(
				'id'=>'classiera_multi_currency',
				'type' => 'button_set',
				'title' => __('Select Currency', 'classiera'),
				'subtitle' => __('Ads Posts', 'classiera'),
				'options' => array('multi' => 'Multi Currency', 'single' => 'Single Currency'),
				'desc' => __('If you want to run your website only in one country then just select Single Currency. If you will select Multi Currency then On Submit Ad Page there will be a dropdown from where user can select currency tag.', 'classiera'),
				'default' =>'multi',
			),
			array(
				'id'=>'classiera_multi_currency_default',
				'type' => 'select',
				'title' => __('Currency Tag', 'classiera'),
				'subtitle' => __('Currency Tag', 'classiera'),
				'desc' => __('Select default selected currency in dropdown', 'classiera'),
				'options' => array(
					'USD' => 'US Dollar', 
					'CAD' => 'Canadian Dollar',
					'EUR' => 'Euro',
					'AED' =>'United Arab Emirates Dirham',
					'AFN' => 'Afghan Afghani',
					'ALL' => 'Albanian Lek',
					'AMD' => 'Armenian Dram',
					'ARS' => 'Argentine Peso',
					'AUD' => 'Australian Dollar',
					'AZN' => 'Azerbaijani Manat',
					'BDT' => 'Bangladeshi Taka',
					'BGN' => 'Bulgarian Lev',
					'BHD' => 'Bahraini Dinar',
					'BND' => 'Brunei Dollar',
					'BOB' => 'Bolivian Boliviano',
					'BRL' => 'Brazilian Real',
					'BWP' => 'Botswanan Pula',
					'BYN' => 'Belarusian Ruble',
					'BZD' => 'Belize Dollar',
					'CHF' => 'Swiss Franc',
					'CLP' => 'Chilean Peso',
					'CNY' => 'Chinese Yuan',
					'COP' => 'Colombian Peso',
					'CRC' => 'Costa Rican Colón',
					'CVE' => 'Cape Verdean Escudo',
					'CZK' => 'Czech Republic Koruna',
					'DJF' => 'Djiboutian Franc',
					'DKK' => 'Danish Krone',
					'DOP' => 'Dominican Peso',
					'DZD' => 'Algerian Dinar',
					'EGP' => 'Egyptian Pound',
					'ERN' => 'Eritrean Nakfa',
					'ETB' => 'Ethiopian Birr',
					'GBP' => 'British Pound',
					'‎GEL' => 'Georgian Lari',
					'GHS' => 'Ghanaian Cedi',
					'GTQ' => 'Guatemalan Quetzal',
					'GMB' => 'Gambia Dalasi',
					'HKD' => 'Hong Kong Dollar',
					'HNL' => 'Honduran Lempira',
					'HRK' => 'Croatian Kuna',
					'HUF' => 'Hungarian Forint',
					'IDR' => 'Indonesian Rupiah',
					'ILS' => 'Israeli SheKel',
					'INR' => 'Indian Rupee',
					'IQD' => 'Iraqi Dinar',
					'IRR' => 'Iranian Rial',
					'ISK' => 'Icelandic Króna',
					'JMD' => 'Jamaican Dollar',
					'JOD' => 'Jordanian Dinar',
					'JPY' => 'Japanese Yen',
					'KES' => 'Kenyan Shilling',
					'KHR' => 'Cambodian Riel',
					'KMF' => 'Comorian Franc',
					'KRW' => 'South Korean Won',
					'KWD' => 'Kuwaiti Dinar',
					'KZT' => 'Kazakhstani Tenge',
					'KM' => 'Konvertibilna Marka',
					'LBP' => 'Lebanese Pound',
					'LKR' => 'Sri Lankan Rupee',
					'LTL' => 'Lithuanian Litas',
					'LVL' => 'Latvian Lats',
					'LYD' => 'Libyan Dinar',
					'MAD' => 'Moroccan Dirham',
					'MDL' => 'Moldovan Leu',
					'MGA' => 'Malagasy Ariary',
					'MKD' => 'Macedonian Denar',
					'MMK' => 'Myanma Kyat',
					'HKD' => 'Macanese Pataca',
					'MUR' => 'Mauritian Rupee',
					'MXN' => 'Mexican Peso',
					'MYR' => 'Malaysian Ringgit',
					'MZN' => 'Mozambican Metical',
					'NAD' => 'Namibian Dollar',
					'NGN' => 'Nigerian Naira',
					'NIO' => 'Nicaraguan Córdoba',
					'NOK' => 'Norwegian Krone',
					'NPR' => 'Nepalese Rupee',
					'NZD' => 'New Zealand Dollar',
					'OMR' => 'Omani Rial',
					'‎PAB' => 'Panamanian Balboa',
					'PEN' => 'Peruvian Nuevo Sol',
					'PHP' => 'Philippine Peso',
					'PKR' => 'Pakistani Rupee',
					'PLN' => 'Polish Zloty',
					'PYG' => 'Paraguayan Guarani',
					'QAR' => 'Qatari Rial',
					'RON' => 'Romanian Leu',
					'RSD' => 'Serbian Dinar',
					'RUB' => 'Russian Ruble',
					'RWF' => 'Rwandan Franc',
					'SAR' => 'Saudi Riyal',
					'SDG' => 'Sudanese Pound',
					'SEK' => 'Swedish Krona',
					'SGD' => 'Singapore Dollar',
					'SOS' => 'Somali Shilling',
					'SYP' => 'Syrian Pound',
					'THB' => 'Thai Baht',
					'TND' => 'Tunisian Dinar',
					'TOP' => 'Tongan Paʻanga',
					'TRY' => 'Turkish Lira',
					'TTD' => 'Trinidad and Tobago Dollar',
					'TWD' => 'New Taiwan Dollar',
					'UAH' => 'Ukrainian Hryvnia',
					'UGX' => 'Ugandan Shilling',
					'UYU' => 'Uruguayan Peso',
					'UZS' => 'Uzbekistan Som',
					'VEF' => 'Venezuelan Bolívar',
					'VND' => 'Vietnamese Dong',
					'YER' => 'Yemeni Rial',
					'ZAR' => 'South African Rand',
					'FCFA' => 'CFA Franc BEAC',
					'TZS' => 'Tanzanian Shillings',
					'SRD' => 'Surinamese dollar',
					'ZMK' => 'Zambian Kwacha'),
				'default' => 'USD',
				'required' => array( 'classiera_multi_currency', '=', 'multi' )
			),
			array(
				'id'=>'classierapostcurrency',
				'type' => 'text',
				'title' => __('Currency Tag', 'classiera'),
				'subtitle' => __('Currency Tag', 'classiera'),
				'desc' => __('Put Your Own Currency Symbol or HTML code to display', 'classiera'),
				'default' => '$',
				'required' => array( 'classiera_multi_currency', '=', 'single' )
			),
			array(
				'id'=>'classiera_currency_left_right',
				'type' => 'radio',
				'title' => __('Currency Tag Display', 'classiera'), 
				'subtitle' => __('Select option', 'classiera'),
				'desc' => __('You want to show currency tag on left or right with post price??', 'classiera'),
				'options' => array('left' => 'Left', 'right' => 'Right'),//Must provide key => value pairs for radio options
				'default' => 'left'
			),
			array(
				'id'=>'classiera_categories_noprice',
				'type' => 'select',
				'data' => 'categories',
				'args' => array(
					'orderby' => 'name',
					'hide_empty' => 0,
				),
				'multi'    => true,				
				'title' => __('Select Categories to hide price', 'classiera'), 
				'subtitle' => __('Hide Price for categories', 'classiera'),
				'desc' => __('Please select categories in which you dont want to use price section, then price section will be hide for that categories', 'classiera'),
				'default' => 1,
			),
		)
    ) );
    // -> START Editors
    Redux::setSection( $opt_name, array(
        'title'            => __( 'Pages', 'classiera' ),
        'id'               => 'pages',
        'customizer_width' => '500px',
        'icon'             => 'el-icon-website',
		'fields'     => array(
			array(
				'id'=>'login',
				'type' => 'text',
				'title' => __('Login Page URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting Login template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'register',
				'type' => 'text',
				'title' => __('Register Page URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting Register template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'profile',
				'type' => 'text',
				'title' => __('Profile Page URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting Profile template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'edit',
				'type' => 'text',
				'title' => __('Profile Settings Page URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting Profile template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'new_post',
				'type' => 'text',
				'title' => __('Submit Ad Page URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting Submit template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'edit_post',
				'type' => 'text',
				'title' => __('Edit Ad Page URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting Edit Ad template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'featured_plans',
				'type' => 'text',
				'title' => __('Pricing Plans Page URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting Pricing Plan template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'all-cat-page-link',
				'type' => 'text',
				'title' => __('All Categories Page URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting All Categories template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'all-ads-page-link',
				'type' => 'text',
				'title' => __('All Ads Page URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting All Ads template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'all-ads',
				'type' => 'text',
				'title' => __('Single User All Ads', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting Single User All Ads template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'classiera_single_user_plans',
				'type' => 'text',
				'title' => __('Single User Plans', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting Single User All Plans template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'classiera_inbox_page_url',
				'type' => 'text',
				'title' => __('Inbox Page URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting inbox template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'all-favourite',
				'type' => 'text',
				'title' => __('Single User All Favorite', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting Favorite Ads template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'classiera_user_follow',
				'type' => 'text',
				'title' => __('Single User All Follower', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting Follow template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'reset',
				'type' => 'text',
				'title' => __('Reset Password Page URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Create page by selecting Reset Password Page template and insert url here.', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'termsandcondition',
				'type' => 'text',
				'title' => __('Terms And Conditions URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('This Link will be shown at registration page', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'classiera_cart_url',
				'type' => 'text',
				'title' => __('WooCommerece Cart URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Please Put Woo Commerce URL here', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
		)
    ) );
    // -> START Color Selection
    Redux::setSection( $opt_name, array(
        'title' => __( 'Color Selection', 'classiera' ),
        'id'    => 'color',
        'desc'  => __( 'Color Selection', 'classiera' ),
        'icon'  => 'el el-brush',
		'fields' => array(
			array(
				'id'       => 'color-primary',
				'type'     => 'color',
				'title'    => __('Primary Color', 'classiera'), 
				'subtitle' => __('Pick a Primary Color default: #e96969.', 'classiera'),
				'default'  => '#b6d91a',
				'validate' => 'color',
				'transparent' => false,
			),
			array(
				'id'       => 'color-secondary',
				'type'     => 'color',
				'title'    => __('Secondary Color', 'classiera'), 
				'subtitle' => __('Pick a Secondary Color default: #232323.', 'classiera'),
				'default'  => '#232323',
				'validate' => 'color',
				'transparent' => false,
			),
			array(
				'id'       => 'classiera_topbar_bg',
				'type'     => 'color',
				'title'    => __('Topbar Background Color', 'classiera'), 
				'subtitle' => __('Pick a color for topbar Background default: #444444.', 'classiera'),
				'default'  => '#444444',
				'validate' => 'color',
				'transparent' => false,
			),
			array(
				'id'       => 'classiera_navbar_color',
				'type'     => 'color',
				'title'    => __('Navbar Background Color', 'classiera'), 
				'subtitle' => __('Pick a color for Navbar Background default: #fafafa.', 'classiera'),
				'default'  => '#fafafa',
				'validate' => 'color',
				'transparent' => true,
			),
			array(
				'id'       => 'classiera_navbar_text_color',
				'type'     => 'color',
				'title'    => __('Navbar Text Color', 'classiera'), 
				'subtitle' => __('Pick a color for Navbar Text default: #444444.', 'classiera'),
				'default'  => '#444444',
				'validate' => 'color',
				'transparent' => false,
			),
			array(
				'id'       => 'classiera_footer_tags_bg',
				'type'     => 'color',
				'title'    => __('Footer Tags Background Color', 'classiera'), 
				'subtitle' => __('Pick a color for Footer tags Background default: #444444.', 'classiera'),
				'default'  => '#444444',
				'validate' => 'color',
				'transparent' => false,
			),
			array(
				'id'       => 'classiera_footer_tags_txt',
				'type'     => 'color',
				'title'    => __('Footer Tags Text Color', 'classiera'), 
				'subtitle' => __('Pick a color for Footer tags Text default: #ffffff.', 'classiera'),
				'default'  => '#ffffff',
				'validate' => 'color',
				'transparent' => false,
			),
			array(
				'id'       => 'classiera_footer_tags_txt_hover',
				'type'     => 'color',
				'title'    => __('Footer Tags Hover Text Color', 'classiera'), 
				'subtitle' => __('Pick a color for Footer tags Hover Text default: #ffffff.', 'classiera'),
				'default'  => '#ffffff',
				'validate' => 'color',
				'transparent' => false,
			),
			array(
				'id'       => 'classiera_footer_txt',
				'type'     => 'color',
				'title'    => __('Footer text widget Text Color', 'classiera'), 
				'subtitle' => __('Pick a color for Footer text widget Text default: #aaaaaa.', 'classiera'),
				'default'  => '#aaaaaa',
				'validate' => 'color',
				'transparent' => false,
			),
			array(
				'id'       => 'classiera_footer_bottom_bg',
				'type'     => 'color',
				'title'    => __('Footer Bottom Background Color', 'classiera'), 
				'subtitle' => __('Pick a color for Footer Bottom Background default: #444444.', 'classiera'),
				'default'  => '#444444',
				'validate' => 'color',
				'transparent' => false,
			),
			array(
				'id'       => 'classiera_footer_bottom_txt',
				'type'     => 'color',
				'title'    => __('Footer Bottom Text Color', 'classiera'), 
				'subtitle' => __('Pick a color for Footer Bottom text default: #8e8e8e.', 'classiera'),
				'default'  => '#8e8e8e',
				'validate' => 'color',
				'transparent' => false,
			),
		)
    ) );

    // -> START Design Fields
    Redux::setSection( $opt_name, array(
        'title' => __( 'Social Links', 'classiera' ),
        'id'    => 'social-links',
        'desc'  => __( 'Put Social Links', 'classiera' ),
        'icon'  => 'el el-glasses',
		'fields' => array(
			array(
			'id'=>'facebook-link',
			'type' => 'text',
			'title' => __('Facebook Page URL', 'classiera'),
			'subtitle' => __('This must be an URL.', 'classiera'),
			'desc' => __('Facebook Page URL', 'classiera'),
			'validate' => 'url',
			'default' => ''
			),

		array(
			'id'=>'twitter-link',
			'type' => 'text',
			'title' => __('Twitter Page URL', 'classiera'),
			'subtitle' => __('This must be an URL.', 'classiera'),
			'desc' => __('Twitter Page URL', 'classiera'),
			'validate' => 'url',
			'default' => ''
			),

		array(
			'id'=>'dribbble-link',
			'type' => 'text',
			'title' => __('Dribbble Page URL', 'classiera'),
			'subtitle' => __('This must be an URL.', 'classiera'),
			'desc' => __('Dribbble Page URL', 'classiera'),
			'validate' => 'url',
			'default' => ''
			),

		array(
			'id'=>'flickr-link',
			'type' => 'text',
			'title' => __('Flickr Page URL', 'classiera'),
			'subtitle' => __('This must be an URL.', 'classiera'),
			'desc' => __('Flickr Page URL', 'classiera'),
			'validate' => 'url',
			'default' => ''
			),

		array(
			'id'=>'github-link',
			'type' => 'text',
			'title' => __('Github Page URL', 'classiera'),
			'subtitle' => __('This must be an URL.', 'classiera'),
			'desc' => __('Github Page URL', 'classiera'),
			'validate' => 'url',
			'default' => ''
			),

		array(
			'id'=>'pinterest-link',
			'type' => 'text',
			'title' => __('Pinterest Page URL', 'classiera'),
			'subtitle' => __('This must be an URL.', 'classiera'),
			'desc' => __('Pinterest Page URL', 'classiera'),
			'validate' => 'url',
			'default' => ''
			),

		array(
			'id'=>'youtube-link',
			'type' => 'text',
			'title' => __('Youtube Page URL', 'classiera'),
			'subtitle' => __('This must be an URL.', 'classiera'),
			'desc' => __('Youtube Page URL', 'classiera'),
			'validate' => 'url',
			'default' => ''
			),

		array(
			'id'=>'google-plus-link',
			'type' => 'text',
			'title' => __('Google+ Page URL', 'classiera'),
			'subtitle' => __('This must be an URL.', 'classiera'),
			'desc' => __('Google+ Page URL', 'classiera'),
			'validate' => 'url',
			'default' => ''
			),

		array(
			'id'=>'linkedin-link',
			'type' => 'text',
			'title' => __('LinkedIn Page URL', 'classiera'),
			'subtitle' => __('This must be an URL.', 'classiera'),
			'desc' => __('LinkedIn Page URL', 'classiera'),
			'validate' => 'url',
			'default' => ''
			),

		array(
			'id'=>'instagram-link',
			'type' => 'text',
			'title' => __('Instagram Page URL', 'classiera'),
			'subtitle' => __('This must be an URL.', 'classiera'),
			'desc' => __('Instagram Page URL', 'classiera'),
			'validate' => 'url',
			'default' => ''
			),

		array(
			'id'=>'vimeo-link',
			'type' => 'text',
			'title' => __('Vimeo Page URL', 'classiera'),
			'subtitle' => __('This must be an URL.', 'classiera'),
			'desc' => __('Vimeo Page URL', 'classiera'),
			'validate' => 'url',
			'default' => ''
			),
		
		)
    ) );


    // -> START Media Uploads
    Redux::setSection( $opt_name, array(
        'title' => __( 'Advertisement', 'classiera' ),
        'id'    => 'advertisement',
        'desc'  => __( 'Advertisement Section, If you want to use image ads then please upload banner image and put website URL, but if you want to use google ads then images ads option must need to be empty.', 'classiera' ),
        'icon'  => 'el el-picture',
    ) );
	Redux::setSection( $opt_name, array(
        'title'            => __( 'Home Page Ads', 'classiera' ),
        'id'               => 'home-page-ads',
		'icon'             => 'el el-home-alt',
		'subsection' => true,
        'customizer_width' => '500px',        
		'desc'=> __('Put HomePage Ads Here.', 'classiera'),
        'fields'           => array( 
			array(
				'id'=>'home_ad1',
				'type' => 'media', 
				'url'=> true,
				'title' => __('Home Page First banner Image Ads', 'classiera'),
				'compiler' => 'true',
				//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
				'desc'=> __('Upload your Ad Image.', 'classiera'),
				'subtitle' => __('Ad Image', 'classiera'),
				'default'=>array('url'=>''),
			),
			array(
				'id'=>'home_ad1_url',
				'type' => 'text',
				'title' => __('Home Page First banner Image URL', 'classiera'),
				'subtitle' => __('link URL', 'classiera'),
				'desc' => __('You can add URL here so when user will click on that image then user will goes to that link.', 'classiera'),
				'default' => '',
				'validate' => 'url',
			),
			array(
				'id'=>'home_html_ad',
				'type' => 'textarea',
				'title' => __('HTML Ads Or Google Ads', 'classiera'),
				'subtitle' => __('HTML ads for HomePage', 'classiera'),
				'desc' => __('Put your HTML or Google Ads Code for First banner here.', 'classiera'),
				'default' => ''
			),
			array(
				'id'=>'classiera_home_banner_2',
				'type' => 'media', 
				'url'=> true,
				'title' => __('Home Page second banner Image Ads', 'classiera'),
				'compiler' => 'true',
				//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
				'desc'=> __('Upload your Ad Image.', 'classiera'),
				'subtitle' => __('Ad Image', 'classiera'),
				'default'=>array('url'=>''),
			),
			array(
				'id'=>'classiera_home_banner_2_url',
				'type' => 'text',
				'title' => __('Home Page second banner Image URL', 'classiera'),
				'subtitle' => __('link URL', 'classiera'),
				'desc' => __('You can add URL here so when user will click on that image then user will goes to that link.', 'classiera'),
				'default' => '',
				'validate' => 'url',
			),
			array(
				'id'=>'classiera_home_banner_2_html',
				'type' => 'textarea',
				'title' => __('HTML Ads Or Google Ads', 'classiera'),
				'subtitle' => __('HTML ads for HomePage', 'classiera'),
				'desc' => __('Put your HTML or Google Ads Code for second banner on home here.', 'classiera'),
				'default' => ''
			),
			array(
				'id'=>'classiera_home_banner_3',
				'type' => 'media', 
				'url'=> true,
				'title' => __('Home Page third banner Image Ads', 'classiera'),
				'compiler' => 'true',
				//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
				'desc'=> __('Upload your Ad Image.', 'classiera'),
				'subtitle' => __('Ad Image', 'classiera'),
				'default'=>array('url'=>''),
			),
			array(
				'id'=>'classiera_home_banner_3_url',
				'type' => 'text',
				'title' => __('Home Page third banner Image URL', 'classiera'),
				'subtitle' => __('link URL', 'classiera'),
				'desc' => __('You can add URL here so when user will click on that image then user will goes to that link.', 'classiera'),
				'default' => '',
				'validate' => 'url',
			),
			array(
				'id'=>'classiera_home_banner_3_html',
				'type' => 'textarea',
				'title' => __('HTML Ads Or Google Ads', 'classiera'),
				'subtitle' => __('HTML ads for HomePage', 'classiera'),
				'desc' => __('Put your HTML or Google Ads Code for third banner on home here.', 'classiera'),
				'default' => ''
			),
		)
    ) );
	Redux::setSection( $opt_name, array(
        'title'      => __( 'Other Ads', 'classiera' ),
        'id'         => 'advertisement-other',
		'desc'  => __( 'If you want to use image ads then please upload banner image and put website URL, but if you want to use google ads then images ads option must need to be empty.', 'classiera' ),
		'icon'  => 'el el-picture',
        'subsection' => true,
        'fields'     => array(
			array(
				'id'=>'home_ad2',
				'type' => 'media', 
				'url'=> true,
				'title' => __('Single Post Page Ad img', 'classiera'),
				'compiler' => 'true',
				//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
				'desc'=> __('Upload your Ad Image.', 'classiera'),
				'subtitle' => __('Single Post Page Ad img', 'classiera'),
				'default'=>array('url'=>''),
			),
			array(
				'id'=>'home_ad2_url',
				'type' => 'text',
				'title' => __('Single Post Ad link URL', 'classiera'),
				'subtitle' => __('Single Post Ad link URL', 'classiera'),
				'desc' => __('You can add URL here so when user will click on that image then user will goes to that link.', 'classiera'),
				'default' => '',
				'validate' => 'url',
			),
			array(
				'id'=>'home_html_ad2',
				'type' => 'textarea',
				'title' => __('HTML Ads Or Google Ads for Single Post', 'classiera'),
				'subtitle' => __('Google ads', 'classiera'),
				'desc' => __('Put your HTML or Google Ads Code here.', 'classiera'),
				'default' => ''
			),	
			array(
				'id'=>'post_ad',
				'type' => 'media', 
				'url'=> true,
				'title' => __(' Location & category page Ad', 'classiera'),
				'compiler' => 'true',
				//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
				'desc'=> __('Upload your Ad Image.', 'classiera'),
				'subtitle' => __('Upload your Ad Image', 'classiera'),
				'default'=>array('url'=>''),
			),
			array(
				'id'=>'post_ad_url',
				'type' => 'text',
				'title' => __('Location & category page Ad link URL', 'classiera'),
				'subtitle' => __('Ad link URL', 'classiera'),
				'desc' => __('You can add URL.', 'classiera'),
				'default' => '',
				'validate' => 'url',
			),
			array(
				'id'=>'post_ad_code_html',
				'type' => 'textarea',
				'title' => __('HTML or Google ads (Location & category page)', 'classiera'),
				'subtitle' => __('Google ads', 'classiera'),
				'desc' => __('Put your HTML or Google Ads Code here.', 'classiera'),
				'default' => ''
			),
        )
    ) );
    // -> START Switch & Button Set
    Redux::setSection( $opt_name, array(
        'title' => __( 'Partners', 'classiera' ),
        'id'    => 'partners',
        'desc'  => __( 'Upload Partners Logos', 'classiera' ),
        'icon'  => 'el el-group',
		'fields' => array(
			array(
				'id' => 'partners-on',
				'type' => 'switch',
				'title' => __('Partners Slider', 'classiera'),
				'subtitle' => __('Turn On/OFF', 'classiera'),
				'desc' => __('This setting will work only on inner pages, If you want to turn OFF from HomePage then Please visit LayOut Manager tab.', 'classiera'),
				'default' => 1,
            ),
			array(
				'id'=>'classiera_partners_style',
				'type' => 'radio',
				'title' => __('Partners Styles', 'classiera'), 
				'subtitle' => __('Select Styles', 'classiera'),
				'desc' => __('Selection Will work on all pages other then homepage, on homepage Design will be used from template', 'classiera'),
				'options' => array('1' => 'Version 1', '2' => 'Version 2', '3' => 'Version 3', '4' => 'Version 4', '5' => 'Version 5', '6' => 'Version 6'),//Must provide key => value pairs for radio options
				'default' => '1'
			),
			array(
				'id'=>'classiera_partners_title',
				'type' => 'text',
				'title' => __('Partner Title', 'classiera'),
				'subtitle' => __('Partner Title', 'classiera'),
				'desc' => __('Partner Sections Title.', 'classiera'),
				'default' => 'See Our Featured Members'
			),
			array(
				'id'=>'classiera_partners_desc',
				'type' => 'text',
				'title' => __('Partner Description', 'classiera'),
				'subtitle' => __('Partner Description', 'classiera'),
				'desc' => __('Replace Your Partner Section Description', 'classiera'),
				'default' => 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour.'
			),
			array(
				'id'=>'partner1',
				'type' => 'media', 
				'url'=> true,
				'title' => __('Partner One', 'classiera'),
				'compiler' => 'true',
				//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
				'desc'=> __('Upload your logo.', 'classiera'),
				'subtitle' => __('Upload your logo', 'classiera'),
				'default'=>array('url'=>''),
			),
			array(
				'id'=>'partner1-url',
				'type' => 'text',
				'title' => __('Partner One URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Partner One URL', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'partner2',
				'type' => 'media', 
				'url'=> true,
				'title' => __('Partner two', 'classiera'),
				'compiler' => 'true',
				//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
				'desc'=> __('Upload your logo.', 'classiera'),
				'subtitle' => __('Upload your logo', 'classiera'),
				'default'=>array('url'=>''),
			),
			array(
				'id'=>'partner2-url',
				'type' => 'text',
				'title' => __('Partner two URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Partner two URL', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),	
			array(
				'id'=>'partner3',
				'type' => 'media', 
				'url'=> true,
				'title' => __('Partner three', 'classiera'),
				'compiler' => 'true',
				//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
				'desc'=> __('Upload your logo.', 'classiera'),
				'subtitle' => __('Upload your logo', 'classiera'),
				'default'=>array('url'=>''),
			),
			array(
				'id'=>'partner3-url',
				'type' => 'text',
				'title' => __('Partner three URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Partner three URL', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'partner4',
				'type' => 'media', 
				'url'=> true,
				'title' => __('Partner four', 'classiera'),
				'compiler' => 'true',
				//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
				'desc'=> __('Upload your logo.', 'classiera'),
				'subtitle' => __('Upload your logo', 'classiera'),
				'default'=>array('url'=>''),
			),
			array(
				'id'=>'partner4-url',
				'type' => 'text',
				'title' => __('Partner four URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Partner four URL', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),	
			array(
				'id'=>'partner5',
				'type' => 'media', 
				'url'=> true,
				'title' => __('Partner five', 'classiera'),
				'compiler' => 'true',
				//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
				'desc'=> __('Upload your logo.', 'classiera'),
				'subtitle' => __('Upload your logo', 'classiera'),
				'default'=>array('url'=>''),
			),
			array(
				'id'=>'partner5-url',
				'type' => 'text',
				'title' => __('Partner five URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Partner five URL', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),	
			array(
				'id'=>'partner6',
				'type' => 'media', 
				'url'=> true,
				'title' => __('Partner six', 'classiera'),
				'compiler' => 'true',
				//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
				'desc'=> __('Upload your logo.', 'classiera'),
				'subtitle' => __('Upload your logo', 'classiera'),
				'default'=>array('url'=>''),
			),
			array(
				'id'=>'partner6-url',
				'type' => 'text',
				'title' => __('Partner six URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Partner six URL', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'partner7',
				'type' => 'media', 
				'url'=> true,
				'title' => __('Partner seven', 'classiera'),
				'compiler' => 'true',
				//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
				'desc'=> __('Upload your logo.', 'classiera'),
				'subtitle' => __('Upload your logo', 'classiera'),
				'default'=>array('url'=>''),
			),
			array(
				'id'=>'partner7-url',
				'type' => 'text',
				'title' => __('Partner seven URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Partner seven URL', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
			array(
				'id'=>'partner8',
				'type' => 'media', 
				'url'=> true,
				'title' => __('Partner eight', 'classiera'),
				'compiler' => 'true',
				//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
				'desc'=> __('Upload your logo.', 'classiera'),
				'subtitle' => __('Upload your logo', 'classiera'),
				'default'=>array('url'=>''),
			),
			array(
				'id'=>'partner8-url',
				'type' => 'text',
				'title' => __('Partner eight URL', 'classiera'),
				'subtitle' => __('This must be an URL.', 'classiera'),
				'desc' => __('Partner eight URL', 'classiera'),
				'validate' => 'url',
				'default' => ''
			),
		)
    ) );


    // -> START Select Fields
    Redux::setSection( $opt_name, array(
        'title' => __( 'Translate', 'classiera' ),
        'id'    => 'translate',
        'icon'  => 'el el-refresh',
		'fields' => array(		
			array(
				'id'=>'trns_new_post_posted',
				'type' => 'text',
				'title' => __('New Post Posted', 'classiera'),
				'subtitle' => __('Translate Text', 'classiera'),
				'desc' => __('Replace New Post Has been Posted', 'classiera'),
				'default' => 'New Post Has been Posted'
			),
			array(
				'id'=>'trns_welcome_user_title',
				'type' => 'text',
				'title' => __('Welcome Email Title', 'classiera'),
				'subtitle' => __('Translate Text', 'classiera'),
				'desc' => __('Replace Welcome Text', 'classiera'),
				'default' => 'Welcome To Classiera'
			),
			array(
				'id'=>'trns_listing_published',
				'type' => 'text',
				'title' => __('Published Post Notification', 'classiera'),
				'subtitle' => __('Translate Text', 'classiera'),
				'desc' => __('Replace Your Listing has been published', 'classiera'),
				'default' => 'Your Listing has been published!'
			)	
		)
    ) );    
    // -> START Fonts
    Redux::setSection( $opt_name, array(
        'title'  => __( 'Fonts', 'classiera' ),
        'id'     => 'Fonts',
        'desc' => __('Select Fonts for your Website', 'classiera'),
        'icon'   => 'el el-font',
        'fields' => array(           
            array(
            'id' => 'heading1-font',
            'type' => 'typography',
            'title' => __('H1 Font', 'classiera'),
            'subtitle' => __('Specify the headings font properties.', 'classiera'),
            'google' => true,
            'output' => array('h1, h1 a'),
            'default' => array(
                'color' => '#232323',
                'font-size' => '36px',
                'font-family' => 'ubuntu',
                'font-weight' => '700',
                'line-height' => '36px',
                ),
         	),

		array(
            'id' => 'heading2-font',
            'type' => 'typography',
            'title' => __('H2 Font', 'classiera'),
            'subtitle' => __('Specify the headings font properties.', 'classiera'),
            'google' => true,
            'output' => array('h2, h2 a, h2 span'),
            'default' => array(
                'color' => '#232323',
                'font-size' => '30px',
                'font-family' => 'ubuntu',
                'font-weight' => '700',
                'line-height' => '30px',
                ),
         	),

		array(
            'id' => 'heading3-font',
            'type' => 'typography',
            'title' => __('H3 Font', 'classiera'),
            'subtitle' => __('Specify the headings font properties.', 'classiera'),
            'google' => true,
            'output' => array('h3, h3 a, h3 span'),
            'default' => array(
                'color' => '#232323',
                'font-size' => '24px',
                'font-family' => 'ubuntu',
                'font-weight' => '700',
                'line-height' => '24px',
                ),
         	),

		array(
            'id' => 'heading4-font',
            'type' => 'typography',
            'title' => __('H4 Font', 'classiera'),
            'subtitle' => __('Specify the headings font properties.', 'classiera'),
            'google' => true,
            'output' => array('h4, h4 a, h4 span'),
            'default' => array(
				'color' => '#232323',
                'font-size' => '18px',
                'font-family' => 'ubuntu',
                'font-weight' => '700',
                'line-height' => '18px',
                ),
         	),

		array(
            'id' => 'heading5-font',
            'type' => 'typography',
            'title' => __('H5 Font', 'classiera'),
            'subtitle' => __('Specify the headings font properties.', 'classiera'),
            'google' => true,
            'output' => array('h5, h5 a, h5 span'),
            'default' => array(
                'color' => '#232323',
                'font-size' => '14px',
                'font-family' => 'ubuntu',
                'font-weight' => '600',
                'line-height' => '24px',
                ),
         	),

		array(
            'id' => 'heading6-font',
            'type' => 'typography',
            'title' => __('H6 Font', 'classiera'),
            'subtitle' => __('Specify the headings font properties.', 'classiera'),
            'google' => true,
            'output' => array('h6, h6 a, h6 span'),
            'default' => array(
                'color' => '#232323',
                'font-size' => '12px',
                'font-family' => 'ubuntu',
                'font-weight' => '600',
                'line-height' => '24px',
                ),
         	),

		array(
            'id' => 'body-font',
            'type' => 'typography',
            'title' => __('Body Font', 'classiera'),
            'subtitle' => __('Specify the body font properties.', 'classiera'),
            'google' => true,
            'output' => array('html, body, div, applet, object, iframe p, blockquote, a, abbr, acronym, address, big, cite, del, dfn, em, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td, article, aside, canvas, details, embed, figure, figcaption, footer, header, hgroup, menu, nav, output, ruby, section, summary, time, mark, audio, video'),
            'default' => array(
                'color' => '#6c6c6c',
                'font-size' => '14px',
                'font-family' => 'Lato',
                'font-weight' => 'Normal',
                'line-height' => '24px',
                'visibility' => 'inherit',
                ),
         	),
        )
    ) );    
	Redux::setSection( $opt_name, array(
        'title' => __( 'Google Settings', 'classiera' ),
        'icon'  => 'el el-map-marker',
        'id'    => 'google-map',
        'desc'  => __( 'Google Settings', 'classiera' ),        
        'fields' => array(
			array(
				'id'=>'classiera_map_post_type',
				'type' => 'radio',
				'title' => __('Select Ads type', 'classiera'),
				'subtitle' => __('Which ads you want to show??', 'classiera'),
				'desc' => __('Which type of ads you want to show on Google MAP on Home? On search result page all ads will be display.', 'classiera'),
				'options' => array('featured' => 'Featured / Premium', 'all' => 'All Ads (Regular & Premium)'),
				'default' => 'all'
			),
			array(
				'id'=>'classiera_map_post_count',
				'type' => 'text',
				'title' => __('How Many ads', 'classiera'),
				'subtitle' => __('Put a number', 'classiera'),
				'desc' => __('How many ads you want to show on Google MAP (MAP on header), On Search result page count will shown from search query.', 'classiera'),
				'default' => '12'
			),
			array(
				'id' => 'classiera_map_on_search',
				'type' => 'switch',
				'title' => __('Google MAP on Search Page', 'classiera'),
				'subtitle' => __('Turn Map On/OFF from Search result page', 'classiera'),
				'desc' => __('If you dont like Google MAP on search result page then just turn OFF this option.', 'classiera'),
				'default' => true,
			),
			array(
				'id'=>'classiera_google_api',
				'type' => 'text',
				'title' => __('Google API Key', 'classiera'),
				'subtitle' => __('Google API Key', 'classiera'),
				'desc' => __('Put Google API Key here to run Google MAP. If you dont know how to get API key Please Visit  <a href="http://www.tthemes.com/get-google-api-key/" target="_blank">Google API Key</a>', 'classiera'),
				'default' => ''
			),
			array(
				'id'=>'classiera_map_lang_code',
				'type' => 'text',
				'title' => __('Google MAP Language', 'classiera'),
				'subtitle' => __('Put your language code', 'classiera'),
				'desc' => __('Google allow only few language in MAP Please copy your language code and paste here. <a href="https://developers.google.com/maps/faq#languagesupport" target="_blank">Click here</a> for Language Code', 'classiera'),
				'default' => 'en'
			),
			array(
				'id'=>'google_analytics',
				'type' => 'textarea',
				'title' => __('Google Analytics', 'classiera'),
				'subtitle' => __('Google Analytics Script Code', 'classiera'),
				'desc' => __('Get analytics on your site. Enter Google Analytics script code', 'classiera'),
				'default' => ''
			),
			array(
				'id'=>'map-style',
				'type' => 'textarea',
				'title' => __('Map Styles', 'classiera'), 
				'subtitle' => __('Check <a href="http://snazzymaps.com/" target="_blank">snazzymaps.com</a> for a list of nice google map styles.', 'classiera'),
				'desc' => __('Ad here your Google map style.', 'classiera'),
				'validate' => 'html_custom',
				'default' => '',
				'allowed_html' => array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
					'br' => array(),
					'em' => array(),
					'strong' => array()
					)
			),
		)
    ) );
	// -> Coming Soon Page
    Redux::setSection( $opt_name, array(
        'title' => __( 'Coming Soon Page', 'classiera' ),
        'id'    => 'coming-soon',
        'desc'  => __( 'Coming Soon Page Settings', 'classiera' ),
        'icon'  => 'el el-magic',
        'fields' => array(
			array(
			'id'=>'coming-soon-logo',
			'type' => 'media', 
			'url'=> true,
			'title' => __('Coming Soon logo', 'classiera'),
			'compiler' => 'true',
			//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
			'desc'=> __('Upload Coming Soon template logo.', 'classiera'),
			'subtitle' => __('Upload Coming Soon template logo', 'classiera'),
			'default'=>array('url'=>''),
			),
			array(
			'id'=>'coming-soon-bg',
			'type' => 'media', 
			'url'=> true,
			'title' => __('Coming Soon BG', 'classiera'),
			'compiler' => 'true',
			//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
			'desc'=> __('Upload Coming Soon template Background.', 'classiera'),
			'subtitle' => __('Coming Soon BG', 'classiera'),
			'default'=>array('url'=>''),
			),
			array(
			'id'=>'coming-soon-txt',
			'type' => 'textarea',
			'title' => __('Coming Soon Text', 'classiera'),
			'subtitle' => __('Coming Soon Text', 'classiera'),
			'desc' => __('Coming Soon Text', 'classiera'),
			'default' => 'Well be here soon with our new awesome site'
			),
			array(
			'id'=>'coming-trns-days',
			'type' => 'text',
			'title' => __('Replace Days text', 'classiera'),
			'subtitle' => __('Days text', 'classiera'),
			'desc' => __('Days text', 'classiera'),
			'default' => 'Days'
			),
			array(
			'id'=>'coming-trns-hours',
			'type' => 'text',
			'title' => __('Replace Hours text', 'classiera'),
			'subtitle' => __('Hours text', 'classiera'),
			'desc' => __('Hours text', 'classiera'),
			'default' => 'Hours'
			),
			array(
			'id'=>'coming-trns-minutes',
			'type' => 'text',
			'title' => __('Replace Minutes text', 'classiera'),
			'subtitle' => __('Minutes text', 'classiera'),
			'desc' => __('Minutes text', 'classiera'),
			'default' => 'Minutes'
			),
			array(
			'id'=>'coming-trns-seconds',
			'type' => 'text',
			'title' => __('Replace Seconds text', 'classiera'),
			'subtitle' => __('Seconds text', 'classiera'),
			'desc' => __('Seconds text', 'classiera'),
			'default' => 'Seconds'
			),			
			array(
			'id'=>'coming-month',
			'type' => 'select',
			'title' => __('Month', 'classiera'), 
			'subtitle' => __('Select Month.', 'classiera'),
			'options' => array('1'=>'January', '2'=>'February', '3'=>'March', '4'=>'April', '5'=>'May', '6'=>'June', '7'=>'July', '8'=>'August', '9'=>'September', '10'=>'October', '11'=>'November', '12'=>'December'),
			'default' => '6',
			),
			array(
			'id'=>'coming-days',
			'type' => 'select',
			'title' => __('Days', 'classiera'), 
			'subtitle' => __('Select Days.', 'classiera'),
			'options' => array('1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6', '7'=>'7', '8'=>'8', '9'=>'9', '10'=>'10', '11'=>'11', '12'=>'12', '13'=>'13', '14'=>'14', '15'=>'15', '16'=>'16', '17'=>'17', '18'=>'18', '19'=>'19', '20'=>'20', '21'=>'21', '22'=>'22', '23'=>'23', '24'=>'24', '25'=>'25', '26'=>'26', '27'=>'27', '28'=>'28', '29'=>'29', '30'=>'30', '31'=>'31'),
			'default' => '10',
			),
			array(
			'id'=>'coming-year',
			'type' => 'text',
			'title' => __('Years', 'classiera'),
			'subtitle' => __('Put Years Example: 2016.', 'classiera'),
			'desc' => __('Years', 'classiera'),			
			'default' => '2017'
			),
			array(
			'id'=>'coming-copyright',
			'type' => 'text',
			'title' => __('Copyright Text', 'classiera'),
			'subtitle' => __('Copyright Text for Coming Soon Page.', 'classiera'),
			'desc' => __('Copyright Text', 'classiera'),			
			'default' => 'Copyright &copy; 2015 Classiera'
			),
       )
    ) );
	// -> Contact Page
    Redux::setSection( $opt_name, array(
        'title' => __( 'Contact Page', 'classiera' ),
        'icon'  => 'el el-envelope',
        'id'    => 'contact-page',
        'desc'  => __( 'Contact Page Settings', 'classiera' ),        
        'fields' => array(
			array(
				'id' => 'contact-map',
				'type' => 'switch',
				'title' => __('Map On Contact Page', 'classiera'),
				'subtitle' => __('Turn Map On/OFF from Contact Page', 'classiera'),
				'default' => 1,
			),
			array(
				'id' => 'classiera_display_email',
				'type' => 'switch',
				'title' => __('Email display on Contact Page', 'classiera'),
				'subtitle' => __('Turn OFF Email display', 'classiera'),
				'default' => 1,
			),
			array(
				'id'=>'contact-email',
				'type' => 'text',
				'title' => __('Your email address', 'classiera'),
				'subtitle' => __('This must be an email address.', 'classiera'),
				'desc' => __('Your email address', 'classiera'),
				'validate' => 'email',
				'default' => ''
			),
			array(
				'id'=>'contact-email-error',
				'type' => 'text',
				'title' => __('Email error message', 'classiera'),
				'subtitle' => __('Email error message', 'classiera'),
				'desc' => __('Email error message', 'classiera'),
				'default' => 'You entered an invalid email.'
			),
			array(
				'id'=>'contact-name-error',
				'type' => 'text',
				'title' => __('Name error message', 'classiera'),
				'subtitle' => __('Name error message', 'classiera'),
				'desc' => __('Name error message', 'classiera'),
				'default' => 'You forgot to enter your name.'
			),
			array(
				'id'=>'contact-message-error',
				'type' => 'text',
				'title' => __('Message error', 'classiera'),
				'subtitle' => __('Message error', 'classiera'),
				'desc' => __('Message error', 'classiera'),
				'default' => 'You forgot to enter your message.'
			),
			array(
				'id'=>'contact-thankyou-message',
				'type' => 'text',
				'title' => __('Thank you message', 'classiera'),
				'subtitle' => __('Thank you message', 'classiera'),
				'desc' => __('Thank you message', 'classiera'),
				'default' => 'Thank you! We will get back to you as soon as possible.'
			),
			array(
				'id'=>'contact-latitude',
				'type' => 'text',
				'title' => __('Google Latitude', 'classiera'),
				'subtitle' => __('Google Latitude', 'classiera'),
				'desc' => __('Put value for Google Latitude of your address', 'classiera'),
				'default' => '31.516370'
			),
			array(
				'id'=>'contact-longitude',
				'type' => 'text',
				'title' => __('Google Longitude', 'classiera'),
				'subtitle' => __('Google Longitude', 'classiera'),
				'desc' => __('Put value for Google Longitude of your address', 'classiera'),
				'default' => '74.258727'
			),
			array(
				'id'=>'contact-zoom',
				'type' => 'text',
				'title' => __('MAP Zoom level', 'classiera'),
				'subtitle' => __('MAP Zoom level', 'classiera'),
				'desc' => __('Put a value for Google MAP Zoom level', 'classiera'),
				'default' => '16'
			),
			array(
				'id'=>'contact-radius',
				'type' => 'text',
				'title' => __('Radius on Google MAP', 'classiera'),
				'subtitle' => __('Radius value', 'classiera'),
				'desc' => __('Put a value for Radius on Google MAP', 'classiera'),
				'default' => '500'
			),
			array(
				'id'=>'contact-address',
				'type' => 'text',
				'title' => __('Contact Page Address', 'classiera'),
				'subtitle' => __('Contact Page Address', 'classiera'),
				'desc' => __('Contact Page Address', 'classiera'),
				'default' => 'Our business address is 1063 Freelon Street San Francisco, CA 95108'
			),	
			array(
				'id'=>'contact-phone',
				'type' => 'text',
				'title' => __('Contact Page Phone', 'classiera'),
				'subtitle' => __('Contact Page Phone', 'classiera'),
				'desc' => __('Contact Page Phone', 'classiera'),
				'default' => '021.343.7575'
			),	
			array(
				'id'=>'contact-phone2',
				'type' => 'text',
				'title' => __('Contact Page Phone Second', 'classiera'),
				'subtitle' => __('Contact Page Phone Second', 'classiera'),
				'desc' => __('Contact Page Phone Second', 'classiera'),
				'default' => '021.343.7576'
			),
		)
    ) );

    if ( file_exists( dirname( __FILE__ ) . '/../README.md' ) ) {
        $section = array(
            'icon'   => 'el el-list-alt',
            'title'  => __( 'Documentation', 'classiera' ),
            'fields' => array(
                array(
                    'id'       => '17',
                    'type'     => 'raw',
                    'markdown' => true,
                    'content_path' => dirname( __FILE__ ) . '/../README.md', // FULL PATH, not relative please
                    //'content' => 'Raw content here',
                ),
            ),
        );
        Redux::setSection( $opt_name, $section );
    }
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
    //add_filter('redux/options/' . $opt_name . '/classiera', 'change_arguments' );

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
                'title'  => __( 'Section via hook', 'classiera' ),
                'desc'   => __( '<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'classiera' ),
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