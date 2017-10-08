<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// CPO settings tabs
add_filter('woocommerce_product_data_tabs', 'uni_cpo_add_settings_tab');
function uni_cpo_add_settings_tab( $product_data_tabs ) {

    $product_data_tabs['uni_cpo_settings'] = array(
							        'label'  => __( 'CPO Price Formula', 'uni-cpo' ),
							        'target' => 'uni_cpo_settings_data',
							        'class'  => array( 'hide_if_grouped', 'hide_if_external', 'hide_if_variable' ),
                                );

    $product_data_tabs['uni_cpo_options'] = array(
							        'label'  => __( 'CPO Product Options', 'uni-cpo' ),
							        'target' => 'uni_cpo_options_data',
							        'class'  => array( 'hide_if_grouped', 'hide_if_external', 'hide_if_variable' ),
                                );

    $product_data_tabs['uni_cpo_discounts'] = array(
							        'label'  => __( 'CPO Cart Discounts', 'uni-cpo' ),
							        'target' => 'uni_cpo_discounts_data',
							        'class'  => array( 'hide_if_grouped', 'hide_if_external', 'hide_if_variable' ),
                                );

    return $product_data_tabs;
}

// CPO settings (price formula) tab content
add_action('woocommerce_product_data_panels', 'uni_cpo_add_custom_settings_tab_content');
function uni_cpo_add_custom_settings_tab_content() {

    global $post;
    $bOptionsCreated = false;
    $iOptionsPostId = ( get_post_meta( $post->ID, '_uni_cpo_options_set', true ) ) ? intval(get_post_meta( $post->ID, '_uni_cpo_options_set', true )) : '';
    $oProduct = wc_get_product( $post, $args = array() );
?>
<div id="uni_cpo_settings_data" class="panel woocommerce_options_panel">

    <div class="unicpo-settings-wrapper">

        <div class="unicpo-settings-general uni-clear">
            <div class="unicpo-settings-general-item uni-clear">
                <div class="checkboxWrap">
                    <div class="onoffswitch">
                        <input type="checkbox" name="uni_cpo_display_options_enable" class="onoffswitch-checkbox" id="uni_cpo_display_options_enable"<?php checked(true, get_post_meta( $post->ID, '_uni_cpo_display_options_enable', true ) ) ?>>
                        <label class="onoffswitch-label" for="uni_cpo_display_options_enable">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <h3><?php esc_html_e('Display custom options on the product page?', 'uni-cpo') ?></h3>
                <p><?php esc_html_e('This is the main option of the plugin. By choosing "off" you are entirely disabling the work of the plugin for this product.
                The plugin still may work for other your products, however.', 'uni-cpo') ?></p>
            </div>
            <div class="unicpo-settings-general-item uni-clear">
                <div class="checkboxWrap">
                    <div class="onoffswitch">
                        <input type="checkbox" name="uni_cpo_price_calculation_enable" class="onoffswitch-checkbox" id="uni_cpo_price_calculation_enable"<?php checked(true, get_post_meta( $post->ID, '_uni_cpo_price_calculation_enable', true ) ) ?>>
                        <label class="onoffswitch-label" for="uni_cpo_price_calculation_enable">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <h3><?php esc_html_e('Enable price calculation based on custom options?', 'uni-cpo') ?></h3>
                <p><?php esc_html_e('Sometimes you just need to display custom options without using their values in a math formula as well as calculate the product price.
                Then just disable this option and the product price will be its regular (or sale) price and the custom options added to this product will be used only as an additional
                information in an order meta.', 'uni-cpo') ?>
                <strong><?php esc_html_e('Important: this option will work only if you enable displaying of custom options!', 'uni-cpo') ?></strong></p>

                <div id="js-calc-btn-container">
                    <div class="checkboxWrap">
                        <div class="onoffswitch">
                            <input type="checkbox" name="uni_cpo_price_calculation_btn_enable" class="onoffswitch-checkbox" id="uni_cpo_price_calculation_btn_enable"<?php checked(true, get_post_meta( $post->ID, '_uni_cpo_price_calculation_btn_enable', true ) ) ?>>
                            <label class="onoffswitch-label" for="uni_cpo_price_calculation_btn_enable">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                    <h3><?php esc_html_e('Use a special "calculate" button instead of instant price calculation?', 'uni-cpo') ?></h3>
                    <p><?php esc_html_e('Enable this option if you want to use "calculate" button and perform calculation on click on this button
                    instead of instant price calculation on options defining/chosing.', 'uni-cpo') ?></p>
                </div>

                <div id="js-calc-btn-container">
                    <div class="checkboxWrap">
                        <div class="onoffswitch">
                            <input type="checkbox" name="uni_cpo_layered_image_enable" class="onoffswitch-checkbox" id="uni_cpo_layered_image_enable"<?php checked(true, get_post_meta( $post->ID, '_uni_cpo_layered_image_enable', true ) ) ?>>
                            <label class="onoffswitch-label" for="uni_cpo_layered_image_enable">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                    <h3><?php esc_html_e('Enable layered image functionality?', 'uni-cpo') ?> <?php echo uni_cpo_get_doc_link( 'http://democpo3.te.ua/docs/#colorify-layered-image' ); ?></h3>
                    <p><?php esc_html_e('Enabling this option adds a special slide at the end of WC main product slider and make it possible to add layers-like
                    images for each "palette_select" option as well as change the color of each layer upon selection any color from connected "palette_select" option.', 'uni-cpo') ?></p>
                </div>
            </div>

            <div class="unicpo-settings-general-item uni-clear">
                <h3><?php esc_html_e('Product price', 'uni-cpo') ?></h3>
                <?php if ( $oProduct->get_price() ) { ?>
                    <p><?php echo sprintf( esc_html__('You have set regular (and/or sale) product price, so you are able to use {uni_cpo_price} variable. The value of its variable
                    is %s (equal to the product price).', 'uni-cpo'), $oProduct->get_price() ) ?></p>
                <?php } else { ?>
                <div class="unicpo-alert-info">
                    <i class="fa fa-times-circle"></i>
                    <p><strong><?php esc_html_e('Important: you have not set regular product price yet! Please, set the price on General tab or this
                    product will be free and it will not be possible to sell it as well as use custom product options!', 'uni-cpo') ?></strong></p>
                </div>
                <?php } ?>
            </div>
            <div class="unicpo-settings-general-item uni-clear">
                <h3><?php esc_html_e('Minimal price', 'uni-cpo') ?></h3>
                <p><?php esc_html_e('Calculated product price will not be lower then the value of min. price. Consider this as the lowest possible price for
                ordering this product regardless the calculated value by using the product custom formula. Additionally, prices of products will be displayed
                as "from XX" on archive pages, where XX is the minimal price value of a particular product.', 'uni-cpo') ?>
                <strong><?php esc_html_e('Important: you still have to define a regular product price under General tab!
                Otherwise, this product will be considered as free.', 'uni-cpo') ?></strong></p>
                <input type="text" name="uni_cpo_min_price" class="input-text wc_input_decimal" value="<?php echo get_post_meta( $post->ID, '_uni_cpo_min_price', true ) ?>" />
            </div>
            <div class="unicpo-settings-general-item uni-clear">
                <h3><?php esc_html_e('Max price', 'uni-cpo') ?></h3>
                <p><?php esc_html_e('It is possible to set a max possible price for the product. The calculated price will be compared with this value and
                ordering of the product will be disabled if calculated price is bigger than this value. The text from "Text to display when ordering is disabled" setting
                could be displayed in this case.', 'uni-cpo') ?></p>
                <input type="text" name="uni_cpo_max_price" class="input-text wc_input_decimal" value="<?php echo get_post_meta( $post->ID, '_uni_cpo_max_price', true ) ?>" />
            </div>
            <div class="unicpo-settings-general-item">
                <h3><?php esc_html_e('Formula', 'uni-cpo') ?></h3>
                <p><?php esc_html_e('This is a simple formula for your product. It will be applied if no conditions are added in "Formula conditions settings" section', 'uni-cpo') ?></p>
                <textarea class="unicpo-settings-general-textarea" name="uni_cpo_price_main_formula" cols="30" rows="30" data-widearea="enable" id="uni_cpo_price_main_formula"><?php echo get_post_meta( $post->ID, '_uni_cpo_price_main_formula', true ) ?></textarea>
            </div>
            <div class="unicpo-settings-general-item">
                <h3><?php esc_html_e('Available variables', 'uni-cpo') ?>:</h3>
                <div class="variablesWrap uni-clear">
                    <ul class="uni-clear" id="js-cpo-formula-variables-list">
                    <?php
                    if ( isset($iOptionsPostId) && !empty($iOptionsPostId) ) {
                        echo uni_cpo_list_of_formula_variables( $iOptionsPostId, 'include_nov', $post->ID );
                    }
                    ?>
                    </ul>
                </div>
                <p><?php esc_html_e('These variables are added automatically based on options added on "CPO Product Options" tab. You can click on certain variable and it will be added to the textarea above.', 'uni-cpo') ?></p>
                <div class="unicpo-info-info">
                    <i class="fa fa-info-circle"></i>
                    <p><?php echo sprintf( esc_html__('Typical math operators: *, +, -, /. You can also use "(" and ")" in the formula. Additionally, these functions are available: %s, %s, %s, %s, %s, %s, %s. %s', 'uni-cpo'),
                        '<span class="help_tip" data-tip="'.esc_html__('Syntax: floor(variable)', 'uni-cpo').'">floor</span>',
                        '<span class="help_tip" data-tip="'.esc_html__('Syntax: ceil(variable)', 'uni-cpo').'">ceil</span>',
                        '<span class="help_tip" data-tip="'.esc_html__('Syntax: round(variable, precision)', 'uni-cpo').'">round</span>',
                        '<span class="help_tip" data-tip="'.esc_html__('Syntax: min(var1, var2, ...)', 'uni-cpo').'">min</span>',
                        '<span class="help_tip" data-tip="'.esc_html__('Syntax: max(var1, var2, ...)', 'uni-cpo').'">max</span>',
                        '<span class="help_tip" data-tip="'.esc_html__('Syntax: average(var1, var2, ...)', 'uni-cpo').'">average</span>',
                        '<span class="help_tip" data-tip="'.esc_html__('Syntax: sum(var1, var2, ...)', 'uni-cpo').'">sum</span>',
                        '<strong>'.esc_html__('You cannot use equality symbol ("=" sign) in a maths formula! Please, use Formula Conditional Rules feature if you want to apply different formulas
                        based on user\'s input or options chosen by user.', 'uni-cpo').'</strong>') ?></p>
                    <p><?php esc_html_e('Every option has its unique name that consists of "uni_cpo_" prefix, option slug and the brackets ("{}") from the both sides. For example: "{uni_cpo_width}" or
                    "{uni_cpo_glass_thickness}". The formula example:  {uni_cpo_width}*{uni_cpo_height}+{uni_cpo_glass_thickness}". These special formatted names are variables in a formula and will be
                    changed to the values of "Price / Rate" this option setting during the formula calculation.', 'uni-cpo') ?>
                    </p>
                </div>
            </div>
            <div class="unicpo-settings-general-item">
                <h3><?php esc_html_e('Additional settings for WC quantity field', 'uni-cpo') ?></h3>
                <p><?php esc_html_e('You can set min., max., starting values and step for a quantity field of the product.', 'uni-cpo') ?></p>
                <?php woocommerce_wp_text_input( array( 'id' => '_uni_cpo_min_qty', 'label' => __('Min. quantity value', 'uni-cpo'), 'description' => '', 'placeholder' => '1', 'type' => 'number', 'custom_attributes' => array('step' => '1', 'min' => '1')  ) ); ?>
                <?php woocommerce_wp_text_input( array( 'id' => '_uni_cpo_max_qty', 'label' => __('Max. quantity value', 'uni-cpo'), 'description' => '', 'placeholder' => '', 'type' => 'number', 'custom_attributes' => array('step' => '1', 'min' => '')  ) ); ?>
            </div>
            <div class="unicpo-settings-general-item">
                <h3><?php esc_html_e('Text to display when ordering is disabled', 'uni-cpo') ?></h3>
                <p><?php esc_html_e('Every time you use a special word "disable" instead of actual formula, the product becomes disabled for ordering and the text below is displayed just under the product price. Leave it empty if you do not want to display this message at all.', 'uni-cpo') ?></p>
                <textarea class="unicpo-settings-general-textarea" name="uni_cpo_ordering_disabled_notice" cols="30" rows="15" data-widearea="enable"><?php echo get_post_meta( $post->ID, '_uni_cpo_ordering_disabled_notice', true ) ?></textarea>
            </div>
        </div>

        <hr>

        <div id="unicpo-non-option-variables" class="unicpo-settings-conditionals">
            <div class="unicpo-settings-general-item">
                <div class="checkboxWrap">
                    <div class="onoffswitch">
                        <input type="checkbox" name="uni_cpo_non_option_vars_enable" class="onoffswitch-checkbox" id="uni_cpo_non_option_vars_enable"<?php checked(true, get_post_meta( $post->ID, '_uni_cpo_non_option_vars_enable', true ) ) ?>>
                        <label class="onoffswitch-label" for="uni_cpo_non_option_vars_enable">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <h3><?php esc_html_e('Enable using of non-option variables?', 'uni-cpo') ?> <?php echo uni_cpo_get_doc_link( 'http://moomoo.agency/demo/cpo/docs/#non-option-vars' ); ?></h3>
                <p><?php esc_html_e('Enable/disable using of created non option variables.', 'uni-cpo') ?></p>
            </div>
            <div class="unicpo-settings-general-item">
                <div class="checkboxWrap">
                    <div class="onoffswitch">
                        <input type="checkbox" name="uni_cpo_non_option_vars_wholesale_enable" class="onoffswitch-checkbox" id="uni_cpo_non_option_vars_wholesale_enable"<?php checked(true, get_post_meta( $post->ID, '_uni_cpo_non_option_vars_wholesale_enable', true ) ) ?>>
                        <label class="onoffswitch-label" for="uni_cpo_non_option_vars_wholesale_enable">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <h3><?php esc_html_e('Enable wholesale like fuctionality for non-option variables?', 'uni-cpo') ?> <?php echo uni_cpo_get_doc_link( 'http://moomoo.agency/demo/cpo/docs/#nov-wholesale' ); ?></h3>
                <p><?php esc_html_e('Enabling this functionality will make it possible to set different value/formula on per user role basis', 'uni-cpo') ?></p>
            </div>
            <div class="unicpo-settings-general-item uni-clear">
                <div id="uni_cpo_user_roles_container">
                <h3><?php esc_html_e('Which roles will be used in the wholesale like fuctionality?', 'uni-cpo') ?></h3>
                <?php
                $all_role_names = uni_cpo_get_all_role_names( true );
                $chosen_roles = ( get_post_meta( $post->ID, '_uni_cpo_user_roles_chosen', true ) ) ? get_post_meta( $post->ID, '_uni_cpo_user_roles_chosen', true ) : array();
                if ( ! empty( $all_role_names ) ) {
                    foreach ( $all_role_names as $role_slug => $role_name ) {
                        echo '<div class="uni-cpo-user-role-item">';
                        echo '<input type="checkbox" name="uni_cpo_user_roles_chosen[]" value="' . esc_attr($role_slug) . '"' . checked( true, ( in_array( $role_slug, $chosen_roles ) ? true : false ), false ) . ' />' . esc_attr($role_name);
                        echo '</div>';
                    }
                }
                ?>
                </div>
            </div>
            <div class="unicpo-settings-general-item">
            </div>
            <div class="uni_cpo_ajax_link_container">
                <span class="uni-cpo-non-option-vars-modal-link uni_cpo_ajax_call" data-action="uni_cpo_non_option_vars_show" data-pid="<?php echo $post->ID ?>"><?php esc_html_e('Add/edit non-option variables', 'uni-cpo') ?></span>
                <?php
                if ( get_post_meta( $post->ID, '_uni_cpo_non_option_vars', true ) ) {
                ?>
                <span class="uni-cpo-non-option-vars-delete-link uni_cpo_ajax_call" data-pid="<?php echo $post->ID ?>" data-action="uni_cpo_non_option_vars_delete"><?php esc_html_e('Delete non-option variables', 'uni-cpo') ?></span>
                <?php
                }
                ?>
            </div>
        </div>

        <hr>

        <div id="unicpo-settings-conditionals" class="unicpo-settings-conditionals">
            <div class="unicpo-settings-general-item uni-clear">
                <div class="checkboxWrap">
                    <div class="onoffswitch">
                        <input type="checkbox" name="uni_cpo_formula_conditional_enable" class="onoffswitch-checkbox" id="uni_cpo_formula_conditional_enable"<?php checked(true, get_post_meta( $post->ID, '_uni_cpo_formula_conditional_enable', true ) ) ?>>
                        <label class="onoffswitch-label" for="uni_cpo_formula_conditional_enable">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <h3><?php esc_html_e('Enable using of formula conditional rules?', 'uni-cpo') ?> <?php echo uni_cpo_get_doc_link( 'http://moomoo.agency/demo/cpo/docs/#conditional-formula' ); ?></h3>
                <p><?php esc_html_e('Enable/disable using of created formula conditional rules.', 'uni-cpo') ?></p>
            </div>
            <div class="unicpo-settings-general-item">
            </div>
            <div class="uni_cpo_ajax_link_container">
                <span class="uni-cpo-conditional-modal-link" data-pid="<?php echo $post->ID ?>" data-type="formula" data-title="<?php echo esc_attr__('Formula conditional rules', 'uni-cpo') ?>" data-action-part="uni_cpo_formula_conditional_rule"><?php esc_html_e('Add/edit formula conditional rules', 'uni-cpo') ?></span>
                <span class="uni-cpo-conditional-imex-modal-link" data-pid="<?php echo $post->ID ?>" data-type="formula" data-title="<?php echo esc_attr__('Import/export formula conditional rules', 'uni-cpo') ?>" data-action-part="uni_cpo_formula_conditional_rule_imex"><?php esc_html_e('Export/import formula conditional rules', 'uni-cpo') ?></span>
                <?php
                if ( get_post_meta( $post->ID, '_uni_cpo_formula_rule_options', true ) ) {
                ?>
                <span class="uni-cpo-conditional-delete-link uni_cpo_ajax_call" data-pid="<?php echo $post->ID ?>" data-action="uni_cpo_formula_conditional_rule_delete"><?php esc_html_e('Delete formula conditional rules', 'uni-cpo') ?></span>
                <?php
                }
                ?>
            </div>
        </div>

        <hr>

        <div id="unicpo-weight-conditionals" class="unicpo-settings-conditionals">
            <div class="unicpo-settings-general-item uni-clear">
                <div class="checkboxWrap">
                    <div class="onoffswitch">
                        <input type="checkbox" name="uni_cpo_weight_conditional_enable" class="onoffswitch-checkbox" id="uni_cpo_weight_conditional_enable"<?php checked(true, get_post_meta( $post->ID, '_uni_cpo_weight_conditional_enable', true ) ) ?>>
                        <label class="onoffswitch-label" for="uni_cpo_weight_conditional_enable">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <h3><?php esc_html_e('Enable using of weight conditional rules?', 'uni-cpo') ?> <?php echo uni_cpo_get_doc_link( 'http://moomoo.agency/demo/cpo/docs/#conditional-weight' ); ?></h3>
                <p><?php esc_html_e('Enable/disable using of created weight conditional rules.', 'uni-cpo') ?></p>
            </div>
            <div class="unicpo-settings-general-item">
            </div>
            <div class="uni_cpo_ajax_link_container">
                <span class="uni-cpo-conditional-modal-link" data-pid="<?php echo $post->ID ?>" data-type="weight" data-title="<?php echo esc_attr__('Product weight conditional rules', 'uni-cpo') ?>" data-action-part="uni_cpo_weight_conditional_rule"><?php esc_html_e('Add/edit weight conditional rules', 'uni-cpo') ?></span>
                <span class="uni-cpo-conditional-imex-modal-link" data-pid="<?php echo $post->ID ?>" data-type="weight" data-title="<?php echo esc_attr__('Import/export of product weight conditional rules', 'uni-cpo') ?>" data-action-part="uni_cpo_weight_conditional_rule_imex"><?php esc_html_e('Export/import weight conditional rules', 'uni-cpo') ?></span>
                <?php
                if ( get_post_meta( $post->ID, '_uni_cpo_weight_rule_options', true ) ) {
                ?>
                <span class="uni-cpo-conditional-delete-link uni_cpo_ajax_call" data-pid="<?php echo $post->ID ?>" data-action="uni_cpo_weight_conditional_rule_delete"><?php esc_html_e('Delete weight conditional rules', 'uni-cpo') ?></span>
                <?php
                }
                ?>
            </div>
        </div>

    </div>

</div>

<?php
}
// Save custom fields
function uni_cpo_save_settings( $post_id ) {

    // product general settings
    update_post_meta( $post_id, '_uni_cpo_display_options_enable', ( ( isset($_POST['uni_cpo_display_options_enable']) && !empty($_POST['uni_cpo_display_options_enable']) ) ? true : false ) );
    update_post_meta( $post_id, '_uni_cpo_price_calculation_enable', ( ( isset($_POST['uni_cpo_price_calculation_enable']) && !empty($_POST['uni_cpo_price_calculation_enable']) ) ? true : false ) );
    update_post_meta( $post_id, '_uni_cpo_price_calculation_btn_enable', ( ( isset($_POST['uni_cpo_price_calculation_btn_enable']) && !empty($_POST['uni_cpo_price_calculation_btn_enable']) ) ? true : false ) );
    update_post_meta( $post_id, '_uni_cpo_layered_image_enable', ( ( isset($_POST['uni_cpo_layered_image_enable']) && !empty($_POST['uni_cpo_layered_image_enable']) ) ? true : false ) );

    // the main formula
    update_post_meta( $post_id, '_uni_cpo_price_main_formula', $_POST['uni_cpo_price_main_formula'] );

    // text of notice when ordering is disabled
    update_post_meta( $post_id, '_uni_cpo_ordering_disabled_notice', $_POST['uni_cpo_ordering_disabled_notice'] );

    // qty
    update_post_meta( $post_id, '_uni_cpo_min_qty', $_POST['_uni_cpo_min_qty'] );
    update_post_meta( $post_id, '_uni_cpo_max_qty', $_POST['_uni_cpo_max_qty'] );

    // non option vars
    update_post_meta( $post_id, '_uni_cpo_non_option_vars_enable', ( isset($_POST['uni_cpo_non_option_vars_enable']) && !empty($_POST['uni_cpo_non_option_vars_enable']) ) ? true : false );
    update_post_meta( $post_id, '_uni_cpo_non_option_vars_wholesale_enable', ( isset($_POST['uni_cpo_non_option_vars_wholesale_enable']) && !empty($_POST['uni_cpo_non_option_vars_wholesale_enable']) ) ? true : false );
    update_post_meta( $post_id, '_uni_cpo_user_roles_chosen', ( isset( $_POST['uni_cpo_user_roles_chosen'] ) ) ? $_POST['uni_cpo_user_roles_chosen'] : array() );
    // formula conditional rules
    update_post_meta( $post_id, '_uni_cpo_formula_conditional_enable', ( isset($_POST['uni_cpo_formula_conditional_enable']) && !empty($_POST['uni_cpo_formula_conditional_enable']) ) ? true : false );

    // weight conditional rules
    update_post_meta( $post_id, '_uni_cpo_weight_conditional_enable', ( isset($_POST['uni_cpo_weight_conditional_enable']) && !empty($_POST['uni_cpo_weight_conditional_enable']) ) ? true : false );

    // minimal price
    $fMinPrice = (float)$_POST['uni_cpo_min_price'];
    update_post_meta( $post_id, '_uni_cpo_min_price', $fMinPrice );

    // maximum price
    $fMaxPrice = (float)$_POST['uni_cpo_max_price'];
    update_post_meta( $post_id, '_uni_cpo_max_price', $fMaxPrice );

    // cart discounts
    update_post_meta( $post_id, '_uni_cpo_cart_discounts_enable', ( isset($_POST['uni_cpo_cart_discounts_enable']) && !empty($_POST['uni_cpo_cart_discounts_enable']) ) ? true : false );

}
add_action('woocommerce_process_product_meta', 'uni_cpo_save_settings');


// Uni CPO product options tab
add_action('woocommerce_product_data_panels', 'uni_cpo_add_custom_options_tab_content');
function uni_cpo_add_custom_options_tab_content() {
    global $post;
    $bOptionsCreated = false;
    $iOptionsSetPostId = ( get_post_meta( $post->ID, '_uni_cpo_options_set', true ) ) ? intval(get_post_meta( $post->ID, '_uni_cpo_options_set', true )) : '';
    $oTestOptionsSetPost = get_post($iOptionsSetPostId);
    if ( !empty($iOptionsSetPostId) && $oTestOptionsSetPost ) {
        $bOptionsCreated = true;
    }

    $all_options_sets_args = apply_filters( 'uni_cpo_all_options_sets_args_filter', array( 'post_type' => 'uni_cpo_option', 'post_parent' => 0, 'posts_per_page' => -1 ), 10 );
    $aAllOptionSets = get_posts( $all_options_sets_args );
?>

<div id="uni_cpo_options_data" class="panel woocommerce_options_panel" data-pid="<?php echo get_the_ID(); ?>">

    <div class="unicpo-options-list-empty uni_cpo_ajax_link_container"<?php if ( $bOptionsCreated ) echo ' style="display:none;"'; ?>>

        <h4><?php esc_html_e('You have not created any CPO options yet', 'uni-cpo') ?></h4>
        <input class="cpo-options-create uni_cpo_ajax_call" data-pid="<?php echo get_the_ID(); ?>" data-action="uni_cpo_options_create" type="button" value="<?php esc_html_e('Create options', 'uni-cpo') ?>">
        <?php if ( !empty($aAllOptionSets) ) { ?>
        <div id="uni-precreated-options-container">
            <h4>
                <?php esc_html_e('...or select one of the previously created sets and attach it to this product', 'uni-cpo') ?>
                <?php echo '<a class="uni_doc_link help_tip" target="_blank" data-tip="'.esc_html__('Go to the documentation and read more about this feature', 'uni-cpo').'" href="http://democpo.te.ua/docs/#reusable-options-sets"><i class="fa fa-graduation-cap""></i></a>'; ?>
            </h4>
            <select id="uni-precreated-options-select">
                <?php
                foreach ( $aAllOptionSets as $oPost ) {
                    echo '<option value="'.esc_attr($oPost->ID).'">'.esc_html($oPost->post_title).'</option>';
                }
                ?>
            </select>
            <div class="uni-clear"></div>
            <input id="cpo-options-attach" class="cpo-options-create uni_cpo_ajax_call" data-pid="<?php echo get_the_ID(); ?>" data-action="uni_cpo_options_attach" type="button" value="<?php esc_html_e('Attach', 'uni-cpo') ?>">
        </div>
        <?php } ?>
    </div>

    <div id="unicpo-options-list-created" class="unicpo-options-list-created uni_cpo_ajax_link_container" data-optionsid="<?php echo esc_attr( $iOptionsSetPostId ); ?>"<?php if ( $bOptionsCreated == false ) echo ' style="display:none;"'; ?>>

        <div class="cpo-list-add-element-wrap">
            <span><?php esc_html_e('Add option', 'uni-cpo') ?></span>
        </div>
        <div class="cpo-list-add-element-popup uni-clear">
        <?php
            $aRegisteredOptionsTypes = uni_cpo_get_option_types();
            if ( !empty($aRegisteredOptionsTypes) ) {
                foreach ( $aRegisteredOptionsTypes as $sTypeOfOption ) {
                    $oOption = uni_cpo_get_option( false, $sTypeOfOption );
                    echo '<span class="uni_cpo_ajax_call" data-action="uni_cpo_option_add" data-listid="unicpo-options-list" data-itemtype="'.esc_attr( $sTypeOfOption ).'" data-optionsid="'.esc_attr( $iOptionsSetPostId ).'"><i class="fa '.esc_attr( $oOption->option_icon ).'"></i> '.esc_html( $oOption->option_name ).'</span>';
                }
            }
        ?>
        </div>
        <input id="cpo-list-remove-all-options" class="cpo-list-remove-all-options uni_cpo_ajax_call_confirmation" data-pid="<?php echo get_the_ID(); ?>" data-action="uni_cpo_options_remove_show_dialog" type="button" value="<?php esc_html_e('Remove all', 'uni-cpo') ?>">
        <div class="uni-clear"></div>

        <div id="unicpo-options-list" class="cpo-list-wrapper">
        </div>

    </div>

</div>

<?php
}

// CPO discounts tab content
add_action('woocommerce_product_data_panels', 'uni_cpo_add_custom_discounts_tab_content');
function uni_cpo_add_custom_discounts_tab_content() {
        global $post;
?>
<div id="uni_cpo_discounts_data" class="panel woocommerce_options_panel">

    <div class="unicpo-settings-wrapper">

        <div clas="unicpo-settings-general uni-clear">
            <div class="unicpo-settings-general-item uni-lear">
                <div class="checkboxWrap">
                    <div class="onoffswitch">
                        <input type="checkbox" name="uni_cpo_cart_discounts_enable" class="onoffswitch-checkbox" id="uni_cpo_cart_discounts_enable"<?php checked(true, get_post_meta( $post->ID, '_uni_cpo_cart_discounts_enable', true ) ) ?>>
                        <label class="onoffswitch-label" for="uni_cpo_cart_discounts_enable">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <h3><?php esc_html_e('Enable cart discounts?', 'uni-cpo') ?></h3>
                <p><?php esc_html_e('Enable/disable cart discounts for this product.', 'uni-cpo') ?></p>
            </div>

            <div id="cpo_quantity_discounts_container" class="unicpo-settings-general-item uni_cpo_ajax_link_container uni-clear">

                <h3><?php esc_html_e('Quantity based discount', 'uni-cpo') ?></h3>
                <span class="cpo-discounts-button uni_cpo_ajax_call" data-action="uni_cpo_cart_discounts_show" data-pid="<?php echo $post->ID ?>" data-base="quantity"><?php esc_html_e('Add/modify rules for discount based on quantity', 'uni-cpo') ?></span>
                <?php
                if ( get_post_meta( $post->ID, '_uni_cpo_cart_discount_quantity', true ) ) {
                ?>
                <span class="cpo-discounts-button-delete-link cpo-quantity-delete-link uni_cpo_ajax_call" data-action="uni_cpo_cart_discounts_delete" data-pid="<?php echo $post->ID ?>" data-base="quantity"><?php esc_html_e('Delete rules', 'uni-cpo') ?></span>
                <?php
                }
                ?>
            </div>

            <?php /*
            <div id="cpo_role_cat_discounts_container" class="unicpo-settings-general-item uni_cpo_ajax_link_container uni-clear">

                <h3><?php esc_html_e('User role based discount', 'uni-cpo') ?></h3>
                <span class="cpo-discounts-button uni_cpo_ajax_call" data-action="uni_cpo_cart_discounts_show" data-pid="<?php echo $post->ID ?>" data-base="role"><?php esc_html_e('Add/modify rules for discount based on role/category', 'uni-cpo') ?></span>
                <?php
                if ( get_post_meta( $post->ID, '_uni_cpo_cart_discount_quantity', true ) ) {
                ?>
                <span class="cpo-discounts-button-delete-link cpo-role-cat-delete-link uni_cpo_ajax_call" data-action="uni_cpo_cart_discounts_delete" data-pid="<?php echo $post->ID ?>" data-base="role"><?php esc_html_e('Delete rules', 'uni-cpo') ?></span>
                <?php
                }
                ?>
            </div>
            */ ?>

        </div>

    </div>

</div>
<?php

}
?>