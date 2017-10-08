    var UniCpo;
    window.UniCpo = window.UniCpo || {};

    UniCpo = window.UniCpo = {
        _init: function() {
            // init

            // maps
            jQuery('.uni_cpo_field_type_google_maps').each(function(){
                UniCpo.initFrontGoogleMaps( jQuery(this) );
            });

            // events
            UniCpo._bindEvents();
        },
        _bindEvents: function(){
            // fix autocomplete for touch screen
            jQuery('body').on( 'touchend', '.pac-container', function(e){
                e.stopImmediatePropagation();
            });
        },
        cpo_on: unicpo.cpo_on,
        calc_on: unicpo.calc_on,
        calc_btn_on: unicpo.calc_btn_on,
        _request_sent: false,
        uni_cpo_default_main_product_image: {},
        colorify: function( $layer, hex ) {

            if ( $layer.length > 0 ) {

                // transforms hex to rgb
                var color = Color( hex ),
                    color_rgb = color.toRgb();

                // gets main product image size
                width = $layer.width(),
                height = $layer.height();

                // gets original image size
                var $fake_img = jQuery("<img>").css("display","none").appendTo("body");
                $fake_img[0]['src'] = $layer[0].src;
                
                var w = $fake_img.width(),
                    h = $fake_img.height();
                $fake_img.remove();

                var canvas = document.createElement("canvas"),
                    ctx = canvas.getContext("2d");
                canvas.width = w;
                canvas.height = h;

                ctx.drawImage( $layer.get( 0 ), 0, 0 );
                var imgd = ctx.getImageData( 0, 0, w, h ),
                    pix = imgd.data,
                    unique_color = [color_rgb.r, color_rgb.g, color_rgb.b];

                // Loops through all of the pixels and modifies the components.
                for (var i = 0, n = pix.length; i <n; i += 4) {
                    pix[i] = unique_color[0];
                    pix[i+1] = unique_color[1];
                    pix[i+2] = unique_color[2];
                }

                ctx.putImageData(imgd, 0, 0);

                // put the new image to the DOM
                $layer.attr( 'src', canvas.toDataURL("image/png") );
                $layer.attr( 'width', width );
                $layer.attr( 'height', height );

            }

        },
        /*
        * Map specific functions
        *
        */
        markers: {},
        getGeocoder: function( latLng, geocoder, idField ) {
            geocoder.geocode({'location': latLng}, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        jQuery( idField ).val( results[0].formatted_address );
                    } else {
                        console.info('No results found');
                    }
                } else {
                    console.log('Geocoder failed due to: ' + status);
                    jQuery( document.body ).trigger( 'cpo_geocoder_returned_no_results', [ idField, status ] );
                }
            });
        },
        setValueRoutes: function( route, option_slug, length, map_type, field_name ){
            //console.log(field_name);
            if ( length ) {
                if ( map_type !== 'cargo_calculator' ) {
                    if ( field_name ) {
                        jQuery( '[name='+field_name+']' ).val( route.legs[0].start_address );
                    } else {
                        jQuery('#' + option_slug + '-field-start').val( route.legs[0].start_address );
                        jQuery('#' + option_slug + '-field').val( route.legs[length-1].end_address );
                    }
                }
                jQuery('#' + option_slug + '-field-distance').val( route.legs[length-1].distance.value );
            }
        },
        calculateAndDisplayRoute: function( directionsService, directionsDisplay, option_slug, origin, destination, map_type, travel_mode, field_name ){

            // it's not '_start' input
            if ( field_name.indexOf( 'start' ) === -1 ) {
                field_name = '';
            }
            jQuery.each( UniCpo.markers[option_slug], function( key, obj ){
                if ( key.indexOf( 'start' ) !== -1 ) {
                    origin = obj.getPosition();
                } else {
                    destination = obj.getPosition();
                }
            });

            directionsService.route({
                origin: origin,
                destination: destination,
                travelMode: google.maps.TravelMode[travel_mode]
            }, function(response, status) {
                if (status == 'OK') {
                    var route = response.routes[0];

                    UniCpo.setValueRoutes( route, option_slug, route.legs.length, map_type, field_name );
                    directionsDisplay.setDirections( response );
                    //console.log('directionsService response');
                    uni_cpo_form_processing();
                } else {
                    console.log('Directions request failed due to ' + status);
                    jQuery( document.body ).trigger( 'cpo_directions_returned_no_results', [ field_name, status ] );
                }
            });
        },
        addMarker: function( loc, map, option_slug, field_name ){
            var marker = new google.maps.Marker({
                position: loc,
                map: map
            });
            UniCpo.markers[option_slug][field_name] = marker;
            return marker;
        },
        initFrontGoogleMaps: function( $map_container ){

            // get id options
            var option_slug = $map_container.attr('id'),
                option_data = $map_container.data(),
                map_center_coords = option_data.mapCenter.split(','),
                map_type = option_data.mapType,
                travel_mode = option_data.travelMode,
                map_mode = option_data.mapMode,
                map_zoom = option_data.mapZoom,
                disable_ui = option_data.mapUiDisable,
                latLng = '';

            UniCpo.markers[option_slug] = {};

            // map options by default
            var map_center  = {lat: parseFloat(map_center_coords[0]), lng: parseFloat(map_center_coords[1])};

            // get base location coordinates
            if ( option_data.baseLocation ) {
                latLng = option_data.baseLocation.split(',');
                map_center.lat = parseFloat(latLng[0]);
                map_center.lng = parseFloat(latLng[1]);
            }

            // init map and Geo
            var geocoder = new google.maps.Geocoder,
                mapСore = new google.maps.Map( $map_container.find('.uni-cpo-mappicker')[0], {
                  center: map_center,
                  zoom: map_zoom,
                  gestureHandling: 'cooperative',
                  draggableCursor: "crosshair",
                  fullscreenControl: false,
                  disableDefaultUI: disable_ui,
                  mapTypeId: map_mode
            });

            // init directions
            var directionsDisplay = new google.maps.DirectionsRenderer({
                    draggable: false,
                    map: mapСore,
                }),
                directionsService = new google.maps.DirectionsService;

            // add directions to map
            /*directionsDisplay.addListener('directions_changed', function() {
                var route = directionsDisplay.getDirections().routes[0];
                UniCpo.setValueRoutes( route, option_slug, route.legs.length );
                uni_cpo_form_processing();
            });*/

            if ( map_type == 'distance_to_base' ) {
                // make a fake input name
                var base_field_name = option_slug + '_base';
                // add default location product
                var marker_base = UniCpo.addMarker( map_center, mapСore, option_slug, base_field_name );
                // location product
                var markerStartPosition = marker_base.getPosition();
            }

            if ( map_type !== 'cargo_calculator' ) {
            // change position marker
            /*mapСore.addListener('click', function(e) {

                //first type map (one locations)
                if ( map_type === 'one_location' ) {
                    console.log(UniCpo.markers);
                    if ( ! UniCpo.markers[option_slug] || ! UniCpo.markers[option_slug].length ) {
                        marker = UniCpo.addMarker( e.latLng, mapСore, option_slug );
                    }
                    marker.setPosition(e.latLng);
                }

                // get address from coordinates
                var id_field;
                if ( ( map_type === 'two_locations' ) && ( ! UniCpo.markers[option_slug] || ! UniCpo.markers[option_slug].length ) ) {
                    id_field = '#' + option_slug + '-field-start';
                } else {
                    id_field = '#' + option_slug + '-field';
                }
                UniCpo.getGeocoder( e.latLng, geocoder, id_field );

                // two type map (two locations and distance)
                if ( map_type === 'two_locations' ) {

                    if ( ! UniCpo.markers[option_slug] || ! UniCpo.markers[option_slug].length ) {
                        marker = UniCpo.addMarker( e.latLng, mapСore, option_slug );
                        marker.setPosition(e.latLng);
                    } else {
                        marker.setMap(null);
                        UniCpo.calculateAndDisplayRoute( directionsService, directionsDisplay, option_slug, marker.getPosition(), e.latLng, travel_mode );
                    }
                }

                // three type map (product locations and distance)
                if ( map_type === 'distance_to_base' ) {
                    marker.setMap(null);
                    UniCpo.calculateAndDisplayRoute( directionsService, directionsDisplay, option_slug, marker.getPosition(), e.latLng, travel_mode );
                }

                // calculate
                uni_cpo_form_processing();

            });*/
            }

            // add event change center
            // get "search-locations" field
            $map_container.find('.js-uni-cpo-field-google_maps').each(function(){
                var $search_field = jQuery(this),
                    field_name = this.name;

                if ( $search_field.length ) {

                    UniCpo.markers[option_slug][field_name] = {};

                    // autocomplete options
                    if ( map_type === 'cargo_calculator' ) {
                        var options = {
                            types: ['(cities)']
                            //placeIdOnly: true
                        };
                    } else {
                        var options = {
                            types: ['address']
                            //placeIdOnly: true
                        };
                    }

                    // init Autocomplete
                    var autocomplete = new google.maps.places.Autocomplete( $search_field[0], options);

                    // add autocomplete event
                    google.maps.event.addListener(autocomplete, 'place_changed', function () {

                        var place = autocomplete.getPlace();

                        if ( ! place ) {
                            return;
                        }

                        if ( ! place.geometry ) {
                            // User entered the name of a Place that was not suggested and
                            // pressed the Enter key, or the Place Details request failed.
                            console.log("No details available for input: '" + place.name + "'");
                            jQuery( document.body ).trigger( 'cpo_place_returned_no_results', [ field_name, place.name ] );
                            return;
                        }

                        //console.log(field_name);
                        // check if there is a marker associated with the input that has just been changed
                        if ( ! jQuery.isEmptyObject( UniCpo.markers[option_slug][field_name] ) ) {
                            UniCpo.markers[option_slug][field_name].setMap(null);
                            UniCpo.markers[option_slug][field_name] = {};
                        }
                        // adds marker
                        marker = UniCpo.addMarker( place.geometry.location, mapСore, option_slug, field_name );
                        marker.setPosition(place.geometry.location);
                        mapСore.fitBounds(place.geometry.viewport);

                        //
                        if ( map_type === 'two_locations' || map_type === 'cargo_calculator' ) {
                            //
                            var all_filled = true;
                            jQuery.each( UniCpo.markers[option_slug], function( key, obj ){
                                if ( jQuery.isEmptyObject( obj ) ) {
                                    all_filled = false;
                                }
                            });
                            if ( all_filled ) {
                                marker.setMap(null);
                                UniCpo.calculateAndDisplayRoute( directionsService, directionsDisplay, option_slug, '', '', map_type, travel_mode, field_name );
                            }
                        }

                        //
                        if ( map_type === 'distance_to_base' ) {
                            marker.setMap(null);
                            marker_base.setMap(null);
                            UniCpo.calculateAndDisplayRoute( directionsService, directionsDisplay, option_slug, marker_base.getPosition(), place.geometry.location, map_type, travel_mode );
                        }

                    });

                }

                // trigger autocomplete (press enter)
                $search_field.keypress(function(e) {
                  if (e.which == 13) {
                    google.maps.event.trigger( autocomplete, 'place_changed' );
                    return false;
                  }
                });

            });
        }

    }

    // init
    UniCpo._init();



    jQuery( document ).ready( function( $ ) {
        'use strict';

        if ( UniCpo.calc_on && ! UniCpo.calc_btn_on ) {
            if ( UniCpo._request_sent ) {
                return false;
            }
            // init
            uni_cpo_form_processing();
        } else if ( UniCpo.calc_on && UniCpo.calc_btn_on ) {
            jQuery('.single_add_to_cart_button').prop('disabled', true);
            var priceTag = jQuery(unicpo.price_selector);
            priceTag.html('<span class="cpo-text-zero-pice">'+unicpo.price_vars.price+' '+unicpo.text_after_zero_price+'</span>');
        }

        if ( UniCpo.cpo_on ) {

            UniCpo.uni_cpo_default_main_product_image = uni_cpo_default_image_data();
            uni_cpo_change_img();

            $('.uni-cpo-tooltip, .uni-cpo-image-select-list li a, .uni-cpo-palette-select-list li a, .uni-cpo-text-select-list li a').tooltipster({
                theme: 'tooltipster-noir',
                contentAsHTML: true,
                interactive: true,
                multiple: true,
                animationDuration: 250,
                maxWidth: 350
            });

            var $layered_image_container = $( document.body ).find('.uni_cpo_main_image_layered_image');
            if ( $layered_image_container.length > 0 ) {
                $('.uni-cpo-main-image-bg-colorpicker').wpColorPicker({
                    mode: 'hsv',
                    target: false,
                    palettes: false,
                    controls: 'strip',
                    width: 130,
                    change: function(event, ui) {
                        $('.uni_cpo_main_image_layered_image').css( 'background-color', ui.color.toString());
                    }
                });
                $('.flex-control-thumbs li:last-child').addClass('cpo-layered-thumb');

            }

        }

    });

    //
    function uni_cpo_is_wc_flex_container() {
        $flex_container = jQuery('.flex-viewport');
        if ( $flex_container.length > 0 ) {
            return true;
        } else {
            return false;
        }
    }

    //
    function uni_cpo_wc_image_container() {
        var $elWrapper = jQuery( unicpo.image_selector ),
            $defaultWcImages = $elWrapper.children('div.woocommerce-product-gallery__image');

        if ( $elWrapper.length > 0 && $defaultWcImages.length > 0 ) {
            var $elImageContainer = $defaultWcImages.first();
        } else {
            var $elImageContainer = '';
        }

        return $elImageContainer;
    }

    //
    function uni_cpo_default_image_data(){

        $elImageContainer = uni_cpo_wc_image_container();

        if ( $elImageContainer.length > 0 ) {

            var $img_link = $elImageContainer.find('a'),
                $img = $img_link.find('img');

            var data = {
                fullimageuri : $img_link.attr('href'),
                imagetitle : $img.attr('title'),
                imagealt : $img.attr('alt'),
                imageuri : $img.attr('src'),
                imagesrcset : $img.attr('srcset'),
                imagesizes : $img.attr('sizes')
            };

            return data;

        }

    }

    //
    function uni_cpo_flexslider_go_first(){
        if ( uni_cpo_is_wc_flex_container() ) {
            var slider_data = jQuery('.woocommerce-product-gallery').data('flexslider');
            slider_data.flexslider(0);
        }
    }

    //
    function uni_cpo_change_img(){

        //
        if ( unicpo.image_selector ) {

            var $elImageContainer = uni_cpo_wc_image_container();
            // checks for the standard WC 3.0 main product image container
            if ( $elImageContainer.length > 0 ) {

                var fieldsSelector = ".js-uni-cpo-field-image_select, .js-uni-cpo-field-select";

                var $first_wc_thumb = jQuery('.flex-control-thumbs li').first().find('img');
                //console.log($first_wc_thumb);
                if ( $first_wc_thumb.length > 0 ) {
                    $first_wc_thumb.on( 'click', function(e){
                        uniCpoReplaceProductImage( e.target );
                    });
                }

                // run once on start
                if ( jQuery( fieldsSelector ).not('.uni-cpo-exclude-image-change').length > 0 ) {
                    var uniDropdownsReversed = jQuery( fieldsSelector ).not('.uni-cpo-exclude-image-change').get().reverse(),
                        $firstPossibleEl = '';

                    jQuery( uniDropdownsReversed ).each(function( i, el ){
                        var $el = jQuery( el );
                        if ( $el.val() && ! $firstPossibleEl ) {
                            $firstPossibleEl = $el;
                        }
                    });

                    if ( $firstPossibleEl ) {
                        uniCpoReplaceProductImage( $firstPossibleEl );
                    }
                }

            }

        }

    }

    if ( UniCpo.cpo_on ) {

        if ( UniCpo.calc_on && ! UniCpo.calc_btn_on ) {
            // bind form processing
            jQuery( document.body ).on( "change", unicpo.selector_opts_change, function(){

                if ( UniCpo._request_sent ) {
                    return false;
                }

                uni_cpo_form_processing();
            });
            if ( ! unicpo.total_off ) {
                jQuery(".single_add_to_cart_button").after('<div class="uni-cpo-total"></div>');
            }
        } else if ( UniCpo.calc_on && UniCpo.calc_btn_on ) {
            // bind form processing
            jQuery( document.body ).on( "click", "#js-uni-cpo-calculate-btn", function(){

                if ( UniCpo._request_sent ) {
                    return false;
                }

                uni_cpo_form_processing();
            } );
            jQuery( document.body ).on( "change", unicpo.selector_opts_change, function(){
                jQuery('.single_add_to_cart_button').prop('disabled', true);
            } );
            if ( !unicpo.total_off ) {
                jQuery(".single_add_to_cart_button").after('<div class="uni-cpo-total"></div>');
            }
        }

        //
        if ( jQuery('.js-uni-cpo-field-range_slider').length > 0 ) {
            jQuery('.js-uni-cpo-field-range_slider').each(function(i, el){
                var $el = jQuery(el),
                    elVal = $el.val();

                if ( unicpo.calc_on == true ) {
                    $el.ionRangeSlider({
                        'from': elVal,
                        'min': elVal,
                        onFinish: function(){
                            if ( unicpo.calc_on == true && unicpo.calc_btn_on == false ) {

                                if ( UniCpo._request_sent ) {
                                    return false;
                                }

                                uni_cpo_form_processing();
                            } else if ( unicpo.calc_on == true && unicpo.calc_btn_on == true ) {
                                jQuery('.single_add_to_cart_button').prop('disabled', true);
                            }
                        },
                        onChange: function (data) {

                            var value = (ionRangeParameters.type == 'single' || ionRangeParameters.type === undefined) ? data.from : data.from + ' - '  + data.to;

                            if (ionRangeParameters.type !== 'single') {
                                jQuery('#'+ this.name +'-field').val(data.from); 
                                jQuery('#'+ $el.attr('id') +'-end').val(data.to);
                            }

                            jQuery('#'+$el.attr('id')+'-preview').val(value);
                        },
                    });
                } else {
                    $el.ionRangeSlider({
                        'from': elVal,
                        'min': elVal
                    });
                }
            });
        }

        // 'add to cart' form validation and submit
        jQuery(document).on( 'click', '.single_add_to_cart_button', function(e){
            e.preventDefault();

            var $add_to_cart_btn = jQuery(this),
                $elForm = $add_to_cart_btn.closest("form"),
                allValid,
                uniValid = true;

            // validates the form
            $elForm.parsley({
                    excluded: '[disabled], .qty, .uni-cpo-excluded-from-validation'
                }).validate();
            if ( $elForm.parsley().isValid() ) {
                $add_to_cart_btn.attr('name', '');
                uni_cpo_clear_hidden_fields();
                $elForm.submit();
            } else {
                $add_to_cart_btn.attr('name', 'add-to-cart');
                //console.log('the form is not valid');
            }

        });

    }

    // process the form
    function uni_cpo_form_processing(){

            var item_id = jQuery(".uni_cpo_product_id").val(),
                fields = {},
                elOptionImageUrl,
                elFinalOptionImageUrl,
                elOptionImageSrcset,
                elFinalOptionImageSrcset,
                allValid,
                uniValid = false,
                priceTag = jQuery(unicpo.price_selector),
                priceSuffixTag,
                $addToCartButton = jQuery('.single_add_to_cart_button, button.product_type_simple'),
                $elAddToCartForm = $addToCartButton.closest(".cart"),
                $prodQtyInput = $elAddToCartForm.find('.input-text.qty'),
                prodQty = ( $prodQtyInput.val() ) ? $prodQtyInput.val() : 1,
                $msgOrderingDisabled = jQuery('.js-uni-cpo-ordering-disabled-notice');

            // no cart form fix
            if ( $elAddToCartForm.length == 0 ) {
                return false;
            }

            if ( unicpo.price_suffix_on ) {
                if ( priceTag.next(".woocommerce-price-suffix").length > 0 ) {
                    priceSuffixTag = priceTag.next(".woocommerce-price-suffix");
                } else {
                    priceSuffixTag = priceTag.parent().next(".woocommerce-price-suffix");
                }
            }

            priceTag.html('<span class="cpo-text-zero-pice">'+unicpo.price_vars.price+' '+unicpo.text_after_zero_price+'</span>');
            if ( unicpo.price_suffix_on ) {
                priceSuffixTag.empty();
            }

            // price calculation
            jQuery( unicpo.selector_opts_all ).each(function() {
                fields["action"]                = 'uni_cpo_calculate_price_ajax';
                fields["uni_cpo_product_id"]    = item_id;
                fields["uni_cpo_quantity"]      = prodQty;

                var $el = jQuery(this),
                    elType = this.type || this.tagName.toLowerCase();

                if ( elType == 'checkbox' && !$el.hasClass("uni-cpo-excluded-from-validation") ) {
                    var checkboxes = [];
                    var name_of_checkboxes = this.name.slice(0,-2);
                    jQuery('input[name="'+ this.name +'"]:checked').each(function() {
                        checkboxes.push( jQuery(this).val() );
                    });
                    fields[name_of_checkboxes] = checkboxes;
                    fields[name_of_checkboxes+'_count'] = fields[name_of_checkboxes].length;
                    jQuery('#'+ name_of_checkboxes +'-field-count').val(fields[name_of_checkboxes+'_count']);
                } else if ( elType == 'radio' && !$el.hasClass("uni-cpo-excluded-from-validation") ) {
                    if ( jQuery('input[name="'+ this.name +'"]:checked').length ) {
                        if ( $el.prop('checked') == true ) {
                            fields[this.name] = $el.val();
                        }
                    } else {
                        fields[this.name] = '';
                    }
                } else if ( elType == 'text' && !$el.hasClass("uni-cpo-excluded-from-validation") ) {
                    // datepicker
                    if ( $el.hasClass('js-uni-cpo-field-date_picker') ) {

                        // checks if there is an input for end date
                        if ( jQuery('#'+ this.name +'-field.js-uni-cpo-field-date_picker-start').length > 0 ) {
                            var $elStart = jQuery('#'+ this.name +'-field');
                        }

                        var fieldDateFormat = jQuery('#'+ this.name +'-format').val();

                        fields[this.name+'_enddate'] = $el.val();

                        if ( typeof $elStart !== 'undefined' ) {

                            fields[this.name+'_startdate'] = $elStart.val();

                            var fieldParsedDateStart = moment(fields[this.name+'_startdate'], fieldDateFormat),
                                fieldParsedDateEnd = moment(fields[this.name+'_enddate'], fieldDateFormat),
                                cpoDatesDuration = moment.duration(fieldParsedDateEnd.diff(fieldParsedDateStart)).asDays();
                            //console.log(cpoDatesDuration);

                            fields[this.name] = fields[this.name+'_startdate']+' - '+fields[this.name+'_enddate'];
                            fields[this.name+'_duration'] = cpoDatesDuration;

                            jQuery('#'+ this.name +'-field-duration').val(cpoDatesDuration);
                        } else {
                            fields[this.name] = fields[this.name+'_enddate'];
                        }
                        
                    } else if ( $el.hasClass('js-uni-cpo-field-range_slider') ) {
                        var slider_data = jQuery('#'+ this.name +'-field').data("ionRangeSlider");

                        slider_data.update({from:slider_data.result.from});

                        var $elRangeTo = jQuery('#'+ this.name +'-field-end');

                        if ($elRangeTo.length) {
                            fields[this.name+'_end'] = Number($elRangeTo.val());
                        }

                        jQuery('#'+ this.name +'-field').val(slider_data.result.from);
                        fields[this.name] = slider_data.result.from;

                    } else if ( $el.hasClass("js-uni-cpo-field-color_picker") ) {
                        if ($el.val()) {
                            fields[this.name] = $el.data('price');
                        }
                    } else if ( $el.hasClass("js-uni-cpo-field-google_maps-single") ) {

                        var $container = jQuery('#'+ this.name),
                            map_type = $container.data('map-type');

                        fields[this.name] = $el.val();

                        if ( 'two_locations' === map_type || 'cargo_calculator' === map_type ) {
                            var $elStart = jQuery('#'+ this.name +'-field-start'),
                                $distance = jQuery('#'+ this.name +'-field-distance');

                            fields[this.name+'_start'] = $elStart.val();
                            fields[this.name+'_distance'] = $distance.val();
                        }

                        if ( 'distance_to_base' === map_type ) {
                            var $distance = jQuery('#'+ this.name +'-field-distance');

                            fields[this.name+'_distance'] = $distance.val();
                        }

                    } else {

                        if ( uniCpoIsNumber($el.val()) == false ) {
                            var n = $el.val().replace(/,/,".");
                            $el.val(n);
                            fields[this.name] = $el.val();
                        } else {
                            fields[this.name] = $el.val();
                        }
                        fields[this.name+'_count_spaces'] = fields[this.name].length;
                        var without_spaces = $el.val().replace(/ /g,"");
                        fields[this.name+'_count'] = without_spaces.length;
                        jQuery('#'+ this.name +'-field-count').val( fields[this.name+'_count'] );

                    }
                } else if ( elType == 'select-one' && !$el.hasClass("uni-cpo-excluded-from-validation") ) {
                    fields[this.name] = $el.val();
                } else if ( elType == 'number' && !$el.hasClass("uni-cpo-excluded-from-validation") ) {
                        if ( uniCpoIsNumber($el.val()) == false ) {
                            var n = $el.val().replace(/,/,".");
                            $el.val(n);
                            fields[this.name] = $el.val();
                        } else {
                            fields[this.name] = $el.val();
                        }
                        fields[this.name+'_count_spaces'] = fields[this.name].length;
                        var without_spaces = $el.val().replace(/ /g,"");
                        fields[this.name+'_count'] = without_spaces.length;
                        jQuery('#'+ this.name +'-field-count').val( fields[this.name+'_count'] );
                } else if ( ! $el.hasClass("uni-cpo-excluded-from-validation") ) {
                    fields[this.name] = $el.val();
                }

                // Triggers an event - for each field
                jQuery( document.body ).trigger( 'cpo_options_data_before_validate_event', [ fields ] );

            });
            //console.log(fields);

            // use this trigger to modify data and return it
            var cpo_fields = jQuery( document.body ).triggerHandler( 'cpo_options_all_data_before_validate_event', [ fields ] );
            if ( typeof cpo_fields !== 'undefined' ) {
                fields = cpo_fields;
            }
            

            // validates
            $elAddToCartForm.parsley({
                    excluded: '[disabled], .qty, .uni-cpo-excluded-from-validation'
                }).validate();
            //console.log($elAddToCartForm.parsley({excluded: '[disabled], .qty, .uni-cpo-excluded-from-validation'}));
            if ($elAddToCartForm.parsley().isValid()) {
                uniValid = true;
            } else {
                //console.log('the form is not valid');
            }

            // checks if everything is valid
            if ( fields["uni_cpo_product_id"] && uniValid ) {

                // Triggers an event - all data collected and validated
			    jQuery( document.body ).trigger( 'cpo_options_data_after_validate_event', [ fields ] );

                if ( unicpo.calc_on == true && unicpo.calc_btn_on == false ) {
                    // disables 'add to cart' button
                    $addToCartButton.prop('disabled', false);
                }

                // fires ajax request
                uni_cpo_ajax_request( fields, priceTag, priceSuffixTag );

            } else {

                // hide the message with 'ordering is disabled' msg
                $msgOrderingDisabled.slideUp();

                // disable 'add to cart' button
                $addToCartButton.prop('disabled', true);

                // removes 'total' and 'discount' msgs left from prev request
                jQuery(".uni-cpo-price-discounted").remove();
                jQuery(".uni-cpo-total").empty();

                // Triggers an event - is not valid
			    jQuery( document.body ).trigger( 'cpo_options_data_not_valid_event', [ fields ] );

            }

            //console.log(fields);
	}

    // fires ajax request
    function uni_cpo_ajax_request( fields, priceTag, priceSuffixTag ) {

            var $addToCartButton = jQuery('.single_add_to_cart_button'),
                $msgOrderingDisabled,
                $elAddToCartForm = $addToCartButton.closest(".cart");

                if ( !unicpo.total_off ) {
                    var $totalTag = jQuery(".uni-cpo-total");
                }

                if ( jQuery('.js-uni-cpo-ordering-disabled-notice').length > 0 ) {
                    var $msgOrderingDisabled = jQuery('.js-uni-cpo-ordering-disabled-notice');
                }

                fields.cheaters_always_disable_js = 'true_bro';

                // ajax request
			    jQuery.ajax({
				    type:'post',
	        	    url: unicpo.ajax_url,
	        	    data: fields,
	        	    dataType: 'json',
                    beforeSend: function(){
                        UniCpo._request_sent = true;
                        // removes 'total' and 'discount' msgs left from prev request
                        jQuery(".uni-cpo-price-discounted").remove();
                        if ( ! unicpo.total_off ) {
                            $totalTag.empty();
                        }

                        // adds 'Calculating' msg instead of price
                        priceTag.html("<span class='uni-cpo-calculating'>"+unicpo.calc_text+"</span>");
                        if ( unicpo.price_suffix_on ) {
                            priceSuffixTag.empty();
                        }

                        // removes 'ordering disabled' custom msg
                        if ( typeof $msgOrderingDisabled !== 'undefined' ) {
                            $msgOrderingDisabled.slideUp();
                        }

                        // Triggers an event - on before send ajax request
			            jQuery( document.body ).trigger( 'cpo_options_data_ajax_before_send_event', [ fields ] );

                        // blocks .cart form
	        	        $elAddToCartForm.block({
	        	            message: null,
                            overlayCSS: { background: '#fff url(' + unicpo.loader + ') no-repeat center', backgroundSize: '24px 24px', opacity: 0.6 }
                        });
                    },
	        	    success: function(response) {
	        	        //console.log(response);
	        		    if ('success' === response.status ) {

                            // copy data from response to global object
                            jQuery.extend(unicpo.nov_vars, response.nov_vars);
                            if ( unicpo.reg_vars.length === 0 ) {
                                unicpo.reg_vars = jQuery.extend({}, response.reg_vars);
                            } else {
                                jQuery.extend(unicpo.reg_vars, response.reg_vars);
                            }
                            jQuery.extend(unicpo.price_vars, response.price_vars);
                            jQuery.extend(unicpo.extra_data, response.extra_data);

                            // checks whether disabled for ordering or not
                            if ( unicpo.extra_data.order_product && 'disabled' === unicpo.extra_data.order_product ) {
                                // disables 'add to cart' button
                                $addToCartButton.prop('disabled', true);
                                //
                                if ( typeof $msgOrderingDisabled !== 'undefined' ) {
                                    priceTag.html('');
                                    $msgOrderingDisabled.slideDown();
                                }
                            } else {
                                // adds newly calculated price
                                priceTag.html( unicpo.price_vars.price );
                                if ( unicpo.price_suffix_on ) {
                                    priceSuffixTag.html( unicpo.price_vars.price_suffix );
                                }
                                // adds new 'discount' msg
                                if ( unicpo.price_vars.price_discounted ) {
                                    jQuery(".single_add_to_cart_button").after('<div class="uni-cpo-price-discounted">'+unicpo.price_discount_text+' <span class="uni-cpo-price-discounted-sum">'+unicpo.price_vars.price_discounted+'</span></div>');
                                }
                                // adds new 'total' msg
                                if ( ! unicpo.total_off && unicpo.price_vars.total_suffix ) {
                                    $totalTag.html(unicpo.total_text_start+' '+fields["uni_cpo_quantity"]+' '+unicpo.total_text_end+' <span class="uni-cpo-total-sum">'+unicpo.price_vars.total+'</span> (<span class="uni-cpo-total-incl-excl-tax">'+unicpo.price_vars.total_suffix+'</span>)');
                                } else if ( ! unicpo.total_off && ! unicpo.price_vars.total_suffix ) {
                                    $totalTag.html(unicpo.total_text_start+' '+fields["uni_cpo_quantity"]+' '+unicpo.total_text_end+' <span class="uni-cpo-total-sum">'+unicpo.price_vars.total+'</span>');
                                }
                                // enables 'add to cart' button
                                $addToCartButton.prop('disabled', false);
                            }

                            // unblocks .cart form
                            $elAddToCartForm.unblock();
                            UniCpo._request_sent = false;

                            // Triggers an event - on successful ajax request
			                jQuery( document.body ).trigger( 'cpo_options_data_ajax_success_event', [ fields, response ] );

		        	    } else if ( 'error' === response.status ) {
                            // unblocks .cart form
                            $elAddToCartForm.unblock();
                            UniCpo._request_sent = false;

                            // Triggers an event - on failure ajax request
                            jQuery( document.body ).trigger( 'cpo_options_data_ajax_fail_event', [ fields, response ] );
		        	    }
	        	    },
	        	    error:function(response){
	        	        if ( 'success' !== response.status ) {
	        	            $elAddToCartForm.unblock();
                            UniCpo._request_sent = false;
	        	    	    console.log('Error in ajax request');
					    }
	        	    }
	            });
                return false;

    }