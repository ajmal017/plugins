<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AwFields {
	/* variables */

	/* methods */
	public static function fireplace_height( $roomTypeID, $areaTypeID, $section_inner, $name = '', $value = '' ){
		ob_start();
		
		?>
			<select class="height_<?php echo $section_inner;?>" name="height_<?php echo $section_inner;?>" data-section="<?php echo $section_inner;?>" data-room="<?php echo $roomTypeID;?>" data-area="<?php echo $areaTypeID;?>">
	            <option>Height</option>
	            <option>test1</option>
	            <option>test2</option>
	        </select>		
		<?php                
		$output = ob_get_clean();
		return $output;
	}



}/* class ends here */

?>
