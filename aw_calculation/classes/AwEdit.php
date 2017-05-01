<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AwEdit {
	/* variables */

	/* methods */

	public static function getFireplace($areas){
		
		
		
		$selectArray = array("height", "edge", "transition", "instructions", "sectionfire", "accent");
		$inputArray = array("amount", "lf");

		//self::$sectionTypeID = $section_inner;

		//return json_encode($areas);
		ob_start();
		?>

<div class="area-wrapper">
	<?php foreach($areas->section as $sec){ ?>				  	
	<?php if($sec->sectionMeta){ 

	foreach($sec->sectionMeta as $secmeta){


			$roomTypeID 	= $secmeta->roomDID;
			$areaTypeID     = $secmeta->areaDID;
			$section_inner 	= $secmeta->sectionDID;



			$fieldType = explode('_', $secmeta->section_key);

			if(in_array($fieldType[0],$selectArray)){
				$function = 'fireplace_'. $fieldType[0];
				?>


			<div class="row-9 row-wrapper" >
				<input type="hidden" name="sectionTypeID" value="<?php echo $section_inner;?>" />
		        <div class="col-2">
		            <div class="select-room-row">
		               <?php echo AwFireplaceFields::$function($roomTypeID, $areaTypeID, $section_inner );?>
		            </div>
		        </div>
		        <div class="clear"></div>
		    </div>




			<?php }


			if(in_array($fieldType[0],$inputArray)){
				$function = 'fireplace_'. $fieldType[0];
				?>


			<div class="row-9 row-wrapper" >
				<input type="hidden" name="sectionTypeID" value="<?php echo $section_inner;?>" />
		        <div class="col-2">
		            <div class="select-room-row">
		               <?php echo AwFireplaceFields::$function($roomTypeID, $areaTypeID, $section_inner );?>
		            </div>
		        </div>
		        <div class="clear"></div>
		    </div>




			<?php }

			?>



		<?php } } } ?>
		</div>
		<?php 
		$output = ob_get_clean();
		return $output;
	}



}/* class ends here */

?>
