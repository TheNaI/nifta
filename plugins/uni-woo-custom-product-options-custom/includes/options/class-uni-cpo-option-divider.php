<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_divider class
*
*/

class Uni_Cpo_Option_divider extends Uni_Cpo_Option {

    public $id = 0;

    public $post = null;

    public $option_type = 'divider';

    public $option_icon = '';

    public $option_name = '';

    public $tab_settings = array();

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

        $this->option_icon = 'fa-paragraph';
        $this->option_name = esc_html__('Divider', 'uni-cpo');
        $this->tab_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_settings_filter", array(
                                    array(
                                        'section_title' => esc_html__('General settings', 'uni-cpo'),
                                        'settings' => array('field_slug')
                                    ),
                                    array(
                                        'section_title' => esc_html__('Display settings', 'uni-cpo'),
                                        'settings' => array('field_divider_type', 'field_extra_class')
                                    ),
                                    array(
                                        'section_title' => esc_html__('Conditional logic', 'uni-cpo'),
                                        'settings' => array('field_conditional_enable', 'field_conditional_default', 'field_conditional_scheme')
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

        do_action( 'uni_cpo_before_option', $this );

        if ( !empty( $aOptionCustomMeta['_uni_cpo_field_divider_type'][0] ) && $aOptionCustomMeta['_uni_cpo_field_divider_type'][0] == 'hr' ) {
            echo '<'.esc_attr($aOptionCustomMeta['_uni_cpo_field_divider_type'][0]).' id="'.esc_attr( $sElementFieldName ).'" data-type="' . esc_attr( $this->get_type() ) . '" class="uni_cpo_fields_header uni_cpo_field_type_' . esc_attr( $this->get_type() ) . ( (!empty($aOptionCustomMeta['_uni_cpo_field_extra_class'][0])) ? ' '.esc_attr( $aOptionCustomMeta['_uni_cpo_field_extra_class'][0] ) : '' ) . '">';
        } else if ( !empty( $aOptionCustomMeta['_uni_cpo_field_divider_type'][0] ) && $aOptionCustomMeta['_uni_cpo_field_divider_type'][0] != 'hr' ) {
            echo '<div id="'.esc_attr( $sElementFieldName ).'" class="uni_cpo_fields_header uni_cpo_field_type_' . esc_attr( $this->get_type() ) . ' uni-cpo-divider-type-'.esc_attr($aOptionCustomMeta['_uni_cpo_field_divider_type'][0]) . ( (!empty($aOptionCustomMeta['_uni_cpo_field_extra_class'][0])) ? ' '.esc_attr( $aOptionCustomMeta['_uni_cpo_field_extra_class'][0] ) : '' ) . '">';
            echo'</div>';
        }

        // the field conditional logic
        $this->render_fc_rules();

        do_action( 'uni_cpo_after_option', $this );

	}
    
	/**
	 * Retrieves value for formula calculation
	 *
	 */
	public function calculation( $aFormPostData, $bCartMeta = false ) {
	    return false;
	}

}

?>