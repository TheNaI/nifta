<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_text_select class
*
*/

class Uni_Cpo_Option_text_select extends Uni_Cpo_Option {

    public $id = 0;

    public $post = null;

    public $option_type = 'text_select';

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

        $this->option_icon = 'fa-copyright';
        $this->option_name = esc_html__('Text Select', 'uni-cpo');
        $this->tab_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_settings_filter", array(
                                    array(
                                        'section_title' => esc_html__('General settings', 'uni-cpo'),
                                        'settings' => array('field_slug', 'field_required_enable',
                                            'field_media_select_padding_top', 'field_media_select_padding_right',
                                            'field_media_select_min_width',
                                            'field_media_select_border_width', 'field_media_select_border_radius',
                                            'field_media_select_font_size',
                                            'field_media_select_border_color', 'field_media_select_border_color_default',
                                            'field_option_text_options')
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
        $aAllowedHtml       = uni_cpo_allowed_html_for_option_titles();

        if ( isset($_POST[$sElementFieldName]) && !empty($_POST[$sElementFieldName]) ) {
            $chosen_value = $_POST[$sElementFieldName];
        } else {
            $chosen_value = '';
        }

        $border_width_css   = ( isset( $option_meta['_uni_cpo_field_media_select_border_width'][0] ) && ! empty( $option_meta['_uni_cpo_field_media_select_border_width'][0] ) ) ? 'border-width:' . $option_meta['_uni_cpo_field_media_select_border_width'][0] . 'px;' : '';
        if ( isset( $option_meta['_uni_cpo_field_media_select_border_color'][0] ) && ! empty( $option_meta['_uni_cpo_field_media_select_border_color'][0] ) ) {
            // fix for hex codes without #
            if ( false === strpos($option_meta['_uni_cpo_field_media_select_border_color_default'][0], '#') ) {
                $option_meta['_uni_cpo_field_media_select_border_color_default'][0] = '#' . $option_meta['_uni_cpo_field_media_select_border_color_default'][0];
            }
            $border_color_def_css = 'border-color:' . $option_meta['_uni_cpo_field_media_select_border_color_default'][0] . ';';
        } else {
            $border_color_def_css = '';
        }
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
        $padding_top_css   = ( isset( $option_meta['_uni_cpo_field_media_select_padding_top'][0] ) && ! empty( $option_meta['_uni_cpo_field_media_select_padding_top'][0] ) ) ? 'padding-top:' . intval( $option_meta['_uni_cpo_field_media_select_padding_top'][0] ) . 'px;padding-bottom:' . intval( $option_meta['_uni_cpo_field_media_select_padding_top'][0] ) . 'px;' : '';
        $padding_right_css   = ( isset( $option_meta['_uni_cpo_field_media_select_padding_right'][0] ) && ! empty( $option_meta['_uni_cpo_field_media_select_padding_right'][0] ) ) ? 'padding-right:' . intval( $option_meta['_uni_cpo_field_media_select_padding_right'][0] ) . 'px;padding-left:' . intval( $option_meta['_uni_cpo_field_media_select_padding_right'][0] ) . 'px;' : '';
        $min_width_css   = ( isset( $option_meta['_uni_cpo_field_media_select_min_width'][0] ) && ! empty( $option_meta['_uni_cpo_field_media_select_min_width'][0] ) ) ? 'min-width:' . intval( $option_meta['_uni_cpo_field_media_select_min_width'][0] ) . 'px;' : '';
        $font_size_css   = ( isset( $option_meta['_uni_cpo_field_media_select_font_size'][0] ) && ! empty( $option_meta['_uni_cpo_field_media_select_font_size'][0] ) ) ? 'font-size:' . intval( $option_meta['_uni_cpo_field_media_select_font_size'][0] ) . 'px;' : '';

        if ( $padding_top_css || $padding_right_css || $min_width_css || $border_color_def_css || $border_color_css || $border_radius_css || $font_size_css ) {
            $option_custom_css = '<style type="text/css">ul#'.esc_attr( $sElementFieldName ).'_list li a {' . esc_attr( $min_width_css ) . esc_attr( $padding_top_css ) . esc_attr( $padding_right_css ) . esc_attr( $border_color_def_css ) . esc_attr( $font_size_css ) . '}ul#'.esc_attr( $sElementFieldName ).'_list li.active a {' . esc_attr( $border_width_css ) . esc_attr( $border_color_css ) . '}</style>';
            echo $option_custom_css;
        }

        do_action( 'uni_cpo_before_option', $this );

        echo '<div id="'.esc_attr( $sElementFieldName ).'" data-type="' . esc_attr( $this->get_type() ) . '" class="uni_cpo_fields_container uni_cpo_field_type_'.esc_attr( $this->get_type() ) . ( (!empty($option_meta['_uni_cpo_field_extra_class'][0])) ? ' '.esc_attr( $option_meta['_uni_cpo_field_extra_class'][0] ) : '' ) .'">';

            if ( !empty( $option_meta['_uni_cpo_field_header_text'][0] ) && !empty( $option_meta['_uni_cpo_field_header_type'][0] ) ) {
                echo '<'.esc_attr($option_meta['_uni_cpo_field_header_type'][0]).' class="uni_cpo_fields_header">';
                echo wp_kses( $option_meta['_uni_cpo_field_header_text'][0], $aAllowedHtml ) . ( ( !empty($option_meta['_uni_cpo_field_required_enable'][0]) && $option_meta['_uni_cpo_field_required_enable'][0] == 'yes' ) ? ' <span class="uni-cpo-required-label">*</span>' : '' );

                // tooltips
                $this->tooltip_output();

                echo'</'.esc_attr($option_meta['_uni_cpo_field_header_type'][0]).'>';
            }

            if ( !empty($option_meta['_uni_cpo_field_required_enable'][0]) && $option_meta['_uni_cpo_field_required_enable'][0] == 'yes' ) {
                echo '<select id="'.esc_attr( $sElementFieldName ).'-field" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).' uni-cpo-required" data-parsley-required="true" data-parsley-trigger="change focusout submit">';
            } else {
                echo '<select id="'.esc_attr( $sElementFieldName ).'-field" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).'">';
            }

            $serialized_values  = ( !empty($option_meta['_uni_cpo_field_option_text_options'][0]) && $option_meta['_uni_cpo_field_option_text_options'][0] ) ? $option_meta['_uni_cpo_field_option_text_options'][0] : '';
            $suboptions         = maybe_unserialize( $serialized_values );

                if ( !empty( $suboptions ) && is_array( $suboptions ) ) {
                    $i = 0;
                    foreach ( $suboptions as $key => $value ) {

                        if( NULL === $value ) {
                            continue;
                        }

                        $label      = ( ! empty( $value['label'] ) ) ? $value['label'] : '';
                        $tooltip    = ( ! empty( $value['tooltip'] ) ) ? $value['tooltip'] : $label;

                        if ( ! empty( $chosen_value ) ) {
                            echo '<option' . selected( $value['slug'], $chosen_value, false ) . ' data-suboptiontitle="' . esc_attr( $label ) . '" data-tooltiptext="' . esc_attr( $tooltip ) . '" value="' . esc_attr( $value['slug'] ) . '">';
                        } else {
                            echo '<option' . selected( $i, ( isset( $value['default'] ) ? $value['default'] : '' ), false ) . ' data-suboptiontitle="' . esc_attr( $label ) . '" data-tooltiptext="' . esc_attr( $tooltip ) . '" value="'.esc_attr( $value['slug'] ) . '">';
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

            uniCpoTransfromTextSelect('[name=<?php echo esc_attr( $sElementFieldName ) ?>]');

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
        $subptions = ( ! empty( $option_meta['_uni_cpo_field_option_text_options'][0] ) && $option_meta['_uni_cpo_field_option_text_options'][0] ) ? $option_meta['_uni_cpo_field_option_text_options'][0] : '';
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
        $aSelectOptions = get_post_meta( $this->get_id(), '_uni_cpo_field_option_text_options', true );
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
            if ( get_post_meta( $this->get_id(), '_uni_cpo_field_option_text_options', true ) ) {
                $aSelectOptions = get_post_meta( $this->get_id(), '_uni_cpo_field_option_text_options', true );
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