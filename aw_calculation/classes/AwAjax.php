<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// div for divider.
// <div class="main-section-divider"></div>

class AwAjax {
	public static function getMainSection(){
		echo AwComponents::getMainSection();
		die();
	}

	public static function getInnerMainSection(){
		$selectedoption = $_POST['selectedoption'];
		AwComponents::$selectedoption = $selectedoption;
		echo AwComponents::getInnerMainSection();
		die();
	}

	public static function getAreaSelect(){
		$itemtype = $_POST['itemtype'];
		echo AwComponents::getArea($itemtype);
		die();
	}

	/*funtion to get areas */
	public static function getAreaSection(){

		if(AwComponents::$areaTypeValue){

			$areaType 	= strtolower(AwComponents::$areaTypeValue);
		}
		else{
			$areaType  	= trim($_POST['area_type']);
			$areaType  	= str_replace(" ", "_", $areaType);
			$areaType 	= strtolower($areaType);
		}
		

		// floor
		// tub_shower
		// shower
		// shower_floor
		// drop_in_tub
		// wainscot
		// backsplash
		// fireplace

		switch ($areaType) {
			case 'floor':
				echo AwComponents::getFloorHeader();
				break;

			case 'tub_shower':
				echo AwComponents::getTubShower();
				break;

			case 'shower':
				echo AwComponents::getShower();
				break;

			case 'shower_floor':
				echo AwComponents::getFloorHeader();
				break;

			case 'drop_in_tub':
				echo AwComponents::getTubShower();
				break;

			case 'wainscot':
				echo AwComponents::getTubShower();
				break;

			case 'backsplash':
				echo AwComponents::getBacksplash();
				break;

			case 'fireplace':
				echo AwComponents::getFireplace();
				break;

			default:
				echo '<div class="area-wrapper"><div class="row-wrapper"><div class="col-1"><div class="select-room-row"> <h1> Selected: '.$areaType.' </h1> </div></div><div class="clear"></div></div></div>';
				break;
		}

		die();
	}

	public static function getFloorSection(){
		echo AwComponents::getFloor();
		die();
	}

	
	public static function saveProject(){

		echo AwSave::saveProject();
		die();
	}



	public static function deleteProject(){

		$projectID = $_POST['projectid'];	



		$deleteproject[0]['projectID'] = $projectID;

		echo AwSave::deleteproject($deleteproject);
		echo 'delete';

		die();
	}

	public static function saveArchiveProject(){

		$projectDID = $_POST['projectid'];	


		echo AwSave::saveArchiveProject($projectDID);
		echo 'archive';

		die();
	}
	
	public static function getBrandSection(){

		echo AwComponents::getBrand();
		die();
		

	}


	public static function getPatternSection(){

		echo AwComponents::getPattern();
		die();
		

	}

	public static function getTransitionsSection(){

		echo AwComponents::getTransitions();
		die();
		

	}

	public static function getThinsetSection(){

		echo AwComponents::getThinset();
		die();
		

	}

	public static function getProjectSectionData(){

		echo AwComponents::getProjectData();
		die();
		

	}


	/*-----------#Save location ------------*/



	public static function saveLocation(){

		$locationcreator = 	$_POST['locationcreator'];


		//the array of arguements to be inserted with wp_insert_post
		$new_location = array(
		'post_title'    => $locationcreator['lot'],
		
		'post_status'   => 'publish',          
		'post_type'     => 'location' 
		);

		//insert the the post into database by passing $new_post to wp_insert_post
		//store our post ID in a variable $pid
		$pid = wp_insert_post($new_location);

		//we now use $pid (post id) to help add out post meta data

		foreach($locationcreator as $key => $locMeta){

			add_post_meta($pid, $key, $locMeta, true);
		}
		
		echo $pid;


		die();

	}


	/*---------------Get locations list --------*/

	public static function getLocations(){


		echo AwComponents::getLocationsLists();
		die();
		

	}

	/*---------------Get Project Navigation --------*/

	public static function getProjectNavigation(){


		echo AwNavigation::leftsidebar();
		die();
		

	}


/* class ends here */
}
?>
