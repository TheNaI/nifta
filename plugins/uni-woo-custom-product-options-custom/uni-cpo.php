<?php
/*
Plugin Name: Uni CPO (Custom) - WooCommerce Options and Price Calculation Formulas
Plugin URI: http://moomoo.agency/demo/cpo/
Description: Creates ability to add custom options for products with the posibility to calculate product price based on chosen options and using custom maths formula!
Version: 3.1.7
Author: MooMoo Studio Team
Author URI: http://moomoo.agency
Domain Path: /languages
Text Domain: uni-cpo
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( version_compare( get_bloginfo('version'), '4.6', '<') )  {
    wp_die( esc_html__('This plugin requires WordPress 4.6+!', 'uni-cpo') );
}

if ( ! class_exists( 'Uni_Cpo' ) ) :

/**
 * Uni_Cpo Class
 */
final class Uni_Cpo {

    public $version = '3.1.7';

    private static $plugin_updates = array();

    /**
     * The single instance of the class.
     */
    protected static $_instance = null;

    /**
     * Option factory instance.
     */
    public $option_factory = null;

    /**
     *
     */
    public $var_slug;

    /**
     *
     */
    public $non_option_var_slug;

    /**
     * Main Uni_Cpo Instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Uni_Cpo Constructor.
     */
    public function __construct() {
        $this->includes();
        $this->init_hooks();
        add_action( 'activated_plugin', array( $this, 'cpo_activation' ) );

        $this->var_slug = 'uni_cpo_';
        $this->non_option_var_slug = 'uni_nov_cpo_';
    }

    /**
     *  Init hooks
     */
    private function init_hooks() {
        add_action( 'init', array( $this, 'init' ), 0 );
    }

    /**
     * What type of request is this?
     * string $type ajax, frontend or admin.
     *
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined( 'DOING_AJAX' );
            case 'cron' :
                return defined( 'DOING_CRON' );
            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

    /**
     *  Includes
     */
    public function includes() {

        //
        include_once( $this->plugin_path() . '/includes/abstracts/abstract-uni-cpo-option.php' );
        include_once( $this->plugin_path() . '/includes/class-uni-cpo-option-factory.php' );

        // options
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-heading.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-divider.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-notice.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-text_input.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-text_area.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-select.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-checkboxes.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-radio.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-range_slider.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-date_picker.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-palette_select.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-text_select.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-file_upload.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-custom_js.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-image_select.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-notice_nonoptionvar.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-color_picker.php' );
        include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-download.php' );

        $aUniCpoSettings = get_option( 'uni_cpo_settings_general' );
        if (!empty($aUniCpoSettings['uni_google_maps_api_key'])) {
            include_once( $this->plugin_path() . '/includes/options/class-uni-cpo-option-google_maps.php' );
        }

        //
        include_once( $this->plugin_path() . '/includes/class-eval-math.php' );
        include_once( $this->plugin_path() . '/includes/class-uni-cpo-ajax.php' );
        include_once( $this->plugin_path() . '/includes/class-uni-cpo-post-types.php' );

        //
        include_once( $this->plugin_path() . '/includes/admin/uni-cpo-admin-functions.php' );
        include_once( $this->plugin_path() . '/includes/admin/uni-cpo-product-options.php' );
        include_once( $this->plugin_path() . '/includes/uni-cpo-functions.php' );
        include_once( $this->plugin_path() . '/includes/uni-cpo-option-functions.php' );
        include_once( $this->plugin_path() . '/includes/class-uni-cpo-options-list.php' );
    }

    /**
     * Init
     */
    public function init() {

        $this->check_version();

        // Multilanguage support
        $this->load_plugin_textdomain();

        $this->option_factory   = new Uni_Cpo_Option_Factory();

        add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ), 10 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 10 );

        add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'display_product_options' ), 10 );

        $sMyPlugin = plugin_basename(__FILE__);
        add_filter( "plugin_action_links_$sMyPlugin", array( $this, 'plugin_action_links' ) );
        add_filter( 'plugin_row_meta',  array( $this, 'plugin_additional_links' ), 10, 2 );

        add_filter( 'display_media_states', array( $this, 'media_states' ), 10, 1 );

        add_action( 'product_cat_edit_form_fields', array( $this, 'tax_custom_data' ) );
        add_action( 'edited_product_cat', array( $this, 'save_custom_meta' ), 10, 2 );

        add_action( 'init', array( $this, 'admin_options_init' ), 10 );

        add_image_size( 'uni-cpo-thumb', 200, 200, true );

    }

    /**
     * load_plugin_textdomain()
     */
    function media_states( $media_states ) {
        global $post;

        $iProductId = get_post_meta( $post->ID, '_uni_cpo_media_uploaded_for_product', true);

        if ( !empty( $iProductId ) ) {
            $media_states[] = esc_html__( 'Cart item/order attachment', 'uni-avatar' );
        }

        return apply_filters( 'uni_avatar_display_media_states_filter', $media_states );

    }

    /**
     * load_plugin_textdomain()
     */
    public function load_plugin_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'uni-cpo' );

        load_textdomain( 'uni-cpo', WP_LANG_DIR . '/uni-woo-custom-product-options/uni-cpo-' . $locale . '.mo' );
        load_plugin_textdomain( 'uni-cpo', false, plugin_basename( dirname( __FILE__ ) ) . "/languages" );
    }

    /**
    *  front_scripts()
    */
    public function front_scripts() {

        global $post;
        // parsley localization
        $sLocale = get_locale();
        $aLocale = explode('_', $sLocale);
        $sLangCode = $aLocale[0];

        if ( is_singular('product') || ( isset($post) && has_shortcode($post->post_content, 'product_page') ) ) {

            // ion.range slider
            wp_enqueue_script('jquery-ion-range-slider', $this->plugin_url() . '/assets/js/ion.rangeSlider.min.js', array('jquery'), '2.1.7' );
            // moment.js
            wp_enqueue_script( 'moment-min', $this->plugin_url().'/assets/js/moment.min.js', array('jquery'), '2.17.0' );
            // mousewheel.min
            wp_enqueue_script( 'jquery-mousewheel', $this->plugin_url().'/assets/js/jquery.mousewheel.min.js', array('jquery'), '5.3.9' );
            // period picker
            wp_enqueue_script('jquery-xdan-periodpicker', $this->plugin_url() . '/assets/js/jquery.periodpicker.full.min.js', array('jquery'), '5.4.6' );
            // time picker
            wp_enqueue_script('jquery-xdan-timepicker', $this->plugin_url() . '/assets/js/jquery.timepicker.min.js', array('jquery'), '5.4.6' );
            // tooltipster
            wp_enqueue_script('tooltipster-bundle-min', $this->plugin_url() . '/assets/js/tooltipster.bundle.min.js', array('jquery'), '4.1.7' );
            // lity
            wp_enqueue_script('lity', $this->plugin_url() . '/assets/js/lity.min.js', array('jquery'), '2.2.2' );
            // parsley
            wp_enqueue_script('jquery-parsley', $this->plugin_url() . '/assets/js/parsley.min.js', array('jquery'), '2.7.2' );
            // parsley localization
            wp_enqueue_script('parsley-localization', $this->plugin_url() . '/assets/js/i18n/parsley/en.js', array('jquery-parsley'), '2.7.2' );

            // uni-cpo-front
            wp_enqueue_script('uni-cpo-front', $this->plugin_url().'/assets/js/uni-cpo-front.js',
                array('jquery', 'jquery-ion-range-slider', 'jquery-xdan-periodpicker', 'jquery-xdan-timepicker', 'tooltipster-bundle-min', 'jquery-parsley', 'jquery-blockui'),
                $this->version);

            $cpo_script_dependecies = array('jquery', 'wp-util', 'jquery-ion-range-slider', 'jquery-xdan-periodpicker', 'jquery-xdan-timepicker', 'tooltipster-bundle-min', 'jquery-parsley', 'jquery-blockui' );

            // uni-cpo-google-maps
            // get google maps key
            $aUniCpoSettings = get_option( 'uni_cpo_settings_general', $this->default_settings() );
            if ( ! empty( $aUniCpoSettings['uni_google_maps_api_key'] ) ){

                $iProductPostId = intval($post->ID);
                // maybe there is 'product_page' shortcode?
                $id_from_shortcode = uni_cpo_detect_shortcode( $post->post_content, 'product_page' );
                if ( false !== $id_from_shortcode ) {
                    $iProductPostId = $id_from_shortcode;
                }
                $post_custom    = get_post_custom( $iProductPostId );
                $options_set_id = ( ! empty( $post_custom['_uni_cpo_options_set'][0] ) ) ? intval( $post_custom['_uni_cpo_options_set'][0] ) : '';

                if ( !empty( $options_set_id ) ) {
                    $options_set = get_post( $options_set_id );
                    if ( null !== $options_set ) {
                        $product_options = get_post_meta( $options_set_id, '_uni_cpo_options_structure', true );

                        foreach ( $product_options as $structure_item ) {
                            if ( ! empty( $structure_item['itemtype'] ) && 'google_maps' === $structure_item['itemtype'] ) {
                                wp_enqueue_script( 'google-maps-cpo', 'https://maps.google.com/maps/api/js?libraries=places&key='. $aUniCpoSettings['uni_google_maps_api_key'], array(), '3.27', true );
                                $cpo_script_dependecies[] = 'google-maps-cpo';
                                break;
                            }
                        }
                    }
                }

            }

            // uni-cpo-front-footer
            wp_enqueue_script('uni-cpo-front-footer', $this->plugin_url().'/assets/js/uni-cpo-front-footer.js',
                $cpo_script_dependecies,
                $this->version, true);

            $iProductPostId = intval($post->ID);
            // maybe there is 'product_page' shortcode?
            $id_from_shortcode = uni_cpo_detect_shortcode( $post->post_content, 'product_page' );
            if ( false !== $id_from_shortcode ) {
                $iProductPostId = $id_from_shortcode;
            }
            $wc_product     = wc_get_product( $iProductPostId );
            $post_custom    = get_post_custom( $iProductPostId );

            if ( isset( $post_custom['_uni_cpo_display_options_enable'][0] ) && true === (bool) $post_custom['_uni_cpo_display_options_enable'][0] ) {

                $sZeroPrice = uni_cpo_price( '0.00' );
                if ( isset( $post_custom['_uni_cpo_non_option_vars'][0] ) && ! empty( $post_custom['_uni_cpo_non_option_vars'][0] ) ) {
                    $non_option_vars = maybe_unserialize( $post_custom['_uni_cpo_non_option_vars'][0] );
                    $non_option_vars_processed = uni_cpo_process_formula_with_non_option_vars( array(), $non_option_vars, 'conditional' );
                } else {
                    $non_option_vars_processed = array();
                }

                $params = array(
                    'version'               => $this->version,
                    'site_url'              => esc_url( home_url('/') ),
                    'ajax_url'              => admin_url( 'admin-ajax.php' ),
                    'price_selector'        => esc_attr( $aUniCpoSettings['product_price_container'] ),
                    'image_selector'        => esc_attr( $aUniCpoSettings['product_image_container'] ),
                    'total_off'             => ( ( ( isset($aUniCpoSettings['product_hide_total']) && $aUniCpoSettings['product_hide_total'] === 'off' ) || $wc_product->is_sold_individually() ) ? true : false ),
                    'locale'                => esc_attr( $sLangCode ),
                    'cpo_on'                => ( ( isset( $post_custom['_uni_cpo_display_options_enable'][0] ) && true === (bool) $post_custom['_uni_cpo_display_options_enable'][0] ) ? true : false ),
                    'calc_on'               => ( ( isset( $post_custom['_uni_cpo_price_calculation_enable'][0] ) && true === (bool) $post_custom['_uni_cpo_price_calculation_enable'][0] ) ? true : false ),
                    'calc_btn_on'           => ( ( isset( $post_custom['_uni_cpo_price_calculation_btn_enable'][0] ) && true === (bool) $post_custom['_uni_cpo_price_calculation_btn_enable'][0] ) ? true : false ),
                    'calc_text'             => esc_html__('Calculating', 'uni-cpo'),
                    'price_suffix_on'       => ( ( get_option( 'woocommerce_price_display_suffix' ) ) ? 1 : 0 ),
                    'text_after_zero_price' => apply_filters( 'uni_cpo_after_zero_price_default_text', esc_html__('(fill in all required fields to calculate the price)', 'uni-cpo'), $wc_product ),
                    'total_text_start'      => apply_filters( 'uni_cpo_total_text_start', esc_html__('Total for', 'uni-cpo'), $wc_product ),
                    'total_text_end'        => apply_filters( 'uni_cpo_total_text_end', esc_html__('is:', 'uni-cpo'), $wc_product ),
                    'price_discount_text'   => apply_filters( 'uni_cpo_price_discount_text', esc_html__('Price with discount:', 'uni-cpo'), $wc_product ),
                    'selector_opts_change'  => uni_cpo_get_registered_options_classes( 'js-change' ),
                    'selector_opts_all'     => uni_cpo_get_registered_options_classes( 'js-all' ),
                    'nov_vars'              => $non_option_vars_processed,
                    'reg_vars'              => array(),
                    'price_vars'            => array(
                            'raw_price' => 0,
                            'raw_price_tax_rev' => 0, 
                            'price' => $sZeroPrice,
                            'price_suffix' => '',
                            'price_discounted' => 0,
                            'raw_total' => 0,
                            'raw_total_tax_rev' => 0, 
                            'total' => $sZeroPrice,
                            'total_tax_rev' => $sZeroPrice,
                            'total_suffix' => '',
                    ),
                    'extra_data'            => array('order_product' => 'enabled'),
                    'loader'                => $this->plugin_url() . '/assets/images/preloader.gif'
                );
            } else {
                $params = array(
                    'version'               => $this->version,
                    'site_url'              => esc_url( home_url('/') ),
                    'ajax_url'              => admin_url( 'admin-ajax.php' ),
                    'locale'                => esc_attr( $sLangCode )
                );
            }

            wp_localize_script( 'uni-cpo-front', 'unicpo', $params );

            // parsley localization
            $aParsleyStrings = apply_filters( 'uni_cpo_parsley_strings_filter', array(
                    'defaultMessage'    => esc_html__("This value seems to be invalid.", 'uni-cpo'),
                    'type_email'        => esc_html__("This value should be a valid email.", 'uni-cpo'),
                    'type_url'          => esc_html__("This value should be a valid url.", 'uni-cpo'),
                    'type_number'       => esc_html__("This value should be a valid number.", 'uni-cpo'),
                    'type_digits'       => esc_html__("This value should be digits.", 'uni-cpo'),
                    'type_alphanum'     => esc_html__("This value should be alphanumeric.", 'uni-cpo'),
                    'type_integer'      => esc_html__("This value should be a valid integer.", 'uni-cpo'),
                    'notblank'          => esc_html__("This value should not be blank.", 'uni-cpo'),
                    'required'          => esc_html__("This value is required.", 'uni-cpo'),
                    'pattern'           => esc_html__("This value seems to be invalid.", 'uni-cpo'),
                    'min'               => esc_html__("This value should be greater than or equal to %s.", 'uni-cpo'),
                    'max'               => esc_html__("This value should be lower than or equal to %s.", 'uni-cpo'),
                    'range'             => esc_html__("This value should be between %s and %s.", 'uni-cpo'),
                    'minlength'         => esc_html__("This value is too short. It should have %s characters or more.", 'uni-cpo'),
                    'maxlength'         => esc_html__("This value is too long. It should have %s characters or fewer.", 'uni-cpo'),
                    'length'            => esc_html__("This value length is invalid. It should be between %s and %s characters long.", 'uni-cpo'),
                    'mincheck'          => esc_html__("You must select at least %s choices.", 'uni-cpo'),
                    'maxcheck'          => esc_html__("You must select %s choices or fewer.", 'uni-cpo'),
                    'check'             => esc_html__("You must select between %s and %s choices.", 'uni-cpo'),
                    'equalto'           => esc_html__("This value should be the same.", 'uni-cpo'),
                    'dateiso'           => esc_html__("This value should be a valid date (YYYY-MM-DD).", 'uni-cpo'),
                    'minwords'          => esc_html__("This value is too short. It should have %s words or more.", 'uni-cpo'),
                    'maxwords'          => esc_html__("This value is too long. It should have %s words or fewer.", 'uni-cpo'),
                    'words'             => esc_html__("This value length is invalid. It should be between %s and %s words long.", 'uni-cpo'),
                    'gt'                => esc_html__("This value should be greater.", 'uni-cpo'),
                    'gte'               => esc_html__("This value should be greater or equal.", 'uni-cpo'),
                    'lt'                => esc_html__("This value should be less.", 'uni-cpo'),
                    'lte'               => esc_html__("This value should be less or equal.", 'uni-cpo'),
                    'notequalto'        => esc_html__("This value should be different.", 'uni-cpo')
                )
            );

            wp_localize_script( 'jquery-parsley', 'uni_cpo_parsley_loc', $aParsleyStrings );

            // styles
            wp_enqueue_style( 'jquery-ion-range-slider-styles', $this->plugin_url().'/assets/css/ion.rangeSlider.css', false, '2.1.6', 'all');
            wp_enqueue_style( 'jquery-ion-range-slider-html5-skin', $this->plugin_url().'/assets/css/ion.rangeSlider.skinHTML5.css', false, '2.1.6', 'all');
            wp_enqueue_style( 'tooltipster-bundle-min-styles', $this->plugin_url().'/assets/css/tooltipster.bundle.min.css', false, '4.1.7', 'all');
            wp_enqueue_style( 'tooltipster-noir-theme-styles', $this->plugin_url().'/assets/css/tooltipster-sideTip-noir.min.css', false, '4.1.7', 'all');
            wp_enqueue_style( 'font-awesome', $this->plugin_url() . '/assets/css/font-awesome.min.css', array(), '4.7.0', 'all' );
            wp_enqueue_style( 'uni-cpo-styles-front', $this->plugin_url().'/assets/css/uni-cpo-styles-front.css', false, $this->version, 'all');

            /* for colorpicker */
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('iris', admin_url('js/iris.min.js'),array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
            wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), false,1);
            $colorpicker_arr = array('clear' => esc_html__('Clear','uni-cpo'), 'defaultString' => esc_html__('Default','uni-cpo'), 'pick' => esc_html__('Select Color','uni-cpo'));
            wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_arr );

        }

    }

    /**
    *  admin_scripts()
    */
    public function admin_scripts( $hook ) {

        $screen       = get_current_screen();
        $screen_id    = $screen ? $screen->id : '';

        $sLocale = get_locale();
        $aLocale = explode('_',$sLocale);
        $sLangCode = $aLocale[0];

        // parsley
        wp_register_script('jquery-parsley', $this->plugin_url() . '/assets/js/parsley.min.js', array('jquery'), '2.7.2' );
        wp_enqueue_script('jquery-parsley');
        // parsley localization
        wp_register_script('parsley-localization', $this->plugin_url() . '/assets/js/i18n/parsley/en.js', array('jquery-parsley'), '2.7.2' );
        wp_enqueue_script('parsley-localization');

        if ( ( $hook == 'post.php' || $hook == 'post-new.php' ) && in_array( get_post_type(), array( 'product', 'shop_order' ) ) ) {
            // jquery-nestable
            wp_enqueue_script('jquery-nestable', $this->plugin_url().'/assets/js/jquery.nestable.js', array('jquery'), '1.0.0' );
            // remodal
            wp_enqueue_script('jquery-remodal', $this->plugin_url().'/assets/js/remodal.min.js', array('jquery'), '1.0.7' );
            // query-builder
            wp_enqueue_script('jquery-query-builder', $this->plugin_url().'/assets/js/query-builder.standalone.min.js', array('jquery'), '2.3.2' );
            // query-builder
            wp_enqueue_script('jquery-repeatable-fields', $this->plugin_url().'/assets/js/repeatable-fields.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), '1.4.8' );


            $aUniCpoSettings = get_option( 'uni_cpo_settings_general', $this->default_settings() );

            if ( ! empty( $aUniCpoSettings['uni_google_maps_api_key'] ) ) {
                // google maps
                wp_enqueue_script( 'google-maps-cpo', 'https://maps.google.com/maps/api/js?libraries=places&key='. $aUniCpoSettings['uni_google_maps_api_key'], array(), '3.27', true );
            }

            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_style( 'wp-color-picker' );

            // uni-cpo-admin-product
            wp_enqueue_script( 'uni-cpo-admin-product', $this->plugin_url() . '/assets/js/uni-cpo-admin-product.js',
                array('jquery', 'jquery-ui-core', 'jquery-ui-dialog',
                'jquery-ui-widget', 'jquery-ui-draggable', 'jquery-ui-mouse', 'jquery-ui-position', 'jquery-ui-button', 'jquery-ui-resizable',
                'jquery-ui-tabs', 'jquery-ui-sortable', 'jquery-effects-core', 'jquery-effects-fade', 'jquery-blockui',
                'jquery-remodal', 'jquery-query-builder', 'jquery-repeatable-fields','wp-color-picker' ), $this->version );
        }

        // Products
        if ( in_array( $screen_id, array( 'edit-product' ) ) ) {
            wp_register_script( 'uni-cpo_quick-edit', $this->plugin_url().'/assets/js/quick-edit.js', array( 'jquery' ), $this->version );
            wp_enqueue_script( 'uni-cpo_quick-edit' );
        }

        // woocommerce_page_uni-cpo-options-list
        if ( in_array( $screen_id, array( 'woocommerce_page_uni-cpo-options-list' ) ) ) {
            wp_register_script( 'uni-cpo_options-set-quick-edit', $this->plugin_url().'/assets/js/options-set-quick-edit.js', array( 'jquery', 'wp-a11y' ), $this->version );
            wp_enqueue_script( 'uni-cpo_options-set-quick-edit' );
        }

        if ( ( $hook == 'post.php' || $hook == 'post-new.php' ) && get_post_type() == 'product' ) {

            global $post;

            $iOptionsPostId = ( get_post_meta( $post->ID, '_uni_cpo_options_set', true ) ) ? intval(get_post_meta( $post->ID, '_uni_cpo_options_set', true )) : '';

            $additonal_params = array(
                'fields_structure'  => ( ( !empty($iOptionsPostId) ) ? uni_cpo_list_of_formula_variables( $iOptionsPostId, 'fields_structure' ) : array() )
            );

            wp_localize_script( 'uni-cpo-admin-product', 'unicpooptions', $additonal_params );

        }

        $params = array(
            'locale'            => $sLangCode,
            'loader'            => $this->plugin_url() . '/assets/images/preloader.gif',
            'fields'            => uni_cpo_get_option_types(),
            'remove_non_vars_text' => esc_html__('Delete non-option variables', 'uni-cpo'),
            'remove_cond_text'  => esc_html__('Delete formula conditional rules', 'uni-cpo'),
            'remove_disc_rules_text'  => esc_html__('Delete rules', 'uni-cpo'),
            'edit_option_text'  => esc_html__('Edit', 'uni-cpo'),
            'copy_option_text'  => esc_html__('Copy', 'uni-cpo'),
            'remove_option_text'  => esc_html__('Remove', 'uni-cpo'),
            'edit_tip_option_text'  => esc_html__('Edit option', 'uni-cpo'),
            'copy_tip_option_text'  => esc_html__('Copy option', 'uni-cpo'),
            'remove_tip_option_text'  => esc_html__('Remove option', 'uni-cpo'),
            'uploader_title_text'  => esc_html__('Choose an image', 'uni-cpo'),
            'uploader_btn_text'  => esc_html__('Use image', 'uni-cpo'),
        );

        wp_localize_script( 'uni-cpo-admin-product', 'unicpo', $params );

        $uniInlineEditL10n = array(
            'error'      => esc_html__( 'Error while saving the changes.', 'uni-cpo' ),
            'ntdeltitle' => esc_html__( 'Remove From Bulk Edit', 'uni-cpo' ),
            'notitle'    => esc_html__( '(no title)', 'uni-cpo' ),
            'saved'      => esc_html__( 'Changes saved.', 'uni-cpo' ),
        );

        wp_localize_script( 'uni-cpo_options-set-quick-edit', 'uniInlineEditL10n', $uniInlineEditL10n );

        // parsley localization
        $aParsleyStrings = apply_filters( 'uni_cpo_parsley_strings_filter', array(
            'defaultMessage'    => esc_html__("This value seems to be invalid.", 'uni-cpo'),
            'type_email'        => esc_html__("This value should be a valid email.", 'uni-cpo'),
            'type_url'          => esc_html__("This value should be a valid url.", 'uni-cpo'),
            'type_number'       => esc_html__("This value should be a valid number.", 'uni-cpo'),
            'type_digits'       => esc_html__("This value should be digits.", 'uni-cpo'),
            'type_alphanum'     => esc_html__("This value should be alphanumeric.", 'uni-cpo'),
            'type_integer'      => esc_html__("This value should be a valid integer.", 'uni-cpo'),
            'notblank'          => esc_html__("This value should not be blank.", 'uni-cpo'),
            'required'          => esc_html__("This value is required.", 'uni-cpo'),
            'pattern'           => esc_html__("This value seems to be invalid.", 'uni-cpo'),
            'min'               => esc_html__("This value should be greater than or equal to %s.", 'uni-cpo'),
            'max'               => esc_html__("This value should be lower than or equal to %s.", 'uni-cpo'),
            'range'             => esc_html__("This value should be between %s and %s.", 'uni-cpo'),
            'minlength'         => esc_html__("This value is too short. It should have %s characters or more.", 'uni-cpo'),
            'maxlength'         => esc_html__("This value is too long. It should have %s characters or fewer.", 'uni-cpo'),
            'length'            => esc_html__("This value length is invalid. It should be between %s and %s characters long.", 'uni-cpo'),
            'mincheck'          => esc_html__("You must select at least %s choices.", 'uni-cpo'),
            'maxcheck'          => esc_html__("You must select %s choices or fewer.", 'uni-cpo'),
            'check'             => esc_html__("You must select between %s and %s choices.", 'uni-cpo'),
            'equalto'           => esc_html__("This value should be the same.", 'uni-cpo'),
            'dateiso'           => esc_html__("This value should be a valid date (YYYY-MM-DD).", 'uni-cpo'),
            'minwords'          => esc_html__("This value is too short. It should have %s words or more.", 'uni-cpo'),
            'maxwords'          => esc_html__("This value is too long. It should have %s words or fewer.", 'uni-cpo'),
            'words'             => esc_html__("This value length is invalid. It should be between %s and %s words long.", 'uni-cpo'),
            'gt'                => esc_html__("This value should be greater.", 'uni-cpo'),
            'gte'               => esc_html__("This value should be greater or equal.", 'uni-cpo'),
            'lt'                => esc_html__("This value should be less.", 'uni-cpo'),
            'lte'               => esc_html__("This value should be less or equal.", 'uni-cpo'),
            'notequalto'        => esc_html__("This value should be different.", 'uni-cpo')
            )
        );

        wp_localize_script( 'jquery-parsley', 'uni_cpo_parsley_loc', $aParsleyStrings );

        wp_enqueue_style( 'font-awesome', $this->plugin_url() . '/assets/css/font-awesome.min.css', array(), '4.7.0', 'all' );
        wp_enqueue_style( 'uni-cpo-style-admin', $this->plugin_url() . '/assets/css/uni-cpo-styles-admin.css', array(), $this->version );

    }

    /**
     * plugin options page
     */
    function admin_options_init() {
        add_action( 'admin_menu', array( $this, 'create_menu') );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * plugin's menu
     */
    function create_menu() {
        add_submenu_page( 'woocommerce', __('Uni CPO Settings', 'uni-cpo'), __('Uni CPO Settings', 'uni-cpo'), 'manage_woocommerce', 'uni-cpo-settings', array( $this, 'settings_page' ) );
        add_submenu_page( 'woocommerce', __('Uni CPO Options Manager', 'uni-cpo'), __('Uni CPO Options Manager', 'uni-cpo'), 'manage_woocommerce', 'uni-cpo-options-list', array( $this, 'options_table_page' ) );

        switch ( isset($_GET['page']) ) {
            case 'cpo-about' :
                $page = add_dashboard_page( __( 'Welcome to Uni CPO', 'uni-cpo' ), __( 'About Uni CPO', 'uni-cpo' ), 'manage_options', 'cpo-about', array( $this, 'about_screen' ) );
                add_action( 'admin_print_styles-' . $page, array( $this, 'admin_css' ) );
            break;
        }
    }

    /**
     * admin_css function.
     */
    public function admin_css() {
        wp_register_style( 'uni-cpo-styles-welcome', $this->plugin_url() . '/assets/css/uni-cpo-styles-welcome.css', array(), $this->version );
        wp_enqueue_style( 'uni-cpo-styles-welcome' );
    }

    /**
     *
     */
    function register_settings() {

        add_settings_section(
            'uni_cpo_settings_general',
            esc_html__('General settings', 'uni-cpo'),
            array( $this, 'settings_general_callback_function' ),
            'general_settings'
        );

        // price_container
        add_settings_field(
            'product_price_container',
            esc_html__('Custom selector (id/class) for a product price html tag', 'uni-cpo'),
            array( $this, 'setting_text_input_callback_function' ),
            'general_settings',
            'uni_cpo_settings_general',
            array(
                'name' => 'product_price_container',
                'section' => 'uni_cpo_settings_general',
                'description' => esc_html__('By default, the selector for a product price html tag is ".summary.entry-summary .price > .amount, .summary.entry-summary .price ins .amount". However, the actual html markup of this block depends on the theme and you may need to define yours custom selector.', 'uni-cpo')
            )
        );
        register_setting( 'general_settings', 'uni_cpo_settings_general' );

        // product_image_container
        add_settings_field(
            'product_image_container',
            esc_html__('Custom selector (id/class) for a product image wrapper html tag', 'uni-cpo'),
            array( $this, 'setting_text_input_callback_function' ),
            'general_settings',
            'uni_cpo_settings_general',
            array(
                'name' => 'product_image_container',
                'section' => 'uni_cpo_settings_general',
                'description' => esc_html__('By default, the selector for a product image wrapper html tag on a single product page is "figure.woocommerce-product-gallery__wrapper". However, the actual html markup of the image block depends on the theme and you may need to define yours custom selector. Reminder: this selector is for element that wraps the main image, not the image ("img" tag) itself!', 'uni-cpo')
            )
        );
        register_setting( 'general_settings', 'uni_cpo_settings_general' );

        // product_image_size
        add_settings_field(
            'product_image_size',
            esc_html__('Image size that is used for single product main image', 'uni-cpo'),
            array( $this, 'setting_select_callback_function' ),
            'general_settings',
            'uni_cpo_settings_general',
            array(
                'name' => 'product_image_size',
                'section' => 'uni_cpo_settings_general',
                'choices' => uni_cpo_get_image_sizes_list(),
                'description' => esc_html__('By default, this is "shop_single". However the actual thumbnail size used depends on the theme and you may need to choose the correct one. This setting works in conjuction with the previous one and it is important to choose proper image size, so it will be used whenever a customer selects new option in a dropdown option or image select option with an image added to this chosen option.', 'uni-cpo')
            )
        );
        register_setting( 'general_settings', 'uni_cpo_settings_general' );

        // hide_total
        add_settings_field(
            'product_hide_total',
            esc_html__('Hide/show "Total for..." text', 'uni-cpo'),
            array( $this, 'setting_select_callback_function' ),
            'general_settings',
            'uni_cpo_settings_general',
            array(
                'name' => 'product_hide_total',
                'section' => 'uni_cpo_settings_general',
                'choices' => array(
                    'on' => esc_html__('Show', 'uni-cpo'),
                    'off' => esc_html__('Hide', 'uni-cpo'),
                ),
                'description' => esc_html__('Hide/show "Total for..." text on a single product page. In case you don\'t need it at all.', 'uni-cpo')
            )
        );
        register_setting( 'general_settings', 'uni_cpo_settings_general' );


        // google maps api key
        add_settings_field(
            'uni_google_maps_api_key',
            esc_html__('Google Maps Api Key', 'uni-cpo'),
            array( $this, 'setting_text_input_callback_function' ),
            'general_settings',
            'uni_cpo_settings_general',
            array(
                'name' => 'uni_google_maps_api_key',
                'section' => 'uni_cpo_settings_general',
                'description' => esc_html__('Please add Google Maps Api Key (optional)', 'uni-cpo')
            )
        );
        register_setting( 'general_settings', 'uni_cpo_settings_general' );

    }

    function default_settings() {
        return array(
            'product_price_container' => '.summary.entry-summary .price > .amount, .summary.entry-summary .price ins .amount',
            'product_image_container' => '.woocommerce-main-image img',
            'product_image_size' => 'shop_single',
            'product_hide_total' => 'on',
            'uni_google_maps_api_key' => '',
        );
    }

    /**
     *
     */
    function settings_general_callback_function() {
        echo esc_html__( 'These are the main settings of the plugin.', 'uni-cpo' );
    }

    function setting_text_input_callback_function( $aArgs ){
        $options = get_option( $aArgs['section'], $this->default_settings() );
        $sOptionName = $aArgs['name'];
        ?>
        <input type="text" name="<?php echo esc_attr( $aArgs['section'].'['.$sOptionName.']' ); ?>" value="<?php echo esc_attr( $options[$sOptionName] ); ?>" class="large-text code" />
        <p class="description"><?php echo $aArgs['description'] ?></p>
        <?php
    }

    function setting_select_callback_function( $aArgs ){
        $options = get_option( $aArgs['section'], $this->default_settings() );
        $sOptionName = $aArgs['name'];
        ?>
        <select name="<?php echo esc_attr( $aArgs['section'].'['.$aArgs['name'].']' ); ?>">
        <?php foreach ( $aArgs['choices'] as $sSlug => $sName ) { ?>
            <option value="<?php echo esc_attr( $sSlug ) ?>"<?php selected($options[$sOptionName], $sSlug) ?>><?php echo esc_attr( $sName ) ?></option>
        <?php } ?>
        </select>
        <p class="description"><?php echo $aArgs['description'] ?></p>
        <?php
    }

    function setting_checkbox_input_callback_function( $aArgs ){
        $options = get_option( $aArgs['section'], $this->default_settings() );
        $sOptionName = $aArgs['name'];
        ?>
        <input type="checkbox" name="<?php echo esc_attr( $aArgs['section'].'['.$sOptionName.']' ); ?>"<?php checked( $options[$sOptionName], 'yes' ); ?> value="yes" class="large-text code" />
        <p class="description"><?php echo $aArgs['description'] ?></p>
        <?php
    }

    /**
     *
     */
    function settings_page() {
        ?>
        <div class="wrap">

            <div id="icon-themes" class="icon32"></div>
            <h2><?php esc_html_e('Uni CPO Plugin Settings', 'uni-cpo') ?></h2>

            <?php settings_errors(); ?>

            <form method="post" action="options.php">
                <?php settings_fields( 'general_settings' ); ?>
                <?php do_settings_sections( 'general_settings' ); ?>
                <?php submit_button(); ?>
            </form>

        </div><!-- /.wrap -->
        <?php
    }

    public function options_table_page() {
        ?>
        <div class="wrap">

            <div id="icon-themes" class="icon32"></div>
            <h2><?php esc_html_e('Uni CPO Options Manager', 'uni-cpo') ?></h2>

            <?php uni_cpo_admin_notices(); ?>

            <?php
                $options_table      = new Uni_Cpo_Option_Sets_List();
                $options_table->prepare_items();
                $options_table->display();
            ?>

        </div><!-- /.wrap -->

        <?php
    }

    /**
     * Intro text/links shown on all about pages.
     */
    private function intro() {
        // Drop minor version if 0
        $major_version = substr( $this->version, 0, 3 );
        ?>
        <h1><?php printf( esc_html__( 'Welcome to Uni CPO %s', 'woocommerce' ), $major_version ); ?></h1>

        <div class="about-text">
            <?php esc_html_e( '"Uni Custom Product Options & Calculations" is a fully featured plugin that gives a possibility to
            add any custom options for your WooCommerce products as well as enables custom price calculation
            based on any maths formula. A real power! :)', 'uni-cpo' ); ?>
        </div>

        <div class="uni-cpo-badge">
            <span>CPO</span><br>
            <?php printf( esc_html__( 'Version %s', 'uni-cpo' ), $this->version ); ?>
        </div>

        <p class="uni-cpo-welcome-actions">
            <a href="<?php echo admin_url('admin.php?page=uni-cpo-settings'); ?>" class="button button-primary"><?php esc_html_e( 'Plugin settings', 'uni-cpo' ); ?></a>
            <a href="http://moomoo.agency/demo/cpo/docs" class="docs button button-primary"><?php esc_html_e( 'Documentation', 'uni-cpo' ); ?></a>
        </p>
        <?php
    }

    /**
     * Output the about screen.
     */
    public function about_screen() {
        ?>
    <div class="wrap about-wrap">

        <?php $this->intro(); ?>

        <hr />

        <div class="feature-section two-col">
            <div class="col">
                <h3><?php esc_html_e( 'What\'s new in 3.1?', 'uni-cpo' ); ?></h3>
                <p><?php esc_html_e( 'We have added a lot of new things - new types of options, new features!', 'uni-cpo' ); ?></p>
                <p><?php esc_html_e( 'So, really, what\'s new? Google Map option, Color picker option, colorify layered image functionality,
                a possibility to add order items with custom options via admin area with the automatic price calculation - these are the
                biggest improvements of this release.', 'uni-cpo' ); ?></p>
                <p><?php esc_html_e( 'Read more about this feature in the documentation.', 'uni-cpo' ); ?></p>
            </div>
            <div class="col">
                <img src="<?php echo $this->plugin_url(); ?>/assets/images/admin-edit-order.png" width="745" height="248" alt="">
            </div>
        </div>

        <div class="feature-section two-col">
            <div class="col">
                <img src="<?php echo $this->plugin_url(); ?>/assets/images/google-map.png" width="588" height="506" alt="">
            </div>
            <div class="col">
                <h4><?php esc_html_e( 'Google Map option', 'uni-cpo' ); ?></h4>
                <p><?php esc_html_e( 'This new type of options includes four sub types: a map with a posisbility to add a single marker,
                two markers, two markers with autocomplete that returns only city names and one marker from a customer with another one
                set on admin area.', 'uni-cpo' ); ?></p>
                <h4><?php esc_html_e( 'Colorify layered image functionality', 'uni-cpo' ); ?></h4>
                <p><?php esc_html_e( 'It is possible to add one or many images as layers and change their colors via connected Palette
                Select options for each such layer.', 'uni-cpo' ); ?></p>
            </div>
        </div>

        <hr />

        <div class="changelog">
            <h3><?php esc_html_e( 'More info', 'uni-cpo' ); ?></h3>

            <div class="feature-section under-the-hood one-col">
                <div class="col">
                    <h4><?php esc_html_e( 'Quick tip', 'uni-cpo' ); ?>:</h4>
                    <p><?php echo sprintf( esc_html__( 'This icon %s means that you can
                        proceed to the documentation and read more about a particular feature there. This sign says the documentation contains additional
                        information related to this feature and this icon actually is a link to that information.', 'uni-cpo' ), '<img src="' . $this->plugin_url() . '/assets/images/doc-link.png" width="31" height="29" alt="">' ); ?>
                    </p>
                </div>
            </div>

        </div>

        <hr>


    </div>
        <?php
    }

    /**
     *  plugin_action_links
     */
    public function plugin_action_links( $links ) {
        $sSettingsLink = '<a href="'.admin_url('admin.php?page=uni-cpo-settings').'">'.esc_html__('Settings', 'uni-cpo').'</a>';
        array_unshift($links, $sSettingsLink);
        return $links;
    }

    /**
     *  plugin_additional_links
     */
    public function plugin_additional_links($links, $file) {
        $base = plugin_basename(__FILE__);
        if ($file == $base) {
            $links[] = '<a href="http://moomoo.agency/demo/cpo/docs">' . esc_html__('Docs', 'uni-cpo') . '</a>';
        }
        return $links;
    }

    /**
     * display_product_options()
     */
    public function display_product_options() {
        global $product;

        $product_id     = $product->get_id();
        $product_meta   = get_post_custom( $product_id );
        $options_set_id = ( ! empty( $product_meta['_uni_cpo_options_set'][0] ) ) ? intval( $product_meta['_uni_cpo_options_set'][0] ) : '';

        if ( !empty( $options_set_id ) ) {
            $options_set = get_post( $options_set_id );
            if ( null !== $options_set ) {
                $product_options = get_post_meta( $options_set_id, '_uni_cpo_options_structure', true );
            }
        }

        if( $product->is_type( 'simple' ) && ! empty( $options_set_id ) && isset( $product_meta['_uni_cpo_display_options_enable'][0] ) &&
            true === (bool) $product_meta['_uni_cpo_display_options_enable'][0] ) {

            echo '<div class="uni_cpo_options_box">';  // start uni_cpo_options_box

            echo '<input type="hidden" class="uni_cpo_product_id" name="uni_cpo_product_id" value="' . esc_attr( $product_id ) . '" />';
            echo '<input type="hidden" class="uni_cpo_add_to_cart" name="add-to-cart" value="' . esc_attr( $product_id ) . '" />';
            echo '<input type="hidden" class="uni_cpo_cart_item_id" name="uni_cpo_cart_item_id" value="' . current_time('timestamp') . '" />';

            if ( isset( $product_meta['_uni_cpo_ordering_disabled_notice'][0] ) && ! empty( $product_meta['_uni_cpo_ordering_disabled_notice'][0] ) ) {
                echo '<div class="js-uni-cpo-ordering-disabled-notice">' . $product_meta['_uni_cpo_ordering_disabled_notice'][0] . '</div>';
            }
            
            if ( ! empty( $product_options ) ) {
                foreach ( $product_options as $structure_item ) {
                    $option = uni_cpo_get_option( $structure_item['id'] );

                    if ( $option instanceof Uni_Cpo_Option && $option->id ) {
                        $option->render_option();
                    }
                }
            }

            echo '<div style="clear:both;"></div>';

            echo '</div>'; // end uni_cpo_options_box

        }

    }

    /**
     * save_custom_meta
     */
    function save_custom_meta( $term_id ) {
        if ( isset( $_POST['uni_cpo_tax_discounts'] ) ) {
            update_term_meta( $term_id, '_uni_cpo_tax_discounts', $_POST['uni_cpo_tax_discounts'] );
        }
    }

    /**
     * uni_tax_custom_data
     */
    function tax_custom_data( $oTerm ) {
        $aRawUserRoles = get_editable_roles();
        $aTermMeta = get_term_meta( $oTerm->term_id, '_uni_cpo_tax_discounts', true );
        ?>
        <tr class="form-field term-parent-wrap">
            <th scope="row"><legend><?php esc_html_e('Uni CPO User Role Based Discounts', 'uni-cpo') ?></legend></th>
            <td></td>
        </tr>
        <?php
        foreach ( $aRawUserRoles as $sKey => $aValue ) {
            $sType = ( isset($aTermMeta[$sKey]['type']) ) ? $aTermMeta[$sKey]['type'] : '';
            $sValue = ( isset($aTermMeta[$sKey]['value']) ) ? $aTermMeta[$sKey]['value'] : '';
        ?>
        <tr class="form-field term-parent-wrap">
        <th scope="row"><label><?php echo $aValue['name'] ?></label></th>
            <td>
                <label><?php echo esc_html__('Type', 'uni-cpo') ?></label>
                <select name="uni_cpo_tax_discounts[<?php echo $sKey ?>][type]" class="postform">';
                    <option value="percentage"<?php selected('percentage', $sType) ?>><?php echo esc_html__('Percentage', 'uni-cpo') ?></option>
                    <option value="amount"<?php selected('amount', $sType) ?>><?php echo esc_html__('Fixed amount', 'uni-cpo') ?></option>
                    <option value="price"<?php selected('price', $sType) ?>><?php echo esc_html__('Fixed price', 'uni-cpo') ?></option>
                </select><br>
                <label><?php echo esc_html__('Value', 'uni-cpo') ?></label>
                <input type="text" name="uni_cpo_tax_discounts[<?php echo $sKey ?>][value]" value="<?php echo $sValue ?>">
            </td>
        </tr>
        <?php
        }
        ?>
    <?php
    }

    /**
     * check_version()
     */
    public function check_version() {

        $sCurrentVersion = get_option( 'uni_cpo_version', null );

        if ( is_null( $sCurrentVersion ) ) {
            update_option( 'uni_cpo_version', $this->version );
        }

        if ( ! defined( 'IFRAME_REQUEST' ) && !empty( $plugin_updates ) && version_compare( $sCurrentVersion, max( array_keys( self::$plugin_updates ) ), '<' ) ) {
            $this->update_plugin();
            do_action( 'uni_cpo_updated' );
        }
    }

    /**
     * update_plugin()
     */
    private function update_plugin() {
        // Silence
    }

    /**
     * plugin_url()
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', __FILE__ ) );
    }

    /**
     * plugin_path()
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * ajax_url()
     */
    public function ajax_url() {
        return admin_url( 'admin-ajax.php' );
    }

    /**
     * cpo_activation()
     */
    public function cpo_activation( $plugin ) {
        if( $plugin == plugin_basename( 'uni-woo-custom-product-options/uni-cpo.php' ) ) {
            // redirects to 'about' admin page
            exit( wp_redirect( admin_url( 'admin.php?page=cpo-about' ) ) );
        }
    }

}

endif;

/**
 *  The main object
 */
function UniCpo() {
    return Uni_Cpo::instance();
}

// Global for backwards compatibility.
$GLOBALS['unicpo'] = UniCpo();
?>