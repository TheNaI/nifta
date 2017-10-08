<?php
/**
 * Suboptions CSV importer
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'WC_Product_Importer', false ) ) {
	include_once( WC_ABSPATH . 'includes/import/abstract-wc-product-importer.php' );
}

/**
 * Uni_Suboptions_CSV_Importer Class.
 */
class Uni_Suboptions_CSV_Importer extends WC_Product_Importer {

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
	 * Initialize importer.
	 *
	 * @param string $file File to read.
	 * @param array  $args Arguments for the parser.
	 */
	public function __construct( $file, $params = array() ) {
		$default_args = array(
			'start_pos'        => 0, // File pointer start.
			'end_pos'          => -1, // File pointer end.
			'lines'            => -1, // Max lines to read.
			'mapping'          => array(), // Column mapping. csv_heading => schema_heading.
			'parse'            => false, // Whether to sanitize and format data.
			'update_existing'  => false, // Whether to update existing items.
			'delimiter'        => ',', // CSV delimiter.
			'prevent_timeouts' => true, // Check memory and time usage and abort if reaching limit.
		);

		$this->params = wp_parse_args( $params, $default_args );
		$this->file   = $file;
		$this->def_cols_keys = array(
            'default',
            'label',
            'slug',
            'price',
            'tooltip',
            'image_id',
            'color',
            'file_id'
        );

		$this->read_file();
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
	 * Read file.
	 *
	 * @return array
	 */
	protected function read_file() {
		if ( false !== ( $handle = fopen( $this->file, 'r' ) ) ) {
			$this->raw_keys = fgetcsv( $handle, 0, $this->params['delimiter'] );

			// Remove BOM signature from the first item.
			if ( isset( $this->raw_keys[0] ) ) {
				$this->raw_keys[0] = $this->remove_utf8_bom( $this->raw_keys[0] );
			}

			if ( 0 !== $this->params['start_pos'] ) {
				fseek( $handle, (int) $this->params['start_pos'] );
			}

			while ( false !== ( $row = fgetcsv( $handle, 0, $this->params['delimiter'] ) ) ) {
				$this->raw_data[]                                 = $row;
				$this->file_positions[ count( $this->raw_data ) ] = ftell( $handle );

				if ( ( $this->params['end_pos'] > 0 && ftell( $handle ) >= $this->params['end_pos'] ) || 0 === --$this->params['lines'] ) {
					break;
				}
			}

			$this->file_position = ftell( $handle );
		}

		//if ( ! empty( $this->params['mapping'] ) ) {
			$this->set_mapped_keys();
		//}

		if ( $this->params['parse'] ) {
			$this->set_parsed_data();
		}
	}

	/**
	 * Remove UTF-8 BOM signature.
	 *
	 * @param  string $string String to handle.
	 * @return string
	 */
	protected function remove_utf8_bom( $string ) {
		if ( 'efbbbf' === substr( bin2hex( $string ), 0, 6 ) ) {
			$string = substr( $string, 3 );
		}

		return $string;
	}

	/**
	 * Set file mapped keys.
	 *
	 * @return array
	 */
	protected function set_mapped_keys() {
		/*mapping = $this->params['mapping'];

		foreach ( $this->raw_keys as $key ) {
			$this->mapped_keys[] = isset( $mapping[ $key ] ) ? $mapping[ $key ] : $key;
		}*/
		$i = 0;
		foreach ( $this->raw_keys as $key ) {
			$this->mapped_keys[] = isset( $this->def_cols_keys[$i] ) ? $this->def_cols_keys[$i] : $key;
			$i++;
		}
	}

	/**
	 * Parse a field that is generally '1' or '0' but can be something else.
	 *
	 * @param string $field Field value.
	 * @return bool|string
	 */
	public function parse_def_field( $field ) {
		return isset( $field ) ? wc_clean( $field ) : '';
	}

	/**
	 * Parse a float value field.
	 *
	 * @param string $field Field value.
	 * @return float|string
	 */
	public function parse_float_field( $field ) {
		if ( '' === $field ) {
			return $field;
		}

		return floatval( $field );
	}

	/**
	 * Parse images list from a CSV.
	 *
	 * @param  string $field Field value.
	 * @return array
	 */
	public function parse_attachment_field( $field ) {
		if ( '' === $field ) {
			return $field;
		}

		$attach_id = uni_cpo_get_attachment_id( $field );
		return $attach_id;
	}


	/**
	 * Get formatting callback.
	 *
	 * @return array
	 */
	protected function get_formating_callback() {

		/**
		 * Columns not mentioned here will get parsed with 'wc_clean'.
		 * column_name => callback.
		 */
		$data_formatting = array(
			'default' => array( $this, 'parse_def_field' ),
			'label' => 'wp_filter_post_kses',
			'slug' => 'wp_filter_post_kses',
			'price' => array( $this, 'parse_float_field' ),
			'tooltip' => 'wp_filter_post_kses',
			'image_id' => array( $this, 'parse_attachment_field' ),
			'color' => 'wp_filter_post_kses',
			'file_id' => array( $this, 'parse_attachment_field' )
		);

		$callbacks = array();

		// Figure out the parse function for each column.
		foreach ( $this->get_mapped_keys() as $index => $heading ) {
			$callback = 'wc_clean';

			if ( isset( $data_formatting[ $heading ] ) ) {
				$callback = $data_formatting[ $heading ];
			}

			$callbacks[] = $callback;
		}
		
		return apply_filters( 'uni_cpo_suboptions_importer_formatting_callbacks', $callbacks, $this );
	}

	/**
	 * Check if strings starts with determined word.
	 *
	 * @param  string $haystack Complete sentence.
	 * @param  string $needle   Excerpt.
	 * @return bool
	 */
	protected function starts_with( $haystack, $needle ) {
		return substr( $haystack, 0, strlen( $needle ) ) === $needle;
	}

	/**
	 * Map and format raw data to known fields.
	 *
	 * @return array
	 */
	protected function set_parsed_data() {
		$parse_functions = $this->get_formating_callback();
		$mapped_keys     = $this->get_mapped_keys();

		// Parse the data.
		foreach ( $this->raw_data as $row ) {
			// Skip empty rows.
			if ( ! count( array_filter( $row ) ) ) {
				continue;
			}
			$data = array();

			do_action( 'woocommerce_product_importer_before_set_parsed_data', $row, $mapped_keys );

			foreach ( $row as $id => $value ) {
				// Skip ignored columns.
				if ( empty( $mapped_keys[ $id ] ) ) {
					continue;
				}

				$data[ $mapped_keys[ $id ] ] = call_user_func( $parse_functions[ $id ], $value );
			}

			$this->parsed_data[] = apply_filters( 'woocommerce_product_importer_parsed_data', $data, $this );
		}
	}

	/**
	 * Percentage of completed
	 *
	 * @return int
	 */
	public function get_complete_percentage( $total ) {
		$size = absint( $this->parsed_data );
		return absint( min( round( ( $total / $size ) * 100 ), 100 ) );
	}

	/**
	 * Process importer.
	 *
	 * Do not import products with IDs or SKUs that already exist if option
	 * update existing is false, and likewise, if updating products, do not
	 * process rows which do not exist if an ID/SKU is provided.
	 *
	 * @return array
	 */
	public function import() {
		if( ! empty( $this->parsed_data ) ) {
			$option_id = $this->option_id;
			$field_name = $this->field_name;
			delete_post_meta( $option_id, '_uni_cpo_' . $field_name );
			add_post_meta( $option_id, '_uni_cpo_' . $field_name, $this->parsed_data );
			return 100;
		}
	}
}
