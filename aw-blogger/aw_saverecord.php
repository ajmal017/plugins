<?php

echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>';

if (isset($_POST["transfer"])) {

	$blog_name 	   	    = $_POST['domain_name'];

	$blog_path 	   	    = "/var/www/html/".$blog_name;

	$domain_url   	    = $_POST['domain_name_url'];

	$dom_alias  	    = $_POST['dom_alias'];

	$config_name  	    = $_POST['config_name'];

	$config_file_path 	= $_POST['config_file_path'];
	
	$site_url 		    = $domain_url."/";
	
	$require_path 	    = "/var/www/html/".$blog_name."/wp-config.php";
	
	require($require_path);
	
	$db_name 		    = DB_NAME;
	$db_user 		    = DB_USER;
	$db_pass 		    = DB_PASSWORD;
	$db_host 		    = DB_HOST;

	$wpdbs   		    = new wpdb($db_user,$db_pass,$db_name,$db_host);
	
	$sql_qry 		    = "UPDATE wp_options SET option_value = '".$site_url."' WHERE option_name = 'siteurl' OR option_name = 'home'";

	$wpdbs->query($sql_qry);

	$get_guid 			= "SELECT guid from wp_posts";

	$results_guid 		= $wpdbs->get_results($get_guid);
	
	$old_guid 			= "http://iris.scanmine.com/";

	$get_siteurl 		= "SELECT option_value FROM wp_options WHERE option_name = 'siteurl'";

	$results_guids 		= $wpdbs->get_results($get_siteurl);
	
	$new_guid 			= $results_guids;
	
	foreach ($new_guid as $values) {
	
		$siteurls 		= $values->option_value;
	
	}

	$output 		    = "aw_create_config ".$domain_url." ".$dom_alias." ".$blog_path." ".$config_name;

	file_put_contents(dirname(__FILE__).'/domain_access.log', print_r($output,true)."\n",FILE_APPEND);
	
	$output 			= shell_exec($output);		
	
	/*foreach ($results_guid as $value) {
	
		$guid_url 		= $value->guid;
		
		$replc_guid 	= str_replace("www.friidrettsnytt.com/", $old_guid, $guid_url);
		
		$updt_guid		= "UPDATE wp_posts SET guid = '".$replc_guid."' WHERE post_type = 'attachment'";

		$wpdbs->query($updt_guid);
	
	}*/

		
	

}
	header("Location:http://iris.scanmine.com/wp-content/plugins/aw-blogger/domain_transfer.php?save=recordsaved");	

?>