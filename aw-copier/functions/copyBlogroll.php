<?php 
	
	/****************************************************************************************
	* FileName:	CopyBlogroll.php 															*
	* Author:	G0947 																		*				
	* Action: 	This is used to copy the bolgrolls form the source blog to the 				*
	*			the Selected multiple blogs. 												*
	*																						*			
	*****************************************************************************************/	 
	


	$serverPath = $_SERVER['DOCUMENT_ROOT']."/".$_POST['blogrollSource']."/wp-config.php";
	require( $serverPath );

	//** File is used get all the wigets data form the soruce url.  **// 
	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$mysql_database =  	str_replace('-', '_', $_POST['blogrollSource'] );		//** As Received form Request. **//

	
	$connection  	= 	new mysqli($servername, $username, $password, $mysql_database);


	$sourceTempate 	= 	getBlogTemplate( $connection );
	$sourceLanguage =	getBlogLanguage( $connection );

	if ($connection->connect_error) {
		echo "Connection Error!";
	}else{
		
		// echo 'connected Succesfully to Source.<br>';
		$sql 			= 	"SELECT * FROM wp_options WHERE option_name='widget_links'";
		$result 		=	$connection->query($sql);
		$row 			= 	mysqli_fetch_assoc($result);	

		$BlogrollCopyFrom 	= 	unserialize($row['option_value']);

		//** Raw Data. **//
		$wp_links_source 	= 	unserialize($row['option_value']);


		$widgetTitles 			=	$_POST["blogrollTitle"];
		$blogrollDestinations 	=	$_POST["blogrollDestination"]; 
		$fromArray 		=	array();


		$sql 			= 	"SELECT * FROM wp_options WHERE option_name='sidebars_widgets'";
		$result 		=	$connection->query($sql);
		$row 			= 	mysqli_fetch_assoc($result);

		$blogrollSidebars 	=	unserialize($row['option_value']);	
		$fromArray 			= $blogrollSidebars;

		$wp_sidebar_links 	=	unserialize($row['option_value']);
		
		foreach ( $blogrollSidebars as $widgetSidebarKey => $widgetSidebar) {

			

		
				
			//** Check if the $widgetSidebar is array, if Not Continue **//
			if( !is_array( $widgetSidebar)){
				continue;
			}			

			//** traverse all the titles in the widgetSidebar.  **//
			foreach ($widgetSidebar as $textTitle ) {					 
				

				//** check if title matches a text-*, that is if it contains a valid text widget location. **//
				if ( preg_match("/link-*/i",$textTitle) ) {
					
					$locationArr 	= 	explode('-', $textTitle);
					$locationIndex 	= 	$locationArr[1];

					$widgetCopyFrom[ $locationIndex ]['widgetUnder'] 			= 	$widgetSidebarKey;
					$widgetCopyFrom[ $locationIndex ]['widgetLocation'] 		= 	$locationIndex;
					$widgetCopyFrom[ $locationIndex ]['widgetLocationText'] 	= 	$textTitle;
					
				}
			} 
			
		}

		//** Code To Copy Widget to Destination Widgets Start. **//
		$blogrollDestinations  = $_POST['blogrollDestination'];

		$successInCopy 	= 	0;
		$ErrorInCopy 	= 	0;
		$ErrorAlready 	= 	0;
		$ErrorLanguage	=	0;
		$ErrorSource	=	0;

		$copyStatus 	= false;
		
		foreach ($blogrollDestinations as $blogrollDestination ) {

			$mysql_database_destination 	=  	str_replace('-', '_', $blogrollDestination );		//** As Received form Request. **//

			$connectionDestination  = 	new mysqli($servername, $username, $password, $mysql_database_destination );

			$destinationTempate 	= 	getBlogTemplate( $connectionDestination );
			$destinationLanguage 	=	getBlogLanguage( $connectionDestination );

			if ($connectionDestination->connect_error) {
				echo "Connection Error!";
			}else{

				//** check if source and destination language match. **//
				if( $destinationLanguage !== $sourceLanguage){
					$ErrorLanguage	=	$ErrorLanguage + 1;	
					continue;
				}
				//** check if source and destination theme is same. **//
				if( $destinationTempate !== $sourceTempate){
					$ErrorTemplate	=	$ErrorTemplate + 1;	
					continue;
				}


				// $sql 			= 	"SELECT * FROM wp_options WHERE option_name='widget_links'";
				// $result 		=	$connectionDestination->query($sql);
				// $row 			= 	mysqli_fetch_assoc($result);	
				
				// $widgetCopyTo 	= 	unserialize($row['option_value']);

				// $sql 			= 	"SELECT * FROM wp_options WHERE option_name='sidebars_widgets'";
				// $result 		=	$connection->query($sql);
				// $row 			= 	mysqli_fetch_assoc($result);

				// $widgetSidebarsDestinations  =	unserialize($row['option_value']);



				$sql 				= 	"SELECT * FROM wp_options WHERE option_name='widget_links'";
				$result_wp_links 	=	$connectionDestination->query($sql);
				

				if( $result_wp_links->num_rows > 0 ){
					//** update **//
					$BlogrollLinks 	=   serialize( $wp_links_source);
					$sql 			= "UPDATE  wp_options set option_value = '$BlogrollLinks ' WHERE option_name = 'widget_links'";
					$resultLink 	=	$connectionDestination->query($sql);
				}else{
					$BlogrollLinks 	=   serialize( $wp_links_source);
					//** Insert **//
					$sql = "INSERT INTO  wp_options (`option_name`, `option_value`) VALUES ( 'widget_links', '$BlogrollLinks')";
					$resultLink 	=	$connectionDestination->query($sql);
				}

				$sql 			= 	"SELECT * FROM wp_options WHERE option_name='sidebars_widgets'";
				$result_wp_side	=	$connection->query($sql);				

				if( $result_wp_side->num_rows > 0 ){
					//** update **//
					$BlogrollSidebar 	=   serialize( $wp_sidebar_links );
					$sql 				= 	"UPDATE  wp_options set option_value = '$BlogrollSidebar' WHERE option_name = 'sidebars_widgets'";
					$resultSidebar 		=	$connectionDestination->query($sql);
				}else{
					$BlogrollSidebar 	=   serialize( $wp_sidebar_links );
					$sql 				= 	"INSERT INTO  wp_options (`option_name`, `option_value`) VALUES ( 'sidebars_widgets', '$BlogrollSidebar')";
					$resultSidebar 		=	$connectionDestination->query($sql);
				}
				
				// //** Update New widget_text array to the updated servers. **//				
				// $widgetText 	=   serialize( $fromArray );				
				
				// $sql 			= 	"UPDATE  wp_options set option_value = '$widgetText' WHERE option_name='widget_text'";
				// $resultText 	=	$connectionDestination->query($sql);
				

				// //** Update New SideBar Widget. **//
				// $widgetSidebar 	=   serialize($widgetSidebarsDestinations);

				// $sql 			= 	"UPDATE  wp_options set option_value = '$widgetSidebar' WHERE option_name='sidebars_widgets'";
				// $resultSidebar 	=	$connectionDestination->query($sql);
				
				if( ( $resultLink && $resultSidebar ) ){
					$successInCopy++;
					$copyStatus 	= true;
					copyLinks( $connection, $connectionDestination );

				}else{
					$ErrorInCopy++;	
				}
			}

			unset( $widgetCopyTo );
			unset( $widgetText );
			unset( $widgetSidebarsDestinations );
			unset( $widgetSidebar );

			$connectionDestination->close();	
		}
			

		$connection->close();
	}




	//**Function to get theme template. **//	
	function getBlogTemplate( $connection ){
		
		$sql 		= "SELECT * FROM `wp_options` WHERE `option_name` = 'template';";

		$resulTemplate	=	$connection->query( $sql );
		
		if( $resulTemplate->num_rows > 0 ){
			$row = mysqli_fetch_assoc( $resulTemplate );
			
			if( $row['option_value'] == '') {
				return null;
			}else{
				return $row['option_value'];
			};

		}else{
			return null;
		}	

	}


	//**function to get the Blog Language**//
	function getBlogLanguage( $connection ){
		
		$sql 		= "SELECT * FROM `wp_options` WHERE `option_name` = 'WPLANG';";

		$resultLang 	=	$connection->query( $sql );

		
		if( $resultLang->num_rows > 0 ){
			$row = mysqli_fetch_assoc( $resultLang );
			
			if( $row['option_value'] == '') {
				return "en_US";
			}else{
				return $row['option_value'];
			};

		}else{
			return "en_US";
		}	

	}


	//** Function to copy link form source to desination. **//
	function copyLinks($sourceConnection, $destinationConnection ){

		//** Get all the links form the source blog. **//
		$sql 		= "SELECT * FROM `wp_links;";

		$resultSource 	=	$sourceConnection->query( $sql );

		
		if( $resultSource->num_rows > 0 ){
			
			//** loop all the links of the souce blog and insert/iupdate in destination one by one**//			
			while ($row = mysqli_fetch_assoc( $resultSource ) ):
				
				//** link Row Value. **//				
				$sourceLink_url			=	$row['link_url'];
				$sourceLink_image		=	$row['link_image'];
				$sourceLink_name		=	$row['link_name'];
				$sourceLink_target		=	$row['link_target'];
				$sourceLink_description	=	$row['link_description'];
				$sourceLink_visible		=	$row['link_visible'];
				$sourceLink_owner		=	$row['link_owner'];
				$sourceLink_rating		=	$row['link_rating'];
				$sourceLink_updated		=	$row['link_updated'];
				$sourceLink_rel			=	$row['link_rel'];
				$sourceLink_notes		=	$row['link_notes'];
				$sourceLink_rss			=	$row['link_rss'];
					

				$sql  = "SELECT * FROM `wp_links WHERE `link_url` = $sourceLink_url;";
				
				$resultDestination	=	$destinationConnection->query( $sql );
				
				if( $resultDestination->num_rows > 0 ){
					$row = mysqli_fetch_assoc( $resultDestination );
					 	
					$link_id					=	$row['link_id'];
					$sqlUpdate 					= 	"UPDATE `wp_links` SET `link_url`='$sourceLink_url',`link_name`='$sourceLink_name',`link_image`='$sourceLink_image',`link_target`='$sourceLink_target',`link_description`='$sourceLink_description',`link_visible`='$sourceLink_visible',`link_owner`='$sourceLink_owner',`link_rating`='$sourceLink_rating',`link_updated`='$sourceLink_updated',`link_rel`='$sourceLink_rel',`link_notes`='$sourceLink_notes',`link_rss`='$sourceLink_rss' WHERE link_id = '$link_id'";
					$resultDestinationUpdate	=	$destinationConnection->query( 	$sqlUpdate );
					
				}else{

					//** Insert new Record in the destination Database. **//
					$sqlInsert 		= "INSERT INTO `wp_links`(`link_url`, `link_name`, `link_image`, `link_target`, `link_description`, `link_visible`, `link_owner`, `link_rating`, `link_updated`, `link_rel`, `link_notes`, `link_rss`) ('$sourceLink_url','$sourceLink_name','$sourceLink_image','$sourceLink_target','$sourceLink_description','$sourceLink_visible','$sourceLink_owner','$sourceLink_rating','$sourceLink_updated','$sourceLink_rel','$sourceLink_notes','$sourceLink_rss')";
					$resultInsert	=	$destinationConnection->query( 	$sqlInsert );
				}
			endwhile;

			return true;

		}else{
			return false;
		}	


		

	}


?>