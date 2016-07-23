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
			
			//**Custom Option **//
			$customOption  = array();
			
			while( $row	= mysqli_fetch_assoc($result) ){
				
				//** Exclude Slider Status Option**//
				if(	$row['option_name'] == 'aw_slider_status' ){
					continue;
				}

				//** Exclude Slider Status Counter**//
				if(	$row['option_name'] == 'aw_slide_counter' ){
					continue;
				}

				//** Exclude Footer Ad**//
				if(	$row['option_name'] == 'aw_footer_ad' ){
					continue;
				}

				//** Exclude Right Ad**//
				if(	$row['option_name'] == 'aw_right_ad' ){
					continue;
				}

				//** Exclude Top Ad**//
				if(	$row['option_name'] == 'aw_top_ad' ){
					continue;
				}


				//** Exclude Single Page Ad**//
				if(	$row['option_name'] == 'aw_single_footer_ad' ){
					continue;
				}
				

				if( !empty( $row['option_value'] ) ){
					$customOption[] =  	$row['option_name'];
				}
			}
			

			$sourceLogoPath 	= 	$_SERVER['DOCUMENT_ROOT']."/".$_GET['source']."/wp-content/plugins/aw-customizer/logo/logo.png";
			if(file_exists( $sourceLogoPath )){
				$customOption[] = 'logo';
			}

			$sourceFaviconPath	=	$_SERVER['DOCUMENT_ROOT']."/".$_GET['source']."/wp-content/plugins/aw-customizer/icon/favicon.png";
			if(file_exists( $sourceFaviconPath )){
				$customOption[] = 'favicon';
			}

				
			if(count($customOption) >0 ){ 
				$table 	=	'<div id="widgetoptiontable">';				
				$table .=  	'<table id="Aw_CustomList" width="290" border="0" cellpadding="2" cellspacing="0" style="font-size: 12px;">';
				
							for($i = 0; $i < count($customOption); $i++){
								
								$table .= 	'<tr>';

								$table .= 	'<td align="left" valign="top">';
									$table .=	'<input type="checkbox" name="customTitle[]" class="aw_checkbox" value="'.$customOption[$i].'" >&nbsp;'.ucwords( str_replace('_', ' ', $customOption[$i]) );
								$table .=	'</td>';
								
								$table .= 	'</tr>';
							}  

				$table .= '<tr><td colspan="3" align="right"></td></tr></table>';
				$table .= 	'</div>';

				echo $table;
			}else{

				$table 	=	'<div id="widgetoptiontable" style="color: red;">';
					$table .=	'No Custom Options Defined for the site.';		
				$table .= 	'</div>';
				
				echo $table;	
			}		


		}else{
			$table 	=	'<div id="widgetoptiontable" style="color: red;">';
				$table .=	'No Custom Options Defined for the site.';		
			$table .= 	'</div>';

			echo $table;	
		}
		
		$connection->close();
	}
	

	
?>