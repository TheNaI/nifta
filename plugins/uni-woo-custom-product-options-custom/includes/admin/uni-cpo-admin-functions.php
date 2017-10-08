<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//////////////////////////////////////////////////////////////////////////////////////
// modal windows
//////////////////////////////////////////////////////////////////////////////////////
add_action('admin_footer', 'uni_cpo_admin_footer_product_function');
function uni_cpo_admin_footer_product_function() {
    $screen = get_current_screen();
    if ( $screen->post_type == 'product' ) {
	?>
<div class="cpo-option-modal" data-remodal-id="option-edit-modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">

    <form action="<?php echo esc_url( UniCpo()->ajax_url() ); ?>" method="post" class="uni_cpo_form">
        <input type="hidden" name="action" value="uni_cpo_field_settings_save" />
        <input type="hidden" name="uni_auth_nonce" value="<?php echo wp_create_nonce('uni_authenticate_nonce') ?>" />
        <input type="hidden" name="pid" value="<?php echo esc_attr( get_the_ID() ) ?>" />
        <input id="uni_cpo_modal_form_option_id" type="hidden" name="oid" value="" />
        <input id="uni_cpo_modal_form_option_type" type="hidden" name="otype" value="" />

        <h3><?php echo esc_html__('Edit settings', 'uni-cpo') ?></h3>

        <div id="cpo-option-modal-tabs">
        </div>

        <div class="cpo-modal-form-footer">
            <input class="uni_cpo_submit uni-cpo-settings-saved" name="uni_cpo_field_submit" type="button" value="<?php echo esc_html__('Save', 'uni-cpo') ?>" />
            <button data-remodal-action="cancel" class="remodal-cancel"><?php echo esc_html__('Close', 'uni-cpo') ?></button>
        </div>
    </form>

</div>

<div class="cpo-non-option-vars-modal" data-remodal-id="non-option-vars-modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">

    <form action="<?php echo esc_url( UniCpo()->ajax_url() ); ?>" method="post" class="uni_cpo_form">
        <input type="hidden" name="action" value="uni_cpo_non_option_vars_save" />
        <input type="hidden" name="uni_auth_nonce" value="<?php echo wp_create_nonce('uni_authenticate_nonce') ?>" />
        <input type="hidden" name="pid" value="<?php echo esc_attr( get_the_ID() ) ?>" />

        <div id="cpo_non_option_vars_container" class="cpo-non-option-vars-container">
        </div>

        <div class="cpo-modal-form-footer">
            <input class="uni_cpo_submit uni-cpo-settings-saved" name="uni_cpo_field_submit" type="button" value="<?php echo esc_html__('Save', 'uni-cpo') ?>" />
            <button data-remodal-action="cancel" class="remodal-cancel"><?php echo esc_html__('Close', 'uni-cpo') ?></button>
        </div>
    </form>

</div>

<div class="cpo-conditional-modal" data-remodal-id="conditional-modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">

    <form action="<?php echo esc_url( UniCpo()->ajax_url() ); ?>" method="post" class="uni_cpo_form">
        <input type="hidden" name="action" value="" />
        <input type="hidden" name="uni_auth_nonce" value="<?php echo wp_create_nonce('uni_authenticate_nonce') ?>" />
        <input type="hidden" name="pid" value="<?php echo esc_attr( get_the_ID() ) ?>" />

        <h3 id="js-cpo-conditional-rules-heading"></h3>

        <div id="js-cpo-conditional-rules-container"<?php /*class="cpo-formula-conditional-rules-container"*/ ?>>
        </div>

        <div class="cpo-modal-form-footer">
            <input class="uni_cpo_submit uni-cpo-settings-saved" name="uni_cpo_field_submit" type="button" value="<?php echo esc_html__('Save', 'uni-cpo') ?>" />
            <button data-remodal-action="cancel" class="remodal-cancel"><?php echo esc_html__('Close', 'uni-cpo') ?></button>
        </div>
    </form>

</div>

<div class="cpo-conditional-imex-modal" data-remodal-id="conditional-imex-modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">

    <form action="<?php echo esc_url( UniCpo()->ajax_url() ); ?>" method="post" class="uni_cpo_form">
        <input type="hidden" name="action" value="" />
        <input type="hidden" name="uni_auth_nonce" value="<?php echo wp_create_nonce('uni_authenticate_nonce') ?>" />
        <input type="hidden" name="pid" value="<?php echo esc_attr( get_the_ID() ) ?>" />

        <h3 id="js-cpo-conditional-rules-imex-heading"></h3>

        <div id="js-cpo-conditional-rules-imex-container"<?php /*class="cpo-formula-conditional-rules-imex-container"*/ ?>>
        </div>

        <div class="cpo-modal-form-footer">
            <input class="uni_cpo_submit uni-cpo-settings-saved" name="uni_cpo_field_submit" type="button" value="<?php echo esc_html__('Save', 'uni-cpo') ?>" />
            <button data-remodal-action="cancel" class="remodal-cancel"><?php echo esc_html__('Close', 'uni-cpo') ?></button>
        </div>
    </form>

</div>

<div class="cpo-discounts-modal" data-remodal-id="discounts-modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">

    <form action="<?php echo esc_url( UniCpo()->ajax_url() ); ?>" method="post" class="uni_cpo_form">
        <input type="hidden" name="action" value="uni_cpo_cart_discounts_save" />
        <input type="hidden" name="uni_auth_nonce" value="<?php echo wp_create_nonce('uni_authenticate_nonce') ?>" />
        <input type="hidden" name="pid" value="<?php echo esc_attr( get_the_ID() ) ?>" />
        <input id="uni_cpo_discounts_base" type="hidden" name="base" value="" />

        <div id="cpo_cart_discounts_container" class="cpo-cart-discounts-container">
        </div>

        <div class="cpo-modal-form-footer">
            <input class="uni_cpo_submit uni-cpo-settings-saved" name="uni_cpo_field_submit" type="button" value="<?php echo esc_html__('Save', 'uni-cpo') ?>" />
            <button data-remodal-action="cancel" class="remodal-cancel"><?php echo esc_html__('Close', 'uni-cpo') ?></button>
        </div>
    </form>

</div>

<div class="cpo-confirmation-modal" data-remodal-id="confirmation-modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">

    <form action="<?php echo esc_url( UniCpo()->ajax_url() ); ?>" method="post" class="uni_cpo_form">
        <input id="uni_cpo_modal_confirmation_action" type="hidden" name="action" value="" />
        <input type="hidden" name="uni_auth_nonce" value="<?php echo wp_create_nonce('uni_authenticate_nonce') ?>" />
        <input id="uni_cpo_modal_confirmation_pid" type="hidden" name="pid" value="<?php echo esc_attr( get_the_ID() ) ?>" />

        <h3><?php echo esc_html__('Confirm the operation', 'uni-cpo') ?></h3>

        <div id="cpo-confirmation-container">
        </div>

        <div class="cpo-modal-form-footer">
            <input class="uni_cpo_submit uni-cpo-settings-saved" name="uni_cpo_field_submit" type="button" value="<?php echo esc_html__('Confirm', 'uni-cpo') ?>" />
            <button data-remodal-action="cancel" class="remodal-cancel"><?php echo esc_html__('Cancel', 'uni-cpo') ?></button>
        </div>
    </form>

</div>

    <?php
    }
}

add_action('admin_footer', 'uni_cpo_admin_footer_order_function');
function uni_cpo_admin_footer_order_function() {
    $screen = get_current_screen();
    if ( $screen->post_type == 'shop_order' ) {
    ?>
<div class="cpo-order-options-modal" data-remodal-id="order-options-modal" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">

    <form action="<?php echo esc_url( UniCpo()->ajax_url() ); ?>" method="post" class="uni_cpo_form">
        <input type="hidden" name="action" value="uni_cpo_order_item_options_save" />
        <input type="hidden" name="uni_auth_nonce" value="<?php echo wp_create_nonce('uni_authenticate_nonce') ?>" />
        <input type="hidden" name="order_id" value="<?php echo esc_attr( get_the_ID() ) ?>" />
        <input id="uni_cpo_modal_form_order_item_id" type="hidden" name="item_id" value="" />
        <input id="uni_cpo_modal_form_order_product_id" type="hidden" name="uni_cpo_product_id" value="" />

        <h3><?php echo esc_html__('Add/Edit option(s)', 'uni-cpo') ?></h3>

        <div id="cpo_order_item_add_options_container">
        </div>

        <div class="cpo-modal-form-footer">
            <input class="uni_cpo_submit uni-cpo-settings-saved" name="uni_cpo_field_submit" type="button" value="<?php echo esc_html__('Save', 'uni-cpo') ?>" />
            <button data-remodal-action="cancel" class="remodal-cancel"><?php echo esc_html__('Close', 'uni-cpo') ?></button>
        </div>
    </form>

</div>
    <?php
    }
}

//////////////////////////////////////////////////////////////////////////////////////
// various functions
//////////////////////////////////////////////////////////////////////////////////////

function uni_cpo_download_suboptions_export_file(){
    if ( isset( $_GET['action'], $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], 'uni-cpo-suboptions-csv' ) && 'uni_cpo_download_suboptions_csv' === $_GET['action'] ) {
        include_once( UniCpo()->plugin_path() . '/includes/admin/uni-suboptions-csv-exporter.php' );
        $exporter = new Uni_Suboptions_CSV_Exporter();
        $exporter->export();
    }
}


add_action( 'admin_init', 'uni_cpo_download_suboptions_export_file' );

/**
 * Get an attachment ID given a URL.
 * 
 * @param string $url
 *
 * @return int Attachment ID on success, 0 on failure
 */
function uni_cpo_get_attachment_id( $url ) {
    $attachment_id = 0;
    $dir = wp_upload_dir();
    if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?
        $file = basename( $url );
        $query_args = array(
            'post_type'   => 'attachment',
            'post_status' => 'inherit',
            'fields'      => 'ids',
            'meta_query'  => array(
                array(
                    'value'   => $file,
                    'compare' => 'LIKE',
                    'key'     => '_wp_attachment_metadata',
                ),
            )
        );
        $query = new WP_Query( $query_args );
        if ( $query->have_posts() ) {
            foreach ( $query->posts as $post_id ) {
                $meta = wp_get_attachment_metadata( $post_id );
                $original_file       = basename( $meta['file'] );
                $cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
                if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
                    $attachment_id = $post_id;
                    break;
                }
            }
        }
    }
    return $attachment_id;
}

//
function uni_cpo_update_field_conditional_rules_scheme( $uOption, $sOldSlug = '', $sNewSlug = '' ) {

    if ( is_object($uOption) ) { // it is a parent option and all its children options schemes must be updated

        $aAllChildrenOptions = get_posts( array(
                            'post_type'  => 'uni_cpo_option',
                            'meta_query' => array(
    		                    array(
    			                    'key'     => '_uni_cpo_parent_option_id',
    			                    'value'   => intval($uOption->get_id()),
    			                    'compare' => '=',
                                    'type' => 'NUMERIC'
                		        ),
    	                    )
                    ) );

        if ( !empty($aAllChildrenOptions) ) {

            foreach ( $aAllChildrenOptions as $oPost ) {

                $oAllConditionalRules = '';
                // with 'true' it outputs an object, with 'false' it outputs array
                $oAllConditionalRules = get_post_meta($oPost->ID, '_uni_cpo_field_conditional_scheme', true);

                if ( isset( $oAllConditionalRules ) && !empty( $oAllConditionalRules ) ) {

                    foreach ( $oAllConditionalRules->rules as $Key => $oConditionBlock ) {

                        if ( isset($oConditionBlock->rules) ) {

                            $iFirstLevelCount = 0;
                            foreach ( $oConditionBlock->rules as $iFirstLevelRuleKey => $oFirstLevelRule ) {

                                if ( $oFirstLevelRule->id === $sOldSlug ) {
                                    $oAllConditionalRules->rules[$Key]->rules[$iFirstLevelCount]->id = $sNewSlug;
                                    $oAllConditionalRules->rules[$Key]->rules[$iFirstLevelCount]->field = $sNewSlug;
                                }

                                $iFirstLevelCount++;
                            }
                        } else {
                            if ( $oConditionBlock->id === $sOldSlug ) {
                                $oAllConditionalRules->rules[$Key]->id = $sNewSlug;
                                $oAllConditionalRules->rules[$Key]->field = $sNewSlug;
                            }
                        }

                    }

                }
                //print_r($oAllConditionalRules);
                update_post_meta($oPost->ID, '_uni_cpo_field_conditional_scheme', $oAllConditionalRules);

            }

        }

    } else if ( is_int( $uOption ) ) {  // it is an ID of children option which conditional scheme must be updated

                $iChildrenOptionId = intval($uOption);

                $oAllConditionalRules = '';
                // with 'true' it outputs an object, with 'false' it outputs array
                $oAllConditionalRules = get_post_meta($iChildrenOptionId, '_uni_cpo_field_conditional_scheme', true);

                if ( isset( $oAllConditionalRules ) && !empty( $oAllConditionalRules ) ) {
                    foreach ( $oAllConditionalRules->rules as $Key => $oConditionBlock ) {

                        if ( isset($oConditionBlock->rules) ) {

                            $iFirstLevelCount = 0;
                            foreach ( $oConditionBlock->rules as $iFirstLevelRuleKey => $oFirstLevelRule ) {

                                if ( $oFirstLevelRule->id === $sOldSlug ) {
                                    $oAllConditionalRules->rules[$Key]->rules[$iFirstLevelCount]->id = $sNewSlug;
                                    $oAllConditionalRules->rules[$Key]->rules[$iFirstLevelCount]->field = $sNewSlug;
                                }

                                $iFirstLevelCount++;
                            }

                        } else {

                            if ( $oConditionBlock->id === $sOldSlug ) {
                                $oAllConditionalRules->rules[$Key]->id = $sNewSlug;
                                $oAllConditionalRules->rules[$Key]->field = $sNewSlug;
                            }

                        }

                    }
                    //print_r($oAllConditionalRules);
                    update_post_meta($iChildrenOptionId, '_uni_cpo_field_conditional_scheme', $oAllConditionalRules);
                }

    }

}

//
function uni_cpo_generate_query_builder_filter_for_nov_var( $aNovItemArray ) {

    $aFilterArray = array(
        'id' => $aNovItemArray['slug'],
        'label' => '{' . UniCpo()->non_option_var_slug . $aNovItemArray['slug'] . '}',
        'type' => 'double',
        'input' => 'text',
        'operators' => array( 'less', 'less_or_equal', 'equal', 'not_equal', 'greater_or_equal', 'greater', 'is_empty', 'is_not_empty' )
    );
    return $aFilterArray;

}

//
function uni_cpo_list_of_formula_variables( $iId, $sPurpose = '', $iProductId = 0 ) {

        $sOutput = '<li class="uni-cpo-price-var"><span>{uni_cpo_price}</span></li>';
        $aFieldsInfo = array();
        $aProductOptions = array();
        $aNonOptionVarsArray = array();


            $iOptionId = intval($iId);
            $oOptionPost = get_post($iOptionId);

            // single option ID provided
            if ( is_object($oOptionPost) && $oOptionPost->post_parent !== 0 ) {
                $aProductOptions = get_post_meta( $oOptionPost->post_parent, '_uni_cpo_options_structure', true );
            } else if ( is_object($oOptionPost) && $oOptionPost->post_parent === 0 ) {
                // set of options ID provided
                $aProductOptions = get_post_meta( $oOptionPost->ID, '_uni_cpo_options_structure', true );
            }

            // add non option vars
            if ( $sPurpose === 'include_nov' && $iProductId !== 0 ) {
                $sNonOptionVarsArray = get_post_meta( $iProductId, '_uni_cpo_non_option_vars', true );
                if ( isset($sNonOptionVarsArray) && !empty($sNonOptionVarsArray) ) {
                    $aNonOptionVarsArray = maybe_unserialize($sNonOptionVarsArray);
                    foreach ( $aNonOptionVarsArray as $aVar ) {
                        $sOutput .= '<li class="uni-cpo-non-option-var"><span>' . '{' . UniCpo()->non_option_var_slug . $aVar['slug'].'}' . '</span></li>';
                    }
                }
            }

        // if $aProductOptions is already an array
        if ( is_array($aProductOptions) && !empty($aProductOptions) ) {
            $aStructure = $aProductOptions;

            foreach ( $aStructure as $aElementStructure ) {

                $oOption = uni_cpo_get_option( $aElementStructure['id'], $aElementStructure['itemtype'] );
           
                $aFieldsInfoItem = array();
                if ( $oOption instanceof Uni_Cpo_Option && $oOption->id ) {
                    $sOptionTitle = get_the_title( $oOption->get_id() );
                    if ( $oOption->is_calculable() != false ) {
                        $sOutput .= '<li><span>' . $sOptionTitle . '</span></li>';

                        if ( ! empty( $oOption->get_special_vars() ) ) {
                            foreach ( $oOption->get_special_vars() as $var_suffix ) {
                                $sOutput .= '<li class="uni-cpo-special-option-var"><span>' . rtrim( $sOptionTitle, '}' ) . '_' . $var_suffix . '}' . '</span></li>';
                            }
                        }
                    }
                    // this option is NOT child, so let's delete field conditional rules post meta
                    //delete_post_meta( $oOption->get_id(), '_uni_cpo_field_conditional_scheme' );
                    // this option is NOT child, so let's delete parent post id post meta
                    delete_post_meta( $oOption->get_id(), '_uni_cpo_parent_option_id' );
                    //
                    $aFieldsInfoItem['id'] = $oOption->get_id();
                    $aFieldsInfoItem['itemtype'] = $oOption->get_type();
                    $aFieldsInfoItem['parentid'] = $aElementStructure['parentid'];
                    $aFieldsInfoItem['title'] = $sOptionTitle;
                    $aFieldsInfoItem['icon'] = $oOption->get_icon();
                    $aFieldsInfoItem['required'] = $oOption->is_required();
                    $aFieldsInfoItem['rules'] = $oOption->has_fc_rules();
                }
                // children options  TODO remove in the next version
                if ( !empty($aElementStructure['children']) ) {
                    foreach ( $aElementStructure['children'] as $aChildElementStructure ) {

                        $oOption = uni_cpo_get_option( $aChildElementStructure['id'], $aChildElementStructure['itemtype'] );
                        if ( $oOption instanceof Uni_Cpo_Option && $oOption->id ) {
                            $sOptionTitle = get_the_title( $oOption->get_id() );
                            if ( $oOption->is_calculable() != false ) {
                                $sOutput .= '<li><span>' . $sOptionTitle . '</span></li>';

                                if ( ! empty( $oOption->get_special_vars() ) ) {
                                    foreach ( $oOption->get_special_vars() as $var_suffix ) {
                                        $sOutput .= '<li class="uni-cpo-special-option-var"><span>' . rtrim( $sOptionTitle, '}' ) . '_' . $var_suffix . '}' . '</span></li>';
                                    }
                                }

                            }
                            // this option is child now, but we have to check whether parent id is changed or not
                            // and we have to test if it has the same type as prev
                            // update slug in the scheme if it is the same type or delete
                            /*$iOldParentOptionId = get_post_meta( $oOption->get_id(), '_uni_cpo_parent_option_id', true );
                            if ( $iOldParentOptionId && $iOldParentOptionId != $aChildElementStructure['parentid'] ) {
                                $oOldParentOption = uni_cpo_get_option( $iOldParentOptionId );
                                $oNewParentOption = uni_cpo_get_option( $aChildElementStructure['parentid'] );
                                if ( is_object($oOldParentOption) && is_object($oNewParentOption) && $oOldParentOption->get_type() === $oNewParentOption->get_type() ) {
                                    uni_cpo_update_field_conditional_rules_scheme( $oOption->get_id(), $oOldParentOption->post->post_name, $oNewParentOption->post->post_name );
                                } else {
                                    delete_post_meta( $oOption->get_id(), '_uni_cpo_field_conditional_scheme' );
                                }
                            }*/
                            // this option is child, so let's add parent post id post meta
                            update_post_meta( $oOption->get_id(), '_uni_cpo_parent_option_id', $aChildElementStructure['parentid'] );
                            //
                            $aChildrenFieldsInfoItem = array();
                            $aChildrenFieldsInfoItem['id'] = $oOption->get_id();
                            $aChildrenFieldsInfoItem['itemtype'] = $oOption->get_type();
                            $aChildrenFieldsInfoItem['parentid'] = $aChildElementStructure['parentid'];
                            $aChildrenFieldsInfoItem['title'] = $sOptionTitle;
                            $aChildrenFieldsInfoItem['icon'] = $oOption->get_icon();
                            $aChildrenFieldsInfoItem['required'] = $oOption->is_required();
                            $aChildrenFieldsInfoItem['rules'] = $oOption->has_fc_rules();
                            $aFieldsInfoItem['children'][] = $aChildrenFieldsInfoItem;
                        }
                    }
                }
                if ( !empty($aFieldsInfoItem) ) {
                    $aFieldsInfo[] = $aFieldsInfoItem;
                }

            }
        }

        if ( $sPurpose === 'fields_structure' ) {
            return $aFieldsInfo;
        } else {
            return $sOutput;
        }
}


//////////////////////////////////////////////////////////////////////////////////////
// back end options settings - type of form elements
//////////////////////////////////////////////////////////////////////////////////////
// option settings field - text input
function uni_cpo_admin_text_input( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    if ( $aArgs['name'] == 'field_slug' ) {
        $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : $oOption->post->post_name;
    } else {
        $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true );
    }
    $aArgs['type'] = isset( $aArgs['type'] ) ? $aArgs['type'] : 'text';
    $aArgs['placeholder'] = isset( $aArgs['placeholder'] ) ? $aArgs['placeholder'] : '';
    $aArgs['validation_pattern'] = isset( $aArgs['validation_pattern'] ) ? $aArgs['validation_pattern'] : '';
    $aArgs['validation_type'] = isset( $aArgs['validation_type'] ) ? $aArgs['validation_type'] : '';
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';
    $aArgs['strip_slashes'] = ( isset( $aArgs['strip_slashes'] ) && isset( $aArgs['strip_slashes'] ) === true ) ? true : false;

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= uni_cpo_admin_setting_leftcell( $aArgs );
        $sOutput .= '<div class="cpo-modal-field-rightcell">';
            if ( $aArgs['strip_slashes'] ) {
                $sOutput .= '<input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '" value="' . esc_attr( stripslashes_deep( $aArgs['value'] ) ) . '" class="uni-cpo-modal-field' . ( ( $aArgs['required'] ) ? ' uni-cpo-field-required' : '' ) . '"' . ( ( $aArgs['required'] ) ? ' data-parsley-required="true" data-parsley-trigger="change focusout submit"' : '' ) . ( ( !empty( $aArgs['validation_pattern'] ) ) ? ' data-parsley-pattern="' . $aArgs['validation_pattern'] . '"' : '' ) . ( ( !empty( $aArgs['validation_type'] ) ) ? ' data-parsley-type="' . $aArgs['validation_type'] . '"' : '' ) . ( ( !empty( $aArgs['placeholder'] ) ) ? ' placeholder="' . esc_attr( $aArgs['placeholder'] ) . '"' : '' ) . ' />';
            } else {
                $sOutput .= '<input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '" value="' . esc_attr( $aArgs['value'] ) . '" class="uni-cpo-modal-field' . ( ( $aArgs['required'] ) ? ' uni-cpo-field-required' : '' ) . '"' . ( ( $aArgs['required'] ) ? ' data-parsley-required="true" data-parsley-trigger="change focusout submit"' : '' ) . ( ( !empty( $aArgs['validation_pattern'] ) ) ? ' data-parsley-pattern="' . $aArgs['validation_pattern'] . '"' : '' ) . ( ( !empty( $aArgs['validation_type'] ) ) ? ' data-parsley-type="' . $aArgs['validation_type'] . '"' : '' ) . ( ( !empty( $aArgs['placeholder'] ) ) ? ' placeholder="' . esc_attr( $aArgs['placeholder'] ) . '"' : '' ) . ' />';
            }
        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - text input
function uni_cpo_admin_textarea( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    if ( $aArgs['name'] == 'field_slug' ) {
        $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : $oOption->post->post_name;
    } else {
        $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true );
    }
    $aArgs['type'] = isset( $aArgs['type'] ) ? $aArgs['type'] : 'text';
    $aArgs['placeholder'] = isset( $aArgs['placeholder'] ) ? $aArgs['placeholder'] : '';
    $aArgs['validation_pattern'] = isset( $aArgs['validation_pattern'] ) ? $aArgs['validation_pattern'] : '';
    $aArgs['validation_type'] = isset( $aArgs['validation_type'] ) ? $aArgs['validation_type'] : '';
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';

    $aAllowedHtml = uni_cpo_allowed_html_for_tooltips();

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= uni_cpo_admin_setting_leftcell( $aArgs );
        $sOutput .= '<div class="cpo-modal-field-rightcell">';
            $sOutput .= '<textarea name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '" class="uni-cpo-modal-field' . ( ( $aArgs['required'] ) ? ' uni-cpo-field-required' : '' ) . '"' . ( ( $aArgs['required'] ) ? ' data-parsley-required="true" data-parsley-trigger="change focusout submit"' : '' ) . ( ( !empty( $aArgs['validation_pattern'] ) ) ? ' data-parsley-pattern="' . $aArgs['validation_pattern'] . '"' : '' ) . ( ( !empty( $aArgs['validation_type'] ) ) ? ' data-parsley-type="' . $aArgs['validation_type'] . '"' : '' ) . ( ( !empty( $aArgs['placeholder'] ) ) ? ' placeholder="' . esc_attr( $aArgs['placeholder'] ) . '"' : '' ) . '>' . wp_kses( $aArgs['value'], $aAllowedHtml ) . '</textarea>';
        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// textarea_js
function uni_cpo_admin_textarea_js( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    if ( $aArgs['name'] == 'field_slug' ) {
        $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : $oOption->post->post_name;
    } else {
        $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true );
    }
    $aArgs['type'] = isset( $aArgs['type'] ) ? $aArgs['type'] : 'text';
    $aArgs['placeholder'] = isset( $aArgs['placeholder'] ) ? $aArgs['placeholder'] : '';
    $aArgs['validation_pattern'] = isset( $aArgs['validation_pattern'] ) ? $aArgs['validation_pattern'] : '';
    $aArgs['validation_type'] = isset( $aArgs['validation_type'] ) ? $aArgs['validation_type'] : '';
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= uni_cpo_admin_setting_leftcell( $aArgs );
        $sOutput .= '<div class="cpo-modal-field-rightcell">';
            $sOutput .= '<textarea name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '" class="uni-cpo-modal-field' . ( ( $aArgs['required'] ) ? ' uni-cpo-field-required' : '' ) . '"' . ( ( $aArgs['required'] ) ? ' data-parsley-required="true" data-parsley-trigger="change focusout submit"' : '' ) . ( ( !empty( $aArgs['validation_pattern'] ) ) ? ' data-parsley-pattern="' . $aArgs['validation_pattern'] . '"' : '' ) . ( ( !empty( $aArgs['validation_type'] ) ) ? ' data-parsley-type="' . $aArgs['validation_type'] . '"' : '' ) . ( ( !empty( $aArgs['placeholder'] ) ) ? ' placeholder="' . esc_attr( $aArgs['placeholder'] ) . '"' : '' ) . '>' . $aArgs['value'] . '</textarea>';
        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - select
function uni_cpo_admin_select( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true );
    $aArgs['options'] = isset( $aArgs['options'] ) ? $aArgs['options'] : array();
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= uni_cpo_admin_setting_leftcell( $aArgs );
        $sOutput .= '<div class="cpo-modal-field-rightcell">';
            $sOutput .= '<select name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '" class="uni-cpo-modal-field' . ( ( $aArgs['required'] ) ? ' uni-cpo-field-required' : '' ) . '"' . ( ( $aArgs['required'] ) ? ' data-parsley-required="true" data-parsley-trigger="change focusout submit"' : '' ) . '>';
            if ( !empty( $aArgs['options'] ) && is_array( $aArgs['options'] ) ) {
                foreach ( $aArgs['options'] as $key => $value ) {
                    $sOutput .= '<option value="' . esc_attr( $key ) . '" ' . selected( esc_attr( $aArgs['value'] ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
                }
            }
            $sOutput .= '</select>';
        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - checkboxes
function uni_cpo_admin_checkboxes( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true );
    $aArgs['value'] = ( get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) ) ? get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) : '';
    $aArgs['value'] = maybe_unserialize( $aArgs['value'] );
    $aArgs['options'] = isset( $aArgs['options'] ) ? $aArgs['options'] : array();
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= uni_cpo_admin_setting_leftcell( $aArgs );
        $sOutput .= '<div class="cpo-modal-field-rightcell">';
            if ( !empty( $aArgs['options'] ) && is_array( $aArgs['options'] ) ) {
                foreach ( $aArgs['options'] as $key => $value ) {
                    $sOutput .= '<label class="checkbox-label"><input type="checkbox" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[' . $key . ']" value="' . esc_attr( $key ) . '" ' . checked( esc_attr( $aArgs['value'][$key] ), esc_attr( $key ), false ) . ' class="uni-cpo-modal-field' . ( ( $aArgs['required'] ) ? ' uni-cpo-field-required' : '' ) . '" />' . esc_html( $value ) . '</label>';
                }
            }
        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - select_options for select
function uni_cpo_admin_select_options( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $sSerializedValues = ( get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) ) ? get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) : '';
    $aArrayOfValues = maybe_unserialize( $sSerializedValues );

    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    $aArgs['options'] = isset( $aArgs['options'] ) ? $aArgs['options'] : array();
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= '<div class="cpo-modal-field-fullwidth">';

            $sOutput .= '<label>' . esc_html( $aArgs['title'] ) . ( ( $aArgs['required'] ) ? '<span class="cpo-modal-field-required">*</span>' : '' ) . '</label>';
            if ( $aArgs['tip'] ) {
                $sOutput .= '<span class="uni_help_tip" data-tip="'.$aArgs['tip'].'"><i class="fa fa-question-circle"></i></span>';
            }
            if ( $aArgs['doc_link'] ) {
                $sOutput .= uni_cpo_get_doc_link( $aArgs['doc_link'] );
            }
            $sOutput .= ( $aArgs['description'] ) ? '<p class="cpo-modal-field-description">' . esc_html( $aArgs['description'] ) . '</p>' : '';

    $sOutput .= '<div class="uni-select-option-repeat">
        <div class="uni-select-option-repeat-wrapper">
            <div class="uni-select-option-add-wrapper">
                <span class="uni_select_option_add">'.esc_html__('Add', 'uni-cpo').'</span>
            </div>';

            $sOutput .= uni_cpo_import_export_html( $aArgs['name'] );

            $sOutput .= '<div class="uni-select-option-options-wrapper">';

            $bEmptySuboptions = false;
            $aNullValues = 0;
            if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {
                    if ( $aValue === NULL ) {
                        $aNullValues++;
                    }
                }
            }

            if ( $aNullValues !== 0 && count($aNullValues) === count($aArrayOfValues) ) {
                $bEmptySuboptions = true;
            }

            if ( !$bEmptySuboptions ) {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label></label>
                                            <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][default]" class="uni-cpo-modal-field-default-deselectable" value="{{row-count}}" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                            <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('Slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                            <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                            <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][price]" value="" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label class="uni_help_tip" data-tip="'.esc_attr__('The main product image can be changed with this one', 'uni-cpo').'">'.esc_html__('Product image', 'uni-cpo').'</label>
                                            <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][image_id]" value="" />
                                            <button type="button" class="upload_image_button uni_help_tip" data-tip="'.esc_attr__('Add/Edit image', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                            <button type="button" class="remove_image_button uni_help_tip" data-tip="'.esc_attr__('Remove image', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
                $i = 0;

                if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {

                    if( $aValue === NULL ) {
                        continue;
                    }

                    $sOutput .= '<div class="uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label></label>
                                            <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][default]" class="uni-cpo-modal-field-default-deselectable" value="'.esc_attr($i).'"'.checked($i, (( isset($aValue['default']) ) ? $aValue['default'] : ''), false).' />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                            <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['label'] ) ) ? esc_attr($aValue['label']) : '' ).'" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                            <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['slug'] ) ) ? esc_attr($aValue['slug']) : '' ).'" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                            <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][price]" value="'.( ( isset( $aValue['price'] ) ) ? esc_attr($aValue['price']) : '' ).'" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label class="uni_help_tip" data-tip="'.esc_attr__('The main product image can be changed with this one', 'uni-cpo').'">'.esc_html__('Product image', 'uni-cpo').'</label>
                                            <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][image_id]" value="'.( ( !empty( $aValue['image_id'] ) ) ? esc_attr($aValue['image_id']) : '' ).'" />
                                            <button type="button" class="upload_image_button uni_help_tip" data-tip="'.esc_attr__('Add/Edit image', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                            <button type="button" class="remove_image_button uni_help_tip" data-tip="'.esc_attr__('Remove image', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
                    $i++;

                }
                }
            } else {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label></label>
                                            <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][default]" class="uni-cpo-modal-field-default-deselectable" value="{{row-count}}" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('Label', 'uni-cpo').'</label>
                                            <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('Option slug', 'uni-cpo').'</label>
                                            <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                            <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][price]" value="" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label class="uni_help_tip" data-tip="'.esc_attr__('The main product image can be changed with this one', 'uni-cpo').'">'.esc_html__('Product image', 'uni-cpo').'</label>
                                            <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][image_id]" value="" />
                                            <button type="button" class="upload_image_button uni_help_tip" data-tip="'.esc_attr__('Add/Edit image', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                            <button type="button" class="remove_image_button uni_help_tip" data-tip="'.esc_attr__('Remove image', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
            }

            $sOutput .= '</div>
                </div>
            </div>';

        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - select_options for chekboxes
function uni_cpo_admin_checkboxes_options( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $sSerializedValues = ( get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) ) ? get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) : '';
    $aArrayOfValues = maybe_unserialize( $sSerializedValues );

    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    $aArgs['options'] = isset( $aArgs['options'] ) ? $aArgs['options'] : array();
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= '<div class="cpo-modal-field-fullwidth">';

            $sOutput .= '<label>' . esc_html( $aArgs['title'] ) . ( ( $aArgs['required'] ) ? '<span class="cpo-modal-field-required">*</span>' : '' ) . '</label>';
            if ( $aArgs['tip'] ) {
                $sOutput .= '<span class="uni_help_tip" data-tip="'.$aArgs['tip'].'"><i class="fa fa-question-circle"></i></span>';
            }
            if ( $aArgs['doc_link'] ) {
                $sOutput .= uni_cpo_get_doc_link( $aArgs['doc_link'] );
            }
            $sOutput .= ( $aArgs['description'] ) ? '<p class="cpo-modal-field-description">' . esc_html( $aArgs['description'] ) . '</p>' : '';

    $sOutput .= '<div class="uni-select-option-repeat">
        <div class="uni-select-option-repeat-wrapper">
            <div class="uni-select-option-add-wrapper">
                <span class="uni_select_option_add">'.esc_html__('Add', 'uni-cpo').'</span>
            </div>';

            $sOutput .= uni_cpo_import_export_html( $aArgs['name'] );

            $sOutput .= '<div class="uni-select-option-options-wrapper">';

            $bEmptySuboptions = false;
            $aNullValues = 0;
            if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {
                    if ( $aValue === NULL ) {
                        $aNullValues++;
                    }
                }
            }

            if ( $aNullValues !== 0 && count($aNullValues) === count($aArrayOfValues) ) {
                $bEmptySuboptions = true;
            }

            if ( !$bEmptySuboptions ) {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="checkbox" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][default]" value="{{row-count}}" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][price]" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Tooltip text', 'uni-cpo').'</label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][tooltip]" class="uni-cpo-modal-field uni-cpo-select-option-field-tooltip" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label class="uni_help_tip" data-tip="'.esc_attr__('This image will be shown next to this suboption', 'uni-cpo').'">'.esc_html__('Suboption image', 'uni-cpo').'</label>
                                                <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][image_id]" value="" />
                                                <button type="button" class="upload_image_button uni_help_tip" data-tip="'.esc_attr__('Add/Edit image', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                                <button type="button" class="remove_image_button uni_help_tip" data-tip="'.esc_attr__('Remove image', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
                $i = 0;

                if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {

                    if( $aValue === NULL ) {
                        continue;
                    }

                    $sOutput .= '<div class="uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="checkbox" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][default]" value="'.esc_attr($i).'"'.checked(true, ( isset($aValue['default']) ) ? true : false, false).' />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['label'] ) ) ? esc_attr($aValue['label']) : '' ).'" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['slug'] ) ) ? esc_attr($aValue['slug']) : '' ).'" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][price]" value="'.( ( isset( $aValue['price'] ) ) ? esc_attr($aValue['price']) : '' ).'" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Tooltip text', 'uni-cpo').'</label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][tooltip]" class="uni-cpo-modal-field uni-cpo-select-option-field-tooltip" value="'.( ( !empty( $aValue['tooltip'] ) ) ? esc_attr($aValue['tooltip']) : '' ).'" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label class="uni_help_tip" data-tip="'.esc_attr__('This image will be shown next to this suboption', 'uni-cpo').'">'.esc_html__('Suboption image', 'uni-cpo').'</label>
                                                <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][image_id]" value="'.( ( !empty( $aValue['image_id'] ) ) ? esc_attr($aValue['image_id']) : '' ).'" />
                                                <button type="button" class="upload_image_button uni_help_tip" data-tip="'.esc_attr__('Add/Edit image', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                                <button type="button" class="remove_image_button uni_help_tip" data-tip="'.esc_attr__('Remove image', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
                    $i++;

                }
                }
            } else {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="checkbox" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][default]" value="{{row-count}}" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][price]" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Tooltip text', 'uni-cpo').'</label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][tooltip]" class="uni-cpo-modal-field uni-cpo-select-option-field-tooltip" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label class="uni_help_tip" data-tip="'.esc_attr__('This image will be shown next to this suboption', 'uni-cpo').'">'.esc_html__('Suboption image', 'uni-cpo').'</label>
                                                <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][image_id]" value="" />
                                                <button type="button" class="upload_image_button uni_help_tip" data-tip="'.esc_attr__('Add/Edit image', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                                <button type="button" class="remove_image_button uni_help_tip" data-tip="'.esc_attr__('Remove image', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
            }

            $sOutput .= '</div>
                </div>
            </div>';

        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - select_options for radio
function uni_cpo_admin_radio_options( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $sSerializedValues = ( get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) ) ? get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) : '';
    $aArrayOfValues = maybe_unserialize( $sSerializedValues );

    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    $aArgs['options'] = isset( $aArgs['options'] ) ? $aArgs['options'] : array();
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= '<div class="cpo-modal-field-fullwidth">';

            $sOutput .= '<label>' . esc_html( $aArgs['title'] ) . ( ( $aArgs['required'] ) ? '<span class="cpo-modal-field-required">*</span>' : '' ) . '</label>';
            if ( $aArgs['tip'] ) {
                $sOutput .= '<span class="uni_help_tip" data-tip="'.$aArgs['tip'].'"><i class="fa fa-question-circle"></i></span>';
            }
            if ( $aArgs['doc_link'] ) {
                $sOutput .= uni_cpo_get_doc_link( $aArgs['doc_link'] );
            }
            $sOutput .= ( $aArgs['description'] ) ? '<p class="cpo-modal-field-description">' . esc_html( $aArgs['description'] ) . '</p>' : '';

    $sOutput .= '<div class="uni-select-option-repeat">
        <div class="uni-select-option-repeat-wrapper">
            <div class="uni-select-option-add-wrapper">
                <span class="uni_select_option_add">'.esc_html__('Add', 'uni-cpo').'</span>
            </div>';

            $sOutput .= uni_cpo_import_export_html( $aArgs['name'] );

            $sOutput .= '<div class="uni-select-option-options-wrapper">';

            $bEmptySuboptions = false;
            $aNullValues = 0;
            if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {
                    if ( $aValue === NULL ) {
                        $aNullValues++;
                    }
                }
            }

            if ( $aNullValues !== 0 && count($aNullValues) === count($aArrayOfValues) ) {
                $bEmptySuboptions = true;
            }

            if ( !$bEmptySuboptions ) {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row option-type-' . esc_attr( $oOption->get_type() ) . '">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][default]" class="uni-cpo-modal-field-default-deselectable" value="{{row-count}}" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][price]" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Tooltip text', 'uni-cpo').'</label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][tooltip]" class="uni-cpo-modal-field uni-cpo-select-option-field-tooltip" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label class="uni_help_tip" data-tip="'.esc_attr__('This image will be shown next to this suboption', 'uni-cpo').'">'.esc_html__('Suboption image', 'uni-cpo').'</label>
                                                <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][image_id]" value="" />
                                                <button type="button" class="upload_image_button uni_help_tip" data-tip="'.esc_attr__('Add/Edit image', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                                <button type="button" class="remove_image_button uni_help_tip" data-tip="'.esc_attr__('Remove image', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';

                $i = 0;

                if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {

                    if( $aValue === NULL ) {
                        continue;
                    }

                    $sOutput .= '<div class="uni-select-option-options-row option-type-' . esc_attr( $oOption->get_type() ) . '">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][default]" class="uni-cpo-modal-field-default-deselectable" value="'.esc_attr($i).'"'.checked($i, (( isset($aValue['default']) ) ? $aValue['default'] : ''), false).' />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['label'] ) ) ? esc_attr($aValue['label']) : '' ).'" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['slug'] ) ) ? esc_attr($aValue['slug']) : '' ).'" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][price]" value="'.( ( isset( $aValue['price'] ) ) ? esc_attr($aValue['price']) : '' ).'" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Tooltip text', 'uni-cpo').'</label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][tooltip]" class="uni-cpo-modal-field uni-cpo-select-option-field-tooltip" value="'.( ( !empty( $aValue['tooltip'] ) ) ? esc_attr($aValue['tooltip']) : '' ).'" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label class="uni_help_tip" data-tip="'.esc_attr__('This image will be shown next to this suboption', 'uni-cpo').'">'.esc_html__('Suboption image', 'uni-cpo').'</label>
                                                <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][image_id]" value="'.( ( !empty( $aValue['image_id'] ) ) ? esc_attr($aValue['image_id']) : '' ).'" />
                                                <button type="button" class="upload_image_button uni_help_tip" data-tip="'.esc_attr__('Add/Edit image', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                                <button type="button" class="remove_image_button uni_help_tip" data-tip="'.esc_attr__('Remove image', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
                    $i++;

                }
                }
            } else {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row option-type-' . esc_attr( $oOption->get_type() ) . '">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][default]" class="uni-cpo-modal-field-default-deselectable" value="{{row-count}}" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][price]" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Tooltip text', 'uni-cpo').'</label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][tooltip]" class="uni-cpo-modal-field uni-cpo-select-option-field-tooltip" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label class="uni_help_tip" data-tip="'.esc_attr__('This image will be shown next to this suboption', 'uni-cpo').'">'.esc_html__('Suboption image', 'uni-cpo').'</label>
                                                <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][image_id]" value="" />
                                                <button type="button" class="upload_image_button uni_help_tip" data-tip="'.esc_attr__('Add/Edit image', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                                <button type="button" class="remove_image_button uni_help_tip" data-tip="'.esc_attr__('Remove image', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
            }

            $sOutput .= '</div>
                </div>
            </div>';

        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - palette_select for palette select
function uni_cpo_admin_palette_select( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $sSerializedValues = ( get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) ) ? get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) : '';
    $aArrayOfValues = maybe_unserialize( $sSerializedValues );

    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    $aArgs['options'] = isset( $aArgs['options'] ) ? $aArgs['options'] : array();
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= '<div class="cpo-modal-field-fullwidth">';

            $sOutput .= '<label>' . esc_html( $aArgs['title'] ) . ( ( $aArgs['required'] ) ? '<span class="cpo-modal-field-required">*</span>' : '' ) . '</label>';
            if ( $aArgs['tip'] ) {
                $sOutput .= '<span class="uni_help_tip" data-tip="'.$aArgs['tip'].'"><i class="fa fa-question-circle"></i></span>';
            }
            if ( $aArgs['doc_link'] ) {
                $sOutput .= uni_cpo_get_doc_link( $aArgs['doc_link'] );
            }
            $sOutput .= ( $aArgs['description'] ) ? '<p class="cpo-modal-field-description">' . esc_html( $aArgs['description'] ) . '</p>' : '';

    $sOutput .= '<div class="uni-select-option-repeat">
        <div class="uni-select-option-repeat-wrapper">
            <div class="uni-select-option-add-wrapper">
                <span class="uni_select_option_add">'.esc_html__('Add', 'uni-cpo').'</span>
            </div>';

            $sOutput .= uni_cpo_import_export_html( $aArgs['name'] );

            $sOutput .= '<div class="uni-select-option-options-wrapper">';

            $bEmptySuboptions = false;
            $aNullValues = 0;
            if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {
                    if ( $aValue === NULL ) {
                        $aNullValues++;
                    }
                }
            }

            if ( $aNullValues !== 0 && count($aNullValues) === count($aArrayOfValues) ) {
                $bEmptySuboptions = true;
            }

            if ( !$bEmptySuboptions ) {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][default]" class="uni-cpo-modal-field-default-deselectable" value="{{row-count}}" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][price]" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Choose a colour', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][color]" class="uni-cpo-modal-field uni-cpo-palette-option-field-color" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
                $i = 0;

                if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {

                    if( $aValue === NULL ) {
                        continue;
                    }

                    $aValue['color'] = strpos($aValue['color'], '#') === false ? '#' . $aValue['color'] : $aValue['color'];

                    $sOutput .= '<div class="uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][default]" class="uni-cpo-modal-field-default-deselectable" value="'.esc_attr($i).'"'.checked($i, (( isset($aValue['default']) ) ? $aValue['default'] : ''), false).' />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['label'] ) ) ? esc_attr($aValue['label']) : '' ).'" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['slug'] ) ) ? esc_attr($aValue['slug']) : '' ).'" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][price]" value="'.( ( isset( $aValue['price'] ) ) ? esc_attr($aValue['price']) : '' ).'" />
                                            </div>

                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Choose a colour', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][color]" class="uni-cpo-modal-field uni-cpo-palette-option-field-color" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['color'] ) ) ? esc_attr($aValue['color']) : '' ).'" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
                    $i++;

                }
                }
            } else {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][default]" class="uni-cpo-modal-field-default-deselectable" value="{{row-count}}" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][price]" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Choose a colour', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][color]" class="uni-cpo-modal-field uni-cpo-palette-option-field-color" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
            }

            $sOutput .= '</div>
                </div>
            </div>';

        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - image_select for image select
function uni_cpo_admin_image_select( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $sSerializedValues = ( get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) ) ? get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) : '';
    $aArrayOfValues = maybe_unserialize( $sSerializedValues );

    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    $aArgs['options'] = isset( $aArgs['options'] ) ? $aArgs['options'] : array();
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= '<div class="cpo-modal-field-fullwidth">';

            $sOutput .= '<label>' . esc_html( $aArgs['title'] ) . ( ( $aArgs['required'] ) ? '<span class="cpo-modal-field-required">*</span>' : '' ) . '</label>';
            if ( $aArgs['tip'] ) {
                $sOutput .= '<span class="uni_help_tip" data-tip="'.$aArgs['tip'].'"><i class="fa fa-question-circle"></i></span>';
            }
            if ( $aArgs['doc_link'] ) {
                $sOutput .= uni_cpo_get_doc_link( $aArgs['doc_link'] );
            }
            $sOutput .= ( $aArgs['description'] ) ? '<p class="cpo-modal-field-description">' . esc_html( $aArgs['description'] ) . '</p>' : '';

    $sOutput .= '<div class="uni-select-option-repeat">
        <div class="uni-select-option-repeat-wrapper">
            <div class="uni-select-option-add-wrapper">
                <span class="uni_select_option_add">'.esc_html__('Add', 'uni-cpo').'</span>
            </div>';

            $sOutput .= uni_cpo_import_export_html( $aArgs['name'] );

            $sOutput .= '<div class="uni-select-option-options-wrapper">';

            $bEmptySuboptions = false;
            $aNullValues = 0;
            if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {
                    if ( $aValue === NULL ) {
                        $aNullValues++;
                    }
                }
            }

            if ( $aNullValues !== 0 && count($aNullValues) === count($aArrayOfValues) ) {
                $bEmptySuboptions = true;
            }

            if ( !$bEmptySuboptions ) {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][default]" class="uni-cpo-modal-field-default-deselectable" value="{{row-count}}" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][price]" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label class="uni_help_tip" data-tip="'.esc_attr__('This image will be used as an image of suboption', 'uni-cpo').'">'.esc_html__('Main image', 'uni-cpo').'</label>
                                                <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][image_id]" value="" />
                                                <button type="button" class="upload_image_button uni_help_tip" data-tip="'.esc_attr__('Add/Edit image', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                                <button type="button" class="remove_image_button uni_help_tip" data-tip="'.esc_attr__('Remove image', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
                $i = 0;

                if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {

                    if( $aValue === NULL ) {
                        continue;
                    }

                    $sOutput .= '<div class="uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][default]" class="uni-cpo-modal-field-default-deselectable" value="'.esc_attr($i).'"'.checked($i, (( isset($aValue['default']) ) ? $aValue['default'] : ''), false).' />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['label'] ) ) ? esc_attr($aValue['label']) : '' ).'" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['slug'] ) ) ? esc_attr($aValue['slug']) : '' ).'" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][price]" value="'.( ( isset( $aValue['price'] ) ) ? esc_attr($aValue['price']) : '' ).'" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label class="uni_help_tip" data-tip="'.esc_attr__('This image will be used as an image of suboption', 'uni-cpo').'">'.esc_html__('Main image', 'uni-cpo').'</label>
                                                <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][image_id]" value="'.( ( !empty( $aValue['image_id'] ) ) ? esc_attr($aValue['image_id']) : '' ).'" />
                                                <button type="button" class="upload_image_button uni_help_tip" data-tip="'.esc_attr__('Add/Edit image', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                                <button type="button" class="remove_image_button uni_help_tip" data-tip="'.esc_attr__('Remove image', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
                    $i++;

                }
                }
            } else {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][default]" class="uni-cpo-modal-field-default-deselectable" value="{{row-count}}" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][price]" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label class="uni_help_tip" data-tip="'.esc_attr__('This image will be used as an image of suboption', 'uni-cpo').'">'.esc_html__('Main image', 'uni-cpo').'</label>
                                                <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][image_id]" value="" />
                                                <button type="button" class="upload_image_button uni_help_tip" data-tip="'.esc_attr__('Add/Edit image', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                                <button type="button" class="remove_image_button uni_help_tip" data-tip="'.esc_attr__('Remove image', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
            }

            $sOutput .= '</div>
                </div>
            </div>';

        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - select_options for text select
function uni_cpo_admin_text_options( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $sSerializedValues = ( get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) ) ? get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) : '';
    $aArrayOfValues = maybe_unserialize( $sSerializedValues );

    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    $aArgs['options'] = isset( $aArgs['options'] ) ? $aArgs['options'] : array();
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= '<div class="cpo-modal-field-fullwidth">';

            $sOutput .= '<label>' . esc_html( $aArgs['title'] ) . ( ( $aArgs['required'] ) ? '<span class="cpo-modal-field-required">*</span>' : '' ) . '</label>';
            if ( $aArgs['tip'] ) {
                $sOutput .= '<span class="uni_help_tip" data-tip="'.$aArgs['tip'].'"><i class="fa fa-question-circle"></i></span>';
            }
            if ( $aArgs['doc_link'] ) {
                $sOutput .= uni_cpo_get_doc_link( $aArgs['doc_link'] );
            }
            $sOutput .= ( $aArgs['description'] ) ? '<p class="cpo-modal-field-description">' . esc_html( $aArgs['description'] ) . '</p>' : '';

    $sOutput .= '<div class="uni-select-option-repeat">
        <div class="uni-select-option-repeat-wrapper">
            <div class="uni-select-option-add-wrapper">
                <span class="uni_select_option_add">'.esc_html__('Add', 'uni-cpo').'</span>
            </div>';

            $sOutput .= uni_cpo_import_export_html( $aArgs['name'] );

            $sOutput .= '<div class="uni-select-option-options-wrapper">';

            $bEmptySuboptions = false;
            $aNullValues = 0;
            if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {
                    if ( $aValue === NULL ) {
                        $aNullValues++;
                    }
                }
            }

            if ( $aNullValues !== 0 && count($aNullValues) === count($aArrayOfValues) ) {
                $bEmptySuboptions = true;
            }

            if ( !$bEmptySuboptions ) {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row option-type-' . esc_attr( $oOption->get_type() ) . '">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][default]" class="uni-cpo-modal-field-default-deselectable" value="{{row-count}}" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][price]" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Tooltip text', 'uni-cpo').'</label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][tooltip]" class="uni-cpo-modal-field uni-cpo-select-option-field-tooltip" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';

                $i = 0;

                if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {

                    if( $aValue === NULL ) {
                        continue;
                    }

                    $sOutput .= '<div class="uni-select-option-options-row option-type-' . esc_attr( $oOption->get_type() ) . '">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][default]" class="uni-cpo-modal-field-default-deselectable" value="'.esc_attr($i).'"'.checked($i, (( isset($aValue['default']) ) ? $aValue['default'] : ''), false).' />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['label'] ) ) ? esc_attr($aValue['label']) : '' ).'" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['slug'] ) ) ? esc_attr($aValue['slug']) : '' ).'" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][price]" value="'.( ( isset( $aValue['price'] ) ) ? esc_attr($aValue['price']) : '' ).'" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Tooltip text', 'uni-cpo').'</label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][tooltip]" class="uni-cpo-modal-field uni-cpo-select-option-field-tooltip" value="'.( ( !empty( $aValue['tooltip'] ) ) ? esc_attr($aValue['tooltip']) : '' ).'" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
                    $i++;

                }
                }
            } else {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row option-type-' . esc_attr( $oOption->get_type() ) . '">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label></label>
                                                <input type="radio" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][default]" class="uni-cpo-modal-field-default-deselectable" value="{{row-count}}" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Option slug', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][slug]" class="uni-cpo-modal-field uni-cpo-select-option-field-slug" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                            </div>
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Price / Rate', 'uni-cpo').'</label>
                                                <input class="uni-cpo-modal-field-price" type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][price]" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-big">
                                            <div class="uni-select-option-content-field-wrapper">
                                                <label>'.esc_html__('Tooltip text', 'uni-cpo').'</label>
                                                <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][tooltip]" class="uni-cpo-modal-field uni-cpo-select-option-field-tooltip" value="" />
                                            </div>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
            }

            $sOutput .= '</div>
                </div>
            </div>';

        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - download_options for download option
function uni_cpo_admin_download_options( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $sSerializedValues = ( get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) ) ? get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true ) : '';
    $aArrayOfValues = maybe_unserialize( $sSerializedValues );

    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    $aArgs['options'] = isset( $aArgs['options'] ) ? $aArgs['options'] : array();
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= '<div class="cpo-modal-field-fullwidth">';

            $sOutput .= '<label>' . esc_html( $aArgs['title'] ) . ( ( $aArgs['required'] ) ? '<span class="cpo-modal-field-required">*</span>' : '' ) . '</label>';
            if ( $aArgs['tip'] ) {
                $sOutput .= '<span class="uni_help_tip" data-tip="'.$aArgs['tip'].'"><i class="fa fa-question-circle"></i></span>';
            }
            if ( $aArgs['doc_link'] ) {
                $sOutput .= uni_cpo_get_doc_link( $aArgs['doc_link'] );
            }
            $sOutput .= ( $aArgs['description'] ) ? '<p class="cpo-modal-field-description">' . esc_html( $aArgs['description'] ) . '</p>' : '';

    $sOutput .= '<div class="uni-select-option-repeat">
        <div class="uni-select-option-repeat-wrapper">
            <div class="uni-select-option-add-wrapper">
                <span class="uni_select_option_add">'.esc_html__('Add', 'uni-cpo').'</span>
            </div>';

            $sOutput .= uni_cpo_import_export_html( $aArgs['name'] );

            $sOutput .= '<div class="uni-select-option-options-wrapper">';

            $bEmptySuboptions = false;
            $aNullValues = 0;
            if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {
                    if ( $aValue === NULL ) {
                        $aNullValues++;
                    }
                }
            }

            if ( $aNullValues !== 0 && count($aNullValues) === count($aArrayOfValues) ) {
                $bEmptySuboptions = true;
            }

            if ( !$bEmptySuboptions ) {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                            <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label wide-input" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('File', 'uni-cpo').'</label>
                                            <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][file_id]" value="" />
                                            <button type="button" class="add_file_button uni_help_tip" data-tip="'.esc_attr__('Choose file', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                            <button type="button" class="remove_file_button uni_help_tip" data-tip="'.esc_attr__('Remove file', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
                $i = 0;

                if ( !empty( $aArrayOfValues ) && is_array( $aArrayOfValues ) ) {
                foreach ( $aArrayOfValues as $sKey => $aValue ) {

                    if( $aValue === NULL ) {
                        continue;
                    }

                    $sOutput .= '<div class="uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('Label', 'uni-cpo').'<span class="cpo-modal-field-required">*</span></label>
                                            <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label wide-input" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="'.( ( !empty( $aValue['label'] ) ) ? esc_attr($aValue['label']) : '' ).'" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('File', 'uni-cpo').'</label>
                                            <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '['.esc_attr($i).'][file_id]" value="'.( ( !empty( $aValue['file_id'] ) ) ? esc_attr($aValue['file_id']) : '' ).'" />
                                            <button type="button" class="add_file_button uni_help_tip" data-tip="'.esc_attr__('Choose file', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                            <button type="button" class="remove_file_button uni_help_tip" data-tip="'.esc_attr__('Remove file', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
                    $i++;

                }
                }
            } else {
                    $sOutput .= '<div class="uni-select-option-options-template uni-select-option-options-row">
                                    <div class="uni-select-option-move-wrapper">
                                        <span class="uni_select_option_move"><i class="fa fa-arrows"></i></span>
                                    </div>
                                    <div class="uni-select-option-content-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('Label', 'uni-cpo').'</label>
                                            <input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][label]" class="uni-cpo-modal-field uni-cpo-select-option-field-label wide-input" data-parsley-required="true" data-parsley-trigger="change focusout submit" value="" />
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper">
                                            <label>'.esc_html__('File', 'uni-cpo').'</label>
                                            <input type="hidden" class="option_thumbnail_id" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '[{{row-count}}][file_id]" value="" />
                                            <button type="button" class="add_file_button uni_help_tip" data-tip="'.esc_attr__('Choose file', 'uni-cpo').'"><i class="fa fa-pencil"></i></button>
                                            <button type="button" class="remove_file_button uni_help_tip" data-tip="'.esc_attr__('Remove file', 'uni-cpo').'"><i class="fa fa-times"></i></button>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                            <span class="uni_select_option_remove"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                </div>';
            }

            $sOutput .= '</div>
                </div>
            </div>';

        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

//
function uni_cpo_import_export_html( $field_name ) {
    return '<div class="uni-select-option-export-wrapper">
                <span id="uni_export_suboptions" data-field-name="' . esc_attr( $field_name ) . '">'.esc_html__('Export', 'uni-cpo').'</span>
            </div>
            <div class="uni-select-option-export-wrapper">
                <span id="uni_import_suboptions">'.esc_html__('Import', 'uni-cpo').'</span>
            </div>
            <div class="uni-clear"></div>
            <div class="uni-exporter-wrapper">
                <progress class="uni-exporter-progress" max="100" value="0"></progress>
            </div>
            <div class="uni-importer-wrapper">
                <input name="suboptions-import" type="file" />
                <input id="uni_import_suboptions_submit" type="button" value="'.esc_html__('Submit', 'uni-cpo').'" data-field-name="' . esc_attr( $field_name ) . '" />
                <div class="uni-clear"></div>
            </div>';
}

// option settings field - builder_cond_field
function uni_cpo_admin_builder_cond_field( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true );
    $aArgs['options'] = isset( $aArgs['options'] ) ? $aArgs['options'] : array();
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= '<div class="cpo-modal-field-fullcell">';

            //$sOutput .= '<label>' . esc_html( $aArgs['title'] ) . ( ( $aArgs['required'] ) ? '<span class="cpo-modal-field-required">*</span>' : '' ) . '</label>';
            $sOutput .= '<div class="uni-query-builder-wrapper">';
                $sOutput .= '<div id="cpo-option-condition-builder"></div>';
            $sOutput .= '</div>';
            $sOutput .= '<input type="hidden" id="uni_cpo_field_conditional_scheme" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '" value="' . esc_attr( json_encode( $aArgs['value'] ) ) . '" class="uni-cpo-modal-field" />';
            $sOutput .= '<input id="parse-option-conditional-rule-builder" type="button" value="' . esc_html__('Fetch formatted rule(s)', 'uni-cpo') . '" class="uni-cpo-settings-btn uni-cpo-settings-saved" />';

        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - builder_cond_val
function uni_cpo_admin_builder_cond_val( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true );
    $aArgs['options'] = isset( $aArgs['options'] ) ? $aArgs['options'] : array();
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';

    $aValidationRulesArray = ( !empty($aArgs['value']) ) ? maybe_unserialize( $aArgs['value'] ) : array();

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= '<div class="cpo-modal-field-fullcell">';

            if ( empty($aValidationRulesArray) && !is_array($aValidationRulesArray) ) {

            $sOutput .= '<div class="uni-validation-conditional-rules-repeat">
                            <div class="uni-validation-conditional-rules-repeat-wrapper">
                                <div class="uni-validation-conditional-rules-add-wrapper">
                                    <span class="uni_validation_conditional_rule_add">' . esc_html__('Add', 'uni-cpo') . '</span>
                                </div>
                                <div class="uni-validation-conditional-rules-options-wrapper">

                                    <div class="uni-validation-conditional-rules-options-template uni-validation-conditional-rules-options-row">
                                        <div class="uni-validation-conditional-rules-move-wrapper">
                                            <span class="uni_validation_conditional_rule_move"><i class="fa fa-arrows"></i></span>
                                        </div>
                                        <div class="uni-validation-conditional-rules-content-wrapper">
                                            <div class="uni-validation-conditional-rules-content-field-wrapper">
                                                <label>' . esc_html__('Rule', 'uni-cpo') . '</label>
                                                <div class="uni-query-builder-wrapper">
                                                    <div id="cpo-validation-rule-builder-{{row-count}}" class="cpo-validation-rule-builder uni-cpo-sort-select-el"></div>

                                                    <input class="cpo-parse-validation-rule-json uni-cpo-settings-btn uni-cpo-settings-saved uni-cpo-sort-select-el-data" data-id="{{row-count}}" type="button" value="' . esc_html__('Fetch the formatted rule', 'uni-cpo') . '" />
                                                </div>
                                                <input id="uni_cpo_validation_rule_scheme-{{row-count}}" type="hidden" name="uni_cpo_val_conditional_scheme[{{row-count}}][rule]" value="" class="uni-cpo-sort-select-el-scheme" />
                                            </div>
                                            <div class="uni-validation-conditional-rules-content-field-wrapper">';
                                                $sOutput .= $oOption->render_validation_fields( '{{row-count}}' );
                                            $sOutput .= '</div>
                                            <div class="uni-validation-conditional-rules-content-field-wrapper uni-validation-conditional-rules-remove-wrapper">
                                                <span class="uni_validation_conditional_rule_remove"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uni-validation-conditional-rules-options-row">
                                        <div class="uni-validation-conditional-rules-move-wrapper">
                                            <span class="uni_validation_conditional_rule_move"><i class="fa fa-arrows"></i></span>
                                        </div>
                                        <div class="uni-validation-conditional-rules-content-wrapper">
                                            <div class="uni-validation-conditional-rules-content-field-wrapper">
                                                <label>' . esc_html__('Rule', 'uni-cpo') . '</label>
                                                <div class="uni-query-builder-wrapper">
                                                    <div id="cpo-validation-rule-builder-0" class="cpo-validation-rule-builder uni-cpo-sort-select-el"></div>

                                                    <input class="cpo-parse-validation-rule-json uni-cpo-settings-btn uni-cpo-settings-saved uni-cpo-sort-select-el-data" data-id="0" type="button" value="' . esc_html__('Fetch the formatted rule', 'uni-cpo') . '" />
                                                </div>
                                                <input id="uni_cpo_validation_rule_scheme-0" type="hidden" name="uni_cpo_val_conditional_scheme[0][rule]" value="" class="uni-cpo-sort-select-el-scheme" />
                                            </div>
                                            <div class="uni-validation-conditional-rules-content-field-wrapper">';
                                                $sOutput .= $oOption->render_validation_fields( '0' );
                                            $sOutput .= '</div>
                                            <div class="uni-validation-conditional-rules-content-field-wrapper uni-validation-conditional-rules-remove-wrapper">
                                                <span class="uni_validation_conditional_rule_remove"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    </div>

                            </div>
                        </div>
                    </div>';

            } else {

            $sOutput .= '<div class="uni-validation-conditional-rules-repeat">
                            <div class="uni-validation-conditional-rules-repeat-wrapper">
                                <div class="uni-validation-conditional-rules-add-wrapper">
                                    <span class="uni_validation_conditional_rule_add">' . esc_html__('Add', 'uni-cpo') . '</span>
                                </div>
                                <div class="uni-validation-conditional-rules-options-wrapper">

                                    <div class="uni-validation-conditional-rules-options-template uni-validation-conditional-rules-options-row">
                                        <div class="uni-validation-conditional-rules-move-wrapper">
                                            <span class="uni_validation_conditional_rule_move"><i class="fa fa-arrows"></i></span>
                                        </div>
                                        <div class="uni-validation-conditional-rules-content-wrapper">
                                            <div class="uni-validation-conditional-rules-content-field-wrapper">
                                                <label>' . esc_html__('Rule', 'uni-cpo') . '</label>
                                                <div class="uni-query-builder-wrapper">
                                                    <div id="cpo-validation-rule-builder-{{row-count}}" class="cpo-validation-rule-builder uni-cpo-sort-select-el"></div>

                                                    <input class="cpo-parse-validation-rule-json uni-cpo-settings-btn uni-cpo-settings-saved uni-cpo-sort-select-el-data" data-id="{{row-count}}" type="button" value="' . esc_html__('Fetch the formatted rule', 'uni-cpo') . '" />
                                                </div>
                                                <input id="uni_cpo_validation_rule_scheme-{{row-count}}" type="hidden" name="uni_cpo_val_conditional_scheme[{{row-count}}][rule]" value="" class="uni-cpo-sort-select-el-scheme" />
                                            </div>
                                            <div class="uni-validation-conditional-rules-content-field-wrapper">';
                                                $sOutput .= $oOption->render_validation_fields( '{{row-count}}' );
                                            $sOutput .= '</div>
                                            <div class="uni-validation-conditional-rules-content-field-wrapper uni-validation-conditional-rules-remove-wrapper">
                                                <span class="uni_validation_conditional_rule_remove"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    </div>';
                $iRulesCount = 0;
                foreach ( $aValidationRulesArray as $sKey => $aValidationRuleArray ) {

                        $sOutput .= '<div class="uni-validation-conditional-rules-options-row">
                                        <div class="uni-validation-conditional-rules-move-wrapper">
                                            <span class="uni_validation_conditional_rule_move"><i class="fa fa-arrows"></i></span>
                                        </div>
                                        <div class="uni-validation-conditional-rules-content-wrapper">
                                            <div class="uni-validation-conditional-rules-content-field-wrapper">
                                                <label>' . esc_html__('Rule', 'uni-cpo') . '</label>
                                                <div class="uni-query-builder-wrapper">
                                                    <div id="cpo-validation-rule-builder-'.esc_attr($iRulesCount).'" class="cpo-validation-rule-builder uni-cpo-sort-select-el"></div>

                                                    <input class="cpo-parse-validation-rule-json uni-cpo-settings-btn uni-cpo-settings-saved uni-cpo-sort-select-el-data" data-id="'.esc_attr($iRulesCount).'" type="button" value="' . esc_html__('Fetch the formatted rule', 'uni-cpo') . '" />
                                                </div>
                                                <input id="uni_cpo_validation_rule_scheme-'.esc_attr($iRulesCount).'" type="hidden" name="uni_cpo_val_conditional_scheme['.esc_attr($iRulesCount).'][rule]" value="'.esc_attr(json_encode($aValidationRuleArray['rule'])).'" class="uni-cpo-sort-select-el-scheme" />
                                            </div>
                                            <div class="uni-validation-conditional-rules-content-field-wrapper">';
                                                $sOutput .= $oOption->render_validation_fields( $iRulesCount, $aValidationRuleArray['value'] );
                                            $sOutput .= '</div>
                                            <div class="uni-validation-conditional-rules-content-field-wrapper uni-validation-conditional-rules-remove-wrapper">
                                                <span class="uni_validation_conditional_rule_remove"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    </div>';

                    $aRulesArray[] = $aValidationRuleArray['rule'];
                    $iRulesCount++;
                }

                $sOutput .= '</div>
                        </div>
                    </div>';

            }

        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - colorpicker
function uni_cpo_admin_colorpicker( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    if ( $aArgs['name'] == 'field_slug' ) {
        $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : $oOption->post->post_name;
    } else {
        $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true );
    }
    // fix for hex codes without #
    if ( false === strpos($aArgs['value'], '#') ) {
        $aArgs['value'] = '#' . $aArgs['value'];
    }
    $aArgs['type'] = isset( $aArgs['type'] ) ? $aArgs['type'] : 'text';
    $aArgs['placeholder'] = isset( $aArgs['placeholder'] ) ? $aArgs['placeholder'] : '';
    $aArgs['validation_pattern'] = isset( $aArgs['validation_pattern'] ) ? $aArgs['validation_pattern'] : '';
    $aArgs['validation_type'] = isset( $aArgs['validation_type'] ) ? $aArgs['validation_type'] : '';
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';
    $aArgs['strip_slashes'] = ( isset( $aArgs['strip_slashes'] ) && isset( $aArgs['strip_slashes'] ) === true ) ? true : false;

    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';
        $sOutput .= uni_cpo_admin_setting_leftcell( $aArgs );
        $sOutput .= '<div class="cpo-modal-field-rightcell">';
            $sOutput .= '<input type="text" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '" class="uni-cpo-modal-field uni-cpo-palette-option-field-color" value="' . esc_attr( $aArgs['value'] ) . '"' . ( ( $aArgs['required'] ) ? ' data-parsley-required="true" data-parsley-trigger="change focusout submit"' : '' ) . ' />';
        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - mappicke (Google Maps)
function uni_cpo_admin_mappicker( $oOption, $aArgs ){

    $iOptionId = $oOption->get_id();
    $aArgs['required'] = ( isset( $aArgs['required'] ) && $aArgs['required'] == true ) ? true : false;
    $aArgs['name'] = isset( $aArgs['name'] ) ? $aArgs['name'] : '';
    if ( $aArgs['name'] == 'field_slug' ) {
        $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : $oOption->post->post_name;
    } else {
        $aArgs['value'] = isset( $aArgs['value'] ) ? $aArgs['value'] : get_post_meta( $iOptionId, '_uni_cpo_'.$aArgs['name'], true );
    }
    $aArgs['type'] = isset( $aArgs['type'] ) ? $aArgs['type'] : 'text';
    $aArgs['placeholder'] = isset( $aArgs['placeholder'] ) ? $aArgs['placeholder'] : '';
    $aArgs['validation_pattern'] = isset( $aArgs['validation_pattern'] ) ? $aArgs['validation_pattern'] : '';
    $aArgs['validation_type'] = isset( $aArgs['validation_type'] ) ? $aArgs['validation_type'] : '';
    $aArgs['title'] = isset( $aArgs['title'] ) ? $aArgs['title'] : '';
    $aArgs['description'] = isset( $aArgs['description'] ) ? $aArgs['description'] : '';
    $aArgs['tip'] = isset( $aArgs['tip'] ) ? $aArgs['tip'] : '';
    $aArgs['doc_link'] = isset( $aArgs['doc_link'] ) ? $aArgs['doc_link'] : '';
    $aArgs['strip_slashes'] = ( isset( $aArgs['strip_slashes'] ) && isset( $aArgs['strip_slashes'] ) === true ) ? true : false;


    $sOutput = '<div id="uni_cpo_'.esc_attr( $aArgs['name'] ).'_container" class="cpo-modal-field-container">';

        $sOutput .= '<label>' . esc_html( $aArgs['title'] ) . ( ( $aArgs['required'] ) ? '<span class="cpo-modal-field-required">*</span>' : '' ) . '</label>';
        
        if ( $aArgs['tip'] ) {
            $sOutput .= '<span class="uni_help_tip" data-tip="'.$aArgs['tip'].'"><i class="fa fa-question-circle"></i></span>';
        }

        $sOutput .= '<div class="cpo-modal-field-rightcell">';

        // search field
        $sOutput .= '<input type="text" class="uni-cpo-search-locations" ' . ( ( !empty( $aArgs['placeholder'] ) ) ? ' placeholder="' . esc_attr( $aArgs['placeholder'] ) . '"' : '' ) . '>';

        // map
        $sOutput .= '<div style="height:300px;" class="uni-cpo-mappicker"></div>';
 
        // enable strip_slashes
        if ( $aArgs['strip_slashes'] ) {
            $aArgs['value'] = stripslashes_deep( $aArgs['value'] );
        }

        // hidden coordinates field
        $sOutput .= '<input type="hidden" name="uni_cpo_' . esc_attr( $aArgs['name'] ) . '"   value="' . esc_attr( $aArgs['value'] ) . '"  class="uni-cpo-modal-field uni-cpo-search-locations-lat-lng ' . ( ( $aArgs['required'] ) ? ' uni-cpo-field-required' : '' ) . '"' . ( ( $aArgs['required'] ) ? ' data-parsley-required="true" data-parsley-trigger="change focusout submit"' : '' ) . ( ( !empty( $aArgs['validation_pattern'] ) ) ? ' data-parsley-pattern="' . $aArgs['validation_pattern'] . '"' : '' ) . ( ( !empty( $aArgs['validation_type'] ) ) ? ' data-parsley-type="' . $aArgs['validation_type'] . '"' : '' ) . ' >';
            
 
        $sOutput .= '</div>';
    $sOutput .= '</div>';

    return $sOutput;

}

// option settings field - colorpicker
function uni_cpo_admin_setting_leftcell( $aArgs ){
        $sOutput = '<div class="cpo-modal-field-leftcell">';
            $sOutput .= '<label>' . esc_html( $aArgs['title'] ) . ( ( $aArgs['required'] ) ? '<span class="cpo-modal-field-required">*</span>' : '' ) . '</label>';
            if ( $aArgs['tip'] ) {
                $sOutput .= '<span class="uni_help_tip" data-tip="'.$aArgs['tip'].'"><i class="fa fa-question-circle"></i></span>';
            }
            if ( $aArgs['doc_link'] ) {
                $sOutput .= uni_cpo_get_doc_link( $aArgs['doc_link'] );
            }
            $sOutput .= ( $aArgs['description'] ) ? '<p class="cpo-modal-field-description">' . $aArgs['description'] . '</p>' : '';
        $sOutput .= '</div>';
    return $sOutput;
}


//////////////////////////////////////////////////////////////////////////////////////
// bulk and quick edit related functions
//////////////////////////////////////////////////////////////////////////////////////

// bulk and quick edit additional fields
add_action( 'woocommerce_product_quick_edit_end', 'uni_cpo_additional_fields_product_quick_edit' );
add_action( 'woocommerce_product_bulk_edit_end', 'uni_cpo_additional_fields_product_quick_edit' );
function uni_cpo_additional_fields_product_quick_edit(){

    include( UniCpo()->plugin_path() . '/includes/admin/view/html-quick-edit-cpo-prod-settings.php' );
    
}

// save data from bulk and quick fields additional fields
add_action( 'woocommerce_product_quick_edit_save', 'uni_cpo_save_additional_fields_product_quick_edit', 99, 1);
add_action( 'woocommerce_product_bulk_edit_save', 'uni_cpo_save_additional_fields_product_quick_edit', 99, 1);
function uni_cpo_save_additional_fields_product_quick_edit( $product ){
    $product_id = $product->get_id();
    //
    update_post_meta( $product_id, '_uni_cpo_options_set', ( ( isset($_REQUEST['uni_cpo_options_set']) ) ? $_REQUEST['uni_cpo_options_set'] : '' ) );
    // general settings
    update_post_meta( $product_id, '_uni_cpo_display_options_enable', ( ( isset($_REQUEST['uni_cpo_display_options_enable']) && !empty($_REQUEST['uni_cpo_display_options_enable']) ) ? true : false ) );
    update_post_meta( $product_id, '_uni_cpo_price_calculation_enable', ( ( isset($_REQUEST['uni_cpo_price_calculation_enable']) && !empty($_REQUEST['uni_cpo_price_calculation_enable']) ) ? true : false ) );

    // minimal price
    $fMinPrice = (float)$_REQUEST['uni_cpo_min_price'];
    update_post_meta( $product_id, '_uni_cpo_min_price', $fMinPrice );
    // the main formula
    update_post_meta( $product_id, '_uni_cpo_price_main_formula', ( ( isset($_REQUEST['uni_cpo_price_main_formula']) ) ? $_REQUEST['uni_cpo_price_main_formula'] : '' ) );
}

// quick edit additional inline meta data
add_action( 'manage_product_posts_custom_column', 'uni_cpo_save_additional_inline_meta_data_quick_edit', 99, 2);
function uni_cpo_save_additional_inline_meta_data_quick_edit($column, $post_id){

    switch ( $column ) {
        case 'name' :
            ?>
            <div class="hidden uni_cpo_inline" id="uni_cpo_inline_<?php echo esc_attr( $post_id ); ?>">
                <div class="uni_cpo_options_set"><?php echo get_post_meta($post_id, '_uni_cpo_options_set', true); ?></div>
                <div class="uni_cpo_display_options_enable"><?php echo get_post_meta($post_id, '_uni_cpo_display_options_enable', true); ?></div>
                <div class="uni_cpo_price_calculation_enable"><?php echo get_post_meta($post_id, '_uni_cpo_price_calculation_enable', true); ?></div>
                <div class="uni_cpo_min_price"><?php echo get_post_meta($post_id, '_uni_cpo_min_price', true); ?></div>
                <div class="uni_cpo_price_main_formula"><?php echo get_post_meta($post_id, '_uni_cpo_price_main_formula', true); ?></div>
            </div>
            <?php

            break;

        default :
            break;
    }

}

//
function uni_cpo_admin_notices() {

}

//
add_action( 'admin_init', 'uni_cpo_options_table_actions' );
function uni_cpo_options_table_actions() {

    if ( isset($_GET['page']) && $_GET['page'] === 'uni-cpo-options-list' ) {

        // duplicate
        if ( isset($_GET['options-set-id']) && isset($_GET['action']) && $_GET['action'] === 'cpo-duplicate' ) {
            uni_cpo_duplicate_options_set( $_GET['options-set-id'] );
            wp_safe_redirect( admin_url( 'admin.php?page=uni-cpo-options-list' ) );
            exit;
        }

        // edit TODO
        if ( isset($_GET['options-set-id']) && isset($_GET['action']) && $_GET['action'] === 'cpo-edit' ) {

        }

        // delete TODO
        if ( isset($_GET['options-set-id']) && isset($_GET['action']) && $_GET['action'] === 'cpo-delete' ) {

        }

    }

}

//
function uni_cpo_duplicate_option_post( $iOriginalPostId, $iDuplicatedParentPostId = '' ) {

    global $wpdb;

	$aOriginalPostId = get_post( $iOriginalPostId, 'ARRAY_A' );

    if ( empty($iDuplicatedParentPostId) ) {
        $aDefaults = array(
            'post_status' => 'publish',
        	'post_type' => 'uni_cpo_option',
        	'post_date' => date('Y-m-d H:i:s', current_time('timestamp',0)),
            'post_date_gmt' => date('Y-m-d H:i:s', current_time('timestamp',1)),
            'post_modified' => date('Y-m-d H:i:s', current_time('timestamp',0)),
            'post_modified_gmt' => date('Y-m-d H:i:s', current_time('timestamp',1)),
        	'post_title' => $aOriginalPostId['post_title'].' '.esc_html__('copy', 'interview'),
        	'post_name' => sanitize_title($aOriginalPostId['post_name'].'-'.'copy')
        );
    } else {
        $sPostTitle = rtrim($aOriginalPostId['post_title'], '}');
        $sPostTitle = $sPostTitle . '_copy}';
        $sPostSlug = sanitize_title_with_dashes( $aOriginalPostId['post_name'] . '_copy' );

        $aDefaults = array(
            'post_status' => 'publish',
        	'post_type' => 'uni_cpo_option',
        	'post_date' => date('Y-m-d H:i:s', current_time('timestamp',0)),
            'post_date_gmt' => date('Y-m-d H:i:s', current_time('timestamp',1)),
            'post_modified' => date('Y-m-d H:i:s', current_time('timestamp',0)),
            'post_modified_gmt' => date('Y-m-d H:i:s', current_time('timestamp',1)),
        	'post_title' => $sPostTitle,
        	'post_name' => $sPostSlug,
            'post_parent' => $iDuplicatedParentPostId
        );
    }

    // inserts the post into the database
	$iDuplicatedPostId = wp_insert_post( $aDefaults );

    // duplicates all the custom fields
    $aOriginalCustomFields = get_post_custom( $iOriginalPostId );
    foreach ( $aOriginalCustomFields as $sKey => $aValue ) {
        if ( is_serialized($aValue[0]) ) {
            $aValueUnserialized = maybe_unserialize($aValue[0]);
            update_post_meta($iDuplicatedPostId, $sKey, $aValueUnserialized);
        } else {
            update_post_meta($iDuplicatedPostId, $sKey, $aValue[0]);
        }
    }
    return $iDuplicatedPostId;

}

//
function uni_cpo_duplicate_options_set( $iOriginalOptionsSetPostId ) {

    // makes a copy of optons set
    $iDuplicatedOptionsSetPostId = uni_cpo_duplicate_option_post( $iOriginalOptionsSetPostId );

    // gets all options of selected options set
    $aOriginalChildrenOptions = get_posts( array('post_type' => 'uni_cpo_option', 'post_parent' => $iOriginalOptionsSetPostId, 'posts_per_page' => -1) );
    // if options set has options
    if ( !empty($aOriginalChildrenOptions) ) {
        $aStructure = get_post_meta( $iDuplicatedOptionsSetPostId, '_uni_cpo_options_structure', true );
        // duplicates all children options of the options set
        foreach ( $aOriginalChildrenOptions as $oOriginalChildPost ) {
            // duplicates every option
            $iDuplicatedChildrenPostId = uni_cpo_duplicate_option_post( $oOriginalChildPost->ID, $iDuplicatedOptionsSetPostId );

            // updates the array of options structure
            foreach ( $aStructure as $key => $array ) {
                if ( intval( $array['id'] ) === intval( $oOriginalChildPost->ID ) ) {
                    $aStructure[$key]['id'] = intval( $iDuplicatedChildrenPostId );
                    $aStructure[$key]['title'] = get_the_title( $iDuplicatedChildrenPostId );
                    break;
                }
                if ( isset($array['children']) && is_array($array['children']) ) {
                    foreach ( $array['children'] as $children_key => $children_array ) {
                        if ( intval( $children_array['id'] ) === intval( $oOriginalChildPost->ID ) ) {
                            $aStructure[$key]['children'][$children_key]['id'] = intval( $iDuplicatedChildrenPostId );
                            $aStructure[$key]['children'][$children_key]['title'] = get_the_title( $iDuplicatedChildrenPostId );
                            break;
                        }
                    }
                }
            }
        }
        // updates 'parentid' value - we have to run a new loop because options are being created in different order in the prev one
        // and sometimes copies of children options are cretaed faster the its parent
        foreach ( $aStructure as $key => $array ) {
                if ( isset($array['children']) && is_array($array['children']) ) {
                    foreach ( $array['children'] as $children_key => $children_array ) {
                        $aStructure[$key]['children'][$children_key]['parentid'] = $array['id'];
                    }
                }
        }
        // saves options structure
        update_post_meta( $iDuplicatedOptionsSetPostId, '_uni_cpo_options_structure', $aStructure );
        // updates an info about parent options and field conditional logic
        foreach ( $aStructure as $key => $array ) {
            if ( isset($array['children']) && is_array($array['children']) ) {
                foreach ( $array['children'] as $children_key => $children_array ) {

                    $iOldParent = get_post_meta( $children_array['id'], '_uni_cpo_parent_option_id', true );

                    // updates an info about parent options
                    update_post_meta( $children_array['id'], '_uni_cpo_parent_option_id', $array['id'] );

                    // updates field conditional rules for every children option
                    $oOldParent = get_post($iOldParent);
                    $sOldSlug = $oOldParent->post_name;
                    $oNewParent = get_post($array['id']);
                    $sNewSlug = $oNewParent->post_name;
                    uni_cpo_update_field_conditional_rules_scheme( $children_array['id'], $sOldSlug, $sNewSlug );
                }
            }
        }
    }

}

//
function uni_cpo_truncate_post_slug( $slug, $length = 200 ) {
    if ( strlen( $slug ) > $length ) {
        $decoded_slug = urldecode( $slug );
        if ( $decoded_slug === $slug )
            $slug = substr( $slug, 0, $length );
        else
            $slug = utf8_uri_encode( $decoded_slug, $length );
    }

    return rtrim( $slug, '-' );
}

//
function uni_cpo_get_unique_option_slug( $slug, $existed_slugs ) {
    $suffix = 2;
    $is_name_valid = true;
    $reserved_slugs = uni_cpo_get_reserved_option_slugs();
    $prohibitted_slugs = array_merge( $existed_slugs, $reserved_slugs );
    do {
        $alt_slug = uni_cpo_truncate_post_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "_$suffix";
        $is_name_valid = ( in_array( $alt_slug, $prohibitted_slugs ) ) ? true : false;
        $suffix++;
    } while ( $is_name_valid );

    return $alt_slug;
}

//
function uni_cpo_get_all_options_slugs_by_options_set_id( $iOptionsPostId ) {

    $aListOptionsSlugs = array();

    $aProductOptions = get_post_meta( $iOptionsPostId, '_uni_cpo_options_structure', true );
    $aListOptionsIds = wp_list_pluck($aProductOptions, 'id');
    foreach ( $aProductOptions as $OptionKey => $aOption ) {
        $aListChildrenOptionsIds = array();
        if ( isset($aOption['children']) ) {
            $aListChildrenOptionsIds = wp_list_pluck($aOption['children'], 'id');
            $aListOptionsIds = array_merge($aListOptionsIds, $aListChildrenOptionsIds);
        }
    }

    $aAllOptionsPosts = get_posts( array('post_type' => 'uni_cpo_option', 'include' => $aListOptionsIds) );
    $aListOptionsSlugs = wp_list_pluck($aAllOptionsPosts, 'post_name');

    return $aListOptionsSlugs;
}

//
function uni_cpo_get_all_role_names( $add_guest = false ) {
    global $wp_roles;
    $all_roles = $wp_roles->roles;
    $role_names = array();
    foreach( $all_roles as $role_name => $role_data ) {
        $role_names[$role_name] = $role_data['name'];
    }
    if ( $add_guest ) {
        $role_names['cpo_nonreg'] = esc_html__('Non reg. users (default)', 'uni-cpo');
    }
    return $role_names;
}

// doc link with icon
function uni_cpo_get_doc_link( $url = '' ) {
    return '<a class="uni_doc_link uni_help_tip" target="_blank" data-tip="' . esc_html__('Go to the documentation and read more about this feature', 'uni-cpo') . '" href="' . esc_url( $url ) . '"><i class="fa fa-graduation-cap"></i></a>';
}

// set min qty
add_filter( 'woocommerce_quantity_input_min', 'uni_cpo_woocommerce_quantity_input_min', 10, 2 );
function uni_cpo_woocommerce_quantity_input_min( $qty, $product ){
    $product_meta = get_post_custom( $product->get_id() );
    if ( ! $product->is_sold_individually() && isset( $product_meta['_uni_cpo_min_qty'][0] ) && ! empty( $product_meta['_uni_cpo_min_qty'][0] ) ) {
        return intval( $product_meta['_uni_cpo_min_qty'][0] );
    } else {
        return $qty;
    }
}

// set max qty
add_filter( 'woocommerce_quantity_input_max', 'uni_cpo_woocommerce_quantity_input_max', 10, 2 );
function uni_cpo_woocommerce_quantity_input_max( $qty, $product ){
    $product_meta = get_post_custom( $product->get_id() );
    if ( ! $product->is_sold_individually() && isset( $product_meta['_uni_cpo_max_qty'][0] ) && ! empty( $product_meta['_uni_cpo_max_qty'][0] ) ) {
        return intval( $product_meta['_uni_cpo_max_qty'][0] );
    } else {
        return $qty;
    }
}

//
function uni_cpo_get_week_days_list(){
    return array(
        '1' => __('Monday', 'uni-cpo'),
        '2' => __('Tuesday', 'uni-cpo'),
        '3' => __('Wednesday', 'uni-cpo'),
        '4' => __('Thursday', 'uni-cpo'),
        '5' => __('Friday', 'uni-cpo'),
        '6' => __('Saturday', 'uni-cpo'),
        '7' => __('Sunday', 'uni-cpo')
    );
}

//
function uni_cpo_get_months_list(){
    return array(
        '0' => __('Current month', 'uni-cpo'),
        '1' => __('January', 'uni-cpo'),
        '2' => __('February', 'uni-cpo'),
        '3' => __('March', 'uni-cpo'),
        '4' => __('April', 'uni-cpo'),
        '5' => __('May', 'uni-cpo'),
        '6' => __('June', 'uni-cpo'),
        '7' => __('July', 'uni-cpo'),
        '8' => __('August', 'uni-cpo'),
        '9' => __('September', 'uni-cpo'),
        '10' => __('October', 'uni-cpo'),
        '11' => __('November', 'uni-cpo'),
        '12' => __('December', 'uni-cpo'),
    );
}

//////////////////////////////////////////////////////////////////////////////////////
// order edit page
//////////////////////////////////////////////////////////////////////////////////////
// adds Add/Edit CPO options btn
add_action( 'woocommerce_after_order_itemmeta', 'uni_cpo_woocommerce_after_order_itemmeta', 10, 2 );
function uni_cpo_woocommerce_after_order_itemmeta( $item_id, $item ) {
    if ( $item instanceof WC_Order_Item_Product ) {
        $order_id           = $item->get_order_id();
        $product_id         = $item->get_product_id();
        $product_meta       = get_post_custom( $product_id );
        if ( isset( $product_meta['_uni_cpo_display_options_enable'][0] ) && $product_meta['_uni_cpo_display_options_enable'][0] ) {
            echo '<div class="uni_cpo_ajax_link_container">';
            echo '<span class="button cpo-add-option-btn uni_cpo_ajax_call" data-action="uni_cpo_order_item_options_add_show" data-pid="' . esc_attr( $product_id ) . '" data-item_id="' . esc_attr( $item_id ) . '" data-order_id="' . esc_attr( $order_id ) . '">' . esc_html__('Add/Edit CPO option(s)', 'uni-cpo') . '</span>';
            echo '<div>';
        }
    }
}

// display order items meta nicely
add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'uni_cpo_woocommerce_order_item_get_formatted_meta_data', 10, 2 );
function uni_cpo_woocommerce_order_item_get_formatted_meta_data( $formatted_meta, $item ){
    $meta_data = $item->get_meta_data();
    foreach ( $meta_data as $meta ) {
        if ( false !== strpos( $meta->key, UniCpo()->var_slug ) ) {
            $slug = str_replace( '_' . UniCpo()->var_slug, '', $meta->key );

            // attachments
            if ( 'list_of_attachments' === $slug ) {

                $attachments_links = array();
                foreach ( $meta->value as $attach_id ) {
                    $formatted_meta[ $meta->id ] = (object) array(
                        'key'           => $meta->key,
                        'value'         => $meta->value,
                        'display_key'   => get_the_title( $attach_id ),
                        'display_value' => wpautop( make_clickable( get_edit_post_link( $attach_id ) ) ),
                    );
                }

            // cpo options, but not attachments
            } else {

                $meta_key_wo_ = ltrim( $meta->key, '_' );
                $post = uni_cpo_get_post_by_slug( $slug );
                if ( $post ) {
                    $option             = uni_cpo_get_option( $post );
                    $display_key        = $option->get_meta_label();
                    $display_value      = $option->calculation( array($meta_key_wo_ => $meta->value), 'order' );
                    $formatted_meta[ $meta->id ] = (object) array(
                        'key'           => $meta->key,
                        'value'         => $meta->value,
                        'display_key'   => $display_key,
                        'display_value' => wpautop( make_clickable( $display_value ) ),
                    );
                }

            }
        }
    }
    return $formatted_meta;
}

//////////////////////////////////////////////////////////////////////////////////////
// WC Subscriptions
//////////////////////////////////////////////////////////////////////////////////////

if ( in_array( 'woocommerce-subscriptions/woocommerce-subscriptions.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    // Checks if a given product is of a switchable type
    function uni_cpo_is_product_switchable_type( $is_product_switchable, $product, $variation ) {

        $allow_switching = get_option( WC_Subscriptions_Admin::$option_prefix . '_allow_switching', 'no' );

        if ( $allow_switching === 'variable_cpo_option' ) {
            $is_product_switchable = ( isset($product->product_custom_fields['_uni_cpo_price_calculation_enable'][0]) && $product->product_custom_fields['_uni_cpo_price_calculation_enable'][0] == 1 ) ? true : false;
        }

        return $is_product_switchable;

    }
    // Extends the Switchable Type function
    add_filter( 'wcs_is_product_switchable', 'uni_cpo_is_product_switchable_type', 10, 3 );

    // Adds custom switchable type
    function uni_cpo_add_custom_settings( $settings ) {

        	foreach ($settings as $k => $s) {
        		if ($s['id'] === WC_Subscriptions_Admin::$option_prefix . '_allow_switching') {
        			$settings[$k]['options']['variable_cpo_option'] = _x( 'Simple Subscription with Uni CPO options', 'when to allow switching', 'uni-cpo' );
        		}
        	}

        	return $settings;
    }
    // Add the settings to control whether Switching is enabled and how it will behave
    add_filter( 'woocommerce_subscription_settings', 'uni_cpo_add_custom_settings', 99, 1 );

    //
    function uni_cpo_woocommerce_subscriptions_switch_is_identical_product( $is_identical_product, $product_id, $quantity, $variation_id, $subscription, $item ) {

        if ( isset($item['uni_cpo_item_id']) ) {

        	$identical_attributes = true;

            $aHiddenItemMeta = get_post_meta( $item['uni_cpo_item_id'], '_uni_cpo_oa_hidden_meta', true );

            $aCpOptions = array();
            foreach ( $aHiddenItemMeta as $sKey => $sValue ) {
                if ( substr($sKey, 0, strlen('_uni_cpo_oa_')) === '_uni_cpo_oa_' ) {
                    $sNewKey = str_replace("_uni_cpo_oa_", "", $sKey);
                    $sNewKey = ltrim( $sNewKey, '{' );
                    $sNewKey = rtrim( $sNewKey, '}' );

                    $aCpOptions[$sNewKey] = $sValue;
                }
            }

            foreach ( $_POST as $key => $value ) {
                if ( isset($aCpOptions[$key]) && ! empty( $aCpOptions[$key] ) && $aCpOptions[$key] != $value ) {
                    $identical_attributes = false;
                    break;
                }
            }

            if ( $product_id == $item['product_id'] && true == $identical_attributes && $quantity == $item['qty'] ) {
                $is_identical_product = true;
            } else {
                $is_identical_product = false;
            }

            return $is_identical_product;

        }

    }
    //
    add_filter( 'woocommerce_subscriptions_switch_is_identical_product', 'uni_cpo_woocommerce_subscriptions_switch_is_identical_product', 99, 6 );

}

?>