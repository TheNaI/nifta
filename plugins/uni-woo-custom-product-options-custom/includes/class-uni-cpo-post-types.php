<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uni_Cpo_Post_types Class.
 */
class Uni_Cpo_Post_types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( post_type_exists('uni_cpo_option') ) {
			return;
		}

	    $labels = array(
		    'name' => esc_html__('CPO option', 'uni-cpo'),
		    'singular_name' => esc_html__('CPO option', 'uni-cpo'),
		    'add_new' => esc_html__('New CPO option', 'uni-cpo'),
		    'add_new_item' => esc_html__('Add CPO option', 'uni-cpo'),
		    'edit_item' => esc_html__('Edit CPO option', 'uni-cpo'),
		    'new_item' => esc_html__('All CPO options', 'uni-cpo'),
		    'view_item' => esc_html__('View CPO option', 'uni-cpo'),
		    'search_items' => esc_html__('Search CPO options', 'uni-cpo'),
		    'not_found' => esc_html__('CPO options not found', 'uni-cpo'),
		    'not_found_in_trash' => esc_html__('CPO options not found in cart', 'uni-cpo'),
		    'parent_item_colon' => esc_html__('Parent CPO option', 'uni-cpo'),
		    'menu_name' => esc_html__('CPO options', 'uni-cpo')
	    );

	    $args = array(
		    'labels' => $labels,
		    'public' => false,
            'show_in_rest' => true,
		    'publicly_queryable' => true,
		    'show_ui' => true,
		    'show_in_menu' => true,
		    'query_var' => true,
		    'menu_position' => 4.8,
            'menu_icon' => 'dashicons-welcome-widgets-menus',
		    'capability_type' => 'page',
		    'hierarchical' => true,
		    'has_archive' => true,
		    'rewrite' => array( 'slug' => 'uni-cpo-option', 'with_front' => false ),
		    'supports' => array('title', 'custom-fields'),
		    'taxonomies' => array(),
	    );
	    register_post_type( 'uni_cpo_option' , $args );

    }

}

Uni_Cpo_Post_types::init();
