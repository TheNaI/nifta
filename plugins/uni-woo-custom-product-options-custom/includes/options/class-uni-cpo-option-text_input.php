<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_text_input class
*
*/

class Uni_Cpo_Option_text_input extends Uni_Cpo_Option {

    public $id = 0;

    public $post = null;

    public $option_type = 'text_input';

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

        $this->option_icon = 'fa-font';
        $this->option_name = esc_html__('Text field', 'uni-cpo');
        $this->tab_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_settings_filter", array(
                                    array(
                                        'section_title' => esc_html__('General settings', 'uni-cpo'),
                                        'settings' => array('field_slug', 'field_required_enable', 'field_input_tag_type',
                                                            'field_input_number_min', 'field_input_number_max', 'field_input_number_step',
                                                            'field_input_number_default', 'field_chars_min', 'field_chars_max', 'field_option_price')
                                    ),
                                    array(
                                        'section_title' => esc_html__('Display settings', 'uni-cpo'),
                                        'settings' => array('field_header_type', 'field_header_text',
                                            'field_header_tooltip_type', 'field_header_tooltip_text', 'field_header_tooltip_image',
                                            'field_extra_tooltip_selector_class', 'field_extra_class', 'field_meta_header_text')
                                    ),
                                    array(
                                        'section_title' => esc_html__('Conditional logic', 'uni-cpo'),
                                        'settings' => array('field_conditional_enable', 'field_conditional_default', 'field_conditional_scheme')
                                    ),
                                    array(
                                        'section_title' => esc_html__('Validation logic', 'uni-cpo'),
                                        'settings' => array('val_conditional_enable', 'val_conditional_scheme')
                                    )
                                )
                            );

        $this->specific_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_specific_settings_filter", array() );

	}

	/**
	 * Displays option in the front end
	 *
	 */
	public function render_option( $aChildrenOptionsIds = array() ) {

        $aOptionCustomMeta  = $this->get_post_meta();
        $sElementFieldName  = $this->get_slug();
        $sInputValue        = '';
        $sInputValueCount   = '';
        $aAllowedHtml       = uni_cpo_allowed_html_for_option_titles();
        $aAllowedTootltipHtml = uni_cpo_allowed_html_for_tooltips();
        $tooltip_icon_html  = uni_cpo_option_tooltip_icon_output( $this );

            if ( isset($_POST[$sElementFieldName]) && !empty($_POST[$sElementFieldName]) ) {
                $sInputValue = $_POST[$sElementFieldName];
                $sInputValueCount = ( isset( $_POST[$sElementFieldName.'_count'] ) && ! empty( $_POST[$sElementFieldName.'_count'] ) ) ? $_POST[$sElementFieldName.'_count'] : '';
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

                if ( isset($aOptionCustomMeta['_uni_cpo_field_input_tag_type'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_input_tag_type'][0]) ) {
                    switch ( $aOptionCustomMeta['_uni_cpo_field_input_tag_type'][0] ) {
                        case 'string':
                            if ( !empty($aOptionCustomMeta['_uni_cpo_field_required_enable'][0]) && $aOptionCustomMeta['_uni_cpo_field_required_enable'][0] == 'yes' ) {
                                echo '<input id="'.esc_attr( $sElementFieldName ).'-field" type="text" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).' uni-cpo-required" value="'.$sInputValue.'" data-parsley-required="true" data-parsley-trigger="change focusout submit"' . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .''.( (isset($aOptionCustomMeta['_uni_cpo_field_chars_min'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_chars_min'][0])) ? ' data-parsley-minlength="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_chars_min'][0]).'"' : '' ).''.( (isset($aOptionCustomMeta['_uni_cpo_field_chars_max'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_chars_max'][0])) ? ' data-parsley-maxlength="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_chars_max'][0]).'"' : '' ).' />';
                            } else {
                                echo '<input id="'.esc_attr( $sElementFieldName ).'-field" type="text" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).'" value="'.$sInputValue.'"' . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .''.( (isset($aOptionCustomMeta['_uni_cpo_field_chars_min'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_chars_min'][0])) ? ' data-parsley-minlength="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_chars_min'][0]).'"' : '' ).''.( (isset($aOptionCustomMeta['_uni_cpo_field_chars_max'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_chars_max'][0])) ? ' data-parsley-maxlength="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_chars_max'][0]).'"' : '' ).' />';
                            }
                        break;
                        case 'integer':
                            if ( !empty($aOptionCustomMeta['_uni_cpo_field_required_enable'][0]) && $aOptionCustomMeta['_uni_cpo_field_required_enable'][0] == 'yes' ) {
                                echo '<input id="'.esc_attr( $sElementFieldName ).'-field" type="number" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).' uni-cpo-required" value="'.$sInputValue.'" data-parsley-type="integer" data-parsley-required="true" data-parsley-trigger="change focusout submit"' . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_min'][0])) ? ' min="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_min'][0]).'"' : '' ) . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_max'][0])) ? ' max="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_max'][0]).'"' : '' ) . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_step'][0])) ? ' step="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_step'][0]).'"' : '' ) . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .' />';
                            } else {
                                echo '<input id="'.esc_attr( $sElementFieldName ).'-field" type="number" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).'" value="'.$sInputValue.'" data-parsley-type="integer" ' . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_min'][0])) ? ' min="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_min'][0]).'"' : '' ) . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_max'][0])) ? ' max="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_max'][0]).'"' : '' ) . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_step'][0])) ? ' step="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_step'][0]).'"' : '' ) . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .' />';
                            }
                        break;
                        case 'double':
                            $sGeneralValidationPatternDouble = '/^(\d+(?:[\.]\d{0,2})?)$/';
                            if ( isset($aOptionCustomMeta['_uni_cpo_field_input_number_step'][0]) ) {
                                $sNumberOfDecimals = uni_cpo_number_of_decimals( $aOptionCustomMeta['_uni_cpo_field_input_number_step'][0] );
                                if ( $sNumberOfDecimals !== 0 && $sNumberOfDecimals !== false && $sNumberOfDecimals <= 5 ) {
                                    $sGeneralValidationPatternDouble = '/^(\d+(?:[\.]\d{0,'.$sNumberOfDecimals.'})?)$/';
                                }
                            }
                            if ( !empty($aOptionCustomMeta['_uni_cpo_field_required_enable'][0]) && $aOptionCustomMeta['_uni_cpo_field_required_enable'][0] == 'yes' ) {
                                echo '<input id="'.esc_attr( $sElementFieldName ).'-field" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).' uni-cpo-required" value="'.$sInputValue.'" data-parsley-required="true" data-parsley-trigger="change focusout submit"' . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_min'][0])) ? ' min="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_min'][0]).'"' : '' ) . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_max'][0])) ? ' max="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_max'][0]).'"' : '' ) . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_step'][0])) ? ' step="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_step'][0]).'"' : ' step="0.1"' ) . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : 'data-parsley-pattern="'.$sGeneralValidationPatternDouble.'"') .' />';
                            } else {
                                echo '<input id="'.esc_attr( $sElementFieldName ).'-field" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).'" value="'.$sInputValue.'"' . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_min'][0])) ? ' min="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_min'][0]).'"' : '' ) . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_max'][0])) ? ' max="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_max'][0]).'"' : '' ) . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_step'][0])) ? ' step="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_step'][0]).'"' : ' step="0.1"' ) . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : ' data-parsley-pattern="'.$sGeneralValidationPatternDouble.'" data-parsley-trigger="change focusout submit" data-parsley-type="number"') .' />';
                            }
                        break;
                        default:
                            if ( !empty($aOptionCustomMeta['_uni_cpo_field_required_enable'][0]) && $aOptionCustomMeta['_uni_cpo_field_required_enable'][0] == 'yes' ) {
                                echo '<input id="'.esc_attr( $sElementFieldName ).'-field" type="text" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).' uni-cpo-required" value="'.$sInputValue.'" data-parsley-required="true" data-parsley-trigger="change focusout submit"' . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_min'][0])) ? ' min="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_min'][0]).'"' : '' ) . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_max'][0])) ? ' max="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_max'][0]).'"' : '' ) . ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_step'][0])) ? ' step="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_step'][0]).'"' : '' ) . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .''.( (isset($aOptionCustomMeta['_uni_cpo_field_chars_min'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_chars_min'][0])) ? ' data-parsley-minlength="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_chars_min'][0]).'"' : '' ).''.( (isset($aOptionCustomMeta['_uni_cpo_field_chars_max'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_chars_max'][0])) ? ' data-parsley-maxlength="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_chars_max'][0]).'"' : '' ).' />';
                            } else {
                                echo '<input id="'.esc_attr( $sElementFieldName ).'-field" type="text" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).'" value="'.$sInputValue.'"' . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .''.( (isset($aOptionCustomMeta['_uni_cpo_field_chars_min'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_chars_min'][0])) ? ' data-parsley-minlength="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_chars_min'][0]).'"' : '' ).''.( (isset($aOptionCustomMeta['_uni_cpo_field_chars_max'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_chars_max'][0])) ? ' data-parsley-maxlength="'.esc_attr($aOptionCustomMeta['_uni_cpo_field_chars_max'][0]).'"' : '' ).' />';
                            }
                        break;
                    }
                }
                echo '<input id="'.esc_attr( $sElementFieldName ).'-field-count" type="hidden" name="'.esc_attr( $sElementFieldName ).'_count" value="' . $sInputValueCount . '" />';

                // the field conditional logic
                $this->render_fc_rules();

                // the validation conditional logic
                if ( isset( $aOptionCustomMeta['_uni_cpo_val_conditional_enable'][0] ) && 'yes' === $aOptionCustomMeta['_uni_cpo_val_conditional_enable'][0] &&
                    isset( $aOptionCustomMeta['_uni_cpo_val_conditional_scheme'][0] ) ) {
                        $val_schemes = maybe_unserialize( $aOptionCustomMeta['_uni_cpo_val_conditional_scheme'][0] );
                        $final_val_js_statement = '';
                        $cond_field_names_array = array();

                        // builds statement
                        if ( ! empty( $val_schemes ) ) {

                            $sStatement = '';
                            $sStatement .= 'if ( ';
                            $rule_scheme_count = count( $val_schemes );
                            $i = 0;
                            //
                            foreach ( $val_schemes as $key => $rule_scheme ) {

                                $rule_scheme = json_decode( json_encode( $rule_scheme ), true );

                                foreach ( $rule_scheme['rule']['rules'] as $rule ) {
                                    $cond_field_slug = $rule['id'];
                                    $cond_field_names_array[$cond_field_slug] = "[name=uni_cpo_{$cond_field_slug}]";
                                }

                                //
                                if ( $i !== 0 ) {
                                    $sStatement .= '} else if ( ';
                                }
                                $sStatement .= uni_cpo_option_condition_js_statement_builder( array( $rule_scheme['rule'] ), $this->get_type() );
                                $sStatement .= ') {';
                                if ( isset($rule_scheme['value']['min']) && ! empty($rule_scheme['value']['min']) ) {
                                    $sStatement .= '$'.esc_attr( $sElementFieldName ).'.attr("min", '.$rule_scheme['value']['min'].');';
                                }
                                if ( isset($rule_scheme['value']['max']) && ! empty($rule_scheme['value']['max']) ) {
                                    $sStatement .= '$'.esc_attr( $sElementFieldName ).'.attr("max", '.$rule_scheme['value']['max'].');';
                                }

                                $i++;
                            }

                            $sStatement .= '} else {';
                            $sStatement .= '$'.esc_attr( $sElementFieldName ).'.attr("min", min_value);';
                            $sStatement .= '$'.esc_attr( $sElementFieldName ).'.attr("max", max_value);';
                            $sStatement .= '}';

                            $final_val_js_statement = $sStatement;

                            $cond_field_names = implode( ', ', $cond_field_names_array );

                        }

                        if ( ! empty( $final_val_js_statement ) ) {
                        ?>
                        <script>
                        jQuery( document ).ready( function( $ ) {
                            'use strict';

                            $('<?php echo $cond_field_names; ?>').on( 'change', function(){
                                <?php echo esc_attr( $sElementFieldName ) ?>_val_func();
                            });

                            function <?php echo esc_attr( $sElementFieldName ) ?>_val_func(){

                                <?php
                                foreach ( $cond_field_names_array as $key => $value ) {
                                ?>
                                var _<?php echo esc_attr( $key ) ?>_slug =  'uni_cpo_<?php echo esc_attr( $key ) ?>',
                                    _<?php echo esc_attr( $key ) ?>_type = $('#'+_<?php echo esc_attr( $key ) ?>_slug).data('type'),
                                    $<?php echo esc_attr( $key ) ?>     = uni_get_var_obj_for_cond( _<?php echo esc_attr( $key ) ?>_type, _<?php echo esc_attr( $key ) ?>_slug ),
                                    _<?php echo esc_attr( $key ) ?>_val  = uni_get_var_val_for_cond( _<?php echo esc_attr( $key ) ?>_type, $<?php echo esc_attr( $key ) ?>, _<?php echo esc_attr( $key ) ?>_slug );
                                <?php
                                }
                                ?>

                                var min_value = <?php echo ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_min'][0])) ? esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_min'][0]) : '""' ); ?>,
                                    max_value = <?php echo ( (isset($aOptionCustomMeta['_uni_cpo_field_input_number_max'][0])) ? esc_attr($aOptionCustomMeta['_uni_cpo_field_input_number_max'][0]) : '""' ); ?>;

                                var $<?php echo esc_attr( $sElementFieldName ) ?>_cont = $('#<?php echo esc_attr( $sElementFieldName ) ?>'),
                            		$<?php echo esc_attr( $sElementFieldName ) ?> = $<?php echo esc_attr( $sElementFieldName ) ?>_cont.find('[name=<?php echo esc_attr( $sElementFieldName ) ?>]');

                            	<?php
                                echo $final_val_js_statement;
                                ?>

                            }

                            <?php echo esc_attr( $sElementFieldName ) ?>_val_func();

                        });
                        </script>
                        <?php
                        } // #end not empty final statement

                }

                echo '<div class="uni-cpo-clear"></div>';
            echo '</div>';

            do_action( 'uni_cpo_after_option', $this );

	}

	/**
	 * Validation settings which can be changed via validation conditional logic
	 *
	 */
	public function render_validation_fields( $count, $value = '' ) {
        $output = '<label>' . esc_html__('Max. value', 'uni-cpo') . '</label>';
        $output .= '<input type="text" name="uni_cpo_val_conditional_scheme[' . esc_attr($count) . '][value][max]" class="uni-cpo-modal-field" value="'.( (!empty($value) ) ? $value['max'] : '' ).'" />';
        $output .= '<label>' . esc_html__('Min. value', 'uni-cpo') . '</label>';
        $output .= '<input type="text" name="uni_cpo_val_conditional_scheme[' . esc_attr($count) . '][value][min]" class="uni-cpo-modal-field" value="'.( (!empty($value) ) ? $value['min'] : '' ).'" />';
        return $output;
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
	 * Returns an array of special vars associated with the option
	 *
	 */
	public function get_special_vars() {
		return array('count', 'count_spaces');
	}

	/**
	 * Generates query builder array of data
	 *
	 */
	public function generate_filter( $bFull = false ) {
        $sTypeOfFieldTag = ( get_post_meta( $this->get_id(), '_uni_cpo_field_input_tag_type', true ) ) ? get_post_meta( $this->get_id(), '_uni_cpo_field_input_tag_type', true ) : 'string';
        $aFilterArray[] = array(
            'id' => $this->post->post_name,
            'label' => $this->post->post_title,
            'type' => $sTypeOfFieldTag,
            'input' => 'text',
            'operators' => array( 'less', 'less_or_equal', 'equal', 'not_equal', 'greater_or_equal', 'greater', 'is_empty', 'is_not_empty' )
        );

        if ( $bFull ) {

            $var_name = trim($this->post->post_title, '{');
            $var_name = trim($var_name, '}');
            $aFilterArray[] = array(
                'id' => $this->post->post_name.'_count',
                'label' => '{'.$var_name.'_count}',
                'type' => 'integer',
                'input' => 'text',
                'operators' => array( 'less', 'less_or_equal', 'equal', 'not_equal', 'greater_or_equal', 'greater', 'is_empty', 'is_not_empty' )
            );

            $aFilterArray[] = array(
                'id' => $this->post->post_name.'_count_spaces',
                'label' => '{'.$var_name.'_count_spaces}',
                'type' => 'integer',
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

        if ( isset( $aFormPostData[$sElementFieldName] ) && ! empty( $aFormPostData[$sElementFieldName] ) ) {
            $price = floatval( get_post_meta( $this->get_id(), '_uni_cpo_field_option_price', true ) );
            if ( ! $bCartMeta ) {
                if ( ! empty( $price ) ) {
                    return  array(
                        $sElementFieldName => $price,
                        $sElementFieldName.'_count' => intval( $aFormPostData[$sElementFieldName.'_count'] )
                    );
                } else {
                    return  array(
                        $sElementFieldName => floatval( $aFormPostData[$sElementFieldName] ),
                        $sElementFieldName.'_count' => intval( $aFormPostData[$sElementFieldName.'_count'] )
                    );
                }
            } else if ( $bCartMeta == 'cart' ) {
                return  array(
                    $sElementFieldName => $aFormPostData[$sElementFieldName],
                    $sElementFieldName.'_count' => intval( $aFormPostData[$sElementFieldName.'_count'] )
                );
            } else {
                return $aFormPostData[$sElementFieldName];
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