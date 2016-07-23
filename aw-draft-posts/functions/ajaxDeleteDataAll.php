<?php

	 
	// $temp['status']		=	'1';
	// $temp['message']	=  '<p  class="aw_drafts_message aw_drafts_success">Posts Published Successfully.</p>';		
	// echo json_encode( $temp );	
	
	
	// die;
	
	$serverPath = $_SERVER['DOCUMENT_ROOT']."/".$_GET['source']."/wp-config.php";
	require( $serverPath );

	//** File is used get all the wigets data form the soruce url.  **// 
	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$mysql_database =  	str_replace('-', '_', $_GET['source'] );		//** As Received form Request. **//
	
	$connection  	= 	new mysqli($servername, $username, $password, $mysql_database);

	$response 		=	array();

	if ($connection->connect_error) {
		echo "Connection Error!";
	}else{

		$post_id =  $_GET['id'];

		$sql = "SELECT ID FROM `wp_posts` WHERE ID IN (".$post_id.")";				
		
		$result 	=	$connection->query($sql);

		if( $result->num_rows > 0 ){
			
			//** Delete entry from term_relationship.  **//	
			$sql = "DELETE FROM `wp_term_relationships` WHERE `object_id` IN ( $post_id ) ";
			$connection->query($sql);

			//** Delete entry form postmeta **//
			$sql = "DELETE FROM `wp_postmeta` WHERE `post_id` IN ( $post_id )";
			$connection->query($sql);
			
			//** Detele entry from posts**//
			$sql = "DELETE FROM `wp_posts` WHERE `ID` IN ( $post_id )";
			$resultDelete 	=	$connection->query($sql);
  
			if( $resultDelete ){
				$temp['status']		=	'1';
				$temp['message']	=  '<p  class="aw_drafts_message aw_drafts_success">Post Deleted Successfully.</p>';
			}else{
				$temp['status']		=	'0';
				$temp['message']	=  '<p  class="aw_drafts_message aw_drafts_error">Error! Deleting Post.</p>';
			}

			$response	 		=	$temp;	

		}else{
			$temp['status']		=	'0';
			$temp['message']	=  '<p  class="aw_drafts_message aw_drafts_error">No Such Post Found! Please Try Again.</p>';

			$response			=	$temp;
		}
				
		echo json_encode( $response );	
		$connection->close();
	}
	

	
?>