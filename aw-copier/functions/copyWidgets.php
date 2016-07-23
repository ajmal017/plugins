<?php 
	
	/****************************************************************************************
	* FileName:	CopyWidget.php 																*
	* Author:	G0947 																		*				
	* Action: 	This is used to copy the selected widgets form the source blog to the 		*
	*			the Selected multiple blogs. 												*
	*																						*			
	*****************************************************************************************/	 
	
	$serverPath = $_SERVER['DOCUMENT_ROOT']."/".$_POST['widgetSource']."/wp-config.php";
	require( $serverPath );

	//** File is used get all the wigets data form the soruce url.  **// 
	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$mysql_database =  	str_replace('-', '_', $_POST['widgetSource'] );		//** As Received form Request. **//

	//** source blog template theme. **//
	$sourceTempate 	= 	'';
		
	//** source blog Language. **//
	$sourceLanguage =	'';
	
	
	
	$connection  	= 	new mysqli($servername, $username, $password, $mysql_database);

	if ($connection->connect_error) {
		echo "Connection Error!";
	}else{

		//** get the source blog template.**//
		$sourceTempate 	= 	getBlogTemplate( $connection );
		

		//** get the source blog language **//
		$sourceLanguage =	getBlogLanguage( $connection );
		


		// echo 'connected Succesfully to Source.<br>';
		$sql 			= 	"SELECT * FROM wp_options WHERE option_name='widget_text'";
		$result 		=	$connection->query($sql);
		$row 			= 	mysqli_fetch_assoc($result);	

		$widgetCopyFrom 	= 	unserialize($row['option_value']);
		
		$widgetTitles 		=	$_POST["widgetTitle"];
		$destinationBlogs 	=	$_POST["widgetDestination"]; 
		$fromArray 		=	array();
		
		$sql 			= 	"SELECT * FROM wp_options WHERE option_name='sidebars_widgets'";
		$result 		=	$connection->query( $sql );
		$row 			= 	mysqli_fetch_assoc( $result );

		$widgetSidebars =	unserialize($row['option_value']);	


		foreach ($widgetSidebars as $widgetSidebarKey => $widgetSidebar) {
			
			//** Check if the $widgetSidebar is array, if Not Continue **//
			if( !is_array( $widgetSidebar)){
				continue;
			}			

			//** traverse all the titles in the widgetSidebar.  **//
			foreach ($widgetSidebar as $textTitle ) {			 	
				
				//** check if title matches a text-*, that is if it contains a valid text widget location. **//
				if ( preg_match("/text-*/i",$textTitle) ) {

					$locationArr 	= 	explode('-', $textTitle);
					$locationIndex 	= 	$locationArr[1];

					$widgetCopyFrom[ $locationIndex ]['widgetUnder'] 			= 	$widgetSidebarKey;
					$widgetCopyFrom[ $locationIndex ]['widgetLocation'] 		= 	$locationIndex;
					$widgetCopyFrom[ $locationIndex ]['widgetLocationText'] 	= 	$textTitle;
					
				}
			} 
			
		}

		//** Make a Array of Content of Widgets of Sources.  **//
		//** Filter the selected widget(s) form all the source widgets. Result is stored in $formArray. **//
		foreach ( $widgetTitles as $widgetTitle ) {
			 
			foreach( $widgetCopyFrom as $fromKey => $fromValue ){

				if( $widgetTitle == $fromValue["title"] ){
					
					$fromArray[] 	= 	array( 	"title" 				=>	$fromValue["title"],
												"text"					=>	$fromValue["text"],
												"filter"				=>	$fromValue["filter"],
												"widgetUnder" 			=> 	$fromValue["widgetUnder"],
											    "widgetLocation" 		=> 	$fromValue["widgetLocation"],
											    "widgetLocationText"	=> 	$fromValue["widgetLocationText"]
											);
				}
			}
		}

		//** Code To Copy Widget to Destination Widgets Start. **//
		$widgetDestinations  = $_POST['widgetDestination'];

		$successInCopy 	= 0;
		$ErrorInCopy 	= 0;
		$ErrorAlready 	= 0;
		$ErrorLanguage	= 0;
		$ErrorTemplate	= 0;
		
		//** destination blog template **//	
		$destinationTempate 	= 	'';
		
		//** destination blog language **//
		$destinationLanguage 	=	'';
		

		$copyStatus 	= false;
		
		foreach ($widgetDestinations as $widgetDestination ) {
			
			$mysql_database_destination 	=  	str_replace('-', '_', $widgetDestination );		//** As Received form Request. **//

			$connectionDestination  	= 	new mysqli($servername, $username, $password, $mysql_database_destination );

			if ($connectionDestination->connect_error) {
				echo "Connection Error!";
			}else{

				//** get the source blog template.**//
				$destinationTempate 	= 	getBlogTemplate( $connectionDestination );			
				
				//** get the source blog language **//				
				$destinationLanguage 	=	getBlogLanguage( $connectionDestination );
				

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
				
				$sql 			= 	"SELECT * FROM wp_options WHERE option_name='widget_text'";
				$result 		=	$connectionDestination->query($sql);
				$row 			= 	mysqli_fetch_assoc($result);	
				
				$widgetCopyTo 	= 	unserialize($row['option_value']);

				$sql 			= 	"SELECT * FROM wp_options WHERE option_name='sidebars_widgets'";
				$result 		=	$connection->query($sql);
				$row 			= 	mysqli_fetch_assoc($result);

				$widgetSidebarsDestinations  =	unserialize($row['option_value']);	

				foreach ($fromArray as $widgetContent ) {

					foreach ($widgetSidebarsDestinations as $key => $value) {
						
						if( $key ==  $widgetContent['widgetUnder'] ){
							
							//** Check of Title is ALready Present In Array. **//
							$foundTitle 	=	false;

							foreach ( $widgetCopyTo as $innerKey => $widget ) {
								if( $widget['title'] == $widgetContent['title'] && $key ==  $widgetContent['widgetUnder']  ){

									$widgetCopyTo[$innerKey]['text'] 	= $widgetContent['text'];
									$widgetCopyTo[$innerKey]['filter'] 	= $widgetContent['filter'];
									 
									$foundTitle	=	true;
									break;
								}
							}
							
							//** If Array With Same Title is found the New Array Will No be Added to the Destination Array. **//
							if ( $foundTitle ) {
								
								$foundTitle = false;
								// $ErrorAlready++;
								$successAlready++;
								continue;	
							}
							

							$widgetCopyTo[] = array(
													"title" 	=> $widgetContent['title'],
										            "text" 		=> $widgetContent['text'],
										            "filter" 	=> $widgetContent['filter'] 
												);

							end( $widgetCopyTo );
							$last_id	=	key( $widgetCopyTo );	
							
							$widgetSidebarsDestinations[$key][] = "text-".$last_id;

						}
						
					}
				}

				//** Update New widget_text array to the updated servers. **//				
				$widgetText 	=   serialize($widgetCopyTo);

				
				$sql 			= 	"UPDATE  wp_options set option_value = '$widgetText' WHERE option_name='widget_text'";
				$resultText 	=	$connectionDestination->query($sql);
				
				// print_r($widgetText);
				
				//** Update New SideBar Widget. **//
				$widgetSidebar 	=   serialize( $widgetSidebarsDestinations );

				

				$sql 			= 	"UPDATE  wp_options set option_value = '$widgetSidebar' WHERE option_name='sidebars_widgets'";
				$resultSidebar 	=	$connectionDestination->query($sql);
				
				if( ( $resultText && $resultSidebar ) ){
					$successInCopy++;
					$copyStatus 	= true;

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



?>