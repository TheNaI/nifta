<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_custom_js class
*
*/

class Uni_Cpo_Option_custom_js extends Uni_Cpo_Option {

    public $id = 0;

    public $post = null;

    public $option_type = 'custom_js';

    public $option_icon = '';

    public $option_name = '';

    public $tab_settings = array();

    public $specific_settings = array();

    protected $calc_status = false;

	/**
	 * Constructor gets the post object and sets the ID for the loaded option.
	 *
	 */
	public function __construct( $Option = false ) {

		if ( is_numeric( $Option ) ) {
			$this->id   = absint( $Option );
			$this->post = get_post( $this->id );
		} else if ( $Option instanceof Uni_Cpo_Option ) {
			$this->id   = absint( $Option->id );
			$this->post = $Option->post;
		} else if ( isset( $Option->ID ) ) {
			$this->id   = absint( $Option->ID );
			$this->post = $Option;
		}

        $this->option_icon = 'fa-code';
        $this->option_name = esc_html__('Custom JS', 'uni-cpo');
        $this->tab_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_settings_filter", array(
                                    array(
                                        'section_title' => esc_html__('General settings', 'uni-cpo'),
                                        'settings' => array('field_slug', 'field_option_custom_js', 'field_option_select_options_custom_js')
                                    ),
                                    array(
                                        'section_title' => esc_html__('Display settings', 'uni-cpo'),
                                        'settings' => array()
                                    )
                                )
                            );

        $this->specific_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_specific_settings_filter", array(
                                    'field_option_custom_js' => array( 'textarea_js' =>
                                        array(
                                            'title' => esc_html__('Custom JS code', 'uni-cpo'),
                                            'description' => esc_html__('Add some custom JS code', 'uni-cpo'),
                                            'doc_link' => '',
                                            'name' => 'field_option_custom_js',
                                            'required' => false
                                        )
                                    )
                                )
                            );

	}

	/**
	 * Displays option in the front end
	 *
	 */
	public function render_option( $aChildrenOptionsIds = array() ) {

        $aOptionCustomMeta  = $this->get_post_meta();
        $sElementFieldName  = $this->get_slug();

            $sCustomJs = ( !empty($aOptionCustomMeta['_uni_cpo_field_option_custom_js'][0]) && $aOptionCustomMeta['_uni_cpo_field_option_custom_js'][0] ) ? $aOptionCustomMeta['_uni_cpo_field_option_custom_js'][0] : '';

            if ( !empty($sCustomJs) ) {

                wp_add_inline_script( 'uni-cpo-front-footer', $sCustomJs );

            }

	}
    
    /**
	 * Generates query builder array of data
	 *
	 */
	public function generate_filter( $bFull = false ) {
	}

	/**
	 * Retrieves value for formula calculation
	 *
	 */
	public function calculation( $aFormPostData, $bCartMeta = false ) {
	}

}

?>