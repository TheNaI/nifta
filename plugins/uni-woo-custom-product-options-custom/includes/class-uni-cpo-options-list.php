<?php
/*
* Class Uni_Cpo_Option_Sets_List
*
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Uni_Cpo_Option_Sets_List extends WP_List_Table {

    public function __construct() {
        parent::__construct( array(
                'singular'  => esc_html__( 'Options Set', 'uni-cpo' ),
                'plural'    => esc_html__( 'Options Sets', 'uni-cpo' ),
                'ajax'      => false,
                'screen'    => 'uni-cpo-options-list'
                )
        );
    }

    //
    function get_columns(){
        $columns = array(
            'cb'                        => '<input type="checkbox" />',
            'title'                     => esc_html__( 'Title', 'uni-cpo' ),
            'date'                      => esc_html__( 'Date', 'uni-cpo' ),
        );
         return $columns;
    }

	/**
	 * Column cb.
	 *
	 * @param  WP_Post $post
	 * @return string
	 */
	public function column_cb( $post ) {
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $post->ID );
	}

    //
    public function no_items() {
        esc_html_e( 'No Options Sets.', 'uni-cpo' );
    }

	/**
	 * Return title column.
	 * @param  object $webhook
	 * @return string
	 */
	public function column_title( $post ) {

		$sOptionSetAdminRawUri      = admin_url( 'admin.php?page=uni-cpo-options-list&amp;options-set-id=' . $post->ID );
        // TODO
        $sOptionSetAdminEditUri     = add_query_arg( array('action' => 'cpo-edit'), $sOptionSetAdminRawUri );
        $sOptionSetAdminDuplicateUri = add_query_arg( array('action' => 'cpo-duplicate'), $sOptionSetAdminRawUri );
        $sOptionSetAdminDeleteUri   = add_query_arg( array('action' => 'cpo-delete'), $sOptionSetAdminRawUri );

		$title                      = get_the_title( $post );
        $post_status                = $post->post_status;
        $can_edit_post              = current_user_can( 'edit_post', $post->ID );

		// Title
		$output = '<strong>';
		if ( 'trash' == $post_status ) {
			$output .= esc_html( $title );
		} else {
            $output .= esc_html( $title );
		}
		$output .= '</strong>';

		get_inline_data( $post );

        $actions['inline'] = '<a class="editinline" href="' . esc_url( $sOptionSetAdminEditUri ) . '">' . esc_html__( 'Quick Edit', 'uni-cpo' ) . '</a>';

        $actions['cpo-duplicate'] = '<a href="' . esc_url( $sOptionSetAdminDuplicateUri ) . '">' . esc_html__( 'Duplicate', 'uni-cpo' ) . '</a>';

		$row_actions = array();

		foreach ( $actions as $action => $link ) {
			$row_actions[] = '<span class="' . esc_attr( $action ) . '">' . $link . '</span>';
		}

		$output .= '<div class="row-actions">' . implode(  ' | ', $row_actions ) . '</div>';

		echo $output;
	}

	/**
	 * Handles the post date column output.
	 */
	public function column_date( $post ) {
		global $mode;

		if ( '0000-00-00 00:00:00' === $post->post_date ) {
			$t_time = $h_time = __( 'Unpublished' );
			$time_diff = 0;
		} else {
			$t_time = get_the_time( __( 'Y/m/d g:i:s a' ) );
			$m_time = $post->post_date;
			$time = get_post_time( 'G', true, $post );

			$time_diff = time() - $time;

			if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
				$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
			} else {
				$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
			}
		}

		if ( 'publish' === $post->post_status ) {
			_e( 'Published' );
		} elseif ( 'future' === $post->post_status ) {
			if ( $time_diff > 0 ) {
				echo '<strong class="error-message">' . __( 'Missed schedule' ) . '</strong>';
			} else {
				_e( 'Scheduled' );
			}
		} else {
			_e( 'Last Modified' );
		}
		echo '<br />';
		if ( 'excerpt' === $mode ) {
			echo apply_filters( 'post_date_column_time', $t_time, $post, 'date', $mode );
		} else {
			echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post, 'date', $mode ) . '</abbr>';
		}
	}

	/**
	 * Get bulk actions.
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		if ( isset( $_GET['status'] ) && 'trash' == $_GET['status'] ) {
			return array(
				'untrash' => esc_html__( 'Restore', 'uni-cpo' ),
				'delete'  => esc_html__( 'Delete Permanently', 'uni-cpo' )
			);
		}

		return array(
			//'trash' => esc_html__( 'Move to Trash', 'uni-cpo' )
		);
	}

	/**
	 * Prepare table list items.
	 */
	public function prepare_items() {
		$per_page = 10;
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		// Column headers
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$current_page = $this->get_pagenum();

		// Query args
		$args = array(
			'post_type'             => 'uni_cpo_option',
            'post_parent'           => 0,
			'posts_per_page'        => $per_page,
			'ignore_sticky_posts'   => true,
			'paged'                 => $current_page
		);

		// Handle the status query
		if ( ! empty( $_REQUEST['status'] ) ) {
			$args['post_status'] = sanitize_text_field( $_REQUEST['status'] );
		}

		if ( ! empty( $_REQUEST['s'] ) ) {
			$args['s'] = sanitize_text_field( $_REQUEST['s'] );
		}

		// Get the options sets
		$options_sets    = new WP_Query( $args );
		$this->items = $options_sets->posts;

		// Set the pagination
		$this->set_pagination_args( array(
			'total_items' => $options_sets->found_posts,
			'per_page'    => $per_page,
			'total_pages' => $options_sets->max_num_pages
		) );
	}

	/**
	 * Display the table
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display() {
		$singular = $this->_args['singular'];

		$this->display_tablenav( 'top' );

		$this->screen->render_screen_reader_content( 'heading_list' );
        ?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
        	<thead>
        	<tr>
        		<?php $this->print_column_headers(); ?>
        	</tr>
        	</thead>

        	<tbody id="the-list"<?php
        		if ( $singular ) {
        			echo " data-wp-lists='list:$singular'";
        		} ?>>
        		<?php $this->display_rows_or_placeholder(); ?>
        	</tbody>

        	<tfoot>
        	<tr>
        		<?php $this->print_column_headers( false ); ?>
        	</tr>
        	</tfoot>

        </table>
        <?php
		$this->display_tablenav( 'bottom' );

        $this->inline_edit();
	}

	/**
	 * Generate the tbody element for the list table.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display_rows_or_placeholder() {
		if ( $this->has_items() ) {
			$this->display_rows( $this->items );
		} else {
			echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
			$this->no_items();
			echo '</td></tr>';
		}
	}

	/**
	 * display_rows
	 */
	public function display_rows( $posts = array(), $level = 0 ) {
		global $wp_query, $per_page;

		if ( empty( $posts ) )
			$posts = $wp_query->posts;

		add_filter( 'the_title', 'esc_html' );

		$this->_display_rows( $posts, $level );

	}

	/**
	 * _display_rows
	 */
	private function _display_rows( $posts, $level = 0 ) {
		foreach ( $posts as $post )
			$this->single_row( $post, $level );
	}

	/**
     * single_row
	 */
	public function single_row( $post, $level = 0 ) {
		$global_post = get_post();

		$post = get_post( $post );
		$this->current_level = $level;

		$GLOBALS['post'] = $post;
		setup_postdata( $post );

		$classes = 'iedit author-' . ( get_current_user_id() == $post->post_author ? 'self' : 'other' );

		$lock_holder = wp_check_post_lock( $post->ID );
		if ( $lock_holder ) {
			$classes .= ' wp-locked';
		}

		if ( $post->post_parent ) {
		    $count = count( get_post_ancestors( $post->ID ) );
		    $classes .= ' level-'. $count;
		} else {
		    $classes .= ' level-0';
		}
	?>
		<tr id="post-<?php echo $post->ID; ?>" class="<?php echo implode( ' ', get_post_class( $classes, $post->ID ) ); ?>">
			<?php $this->single_row_columns( $post ); ?>
		</tr>
	<?php
		$GLOBALS['post'] = $global_post;
	}

	/**
	 * single_row_columns
	 */
	protected function single_row_columns( $item ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$classes = "$column_name column-$column_name";
			if ( $primary === $column_name ) {
				$classes .= ' has-row-actions column-primary';
			}

			if ( in_array( $column_name, $hidden ) ) {
				$classes .= ' hidden';
			}

			// Comments column uses HTML in the display name with screen reader text.
			// Instead of using esc_attr(), we strip tags to get closer to a user-friendly string.
			$data = 'data-colname="' . wp_strip_all_tags( $column_display_name ) . '"';

			$attributes = "class='$classes' $data";
            //print_r($column_name);
			if ( 'cb' === $column_name ) {
				echo '<th scope="row" class="check-column">';
				echo $this->column_cb( $item );
				echo '</th>';
			} elseif ( method_exists( $this, '_column_' . $column_name ) ) {
				echo call_user_func(
					array( $this, '_column_' . $column_name ),
					$item,
					$classes,
					$data,
					$primary
				);
			} elseif ( method_exists( $this, 'column_' . $column_name ) ) {
				echo "<td $attributes>";
				echo call_user_func( array( $this, 'column_' . $column_name ), $item );
				//echo $this->handle_row_actions( $item, $column_name, $primary );
				echo "</td>";
			} else {
				echo "<td $attributes>";
				echo $this->column_default( $item, $column_name );
				//echo $this->handle_row_actions( $item, $column_name, $primary );
				echo "</td>";
			}
		}
	}

	/**
	 * Outputs the hidden row displayed when inline editing
	 */
	public function inline_edit() {
		global $mode;

		$screen = $this->screen;
        $screen->post_type = 'uni_cpo_option';

		$post = get_default_post_to_edit( $screen->post_type );
		$post_type_object = get_post_type_object( $screen->post_type );

		$m = ( isset( $mode ) && 'excerpt' === $mode ) ? 'excerpt' : 'list';
        $core_columns = array( 'cb' => true, 'title' => true);

	?>

	<form method="get"><table style="display: none"><tbody id="inlineedit">
		<?php
        $hclass = 'page';
		$bulk = 0;
		while ( $bulk < 2 ) { ?>

		<tr id="<?php echo $bulk ? 'bulk-edit' : 'inline-edit'; ?>" class="inline-edit-row inline-edit-row-<?php echo "$hclass inline-edit-" . $screen->post_type;
			echo $bulk ? " bulk-edit-row bulk-edit-row-$hclass bulk-edit-{$screen->post_type}" : " quick-edit-row quick-edit-row-$hclass inline-edit-{$screen->post_type}";
		?>" style="display: none"><td colspan="<?php echo $this->get_column_count(); ?>" class="colspanchange">

		<fieldset class="inline-edit-col-left">
			<legend class="inline-edit-legend"><?php echo $bulk ? __( 'Bulk Edit' ) : __( 'Quick Edit' ); ?></legend>
			<div class="inline-edit-col">
	<?php

	if ( post_type_supports( $screen->post_type, 'title' ) ) :
		if ( $bulk ) : ?>
			<div id="bulk-title-div">
				<div id="bulk-titles"></div>
			</div>

	<?php else : // $bulk ?>

			<label>
				<span class="title"><?php _e( 'Title' ); ?></span>
				<span class="input-text-wrap"><input type="text" name="post_title" class="ptitle" value="" /></span>
			</label>

	<?php endif; // $bulk
	endif; // post_type_supports title ?>

		    </div>
        </fieldset>

		<fieldset class="inline-edit-col-right">
            <div class="inline-edit-col"></div>
        </fieldset>

	<?php
		list( $columns ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			if ( isset( $core_columns[$column_name] ) )
				continue;

			if ( $bulk ) {
				do_action( 'bulk_edit_custom_box', $column_name, $screen->post_type );
			} else {
				do_action( 'quick_edit_custom_box', $column_name, $screen->post_type );
			}

		}
	?>
		<p class="submit inline-edit-save">
			<button type="button" class="button-secondary cancel alignleft"><?php _e( 'Cancel' ); ?></button>
			<?php if ( ! $bulk ) {
				wp_nonce_field( 'inlineeditnonce', '_inline_edit', false );
				?>
				<button type="button" class="button-primary save alignright"><?php _e( 'Update' ); ?></button>
				<span class="spinner"></span>
			<?php } else {
				submit_button( __( 'Update' ), 'button-primary alignright', 'bulk_edit', false );
			} ?>
			<input type="hidden" name="post_view" value="<?php echo esc_attr( $m ); ?>" />
			<input type="hidden" name="screen" value="<?php echo esc_attr( $screen->id ); ?>" />
			<?php if ( ! $bulk && ! post_type_supports( $screen->post_type, 'author' ) ) { ?>
				<input type="hidden" name="post_author" value="<?php echo esc_attr( $post->post_author ); ?>" />
			<?php } ?>
			<span class="error" style="display:none"></span>
			<br class="clear" />
		</p>
		</td></tr>
	<?php
		$bulk++;
		}
?>
		</tbody></table></form>
<?php
	}

}

?>