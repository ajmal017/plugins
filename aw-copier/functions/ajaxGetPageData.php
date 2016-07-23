<?php
	

	$serverPath = $_SERVER['DOCUMENT_ROOT']."/".$_GET['source']."/wp-config.php";
	require( $serverPath );

	//** File is used get all the Page data form the soruce url.  **// 
	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$mysql_database =  	str_replace('-', '_', $_GET['source'] );		//** As Received form Request. **//
	
	$connection  	= 	new mysqli($servername, $username, $password, $mysql_database);

	if ($connection->connect_error) {
		echo "Connection Error!";
	}else{
		$sql = "SELECT wp_posts.ID,wp_posts.post_title FROM `wp_posts` WHERE wp_posts.post_type = 'page' AND wp_posts.post_status = 'publish'";
			
		$result 	=	$connection->query($sql);

		$pageTitles = array();
		$dataArray 	= array();

		if( $result->num_rows > 0 ){
			
			while( $row = mysqli_fetch_assoc($result) ){
				$pageTitles[] 	=  $row['post_title'];				
				$dataArray[] 	=  $row;
			};
			
			if(count( $pageTitles ) > 0 ){ 
				$table 	=	'<div id="widgetoptiontable">';				
				$table .=  	'<table id="Aw_widgetList" width="290" border="0" cellpadding="2" cellspacing="0" style="font-size: 12px;">';
				
							for($i = 0; $i < count($pageTitles); $i++){
								
								$table .= 	'<tr>';

								$table .= 	'<td align="left" valign="top">';
									$table .=	'<input type="checkbox" name="pageTitle[]" class="aw_checkbox" value="'.$dataArray[$i]['ID'].'" >&nbsp;'.$pageTitles[$i];
								$table .=	'</td>';
								
								$table .= 	'</tr>';
							}  

				$table .= '<tr><td colspan="3" align="right"></td></tr></table>';
				$table .= 	'</div>';

				echo $table;
			}else{

				$table 	=	'<div id="widgetoptiontable" style="color: red;">';
					$table .=	'No Pages(s) Defined for the site.';		
				$table .= 	'</div>';
				
				echo $table;	
			}		


		}else{
			$table 	=	'<div id="widgetoptiontable" style="color: red;">';
				$table .=	'No  Pages(s) Defined for the site.';		
			$table .= 	'</div>';

			echo $table;
		}
		
		$connection->close();
	}
	

	
?>