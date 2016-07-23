<?php
	
	$serverPath = $_SERVER['DOCUMENT_ROOT']."/".$_GET['source']."/wp-config.php";
	
	require( $serverPath );

	//** File is used get all the wigets Link data form the soruce url.  **// 
	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$mysql_database =  	str_replace('-', '_', $_GET['source'] );		//** As Received form Request. **//
	
	$connection  	= 	new mysqli($servername, $username, $password, $mysql_database);

	if ($connection->connect_error) {
		$table 	=	'<div id="widgetoptiontable" style="color: red;">';
			$table .=	'Error In Database Connection.';		
		$table .= 	'</div>';
				
		echo $table;	
	}else{
		
		$sql = "SELECT * FROM wp_options WHERE option_name='widget_links'";
		
		$result 	=	$connection->query($sql);


		if( $result->num_rows > 0 ){

			
				$table 	=	'<div id="widgetoptiontable">';				
				$table .=  	'<table id="Aw_widgetList" width="290" border="0" cellpadding="2" cellspacing="0" style="font-size: 12px;">';
				
				$table .= 	'<tr>';

				$table .= 	'<td align="left" valign="top">';
					$table .=	'<input type="checkbox" name="blogrollTitle[]" class="aw_checkbox" value="1" >&nbsp;Copy All Blogrolls';
				$table .=	'</td>';  	
				
				$table .= 	'</tr>';

				$table .= '<tr><td colspan="3" align="right"></td></tr></table>';
				$table .= 	'</div>';

				echo $table;
			

		}else{
			$table 	=	'<div id="widgetoptiontable" style="color: red;">';
				$table .=	'No BlogRoll Defined for the site.';		
			$table .= 	'</div>';

			echo $table;
		}
		
		$connection->close();
	}
	

	
?>