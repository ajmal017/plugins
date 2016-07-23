<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('memory_limit','1024M');

require '/var/www/html/wp-content/plugins/aw-blogger/class/utilitiesFunctions.php';
if(!class_exists('ConfigReader')){
  require_once '/var/www/html/wp-content/plugins/aw-blogger/configReader/ConfigReader.php';
}

$sites 	= 	'';
$items 	=	array();

$itemsCreate = array();
$itemsDelete = array();
$itemsUpdate = array();
$itemsitemes = array();


$handle = fopen("/var/www/html/go/sm/rest_wp.txt", "r");


if ($handle) {
	while (( $line = fgets($handle)) !== false) {	  		
       	$item = (string) $line;

       	$urlParts 	= 	explode(" ", $item );
       	$action 	= 	$urlParts[0]; 
       	$url 		=	$urlParts[1]; 

       	
       	if( $action == 'create' ){
       		$itemsCreate[]	=	$url;
       	}elseif($action == 'delete'){
       		$itemsDelete[]	=	$url;
       	}elseif ($action == 'update') {
       		$itemsUpdate[]	=	$url;
       	}
   }

    fclose($handle);

} else {
   echo "Error In Opening File.";
}

echo "<pre>";
	print_r( "create result<br>" );
	print_r( $itemsCreate );
	print_r( "Delete result<br>" );
	print_r( $itemsDelete );
	print_r( "update result<br>" );
	print_r( $itemsUpdate );
echo "</pre>";


foreach( $itemsDelete as $site ) {
     
      $blogName = utilitiesFunctions::getBlogNameFormConfig( $site ); 
      
     if( $blogName ){ 
        $delStatus = utilitiesFunctions::deleteSite( $blogName );
        
        if( $delStatus ){
            
            echo "DELETED Successfully $blogName <br>";
        }
     }else{
        echo "<br/> Error in Config:  $site<br/>";
     }
}



function removeDirectory( $dir ){
  echo "Removing Directory: ".$dir."<br>";
    if (is_dir($dir)){ // ensures that we actually have a directory
        $objects = scandir($dir); // gets all files and folders inside
          foreach ($objects as $object){

              if ($object != '.' && $object != '..'){

                  if (is_dir($dir . '/' . $object)){
                      // if we find a directory, do a recursive call
                      removeDirectory($dir . '/' . $object);                  
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
?>