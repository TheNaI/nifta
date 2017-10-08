<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_download class
*
*/

class Uni_Cpo_Option_download extends Uni_Cpo_Option {

    public $id = 0;

    public $post = null;

    public $option_type = 'download';

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

        $this->option_icon = 'fa-download';
        $this->option_name = esc_html__('Download file(s)', 'uni-cpo');
        $this->tab_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_settings_filter", array(
                                    array(
                                        'section_title' => esc_html__('General settings', 'uni-cpo'),
                                        'settings' => array('field_slug', 'field_downloads_display_mode',
                                            'field_option_download_options')
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
                                    'field_option_download_options' => array( 'download_options' =>
                                        array(
                                            'title' => esc_html__('List of files', 'uni-cpo'),
                                            'description' => esc_html__('This is the list of the files available for download. Every file is an attachment.', 'uni-cpo'),
                                            'doc_link' => '',
                                            'name' => 'field_option_download_options',
                                            'required' => false
                                        )
                                    ),
                                    'field_downloads_display_mode' => array( 'select' =>
                                        array(
                                            'title' => esc_html__('Display mode', 'uni-cpo'),
                                            'tip' => '',
                                            'options' => array(
                                                'list' => esc_html__( 'List of links', 'uni-cpo' ),
                                                'links' => esc_html__( 'Links only ("a" tags)', 'uni-cpo' )
                                            ),
                                            'name' => 'field_downloads_display_mode',
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
        $aAllowedTootltipHtml = uni_cpo_allowed_html_for_tooltips();
        $tooltip_icon_html  = uni_cpo_option_tooltip_icon_output( $this );

        if ( !isset($option_meta['_uni_cpo_field_header_tooltip_type'][0]) || empty($option_meta['_uni_cpo_field_header_tooltip_type'][0]) ) {
            $sTooltipType = 'classic';
        } else {
            $sTooltipType = $option_meta['_uni_cpo_field_header_tooltip_type'][0];
        }

        $display_mode = ( isset( $option_meta['_uni_cpo_field_downloads_display_mode'][0] ) && ! empty( $option_meta['_uni_cpo_field_downloads_display_mode'][0] ) ) ? $option_meta['_uni_cpo_field_downloads_display_mode'][0] : 'list';

        do_action( 'uni_cpo_before_option', $this );

        echo '<div id="'.esc_attr( $sElementFieldName ).'" data-type="' . esc_attr( $this->get_type() ) . '" class="uni_cpo_fields_container uni_cpo_field_type_'.esc_attr( $this->get_type() ) . ( (!empty($option_meta['_uni_cpo_field_extra_class'][0])) ? ' '.esc_attr( $option_meta['_uni_cpo_field_extra_class'][0] ) : '' ) .'">';

            if ( !empty( $option_meta['_uni_cpo_field_header_text'][0] ) && !empty( $option_meta['_uni_cpo_field_header_type'][0] ) ) {
                echo '<'.esc_attr($option_meta['_uni_cpo_field_header_type'][0]).' class="uni_cpo_fields_header">';
                echo esc_html($option_meta['_uni_cpo_field_header_text'][0]) . ( ( !empty($option_meta['_uni_cpo_field_required_enable'][0]) && $option_meta['_uni_cpo_field_required_enable'][0] == 'yes' ) ? ' <span class="uni-cpo-required-label">*</span>' : '' );

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

            $list_of_files = ( !empty($option_meta['_uni_cpo_field_option_download_options'][0]) && $option_meta['_uni_cpo_field_option_download_options'][0] ) ? $option_meta['_uni_cpo_field_option_download_options'][0] : '';
            $list_of_files = maybe_unserialize( $list_of_files );
  
            //
            $output = array();
            foreach( $list_of_files as $item ) {
                if ( ! empty( $item['label'] ) && ! empty( $item['file_id'] ) ) {
                    $output[] = '<a href="' . esc_url( wp_get_attachment_url( $item['file_id'] ) ) . '">' . esc_html( $item['label'] ) . '</a>';
                }
            }

            $output = apply_filters( 'uni_cpo_downloads_items_output', $output, $this );

            if ( 'list' === $display_mode ) {
                echo '<ul><li>' . implode('</li><li>', $output) . '</li></ul>';
            } elseif ( 'links' === $display_mode ) {
                echo implode(', ', $output);
            }

            // the field conditional logic
            $this->render_fc_rules();

            echo '<div class="uni-cpo-clear"></div>';
        echo '</div>';

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