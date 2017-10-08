<?php
/**
 * Uni Cpo Option Functions
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main function for returning options, uses the Uni_Cpo_Option_Factory class.
 *
 */
function uni_cpo_get_option( $option = false, $type = '' ) {
	return UniCpo()->option_factory->get_option( $option, $type );
}

/**
 * Get all registered option types.
 *
 */
function uni_cpo_get_option_types() {

	$option_types = array();

    $option_types = array(
        'heading', 'divider', 'notice', 'notice_nonoptionvar', 'custom_js',
        'text_input', 'text_area', 'select', 'checkboxes', 'radio',
        'range_slider', 'date_picker', 'palette_select', 'image_select', 'text_select',
        'file_upload', 'color_picker', 'download'
    );

    $aUniCpoSettings = get_option( 'uni_cpo_settings_general' );
    if ( ! empty($aUniCpoSettings['uni_google_maps_api_key']) ) {
        $option_types[] = 'google_maps';
    }

    // make it possible for third-party plugins to add a new type of options
	$option_types = apply_filters( 'uni_cpo_option_types_filter', $option_types );
    // reserved option types
    $reserved = uni_cpo_get_reserved_option_types();
    foreach ( $option_types as $type ) {
        if ( in_array( $type, $reserved ) ) {
            unset( $option_types[$type] );
        }
    }
    return $option_types;
}

/**
 * uni_cpo_get_reserved_option_types()
 *
 */
function uni_cpo_get_reserved_option_types() {
    return array('special_var');
}

/**
 * uni_cpo_get_reserved_option_slugs()
 *
 */
function uni_cpo_get_reserved_option_slugs() {
    return array( 
            'quantity', 'list_of_attachments',
            'raw_price', 'raw_price_tax_rev', 'price', 'price_suffix', 'price_discounted',
            'raw_total', 'raw_total_tax_rev', 'total', 'total_tax_rev', 'total_suffix'
    );
}

/**
 * uni_cpo_get_option_admin_fields_attributes()
 *
 */
function uni_cpo_get_option_admin_fields_attributes() {

        return apply_filters( 'uni_cpo_admin_fields_attributes_filter', array(
                    'field_slug' => array( 'text_input' =>
                        array(
                            'type' => 'text',
                            'title' => esc_html__('Field slug', 'uni-cpo'),
                            'description' => esc_html__('Enter a unique slug name for this field (only lowercase latin letters, digits and underscore!). This will be used as a name of the formula variable connected with this field!', 'uni-cpo'),
                            'name' => 'field_slug',
                            'required' => true,
                            'validation_pattern' => '/^[a-z0-9]+(?:_[a-z0-9]+)*$/'
                        )
                    ),
                    'field_header_type' => array( 'select' =>
                        array(
                            'title' => esc_html__('Header type', 'uni-cpo'),
                            'tip' => esc_html__('Choose type of HTML tag to be used for this field title', 'uni-cpo'),
                            'options' => array('h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6', 'p' => 'p', 'span' => 'span', 'label' => 'label'),
                            'name' => 'field_header_type',
                            'required' => false
                        )
                    ),
                    'field_header_text' => array( 'textarea' =>
                        array(
                            'type' => 'text',
                            'title' => esc_html__('Header text', 'uni-cpo'),
                            'tip' => esc_html__('Custom text that will be used for this field as its title on the product page and as the cart/order
                            item\'s option\'s title. Leave it blank and there will not be a title for this field and the option\'s slug will be used
                             as a title for cart/order item', 'uni-cpo'),
                            'name' => 'field_header_text',
                            'required' => false
                        )
                    ),
                    'field_header_tooltip_type' => array( 'select' =>
                        array(
                            'title' => esc_html__('Tooltip type', 'uni-cpo'),
                            'tip' => esc_html__('Choose between classic toltip or lightbox', 'uni-cpo'),
                            'options' => array( 'classic' => esc_html__('Classic', 'uni-cpo'), 'lightbox' => esc_html__('Lightbox', 'uni-cpo') ),
                            'name' => 'field_header_tooltip_type',
                            'required' => false
                        )
                    ),
                    'field_header_tooltip_text' => array( 'textarea' =>
                        array(
                            'type' => 'text',
                            'title' => esc_html__('Text of tooltip', 'uni-cpo'),
                            'tip' => esc_html__('Custom text that will be used in tooltip.', 'uni-cpo'),
                            'name' => 'field_header_tooltip_text',
                            'required' => false
                        )
                    ),
                    'field_header_tooltip_image' => array( 'text_input' =>
                        array(
                            'type' => 'text',
                            'title' => esc_html__('Image to be used in lightboxed tooltip', 'uni-cpo'),
                            'tip' => esc_html__('Something', 'uni-cpo'),
                            'name' => 'field_header_tooltip_image',
                            'required' => false
                        )
                    ),
                    'field_extra_tooltip_selector_class' => array( 'text_input' =>
                        array(
                            'type' => 'text',
                            'title' => esc_html__('Custom css selector for tooltip', 'uni-cpo'),
                            'tip' => esc_html__('Define custom class name for the tootltip element. Some themes (like Flatsome) hardcoded the related tooltip plugin, so you have to define here a class that works in such themes.', 'uni-cpo'),
                            'name' => 'field_extra_tooltip_selector_class',
                            'required' => false
                        )
                    ),
                    'field_meta_header_text' => array( 'textarea' =>
                        array(
                            'type' => 'text',
                            'title' => esc_html__('Header for cart/order meta', 'uni-cpo'),
                            'tip' => esc_html__('Custom text that will be used as a title for this option in cart/order item meta', 'uni-cpo'),
                            'name' => 'field_meta_header_text',
                            'required' => false
                        )
                    ),
                    'field_notice_header' => array( 'textarea' =>
                        array(
                            'type' => 'text',
                            'title' => esc_html__('Notice heading', 'uni-cpo'),
                            'tip' => esc_html__('Just a piece of text that will be displayed on top and in bold.', 'uni-cpo'),
                            'name' => 'field_notice_header',
                            'required' => false
                        )
                    ),
                    'field_notice_text' => array( 'textarea_js' =>
                        array(
                            'type' => 'text',
                            'title' => esc_html__('Notice text', 'uni-cpo'),
                            'tip' => esc_html__('Custom text for this field. Simple HTML tags can be used here.', 'uni-cpo'),
                            'name' => 'field_notice_text',
                            'required' => true
                        )
                    ),
                    'field_notice_shortcode' => array( 'text_input' =>
                        array(
                            'type' => 'text',
                            'title' => esc_html__('Shortcode', 'uni-cpo'),
                            'tip' => esc_html__('It is possible to add any WP shortcode. It will be executed and displayed below notice text.', 'uni-cpo'),
                            'name' => 'field_notice_shortcode',
                            'strip_slashes' => true,
                            'required' => false
                        )
                    ),
                    'field_notice_type' => array( 'select' =>
                        array(
                            'title' => esc_html__('Notice style', 'uni-cpo'),
                            'tip' => esc_html__('Choose a style of the notice to be used', 'uni-cpo'),
                            'options' => uni_cpo_get_styles_of_notice_div(),
                            'name' => 'field_notice_type',
                            'required' => false
                        )
                    ),
                    'field_extra_class' => array( 'text_input' =>
                        array(
                            'type' => 'text',
                            'title' => esc_html__('Custom class name', 'uni-cpo'),
                            'tip' => esc_html__('Add an additional css class name for the wrapper of this field', 'uni-cpo'),
                            'name' => 'field_extra_class',
                            'required' => false
                        )
                    ),
                    'field_conditional_enable' => array( 'select' =>
                        array(
                            'title' => esc_html__('Enable conditional logic?', 'uni-cpo'),
                            'tip' => esc_html__('Enables conditional logic for showing or hiding this element.', 'uni-cpo'),
                            'doc_link' => esc_url('http://moomoo.agency/demo/cpo/docs#conditional-fields'),
                            'options' => array( 'no' => esc_html__('No', 'uni-cpo'), 'yes' => esc_html__('Yes', 'uni-cpo') ),
                            'name' => 'field_conditional_enable',
                            'required' => false
                        )
                    ),
                    'field_conditional_default' => array( 'select' =>
                        array(
                            'title' => esc_html__('Show or hide by default?', 'uni-cpo'),
                            'tip' => esc_html__('Choose whether this option should be shown or hidden at the beggining, so its state will be changed to the opposite once the condition is met.', 'uni-cpo'),
                            'doc_link' => '',
                            'options' => array( 'show' => esc_html__('Show', 'uni-cpo'), 'hide' => esc_html__('Hide', 'uni-cpo') ),
                            'name' => 'field_conditional_default',
                            'required' => false
                        )
                    ),
                    'field_conditional_scheme' => array( 'builder_cond_field' =>
                        array(
                            'title' => esc_html__('Field conditional logic builder', 'uni-cpo'),
                            'tip' => '',
                            'doc_link' => '',
                            'name' => 'field_conditional_scheme',
                            'required' => false
                        )
                    ),
                    'val_conditional_enable' => array( 'select' =>
                        array(
                            'title' => esc_html__('Enable validation conditional logic?', 'uni-cpo'),
                            'tip' => esc_html__('Enables validation conditional logic for this element.', 'uni-cpo'),
                            'doc_link' => esc_url('http://moomoo.agency/demo/cpo/docs#conditional-fields'),
                            'options' => array( 'no' => esc_html__('No', 'uni-cpo'), 'yes' => esc_html__('Yes', 'uni-cpo') ),
                            'name' => 'val_conditional_enable',
                            'required' => false
                        )
                    ),
                    'val_conditional_scheme' => array( 'builder_cond_val' =>
                        array(
                            'title' => esc_html__('Validation conditional logic builder', 'uni-cpo'),
                            'tip' => '',
                            'doc_link' => '',
                            'name' => 'val_conditional_scheme',
                            'required' => false
                        )
                    ),
                    'field_required_enable' => array( 'select' =>
                        array(
                            'title' => esc_html__('Required?', 'uni-cpo'),
                            'tip' => esc_html__('Choose whether the user must fill out this field or not.', 'uni-cpo'),
                            'options' => array( 'no' => esc_html__('No', 'uni-cpo'), 'yes' => esc_html__('Yes', 'uni-cpo') ),
                            'name' => 'field_required_enable',
                            'required' => false
                        )
                    ),
                    'field_chars_min' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Min. number of characters', 'uni-cpo'),
                            'tip' => esc_html__('Add the minimum allowed number of characters for this field.', 'uni-cpo'),
                            'type' => 'number',
                            'name' => 'field_chars_min',
                            'validation_type' => 'number',
                            'required' => false
                        )
                    ),
                    'field_chars_max' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Max. number of characters', 'uni-cpo'),
                            'tip' => esc_html__('Add the maximum allowed number of characters for this field.', 'uni-cpo'),
                            'type' => 'number',
                            'name' => 'field_chars_max',
                            'validation_type' => 'number',
                            'required' => false
                        )
                    ),
                    'field_input_tag_type' => array( 'select' =>
                        array(
                            'title' => esc_html__('Type of "input" html tag', 'uni-cpo'),
                            'tip' => esc_html__('Choose the type based on type of data you are expecting from users. For instance, if you expect some text or
                             don\'t mind about type of data, choose type "string". It is an universal format. In the case you want numbers only, choose type "integer".', 'uni-cpo'),
                            'options' => array(
                                'string' => esc_html__('String', 'uni-cpo'),
                                'integer' => esc_html__('Integer', 'uni-cpo'),
                                'double' => esc_html__('Double', 'uni-cpo')
                            ),
                            'name' => 'field_input_tag_type',
                            'required' => false
                        )
                    ),
                    'field_input_number_min' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Min value', 'uni-cpo'),
                            'tip' => esc_html__('Add the minimum allowed value for this field. Only integer or float number.', 'uni-cpo'),
                            'type' => 'text',
                            'name' => 'field_input_number_min',
                            'validation_pattern' => '/^(\d+(?:[\.]\d{0,4})?)$/',
                            'required' => false
                        )
                    ),
                    'field_input_number_max' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Max value', 'uni-cpo'),
                            'tip' => esc_html__('Add the maximum allowed value for this field. Only integer or float number.', 'uni-cpo'),
                            'type' => 'text',
                            'name' => 'field_input_number_max',
                            'validation_pattern' => '/^(\d+(?:[\.]\d{0,4})?)$/',
                            'required' => false
                        )
                    ),
                    'field_input_number_step' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Step value', 'uni-cpo'),
                            'tip' => esc_html__('Add the step value for this field. Only integer or float number.', 'uni-cpo'),
                            'type' => 'text',
                            'name' => 'field_input_number_step',
                            'validation_pattern' => '/^(\d+(?:[\.]\d{0,4})?)$/',
                            'required' => false
                        )
                    ),
                    'field_input_number_default' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Default value', 'uni-cpo'),
                            'tip' => esc_html__('Add the default value for this field', 'uni-cpo'),
                            'type' => 'text',
                            'name' => 'field_input_number_default',
                            'required' => false
                        )
                    ),
                    'field_divider_type' => array( 'select' =>
                        array(
                            'title' => esc_html__('Divider type', 'uni-cpo'),
                            'tip' => esc_html__('Choose a type of HTML tag / style of the divider to be used', 'uni-cpo'),
                            'options' => uni_cpo_get_styles_of_divider_div(),
                            'name' => 'field_divider_type',
                            'required' => false
                        )
                    ),
                    'field_option_price' => array( 'text_input' =>
                        array(
                            'type' => 'text',
                            'title' => esc_html__('Price / Rate', 'uni-cpo'),
                            'tip' => esc_html__('Enter the price (or rate) for this field. This value can be used in a formula. Or leave it blank if you need just an information added by the user.', 'uni-cpo'),
                            'name' => 'field_option_price',
                            'required' => false,
                            'validation_pattern' => '/^(\d+(?:[\.]\d{0,2})?)$/'
                        )
                    ),
                    'field_display_price_in_front' => array( 'select' =>
                        array(
                            'title' => esc_html__('Enable/disable displaying of price(s)', 'uni-cpo'),
                            'tip' => esc_html__('Enable/disable displaying of price(s) in front of option(s) on the product page', 'uni-cpo'),
                            'options' => array( 'no' => esc_html__('No', 'uni-cpo'), 'yes' => esc_html__('Yes', 'uni-cpo') ),
                            'name' => 'field_display_price_in_front',
                            'required' => false
                        )
                    ),
                    'field_display_price_in_front_text' => array( 'text_input' =>
                        array(
                            'type' => 'text',
                            'title' => esc_html__('Custom text for "Display price(s)" option', 'uni-cpo'),
                            'tip' => esc_html__('Add custom text instead of default "+{sPriceValue}". You can use "{sPriceValue}" variable and this var will be replaced with actual price of the option defined in "Options" table located below.', 'uni-cpo'),
                            'name' => 'field_display_price_in_front_text',
                            'required' => false
                        )
                    ),
                    'field_option_select_options' => array( 'select_options' =>
                        array(
                            'title' => esc_html__('Sub options', 'uni-cpo'),
                            'description' => esc_html__('Add some sub options for select and, please, keep unique slugs for them. These slugs might be used in a formula conditional rules (e.g. when you use operators "equal", "not equal" etc).', 'uni-cpo'),
                            'doc_link' => esc_url('http://moomoo.agency/demo/cpo/docs'),
                            'name' => 'field_option_select_options',
                            'required' => false
                        )
                    ),
                    'field_option_checkboxes_options' => array( 'checkboxes_options' =>
                        array(
                            'title' => esc_html__('Sub options', 'uni-cpo'),
                            'description' => esc_html__('Add some sub options for checkboxes and, please, keep unique slugs for them. These slugs might be used in a formula conditional rules (e.g. when you use operators "equal", "not equal" etc).', 'uni-cpo'),
                            'doc_link' => esc_url('http://moomoo.agency/demo/cpo/docs'),
                            'name' => 'field_option_checkboxes_options',
                            'required' => false
                        )
                    ),
                    'field_option_text_options' => array( 'text_options' =>
                        array(
                            'title' => esc_html__('Sub options', 'uni-cpo'),
                            'description' => esc_html__('Add some sub options and, please, keep unique slugs for them. These slugs might be used in a formula conditional rules (e.g. when you use operators "equal", "not equal" etc).', 'uni-cpo'),
                            'doc_link' => esc_url('http://moomoo.agency/demo/cpo/docs'),
                            'name' => 'field_option_text_options',
                            'required' => false
                        )
                    ),
                    'field_thumb_size' => array( 'select' =>
                        array(
                            'title' => esc_html__('Choose an image size', 'uni-cpo'),
                            'tip' => esc_html__('Choose a size for thumbnails for this option.', 'uni-cpo'),
                            'options' => uni_cpo_get_image_sizes_list(),
                            'name' => 'field_thumb_size',
                            'required' => false
                        )
                    ),
                    'field_range_number_min' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Min value', 'uni-cpo'),
                            'tip' => esc_html__('Add the minimum allowed value for this field. Only integer or float number. Default is "1".', 'uni-cpo'),
                            'type' => 'text',
                            'name' => 'field_range_number_min',
                            'validation_pattern' => '/^(\d+(?:[\.]\d{0,2})?)$/',
                            'required' => false
                        )
                    ),
                    'field_range_number_max' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Max value', 'uni-cpo'),
                            'tip' => esc_html__('Add the maximum allowed value for this field. Only integer or float number. Default is "100".', 'uni-cpo'),
                            'type' => 'text',
                            'name' => 'field_range_number_max',
                            'validation_pattern' => '/^(\d+(?:[\.]\d{0,2})?)$/',
                            'required' => false
                        )
                    ),
                    'field_range_number_step' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Step value', 'uni-cpo'),
                            'tip' => esc_html__('Add the step value for this field. Only integer or float number. Default is "1".', 'uni-cpo'),
                            'type' => 'text',
                            'name' => 'field_range_number_step',
                            'validation_pattern' => '/^(\d+(?:[\.]\d{0,2})?)$/',
                            'required' => false
                        )
                    ),
                    'field_media_select_size' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Width of suboption items', 'uni-cpo'),
                            'tip' => esc_html__('Set the width of the items. The default value is 70(px).', 'uni-cpo'),
                            'type' => 'number',
                            'name' => 'field_media_select_size',
                            'validation_type' => 'number',
                            'required' => false
                        )
                    ),
                    'field_media_select_size_height' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Height of suboption items', 'uni-cpo'),
                            'tip' => esc_html__('Set the height of the items. If no height set, the same value as for width will be used. The default value is 70(px).', 'uni-cpo'),
                            'type' => 'number',
                            'name' => 'field_media_select_size_height',
                            'validation_type' => 'number',
                            'required' => false
                        )
                    ),
                    'field_media_select_min_width' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Min width of suboption items', 'uni-cpo'),
                            'tip' => esc_html__('Set the minimum width of the items.', 'uni-cpo'),
                            'type' => 'number',
                            'name' => 'field_media_select_min_width',
                            'validation_type' => 'number',
                            'required' => false
                        )
                    ),
                    'field_media_select_font_size' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Font size', 'uni-cpo'),
                            'tip' => esc_html__('Set the size of the font for the text of a suboption item (px)', 'uni-cpo'),
                            'type' => 'number',
                            'name' => 'field_media_select_font_size',
                            'validation_type' => 'number',
                            'required' => false
                        )
                    ),
                    'field_media_select_padding_top' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Padidng top and bottom', 'uni-cpo'),
                            'tip' => esc_html__('This setting helps to set the height of a suboption item by defining padding (px).', 'uni-cpo'),
                            'type' => 'number',
                            'name' => 'field_media_select_padding_top',
                            'validation_type' => 'number',
                            'required' => false
                        )
                    ),
                    'field_media_select_padding_right' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Padding left and right', 'uni-cpo'),
                            'tip' => esc_html__('This setting helps to set the width of a suboption item by defining padding (px).', 'uni-cpo'),
                            'type' => 'number',
                            'name' => 'field_media_select_padding_right',
                            'validation_type' => 'number',
                            'required' => false
                        )
                    ),
                    'field_media_select_border_width' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Border width', 'uni-cpo'),
                            'tip' => esc_html__('Set the width of the border of an active suboption item in px. The default value is 1(px).', 'uni-cpo'),
                            'type' => 'number',
                            'name' => 'field_media_select_border_width',
                            'validation_type' => 'number',
                            'required' => false
                        )
                    ),
                    'field_media_select_border_color' => array( 'colorpicker' =>
                        array(
                            'title' => esc_html__('Border color (active/hovered)', 'uni-cpo'),
                            'tip' => esc_html__('Choose a color of the border of a suboption item when it is active/hovered.', 'uni-cpo'),
                            'name' => 'field_media_select_border_color',
                            'validation_type' => 'number',
                            'required' => false
                        )
                    ),
                    'field_media_select_border_color_default' => array( 'colorpicker' =>
                        array(
                            'title' => esc_html__('Border color (default)', 'uni-cpo'),
                            'tip' => esc_html__('Choose a color of the border of a suboption item.', 'uni-cpo'),
                            'name' => 'field_media_select_border_color_default',
                            'validation_type' => 'number',
                            'required' => false
                        )
                    ),
                    'field_media_select_border_radius' => array( 'text_input' =>
                        array(
                            'title' => esc_html__('Choose border radius', 'uni-cpo'),
                            'tip' => esc_html__('The radius of the border suboption item in px. The default value is 7(px).', 'uni-cpo'),
                            'type' => 'number',
                            'name' => 'field_media_select_border_radius',
                            'validation_type' => 'number',
                            'required' => false
                        )
                    ),
                    'field_image_change_disable' => array( 'select' =>
                        array(
                            'title' => esc_html__('Disable changing of the main image?', 'uni-cpo'),
                            'tip' => esc_html__('Disable changing of the main image uppon selection of certain suboption? Default is "no".', 'uni-cpo'),
                            'doc_link' => '',
                            'options' => array( 'no' => esc_html__('No', 'uni-cpo'), 'yes' => esc_html__('Yes', 'uni-cpo') ),
                            'name' => 'field_image_change_disable',
                            'required' => false
                        )
                    )
                )
            );

}

    /**
     * styles for divider
     * in case you add anything except 'hr' as an option slug
     * 'div' tag will be used in the front end;
     *
     * a related css class will be applied for the element (div) automatically;
     * this css class will look like: 'uni-cpo-divider-type-*' where '*'
     * is a slug name of the option provided in this array;
     * example: 'uni-cpo-divider-type-thin-line';
     */
    function uni_cpo_get_styles_of_divider_div() {
        return apply_filters( 'uni_cpo_styles_of_divider_div_filter', array(
            'hr' => esc_html__('Standard (hr)', 'uni-cpo'),
            'thin-line' => esc_html__('Thin line (div)', 'uni-cpo')
            )
        );
    }

    /**
     * styles for notice
     */
    function uni_cpo_get_styles_of_notice_div() {
        return apply_filters( 'uni_cpo_styles_of_notice_div_filter', array(
            'style-one' => esc_html__('Notice style #1', 'uni-cpo'),
            //'style-two' => esc_html__('Notice style #2', 'uni-cpo')
            )
        );
    }

    /**
     * uni_cpo_get_image_sizes_list
     */
    function uni_cpo_get_image_sizes_list() {
        $registered_sizes = uni_cpo_get_registered_image_sizes();
        $aSizes = array();
        foreach ( $registered_sizes as $sSlug => $aValue ) {
            $aSizes[$sSlug] = $sSlug.' ('.$aValue['width'].'x'.$aValue['height'].')';
        }
        return $aSizes;
    }

?>