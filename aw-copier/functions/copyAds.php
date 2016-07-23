<?php 
	 

	
	$adsSuccessInCopy 	=	0;
	$adsErrorInCopy 	= 	0;
	$adsErrorAlready 	= 	0;
	$adsErrorLanguage	= 	0;
	$adsErrorTemplate	= 	0;
	
	$adsCopyStatus 		= false;
	
	$serverPath = $_SERVER['DOCUMENT_ROOT']."/".$_POST['adsSource']."/wp-config.php";
	require( $serverPath );

	//** File is used get all the wigets data form the soruce url.  **// 
	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$mysql_database =  	str_replace('-', '_', $_POST['adsSource'] );		//** As Received form Request. **//

	
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
		$adsTitles 		=	$_POST["adsTitle"];
		$destinationBlogs 	=	$_POST["adsDestination"]; 
		
		//** $formArray: Store the option_name and Value of Selected source blog. **//
		$fromArray 			=	array();

		while( $row = mysqli_fetch_assoc($result)){
			
			if( in_array($row['option_name'], $adsTitles ) ){

					$temp['option_name']	=  	(string)	$row['option_name'];
					$temp['option_value'] 	= 	(string)	$row['option_value'];
					$fromArray[]			= 	$temp;
			}
		
		}

		
		//** Code To Copy ads OPtions to Destination Blogs Starts. **//
		$destinationBlogs 	=	$_POST["adsDestination"]; 

		
		foreach ($destinationBlogs as $destinationBlog ) {
			
			$mysql_database_destination =  	str_replace('-', '_', $destinationBlog );		//** As Received form Request. **//
			$connectionDestination  	= 	new mysqli($servername, $username, $password, $mysql_database_destination );

			if ($connectionDestination->connect_error) {
				echo "Connection Error!";
			}else{

				//** get the source blog template.**//
				$destinationTempate 	= 	getBlogTemplate( $connectionDestination );

				//** get the source blog language **//
				$destinationLanguage =	getBlogLanguage( $connectionDestination );



				//** check if source and destination language match. **//
				if( $destinationLanguage !== $sourceLanguage){
					$adsErrorLanguage	=	$adsErrorLanguage + 1;	
					continue;
				}

				//** check if source and destination theme is same. **//
				if( $destinationTempate !== $sourceTempate){
					$adsErrorTemplate	=	$adsErrorTemplate + 1;	
					continue;
				}
				
				foreach ($fromArray as $optionArray ) {
					
					$sql 			= 	"SELECT * FROM wp_options WHERE option_name = '".$optionArray['option_name']."'";
					$result 		=	$connectionDestination->query($sql);

					$dataOptionName 	=	(string) $optionArray['option_name'];
					$dataOptionValue 	=	(string) trim($optionArray['option_value']);
					
					if( $result->num_rows > 0 ){
						$row 	=	mysqli_fetch_assoc($result);	
						
						$tempvalue 	=	trim( $row['option_value'] );
						if( empty( $tempvalue ) ){
						
							$customSql 	= "UPDATE  wp_options SET `option_value` = '$dataOptionValue' WHERE option_name = '".$optionArray['option_name']."'";
							$resultCustomSql 	= $connectionDestination->query( $customSql );
							
							if($resultCustomSql){
								
								$adsSuccessInCopy++;
								$adsCopyStatus = true;

							}else{

								$adsErrorInCopy++;
							}
						
						}else{
							//$adsErrorAlready++;

							//** update the ads content  **//
							$customSql 	= "UPDATE  wp_options SET `option_value` = '$dataOptionValue' WHERE option_name = '".$optionArray['option_name']."'";
							$resultCustomSql 	= $connectionDestination->query( $customSql );
							
							if($resultCustomSql){
								
								$adsSuccessInCopy++;
								$adsCopyStatus = true;

							}else{

								$adsErrorInCopy++;
							}
						}

						unset(	$dataOptionName );
						unset( 	$dataOptionValue );
					
						
					}else{
						
						//** Execute Query **//
						$adsSql 	= "INSERT INTO wp_options(`option_name`, `option_value`) values( '$dataOptionName',  '$dataOptionValue')";
						$resultadsInsert 	= $connectionDestination->query( $adsSql );
						
						if( $resultadsInsert ){
							
							$adsCopyStatus = true;
							$adsSuccessInCopy++;	

						}else{
							$adsErrorInCopy++;							
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