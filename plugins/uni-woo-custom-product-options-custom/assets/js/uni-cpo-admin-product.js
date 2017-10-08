window.Parsley.setLocale( "en" );

/* wideArea */
(function(g,b){"object"===typeof exports?b(exports):"function"===typeof define&&define.amd?define(["exports"],b):b(g)})(this,function(g){function b(a){this._targetElement=a;this._options={wideAreaAttr:"data-widearea",exitOnEsc:!0,defaultColorScheme:"light",closeIconLabel:"Close WideArea",changeThemeIconLabel:"Toggle Color Scheme",fullScreenIconLabel:"WideArea Mode"};var f=this;a=this._targetElement.querySelectorAll("textarea["+this._options.wideAreaAttr+"='enable']");for(var c=function(){n.call(f,
this)},e=a.length-1;0<=e;e--){var d=a[e],h=document.createElement("div"),k=document.createElement("div"),b=document.createElement("a");h.className="widearea-wrapper";k.className="widearea-icons";b.className="widearea-icon fullscreen";b.title=this._options.fullScreenIconLabel;h.style.width=parseInt(j(d,"width"))+"px";h.style.height=j(d,"height");d.style.width=j(d,"width");d.style.height=j(d,"height");b.href="javascript:void(0);";b.onclick=c;var g=d.cloneNode();g.value=d.value;h.appendChild(g);k.appendChild(b);
h.appendChild(k);d.parentNode.replaceChild(h,d)}}function j(a,f){var c="";a.currentStyle?c=a.currentStyle[f]:document.defaultView&&document.defaultView.getComputedStyle&&(c=document.defaultView.getComputedStyle(a,null).getPropertyValue(f));return c.toLowerCase?c.toLowerCase():c}function n(a){var f=this;a=a.parentNode.parentNode.querySelector("textarea");var c=a.cloneNode();c.className=("widearea-fullscreen "+a.className).replace(/^\s+|\s+$/g,"");a.className=("widearea-fullscreened "+a.className).replace(/^\s+|\s+$/g,
"");var e=document.createElement("div");e.className="widearea-controlPanel";var d=document.createElement("a");d.href="javascript:void(0);";d.className="widearea-icon close";d.title=this._options.closeIconLabel;d.onclick=function(){m.call(f)};var b=document.createElement("a");b.href="javascript:void(0);";b.className="widearea-icon changeTheme";b.title=this._options.changeThemeIconLabel;b.onclick=function(){p.call(f)};e.appendChild(d);e.appendChild(b);d=document.createElement("div");d.className="widearea-overlayLayer "+
this._options.defaultColorScheme;d.appendChild(c);d.appendChild(e);document.body.appendChild(d);c.focus();c.value=a.value;this._onKeyDown=function(a){27===a.keyCode&&f._options.exitOnEsc&&m.call(f);9==a.keyCode&&(a.preventDefault(),a=c.selectionStart,c.value=c.value.substring(0,a)+"\t"+c.value.substring(c.selectionEnd),c.selectionEnd=a+1)};window.addEventListener?window.addEventListener("keydown",f._onKeyDown,!0):document.attachEvent&&document.attachEvent("onkeydown",f._onKeyDown)}function p(){var a=
document.querySelector(".widearea-overlayLayer");a.className=/dark/gi.test(a.className)?a.className.replace("dark","light"):a.className.replace("light","dark")}function m(){var a=document.querySelector("textarea.widearea-fullscreened"),b=document.querySelector(".widearea-overlayLayer"),c=b.querySelector("textarea");a.focus();a.value=c.value;a.className=a.className.replace(/widearea-fullscreened/gi,"");b.parentNode.removeChild(b);window.removeEventListener?window.removeEventListener("keydown",this._onKeyDown,
!0):document.detachEvent&&document.detachEvent("onkeydown",this._onKeyDown)}var l=function(a){if("string"===typeof a){if(a=document.querySelector(a))return new b(a);throw Error("There is no element with given selector.");}return new b(document.body)};l.version="0.1.3";l.fn=b.prototype={clone:function(){return new b(this)},setOption:function(a,b){this._options[a]=b;return this},setOptions:function(a){var b=this._options,c={},e;for(e in b)c[e]=b[e];for(e in a)c[e]=a[e];this._options=c;return this}};
return g.wideArea=l});


jQuery( document ).ready( function ( $ ) {
    'use strict';

    var UniCpo;

    window.UniCpo = window.UniCpo || {};

    UniCpo = window.UniCpo = {
        stupidtable: {
            init: function () {
                $( '.woocommerce_order_items' ).stupidtable();
                $( '.woocommerce_order_items' ).on( 'aftertablesort', this.add_arrows );
            },

            add_arrows: function ( event, data ) {
                var th = $( this ).find( 'th' );
                var arrow = data.direction === 'asc' ? '&uarr;' : '&darr;';
                var index = data.column;
                th.find( '.wc-arrow' ).remove();
                th.eq( index ).append( '<span class="wc-arrow">' + arrow + '</span>' );
            }
        },
        _initTipTip: function () {
            var tiptip_args = {
                'attribute': 'data-tip',
                'fadeIn': 50,
                'fadeOut': 50,
                'delay': 200
            };
            $( ".uni_help_tip" ).tipTip( tiptip_args );
        }
    }

    UniCpo._initTipTip();

    //////////////////////////////////////////////////////////////////////////////////////
    // nestable
    //////////////////////////////////////////////////////////////////////////////////////
    // nestable - nested list of options with drag and drop items
    if ( typeof unicpooptions !== 'undefined' && unicpooptions.fields_structure ) {
        uni_cpo_generate_options_list( unicpooptions.fields_structure );
    }
    //
    function uni_cpo_generate_options_list( structure ) {
        $( '#unicpo-options-list' ).nestable( {
            listNodeName: 'ol',
            itemNodeName: 'li',
            handleNodeName: 'div',
            contentNodeName: 'div',
            rootClass: 'cpo-list-wrapper',
            listClass: 'cpo-list',
            itemClass: 'cpo-list-item',
            dragClass: 'cpo-list-dragel',
            handleClass: 'cpo-list-handle',
            contentClass: 'cpo-list3-content uni-clear',
            collapsedClass: 'cpo-list-collapsed',
            placeClass: 'cpo-list-placeholder',
            noDragClass: 'cpo-list-nodrag',
            noChildrenClass: 'cpo-list-nochildren',
            emptyClass: 'cpo-list-empty',
            expandBtnHTML: '',
            collapseBtnHTML: '',
            maxDepth: 2,
            group: 1,
            json: structure,
            contentCallback: function ( item ) {
                var content = '';
                //console.log(item);
                content += '<div class="cpo-list-item-icon help_tip" data-tip="' + item.itemtype + '"><i class="fa ' + item.icon + '"></i></div>';
                content += '<span class="cpo-list-item-slug">' + item.title;
                if ( item.required ) {
                    content += '<i class="fa fa-dot-circle-o cpo-list-item-required"></i>';
                }
                if ( item.rules && item.rules === 'on' ) {
                    content += '<i class="fa fa-cog cpo-list-item-rules"></i>';
                } else if ( item.rules && item.rules === 'off' ) {
                    content += '<i class="fa fa-cog cpo-list-item-rules-off"></i>';
                }
                content += '</span>';
                content += '<div class="cpo-list-item-panel uni-clear">';
                content += '<span class="edit-list-item-btn help_tip" data-tip="' + unicpo.edit_tip_option_text + '">' + unicpo.edit_option_text + '</span>';
                content += '<span class="copy-list-item-btn help_tip" data-tip="' + unicpo.copy_tip_option_text + '">' + unicpo.copy_option_text + '</span>';
                content += '<span class="remove-list-item-btn uni_cpo_ajax_call_confirmation help_tip" data-action="uni_cpo_option_delete_show_dialog" data-tip="' + unicpo.remove_tip_option_text + '">' + unicpo.remove_option_text + '</span>';
                content += '</div>';
                return content;
            },
            itemRenderer: function ( item_attrs, content, children, options, item ) {
                var item_attrs_string = $.map( item_attrs, function ( value, key ) {
                    return ' ' + key + '="' + value + '"';
                } ).join( ' ' );

                var html = '<' + options.itemNodeName + item_attrs_string + '>';
                html += '<' + options.handleNodeName + ' class="' + options.handleClass + '"><i class="fa fa-bars"></i>';
                html += '</' + options.handleNodeName + '>';
                html += '<' + options.contentNodeName + ' class="' + options.contentClass + '">';
                html += content;
                html += '</' + options.contentNodeName + '>';
                html += children;
                html += '</' + options.itemNodeName + '>';

                return html;
            }
        } ).on( 'change', function ( e ) {
            uni_update_options_structure( $( e.target ), e );
            // debug
            //updateOutput($('#unicpo-options-list').data('output', $('#cpo-list-output')), 'serialize');
            //updateOutput($('#unicpo-options-list').data('output', $('#cpo-list-output-new')), 'asNestedSet');
        } );
    }

    // debug
    //updateOutput($('#unicpo-options-list').data('output', $('#cpo-list-output')), 'serialize');
    //updateOutput($('#unicpo-options-list').data('output', $('#cpo-list-output-new')), 'asNestedSet');

    // saves via ajax parent-child options structure
    function uni_update_options_structure( $list, event ) {
        event = event || '';
        var structure = $list.nestable( 'serialize' ),
            wrapper = $( "#unicpo-options-list-created" );

        if ( structure ) {

            var sendData = {
                action: 'uni_cpo_structure_update',
                structure: structure,
                optionsid: wrapper.data( "optionsid" ),
                productid: wrapper.parent().data( "pid" ),
                cheaters_always_disable_js: 'true_bro',
            };

            $.ajax( {
                type: 'post',
                url: ajaxurl,
                data: sendData,
                dataType: 'json',
                beforeSend: function ( response ) {
                    wrapper.block( {
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    } );
                },
                success: function ( response ) {
                    //console.log(response);
                    if ( response.status == "success" ) {
                        $( "#js-cpo-formula-variables-list" ).html( response.formulavarslist );
                        if ( typeof event.type !== 'undefined' && event.type == 'change' ) {
                            unicpooptions.fields_structure = response.updated_structure;
                            var rebuildArgs = [ unicpooptions.fields_structure ];
                            $list.nestable( 'rebuild', rebuildArgs );
                        }
                        block_ui_change_to_green();
                        wrapper.unblock();
                    } else if ( response.status == "error" ) {
                        block_ui_change_to_red();
                        wrapper.unblock();
                    }
                },
                error: function ( response ) {
                    block_ui_change_to_red();
                    wrapper.unblock();
                }
            } );
        }
    }


    //////////////////////////////////////////////////////////////////////////////////////
    // callbacks
    //////////////////////////////////////////////////////////////////////////////////////
    //
    function uni_cpo_options_create_callback( pId, optionsId ) {
        $( "#unicpo-options-list-created" ).slideDown( "fast", function () {

            var $el = $( this ),
                $listRemoveButton = $( "#cpo-list-remove-all-options" );

            $el.attr( "data-pid", pId );
            $el.attr( "data-optionsid", optionsId );
            $el.data( "pid", pId );
            $el.data( "optionsid", optionsId );
            $( ".cpo-list-add-element-popup span" ).each( function ( i, el ) {
                //console.log(el);
                $( el ).data( "optionsid", optionsId );
                $( el ).attr( "data-optionsid", optionsId );
            } );
            $listRemoveButton.data( "pid", pId );
            $listRemoveButton.attr( "data-pid", pId );
        } );
    }

    //
    function uni_cpo_options_remove_callback() {

        var $listCreated = $( ".unicpo-options-list-created" ),
            $listRemoveButton = $( "#cpo-list-remove-all-options" );

        $listRemoveButton.data( "pid", "" );
        $listRemoveButton.attr( "data-pid", "" );
        $( ".cpo-list-add-element-popup span" ).each( function ( e ) {
            $( e.target ).data( "optionsid", "" );
            $( e.target ).attr( "data-optionsid", "" );
        } );

        $listCreated.attr( "data-pid", "" );
        $listCreated.attr( "data-optionsid", "" );
        $listCreated.data( "pid", "" );
        $listCreated.data( "optionsid", "" );
        $listCreated.hide();
        if ( typeof unicpooptions !== 'undefined' && unicpooptions.fields_structure ) {
            unicpooptions.fields_structure = [];
        }
        $( "#unicpo-options-list" ).empty();
        $( "#unicpo-options-list" ).nestable( "reset" );
        $( ".unicpo-options-list-empty" ).slideDown( "fast" );
    }



    //////////////////////////////////////////////////////////////////////////////////////
    // form submit and links handlers
    //////////////////////////////////////////////////////////////////////////////////////
    // form submit
    $( document ).on( "click", ".uni_cpo_submit", function ( e ) {
        var submit_button = $( this ),
            this_form = submit_button.closest( ".uni_cpo_form" );

        this_form.submit();
    } );

    $.fn.serializeObject = function () {

        var self = this,
            json = {},
            push_counters = {},
            patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key": /[a-zA-Z0-9_]+|(?=\[\])/g,
                "push": /^$/,
                "fixed": /^\d+$/,
                "named": /^[a-zA-Z0-9_]+$/
            };


        this.build = function ( base, key, value ) {
            base[ key ] = value;
            return base;
        };

        this.push_counter = function ( key ) {
            if ( push_counters[ key ] === undefined ) {
                push_counters[ key ] = 0;
            }
            return push_counters[ key ]++;
        };

        $.each( $( this ).serializeArray(), function () {

            // skip invalid keys
            if ( !patterns.validate.test( this.name ) ) {
                return;
            }

            var k,
                keys = this.name.match( patterns.key ),
                merge = this.value,
                reverse_key = this.name;

            while ( ( k = keys.pop() ) !== undefined ) {

                // adjust reverse_key
                reverse_key = reverse_key.replace( new RegExp( "\\[" + k + "\\]$" ), '' );

                // push
                if ( k.match( patterns.push ) ) {
                    merge = self.build( [], self.push_counter( reverse_key ), merge );
                }

                // fixed
                else if ( k.match( patterns.fixed ) ) {
                    merge = self.build( [], k, merge );
                }

                // named
                else if ( k.match( patterns.named ) ) {
                    merge = self.build( {}, k, merge );
                }

            }

            json = $.extend( true, json, merge );
        } );

        return json;
    };

    $( document ).on( "submit", ".uni_cpo_form", function ( e ) {

        var $this_form = $( this ),
            this_form_action = $this_form.find( "input[name=action]" ).val(),
            action = $this_form.attr( 'action' );

        //console.log($this_form.find('.uni-cpo-search-locations-lat-lng').val());
        var form_valid = $this_form.parsley( {
            excluded: '[disabled], .rule-value-container input'
        } ).validate();

        if ( form_valid ) {

            var dataToSend,
                dataToSendOldMethod = $this_form.serialize(),
                dataToSendJsonObj = {};

            // partly supported for now
            if ( this_form_action == 'uni_cpo_field_settings_save' || this_form_action == 'uni_cpo_formula_conditional_rule_save' ||
                this_form_action == 'uni_cpo_weight_conditional_rule_save' || this_form_action === 'uni_cpo_cart_discounts_save' ||
                this_form_action == 'uni_cpo_formula_conditional_rule_imex_save' || this_form_action == 'uni_cpo_weight_conditional_rule_imex_save' ||
                this_form_action === 'uni_cpo_non_option_vars_save'
            ) {
                dataToSendJsonObj.action = this_form_action;
                dataToSendJsonObj.form_data = JSON.stringify( $this_form.serializeObject() );

                dataToSendJsonObj.cheaters_always_disable_js = 'true_bro';

                dataToSend = dataToSendJsonObj;

            } else if ( this_form_action == 'uni_cpo_order_item_options_save' ) {
                dataToSendJsonObj.action = this_form_action;
                dataToSendJsonObj.form_data = JSON.stringify( $this_form.serializeObject() );

                dataToSendJsonObj.cheaters_always_disable_js = 'true_bro';
                dataToSendJsonObj.price = $( '#cpo_order_add_option_calculated_price' ).val();

                dataToSend = dataToSendJsonObj;
            } else {
                dataToSend = dataToSendOldMethod;
                dataToSend = dataToSend + '&cheaters_always_disable_js=true_bro';
            }

            $.ajax( {
                type: 'post',
                url: action,
                data: dataToSend,
                //dataType: 'json',
                beforeSend: function () {
                    $this_form.block( {
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    } );
                },
                success: function ( response ) {
                    if ( response.status == "success" ) {
                        block_ui_change_to_green();
                        $this_form.unblock();
                        $this_form.find( ".uni_cpo_submit" ).removeClass( "uni-cpo-settings-error uni-cpo-settings-unsaved" ).addClass( "uni-cpo-settings-saved" );

                        // callbacks
                        switch ( this_form_action ) {
                        case 'uni_cpo_options_remove':
                            cpoConfirmationModal.close();
                            uni_cpo_options_remove_callback();
                            break;

                        case 'uni_cpo_option_delete':
                            cpoConfirmationModal.close();
                            if ( typeof unicpooptions !== 'undefined' && unicpooptions.fields_structure ) {
                                var indexOfChosenOption, indexOfChosenChildOption;
                                $.each( unicpooptions.fields_structure, function ( i, val ) {
                                    if ( val.id == response.oid ) {
                                        indexOfChosenOption = i;
                                    }
                                    if ( val.children ) {
                                        $.each( val.children, function ( k, child ) {
                                            if ( child.id == response.oid ) {
                                                indexOfChosenChildOption = k;
                                                indexOfChosenOption = i;
                                            }
                                        } );
                                    }
                                } );
                                if ( typeof indexOfChosenChildOption !== 'undefined' ) {
                                    unicpooptions.fields_structure[ indexOfChosenOption ].children.splice( indexOfChosenChildOption, 1 );
                                } else {
                                    unicpooptions.fields_structure.splice( indexOfChosenOption, 1 );
                                }
                                var rebuildArgs = [ unicpooptions.fields_structure ];
                                $( "#" + response.listid ).nestable( 'rebuild', rebuildArgs );
                                uni_update_options_structure( $( "#" + response.listid ) );
                            }
                            break;

                        case 'uni_cpo_field_settings_save':
                            cpoOptionEditModal.close();
                            if ( typeof unicpooptions !== 'undefined' && unicpooptions.fields_structure && response.newoptiondata ) {
                                var indexOfChosenOption, indexOfChosenChildOption;
                                $.each( unicpooptions.fields_structure, function ( i, val ) {
                                    if ( val.id == response.oid ) {
                                        indexOfChosenOption = i;
                                    }
                                    if ( val.children ) {
                                        $.each( val.children, function ( k, child ) {
                                            if ( child.id == response.oid ) {
                                                indexOfChosenChildOption = k;
                                                indexOfChosenOption = i;
                                            }
                                        } );
                                    }
                                } );
                                if ( typeof indexOfChosenChildOption !== 'undefined' ) {
                                    unicpooptions.fields_structure[ indexOfChosenOption ].children[ indexOfChosenChildOption ] = response.newoptiondata;
                                } else {
                                    var currentItem = unicpooptions.fields_structure[ indexOfChosenOption ],
                                        newItem = response.newoptiondata;
                                    // copy info about children items
                                    if ( typeof currentItem.children !== 'undefined' ) {
                                        newItem.children = currentItem.children;
                                    }
                                    unicpooptions.fields_structure[ indexOfChosenOption ] = newItem;
                                }
                                var rebuildArgs = [ unicpooptions.fields_structure ];
                                $( '#unicpo-options-list' ).nestable( 'rebuild', rebuildArgs );
                                uni_update_options_structure( $( '#unicpo-options-list' ) );
                            }
                            break;

                        case 'uni_cpo_non_option_vars_save':
                            if ( !$( document ).find( ".uni-cpo-non-option-vars-delete-link" ).length ) {
                                $( document ).find( "#unicpo-non-option-variables" ).find( ".uni_cpo_ajax_link_container" ).append( '<span class="uni-cpo-non-option-vars-delete-link uni_cpo_ajax_call" data-pid="' + response.pid + '" data-action="uni_cpo_non_option_vars_delete">' + unicpo.remove_non_vars_text + '</span>' );
                            }
                            $( "#js-cpo-formula-variables-list" ).html( response.formulavarslist );
                            break;

                        case 'uni_cpo_formula_conditional_rule_save':
                            if ( !$( document ).find( ".uni-cpo-conditional-delete-link" ).length ) {
                                $( document ).find( "#unicpo-settings-conditionals" ).find( ".uni_cpo_ajax_link_container" ).append( '<span class="uni-cpo-conditional-delete-link uni_cpo_ajax_call" data-pid="' + response.pid + '" data-action="uni_cpo_formula_conditional_rule_delete">' + unicpo.remove_cond_text + '</span>' );
                            }
                            break;

                        case 'uni_cpo_weight_conditional_rule_save':
                            if ( !$( document ).find( ".uni-cpo-conditional-delete-link" ).length ) {
                                $( document ).find( "#unicpo-weight-conditionals" ).find( ".uni_cpo_ajax_link_container" ).append( '<span class="uni-cpo-conditional-delete-link uni_cpo_ajax_call" data-pid="' + response.pid + '" data-action="uni_cpo_weight_conditional_rule_delete">' + unicpo.remove_cond_text + '</span>' );
                            }
                            break;

                        case 'uni_cpo_cart_discounts_save':
                            if ( !$( document ).find( ".cpo-" + response.base + "-delete-link" ).length ) {
                                $( document ).find( "#cpo_" + response.base + "_discounts_container" ).append( '<span class="cpo-discounts-button-delete-link cpo-' + response.base + '-delete-link uni_cpo_ajax_call" data-action="uni_cpo_cart_discounts_delete" data-pid="' + response.pid + '" data-base="' + response.base + '">' + unicpo.remove_disc_rules_text + '</span>' );
                            }
                            break;

                        default:
                            break;
                        }

                    } else if ( response.status == "error" ) {
                        block_ui_change_to_red();
                        $this_form.unblock();
                    }

                    // special callback
                    switch ( this_form_action ) {
                    case 'uni_cpo_order_item_options_save':
                        // closes modal window
                        cpoOrderItemAddOptions.close();
                        // reloads order items
                        $( '#woocommerce-order-items' ).find( '.inside' ).empty();
                        $( '#woocommerce-order-items' ).find( '.inside' ).append( response );
                        // wc_meta_boxes_order.init_tiptip();
                        $( '#tiptip_holder' ).removeAttr( 'style' );
                        $( '#tiptip_arrow' ).removeAttr( 'style' );
                        $( '.tips' ).tipTip( {
                            'attribute': 'data-tip',
                            'fadeIn': 50,
                            'fadeOut': 50,
                            'delay': 200
                        } );
                        // wc_meta_boxes_order_items.unblock();
                        $this_form.unblock();
                        // wc_meta_boxes_order_items.stupidtable.init();
                        UniCpo.stupidtable.init();
                        break;
                    }
                },
                error: function ( response ) {
                    block_ui_change_to_red();
                    $this_form.unblock();
                }
            } );
        } else {
            $this_form.find( ".uni_cpo_submit" ).removeClass( "uni-cpo-settings-saved uni-cpo-settings-unsaved" ).addClass( "uni-cpo-settings-error" );
        }
        return false;
    } );


    // ajax call from single link
    $( document ).on( "click", ".uni_cpo_ajax_call", function ( e ) {

        e.preventDefault();

        var $this_link = $( e.target ),
            this_link_data = $this_link.data(),
            wrapper = $this_link.closest( ".uni_cpo_ajax_link_container" );

        var dataToSend = this_link_data;
        dataToSend.cheaters_always_disable_js = 'true_bro';

        $.ajax( {
            type: 'post',
            url: ajaxurl,
            data: dataToSend,
            dataType: 'json',
            beforeSend: function () {
                if ( this_link_data.action == 'uni_cpo_option_add' ) {
                    $this_link.closest( ".cpo-list-add-element-popup" ).slideToggle( "100" );
                    $( ".cpo-list-add-element-wrap span" ).removeClass( "clicked" );
                }
                if ( this_link_data.action == 'uni_cpo_cart_discounts_show' ) {
                    $( "#cpo_cart_discounts_container" ).empty();
                }
                wrapper.block( {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                } );
            },
            success: function ( response ) {
                if ( response.status == "success" ) {

                    switch ( this_link_data.action ) {
                    case 'uni_cpo_options_create':
                        wrapper.hide();
                        var pId = this_link_data.pid;
                        uni_cpo_options_create_callback( pId, response.optionsid );
                        break;

                    case 'uni_cpo_option_add':
                        if ( typeof unicpooptions !== 'undefined' && unicpooptions.fields_structure ) {
                            unicpooptions.fields_structure.push( response.newoptiondata );
                            var rebuildArgs = [ unicpooptions.fields_structure ];
                            $( '#unicpo-options-list' ).nestable( 'rebuild', rebuildArgs );
                            uni_update_options_structure( $( '#unicpo-options-list' ) );
                        }
                        break;

                    case 'uni_cpo_non_option_vars_show':
                        $( "#cpo_non_option_vars_container" ).html( response.output );
                        cpoNonOptionVarsModal.open();
                        break;

                    case 'uni_cpo_non_option_vars_delete':
                        if ( $( document ).find( ".uni-cpo-non-option-vars-delete-link" ).length ) {
                            $( ".uni-cpo-non-option-vars-delete-link" ).remove();
                        }
                        $( "#js-cpo-formula-variables-list" ).html( response.formulavarslist );
                        break;

                    case 'uni_cpo_formula_conditional_rule_delete':
                        wrapper.find( ".uni-cpo-conditional-delete-link" ).remove();
                        break;

                    case 'uni_cpo_weight_conditional_rule_delete':
                        wrapper.find( ".uni-cpo-conditional-delete-link" ).remove();
                        break;

                    case 'uni_cpo_cart_discounts_show':
                        $( "#cpo_cart_discounts_container" ).html( response.output );
                        $( "#uni_cpo_discounts_base" ).val( this_link_data.base );
                        cpoCartDiscountsModal.open();
                        break;

                    case 'uni_cpo_cart_discounts_delete':
                        wrapper.find( ".cpo-" + this_link_data.base + "-delete-link" ).remove();
                        break;

                    case 'uni_cpo_order_item_options_add_show':
                        $( '#cpo_order_item_add_options_container' ).html( response.output );
                        $( '#uni_cpo_modal_form_order_item_id' ).val( this_link_data.item_id );
                        $( '#uni_cpo_modal_form_order_product_id' ).val( this_link_data.pid );
                        cpoOrderItemAddOptions.open();
                        break;

                    default:
                        break;
                    }

                    block_ui_change_to_green();
                    wrapper.unblock();

                    if ( response.redirect.length > 0 ) {
                        setTimeout( function () {
                            window.location.replace( response.redirect );
                        }, 500 );
                    }
                } else if ( response.status == "error" ) {
                    block_ui_change_to_red();
                    wrapper.unblock();
                }
            },
            error: function ( response ) {
                block_ui_change_to_red();
                wrapper.unblock();
            }
        } );
        return false;
    } );

    // calculate order item price
    $( document ).on( "click", "#cpo_order_add_option_calculate", function ( e ) {

        e.preventDefault();

        var $this_link = $( e.target ),
            $this_form = $this_link.closest( 'form' ),
            dataToSend = {},
            temp_data = $this_form.serializeArray();

        $( temp_data ).each( function ( index, obj ) {
            dataToSend[ obj.name ] = obj.value;
        } );
        dataToSend[ 'action' ] = 'uni_cpo_calculate_price_ajax';
        dataToSend[ 'cheaters_always_disable_js' ] = 'true_bro';

        $.ajax( {
            type: 'post',
            url: ajaxurl,
            data: dataToSend,
            dataType: 'json',
            beforeSend: function () {
                $this_form.block( {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                } );
            },
            success: function ( response ) {
                if ( response.status == "success" ) {

                    block_ui_change_to_green();
                    $this_form.unblock();
                    $( '#cpo_order_add_option_calculated_price' ).val( response.price_vars.raw_price );

                } else if ( response.status == "error" ) {
                    block_ui_change_to_red();
                    $this_form.unblock();
                }
            },
            error: function ( response ) {
                block_ui_change_to_red();
                $this_form.unblock();
            }
        } );
        return false;
    } );

    //
    $( document ).on( "click", ".uni_cpo_ajax_call_confirmation", function ( e ) {

        e.preventDefault();
        var $this_link = $( this ),
            this_link_data = $this_link.data(),
            wrapper = $this_link.closest( ".uni_cpo_ajax_link_container" ),
            container = $( "#cpo-confirmation-container" );

        var dataToSend = this_link_data;
        dataToSend.cheaters_always_disable_js = 'true_bro';

        if ( this_link_data.action == 'uni_cpo_option_delete_show_dialog' ) {
            var $liEl = $this_link.closest( "li.cpo-list-item" ),
                liElId = $liEl.data( "id" ),
                listId = "unicpo-options-list";

            if ( $liEl.children( ".cpo-list" ).length > 0 ) {
                // generates an array of all children options
                var childrenItems = [];
                $liEl.children( ".cpo-list" ).children( ".cpo-list-item" ).each( function ( i ) {
                    childrenItems[ i ] = $( this ).data( "id" );
                } );
            } else {
                var childrenItems = [];
            }

            dataToSend.oid = liElId;
        }

        $.ajax( {
            type: 'post',
            url: ajaxurl,
            data: dataToSend,
            dataType: 'json',
            beforeSend: function () {
                wrapper.block( {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                } );
            },
            success: function ( response ) {
                if ( response.status == "success" ) {

                    switch ( this_link_data.action ) {
                    case 'uni_cpo_options_remove_show_dialog':
                        $( "#uni_cpo_modal_confirmation_action" ).val( response.action );
                        $( "#uni_cpo_modal_confirmation_pid" ).val( response.pid );
                        container.html( response.output );
                        cpoConfirmationModal.open();
                        break;

                    case 'uni_cpo_option_delete_show_dialog':
                        $( "#uni_cpo_modal_confirmation_action" ).val( response.action );
                        container.html( response.output );
                        container.append( '<input id="cpo-input-oid" type="hidden" name="oid" value="" />' );
                        $( "#cpo-input-oid" ).val( liElId );
                        container.append( '<input id="cpo-input-listid" type="hidden" name="listid" value="" />' );
                        $( "#cpo-input-listid" ).val( listId );
                        container.append( '<input id="cpo-input-childrenoptions" type="hidden" name="childrenoptions" value="" />' );
                        $( "#cpo-input-childrenoptions" ).val( childrenItems );
                        cpoConfirmationModal.open();
                        break;

                    default:
                        break;
                    }

                    block_ui_change_to_green();
                    wrapper.unblock();

                    if ( response.redirect.length > 0 ) {
                        setTimeout( function () {
                            window.location.replace( response.redirect );
                        }, 500 );
                    }
                } else if ( response.status == "error" ) {
                    block_ui_change_to_red();
                    wrapper.unblock();
                }
            },
            error: function ( response ) {
                block_ui_change_to_red();
                wrapper.unblock();
            }
        } );
        return false;
    } );


    //////////////////////////////////////////////////////////////////////////////////////
    // helpers and various functions
    //////////////////////////////////////////////////////////////////////////////////////
    // blockui change colours
    function block_ui_change_to_red() {
        $( ".blockOverlay" ).css( {
            "background-color": "#FFA07A"
        } );
    }

    function block_ui_change_to_green() {
        $( ".blockOverlay" ).css( {
            "background-color": "#94E594"
        } );
    }

    // add element popup
    $( document ).on( "click", ".cpo-list-add-element-wrap span", function ( e ) {
        var $el = $( e.target );
        if ( $el.hasClass( "clicked" ) ) {
            $el.removeClass( "clicked" ).closest( "#unicpo-options-list-created" ).find( ".cpo-list-add-element-popup" ).slideUp( 200 );
        } else {
            $el.addClass( "clicked" ).closest( "#unicpo-options-list-created" ).find( ".cpo-list-add-element-popup" ).slideDown( 200 );
        }
    } );
    $( document ).on( 'click', function ( e ) {
        var $el = $( e.target );
        if ( !$el.parents().hasClass( 'cpo-list-add-element-wrap' ) && !$el.hasClass( 'cpo-list-add-element-popup' ) ) {
            $( "#unicpo-options-list-created" ).find( ".cpo-list-add-element-popup" ).slideUp( 300 );
            $( ".cpo-list-add-element-wrap span" ).removeClass( "clicked" );
        }
    } );

    // change options set ID on select
    $( document ).on( "change", "#uni-precreated-options-select", function () {
        var optionsId = $( this ).val();
        //console.log(optionsId);
        $( "#cpo-options-attach" ).data( "optionsid", optionsId );
        $( "#cpo-options-attach" ).attr( "data-optionsid", optionsId );
    } );
    // init of dropdown with the list of options sets
    if ( $( "#uni-precreated-options-select" ).length > 0 ) {
        var optionsId = $( "#uni-precreated-options-select" ).val();
        //console.log(optionsId);
        $( "#cpo-options-attach" ).data( "optionsid", optionsId );
        $( "#cpo-options-attach" ).attr( "data-optionsid", optionsId );
    }

    //
    wideArea();

    //
    $( document ).on( 'click', '#js-cpo-formula-variables-list li', function () {
        $( "#uni_cpo_price_main_formula" ).insertAtCaret( $( this ).text() );
        return false
    } );

    // parsleyJS global callback for fields in .uni_cpo_form - fires on 'error'
    window.Parsley.on( 'field:error', function () {
        if ( this.$element.hasClass( "uni-cpo-modal-field" ) ) {
            var this_form = this.$element.closest( ".uni_cpo_form" ),
                submit_button = this_form.find( ".uni_cpo_submit" );
            submit_button.removeClass( "uni-cpo-settings-saved uni-cpo-settings-unsaved" ).addClass( "uni-cpo-settings-error" );
        }
    } );
    // parsleyJS global callback for fields in .uni_cpo_form - fires on 'success'
    window.Parsley.on( 'field:success', function () {
        if ( this.$element.hasClass( "uni-cpo-modal-field" ) ) {
            var this_form = this.$element.closest( ".uni_cpo_form" ),
                submit_button = this_form.find( ".uni_cpo_submit" );
            submit_button.removeClass( "uni-cpo-settings-error" ).addClass( "uni-cpo-settings-unsaved" );
        }
    } );

    // uni_after_add_suboption
    function uni_after_add_suboption( container, new_row ) {
        var row_count = $( container ).attr( 'data-rf-row-count' );

        row_count++;

        $( '*', new_row ).each( function () {
            $.each( this.attributes, function ( index, element ) {
                this.value = this.value.replace( '{{row-count}}', row_count - 1 );
            } );
        } );

        $( container ).attr( 'data-rf-row-count', row_count );

        new_row.find( '.wp-color-result' ).remove();
        new_row.find( '.wp-picker-holder' ).remove();
        new_row.find( 'input.wp-color-picker' ).unwrap().unwrap();

        // color picker
        new_row.find( '.uni-cpo-palette-option-field-color' ).wpColorPicker( {
            mode: 'hsl',
            palettes: false
        } );


    }

    // uni_after_add_row
    function uni_after_add_row( container, new_row ) {
        var row_count = $( container ).attr( 'data-rf-row-count' );

        row_count++;

        $( '*', new_row ).each( function () {
            $.each( this.attributes, function ( index, element ) {
                this.value = this.value.replace( '{{row-count}}', row_count - 1 );
            } );
        } );

        $( container ).attr( 'data-rf-row-count', row_count );

    }

    // uni_after_conditional_add
    function uni_after_conditional_add( container, new_row, aFilter ) {

        var row_count = $( container ).attr( 'data-rf-row-count' );

        row_count++;

        var neededIndex = row_count - 1;

        $( '*', new_row ).each( function () {
            $.each( this.attributes, function ( index, element ) {
                this.value = this.value.replace( '{{row-count}}', neededIndex );
            } );
        } );

        $( container ).attr( 'data-rf-row-count', row_count );

        $( "#cpo-formula-rule-builder-" + neededIndex ).queryBuilder( {

            plugins: {
                //'bt-tooltip-errors': { delay: 100},
                'sortable': null
            },
            allow_groups: 1,
            filters: aFilter

        } );

        //
        $( "#cpo-formula-rule-builder-" + neededIndex ).on( 'afterAddRule afterDeleteRule.queryBuilder afterUpdateRuleValue.queryBuilder afterUpdateRuleFilter.queryBuilder afterUpdateRuleOperator.queryBuilder afterUpdateGroupCondition.queryBuilder', function ( e, rule, error, value ) {
            var $getRuleButton = $( e.target ).next( ".cpo-parse-formula-rule-json" );
            $getRuleButton.removeClass( "uni-cpo-settings-saved" ).addClass( "uni-cpo-settings-unsaved" );
        } );

    }

    // uni_after_val_conditional_add
    function uni_after_val_conditional_add( container, new_row, aFilter ) {

        var row_count = $( container ).attr( 'data-rf-row-count' );

        row_count++;

        var neededIndex = row_count - 1;

        $( '*', new_row ).each( function () {
            $.each( this.attributes, function ( index, element ) {
                this.value = this.value.replace( '{{row-count}}', neededIndex );
            } );
        } );

        $( container ).attr( 'data-rf-row-count', row_count );

        $( "#cpo-validation-rule-builder-" + neededIndex ).queryBuilder( {

            plugins: {
                //'bt-tooltip-errors': { delay: 100},
                'sortable': null
            },
            allow_groups: 1,
            filters: aFilter

        } );

        //
        $( "#cpo-validation-rule-builder-" + neededIndex ).on( 'afterAddRule afterDeleteRule.queryBuilder afterUpdateRuleValue.queryBuilder afterUpdateRuleFilter.queryBuilder afterUpdateRuleOperator.queryBuilder afterUpdateGroupCondition.queryBuilder', function ( e, rule, error, value ) {
            var $getRuleButton = $( e.target ).next( ".cpo-parse-validation-rule-json" );
            $getRuleButton.removeClass( "uni-cpo-settings-saved" ).addClass( "uni-cpo-settings-unsaved" );
        } );

    }

    // converts a value of field_slug input to slug like formatted text
    $( document ).on( "change focusin focusout", "input[name=uni_cpo_field_slug], input.uni-cpo-non-option-slug-field", function () {
        var $el = $( this ),
            thisValue = $el.val();
        $el.val( uniConvertToSlug( thisValue ) );
    } );

    // converts values of sub options slug inputs to slug like formatted text
    $( document ).on( "change focusin focusout", "input.uni-cpo-select-option-field-label", function () {
        var $el = $( this ),
            thisValue = $el.val(),
            $slugEl = $el.parent().next( 'div' ).find( "input.uni-cpo-select-option-field-slug" ),
            slugElVal = $slugEl.val();
        if ( !slugElVal ) {
            $slugEl.val( uniConvertToSlug( thisValue ) );
        }
    } );
    // converts values of sub options slug inputs to slug like formatted text - the same, just events binded to other form elements
    $( document ).on( "change focusin focusout", "input.uni-cpo-select-option-field-slug", function () {
        var $el = $( this ),
            thisValue = $el.val();
        $el.val( uniConvertToSlug( thisValue ) );
    } );

    // deselectable radio inputs
    $( document ).on( 'click', '.uni-cpo-modal-field-default-deselectable', uniMarkIt );

    // show hidden options for an option
    $( document ).on( "change", 'select.uni-cpo-modal-field', function ( e ) {
        uni_display_hidden_related_settings( e.target );
    } );

    function uni_display_hidden_related_settings( el ) {
        var $el = $( el ),
            elVal = $el.val(),
            fieldName = $el.attr( "name" );

        if ( fieldName === 'uni_cpo_field_input_tag_type' ) {
            if ( elVal === 'integer' || elVal === 'double' ) {
                $( "#uni_cpo_field_input_number_min_container, #uni_cpo_field_input_number_max_container, #uni_cpo_field_input_number_step_container" ).slideDown();
                $( "#uni_cpo_field_chars_min_container, #uni_cpo_field_chars_max_container" ).hide();
            } else {
                $( "#uni_cpo_field_chars_min_container, #uni_cpo_field_chars_max_container" ).slideDown();
                $( "#uni_cpo_field_input_number_min_container, #uni_cpo_field_input_number_max_container, #uni_cpo_field_input_number_step_container" ).hide();
            }
        }
        if ( fieldName === 'uni_cpo_field_display_price_in_front' ) {
            if ( elVal === 'yes' ) {
                $( "#uni_cpo_field_display_price_in_front_text_container" ).slideDown();
            } else {
                $( "#uni_cpo_field_display_price_in_front_text_container" ).hide();
            }
        }
        if ( fieldName === 'uni_cpo_field_datepicker_select' ) {
            $( "#uni_cpo_field_datepicker_cells_no_container, #uni_cpo_field_datepicker_day_of_week_start_container, #uni_cpo_field_datepicker_day_of_week_end_container, #uni_cpo_field_datepicker_start_month_container, #uni_cpo_field_datepicker_format_date_container, #uni_cpo_field_datepicker_format_datetime_container, #uni_cpo_field_datepicker_format_time_container, #uni_cpo_field_datepicker_min_date_container, #uni_cpo_field_datepicker_disable_spec_dates_container, #uni_cpo_field_datepicker_disable_days_of_week_container, #uni_cpo_field_datepicker_max_date_container, #uni_cpo_field_datepicker_hours_enable_container, #uni_cpo_field_datepicker_minutes_enable_container, #uni_cpo_field_datepicker_seconds_enable_container, #uni_cpo_field_datepicker_ampm_enable_container, #uni_cpo_field_datepicker_hours_step_container, #uni_cpo_field_datepicker_minutes_step_container, #uni_cpo_field_datepicker_seconds_step_container, #uni_cpo_field_datepicker_min_time_container, #uni_cpo_field_datepicker_max_time_container" ).hide();
            if ( elVal === 'date' || elVal === 'period_date' ) {
                $( "#uni_cpo_field_datepicker_cells_no_container, #uni_cpo_field_datepicker_day_of_week_start_container, #uni_cpo_field_datepicker_day_of_week_end_container, #uni_cpo_field_datepicker_start_month_container, #uni_cpo_field_datepicker_format_date_container, #uni_cpo_field_datepicker_min_date_container, #uni_cpo_field_datepicker_disable_spec_dates_container, #uni_cpo_field_datepicker_disable_days_of_week_container, #uni_cpo_field_datepicker_max_date_container" ).slideDown();
            } else if ( elVal === 'datetime' || elVal === 'period' ) {
                $( "#uni_cpo_field_datepicker_cells_no_container, #uni_cpo_field_datepicker_day_of_week_start_container, #uni_cpo_field_datepicker_day_of_week_end_container, #uni_cpo_field_datepicker_start_month_container, #uni_cpo_field_datepicker_format_datetime_container, #uni_cpo_field_datepicker_min_date_container, #uni_cpo_field_datepicker_disable_spec_dates_container, #uni_cpo_field_datepicker_disable_days_of_week_container, #uni_cpo_field_datepicker_max_date_container, #uni_cpo_field_datepicker_hours_enable_container, #uni_cpo_field_datepicker_minutes_enable_container, #uni_cpo_field_datepicker_seconds_enable_container, #uni_cpo_field_datepicker_ampm_enable_container, #uni_cpo_field_datepicker_hours_step_container, #uni_cpo_field_datepicker_minutes_step_container, #uni_cpo_field_datepicker_seconds_step_container, #uni_cpo_field_datepicker_min_time_container, #uni_cpo_field_datepicker_max_time_container" ).slideDown();
            } else if ( elVal === 'time' ) {
                $( "#uni_cpo_field_datepicker_format_time_container, #uni_cpo_field_datepicker_hours_enable_container, #uni_cpo_field_datepicker_minutes_enable_container, #uni_cpo_field_datepicker_seconds_enable_container, #uni_cpo_field_datepicker_ampm_enable_container, #uni_cpo_field_datepicker_hours_step_container, #uni_cpo_field_datepicker_minutes_step_container, #uni_cpo_field_datepicker_seconds_step_container, #uni_cpo_field_datepicker_min_time_container, #uni_cpo_field_datepicker_max_time_container" ).slideDown();
            }
        }
        if ( fieldName === 'uni_cpo_field_header_tooltip_type' ) {
            if ( elVal === 'classic' ) {
                $( "#uni_cpo_field_header_tooltip_text_container" ).slideDown();
                $( "#uni_cpo_field_header_tooltip_image_container" ).hide();
            } else if ( elVal === 'lightbox' ) {
                $( "#uni_cpo_field_header_tooltip_image_container" ).slideDown();
                $( "#uni_cpo_field_header_tooltip_text_container" ).hide();
            }
        }
        if ( fieldName === 'uni_cpo_field_map_type' ) {

            if ( elVal === 'distance_to_base' ) {
                $( "#uni_cpo_field_base_location_container" ).show();
                cpoInitAdminGoogleMaps();
            } else {
                $( "#uni_cpo_field_base_location_container" ).hide();
            }

            if ( elVal === 'two_locations' || elVal === 'cargo_calculator' ) {
                $( "#uni_cpo_field_label_start_container" ).show();
            } else {
                $( "#uni_cpo_field_label_start_container" ).hide();
            }

            if ( elVal === 'two_locations' || elVal === 'cargo_calculator' || elVal === 'distance_to_base' ) {
                $( "#uni_cpo_field_label_distance_container" ).show();
            } else {
                $( "#uni_cpo_field_label_distance_container" ).hide();
            }
        }

        if ( fieldName === 'uni_cpo_field_input_range_type' ) {  

            $( "#uni_cpo_field_input_min_interval_container,#uni_cpo_field_input_max_interval_container" ).hide()
            if ( elVal === 'double' ) {
                $( "#uni_cpo_field_input_default_end_container" ).show();
                $( "#uni_cpo_field_input_min_interval_container" ).show();
                $( "#uni_cpo_field_input_max_interval_container" ).show();
                $( "#uni_cpo_field_input_show_text_input_container" ).hide();
            } else {
                $( "#uni_cpo_field_input_default_end_container" ).hide();
                $( "#uni_cpo_field_input_min_interval_container" ).hide();
                $( "#uni_cpo_field_input_max_interval_container" ).hide();
                $( "#uni_cpo_field_input_show_text_input_container" ).show();
            }
            
        }
    }

    //
    $( document ).on( 'click', '#uni_cpo_non_option_vars_wholesale_enable', function ( e ) {
        var $field = $( this );
        if ( $field.is( ':checked' ) ) {
            $( '#uni_cpo_user_roles_container' ).show();
        } else {
            $( '#uni_cpo_user_roles_container' ).hide();
        }
    } );
    if ( $( '#uni_cpo_non_option_vars_wholesale_enable' ).length > 0 ) {
        if ( $( '#uni_cpo_non_option_vars_wholesale_enable' ).is( ':checked' ) ) {
            $( '#uni_cpo_user_roles_container' ).show();
        } else {
            $( '#uni_cpo_user_roles_container' ).hide();
        }
    }

    /* For Google Maps field */
    function getGeocoder( latLng, geocoder ) {
        geocoder.geocode( {
            'location': latLng
        }, function ( results, status ) {
            if ( status === 'OK' ) {
                if ( results[ 1 ] ) {
                    $( '.uni-cpo-search-locations' ).val( results[ 1 ].formatted_address );
                } else {
                    window.alert( 'No results found' );
                }
            } else {
                window.alert( 'Geocoder failed due to: ' + status );
            }
        } );
    }

    function cpoInitAdminGoogleMaps() {

        // map options default
        var center = {
                lat: -33.8688,
                lng: 151.2195
            },
            $container = $( "#cpo-option-modal-tabs" );

        if ( !$container.find( '.uni-cpo-mappicker' ).length ) {
            return;
        }

        var latLng = $container.find( '.uni-cpo-search-locations-lat-lng' ).val();

        if ( latLng ) {
            latLng = latLng.split( ',' );
            center.lat = parseFloat( latLng[ 0 ] );
            center.lng = parseFloat( latLng[ 1 ] );
        }

        // init map
        var geocoder = new google.maps.Geocoder,
            map_core = new google.maps.Map( $container.find( '.uni-cpo-mappicker' )[ 0 ], {
                center: center,
                zoom: 9,
                gestureHandling: 'cooperative',
                draggableCursor: "crosshair",
                fullscreenControl: false

            } );

        // add default location
        var marker = new google.maps.Marker( {
            position: center,
            map: map_core
        } );

        // change position marker
        map_core.addListener( 'click', function ( e ) {

            //lat and lng coordinates
            if ( marker ) {
                marker.setPosition( e.latLng );
            } else {
                marker = new google.maps.Marker( {
                    position: e.latLng,
                    map: map_core
                } );
            }

            // get geocoder
            getGeocoder( e.latLng, geocoder );

            $container.find( '.uni-cpo-search-locations-lat-lng' ).val( marker.position.lat() + ',' + marker.position.lng() );

        } );

        // add event change center
        // get "search-locations" field
        var $search_field = $container.find( '.uni-cpo-search-locations' );

        // autocomplete options
        var options = {
            //placeIdOnly: true
        };

        // init Autocomplete
        var autocomplete = new google.maps.places.Autocomplete( $search_field[ 0 ], options );

        // Binds the map's bounds (viewport) property to the autocomplete object
        autocomplete.bindTo( 'bounds', map_core );

        // add autocomplete event
        google.maps.event.addListener( autocomplete, 'place_changed', function () {

            setTimeout( function () {

                // get place data
                var place = autocomplete.getPlace();

                if ( !place.geometry ) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    window.alert( "No details available for input: '" + place.name + "'" );
                    return;
                }

                // hide current marker
                marker.setVisible( false );

                // If the place has a geometry, then present it on a map.
                if ( place.geometry.viewport ) {
                    map_core.fitBounds( place.geometry.viewport );
                } else {
                    map_core.setCenter( place.geometry.location );
                    map_core.setZoom( 17 ); // Why 17? Because it looks good.
                }

                // add marker
                marker.setPosition( place.geometry.location );
                marker.setVisible( true );

                getGeocoder( place.geometry.location, geocoder );
                $( '.uni-cpo-search-locations-lat-lng' ).val( place.geometry.location.lat() + ',' + place.geometry.location.lng() );

            }, 0 );

        } );

        // trigger autocomplete (press enter)
        $search_field.keypress( function ( e ) {
            if ( e.which == 13 ) {
                google.maps.event.trigger( autocomplete, 'place_changed' );
                return false;
            }
        } );

        // fix autocomplete for touch screen
        $( 'body' ).on( 'touchend', '.pac-container', function ( e ) {
            e.stopImmediatePropagation();
        } );
    }


    //////////////////////////////////////////////////////////////////////////////////////
    // remodal
    //////////////////////////////////////////////////////////////////////////////////////
    // edit single option
    var cpoOptionEditModal = $( '[data-remodal-id=option-edit-modal]' ).remodal( {
        hashTracking: false,
        closeOnOutsideClick: false
    } );
    // manage non option variables
    var cpoNonOptionVarsModal = $( '[data-remodal-id=non-option-vars-modal]' ).remodal( {
        hashTracking: false,
        closeOnOutsideClick: false
    } );

    // manage formula conditional rules
    var cpoConditionalRulesModal = $( '[data-remodal-id=conditional-modal]' ).remodal( {
        hashTracking: false,
        closeOnOutsideClick: false
    } );
    // import/export formula conditional rules
    var cpoConditionalRulesImexModal = $( '[data-remodal-id=conditional-imex-modal]' ).remodal( {
        hashTracking: false,
        closeOnOutsideClick: false
    } );

    // manage discounts
    var cpoCartDiscountsModal = $( '[data-remodal-id=discounts-modal]' ).remodal( {
        hashTracking: false,
        closeOnOutsideClick: false
    } );
    // confirmation modal window
    var cpoConfirmationModal = $( '[data-remodal-id=confirmation-modal]' ).remodal( {
        hashTracking: false,
        closeOnOutsideClick: false
    } );

    // add/edit options for order item
    var cpoOrderItemAddOptions = $( '[data-remodal-id=order-options-modal]' ).remodal( {
        hashTracking: false,
        closeOnOutsideClick: false
    } );

    // opens single option edit settings modal window
    $( document ).on( "click", ".edit-list-item-btn", function () {
        var $el = $( this ),
            $liEl = $el.closest( "li.cpo-list-item" ),
            fieldType = $liEl.data( "itemtype" ),
            setid = $( '#unicpo-options-list-created' ).data( 'optionsid' ),
            oId = $liEl.data( "id" );

        var wrapper = $( "#unicpo-options-list-created" ),
            $container = $( "#cpo-option-modal-tabs" );

        var sendData = {
            action: 'uni_cpo_field_settings_show',
            field_type: fieldType,
            setid: setid,
            oid: oId,
            cheaters_always_disable_js: 'true_bro',
        };

        $.ajax( {
            type: 'post',
            url: ajaxurl,
            data: sendData,
            dataType: 'json',
            beforeSend: function ( response ) {
                wrapper.block( {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                } );
            },
            success: function ( response ) {
                //console.log(response);
                if ( response.status == "success" ) {
                    block_ui_change_to_green();
                    wrapper.unblock();

                    $( "#uni_cpo_modal_form_option_id" ).val( oId );
                    $( "#uni_cpo_modal_form_option_type" ).val( fieldType );
                    $container.html( response.output );
                    cpoOptionEditModal.open();
                    if ( response.filter.length > 0 && $( '#uni_cpo_field_conditional_scheme_container' ).length > 0 ) {
                        uni_cpo_init_conditional_rules_builder( response.filter, response.rules_fields );
                    }
                    if ( response.filter.length > 0 && $( '#uni_cpo_val_conditional_scheme_container' ).length > 0 ) {
                        uni_cpo_init_validation_conditional_rules_builder( response.filter, response.rules_val );
                    }

                    cpoInitAdminGoogleMaps( $container );



                } else if ( response.status == "error" ) {
                    block_ui_change_to_red();
                    wrapper.unblock();
                    $container.html( response.message );
                    cpoOptionEditModal.open();
                }
            },
            error: function ( response ) {
                block_ui_change_to_red();
                wrapper.unblock();
                $container.html( response.message );
            }
        } );

    } );

    // 'opening' event for option settings modal window
    $( document ).on( 'opening', '.cpo-option-modal', function () {
        // init tabs
        $( "#cpo-option-modal-tabs" ).tabs();
        // on form's inputs change
        $( ".uni-cpo-modal-field" ).on( "change", function ( e ) {
            var $this_field = $( e.target ),
                $this_form = $this_field.closest( "form" ),
                $submit_button = $this_form.find( ".uni_cpo_submit" );
            $submit_button.removeClass( "uni-cpo-settings-saved" ).addClass( "uni-cpo-settings-unsaved" );
        } );

        if ( $( ".uni-select-option-repeat" ).length > 0 ) {
            $( ".uni-select-option-repeat" ).each( function () {
                $( this ).repeatable_fields( {
                    wrapper: '.uni-select-option-repeat-wrapper',
                    container: '.uni-select-option-options-wrapper',
                    row: '.uni-select-option-options-row',
                    add: '.uni_select_option_add',
                    remove: '.uni_select_option_remove',
                    move: '.uni_select_option_move',
                    template: '.uni-select-option-options-template',
                    is_sortable: true,
                    before_add: null,
                    after_add: uni_after_add_suboption,
                    before_remove: null,
                    //after_remove: null,
                    sortable_options: null,
                    row_count_placeholder: '{{row-count}}',
                } );
            } );
        }

        // check the val of select and display/hide fields
        uni_display_hidden_related_settings( 'select[name="uni_cpo_field_input_tag_type"]' );
        uni_display_hidden_related_settings( 'select[name="uni_cpo_field_display_price_in_front"]' );
        uni_display_hidden_related_settings( 'select[name="uni_cpo_field_datepicker_select"]' );
        uni_display_hidden_related_settings( 'select[name="uni_cpo_field_map_type"]' );
        uni_display_hidden_related_settings( 'select[name="uni_cpo_field_header_tooltip_type"]' );
        uni_display_hidden_related_settings( 'select[name="uni_cpo_field_input_range_type"]' );

        // tiptip
        var tiptip_args = {
            'attribute': 'data-tip',
            'fadeIn': 50,
            'fadeOut': 50,
            'delay': 200
        };
        $( ".uni_help_tip" ).tipTip( tiptip_args );

        // image add/remove
        var file_frame;
        $( document ).on( 'click', '.upload_image_button, input[name=uni_cpo_field_header_tooltip_image], .add_file_button', function ( e ) {

            e.preventDefault();
            var $link = $( e.target );

            if ( typeof file_frame !== 'undefined' ) {
                file_frame.close();
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media( {
                title: unicpo.uploader_title_text,
                button: {
                    text: unicpo.uploader_btn_text,
                },
                multiple: false
            } );

            // When an image is selected, run a callback.
            file_frame.on( 'select', function () {
                var attachment = file_frame.state().get( 'selection' ).first().toJSON();
                //console.log(attachment);
                if ( $link.attr( 'type' ) == 'text' ) {
                    $link.val( attachment.id );
                } else if ( $link.attr( 'type' ) == 'button' ) {
                    if ( $link.hasClass( 'add_file_button' ) ) {
                        var $label = $link.parent().parent().find( '.uni-cpo-select-option-field-label' );
                        $link.prevAll( "input.option_thumbnail_id" ).val( attachment.id );
                        $link.nextAll( ".remove_file_button" ).show();
                        $label.val( attachment.filename + ' (' + attachment.filesizeHumanReadable + ')' );
                    } else {
                        $link.prevAll( "input.option_thumbnail_id" ).val( attachment.id );
                        $link.nextAll( ".remove_image_button" ).show();
                    }
                }
                //file_frame.close();
            } );

            // Finally, open the modal on click
            file_frame.open();

        } );
        //
        $( document ).on( 'click', '.remove_image_button', function ( e ) {
            var $el = $( e.target );
            $el.prevAll( 'input.option_thumbnail_id' ).val( '' );
            $el.hide();
        } );
        //
        $( document ).on( 'click', '.remove_file_button', function ( e ) {
            var $el = $( e.target );
            $el.prevAll( 'input.option_thumbnail_id' ).val( '' );
            $el.hide();
        } );
        // Only show the "remove image" button when needed
        $.each( $( "input.option_thumbnail_id" ), function ( key, el ) {
            var $el = $( el ),
                elVal = $el.val();

            if ( elVal ) {
                $el.nextAll( '.remove_image_button' ).show();
            }
        } );

        // color picker
        $( '.uni-cpo-palette-option-field-color' ).wpColorPicker( {
            mode: 'hsl',
            palettes: false
        } );



    } );

    // 'closed' event for option settings modal window
    $( document ).on( 'closed', '.cpo-option-modal', function () {
        $( "#uni_cpo_modal_form_option_id" ).val( '' );
        $( "#uni_cpo_modal_form_option_type" ).val( '' );
        $( this ).find( ".uni_cpo_submit" ).removeClass( "uni-cpo-settings-error uni-cpo-settings-unsaved" ).addClass( "uni-cpo-settings-saved" );
        // destroy rule builder
        $( '#cpo-option-condition-builder' ).queryBuilder( 'destroy' );
        // destroy tabs
        $( "#cpo-option-modal-tabs" ).tabs( "destroy" ).empty();
        // unbind click event on ".upload_image_button" in order to prevent openning multiple uploader modal windows
        $( document ).off( 'click', '.upload_image_button, input[name=uni_cpo_field_header_tooltip_image], .add_file_button' );
    } );

    // 'opening' event for non option variables modal window
    $( document ).on( 'opening', '.cpo-non-option-vars-modal', function () {
        // on form's inputs change
        $( ".uni-cpo-modal-field" ).on( "change", function () {
            var this_field = $( this ),
                this_form = this_field.closest( "form" ),
                submit_button = this_form.find( ".uni_cpo_submit" );
            submit_button.removeClass( "uni-cpo-settings-saved" ).addClass( "uni-cpo-settings-unsaved" );
        } );

        if ( $( ".uni-cpo-non-option-vars-options-repeat" ).length > 0 ) {
            $( ".uni-cpo-non-option-vars-options-repeat" ).each( function () {
                $( this ).repeatable_fields( {
                    wrapper: '.uni-cpo-non-option-vars-options-repeat-wrapper',
                    container: '.uni-cpo-non-option-vars-options-wrapper',
                    row: '.uni-cpo-non-option-vars-options-row',
                    add: '.uni_cpo_non_option_vars_option_add',
                    remove: '.uni_cpo_non_option_vars_option_remove',
                    move: '.uni_cpo_non_option_vars_option_move',
                    template: '.uni-cpo-non-option-vars-options-template',
                    is_sortable: true,
                    before_add: null,
                    after_add: uni_after_add_row,
                    before_remove: null,
                    after_remove: null,
                    sortable_options: null,
                    row_count_placeholder: '{{row-count}}',
                } );
            } );
        }

    } );

    // 'opening' event for formula conditional rules modal window
    $( document ).on( 'opening', '.cpo-conditional-modal', function () {
        // on form's inputs change
        $( '.uni-cpo-modal-field' ).on( 'change', function () {
            var $this_field = $( this ),
                $this_form = $this_field.closest( 'form' ),
                $submit_button = $this_form.find( '.uni_cpo_submit' );

            $submit_button.removeClass( 'uni-cpo-settings-saved' ).addClass( 'uni-cpo-settings-unsaved' );
        } );

    } );

    // 'closed' event for formula conditional rules modal window
    $( document ).on( 'closed', '.cpo-conditional-modal', function () {
        $( this ).find( '.uni_cpo_submit' ).removeClass( 'uni-cpo-settings-error uni-cpo-settings-unsaved' ).addClass( 'uni-cpo-settings-saved' );
        // destroy rule builder
        if ( $( '.cpo-formula-rule-builder' ).length > 1 ) {
            $( '.cpo-formula-rule-builder' ).each( function ( i ) {
                $( '#cpo-formula-rule-builder-' + i ).queryBuilder( 'destroy' );
            } );
        }
    } );

    // 'opening' event for cart discounts modal window
    $( document ).on( 'opening', '.cpo-discounts-modal', function () {
        // on form's inputs change
        $( ".uni-cpo-modal-field" ).on( "change", function () {
            var this_field = $( this ),
                this_form = this_field.closest( "form" ),
                submit_button = this_form.find( ".uni_cpo_submit" );
            submit_button.removeClass( "uni-cpo-settings-saved" ).addClass( "uni-cpo-settings-unsaved" );
        } );

        if ( $( ".uni-cpo-discounts-options-repeat" ).length > 0 ) {
            $( ".uni-cpo-discounts-options-repeat" ).each( function () {
                $( this ).repeatable_fields( {
                    wrapper: '.uni-cpo-discounts-options-repeat-wrapper',
                    container: '.uni-cpo-discounts-options-wrapper',
                    row: '.uni-cpo-discounts-options-row',
                    add: '.uni_cpo_discounts_option_add',
                    remove: '.uni_cpo_discounts_option_remove',
                    move: '.uni_cpo_discounts_option_move',
                    template: '.uni-cpo-discounts-options-template',
                    is_sortable: true,
                    before_add: null,
                    after_add: uni_after_add_row,
                    before_remove: null,
                    after_remove: null,
                    sortable_options: null,
                    row_count_placeholder: '{{row-count}}',
                } );
            } );
        }

    } );



    //////////////////////////////////////////////////////////////////////////////////////
    // expression query builder
    //////////////////////////////////////////////////////////////////////////////////////
    // expression query builder - option displaying conditional rules
    function uni_cpo_init_conditional_rules_builder( aFilter, oRules ) {

        $( "#cpo-option-condition-builder" ).queryBuilder( {
            plugins: {
                //'bt-tooltip-errors': { delay: 100},
                'sortable': null
            },
            allow_groups: 1,
            filters: aFilter
        } );

        if ( typeof oRules !== 'undefined' && oRules !== null && !$.isEmptyObject( oRules ) ) {
            $( "#cpo-option-condition-builder" ).queryBuilder( 'setRules', oRules );
        }


        //
        $( '#cpo-option-condition-builder' ).on( 'afterAddRule afterDeleteRule.queryBuilder afterUpdateRuleValue.queryBuilder afterUpdateRuleFilter.queryBuilder afterUpdateRuleOperator.queryBuilder afterUpdateGroupCondition.queryBuilder', function ( e, rule, error, value ) {
            $( "#parse-option-conditional-rule-builder" ).removeClass( "uni-cpo-settings-saved" ).addClass( "uni-cpo-settings-unsaved" );
        } );

    }

    //
    $( document.body ).on( 'click', '#parse-option-conditional-rule-builder', function ( e ) {
        var $this_link = $( e.target ),
            rules = $( "#cpo-option-condition-builder" ).queryBuilder( "getRules" );

        if ( rules ) {
            // save into input
            $( "#uni_cpo_field_conditional_scheme" ).empty()
                .val( JSON.stringify( rules, undefined, 2 ) );
            $( ".uni-cpo-modal-field" ).trigger( "change" );
            $this_link.removeClass( "uni-cpo-settings-unsaved" ).addClass( "uni-cpo-settings-saved" );
        } else {
            $this_link.removeClass( "uni-cpo-settings-saved" ).addClass( "uni-cpo-settings-unsaved" );
        }
    } );

    // expression builder - price & weight conditional rules
    function uni_cpo_init_formula_conditional_rules_builder( aFilter, aRules ) {

        if ( $( ".uni-formula-conditional-rules-repeat" ).length > 0 ) {
            $( ".uni-formula-conditional-rules-repeat" ).each( function () {
                $( this ).repeatable_fields( {
                    wrapper: '.uni-formula-conditional-rules-repeat-wrapper',
                    container: '.uni-formula-conditional-rules-options-wrapper',
                    row: '.uni-formula-conditional-rules-options-row',
                    add: '.uni_formula_conditional_rule_add',
                    remove: '.uni_formula_conditional_rule_remove',
                    move: '.uni_formula_conditional_rule_move',
                    template: '.uni-formula-conditional-rules-options-template',
                    is_sortable: true,
                    before_add: null,
                    after_add: function ( container, new_row ) {
                        uni_after_conditional_add( container, new_row, aFilter );
                    },
                    before_remove: null,
                    //after_remove: null,
                    sortable_options: {
                        stop: function ( event, ui ) {
                            $( ".cpo-parse-formula-rule-json" ).each( function () {
                                $( this ).trigger( "click" );
                            } );
                        }
                    },
                    row_count_placeholder: '{{row-count}}',
                } );
            } );
        }

        // conditional rule builder
        if ( $( ".cpo-formula-rule-builder" ).length > 1 ) {
            $( ".cpo-formula-rule-builder" ).each( function ( i ) {

                // init builder
                if ( $( "#cpo-formula-rule-builder-" + i ).length > 0 ) {
                    $( "#cpo-formula-rule-builder-" + i ).queryBuilder( {

                        plugins: {
                            //'bt-tooltip-errors': { delay: 100},
                            'sortable': null
                        },
                        allow_groups: 1,
                        filters: aFilter

                    } );

                    if ( typeof aRules[ i ] !== 'undefined' && aRules[ i ] !== null && !$.isEmptyObject( aRules[ i ] ) ) {
                        $( "#cpo-formula-rule-builder-" + i ).queryBuilder( 'setRules', aRules[ i ] );
                    }

                    //
                    $( "#cpo-formula-rule-builder-" + i ).on( 'afterAddRule afterDeleteRule.queryBuilder afterUpdateRuleValue.queryBuilder afterUpdateRuleFilter.queryBuilder afterUpdateRuleOperator.queryBuilder afterUpdateGroupCondition.queryBuilder', function ( e, rule, error, value ) {
                        var $getRuleButton = $( e.target ).next( ".cpo-parse-formula-rule-json" );
                        $getRuleButton.removeClass( "uni-cpo-settings-saved" ).addClass( "uni-cpo-settings-unsaved" );
                    } );
                }

            } );
        }

    }

    //
    $( document.body ).on( "click", ".cpo-parse-formula-rule-json", function () {
        var $this_link = $( this ),
            this_id = $( this ).data( "id" ),
            rules = $( "#cpo-formula-rule-builder-" + this_id ).queryBuilder( "getRules" );

        if ( rules ) {
            // save into input
            $( "#uni_cpo_formula_rule_scheme-" + this_id ).empty()
                .val( JSON.stringify( rules, undefined, 2 ) );
            $( ".uni-cpo-modal-field" ).trigger( "change" );
            //
            $this_link.removeClass( "uni-cpo-settings-unsaved" ).addClass( "uni-cpo-settings-saved" );
        } else {
            $this_link.removeClass( "uni-cpo-settings-saved" ).addClass( "uni-cpo-settings-unsaved" );
        }
    } );

    // expression builder - price & weight conditional rules
    function uni_cpo_init_validation_conditional_rules_builder( aFilter, aRules ) {

        if ( $( ".uni-validation-conditional-rules-repeat" ).length > 0 ) {
            $( ".uni-validation-conditional-rules-repeat" ).each( function () {
                $( this ).repeatable_fields( {
                    wrapper: '.uni-validation-conditional-rules-repeat-wrapper',
                    container: '.uni-validation-conditional-rules-options-wrapper',
                    row: '.uni-validation-conditional-rules-options-row',
                    add: '.uni_validation_conditional_rule_add',
                    remove: '.uni_validation_conditional_rule_remove',
                    move: '.uni_validation_conditional_rule_move',
                    template: '.uni-validation-conditional-rules-options-template',
                    is_sortable: true,
                    before_add: null,
                    after_add: function ( container, new_row ) {
                        uni_after_val_conditional_add( container, new_row, aFilter );
                    },
                    before_remove: null,
                    //after_remove: null,
                    sortable_options: {
                        stop: function ( event, ui ) {
                            $( ".cpo-parse-validation-rule-json" ).each( function () {
                                $( this ).trigger( "click" );
                            } );
                        }
                    },
                    row_count_placeholder: '{{row-count}}',
                } );
            } );
        }

        // conditional rule builder
        if ( $( ".cpo-validation-rule-builder" ).length > 1 ) {
            $( ".cpo-validation-rule-builder" ).each( function ( i ) {

                // init builder
                if ( $( "#cpo-validation-rule-builder-" + i ).length > 0 ) {
                    $( "#cpo-validation-rule-builder-" + i ).queryBuilder( {

                        plugins: {
                            //'bt-tooltip-errors': { delay: 100},
                            'sortable': null
                        },
                        allow_groups: 1,
                        filters: aFilter

                    } );

                    if ( typeof aRules[ i ] !== 'undefined' && aRules[ i ] !== null && !$.isEmptyObject( aRules[ i ].rule ) ) {
                        $( "#cpo-validation-rule-builder-" + i ).queryBuilder( 'setRules', aRules[ i ].rule );
                    }

                    //
                    $( "#cpo-validation-rule-builder-" + i ).on( 'afterAddRule afterDeleteRule.queryBuilder afterUpdateRuleValue.queryBuilder afterUpdateRuleFilter.queryBuilder afterUpdateRuleOperator.queryBuilder afterUpdateGroupCondition.queryBuilder', function ( e, rule, error, value ) {
                        var $getRuleButton = $( e.target ).next( ".cpo-parse-validation-rule-json" );
                        $getRuleButton.removeClass( "uni-cpo-settings-saved" ).addClass( "uni-cpo-settings-unsaved" );
                    } );
                }

            } );
        }

    }

    //
    $( document.body ).on( "click", ".cpo-parse-validation-rule-json", function () {
        var $this_link = $( this ),
            this_id = $( this ).data( "id" ),
            rules = $( "#cpo-validation-rule-builder-" + this_id ).queryBuilder( "getRules" );

        if ( rules ) {
            // save into input
            $( "#uni_cpo_validation_rule_scheme-" + this_id ).empty()
                .val( JSON.stringify( rules, undefined, 2 ) );
            $( ".uni-cpo-modal-field" ).trigger( "change" );
            //
            $this_link.removeClass( "uni-cpo-settings-unsaved" ).addClass( "uni-cpo-settings-saved" );
        } else {
            $this_link.removeClass( "uni-cpo-settings-saved" ).addClass( "uni-cpo-settings-unsaved" );
        }
    } );


    // opens a modal window to add/edit conditional rules
    $( '.uni-cpo-conditional-modal-link' ).on( 'click', function () {

        var el = $( this ),
            pId = el.data( 'pid' ),
            type = el.data( 'type' ),
            title = el.data( 'title' ),
            action_part = el.data( 'action-part' );

        var $wrapper = $( '.unicpo-settings-wrapper' ),
            $container_title = $( '#js-cpo-conditional-rules-heading' ),
            $container = $( '#js-cpo-conditional-rules-container' ),
            $action_input = $container.closest( 'form' ).find( 'input[name=action]' );

        var sendData = {
            action: action_part + '_show',
            pid: pId,
            cheaters_always_disable_js: 'true_bro',
        };

        $.ajax( {
            type: 'post',
            url: ajaxurl,
            data: sendData,
            dataType: 'json',
            beforeSend: function ( response ) {
                $wrapper.block( {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                } );
            },
            success: function ( response ) {
                //console.log(response);
                if ( response.status == "success" ) {
                    block_ui_change_to_green();
                    $wrapper.unblock();

                    // modify modal form
                    $action_input.val( action_part + '_save' );
                    $container_title.text( title );
                    $container.html( response.output );

                    cpoConditionalRulesModal.open();
                    //
                    if ( response.filter.length > 0 ) {
                        uni_cpo_init_formula_conditional_rules_builder( response.filter, response.rules );
                    }
                } else if ( response.status == "error" ) {
                    block_ui_change_to_red();
                    $wrapper.unblock();

                    // modify modal form
                    $container_title.text( title );
                    $container.html( response.message );

                    cpoConditionalRulesModal.open();
                }
            },
            error: function ( response ) {
                block_ui_change_to_red();
                $wrapper.unblock();
                $container.html( response.message );
            }
        } );

    } );

    // opens a modal window to export/import conditional rules
    $( ".uni-cpo-conditional-imex-modal-link" ).on( "click", function () {

        var el = $( this ),
            pId = el.data( 'pid' ),
            type = el.data( 'type' ),
            title = el.data( 'title' ),
            action_part = el.data( 'action-part' );

        var $wrapper = $( '.unicpo-settings-wrapper' ),
            $container_title = $( '#js-cpo-conditional-rules-imex-heading' ),
            $container = $( '#js-cpo-conditional-rules-imex-container' ),
            $action_input = $container.closest( 'form' ).find( 'input[name=action]' );

        var sendData = {
            action: action_part + '_show',
            pid: pId,
            cheaters_always_disable_js: 'true_bro',
        };

        $.ajax( {
            type: 'post',
            url: ajaxurl,
            data: sendData,
            dataType: 'json',
            beforeSend: function ( response ) {
                $wrapper.block( {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                } );
            },
            success: function ( response ) {
                //console.log(response);
                if ( response.status == "success" ) {
                    block_ui_change_to_green();
                    $wrapper.unblock();

                    // modify modal form
                    $action_input.val( action_part + '_save' );
                    $container_title.text( title );
                    $container.html( response.output );

                    cpoConditionalRulesImexModal.open();

                    if ( response.rules.length > 0 ) {
                        var prettyJson = JSON.stringify( response.rules, null, 4 );
                        $( 'textarea[name=uni_cpo_' + type + '_rule_imex]' ).val( prettyJson );
                    }
                } else if ( response.status == "error" ) {
                    block_ui_change_to_red();
                    $wrapper.unblock();

                    // modify modal form
                    $container_title.text( title );
                    $container.html( response.message );

                    cpoConditionalRulesImexModal.open();
                }
            },
            error: function ( response ) {
                block_ui_change_to_red();
                $wrapper.unblock();
                $container.html( response.message );
            }
        } );

    } );

    $( document ).on( 'click', '#uni_export_suboptions', function ( e ) {
        var $exporterWrapper = $( '.uni-exporter-wrapper' );

        if ( $exporterWrapper.is(":visible") ) {
            $exporterWrapper.slideToggle();
        }

        var $exporterProgress = $exporterWrapper.find( '.uni-exporter-progress' ),
            $thisForm = $exporterWrapper.closest( '.uni_cpo_form' ),
            nonce = $thisForm.find( 'input[name=uni_auth_nonce]' ).val(),
            pid = $thisForm.find( 'input[name=pid]' ).val(),
            oid = $thisForm.find( '#uni_cpo_modal_form_option_id' ).val(),
            field_name = $( this ).data( 'field-name' );

        $exporterProgress.val( 0 );
        $exporterWrapper.slideToggle();

        $.ajax( {
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'uni_cpo_do_suboptions_export',
                option_id: oid,
                product_id: pid,
                security: nonce,
                field_name: field_name
            },
            dataType: 'json',
            success: function ( response ) {
                if ( response.success ) {
                    $exporterProgress.val( response.data.percentage );
                    window.location = response.data.url;
                    setTimeout( function() {
                        $exporterWrapper.slideToggle();
                    }, 3000 );
                }
            }
        } ).fail( function ( response ) {
            window.console.log( response );
        } );
    } );

    $( document ).on( 'click', '#uni_import_suboptions', function ( e ) {
        var $importerWrapper = $( '.uni-importer-wrapper' );
        $importerWrapper.slideToggle();
    } );

    $( document ).on( 'click', '#uni_import_suboptions_submit', function ( e ) {
        var $importerWrapper = $( '.uni-importer-wrapper' ),
            $file = $importerWrapper.find('input[name=suboptions-import]'),
            file = $file[0].files[0],
            $thisForm = $importerWrapper.closest( '.uni_cpo_form' ),
            nonce = $thisForm.find( 'input[name=uni_auth_nonce]' ).val(),
            pid = $thisForm.find( 'input[name=pid]' ).val(),
            oid = $thisForm.find( '#uni_cpo_modal_form_option_id' ).val(),
            field_name = $( this ).data( 'field-name' ),
            formData = new FormData(),
            otherData = {
                //action: 'uni_cpo_do_suboptions_import',
                option_id: oid,
                product_id: pid,
                security: nonce,
                field_name: field_name
            };

        if ( typeof file !== 'undefined' ) {
            formData.append('file', file);
            $.each(otherData, function(key, value){
                formData.append(key, value);
            });

            $.ajax( {
                type: 'POST',
                url: ajaxurl + '?action=uni_cpo_do_suboptions_import',
                data: formData,
                //dataType: 'json',
                processData: false,
                contentType: false,
                success: function ( response ) {
                    if ( response.success ) {
                        cpoOptionEditModal.close()
                    }
                }
            } ).fail( function ( response ) {
                window.console.log( response );
            } );
        }

    } );

} );


// deselectable radio inputs
var uniPrvState;
var uniMarkIt = function ( e ) {

    var elClickedInput = this;
    jQuery( '.uni-cpo-modal-field-default-deselectable' ).each( function ( i ) {
        if ( elClickedInput !== this ) {
            this.checked = false;
        }
    } );

    if ( uniPrvState === this && this.checked ) {
        this.checked = false;
        uniPrvState = null; //allow seemless selection for the same radio
    } else {
        uniPrvState = this;
    }
};

// checks if it is a number
function uniIsNumber( n ) {
    return !isNaN( parseFloat( n ) ) && isFinite( n );
}

// converts any text to slug like text
function uniConvertToSlug( text ) {

    st = text.toLowerCase();
    st = st.replace(/[\u00C0-\u00C5]/ig,'a');
    st = st.replace(/[\u00C8-\u00CB]/ig,'e');
    st = st.replace(/[\u00CC-\u00CF]/ig,'i');
    st = st.replace(/[\u00D2-\u00D6]/ig,'o');
    st = st.replace(/[\u00D9-\u00DC]/ig,'u');
    st = st.replace(/[\u00D1]/ig,'n');
    st = st.trim();
    st = st.replace(/ /g,'_');
    st = st.replace(/-/g, '_');
    st = st.replace(/[^\w-]+/g, '');
    return st;
}

// mini plugin - inserts some text at place of caret
jQuery.fn.insertAtCaret = function ( myValue ) {
    return this.each( function () {
        //IE support
        if ( document.selection ) {
            this.focus();
            sel = document.selection.createRange();
            sel.text = myValue;
            this.focus();
        }
        //MOZILLA / NETSCAPE support
        else if ( this.selectionStart || this.selectionStart == '0' ) {
            var startPos = this.selectionStart;
            var endPos = this.selectionEnd;
            var scrollTop = this.scrollTop;
            this.value = this.value.substring( 0, startPos ) + myValue + this.value.substring( endPos, this.value.length );
            this.focus();
            this.selectionStart = startPos + myValue.length;
            this.selectionEnd = startPos + myValue.length;
            this.scrollTop = scrollTop;
        } else {
            this.value += myValue;
            this.focus();
        }
    } );
};