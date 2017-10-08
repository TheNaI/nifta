/*global ajaxurl, inlineEditPost, inlineEditL10n */
jQuery(function( $ ) {
	$( '#the-list' ).on( 'click', '.editinline', function() {

		inlineEditPost.revert();

		var post_id = $( this ).closest( 'tr' ).attr( 'id' );

		post_id = post_id.replace( 'post-', '' );

		var $uni_cpo_inline_data = $( '#uni_cpo_inline_' + post_id );

		var uni_cpo_options_set = $uni_cpo_inline_data.find( '.uni_cpo_options_set' ).text(),
            uni_cpo_display_options_enable = $uni_cpo_inline_data.find( '.uni_cpo_display_options_enable' ).text(),
			uni_cpo_price_calculation_enable = $uni_cpo_inline_data.find( '.uni_cpo_price_calculation_enable' ).text(),
            uni_cpo_min_price = $uni_cpo_inline_data.find( '.uni_cpo_min_price' ).text(),
            uni_cpo_price_main_formula = $uni_cpo_inline_data.find( '.uni_cpo_price_main_formula' ).text();

        $( 'select[name="uni_cpo_options_set"] option[value="' + uni_cpo_options_set + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );

		if ( '1' === uni_cpo_display_options_enable ) {
			$( 'input[name="uni_cpo_display_options_enable"]', '.inline-edit-row' ).attr( 'checked', 'checked' );
		} else {
			$( 'input[name="uni_cpo_display_options_enable"]', '.inline-edit-row' ).removeAttr( 'checked' );
		}

        if ( '1' === uni_cpo_price_calculation_enable ) {
			$( 'input[name="uni_cpo_price_calculation_enable"]', '.inline-edit-row' ).attr( 'checked', 'checked' );
		} else {
			$( 'input[name="uni_cpo_price_calculation_enable"]', '.inline-edit-row' ).removeAttr( 'checked' );
		}

        $( 'input[name="uni_cpo_min_price"]', '.inline-edit-row' ).text( uni_cpo_min_price );
        $( 'textarea[name="uni_cpo_price_main_formula"]', '.inline-edit-row' ).text( uni_cpo_price_main_formula );

	});

});
