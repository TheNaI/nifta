<?php

class The7_Less_Compiler {

	protected $lessc;

	public function __construct( $less_vars = array() ) {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! $wp_filesystem && ! WP_Filesystem() ) {
			throw new Exception( __( 'Cannot access file system.', 'the7mk2' ) );
		}

		if ( ! class_exists( 'lessc' ) ) {
			require PRESSCORE_DIR . '/vendor/lessphp/lessc.inc.php';
		}

		$this->lessc = new lessc();

		// Register custom less functions.
		DT_LessPHP_Functions::register_functions( $this->lessc );

		$this->lessc->setImportDir( array( PRESSCORE_THEME_DIR . '/css', PRESSCORE_THEME_DIR . '/css/dynamic-less' ) );
		$this->lessc->setVariables( $less_vars );
	}

	public function compile_to_file( $source_file, $output_file ) {
		global $wp_filesystem;

		$css = $this->compile_file( $source_file );
		$css = $this->make_relative_urls( $css, $source_file, $output_file );
		wp_mkdir_p( dirname( $output_file ) );
		$wp_filesystem->put_contents( $output_file, $css );
	}

	public function compile_file( $less_file ) {
		global $wp_filesystem;

		return $this->lessc->compile( $wp_filesystem->get_contents( $less_file ) );
	}

	public function is_writable( $abspath ) {
		global $wp_filesystem;

		return $wp_filesystem->is_writable( $abspath );
	}

	protected function make_relative_urls( $css, $source_file, $output_file ) {
		$url_filter = new The7_Relative_Url_Filter( $source_file, $output_file );
		$css = preg_replace_callback( '#url\s*\((?P<quote>[\'"]{0,1})(?P<url>[^\'"\)]+)\1\)#siU', array( $url_filter, 'filter' ), $css );

		return $css;
	}
}

class The7_Relative_Url_Filter {

	protected $path_from_uploads_to_wp_root;
	protected $path_from_wp_root_to_theme;

	public function __construct( $source_file, $output_file ) {
		$content_relative_path = str_replace( ABSPATH, '', dirname( $output_file ) );
		$this->path_from_uploads_to_wp_root = implode( '/', array_fill( 0, count( explode( '/', $content_relative_path ) ), '..' ) );
		$this->path_from_wp_root_to_theme = str_replace( ABSPATH, '', dirname( $source_file ) );
	}

	public function filter( $matches ) {
		if ( preg_match( '#^(http|@|data:|/)#Ui', $matches[2] ) ) {
			return str_replace( site_url(), $this->path_from_uploads_to_wp_root, $matches[0] );
		}

		return sprintf( 'url(%s%s%1$s)', $matches[1], "{$this->path_from_uploads_to_wp_root}/{$this->path_from_wp_root_to_theme}/{$matches[2]}" );
	}
}