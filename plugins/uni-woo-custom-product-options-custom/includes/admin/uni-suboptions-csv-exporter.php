<?php
/**
 * Handles suboptions CSV export.
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'WC_CSV_Batch_Exporter', false ) ) {
	include_once( WC_ABSPATH . 'includes/export/abstract-wc-csv-batch-exporter.php' );
}

/**
 * Uni_Suboptions_CSV_Exporter Class.
 */
class Uni_Suboptions_CSV_Exporter extends WC_CSV_Batch_Exporter {

	/**
	 * Type of export used in filter names.
	 * @var string
	 */
	protected $export_type = 'suboption';

	/**
	 * ID of an option
	 * @var integer
	 */
	protected $option_id = 0;

	/**
	 * Field name
	 * @var string
	 */
	protected $field_name = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Return an array of columns to export.
	 *
	 * @return array
	 */
	public function get_default_column_names() {
		return apply_filters( "uni_cpo_export_{$this->export_type}_default_columns", array(
			'default'             	=> __( 'Default?', 'uni-cpo' ),
			'label'               	=> __( 'Label', 'uni-cpo' ),
			'slug'                	=> __( 'Slug', 'uni-cpo' ),
			'price'               	=> __( 'Price/Rate', 'uni-cpo' ),
			'tooltip'				=> __( 'Tooltip', 'uni-cpo' ),
			'image_id'          	=> __( 'Image', 'uni-cpo' ),
			'color'					=> __( 'Color', 'uni-cpo' ),
			'file_id'				=> __( 'File', 'uni-cpo' )
		) );
	}

	/**
	 * Return an array of supported column names and ids.
	 *
	 * @since 3.1.0
	 * @return array
	 */
	public function get_column_names() {
		return apply_filters( "uni_cpo_export_{$this->export_type}_column_names", $this->column_names, $this );
	}

	/**
	 * Prepare data for export.
	 *
	 */
	public function prepare_data_to_export() {
		$columns  = $this->get_column_names();
		$option_id = $this->option_id;
		$field_name = $this->field_name;
    	$suboptions_serialized = ( get_post_meta( $option_id, '_uni_cpo_' . $field_name , true ) ) ? get_post_meta( $option_id, '_uni_cpo_' . $field_name, true ) : '';
    	$suboptions = maybe_unserialize( $suboptions_serialized );
		$this->total_rows = count( $suboptions );
		$this->row_data   = array();
	
		foreach ( $suboptions as $suboption ) {
			$row = array();

			foreach ( $columns as $column_id => $column_name ) {

				$column_id = strstr( $column_id, ':' ) ? current( explode( ':', $column_id ) ) : $column_id;
				$value     = '';

				if ( ! isset( $suboption[$column_id] ) ) {
					continue;
				}

				// Filter for 3rd parties.
				if ( has_filter( "uni_cpo_export_{$this->export_type}_column_{$column_id}" ) ) {
					$value = apply_filters( "uni_cpo_export_{$this->export_type}_column_{$column_id}", $suboption[$column_id], $option_id );

				// Handle special columns which don't map 1:1 to suboption data.
				} elseif ( is_callable( array( $this, "get_column_value_{$column_id}" ) ) ) {
					$value = $this->{"get_column_value_{$column_id}"}( $suboption[$column_id], $option_id );

				} else {
					$value = $suboption[$column_id];
				}
				
				$row[ $column_id ] = $value;
			}

			$this->row_data[] = apply_filters( 'uni_cpo_export_{$this->export_type}_row_data', $row, $option_id );
		}

	}

	/**
	 * Sets Option ID
	 *
	 */
	public function set_option_id( $option_id ) {
		$this->option_id = absint( $option_id );
	}

	/**
	 * Sets field_name
	 *
	 */
	public function set_field_name( $name ) {
		$this->field_name = wc_clean( $name );
	}


	/**
	 * Generate and return a filename.
	 *
	 * @return string
	 */
	public function get_filename() {
		return sanitize_file_name( 'uni-' . $this->export_type . '-export-' . date_i18n( 'Y-m-d', current_time( 'timestamp' ) ) . '.csv' );
	}

	/**
	 * Get default value.
	 *
	 * @return int
	 */
	protected function get_column_value_default( $value ) {
		return isset( $value ) ? $value : '';
	}

	/**
	 * Get image value.
	 *
	 * @return string
	 */
	protected function get_column_value_image_id( $value ) {
		$image_url = '';
		$image  = wp_get_attachment_image_src( $value, 'full' );

		if ( $image ) {
			$image_url = $image[0];
		}

		return $image_url;
	}

	/**
	 * Get total % complete.
	 *
	 * @return int
	 */
	public function get_percent_complete() {
		return 100;
	}

}