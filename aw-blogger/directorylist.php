<?php

error_reporting(E_ALL);
ini_set('display_errors', 1 );

include '/var/www/html/wp-load.php';

global $wpdb;

$sql 	 = "SELECT * FROM wp_aw_blog_sites";
$results  = $wpdb->get_results( $sql, OBJECT );

echo "<select>";
foreach ( $results as $directory ) {
	/*echo "<pre>";
	print_r( $directory->ID."<br>" );
	print_r( $directory->site_name );
	echo "</pre>";*/
	echo "<option>".$directory->site_name."</option>";

}
echo "</select>";



foreach ( $results as $directory ) {
	echo "<pre>";
	print_r( $directory->ID."<br>" );
	print_r( $directory->site_name );
	echo "</pre>";
}



/*
$directories = glob($somePath . './*' , GLOB_ONLYDIR);
// print_r($directories);

$wordpressBlogs = array();

foreach ($directories as $directory ) {
	
	$filename = $directory.'/wp-config.php';

	if (file_exists( $filename ) ) {
		// print_r( $directory );
		// print_r("<br>");

		$wordpressBlogs[] = $directory;
	    
	    // echo "The file $filename exists";
	    // print_r("<br>");
	} else {
		continue;
	    echo "The file $filename does not exist";
	    print_r("<br>");
	}
}

 
	echo "<pre>";
	print_r( $wordpressBlogs );
	echo "</pre>";	
*/
?>

