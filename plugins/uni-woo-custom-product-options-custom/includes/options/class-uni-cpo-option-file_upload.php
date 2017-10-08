<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_file_upload class
*
*/

class Uni_Cpo_Option_file_upload extends Uni_Cpo_Option {

    public $id = 0;

    public $post = null;

    public $option_type = 'file_upload';

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

        $this->option_icon = 'fa-upload';
        $this->option_name = esc_html__('File upload', 'uni-cpo');
        $this->tab_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_settings_filter", array(
                                    array(
                                        'section_title' => esc_html__('General settings', 'uni-cpo'),
                                        'settings' => array('field_slug', 'field_required_enable', 'field_option_price',
                                                    'field_file_max_size', 'field_file_allowed_mime_types')
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
                                    'field_file_max_size' => array( 'text_input' =>
                                        array(
                                            'title' => esc_html__('Max. file size (MB)', 'uni-cpo'),
                                            'tip' => esc_html__('Define max file size allowed in MB. Default is "1". Also you can define a decimal value like this "0.5".', 'uni-cpo'),
                                            'description' => sprintf( esc_html__('Important: maximum upload file size set on your hosting is %d MB. You must not set a bigger value than this!'), esc_attr(ini_get('upload_max_filesize')) ),
                                            'type' => 'text',
                                            'name' => 'field_file_max_size',
                                            'validation_pattern' => '/^(\d+(?:[\.]\d{0,2})?)$/',
                                            'required' => false
                                        )
                                    ),
                                    'field_file_allowed_mime_types' => array( 'text_input' =>
                                        array(
                                            'type' => 'text',
                                            'title' => esc_html__('Allowed mime types of files', 'uni-cpo'),
                                            'tip' => esc_html__('Define allowed mime types of files. Add comma separated values. Example: "image/jpeg,image/png" (these are also default types)', 'uni-cpo'),
                                            'description' => sprintf( esc_html__('You may find helpful this', 'uni-cpo') . ' <a href="https://codex.wordpress.org/Function_Reference/get_allowed_mime_types" target="_blank">'.esc_html__('list of WP default allowed mime types', 'uni-cpo').'</a>'),
                                            'name' => 'field_file_allowed_mime_types',
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
        $sInputValue        = '';
        $aAllowedHtml       = uni_cpo_allowed_html_for_option_titles();
        $aAllowedTootltipHtml = uni_cpo_allowed_html_for_tooltips();
        $tooltip_icon_html  = uni_cpo_option_tooltip_icon_output( $this );

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

                if ( !empty($aOptionCustomMeta['_uni_cpo_field_required_enable'][0]) && $aOptionCustomMeta['_uni_cpo_field_required_enable'][0] == 'yes' ) {
                    echo '<input id="'.esc_attr( $sElementFieldName ).'-field" type="file" name="'.esc_attr( $sElementFieldName ).'" id="'.esc_attr( $sElementFieldName ).'-field" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).' uni-cpo-required" value="" data-parsley-filemaxmegabytes="' . ( ( isset($aOptionCustomMeta['_uni_cpo_field_file_max_size'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_file_max_size'][0]) ) ? esc_attr($aOptionCustomMeta['_uni_cpo_field_file_max_size'][0]) : '1') . '" data-parsley-filemimetypes="' . ( ( isset($aOptionCustomMeta['_uni_cpo_field_file_allowed_mime_types'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_file_allowed_mime_types'][0]) ) ? esc_attr($aOptionCustomMeta['_uni_cpo_field_file_allowed_mime_types'][0]) : 'image/jpeg,image/png') . '" data-parsley-required="true" data-parsley-trigger="change focusout submit"' . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .' />';
                    echo '<label for="'.esc_attr( $sElementFieldName ).'-field" class="'.esc_attr( $sElementFieldName ).'-field-label uni-cpo-input-file-label"><span>'.esc_html__('Choose a file&hellip;', 'uni-cpo').'</span></label>';
                } else {
                    echo '<input id="'.esc_attr( $sElementFieldName ).'-field" type="file" name="'.esc_attr( $sElementFieldName ).'" id="'.esc_attr( $sElementFieldName ).'-field" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).'" value="" data-parsley-filemaxmegabytes="' . ( ( isset($aOptionCustomMeta['_uni_cpo_field_file_max_size'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_file_max_size'][0]) ) ? esc_attr($aOptionCustomMeta['_uni_cpo_field_file_max_size'][0]) : '1') . '" data-parsley-filemimetypes="' . ( ( isset($aOptionCustomMeta['_uni_cpo_field_file_allowed_mime_types'][0]) && !empty($aOptionCustomMeta['_uni_cpo_field_file_allowed_mime_types'][0]) ) ? esc_attr($aOptionCustomMeta['_uni_cpo_field_file_allowed_mime_types'][0]) : 'image/jpeg,image/png') . '"' . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .' />';
                    echo '<label for="'.esc_attr( $sElementFieldName ).'-field" class="'.esc_attr( $sElementFieldName ).'-field-label uni-cpo-input-file-label"><span>'.esc_html__('Choose a file&hellip;', 'uni-cpo').'</span></label>';
                }

                    ?>
                <script>
                jQuery( document ).ready( function( $ ) {
                    'use strict';

                    uniCpoBetterFileUpload( "[name=<?php echo esc_attr( $sElementFieldName ) ?>]" );

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
	 * Generates query builder array of data
	 *
	 */
	public function generate_filter( $bFull = false ) {
        $aFilterArray[] = array(
            'id' => $this->post->post_name,
            'label' => $this->post->post_title,
            'type' => 'string',
            'input' => 'text',
            'operators' => array( 'is_empty', 'is_not_empty' )
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