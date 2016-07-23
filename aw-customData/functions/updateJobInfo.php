<?php 
	
	$copyStatus 	=	false;

	$copySuccess	=	0;
	$copyError		=	0;
	$copyAlready	=	0;

	$file = fopen( $_FILES['jobInfoFile']['tmp_name'],"r");

 	$firstRow 	=	true;

 	$header		=	array();
 	$content	=	array();

 	$finalData	=	array();
	

	//** Fetch Content Form File. **//
	while(! feof($file)){
		
		if( $firstRow){
			$header 	=	fgetcsv($file);
			$firstRow = false;
		}else{
			$content[] 	=	fgetcsv($file);
		}
	}
	
	fclose($file);

	global $wpdb;					
	
	$finalData = makeData( $header, $content);

	foreach ($finalData as $dataRow ) {
		
		$external_post_id = $dataRow['external_post_id'];

		//** check if post is already in database **//
		$result  =	$wpdb->get_results("SELECT * FROM `wp_postmeta` WHERE `meta_key` = 'external_post_id' AND  `meta_value` = '$external_post_id';");	

		if( !isset( $result[0] ) ){ 

		
			$user_id = get_current_user_id();
			// Create post object
			$my_post = array(
				'post_title'    => $dataRow['postTitle'],
				'post_content'  => $dataRow['postContent'],
				'post_status'   => 'publish',
				'post_author'   => $user_id
			);

			// Insert the post into the database
			$post_id = wp_insert_post( $my_post );
			
			if( $post_id ){
				//** insert Post meta **//
				add_post_meta( $post_id, 'external_post_id', 	$dataRow['external_post_id'] );
				add_post_meta( $post_id, 'extrenal_category',	$dataRow['extrenal_category'] );
				add_post_meta( $post_id, 'vacancies', 			$dataRow['vacancies'] );
				add_post_meta( $post_id, 'age_limit', 			$dataRow['age_limit'] );
				add_post_meta( $post_id, 'educational_qualification', $dataRow['educational_qualification'] );
				add_post_meta( $post_id, 'selction_process', 		$dataRow['selction_process'] );
				add_post_meta( $post_id, 'how_to_apply', 			$dataRow['how_to_apply'] );
				add_post_meta( $post_id, 'instructions_to_apply_online', $dataRow['instructions_to_apply_online'] );
				add_post_meta( $post_id, 'important_dates', 		$dataRow['important_dates'] );
				add_post_meta( $post_id, 'recruitment_advt_link', 	$dataRow['recruitment_advt_link'] );
				add_post_meta( $post_id, 'apply_online_link', 		$dataRow['apply_online_link'] );
				
				$copyStatus = true;
				$copySuccess++;				
			}else{
				$copyError++;
			}

		}else{
			$copyAlready++;
		}
	}
	

//** function Starts here. **//

function makeData( $header, $content){

	$finalData = array();

	//** Make  Final Data Array **//
	foreach ($content as $dataRow) {
	
		$postTitle 					= 	"postTitle";
		$postContent 				= 	"postContent";
		$postMetaExternalID 		= 	"external_post_id";

		$postMetaExternal 			= 	strtolower( $header[1] );
		$postMetaExternal 			= 	strtolower( $header[1] );
		$postMetaExternal 			= 	"extrenal_".$postMetaExternal;

		$postMetaVacancies			= 	strtolower( $header[4] );
		$postMetaVacancies			= 	str_replace(' ', '_', $postMetaVacancies );

		$postMetaAge				= 	strtolower( $header[5] );
		$postMetaAge				= 	str_replace(' ', '_', $postMetaAge );	

		$postMetaEducation			= 	strtolower( $header[6] );
		$postMetaEducation			= 	str_replace(' ', '_', $postMetaEducation );

		$postMetaSelction			= 	strtolower( $header[7] );
		$postMetaSelction			= 	str_replace(' ', '_', $postMetaSelction );		

		$postMetaHowToApply			= 	strtolower( $header[8] );
		$postMetaHowToApply			= 	str_replace(' ', '_', $postMetaHowToApply );

		$postMetaInstructions		= 	strtolower( $header[9] );
		$postMetaInstructions		= 	str_replace(' ', '_', $postMetaInstructions );					

		$postMetaImportantDates		= 	strtolower( $header[10] );
		$postMetaImportantDates		= 	str_replace(' ', '_', $postMetaImportantDates );

		$postMetaRecruitmentLink	= 	strtolower( $header[11] );
		$postMetaRecruitmentLink	= 	str_replace(' ', '_', $postMetaRecruitmentLink );

		$postMetaApplyLink			= 	strtolower( $header[12] );
		$postMetaApplyLink			= 	str_replace(' ', '_', $postMetaApplyLink );		

		//** MAke Date**//

		$temp[$postMetaExternalID ]		=	$dataRow[0];
		$temp[$postTitle ]				=	$dataRow[2];
		$temp[$postContent ]			=	$dataRow[3];

		$temp[$postMetaExternal]		=	$dataRow[1];
		$temp[$postMetaVacancies]		=	$dataRow[4];
		$temp[$postMetaAge]				=	$dataRow[5];
		$temp[$postMetaEducation]		=	$dataRow[6];
		$temp[$postMetaSelction]		=	$dataRow[7];
		$temp[$postMetaHowToApply]		=	$dataRow[8];
		$temp[$postMetaInstructions]	=	$dataRow[9];
		$temp[$postMetaImportantDates]	=	$dataRow[10];
		$temp[$postMetaRecruitmentLink]	=	$dataRow[11];
		$temp[$postMetaApplyLink]		=	$dataRow[12];

		$finalData[] =	$temp;			
		
	}

	return $finalData;

}


?>