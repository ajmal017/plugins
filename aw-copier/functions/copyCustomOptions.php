<?php 

	/****************************************************************************************
	* FileName:	CopyCustomOptions.php 														*
	* Author:	G0947 																		*				
	* Action: 	This is used to copy the Select Custom Options form hte source blog to  	*
	*			the Selected multiple blogs. The Custom Options include Custom Footer, 		*
	*			Favicon,  and Logo. 														*
	*																						*			
	*****************************************************************************************/	 
	

	//** Flag Variable for the copy Operation. **//	
	$customSuccessInCopy 	= 0;
	$customErrorInCopy 		= 0;
	$customErrorAlready 	= 0;
	$customErrorLanguage 	= 0;
	$customErrorTemplate 	= 0;
	
	$customCopyStatus 		= false;

	$logoCopySuccess		=	0;	
	$logoCopyNotice			=	0;
	$logoCopyError			=	0;


	$faviconCopySuccess		=	0;	
	$faviconCopyNotice		=	0;
	$faviconCopyError		=	0;

	$sourceTempate 			= 	'';
	$sourceLanguage 		=	'';
	
	
	$serverPath = $_SERVER['DOCUMENT_ROOT']."/".$_POST['customSource']."/wp-config.php";
	require( $serverPath );

	//** File is used get all the Custom Options data form the soruce url.  **// 
	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$mysql_database =  	str_replace('-', '_', $_POST['customSource'] );		//** As Received form Request. **//

	
	$connection  	= 	new mysqli($servername, $username, $password, $mysql_database);

	if ($connection->connect_error) {
		echo "Connection Error!";
	}else{
		
		//** get the source blog template.**//
		$sourceTempate 	= 	getBlogTemplate( $connection );

		//** get the source blog language **//
		$sourceLanguage =	getBlogLanguage( $connection );	

		// echo 'connected Succesfully to Source.<br>';
		$sql 				= 	"SELECT * FROM wp_options WHERE option_name LIKE 'aw%'";
		$result 			=	$connection->query($sql);
		$customTitles 		=	$_POST["customTitle"];
		$destinationBlogs 	=	$_POST["customDestination"]; 
		
		//** $formArray: Store the option_name and Value of Selected source blog. **//
		$fromArray 			=	array();

		while( $row = mysqli_fetch_assoc($result)){
			
			if( in_array($row['option_name'], $customTitles ) ){

					$temp['option_name']	=  	(string)	$row['option_name'];
					$temp['option_value'] 	= 	(string)	$row['option_value'];
					$fromArray[]			= 	$temp;
			}
		
		}


		//** Code To Copy Custom OPtions to Destination Blogs Starts. **//
		$destinationBlogs 	=	$_POST["customDestination"]; 

		//** Code TO Copy Favicon and Logo to Destinations if Selected. **//
		foreach ($destinationBlogs as $destinationBlog ) {

			//** Code to copy Other Options starts here. **//
			$mysql_database_destination =  	str_replace('-', '_', $destinationBlog );		//** As Received form Request. **//
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
					$customErrorLanguage	=	$customErrorLanguage + 1;	
					continue;
				}

				//** check if source and destination theme is same. **//
				if( $destinationTempate !== $sourceTempate){
					$customErrorTemplate	=	$customErrorTemplate + 1;	
					continue;
				}


				//** code to copy logo. **//
				if( in_array('logo', $customTitles ) ){

					$sourceLogoPath 		= 	$_SERVER['DOCUMENT_ROOT']."/".$_POST['customSource']."/wp-content/plugins/aw-customizer/logo/logo.png";				
					$DestinationLogoPath 	= 	$_SERVER['DOCUMENT_ROOT']."/".$destinationBlog."/wp-content/plugins/aw-customizer/logo/logo.png";				

					//** Check if logo already in Destination blog. **//
					if(file_exists( $DestinationLogoPath )){
						// $logoCopyNotice++;
						unlink ( $DestinationFaviconPath );

						if(copy($sourceLogoPath, $DestinationLogoPath )){
							$logoCopySuccess++;	
						}else{
							$logoCopyError++;
						}
					}else{
						if(copy($sourceLogoPath, $DestinationLogoPath )){
							$logoCopySuccess++;	
						}else{
							$logoCopyError++;
						}
						
					}

				}

				//** Code To Copy  Favicon. **//
				if( in_array('favicon', $customTitles ) ){
					
					$sourceFaviconPath 			= 	$_SERVER['DOCUMENT_ROOT']."/".$_POST['customSource']."/wp-content/plugins/aw-customizer/icon/favicon.png";				
					$DestinationFaviconPath 	= 	$_SERVER['DOCUMENT_ROOT']."/".$destinationBlog."/wp-content/plugins/aw-customizer/icon/favicon.png";				
					
					//** Check if  Favicon already exists in Destination blog. **//	
					if(file_exists( $DestinationFaviconPath )){
						// $faviconCopyNotice++;
						unlink ( $DestinationFaviconPath );

						if( copy($sourceFaviconPath, $DestinationFaviconPath )){
							$faviconCopySuccess++;	
						}else{
							$faviconCopyError++;
						}
					}else{
						if( copy($sourceFaviconPath, $DestinationFaviconPath )){
							$faviconCopySuccess++;	
						}else{
							$faviconCopyError++;
						}
						
					}	
					
				}
			}
		}	

		//** Unset all variables, used for the copy opration of the Favicon and logo. **//
		unset($destinationBlogs);
		unset($destinationBlog);
		unset($sourceLogoPath);
		unset($DestinationLogoPath);
		unset($sourceFaviconPath);
		unset($DestinationFaviconPath);
		

		//** Code To Copy Custom Options to Destination Blogs Starts. **//
		$destinationBlogs 	=	$_POST["customDestination"]; 

		
		foreach ($destinationBlogs as $destinationBlog ) {

			
			//** Code to copy Other Options starts here. **//
			$mysql_database_destination =  	str_replace('-', '_', $destinationBlog );		//** As Received form Request. **//
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
					$customErrorLanguage	=	$customErrorLanguage + 1;	
					continue;
				}

				//** check if source and destination theme is same. **//
				if( $destinationTempate !== $sourceTempate){
					$customErrorTemplate	=	$customErrorLanguage + 1;	
					continue;
				}

				
				foreach ($fromArray as $optionArray ) {
					//** Select $optionArray[{'Custom_option_key'}] in the destination blog if that option already exists. **//	
					$sql 			= 	"SELECT * FROM wp_options WHERE option_name = '".$optionArray['option_name']."'";
					$result 		=	$connectionDestination->query($sql);

					//** prepare data to be inserted in the destination blog(s) **//
					$dataOptionName 	=	(string) $optionArray['option_name'];
					$dataOptionValue 	=	(string) trim($optionArray['option_value']);
					
					//** check if custom option already exists in the destination  blog. **//
					if( $result->num_rows > 0 ){
						$row 	=	mysqli_fetch_assoc($result);	
						
						//** get the value of the custom option. **//
						$tempvalue 	=	trim( $row['option_value'] );
						if( empty( $tempvalue ) ){
							
							//** if the custom option of the destination blog is empty, the update the blog with new value.{value of the custom option of the source blog.} **//
							$customSql 	= "UPDATE  wp_options SET `option_value` = '$dataOptionValue' WHERE option_name = '".$optionArray['option_name']."'";
							$resultCustomSql 	= $connectionDestination->query( $customSql );
							
							if($resultCustomSql){
								
								//** Update the custom option Flags. **//
								$customSuccessInCopy++;
								$customCopyStatus = true;

							}else{

								//** Set flag if the custom option if the destination blog is already set. **//
								$customErrorInCopy++;
							}
						
						}else{
							//** Set Error if the is problem in fetching the custom option of the destination blog. **//
							// $customErrorAlready++;

							//** Overwrite the content if already defined. **//
							//** if the custom option of the destination blog is empty, the update the blog with new value.{value of the custom option of the source blog.} **//
							$customSql 	= "UPDATE  wp_options SET `option_value` = '$dataOptionValue' WHERE option_name = '".$optionArray['option_name']."'";
							$resultCustomSql 	= $connectionDestination->query( $customSql );
							
							if($resultCustomSql){
								
								//** Update the custom option Flags. **//
								$customSuccessInCopy++;
								$customCopyStatus = true;

							}else{

								//** Set flag if the custom option if the destination blog is already set. **//
								$customErrorInCopy++;
							}
						}

						//** unset data variables. **//
						unset(	$dataOptionName );
						unset( 	$dataOptionValue );
					
						
					}else{
						
						//** Insert custom in the destination blog if blog is not present in the destination blog. **//
						$customSql 	= "INSERT INTO wp_options(`option_name`, `option_value`) values( '$dataOptionName',  '$dataOptionValue')";
						$resultCustomInsert 	= $connectionDestination->query( $customSql );
						
						if( $resultCustomInsert ){
							
							//** Set success flag for the insert operation. **//
							$customCopyStatus = true;
							$customSuccessInCopy++;	

						}else{
							//** set error Flag if some error occured in the insert operation opration of the destination blog. **//
							$customErrorInCopy++;							
						}
					}	
				}

				$connectionDestination->close();	
			}
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