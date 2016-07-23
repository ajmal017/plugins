<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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


$handle = fopen("/var/www/html/split_url.txt", "r");


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


//** Code to Block create Blogs **//
/*createBlogs( $itemsCreate );*/

//** code block Dele blocks**//
deleteBlogs( $itemsDelete );


//** function to delete blogs**//
function deleteBlogs( $itemsDelete ){
	foreach( $itemsDelete as $site ) {
     
      	$blogName = utilitiesFunctions::getBlogNameFormConfig( $site ); 
      	$dir = '/var/www/html/'.$blogName."/";
	    
	    if( $blogName ){ 
			$delStatus = utilitiesFunctions::deleteSite( $blogName );
			if( $delStatus ){
			    echo "<br>DELETED Successfully $blogName <br>";
			}
		}else{
			echo "<br/> Error in Config:  $site<br/>";
		}
	}	
}

//**  Function to Delete **//
function createBlogs( $itemsCreate ){
	
	$site_created;

	$sites 	= 	'';
	$items 	=	array();

	$sites = implode(",", $itemsCreate );

	if( !empty($sites ) ){
		if( $sites  !== '' ){	

			$_POST['siteTemplate'] 		= 	'fotboll-halmstad';
			$_POST['site_config'] 		= 	$sites;
			$_SERVER['DOCUMENT_ROOT'] 	= 	'/var/www/html';
			$_SERVER['SERVER_NAME'] 	=	'iris.scanmine.com';		

			require_once dirname(__FILE__)."/functions/create-site.php";		
		}
	}	
}

?>