<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_checkboxes class
*
*/

class Uni_Cpo_Option_checkboxes extends Uni_Cpo_Option {

    public $id = 0;

    public $post = null;

    public $option_type = 'checkboxes';

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

        $this->option_icon = 'fa-check-square-o';
        $this->option_name = esc_html__('Checkboxes', 'uni-cpo');
        $this->tab_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_settings_filter", array(
                                    array(
                                        'section_title' => esc_html__('General settings', 'uni-cpo'),
                                        'settings' => array('field_slug', 'field_required_enable', 'field_display_price_in_front',
                                                    'field_display_price_in_front_text', 'field_thumb_size',
                                                    'field_option_checkboxes_options')
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
        $aCheckboxValue     = array();
        $aAllowedHtml       = uni_cpo_allowed_html_for_option_titles();
        $aAllowedTootltipHtml = uni_cpo_allowed_html_for_tooltips();
        $tooltip_icon_html  = uni_cpo_option_tooltip_icon_output( $this );

        if ( isset($_POST[$sElementFieldName]) && !empty($_POST[$sElementFieldName]) ) {
            $aCheckboxValue = $_POST[$sElementFieldName];
        }

        if ( !isset($aOptionCustomMeta['_uni_cpo_field_header_tooltip_type'][0]) || empty($aOptionCustomMeta['_uni_cpo_field_header_tooltip_type'][0]) ) {
            $sTooltipType = 'classic';
        } else {
            $sTooltipType = $aOptionCustomMeta['_uni_cpo_field_header_tooltip_type'][0];
        }

        $sThumbSize         = ( !empty($aOptionCustomMeta['_uni_cpo_field_thumb_size'][0]) ) ? $aOptionCustomMeta['_uni_cpo_field_thumb_size'][0] : 'thumbnail';

        do_action( 'uni_cpo_before_option', $this );

        echo '<div id="'.esc_attr( $sElementFieldName ).'" data-type="' . esc_attr( $this->get_type() ) . '" class="uni_cpo_fields_container uni_cpo_field_type_'.esc_attr( $this->get_type() ) . ( (!empty($aOptionCustomMeta['_uni_cpo_field_extra_class'][0])) ? ' '.sanitize_html_class( $aOptionCustomMeta['_uni_cpo_field_extra_class'][0] ) : '' ) .'">';

            if ( !empty( $aOptionCustomMeta['_uni_cpo_field_header_text'][0] ) && !empty( $aOptionCustomMeta['_uni_cpo_field_header_type'][0] ) ) {
                echo '<'.esc_attr($aOptionCustomMeta['_uni_cpo_field_header_type'][0]).' class="uni_cpo_fields_header">';
                echo wp_kses( $aOptionCustomMeta['_uni_cpo_field_header_text'][0], $aAllowedHtml ) . ( ( !empty($aOptionCustomMeta['_uni_cpo_field_required_enable'][0]) && $aOptionCustomMeta['_uni_cpo_field_required_enable'][0] == 'yes' ) ? ' <span class="uni-cpo-required-label">*</span>' : '' );

                // tooltips
                if ( $sTooltipType == 'classic' ) {
                    if ( !empty($aOptionCustomMeta['_uni_cpo_field_header_tooltip_text'][0]) && empty($aOptionCustomMeta['_uni_cpo_field_extra_tooltip_selector_class'][0]) ) {
                        echo ' <span class="uni-cpo-tooltip" data-tooltip-content="#uni-cpo-tooltip-'.esc_attr( $sElementFieldName ).'">'.$tooltip_icon_html.'</i></span>';
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

            $sSerializedValues = ( !empty($aOptionCustomMeta['_uni_cpo_field_option_checkboxes_options'][0]) && $aOptionCustomMeta['_uni_cpo_field_option_checkboxes_options'][0] ) ? $aOptionCustomMeta['_uni_cpo_field_option_checkboxes_options'][0] : '';
            $aArrayOfValues = maybe_unserialize( $sSerializedValues );

            if ( !empty($aOptionCustomMeta['_uni_cpo_field_required_enable'][0]) && $aOptionCustomMeta['_uni_cpo_field_required_enable'][0] == 'yes' ) {

                if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                    $i = 0;
                    foreach ( $aArrayOfValues as $sKey => $aValue ) {

                        if( $aValue === NULL ) {
                            continue;
                        }

                        $sThisOptionLabel = ( ( !empty( $aValue['label'] ) ) ? $aValue['label'] : '' );
                        echo '<label class="uni-cpo-option-label uni-cpo-checkbox-option-label'.( ( !empty( $aValue['image_id'] ) ) ? ' uni-cpo-checkbox-option-label-with-thumb' : '' ).'">';
                            if ( !empty($aCheckboxValue) ) {
                                echo '<input id="'.esc_attr( $sElementFieldName ).'-field" type="checkbox" name="'.esc_attr( $sElementFieldName ).'[]" data-multiple="yes" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).' uni-cpo-required" value="'.esc_attr($aValue['slug']).'" data-parsley-mincheck="1" data-parsley-required="true" data-parsley-trigger="change focusout submit"'.checked($i, ( in_array($aValue['slug'], $aCheckboxValue) ) ? true : false, false).' />';
                            } else {
                                echo '<input id="'.esc_attr( $sElementFieldName ).'-field" type="checkbox" name="'.esc_attr( $sElementFieldName ).'[]" data-multiple="yes" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).' uni-cpo-required" value="'.esc_attr($aValue['slug']).'" data-parsley-mincheck="1" data-parsley-required="true" data-parsley-trigger="change focusout submit"'.checked($i, ( isset($aValue['default']) ) ? $aValue['default'] : '', false).' />';
                            }
                            echo '<span>'.wp_kses( $sThisOptionLabel, $aAllowedHtml );
                            if ( !empty($aOptionCustomMeta['_uni_cpo_field_display_price_in_front'][0])
                                && $aOptionCustomMeta['_uni_cpo_field_display_price_in_front'][0] == 'yes' ) {

                                if ( !empty($aOptionCustomMeta['_uni_cpo_field_display_price_in_front_text'][0]) ) {
                                    $sCustomDisplayPriceText = $aOptionCustomMeta['_uni_cpo_field_display_price_in_front_text'][0];
                                    $sCustomDisplayPriceText = preg_replace('/({sPriceValue})/', uni_cpo_price( $aValue['price'] ), $sCustomDisplayPriceText);
                                    echo '<span class="uni-cpo-option-price">'.esc_attr($sCustomDisplayPriceText).'</span>';
                                } else {
                                    echo '<span class="uni-cpo-option-price">+'.uni_cpo_price( $aValue['price'] ).'</span>';
                                }

                            }
                            echo '</span>';
                            if ( !empty( $aValue['image_id'] ) ) {
                                $iThumbId = '';
                                $iThumbId = intval($aValue['image_id']);
                                $aImage = wp_get_attachment_image_src( $iThumbId, $sThumbSize );
                                $sImageUrl = $aImage[0];
                                echo '<img src="'.$sImageUrl.'" alt="'.esc_attr($sThisOptionLabel).'" />';
                                echo '<div class="uni-cpo-clear"></div>';
                            }
                        echo '</label>';
                        $i++;
                    }
                }
            } else {

                if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                    $i = 0;
                    foreach ( $aArrayOfValues as $sKey => $aValue ) {

                        if( $aValue === NULL ) {
                            continue;
                        }

                        $sThisOptionLabel = ( ( !empty( $aValue['label'] ) ) ? $aValue['label'] : '' );
                        echo '<label class="uni-cpo-option-label uni-cpo-checkbox-option-label'.( ( !empty( $aValue['image_id'] ) ) ? ' uni-cpo-checkbox-option-label-with-thumb' : '' ).'">';
                            if ( !empty($aCheckboxValue) ) {
                                echo '<input id="'.esc_attr( $sElementFieldName ).'-field" type="checkbox" name="'.esc_attr( $sElementFieldName ).'[]" data-multiple="yes" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).'" value="'.esc_attr($aValue['slug']).'"'.checked(true, ( in_array($aValue['slug'], $aCheckboxValue) ) ? true : false, false).' />';
                            } else {
                                echo '<input id="'.esc_attr( $sElementFieldName ).'-field" type="checkbox" name="'.esc_attr( $sElementFieldName ).'[]" data-multiple="yes" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).'" value="'.esc_attr($aValue['slug']).'"'.checked(true, ( isset($aValue['default']) ) ? true : false, false).' />';
                            }
                            echo '<span>'.wp_kses( $sThisOptionLabel, $aAllowedHtml );
                            if ( !empty($aOptionCustomMeta['_uni_cpo_field_display_price_in_front'][0])
                                && $aOptionCustomMeta['_uni_cpo_field_display_price_in_front'][0] == 'yes' ) {

                                if ( !empty($aOptionCustomMeta['_uni_cpo_field_display_price_in_front_text'][0]) ) {
                                    $sCustomDisplayPriceText = $aOptionCustomMeta['_uni_cpo_field_display_price_in_front_text'][0];
                                    $sCustomDisplayPriceText = preg_replace('/({sPriceValue})/', uni_cpo_price( $aValue['price'] ), $sCustomDisplayPriceText);
                                    echo '<span class="uni-cpo-option-price">'.esc_attr($sCustomDisplayPriceText).'</span>';
                                } else {
                                    echo '<span class="uni-cpo-option-price">+'.uni_cpo_price( $aValue['price'] ).'</span>';
                                }

                            }
                            echo '</span>';
                            if ( isset( $aValue['tooltip'] ) && ! empty( $aValue['tooltip'] ) ) {
                                echo '<span class="uni-cpo-tooltip" data-tooltip-content="#uni-cpo-tooltip-'.esc_attr( $sElementFieldName ).'-suboption-'.esc_attr($aValue['slug']).'">'.$tooltip_icon_html.'</span>';
                                echo '<div class="tooltip_templates"><div id="uni-cpo-tooltip-'.esc_attr( $sElementFieldName ).'-suboption-'.esc_attr($aValue['slug']).'">'.wp_kses( $aValue['tooltip'], $aAllowedTootltipHtml ).'</div></div>';
                            }
                            if ( !empty( $aValue['image_id'] ) ) {
                                $iThumbId = '';
                                $iThumbId = intval($aValue['image_id']);
                                $aImage = wp_get_attachment_image_src( $iThumbId, $sThumbSize );
                                $sImageUrl = $aImage[0];
                                echo '<img src="'.$sImageUrl.'" alt="'.esc_attr($sThisOptionLabel).'" />';
                                echo '<div class="uni-cpo-clear"></div>';
                            }
                        echo '</label>';
                        $i++;
                    }
                }
            }
            echo '<input id="'.esc_attr( $sElementFieldName ).'-field-count" type="hidden" name="'.esc_attr( $sElementFieldName ).'_count" value="" />';

            // the field conditional logic
            $this->render_fc_rules();

            echo '<div class="uni-cpo-clear"></div>';
        echo '</div>';

        do_action( 'uni_cpo_after_option', $this );

	}

	/**
	 * gets form field(s) for option for add/edit options modal on order edit page
	 *
	 */
	public function get_order_form_field( $posted_data = array() ) {
        $option_meta    = $this->get_post_meta();
        $subptions = ( ! empty( $option_meta['_uni_cpo_field_option_checkboxes_options'][0] ) && $option_meta['_uni_cpo_field_option_checkboxes_options'][0] ) ? $option_meta['_uni_cpo_field_option_checkboxes_options'][0] : '';
        $subptions = maybe_unserialize( $subptions );

        $output = '<tr>';

        $output .= '<th><label>' . esc_html( $this->get_label() ) . ' <i>(' . esc_html( $this->get_slug() ) . ')</i></label></th>';
        $output .= '<td>';
        if ( !empty( $subptions ) && is_array( $subptions ) ) {
            foreach ( $subptions as $key => $val ) {
                if( $val === NULL ) {
                    continue;
                }
                $slug = $val['slug'];
                $output .= '<input type="checkbox" value="' . esc_attr( $slug ) . '"' . checked( $slug, ( isset( $posted_data[$this->get_slug()][$slug] ) ? $posted_data[$this->get_slug()][$slug] : '' ), false ) . ' />' . esc_html( $val['label'] ) . '<br>';
            }
        }
        $output .= '</td>';

        $output .= '</tr>';

        if ( ! empty( $this->get_special_vars() ) && ! empty( $subptions ) && is_array( $subptions ) ) {
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
		return array('count');
	}
    
	/**
	 * Generates query builder array of data
	 *
	 */
	public function generate_filter( $bFull = false ) {
        $aSelectOptions = get_post_meta( $this->get_id(), '_uni_cpo_field_option_'.$this->get_type().'_options', true );
        $aSelectOptionsForQueryBuilder = array();
        if ( !empty($aSelectOptions) ) {
            if ( isset($aSelectOptions['default']) ) {
                unset($aSelectOptions['default']);
            }
            foreach ( $aSelectOptions as $sKey => $aValue ) {

                if( $aValue === NULL ) {
                    continue;
                }

                $aSelectOptionsForQueryBuilder[] = ( !empty($aValue['slug']) ) ? $aValue['slug'] : '';
            }
        }
        $aPieceOfFiltersArray = array(
            'id' => $this->post->post_name,
            'label' => $this->post->post_title,
            'type' => 'string',
            'input' => 'select',
            'values' => $aSelectOptionsForQueryBuilder,
            'operators' => array( 'equal', 'not_equal', 'is_empty', 'is_not_empty' )
        );
        $aFilterArray[] = $aPieceOfFiltersArray;

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

        }

        return $aFilterArray;
	}

	/**
	 * Retrieves value for formula calculation
	 *
	 */
	public function calculation( $aFormPostData, $bCartMeta = false ) {

        $sElementFieldName  = trim($this->post->post_title, '{}');

        if ( isset($aFormPostData[$sElementFieldName]) && !empty($aFormPostData[$sElementFieldName]) ) {
            if ( get_post_meta( $this->get_id(), '_uni_cpo_field_option_checkboxes_options', true ) ) {
                $aSelectOptions = get_post_meta( $this->get_id(), '_uni_cpo_field_option_checkboxes_options', true );
                $iTotalPrice = 0;
                $sAllOptionsChosen = '';
                $aAllOptionsChosen = array();
                if ( !empty($aSelectOptions) ) {
                    unset($aSelectOptions['default']);
                    foreach ( $aSelectOptions as $sKey => $aValue ) {
                        if ( in_array( (( !empty($aValue['slug']) ) ? $aValue['slug'] : ''), $aFormPostData[$sElementFieldName] ) ) {
                            if ( !$bCartMeta ) {
                                $iTotalPrice += ( !empty($aValue['price']) ) ? floatval( $aValue['price'] ) : 0;
                            } else if ( $bCartMeta == 'cart' ) {
                                $aAllOptionsChosen[] = $aValue['slug'];
                            } else if ( $bCartMeta == 'order' ) {
                                $sAllOptionsChosen .= $aValue['label'].', ';
                            }
                        }
                    }
                }
                if ( !$bCartMeta ) {
                    return  array(
                            $sElementFieldName => $iTotalPrice,
                            $sElementFieldName.'_count' => intval( $aFormPostData[$sElementFieldName.'_count'] )
                            );
                } else if ( $bCartMeta == 'cart' ) {
                    return  array(
                            $sElementFieldName => $aAllOptionsChosen,
                            $sElementFieldName.'_count' => intval( $aFormPostData[$sElementFieldName.'_count'] )
                            );
                } else if ( $bCartMeta == 'order' ) {
                    $sAllOptionsChosen = rtrim( $sAllOptionsChosen, ', ' );
                    return $sAllOptionsChosen;
                }
            } else {
                if ( !$bCartMeta ) {
                    return '0';
                } else {
                    return '';
                }
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