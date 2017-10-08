<?php
/*
*   Uni_Cpo_Ajax Class
*
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Uni_Cpo_Ajax {

    protected $sNonceInputName      = 'uni_auth_nonce';
    protected $sNonce               = 'uni_authenticate_nonce';

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'do_cpo_ajax' ), 0 );
		self::add_ajax_events();
	}

	/**
	 * Get Ajax Endpoint.
	 */
	public static function get_endpoint( $request = '' ) {
		return esc_url_raw( add_query_arg( 'cpo-ajax', $request ) );
	}

	/**
	 * Set CPO AJAX constant and headers.
	 */
	public static function define_ajax() {
		if ( ! empty( $_GET['cpo-ajax'] ) ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}
			if ( ! defined( 'CPO_DOING_AJAX' ) ) {
				define( 'CPO_DOING_AJAX', true );
			}
			$GLOBALS['wpdb']->hide_errors();
		}
	}

	/**
	 * Send headers for CPO Ajax Requests
	 */
	private static function cpo_ajax_headers() {
		send_origin_headers();
		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );
		send_nosniff_header();
		nocache_headers();
		status_header( 200 );
	}

	/**
	 * Check for CPO Ajax request and fire action.
	 */
	public static function do_cpo_ajax() {
		global $wp_query;

		if ( ! empty( $_GET['cpo-ajax'] ) ) {
			$wp_query->set( 'cpo-ajax', sanitize_text_field( $_GET['cpo-ajax'] ) );
		}

		if ( $action = $wp_query->get( 'cpo-ajax' ) ) {
			self::cpo_ajax_headers();
			do_action( 'cpo_ajax_' . sanitize_text_field( $action ) );
			die();
		}
	}

	/**
	*   Hook in methods
	*/
	public static function add_ajax_events() {

        $aAjaxEvents = array(
                    'uni_cpo_options_create' => false,
                    'uni_cpo_options_attach' => false,
                    'uni_cpo_options_remove_show_dialog' => false,
                    'uni_cpo_options_remove' => false,
                    'uni_cpo_option_add' => false,
                    'uni_cpo_option_delete_show_dialog' => false,
                    'uni_cpo_option_delete' => false,
                    'uni_cpo_structure_update' => false,
                    'uni_cpo_field_settings_show' => false,
                    'uni_cpo_field_settings_save' => false,
                    'uni_cpo_do_suboptions_export' => false,
                    'uni_cpo_do_suboptions_import' => false,
                    'uni_cpo_non_option_vars_show' => false,
                    'uni_cpo_non_option_vars_save' => false,
                    'uni_cpo_non_option_vars_delete' => false,
                    'uni_cpo_formula_conditional_rule_show' => false,
                    'uni_cpo_formula_conditional_rule_save' => false,
                    'uni_cpo_formula_conditional_rule_delete' => false,
                    'uni_cpo_formula_conditional_rule_imex_show' => false,
                    'uni_cpo_formula_conditional_rule_imex_save' => false,
                    'uni_cpo_weight_conditional_rule_show' => false,
                    'uni_cpo_weight_conditional_rule_save' => false,
                    'uni_cpo_weight_conditional_rule_delete' => false,
                    'uni_cpo_weight_conditional_rule_imex_show' => false,
                    'uni_cpo_weight_conditional_rule_imex_save' => false,
                    'uni_cpo_cart_discounts_show' => false,
                    'uni_cpo_cart_discounts_save' => false,
                    'uni_cpo_cart_discounts_delete' => false,
                    'uni_cpo_order_item_options_add_show' => false,
                    'uni_cpo_order_item_options_save' => false,
                    'uni_cpo_calculate_price_ajax' => true,
                    'uni_cpo_inline_save' => false
        );

		foreach ( $aAjaxEvents as $sAjaxEvent => $bPriv ) {
			add_action( 'wp_ajax_' . $sAjaxEvent, array(__CLASS__, $sAjaxEvent) );

			if ( $bPriv ) {
				add_action( 'wp_ajax_nopriv_' . $sAjaxEvent, array(__CLASS__, $sAjaxEvent) );
			}
		}

	}

	/**
	*   uni_cpo_options_create
    */
    public static function uni_cpo_options_create() {

	    $aResult 		= self::r();

        $iProductId		= ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';

        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) ) {

            // create uni_cpo_option post
            $sTitle = sanitize_title_with_dashes( uniqid('options_set_') ).'_pid_'.$iProductId;
            $iNewOptionsPostId = wp_insert_post(
                array('post_type' => 'uni_cpo_option', 'post_title' => $sTitle, 'post_status' => 'publish', 'post_content' => '')
            );

            if ( !is_wp_error($iNewOptionsPostId) && $iNewOptionsPostId !== 0 ) {
                // update product meta - connect with newly created options post
                update_post_meta( $iProductId, '_uni_cpo_options_set', $iNewOptionsPostId );

                $aResult['status'] 	    = 'success';
                $aResult['optionsid'] 	= $iNewOptionsPostId;
                $aResult['message'] 	= esc_html__('A new options set is successfully created!', 'uni-cpo');
            } else {
                $aResult['message']     = $iNewOptionsPostId->get_error_message();
            }

        } else {
	        $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_cpo_options_attach
    */
    public static function uni_cpo_options_attach() {

	    $aResult 		= self::r();

        $iProductId		= ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';
        $iOptionsPostId	= ( !empty($_POST['optionsid']) ) ? esc_sql(intval($_POST['optionsid'])) : '';

        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) && !empty($iOptionsPostId) ) {

            // update product meta - connect with options post
            update_post_meta( $iProductId, '_uni_cpo_options_set', $iOptionsPostId );

            $aResult['status'] 	    = 'success';
            $aResult['optionsid'] 	= $iOptionsPostId;
            $aResult['redirect'] 	= get_edit_post_link( $iProductId, '' );
            $aResult['message'] 	= esc_html__('A new options set is successfully attached!', 'uni-cpo');

        } else {
	        $aResult['message'] 	= esc_html__('Product ID and/or Options ID are not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_cpo_options_remove_show_dialog
    */
    public static function uni_cpo_options_remove_show_dialog() {

	    $aResult 		= self::r();

        $iProductId		= ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';

        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) ) {

            $sOutput = '<p class="confirmation-text">'.esc_html__('Are you sure you want to remove all the options from this product? These options will not be
            deleted, however. And you will be able to re-attach them.', 'uni-cpo').'</p>';

            $aResult['status'] 	    = 'success';
            $aResult['action'] 	    = 'uni_cpo_options_remove';
            $aResult['output'] 	    = $sOutput;
            $aResult['pid'] 	    = $iProductId;
            $aResult['message'] 	= esc_html__('Success!', 'uni-cpo');

        } else {
	        $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_cpo_options_remove
    */
    public static function uni_cpo_options_remove() {

	    $aResult 		= self::r();

        $iProductId		= ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';

        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) ) {

            // updates product meta - connects with newly created options post
            delete_post_meta( $iProductId, '_uni_cpo_options_set' );
            // deletes formula conditional rules as we changed options set
            delete_post_meta( $iProductId, '_uni_cpo_formula_rule_options' );
            // deletes non option vars
            delete_post_meta( $iProductId, '_uni_cpo_non_option_vars' );
            // deletes main formula
            delete_post_meta( $iProductId, '_uni_cpo_price_main_formula' );

            $aResult['status'] 	    = 'success';
            $aResult['message'] 	= esc_html__('Options set is successfully removed!', 'uni-cpo');

        } else {
	        $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_cpo_option_add
    */
    public static function uni_cpo_option_add() {

	    $aResult 		= self::r();

        $iOptionsPostId	= ( !empty($_POST['optionsid']) ) ? esc_sql(intval($_POST['optionsid'])) : '';
        $sType	        = ( !empty($_POST['itemtype']) ) ? esc_sql($_POST['itemtype']) : '';

        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iOptionsPostId) && !empty($sType) ) {

            // create uni_cpo_option child post
            $sSlug = sanitize_title_with_dashes( uniqid('option_') );
            $sVarName = '{' . UniCpo()->var_slug . $sSlug.'}';
            $iNewOptionPostId = wp_insert_post(
                                    array(
                                        'post_type'     => 'uni_cpo_option',
                                        'post_title'    => $sVarName,
                                        'post_name'     => $sSlug,
                                        'post_status'   => 'publish',
                                        'post_parent'   => $iOptionsPostId
                                    )
                                );

            // important meta data
            update_post_meta( $iNewOptionPostId, '_uni_cpo_field_type', $sType );

            $oOption = uni_cpo_get_option( $iNewOptionPostId, $sType );

            if ( $oOption !== false ) {

                $aNewOptionData                 = $oOption->get_option_data();

                $aResult['status'] 	            = 'success';
                $aResult['newoptiondata']       = $aNewOptionData;
                $aResult['message'] 	        = esc_html__('A new option element is successfully created!', 'uni-cpo');

            } else {
                $aResult['message'] 	        = esc_html__('Smth went wrong.', 'uni-cpo');
            }

        } else {
	        $aResult['message'] 	        = esc_html__('Options Post ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_cpo_option_delete_show_dialog
    */
    public static function uni_cpo_option_delete_show_dialog() {

	    $aResult 		= self::r();

        $iOptionPostId	        = ( !empty($_POST['oid']) ) ? esc_sql(intval($_POST['oid'])) : '';

        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iOptionPostId) ) {

            $sOutput = '<p class="confirmation-text">'.esc_html__('Are you sure you want to delete this option? This operation cannot be undone! The
            option will be deleted from this list as well as from this set of options. It means that the option will disappear from options lists of
            all products which use the same set of options! Important: all nested options will be also deleted!', 'uni-cpo').'</p>';

            $aResult['status'] 	    = 'success';
            $aResult['action'] 	    = 'uni_cpo_option_delete';
            $aResult['output'] 	    = $sOutput;
            $aResult['oid'] 	    = $iOptionPostId;
            $aResult['message'] 	= esc_html__('Success!', 'uni-cpo');

        } else {
	        $aResult['message'] 	= esc_html__('Option ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_cpo_option_delete
    */
    public static function uni_cpo_option_delete() {

	    $aResult 		= self::r();

        $iOptionPostId	= ( !empty($_POST['oid']) ) ? esc_sql(intval($_POST['oid'])) : '';
        $sChildrenOptionsIds = ( !empty($_POST['childrenoptions']) ) ? esc_sql($_POST['childrenoptions']) : '';
        $sListId        = ( !empty($_POST['listid']) ) ? esc_sql($_POST['listid']) : '';

        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iOptionPostId) ) {

            wp_delete_post($iOptionPostId, true);

            if ( !empty($sChildrenOptionsIds) ) {
                $aChildrenOptionsIds = explode(',', $sChildrenOptionsIds);
                if ( !empty($aChildrenOptionsIds) && is_array($aChildrenOptionsIds) ) {
                    foreach ( $aChildrenOptionsIds as $iChildrenOptionId ) {
                        wp_delete_post($iChildrenOptionId, true);
                    }
                }
            }

            $aResult['status'] 	    = 'success';
            $aResult['oid'] 	    = $iOptionPostId;
            $aResult['listid']      = $sListId;
            $aResult['message'] 	= esc_html__('The option/options is/are successfully deleted!', 'uni-cpo');

        } else {
	        $aResult['message'] 	= esc_html__('Option Post ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_cpo_structure_update
    */
    public static function uni_cpo_structure_update() {

	    $aResult 		= self::r();

        $aStructure     = ( !empty($_POST['structure']) ) ? $_POST['structure'] : '';
        $iOptionsPostId	= ( !empty($_POST['optionsid']) ) ? esc_sql(intval($_POST['optionsid'])) : '';
        $iProductId	    = ( !empty($_POST['productid']) ) ? esc_sql(intval($_POST['productid'])) : '';

        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iOptionsPostId) ) {

            update_post_meta( $iOptionsPostId, '_uni_cpo_options_structure', $aStructure );

            // prepares a well formatted list of variables to display near main formula field
            // and marks children items with a special post meta
            $sOutput = uni_cpo_list_of_formula_variables( $iOptionsPostId, 'include_nov', $iProductId );

            $oFieldsStructure = uni_cpo_list_of_formula_variables( $iOptionsPostId, 'fields_structure' );

            $aResult['status'] 	            = 'success';
            $aResult['formulavarslist']     = $sOutput;
            $aResult['updated_structure']   = $oFieldsStructure;
            $aResult['message'] 	        = esc_html__('Saved!', 'uni-cpo');

        } else {
	        $aResult['message'] 	        = esc_html__('Options Post ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );

    }

	/**
	*   uni_cpo_field_settings_show
    */
    public static function uni_cpo_field_settings_show() {

	    $aResult 		    = self::r();

        $sType			    = ( !empty($_POST['field_type']) ) ? esc_sql($_POST['field_type']) : '';
        $iOptionPostId	    = ( !empty($_POST['oid']) ) ? intval(esc_sql($_POST['oid'])) : '';
        $options_set_id	    = ( !empty($_POST['setid']) ) ? intval(esc_sql($_POST['setid'])) : '';

        $sNonce             = ( !empty($_POST['uni_auth_nonce']) ) ? esc_sql($_POST['uni_auth_nonce']) : '';
        $sAntiCheat         = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($sType) && !empty($iOptionPostId) ) {

            $oOption            = uni_cpo_get_option( $iOptionPostId, $sType );
            $product_options    = get_post_meta( $options_set_id, '_uni_cpo_options_structure', true );

            // checks if related post's object exists
            if ( $oOption->get_id() ) {

                $aFieldSpecificAttributes = $oOption->get_specific_settings();
                $admin_fields_attributes = uni_cpo_get_option_admin_fields_attributes();
                $aFieldsAttributes  = ( !empty($aFieldSpecificAttributes) ) ? array_merge( $admin_fields_attributes, $aFieldSpecificAttributes )  : $admin_fields_attributes;
                $bIsChildOption     = false;
                $bIsChildOption     = $oOption->is_child();
                $aFilterArray       = array();
                $aRulesFieldsArray  = array();
                $aRulesValArray     = '';
                $aOptionSettings    = $oOption->get_settings();

                $sCommonMarkup = '<ul>';
                if ( !empty($aOptionSettings) ) {
                    $iSectionCount = 1;
                    foreach ( $aOptionSettings as $aArray ) {
                        $sCommonMarkup .= '<li><a href="#cpo-option-modal-'.$iSectionCount.'">' . $aArray['section_title'] . '</a></li>';
                        $iSectionCount++;
                    }
                }
                $sCommonMarkup .= '</ul>';

                if ( !empty($aOptionSettings) ) {
                    $iSettingsContentCount = 1;
                    foreach ( $aOptionSettings as $aArray ) {
                        $sCommonMarkup .= '<div id="cpo-option-modal-'.$iSettingsContentCount.'">';
                        if ( !empty( $aArray['settings'] ) ) {
                            foreach ( $aArray['settings'] as $sFieldSlug ) {
                                if ( !empty( $aFieldsAttributes[$sFieldSlug] ) ) {
                                    foreach ( $aFieldsAttributes[$sFieldSlug] as $sFormElementName => $aFormElementProperties ) {
                                        if ( function_exists( 'uni_cpo_admin_'.$sFormElementName ) ) {
                                            $sCommonMarkup .= call_user_func( 'uni_cpo_admin_'.$sFormElementName, $oOption, $aFormElementProperties );
                                        } else {
                                            $sCommonMarkup .= '<p>' . esc_html__( 'Sorry, this function does not exist', 'uni-cpo' ) . '</p>';
                                        }
                                    }
                                }
                            }
                        } else {
                            $sCommonMarkup .= '<div class="cpo-modal-field-container"><p class="no-settings">' . esc_html__('There are no settings of this type.', 'uni-cpo') . '</p></div>';
                        }
                        $sCommonMarkup .= '</div>';

                        $iSettingsContentCount++;
                    }
                }

                // array of data for filter
                $aFilterArray       = uni_cpo_get_options_filter_data( $product_options, $iOptionPostId );

                // an array of fields conditional rules if they exist
                if ( get_post_meta( $oOption->get_id(), '_uni_cpo_field_conditional_scheme', true ) ) {
                    $aRulesFieldsArray = get_post_meta( $oOption->get_id(), '_uni_cpo_field_conditional_scheme', true );
                }

                // an array of fields conditional rules if they exist
                if ( get_post_meta( $oOption->get_id(), '_uni_cpo_val_conditional_scheme', true ) ) {
                    $aRulesValArray = get_post_meta( $oOption->get_id(), '_uni_cpo_val_conditional_scheme', true );
                }


                $aResult['status']          = 'success';
                $aResult['message']         = '';
                $aResult['output'] 	        = $sCommonMarkup;
                $aResult['filter']          = $aFilterArray;
                $aResult['rules_fields']    = $aRulesFieldsArray;
                $aResult['rules_val']       = $aRulesValArray;

            }

        } else {
	        $aResult['message'] 	= esc_html__('Type of the field and/or option post ID are not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_cpo_field_settings_save
    */
    public static function uni_cpo_field_settings_save() {

	    $aResult 		= self::r();

        $aFormData = $_POST['form_data'];
        $aFormData = stripslashes_deep( $aFormData );
        $aFormData = json_decode($aFormData, true);

        $iProductId		= ( !empty($aFormData['pid']) ) ? intval( strip_tags( $aFormData['pid'] ) ) : '';
        $iOptionPostId	= ( !empty($aFormData['oid']) ) ? intval( strip_tags( $aFormData['oid'] ) ) : '';
        $sType	        = ( !empty($aFormData['otype']) ) ? strip_tags( $aFormData['otype'] ) : '';

        $sNonce         = ( !empty($aFormData['uni_auth_nonce']) ) ? esc_sql($aFormData['uni_auth_nonce']) : '';
        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iOptionPostId) && !empty($sType) ) {

            if ( !empty($aFormData['uni_cpo_field_slug']) ) {

                $oOption        = uni_cpo_get_option( $iOptionPostId, $sType );
                $reserved_slugs = uni_cpo_get_reserved_option_slugs();

                // checks if related post's object exists
                if ( $oOption->get_id() ) {

                    if ( $aFormData['uni_cpo_field_slug'] !== $oOption->post->post_name ) {
                        $aListOptionsSlugs = uni_cpo_get_all_options_slugs_by_options_set_id( $oOption->post->post_parent );

                        if ( in_array($aFormData['uni_cpo_field_slug'], $aListOptionsSlugs) || in_array( $aFormData['uni_cpo_field_slug'], $reserved_slugs ) ) {
                		    $sSlug = uni_cpo_get_unique_option_slug( $aFormData['uni_cpo_field_slug'], $aListOptionsSlugs );
                        } else {
                            $sSlug = $aFormData['uni_cpo_field_slug'];
                        }
                        $sVarName = '{' . UniCpo()->var_slug . $sSlug.'}';
                    }

                    $aOptionSettings        = $oOption->get_settings();
                    // save values of all the settings except displaying conditional scheme
                    if ( !empty($aOptionSettings) ) {
                        foreach ( $aOptionSettings as $aArray ) {
                            if ( !empty($aArray['settings']) ) {
                                foreach ( $aArray['settings'] as $sFieldSlug ) {
                                    if ( isset($aFormData[UniCpo()->var_slug . $sFieldSlug]) && ( $aFormData[UniCpo()->var_slug . $sFieldSlug] === '0'
                                        || !empty($aFormData[UniCpo()->var_slug . $sFieldSlug]) ) ) {

                                        // skip these special settings for now
                                        if ( 'field_conditional_scheme' === $sFieldSlug || 'val_conditional_scheme' === $sFieldSlug ) {
                                            continue;
                                        }

                                        if ( $sFieldSlug == 'field_slug' ) {
                                            if ( $aFormData['uni_cpo_field_slug'] !== $oOption->post->post_name ) {
                                                wp_update_post( array('ID' => $oOption->get_id(), 'post_title' => $sVarName, 'post_name' => $sSlug) );
                                                // TODO - update
                                                // update children options field conditional rules schemes with the new slug
                                                //uni_cpo_update_field_conditional_rules_scheme( $oOption, $sOldSlug, $sSlug );
                                                // update main formula and formula conditional rules scheme with the new slug
                                                uni_cpo_process_formula_conditional_rules_scheme( $iProductId, 'update', $sOldSlug, $sSlug );
                                            }
                                        } else {
                                            // save suboptions
                                            update_post_meta( $oOption->get_id(), '_' . UniCpo()->var_slug . $sFieldSlug, $aFormData[UniCpo()->var_slug . $sFieldSlug] );
                                        }

                                    } else {

                                        delete_post_meta( $oOption->get_id(), '_' . UniCpo()->var_slug . $sFieldSlug );

                                    }
                                }
                            }
                        }
                    }

                    // updates option's field conditional logic scheme
                    if ( isset($aFormData['uni_cpo_field_conditional_scheme']) && ! empty($aFormData['uni_cpo_field_conditional_scheme']) ) {
                        $stripped_field_rules = stripslashes_deep($aFormData['uni_cpo_field_conditional_scheme']);
                        $field_rules = json_decode($stripped_field_rules, true);
                        update_post_meta( $oOption->get_id(), '_uni_cpo_field_conditional_scheme', $field_rules );
                    } else {
                        delete_post_meta( $oOption->get_id(), '_uni_cpo_field_conditional_scheme' );
                    }

                    // update option's validation conditional logic scheme
                    if ( isset($aFormData['uni_cpo_val_conditional_scheme']) && ! empty($aFormData['uni_cpo_val_conditional_scheme']) ) {
                        $stripped_val_rules = stripslashes_deep($aFormData['uni_cpo_val_conditional_scheme']);
                        $i = 0;
                        foreach ( $stripped_val_rules as $sKey => $rule_array ) {
                            if ( empty( $rule_array['value'] ) ) {
                                unset( $stripped_val_rules[$sKey] );
                                continue;
                            }

                            if ( ! empty( $rule_array['value'] ) && is_array( $rule_array['value'] ) ) {
                                $is_all_empty = true;
                                foreach ( $rule_array['value'] as $key => $value ) {
                                    if ( ! empty( $value ) ) {
                                        $is_all_empty = false;
                                    }
                                }
                            }

                            if ( $is_all_empty ) {
                                unset( $stripped_val_rules[$sKey] );
                                continue;
                            }

                            if ( empty( $rule_array['rule'] ) || null === $rule_array['rule'] ) {
                                unset( $stripped_val_rules[$sKey] );
                                continue;
                            }

                            $stripped_val_rules[$i]['rule'] = json_decode($rule_array['rule']);
                            $stripped_val_rules[$i]['value'] = $rule_array['value'];
                            $i++;

                        }
                        ksort($stripped_val_rules);
                        update_post_meta( $oOption->get_id(), '_uni_cpo_val_conditional_scheme', $stripped_val_rules );
                    } else {
                        delete_post_meta( $oOption->get_id(), '_uni_cpo_val_conditional_scheme' );
                    }

                    //
                    $aNewOptionData             = $oOption->get_option_data();

                    $aResult['status'] 	        = 'success';
                    $aResult['message'] 	    = esc_html__('Success!', 'uni-cpo');
                    $aResult['oid']             = $oOption->get_id();
                    $aResult['newoptiondata']   = $aNewOptionData;
                    $aResult['test']   = get_post_meta( $oOption->get_id(), '_uni_cpo_field_conditional_scheme', true );

                } else {
    	            $aResult['message'] 	= esc_html__('Option post object is not found!', 'uni-cpo');
                }

            } else {
	            $aResult['message'] 	= esc_html__('Post slug is not set!', 'uni-cpo');
            }

        } else {
	        $aResult['message'] 	= esc_html__('Type of the field and/or option post ID are not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

    /**
    *   uni_cpo_suboption_export
    */
    public static function uni_cpo_do_suboptions_export() {
        
        check_ajax_referer( 'uni_authenticate_nonce', 'security' );

        if ( ! current_user_can( 'edit_products' ) ) {
            wp_die( -1 );
        }

        include_once( UniCpo()->plugin_path() . '/includes/admin/uni-suboptions-csv-exporter.php' );

        $product_id = absint( $_POST['product_id'] );

        //$step     = absint( $_POST['step'] );
        $exporter = new Uni_Suboptions_CSV_Exporter();

        $exporter->set_limit( 300 );
        $exporter->set_option_id( $_POST['option_id'] );
        $exporter->set_field_name( $_POST['field_name'] );
        $exporter->generate_file();

        //if ( 100 === $exporter->get_percent_complete() ) {
            wp_send_json_success( array(
                'step'       => 'done',
                'percentage' => 100,
                'url'        => add_query_arg( array( 'nonce' => wp_create_nonce( 'uni-cpo-suboptions-csv' ), 'action' => 'uni_cpo_download_suboptions_csv' ), admin_url( 'post.php?post=' . $product_id . '&action=edit' ) ),
            ) );
        /*} else {
            wp_send_json_success( array(
                'step'       => ++$step,
                'percentage' => $exporter->get_percent_complete(),
                'columns'    => $exporter->get_column_names(),
            ) );
        }*/

    }

    /**
    *   uni_cpo_suboption_import
    */
    public static function uni_cpo_do_suboptions_import() {

        check_ajax_referer( 'uni_authenticate_nonce', 'security' );

        if ( ! current_user_can( 'edit_products' ) || ! isset( $_FILES['file'] ) ) {
            wp_die( -1 );
        }

        include_once( UniCpo()->plugin_path() . '/includes/admin/uni-suboptions-csv-importer.php' );
        $file   = wc_clean( $_FILES['file']['tmp_name'] );
        $params = array(
            'delimiter'       => ! empty( $_POST['delimiter'] ) ? wc_clean( $_POST['delimiter'] ) : ',',
            'start_pos'       => isset( $_POST['position'] ) ? absint( $_POST['position'] ) : 0,
            'mapping'         => isset( $_POST['mapping'] ) ? (array) $_POST['mapping'] : array(),
            'update_existing' => isset( $_POST['update_existing'] ) ? (bool) $_POST['update_existing'] : false,
            'lines'           => 300,
            'parse'           => true,
        );
        $importer = new Uni_Suboptions_CSV_Importer( $file, $params );
        $importer->set_option_id( $_POST['option_id'] );
        $importer->set_field_name( $_POST['field_name'] );
        $total = $importer->import();
        $percent_complete = $importer->get_complete_percentage( $total );

        if ( 100 === $percent_complete ) {
            wp_send_json_success( array(
                'step'       => 'done',
                'percentage' => 100
            ) );
        }
    }

	/**
	*   uni_cpo_non_option_vars_show
    */
    public static function uni_cpo_non_option_vars_show() {

	    $aResult 		= self::r();

        $iProductId		= ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';

        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) ) {

            $aPostCustom = get_post_custom($iProductId);

            $aNonOptionVarsArray = array();
            if ( isset($aPostCustom['_uni_cpo_non_option_vars'][0]) && !empty($aPostCustom['_uni_cpo_non_option_vars'][0]) ) {
                $aNonOptionVarsArray = maybe_unserialize($aPostCustom['_uni_cpo_non_option_vars'][0]);
            }
            //
            $is_non_option_wholesale = ( isset( $aPostCustom['_uni_cpo_non_option_vars_wholesale_enable'][0] ) && ! empty( $aPostCustom['_uni_cpo_non_option_vars_wholesale_enable'][0] ) ) ? true : false;
            if ( $is_non_option_wholesale ) {
                $all_role_names = uni_cpo_get_all_role_names( true );
                $chosen_roles   = isset( $aPostCustom['_uni_cpo_user_roles_chosen'][0] ) ? $aPostCustom['_uni_cpo_user_roles_chosen'][0] : array();
                $chosen_roles   = maybe_unserialize( $chosen_roles );
            }

            $sOutput = '<div class="uni-cpo-non-option-vars-options-header">';

            $sOutput .= '<h3>'.esc_html__('Non-option variables').'</h3>';

            $sOutput .= '<p>'.esc_html__('It is possible to create an additional variable that is not based on option and
            assign any maths formula for it. It is also possible to use any existed option based variable in formula for
            non-option variable.').'</p>';

            if ( isset($aPostCustom['_uni_cpo_options_set'][0]) && !empty($aPostCustom['_uni_cpo_options_set'][0]) ) {
                $iOptionsSetPostId = intval($aPostCustom['_uni_cpo_options_set'][0]);
                $sOutput .= '<h3>'.esc_html__('Available option based variables', 'uni-cpo').':</h3>
                    <div class="variablesWrap uni-clear">
                        <ul class="uni-clear">';

                        $sOutput .= uni_cpo_list_of_formula_variables( $iOptionsSetPostId );

                $sOutput .= '</ul>
                    </div>';
            }

            $sOutput .= '</div>';

            if ( empty($aNonOptionVarsArray) || !is_array($aNonOptionVarsArray) ) {

            $sOutput .= '<div class="uni-cpo-non-option-vars-options-repeat">
                            <div class="uni-cpo-non-option-vars-options-repeat-wrapper">

                                <div class="uni-cpo-non-option-vars-options-add-wrapper">
                                    <span class="uni_cpo_non_option_vars_option_add">' . esc_html__('Add', 'uni-cpo') . '</span>
                                </div>
                                <div class="uni-clear"></div>
                                <div class="uni-cpo-non-option-vars-options-wrapper">

                                    <div class="uni-cpo-non-option-vars-options-template uni-cpo-non-option-vars-options-row">
                                        <div class="uni-cpo-non-option-vars-options-move-wrapper">
                                            <span class="uni_cpo_non_option_vars_option_move"><i class="fa fa-arrows"></i></span>
                                        </div>
                                        <div class="uni-cpo-non-option-vars-options-content-wrapper uni-clear">
                                            <div class="uni-cpo-non-option-vars-options-content-field-wrapper">
                                                <span><code>{' . UniCpo()->non_option_var_slug . '</code></span>
                                                <input type="text" name="uni_cpo_non_option_vars[{{row-count}}][slug]" value="" class="uni-cpo-modal-field uni-cpo-non-option-slug-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" />
                                                <span><code>}</code></span>
                                            </div>
                                            <div class="uni-cpo-non-option-vars-options-content-field-wrapper">';
                                            if ( $is_non_option_wholesale ) {
                                            $sOutput .= '<label>' . esc_html__('Value / Formula by user roles', 'uni-cpo') . '</label>';
                                                if ( ! empty( $all_role_names ) ) {
                                                    foreach ( $all_role_names as $role_slug => $role_name ) {
                                                    if ( in_array( $role_slug, $chosen_roles ) ) {
                                                    $sOutput .= '<div class="uni-cpo-non-option-vars-options-content-formula-wrapper">
                                                        <label>' . esc_html__('Role:', 'uni-cpo') . ' ' . esc_html( $role_name ) . '</label>
                                                        <textarea name="uni_cpo_non_option_vars[{{row-count}}][formula][' . esc_attr( $role_slug ) . ']" col="10" row="3"></textarea>
                                                    </div>';
                                                    }
                                                    }
                                                }
                                            } else {
                                            $sOutput .= '<label>' . esc_html__('Value / Formula', 'uni-cpo') . '</label>
                                                <div class="uni-cpo-non-option-vars-options-content-formula-wrapper full-wrapper">
                                                    <textarea name="uni_cpo_non_option_vars[{{row-count}}][formula]" col="10" row="3"></textarea>
                                                </div>';
                                            }
                                            $sOutput .= '</div>
                                            <div class="uni-cpo-non-option-vars-options-rules-content-field-wrapper uni-cpo-non-option-vars-options-rules-remove-wrapper">
                                                <span class="uni_cpo_non_option_vars_option_remove"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uni-cpo-non-option-vars-options-row">
                                        <div class="uni-cpo-non-option-vars-options-move-wrapper">
                                            <span class="uni_cpo_non_option_vars_option_move"><i class="fa fa-arrows"></i></span>
                                        </div>
                                        <div class="uni-cpo-non-option-vars-options-content-wrapper uni-clear">
                                            <div class="uni-cpo-non-option-vars-options-content-field-wrapper">
                                                <span><code>{' . UniCpo()->non_option_var_slug . '</code></span>
                                                <input type="text" name="uni_cpo_non_option_vars[0][slug]" value="" class="uni-cpo-modal-field uni-cpo-non-option-slug-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" />
                                                <span><code>}</code></span>
                                            </div>
                                            <div class="uni-cpo-non-option-vars-options-content-field-wrapper">';
                                            if ( $is_non_option_wholesale ) {
                                            $sOutput .= '<label>' . esc_html__('Value / Formula by user roles', 'uni-cpo') . '</label>';
                                                if ( ! empty( $all_role_names ) ) {
                                                    foreach ( $all_role_names as $role_slug => $role_name ) {
                                                    if ( in_array( $role_slug, $chosen_roles ) ) {
                                                    $sOutput .= '<div class="uni-cpo-non-option-vars-options-content-formula-wrapper">
                                                        <label>' . esc_html__('Role:', 'uni-cpo') . ' ' . esc_html( $role_name ) . '</label>
                                                        <textarea name="uni_cpo_non_option_vars[0][formula][' . esc_attr( $role_slug ) . ']" col="10" row="3"></textarea>
                                                    </div>';
                                                    }
                                                    }
                                                }
                                            } else {
                                            $sOutput .= '<label>' . esc_html__('Value / Formula', 'uni-cpo') . '</label>
                                                <div class="uni-cpo-non-option-vars-options-content-formula-wrapper full-wrapper">
                                                    <textarea name="uni_cpo_non_option_vars[0][formula]" col="10" row="3"></textarea>
                                                </div>';
                                            }
                                            $sOutput .= '</div>
                                            <div class="uni-cpo-non-option-vars-options-rules-content-field-wrapper uni-cpo-non-option-vars-options-rules-remove-wrapper">
                                                <span class="uni_cpo_non_option_vars_option_remove"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>';

            } else {

            $sOutput .= '<div class="uni-cpo-non-option-vars-options-repeat">
                            <div class="uni-cpo-non-option-vars-options-repeat-wrapper">

                                <div class="uni-cpo-non-option-vars-options-add-wrapper">
                                    <span class="uni_cpo_non_option_vars_option_add">' . esc_html__('Add', 'uni-cpo') . '</span>
                                </div>
                                <div class="uni-clear"></div>
                                <div class="uni-cpo-non-option-vars-options-wrapper">

                                    <div class="uni-cpo-non-option-vars-options-template uni-cpo-non-option-vars-options-row">
                                        <div class="uni-cpo-non-option-vars-options-move-wrapper">
                                            <span class="uni_cpo_non_option_vars_option_move"><i class="fa fa-arrows"></i></span>
                                        </div>
                                        <div class="uni-cpo-non-option-vars-options-content-wrapper uni-clear">
                                            <div class="uni-cpo-non-option-vars-options-content-field-wrapper">
                                                <span><code>{' . UniCpo()->non_option_var_slug . '</code></span>
                                                <input type="text" name="uni_cpo_non_option_vars[{{row-count}}][slug]" value="" class="uni-cpo-modal-field uni-cpo-non-option-slug-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" />
                                                <span><code>}</code></span>
                                            </div>
                                            <div class="uni-cpo-non-option-vars-options-content-field-wrapper">';
                                            if ( $is_non_option_wholesale ) {
                                            $sOutput .= '<label>' . esc_html__('Value / Formula by user roles', 'uni-cpo') . '</label>';
                                                if ( ! empty( $all_role_names ) ) {
                                                    foreach ( $all_role_names as $role_slug => $role_name ) {
                                                    if ( in_array( $role_slug, $chosen_roles ) ) {
                                                    $sOutput .= '<div class="uni-cpo-non-option-vars-options-content-formula-wrapper">
                                                        <label>' . esc_html__('Role:', 'uni-cpo') . ' ' . esc_html( $role_name ) . '</label>
                                                        <textarea name="uni_cpo_non_option_vars[{{row-count}}][formula][' . esc_attr( $role_slug ) . ']" col="10" row="3"></textarea>
                                                    </div>';
                                                    }
                                                    }
                                                }
                                            } else {
                                            $sOutput .= '<label>' . esc_html__('Value / Formula', 'uni-cpo') . '</label>
                                                <div class="uni-cpo-non-option-vars-options-content-formula-wrapper full-wrapper">
                                                    <textarea name="uni_cpo_non_option_vars[{{row-count}}][formula]" col="10" row="3"></textarea>
                                                </div>';
                                            }
                                            $sOutput .= '</div>
                                            <div class="uni-cpo-non-option-vars-options-rules-content-field-wrapper uni-cpo-non-option-vars-options-rules-remove-wrapper">
                                                <span class="uni_cpo_non_option_vars_option_remove"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    </div>';

                    $nov_options_count = 0;
                    foreach ( $aNonOptionVarsArray as $sKey => $aNovItemArray ) {

                        $sOutput .= '<div class="uni-cpo-non-option-vars-options-row">
                                        <div class="uni-cpo-non-option-vars-options-move-wrapper">
                                            <span class="uni_cpo_non_option_vars_option_move"><i class="fa fa-arrows"></i></span>
                                        </div>
                                        <div class="uni-cpo-non-option-vars-options-content-wrapper uni-clear">
                                            <div class="uni-cpo-non-option-vars-options-content-field-wrapper">
                                                <span><code>{' . UniCpo()->non_option_var_slug . '</code></span>
                                                <input type="text" name="uni_cpo_non_option_vars['.esc_attr($nov_options_count).'][slug]" value="'.(( !empty($aNovItemArray['slug']) ) ? esc_attr($aNovItemArray['slug']) : '').'" class="uni-cpo-modal-field uni-cpo-non-option-slug-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" />
                                                <span><code>}</code></span>
                                            </div>
                                            <div class="uni-cpo-non-option-vars-options-content-field-wrapper">';
                                            if ( $is_non_option_wholesale ) {
                                            $sOutput .= '<label>' . esc_html__('Value / Formula by user roles', 'uni-cpo') . '</label>';
                                                if ( ! empty( $all_role_names ) ) {
                                                    foreach ( $all_role_names as $role_slug => $role_name ) {
                                                    if ( in_array( $role_slug, $chosen_roles ) ) {
                                                    $sOutput .= '<div class="uni-cpo-non-option-vars-options-content-formula-wrapper">
                                                        <label>' . esc_html__('Role:', 'uni-cpo') . ' ' . esc_html( $role_name ) . '</label>
                                                        <textarea name="uni_cpo_non_option_vars['.esc_attr($nov_options_count).'][formula][' . esc_attr( $role_slug ) . ']" col="10" row="3">'.(( isset( $aNovItemArray['formula'][$role_slug] ) && ! empty( $aNovItemArray['formula'][$role_slug] ) ) ? esc_attr( $aNovItemArray['formula'][$role_slug] ) : '').'</textarea>
                                                    </div>';
                                                    }
                                                    }
                                                }
                                            } else {
                                            $sOutput .= '<label>' . esc_html__('Value / Formula', 'uni-cpo') . '</label>
                                                <div class="uni-cpo-non-option-vars-options-content-formula-wrapper full-wrapper">
                                                    <textarea name="uni_cpo_non_option_vars['.esc_attr($nov_options_count).'][formula]" col="10" row="3">'.(( ! empty( $aNovItemArray['formula'] ) && ! is_array( $aNovItemArray['formula'] ) ) ? esc_attr($aNovItemArray['formula']) : '').'</textarea>
                                                </div>';
                                            }
                                            $sOutput .= '</div>
                                            <div class="uni-cpo-non-option-vars-options-rules-content-field-wrapper uni-cpo-non-option-vars-options-rules-remove-wrapper">
                                                <span class="uni_cpo_non_option_vars_option_remove"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    </div>';
                        $nov_options_count++;
                    }

                    $sOutput .= '</div>
                            </div>
                        </div>';

            }

            $aResult['status']          = 'success';
            $aResult['message']         = '';
            $aResult['output'] 	        = $sOutput;

        } else {
	        $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_cpo_non_option_vars_save
    */
    public static function uni_cpo_non_option_vars_save() {

	    $aResult 		= self::r();

        $form_data      = $_POST['form_data'];
        $form_data      = stripslashes_deep( $form_data );
        $form_data      = json_decode($form_data, true);

        $product_id	    = ( !empty($form_data['pid']) ) ? intval( strip_tags( $form_data['pid'] ) ) : '';
        $options_set_id = ( get_post_meta( $product_id, '_uni_cpo_options_set', true ) ) ? intval( get_post_meta( $product_id, '_uni_cpo_options_set', true ) ) : '';

        $nonce          = ( !empty($form_data['uni_auth_nonce']) ) ? strip_tags( $form_data['uni_auth_nonce'] ) : '';
        $anti_cheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? strip_tags( $_POST['cheaters_always_disable_js'] ) : '';

        if ( ( empty( $anti_cheat ) || $anti_cheat != 'true_bro' ) || !wp_verify_nonce( $nonce, 'uni_authenticate_nonce' ) ) {
            wp_send_json( $aResult );
        }


        if ( ! empty( $product_id ) && ! empty( $options_set_id ) ) {

            // saves non option variables - checks for uniqueness
            if ( ! empty( $form_data['uni_cpo_non_option_vars'] ) ) {
                $new_data           = array();
                $options_slugs_list = uni_cpo_get_all_options_slugs_by_options_set_id( $options_set_id );
                $reserved_slugs     = uni_cpo_get_reserved_option_slugs();
                $prohibitted_slugs  = array_merge( $reserved_slugs, $options_slugs_list );
                $breaks             = array("\r\n", "\n", "\r");
                $i                  = 0;

                foreach ( $form_data['uni_cpo_non_option_vars'] as $value ) {
                    if ( null === $value || empty( $value ) || ! isset( $value['slug'] ) || empty( $value['slug'] ) ) {
                        continue;
                    }
                    if ( in_array( $value['slug'], $prohibitted_slugs ) ) {
        			    $new_data[$i]['slug'] = uni_cpo_get_unique_option_slug( $value['slug'], $options_slugs_list );
                    } else {
                        $new_data[$i]['slug'] = $value['slug'];
                    }
                    if ( is_array( $value['formula'] ) ) {
                        foreach ( $value['formula'] as $role => $formula ) {
                            $new_data[$i]['formula'][$role] = str_replace( $breaks, "", $value['formula'][$role] );
                        }
                    } else {
                        $new_data[$i]['formula'] = str_replace( $breaks, "", $value['formula'] );
                    }
                    $i++;
                }
                update_post_meta( $product_id, '_uni_cpo_non_option_vars', $new_data );
            }

            // prepares a well formatted list of variables to display near main formula field
            // and marks children items with a special post meta
            $output = uni_cpo_list_of_formula_variables( $options_set_id, 'include_nov', $product_id );

            $aResult['status'] 	            = 'success';
            $aResult['pid'] 	            = $product_id;
            $aResult['formulavarslist']     = $output;
            $aResult['message'] 	        = esc_html__('Success!', 'uni-cpo');

        } else {
	        $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

    /**
	*   uni_cpo_non_option_vars_delete
    */
    public static function uni_cpo_non_option_vars_delete() {

	    $aResult 		= self::r();

        $iProductId	    = ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';
        $iOptionsPostId = ( get_post_meta( $_POST['pid'], '_uni_cpo_options_set', true ) ) ? intval(get_post_meta( $_POST['pid'], '_uni_cpo_options_set', true )) : '';

        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) ) {

            // delete
            delete_post_meta( $iProductId, '_uni_cpo_non_option_vars' );

            $sOutput = uni_cpo_list_of_formula_variables( $iOptionsPostId, 'include_nov', $iProductId );

            $aResult['status'] 	            = 'success';
            $aResult['formulavarslist']     = $sOutput;
            $aResult['message'] 	        = esc_html__('Success!', 'uni-cpo');

        } else {
	        $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }


    //*******************************
    //
    //  Formula Conditional Rules
    //
    //*******************************


	/**
	*   uni_cpo_formula_conditional_rule_show
    */
    public static function uni_cpo_formula_conditional_rule_show() {

	    $aResult 		= self::r();

        $iProductId		= ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';

        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) ) {

            $aProductCustom = get_post_custom($iProductId);

            // for all added options for the product
            $iOptionsPostId     = ( !empty($aProductCustom['_uni_cpo_options_set'][0]) ) ? intval( $aProductCustom['_uni_cpo_options_set'][0] ) : 0;
            $aProductOptions    = get_post_meta( $iOptionsPostId, '_uni_cpo_options_structure', true );

            // generates an array of option based vars
            $aFilterArray       = uni_cpo_get_options_filter_data( $aProductOptions );

            // generates an array of option based vars
            if ( isset($aProductCustom['_uni_cpo_non_option_vars'][0]) && !empty($aProductCustom['_uni_cpo_non_option_vars'][0]) ) {

                $aNonOptionVarsArray = maybe_unserialize($aProductCustom['_uni_cpo_non_option_vars'][0]);
                if ( !empty($aNonOptionVarsArray) ) {
                    foreach ( $aNonOptionVarsArray as $sKey => $aNovItemArray ) {
                        $aFilterArray[] = uni_cpo_generate_query_builder_filter_for_nov_var( $aNovItemArray );
                    }
                }

            }

            if ( !empty($aFilterArray) ) {

                $aFormulaRulesArray = '';
                $aRulesArray = array();

                if ( !empty($aProductCustom['_uni_cpo_formula_rule_options'][0]) ) {
                    $aFormulaRulesArray = maybe_unserialize($aProductCustom['_uni_cpo_formula_rule_options'][0]);
                }

            if ( empty($aFormulaRulesArray) && !is_array($aFormulaRulesArray) ) {

            $sOutput = '<div class="uni-formula-conditional-rules-repeat">
                            <div class="uni-formula-conditional-rules-repeat-wrapper">
                                <div class="uni-formula-conditional-rules-add-wrapper">
                                    <span class="uni_formula_conditional_rule_add">' . esc_html__('Add', 'uni-cpo') . '</span>
                                </div>
                                <div class="uni-clear"></div>
                                <div class="uni-formula-conditional-rules-options-wrapper">

                                    <div class="uni-formula-conditional-rules-options-template uni-formula-conditional-rules-options-row">
                                        <div class="uni-formula-conditional-rules-move-wrapper">
                                            <span class="uni_formula_conditional_rule_move"><i class="fa fa-arrows"></i></span>
                                        </div>
                                        <div class="uni-formula-conditional-rules-content-wrapper">
                                            <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                <label>' . esc_html__('Rule', 'uni-cpo') . '</label>
                                                <div class="uni-query-builder-wrapper">
                                                    <div id="cpo-formula-rule-builder-{{row-count}}" class="cpo-formula-rule-builder uni-cpo-sort-select-el"></div>

                                                    <input class="cpo-parse-formula-rule-json uni-cpo-settings-btn uni-cpo-settings-saved uni-cpo-sort-select-el-data" data-id="{{row-count}}" type="button" value="' . esc_html__('Fetch the formatted rule', 'uni-cpo') . '" />
                                                </div>
                                                <input id="uni_cpo_formula_rule_scheme-{{row-count}}" type="hidden" name="uni_cpo_formula_rule_options[{{row-count}}][rule]" value="" class="uni-cpo-sort-select-el />
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                <label>' . esc_html__('Formula', 'uni-cpo') . '</label>
                                                <textarea name="uni_cpo_formula_rule_options[{{row-count}}][formula]" class="uni-cpo-modal-field uni-cpo-sort-select-el" data-parsley-required="true" data-parsley-trigger="change focusout submit"></textarea>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-field-wrapper uni-formula-conditional-rules-remove-wrapper">
                                                <span class="uni_formula_conditional_rule_remove"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uni-formula-conditional-rules-options-row">
                                        <div class="uni-formula-conditional-rules-move-wrapper">
                                            <span class="uni_formula_conditional_rule_move"><i class="fa fa-arrows"></i></span>
                                        </div>
                                        <div class="uni-formula-conditional-rules-content-wrapper">
                                            <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                <label>' . esc_html__('Rule', 'uni-cpo') . '</label>
                                                <div class="uni-query-builder-wrapper">
                                                    <div id="cpo-formula-rule-builder-0" class="cpo-formula-rule-builder uni-cpo-sort-select-el"></div>

                                                    <input class="cpo-parse-formula-rule-json uni-cpo-settings-btn uni-cpo-settings-saved uni-cpo-sort-select-el-data" data-id="0" type="button" value="' . esc_html__('Fetch the formatted rule', 'uni-cpo') . '" />
                                                </div>
                                                <input id="uni_cpo_formula_rule_scheme-0" type="hidden" name="uni_cpo_formula_rule_options[0][rule]" value="" class="uni-cpo-sort-select-el" />
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                <label>' . esc_html__('Formula', 'uni-cpo') . '</label>
                                                <textarea name="uni_cpo_formula_rule_options[0][formula]" class="uni-cpo-modal-field uni-cpo-sort-select-el" data-parsley-required="true" data-parsley-trigger="change focusout submit"></textarea>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-field-wrapper uni-formula-conditional-rules-remove-wrapper">
                                                <span class="uni_formula_conditional_rule_remove"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    </div>

                            </div>
                        </div>
                    </div>';

            } else {

            $sOutput = '<div class="uni-formula-conditional-rules-repeat">
                            <div class="uni-formula-conditional-rules-repeat-wrapper">
                                <div class="uni-formula-conditional-rules-add-wrapper">
                                    <span class="uni_formula_conditional_rule_add">' . esc_html__('Add', 'uni-cpo') . '</span>
                                </div>
                                <div class="uni-clear"></div>
                                <div class="uni-formula-conditional-rules-options-wrapper">

                                    <div class="uni-formula-conditional-rules-options-template uni-formula-conditional-rules-options-row">
                                        <div class="uni-formula-conditional-rules-move-wrapper">
                                            <span class="uni_formula_conditional_rule_move"><i class="fa fa-arrows"></i></span>
                                        </div>
                                        <div class="uni-formula-conditional-rules-content-wrapper">
                                            <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                <label>' . esc_html__('Rule', 'uni-cpo') . '</label>
                                                <div class="uni-query-builder-wrapper">
                                                    <div id="cpo-formula-rule-builder-{{row-count}}" class="cpo-formula-rule-builder uni-cpo-sort-select-el"></div>

                                                    <input class="cpo-parse-formula-rule-json uni-cpo-settings-btn uni-cpo-settings-saved uni-cpo-sort-select-el-data" data-id="{{row-count}}" type="button" value="' . esc_html__('Fetch the formatted rule', 'uni-cpo') . '" />
                                                </div>
                                                <input id="uni_cpo_formula_rule_scheme-{{row-count}}" type="hidden" name="uni_cpo_formula_rule_options[{{row-count}}][rule]" value="" class="uni-cpo-sort-select-el" />
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                <label>' . esc_html__('Formula', 'uni-cpo') . '</label>
                                                <textarea name="uni_cpo_formula_rule_options[{{row-count}}][formula]" class="uni-cpo-modal-field uni-cpo-sort-select-el" data-parsley-required="true" data-parsley-trigger="change focusout submit"></textarea>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-field-wrapper uni-formula-conditional-rules-remove-wrapper">
                                                <span class="uni_formula_conditional_rule_remove"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    </div>';
                $iRulesCount = 0;
                foreach ( $aFormulaRulesArray as $sKey => $aFormulaRuleArray ) {

                        $sOutput .= '<div class="uni-formula-conditional-rules-options-row">
                                        <div class="uni-formula-conditional-rules-move-wrapper">
                                            <span class="uni_formula_conditional_rule_move"><i class="fa fa-arrows"></i></span>
                                        </div>
                                        <div class="uni-formula-conditional-rules-content-wrapper">
                                            <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                <label>' . esc_html__('Rule', 'uni-cpo') . '</label>
                                                <div class="uni-query-builder-wrapper">
                                                    <div id="cpo-formula-rule-builder-'.esc_attr($iRulesCount).'" class="cpo-formula-rule-builder"></div>

                                                    <input class="cpo-parse-formula-rule-json uni-cpo-settings-btn uni-cpo-settings-saved uni-cpo-sort-select-el-data" data-id="'.esc_attr($iRulesCount).'" type="button" value="' . esc_html__('Fetch the formatted rule', 'uni-cpo') . '" />
                                                </div>
                                                <input id="uni_cpo_formula_rule_scheme-'.esc_attr($iRulesCount).'" type="hidden" name="uni_cpo_formula_rule_options['.esc_attr($iRulesCount).'][rule]" value="'.esc_attr(json_encode($aFormulaRuleArray['rule'])).'" class="uni-cpo-sort-select-el-scheme" />
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                <label>' . esc_html__('Formula', 'uni-cpo') . '</label>
                                                <textarea name="uni_cpo_formula_rule_options['.esc_attr($iRulesCount).'][formula]" class="uni-cpo-modal-field uni-cpo-sort-select-el-formula" data-parsley-required="true" data-parsley-trigger="change focusout submit">'.esc_html($aFormulaRuleArray['formula']).'</textarea>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-field-wrapper uni-formula-conditional-rules-remove-wrapper">
                                                <span class="uni_formula_conditional_rule_remove"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    </div>';

                    $aRulesArray[] = $aFormulaRuleArray['rule'];
                    $iRulesCount++;
                }

                $sOutput .= '</div>
                        </div>
                    </div>';

            }

            $aResult['status']          = 'success';
            $aResult['message']         = '';
            $aResult['output'] 	        = $sOutput;
            $aResult['filter']          = $aFilterArray;
            $aResult['rules']           = $aRulesArray;

            } else {
                $aResult['message'] 	= esc_html__('No options created/added yet', 'uni-cpo');
            }

        } else {
	        $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_cpo_formula_conditional_rule_save
    */
    public static function uni_cpo_formula_conditional_rule_save() {

	    $aResult 		= self::r();

        $aFormData = $_POST['form_data'];
        $aFormData = stripslashes_deep( $aFormData );
        $aFormData = json_decode($aFormData, true);

        $iProductId	    = ( !empty($aFormData['pid']) ) ? intval( strip_tags( $aFormData['pid'] ) ) : '';

        $sNonce         = ( !empty($aFormData['uni_auth_nonce']) ) ? strip_tags( $aFormData['uni_auth_nonce'] ) : '';
        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? strip_tags( $_POST['cheaters_always_disable_js'] ) : '';

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) || !wp_verify_nonce( $aFormData['uni_auth_nonce'], 'uni_authenticate_nonce' ) ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) ) {

            // save a conditional scheme value
            if ( !empty($aFormData['uni_cpo_formula_rule_options']) ) {
                $aSlashesStrippedRules = stripslashes_deep($aFormData['uni_cpo_formula_rule_options']);
                $i = 0;
                foreach ( $aSlashesStrippedRules as $sKey => $aRuleArray ) {
                    $aSlashesStrippedRules[$i]['rule'] = json_decode($aRuleArray['rule']);
                    $aSlashesStrippedRules[$i]['formula'] = $aRuleArray['formula'];
                    $i++;
                }
                ksort($aSlashesStrippedRules);
                update_post_meta( $iProductId, '_uni_cpo_formula_rule_options', $aSlashesStrippedRules );
            }

            $aResult['status'] 	    = 'success';
            $aResult['pid'] 	    = $iProductId;
            $aResult['message'] 	= esc_html__('Success!', 'uni-cpo');

        } else {
	        $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_cpo_formula_conditional_rule_delete
    */
    public static function uni_cpo_formula_conditional_rule_delete() {

	    $aResult 		= self::r();

        $iProductId	    = ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';

        $sNonce         = ( !empty($_POST['uni_auth_nonce']) ) ? esc_sql($_POST['uni_auth_nonce']) : '';
        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) ) {

            // delete a conditional scheme value
            delete_post_meta( $iProductId, '_uni_cpo_formula_rule_options' );

            $aResult['status'] 	    = 'success';
            $aResult['message'] 	= esc_html__('Success!', 'uni-cpo');

        } else {
	        $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

    /**
	*   uni_cpo_formula_conditional_rule_imex_show
    */
    public static function uni_cpo_formula_conditional_rule_imex_show() {

	    $aResult 		= self::r();

        $iProductId		= ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';

        $sNonce         = ( !empty($_POST['uni_auth_nonce']) ) ? esc_sql($_POST['uni_auth_nonce']) : '';
        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) ) {

            $aProductCustom = get_post_custom($iProductId);

            //
            $aFormulaRules  = get_post_meta( $iProductId, '_uni_cpo_formula_rule_options', true );

            $sOutput = '<div class="uni-cpo-non-option-vars-options-header">';

            $sOutput .= '<p style="color:red;">'.esc_html__('Warning! Do not change the structure of the json object. This may be resulted in an error and unaccessability of the formula conditional rules builder.', 'uni-cpo').'</p>';

            $sOutput .= '<div class="uni-cpo-non-option-vars-options-header">';

            $sOutput .= '<textarea name="uni_cpo_formula_rule_imex" class="uni-cpo-formula-rule-imex-textarea">';
            $sOutput .= '</textarea>';

            $aResult['status']          = 'success';
            $aResult['message']         = '';
            $aResult['output'] 	        = $sOutput;
            $aResult['rules'] 	        = $aFormulaRules;

        } else {
	        $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

    /**
	*   uni_cpo_formula_conditional_rule_imex_save
    */
    public static function uni_cpo_formula_conditional_rule_imex_save() {

	    $aResult 		= self::r();

        $aFormData = $_POST['form_data'];
        $aFormData = stripslashes_deep( $aFormData );
        $aFormData = json_decode($aFormData, true);

        $iProductId	    = ( ! empty($aFormData['pid']) ) ? intval( strip_tags( $aFormData['pid'] ) ) : '';

        $sNonce         = ( ! empty($aFormData['uni_auth_nonce']) ) ? strip_tags( $aFormData['uni_auth_nonce'] ) : '';
        $sAntiCheat     = ( ! empty($_POST['cheaters_always_disable_js']) ) ? strip_tags( $_POST['cheaters_always_disable_js'] ) : '';

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) || ! wp_verify_nonce( $aFormData['uni_auth_nonce'], 'uni_authenticate_nonce' ) ) {
            wp_send_json( $aResult );
        }

        $aRules = json_decode( $aFormData['uni_cpo_formula_rule_imex'], true );

        if ( ! empty( $iProductId ) && ! empty( $aRules ) ) {

            $aNewArray = array();
            foreach ( $aRules as $sKey => $oObject ) {
                $aNewArray[] = (array)$oObject;
            }

            //
            update_post_meta( $iProductId, '_uni_cpo_formula_rule_options', $aNewArray );

            $aResult['status']          = 'success';
            $aResult['message']         = '';

        } else {
	        $aResult['message'] 	    = esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }


    //*******************************
    //
    //  Weight Conditional Rules
    //
    //*******************************


    /**
    *   uni_cpo_weight_conditional_rule_show
    */
    public static function uni_cpo_weight_conditional_rule_show() {

        $aResult 		= self::r();

        $iProductId		= ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';

        $sNonce         = ( !empty($_POST['uni_auth_nonce']) ) ? esc_sql($_POST['uni_auth_nonce']) : '';
        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) ) {

            $aProductCustom = get_post_custom($iProductId);

            // for all added options for the product
            $iOptionsPostId     = ( !empty($aProductCustom['_uni_cpo_options_set'][0]) ) ? intval( $aProductCustom['_uni_cpo_options_set'][0] ) : 0;
            $aProductOptions    = get_post_meta( $iOptionsPostId, '_uni_cpo_options_structure', true );

            // generates an array of option based vars
            $aFilterArray       = uni_cpo_get_options_filter_data( $aProductOptions );

            // generates an array of non option based vars
            if ( isset($aProductCustom['_uni_cpo_non_option_vars'][0]) && !empty($aProductCustom['_uni_cpo_non_option_vars'][0]) ) {

                $aNonOptionVarsArray = maybe_unserialize($aProductCustom['_uni_cpo_non_option_vars'][0]);
                if ( !empty($aNonOptionVarsArray) ) {
                    foreach ( $aNonOptionVarsArray as $sKey => $aNovItemArray ) {
                        $aFilterArray[] = uni_cpo_generate_query_builder_filter_for_nov_var( $aNovItemArray );
                    }
                }

            }

            if ( !empty($aFilterArray) ) {

                $aConditionalRulesArray = '';
                $aRulesArray = array();

                if ( !empty($aProductCustom['_uni_cpo_weight_rule_options'][0]) ) {
                    $aConditionalRulesArray = maybe_unserialize($aProductCustom['_uni_cpo_weight_rule_options'][0]);
                }

                if ( empty( $aConditionalRulesArray ) && !is_array( $aConditionalRulesArray ) ) {

                $sOutput = '<div class="uni-formula-conditional-rules-repeat">
                                <div class="uni-formula-conditional-rules-repeat-wrapper">
                                    <div class="uni-formula-conditional-rules-add-wrapper">
                                        <span class="uni_formula_conditional_rule_add">' . esc_html__('Add', 'uni-cpo') . '</span>
                                    </div>
                                    <div class="uni-clear"></div>
                                    <div class="uni-formula-conditional-rules-options-wrapper">

                                        <div class="uni-formula-conditional-rules-options-template uni-formula-conditional-rules-options-row">
                                            <div class="uni-formula-conditional-rules-move-wrapper">
                                                <span class="uni_formula_conditional_rule_move"><i class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-wrapper">
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <label>' . esc_html__('Rule', 'uni-cpo') . '</label>
                                                    <div class="uni-query-builder-wrapper">
                                                        <div id="cpo-formula-rule-builder-{{row-count}}" class="cpo-formula-rule-builder uni-cpo-sort-select-el"></div>

                                                        <input class="cpo-parse-formula-rule-json uni-cpo-settings-btn uni-cpo-settings-saved uni-cpo-sort-select-el-data" data-id="{{row-count}}" type="button" value="' . esc_html__('Fetch the formatted rule', 'uni-cpo') . '" />
                                                    </div>
                                                    <input id="uni_cpo_formula_rule_scheme-{{row-count}}" type="hidden" name="uni_cpo_formula_rule_options[{{row-count}}][rule]" value="" class="uni-cpo-sort-select-el />
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <label>' . esc_html__('Formula for the product weight calculation', 'uni-cpo') . '</label>
                                                    <textarea name="uni_cpo_formula_rule_options[{{row-count}}][formula]" class="uni-cpo-modal-field uni-cpo-sort-select-el" data-parsley-required="true" data-parsley-trigger="change focusout submit"></textarea>
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-field-wrapper uni-formula-conditional-rules-remove-wrapper">
                                                    <span class="uni_formula_conditional_rule_remove"><i class="fa fa-times"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="uni-formula-conditional-rules-options-row">
                                            <div class="uni-formula-conditional-rules-move-wrapper">
                                                <span class="uni_formula_conditional_rule_move"><i class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-wrapper">
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <label>' . esc_html__('Rule', 'uni-cpo') . '</label>
                                                    <div class="uni-query-builder-wrapper">
                                                        <div id="cpo-formula-rule-builder-0" class="cpo-formula-rule-builder uni-cpo-sort-select-el"></div>

                                                        <input class="cpo-parse-formula-rule-json uni-cpo-settings-btn uni-cpo-settings-saved uni-cpo-sort-select-el-data" data-id="0" type="button" value="' . esc_html__('Fetch the formatted rule', 'uni-cpo') . '" />
                                                    </div>
                                                    <input id="uni_cpo_formula_rule_scheme-0" type="hidden" name="uni_cpo_formula_rule_options[0][rule]" value="" class="uni-cpo-sort-select-el" />
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <label>' . esc_html__('Formula for the product weight calculation', 'uni-cpo') . '</label>
                                                    <textarea name="uni_cpo_formula_rule_options[0][formula]" class="uni-cpo-modal-field uni-cpo-sort-select-el" data-parsley-required="true" data-parsley-trigger="change focusout submit"></textarea>
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-field-wrapper uni-formula-conditional-rules-remove-wrapper">
                                                    <span class="uni_formula_conditional_rule_remove"><i class="fa fa-times"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                </div>
                            </div>
                        </div>';

                } else {

                $sOutput = '<div class="uni-formula-conditional-rules-repeat">
                                <div class="uni-formula-conditional-rules-repeat-wrapper">
                                    <div class="uni-formula-conditional-rules-add-wrapper">
                                        <span class="uni_formula_conditional_rule_add">' . esc_html__('Add', 'uni-cpo') . '</span>
                                    </div>
                                    <div class="uni-clear"></div>
                                    <div class="uni-formula-conditional-rules-options-wrapper">

                                        <div class="uni-formula-conditional-rules-options-template uni-formula-conditional-rules-options-row">
                                            <div class="uni-formula-conditional-rules-move-wrapper">
                                                <span class="uni_formula_conditional_rule_move"><i class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-wrapper">
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <label>' . esc_html__('Rule', 'uni-cpo') . '</label>
                                                    <div class="uni-query-builder-wrapper">
                                                        <div id="cpo-formula-rule-builder-{{row-count}}" class="cpo-formula-rule-builder uni-cpo-sort-select-el"></div>

                                                        <input class="cpo-parse-formula-rule-json uni-cpo-settings-btn uni-cpo-settings-saved uni-cpo-sort-select-el-data" data-id="{{row-count}}" type="button" value="' . esc_html__('Fetch the formatted rule', 'uni-cpo') . '" />
                                                    </div>
                                                    <input id="uni_cpo_formula_rule_scheme-{{row-count}}" type="hidden" name="uni_cpo_formula_rule_options[{{row-count}}][rule]" value="" class="uni-cpo-sort-select-el" />
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <label>' . esc_html__('Formula for the product weight calculation', 'uni-cpo') . '</label>
                                                    <textarea name="uni_cpo_formula_rule_options[{{row-count}}][formula]" class="uni-cpo-modal-field uni-cpo-sort-select-el" data-parsley-required="true" data-parsley-trigger="change focusout submit"></textarea>
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-field-wrapper uni-formula-conditional-rules-remove-wrapper">
                                                    <span class="uni_formula_conditional_rule_remove"><i class="fa fa-times"></i></span>
                                                </div>
                                            </div>
                                        </div>';
                    $iRulesCount = 0;
                    foreach ( $aConditionalRulesArray as $sKey => $aConditionalRuleArray ) {

                            $sOutput .= '<div class="uni-formula-conditional-rules-options-row">
                                            <div class="uni-formula-conditional-rules-move-wrapper">
                                                <span class="uni_formula_conditional_rule_move"><i class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-wrapper">
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <label>' . esc_html__('Rule', 'uni-cpo') . '</label>
                                                    <div class="uni-query-builder-wrapper">
                                                        <div id="cpo-formula-rule-builder-'.esc_attr($iRulesCount).'" class="cpo-formula-rule-builder"></div>

                                                        <input class="cpo-parse-formula-rule-json uni-cpo-settings-btn uni-cpo-settings-saved uni-cpo-sort-select-el-data" data-id="'.esc_attr($iRulesCount).'" type="button" value="' . esc_html__('Fetch the formatted rule', 'uni-cpo') . '" />
                                                    </div>
                                                    <input id="uni_cpo_formula_rule_scheme-'.esc_attr($iRulesCount).'" type="hidden" name="uni_cpo_formula_rule_options['.esc_attr($iRulesCount).'][rule]" value="'.esc_attr(json_encode($aConditionalRuleArray['rule'])).'" class="uni-cpo-sort-select-el-scheme" />
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <label>' . esc_html__('Formula for the product weight calculation', 'uni-cpo') . '</label>
                                                    <textarea name="uni_cpo_formula_rule_options['.esc_attr($iRulesCount).'][formula]" class="uni-cpo-modal-field uni-cpo-sort-select-el-formula" data-parsley-required="true" data-parsley-trigger="change focusout submit">'.esc_html($aConditionalRuleArray['formula']).'</textarea>
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-field-wrapper uni-formula-conditional-rules-remove-wrapper">
                                                    <span class="uni_formula_conditional_rule_remove"><i class="fa fa-times"></i></span>
                                                </div>
                                            </div>
                                        </div>';

                        $aRulesArray[] = $aConditionalRuleArray['rule'];
                        $iRulesCount++;
                    }

                    $sOutput .= '</div>
                            </div>
                        </div>';

                }

                $aResult['status']          = 'success';
                $aResult['message']         = '';
                $aResult['output'] 	        = $sOutput;
                $aResult['filter']          = $aFilterArray;
                $aResult['rules']           = $aRulesArray;

            } else {
                $aResult['message'] 	= esc_html__('No options created/added yet', 'uni-cpo');
            }

        } else {
            $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

    /**
    *   uni_cpo_weight_conditional_rule_save
    */
    public static function uni_cpo_weight_conditional_rule_save() {

        $aResult 		= self::r();

        $aFormData = $_POST['form_data'];
        $aFormData = stripslashes_deep( $aFormData );
        $aFormData = json_decode($aFormData, true);

        $iProductId	    = ( !empty($aFormData['pid']) ) ? intval( strip_tags( $aFormData['pid'] ) ) : '';

        $sNonce         = ( !empty($aFormData['uni_auth_nonce']) ) ? strip_tags( $aFormData['uni_auth_nonce'] ) : '';
        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? strip_tags( $_POST['cheaters_always_disable_js'] ) : '';

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) || !wp_verify_nonce( $aFormData['uni_auth_nonce'], 'uni_authenticate_nonce' ) ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) ) {

            // save a conditional scheme value
            if ( !empty($aFormData['uni_cpo_formula_rule_options']) ) {
                $aSlashesStrippedRules = stripslashes_deep($aFormData['uni_cpo_formula_rule_options']);
                $i = 0;
                foreach ( $aSlashesStrippedRules as $sKey => $aRuleArray ) {
                    $aSlashesStrippedRules[$i]['rule'] = json_decode($aRuleArray['rule']);
                    $aSlashesStrippedRules[$i]['formula'] = $aRuleArray['formula'];
                    $i++;
                }
                ksort($aSlashesStrippedRules);
                update_post_meta( $iProductId, '_uni_cpo_weight_rule_options', $aSlashesStrippedRules );
            }

            $aResult['status'] 	    = 'success';
            $aResult['pid'] 	    = $iProductId;
            $aResult['message'] 	= esc_html__('Success!', 'uni-cpo');

        } else {
            $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

    /**
    *   uni_cpo_weight_conditional_rule_delete
    */
    public static function uni_cpo_weight_conditional_rule_delete() {

        $aResult 		= self::r();

        $iProductId	    = ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';

        $sNonce         = ( !empty($_POST['uni_auth_nonce']) ) ? esc_sql($_POST['uni_auth_nonce']) : '';
        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) ) {

            // delete a conditional scheme value
            delete_post_meta( $iProductId, '_uni_cpo_weight_rule_options' );

            $aResult['status'] 	    = 'success';
            $aResult['message'] 	= esc_html__('Success!', 'uni-cpo');

        } else {
            $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

    /**
    *   uni_cpo_weight_conditional_rule_imex_show
    */
    public static function uni_cpo_weight_conditional_rule_imex_show() {

        $aResult 		= self::r();

        $iProductId		= ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';

        $sNonce         = ( !empty($_POST['uni_auth_nonce']) ) ? esc_sql($_POST['uni_auth_nonce']) : '';
        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) ) {

            $aProductCustom = get_post_custom($iProductId);

            //
            $aFormulaRules  = get_post_meta( $iProductId, '_uni_cpo_weight_rule_options', true );

            $sOutput = '<div class="uni-cpo-non-option-vars-options-header">';

            $sOutput .= '<p style="color:red;">'.esc_html__('Warning! Do not change the structure of the json object. This may be resulted in an error and unaccessability of the weight conditional rules builder.', 'uni-cpo').'</p>';

            $sOutput .= '<div class="uni-cpo-non-option-vars-options-header">';

            $sOutput .= '<textarea name="uni_cpo_weight_rule_imex" class="uni-cpo-formula-rule-imex-textarea">';
            $sOutput .= '</textarea>';

            $aResult['status']          = 'success';
            $aResult['message']         = '';
            $aResult['output'] 	        = $sOutput;
            $aResult['rules'] 	        = $aFormulaRules;

        } else {
            $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

    /**
    *   uni_cpo_weight_conditional_rule_imex_save
    */
    public static function uni_cpo_weight_conditional_rule_imex_save() {

        $aResult 		= self::r();

        $aFormData = $_POST['form_data'];
        $aFormData = stripslashes_deep( $aFormData );
        $aFormData = json_decode($aFormData, true);

        $iProductId	    = ( ! empty($aFormData['pid']) ) ? intval( strip_tags( $aFormData['pid'] ) ) : '';

        $sNonce         = ( ! empty($aFormData['uni_auth_nonce']) ) ? strip_tags( $aFormData['uni_auth_nonce'] ) : '';
        $sAntiCheat     = ( ! empty($_POST['cheaters_always_disable_js']) ) ? strip_tags( $_POST['cheaters_always_disable_js'] ) : '';

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) || ! wp_verify_nonce( $aFormData['uni_auth_nonce'], 'uni_authenticate_nonce' ) ) {
            wp_send_json( $aResult );
        }

        $aRules = json_decode( $aFormData['uni_cpo_weight_rule_imex'], true );

        if ( ! empty( $iProductId ) && ! empty( $aRules ) ) {

            $aNewArray = array();
            foreach ( $aRules as $sKey => $oObject ) {
                $aNewArray[] = (array)$oObject;
            }

            //
            update_post_meta( $iProductId, '_uni_cpo_weight_rule_options', $aNewArray );

            $aResult['status']          = 'success';
            $aResult['message']         = '';

        } else {
            $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_cpo_cart_discounts_show
    */
    public static function uni_cpo_cart_discounts_show() {

	    $aResult 		= self::r();

        $iProductId		= ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';
        $sDiscountsBase	= ( !empty($_POST['base']) ) ? esc_sql($_POST['base']) : '';

        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) && !empty($sDiscountsBase) ) {

            $aPostCustom = get_post_custom($iProductId);

            $aDiscountsRulesArray = '';
            if ( isset($aPostCustom['_uni_cpo_cart_discount_'.$sDiscountsBase][0]) && !empty($aPostCustom['_uni_cpo_cart_discount_'.$sDiscountsBase][0]) ) {
                $aDiscountsRulesArray = maybe_unserialize($aPostCustom['_uni_cpo_cart_discount_'.$sDiscountsBase][0]);
            }

            if ( $sDiscountsBase === 'quantity' ) {

                $sOutput = '<div class="uni-cpo-non-option-vars-options-header">';

                $sOutput .= '<h3>'.esc_html__('Quantity discount', 'uni-cpo').'</h3>';

                $sOutput .= '<p>'.esc_html__('It is possible to use a maths formula in the "value" field. It is also possible to use special
                variables: {uni_cpo_quantity} and {uni_cpo_calc_price}. The first one holds the value of WC quantity field chosen on the product page.
                The latter one holds the value about the price calculated on the product page with the help of custom options.', 'uni-cpo') . '</p>';

                /*$sOutput .= '<div class="cpo-modal-field-container">';
                    $sOutput .= '<div class="cpo-modal-field-leftcell">';
                        $sOutput .= '<label>' . esc_html__( 'Table description', 'uni-cpo' ) . '</label>';
                        $sOutput .= '<p class="cpo-modal-field-description">' . esc_html__( 'This text will be shown above the table of quantity discount', 'uni-cpo' ) . '</p>';
                    $sOutput .= '</div>';
                    $sOutput .= '<div class="cpo-modal-field-rightcell">';
                        $sOutput .= '<textarea name="uni_cpo_discount_description" class="uni-cpo-modal-field"></textarea>';
                    $sOutput .= '</div>';
                $sOutput .= '</div>'; */

                $sOutput .= '</div>';

                if ( empty($aDiscountsRulesArray) && !is_array($aDiscountsRulesArray) ) {

                $sOutput .= '<div class="uni-cpo-discounts-options-repeat">
                                <div class="uni-cpo-discounts-options-repeat-wrapper">

                                    <div class="uni-cpo-discounts-options-add-wrapper">
                                        <span class="uni_cpo_discounts_option_add">' . esc_html__('Add', 'uni-cpo') . '</span>
                                    </div>

                                    <div class="uni-cpo-discounts-options-wrapper">

                                        <div class="uni-cpo-discounts-options-template uni-cpo-discounts-options-row">
                                            <div class="uni-cpo-discounts-options-move-wrapper">
                                                <span class="uni_cpo_discounts_option_move"><i class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-cpo-discounts-options-content-wrapper uni-clear">
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Min. qty', 'uni-cpo') . '</label>
                                                    <input type="number" name="uni_cpo_discounts[{{row-count}}][min]" value="" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" />
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Max. qty', 'uni-cpo') . '</label>
                                                    <input type="number" name="uni_cpo_discounts[{{row-count}}][max]" value="" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" />
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Type', 'uni-cpo') . '</label>
                                                    <select name="uni_cpo_discounts[{{row-count}}][type]">
                                                        <option value="percentage">' . esc_html__('Percentage', 'uni-cpo') . '</option>
                                                        <option value="amount">' . esc_html__('Fixed amount', 'uni-cpo') . '</option>
                                                        <option value="price">' . esc_html__('Fixed price', 'uni-cpo') . '</option>
                                                    </select>
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Value', 'uni-cpo') . '</label>
                                                    <textarea name="uni_cpo_discounts[{{row-count}}][value]" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit"></textarea>
                                                </div>
                                                <div class="uni-cpo-discounts-options-rules-content-field-wrapper uni-cpo-discounts-options-rules-remove-wrapper">
                                                    <span class="uni_cpo_discounts_option_remove"><i class="fa fa-times"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="uni-cpo-discounts-options-row">
                                            <div class="uni-cpo-discounts-options-move-wrapper">
                                                <span class="uni_cpo_discounts_option_move"><i class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-cpo-discounts-options-content-wrapper uni-clear">
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Min. qty', 'uni-cpo') . '</label>
                                                    <input type="number" name="uni_cpo_discounts[0][min]" value="" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" />
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Max. qty', 'uni-cpo') . '</label>
                                                    <input type="number" name="uni_cpo_discounts[0][max]" value="" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" />
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Type', 'uni-cpo') . '</label>
                                                    <select name="uni_cpo_discounts[0][type]">
                                                        <option value="percentage">' . esc_html__('Percentage', 'uni-cpo') . '</option>
                                                        <option value="amount">' . esc_html__('Fixed amount', 'uni-cpo') . '</option>
                                                        <option value="price">' . esc_html__('Fixed price', 'uni-cpo') . '</option>
                                                    </select>
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Value', 'uni-cpo') . '</label>
                                                    <textarea name="uni_cpo_discounts[0][value]" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit"></textarea>
                                                </div>
                                                <div class="uni-cpo-discounts-options-rules-content-field-wrapper uni-cpo-discounts-options-rules-remove-wrapper">
                                                    <span class="uni_cpo_discounts_option_remove"><i class="fa fa-times"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>';

                } else {

                $sOutput .= '<div class="uni-cpo-discounts-options-repeat">
                                <div class="uni-cpo-discounts-options-repeat-wrapper">

                                    <div class="uni-cpo-discounts-options-add-wrapper">
                                        <span class="uni_cpo_discounts_option_add">' . esc_html__('Add', 'uni-cpo') . '</span>
                                    </div>

                                    <div class="uni-cpo-discounts-options-wrapper">

                                        <div class="uni-cpo-discounts-options-template uni-cpo-discounts-options-row">
                                            <div class="uni-cpo-discounts-options-move-wrapper">
                                                <span class="uni_cpo_discounts_option_move"><i class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-cpo-discounts-options-content-wrapper uni-clear">
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Min. qty', 'uni-cpo') . '</label>
                                                    <input type="number" name="uni_cpo_discounts[{{row-count}}][min]" value="" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" />
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Max. qty', 'uni-cpo') . '</label>
                                                    <input type="number" name="uni_cpo_discounts[{{row-count}}][max]" value="" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" />
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Type', 'uni-cpo') . '</label>
                                                    <select name="uni_cpo_discounts[{{row-count}}][type]">
                                                        <option value="percentage">' . esc_html__('Percentage', 'uni-cpo') . '</option>
                                                        <option value="amount">' . esc_html__('Fixed amount', 'uni-cpo') . '</option>
                                                        <option value="price">' . esc_html__('Fixed price', 'uni-cpo') . '</option>
                                                    </select>
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Value', 'uni-cpo') . '</label>
                                                    <textarea name="uni_cpo_discounts[{{row-count}}][value]" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit"></textarea>
                                                </div>
                                                <div class="uni-cpo-discounts-options-rules-content-field-wrapper uni-cpo-discounts-options-rules-remove-wrapper">
                                                    <span class="uni_cpo_discounts_option_remove"><i class="fa fa-times"></i></span>
                                                </div>
                                            </div>
                                        </div>';

                        $iRulesCount = 0;
                        foreach ( $aDiscountsRulesArray as $sKey => $aRuleArray ) {

                            $sOutput .= '<div class="uni-cpo-discounts-options-row">
                                            <div class="uni-cpo-discounts-options-move-wrapper">
                                                <span class="uni_cpo_discounts_option_move"><i class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-cpo-discounts-options-content-wrapper uni-clear">
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Min. qty', 'uni-cpo') . '</label>
                                                    <input type="number" name="uni_cpo_discounts['.esc_attr( $iRulesCount ).'][min]" value="'.(( !empty($aRuleArray['min']) ) ? esc_attr($aRuleArray['min']) : '').'" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" />
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Max. qty', 'uni-cpo') . '</label>
                                                    <input type="number" name="uni_cpo_discounts['.esc_attr( $iRulesCount ).'][max]" value="'.(( !empty($aRuleArray['max']) ) ? esc_attr($aRuleArray['max']) : '').'" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" />
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Type', 'uni-cpo') . '</label>
                                                    <select name="uni_cpo_discounts['.esc_attr( $iRulesCount ).'][type]">
                                                        <option value="percentage"'.selected('percentage',(( !empty($aRuleArray['type']) ) ? esc_attr($aRuleArray['type']) : ''),false).'>' . esc_html__('Percentage', 'uni-cpo') . '</option>
                                                        <option value="amount"'.selected('amount',(( !empty($aRuleArray['type']) ) ? esc_attr($aRuleArray['type']) : ''),false).'>' . esc_html__('Fixed amount', 'uni-cpo') . '</option>
                                                        <option value="price"'.selected('price',(( !empty($aRuleArray['type']) ) ? esc_attr($aRuleArray['type']) : ''),false).'>' . esc_html__('Fixed price', 'uni-cpo') . '</option>
                                                    </select>
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Value', 'uni-cpo') . '</label>
                                                    <textarea name="uni_cpo_discounts['.esc_attr( $iRulesCount ).'][value]" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit">'.(( !empty($aRuleArray['value']) ) ? esc_attr($aRuleArray['value']) : '').'</textarea>
                                                </div>
                                                <div class="uni-cpo-discounts-options-rules-content-field-wrapper uni-cpo-discounts-options-rules-remove-wrapper">
                                                    <span class="uni_cpo_discounts_option_remove"><i class="fa fa-times"></i></span>
                                                </div>
                                            </div>
                                        </div>';
                            $iRulesCount++;
                        }

                        $sOutput .= '</div>
                                </div>
                            </div>';

                }

            } else if ( $sDiscountsBase === 'role' ) {
            /*
                $aRawUserRoles = get_editable_roles();

                $sOutput = '<h3>'.esc_html__('User role based discount', 'uni-cpo').'</h3>';

                if ( empty($aDiscountsRulesArray) && !is_array($aDiscountsRulesArray) ) {

                $sOutput .= '<div class="uni-cpo-discounts-options-repeat">
                                <div class="uni-cpo-discounts-options-repeat-wrapper">

                                    <div class="uni-cpo-discounts-options-add-wrapper">
                                        <span class="uni_cpo_discounts_option_add">' . esc_html__('Add', 'uni-cpo') . '</span>
                                    </div>

                                    <div class="uni-cpo-discounts-options-wrapper">

                                        <div class="uni-cpo-discounts-options-template uni-cpo-discounts-options-row">
                                            <div class="uni-cpo-discounts-options-move-wrapper">
                                                <span class="uni_cpo_discounts_option_move"><i class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-cpo-discounts-options-content-wrapper uni-clear">
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('User role', 'uni-cpo') . '</label>
                                                    <select name="uni_cpo_discounts[{{row-count}}][role]" value="" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit">';
                                                    foreach ( $aRawUserRoles as $sKey => $aValue ) {
                                                        $sOutput .= '<option value="'.$sKey.'">'.$aValue['name'].'</option>';
                                                    }
                                        $sOutput .= '</select>
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Type', 'uni-cpo') . '</label>
                                                    <select name="uni_cpo_discounts[{{row-count}}][type]">
                                                        <option value="percentage">' . esc_html__('Percentage', 'uni-cpo') . '</option>
                                                        <option value="amount">' . esc_html__('Fixed amount', 'uni-cpo') . '</option>
                                                        <option value="price">' . esc_html__('Fixed price', 'uni-cpo') . '</option>
                                                    </select>
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Value', 'uni-cpo') . '</label>
                                                    <input type="text" name="uni_cpo_discounts[{{row-count}}][value]" value="" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" data-parsley-pattern="/^(\d+(?:[\.]\d{0,2})?)$/" />
                                                </div>
                                                <div class="uni-cpo-discounts-options-rules-content-field-wrapper uni-cpo-discounts-options-rules-remove-wrapper">
                                                    <span class="uni_cpo_discounts_option_remove"><i class="fa fa-times"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="uni-cpo-discounts-options-row">
                                            <div class="uni-cpo-discounts-options-move-wrapper">
                                                <span class="uni_cpo_discounts_option_move"><i class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-cpo-discounts-options-content-wrapper uni-clear">
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('User role', 'uni-cpo') . '</label>
                                                    <select name="uni_cpo_discounts[{{row-count}}][role]" value="" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit">';
                                                    foreach ( $aRawUserRoles as $sKey => $aValue ) {
                                                        $sOutput .= '<option value="'.$sKey.'">'.$aValue['name'].'</option>';
                                                    }
                                        $sOutput .= '</select>
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Type', 'uni-cpo') . '</label>
                                                    <select name="uni_cpo_discounts[0][type]">
                                                        <option value="percentage">' . esc_html__('Percentage', 'uni-cpo') . '</option>
                                                        <option value="amount">' . esc_html__('Fixed amount', 'uni-cpo') . '</option>
                                                        <option value="price">' . esc_html__('Fixed price', 'uni-cpo') . '</option>
                                                    </select>
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Value', 'uni-cpo') . '</label>
                                                    <input type="text" name="uni_cpo_discounts[0][value]" value="" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" data-parsley-pattern="/^(\d+(?:[\.]\d{0,2})?)$/" />
                                                </div>
                                                <div class="uni-cpo-discounts-options-rules-content-field-wrapper uni-cpo-discounts-options-rules-remove-wrapper">
                                                    <span class="uni_cpo_discounts_option_remove"><i class="fa fa-times"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>';

                } else {

                $sOutput .= '<div class="uni-cpo-discounts-options-repeat">
                                <div class="uni-cpo-discounts-options-repeat-wrapper">

                                    <div class="uni-cpo-discounts-options-add-wrapper">
                                        <span class="uni_cpo_discounts_option_add">' . esc_html__('Add', 'uni-cpo') . '</span>
                                    </div>

                                    <div class="uni-cpo-discounts-options-wrapper">

                                        <div class="uni-cpo-discounts-options-template uni-cpo-discounts-options-row">
                                            <div class="uni-cpo-discounts-options-move-wrapper">
                                                <span class="uni_cpo_discounts_option_move"><i class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-cpo-discounts-options-content-wrapper uni-clear">
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('User role', 'uni-cpo') . '</label>
                                                    <select name="uni_cpo_discounts[{{row-count}}][role]" value="" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit">';
                                                    foreach ( $aRawUserRoles as $sKey => $aValue ) {
                                                        $sOutput .= '<option value="'.$sKey.'">'.$aValue['name'].'</option>';
                                                    }
                                        $sOutput .= '</select>
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Type', 'uni-cpo') . '</label>
                                                    <select name="uni_cpo_discounts[{{row-count}}][type]">
                                                        <option value="percentage">' . esc_html__('Percentage', 'uni-cpo') . '</option>
                                                        <option value="amount">' . esc_html__('Fixed amount', 'uni-cpo') . '</option>
                                                        <option value="price">' . esc_html__('Fixed price', 'uni-cpo') . '</option>
                                                    </select>
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Value', 'uni-cpo') . '</label>
                                                    <input type="text" name="uni_cpo_discounts[{{row-count}}][value]" value="" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" data-parsley-pattern="/^(\d+(?:[\.]\d{0,2})?)$/" />
                                                </div>
                                                <div class="uni-cpo-discounts-options-rules-content-field-wrapper uni-cpo-discounts-options-rules-remove-wrapper">
                                                    <span class="uni_cpo_discounts_option_remove"><i class="fa fa-times"></i></span>
                                                </div>
                                            </div>
                                        </div>';

                        foreach ( $aDiscountsRulesArray as $sKey => $aRuleArray ) {

                            $sOutput .= '<div class="uni-cpo-discounts-options-row">
                                            <div class="uni-cpo-discounts-options-move-wrapper">
                                                <span class="uni_cpo_discounts_option_move"><i class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-cpo-discounts-options-content-wrapper uni-clear">
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('User role', 'uni-cpo') . '</label>
                                                    <select name="uni_cpo_discounts[{{row-count}}][role]" value="" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit">';
                                                    foreach ( $aRawUserRoles as $sKey => $aValue ) {
                                                        $sOutput .= '<option value="'.$sKey.'"'.selected($sKey, $aRuleArray['role']).'>'.$aValue['name'].'</option>';
                                                    }
                                        $sOutput .= '</select>
                                                </div>
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Type', 'uni-cpo') . '</label>
                                                    <select name="uni_cpo_discounts['.esc_attr($sKey).'][type]">
                                                        <option value="percentage"'.selected('percentage',(( !empty($aRuleArray['type']) ) ? esc_attr($aRuleArray['type']) : ''),false).'>' . esc_html__('Percentage', 'uni-cpo') . '</option>
                                                        <option value="amount"'.selected('amount',(( !empty($aRuleArray['type']) ) ? esc_attr($aRuleArray['type']) : ''),false).'>' . esc_html__('Fixed amount', 'uni-cpo') . '</option>
                                                        <option value="price"'.selected('price',(( !empty($aRuleArray['type']) ) ? esc_attr($aRuleArray['type']) : ''),false).'>' . esc_html__('Fixed price', 'uni-cpo') . '</option>
                                                    </select>
                                                </div>
                                                <div class="uni-cpo-discounts-options-content-field-wrapper">
                                                    <label>' . esc_html__('Value', 'uni-cpo') . '</label>
                                                    <input type="text" name="uni_cpo_discounts['.esc_attr($sKey).'][value]" value="'.(( !empty($aRuleArray['value']) ) ? esc_attr($aRuleArray['value']) : '').'" class="uni-cpo-modal-field" data-parsley-required="true" data-parsley-trigger="change focusout submit" data-parsley-pattern="/^(\d+(?:[\.]\d{0,2})?)$/" />
                                                </div>
                                                <div class="uni-cpo-discounts-options-rules-content-field-wrapper uni-cpo-discounts-options-rules-remove-wrapper">
                                                    <span class="uni_cpo_discounts_option_remove"><i class="fa fa-times"></i></span>
                                                </div>
                                            </div>
                                        </div>';

                    }

                        $sOutput .= '</div>
                                </div>
                            </div>';

                }
            */
            }

            $aResult['status']          = 'success';
            $aResult['message']         = '';
            $aResult['output'] 	        = $sOutput;

        } else {
	        $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

	/**
	*   uni_cpo_cart_discounts_save
    */
    public static function uni_cpo_cart_discounts_save() {

	    $aResult 		= self::r();

        $aFormData = $_POST['form_data'];
        $aFormData = stripslashes_deep( $aFormData );
        $aFormData = json_decode($aFormData, true);

        $iProductId	    = ( !empty($aFormData['pid']) ) ? esc_sql(intval($aFormData['pid'])) : '';
        $sDiscountsBase	= ( !empty($aFormData['base']) ) ? esc_sql($aFormData['base']) : '';

        $sNonce         = ( !empty($aFormData['uni_auth_nonce']) ) ? esc_sql($aFormData['uni_auth_nonce']) : '';
        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) || !wp_verify_nonce( $aFormData['uni_auth_nonce'], 'uni_authenticate_nonce' ) ) {
            wp_send_json( $aResult );
        }


        if ( !empty($iProductId) && !empty($sDiscountsBase) ) {

            $aPostCustom = get_post_custom($iProductId);

            if ( !empty($aFormData['uni_cpo_discounts']) ) {

                $i = 0;
                $array_discount_rules = array();
                foreach ( $aFormData['uni_cpo_discounts'] as $key => $array_rules ) {
                    if ( ! empty( $array_rules ) ) {
                        $array_discount_rules[$i] = $array_rules;
                        $i++;
                    }
                }
                ksort($array_discount_rules);
                update_post_meta( $iProductId, '_uni_cpo_cart_discount_'.$sDiscountsBase, $array_discount_rules );
            }

            $aResult['status'] 	    = 'success';
            $aResult['pid'] 	    = $iProductId;
            $aResult['base'] 	    = $sDiscountsBase;
            $aResult['message'] 	= esc_html__('Success!', 'uni-cpo');

        } else {
	        $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

    /**
	*   uni_cpo_cart_discounts_delete
    */
    public static function uni_cpo_cart_discounts_delete() {

	    $aResult 		= self::r();

        $iProductId	    = ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';
        $sDiscountsBase	= ( !empty($_POST['base']) ) ? esc_sql($_POST['base']) : '';

        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }

        if ( !empty($iProductId) && !empty($sDiscountsBase) ) {

            // delete
            delete_post_meta( $iProductId, '_uni_cpo_cart_discount_'.$sDiscountsBase );

            $aResult['status'] 	    = 'success';
            $aResult['message'] 	= esc_html__('Success!', 'uni-cpo');

        } else {
	        $aResult['message'] 	= esc_html__('Product ID is not set!', 'uni-cpo');
        }

        wp_send_json( $aResult );
    }

    /**
    *   uni_cpo_order_item_options_add_show
    */
    public static function uni_cpo_order_item_options_add_show() {

        $result 		= self::r();

        $product_id		= ( !empty($_POST['pid']) ) ? esc_sql(intval($_POST['pid'])) : '';
        $order_id		= ( !empty($_POST['order_id']) ) ? esc_sql(intval($_POST['order_id'])) : '';
        $item_id		= ( !empty($_POST['item_id']) ) ? esc_sql(intval($_POST['item_id'])) : '';

        $anti_cheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($anti_cheat) || $anti_cheat != 'true_bro' ) {
            wp_send_json( $result );
        }

        if ( ! empty( $product_id ) ) {

            // info about product options
            $product_meta       = get_post_custom( $product_id );
            $option_set_id      = ( !empty($product_meta['_uni_cpo_options_set'][0]) ) ? intval( $product_meta['_uni_cpo_options_set'][0] ) : 0;
            $product_options    = get_post_meta( $option_set_id, '_uni_cpo_options_structure', true );

            // info about possible already added options for the order item
            $order = wc_get_order( $order_id );
            $items = $order->get_items( 'line_item' );
            $meta_data = $formatted_meta = array();
            foreach ( $items as $id => $item ) {
                if ( (int) $item_id === (int) $id ) {
                    $meta_data = $item->get_meta_data();
                }
            }

            if ( ! empty( $meta_data ) ) {
                foreach ( $meta_data as $meta ) {
                    if ( false !== strpos( $meta->key, UniCpo()->var_slug ) ) {
                        $meta_key_wo_ = ltrim( $meta->key, '_' );
                        $formatted_meta[$meta_key_wo_] = $meta->value;
                    }
                }
            }

            // output
            if ( !empty( $product_options ) ) {
                $output = '<table id="cpo_order_item_options_table">';
                $output .= '<tbody id="cpo_order_item_options_items">';
                foreach ( $product_options as $structure_item ) {
                    $option = uni_cpo_get_option( $structure_item['id'] );

                    if ( $option instanceof Uni_Cpo_Option && $option->get_id() && $option->is_calculable() ) {
                        $option_type = $option->get_type();
                        //
                        $output .= $option->get_order_form_field( $formatted_meta );
                    }

                }
                $output .= '</tbody>';
                $output .= '<tbody id="cpo_order_item_options_extra">';
                $output .= '<tr><th><label>'.esc_html__( 'Quantity', 'uni-cpo' ).'<label></th>';
                $output .= '<td><input type="text" name="quantity" value="1" /></td><tr>';
                $output .= '<tr><th><input id="cpo_order_add_option_calculate" type="button" value="'.esc_html__( 'Calculate', 'uni-cpo' ).'" /></th>';
                $output .= '<td><input id="cpo_order_add_option_calculated_price" type="text" value="" disabled /></td><tr>';
                $output .= '</tbody>';
                $output .= '</table>';
            }

        }

        $result['status'] = 'success';
        $result['output'] = $output;

        wp_send_json( $result );

    }

    /**
    *   uni_cpo_order_item_options_save
    */
    public static function uni_cpo_order_item_options_save() {

        $result 		= self::r();

        $raw_posted_data = $_POST;

        $anti_cheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($anti_cheat) || $anti_cheat != 'true_bro' ) {
            wp_send_json( $result );
        }

        $posted_data = $raw_posted_data['form_data'];
        $posted_data = stripslashes_deep( $posted_data );
        $posted_data = json_decode($posted_data, true);

        //print_r($posted_data);
        $formatted_data = $posted_data;
        $item_id = $formatted_data['item_id'];
        $order_id = $formatted_data['order_id'];
        $product_id = $formatted_data['uni_cpo_product_id'];
        $quantity = $formatted_data['quantity'];
        $price = $raw_posted_data['price'];

        unset($formatted_data['action']);
        unset($formatted_data['uni_auth_nonce']);
        unset($formatted_data['item_id']);
        unset($formatted_data['order_id']);
        unset($formatted_data['uni_cpo_product_id']);
        unset($formatted_data['quantity']);
        //print_r($formatted_data);

        // deletes all existed metas
        $order = wc_get_order( $order_id );
        $items = $order->get_items( 'line_item' );
        foreach ( $items as $id => $item ) {
            if ( (int) $item_id === (int) $id ) {
                $chosen_item = $item;
            }
        }
        if ( isset( $chosen_item ) ) {
            $meta_data = array();
            $meta_data = $chosen_item->get_meta_data();
            if ( ! empty( $meta_data ) ) {
                foreach ( $meta_data as $meta ) {
                    $chosen_item->delete_meta_data( $meta->key );
                }
            }

            // adds new metas
            if ( !empty( $formatted_data ) ) {
                foreach ( $formatted_data as $key => $value ) {
                    if ( ! empty( $value ) ) {
                        $chosen_item->add_meta_data( '_'.$key, $value, true );
                    }
                }
            }

            // qty
            $chosen_item->set_quantity( $quantity );
            // price
            $chosen_item->set_subtotal( $price );
            // total
            $total = $quantity * $price;
            $chosen_item->set_total( $total );


            $chosen_item->save();

            //
            $wc_path = str_replace( 'uni-woo-custom-product-options/', 'woocommerce/', plugin_dir_path( __DIR__ ) );
            include( $wc_path . 'includes/admin/meta-boxes/views/html-order-items.php' );
            wp_die();

        }

        wp_send_json( $result );

    }

	/**
	*   uni_cpo_calculate_price_ajax
    */
    public static function uni_cpo_calculate_price_ajax() {

	    $aResult 		= self::r();

        $iProductId	    = ( !empty($_POST['uni_cpo_product_id']) ) ? esc_sql(intval($_POST['uni_cpo_product_id'])) : '';
        $aFormPostData  = $_POST;
        //print_r($aFormPostData);
        $sNonce         = ( !empty($_POST['uni_auth_nonce']) ) ? esc_sql($_POST['uni_auth_nonce']) : '';
        $sAntiCheat     = ( !empty($_POST['cheaters_always_disable_js']) ) ? esc_sql($_POST['cheaters_always_disable_js']) : '';

        if ( empty($sAntiCheat) || $sAntiCheat != 'true_bro' ) {
            wp_send_json( $aResult );
        }



        if ( ! empty( $iProductId ) ) {

            $oProduct           = wc_get_product( $iProductId );
            $aProductCustom     = get_post_custom( $iProductId );
            $extra_data         = array();

            if( isset($aProductCustom['_uni_cpo_price_calculation_enable'][0]) && $aProductCustom['_uni_cpo_price_calculation_enable'][0] == true ) {

                $sMainFormula           = ( !empty($aProductCustom['_uni_cpo_price_main_formula'][0]) ) ? $aProductCustom['_uni_cpo_price_main_formula'][0] : '';

                // create an array of all the variables and their values
                $iOptionsPostId     = ( !empty($aProductCustom['_uni_cpo_options_set'][0]) ) ? intval( $aProductCustom['_uni_cpo_options_set'][0] ) : 0;
                $aProductOptions    = get_post_meta( $iOptionsPostId, '_uni_cpo_options_structure', true );
                $is_non_option_wholesale = ( isset( $aProductCustom['_uni_cpo_non_option_vars_wholesale_enable'][0] ) && ! empty( $aProductCustom['_uni_cpo_non_option_vars_wholesale_enable'][0] ) ) ? true : false;
                $aArray             = array();
                $chosen_nice_names  = array();

                // regular or sale price?
                if ( empty( $aProductCustom['_sale_price'][0] ) ) {
                    // pick regular price
                    $sProductPrice = $aProductCustom['_regular_price'][0];
                } else {
                    // pick sale price
                    $sProductPrice = $aProductCustom['_sale_price'][0];
                }

                if ( !empty($aProductOptions) ) {
                    foreach ( $aProductOptions as $aElementStructure ) {
                        $option = uni_cpo_get_option( $aElementStructure['id'] );

                        if ( $option instanceof Uni_Cpo_Option && $option->get_id() && $option->is_calculable() ) {
                            $uCalcResult = $option->calculation( $aFormPostData );

                            $option_val  = $option->calculation( $aFormPostData, 'order' );
                            if ( is_array( $uCalcResult ) ) {
                                foreach ( $uCalcResult as $sVarName => $sVarValue ) {
                                    $aArray['{'.$sVarName.'}'] = $sVarValue;
                                    $chosen_nice_names[$sVarName] = $option_val;
                                }
                            } else {
                                $aArray[$option->post->post_title] = $uCalcResult;
                                $chosen_nice_names[$option->get_slug()] = $option_val;
                            }
                        }

                    }
                }

                
                
                // add {uni_cpo_price}
                $aArray['{uni_cpo_price}'] = $sProductPrice;
                //print_r($aArray);

                // non option variables
                if ( isset($aProductCustom['_uni_cpo_non_option_vars'][0]) && !empty($aProductCustom['_uni_cpo_non_option_vars'][0]) ) {

                    $non_option_vars = maybe_unserialize($aProductCustom['_uni_cpo_non_option_vars'][0]);
                    // add to the array of vars
                    $non_option_vars_processed = uni_cpo_process_formula_with_non_option_vars( $aArray, $non_option_vars, 'none', $is_non_option_wholesale );
                    $aArray = array_merge($aArray, $non_option_vars_processed);

                    // create and additional array to be used in conditional logic
                    $nov_values = uni_cpo_process_formula_with_non_option_vars( $aArray, $non_option_vars, 'conditional', $is_non_option_wholesale );

                }
                //print_r($aArray);

                //print_r($aFormPostData);
                // formula conditional logic evaluation
                if ( isset($aProductCustom['_uni_cpo_formula_conditional_enable'][0]) && $aProductCustom['_uni_cpo_formula_conditional_enable'][0] == true
                    && !empty($aProductCustom['_uni_cpo_formula_rule_options'][0]) ) {

                    if ( isset( $nov_values ) && ! empty( $nov_values ) ) {
                        $aFormPostData = array_merge( $aFormPostData, $nov_values );
                    }
                    //print_r($aFormPostData);
                    $sMainFormula = uni_cpo_process_formula_conditional_rules_scheme( $iProductId, 'check', '', '', $aFormPostData );

                }
                //print_r($sMainFormula);

                // a special word that prevents a product ordering when in use
                $is_ordering_disabled = false;
                if ( 'disable' === $sMainFormula ) {
                    $is_ordering_disabled = true;
                }

                $price_vars = array();

                // if not disabled
                if ( true !== $is_ordering_disabled ) {

                    // change vars into values
                    //print_r(' / formula before: ' . $sMainFormula);
                    $sMainFormula = uni_cpo_process_formula_with_vars( $sMainFormula, $aArray );
                    //print_r(' / formula after: ' . $sMainFormula);

                    // calculates formula
                    $fOrderPrice = uni_cpo_calculate_formula( $sMainFormula );

                    // debug
                    //print_r(' | calc price: '.$fOrderPrice);

                    $fMinPrice = ( !empty($aProductCustom['_uni_cpo_min_price'][0]) ) ? floatval( $aProductCustom['_uni_cpo_min_price'][0] ) : 0;
                    $fMaxPrice = ( !empty($aProductCustom['_uni_cpo_max_price'][0]) ) ? floatval( $aProductCustom['_uni_cpo_max_price'][0] ) : 0;

                    // the final price - compare with min. price if defined
                    if ( !empty($fMinPrice) && ( $fOrderPrice < $fMinPrice ) ) {
                        $fCalculatedPrice = $fMinPrice;
                    } else {
                        $fCalculatedPrice = $fOrderPrice;
                    }

                    // debug
                    //print_r('calc price: '.$fCalculatedPrice);

                    // max price
                    if ( ! empty( $fMaxPrice ) && $fCalculatedPrice >= $fMaxPrice ) {
                        $is_ordering_disabled = true;
                    }

                    // if not disabled
                    if ( true !== $is_ordering_disabled ) {

                        // user role/cat based discounts
                        $aRoleCatDiscountsRulesArray = '';
                        $aDiscCalculatedPrice = array();
                        $aProdCats = wp_get_post_terms( $iProductId, 'product_cat' );
                        if ( !empty($aProdCats) && !is_wp_error($aProdCats) ) {

                            $oCurrentUser = wp_get_current_user();
                            $sUserRole = ( !empty($oCurrentUser->roles) ) ? $oCurrentUser->roles[0] : '';

                            foreach ( $aProdCats as $oProdCat ) {
                                $aRoleCatDiscountsRulesArray = get_term_meta( $oProdCat->term_id, '_uni_cpo_tax_discounts', true );

                                if ( !empty($aRoleCatDiscountsRulesArray) && is_array($aRoleCatDiscountsRulesArray) ) {

                                    if ( !empty($sUserRole) && isset($aRoleCatDiscountsRulesArray[$sUserRole]['value']) ) {
                                        if ( $aRoleCatDiscountsRulesArray[$sUserRole]['type'] == 'percentage' ) {
                                            $aDiscCalculatedPrice[] = $fCalculatedPrice - $fCalculatedPrice * ($aRoleCatDiscountsRulesArray[$sUserRole]['value']/100);
                                        } else if ( $aRoleCatDiscountsRulesArray[$sUserRole]['type'] == 'amount' ) {
                                            $aDiscCalculatedPrice[] = $fCalculatedPrice - $aRoleCatDiscountsRulesArray[$sUserRole]['value'];
                                        } else if ( $aRoleCatDiscountsRulesArray[$sUserRole]['type'] == 'price' ) {
                                            $aDiscCalculatedPrice[] = floatval(str_replace(",", "", $aRoleCatDiscountsRulesArray[$sUserRole]['value']));
                                        }
                                    }

                                }

                            }

                            // debug
                            //print_r($aDiscCalculatedPrice);
                            if ( !empty($aDiscCalculatedPrice) ) {
                                $fCalculatedPrice = min($aDiscCalculatedPrice);
                            }
                        }

                        // filter, so 3rd party scripts can hook up
                        $fCalculatedPrice = apply_filters( 'uni_cpo_ajax_calculated_price', $fCalculatedPrice, $oProduct );

                        // debug
                        //print_r('calc price after role/cat discount: '.$fCalculatedPrice);
                        $fCalcProductDisplayPrice = wc_get_price_to_display( $oProduct, array('qty' => 1, 'price' => $fCalculatedPrice) );

                        if ( $oProduct->is_taxable() ) {
                            $fCalcProductDisplayPriceTaxReversed = uni_cpo_get_display_price_reversed( $oProduct, $fCalculatedPrice );
                            // Returns the price with suffix inc/excl tax opposite to one above
                            $sCalcProductPriceWithSuffix = $oProduct->get_price_suffix($fCalculatedPrice, 1);
                        }

                        // debug
                        //print_r('calculated and get_display_price: '.$fCalcProductDisplayPrice.' | ');
                        //print_r('calculated and price tax reversed: '.$fCalcProductDisplayPriceTaxReversed.' | ');
                        //print_r('calculated and get_price_suffix: '.$sCalcProductPriceWithSuffix.' | ');

                        // cart discounts rules showed in total
                        $aDiscountsRulesArray       = '';
                        $fProductPriceDiscounted    = '';
                        if ( !empty($aProductCustom['_uni_cpo_cart_discount_quantity'][0]) ) {
                            $aDiscountsRulesArray = maybe_unserialize($aProductCustom['_uni_cpo_cart_discount_quantity'][0]);
                        }

                        if ( isset($aProductCustom['_uni_cpo_cart_discounts_enable'][0]) && $aProductCustom['_uni_cpo_cart_discounts_enable'][0] == true
                            && !empty($aDiscountsRulesArray) && is_array($aDiscountsRulesArray) ) {

                            $cart_discounts_vars['{uni_cpo_quantity}']      = $aFormPostData['uni_cpo_quantity'];
                            $cart_discounts_vars['{uni_cpo_calc_price}']    = $fCalculatedPrice;

                            foreach ( $aDiscountsRulesArray as $sKey => $aRuleArray ) {

                                $cart_discounts_formula = '';

                                if ( $_POST['uni_cpo_quantity'] >= $aRuleArray['min'] && $_POST['uni_cpo_quantity'] <= $aRuleArray['max'] ) {

                                    $cart_discounts_formula = uni_cpo_process_cart_discounts_formula_with_vars( $aRuleArray['value'], $cart_discounts_vars );
                                    $calc_discount = uni_cpo_calculate_cart_discounts_formula( $cart_discounts_formula );

                                    if ( $aRuleArray['type'] == 'percentage' ) {
                                        $fProductPriceDiscounted = $fCalcProductDisplayPrice - $fCalcProductDisplayPrice * ($calc_discount/100);
                                        if ( $oProduct->is_taxable() ) {
                                            $fProductPriceDiscountedTaxReversed = $fCalcProductDisplayPriceTaxReversed - $fCalcProductDisplayPriceTaxReversed * ($calc_discount/100);
                                        }
                                    } else if ( $aRuleArray['type'] == 'amount' ) {
                                        $fProductPriceDiscounted = $fCalcProductDisplayPrice - $calc_discount;
                                        if ( $oProduct->is_taxable() ) {
                                            $fProductPriceDiscountedTaxReversed = $fCalcProductDisplayPriceTaxReversed - $calc_discount;
                                        }
                                    } else if ( $aRuleArray['type'] == 'price' ) {
                                        $fProductPriceDiscounted = $calc_discount;
                                        if ( $oProduct->is_taxable() ) {
                                            $fProductPriceDiscountedTaxReversed = uni_cpo_get_display_price_reversed( $oProduct, $calc_discount );
                                        }
                                    }
                                    break;
                                }

                            }

                        }

                        // debug
                        //print_r('discounted price: '.$fProductPriceDiscounted.' | ');
                        //print_r('discounted price tax reversed: '.$fProductPriceDiscountedTaxReversed.' | ');

                        // result
                        $aResult['status']  = 'success';
                        // price without html tags
                        $price_vars['price']   = apply_filters( 'uni_cpo_ajax_calculation_price_tag_filter', uni_cpo_price( $fCalcProductDisplayPrice ), $fCalcProductDisplayPrice );
                        if ( !empty($fProductPriceDiscounted) ) {
                            $price_vars['raw_price'] = $fProductPriceDiscounted;
                            $price_vars['raw_total'] = $price_vars['raw_price'] * $_POST['uni_cpo_quantity'];
                            $price_vars['total']   = uni_cpo_price( $price_vars['raw_total'] );
                            if ( $oProduct->is_taxable() ) {
                                $price_vars['raw_price_tax_rev'] = $fProductPriceDiscountedTaxReversed;
                                $price_vars['raw_total_tax_rev'] = $price_vars['raw_price_tax_rev'] * $_POST['uni_cpo_quantity'];
                                $price_vars['total_tax_rev']   = uni_cpo_price( $price_vars['raw_total_tax_rev'] );
                            }
                        } else {
                            $price_vars['raw_price'] = $fCalcProductDisplayPrice;
                            $price_vars['raw_total'] = $price_vars['raw_price'] * $_POST['uni_cpo_quantity'];
                            $price_vars['total']   = uni_cpo_price( $price_vars['raw_total'] );
                            if ( $oProduct->is_taxable() ) {
                                $price_vars['raw_price_tax_rev'] = $fCalcProductDisplayPriceTaxReversed;
                                $price_vars['raw_total_tax_rev'] = $price_vars['raw_price_tax_rev'] * $_POST['uni_cpo_quantity'];
                                $price_vars['total_tax_rev']   = uni_cpo_price( $price_vars['raw_total_tax_rev'] );
                            }
                        }

                        // debug
                        //print_r('total: ');
                        //print_r( $price_vars );

                        // price and total with suffixes
                        if ( $oProduct->is_taxable() ) {

                            // price with suffix - strips unnecessary
                            $sCalcProductPriceWithSuffix = str_replace( ' <small class="woocommerce-price-suffix">', '', $sCalcProductPriceWithSuffix );
                            $sCalcProductPriceWithSuffix = str_replace( ' </small>', '', $sCalcProductPriceWithSuffix );

                            // total with suffix
                            // creates 'with suffix' value for total
                            if ( get_option( 'woocommerce_prices_include_tax' ) === 'no' && get_option( 'woocommerce_tax_display_shop' ) == 'incl' ) {
                                $sTotalWithSuffix = $oProduct->get_price_suffix( $price_vars['raw_price_tax_rev'] * $_POST['uni_cpo_quantity'] );
                            } else if ( get_option( 'woocommerce_prices_include_tax' ) === 'yes' && get_option( 'woocommerce_tax_display_shop' ) == 'incl' ) {
                                $sTotalWithSuffix = $oProduct->get_price_suffix( $price_vars['raw_price'] * $_POST['uni_cpo_quantity'] );
                            } else if ( get_option( 'woocommerce_prices_include_tax' ) === 'no' && get_option( 'woocommerce_tax_display_shop' ) == 'excl' ) {
                                $sTotalWithSuffix = $oProduct->get_price_suffix( $price_vars['raw_price'] * $_POST['uni_cpo_quantity'] );
                            } else if ( get_option( 'woocommerce_prices_include_tax' ) === 'yes' && get_option( 'woocommerce_tax_display_shop' ) == 'excl' ) {
                                $sTotalWithSuffix = $oProduct->get_price_suffix( $price_vars['raw_price_tax_rev'] * $_POST['uni_cpo_quantity'] );
                            }

                            $sTotalWithSuffix = str_replace( ' <small class="woocommerce-price-suffix">', '', $sTotalWithSuffix );
                            $sTotalWithSuffix = str_replace( ' </small>', '', $sTotalWithSuffix );
                            $sTotalWithSuffix = str_replace( '<span class="amount">', '', $sTotalWithSuffix );
                            $sTotalWithSuffix = str_replace( '</span>', '', $sTotalWithSuffix );

                            // debug
                            //print_r('$sTotalWithSuffix: '.$sTotalWithSuffix.' | ');

                            $price_vars['price_suffix']   = $sCalcProductPriceWithSuffix;
                            $price_vars['total_suffix']   = $sTotalWithSuffix;

                        }
                        // if a discount is applied
                        if ( ! empty( $fProductPriceDiscounted ) ) {
                            $price_vars['price_discounted'] = uni_cpo_price( $fProductPriceDiscounted );
                        } else {
                            $price_vars['price_discounted'] = 0;
                        }

                    }

                }

                if ( true === $is_ordering_disabled ) {  // ordering is disabled

                    // result
                    $aResult['status']          = 'success';
                    $fCalcProductDisplayPrice   = 0;
                    $price_vars['price']        = apply_filters( 'uni_cpo_ajax_calculation_price_tag_disabled_filter', uni_cpo_price( $fCalcProductDisplayPrice ), $fCalcProductDisplayPrice );
                    $extra_data                 = array( 'order_product' => 'disabled' );

                } else {
                    $extra_data                 = array( 'order_product' => 'enabled' );
                }

                // output nov vars
                if ( isset( $nov_values ) ) {
                    $aResult['nov_vars'] = $nov_values;
                }

                // output nice names of choices in custom options
                $aResult['reg_vars'] = $chosen_nice_names;

                // output nice names of choices in custom options
                $aResult['price_vars'] = $price_vars;

            } else {
                $aResult['message'] = esc_html__('Price calculation for this product is disabled!', 'uni-cpo');
            }

        } else {
	        $aResult['message'] = esc_html__('Product ID is not set!', 'uni-cpo');
        }

        // extra data can be passed through the filter
        $aResult['extra_data'] = apply_filters( 'uni_cpo_ajax_calculation_extra_data_filter', $extra_data, $aResult, $aFormPostData );



        wp_send_json( $aResult );

    }

    /**
     *  Custom ajax handler for Quick Edit saving a post from a cpo options sets list table.
     *
     */
    function uni_cpo_inline_save() {
    	global $wp_list_table, $mode;

    	check_ajax_referer( 'inlineeditnonce', '_inline_edit' );

    	if ( ! isset($_POST['post_ID']) || ! ( $post_ID = (int) $_POST['post_ID'] ) )
    		wp_die();

    	if ( 'page' == $_POST['post_type'] ) {
    		if ( ! current_user_can( 'edit_page', $post_ID ) )
    			wp_die( __( 'You are not allowed to edit this page.' ) );
    	} else {
    		if ( ! current_user_can( 'edit_post', $post_ID ) )
    			wp_die( __( 'You are not allowed to edit this post.' ) );
    	}

    	if ( $last = wp_check_post_lock( $post_ID ) ) {
    		$last_user = get_userdata( $last );
    		$last_user_name = $last_user ? $last_user->display_name : __( 'Someone' );
    		printf( $_POST['post_type'] == 'page' ? __( 'Saving is disabled: %s is currently editing this page.' ) : __( 'Saving is disabled: %s is currently editing this post.' ),	esc_html( $last_user_name ) );
    		wp_die();
    	}

    	$data = &$_POST;

    	$post = get_post( $post_ID, ARRAY_A );

    	// Since it's coming from the database.
    	$post = wp_slash($post);

    	$data['content'] = $post['post_content'];
    	$data['excerpt'] = $post['post_excerpt'];

    	// Rename.
    	$data['user_ID'] = get_current_user_id();

    	if ( isset($data['post_parent']) )
    		$data['parent_id'] = $data['post_parent'];

    	// Status.
    	if ( isset( $data['keep_private'] ) && 'private' == $data['keep_private'] ) {
    		$data['visibility']  = 'private';
    		$data['post_status'] = 'private';
    	} else {
    		$data['post_status'] = $data['_status'];
    	}

    	if ( empty($data['comment_status']) )
    		$data['comment_status'] = 'closed';
    	if ( empty($data['ping_status']) )
    		$data['ping_status'] = 'closed';

    	// Hack: wp_unique_post_slug() doesn't work for drafts, so we will fake that our post is published.
    	if ( ! empty( $data['post_name'] ) && in_array( $post['post_status'], array( 'draft', 'pending' ) ) ) {
    		$post['post_status'] = 'publish';
    		$data['post_name'] = wp_unique_post_slug( $data['post_name'], $post['ID'], $post['post_status'], $post['post_type'], $post['post_parent'] );
    	}

    	// Update the post.
    	edit_post();

        /* cpo mod */
    	$wp_list_table = new Uni_Cpo_Option_Sets_List( array( 'screen' => $_POST['screen'] ) );
        /* end of cpo mod */

    	$mode = $_POST['post_view'] === 'excerpt' ? 'excerpt' : 'list';

    	$level = 0;
    	$request_post = array( get_post( $_POST['post_ID'] ) );
    	$parent = $request_post[0]->post_parent;

    	while ( $parent > 0 ) {
    		$parent_post = get_post( $parent );
    		$parent = $parent_post->post_parent;
    		$level++;
    	}

    	$wp_list_table->display_rows( array( get_post( $_POST['post_ID'] ) ), $level );

    	wp_die();
    }

	/**
	*   r()
    */
    protected static function r() {
        $aResult = array(
		    'status' 	=> 'error',
			'message' 	=> esc_html__('Error!', 'uni-cpo'),
			'redirect'	=> ''
		);
        return $aResult;
    }

}

Uni_Cpo_Ajax::init();

?>