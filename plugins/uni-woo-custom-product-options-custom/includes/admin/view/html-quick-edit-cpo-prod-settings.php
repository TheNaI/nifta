<?php
/**
 * Admin View: Quick Edit CPO Product Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$aAllOptionSets = get_posts( array( 'post_type' => 'uni_cpo_option', 'post_parent' => 0, 'posts_per_page' => -1 ) );
?>

        <br class="clear" />
		<h4><?php esc_html_e( 'Uni CPO Quick Settings', 'uni-cpo' ); ?></h4>

		<label>
		    <span class="title"><?php esc_html_e( 'Options set', 'woocommerce' ); ?></span>
			<span class="input-text-wrap">
			    <select name="uni_cpo_options_set">
                    <option value=""><?php esc_html_e( 'Choose...', 'uni-cpo' ); ?></option>
                <?php
                foreach ( $aAllOptionSets as $oPost ) {
                    echo '<option value="'.esc_attr($oPost->ID).'">'.esc_html($oPost->post_title).'</option>';
                }
                ?>
				</select>
			</span>
		</label>

		<label>
			<input type="checkbox" name="uni_cpo_display_options_enable" value="1">
			<span class="checkbox-title"><?php esc_html_e( 'Display custom options on the product page?', 'uni-cpo' ); ?></span>
		</label>

		<label>
			<input type="checkbox" name="uni_cpo_price_calculation_enable" value="1">
			<span class="checkbox-title"><?php esc_html_e( 'Enable price calculation based on custom options?', 'uni-cpo' ); ?></span>
		</label>

        <label>
		    <span class="title"><?php esc_html_e( 'Minimal price', 'uni-cpo' ); ?></span>
			<span class="input-text-wrap">
			    <input type="text" name="uni_cpo_min_price" class="text wc_input_decimal" placeholder="<?php esc_attr_e( 'Minimal price', 'uni-cpo' ); ?>" value="">
			</span>
		</label>
        <br class="clear" />

		<label>
		    <span class="title"><?php esc_html_e( 'Formula', 'uni-cpo' ); ?></span>
			<span class="input-text-wrap">
			    <textarea autocomplete="off" cols="22" rows="1" name="uni_cpo_price_main_formula"></textarea>
			</span>
		</label>
		<br class="clear" />
