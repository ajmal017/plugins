<?php
	
	$serverPath = $_SERVER['DOCUMENT_ROOT']."/".$_GET['source']."/wp-config.php";
	require( $serverPath );

	//** File is used get all the wigets data form the soruce url.  **// 
	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$mysql_database =  	str_replace('-', '_', $_GET['source'] );		//** As Received form Request. **//
	
	$connection  	= 	new mysqli($servername, $username, $password, $mysql_database);

	if ($connection->connect_error) {
		echo "Connection Error!";
	}else{
		$sql = "SELECT * FROM wp_options WHERE option_name LIKE 'aw_%'";
		
		$result 	=	$connection->query($sql);
		
		if( $result->num_rows > 0 ){
			
			//**Ads Option **//
			$adsOption  = array();
			
			while( $row	= mysqli_fetch_assoc($result) ){

				//** include Footer Ad**//
				if(	$row['option_name'] == 'aw_sidebar_top_ad' ){
					
					if( !empty( $row['option_value'] ) ){
						$adsOption[] =  	$row['option_name'];
					}					
				}

				//** include Footer Ad**//
				if(	$row['option_name'] == 'aw_main_area_ad' ){
					
					if( !empty( $row['option_value'] ) ){
						$adsOption[] =  	$row['option_name'];
					}					
				}

				//** include Footer Ad**//
				if(	$row['option_name'] == 'aw_secondary_left_ad' ){
					
					if( !empty( $row['option_value'] ) ){
						$adsOption[] =  	$row['option_name'];
					}					
				}

				//** include Footer Ad**//
				if(	$row['option_name'] == 'aw_secondary_top_right_ad' ){
					
					if( !empty( $row['option_value'] ) ){
						$adsOption[] =  	$row['option_name'];
					}					
				}

				//** include Footer Ad**//
				if(	$row['option_name'] == 'aw_secondary_bottom_right_ad' ){
					
					if( !empty( $row['option_value'] ) ){
						$adsOption[] =  	$row['option_name'];
					}					
				}

				//** include Footer Ad**//
				if(	$row['option_name'] == 'aw_techsub_top_ad' ){
					
					if( !empty( $row['option_value'] ) ){
						$adsOption[] =  	$row['option_name'];
					}					
				}

				//** include Footer Ad**//
				if(	$row['option_name'] == 'aw_footer_left_ad' ){
					
					if( !empty( $row['option_value'] ) ){
						$adsOption[] =  	$row['option_name'];
					}					
				}

				//** include Footer Ad**//
				if(	$row['option_name'] == 'aw_footer_right_ad' ){
					
					if( !empty( $row['option_value'] ) ){
						$adsOption[] =  	$row['option_name'];
					}					
				}

				//** include Footer Ad**//
				if(	$row['option_name'] == 'aw_footer_ad' ){
					
					if( !empty( $row['option_value'] ) ){
						$adsOption[] =  	$row['option_name'];
					}					
				}

				//** include Right Ad**//
				if(	$row['option_name'] == 'aw_right_ad' ){
					if( !empty( $row['option_value'] ) ){
						$adsOption[] =  	$row['option_name'];
					}					
				}

				//** include Top Ad**//
				if(	$row['option_name'] == 'aw_top_ad' ){
					if( !empty( $row['option_value'] ) ){
						$adsOption[] =  	$row['option_name'];
					}					
				}


				//** include Single Page Ad**//
				if(	$row['option_name'] == 'aw_single_footer_ad' ){
					if( !empty( $row['option_value'] ) ){
						$adsOption[] =  	$row['option_name'];
					}					
				}
				
			}


		
			
			if(count($adsOption) >0 ){ 
				$table 	=	'<div id="widgetoptiontable">';				
				$table .=  	'<table id="Aw_CustomList" width="290" border="0" cellpadding="2" cellspacing="0" style="font-size: 12px;">';
				
							for($i = 0; $i < count($adsOption); $i++){
								
								$table .= 	'<tr>';

								$table .= 	'<td align="left" valign="top">';
									$table .=	'<input type="checkbox" name="adsTitle[]" class="aw_checkbox" value="'.$adsOption[$i].'" >&nbsp;'.ucwords( str_replace('_', ' ', $adsOption[$i]) );
								$table .=	'</td>';
								
								$table .= 	'</tr>';
							}  

				$table .= '<tr><td colspan="3" align="right"></td></tr></table>';
				$table .= 	'</div>';

				echo $table;
			}else{

				$table 	=	'<div id="widgetoptiontable" style="color: red;">';
					$table .=	'No Ads Defined for the site.';		
				$table .= 	'</div>';
				
				echo $table;	
			}		


		}else{
			$table 	=	'<div id="widgetoptiontable" style="color: red;">';
				$table .=	'No Ads Defined for the site.';		
			$table .= 	'</div>';

			echo $table;
		}
		
		$connection->close();
	}
	

	
?>