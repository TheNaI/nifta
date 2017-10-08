<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_text_input class
*
*/

class Uni_Cpo_Option_date_picker extends Uni_Cpo_Option {

    public $id = 0;

    public $post = null;

    public $option_type = 'date_picker';

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

        $this->option_icon = 'fa-calendar';
        $this->option_name = esc_html__('Date picker', 'uni-cpo');
        $this->tab_settings = apply_filters( "uni_cpo_".$this->option_type."_admin_settings_filter", array(
                                    array(
                                        'section_title' => esc_html__('General settings', 'uni-cpo'),
                                        'settings' => array('field_slug', 'field_required_enable', 'field_datepicker_select',
                                                        'field_datepicker_inline_enable', 'field_datepicker_cells_no',
                                                        'field_datepicker_day_of_week_start', 'field_datepicker_day_of_week_end', 'field_datepicker_start_month',
                                                        'field_datepicker_format_date', 'field_datepicker_format_datetime', 'field_datepicker_format_time',
                                                        'field_datepicker_min_date', 'field_datepicker_max_date',
                                                        'field_datepicker_disable_spec_dates', 'field_datepicker_disable_days_of_week',
                                                        'field_datepicker_hours_enable', 'field_datepicker_minutes_enable', 'field_datepicker_seconds_enable',
                                                        'field_datepicker_ampm_enable',
                                                        'field_datepicker_hours_step', 'field_datepicker_minutes_step', 'field_datepicker_seconds_step',
                                                        'field_datepicker_min_time', 'field_datepicker_max_time',
                                                        'field_option_price')
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
                                    'field_datepicker_select' => array( 'select' =>
                                        array(
                                            'title' => esc_html__('Datetime picker mode', 'uni-cpo'),
                                            'tip' => esc_html__('Switch between only date, only time, both, period picker or period picker with dates only', 'uni-cpo'),
                                            'options' => array(
                                                'date' => esc_html__('Date picker', 'uni-cpo'),
                                                'time' => esc_html__('Time picker', 'uni-cpo'),
                                                'datetime' => esc_html__('Date and time', 'uni-cpo'),
                                                'period' => esc_html__('Period picker (dates and time)', 'uni-cpo'),
                                                'period_date' => esc_html__('Period picker (dates only)', 'uni-cpo')
                                            ),
                                            'name' => 'field_datepicker_select',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_inline_enable' => array( 'select' =>
                                        array(
                                            'title' => esc_html__('Display picker in inline mode?', 'uni-cpo'),
                                            'tip' => esc_html__('Switch between only date, only time, both, period picker or period picker with dates only', 'uni-cpo'),
                                            'options' => array(
                                                'no' => esc_html__('Display in modal', 'uni-cpo'),
                                                'yes' => esc_html__('Display inline', 'uni-cpo')
                                            ),
                                            'name' => 'field_datepicker_inline_enable',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_cells_no' => array( 'select' =>
                                        array(
                                            'title' => esc_html__('Number of months to display in picker', 'uni-cpo'),
                                            'tip' => esc_html__('Choose a number of months to display in the picker', 'uni-cpo'),
                                            'options' => array(
                                                '1' => '1',
                                                '2' => '2',
                                                '3' => '3',
                                                '4' => '4',
                                                '5' => '5',
                                                '6' => '6'
                                            ),
                                            'name' => 'field_datepicker_cells_no',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_day_of_week_start' => array( 'select' =>
                                        array(
                                            'title' => esc_html__('Day for start week', 'uni-cpo'),
                                            'tip' => esc_html__('Default is "Sunday"', 'uni-cpo'),
                                            'options' => uni_cpo_get_week_days_list(),
                                            'name' => 'field_datepicker_day_of_week_start',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_day_of_week_end' => array( 'checkboxes' =>
                                        array(
                                            'title' => esc_html__('The days of the week, which will be designated as weekend', 'uni-cpo'),
                                            'tip' => esc_html__('Default are Saturday and Sunday. The same will be applied when none is checked.', 'uni-cpo'),
                                            'options' => uni_cpo_get_week_days_list(),
                                            'name' => 'field_datepicker_day_of_week_end',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_start_month' => array( 'select' =>
                                        array(
                                            'title' => esc_html__('Start month', 'uni-cpo'),
                                            'tip' => esc_html__('The date picker will display this month instead of the current by default', 'uni-cpo'),
                                            'options' => uni_cpo_get_months_list(),
                                            'name' => 'field_datepicker_start_month',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_format_date' => array( 'text_input' =>
                                        array(
                                            'title' => esc_html__('Custom format for dates', 'uni-cpo'),
                                            'tip' => esc_html__('Must be defined in moment.js format. Default is "DD.MM.YYYY".', 'uni-cpo'),
                                            'doc_link' => 'http://moomoo.agency/demo/cpo/docs#field_datepicker_format_date',
                                            'type' => 'text',
                                            'name' => 'field_datepicker_format_date',
                                            'validation_pattern' => '',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_format_datetime' => array( 'text_input' =>
                                        array(
                                            'title' => esc_html__('Custom format for dates and time', 'uni-cpo'),
                                            'tip' => esc_html__('Must be defined in moment.js format. Default is "HH:mm DD.MM.YYYY".', 'uni-cpo'),
                                            'doc_link' => 'http://moomoo.agency/demo/cpo/docs#field_datepicker_format_date',
                                            'type' => 'text',
                                            'name' => 'field_datepicker_format_datetime',
                                            'validation_pattern' => '',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_format_time' => array( 'text_input' =>
                                        array(
                                            'title' => esc_html__('Custom format for time', 'uni-cpo'),
                                            'tip' => esc_html__('Must be defined in moment.js format. Default is "H:m".', 'uni-cpo'),
                                            'doc_link' => 'http://moomoo.agency/demo/cpo/docs#field_datepicker_format_date',
                                            'type' => 'text',
                                            'name' => 'field_datepicker_format_time',
                                            'validation_pattern' => '',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_min_date' => array( 'text_input' =>
                                        array(
                                            'title' => esc_html__('Min date that can be chosen', 'uni-cpo'),
                                            'tip' => esc_html__('Must be defined in the same format as format for dates setting. Example: if the format is "DD.MM.YYYY", the valid date is "11.06.2016". No min date by default.', 'uni-cpo'),
                                            'doc_link' => 'http://moomoo.agency/demo/cpo/docs#field_datepicker_min_date',
                                            'type' => 'text',
                                            'name' => 'field_datepicker_min_date',
                                            'validation_pattern' => '',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_disable_spec_dates' => array( 'text_input' =>
                                        array(
                                            'title' => esc_html__('Disable specific dates', 'uni-cpo'),
                                            'tip' => esc_html__('Comma separated dates. These days you can not select. Must be defined in the same format as format for dates setting.', 'uni-cpo'),
                                            'doc_link' => 'http://moomoo.agency/demo/cpo/docs#field_datepicker_disable_spec_dates',
                                            'type' => 'text',
                                            'name' => 'field_datepicker_disable_spec_dates',
                                            'validation_pattern' => '',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_disable_days_of_week' => array( 'checkboxes' =>
                                        array(
                                            'title' => esc_html__('The days of the week, which will be disabled', 'uni-cpo'),
                                            'tip' => esc_html__('Checked days of the week will be disabled for a certain period of time starting from today and up to max date defined. Max date setting MUST be specified!', 'uni-cpo'),
                                            'options' => uni_cpo_get_week_days_list(),
                                            'name' => 'field_datepicker_disable_days_of_week',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_max_date' => array( 'text_input' =>
                                        array(
                                            'title' => esc_html__('Max date that can be chosen', 'uni-cpo'),
                                            'tip' => esc_html__('Must be defined in the same format as format for dates setting. Example: if the format is "DD.MM.YYYY", the valid date is "11.06.2016". No max date by default.', 'uni-cpo'),
                                            'doc_link' => '#',
                                            'type' => 'text',
                                            'name' => 'field_datepicker_max_date',
                                            'validation_pattern' => '',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_hours_enable' => array( 'select' =>
                                        array(
                                            'title' => esc_html__('Enable hours in time picker?', 'uni-cpo'),
                                            'tip' => '',
                                            'options' => array(
                                                'yes' => esc_html__('Enable', 'uni-cpo'),
                                                'no' => esc_html__('Disable', 'uni-cpo')
                                            ),
                                            'name' => 'field_datepicker_hours_enable',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_minutes_enable' => array( 'select' =>
                                        array(
                                            'title' => esc_html__('Enable minutes in time picker?', 'uni-cpo'),
                                            'tip' => '',
                                            'options' => array(
                                                'yes' => esc_html__('Enable', 'uni-cpo'),
                                                'no' => esc_html__('Disable', 'uni-cpo')
                                            ),
                                            'name' => 'field_datepicker_minutes_enable',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_seconds_enable' => array( 'select' =>
                                        array(
                                            'title' => esc_html__('Enable seconds in time picker?', 'uni-cpo'),
                                            'tip' => '',
                                            'options' => array(
                                                'yes' => esc_html__('Enable', 'uni-cpo'),
                                                'no' => esc_html__('Disable', 'uni-cpo')
                                            ),
                                            'name' => 'field_datepicker_seconds_enable',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_ampm_enable' => array( 'select' =>
                                        array(
                                            'title' => esc_html__('Switch between 12 and 24 hours format', 'uni-cpo'),
                                            'tip' => '',
                                            'options' => array(
                                                'yes' => esc_html__('12 hours', 'uni-cpo'),
                                                'no' => esc_html__('24 hours', 'uni-cpo')
                                            ),
                                            'name' => 'field_datepicker_ampm_enable',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_hours_step' => array( 'text_input' =>
                                        array(
                                            'title' => esc_html__('Step for hours for time picker', 'uni-cpo'),
                                            'tip' => esc_html__('Add the step value for hours for time picker. Default is "1".', 'uni-cpo'),
                                            'type' => 'text',
                                            'name' => 'field_datepicker_hours_step',
                                            'validation_pattern' => '/^[0-9]+$/',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_minutes_step' => array( 'text_input' =>
                                        array(
                                            'title' => esc_html__('Step for minutes for time picker', 'uni-cpo'),
                                            'tip' => esc_html__('Add the step value for minutes for time picker. Default is "1".', 'uni-cpo'),
                                            'type' => 'text',
                                            'name' => 'field_datepicker_minutes_step',
                                            'validation_pattern' => '/^[0-9]+$/',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_seconds_step' => array( 'text_input' =>
                                        array(
                                            'title' => esc_html__('Step for seconds for time picker', 'uni-cpo'),
                                            'tip' => esc_html__('Add the step value for seconds for time picker. Default is "1".', 'uni-cpo'),
                                            'type' => 'text',
                                            'name' => 'field_datepicker_seconds_step',
                                            'validation_pattern' => '/^[0-9]+$/',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_min_time' => array( 'text_input' =>
                                        array(
                                            'title' => esc_html__('Set min time (in format "H:mm")', 'uni-cpo'),
                                            'tip' => '',
                                            'type' => 'text',
                                            'name' => 'field_datepicker_min_time',
                                            'validation_pattern' => '/(([0]?[0-9]|1[0-2]):[0-5][0-9])|((1[3-9]|2[0-3]):[0-5][0-9])/',
                                            'required' => false
                                        )
                                    ),
                                    'field_datepicker_max_time' => array( 'text_input' =>
                                        array(
                                            'title' => esc_html__('Set max time (in format "H:mm")', 'uni-cpo'),
                                            'tip' => '',
                                            'type' => 'text',
                                            'name' => 'field_datepicker_max_time',
                                            'validation_pattern' => '/(([0]?[0-9]|1[0-2]):[0-5][0-9])|((1[3-9]|2[0-3]):[0-5][0-9])/',
                                            'required' => false
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

        $option_meta        = $this->get_post_meta();
        $sElementFieldName  = $this->get_slug();
        $sInputValueEndDate = $sInputValueStartDate = '';
        $aAllowedHtml       = uni_cpo_allowed_html_for_option_titles();
        $sTypeOfPicker      = 'date'; // default value
        $bFieldIsRequired   = false; // default value

        // sets actual type of picker
        if ( !empty($option_meta['_uni_cpo_field_datepicker_select'][0]) ) {
            $sTypeOfPicker = $option_meta['_uni_cpo_field_datepicker_select'][0];
        }

        // defines requireness of the field
        if ( !empty($option_meta['_uni_cpo_field_required_enable'][0]) ) {
            if ( $option_meta['_uni_cpo_field_required_enable'][0] === 'yes' ) {
                $bFieldIsRequired   = true;
            }
        }

        // date format
        $sFormatDate = ( isset($option_meta['_uni_cpo_field_datepicker_format_date'][0]) ) ? esc_attr( $option_meta['_uni_cpo_field_datepicker_format_date'][0] ) : 'DD.MM.YYYY';

        // week end day(s)
        if ( isset($option_meta['_uni_cpo_field_datepicker_day_of_week_end'][0]) ) {
            $days_of_week_end = maybe_unserialize( $option_meta['_uni_cpo_field_datepicker_day_of_week_end'][0] );
            if ( is_array( $days_of_week_end ) ) {
                foreach ( $days_of_week_end as $key => $value ) {
                    if ( ! empty( $value ) ) {
                        $days_of_week_end_filtered[] = $value;
                    }
                }
                $days_of_week_end = $days_of_week_end_filtered;
                $days_of_week_end = '[' . implode(',', $days_of_week_end) . ']';
            } else {
                $days_of_week_end = '[6,7]';
            }
        } else {
            $days_of_week_end = '[6,7]';
        }

        // min/max dates
        $min_date = ( isset( $option_meta['_uni_cpo_field_datepicker_min_date'][0] ) ) ? $option_meta['_uni_cpo_field_datepicker_min_date'][0] : '';
        $max_date = ( isset( $option_meta['_uni_cpo_field_datepicker_max_date'][0] ) ) ? $option_meta['_uni_cpo_field_datepicker_max_date'][0] : '';

        // disabled dates
        if ( isset( $option_meta['_uni_cpo_field_datepicker_disable_spec_dates'][0] ) ) {
            $disabled_dates = $option_meta['_uni_cpo_field_datepicker_disable_spec_dates'][0];
            $array_of_disabled_dates = explode(',', $disabled_dates);
            foreach ( $array_of_disabled_dates as $value ) {
                $array_of_disabled_dates_new[] = "'" . $value . "'";
            }
            $disabled_dates = '[' . implode(',', $array_of_disabled_dates_new ) . ']';
        } else {
            $disabled_dates = '';
        }

        // disabled day(s)
        if ( isset( $option_meta['_uni_cpo_field_datepicker_disable_days_of_week'][0] ) && ! empty( $max_date ) ) {
            $days_of_week_disabled = maybe_unserialize( $option_meta['_uni_cpo_field_datepicker_disable_days_of_week'][0] );
            if ( is_array( $days_of_week_disabled ) ) {
                foreach ( $days_of_week_disabled as $key => $value ) {
                    if ( ! empty( $value ) ) {
                        $days_of_week_disabled_filtered[] = $value;
                    }
                }
                $days_of_week_disabled = $days_of_week_disabled_filtered;
            } else {
                $days_of_week_disabled = array();
            }
        } else {
            $days_of_week_disabled = array();
        }

        // default
        if ( isset($_POST[$sElementFieldName]) && !empty($_POST[$sElementFieldName]) ) {
            $sInputValueEndDate = $_POST[$sElementFieldName];
        }
        if ( isset($_POST[$sElementFieldName.'_start']) && !empty($_POST[$sElementFieldName.'_start']) ) {
            $sInputValueStartDate = $_POST[$sElementFieldName.'_start'];
        }

        // min/max dates
        $min_time = ( isset( $option_meta['_uni_cpo_field_datepicker_min_time'][0] ) ) ? $option_meta['_uni_cpo_field_datepicker_min_time'][0] : '';
        $max_time = ( isset( $option_meta['_uni_cpo_field_datepicker_max_time'][0] ) ) ? $option_meta['_uni_cpo_field_datepicker_max_time'][0] : '';

        // is in 12 hours format?
        $is_time_ampm = ( isset($option_meta['_uni_cpo_field_datepicker_ampm_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_ampm_enable'][0] === 'yes' ) ? true : false;

        $sParsleyPattern = '';
        if ( isset($option_meta['_uni_cpo_field_input_parsley_pattern'][0]) && !empty($option_meta['_uni_cpo_field_input_parsley_pattern'][0]) ) {
            $sParsleyPattern = ' data-parsley-pattern="'.esc_attr($option_meta['_uni_cpo_field_input_parsley_pattern'][0]).'"';
        } else if ( $bFieldIsRequired && $sTypeOfPicker == 'time' && empty($option_meta['_uni_cpo_field_input_parsley_pattern'][0]) ) {
            $sParsleyPattern = ' data-parsley-pattern="(([0]?[0-9]|1[0-2]):[0-5][0-9])|((1[3-9]|2[0-3]):[0-5][0-9])"';
        }

        do_action( 'uni_cpo_before_option', $this );

        echo '<div id="'.esc_attr( $sElementFieldName ).'" data-type="' . esc_attr( $this->get_type() ) . '" class="uni_cpo_fields_container uni_cpo_field_type_'.esc_attr( $this->get_type() ) . ( (!empty($option_meta['_uni_cpo_field_extra_class'][0])) ? ' '.sanitize_html_class( $option_meta['_uni_cpo_field_extra_class'][0] ) : '' ) .'">';

            if ( !empty( $option_meta['_uni_cpo_field_header_text'][0] ) && !empty( $option_meta['_uni_cpo_field_header_type'][0] ) ) {
                echo '<'.esc_attr($option_meta['_uni_cpo_field_header_type'][0]).' class="uni_cpo_fields_header">';
                echo wp_kses( $option_meta['_uni_cpo_field_header_text'][0], $aAllowedHtml ) . ( ( $bFieldIsRequired ) ? ' <span class="uni-cpo-required-label">*</span>' : '' );

                // tooltips
                $this->tooltip_output();

                echo'</'.esc_attr($option_meta['_uni_cpo_field_header_type'][0]).'>';
            }

            if ( $bFieldIsRequired ) {
                if ( in_array( $sTypeOfPicker, array('date', 'time', 'datetime') ) ) {
                    echo '<input id="'.esc_attr( $sElementFieldName ).'-field" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).' js-uni-cpo-field-'.esc_attr( $this->get_type() ).'-single uni-cpo-required" value="'.esc_attr($sInputValueEndDate).'" data-parsley-required="true" data-parsley-trigger="change focusout submit"' . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .' />';
                } else if ( in_array( $sTypeOfPicker, array('period', 'period_date') ) ) {
                    echo '<input id="'.esc_attr( $sElementFieldName ).'-field" name="'.esc_attr( $sElementFieldName ).'_start" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).'-start uni-cpo-required" value="'.esc_attr($sInputValueStartDate).'" data-parsley-required="true" data-parsley-trigger="change focusout submit"' . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .' />';
                    echo '<input id="'.esc_attr( $sElementFieldName ).'-field-end" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).' uni-cpo-required" value="'.esc_attr($sInputValueEndDate).'" data-parsley-required="true" data-parsley-trigger="change focusout submit"' . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .' />';
                    echo '<input id="'.esc_attr( $sElementFieldName ).'-field-duration" type="hidden" name="'.esc_attr( $sElementFieldName ).'_duration" value="" />';
                }
            } else {
                if ( in_array( $sTypeOfPicker, array('date', 'time', 'datetime') ) ) {
                    echo '<input id="'.esc_attr( $sElementFieldName ).'-field" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).' js-uni-cpo-field-'.esc_attr( $this->get_type() ).'-single" value="'.esc_attr($sInputValueEndDate).'"' . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .' />';
                } else if ( in_array( $sTypeOfPicker, array('period', 'period_date') ) ) {
                    echo '<input id="'.esc_attr( $sElementFieldName ).'-field" name="'.esc_attr( $sElementFieldName ).'_start" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).'-start" value="'.esc_attr($sInputValueStartDate).'"' . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .' />';
                    echo '<input id="'.esc_attr( $sElementFieldName ).'-field-end" name="'.esc_attr( $sElementFieldName ).'" class="'.esc_attr( $sElementFieldName ).'-field js-uni-cpo-field-'.esc_attr( $this->get_type() ).'" value="'.esc_attr($sInputValueEndDate).'"' . ( ( !empty($sParsleyPattern) ) ? $sParsleyPattern : '') .' />';
                    echo '<input id="'.esc_attr( $sElementFieldName ).'-field-duration" type="hidden" name="'.esc_attr( $sElementFieldName ).'_duration" value="" />';
                }
            }

            // input hidden for date format
            if ( in_array( $sTypeOfPicker, array('time') ) ) {
                echo '<input id="'.esc_attr( $sElementFieldName ).'-format" type="hidden" value="'.(( isset($option_meta['_uni_cpo_field_datepicker_format_time'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_format_time'][0] : 'H:m').'" />';
            } else if ( in_array( $sTypeOfPicker, array('date', 'period_date') ) ) {
                echo '<input id="'.esc_attr( $sElementFieldName ).'-format" type="hidden" value="'.(( isset($option_meta['_uni_cpo_field_datepicker_format_date'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_format_date'][0] : 'DD.MM.YYYY').'" />';
            } else if ( in_array( $sTypeOfPicker, array('datetime', 'period') ) ) {
                echo '<input id="'.esc_attr( $sElementFieldName ).'-format" type="hidden" value="'.(( isset($option_meta['_uni_cpo_field_datepicker_format_datetime'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_format_datetime'][0] : 'HH:mm DD.MM.YYYY').'" />';
            }
                ?>
    <script>
    jQuery( document ).ready( function( $ ) {
        'use strict';

        <?php if ( in_array( $sTypeOfPicker, array('date', 'datetime', 'period_date', 'period') ) ) { ?>

        var disabled_dates = <?php echo ( ! empty( $disabled_dates ) ) ? $disabled_dates : '[]'; ?>;

        <?php if ( ! empty( $days_of_week_disabled ) ) {
        ?>
        var result = [],
            start = moment(),
            end   = <?php echo $this->get_max_date( $max_date, $sFormatDate, 'object' ); ?>;
        <?php
            $i = 1;
            foreach( $days_of_week_disabled as $day_index ) {
            ?>
        var day<?php echo $i; ?>  = <?php echo ( 7 === intval($day_index) ) ? 0 : intval($day_index); ?>,
            current<?php echo $i; ?> = start.clone();

        while ( current<?php echo $i; ?>.day(day<?php echo $i; ?> + 7).isBefore(end) ) {
            result.push(current<?php echo $i; ?>.clone());
        }
            <?php
            $i++;
            }
        ?>
        result = result.map(m => m.format('<?php echo $sFormatDate; ?>'));
        disabled_dates = uniArrayUnique( disabled_dates.concat( result ) );
        <?php
        }
        ?>
        <?php } ?>

    <?php if ( in_array( $sTypeOfPicker, array('date', 'datetime', 'period', 'period_date') ) ) { ?>

        jQuery("#<?php echo esc_attr( $sElementFieldName ) ?>-field").periodpicker({

        <?php if ( $sTypeOfPicker === 'date' ) { ?>

            lang: unicpo.locale,
            inline: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_inline_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_inline_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
            clearButtonInButton: true,
            dayOfWeekStart: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_day_of_week_start'][0]) ) ? esc_attr( $option_meta['_uni_cpo_field_datepicker_day_of_week_start'][0] ) : '1'; ?>,
            weekEnds: <?php echo $days_of_week_end; ?>,
            hideAfterSelect: true,
            withoutBottomPanel: true,
            yearsLine: false,
            norange: true,
            cells: [1, <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_cells_no'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_cells_no'][0] : '1'; ?>],
            resizeButton: false,
            fullsizeButton: false,
            fullsizeOnDblClick: false,
            timepicker: false,
            startMonth: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_start_month'][0]) && '0' !== $option_meta['_uni_cpo_field_datepicker_start_month'][0] ) ? $option_meta['_uni_cpo_field_datepicker_start_month'][0] : date('n'); ?>,
            formatDate: '<?php echo $sFormatDate; ?>',
            formatDecoreDate: '<?php echo $sFormatDate; ?>',
            minDate: <?php echo $this->get_min_date( $min_date, $sFormatDate ); ?>,
            disableDays: disabled_dates,
            maxDate: <?php echo $this->get_max_date( $max_date, $sFormatDate ); ?>,

        <?php } else if ( $sTypeOfPicker === 'datetime' ) { ?>

            lang: unicpo.locale,
            inline: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_inline_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_inline_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
            clearButtonInButton: true,
            dayOfWeekStart: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_day_of_week_start'][0]) ) ? esc_attr( $option_meta['_uni_cpo_field_datepicker_day_of_week_start'][0] ) : '1'; ?>,
            weekEnds: <?php echo $days_of_week_end; ?>,
            withoutBottomPanel: true,
            yearsLine: false,
            norange: true,
            cells: [1, <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_cells_no'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_cells_no'][0] : '1'; ?>],
            resizeButton: false,
            fullsizeButton: false,
            fullsizeOnDblClick: false,
            formatDate: 'DD.MM.YYYY',
            formatDateTime: '<?php $sFormatDateTime = ( isset($option_meta['_uni_cpo_field_datepicker_format_datetime'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_format_datetime'][0] : 'HH:mm DD.MM.YYYY'; echo $sFormatDateTime; ?>',
            formatDecoreDateTime: '<?php echo $sFormatDateTime; ?>',
            timepicker: true,
            startMonth: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_start_month'][0]) && '0' !== $option_meta['_uni_cpo_field_datepicker_start_month'][0] ) ? $option_meta['_uni_cpo_field_datepicker_start_month'][0] : date('n'); ?>,
            timepickerOptions: {
        		hours: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_hours_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_hours_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
        		minutes: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_minutes_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_minutes_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
        		seconds: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_seconds_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_seconds_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
                twelveHoursFormat: <?php echo ( $is_time_ampm ) ? 'true' : 'false'; ?>,
        		ampm: <?php echo ( $is_time_ampm ) ? 'true' : 'false'; ?>,
                steps:[<?php echo ( isset($option_meta['_uni_cpo_field_datepicker_hours_step'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_hours_step'][0] : '1'; ?>,<?php echo ( isset($option_meta['_uni_cpo_field_datepicker_minutes_step'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_minutes_step'][0] : '1'; ?>,<?php echo ( isset($option_meta['_uni_cpo_field_datepicker_seconds_step'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_seconds_step'][0] : '1'; ?>,1]
        	},
            minDate: <?php echo $this->get_min_date( $min_date, 'DD.MM.YYYY' ); ?>,
            disableDays: disabled_dates,
            maxDate: <?php echo $this->get_max_date( $max_date, 'DD.MM.YYYY' ); ?>,

        <?php } else if ( $sTypeOfPicker === 'period' ) { ?>

            lang: unicpo.locale,
            inline: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_inline_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_inline_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
            clearButtonInButton: true,
            dayOfWeekStart: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_day_of_week_start'][0]) ) ? esc_attr( $option_meta['_uni_cpo_field_datepicker_day_of_week_start'][0] ) : '1'; ?>,
            weekEnds: <?php echo $days_of_week_end; ?>,
            withoutBottomPanel: true,
            yearsLine: false,
            norange: false,
            end: "#<?php echo esc_attr( $sElementFieldName ) ?>-field-end",
            cells: [1, <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_cells_no'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_cells_no'][0] : '1'; ?>],
            resizeButton: false,
            fullsizeButton: false,
            fullsizeOnDblClick: false,
            formatDate: 'DD.MM.YYYY',
            formatDateTime: '<?php $sFormatDateTime = ( isset($option_meta['_uni_cpo_field_datepicker_format_datetime'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_format_datetime'][0] : 'HH:mm DD.MM.YYYY'; echo $sFormatDateTime; ?>',
            formatDecoreDateTime: '<?php echo $sFormatDateTime; ?>',
            timepicker: true,
            startMonth: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_start_month'][0]) && '0' !== $option_meta['_uni_cpo_field_datepicker_start_month'][0] ) ? $option_meta['_uni_cpo_field_datepicker_start_month'][0] : date('n'); ?>,
            timepickerOptions: {
        		hours: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_hours_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_hours_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
        		minutes: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_minutes_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_minutes_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
        		seconds: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_seconds_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_seconds_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
                twelveHoursFormat: <?php echo ( $is_time_ampm ) ? 'true' : 'false'; ?>,
        		ampm: <?php echo ( $is_time_ampm ) ? 'true' : 'false'; ?>,
                steps:[<?php echo ( isset($option_meta['_uni_cpo_field_datepicker_hours_step'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_hours_step'][0] : '1'; ?>,<?php echo ( isset($option_meta['_uni_cpo_field_datepicker_minutes_step'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_minutes_step'][0] : '1'; ?>,<?php echo ( isset($option_meta['_uni_cpo_field_datepicker_seconds_step'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_seconds_step'][0] : '1'; ?>,1]
        	},
            minDate: <?php echo $this->get_min_date( $min_date, 'DD.MM.YYYY' ); ?>,
            disableDays: disabled_dates,
            maxDate: <?php echo $this->get_max_date( $max_date, 'DD.MM.YYYY' ); ?>

        <?php } else if ( $sTypeOfPicker === 'period_date' ) { ?>

            lang: unicpo.locale,
            inline: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_inline_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_inline_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
            clearButtonInButton: true,
            dayOfWeekStart: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_day_of_week_start'][0]) ) ? esc_attr( $option_meta['_uni_cpo_field_datepicker_day_of_week_start'][0] ) : '1'; ?>,
            weekEnds: <?php echo $days_of_week_end; ?>,
            withoutBottomPanel: true,
            yearsLine: false,
            norange: false,
            end: "#<?php echo esc_attr( $sElementFieldName ) ?>-field-end",
            cells: [1, <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_cells_no'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_cells_no'][0] : '1'; ?>],
            resizeButton: false,
            fullsizeButton: false,
            fullsizeOnDblClick: false,
            formatDate: '<?php echo $sFormatDate; ?>',
            formatDecoreDate: '<?php echo $sFormatDate; ?>',
            minDate: <?php echo $this->get_min_date( $min_date, $sFormatDate ); ?>,
            disableDays: disabled_dates,
            maxDate: <?php echo $this->get_max_date( $max_date, $sFormatDate ); ?>,
            startMonth: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_start_month'][0]) && '0' !== $option_meta['_uni_cpo_field_datepicker_start_month'][0] ) ? $option_meta['_uni_cpo_field_datepicker_start_month'][0] : date('n'); ?>,

        <?php } ?>

        });

        <?php if ( !empty($sInputValueEndDate) && empty($sInputValueStartDate) ) { ?>
        jQuery('#<?php echo esc_attr( $sElementFieldName ) ?>-field').periodpicker('value', '<?php echo $sInputValueEndDate; ?>');
        <?php } else if ( !empty($sInputValueEndDate) && !empty($sInputValueStartDate) ) { ?>
        jQuery('#<?php echo esc_attr( $sElementFieldName ) ?>-field').periodpicker('value', ['<?php echo $sInputValueStartDate; ?>', '<?php echo $sInputValueEndDate; ?>']);
        <?php } ?>

    <?php } else if ( $sTypeOfPicker === 'time' ) { ?>

        jQuery("#<?php echo esc_attr( $sElementFieldName ) ?>-field").timepickeralone({

            lang: unicpo.locale,
            withoutBottomPanel: true,
            inline: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_inline_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_inline_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
            inputFormat: '<?php echo ( isset($option_meta['_uni_cpo_field_datepicker_format_time'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_format_time'][0] : 'H:mm'; ?>',
            timepicker: true,
        	hours: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_hours_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_hours_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
        	minutes: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_minutes_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_minutes_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
        	seconds: <?php echo ( isset($option_meta['_uni_cpo_field_datepicker_seconds_enable'][0]) && $option_meta['_uni_cpo_field_datepicker_seconds_enable'][0] === 'yes' ) ? 'true' : 'false'; ?>,
            twelveHoursFormat: <?php echo ( $is_time_ampm ) ? 'true' : 'false'; ?>,
        	ampm: <?php echo ( $is_time_ampm ) ? 'true' : 'false'; ?>,
            steps:[<?php echo ( isset($option_meta['_uni_cpo_field_datepicker_hours_step'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_hours_step'][0] : '1'; ?>,<?php echo ( isset($option_meta['_uni_cpo_field_datepicker_minutes_step'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_minutes_step'][0] : '1'; ?>,<?php echo ( isset($option_meta['_uni_cpo_field_datepicker_seconds_step'][0]) ) ? $option_meta['_uni_cpo_field_datepicker_seconds_step'][0] : '1'; ?>,1]

        });

        <?php if ( ! empty( $sInputValueEndDate ) ) { ?>
        jQuery("#<?php echo esc_attr( $sElementFieldName ) ?>-field").TimePickerAlone('setValue', '<?php echo $sInputValueEndDate; ?>');
        <?php } ?>

        <?php if ( ! empty( $min_time ) ) { ?>
        jQuery("#<?php echo esc_attr( $sElementFieldName ) ?>-field").TimePickerAlone('setMin', '<?php echo $min_time; ?>');
        <?php } ?>
        <?php if ( ! empty( $max_time ) ) { ?>
        jQuery("#<?php echo esc_attr( $sElementFieldName ) ?>-field").TimePickerAlone('setMax', '<?php echo $max_time; ?>');
        <?php } ?>

    <?php } ?>

        $( document.body ).on("change", "[name=<?php echo esc_attr( $sElementFieldName ) ?>]", function(){
            if ( unicpo.calc_on == true && unicpo.calc_btn_on == false ) {
                uni_cpo_form_processing();
            } else if ( unicpo.calc_on == true && unicpo.calc_btn_on == true ) {
                jQuery('.single_add_to_cart_button').prop('disabled', true);
            }
        });

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
     *
     *
     */
    protected function get_min_date( $min_date, $format ) {
        if ( false !== strpos($min_date, '+') ) {
            $days_count = intval( ltrim( $min_date, '+' ) );
            $min_date = 'moment().add(' . $days_count .', "d").format("' . $format . '")';
        } else if ( '0' === $min_date ) {
            $min_date = 'moment().format("' . $format . '")';
        } else if ( ! empty( $min_date ) ) {
            $min_date = 'moment("'.$min_date.'", "' . $format . '").format("' . $format . '")';
        } else {
            $min_date = 'false';
        }
        return $min_date;
    }

    /**
     *
     *
     */
    protected function get_max_date( $max_date, $format, $return_type = 'string' ) {
        if ( 'string' === $return_type ) {
            if ( false !== strpos($max_date, '+') ) {
                $days_count = intval( ltrim( $max_date, '+' ) );
                $max_date = 'moment().add(' . $days_count .', "d").format("' . $format . '")';
            } else if ( '0' === $max_date ) {
                $max_date = 'moment().format("' . $format . '")';
            } else if ( ! empty( $max_date ) ) {
                $max_date = 'moment("'.$max_date.'", "' . $format . '").add(1, "d").format("' . $format . '")';
            } else {
                $max_date = 'false';
            }
        } else if ( 'object' === $return_type ) {
            if ( false !== strpos($max_date, '+') ) {
                $days_count = intval( ltrim( $max_date, '+' ) );
                $max_date = 'moment().add(' . $days_count .', "d")';
            } else if ( '0' === $max_date ) {
                $max_date = 'moment()';
            } else if ( ! empty( $max_date ) ) {
                $max_date = 'moment("'.$max_date.'", "' . $format . '").add(1, "d")';
            }
        }
        return $max_date;
    }

    /**
     * gets form field(s) for option for add/edit options modal on order edit page
     *
     */
    public function get_order_form_field( $posted_data = array() ) {
        $option_meta    = $this->get_post_meta();

        $picker_type = 'date';
        // sets actual type of picker
        if ( ! empty( $option_meta['_uni_cpo_field_datepicker_select'][0] ) ) {
            $picker_type = $option_meta['_uni_cpo_field_datepicker_select'][0];
        }
        $output = '<tr>';

        $output .= '<th><label>' . esc_html( $this->get_label() ) . ' <i>(' . esc_html( $this->get_slug() ) . ')</i></label></th>';
        $output .= '<td><input type="text" name="' . esc_attr( $this->get_slug() ) . '" value="' . ( isset( $posted_data[$this->get_slug()] ) ? $posted_data[$this->get_slug()] : '' ) . '" /></td>';

        $output .= '</tr>';

        if ( ! empty( $this->get_special_vars() ) && in_array( $picker_type, array('period_date', 'period') ) ) {
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
        $option_meta  = $this->get_post_meta();
        if ( 'date_picker' === $this->option_type && isset( $option_meta['_uni_cpo_field_datepicker_select'][0] ) &&
            in_array( $option_meta['_uni_cpo_field_datepicker_select'][0], array( 'period', 'period_date' ) )
        ) {
            return array('duration');
        } else {
            return array();
        }
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

        if ( $bFull ) {

            $sPostName = trim($this->post->post_title, '{');
            $sPostName = trim($sPostName, '}');
            $aFilterArray[] = array(
                'id' => $this->post->post_name.'_duration',
                'label' => '{'.$sPostName.'_duration}',
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

        if ( isset($aFormPostData[$sElementFieldName]) && !empty($aFormPostData[$sElementFieldName]) ) {
            // it is a period picker
            if ( isset($aFormPostData[$sElementFieldName.'_duration']) && !empty($aFormPostData[$sElementFieldName.'_duration']) ) {
                // joins two start and end dates
                if ( isset($aFormPostData[$sElementFieldName.'_start']) && !empty($aFormPostData[$sElementFieldName.'_start']) ) {
                    $aFormPostData[$sElementFieldName] = $aFormPostData[$sElementFieldName.'_start'] . ' - ' . $aFormPostData[$sElementFieldName];
                }
                if ( !$bCartMeta ) {
                    if ( get_post_meta( $this->get_id(), '_uni_cpo_field_option_price', true ) ) {
                        // return price of the field - it acts like a single option with its own price
                        return array(
                                $sElementFieldName => floatval( get_post_meta( $this->get_id(), '_uni_cpo_field_option_price', true ) ),
                                $sElementFieldName.'_duration' => intval( $aFormPostData[$sElementFieldName.'_duration'] ),
                                );
                    } else {
                        // return field value
                        return array(
                                $sElementFieldName => '0',
                                $sElementFieldName.'_duration' => intval( $aFormPostData[$sElementFieldName.'_duration'] ),
                                );
                    }
                } else if ( $bCartMeta == 'cart' ) {
                    return array(
                            $sElementFieldName => $aFormPostData[$sElementFieldName],
                            $sElementFieldName.'_duration' => intval( $aFormPostData[$sElementFieldName.'_duration'] ),
                            );
                } else {
                    return $aFormPostData[$sElementFieldName];
                }
            } else {
                if ( !$bCartMeta ) {
                    if ( get_post_meta( $this->get_id(), '_uni_cpo_field_option_price', true ) ) {
                        // return price of the field - it acts like a single option with its own price
                        return floatval( get_post_meta( $this->get_id(), '_uni_cpo_field_option_price', true ) );
                    } else {
                        // return field value
                        return '0';
                    }
                } else {
                    return $aFormPostData[$sElementFieldName];
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