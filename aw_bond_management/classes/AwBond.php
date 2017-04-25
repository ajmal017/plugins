<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AwBond {

	public static function rollback( $bondIDs = null ) {
		if( !$bondIDs ){
			return false;
		}

		foreach ($bondIDs as $ID ) {
			wp_delete_post($ID);
		}
		return true;
	}
}/* class ends here */

?>