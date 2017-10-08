<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 *   Uni_Cpo_Option_range_slider class
 *
 */

class Uni_Cpo_Option_range_slider extends Uni_Cpo_Option {

	public $id = 0;

	public $post = NULL;

	public $option_type = 'range_slider';

	public $option_icon = '';

	public $option_name = '';

	public $tab_settings = array();

	public $specific_settings = array();

	protected $calc_status = TRUE;

	/**
	 * Constructor gets the post object and sets the ID for the loaded option.
	 *
	 */
	public function __construct( $Option = FALSE ) {

		if ( is_numeric( $Option ) ) {
			$this->id   = absint( $Option );
			$this->post = get_post( $this->id );
		} elseif ( $Option instanceof Uni_Cpo_Option ) {
			$this->id   = absint( $Option->id );
			$this->post = $Option->post;
		} elseif ( isset( $Option->ID ) ) {
			$this->id   = absint( $Option->ID );
			$this->post = $Option;
		}

		$this->option_icon  = 'fa-sliders';
		$this->option_name  = esc_html__( 'Range slider', 'uni-cpo' );
		$this->tab_settings = apply_filters( "uni_cpo_" . $this->option_type
		                                     . "_admin_settings_filter", array(
			array(
				'section_title' => esc_html__( 'General settings', 'uni-cpo' ),
				'settings'      => array(
					'field_slug',
					'field_required_enable',
					'field_input_range_type',
					'field_input_default_from',
					'field_input_default_end',
					'field_range_number_min',
					'field_range_number_max',
					'field_range_number_step',
					'field_input_grid',
					'field_input_min_interval',
					'field_input_max_interval',
					'field_input_show_text_input',
					'field_option_price'
				),
			),
			array(
				'section_title' => esc_html__( 'Display settings', 'uni-cpo' ),
				'settings'      => array(
					'field_header_type',
					'field_header_text',
					'field_header_tooltip_type',
					'field_header_tooltip_text',
					'field_header_tooltip_image',
					'field_extra_tooltip_selector_class',
					'field_extra_class',
					'field_meta_header_text'
				),
			),
			array(
				'section_title' => esc_html__( 'Conditional logic', 'uni-cpo' ),
				'settings'      => array(
					'field_conditional_enable',
					'field_conditional_default',
					'field_conditional_scheme'
				),
			),
		) );

		$this->specific_settings = apply_filters( "uni_cpo_"
		                                          . $this->option_type
		                                          . "_admin_specific_settings_filter",
			array(
				'field_input_show_text_input' => array(
					'select' => array(
						'title'    => esc_html__( 'Show input', 'uni-cpo' ),
						'options'  => array(
							'no'  => esc_html__( 'No', 'uni-cpo' ),
							'yes' => esc_html__( 'Yes', 'uni-cpo' ),
						),
						'tip'      => esc_html__( 'Show/hide text input',
							'uni-cpo' ),
						'name'     => 'field_input_show_text_input',
						'required' => FALSE,
					),
				),
				'field_input_range_type'      => array(
					'select' => array(
						'title'    => esc_html__( 'Type range', 'uni-cpo' ),
						'options'  => array(
							'single' => esc_html__( 'Single', 'uni-cpo' ),
							'double' => esc_html__( 'Double', 'uni-cpo' ),
						),
						'tip'      => esc_html__( 'Change type range input',
							'uni-cpo' ),
						'name'     => 'field_input_range_type',
						'required' => FALSE,
					),
				),
				'field_input_grid'            => array(
					'select' => array(
						'title'    => esc_html__( 'Enable grid', 'uni-cpo' ),
						'options'  => array(
							'no'  => esc_html__( 'No', 'uni-cpo' ),
							'yes' => esc_html__( 'Yes', 'uni-cpo' ),
						),
						'tip'      => esc_html__( 'Enable/disable numbered grid',
							'uni-cpo' ),
						'name'     => 'field_input_grid',
						'required' => FALSE,
					),
				),
				'field_input_default_from'    => array(
					'text_input' => array(
						'title'              => esc_html__( 'Default from',
							'uni-cpo' ),
						'type'               => 'text',
						'name'               => 'field_input_default_from',
						'validation_pattern' => '/^(\d+(?:[\.]\d{0,4})?)$/',
						'required'           => FALSE,
					),
				),
				'field_input_default_end'     => array(
					'text_input' => array(
						'title'              => esc_html__( 'Default end',
							'uni-cpo' ),
						'type'               => 'text',
						'name'               => 'field_input_default_end',
						'validation_pattern' => '/^(\d+(?:[\.]\d{0,4})?)$/',
						'required'           => FALSE,
					),
				),
				'field_input_max_interval'    => array(
					'text_input' => array(
						'title'              => esc_html__( 'Max interval',
							'uni-cpo' ),
						'tip'                => esc_html__( 'Add the maximum interval for this field. Only integer or float number.',
							'uni-cpo' ),
						'type'               => 'text',
						'name'               => 'field_input_max_interval',
						'validation_pattern' => '/^(\d+(?:[\.]\d{0,4})?)$/',
						'required'           => FALSE,
					),
				),
				'field_input_min_interval'    => array(
					'text_input' => array(
						'title'              => esc_html__( 'Min interval',
							'uni-cpo' ),
						'tip'                => esc_html__( 'Add the minimum interval for this field. Only integer or float number.',
							'uni-cpo' ),
						'type'               => 'text',
						'name'               => 'field_input_min_interval',
						'validation_pattern' => '/^(\d+(?:[\.]\d{0,4})?)$/',
						'required'           => FALSE,
					),
				),
			) );
	}

	/**
	 * Displays option in the front end
	 *
	 */
	public function render_option( $aChildrenOptionsIds = array() ) {

		$option_meta          = $this->get_post_meta();
		$sElementFieldName    = $this->get_slug();
		$sInputValue          = '';
		$aAllowedHtml         = uni_cpo_allowed_html_for_option_titles();
		$aAllowedTootltipHtml = uni_cpo_allowed_html_for_tooltips();
		$tooltip_icon_html    = uni_cpo_option_tooltip_icon_output( $this );
		$is_hide
		                      = ( isset( $option_meta['_uni_cpo_field_conditional_default'][0] )
		                          && 'hide'
		                             === $option_meta['_uni_cpo_field_conditional_default'][0] )
			? 'hide' : 'show';

		if ( isset( $_POST[ $sElementFieldName ] )
		     && ! empty( $_POST[ $sElementFieldName ] )
		) {
			$sInputValue = $_POST[ $sElementFieldName ];
		} elseif ( isset( $option_meta['_uni_cpo_field_input_default_from'][0] ) ) {
			$sInputValue
				= esc_attr( $option_meta['_uni_cpo_field_input_default_from'][0] );
		} elseif ( isset( $option_meta['_uni_cpo_field_range_number_min'][0] ) ) {
			$sInputValue
				= esc_attr( $option_meta['_uni_cpo_field_range_number_min'][0] );
		} else {
			$sInputValue = 0;
		}

		if ( ! empty( $option_meta['_uni_cpo_field_input_range_type'][0] )
		     && $option_meta['_uni_cpo_field_input_range_type'][0] === 'double'
		) {
			if ( ! empty( $_POST[ $sElementFieldName . '_end' ] ) ) {
				$sInputValue_end = $_POST[ $sElementFieldName . '_end' ];
			} elseif ( ! empty( $option_meta['_uni_cpo_field_input_default_end'][0] ) ) {
				$sInputValue_end
					= esc_attr( $option_meta['_uni_cpo_field_input_default_end'][0] );
			} elseif ( ! empty( $option_meta['_uni_cpo_field_range_number_max'][0] ) ) {
				$sInputValue_end
					= esc_attr( $option_meta['_uni_cpo_field_range_number_max'][0] );
			} else {
				$sInputValue_end = '100';
			}
		}

		if ( ! isset( $option_meta['_uni_cpo_field_header_tooltip_type'][0] )
		     || empty( $option_meta['_uni_cpo_field_header_tooltip_type'][0] )
		) {
			$sTooltipType = 'classic';
		} else {
			$sTooltipType
				= $option_meta['_uni_cpo_field_header_tooltip_type'][0];
		}

		$sParsleyPattern = '';
		if ( isset( $option_meta['_uni_cpo_field_input_parsley_pattern'][0] )
		     && ! empty( $option_meta['_uni_cpo_field_input_parsley_pattern'][0] )
		) {
			$sParsleyPattern = ' data-parsley-pattern="'
			                   . esc_attr( $option_meta['_uni_cpo_field_input_parsley_pattern'][0] )
			                   . '"';
		}

		do_action( 'uni_cpo_before_option', $this );

		echo '<div id="' . esc_attr( $sElementFieldName ) . '" data-type="'
		     . esc_attr( $this->get_type() )
		     . '" class="uni_cpo_fields_container uni_cpo_field_type_'
		     . esc_attr( $this->get_type() )
		     . ( ( ! empty( $option_meta['_uni_cpo_field_extra_class'][0] ) )
				? ' '
				  . esc_attr( $option_meta['_uni_cpo_field_extra_class'][0] )
				: '' ) . '">';

		if ( ! empty( $option_meta['_uni_cpo_field_header_text'][0] )
		     && ! empty( $option_meta['_uni_cpo_field_header_type'][0] )
		) {
			echo '<' . esc_attr( $option_meta['_uni_cpo_field_header_type'][0] )
			     . ' class="uni_cpo_fields_header">';
			echo wp_kses( $option_meta['_uni_cpo_field_header_text'][0],
					$aAllowedHtml )
			     . ( ( ! empty( $option_meta['_uni_cpo_field_required_enable'][0] )
			           && $option_meta['_uni_cpo_field_required_enable'][0]
			              == 'yes' )
					? ' <span class="uni-cpo-required-label">*</span>' : '' );

			// tooltips
			if ( $sTooltipType == 'classic' ) {
				if ( ! empty( $option_meta['_uni_cpo_field_header_tooltip_text'][0] )
				     && empty( $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0] )
				) {
					echo ' <span class="uni-cpo-tooltip" data-tooltip-content="#uni-cpo-tooltip-'
					     . esc_attr( $sElementFieldName ) . '">'
					     . $tooltip_icon_html . '</span>';
					echo '<div class="tooltip_templates"><div id="uni-cpo-tooltip-'
					     . esc_attr( $sElementFieldName ) . '">'
					     . wp_kses( $option_meta['_uni_cpo_field_header_tooltip_text'][0],
							$aAllowedTootltipHtml ) . '</div></div>';
				} elseif ( ! empty( $option_meta['_uni_cpo_field_header_tooltip_text'][0] )
				           && ! empty( $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0] )
				           && $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0]
				              !== 'uni-cpo-custom-tooltip'
				) {
					echo ' <span class="'
					     . esc_attr( $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0] )
					     . ' uni-cpo-tooltip-element" title="'
					     . esc_attr( wp_kses( $option_meta['_uni_cpo_field_header_tooltip_text'][0],
							$aAllowedTootltipHtml ) ) . '">'
					     . $tooltip_icon_html . '</span>';
				} elseif ( ! empty( $option_meta['_uni_cpo_field_header_tooltip_text'][0] )
				           && ! empty( $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0] )
				           && $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0]
				              === 'uni-cpo-custom-tooltip'
				) {
					echo ' <div class="uni-cpo-custom-tooltip-element">';
					echo $tooltip_icon_html;
					echo '<div class="uni-cpo-custom-tooltip-content">';
					echo wp_kses( $option_meta['_uni_cpo_field_header_tooltip_text'][0],
						$aAllowedTootltipHtml );
					echo '</div>';
					echo '</div>';
				}
			} elseif ( $sTooltipType == 'lightbox' ) {
				$iThumbId
					       = intval( $option_meta['_uni_cpo_field_header_tooltip_image'][0] );
				$aImage    = wp_get_attachment_image_src( $iThumbId, 'full' );
				$sImageUrl = $aImage[0];
				echo '<a href="' . esc_url( $sImageUrl )
				     . '" data-lity data-lity-desc="" class="uni-cpo-tooltip-element">';
				echo $tooltip_icon_html;
				echo '</a>';
			}

			echo '</'
			     . esc_attr( $option_meta['_uni_cpo_field_header_type'][0] )
			     . '>';
		}

		if ( ! empty( $option_meta['_uni_cpo_field_required_enable'][0] )
		     && $option_meta['_uni_cpo_field_required_enable'][0] == 'yes'
		) {
			echo '<input id="' . esc_attr( $sElementFieldName )
			     . '-field" name="' . esc_attr( $sElementFieldName )
			     . '" class="' . esc_attr( $sElementFieldName )
			     . '-field js-uni-cpo-field-' . esc_attr( $this->get_type() )
			     . ( ( 'hide' === $is_hide )
					? ' uni-cpo-excluded-from-validation' : '' )
			     . ' uni-cpo-required" value="' . esc_attr( $sInputValue )
			     . '" data-parsley-required="true" data-parsley-trigger="change focusout submit"'
			     . ( ( ! empty( $sParsleyPattern ) ) ? $sParsleyPattern : '' )
			     . ' />';
		} else {
			echo '<input id="' . esc_attr( $sElementFieldName )
			     . '-field" name="' . esc_attr( $sElementFieldName )
			     . '" class="' . esc_attr( $sElementFieldName )
			     . '-field js-uni-cpo-field-' . esc_attr( $this->get_type() )
			     . ( ( 'hide' === $is_hide )
					? ' uni-cpo-excluded-from-validation' : '' ) . '" value="'
			     . esc_attr( $sInputValue ) . '"'
			     . ( ( ! empty( $sParsleyPattern ) ) ? $sParsleyPattern : '' )
			     . ' />';
		}

		if ( ! empty( $option_meta['_uni_cpo_field_input_show_text_input'][0] )
		     && $option_meta['_uni_cpo_field_input_show_text_input'][0]
		        === 'yes'
		) {

			$value = $sInputValue;

			if ( ! empty( $option_meta['_uni_cpo_field_input_range_type'][0] )
			     && $option_meta['_uni_cpo_field_input_range_type'][0]
			        === 'double'
			) {
				$values = array();

				$values[] = $sInputValue;
				$values[] = $sInputValue_end;

				$value = implode( ' - ', $values );

			}

			if ( ! empty( $option_meta['_uni_cpo_field_input_range_type'][0] )
			     && $option_meta['_uni_cpo_field_input_range_type'][0]
			        === 'single'
			) {
				echo '<input type="text" id="' . esc_attr( $sElementFieldName )
				     . '-field-preview" class="'
				     . esc_attr( $sElementFieldName )
				     . '-field-preview" value="' . esc_attr( $value ) . '" />';
			}


		}

		if ( ! empty( $option_meta['_uni_cpo_field_input_range_type'][0] )
		     && $option_meta['_uni_cpo_field_input_range_type'][0] === 'double'
		) {
			echo '<input id="' . esc_attr( $sElementFieldName )
			     . '-field-end" type="hidden" name="'
			     . esc_attr( $sElementFieldName ) . '_end" value="'
			     . esc_attr( $sInputValue_end ) . '" />';
		}

		?>

        <script>
            jQuery(document).ready(function ($) {
                'use strict';

                var $slider_<?php echo esc_attr( $sElementFieldName ) ?> = $('input[name=<?php echo esc_attr( $sElementFieldName ) ?>]').data("ionRangeSlider");

                window.ionRangeParameters = {
                    'from': <?php echo esc_attr( $sInputValue ); ?>,
                    'min': <?php echo( ( isset( $option_meta['_uni_cpo_field_range_number_min'][0] ) )
					? esc_attr( $option_meta['_uni_cpo_field_range_number_min'][0] )
					: '0' ) ?>,
                    'max': <?php echo( ( isset( $option_meta['_uni_cpo_field_range_number_max'][0] ) )
					? esc_attr( $option_meta['_uni_cpo_field_range_number_max'][0] )
					: '100' ) ?>,
                    'step': <?php echo( ( isset( $option_meta['_uni_cpo_field_range_number_step'][0] ) )
					? esc_attr( $option_meta['_uni_cpo_field_range_number_step'][0] )
					: '1' ) ?>
                };



				<?php if (! empty( $sInputValue_end )) {?>
                ionRangeParameters['to'] = '<?php echo esc_attr( $sInputValue_end ); ?>';
				<?php }?>

				<?php if (! empty( $option_meta['_uni_cpo_field_input_range_type'][0] )) {?>
                ionRangeParameters['type'] = '<?php echo esc_attr( $option_meta['_uni_cpo_field_input_range_type'][0] ); ?>';
				<?php }?>

				<?php if (! empty( $option_meta['_uni_cpo_field_input_min_interval'][0] )) {?>
                ionRangeParameters['min_interval'] = <?php echo esc_attr( $option_meta['_uni_cpo_field_input_min_interval'][0] ); ?>;
				<?php }?>

				<?php if (! empty( $option_meta['_uni_cpo_field_input_max_interval'][0] )) {?>
                ionRangeParameters['max_interval'] = <?php echo esc_attr( $option_meta['_uni_cpo_field_input_max_interval'][0] ); ?>;
				<?php }?>

				<?php if ( ! empty( $option_meta['_uni_cpo_field_input_grid'][0] )
				           && $option_meta['_uni_cpo_field_input_grid'][0]
				              == 'yes' ) {?>
                ionRangeParameters['grid'] = true;
				<?php }?>

                if ($slider_<?php echo esc_attr( $sElementFieldName ) ?> ) {

                    $slider_<?php echo esc_attr( $sElementFieldName ) ?>.update(ionRangeParameters);

                    $('#<?php echo esc_attr( $sElementFieldName ); ?>-field-preview').on('keyup', function () {

                        if (this.value > ionRangeParameters['max']) {
                            ionRangeParameters['from'] = ionRangeParameters['max'];
                        }

                        if (this.value < ionRangeParameters['min']) {
                            ionRangeParameters['from'] = ionRangeParameters['min'];
                        }

                        $slider_<?php echo esc_attr( $sElementFieldName ) ?>.update(ionRangeParameters);

                        uni_cpo_form_processing();

                    });
                }

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
		$option_meta = $this->get_post_meta();

		$output = '<tr>';

		$output .= '<th><label>' . esc_html( $this->get_label() ) . ' <i>('
		           . esc_html( $this->get_slug() ) . ')</i></label></th>';
		$output .= '<td><input type="text" name="'
		           . esc_attr( $this->get_slug() ) . '" value="'
		           . ( isset( $posted_data[ $this->get_slug() ] )
				? $posted_data[ $this->get_slug() ] : '' ) . '" /></td>';

		$output .= '</tr>';

		if ( ! empty( $this->get_special_vars() ) ) {
			foreach ( $this->get_special_vars() as $var_suffix ) {
				$output .= '<tr class="cpo_order_item_options_special_var">';

				$output .= '<th><label><i>(' . esc_html( $this->get_slug() . '_'
				                                         . $var_suffix )
				           . ')</i></label></th>';
				$output .= '<td><input type="text" name="'
				           . esc_attr( $this->get_slug() . '_' . $var_suffix )
				           . '" value="'
				           . ( isset( $posted_data[ $this->get_slug() . '_'
				                                    . $var_suffix ] )
						? $posted_data[ $this->get_slug() . '_' . $var_suffix ]
						: '' ) . '" /></td>';

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
		$option_meta = $this->get_post_meta();
		if ( 'range_slider' === $this->option_type
		     && isset( $option_meta['_uni_cpo_field_input_range_type'][0] )
		     && $option_meta['_uni_cpo_field_input_range_type'][0] == 'double'
		) {
			return array( 'end' );
		} else {
			return array();
		}
	}

	/**
	 * Generates query builder array of data
	 *
	 */
	public function generate_filter( $bFull = FALSE ) {
		$aFilterArray[] = array(
			'id'        => $this->post->post_name,
			'label'     => $this->post->post_title,
			'type'      => 'string',
			'input'     => 'text',
			'operators' => array(
				'less',
				'less_or_equal',
				'equal',
				'not_equal',
				'greater_or_equal',
				'greater',
				'is_empty',
				'is_not_empty'
			),
		);

		if ( $bFull ) {

			$sPostName      = trim( $this->post->post_title, '{' );
			$sPostName      = trim( $sPostName, '}' );
			$aFilterArray[] = array(
				'id'        => $this->post->post_name . '_end',
				'label'     => '{' . $sPostName . '_end}',
				'type'      => 'string',
				'input'     => 'text',
				'operators' => array(
					'less',
					'less_or_equal',
					'equal',
					'not_equal',
					'greater_or_equal',
					'greater',
					'is_empty',
					'is_not_empty'
				)
			);

		}

		return $aFilterArray;
	}

	/**
	 * Generates an array of vars and values for formula calculation associated with this option
	 *
	 */
	public function calculation( $aFormPostData, $bCartMeta = FALSE ) {

		$sElementFieldName = trim( $this->post->post_title, '{}' );

		if ( isset( $aFormPostData[ $sElementFieldName ] )
		     && ! empty( $aFormPostData[ $sElementFieldName ] )
		) {

			$price = floatval( get_post_meta( $this->get_id(),
				'_uni_cpo_field_option_price', TRUE ) );

			if ( ! empty( $aFormPostData[ $sElementFieldName . '_end' ] ) ) {

				if ( ! $bCartMeta ) {
					if ( ! empty( $price ) ) {
						return array(
							$sElementFieldName          => $price,
							$sElementFieldName
							. '_end'                    => intval( $aFormPostData[ $sElementFieldName
							                                                       . '_end' ] )
						);
					} else {
						return array(
							$sElementFieldName          => intval( $aFormPostData[ $sElementFieldName ] ),
							$sElementFieldName
							. '_end'                    => intval( $aFormPostData[ $sElementFieldName
							                                                       . '_end' ] )
						);
					}
				} else if ( $bCartMeta == 'cart' ) {

					return array(
						$sElementFieldName          => $aFormPostData[ $sElementFieldName ],
						$sElementFieldName
						. '_end'                    => intval( $aFormPostData[ $sElementFieldName
						                                                       . '_end' ] )
					);
				} else {
					return $aFormPostData[ $sElementFieldName ] . ' - '
					       . $aFormPostData[ $sElementFieldName . '_end' ];
				}
			} else {
				if ( ! $bCartMeta ) {
					if ( ! empty( $price ) ) {
						return $price;
					} else {
						return $aFormPostData[ $sElementFieldName ];
					}
				} else {
					return $aFormPostData[ $sElementFieldName ];
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