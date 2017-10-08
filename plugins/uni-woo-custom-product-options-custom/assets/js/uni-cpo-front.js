window.Parsley.setLocale( "en" );

// additional Parsley validators
window.Parsley
    .addValidator( 'filemaxmegabytes', {
        requirementType: 'string',
        validateString: function ( value, requirement, parsleyInstance ) {

            var file = parsleyInstance.$element[ 0 ].files;
            var maxBytes = requirement * 1048576;

            if ( file.length == 0 ) {
                return true;
            }

            return file.length === 1 && file[ 0 ].size <= maxBytes;

        },
        messages: {
            en: 'File is to big'
        }
    } )
    .addValidator( 'filemimetypes', {
        requirementType: 'string',
        validateString: function ( value, requirement, parsleyInstance ) {

            var file = parsleyInstance.$element[ 0 ].files;
            if ( file.length == 0 ) {
                return true;
            }

            var allowedMimeTypes = requirement.replace( /\s/g, "" ).split( ',' );
            return allowedMimeTypes.indexOf( file[ 0 ].type ) !== -1;

        },
        messages: {
            en: 'File mime type not allowed'
        }
    } );

function uni_cpo_clear_hidden_fields() {
    // price calculation
    jQuery( unicpo.selector_opts_all ).each( function () {

        var $el = jQuery( this ),
            elType = this.type || this.tagName.toLowerCase();

        if ( elType == 'checkbox' && $el.hasClass( "uni-cpo-excluded-from-validation" ) ) {
            jQuery( 'input[name="' + this.name + '"]:checked' ).each( function () {
                jQuery( this ).prop( "checked", false );
            } );
        } else if ( elType == 'radio' && $el.hasClass( "uni-cpo-excluded-from-validation" ) ) {
            $el.prop( "checked", false );
        } else if ( elType == 'text' && $el.hasClass( "uni-cpo-excluded-from-validation" ) ) {
            $el.val( '' );
        } else if ( elType == 'select-one' && $el.hasClass( "uni-cpo-excluded-from-validation" ) ) {
            $el.val( '' );
        } else if ( $el.hasClass( "uni-cpo-excluded-from-validation" ) ) {
            $el.val( '' );
        }

    } );
}

function uniCpoIsNumber( n ) {
    return !isNaN( parseFloat( n ) ) && isFinite( n );
}

jQuery.fn.uniNumVal = function () {
    return parseFloat( this.val() ) || 0;
}

function uniIsInt( n ) {
    return Number( n ) === n && n % 1 === 0;
}

function uniIsFloat( n ) {
    return Number( n ) === n && n % 1 !== 0;
}

function uniArrayUnique( array ) {
    var a = array.concat();
    for ( var i = 0; i < a.length; ++i ) {
        for ( var j = i + 1; j < a.length; ++j ) {
            if ( a[ i ] === a[ j ] )
                a.splice( j--, 1 );
        }
    }

    return a;
}

//
function uni_get_var_obj_for_cond( type, slug ) {
    var obj = {
        fields: '',
        main: '',
        end: '',
        start: ''
    };
    if ( typeof type !== 'undefined' ) {
        switch ( type ) {
            case 'checkboxes' :
                obj.fields = jQuery( '[name="' + slug + '\\[\\]"]' );
                obj.main = jQuery( '[name="' + slug + '\\[\\]"]:checked' );
                break;
            case 'radio' :
                obj.fields = jQuery( '[name="' + slug + '"]' );
                obj.main = jQuery( '[name="' + slug + '"]:checked' );
                break;
            case 'date_picker' :
                obj.main = jQuery( '#' + slug + '-field' );
                obj.end = jQuery( '#' + slug + '-field-end' );
                break;
            case 'notice' :
                obj.main = '';
                break;
            case 'notice_nonoptionvar' :
                obj.main = '';
                break;
            case 'heading' :
                obj.main = '';
                break;
            case 'divider' :
                obj.main = '';
                break;
            case 'google_maps' :
                obj.main = jQuery( '#' + slug + '-field' );
                obj.start = jQuery( '#' + slug + '-field-start' );
                break;
            case 'range_slider' :
                obj.main = jQuery( '#' + slug + '-field' );
                obj.end = jQuery( '#' + slug + '-field-end' );
                break;
            default :
                obj.main = jQuery( '[name=' + slug + ']' );
                break;
        }
    }
    return obj;
}

//
function uni_get_var_val_for_cond( type, $field, slug ) {
    var val = {
        value: '',
        value_start: '',
        value_end: '',
        values: [],
        duration: 0,
        count: 0,
        count_spaces: 0
    };
    if ( typeof type !== 'undefined' && '' !== $field ) {
        switch ( type ) {
            case 'text_input' :
                val.value = $field.main.val();
                val.count_spaces = val.value.length;
                var without_spaces = val.value.replace( / /g, "" );
                val.count = without_spaces.length;
                break;
            case 'image_select' :
                val.value = $field.main.find( ":selected" ).val();
                break;
            case 'palette_select' :
                val.value = $field.main.find( ":selected" ).val();
                break;
            case 'date_picker' :
                var fieldDateFormat = jQuery( '#' + slug + '-format' ).val();
                val.value = $field.main.val();
                val.value_end = $field.end.val();
                if ( val.value_end ) {
                    var fieldParsedDateStart = moment( val.value, fieldDateFormat ),
                        fieldParsedDateEnd = moment( val.value_end, fieldDateFormat );
                    val.duration = moment.duration( fieldParsedDateEnd.diff( fieldParsedDateStart ) ).asDays();
                }
                break;
            case 'checkboxes' :
                $field.main.each( function ( i ) {
                    val.values[ i ] = jQuery( this ).val();
                } );
                val.count = val.values.length;
                break;
            case 'google_maps' :
                val.value = $field.main.val();
                val.value_start = $field.start.val();
                val.distance = jQuery( '#' + slug + '-field-distance' ).val();
                break;
            case 'range_slider' :

                if ( /;/.test( $field.main.val() ) ) {
                    var field_value = $field.main.val().split( ';' );
                    val.value = field_value[ 0 ];
                    val.end = field_value[ 1 ];
                } else {
                    val.value = $field.main.val();
                }

                break;
            default :
                val.value = $field.main.val();
                break;
        }
    }
    return val;
}

// for palette select type option
var uniCpoTransformPaletteSelect = function ( selector ) {
    var $dropdown = jQuery( selector ),
        val_selected = $dropdown.val(),
        dropdownName = $dropdown.attr( "name" ),
        $dropdownOptions = jQuery( selector + ' option' ),
        is_layered = $dropdown.data( 'layered' );

    $dropdown.after( '<ul id="' + dropdownName + '_list" class="uni-cpo-palette-select-list"></ul><div class="uni-cpo-clear"></div>' );

    var $list = $dropdown.next( '#' + dropdownName + '_list' );
    if ( is_layered && 'yes' === is_layered ) {
        var $layer = jQuery( '#palette-layer-' + dropdownName );
    }
    $dropdownOptions.each( function ( i, el ) {
        var $el = jQuery( el ),
            elVal = $el.val(),
            elColor = $el.data( "color" ),
            elThumbTitle = $el.data( "imagetitle" );

        if ( val_selected && val_selected === elVal ) {
            $list.append( '<li class="active"><a href="#" data-value="' + elVal + '" data-hex="' + elColor + '" style="background:' + elColor + '" title="' + elThumbTitle + '"></a></li>' );
            if ( is_layered && 'yes' === is_layered ) {
                UniCpo.colorify( $layer, elColor );
            }
        } else {
            $list.append( '<li><a href="#" data-value="' + elVal + '" data-hex="' + elColor + '" style="background:' + elColor + '" title="' + elThumbTitle + '"></a></li>' );
        }
    } );

    //set first item as active on ready or highlight default value
    if ( !val_selected ) {
        $list.children( 'li:eq(0)' ).addClass( 'active' );
        if ( is_layered && 'yes' === is_layered ) {
            var $first_option = $dropdownOptions.first(),
                first_opt_color = $first_option.data( "color" );
            UniCpo.colorify( $layer, first_opt_color );
        }
    }

    // update dropdown when links selected
    $list.children( 'li' ).children( 'a' ).on( "click", function ( e ) {
        e.preventDefault();

        var $aEl = jQuery( e.target ),
            $liEl = $aEl.parent(),
            val = $aEl.data( "value" ),
            hex = $aEl.data( "hex" );

        $list.children( 'li.active' ).removeClass( 'active' );
        $liEl.addClass( 'active' );
        $dropdown.val( val );

        if ( is_layered && 'yes' === is_layered ) {
            var last_slide_index = jQuery( '.flex-control-thumbs li' ).index( jQuery( '.flex-control-thumbs li:last' ) );
            if ( jQuery.flexslider ) {
                jQuery( '.woocommerce-product-gallery' ).flexslider( last_slide_index );
            }
            UniCpo.colorify( $layer, hex );
        }

        $dropdown.trigger( "change" );

        // Triggers an event - certain colour selected
        jQuery( document.body ).trigger( 'cpo_options_palette_select_selected_event', [ dropdownName, val, hex ] );

    } );
};

// for image select type option
var uniCpoTransformImageSelect = function ( obj ) {

    var $dropdown = obj,
        dropdownName = $dropdown.attr( 'name' ),
        $dropdownOptions = $dropdown.find( 'option' ),
        val_selected = $dropdown.find( ":selected" ).val();

    $dropdown.after( '<ul id="' + dropdownName + '_list" data-option-slug="' + dropdownName + '" class="uni-cpo-image-select-list"></ul><div class="uni-cpo-clear"></div>' );

    var $list = $dropdown.next( '#' + dropdownName + '_list' );

    $dropdownOptions.each( function ( i, el ) {
        var $el = jQuery( el ),
            elVal = $el.val(),
            elThumbUri = $el.data( "thumbimageuri" ),
            elThumbTitle = $el.data( "imagetitle" );

        if ( !elVal ) {
            return;
        }

        if ( val_selected && val_selected === elVal ) {
            $list.append( '<li class="active"><a href="#" data-value="' + elVal + '" title="' + elThumbTitle + '"><img src="' + elThumbUri + '" alt="" /></a></li>' );
        } else {
            $list.append( '<li><a href="#" data-value="' + elVal + '" title="' + elThumbTitle + '"><img src="' + elThumbUri + '" alt="" /></a></li>' );
        }
    } );

    //set first item as active on ready or highlight default value
    if ( !val_selected ) {
        $list.children( 'li:eq(0)' ).addClass( 'active' );
    }

    // update dropdown when links selected
    $list.children( 'li' ).children( 'a' ).on( 'click', 'img', function ( e ) {
        e.preventDefault();

        var $imgEl = jQuery( e.target ),
            $aEl = $imgEl.parent(),
            $liEl = $aEl.parent(),
            $ulEl = $liEl.parent(),
            valSelected = $aEl.data( 'value' ),
            option_slug = $ulEl.data( 'option-slug' ),
            $dropdown = jQuery( '#' + option_slug + '-field' );

        $ulEl.children( 'li.active' ).removeClass( 'active' );
        $liEl.addClass( 'active' );

        $dropdown.val( valSelected );
        $dropdown.trigger( 'change' );
        uni_cpo_flexslider_go_first();
        uniCpoReplaceProductImage( $dropdown );

        // Triggers an event - certain colour selected
        jQuery( document.body ).trigger( 'cpo_options_image_select_selected_event', [ option_slug, valSelected ] );

    } );
};

// for text select type option
var uniCpoTransfromTextSelect = function ( selector ) {
    var $dropdown = jQuery( selector ),
        val_selected = $dropdown.val(),
        dropdown_name = $dropdown.attr( "name" ),
        $dropdown_options = jQuery( selector + ' option' );

    $dropdown.after( '<ul id="' + dropdown_name + '_list" class="uni-cpo-text-select-list"></ul><div class="uni-cpo-clear"></div>' );

    var $list = $dropdown.next( '#' + dropdown_name + '_list' );

    $dropdown_options.each( function ( i, el ) {
        var $el = jQuery( el ),
            elVal = $el.val(),
            elTitle = $el.data( 'suboptiontitle' ),
            elTooltipTitle = $el.data( 'tooltiptext' );

        if ( val_selected && val_selected === elVal ) {
            $list.append( '<li class="active"><a href="#" data-value="' + elVal + '" title="' + elTooltipTitle + '">' + elTitle + '</a></li>' );
        } else {
            $list.append( '<li><a href="#" data-value="' + elVal + '" title="' + elTooltipTitle + '">' + elTitle + '</a></li>' );
        }
    } );

    //set first item as active on ready or highlight default value
    if ( !val_selected ) {
        $list.children( 'li:eq(0)' ).addClass( 'active' );
    }

    // update dropdown when links selected
    $list.children( 'li' ).children( 'a' ).on( "click", function ( e ) {
        e.preventDefault();

        var $aEl = jQuery( e.target ),
            $liEl = $aEl.parent(),
            val = $aEl.data( "value" );

        $list.children( 'li.active' ).removeClass( 'active' );
        $liEl.addClass( 'active' );
        $dropdown.val( val );

        $dropdown.trigger( "change" );

        // Triggers an event - certain colour selected
        jQuery( document.body ).trigger( 'cpo_options_text_select_selected_event', [ dropdown_name, val ] );

    } );
};

// for file upload type option
var uniCpoBetterFileUpload = function ( selector ) {

    var $input = jQuery( selector ),
        $label = $input.nextAll( 'label' ),
        labelVal = $label.html();

    // empties and re-validates
    $input.val( '' );
    $input.parsley().validate();

    $input.on( 'change', function ( e ) {
        var fileName = '';

        if ( this.files && this.files.length > 1 ) {
            fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
        } else if ( e.target.value ) {
            fileName = e.target.value.split( '\\' ).pop();
        }

        if ( fileName ) {
            $label.find( 'span' ).html( fileName );
        } else {
            $label.html( labelVal );
        }
    } );

    // Firefox bug fix
    $input
        .on( 'focus', function () {
            $input.addClass( 'has-focus' );
        } )
        .on( 'blur', function () {
            $input.removeClass( 'has-focus' );
        } );
};

//
var uniCpoReplaceProductImage = function ( target ) {

    if ( target instanceof jQuery ) {
        var $dropdown = target;
    } else {
        var $dropdown = jQuery( target );
    }

    var $elImageContainer = uni_cpo_wc_image_container();
    //
    if ( !$elImageContainer ) {
        return false;
    }

    var $img_link = $elImageContainer.find( 'a' ),
        $img = $img_link.find( 'img' );

    var uniDefDataObj = UniCpo.uni_cpo_default_main_product_image;
    if ( typeof uniDefDataObj === 'undefined' ) {
        console.info( 'Uni CPO: the default image or its container is not found! For more info please read the documentation: http://moomoo.agency/demo/cpo/docs/#faq' );
        return false;
    }

    //
    if ( $dropdown.hasClass( 'uni-cpo-exclude-image-change' ) ) {
        return false;
    }

    var uniOptionDataObj = {};

    uniOptionDataObj.fullimageuri = $dropdown.find( ':selected' ).data( 'fullimageuri' ),
        uniOptionDataObj.imagetitle = $dropdown.find( ':selected' ).data( 'imagetitle' ),
        uniOptionDataObj.imageuri = $dropdown.find( ':selected' ).data( 'imageuri' ),
        uniOptionDataObj.imagesrcset = $dropdown.find( ':selected' ).data( 'imagesrcset' );
    uniOptionDataObj.imagesizes = $dropdown.find( ':selected' ).data( 'imagesizes' );

    //console.log(uniOptionDataObj);

    if ( $dropdown.hasClass( 'uni-cpo-excluded-from-validation' ) ) {
        var uniFinalDataObj = uniDefDataObj;
    } else {
        if ( typeof uniOptionDataObj.imageuri !== 'undefined' && uniOptionDataObj.imageuri !== uniDefDataObj.imageuri ) {
            var uniFinalDataObj = uniOptionDataObj;
        } else {
            var uniFinalDataObj = uniDefDataObj;
        }
    }

    //console.log(uniFinalDataObj);

    $img.parent().attr( 'href', uniFinalDataObj.fullimageuri );

    $img.parent().attr( 'title', uniFinalDataObj.imagetitle );
    $img.attr( 'title', uniFinalDataObj.imagetitle );
    $img.attr( 'alt', uniFinalDataObj.imagetitle );

    $img.attr( 'src', uniFinalDataObj.imageuri );
    $img.attr( 'srcset', uniFinalDataObj.imagesrcset );
    $img.attr( 'sizes', uniFinalDataObj.imagesizes );

    // Triggers an event
    jQuery( document.body ).trigger( 'cpo_options_product_image_replaced_event', [ $dropdown, $img, uniFinalDataObj ] );

};