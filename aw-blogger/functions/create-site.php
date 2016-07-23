<?php
/*
 error_reporting(E_ALL);
 ini_set('display_errors', 1);*/

global $site_created;
require dirname(__FILE__)."/BlogManagerNew.php";

///////////THIS FUNCTIONALITY WILL CREATE A NEW WORDPRESS SITE /////////


$siteTemplate 		=	$_POST['siteTemplate'];
$wordpress_folder 	= 	utilitiesFunctions::getSourceTemplate( $_POST['siteTemplate'] );

//** Check if source directory exists. **//
if (file_exists( $wordpress_folder ) &&  is_dir( $wordpress_folder ) ){

   	$site_config 	= 	$_POST['site_config'];
	$urls 			= 	explode( ',', $site_config );
   	$count 			= 	count($urls);
    $keys 			= 	array_keys($urls);
	$manager 		= 	new BlogManager();
	$site_info 		= 	array();
	$newurl 		= 	"";
	$urlError 		= 	false;
	$errorMessages 	= 	array();

	//** Loop Through All config Files. **//
	for( $i = 0; $i < $count; $i++ ){

		$newurl		=	trim($urls[$keys[$i]]);
		$reader 	= 	new ConfigReader( $newurl );
		$reader->parse();

		//** Loop All Error in Config file. **//
		foreach ($reader->getErrors() as $error){

			$errorMessages[] = "<b style='color:red'>$error</b>";
		}

		if( !empty( $errorMessages ) ){

			echo "<br>Error in Reading config URL: ";
			foreach($errorMessages as $errorMessage){
				echo "<pr>".$errorMessage."</p>";
				echo "<hr>";
				unset( $reader );
				unset( $error );
				continue;
			}

		}else{

			$site_info['address'] 		= (string) $reader->getProperty('address');
			$site_info['title'] 		= (string) $reader->getProperty('title');
			$site_info['email'] 		= (string) $reader->getProperty('owner');
			$site_info['theme'] 		= (string) $reader->getProperty('theme');
			$site_info['template'] 		= (string) $reader->getProperty('template');
			$site_info['description'] 	= (string) $reader->getProperty('description');
			$site_info['topic'] 		= (string) $reader->getProperty('topic');

			//** Code Added By G0947 on 11.02.2015 **//
			//** Action: Check if Site is Already Created. **//

			global $wpdb;
			$errorFlag 	=	false;
			$siteName 	= 	trim($site_info['address']);
			$result 	=	$wpdb->get_results("SELECT site_slug FROM wp_aw_blog_sites");

			foreach ($result as $value) {

				if( $siteName == $value->site_slug ){
					$errorFlag = true;
				}

			}

			if( $errorFlag == true ){

				$url 	= 	site_url();
				$url1 	=	$url."/wp-admin/admin.php?page=awblogger";

				echo "<br /><p style='text-align: center; Color: red; font-size: 18px;'> <b style='center; Color: black;'>$siteName</b> is Already Present. </p> <br />";
				echo "<hr>";
				continue;

			}
			//** Block Ends Here. **//

			/////main code from here///////////
			$site_slug 			= 	trim( $site_info['address'] );
			$destinationPath 	= 	$_SERVER['DOCUMENT_ROOT']."/".$site_slug;

			if ( !file_exists( $destinationPath ) && !is_dir( $destinationPath ) ) {

			    //** create Destination Directory. **//
			    if (false === @mkdir( $destinationPath, 0775, true )) {

			   		echo "<br /><p style='text-align: center; Color: red; font-size: 18px;'> <br>Unable to create directory <b style='center; Color: black;'>$site_slug;</b></p> <br />";
					echo "<hr>";
					continue;

			    }

			    //** Copy File form source Blog to Destination Blog. **//
			    $result 	=	 xcopy( $wordpress_folder, $destinationPath, 0775 );

				if( $result ){

					/* code block to update the File Premssion starts */
					$commandChown    = "chown -R artworld:testing ".$destinationPath."/";
					$filePermissions = "find ".$destinationPath." -type f -exec chmod -R 0664 {} \;";
					$dirPermissions  = "find ".$destinationPath." -type d -exec chmod -R 0775 {} \;";

					file_put_contents(dirname(__FILE__)."/cmd_log/updater.log", print_r('#!/bin/bash', true) );
					file_put_contents(dirname(__FILE__)."/cmd_log/updater.log", print_r("\n", true),FILE_APPEND );

					file_put_contents(dirname(__FILE__)."/cmd_log/updater.log", print_r($commandChown, true),FILE_APPEND );
					file_put_contents(dirname(__FILE__)."/cmd_log/updater.log", print_r("\n", true),FILE_APPEND );

					file_put_contents(dirname(__FILE__)."/cmd_log/updater.log", print_r($filePermissions, true),FILE_APPEND );
					file_put_contents(dirname(__FILE__)."/cmd_log/updater.log", print_r("\n", true),FILE_APPEND );

					file_put_contents(dirname(__FILE__)."/cmd_log/updater.log", print_r($dirPermissions, true),FILE_APPEND );
					file_put_contents(dirname(__FILE__)."/cmd_log/updater.log", print_r("\n", true),FILE_APPEND );

					/* code block to update the File Premssion ends */

					//** Path to the destination blog Upload folder. **//
					$destinationUplaod 	=	$destinationPath.'/wp-content/uploads';

					//** Delete Uplaod Folder of the Template Blog**//
					rrmdir( $destinationUplaod );

					//** Update Destination Config File. **//
				   	UpdateConfigFile( $destinationPath, $site_slug );

				   	//** Add HTACCESS FILE  **//
				   	addHTACCESS( $site_slug );

				   	//** Create Destination Database **//
				   	$resultDB = createDestinationDatabse( $site_slug );
				   	if(	 $resultDB ){
				   		//** Copy Datase **//
				   		//** code updated on 29.08.2015. **//gurjeet
				   		$sourceDB 		=	utilitiesFunctions::getSourceDb( $siteTemplate );
				   		$destinationDB 	=   str_replace('-', '_', $site_slug );
				   		$resultDBCopy 	= copyDatabase( $sourceDB , $destinationDB );

				   		if(	 $resultDBCopy ){

							//** Delete Dummy Posts Form Destination.  **//
							deleteDummyPosts( $destinationDB );

							/*delete all users except admin*/
							deleteUsers( $destinationDB );

				   			//** Update site home and url **//
				   			$resultOPtions 	  = 	 updateSiteOptions( $site_slug, $site_info['email'], $site_info['title'], $site_info['description'], $site_info['topic'] );

				   			if(	$resultOPtions == false ){

				   				echo "<br /><h1>Unable to update wordpress settings. </h1>";
								echo"<hr>";
								//** rollback changes. **//
								//delete created folders.
								rrmdir($destinationPath);
								//delete database.
								dropDatabase(  $destinationDB  );
								continue;

				   			}

				   			//** Update Master Theme.**//
				   			//** list of input arguments **//
							$args['site_name']		=	$site_slug;
							$args['site_slug'] 		=	$site_slug;
							$args['site_theme'] 	=	$site_info['theme'];
							$args['site_url'] 		=	$resultOPtions;
							$args['site_language'] 	=	getBlogLanguage(  $siteTemplate );

							//** update blog list function call. **//
							update_blog_list( $args );

							//** Update Posts From Freed.**//
							$manager->createNewFeedSiteFromConfig( $newurl );

							//** activate theme plugins**//
							// updateActivePlugins( $sourceDB, $destinationDB );

							//** Create menu top menu for the theme. **//
							createTopMenu( $destinationDB );

							//** set the permalink sturcture **//
							setPermalinkStructure( $destinationDB );

							echo "<br /><p style='border: 1px solid;color: #008000;font-size: 20px;    text-align: center;'><b>Wordpress Site <a href='".$resultOPtions."'>". $site_slug ."</a> Created Successfully!</b> <br /><hr>";
							$site_created 	=	true;

							continue;

				   		}else{
				   			echo "<br /><h1>Error in Copying Database. </h1>";
							echo"<hr>";
							//** rollback changes. **//
							//delete created folders
							rrmdir($destinationPath);
							//** Drop Database **//
							dropDatabase(  $destinationDB  );
							continue;

				   		}

				   	}else{

				   		//** rollback changes. **//
						//delete created folders.
						rrmdir($destinationPath);

				   		echo "<br /><h1>Error in Create Database. </h1>";
						echo"<hr>";
						continue;

				   	}

				}else{
				   	echo "<br /><h1>Error In Extraction Files. </h1>";
					echo"<hr>";
					//** rollback changes. **//
					//delete created folders.
					rrmdir($destinationPath);
					continue;

				}

			}else{

				echo "<br /><h1>Destination Blog Folder Already Present on Server. </h1>";
				echo"<hr>";
				continue;

			}
		}
	}//** config file loop ends here**//

}else{
  		echo("<br><h1>Source Template could not Found.</h1><br>");
}

//** Update the record for newly created site. **//
//**  @args: array as arguments. all the fields to be updated. **//
function update_blog_list( $args ){

	global $wpdb;

	//** list of input arguments **//
	$site_name		=	$args['site_name'];
	$site_slug 		=	$args['site_slug'];
	$site_theme 	=	$args['site_theme'];
	$site_url 		=	$args['site_url'];
	$site_language  =  	$args['site_language'];

	$sql 			=	"INSERT INTO wp_aw_blog_sites ( `site_name`,`site_slug`,`site_theme`,`site_url`, `site_language`) values( '$site_name', '$site_slug', '$site_theme', '$site_url', '$site_language')";
	$wpdb->query( $sql );

}//** Function ends here. **//

//** Function to clone database.  **//
function copyDatabase( $sourceDB, $destinationDB ){

    $message 		= 	array();
	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//

    $connect2  		= mysqli_connect( $servername, $username , $password , $destinationDB );

    if (mysqli_connect_errno()){

    	echo "Destination Database Not Found:  $destinationDB";
        return false;

    }

    set_time_limit(0);

    $connect 		= 	mysqli_connect( $servername, $username , $password , $sourceDB );

    if (mysqli_connect_errno()){

    	echo "Source Database Not Found:  $sourceDB";
        return false;

    }

    $tables 		=	 mysqli_query( $connect,"SHOW TABLES FROM $sourceDB");

    while ($line 	=    mysqli_fetch_row($tables)) {

        $tab 		= 	 $line[0];

        mysqli_query($connect, "DROP TABLE IF EXISTS $destinationDB.$tab");
        mysqli_query($connect, "CREATE TABLE $destinationDB.$tab LIKE $sourceDB.$tab") or die(mysql_error());
        mysqli_query($connect, "INSERT INTO $destinationDB.$tab SELECT * FROM $sourceDB.$tab");

        $message[] 	= 	 "Table: <b>" . $line[0] . " </b>Done<br>";
    }

    return $message;
}

//**Function to copy files. **//
function xcopy($source, $dest, $permissions = 0775){

    // Check for symlinks
    if ( is_link($source) ) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest, $permissions);
    }

    // Loop through the folder
    $dir 		=	dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        xcopy("$source/$entry", "$dest/$entry", $permissions);
    }

    // Clean up
    $dir->close();
    return true;
}

function serialData(){
	echo "<h1>Serial data</h1>";
}

//** Function to getTemplate **//
//** function returns the path of source directory. **//
/*function getSourceTemplate(  $siteTemplate ){

	$wordpress_folder 		= '';
	if( $siteTemplate  == 1){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'templates/english/template_one_en';
	}else if( $siteTemplate  == 2 ){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'templates/swedish/template_one_sw';
	}else if( $siteTemplate  == 3){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'templates/norwegian/template_one_no';
	}else if( $siteTemplate  == 4){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'templates/english/template_two_en';
	}else if( $siteTemplate  == 5){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'templates/swedish/template_two_sw';
	}else if( $siteTemplate  == 6){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'templates/norwegian/template_two_no';
	}else if( $siteTemplate  == 7){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'templates/english/template_three_en';
	}else if( $siteTemplate  == 8){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'templates/swedish/template_three_sw';
	}else if( $siteTemplate  == 9){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'templates/norwegian/template_three_no';
	}else if( $siteTemplate  == 10 ){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'templates/english/template_multi_en';
	}else if( $siteTemplate  == 11 ){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'templates/swedish/template_multi_sw';
	}else if( $siteTemplate  == 12 ){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'templates/norwegian/template_multi_no';
	}else if( $siteTemplate  == 13 ){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'/templates/genesis/newspro';
	}else if( $siteTemplate  == 14 ){
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'/templates/genesis/widget_template';
	}else{
		$wordpress_folder 	= $_SERVER['DOCUMENT_ROOT']."/".'templates/english/template_one_en';
	}

	return $wordpress_folder;

}*///** function ends here. **//

//** function to get the source database name **//
/*function getSourceDb(  $siteTemplate ){

	$wordpress_folder 		= '';

	if( $siteTemplate  == 1){
		$wordpress_folder 	= 'template_one_en';
	}else if( $siteTemplate  == 2 ){
		$wordpress_folder 	= 'template_one_sw';
	}else if( $siteTemplate  == 3){
		$wordpress_folder 	= 'template_one_no';
	}else if( $siteTemplate  == 4){
		$wordpress_folder 	= 'template_two_en';
	}else if( $siteTemplate  == 5){
		$wordpress_folder 	= 'template_two_sw';
	}else if( $siteTemplate  == 6){
		$wordpress_folder = 'template_two_no';
	}else if( $siteTemplate  == 7){
		$wordpress_folder = 'template_three_en';
	}else if( $siteTemplate  == 8){
		$wordpress_folder = 'template_three_sw';
	}else if( $siteTemplate  == 9){
		$wordpress_folder = 'template_three_no';
	}else if( $siteTemplate  == 10){
		$wordpress_folder = 'template_mulit_en';
	}else if( $siteTemplate  == 11){
		$wordpress_folder = 'template_mulit_sw';
	}else if( $siteTemplate  == 12){
		$wordpress_folder = 'template_mulit_no';
	}else if( $siteTemplate  == 13){
		$wordpress_folder = 'newspro';
	}else if( $siteTemplate  == 14){
		$wordpress_folder = 'widget_template';
	}else{
		$wordpress_folder = 'template_one_en';
	}


	return $wordpress_folder;

}*///** function ends here **//

//** function to get the source database name **//
function getBlogLanguage( $siteTemplate ){

	$wordpress_language 	= '';

	if( $siteTemplate  == 1){
		$wordpress_language = 'English';
	}else if( $siteTemplate  == 2 ){
		$wordpress_language = 'Swedish';
	}else if( $siteTemplate  == 3){
		$wordpress_language = 'Norwegian';
	}else if( $siteTemplate  == 4){
		$wordpress_language = 'English';
	}else if( $siteTemplate  == 5){
		$wordpress_language = 'Swedish';
	}else if( $siteTemplate  == 6){
		$wordpress_language = 'Norwegian';
	}else if( $siteTemplate  == 7){
		$wordpress_language = 'English';
	}else if( $siteTemplate  == 8){
		$wordpress_language = 'Swedish';
	}else if( $siteTemplate  == 9){
		$wordpress_language = 'Norwegian';
	}else if( $siteTemplate  == 10){
		$wordpress_language = 'English';
	}else if( $siteTemplate  == 11){
		$wordpress_language = 'Swedish';
	}else if( $siteTemplate  == 12){
		$wordpress_language = 'Norwegian';
	}else if( $siteTemplate  == 13){
		$wordpress_language = 'English';
	}else{
		$wordpress_language = 'English';
	}

	return $wordpress_language;

}//** function ends here **//

//** Function to Update the Config File. **//
function UpdateConfigFile( $extractionPath, $site_slug ){

	$servername 		=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 			= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 			= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//

	chmod($extractionPath, 0775);

	$config_file 		= 	$extractionPath.DIRECTORY_SEPARATOR.'aw-wp-config.php';
	$config_file_final 	= 	$extractionPath.DIRECTORY_SEPARATOR.'wp-config.php';

	chmod($config_file, 0664);
	chmod($config_file_final, 0664);

	//** Change Permission of wp-content**//

	//** Update the Database Name/ **//
	$file_contents 		= 	file($config_file);
	$new_content 		=	array();

	foreach ( $file_contents as $line){

		$pos 			= 	strpos($line, "define('DB_NAME', '");

		if ($pos === false) {
		} else {
			$dbNameNew 	= 	str_replace('-', '_', $site_slug);
			$line 		= 	"define('DB_NAME', '".$dbNameNew."');";
		}

		$new_content[] 	= 	$line;
	}

	$str_contents 		= 	implode("", $new_content);
	$fp 				= 	fopen($config_file, "w");
	fwrite($fp, $str_contents);
	fclose($fp);

	//** Update the User Name/ **//
	$file_contents 		= 	file($config_file);
	$new_content 		= 	array();

	foreach ( $file_contents as $line) {

		$pos 			= 	strpos($line, "define('DB_USER', '");

		if ($pos === false) {
		} else {
			$DB_USER 	= 	DB_USER;
			$line 		= 	"define('DB_USER', '".$DB_USER."');";
		}

		$new_content[] 	= 	$line;

	}

	$str_contents 		= 	implode("", $new_content);
	$fp 				= 	fopen($config_file, "w");
	fwrite($fp, $str_contents);
	fclose($fp);

	//** Update the Password Name/ **//
	$file_contents 		= 	file($config_file);
	$new_content 		= 	array();

	foreach ( $file_contents as $line) {

		$pos 			= 	strpos($line, "define('DB_PASSWORD', '');");

		if ($pos === false) {
		} else {
			$DB_PASSWORD = 	DB_PASSWORD;
			$line 		 = 	"define('DB_PASSWORD', '".$DB_PASSWORD."');";
		}

		$new_content[] 	= 	$line;
	}

	$str_contents 		= 	implode("", $new_content);
	$fp 				= 	fopen($config_file_final, "w");
	fwrite($fp, $str_contents);
	fclose($fp);
}

//** function to Create Destination Database **//
function createDestinationDatabse( $site_slug ){

	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$mysql_database =  	str_replace('-', '_', $site_slug);

	$conn 			= 	new mysqli($servername, $username, $password);

	//** Code to process UTF-8 characters. **//
	mysqli_query($conn, "SET SESSION CHARACTER_SET_CLIENT =utf8;");

	if ($conn->connect_error) {
		return false;
	}else{

		$db_command = 	"CREATE DATABASE $mysql_database CHARACTER SET utf8 COLLATE utf8_general_ci;";

		if ($conn->query($db_command) === TRUE){
			return true;
		}else{
			return false;
		}
		return true;
	}

}//** Function Ends here. **//

//** Function to update Site home**//
function updateSiteOptions( $site_slug , $adminEmail, $siteTitle, $siteDescription, $topic = '' ){

	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//

	$mysql_database =  	str_replace('-', '_', $site_slug);

	$conn 			= 	new mysqli($servername, $username, $password, $mysql_database);

	//** Code to process UTF-8 characters. **//

	mysqli_query($conn, "SET SESSION CHARACTER_SET_CLIENT =utf8;");

	if ($conn->connect_error) {
		return false;
	}else{

		$protocol 	= 	isset($_SERVER["https"]) ? 'https' : 'http';
		$blog_path 	= 	$protocol . "://" . $_SERVER['SERVER_NAME'] . DIRECTORY_SEPARATOR . $site_slug . DIRECTORY_SEPARATOR;

		$update_siteurl = "UPDATE `wp_options` SET `option_value` = '".$blog_path."' WHERE option_name = 'siteurl'";
		$conn->query($update_siteurl);

		$update_home = "UPDATE `wp_options` SET `option_value` = '".$blog_path."' WHERE option_name = 'home'";
		$conn->query($update_home);

		$update_user = "UPDATE  `wp_users` SET  `user_email` =  '".trim($adminEmail)."' WHERE  `wp_users`.`ID` = 1;";
		$conn->query($update_user);

		//** update admin Email. **//
		$update_email = "UPDATE `wp_options` SET `option_value` = '".trim($adminEmail)."' WHERE option_name = 'admin_email'";
		$conn->query($update_email);

		//** Update site Title. **//
		$update_blogname = "UPDATE `wp_options` SET `option_value` = '".$siteTitle."' WHERE option_name = 'blogname'";
		$conn->query($update_blogname);

		//** Update Site Description **//
		$update_blogdescription = "UPDATE `wp_options` SET `option_value` = '".$siteDescription."' WHERE option_name = 'blogdescription'";
		$conn->query($update_blogdescription);

		//** Insert Topic  **//
		$update_blogdescription = "INSERT INTO `wp_options` SET `option_value` = '".$topic."', option_name = 'topic'";
		$conn->query($update_blogdescription);

		//** Close Database Connection. **//
		$conn->close();

		return $blog_path;
	}
}

//** Function to update active plugins. **//
function updateActivePlugins( $sourceDB, $destinationDB ){

	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//

	$conn 			= 	new mysqli($servername, $username, $password, $sourceDB );

	//** Code to process UTF-8 characters. **//
	mysqli_query($conn, "SET SESSION CHARACTER_SET_CLIENT =utf8;");

	if ($conn->connect_error) {
		return false;
	}else{
		$sql 		= 	"SELECT * FROM `wp_options` WHERE `option_name` = 'active_plugins'";
		$result 	= 	$conn->query( $sql  );

		if( $result->num_rows > 0 ) {

			$row 	= 	mysqli_fetch_assoc($result);
			$value 	= 	(string) $row['option_value'];
			//** Connect to the destination Blog.**//
			$conn2 	= 	new mysqli($servername, $username, $password, $destinationDB );
			mysqli_query($conn2, "SET SESSION CHARACTER_SET_CLIENT =utf8;");
			if ($conn2->connect_error) {
				return false;
			}else{
				//** Code to update DEstination Blog**//
				$sql = 	"UPDATE `wp_options` SET `option_value`= '$value' WHERE `option_name` = 'active_plugins'";
				$result = $conn2->query( $sql  );
				return true;
			}
		}else{
			return false;
		}
	}
}//** function ends here.**//

//** funtion to drop database **//
function dropDatabase( $databaseName ){

	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//

	$conn 			= 	new mysqli($servername, $username, $password);

	//** Code to process UTF-8 characters. **//

	mysqli_query($conn, "SET SESSION CHARACTER_SET_CLIENT =utf8;");

	if ($conn->connect_error) {
		return false;
	}else{

		$db_command = 	"DROP DATABASE  $databaseName";

		if ($conn->query($db_command) === TRUE){
			return true;
		}else{
			return false;
		}
		return true;
	}
}

//** Delete Ceated Floder**//
function rrmdir($dir){
    if (is_dir($dir)){

        $objects 	= 	scandir($dir); // gets all files and folders inside
        foreach ($objects as $object){

            if ($object != '.' && $object != '..'){

                if (is_dir($dir . '/' . $object)){
                    rrmdir($dir . '/' . $object);
                }else{
                    // if we find a file, simply delete it
                    unlink($dir . '/' . $object);
                }
            }
        }
        // the original directory is now empty, so delete it
        rmdir($dir);
    }
}

//** Delete Dummy Posts form the Destination Blog. **//
function deleteDummyPosts( $databaseName ){

	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//

	$conn 			= 	new mysqli( $servername, $username, $password, $databaseName );

	if ($conn->connect_error) {
		return false;
	}else{

		//** Select all posts form**//
		$sql 		= 	"SELECT ID FROM `wp_posts` WHERE `post_type` = 'post'";
		$result 	= 	$conn->query( $sql );

		while( $row = 	mysqli_fetch_assoc( $result ) ){

			$postid = 	$row['ID'];
			$sql_postmeta 	= 	"DELETE FROM `wp_postmeta` WHERE `post_id` = '$postid'";
			$conn->query( $sql_postmeta );
			$sql_post 	  	= 	"DELETE FROM `wp_posts` WHERE `ID` = $postid";
			$conn->query( $sql_post );
		}

		//** Delete Rss Links **//
		$sql_rssLinks 		= 	"DELETE FROM `wp_links`";
		$conn->query( $sql_rssLinks );

		//** Delete Catgegories **//
		deleteCategory( $databaseName );

		$conn->close();
		return true;
	}
}


/*delete users*/
//** Delete Dummy Posts form the Destination Blog. **//
function deleteUsers( $databaseName ){

	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//

	$conn 			= 	new mysqli( $servername, $username, $password, $databaseName );

	if ($conn->connect_error) {
		return false;
	}else{

		//** Select all posts form**//
		$sql 		= 	"SELECT * FROM `wp_users`";
		$result 	= 	$conn->query( $sql );

		while( $row = 	mysqli_fetch_assoc( $result ) ){

			$ID       = $row['ID'];
			$username = $row['user_login'];

			if(( $username == 'scanmine' || $username == 'aiko' || $username == 'stewiks' )){
				continue;
			}

			$sql_post 	  	= 	"DELETE FROM `wp_users` WHERE `ID` = $ID";
			$conn->query( $sql_post );
		}

		$conn->close();
		return true;
	}
}

//** Function create Top Menu. **//

function createTopMenu( $databaseName ){

	$menuTermName  		=	'top-primary-menu';
	$menuTermSlug		=	'top-primary-menu';
	$menuTermGroup		=	0;
	$menuTaxonomyID 	=	0;
	$servername 		=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 			= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 			= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//

	$connectionDestination = new mysqli( $servername, $username, $password, $databaseName );


	if ($connectionDestination->connect_error) {
		return false;
	}else{

		$sqlTerm 			= 	"INSERT INTO `wp_terms` (`name`,`slug`,`term_group`) VALUES ('$menuTermName','$menuTermSlug','$menuTermGroup')";
		$resultTerm 		=	$connectionDestination->query( $sqlTerm );

		if( $resultTerm ){
			$menuName 				=	strtolower($menuTermName);
			$menuTermTaxonomyID 	= 	mysqli_insert_id( $connectionDestination );

			$SqlTermsTaxonomy		= 	"INSERT INTO `wp_term_taxonomy`(`term_id`, `taxonomy`, `parent`, `count` ) VALUES( '$menuTermTaxonomyID', 'nav_menu', '$menuTermTaxonomyParentID','$menuTermTaxonomyTotal')";
			$resultTermsTaxonomy 	=	$connectionDestination->query($SqlTermsTaxonomy);

			if( $resultTermsTaxonomy ){
				$menuTaxonomyID 	= 	mysqli_insert_id( $connectionDestination );

				//** set menu as primary menu.**//
				$sqlOptions 		= 	"SELECT * FROM `wp_options` WHERE `option_name` = 'theme_mods_frank'";
				$resultNavOptions	=	$connectionDestination->query( $sqlOptions );

				if( $resultNavOptions ){

					$row  			= 	mysqli_fetch_assoc( $resultNavOptions );
					$str  			= 	$row['option_value'];
					$arrayData 		= 	unserialize($str);
					$arryItem 		= 	$arrayData['nav_menu_locations'];
					if(array_key_exists('frank_primary_navigation', $arryItem )){
						$arryItem['frank_primary_navigation'] = $menuTaxonomyID ;
					}else{
						$arryItem['frank_primary_navigation'] = $menuTaxonomyID ;
					}

					$arrayData['nav_menu_locations'] = $arryItem;
					$str_output 					 = serialize($arrayData );

					//** Update menu Options.  **//
					$sqlOptions 			= "UPDATE `wp_options` SET `option_value` = '$str_output' WHERE `option_name` = 'theme_mods_frank'";
					$resultNavOptionsUpdate	=	$connectionDestination->query( $sqlOptions );

				}

				//** Set Primary Menu for the NewsPro theme **//
				$sqlOptions 				= 	"SELECT * FROM `wp_options` WHERE `option_name` = 'theme_mods_news-pro'";
				$resultNavOptions			=	$connectionDestination->query( $sqlOptions );

				if( $resultNavOptions ){

					$row  		= 	mysqli_fetch_assoc( $resultNavOptions );
					$str  		= 	$row['option_value'];
					$arrayData 	= 	unserialize($str);
					$arryItem 	= 	$arrayData['nav_menu_locations'];

					if(array_key_exists('frank_primary_navigation', $arryItem )){
						$arryItem['primary'] = $menuTaxonomyID ;
					}else{
						$arryItem['primary'] = $menuTaxonomyID ;
					}

					$arrayData['nav_menu_locations'] = $arryItem;
					$str_output 					 = serialize($arrayData );

					//** Update menu Options.  **//
					$sqlOptions 			= "UPDATE `wp_options` SET `option_value` = '$str_output' WHERE `option_name` = 'theme_mods_news-pro'";
					$resultNavOptionsUpdate	=	$connectionDestination->query( $sqlOptions );

				};
			};
		};

		//** check if menu is created. **//
		if( $menuTaxonomyID  !== 0 ){
			//**Get the list of all categories in the database. **//
			$SqlTermsTaxonomy 		= "SELECT `wp_terms`.`term_id`, `wp_term_taxonomy`.`term_taxonomy_id`, `wp_terms`.`name`, `wp_terms`.`slug`  FROM `wp_term_taxonomy`,`wp_terms` WHERE `taxonomy` = 'category' AND `wp_terms`.`term_id` = `wp_term_taxonomy`.`term_id` AND `wp_terms`.`slug` != 'uncategorized'";
			$resultTermsTaxonomy 	= $connectionDestination->query( $SqlTermsTaxonomy );

			if(  $resultTermsTaxonomy->num_rows > 0 ){

				$menu_order = 0;
				while( $row = mysqli_fetch_assoc( $resultTermsTaxonomy ) ){
					$wp_term_taxonomy_id = $row['term_taxonomy_id'];

					//**Insert nav-menu-item in wp_posts **//
					  	$post_author	=	 1;
					    $post_status	=	'publish';
					    $comment_status	=	'open';
					    $ping_status	=	'open';
					    $post_type		=	'nav_menu_item';

						$sqlPost 		= 	"INSERT INTO `wp_posts` (  `post_author`, `post_status`,`comment_status`,`ping_status`,`post_type`, `menu_order` )VALUES( '$post_author','$post_status', '$comment_status', '$ping_status', '$post_type', '$menu_order')";
						$resultPost 	= $connectionDestination->query( $sqlPost );
						if( $resultPost ){

							//** incremant menu Order.
							$menu_order = $menu_order + 1;
							$PostID 	= 	mysqli_insert_id( $connectionDestination );
							$siteUrl 	=	getSiteURL( $connectionDestination );
							$guid 		= 	$siteUrl."/?p=".$PostID;
							$post_name	=	$PostID;

							//** Update Post data. **//
							$sqlUpdatePost 	= "UPDATE `wp_posts` SET `guid` = '$guid', `post_name` = '$post_name', `post_status` = 'publish' WHERE `ID` = '$PostID'";
							$resultPost 	= $connectionDestination->query( $sqlUpdatePost );

							//** Update PostMeta. **//
							$sqlPostMeta  = "INSERT INTO wp_postmeta (`post_id`,`meta_key`,`meta_value`) VALUES ( '$PostID', '_menu_item_type', 'taxonomy'),
																						( '$PostID', '_menu_item_menu_item_parent', '0'),
																						( '$PostID', '_menu_item_object_id', '$wp_term_taxonomy_id'),
																						( '$PostID', '_menu_item_object', 'category'),
																						( '$PostID', '_menu_item_target', ''),
																						( '$PostID', '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'),
																						( '$PostID', '_menu_item_xfn', ''),
																						( '$PostID', '_menu_item_url', '') ";
							$resultPost 	= $connectionDestination->query( $sqlPostMeta  );

							//** update term_relationship. **//
							$sqlTermRelationship 	= "INSERT INTO `wp_term_relationships`(`object_id`, `term_taxonomy_id`, `term_order`) VALUES ( '$PostID', '$menuTermTaxonomyID', '0' )";
							$resultTermRelationship	= $connectionDestination->query( $sqlTermRelationship  );
						}
				}
			}
		}else{
			return false;
		}
		return true;
	};
}//** Function ends here.**//

//** Function to get the Site URL **//
function getSiteURL( $con ){

	$sql  	= 	'SELECT option_value FROM wp_options WHERE option_name="siteurl"';
	$result =	$con->query($sql);

	if( $result->num_rows > 0 ){
		$row = mysqli_fetch_assoc( $result);
		return $row['option_value'];
	}else{
		return null;
	}

}

//** Function To Set Permalinks **//
function setPermalinkStructure( $blogname ){

	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$mysql_database =  	str_replace('-', '_', $blogname);

	$conn 			= 	new mysqli($servername, $username, $password, $mysql_database);

	//** Code to process UTF-8 characters. **//
	mysqli_query($conn, "SET SESSION CHARACTER_SET_CLIENT =utf8;");

	if ($conn->connect_error) {
		return false;
	}else{

		$update_permalink_structure = "UPDATE `wp_options` SET `option_value` = '/%postname%/' WHERE option_name = 'permalink_structure'";
		$conn->query($update_permalink_structure);

		//** Close Database Connection. **//
		$conn->close();
		return true;
	}
}

//** Function to Add .Htaccess **//
function addHTACCESS( $blogName ){

	$filenameSource 		= '/var/www/html/templates/htaccess/.htaccess';
	$filenameDestination 	= '/var/www/html/'.$blogName.'/.htaccess';

	copy($filenameSource, $filenameDestination);
	chmod($filenameDestination , 0664);

	$content 				= file_get_contents( $filenameDestination );
	$newContent 			= str_replace("BLOGNAME", $blogName, $content );
	file_put_contents($filenameDestination, $newContent);

}

//** function to  delete caterory **//
function deleteCategory( $databaseName ){

	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//

	$con 			= 	new mysqli( $servername, $username, $password, $databaseName );

	$query 			= 	"SELECT term_id FROM `wp_term_taxonomy` WHERE `wp_term_taxonomy`.`taxonomy` = 'category'";
	$result 		= 	mysqli_query($con,$query);

	if( $result->num_rows > 0 ){

		$row 		=	mysqli_fetch_array($result);

		foreach($row as $termId) {

		 	$delterms = "DELETE FROM `wp_terms` Where `term_id` = '".$termId["term_id"]."'";
			mysqli_query($con,$delterms);

		}

		$delQuery 	= 	"DELETE FROM `wp_term_taxonomy` WHERE  `wp_term_taxonomy`.`taxonomy` = 'category'";
		mysqli_query($con,$delQuery);
		return true;
	}else{
			return null;
		 }
}
?>