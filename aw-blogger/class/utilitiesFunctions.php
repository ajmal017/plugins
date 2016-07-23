<?php 
/**
* utilitiesFunctions.php
*/
/*error_reporting(E_ALL);
ini_set('display_errors', 1);
*/
class utilitiesFunctions{	

	public static function getBloglist(){
		global $wpdb;

		$sql 	 = "SELECT * FROM wp_aw_blog_sites";
		$results  = $wpdb->get_results( $sql, OBJECT );

		$html = '';
		$html .= "<select name='siteTemplate' id='directories'>";
		foreach ( $results as $directory ) {
			$html .= "<option value='".$directory->site_name."'>".$directory->site_name."</option>";
		}
		$html .= "</select>";

		$html .= "<script>jQuery('#directories').select2();</script>";

		return $html;
	}

	//** funtion to the template path. **//
	public static function getSourceTemplate( $siteTemplate = null ){
		
		if( $siteTemplate == null OR $siteTemplate == '' ){
			return false;
		}
		$wordpress_folder = $_SERVER['DOCUMENT_ROOT']."/".$siteTemplate; 	
		return $wordpress_folder;
		// echo "BlogTemplate: $wordpress_folder";
	}

	//** function to get the template database name. **//
	public static function getSourceDb(  $siteTemplate = null ){
		if( $siteTemplate == null OR $siteTemplate == '' ){
			return false;
		}
		$siteDatabase = str_replace('-', '_', $siteTemplate );
		return $siteDatabase;
		// echo "Blog Database: $siteDatabase";
	}

	//** function to get the template template language. **//
	public static function getBlogLanguage( $siteTemplate = null ){
		
		if( $siteTemplate == null OR $siteTemplate == '' ){
			return false;
		}

		$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
		$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
		$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//

		$mysql_database =  str_replace('-', '_', $siteTemplate );

		$conn = new mysqli($servername, $username, $password, $mysql_database);

		if ($conn->connect_error) {
			return false;
		}else{
			$sql = "SELECT * FROM wp_options WHERE option_name = 'WPLANG'";
			$result = mysqli_query( $conn,$sql );

			if( $result->num_rows > 0 ) {
				$row = mysqli_fetch_assoc($result);
				// $row['option_value']; =nb_NO
				$language =  '';
				if($row['option_value'] == ''){
					$language =  'English';

				}else if ($row['option_value'] ==  'en_GB' ) {
					$language =  'English';

				}else if ( $row['option_value'] ==  'sv_SE' ) {
					$language =  'Swedish';

				}else if ( $row['option_value'] ==  'nb_NO' ) {
					$language =  'Norwegian';
				}

				// echo "Blog Language: ".$language;	
				return $language;
			}else{
				return "English";
				// echo "Blog Language: English";	
			}

		}

		$conn->close();
	}

	public static function deleteSite( $blogName ){

		$servername 	=	'localhost'; 			//** As Defined in Wp_config. **//
		$username 		= 	'iris';			//** As Defined in Wp_config. **//
		$password 		= 	'For$Db!php5'; 		//** As Defined in Wp_config. **//
		$databaseName	=	str_replace('-', '_', $blogName);

		$con = new mysqli( $servername, $username, $password );
		
		if ($con->connect_error) {
			return false;
		}else{
			
			//** Delete Database. **//
			$dropDB 	=	"DROP DATABASE $databaseName";
			$delRes 	=	$con->query( $dropDB  );
			$response = utilitiesFunctions::removeFormList( $blogName );

	        if( $delRes ){	
	        	$dir = '/var/www/html/'.$blogName."/";
    			utilitiesFunctions::removeDirectory( $dir );
	        	
	        	$con->close();
				return true;	
			}else{
				$con->close();
				return false;
			}
		}

	}

	//** Function get the Blog Name form the config file. **//
	public static function getBlogNameFormConfig( $config_file ){

		$siteName = '';
		$newurl    = trim( $config_file );

		$reader   =   new ConfigReader( $newurl );
		$reader->parse();

	  //** Loop All Error in Config file. **//      
		foreach ($reader->getErrors() as $error){

			$errorMessages[] = "<b style='color:red'>$error</b>";
		}     

		if( !empty( $errorMessages ) ){
			unset($reader);
			return false;
		}else{
			$siteName     = (string) $reader->getProperty('address');
		}  

		unset($reader);  

		return $siteName;
	}

	//** Function to remove the Blog entry for the Aw-Blogger list.  **//
	public static function removeFormList( $blogName ){
  		
		$servername 	=	'localhost'; 			//** As Defined in Wp_config. **//
		$username 		= 	'iris';			//** As Defined in Wp_config. **//
		$password 		= 	'For$Db!php5'; 		//** As Defined in Wp_config. **//
		$databaseName	=	'scanmine';

		$con = new mysqli( $servername, $username, $password, $databaseName );
		
		if ($con->connect_error) {
			return false;
		}else{
			//** code to delete the Blog form Aw-Blogger**//
			$sql = "DELETE FROM wp_aw_blog_sites WHERE `site_name` = '".$blogName."'";
			$result = mysqli_query( $con, $sql );

			return true;

		}
	}

	public static function removeDirectory( $dir ){
		if (is_dir($dir)){ // ensures that we actually have a directory
	    	$objects = scandir($dir); // gets all files and folders inside
	        foreach ($objects as $object){

	            if ($object != '.' && $object != '..'){

	                if (is_dir($dir . '/' . $object)){
	                    // if we find a directory, do a recursive call
	                    utilitiesFunctions::removeDirectory($dir . '/' . $object);
	                
	                }else{
	                    // if we find a file, simply delete it
	                    unlink($dir . '/' . $object);
	                }
	            }
	        }
	        // the original directory is now empty, so delete it
	        rmdir( $dir );
	    }
	}
	
} //** class ends here. **//	