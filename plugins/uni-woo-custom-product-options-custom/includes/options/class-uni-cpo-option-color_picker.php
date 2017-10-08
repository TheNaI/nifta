<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_color_picker class
*
*/

class Uni_Cpo_Option_color_picker extends Uni_Cpo_Option {

    public $id = 0;

    public $post = null;

    public $option_type = 'color_picker';

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

        $this->option_icon = 'fa-paint-brush';
        $this->option_name = esc_html__('Color picker', 'uni-cpo');
        $this->tab_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_settings_filter", array(
                                    array(
                                        'section_title' => esc_html__('General settings', 'uni-cpo'),
                                        'settings' => array(
                                                            'field_slug',
                                                            'field_required_enable',
                                                            'field_colorpicker_tag_type',
                                                            'field_colorpicker_number_step',
                                                            'field_colorpicker_number_default',
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
                                    ),
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
        $sInputValue        = '';
        $aAllowedHtml       = uni_cpo_allowed_html_for_option_titles();
        $aAllowedTootltipHtml = uni_cpo_allowed_html_for_tooltips();
        $tooltip_icon_html  = uni_cpo_option_tooltip_icon_output( $this );


        if ( isset($_POST[$sElementFieldName]) && !empty($_POST[$sElementFieldName]) ) {
            $sInputValue = $_POST[$sElementFieldName];
        } else if ( isset($aOptionCustomMeta['_uni_cpo_field_input_number_default'][0]) ) {
            $sInputValue = esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_default'][0]);
        }

        if ( !isset($aOptionCustomMeta['_uni_cpo_field_header_tooltip_type'][0]) || empty($aOptionCustomMeta['_uni_cpo_field_header_tooltip_type'][0]) ) {
            $sTooltipType = 'classic';
        } else {
            $sTooltipType = $aOptionCustomMeta['_uni_cpo_field_header_tooltip_type'][0];
        }

        $sParsleyPattern = '';
        if ( isset($aOptionCustomMeta['_uni_cpo_field_input_parsley_pattern'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_input_parsley_pattern'][0]) ) {
            $sParsleyPattern = ' data-parsley-pattern="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_parsley_pattern'][0]).'"';
        }

        do_action( 'uni_cpo_before_option', $this );

        echo '<div id="'.esc_attr( $sElementFieldName ).'" data-type="' . esc_attr( $this->get_type() ) . '" class="uni_cpo_fields_container uni_cpo_field_type_'.esc_attr( $this->get_type() ) . ( (!empty($aOptionCustomMeta['_uni_cpo_field_extra_class'][0])) ? ' '.esc_attr( $aOptionCustomMeta['_uni_cpo_field_extra_class'][0] ) : '' ) .'">';


            if ( !empty( $aOptionCustomMeta['_uni_cpo_field_header_text'][0] ) && !empty( $aOptionCustomMeta['_uni_cpo_field_header_type'][0] ) ) {
                echo '<'.esc_attr($aOptionCustomMeta['_uni_cpo_field_header_type'][0]).' class="uni_cpo_fields_header">';
                echo wp_kses( $aOptionCustomMeta['_uni_cpo_field_header_text'][0], $aAllowedHtml ) . ( ( !empty($aOptionCustomMeta['_uni_cpo_field_required_enable'][0]) && $aOptionCustomMeta['_uni_cpo_field_required_enable'][0] == 'yes' ) ? ' <span class="uni-cpo-required-label">*</span>' : '' );

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


            $required_class = '';
            $required_data = '';

            if ( !empty($aOptionCustomMeta['_uni_cpo_field_required_enable'][0]) && $aOptionCustomMeta['_uni_cpo_field_required_enable'][0] == 'yes' ) { 

                $required_class = ' uni-cpo-required';
                $required_data  = ' data-parsley-required="true" data-parsley-trigger="change focusout submit"';

            } 
            echo '<input id="'.esc_attr( $sElementFieldName ).'-field" type="text" 
                class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).' ' . esc_attr($required_class) . '"
                ' . $required_data . '
                name="'.esc_attr( $sElementFieldName ).'"
                value="'.$sInputValue.'"
                ' . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .''.( (isset($aOptionCustomMeta['_uni_cpo_field_chars_min'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_chars_min'][0])) ? '
                data-parsley-minlength="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_chars_min'][0]).'"' : '' ).''.( (isset($aOptionCustomMeta['_uni_cpo_field_chars_max'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_chars_max'][0])) ? '
                data-parsley-maxlength="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_chars_max'][0]).'"' : '' ).'
                ' . (!empty($aOptionCustomMeta['_uni_cpo_field_option_price']) ? ' data-price="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_option_price'][0]).'"' : '') .
                '/>';

            ?>

            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery('.<?php echo esc_attr( $sElementFieldName ); ?>-field').wpColorPicker({
                        change: function(event, ui) {
                            jQuery( document.body ).trigger( 'cpo_options_colorpicker_after_change', [ ui, event] );
                            uni_cpo_form_processing();
                        },
                        clear: function(event, ui) {
                            uni_cpo_form_processing();
                        }
                    });
                });
            </script>
            <?php
            // the field conditional logic
            $this->render_fc_rules();

            echo '<div class="uni-cpo-clear"></div>';

        echo'</div>';

        do_action( 'uni_cpo_after_option', $this );

    }

    /**
     * gets form field(s) for option for add/edit options modal on order edit page
     *
     */
    public function get_order_form_field( $posted_data = array() ) {
        $option_meta    = $this->get_post_meta();

        $output = '<tr>';

        $output .= '<th><label>' . esc_html( $this->get_label() ) . ' <i>(' . esc_html( $this->get_slug() ) . ')</i></label></th>';
        $output .= '<td><input type="text" name="' . esc_attr( $this->get_slug() ) . '" value="' . ( isset( $posted_data[$this->get_slug()] ) ? $posted_data[$this->get_slug()] : '' ) . '" /></td>';

        $output .= '</tr>';

        if ( ! empty( $this->get_special_vars() ) ) {
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
     * Generates query builder array of data
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
        return $aFilterArray;
    }

    /**
     * Generates an array of vars and values for formula calculation associated with this option
     *
     */
    public function calculation( $aFormPostData, $bCartMeta = false ) {

        $sElementFieldName  = trim($this->post->post_title, '{}');

        if ( isset($aFormPostData[$sElementFieldName]) && !empty($aFormPostData[$sElementFieldName]) ) {
            if ( !$bCartMeta ) {
                // if price/rate for this option is set
                if ( get_post_meta( $this->get_id(), '_uni_cpo_field_option_price', true ) ) {
                    // return price of the field - it acts like a single option with its own price
                    return floatval( get_post_meta( $this->get_id(), '_uni_cpo_field_option_price', true ) );
                } else {
                    // return field value
                    return 0;
                }
            } else {
                return $aFormPostData[$sElementFieldName];
            }
        } else {
            if ( !$bCartMeta ) {
                return '0';
            } else {
                return '';
            }
        }

    }

}

?>