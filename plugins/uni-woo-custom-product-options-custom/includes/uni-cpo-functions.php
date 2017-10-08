<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//////////////////////////////////////////////////////////////////////////////////////
// various functions
//////////////////////////////////////////////////////////////////////////////////////
//
function uni_cpo_slug_exists( $slug ) {
    $query = new WP_Query( array( 'name' => $slug, 'post_type' => 'uni_cpo_option' ) );
    if ( ! empty( $query->posts ) ) {
        return true;
    } else {
        return false;
    }
}

//
function uni_cpo_get_post_by_slug( $slug ) {
    $query = new WP_Query( array( 'name' => $slug, 'post_type' => 'uni_cpo_option' ) );
    if ( ! empty( $query->posts ) ) {
        return $query->posts[0];
    }
    return null;
}

//
function uni_cpo_get_registered_options_classes( $sPurpose = 'js' ) {

    $aRegisteredOptionsTypes = uni_cpo_get_option_types();
    if ( !empty($aRegisteredOptionsTypes) ) {
        $sClasses = '';
        foreach ( $aRegisteredOptionsTypes as $sTypeOfOption ) {
            $oOption = uni_cpo_get_option( false, $sTypeOfOption );
            if ( $oOption instanceof Uni_Cpo_Option && $oOption->is_calculable() ) {
                // exceptions
                $exceptions = apply_filters( 'uni_cpo_js_change_event_exceptions', array( 'range_slider', 'date_picker', 'google_maps' ) );

                if ( $sPurpose === 'js-change' ) {
                    if ( ! in_array( $oOption->get_type(), $exceptions ) ) {
                        $sClasses .= '.js-uni-cpo-field-'.esc_attr( $oOption->get_type() ).', ';
                    }
                } else if ( $sPurpose === 'js-all' ) {
                    $sClasses .= '.js-uni-cpo-field-'.esc_attr( $oOption->get_type() ).', ';
                }
            }
        }
        $sClasses = rtrim($sClasses, ', ');
        // adds ".qty"
        $sClasses .= ', .qty';
    }
    return $sClasses;
}

//
function uni_cpo_process_formula_conditional_rules_scheme( $iProductId, $sPurpose = 'check', $sOldSlug = '', $sNewSlug = '', $aFormPostData = array() ) {

    $aAllFormulaRules   = get_post_meta( $iProductId, '_uni_cpo_formula_rule_options', true );
    $sMainFormula       = get_post_meta( $iProductId, '_uni_cpo_price_main_formula', true );

    // update
    if ( $sPurpose === 'update' && isset($aAllFormulaRules) && !empty($aAllFormulaRules) && !empty($sOldSlug) && !empty($sNewSlug) ) {

        if ( isset( $aAllFormulaRules ) && !empty( $aAllFormulaRules ) ) {

            foreach ( $aAllFormulaRules as $iRuleFormulaBlockKey => $aRuleFormulaBlock ) {

                // rule
                if ( isset($aRuleFormulaBlock['rule']['rules']) && !empty($aRuleFormulaBlock['rule']['rules']) ) {
                    $sFirstLevelCondition   = $aRuleFormulaBlock['rule']['condition'];

                    foreach ( $aRuleFormulaBlock['rule']['rules'] as $iFirstLevelRuleKey => $oFirstLevelRule ) {

                        // test if this is a group
                        // else it is a single rule (are several single rules)
                        if ( isset($oFirstLevelRule['rules']) && !empty($oFirstLevelRule['rules']) ) {
                            foreach ( $oFirstLevelRule['rules'] as $iSecondLevelRuleKey => $oSecondLevelRule ) {
                                if ( $oSecondLevelRule['id'] === $sOldSlug ) {
                                    $aAllFormulaRules[$iRuleFormulaBlockKey]['rule']['rules'][$iFirstLevelRuleKey]['rules'][$iSecondLevelRuleKey]['id'] = $sNewSlug;
                                    $aAllFormulaRules[$iRuleFormulaBlockKey]['rule']['rules'][$iFirstLevelRuleKey]['rules'][$iSecondLevelRuleKey]['field'] = $sNewSlug;
                                }
                            }
                        } else {
                            if ( $oFirstLevelRule['id'] === $sOldSlug ) {
                                $aAllFormulaRules[$iRuleFormulaBlockKey]['rule']['rules'][$iFirstLevelRuleKey]['id'] = $sNewSlug;
                                $aAllFormulaRules[$iRuleFormulaBlockKey]['rule']['rules'][$iFirstLevelRuleKey]['field'] = $sNewSlug;
                            }
                        }

                    }

                }

                // formula
                if ( isset($aRuleFormulaBlock['formula']) && !empty($aRuleFormulaBlock['formula']) ) {
                    $sReplacedCharsFormula = str_replace( $sOldSlug, $sNewSlug, $aRuleFormulaBlock['formula'] );
                    $aAllFormulaRules[$iRuleFormulaBlockKey]['formula'] = $sReplacedCharsFormula;
                }

            }

        }

        // update in main formula
        if ( isset($sMainFormula) && !empty($sMainFormula) ) {
            $sReplacedCharsFormula = str_replace( $sOldSlug, $sNewSlug, $sMainFormula );
            update_post_meta( $iProductId, '_uni_cpo_price_main_formula', $sReplacedCharsFormula );
        }

        return $aAllFormulaRules;

    // check
    } else if ( $sPurpose === 'check' && isset($aAllFormulaRules) && ! empty($aAllFormulaRules) && isset($aFormPostData) ) {

        if ( isset( $aAllFormulaRules ) && ! empty( $aAllFormulaRules ) ) {
            // convert all the objects to arrays
            $aAllFormulaRules = json_decode( json_encode( $aAllFormulaRules ), true );

            foreach ( $aAllFormulaRules as $iRuleFormulaBlockKey => $aRuleFormulaBlock ) {

                // rule
                if ( isset($aRuleFormulaBlock['rule']['rules']) && !empty($aRuleFormulaBlock['rule']['rules'])
                        && isset($aRuleFormulaBlock['formula']) && !empty($aRuleFormulaBlock['formula']) ) {

                    $sFirstLevelCondition   = $aRuleFormulaBlock['rule']['condition'];
                    $iCountRules = count($aRuleFormulaBlock['rule']['rules']);

                    // if there are more than one rule
                    // then we must take into account the comparison operator
                    $aPassed = array();
                    if ( $iCountRules > 1 ) {
                        $bFirstLevelPassed = false;

                        foreach ( $aRuleFormulaBlock['rule']['rules'] as $iFirstLevelRuleKey => $oFirstLevelRule ) {

                            // if there are more than one rule
                            // then we must take into account the comparison operator
                            if ( isset($oFirstLevelRule['rules']) ) {

                                $sSecondLevelCondition = $oFirstLevelRule['condition'];
                                $aPassedSl = array();
                                foreach ( $oFirstLevelRule['rules'] as $iSecondLevelRuleKey => $oSecondLevelRule ) {
                                    $aPassedSl[] = uni_cpo_formula_condition_check( $oSecondLevelRule, $aFormPostData );
                                }
                                // print_r(' /start$aPassedSl: ');
                                // print_r($aPassedSl);
                                // print_r(' end of $aPassedSl/ ');
                                //
                                if( in_array(false, $aPassedSl, true) === false && $sSecondLevelCondition == 'AND' ){
                                    $aPassed[] = true;
                                } else if( in_array(true, $aPassedSl, true) === false ){
                                    $aPassed[] = false;
                                } else {
                                    if ( $sSecondLevelCondition == 'OR' ) {
                                        $aPassed[] = true;
                                    } else {
                                        $aPassed[] = false;
                                    }
                                }
                                //
                                if ( $bFirstLevelPassed === true ) {
                                    $sConditionFormula = $aRuleFormulaBlock['formula'];
                                    break;
                                }
                            } else {
                                $aPassed[] = uni_cpo_formula_condition_check( $oFirstLevelRule, $aFormPostData );
                            }

                        }

                    } else {
                        //print_r($aRuleFormulaBlock['rule']['rules'][0]);
                        $aPassed[] = uni_cpo_formula_condition_check( $aRuleFormulaBlock['rule']['rules'][0], $aFormPostData );
                    }
                    //print_r($aPassed);

                    //
                    if( in_array(false, $aPassed, true) === false && $sFirstLevelCondition == 'AND' ){
                        $bFirstLevelPassed = true;
                    } else if( in_array(true, $aPassed, true) === false ){
                        //only false;
                        $bFirstLevelPassed = false;
                    } else {
                        if ( $sFirstLevelCondition == 'OR' ) {
                            $bFirstLevelPassed = true;
                        } else {
                            $bFirstLevelPassed = false;
                        }
                    }
                    //var_dump($bFirstLevelPassed);
                    //
                    if ( $bFirstLevelPassed === true ) {
                        $sConditionFormula = $aRuleFormulaBlock['formula'];
                        break;
                    }

                }

            }

        }

        //
        if ( isset($sConditionFormula) && !empty($sConditionFormula) ) {
            $sMainFormula = $sConditionFormula;
        }

        return $sMainFormula;

    }

}

//
function uni_cpo_process_weight_conditional_rules_scheme( $iProductId, $aFormPostData = array() ) {

    $aWeightConditionalRules    = get_post_meta( $iProductId, '_uni_cpo_weight_rule_options', true );
    $sWeightFormula             = '';

        if ( isset( $aWeightConditionalRules ) && !empty( $aWeightConditionalRules ) ) {

            // convert all the objects to arrays
            $aWeightConditionalRules = json_decode( json_encode( $aWeightConditionalRules ), true );

            foreach ( $aWeightConditionalRules as $iRuleFormulaBlockKey => $aRuleFormulaBlock ) {

                // rule
                if ( isset($aRuleFormulaBlock['rule']['rules']) && !empty($aRuleFormulaBlock['rule']['rules'])
                        && isset($aRuleFormulaBlock['formula']) && !empty($aRuleFormulaBlock['formula']) ) {

                    $sFirstLevelCondition   = $aRuleFormulaBlock['rule']['condition'];
                    $iCountRules = count($aRuleFormulaBlock['rule']['rules']);

                    // if there are more than one rule
                    // then we must take into account the comparison operator
                    $aPassed = array();
                    if ( $iCountRules > 1 ) {
                        $bFirstLevelPassed = false;

                        foreach ( $aRuleFormulaBlock['rule']['rules'] as $iFirstLevelRuleKey => $oFirstLevelRule ) {

                            // if there are more than one rule
                            // then we must take into account the comparison operator
                            if ( isset($oFirstLevelRule['rules']) ) {

                                $sSecondLevelCondition = $oFirstLevelRule['condition'];
                                $aPassedSl = array();
                                foreach ( $oFirstLevelRule['rules'] as $iSecondLevelRuleKey => $oSecondLevelRule ) {
                                    $aPassedSl[] = uni_cpo_formula_condition_check( $oSecondLevelRule, $aFormPostData );
                                }
                                // print_r(' /start$aPassedSl: ');
                                // print_r($aPassedSl);
                                // print_r(' end of $aPassedSl/ ');
                                //
                                if( in_array(false, $aPassedSl, true) === false && $sSecondLevelCondition == 'AND' ){
                                    $aPassed[] = true;
                                } else if( in_array(true, $aPassedSl, true) === false ){
                                    $aPassed[] = false;
                                } else {
                                    if ( $sSecondLevelCondition == 'OR' ) {
                                        $aPassed[] = true;
                                    } else {
                                        $aPassed[] = false;
                                    }
                                }
                                //
                                if ( $bFirstLevelPassed === true ) {
                                    $sConditionFormula = $aRuleFormulaBlock['formula'];
                                    break;
                                }
                            } else {
                                $aPassed[] = uni_cpo_formula_condition_check( $oFirstLevelRule, $aFormPostData );
                            }

                        }

                    } else {
                        //print_r($aRuleFormulaBlock['rule']['rules'][0]);
                        $aPassed[] = uni_cpo_formula_condition_check( $aRuleFormulaBlock['rule']['rules'][0], $aFormPostData );
                    }
                    //print_r($aPassed);

                    //
                    if( in_array(false, $aPassed, true) === false && $sFirstLevelCondition == 'AND' ){
                        $bFirstLevelPassed = true;
                    } else if( in_array(true, $aPassed, true) === false ){
                        //only false;
                        $bFirstLevelPassed = false;
                    } else {
                        if ( $sFirstLevelCondition == 'OR' ) {
                            $bFirstLevelPassed = true;
                        } else {
                            $bFirstLevelPassed = false;
                        }
                    }
                    // print_r($bFirstLevelPassed);
                    //
                    if ( $bFirstLevelPassed === true ) {
                        $sConditionFormula = $aRuleFormulaBlock['formula'];
                        break;
                    }

                }

            }

        }

        //
        if ( isset($sConditionFormula) && !empty($sConditionFormula) ) {
            $sWeightFormula = $sConditionFormula;
        }

        return $sWeightFormula;

}

//  $purpose | empty or 'conditional'
function uni_cpo_process_formula_with_non_option_vars( $option_vars, $non_option_vars, $purpose = '', $is_wholesale = false ) {

    if (  $purpose === 'conditional' ) {

        if ( isset( $non_option_vars ) && ! empty( $non_option_vars ) ) {
            // we don't need array of option based variables in this case
            // let's make it empty
            $option_vars_for_conditional = array();
            foreach ( $non_option_vars as $variable ) {
                $nov_slug = UniCpo()->non_option_var_slug . $variable['slug'];
                $formula = uni_cpo_get_wholesale_formula_for_nov( $is_wholesale, $variable['formula'] );
                $nov_val = uni_cpo_process_formula_with_vars( $formula, $option_vars );
                $nov_val = uni_cpo_calculate_formula( $nov_val );
                $option_vars_for_conditional[$nov_slug] = $nov_val;
            }
        }

        return $option_vars_for_conditional;

    } else {

        if ( isset( $non_option_vars ) && ! empty( $non_option_vars ) ) {
            foreach ( $non_option_vars as $variable ) {
                $nov_slug = '{' . UniCpo()->non_option_var_slug . $variable['slug'] . '}';
                $formula = uni_cpo_get_wholesale_formula_for_nov( $is_wholesale, $variable['formula'] );
                $nov_val = uni_cpo_process_formula_with_vars( $formula, $option_vars );
                $option_vars[$nov_slug] = $nov_val;
            }
        }

        return $option_vars;

    }

}

function uni_cpo_get_wholesale_formula_for_nov( $is_wholesale, $formula_data ) {
    if ( $is_wholesale && is_array( $formula_data ) ) {
        $current_user = wp_get_current_user();

        if( 0 == $current_user->ID ) {  // non reg
            if ( isset( $formula_data['cpo_nonreg'] ) ) {
                return $formula_data['cpo_nonreg'];
            } else {
                return '';
            }
        } else { // reg
            $role = $current_user->roles ? $current_user->roles[0] : false;
            if ( isset( $formula_data[$role] ) ) {
                return $formula_data[$role];
            } else if ( isset( $formula_data['cpo_nonreg'] ) ) {
                return $formula_data['cpo_nonreg'];
            } else {
                return '';
            }
        }
    } else if ( ! $is_wholesale && ! is_array( $formula_data ) ) {
        return $formula_data;
    } else {
        return '';
    }
}

//
function uni_cpo_process_formula_with_vars( $sFormula, $aVarsArray = array() ) {

                if ( isset( $aVarsArray ) && ! empty( $aVarsArray ) ) {
                    foreach ( $aVarsArray as $Key => $Value ) {
                        if ( is_array($Value) ) {
                            if ( !empty($Value) ) {
                                foreach ( $Value as $ChildKey => $ChildValue ) {
                                    $sSearch        = "/($ChildKey)/";
                                    $sFormula   = preg_replace($sSearch, $ChildValue, $sFormula);
                                }
                            }
                        } else {
                            $sSearch                = "/($Key)/";
                            $sFormula           = preg_replace($sSearch, $Value, $sFormula);
                        }
                    }

                    $sVarsPattern = "/{([^}]*)}/";
                    $sFormula = preg_replace($sVarsPattern, '0', $sFormula);
                } else {
                    $sVarsPattern = "/{([^}]*)}/";
                    $sFormula = preg_replace($sVarsPattern, '0', $sFormula);
                }

                return $sFormula;

}

//
function uni_cpo_calculate_formula( $formula = '' ) {

            if ( ! empty( $formula ) && 'disable' !== $formula ) {
                // change the all unused variables to zero, so formula calculation will not fail
                $pattern = "/{([^}]*)}/";
                $formula = preg_replace( $pattern, '0', $formula );
                //print_r(' / formula after clearing: ' . $sMainFormula);

                // calculate
                $m = new EvalMath;
                $m->suppress_errors = true;
                $calc_price = $m->evaluate( $formula );
                $calc_price = ( ! is_infinite( $calc_price ) && ! is_nan( $calc_price ) ) ? $calc_price : 0;

                return floatval( $calc_price );
            } else {
                return 0;
            }
}

//
function uni_cpo_process_cart_discounts_formula_with_vars( $formula, $cart_discounts_vars = array() ) {

                if ( isset($cart_discounts_vars) && !empty($cart_discounts_vars) ) {
                    foreach ( $cart_discounts_vars as $key => $value ) {
                        $search            = "/($key)/";
                        $formula           = preg_replace($search, $value, $formula);
                    }
                }
                //print_r($formula);
                return $formula;

}

//
function uni_cpo_calculate_cart_discounts_formula( $formula ) {

            if ( !empty($formula) && $formula !== 'disable' ) {

                // change the all unused variables to zero, so formula calculation will not fail
                $vars_pattern = "/{([^}]*)}/";
                $formula = preg_replace($vars_pattern, '0', $formula);

                // calculate
                $m = new EvalMath;
                $m->suppress_errors = true;
                $calculated_val = $m->evaluate( $formula );
                $discount = floatval( $calculated_val );

                return floatval( $discount );
            } else {
                return 0;
            }
}

//
function uni_cpo_upload_files( $aFormFilesData, $iProductId ) {

    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $aUploadDir = wp_upload_dir();

    $overrides = array(
        'test_form' => false
    );

	$file = wp_handle_upload($aFormFilesData, $overrides);

	if ( isset($file['error']) ){
		return false;
    } else {

	    $attachment = array (
		    'post_mime_type'    => $file['type'],
            'post_title'        => esc_attr($aFormFilesData['name']),
            'post_content'      => '',
		    'guid'              => $file['url'],
		    'post_parent'       => 0,
            'post_author'       => 1
      	);

		$iAttachmentID = wp_insert_attachment( $attachment, $file['file'] );
		if ( !is_wp_error($iAttachmentID) ) {
		    wp_update_attachment_metadata($iAttachmentID, wp_generate_attachment_metadata($iAttachmentID, $file['file']));
  		}
        //
        update_post_meta( $iAttachmentID, '_uni_cpo_media_uploaded_for_product', $iProductId);

        return $iAttachmentID;

    }
}

// formula condition check
function uni_cpo_get_options_filter_data( $aProductOptions, $id_excl = '' ) {

    $aFilterArray = array();

    $is_need_to_excl = false;
    if ( !empty($id_excl) ) {
        $id_excl = intval($id_excl);
        $is_need_to_excl = true;
    }

            if ( !empty($aProductOptions) ) {
                foreach ( $aProductOptions as $aElementStructure ) {

                    if ( $is_need_to_excl && $id_excl === intval($aElementStructure['id']) ) {
                        continue;
                    }

                    $oOption = uni_cpo_get_option( $aElementStructure['id'] );

                    if ( $oOption instanceof Uni_Cpo_Option && $oOption->id && $oOption->is_calculable() ) {
                        if ( ! empty( $oOption->get_special_vars() ) ) {
                            $aGeneratedArray = $oOption->generate_filter( true );
                        } else {
                            $aGeneratedArray = $oOption->generate_filter();
                        }

                        if ( is_array($aGeneratedArray) ) {
                            foreach ( $aGeneratedArray as $aArray ) {
                                $aFilterArray[] = $aArray;
                            }
                        }
                    }

                    // children options  TODO remove in the next version
                    if ( !empty($aElementStructure['children']) ) {
                        foreach ( $aElementStructure['children'] as $aChildElementStructure ) {
                            $oChildOption = uni_cpo_get_option( $aChildElementStructure['id'], $aChildElementStructure['itemtype'] );

                            if ( $oChildOption instanceof Uni_Cpo_Option && $oChildOption->id && $oChildOption->is_calculable() ) {
                                if ( ! empty( $oChildOption->get_special_vars() ) ) {
                                    $aGeneratedArray = $oChildOption->generate_filter( true );
                                } else {
                                    $aGeneratedArray = $oChildOption->generate_filter();
                                }
                                if ( is_array($aGeneratedArray) ) {
                                    foreach ( $aGeneratedArray as $aArray ) {
                                        $aFilterArray[] = $aArray;
                                    }
                                }
                            }

                        }
                    }
                }
            }

    return $aFilterArray;

}

// formula condition check
function uni_cpo_formula_condition_check( $oRule, $aFormPostData ) {

    $sFieldName = '';
    $bPassed = false;

    // it can be an option based var or non option based var or special var
    // let's test it
    if ( isset($aFormPostData[UniCpo()->var_slug . $oRule['id']]) ) {
        $sFieldName = UniCpo()->var_slug . $oRule['id'];
    } else if ( isset($aFormPostData[UniCpo()->non_option_var_slug . $oRule['id']]) ) {
        $sFieldName = UniCpo()->non_option_var_slug . $oRule['id'];
    }
    //print_r($sFieldName);
    if ( !empty($sFieldName) ) {

        $sRuleValue = $oRule['value'];
        //print_r($sFieldName);
        //print_r($aFormPostData[$sFieldName]);

        switch ( $oRule['operator'] ) {

            case 'less':
                if ( isset($aFormPostData[$sFieldName]) && $aFormPostData[$sFieldName] < $sRuleValue ) {
                    $bPassed = true;
                }
            break;

            case 'less_or_equal':
                if ( isset($aFormPostData[$sFieldName]) && $aFormPostData[$sFieldName] <= $sRuleValue ) {
                    $bPassed = true;
                }
            break;

            case 'equal':
                if ( isset($aFormPostData[$sFieldName]) && !is_array($aFormPostData[$sFieldName]) && $aFormPostData[$sFieldName] === $sRuleValue ) {
                    $bPassed = true;
                } else if ( isset($aFormPostData[$sFieldName]) && is_array($aFormPostData[$sFieldName]) ) {
                    foreach ( $aFormPostData[$sFieldName] as $sValue ) {
                        if ( $sValue === $sRuleValue ) {
                            $bPassed = true;
                            break;
                        }
                    }
                }
            break;

            case 'not_equal':
                if ( isset($aFormPostData[$sFieldName]) && !is_array($aFormPostData[$sFieldName]) && $aFormPostData[$sFieldName] !== $sRuleValue ) {
                    $bPassed = true;
                } else if ( isset($aFormPostData[$sFieldName]) && is_array($aFormPostData[$sFieldName]) ) {
                    foreach ( $aFormPostData[$sFieldName] as $sValue ) {
                        if ( $sValue !== $sRuleValue ) {
                            $bPassed = true;
                            break;
                        }
                    }
                }
            break;

            case 'greater_or_equal':
                if ( isset($aFormPostData[$sFieldName]) && $aFormPostData[$sFieldName] >= $sRuleValue ) {
                    $bPassed = true;
                }
            break;

            case 'greater':
                if ( isset($aFormPostData[$sFieldName]) && $aFormPostData[$sFieldName] > $sRuleValue ) {
                    $bPassed = true;
                }
            break;

            case 'is_empty':
                if ( !isset($aFormPostData[$sFieldName]) || empty($aFormPostData[$sFieldName]) ) {
                    $bPassed = true;
                }
            break;

            case 'is_not_empty':
                if ( !empty($aFormPostData[$sFieldName]) ) {
                    $bPassed = true;
                }
            break;

        }

    } else {

        $sRuleValue = $oRule['value'];

        switch ( $oRule['operator'] ) {

            case 'not_equal':
                if ( !isset($aFormPostData[$sFieldName]) || empty($aFormPostData[$sFieldName]) ) {
                    $bPassed = true;
                }
            break;

            case 'is_empty':
                if ( !isset($aFormPostData[$sFieldName]) || empty($aFormPostData[$sFieldName]) ) {
                    $bPassed = true;
                }
            break;

        }

    }

    return $bPassed;
}

// option condition js statement builder
function uni_cpo_option_condition_js_statement_builder( $aRulesArray ) {

    $sStatement = '';
    foreach ( $aRulesArray as $sConditionBlockKey => $condition_block ) {
                            $sFirstLevelCondition = $condition_block['condition'];
                            if ( $sFirstLevelCondition == 'AND' ) {
                                $sFirstLevelCondition = '&&';
                            } else if ( $sFirstLevelCondition == 'OR' ) {
                                $sFirstLevelCondition = '||';
                            }
                            $iCountRules = count( $condition_block['rules'] );
                            if ( $iCountRules > 1 ) {
                                foreach ( $condition_block['rules'] as $key => $first_level_rule ) {
                                    if ( isset( $first_level_rule['rules'] ) ) {
                                        $sSecondLevelCondition = $first_level_rule['condition'];
                                        if ( $sSecondLevelCondition == 'AND' ) {
                                            $sSecondLevelCondition = '&&';
                                        } else if ( $sSecondLevelCondition == 'OR' ) {
                                            $sSecondLevelCondition = '||';
                                        }
                                        $sSlStatement = '';
                                        foreach ( $first_level_rule['rules'] as $sSecondLevelRuleKey => $second_level_rule ) {
                                            $sSlStatement .= uni_cpo_option_condition_js_builder( $second_level_rule ).' '.$sSecondLevelCondition.' ';
                                        }
                                        $sSlStatement = rtrim($sSlStatement, ' '.$sSecondLevelCondition);
                                        $sStatement .= '('.$sSlStatement.') '.$sFirstLevelCondition.' ';
                                    } else {
                                        $sStatement .= uni_cpo_option_condition_js_builder( $first_level_rule ).' '.$sFirstLevelCondition.' ';
                                    }
                                }
                            } else {
                                $sStatement .= uni_cpo_option_condition_js_builder( $condition_block['rules'][0] );
                            }

    }
    $sStatement = rtrim($sStatement, ' ' . $sFirstLevelCondition);

    return $sStatement;
}

// option condition js builder
function uni_cpo_option_condition_js_builder( $rule ) {

        $option_post    = uni_cpo_get_post_by_slug( $rule['id'] );
        $option         = uni_cpo_get_option( $option_post );
        // detect special vars: '_count', '_duration', 'end'
        if ( false === $option ) {
            $field_type     = 'special_var';
            $name_and_prop  = uni_cpo_detect_special_var_name( $rule['id'] );
            if ( ! empty($name_and_prop) ) {
                $sFieldName     = $name_and_prop['name'];
                $js_prop        = $name_and_prop['prop'];
            } else {
                return;
            }
        } else {
            $field_type     = $option->get_type();
            $sFieldName     = $rule['id'];
        }
        $sRuleValue     = $rule['value'];
        $sStatement     = '';

        switch ( $rule['operator'] ) {
            case 'less':
                if ( in_array( $field_type, array('special_var') ) ) {
                    $sStatement = "_{$sFieldName}_val.{$js_prop} < {$sRuleValue}";
                } else {
                    $sStatement = "_{$sFieldName}_val.value < {$sRuleValue}";
                }
            break;
            case 'less_or_equal':
                if ( in_array( $field_type, array('special_var') ) ) {
                    $sStatement = "_{$sFieldName}_val.{$js_prop} <= {$sRuleValue}";
                } else {
                    $sStatement = "_{$sFieldName}_val.value <= {$sRuleValue}";
                }
            break;
            case 'equal':
                if ( in_array( $field_type, array('radio', 'select', 'date_picker') ) ) {
                    $sStatement = "_{$sFieldName}_val.value === '{$sRuleValue}'";
                } else if ( in_array( $field_type, array('checkboxes') ) ) {
                    $sStatement = "$.inArray( '$sRuleValue', _{$sFieldName}_val.values ) != -1";
                } else if ( in_array( $field_type, array('special_var') ) ) {
                    $sStatement = "parseInt( _{$sFieldName}_val.{$js_prop} ) === {$sRuleValue}";
                } else {
                    if ( false === $option ) {
                        if ( 'integer' === $option->get_input_type() ) {
                            $sRuleValue = intval($sRuleValue);
                        } else if ( 'double' === $option->get_input_type() ) {
                            $sRuleValue = floatval($sRuleValue);
                        }
                    }
                    if ( is_int( $sRuleValue ) ) {
                        $sStatement = "parseInt( _{$sFieldName}_val.value, 10 ) === parseInt({$sRuleValue}, 10)";
                    } else if ( is_float($sRuleValue) ) {
                        $sStatement = "parseFloat( _{$sFieldName}_val.value ) === parseFloat({$sRuleValue})";
                    } else {
                        $sStatement = "_{$sFieldName}_val.value === '{$sRuleValue}'";
                    }
                }
            break;
            case 'not_equal':
                if ( in_array( $field_type, array('radio', 'select', 'date_picker') ) ) {
                    $sStatement = "_{$sFieldName}_val.value !== '{$sRuleValue}'";
                } else if ( in_array( $field_type, array('checkboxes') ) ) {
                    $sStatement = "$.inArray( '$sRuleValue', _{$sFieldName}_val.values ) == -1";
                } else if ( in_array( $field_type, array('special_var') ) ) {
                    $sStatement = "parseInt( _{$sFieldName}_val.{$js_prop} ) !== {$sRuleValue}";
                } else {
                    if ( false === $option ) {
                        if ( 'integer' === $option->get_input_type() ) {
                            $sRuleValue = intval($sRuleValue);
                        } else if ( 'double' === $option->get_input_type() ) {
                            $sRuleValue = floatval($sRuleValue);
                        }
                    }
                    if ( is_int( $sRuleValue ) ) {
                        $sStatement = "parseInt( _{$sFieldName}_val.value, 10 ) !== parseInt({$sRuleValue}, 10)";
                    } else if ( is_float($sRuleValue) ) {
                        $sStatement = "parseFloat( _{$sFieldName}_val.value ) !== parseFloat({$sRuleValue})";
                    } else {
                        $sStatement = "_{$sFieldName}_val.value !== '{$sRuleValue}'";
                    }
                }
            break;
            case 'greater_or_equal':
                if ( in_array( $field_type, array('special_var') ) ) {
                    $sStatement = "_{$sFieldName}_val.{$js_prop} >= {$sRuleValue}";
                } else {
                    $sStatement = "_{$sFieldName}_val.value >= {$sRuleValue}";
                }
            break;
            case 'greater':
                if ( in_array( $field_type, array('special_var') ) ) {
                    $sStatement = "_{$sFieldName}_val.{$js_prop} > {$sRuleValue}";
                } else {
                    $sStatement = "_{$sFieldName}_val.value > {$sRuleValue}";
                }
            break;
            case 'is_empty':
                if ( in_array( $field_type, array('checkboxes') ) ) {
                    $sStatement = "_{$sFieldName}_val.values.length === 0";
                } else {
                    $sStatement = "!_{$sFieldName}_val.value";
                }
            break;
            case 'is_not_empty':
                if ( in_array( $field_type, array('checkboxes') ) ) {
                    $sStatement = "_{$sFieldName}_val.values.length > 0";
                } else {
                    $sStatement = "( _{$sFieldName}_val.value !== 'undefined' && _{$sFieldName}_val.value !== '' )";
                }
            break;
        }

    return $sStatement;
}

//
function uni_cpo_detect_special_var_name( $rule_var_name ) {
    // TODO get all the possible special vars from all the options
    $special_var_names  = apply_filters( 'uni_cpo_add_special_var_names', 
        array('count', 'count_spaces', 'duration', 'distance', 'end') );
    $name_and_prop      = array();
    foreach ( $special_var_names as $name ) {
        if ( false !== strpos( $rule_var_name, $name ) ) {
            $name_and_prop['prop'] = $name;
            $name_and_prop['name'] = str_replace( '_'.$name, '', $rule_var_name );
            $option_post    = uni_cpo_get_post_by_slug( $name_and_prop['name'] );
            $option         = uni_cpo_get_option( $option_post );
            if ( false !== $option ) {
                $name_and_prop['type'] = $option->get_type();
                return $name_and_prop;
            }
        }
    }
    return $name_and_prop;
}

//
function uni_cpo_allowed_html_for_option_titles() {
    $aAllowedHtml = array(
        'a' => array(
            'href' => array(),
            'title' => array(),
            'target' => array()
        ),
        'em' => array(),
        'strong' => array(),
        'code' => array(),
        'sup' => array(),
        'sub' => array()
    );
    return $aAllowedHtml;
}

//
function uni_cpo_allowed_html_for_tooltips() {
    $aAllowedHtml = array(
        'a' => array(
            'href' => array(),
            'title' => array(),
            'target' => array(),
            'class' => array()
        ),
        'p' => array(
            'class' => array()
        ),
        'em' => array(),
        'strong' => array(),
        'code' => array(),
        'sup' => array(),
        'sub' => array(),
        'img' => array(
            'src' => array(),
            'title' => array(),
            'alt' => array(),
            'width' => array(),
            'height' => array(),
            'class' => array()
        ),
        'span' => array(
            'class' => array()
        ),
        'br' => array()
    );
    return $aAllowedHtml;
}

//
function uni_cpo_allowed_html_for_js_tmpl() {
    $aAllowedHtml = array(
        'a' => array(
            'href' => array(),
            'title' => array(),
            'target' => array(),
            'class' => array()
        ),
        'p' => array(
            'class' => array()
        ),
        'em' => array(),
        'strong' => array(),
        'code' => array(),
        'sup' => array(),
        'sub' => array(),
        'img' => array(
            'src' => array(),
            'title' => array(),
            'alt' => array(),
            'width' => array(),
            'height' => array(),
            'class' => array()
        ),
        'span' => array(
            'class' => array()
        ),
        'br' => array(),
        '#' => array()
    );
    return $aAllowedHtml;
}


//////////////////////////////////////////////////////////////////////////////////////
// WC related functions and hooks
//////////////////////////////////////////////////////////////////////////////////////
/**
 * Format the price with a currency symbol. Adapted from wc_price()
 *
 * @param $price
 * @param array $args
 * @return string
 */
function uni_cpo_price( $price, $args = array() ) {

    extract( apply_filters( 'wc_price_args', wp_parse_args( $args, array(
        'ex_tax_label'       => false,
        'currency'           => '',
        'decimal_separator'  => wc_get_price_decimal_separator(),
        'thousand_separator' => wc_get_price_thousand_separator(),
        'decimals'           => wc_get_price_decimals(),
        'price_format'       => get_woocommerce_price_format(),
    ) ) ) );

    $negative        = $price < 0;
    $price           = apply_filters( 'raw_uni_cpo_price', floatval( $negative ? $price * -1 : $price ) );
    $price           = apply_filters( 'formatted_uni_cpo_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

    if ( apply_filters( 'uni_cpo__price_trim_zeros', false ) && $decimals > 0 ) {
        $price = wc_trim_zeros( $price );
    }

    $formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format,  get_woocommerce_currency_symbol( $currency ), $price );

    if ( $ex_tax_label && wc_tax_enabled() ) {
        $formatted_price .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
    }

    return apply_filters( 'uni_cpo_price', $formatted_price, $price, $args );

}
function uni_cpo_get_formatted_price( $price, $args = array() ) {

    _deprecated_function( __FUNCTION__, '3.1.3', 'uni_cpo_price()' );

    return uni_cpo_price( $price, $args );

}


/**
 * Raw price (float)
 *
 * @param $price
 * @return float
 */
function uni_cpo_price_raw( $price ) {

    $decimal_separator  = wc_get_price_decimal_separator();
    $thousand_separator = wc_get_price_thousand_separator();
    $decimals           = wc_get_price_decimals();

    //$negative           = $price < 0;
    //$price              = apply_filters( 'raw_uni_cpo_price', floatval( $negative ? $price * -1 : $price ) );
    //$price              = apply_filters( 'formatted_uni_cpo_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

    return (float) $price;
}
function uni_cpo_get_formatted_float( $price ) {

    _deprecated_function( __FUNCTION__, '3.1.3', 'uni_cpo_price_raw()' );

    return uni_cpo_price_raw( $price );
}

//
function uni_cpo_get_registered_image_sizes( $size = '' ) {

        global $_wp_additional_image_sizes;

        $sizes = array();
        $get_intermediate_image_sizes = get_intermediate_image_sizes();

        // Create the full array with sizes and crop info
        foreach( $get_intermediate_image_sizes as $_size ) {

                if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

                        $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
                        $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
                        $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

                } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

                        $sizes[ $_size ] = array(
                                'width'     => $_wp_additional_image_sizes[ $_size ]['width'],
                                'height'    => $_wp_additional_image_sizes[ $_size ]['height'],
                                'crop'      =>  $_wp_additional_image_sizes[ $_size ]['crop']
                        );

                }

        }

        // Get only 1 size if found
        if ( $size ) {

                if( isset( $sizes[ $size ] ) ) {
                        return $sizes[ $size ];
                } else {
                        return false;
                }

        }

        return $sizes;
}

// customers try to add a product to the cart from an archive page? let's check if it is possible to do!
add_filter( 'woocommerce_loop_add_to_cart_link', 'uni_cpo_add_to_cart_button', 10, 2 );
function uni_cpo_add_to_cart_button( $link, $product ){

    $iProductId         = intval( $product->get_id() );
    $aProductCustom     = get_post_custom( $iProductId );
    $product_type       = $product->get_type();

    if( isset($aProductCustom['_uni_cpo_display_options_enable'][0]) && $aProductCustom['_uni_cpo_display_options_enable'][0] == true ) {
        $link = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button product_type_%s">%s</a>',
            esc_url( get_permalink( $iProductId ) ),
            esc_attr( $iProductId ),
            esc_attr( $product->get_sku() ),
            esc_attr( isset( $quantity ) ? $quantity : 1 ),
            esc_attr( $product_type ),
            esc_html( __( 'Select options', 'woocommerce' ) )
        );
    }
    return $link;
}

//
add_action( 'woocommerce_before_add_to_cart_button', 'uni_cpo_calculate_button_output', 15 );
function uni_cpo_calculate_button_output(){
    global $post;
    $aProductCustom         = get_post_custom( $post->ID );

    if ( isset($aProductCustom['_uni_cpo_price_calculation_btn_enable'][0]) && $aProductCustom['_uni_cpo_price_calculation_btn_enable'][0] == true ) {

        $sBtnText = apply_filters( 'cpo_calc_btn_text_filter', '<i class="fa fa-calculator" aria-hidden="true"></i>'.esc_html__('Calculate', 'uni-cpo'), $post->ID );

        echo '<button type="button" id="js-uni-cpo-calculate-btn" class="uni-cpo-calculate-btn button alt">'.$sBtnText.'</button>';

    }
}

//
add_filter( 'woocommerce_get_price_html', 'uni_cpo_display_price_with_preffix', 10, 2 );
function uni_cpo_display_price_with_preffix( $price, $product ){

    $iProductId         = intval($product->get_id());
    $aProductCustom     = get_post_custom( $iProductId );
    $iProductPostId     = 0;
    global $wp_query;

    if ( isset($wp_query->queried_object->post_content) && has_shortcode($wp_query->queried_object->post_content, 'product_page') ) {
            if ( has_shortcode($wp_query->queried_object->post_content, 'product_page') ) {
                $pattern = '\[(\[?)(product_page)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
                if (   preg_match_all( '/'. $pattern .'/s', $wp_query->queried_object->post_content, $matches )
                    && array_key_exists( 2, $matches )
                    && in_array( 'product_page', $matches[2] ) )
                {
                    foreach ( $matches[2] as $Key => $Value ) {
                        if ( $Value === 'product_page' ) {
                            $aParsed = shortcode_parse_atts( $matches[3][$Key] );
                            if ( is_array($aParsed) ) {
                                foreach ( $aParsed as $sAttrName => $sAttrValue ) {
                                    if ( $sAttrName === 'id' ) {
                                        $iProductPostId = intval($sAttrValue);
                                        break 2;
                                    }
                                }
                            }
                        }
                    }
                }
            }
    }

    if( isset($aProductCustom['_uni_cpo_price_calculation_enable'][0]) && $aProductCustom['_uni_cpo_price_calculation_enable'][0] == true
        && !empty($aProductCustom['_uni_cpo_min_price'][0])
        && (
            ( is_single() && $product->get_id() !== $wp_query->queried_object_id )
            || ( is_page() && $product->get_id() !== $iProductPostId )
            || is_tax() || is_archive()
            || ( is_single() && !is_singular('product') && isset($wp_query->queried_object->post_content) && !has_shortcode($wp_query->queried_object->post_content, 'product_page') )
        )
    ) {

        $product_price = wc_get_price_to_display( $product, array('price' => $aProductCustom['_uni_cpo_min_price'][0]) );
        $product_price = apply_filters( 'uni_cpo_price_archive_displaying', $product_price, $product );
        $sDisplayPrice = uni_cpo_price( $product_price );
        $sDisplayPrice = sprintf( esc_html__('from %s', 'uni-cpo'), $sDisplayPrice);
        if ( $product->is_taxable() ) {
            $sPrice = $sDisplayPrice . $product->get_price_suffix( $product_price );
        } else {
            $sPrice = $sDisplayPrice;
        }

        return $sPrice;
    } else {
        return $price;
    }
}

// to be used in '<meta itemprop="price" value="...">'
function uni_cpo_get_price_for_meta() {

    global $product;
    $iProductId         = intval( $product->get_id() );
    $aProductCustom     = get_post_custom( $iProductId );

    if( isset($aProductCustom['_uni_cpo_price_calculation_enable'][0]) && $aProductCustom['_uni_cpo_price_calculation_enable'][0] == true
        && !empty($aProductCustom['_uni_cpo_min_price'][0])
    ) {
        $product_price = wc_get_price_to_display( $product, array('price' => $aProductCustom['_uni_cpo_min_price'][0]) );
        $product_price = apply_filters( 'uni_cpo_price_for_meta_tag_displaying', $product_price, $product );
        return $product_price;
    } else {
        return wc_get_price_to_display( $product );
    }

}

// to be used instead of standard '<meta itemprop="price">'
function uni_cpo_display_price_custom_meta() {

    global $product;
    $iProductId         = intval( $product->get_id() );
    $aProductCustom     = get_post_custom( $iProductId );

    if( isset($aProductCustom['_uni_cpo_price_calculation_enable'][0]) && $aProductCustom['_uni_cpo_price_calculation_enable'][0] == true
        && !empty($aProductCustom['_uni_cpo_min_price'][0])
    ) {
        $product_price = wc_get_price_to_display( $product, array('price' => $aProductCustom['_uni_cpo_min_price'][0]) );
        $product_price = apply_filters( 'uni_cpo_price_for_meta_tag_displaying', $product_price, $product );
        echo '<meta itemprop="lowPrice" content="' . esc_attr( $product_price ) . '" />';
    } else {
        $product_price = wc_get_price_to_display( $product );
        echo '<meta itemprop="price" content="' . esc_attr( $product_price ) . '" />';
    }

}

//
function uni_cpo_get_display_price_reversed( $oProduct, $fPrice ) {

		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
        $price_incl = wc_get_price_including_tax( $oProduct, array( 'qty' => 1, 'price' => $fPrice ) );
        $price_excl = wc_get_price_excluding_tax( $oProduct, array( 'qty' => 1, 'price' => $fPrice ) );
        $display_price = $tax_display_mode == 'incl' ? $price_excl : $price_incl;
		return $display_price;
}

// displays a discounted price with crossed an old price
function uni_cpo_get_cart_price_crossed( $fCalcPrice, $fDiscountedPrice, $sDiscountType = '' ) {

    if ( $fDiscountedPrice < $fCalcPrice ) {
	    $sOutput = '<span class="uni-item-price amount">' . wc_price( $fCalcPrice ) . '</span><span class="uni-item-discounted-price amount"> ' . wc_price( $fDiscountedPrice ) . '</span>';

        switch ( $sDiscountType ) {
            case 'percentage':
                $fDiscountPercentage = 100 - ($fDiscountedPrice/$fCalcPrice)*100;
                $fDiscountPercentage = round($fDiscountPercentage, 0);
                $sOutput .= '<small class="uni-item-discount amount"> '.sprintf(esc_html__('You save %d%s!', 'uni-cpo'), $fDiscountPercentage, '%').'</small>';
            break;

            case 'amount':
                $fDiscountValue = $fCalcPrice - $fDiscountedPrice;
                $sOutput .= '<small class="uni-item-discount amount"> '.sprintf(esc_html__('You save %s!', 'uni-cpo'), wc_price( $fDiscountValue )).'</small>';
            break;

            case 'price':
                $fDiscountValue = $fCalcPrice - $fDiscountedPrice;
                $sOutput .= '<small class="uni-item-discount amount"> '.esc_html__('Promo price!', 'uni-cpo').'</small>';
            break;

            default:
                // empty
            break;
        }
	} else {
	    $sOutput = wc_price( $fCalcPrice );
	}

    return $sOutput;

}

// displays a new and discounted price in the cart
function uni_cpo_change_cart_item_price( $fProductPrice, $cart_item, $cart_item_key ) {

    $iProductId             = $cart_item['product_id'];
    $aProductCustom         = get_post_custom( $iProductId );

    if( ( isset($aProductCustom['_uni_cpo_price_calculation_enable'][0]) && $aProductCustom['_uni_cpo_price_calculation_enable'][0] == true ) ||
        ( isset($aProductCustom['_uni_cpo_cart_discounts_enable'][0]) && $aProductCustom['_uni_cpo_cart_discounts_enable'][0] == true ) ) {

        $oProduct               = wc_get_product( $iProductId );
        $fCalcPrice             = wc_get_price_to_display( $oProduct, array('qty' => 1, 'price' => $cart_item['_uni_cpo_data']['uni_cpo_price']) );

        // cart discounts rules
        $aDiscountsRulesArray = '';
        if ( !empty($aProductCustom['_uni_cpo_cart_discount_quantity'][0]) ) {
            $aDiscountsRulesArray = maybe_unserialize($aProductCustom['_uni_cpo_cart_discount_quantity'][0]);
        }

        // just gets a type of discount if the discount rule is appliable
        // calculations have been already made in 'uni_cpo_add_cart_item'
        if ( isset($aProductCustom['_uni_cpo_cart_discounts_enable'][0]) && $aProductCustom['_uni_cpo_cart_discounts_enable'][0] == true
            && !empty($aDiscountsRulesArray) && is_array($aDiscountsRulesArray) ) {

                $sTypeOfDiscount = '';

                foreach ( $aDiscountsRulesArray as $sKey => $aRuleArray ) {

                    if ( $cart_item['quantity'] >= $aRuleArray['min'] && $cart_item['quantity'] <= $aRuleArray['max'] ) {

                        if ( $aRuleArray['type'] == 'percentage' ) {
                            // stores type of discount
                            $sTypeOfDiscount = $aRuleArray['type'];
                        } else if ( $aRuleArray['type'] == 'amount' ) {
                            // stores type of discount
                            $sTypeOfDiscount = $aRuleArray['type'];
                        } else if ( $aRuleArray['type'] == 'price' ) {
                            // stores type of discount
                            $sTypeOfDiscount = $aRuleArray['type'];
                        }
                        break;
                    }
                }

                $fDiscountedPriceFloat = floatval( str_replace( ',', '', $cart_item['data']->get_price() ) );
                $fDiscountedPriceFloat = apply_filters( 'cpo_get_cart_price_calculated_discounted_raw', $fDiscountedPriceFloat, $aProductCustom );
                $fDiscountedPrice = wc_get_price_to_display( $oProduct, array('qty' => 1, 'price' => $fDiscountedPriceFloat) );
                $cpo_new_price_display = uni_cpo_get_cart_price_crossed( $fCalcPrice, $fDiscountedPrice, $sTypeOfDiscount );
                return apply_filters( 'cpo_get_cart_price_calculated_discounted_display', $cpo_new_price_display, $aProductCustom );

        } else {
            $cpo_price = apply_filters( 'cpo_get_cart_price_calculated_raw', $fCalcPrice, $aProductCustom );
            $cpo_price = wc_price( $cpo_price );
            return $cpo_price;
        }

    } else {
        return $fProductPrice;
    }

}
// a hack for Subscriptio plugin compatibility
if ( class_exists('Subscriptio') ) {
    add_filter('subscriptio_get_recurring_price_in_cart', 'uni_cpo_subscriptio_recurring_price_in_cart', 10, 2);
    add_filter('woocommerce_cart_item_price', 'uni_cpo_change_cart_item_price', 100, 3);
    //
    function uni_cpo_subscriptio_recurring_price_in_cart( $price, $cart_item ){
        $iProductId     = $cart_item['product_id'];
        $aProductCustom = get_post_custom( $iProductId );
        if ( Subscriptio_Subscription_Product::is_subscription( $iProductId ) &&
            ( isset($aProductCustom['_uni_cpo_price_calculation_enable'][0]) && $aProductCustom['_uni_cpo_price_calculation_enable'][0] == true ) ) {
            $price = $cart_item['_uni_cpo_data']['uni_cpo_price']*$cart_item['quantity'];
            return $price;
        } else {
            return $price;
        }
    }
} else {
    add_filter('woocommerce_cart_item_price', 'uni_cpo_change_cart_item_price', 10, 3);
}


//
add_action( 'woocommerce_before_calculate_totals', 'uni_cpo_before_calculate_totals', 10, 1 );
function uni_cpo_before_calculate_totals( $object ) {
    if ( method_exists( $object, 'get_cart' ) ) {
        foreach ( $object->get_cart() as $cart_item_key => $values ) {
            $product            = $values['data'];
            $product_id         = $values['product_id'];
            $product_meta       = get_post_custom( $product_id );

            // checks min qty
            if ( isset( $product_meta['_uni_cpo_min_qty'][0] ) && ! empty( $product_meta['_uni_cpo_min_qty'][0] ) &&
                $values['quantity'] < intval( $product_meta['_uni_cpo_min_qty'][0] )
            ) {
                $qty = intval( $product_meta['_uni_cpo_min_qty'][0] );
                $object->set_quantity( $cart_item_key, $qty );
            }

            // checks max qty
            if ( isset( $product_meta['_uni_cpo_max_qty'][0] ) && ! empty( $product_meta['_uni_cpo_max_qty'][0] ) &&
                $values['quantity'] > intval( $product_meta['_uni_cpo_max_qty'][0] )
            ) {
                $qty = intval( $product_meta['_uni_cpo_max_qty'][0] );
                $object->set_quantity( $cart_item_key, $qty );
            }

            if ( $product->is_type( 'simple' ) && ! empty( $object->coupons ) ) {
                foreach ( $object->coupons as $code => $coupon ) {
                    if ( $coupon->is_valid() && ( $coupon->is_valid_for_product( $product, $values ) || $coupon->is_valid_for_cart() ) ) {
                        //print_r($values);
                        if ( isset( $values['_uni_cpo_data']['uni_cpo_price'] ) ) {
                            $product->set_price($values['_uni_cpo_data']['uni_cpo_price']);
                        }
                    }
                }
            }

        }
    }
}

// associate with order's meta
add_filter( 'woocommerce_add_cart_item_data', 'uni_cpo_add_cart_item_data', 10, 2 );
add_filter( 'woocommerce_get_cart_item_from_session', 'uni_cpo_get_cart_item_from_session', 10, 3 );
add_filter( 'woocommerce_get_item_data', 'uni_cpo_get_item_data', 10, 2 ); // get item data to display in cart and checkout page
add_filter( 'woocommerce_add_cart_item', 'uni_cpo_add_cart_item', 10, 1 );
add_action( 'woocommerce_checkout_create_order_line_item', 'uni_cpo_checkout_create_order_line_item', 10, 4 ); // add meta data for each order item

// adds custom option data to the cart
function uni_cpo_add_cart_item_data( $cart_item_meta, $product_id ) {

    $product_meta = get_post_custom( $product_id );

    // get data from traditionally submitted form
    $posted_data  = $_POST;

    // saves an info whether calc option is enabled or not
    $cart_item_meta['_uni_cpo_calc_option']         = ( isset( $product_meta['_uni_cpo_price_calculation_enable'][0] ) && true === (bool)$product_meta['_uni_cpo_price_calculation_enable'][0] ) ? true : false;
    // custom cart item id
    if ( isset( $posted_data['uni_cpo_cart_item_id'] ) ) {
        $cart_item_meta['_uni_cpo_cart_item_id']    = ( ! empty( $posted_data['uni_cpo_cart_item_id'] ) ) ? $posted_data['uni_cpo_cart_item_id'] : '';
        unset( $posted_data['uni_cpo_cart_item_id'] );
    }

    // it is isset when ordering again has been initiated
    if ( ! isset( $cart_item_meta['_uni_cpo_data'] ) ) {

        // array with the data about custom options
        $cart_item_meta['_uni_cpo_data'] = array();

        // saves an information about chosen options and their values in cart meta
        $options_set_id     = ( !empty( $product_meta['_uni_cpo_options_set'][0] ) ) ? intval( $product_meta['_uni_cpo_options_set'][0] ) : 0;
        $product_options    = get_post_meta( $options_set_id, '_uni_cpo_options_structure', true );
        $formatted_array    = array();

        if ( !empty( $product_options ) ) {
            foreach ( $product_options as $structure_item ) {
                $option = uni_cpo_get_option( $structure_item['id'] );

                if ( $option instanceof Uni_Cpo_Option && $option->get_id() && $option->is_calculable() ) {
                    $option_name = trim($option->post->post_title, '{}');
                    if ( isset( $posted_data[$option_name] ) && ! empty( $posted_data[$option_name] ) ) {
                        $calc_result = $option->calculation( $posted_data, 'cart' );
                        if ( is_array( $calc_result ) ) {
                            foreach ( $calc_result as $name => $value ) {
                                $formatted_array[$name] = $value;
                            }
                        } else {
                            $formatted_array[$option_name] = $calc_result;
                        }
                    }
                }

            }
        }
        //print_r( $formatted_array );
        $cart_item_meta['_uni_cpo_data'] = $formatted_array;

    }

    // handles submitted files  TODO
    $posted_files = $_FILES;
    if ( $posted_files ) {
        $attachments = array();
        foreach( $posted_files as $file ) {
            $attach_id = uni_cpo_upload_files( $file, $product_id );
            if ( $attach_id !== false ) {
                $attachments[] = $attach_id;
            }
        }
        if ( ! empty( $attachments ) ) {
            $cart_item_meta['_uni_cpo_item_attachments'] = $attachments;
        }
    }

    // regular or sale price?
    if ( empty( $product_meta['_sale_price'][0] ) ) {
        // picks regular price
        $product_price = $product_meta['_regular_price'][0];
    } else {
        // picks sale price
        $product_price = $product_meta['_sale_price'][0];
    }

    if ( $cart_item_meta['_uni_cpo_calc_option'] == true ) {
        $price = uni_cpo_calculate_price_in_cart( $cart_item_meta, $product_id, false );
    } else {
        $price = $product_price;
    }
    //$price = uni_cpo_price_raw( $price );
    $cart_item_meta['_uni_cpo_data']['uni_cpo_price']   = $price;

    return $cart_item_meta;

}

//
function uni_cpo_get_cart_item_from_session( $session_data, $values, $key ) {

    $session_data['_uni_cpo_calc_option']       = ( isset($values['_uni_cpo_calc_option']) ) ? $values['_uni_cpo_calc_option'] : false;
    $session_data['_uni_cpo_cart_item_id']      = ( isset($values['_uni_cpo_cart_item_id']) ) ? $values['_uni_cpo_cart_item_id'] : '';
    $session_data['_uni_cpo_data']              = $values['_uni_cpo_data'];
    $session_data['_uni_cpo_item_attachments']  = ( isset($values['_uni_cpo_item_attachments']) ) ? $values['_uni_cpo_item_attachments'] : array();

    if ( isset( $session_data['_uni_cpo_data'] ) ) {
        return uni_cpo_add_cart_item( $session_data );
    } else {
        return $session_data;
    }
}

//
function uni_cpo_get_item_data( $item_data, $cart_item ) {

    if ( ! empty( $cart_item['_uni_cpo_data'] ) ) {

        // saves an information about chosen options and their values in cart meta
        $formatted_array    = $cart_item['_uni_cpo_data'];
        unset( $formatted_array['uni_cpo_price'] );

        foreach ( $formatted_array as $name => $value ) {
            if ( false !== strpos( $name, UniCpo()->var_slug ) ) {
                $slug = str_replace( UniCpo()->var_slug, '', $name );
                $post = uni_cpo_get_post_by_slug( $slug );
                if ( $post ) {
                    $option         = uni_cpo_get_option( $post );
                    $option_val     = $option->calculation( $formatted_array, 'order' );
                    $item_data[]    = array('name' => $option->get_meta_label(), 'value' => $option_val);
                }
            }
        }

    }

    // uploaded files
    if ( ! empty( $cart_item['_uni_cpo_item_attachments'] ) ) {
        $attachments_names = array();
        foreach ( $cart_item['_uni_cpo_item_attachments'] as $attach_id ) {
            $attachments_names[] = get_the_title( $attach_id );
        }
        $attchments_count   = count( $attachments_names );
        $attachments_names = implode(', ', $attachments_names);
        if ( $attchments_count > 1 ) {
            $item_data[]    = array('name' => esc_html__('Uploaded files'), 'value' => $attachments_names);
        } else {
            $item_data[]    = array('name' => esc_html__('Uploaded file'), 'value' => $attachments_names);
        }
    }

    return $item_data;
}

function uni_cpo_add_cart_item( $cart_item ) {

        $product_id         = $cart_item['product_id'];
        $product_meta       = get_post_custom( $product_id );
        $is_calc_enabled    = $cart_item['_uni_cpo_calc_option'];

        // price calc
        if ( $is_calc_enabled ) {
            if ( isset( $cart_item['_uni_cpo_data'] ) ) {
                $fPrice = uni_cpo_calculate_price_in_cart( $cart_item, $product_id, true );
                //$fPrice = uni_cpo_price_raw( $fPrice );
                $cart_item['_uni_cpo_data']['uni_cpo_price'] = $fPrice;
                // set cart item price
                $cart_item['data']->set_price($cart_item['_uni_cpo_data']['uni_cpo_price']);
            }
        }

        // cart discounts rules
        $aDiscountsRulesArray = '';
        if ( ! empty( $product_meta['_uni_cpo_cart_discount_quantity'][0] ) ) {
            $aDiscountsRulesArray = maybe_unserialize( $product_meta['_uni_cpo_cart_discount_quantity'][0] );
        }

        // calculates and stores discounted price if the discount rule is appliable
        if ( isset( $product_meta['_uni_cpo_cart_discounts_enable'][0] ) && true === (bool)$product_meta['_uni_cpo_cart_discounts_enable'][0] &&
            ! empty( $aDiscountsRulesArray ) && is_array( $aDiscountsRulesArray ) ) {

                $sDiscountedPrice = '';
                $sCpoPrice                                      = $cart_item['_uni_cpo_data']['uni_cpo_price'];
                $cart_discounts_vars['{uni_cpo_quantity}']      = $cart_item['quantity'];
                $cart_discounts_vars['{uni_cpo_calc_price}']    = $sCpoPrice;

                foreach ( $aDiscountsRulesArray as $sKey => $aRuleArray ) {

                    $cart_discounts_formula = '';

                    if ( $cart_item['quantity'] >= $aRuleArray['min'] && $cart_item['quantity'] <= $aRuleArray['max'] ) {

                        $cart_discounts_formula = uni_cpo_process_cart_discounts_formula_with_vars( $aRuleArray['value'], $cart_discounts_vars );
                        $calc_discount = uni_cpo_calculate_cart_discounts_formula( $cart_discounts_formula );

                        if ( $aRuleArray['type'] == 'percentage' ) {
                            $sDiscountedPrice = $sCpoPrice - $sCpoPrice*($calc_discount/100);
                        } else if ( $aRuleArray['type'] == 'amount' ) {
                            $sDiscountedPrice = $sCpoPrice - $calc_discount;
                        } else if ( $aRuleArray['type'] == 'price' ) {
                            $sDiscountedPrice = $calc_discount;
                        }
                        break;
                    }
                }

            if ( !empty($sDiscountedPrice) ) {
                $sDiscountedPrice = floatval(str_replace(',', '', $sDiscountedPrice));
                //$sDiscountedPrice = uni_cpo_price_raw( $sDiscountedPrice );
                $cart_item['data']->set_price($sDiscountedPrice);
            }
        }

        return $cart_item;
}

// adds meta info for order items
function uni_cpo_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {

    if ( isset( $values['_uni_cpo_data'] ) ) {  // cpo data exists for this cart item

        //
        $formatted_array    = $values['_uni_cpo_data'];
        unset( $formatted_array['uni_cpo_price'] );

        foreach ( $formatted_array as $name => $value ) {
            if ( false !== strpos( $name, UniCpo()->var_slug ) ) {
                $item->add_meta_data( '_' . $name, $value );
            }
        }

    }

    // uploaded files
    if ( ! empty( $cart_item['_uni_cpo_item_attachments'] ) ) {
        $attachments_names = array();
        foreach ( $cart_item['_uni_cpo_item_attachments'] as $attach_id ) {
            $attachments_names[] = get_the_title( $attach_id );
        }
        $attchments_count   = count( $attachments_names );
        if ( $attchments_count > 1 ) {
            $item_data[]    = array('name' => esc_html__('Uploaded files'), 'value' => $attachments_names);
        } else {
            $item_data[]    = array('name' => esc_html__('Uploaded file'), 'value' => $attachments_names);
        }
    }

    // adds item meta with attachments added
    if ( ! empty( $values['_uni_cpo_item_attachments'] ) ) {
        $item->add_meta_data( '_uni_cpo_list_of_attachments', $values['_uni_cpo_item_attachments'] );
    }

}

//
function uni_cpo_calculate_price_in_cart( $cart_item, $iProdId = '', $is_set_weight = false ) {

            $iProductId     = ( isset($cart_item['product_id']) ) ? $cart_item['product_id'] : $iProdId;
            $oProduct       = wc_get_product( $iProductId );
            $aProductCustom = get_post_custom( $iProductId );
            $aFormPostData  = $cart_item['_uni_cpo_data'];

                $sMainFormula           = ( !empty($aProductCustom['_uni_cpo_price_main_formula'][0]) ) ? $aProductCustom['_uni_cpo_price_main_formula'][0] : '';

                // create an array of all the variables and their values
                $iOptionsPostId     = ( !empty($aProductCustom['_uni_cpo_options_set'][0]) ) ? intval( $aProductCustom['_uni_cpo_options_set'][0] ) : 0;
                $aProductOptions    = get_post_meta( $iOptionsPostId, '_uni_cpo_options_structure', true );
                $is_non_option_wholesale = ( isset( $aProductCustom['_uni_cpo_non_option_vars_wholesale_enable'][0] ) && ! empty( $aProductCustom['_uni_cpo_non_option_vars_wholesale_enable'][0] ) ) ? true : false;
                $aArray             = array();

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
                        $oOption = uni_cpo_get_option( $aElementStructure['id'] );

                        if ( $oOption instanceof Uni_Cpo_Option && $oOption->id && $oOption->is_calculable() ) {
                            $uCalcResult = $oOption->calculation( $aFormPostData );
                            if ( is_array($uCalcResult) ) {
                                foreach ( $uCalcResult as $sVarName => $sVarValue ) {
                                    $aArray['{'.$sVarName.'}'] = $sVarValue;
                                }
                            } else {
                                $aArray[$oOption->post->post_title] = $uCalcResult;
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
                    $aArrayWithNonOptionVarsForConditional = uni_cpo_process_formula_with_non_option_vars( $aArray, $non_option_vars, 'conditional', $is_non_option_wholesale );

                }
                //print_r($aArray);

                //print_r($aFormPostData);
                // formula conditional logic evaluation
                if ( isset($aProductCustom['_uni_cpo_formula_conditional_enable'][0]) && $aProductCustom['_uni_cpo_formula_conditional_enable'][0] == true
                    && !empty($aProductCustom['_uni_cpo_formula_rule_options'][0]) ) {

                    if ( isset($aArrayWithNonOptionVarsForConditional) && !empty($aArrayWithNonOptionVarsForConditional) ) {
                        $aFormPostData = array_merge($aFormPostData, $aArrayWithNonOptionVarsForConditional);
                    }
                    //print_r($aFormPostData);
                    $sMainFormula = uni_cpo_process_formula_conditional_rules_scheme( $iProductId, 'check', '', '', $aFormPostData );

                }

                // change vars into values
                //print_r(' / formula before: ' . $sMainFormula);
                $sMainFormula = uni_cpo_process_formula_with_vars( $sMainFormula, $aArray );
                //print_r(' / formula after: ' . $sMainFormula);

                // calculate formula
                $fOrderPrice = uni_cpo_calculate_formula( $sMainFormula );

                $fMinPrice = ( !empty($aProductCustom['_uni_cpo_min_price'][0]) ) ? floatval( $aProductCustom['_uni_cpo_min_price'][0] ) : 0;

                // the final price - compare with min. price if defined
                if ( !empty($fMinPrice) && ( $fOrderPrice < $fMinPrice ) ) {
                    $fCalculatedPrice = $fMinPrice;
                } else {
                    $fCalculatedPrice = $fOrderPrice;
                }

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

                if ( $is_set_weight ) {

                    // cart discounts rules
                    $aWeightConditionalRulesArray = '';
                    if ( !empty( $aProductCustom['_uni_cpo_weight_rule_options'][0] ) ) {
                        $aWeightConditionalRulesArray = maybe_unserialize($aProductCustom['_uni_cpo_weight_rule_options'][0]);
                    }

                    // calculates and sets cart item custom weight
                    if ( isset($aProductCustom['_uni_cpo_weight_conditional_enable'][0]) && $aProductCustom['_uni_cpo_weight_conditional_enable'][0] == true
                        && !empty($aWeightConditionalRulesArray) ) {

                        if ( isset($aArrayWithNonOptionVarsForConditional) && !empty($aArrayWithNonOptionVarsForConditional) ) {
                            $aFormPostData = array_merge($aFormPostData, $aArrayWithNonOptionVarsForConditional);
                        }

                        $sWeightFormula = uni_cpo_process_weight_conditional_rules_scheme( $iProductId, $aFormPostData );

                        if ( !empty( $sWeightFormula ) ) {

                            $sWeightFormula = uni_cpo_process_formula_with_vars( $sWeightFormula, $aArray );
                            //print_r($sWeightFormula);
                            $weight = uni_cpo_calculate_formula( $sWeightFormula );
                            //print_r($weight);
                            $cart_item['data']->set_weight($weight);
                        }

                    }

                }

                // filter, so 3rd party scripts can hook up
                $fCalculatedPrice = apply_filters( 'uni_cpo_in_cart_calculated_price', $fCalculatedPrice, $oProduct, $cart_item );

                return $fCalculatedPrice;

}

// update weight of the cart content
add_action('woocommerce_checkout_update_order_meta', 'uni_cpo_save_cart_weight_in_order_meta');
function uni_cpo_save_cart_weight_in_order_meta( $order_id ) {
    global $woocommerce;

    $weight = $woocommerce->cart->cart_contents_weight;
    update_post_meta( $order_id, '_uni_cpo_cart_weight', $weight );
}

// makes it posible to order again the same items
add_filter( 'woocommerce_order_again_cart_item_data', 'uni_cpo_woocommerce_order_again_cart_item_data', 10, 3 );
function uni_cpo_woocommerce_order_again_cart_item_data( $cart_item_meta, $item, $order ){
    $cart_item_meta['_uni_cpo_data'] = array();

    foreach ( $item->get_meta_data() as $meta ) {
        if ( false !== strpos( $meta->key, UniCpo()->var_slug ) ) {
            $slug = str_replace( '_' . UniCpo()->var_slug, '', $meta->key );

            // attachments
            if ( 'list_of_attachments' === $slug ) {

                $cart_item_meta['_uni_cpo_item_attachments'] = $meta->value;

            // cpo options, but not attachments
            } else {

                $meta_key_wo_ = ltrim( $meta->key, '_' );
                $cart_item_meta['_uni_cpo_data'][$meta_key_wo_] = $meta->value;

            }
        }
    }

    $cart_item_meta['uni_cpo_cart_item_id'] = current_time('timestamp');
    return $cart_item_meta;

}

//
function uni_cpo_number_of_decimals( $value ){
    if ((int)$value == $value) {
        return 0;
    } else if (! is_numeric($value)) {
        return false;
    }

    return strlen($value) - strrpos($value, '.') - 1;
}

//
function uni_cpo_detect_shortcode( $content, $shortcode_name ){
    global $post;
    if ( has_shortcode( $content, $shortcode_name ) ) {
        $pattern = '\[(\[?)('.$shortcode_name.')(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
        global $post;
        if ( preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) &&
            array_key_exists( 2, $matches ) && in_array( $shortcode_name, $matches[2] ) )
        {
            foreach ( $matches[2] as $key => $val ) {
                if ( $shortcode_name === $val ) {
                    $parsed = shortcode_parse_atts( $matches[3][$key] );
                    if ( is_array( $parsed ) ) {
                        foreach ( $parsed as $attr_name => $attr_value ) {
                            if ( 'id' === $attr_name ) {
                                $product_id = intval( $attr_value );
                                return $product_id;
                            }
                        }
                    }
                }
            }
        }
    }
    return false;
}

//
function uni_cpo_option_tooltip_icon_output( $option ){
    $html = '<i class="fa fa-question-circle-o" aria-hidden="true"></i>';
    return apply_filters( 'uni_cpo_option_tooltip_icon_output', $html, $option );
}

//
add_action( 'woocommerce_product_thumbnails', 'uni_cpo_woocommerce_product_thumbnails', 33 );
function uni_cpo_woocommerce_product_thumbnails() {

    if ( is_singular('product') ) {
        global $product;
        $product_id     = $product->get_id();
        $product_meta   = get_post_custom( $product_id );

        if ( isset( $product_meta['_uni_cpo_layered_image_enable'][0] ) && true === (bool) $product_meta['_uni_cpo_layered_image_enable'][0] && has_post_thumbnail() ) {

            $options_set_id     = ( ! empty( $product_meta['_uni_cpo_options_set'][0] ) ) ? intval( $product_meta['_uni_cpo_options_set'][0] ) : '';
            $product_options    = get_post_meta( $options_set_id, '_uni_cpo_options_structure', true );
            $palettes_data      = array();

            if ( ! empty( $product_options ) ) {
                foreach ( $product_options as $structure_item ) {
                    $option = uni_cpo_get_option( $structure_item['id'] );

                    if ( $option instanceof Uni_Cpo_Option && $option->get_id() && 'palette_select' === $option->get_type() ) {
                        $option_meta = $option->get_post_meta();
                        if ( isset( $option_meta['_uni_cpo_field_palette_image_encoded'][0] ) && ! empty( $option_meta['_uni_cpo_field_palette_image_encoded'][0] ) ) {
                            $palettes_data[] = array(
                                'slug'  => $option->get_slug(),
                                'src'   => $option_meta['_uni_cpo_field_palette_image_encoded'][0]
                            );
                        }
                    }

                }
            }

            if ( ! empty( $palettes_data ) ) {
                $thumbnail_uri = UniCpo()->plugin_url().'/assets/images/blank.png';

                $html = '<div data-thumb="' . esc_url( $thumbnail_uri ) . '" class="woocommerce-product-gallery__image uni_cpo_main_image_layered_image">';
                foreach ( $palettes_data as $data ) {
                    $html .= '<img id="palette-layer-' . $data['slug'] . '" src="' . $data['src'] . '" />';
                }
            	    $html .= '<div class="uni_cpo_main_image_overlay">';
                        $html .= '<p>' . esc_html('Please, choose a color for the background:', 'moomoo') . '</p>';
                        $html .= '<span class="uni-cpo-main-image-bg-colorpicker"></span>';
                    $html .= '</div>';
                $html .= '</div>';

                echo $html;
            }

        }
    }

}

// aelia currency switcher support
    /**
         * Basic integration with WooCommerce Currency Switcher, developed by Aelia
         * (http://aelia.co). This method can be used by any 3rd party plugin to
         * return prices converted to the active currency.
         *
         * Need a consultation? Find us on Codeable: https://bit.ly/aelia_codeable
         *
         * @param double price The source price.
         * @param string to_currency The target currency. If empty, the active currency
         * will be taken.
         * @param string from_currency The source currency. If empty, WooCommerce base
         * currency will be taken.
         * @return double The price converted from source to destination currency.
         * @author Aelia <support@aelia.co>
         * @link http://aelia.co
         */
        function uni_cpo_aelia_price_convert( $price, $to_currency = null, $from_currency = null ){
            // If source currency is not specified, take the shop's base currency as a default
            if( empty( $from_currency ) ) {
                $from_currency = get_option('woocommerce_currency');
            }
            // If target currency is not specified, take the active currency as a default.
            // The Currency Switcher sets this currency automatically, based on the context. Other
            // plugins can also override it, based on their own custom criteria, by implementing
            // a filter for the "woocommerce_currency" hook.
            //
            // For example, a subscription plugin may decide that the active currency is the one
            // taken from a previous subscription, because it's processing a renewal, and such
            // renewal should keep the original prices, in the original currency.
            if( empty( $to_currency ) ) {
                $to_currency = get_woocommerce_currency();
            }

            // Call the currency conversion filter. Using a filter allows for loose coupling. If the
            // Aelia Currency Switcher is not installed, the filter call will return the original
            // amount, without any conversion being performed. Your plugin won't even need to know if
            // the multi-currency plugin is installed or active
            return apply_filters( 'wc_aelia_cs_convert', $price, $from_currency, $to_currency );
    }
    // price calculated on the product page
    add_filter( 'uni_cpo_ajax_calculated_price', 'uni_cpo_ajax_calculated_price_aelia_currency_switcher', 10, 1 );
    function uni_cpo_ajax_calculated_price_aelia_currency_switcher( $price ) {
        return uni_cpo_aelia_price_convert( $price );
    }

    add_filter( 'uni_cpo_in_cart_calculated_price', 'uni_cpo_in_cart_calculated_price_aelia_currency_switcher', 11, 1 );
    function uni_cpo_in_cart_calculated_price_aelia_currency_switcher( $price ) {
        return uni_cpo_aelia_price_convert( $price );
    }

    add_filter( 'uni_cpo_price_archive_displaying', 'uni_cpo_price_archive_displaying_aelia_currency_switcher', 9, 1 );
    function uni_cpo_price_archive_displaying_aelia_currency_switcher( $price ) {
        return uni_cpo_aelia_price_convert( $price );
    }

    add_filter( 'uni_cpo_price_for_meta_tag_displaying', 'uni_cpo_price_for_meta_tag_displaying_aelia_currency_switcher', 10, 1 );
    function uni_cpo_price_for_meta_tag_displaying_aelia_currency_switcher( $price ) {
        return uni_cpo_aelia_price_convert( $price );
    }


?>