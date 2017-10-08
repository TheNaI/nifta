<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_palette_select class
*
*/

class Uni_Cpo_Option_palette_select extends Uni_Cpo_Option {

    public $id = 0;

    public $post = null;

    public $option_type = 'palette_select';

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

        $this->option_icon = 'fa-eyedropper';
        $this->option_name = esc_html__('Palette select', 'uni-cpo');
        $this->tab_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_settings_filter", array(
                                    array(
                                        'section_title' => esc_html__('General settings', 'uni-cpo'),
                                        'settings' => array('field_slug', 'field_required_enable',
                                            'field_media_select_size', 'field_media_select_size_height',
                                            'field_media_select_border_width', 'field_media_select_border_color', 'field_media_select_border_radius',
                                            'field_palette_image_encoded',
                                            'field_option_palette_select_options')
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

        $this->specific_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_specific_settings_filter", array(
                                    'field_option_palette_select_options' => array( 'palette_select' =>
                                        array(
                                            'title' => esc_html__('Sub options', 'uni-cpo'),
                                            'description' => esc_html__('Add some options for select and, please, keep unique slugs for them. These slugs might be used in a formula conditional rules (e.g. when you use operators "equal", "not equal" etc).', 'uni-cpo'),
                                            'doc_link' => esc_url('http://moomoo.agency/demo/cpo/docs'),
                                            'name' => 'field_option_palette_select_options',
                                            'required' => false
                                        )
                                    ),
                                    'field_palette_image_encoded' => array( 'text_input' =>
                                        array(
                                            'title' => esc_html__('Image encoded in base64', 'uni-cpo'),
                                            'tip' => esc_html__('Paste here a code of the encoded image. You can use b64.io web service to perform encoding.', 'uni-cpo'),
                                            'name' => 'field_palette_image_encoded',
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

        $option_meta        = $this->get_post_meta();
        $sElementFieldName  = $this->get_slug();
        $sSelectValue       = '';
        $aAllowedHtml       = uni_cpo_allowed_html_for_option_titles();
        $aAllowedTootltipHtml = uni_cpo_allowed_html_for_tooltips();
        $tooltip_icon_html  = uni_cpo_option_tooltip_icon_output( $this );
        $iImageCustomSize   = '';

        if ( isset($_POST[$sElementFieldName]) && !empty($_POST[$sElementFieldName]) ) {
            $sSelectValue = $_POST[$sElementFieldName];
        }

        if ( !isset($option_meta['_uni_cpo_field_header_tooltip_type'][0]) || empty($option_meta['_uni_cpo_field_header_tooltip_type'][0]) ) {
            $sTooltipType = 'classic';
        } else {
            $sTooltipType = $option_meta['_uni_cpo_field_header_tooltip_type'][0];
        }

        $border_width_css   = ( isset( $option_meta['_uni_cpo_field_media_select_border_width'][0] ) && ! empty( $option_meta['_uni_cpo_field_media_select_border_width'][0] ) ) ? 'border-width:' . $option_meta['_uni_cpo_field_media_select_border_width'][0] . 'px;' : '';
        if ( isset( $option_meta['_uni_cpo_field_media_select_border_color'][0] ) && ! empty( $option_meta['_uni_cpo_field_media_select_border_color'][0] ) ) {
            // fix for hex codes without #
            if ( false === strpos($option_meta['_uni_cpo_field_media_select_border_color'][0], '#') ) {
                $option_meta['_uni_cpo_field_media_select_border_color'][0] = '#' . $option_meta['_uni_cpo_field_media_select_border_color'][0];
            }
            $border_color_css = 'border-color:' . $option_meta['_uni_cpo_field_media_select_border_color'][0] . ';';
        } else {
            $border_color_css = '';
        }
        $border_radius_css  = ( isset( $option_meta['_uni_cpo_field_media_select_border_radius'][0] ) && ! empty( $option_meta['_uni_cpo_field_media_select_border_radius'][0] ) ) ? 'border-radius:' . $option_meta['_uni_cpo_field_media_select_border_radius'][0] . 'px ' . $option_meta['_uni_cpo_field_media_select_border_radius'][0] . 'px ' . $option_meta['_uni_cpo_field_media_select_border_radius'][0] . 'px ' . $option_meta['_uni_cpo_field_media_select_border_radius'][0] . 'px;' : '';
        $image_height_css   = ( isset( $option_meta['_uni_cpo_field_media_select_size_height'][0] ) && ! empty( $option_meta['_uni_cpo_field_media_select_size_height'][0] ) ) ? 'height:' . intval( $option_meta['_uni_cpo_field_media_select_size_height'][0] ) . 'px;' : '';
        if ( ! empty( $image_height_css ) ) {
            $image_size_css     = ( isset( $option_meta['_uni_cpo_field_media_select_size'][0] ) && ! empty( $option_meta['_uni_cpo_field_media_select_size'][0] ) ) ? 'width:' . intval( $option_meta['_uni_cpo_field_media_select_size'][0] ) . 'px;' . $image_height_css : '';
        } else {
            $image_size_css     = ( isset( $option_meta['_uni_cpo_field_media_select_size'][0] ) && ! empty( $option_meta['_uni_cpo_field_media_select_size'][0] ) ) ? 'width:' . intval( $option_meta['_uni_cpo_field_media_select_size'][0] ) . 'px;height:' . intval( $option_meta['_uni_cpo_field_media_select_size'][0] ) . 'px;' : '';
        }

        if ( $border_width_css || $border_color_css || $border_radius_css || $image_size_css ) {
            $option_custom_css = '<style type="text/css">ul#'.esc_attr( $sElementFieldName ).'_list li{' . esc_attr( $image_size_css ) . '}ul#'.esc_attr( $sElementFieldName ).'_list li a {' . esc_attr( $border_radius_css ) . '}ul#'.esc_attr( $sElementFieldName ).'_list li.active a {' . esc_attr( $border_width_css ) . esc_attr( $border_color_css ) . '}</style>';
            echo $option_custom_css;
        }

        if ( isset( $option_meta['_uni_cpo_field_palette_image_encoded'][0] ) && ! empty( $option_meta['_uni_cpo_field_palette_image_encoded'][0] ) ) {
            $encoded_image_added = 'yes';
        } else {
            $encoded_image_added = 'no';
        }

        do_action( 'uni_cpo_before_option', $this );

        echo '<div id="'.esc_attr( $sElementFieldName ).'" data-type="' . esc_attr( $this->get_type() ) . '" class="uni_cpo_fields_container uni_cpo_field_type_'.esc_attr( $this->get_type() ) . ( (!empty($option_meta['_uni_cpo_field_extra_class'][0])) ? ' '.esc_attr( $option_meta['_uni_cpo_field_extra_class'][0] ) : '' ) .'">';

            if ( !empty( $option_meta['_uni_cpo_field_header_text'][0] ) && !empty( $option_meta['_uni_cpo_field_header_type'][0] ) ) {
                echo '<'.esc_attr($option_meta['_uni_cpo_field_header_type'][0]).' class="uni_cpo_fields_header">';
                echo wp_kses( $option_meta['_uni_cpo_field_header_text'][0], $aAllowedHtml ) . ( ( !empty($option_meta['_uni_cpo_field_required_enable'][0]) && $option_meta['_uni_cpo_field_required_enable'][0] == 'yes' ) ? ' <span class="uni-cpo-required-label">*</span>' : '' );

                // tooltips
                if ( $sTooltipType == 'classic' ) {
                    if ( !empty($option_meta['_uni_cpo_field_header_tooltip_text'][0]) && empty($option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0]) ) {
                        echo ' <span class="uni-cpo-tooltip" data-tooltip-content="#uni-cpo-tooltip-'.esc_attr( $sElementFieldName ).'">'.$tooltip_icon_html.'</span>';
                        echo '<div class="tooltip_templates"><div id="uni-cpo-tooltip-'.esc_attr( $sElementFieldName ).'">'.wp_kses( $option_meta['_uni_cpo_field_header_tooltip_text'][0], $aAllowedTootltipHtml ).'</div></div>';
                    } else if ( !empty($option_meta['_uni_cpo_field_header_tooltip_text'][0]) && !empty($option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0]) && $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0] !== 'uni-cpo-custom-tooltip' ) {
                        echo ' <span class="'.esc_attr($option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0]).' uni-cpo-tooltip-element" title="'.esc_attr(wp_kses( $option_meta['_uni_cpo_field_header_tooltip_text'][0], $aAllowedTootltipHtml )).'">'.$tooltip_icon_html.'</span>';
                    } else if ( !empty($option_meta['_uni_cpo_field_header_tooltip_text'][0]) && !empty($option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0]) && $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0] === 'uni-cpo-custom-tooltip' ) {
                        echo ' <div class="uni-cpo-custom-tooltip-element">';
                        echo $tooltip_icon_html;
                            echo '<div class="uni-cpo-custom-tooltip-content">';
                            echo wp_kses( $option_meta['_uni_cpo_field_header_tooltip_text'][0], $aAllowedTootltipHtml );
                            echo '</div>';
                        echo '</div>';
                    }
                } else if ( $sTooltipType == 'lightbox' ) {
                    $iThumbId = intval($option_meta['_uni_cpo_field_header_tooltip_image'][0]);
                    $aImage = wp_get_attachment_image_src( $iThumbId, 'full' );
                    $sImageUrl = $aImage[0];
                    echo '<a href="' . esc_url($sImageUrl) . '" data-lity data-lity-desc="" class="uni-cpo-tooltip-element">';
                    echo $tooltip_icon_html;
                    echo '</a>';
                }

                echo'</'.esc_attr($option_meta['_uni_cpo_field_header_type'][0]).'>';
            }

            if ( !empty($option_meta['_uni_cpo_field_required_enable'][0]) && $option_meta['_uni_cpo_field_required_enable'][0] == 'yes' ) {
                echo '<select id="'.esc_attr( $sElementFieldName ).'-field" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).' uni-cpo-required" data-layered="'.esc_attr( $encoded_image_added ).'" data-parsley-required="true" data-parsley-trigger="change focusout submit">';
            } else {
                echo '<select id="'.esc_attr( $sElementFieldName ).'-field" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).'" data-layered="'.esc_attr( $encoded_image_added ).'">';
            }

            $sSerializedValues = ( !empty($option_meta['_uni_cpo_field_option_palette_select_options'][0]) && $option_meta['_uni_cpo_field_option_palette_select_options'][0] ) ? $option_meta['_uni_cpo_field_option_palette_select_options'][0] : '';
            $aArrayOfValues = maybe_unserialize( $sSerializedValues );

                if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                    $i = 0;
                    foreach ( $aArrayOfValues as $sKey => $aValue ) {

                        if( $aValue === NULL ) {
                            continue;
                        }

                        $aValue['color'] = strpos($aValue['color'], '#') === false ? '#' . $aValue['color'] : $aValue['color'];

                            $sThisOptionLabel = ( ( !empty( $aValue['label'] ) ) ? esc_attr($aValue['label']) : '' );

                            if ( !empty($sSelectValue) ) {
                                echo '<option'.selected( $aValue['slug'], $sSelectValue, false ).' data-color="'.esc_attr($aValue['color']).'" data-imagetitle="'.$sThisOptionLabel.'" value="'.esc_attr($aValue['slug']).'">';
                            } else {
                                echo '<option'.selected( $i, ( isset($aValue['default']) ? $aValue['default'] : '' ), false ).' data-color="'.esc_attr($aValue['color']).'" data-imagetitle="'.$sThisOptionLabel.'" value="'.esc_attr($aValue['slug']).'">';
                            }
                            echo '</option>';

                        $i++;
                    }
                }

            echo '</select>';
                    ?>
        <script>
        jQuery( document ).ready( function( $ ) {
            'use strict';

            uniCpoTransformPaletteSelect( "[name=<?php echo esc_attr( $sElementFieldName ) ?>]" );

        });
        </script>
                    <?php

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
        $subptions = ( ! empty( $option_meta['_uni_cpo_field_option_palette_select_options'][0] ) && $option_meta['_uni_cpo_field_option_palette_select_options'][0] ) ? $option_meta['_uni_cpo_field_option_palette_select_options'][0] : '';
        $subptions = maybe_unserialize( $subptions );

        $output = '<tr>';

        $output .= '<th><label>' . esc_html( $this->get_label() ) . ' <i>(' . esc_html( $this->get_slug() ) . ')</i></label></th>';
        $output .= '<td><select name="' . esc_attr( $this->get_slug() ) . '">';
        if ( !empty( $subptions ) && is_array( $subptions ) ) {
            $output .= '<option value="">'.esc_html__( 'Please select...', 'uni-cpo' ).'</option>';
            foreach ( $subptions as $key => $val ) {
                if( $val === NULL ) {
                    continue;
                }
                $output .= '<option value="' . esc_attr( $val['slug'] ) . '"' . selected( $val['slug'], ( isset( $posted_data[$this->get_slug()] ) ? $posted_data[$this->get_slug()] : '' ), false ) . '>' . esc_html( $val['label'] ) . '</option>';
            }
        }
        $output .= '</select></td>';

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

        return $aFilterArray;
	}

	/**
	 * Generates an array of vars and values for formula calculation associated with this option
	 *
	 */
	public function calculation( $aFormPostData, $bCartMeta = false ) {

        $sElementFieldName  = trim($this->post->post_title, '{}');

        if ( isset($aFormPostData[$sElementFieldName]) && !empty($aFormPostData[$sElementFieldName]) ) {
            if ( get_post_meta( $this->get_id(), '_uni_cpo_field_option_palette_select_options', true ) ) {
                $aSelectOptions = get_post_meta( $this->get_id(), '_uni_cpo_field_option_palette_select_options', true );
                $sAllOptionsChosen = '';
                if ( !empty($aSelectOptions) ) {
                unset($aSelectOptions['default']);
                foreach ( $aSelectOptions as $sKey => $aValue ) {
                    if ( (( !empty($aValue['slug']) ) ? $aValue['slug'] : '') === $aFormPostData[$sElementFieldName] ) {
                        if ( !$bCartMeta ) {
                            return ( !empty($aValue['price']) ) ? floatval( $aValue['price'] ) : '0';
                        } else if ( $bCartMeta == 'cart' ) {
                            return $aValue['slug'];
                        } else if ( $bCartMeta == 'order' ) {
                            return $aValue['label'];
                        }
                    }
                }
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