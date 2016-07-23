<?php
	
/*	error_reporting(E_ALL);
	ini_set('display_errors', 1);	
*/
	include_once '/var/www/html/wp-load.php';

	$site_create;

	if(isset($_REQUEST['url'])){
		$_POST['siteTemplate'] 		= 	'gold-silver';
		$_POST['site_config'] 		= 	$_REQUEST['url'];
		$_SERVER['DOCUMENT_ROOT'] 	= 	'/var/www/html';
		$_SERVER['SERVER_NAME'] 	=	'iris.scanmine.com';		

		require_once dirname(__FILE__)."/functions/create-site.php";
	}else{
		echo "error";
	}

?>