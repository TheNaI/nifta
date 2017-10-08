<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_text_input class
*
*/

class Uni_Cpo_Option_google_maps extends Uni_Cpo_Option {

    public $id = 0;

    public $post = null;

    public $option_type = 'google_maps';

    public $option_icon = '';

    public $option_name = '';

    public $tab_settings = array();

    public $specific_settings = array();

    protected $calc_status = true;

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

        $this->option_icon = 'fa-map-marker';
        $this->option_name = esc_html__('Google Map', 'uni-cpo');
        $this->tab_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_settings_filter", array(
                    array(
                        'section_title' => esc_html__('General settings', 'uni-cpo'),
                        'settings' => array(
                            'field_slug',
                            'field_required_enable', 
                            'field_map_type',
                            'field_base_location',
                            'field_label_start',
                            'field_label_end',
                            //'field_label_distance',
                            'field_map_center',
                            'field_map_zoom',
                            'field_travel_mode',
                            'field_map_mode',
                            'field_disable_map_ui',
                            'field_option_price'
                        )
                    ),
                    array(
                        'section_title' => esc_html__('Display settings', 'uni-cpo'),
                        'settings' => array(
                             'field_header_type',
                             'field_header_text',
                             'field_header_tooltip_type',
                             'field_header_tooltip_text',
                             'field_header_tooltip_image',
                             'field_extra_tooltip_selector_class',
                             'field_extra_class',
                             'field_meta_header_text'
                         )
                    ),
                    array(
                        'section_title' => esc_html__('Conditional logic', 'uni-cpo'),
                        'settings' => array(
                             'field_conditional_enable',
                             'field_conditional_default',
                             'field_conditional_scheme'
                        )
                    )
                )
        );

        $this->specific_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_specific_settings_filter", 

                array( 
                    'field_map_type' => array(
                        'select' => array(
                            'title' => esc_html__('Type', 'uni-cpo'),
                            'tip' => esc_html__('Choose among three modes of using this option', 'uni-cpo'),
                            'options' => array(
                                'one_location' => esc_html__('Customer\'s address', 'uni-cpo'),
                                'two_locations' => esc_html__('Customer\'s start and end adresses (with distance)', 'uni-cpo'),
                                'cargo_calculator' => esc_html__('Cargo calculator ("from", "to" and distance)', 'uni-cpo'),
                                'distance_to_base' => esc_html__('Customer\'s address and distance to the base', 'uni-cpo'),
                            ),
                            'name' => 'field_map_type',
                            'required' => false
                        )
                    ),
                    'field_base_location' => array(
                        'mappicker' => array(
                            'title' => esc_html__('Base location', 'uni-cpo'),
                            'tip' => '',
                            'type' => 'text',
                            'name' => 'field_base_location',
                            'validation_pattern' => '/^-?\d+\.\d*?,-?\d+\.\d*?$/',
                            'required' => false
                        )
                    ),
                    'field_label_start' => array(
                        'text_input' => array(
                            'title' => esc_html__('Label for Initial Location input', 'uni-cpo'),
                            'tip' => '',
                            'type' => 'text',
                            'name' => 'field_label_start',
                            'validation_pattern' => '',
                            'required' => false
                        )
                    ),
                    'field_label_end' => array( 
                        'text_input' => array(
                            'title' => esc_html__('Label for Address input', 'uni-cpo'),
                            'tip' => '',
                            'type' => 'text',
                            'name' => 'field_label_end',
                            'validation_pattern' => '',
                            'required' => false
                        )
                    ),
                    'field_label_distance' => array(
                        'text_input' => array(
                            'title' => esc_html__('Label for Distance input', 'uni-cpo'),
                            'tip' => '',
                            'type' => 'text',
                            'name' => 'field_label_distance',
                            'validation_pattern' => '',
                            'required' => false
                        )
                    ),
                    'field_map_center' => array(
                        'text_input' => array(
                            'title' => esc_html__('Coordinates of the map center', 'uni-cpo'),
                            'tip' => '',
                            'type' => 'text',
                            'name' => 'field_map_center',
                            'validation_pattern' => '/^-?\d+\.\d*?,-?\d+\.\d*?$/',
                            'required' => false
                        )
                    ),
                    'field_map_zoom' => array(
                        'text_input' => array(
                            'title' => esc_html__('Zoom', 'uni-cpo'),
                            'tip' => '',
                            'type' => 'number',
                            'name' => 'field_map_zoom',
                            'required' => false
                        )
                    ),
                    'field_travel_mode' => array( 
                        'select' => array(
                            'title' => esc_html__('Travel mode', 'uni-cpo'),
                            'tip' => esc_html__('Google Map travel mode', 'uni-cpo'),
                            'options' => array(
                                'DRIVING' => esc_html__( 'DRIVING', 'uni-cpo' ),
                                'WALKING' => esc_html__( 'WALKING', 'uni-cpo' ),
                                'BICYCLING' => esc_html__( 'BICYCLING', 'uni-cpo' ),
                                'TRANSIT' => esc_html__( 'TRANSIT', 'uni-cpo' )
                            ),
                            'name' => 'field_travel_mode',
                            'required' => false
                        )
                    ),
                    'field_map_mode' => array(
                        'select' => array(
                            'title' => esc_html__('Type of the map', 'uni-cpo'),
                            'tip' => esc_html__('Google Map map mode', 'uni-cpo'),
                            'options' => array(
                                'roadmap' => esc_html__( 'Roadmap', 'uni-cpo' ),
                                'satellite' => esc_html__( 'Satellite', 'uni-cpo' ),
                                'hybrid' => esc_html__( 'Hybrid', 'uni-cpo' ),
                                'terrain' => esc_html__( 'Terrain', 'uni-cpo' )
                            ),
                            'name' => 'field_map_mode',
                            'required' => false
                        )
                    ),
                    'field_disable_map_ui' => array(
                        'checkboxes' => array(
                            'title' => esc_html__('Disable the default map UI', 'uni-cpo'),
                            'tip' => '',
                            'options' => array(
                                'yes' => 'Yes'
                            ),
                            'name' => 'field_disable_map_ui',
                            'required' => false
                        )
                    ),
                    /*'field_disable_autocomplet_fields' => array(
                        'checkboxes' => array(
                            'title' => esc_html__('Disable autocomplete', 'uni-cpo'),
                            'tip' => esc_html__('Disable autocomplete functionality', 'uni-cpo'),
                            'options' => array(
                                'yes' => 'Yes'
                            ),
                            'name' => 'field_disable_autocomplet_fields',
                            'required' => false
                        )
                    ),*/
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
        $sInputValueEndDate = $sInputValueStartDate = '';
        $aAllowedHtml       = uni_cpo_allowed_html_for_option_titles();
        $aAllowedTootltipHtml = uni_cpo_allowed_html_for_tooltips();
        $tooltip_icon_html  = uni_cpo_option_tooltip_icon_output( $this );
        $bFieldIsRequired   = false; // default value

        // defines requireness of the field
        if ( !empty($aOptionCustomMeta['_uni_cpo_field_required_enable'][0]) ) {
            if ( $aOptionCustomMeta['_uni_cpo_field_required_enable'][0] === 'yes' ) {
                $bFieldIsRequired   = true;
            }
        }

        $required_class = '';
        $required_data = '';
        $sInputValueDistance = $sInputValueLocationStart = $sInputValueLocationEnd = '';

        // is required
        if ( $bFieldIsRequired ) {
            $required_class = ' uni-cpo-required';
        }

        $map_type = 'one_location';
        if( ! empty( $aOptionCustomMeta['_uni_cpo_field_map_type'][0] ) ){
            $map_type = $aOptionCustomMeta['_uni_cpo_field_map_type'][0];
        }

        //  css classes
        $class_wrap_field = '';
        if( ! empty( $aOptionCustomMeta['_uni_cpo_field_extra_class'][0] ) ) {
            $class_wrap_field = sanitize_html_class( $aOptionCustomMeta['_uni_cpo_field_extra_class'][0] );
        }

        //  parsley pattern 
        $sParsleyPattern = '';
        if ( ! empty( $aOptionCustomMeta['_uni_cpo_field_input_parsley_pattern'][0] ) ) {
            $sParsleyPattern = $aOptionCustomMeta['_uni_cpo_field_input_parsley_pattern'][0];
        } 

        // tooltip type
        if ( ! empty( $aOptionCustomMeta['_uni_cpo_field_header_tooltip_type'][0] ) ) {
            $sTooltipType = $aOptionCustomMeta['_uni_cpo_field_header_tooltip_type'][0];
        } else {
            $sTooltipType = 'classic';
        }

        // get start locations
        $sInputValueBaseLocation = '';
        if ( ! empty( $aOptionCustomMeta['_uni_cpo_field_base_location'][0] ) && $map_type == 'distance_to_base' ) {
            $sInputValueBaseLocation = $aOptionCustomMeta['_uni_cpo_field_base_location'][0];
        } 

        $travel_mode = 'DRIVING';
        if ( ! empty( $aOptionCustomMeta['_uni_cpo_field_travel_mode'][0] ) ) {
            $travel_mode = $aOptionCustomMeta['_uni_cpo_field_travel_mode'][0];
        }

        /*$disable_all_ui = 'false';
        $disable_autocomplet_fields = array();
        if ( !empty($aOptionCustomMeta['_uni_cpo_field_disable_ui'][0]) ) {
            $disable_autocomplet_fields = maybe_unserialize( $aOptionCustomMeta['_uni_cpo_field_disable_ui'][0] );
        }
        if (in_array('yes', $disable_autocomplet_fields)) {
           $disable_all_ui = 'true';
        }*/

        $map_mode = 'roadmap';
        if ( !empty($aOptionCustomMeta['_uni_cpo_field_type_map'][0]) ) {
            $map_mode = $aOptionCustomMeta['_uni_cpo_field_type_map'][0];
        }

        $map_ui_disable = '';
        if ( isset( $aOptionCustomMeta['_uni_cpo_field_disable_map_ui'][0] ) && ! empty( $aOptionCustomMeta['_uni_cpo_field_disable_map_ui'][0] ) ) {
            $map_ui_disable_meta = $aOptionCustomMeta['_uni_cpo_field_disable_map_ui'][0];
            $map_ui_disable_meta = maybe_unserialize( $map_ui_disable_meta );
            if ( isset( $map_ui_disable_meta['yes'] ) && 'yes' === $map_ui_disable_meta['yes'] ) {
                $map_ui_disable = 'yes';
            }
        }

        $map_center = ( isset( $aOptionCustomMeta['_uni_cpo_field_map_center'][0] ) && ! empty( $aOptionCustomMeta['_uni_cpo_field_map_center'][0] ) ) ? $aOptionCustomMeta['_uni_cpo_field_map_center'][0] : '-33.8688,151.2195';
        $map_zoom = ( isset( $aOptionCustomMeta['_uni_cpo_field_map_zoom'][0] ) && ! empty( $aOptionCustomMeta['_uni_cpo_field_map_zoom'][0] ) ) ? $aOptionCustomMeta['_uni_cpo_field_map_zoom'][0] : '14';

        /* get POST data */
        if ( ! empty( $_POST[$sElementFieldName.'_start'] ) ) {
            $sInputValueLocationStart = $_POST[$sElementFieldName.'_start'];
        }
        if ( ! empty( $_POST[$sElementFieldName] ) ) {
            $sInputValueLocationEnd = $_POST[$sElementFieldName];
        }
        if ( ! empty( $_POST[$sElementFieldName.'_distance'] ) ) {
            $sInputValueDistance = $_POST[$sElementFieldName.'_distance'];
        }

        do_action( 'uni_cpo_before_option', $this );
        ?>

        <div 
            id="<?php echo esc_attr( $sElementFieldName  ); ?>" 
            data-type="<?php echo esc_attr( $this->get_type() ); ?>" 
            data-map-type="<?php echo esc_attr( $map_type ); ?>"
            data-map-center="<?php echo esc_attr( $map_center ); ?>"
            data-map-zoom="<?php echo esc_attr( $map_zoom ); ?>"
            data-base-location="<?php echo esc_attr( $sInputValueBaseLocation ); ?>"
            data-travel-mode="<?php echo esc_attr( $travel_mode ); ?>"
            data-map-mode="<?php echo esc_attr( $map_mode ); ?>"
            data-map-ui-disable="<?php echo esc_attr( $map_ui_disable ); ?>"
            class="uni_cpo_fields_container 
                   uni_cpo_field_type_<?php echo esc_attr( $this->get_type() ); ?> <?php echo esc_attr( $class_wrap_field );  ?>" >
            <?php
            if ( !empty( $aOptionCustomMeta['_uni_cpo_field_header_text'][0] ) && !empty( $aOptionCustomMeta['_uni_cpo_field_header_type'][0] ) ) {
                echo '<'.esc_attr($aOptionCustomMeta['_uni_cpo_field_header_type'][0]).' class="uni_cpo_fields_header">';
                echo wp_kses( $aOptionCustomMeta['_uni_cpo_field_header_text'][0], $aAllowedHtml ) . ( ( $bFieldIsRequired ) ? ' <span class="uni-cpo-required-label">*</span>' : '' );

                // tooltips
                if ( $sTooltipType == 'classic' ) {
                    if ( !empty($aOptionCustomMeta['_uni_cpo_field_header_tooltip_text'][0]) && empty($aOptionCustomMeta['_uni_cpo_field_extra_tooltip_selector_class'][0]) ) {
                        echo ' <span class="uni-cpo-tooltip" data-tooltip-content="#uni-cpo-tooltip-'.esc_attr( $sElementFieldName ).'">'.$tooltip_icon_html.'</span>';
                        echo '<div class="tooltip_templates"><div id="uni-cpo-tooltip-'.esc_attr( $sElementFieldName ).'">'.wp_kses( $aOptionCustomMeta['_uni_cpo_field_header_tooltip_text'][0], $aAllowedTootltipHtml ).'</div></div>';
                    } else if ( !empty($aOptionCustomMeta['_uni_cpo_field_header_tooltip_text'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_extra_tooltip_selector_class'][0]) && $aOptionCustomMeta['_uni_cpo_field_extra_tooltip_selector_class'][0] !== 'uni-cpo-custom-tooltip' ) {
                        echo ' <span class="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_extra_tooltip_selector_class'][0]).' uni-cpo-tooltip-element" title="'.esc_attr(wp_kses( $aOptionCustomMeta['_uni_cpo_field_header_tooltip_text'][0], $aAllowedTootltipHtml )).'">'.$tooltip_icon_html.'</span>';
                    } else if ( !empty($aOptionCustomMeta['_uni_cpo_field_header_tooltip_text'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_extra_tooltip_selector_class'][0]) && $aOptionCustomMeta['_uni_cpo_field_extra_tooltip_selector_class'][0] === 'uni-cpo-custom-tooltip' ) {
                        echo ' <div class="uni-cpo-custom-tooltip-element">';
                        echo $tooltip_icon_html;
                            echo '<div class="uni-cpo-custom-tooltip-content">';
                            echo wp_kses( $aOptionCustomMeta['_uni_cpo_field_header_tooltip_text'][0], $aAllowedTootltipHtml );
                            echo '</div>';
                        echo '</div>';
                    }
                } else if ( $sTooltipType == 'lightbox' ) {
                    $iThumbId = intval($aOptionCustomMeta['_uni_cpo_field_header_tooltip_image'][0]);
                    $aImage = wp_get_attachment_image_src( $iThumbId, 'full' );
                    $sImageUrl = $aImage[0];
                    echo '<a href="' . esc_url($sImageUrl) . '" data-lity data-lity-desc="" class="uni-cpo-tooltip-element">';
                    echo $tooltip_icon_html;
                    echo '</a>';
                }

                echo'</'.esc_attr($aOptionCustomMeta['_uni_cpo_field_header_type'][0]).'>';
            }
            ?>

            <?php
            $loc_start_label = ( isset( $aOptionCustomMeta['_uni_cpo_field_label_start'][0] ) && ! empty( $aOptionCustomMeta['_uni_cpo_field_label_start'][0] ) ) ? $aOptionCustomMeta['_uni_cpo_field_label_start'][0] : '';
            $loc_end_label = ( isset( $aOptionCustomMeta['_uni_cpo_field_label_end'][0] ) && ! empty( $aOptionCustomMeta['_uni_cpo_field_label_end'][0] ) ) ? $aOptionCustomMeta['_uni_cpo_field_label_end'][0] : '';

            $disable_autocomplet_fields = array();
            if ( !empty($aOptionCustomMeta['_uni_cpo_field_disable_autocomplet_fields'][0]) ) {

                $disable_autocomplet_fields = maybe_unserialize( $aOptionCustomMeta['_uni_cpo_field_disable_autocomplet_fields'][0] );
            }
            if ( $bFieldIsRequired ) {
            ?>

                <?php if ( 'two_locations' === $map_type || 'cargo_calculator' === $map_type ): ?>
                <span class="uni-cpo-label-location uni-cpo-label-location-start">
                    <?php echo esc_html( $loc_start_label ); ?>
                </span>
                <!-- Location start -->
                <input type="text"
                       id="<?php echo esc_attr( $sElementFieldName ); ?>-field-start"
                       name="<?php echo esc_attr( $sElementFieldName ); ?>_start"
                       class="<?php echo esc_attr( $sElementFieldName ); ?>-field
                       js-uni-cpo-field-<?php echo esc_attr( $this->get_type() ); ?>
                       js-uni-cpo-field-<?php echo esc_attr( $this->get_type() ); ?>-start
                       <?php echo esc_attr( $required_class ); ?>"
                       value="<?php //echo esc_attr( $sInputValueLocationStart ); ?>"
                       data-parsley-required="true" data-parsley-trigger="change focusout submit"
                       <?php echo ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') ; ?> />
                <?php endif ?>

                <!-- Location end -->
                <span class="uni-cpo-label-location uni-cpo-label-location-end">
                    <?php echo esc_html( $loc_end_label ); ?>
                </span>
                <input type="text"
                       id="<?php echo esc_attr( $sElementFieldName ); ?>-field"
                       name="<?php echo esc_attr( $sElementFieldName ); ?>"
                       class="<?php echo esc_attr( $sElementFieldName ); ?>-field
                            js-uni-cpo-field-<?php echo esc_attr( $this->get_type() ); ?>
                            js-uni-cpo-field-<?php echo esc_attr( $this->get_type() ); ?>-single
                            <?php echo esc_attr( $required_class ); ?>"
                       value="<?php //echo esc_attr($sInputValueLocationEnd); ?>"
                       data-parsley-required="true" data-parsley-trigger="change focusout submit"
                       <?php echo ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') ; ?> />

            <input id="<?php echo esc_attr( $sElementFieldName ); ?>-field-distance"
                  name="<?php echo esc_attr( $sElementFieldName ); ?>_distance"
                  value="<?php //echo esc_attr($sInputValueDistance); ?>"
                  class="uni-cpo-required"
                  data-parsley-required="true" data-parsley-trigger="change focusout submit"
                  type="hidden" />

            <?php
            } else {
            ?>

                <?php if ( 'two_locations' === $map_type || 'cargo_calculator' === $map_type ): ?>
                <span class="uni-cpo-label-location uni-cpo-label-location-start">
                    <?php echo esc_html( $loc_start_label ); ?>
                </span>
                <!-- Location start -->
                <input type="text"
                       id="<?php echo esc_attr( $sElementFieldName ); ?>-field-start"
                       name="<?php echo esc_attr( $sElementFieldName ); ?>_start"
                       class="<?php echo esc_attr( $sElementFieldName ); ?>-field
                       js-uni-cpo-field-<?php echo esc_attr( $this->get_type() ); ?>
                       js-uni-cpo-field-<?php echo esc_attr( $this->get_type() ); ?>-start
                       <?php echo esc_attr( $required_class ); ?>"
                       value="<?php //echo esc_attr( $sInputValueLocationStart ); ?>" />
                <?php endif ?>

                <!-- Location end -->
                <span class="uni-cpo-label-location uni-cpo-label-location-end">
                    <?php echo esc_html( $loc_end_label ); ?>
                </span>
                <input type="text"
                       id="<?php echo esc_attr( $sElementFieldName ); ?>-field"
                       name="<?php echo esc_attr( $sElementFieldName ); ?>"
                       class="<?php echo esc_attr( $sElementFieldName ); ?>-field
                            js-uni-cpo-field-<?php echo esc_attr( $this->get_type() ); ?>
                            js-uni-cpo-field-<?php echo esc_attr( $this->get_type() ); ?>-single
                            <?php echo esc_attr( $required_class ); ?>"
                       value="<?php //echo esc_attr($sInputValueLocationEnd); ?>" />

            <input id="<?php echo esc_attr( $sElementFieldName ); ?>-field-distance"
                  name="<?php echo esc_attr( $sElementFieldName ); ?>_distance"
                  value="<?php //echo esc_attr($sInputValueDistance); ?>"
                  type="hidden" />

            <?php
            }
            ?>

            <div class="uni-cpo-mappicker"></div>

            <div class="uni-cpo-clear"></div>

            <?php
            // the field conditional logic
            $this->render_fc_rules();
            ?>
        </div>

        <?php 

        do_action( 'uni_cpo_after_option', $this );

    }

    /**
     * gets form field(s) for option for add/edit options modal on order edit page
     *
     */
    public function get_order_form_field( $posted_data = array() ) {
        $option_meta    = $this->get_post_meta();

        $map_type = 'one_location';
        // sets actual type of picker
        if ( ! empty( $option_meta['_uni_cpo_field_map_type'][0] ) ) {
            $map_type = $option_meta['_uni_cpo_field_map_type'][0];
        }
        $output = '<tr>';

        $output .= '<th><label>' . esc_html( $this->get_label() ) . ' <i>(' . esc_html( $this->get_slug() ) . ')</i></label></th>';
        $output .= '<td><input type="text" name="' . esc_attr( $this->get_slug() ) . '" value="' . ( isset( $posted_data[$this->get_slug()] ) ? $posted_data[$this->get_slug()] : '' ) . '" /></td>';

        $output .= '</tr>';

        if ( ! empty( $this->get_special_vars() ) && in_array( $map_type, array( 'two_locations', 'cargo_calculator', 'distance_to_base' ) ) ) {
            foreach ( $this->get_special_vars() as $var_suffix ) {
                $output .= '<tr class="cpo_order_item_options_special_var">';

                $output .= '<th><label><i>(' . esc_html( $this->get_slug() . '_' . $var_suffix ) . ')</i></label></th>';
                $output .= '<td><input type="text" name="' . esc_attr( $this->get_slug() . '_' . $var_suffix ) . '" value="' . ( isset( $posted_data[$this->get_slug() . '_' . $var_suffix] ) ? $posted_data[$this->get_slug() . '_' . $var_suffix] : '' ) . '" /></td>';

                $output .= '</tr>';
            }
        }

        return $output;
    }

    /**
	 * Returns an array of special vars associated with the option
	 *
	 */
	public function get_special_vars() {
		return array('distance');
	}

    /**
     * Generates query builder array of map
     *
     */
    public function generate_filter( $bFull = false ) {

        $aFilterArray[] = array(
            'id' => $this->post->post_name,
            'label' => $this->post->post_title,
            'type' => 'string',
            'input' => 'text',
            'operators' => array( 'equal', 'not_equal', 'is_empty', 'is_not_empty' )
        );

        if ( $bFull ) {

            $sPostName = trim($this->post->post_title, '{');
            $sPostName = trim($sPostName, '}');
            $aFilterArray[] = array(
                'id' => $this->post->post_name.'_distance',
                'label' => '{'.$sPostName.'_distance}',
                'type' => 'string',
                'input' => 'text',
                'operators' => array( 'less', 'less_or_equal', 'equal', 'not_equal', 'greater_or_equal', 'greater', 'is_empty', 'is_not_empty' )
            );

        }

        return $aFilterArray;
    }

    /**
     * Generates an array of vars and values for formula calculation associated with this option
     *
     */
    public function calculation( $aFormPostData, $bCartMeta = false ) {

        $sElementFieldName  = trim($this->post->post_title, '{}');
        $aOptionCustomMeta  = $this->get_post_meta();

        if ( isset( $aFormPostData[$sElementFieldName] ) && ! empty( $aFormPostData[$sElementFieldName] ) ) {
            $price = floatval( get_post_meta( $this->get_id(), '_uni_cpo_field_option_price', true ) );
            // it is a distance picker (google maps)
            if ( ! empty( $aFormPostData[$sElementFieldName.'_distance'] ) ) {
                // joins two start and end locations
                if (  ! empty( $aFormPostData[$sElementFieldName.'_start'] ) ) {
                    $route_start = $aFormPostData[$sElementFieldName.'_start'];
                }
                if ( ! $bCartMeta ) {
                    if ( ! empty( $price ) ) {
                        return  array(
                            $sElementFieldName => $price,
                            $sElementFieldName.'_distance' => intval( $aFormPostData[$sElementFieldName.'_distance'] )
                        );
                    } else {
                        return  array(
                            $sElementFieldName => floatval( $aFormPostData[$sElementFieldName] ),
                            $sElementFieldName.'_distance' => intval( $aFormPostData[$sElementFieldName.'_distance'] )
                        );
                    }
                } else if ( $bCartMeta == 'cart' ) {
                    return  array(
                        $sElementFieldName.'_start' => $route_start,
                        $sElementFieldName => $aFormPostData[$sElementFieldName],
                        $sElementFieldName.'_distance' => intval( $aFormPostData[$sElementFieldName.'_distance'] )
                    );
                } else {
                    $distance_formatted = apply_filters( 'uni_cpo_google_maps_distance_formatted_for_order', '(' . intval( $aFormPostData[$sElementFieldName.'_distance'] ) . ' m)', $this );
                    return ( ( ! empty($route_start) ) ? $route_start . ' - ' : '' ) . $aFormPostData[$sElementFieldName] . ' ' . $distance_formatted;
                }
            } else {
                if ( ! $bCartMeta ) {
                    if ( ! empty( $price ) ) {
                        return $price;
                    } else {
                        return $aFormPostData[$sElementFieldName];
                    }
                } else {
                    return $aFormPostData[$sElementFieldName];
                }
            }
        } else {
            if ( ! $bCartMeta ) {
                return '0';
            } else {
                return '';
            }
        }

    }

}

?>