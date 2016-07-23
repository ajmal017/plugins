<?php
	

	error_reporting(E_ALL); 
	ini_set('display_errors', '1');

	// $id = 11;	
	// echo $sql 		=	"DELETE FROM wp_aw_blog_sites WHERE  id= $id";

	// $DelRowRes 	=	$wpdb->query($sql);	

	// if( $DelRowRes ){
	// 	echo "Deleted";
	// }else{
	// 	echo "Not Deleted.";
	// }
	// die;
	// echo "trying to Delete";
	// rrmdir('/var/www/artworldwebsolutions/santorini/');
	// echo "done";
	// exit();
	global $wpdb;
	$servername 	= 	DB_HOST;
	$username 		= 	DB_USER;
	$password 		= 	DB_PASSWORD;

	if( isset($_GET['action']) && $_GET['action'] == 'delete'){

		$id 		= 	trim( $_GET['id'] );
		$sql 		=	"SELECT * FROM wp_aw_blog_sites WHERE  id= $id";

		$result 	=	$wpdb->get_results($sql);

	
		if( !empty( $result ) ){

			$mysql_database 	= 	$dbName =	str_replace('-', '_', $result[0]->site_slug );
			$siteFolder 		= 	$result[0]->site_slug;	
				
			$conn = new mysqli( $servername, $username, $password );

			if( $conn ){
				
				//** Delete Database. **//
				$dropDB 	=	"DROP DATABASE $mysql_database";
				$delRes 	=	$conn->query( $dropDB  );

				if( $delRes ){
					
					//** Delete Reccord Form Table. **//
					$sql 		=	"DELETE FROM wp_aw_blog_sites WHERE  id= $id";
					$wpdb->query($sql);	

					//** DELETE FOLEDERS **//
					$pathToFolder 	= 	$_SERVER['DOCUMENT_ROOT']."/".$siteFolder."/"; 	
					rrmdir($pathToFolder);
					// $command = "rm - rf ". $pathToFolder;
					
					

					//** Return to List Page On success**//
					$url = site_url();
					$url1 =	$url."/wp-admin/admin.php?page=aw_blogger_list";						
					echo "<script>window.location = '".$url1."&success=true';</script>";		
					exit();

				}else{

					//** Return to List Page On error. **//
					$url = site_url();
					$url1 =	$url."/wp-admin/admin.php?page=aw_blogger_list";						
					echo "<script>window.location = '".$url1."&error=true';</script>";		
					exit();

				}

			}else{
				
					//** Return to List Page On error. **//
					$url = site_url();
					$url1 =	$url."/wp-admin/admin.php?page=aw_blogger_list";						
					echo "<script>window.location = '".$url1."&dbErr=true';</script>";		
					exit();
			
			}

		}else{
			
			$url = site_url();
			$url1 =	$url."/wp-admin/admin.php?page=aw_blogger_list";						
			echo "<script>window.location = '".$url1."&siteErr=true';</script>";		
			exit();

		}
	
	}else{

		$url = site_url();
		$url1 =	$url."/wp-admin/admin.php?page=aw_blogger_list";						
		echo "<script>window.location = '".$url1."&siteErr=true';</script>";		
		exit();	
	}	

	//** Delete folders recursiverly.  **//	
	function delete_folder($folder) {
	    $glob = glob($folder);
	    
	    foreach ($glob as $g) {
	        
	        if (!is_dir($g)) {
	            unlink($g);
	        } else {
	            delete_folder("$g/*");
	            rmdir($g);
	        }
	    }

	}


function rrmdir($dir)
{
    if (is_dir($dir)) // ensures that we actually have a directory
    {
        $objects = scandir($dir); // gets all files and folders inside
        foreach ($objects as $object)
        {
            if ($object != '.' && $object != '..')
            {
                if (is_dir($dir . '/' . $object))
                {
                    // if we find a directory, do a recursive call
                    rrmdir($dir . '/' . $object);
                }
                else
                {
                    // if we find a file, simply delete it
                    unlink($dir . '/' . $object);
                }
            }
        }
        // the original directory is now empty, so delete it
        rmdir($dir);
    }
}
	


	
		
?>