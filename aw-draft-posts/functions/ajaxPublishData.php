<?php
	
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

		$post_id = $_GET['id'];
		$sql = "SELECT ID FROM `wp_posts` WHERE ID = ".$_GET['id'];				
		
		$result 	=	$connection->query($sql);

		if( $result->num_rows > 0 ){
			
			
			//** Publish Entry From `wp_posts` **//
			$sql = "UPDATE `wp_posts` SET `post_status` = 'publish' WHERE `ID` = $post_id";
			$resultPublish 	=	$connection->query($sql);
  
			if( $resultPublish ){
				$temp['status']		=	'1';
				$temp['message']	=  '<p  class="aw_drafts_message aw_drafts_success">Post Published Successfully.</p>';
			}else{
				$temp['status']		=	'0';
				$temp['message']	=  '<p  class="aw_drafts_message aw_drafts_error">Error! Publishing Post.</p>';
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