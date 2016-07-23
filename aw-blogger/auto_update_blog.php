<?php
	
/*	error_reporting(E_ALL);
	ini_set('display_errors', 1);	
*/
	include_once '/var/www/html/wp-load.php';

	$site_created;

	$sites 	= 	'';
	$items 	=	array();

	//$handle = fopen("/var/www/html/go/sm/rest_wp.txt", "r"); 
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
   echo "<pre>";
	print_r( "create result<br>" );
	print_r( $itemsCreate );
	print_r( "Delete result<br>" );
	print_r( $itemsDelete );
	print_r( "update result<br>" );
	print_r( $itemsUpdate );
echo "</pre>";die;

	    fclose($handle);

	} else {
	   echo "Error In Opening File.";
	}

		$sites = implode(",", $itemsUpdate );


	/*if( $sites  !== '' ){

		$_POST['siteTemplate'] 		= 	'fotboll-halmstad';
		$_POST['site_config'] 		= 	$sites;
		$_SERVER['DOCUMENT_ROOT'] 	= 	'/var/www/html';
		$_SERVER['SERVER_NAME'] 	=	'iris.scanmine.com';		

		require_once dirname(__FILE__)."/functions/create-site.php";		
	}*/


?>