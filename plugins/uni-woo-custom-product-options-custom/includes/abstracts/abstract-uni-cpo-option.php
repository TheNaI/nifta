<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option Abstract class
*
*/

abstract class Uni_Cpo_Option {

    public $id = 0;

    public $post = null;

    public $option_type = null;

    public $option_icon = 'fa-question';

    public $option_name = '';

    public $tab_settings = array();

    public $specific_settings = array();

    // make it 'false' if:
    // - this option cannot be modified in the front end in any way
    // - a value of this option cannot be get and/or used in a formula
    protected $calc_status = true;

	/**
	 * Constructor gets the post object and sets the ID for the loaded option.
	 *
	 */
	public function __construct( $Option = false ) {
		if ( is_numeric( $Option ) ) {
			$this->id   = absint( $Option );
			$this->post = get_post( $this->id );
		} else if ( $Option instanceof Uni_Cpo_Option ) {
			$this->id   = absint( $Option->id );
			$this->post = $Option->post;
		} else if ( isset( $Option->ID ) ) {
			$this->id   = absint( $Option->ID );
			$this->post = $Option;
		}
	}

	/**
	 * Returns the option ID
	 *
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Returns fontawesome icon class
	 *
	 */
	public function get_icon() {
		return $this->option_icon;
	}

	/**
	 * Returns type of option
	 *
	 */
	public function get_type() {
		return is_null( $this->option_type ) ? '' : $this->option_type;
	}

    /**
	 * Returns form element unique slug-name
	 *
	 */
	public function get_slug() {
		return trim( $this->post->post_title, '{}' );
	}

    /**
	 * Returns form element unique slug-name
	 *
	 */
	public function get_post_meta() {
		return get_post_custom( $this->get_id() );
	}

    /**
	 * Returns an array of special vars associated with the option
	 *
	 */
	public function get_special_vars() {
		return array();
	}

	/**
	 * Checks if this option will be used in price calculation
	 *
	 */
	public function is_calculable() {
		return $this->calc_status;
	}

	/**
	 * Checks if this option is required
	 *
	 */
	public function is_required() {
		return ( get_post_meta( $this->get_id(), '_uni_cpo_field_required_enable', true ) === 'yes' ) ? true : false;
	}

	/**
	 * Checks if this option has field conditional rules
	 *
	 */
	public function has_fc_rules() {
		if ( get_post_meta( $this->get_id(), '_uni_cpo_field_conditional_scheme', true ) ) {
            if ( get_post_meta( $this->get_id(), '_uni_cpo_field_conditional_enable', true )
                    && get_post_meta( $this->get_id(), '_uni_cpo_field_conditional_enable', true ) === 'yes' ) {
                return 'on';
            } else {
                return 'off';
            }
        } else {
            return false;
        }
	}

	/**
	 * displays tooltip
	 *
	 */
	public function tooltip_output() {
        $option_meta    = $this->get_post_meta();
        $field_name     = $this->get_slug();
        $allowed_tags   = uni_cpo_allowed_html_for_tooltips();
        $tooltip_icon   = uni_cpo_option_tooltip_icon_output( $this );
        $tooltip_type   = ( ! isset( $option_meta['_uni_cpo_field_header_tooltip_type'][0] ) || empty( $option_meta['_uni_cpo_field_header_tooltip_type'][0] ) ) ? 'classic' : $option_meta['_uni_cpo_field_header_tooltip_type'][0];

        if ( 'classic' === $tooltip_type ) {
            if ( ! empty( $option_meta['_uni_cpo_field_header_tooltip_text'][0] ) && empty( $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0] ) ) {
                echo ' <span class="uni-cpo-tooltip" data-tooltip-content="#uni-cpo-tooltip-'.esc_attr( $field_name ).'">' . $tooltip_icon . '</span>';
                echo '<div class="tooltip_templates"><div id="uni-cpo-tooltip-'.esc_attr( $field_name ).'">' . wp_kses( $option_meta['_uni_cpo_field_header_tooltip_text'][0], $allowed_tags ) . '</div></div>';
            } else if ( ! empty( $option_meta['_uni_cpo_field_header_tooltip_text'][0] ) && ! empty( $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0] ) && 'uni-cpo-custom-tooltip' !== $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0] ) {
                echo ' <span class="' . esc_attr( $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0] ) . ' uni-cpo-tooltip-element" title="' . esc_attr( wp_kses( $option_meta['_uni_cpo_field_header_tooltip_text'][0], $allowed_tags ) ) . '">' . $tooltip_icon . '</span>';
            } else if ( ! empty( $option_meta['_uni_cpo_field_header_tooltip_text'][0] ) && ! empty( $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0] ) && 'uni-cpo-custom-tooltip' === $option_meta['_uni_cpo_field_extra_tooltip_selector_class'][0] ) {
                echo ' <div class="uni-cpo-custom-tooltip-element">';
                    echo $tooltip_icon;
                    echo '<div class="uni-cpo-custom-tooltip-content">';
                    echo wp_kses( $option_meta['_uni_cpo_field_header_tooltip_text'][0], $allowed_tags );
                    echo '</div>';
                echo '</div>';
            }
        } else if ( 'lightbox' === $tooltip_type ) {
            $tooltip_attach_id = intval( $option_meta['_uni_cpo_field_header_tooltip_image'][0] );
            $image = wp_get_attachment_image_src( $tooltip_attach_id, 'full' );
            echo '<a href="' . esc_url( $image[0] ) . '" data-lity data-lity-desc="" class="uni-cpo-tooltip-element">';
            echo $tooltip_icon;
            echo '</a>';
        }
	}

	/**
	 * Checks if this option has field conditional rules
	 *
	 */
	public function render_fc_rules() {

        if ( 'on' === $this->has_fc_rules() ) {

            $post_custom            = $this->get_post_meta();
            $slug                   = $this->get_slug();
            $field_rules            = maybe_unserialize( $post_custom['_uni_cpo_field_conditional_scheme'][0] );
            $visibility             = ( isset( $post_custom['_uni_cpo_field_conditional_default'][0] ) ) ? $post_custom['_uni_cpo_field_conditional_default'][0] : 'hide';
            $statement              = '';
            $final_js_statement     = '';
            $cond_field_names_array = array();

            // builds a statement
            if ( is_array( $field_rules ) && ! empty( $field_rules['rules'] ) ) {

                // builds array of option slugs which are used in the statement
                foreach ( $field_rules['rules'] as $rule ) {
                    if ( ! isset($rule['rules']) ) {
                        $cond_field_slug    = $rule['id'];
                        $option_post        = uni_cpo_get_post_by_slug( $rule['id'] );
                        $option             = uni_cpo_get_option( $option_post );
                        // detect special vars
                        if ( false === $option ) {
                            $field_type     = 'special_var';
                            $name_and_prop  = uni_cpo_detect_special_var_name( $rule['id'] );
                            if ( ! empty( $name_and_prop ) ) {
                                $cond_field_slug = $name_and_prop['name'];
                                $var_field_type  = $name_and_prop['type'];
                            } else {
                                continue;
                            }
                        } else {
                            $field_type         = $option->get_type();
                        }
                        if ( 'checkboxes' === $field_type ) {
                            $cond_field_names_array[$cond_field_slug] = "[name='uni_cpo_{$cond_field_slug}[]']";
                        } elseif ( 'special_var' === $field_type ) {
                            if ( 'checkboxes' === $var_field_type ) {
                                $cond_field_names_array[$cond_field_slug] = "[name='uni_cpo_{$cond_field_slug}[]']";
                            } else {
                                $cond_field_names_array[$cond_field_slug] = "[name='uni_cpo_{$cond_field_slug}']";
                            }
                        } else {
                            $cond_field_names_array[$cond_field_slug] = "[name='uni_cpo_{$cond_field_slug}']";
                        }
                    } else {
                        foreach ( $rule['rules'] as $second_level_rule ) {
                            $cond_field_slug    = $second_level_rule['id'];
                            $option_post        = uni_cpo_get_post_by_slug( $second_level_rule['id'] );
                            $option             = uni_cpo_get_option( $option_post );
                            // detect special vars
                            if ( false === $option ) {
                                $field_type     = 'special_var';
                                $name_and_prop  = uni_cpo_detect_special_var_name( $second_level_rule['id'] );
                                if ( ! empty( $name_and_prop ) ) {
                                    $cond_field_slug = $name_and_prop['name'];
                                    $field_type      = $name_and_prop['type'];
                                } else {
                                    continue;
                                }
                            } else {
                                $field_type         = $option->get_type();
                            }
                            if ( 'checkboxes' === $field_type ) {
                                $cond_field_names_array[$cond_field_slug] = "[name='uni_cpo_{$cond_field_slug}[]']";
                            } elseif ( 'special_var' === $field_type ) {
                                if ( 'checkboxes' === $var_field_type ) {
                                    $cond_field_names_array[$cond_field_slug] = "[name='uni_cpo_{$cond_field_slug}[]']";
                                } else {
                                    $cond_field_names_array[$cond_field_slug] = "[name='uni_cpo_{$cond_field_slug}']";
                                }
                            } else {
                                $cond_field_names_array[$cond_field_slug] = "[name='uni_cpo_{$cond_field_slug}']";
                            }
                        }
                    }
                }
                $cond_field_names       = implode( ', ', $cond_field_names_array );

                //
                $statement = "var extraClass = 'uni-cpo-excluded-from-validation';"; 
                $statement .= 'if ( ';

                $statement .= uni_cpo_option_condition_js_statement_builder( array( $field_rules ) );

                $statement .= ') {';

                if ( 'hide' === $visibility ) {
                    $statement .= 'if ( ! $' . esc_attr( $slug ) . '_cont.hasClass("cpo-visible") ) {';
                        $statement .= '$' . esc_attr( $slug ) . '_cont.slideDown(400).addClass("cpo-visible-field");';
                    $statement .= '}';
                    if ( 'checkboxes' === $this->get_type() || 'radio' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.fields.each(function( index ) { $(this).removeClass( extraClass ); });';
                        $statement .= '$' . esc_attr( $slug ) . '.main.removeClass( extraClass );';
                    } elseif ( 'date_picker' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.main.removeClass( extraClass );';
                        $statement .= '$' . esc_attr( $slug ) . '.end.removeClass( extraClass );';
                    } elseif ( in_array( $this->get_type(), array('notice', 'heading', 'divider', 'notice_nonoptionvar') ) ) {
                    } elseif ( 'image_select' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.main.removeClass( extraClass );';
                        $statement .= '$("#' . esc_attr( $slug ) . '_list").find("li.active a img").trigger("click");';
                    } else {
                        $statement .= '$' . esc_attr( $slug ) . '.main.removeClass( extraClass );';
                    }
                } elseif ( 'show' === $visibility ) {
                    $statement .= '$' . esc_attr( $slug ) . '_cont.hide().removeClass("cpo-visible-field");';
                    if ( 'checkboxes' === $this->get_type() || 'radio' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.fields.each(function( index ) { $(this).addClass( extraClass ); });';
                        $statement .= '$' . esc_attr( $slug ) . '.main.addClass( extraClass );';
                    } elseif ( 'date_picker' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.main.addClass( extraClass );';
                        $statement .= '$' . esc_attr( $slug ) . '.end.addClass( extraClass );';
                    } elseif ( in_array( $this->get_type(), array('notice', 'heading', 'divider', 'notice_nonoptionvar') ) ) {
                    } elseif ( 'image_select' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.main.addClass( extraClass ).val("");';
                        $statement .= 'uniCpoReplaceProductImage( $' . esc_attr( $slug ) . '.main );';
                    } elseif ( 'select' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.main.addClass( extraClass ).val("");';
                        $statement .= 'uniCpoReplaceProductImage( $' . esc_attr( $slug ) . '.main );';
                    } else {
                        $statement .= '$' . esc_attr( $slug ) . '.main.addClass( extraClass ).val("");';
                    }
                }

                $statement .= '} else {';

                if ( 'hide' === $visibility ) {
                    $statement .= '$' . esc_attr( $slug ) . '_cont.hide().removeClass("cpo-visible-field");';
                    if ( 'checkboxes' === $this->get_type() || 'radio' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.fields.each(function( index ) { $(this).addClass( extraClass ); });';
                        $statement .= '$' . esc_attr( $slug ) . '.main.addClass( extraClass );';
                    } elseif ( 'date_picker' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.main.addClass( extraClass );';
                        $statement .= '$' . esc_attr( $slug ) . '.end.addClass( extraClass );';
                    } elseif ( in_array( $this->get_type(), array('notice', 'heading', 'divider', 'notice_nonoptionvar') ) ) {
                    } elseif ( 'image_select' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.main.addClass( extraClass ).val("");';
                        $statement .= 'uniCpoReplaceProductImage( $' . esc_attr( $slug ) . '.main );';
                    } elseif ( 'select' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.main.addClass( extraClass ).val("");';
                        $statement .= 'uniCpoReplaceProductImage( $' . esc_attr( $slug ) . '.main );';
                    } else {
                        $statement .= '$' . esc_attr( $slug ) . '.main.addClass( extraClass ).val("");';
                    }
                } else if ( 'show' === $visibility ) {
                    $statement .= 'if ( ! $' . esc_attr( $slug ) . '_cont.hasClass("cpo-visible") ) {';
                        $statement .= '$' . esc_attr( $slug ) . '_cont.slideDown(400).addClass("cpo-visible-field");';
                    $statement .= '}';
                    if ( 'checkboxes' === $this->get_type() || 'radio' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.fields.each(function( index ) { $(this).removeClass( extraClass ); });';
                        $statement .= '$' . esc_attr( $slug ) . '.main.removeClass( extraClass );';
                    } elseif ( 'date_picker' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.main.removeClass( extraClass );';
                        $statement .= '$' . esc_attr( $slug ) . '.end.removeClass( extraClass );';
                    } elseif ( in_array( $this->get_type(), array('notice', 'heading', 'divider', 'notice_nonoptionvar') ) ) {
                    } elseif ( 'image_select' === $this->get_type() ) {
                        $statement .= '$' . esc_attr( $slug ) . '.main.removeClass( extraClass );';
                        $statement .= '$("#' . esc_attr( $slug ) . '_list").find("li.active a img").trigger("click");';
                    } else {
                        $statement .= '$' . esc_attr( $slug ) . '.main.removeClass( extraClass );';
                    }
                }

                $statement .= '}';

            }

            if ( ! empty( $statement ) ) {
                $final_js_statement = $statement;
            }

            // outputs a js code
            if ( ! empty( $final_js_statement ) ) {
            ?>
                        <script>
                        jQuery( document ).ready( function( $ ) {
                            'use strict';

                            $("<?php echo $cond_field_names; ?>").on( 'change', function(){
                                <?php echo esc_attr( $slug ) ?>_func();
                            });

                            <?php if ( 'notice_nonoptionvar' === $this->get_type() ) { ?>
                                    var cpo_dnotice_<?php echo esc_attr( $slug ); ?> = wp.template( 'cpo-dnotice-<?php echo esc_attr( $slug ); ?>' ),
                                        def_data = unicpo.nov_vars;
                                    jQuery( '#cpo_container_dnotice_<?php echo esc_attr( $slug ); ?>' ).html( cpo_dnotice_<?php echo esc_attr( $slug ); ?>( def_data ) );
                            <?php } ?>

                            function <?php echo esc_attr( $slug ) ?>_func(){

                                <?php if ( 'notice_nonoptionvar' === $this->get_type() ) { ?>
                                    var $<?php echo esc_attr( $slug ) ?>_cont   = $('#<?php echo esc_attr( $slug ) ?>');
                                <?php } else { ?>
                                var $<?php echo esc_attr( $slug ) ?>_cont  = $('#<?php echo esc_attr( $slug ) ?>'),
                                    _<?php echo esc_attr( $slug ) ?>_slug  =  '<?php echo esc_attr( $slug ) ?>',
                                    _<?php echo esc_attr( $slug ) ?>_type  = '<?php echo esc_attr( $this->get_type() ) ?>',
                                    <?php echo '$' . esc_attr( $slug ) . ' = uni_get_var_obj_for_cond( _' . esc_attr( $slug ) . '_type, _' . esc_attr( $slug ) . '_slug );'; ?>
                                <?php } ?>

                                <?php
                                if ( 'hide' === $visibility ) {
                                    echo 'if ( ! $' . esc_attr( $slug ) . '_cont.hasClass("cpo-visible-field") ) {';
                                        echo '$'.$slug.'_cont.hide();';
                                    echo '}';
                                }
                                ?>

                                <?php
                                foreach ( $cond_field_names_array as $key => $value ) {
                                ?>
                                var _<?php echo esc_attr( $key ) ?>_slug =  'uni_cpo_<?php echo esc_attr( $key ) ?>',
                                    _<?php echo esc_attr( $key ) ?>_type = $('#'+_<?php echo esc_attr( $key ) ?>_slug).data('type'),
                                    $<?php echo esc_attr( $key ) ?>     = uni_get_var_obj_for_cond( _<?php echo esc_attr( $key ) ?>_type, _<?php echo esc_attr( $key ) ?>_slug ),
                                    _<?php echo esc_attr( $key ) ?>_val  = uni_get_var_val_for_cond( _<?php echo esc_attr( $key ) ?>_type, $<?php echo esc_attr( $key ) ?>, _<?php echo esc_attr( $key ) ?>_slug );
                                    <?php /* ?>
                                    if ( typeof _<?php echo esc_attr( $key ) ?>_val !== 'undefined' ) {
                                        console.log(_<?php echo esc_attr($key) ?>_val);
                                    }
                                    <?php */ ?>
                                <?php
                                }
                                ?>

                            	<?php echo $final_js_statement; ?>

                            }

                            <?php echo esc_attr( $slug ) ?>_func();

                            <?php if ( 'notice_nonoptionvar' === $this->get_type() ) { ?>
                            jQuery( document.body ).on( 'cpo_options_data_ajax_success_event', function( e, fields, response ){
                                jQuery( '#cpo_container_dnotice_<?php echo esc_attr( $slug ); ?>' ).html( cpo_dnotice_<?php echo esc_attr( $slug ); ?>( response.nov_vars ) );
                            });
                            <?php } ?>

                        });
                        </script>
            <?php
            } // #end not empty final statement

        }

	}

	/**
	 * gets label
	 *
	 */
	public function get_label() {
        return get_post_meta( $this->get_id(), '_uni_cpo_field_header_text', true );
	}

	/**
	 * gets nice label for cart/order meta
	 *
	 */
	public function get_meta_label() {
        if ( get_post_meta( $this->get_id(), '_uni_cpo_field_meta_header_text', true ) ) {
            $label = get_post_meta( $this->get_id(), '_uni_cpo_field_meta_header_text', true );
        } else if ( get_post_meta( $this->get_id(), '_uni_cpo_field_header_text', true ) ) {
            $label = get_post_meta( $this->get_id(), '_uni_cpo_field_header_text', true );
        } else {
            $label = $this->post->post_title;
        }
        return $label;
	}

	/**
	 * gets form field(s) for option for add/edit options modal on order edit page
	 *
	 */
	public function get_order_form_field() {
	}

	/**
	 * Returns option data
	 *
	 */
	public function get_option_data() {
		return array(
                    'id' => $this->get_id(),
                    'itemtype' => $this->get_type(),
                    'parentid' => get_post_meta( $this->get_id(), '_uni_cpo_parent_option_id', true ),
                    'title' => get_the_title( $this->get_id() ),
                    'icon' => $this->get_icon(),
                    'required' => $this->is_required(),
                    'rules' => $this->has_fc_rules(),
                    'label' => $this->get_label(),
                    'meta_title' => $this->get_meta_label()
                );
	}

	/**
	 * Checks if this option is a child  (deprecated, must be removed)
	 *
	 */
	public function is_child() {
		return ( get_post_meta( $this->get_id(), '_uni_cpo_parent_option_id', true ) ) ? true : false;
	}

    /**
	 * Gets text input type
	 *
	 */
	public function get_input_type() {
	    if ( 'text_input' === $this->get_type ) {
		    return ( get_post_meta( $this->get_id(), '_uni_cpo_field_input_tag_type', true ) ) ? get_post_meta( $this->get_id(), '_uni_cpo_field_input_tag_type', true ) : '';
        } else {
            return '';
        }
	}

    /**
	 * Gets text input type
	 *
	 */
	public function get_datepicker_type() {
	    if ( 'date_picker' === $this->get_type ) {
		    return ( get_post_meta( $this->get_id(), '_uni_cpo_field_datepicker_select', true ) ) ? get_post_meta( $this->get_id(), '_uni_cpo_field_datepicker_select', true ) : '';
        } else {
            return '';
        }
	}

	/**
	 * Returns option admin settings
     *
	 */
	public function get_settings() {
	    return $this->tab_settings;
	}

	/**
	 * Returns option's specific attributtes (settings)
	 *
	 */
	public function get_specific_settings() {
		return $this->specific_settings;
	}

	/**
	 * Displays option in the front end
	 *
	 */
	public function render_option( $aChildrenOptionsIds = array() ) {
	    //
	}

	/**
	 * Generates query builder array of data
	 *
	 */
	public function generate_filter( $bFull = false ) {
	    return array();
	}

	/**
	 * Generates an array of vars and values for formula calculation associated with this option
	 *
	 */
	public function calculation( $aFormPostData, $bCartMeta = false ) {
	    //
	}

}

?>