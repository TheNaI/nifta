<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Uni_Cpo_Option_Factory Class
 *
 */
class Uni_Cpo_Option_Factory {

	//
	public function get_option( $uOption = false, $sType = '' ) {

    		$oOption = $this->get_option_object( $uOption );

    		if ( ! $oOption ) {
    			$oOption = false;
    		}

            // wrong type of field
            if ( $oOption !== false ) {
                $sTypeFromPostMeta = get_post_meta( $oOption->ID, '_uni_cpo_field_type', true );
                if ( !empty($sType) && $sTypeFromPostMeta !== $sType ) {
                    $oOption = false;
                }
                $sType = $sTypeFromPostMeta;
            }
        
            $sClassname = 'Uni_Cpo_Option_'.$sType;

    		if ( ! class_exists($sClassname) ) {
    			return false;
    		}

            return new $sClassname( $oOption );

	}

	//
	private function get_option_object( $Option ) {
		if ( is_numeric( $Option ) ) {
			$Option = get_post( $Option );
		} elseif ( $Option instanceof Uni_Cpo_Option ) {
			$Option = get_post( $Option->id );
		} elseif ( ! ( $Option instanceof WP_Post ) ) {
			$Option = false;
		}

		return apply_filters( 'uni_cpo_option_object', $Option );
	}

}

?>