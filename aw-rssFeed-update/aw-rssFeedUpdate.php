<?php
//** Executionn Log Time**//
$time 		=  date('Y-m-d H:i:s');
$logTime 	= "Updated at: ". $time. "\n";
$file_time 		=  date('d-m-Y');
$log_filname = "logs/autoScriptLog-".$file_time .".log";
//** Hold list of all Blogs **//
$blogUpdate = array();
//** Executionn Log Time code ends **//
set_time_limit(0);
// require '../../../wp-load.php';
require '/var/www/html/wp-load.php';
global $wpdb;
//** All RSS FEEDS WILL BE STORED IN THIS ARRAY **//
$rssfeedarray = array();
$rssFileList 	= $wpdb->get_results("SELECT * FROM wp_rssfilelist WHERE status='Active'");

$fileurlarray 	= array();
for( $x = 0; $x < count($rssFileList); $x++ ){
	$fileurlarray[] = $rssFileList[$x]->fileurl;
}
//** Check if there are files to exceute. **//
if( !empty( $fileurlarray ) ){
	//** check each file in the list. **//
	foreach( $fileurlarray as $file ){
		$contents 	= file( $file, true);
		$cnt 		= 0;
		//** loop through the conetent on the cron job files **//
		$i = 0;
		foreach( $contents as $value ){
			//** get each line form the cron jon list. **//
			$contentssub 	= explode("\n",	$value );
			foreach( $contentssub as $itemValue ){
				if( $itemValue != "" ){
					$itemValueSub = preg_split("/[\s,]+/", $itemValue );
					if( !empty( $itemValueSub[1] ) && count( $itemValueSub ) > 4 ){
						$blogkeys			 	= $itemValueSub[0]."&nbsp;".$itemValueSub[1]."&nbsp;".$itemValueSub[2];
						$rssfilelist[$blogkeys] = $itemValueSub[3];
					}
				}
			}
		}
		unset( $contents );
	}
	//** create unique array for each rss blog. **//
	$rssfeedarray = array_unique($rssfilelist);
}else{
	echo "NO FEEDS FOUND.";
}

/* used to change folder permissions for images upload */
function setFolderPermissions( $directory ){

	$directory = "/var/www/html/".$directory;

	$commandOwn = "chown artworld:testing ".$directory;

	$commandPer = "chmod 0775 ".$directory;

	shell_exec( $commandOwn );

	shell_exec( $commandPer );
}




//** code to process Feeds start here. **//
if( count( $rssfeedarray ) ){
	//** Check all the Feeds **//
	foreach( $rssfeedarray as $rsskey => $rssvalue ){
		$cnt 		=	0;
	 	$valuesub 	= explode("&nbsp;",$rsskey);
	 	$rssschedule 	= $valuesub[0];
	  	$feedstatus		= $valuesub[1];
	  	$blogdir 		= $valuesub[2];
	  	$rssfeed 		= $rssvalue;
	  	//=========Get Blog Id==============//
		$blgmsc 		= 	microtime(true);
		global $wpdb;

		$sql 		= 	"SELECT * FROM wp_aw_blog_sites where  site_name ='$blogdir'";
		$rsBlog 	= 	$wpdb->get_results( $sql );
		$Blog_name 		= $rsBlog[0]->site_name;
		$blgmsc 		= microtime(true)-$blgmsc;

		if( !empty( $rsBlog ) ){
			foreach ($rsBlog as $blog ) {
				$blogName = $blog->site_name;
				$connection = connectToDatabase( $blogName );
				//** Update RSS IF Connection Successful**//
				if( $connection ){
					//** Update Rss **//
					//deleteDuplicatePost($connection);
					readRSS( $connection, $rssschedule, $feedstatus, $blogdir, $rssfeed );
					//deleteDuplicatePost($connection);
				}
			}//** For Each Ends here **//
		}//** IF Block Ends Here. **//
	}
}else{
	echo "NOTHING TO UPDATE";
}
//** ===================================================== FUNCTIONS BLOCK START HERE ================================================================== **//

//** function to connect to The datdabase for the blog. **//
//** fucntion will return the connection object if databse is connected, and retun false if connection id failed.  **//
function connectToDatabase( $destinationBlog ){
	$servername 		=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 			= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 			= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$destinationBlog  	= 	str_replace('-', '_', $destinationBlog );

    $connection  = mysqli_connect( $servername, $username , $password , $destinationBlog );
    if (mysqli_connect_errno()){
    	echo "Destination Database Not Found:  $destinationDB";
        return false;
    }else{
    	return $connection;
    }
    $connection->close();
}

//** function to close database connection  **//
function closeDatabaseConnection( $connnection ){
	$connection->close();
}


//** Function to Read RSS File.  **//
function readRSS( $connection, $rssschedule, $feedstatus, $blogdir, $rssFeed ){
	global $blogUpdate;
	$blogUpdate[] = $blogdir;
	//echo "<br>Feed Link: ".$rssFeed ."<br>";
	$doc 	=	new DOMDocument();
	if( empty( $rssFeed) ){
		echo "Blog Rss Feed Empty: ".$blogdir."\n";
	}else{
		$doc->load($rssFeed);
	}

	$cattitle 		= 	$doc->getElementsByTagName('title')->item(0)->nodeValue;
	$pubdate 		= 	$doc->getElementsByTagName('pubDate')->item(0)->nodeValue;
	$rsfeedstatus 	= 	explode(":",$feedstatus);
	$counter_feed_status = 0;
	//** check all node of RSS FEED. **//
	foreach ( $doc->getElementsByTagName('item') as $node ){

		$description 	=	preg_replace('~>\s+<~m', '><', $node->getElementsByTagName('description')->item(0)->nodeValue);
		$description 	= 	trim($description);
		$chart_count	=	0;
		$smblock 		= 	$node->getElementsByTagName('block')->item(0)->nodeValue;
		$smwidget 		= 	$node->getElementsByTagName('widget')->item(0)->nodeValue;
		//** Code Block For POST and WIDGETS  start **//
		if(!empty($smwidget) && empty($smblock)){
			//** area to process CHARTS  **//
		}else{
			$actionstatus 	= $rsfeedstatus[$counter_feed_status];
			$enclosurelink 	= $node->getElementsByTagName('enclosure');
			if( $enclosurelink->item(0) != "" ){
				$URL 		= $enclosurelink->item(0)->getAttribute('url');
				$imagetype 	= $enclosurelink->item(0)->getAttribute('type');
			}else{
				$URL 		= "";
				$imagetype 	= "";
			}
			$itemRSS = array(
				'title' 			=> $node->getElementsByTagName('title')->item(0)->nodeValue,
				'smblock' 			=> $node->getElementsByTagName('block')->item(0)->nodeValue,
				'smwidget' 			=> $node->getElementsByTagName('widget')->item(0)->nodeValue,
				'enclosure' 		=> $URL,
				'sourcelink' 		=> $node->getElementsByTagName('link')->item(0)->nodeValue,
				'post_mimie_type' 	=> $imagetype,
				'description' 		=> $description,
				'pubDate' 			=> $node->getElementsByTagName('pubDate')->item(0)->nodeValue
		   	);

			$post_mimie_type 	=	$itemRSS['post_mimie_type'];
		  	$post_title 		= 	preg_replace('/\s+/',' ',trim($itemRSS['title']));
		  	$post_name 			= 	sanitize_title($post_title);
		  	$smblock 			= 	$itemRSS['smblock'];
		  	$smwidget 			= 	$itemRSS['smwidget'];

		   	if( strstr($smblock, "featured1") ){
				$featured = $smblock;
		  	}

			$enclosure 			= 	$itemRSS['enclosure'];
			$sourcelink 		= 	$itemRSS['sourcelink'];
			$post_date 			= 	date("Y-m-d H:i:s",strtotime( $itemRSS['pubDate'] ) );
			$description 		= 	trim( $description );
			$excerpt 			= 	$description;
			$post_author 		= 	1;  //** Set Default to 1. All Posts Created by Admin. **//
			$sql 				=  "SELECT * FROM wp_terms WHERE name = '".addslashes($cattitle)."'";
			$r_terms 			=  $connection->query( $sql );

			//** Insert Term If Not Exists **//
			if( $r_terms->num_rows ==  0 ) {
				mysqli_set_charset($connection, 'utf8');
				$insert_term_query = 'INSERT INTO wp_terms (`name`,`slug`) VALUES("'.mysql_real_escape_string($cattitle).'","'.strtolower(str_replace(' ','-',mysql_real_escape_string($cattitle))).'")';
				$connection->query($insert_term_query);
				$ID_t = mysqli_insert_id( $connection );
				$terms_tax_query = $connection->query("SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE term_id='".$termsid."' AND taxonomy='category'");
				if( $terms_tax_query->num_rows == 0){
						mysqli_set_charset($connection, 'utf8');
						$insert_term_query 	= "INSERT INTO wp_term_taxonomy(`term_id`,`taxonomy`) VALUES('".$ID_t."','category')";
						$connection->query($insert_term_query);
						$ID_t 	= mysqli_insert_id( $connection );
				}
			}
			//** =============================== Search Term ==================== **//
			$sql 		=	"SELECT * FROM wp_terms WHERE name = '".addslashes($cattitle)."'";
		   	$rsTerms 	= 	$connection->query( $sql );
			$rsTermsRow = 	mysqli_fetch_assoc( $rsTerms  );

			//**  check if Term Exists **//
			if( !empty( $rsTermsRow ) ){
					$termsid 			= $rsTermsRow['term_id'];
					$term_taxonomy_id 	= 0;
					if( $termsid != 0 ){
						$termstaxquery 		= 	"SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE term_id='".$termsid."' AND taxonomy='category'";
						$rsTermsTaxonomy 	= 	$connection->query($termstaxquery);
						$rsTermsTaxonomyRS 	= 	mysqli_fetch_assoc( $rsTermsTaxonomy );
						$term_taxonomy_id 	= 	$rsTermsTaxonomyRS['term_taxonomy_id'];
					}
					//** Place Holder For Post DATA**//
					$rsPosts = array();
					if( $term_taxonomy_id != 0 ){

						$SQL_POSTS 		= "SELECT `wp_posts`.`ID`, `wp_posts`.`post_title`  FROM wp_posts LEFT JOIN wp_term_relationships ON wp_posts.ID = `wp_term_relationships`.`object_id` WHERE `wp_posts`.`post_name` LIKE '%$post_name%' AND `wp_term_relationships`.`term_taxonomy_id` = '$term_taxonomy_id' AND `wp_posts`.`post_type` = 'post'";
						$result_post 	= 	$connection->query($SQL_POSTS);
						$rsPosts 		= 	mysqli_fetch_assoc( $result_post );
					}

					if(empty($rsPosts['post_title']) && (!empty($description) || !empty($enclosure)) && $actionstatus == "*"){
						//echo "     ==> In IF SECTION : ".$blogdir."\n";
						//=====Check post name exists===//
						// $SQLPOSTSLUG 	= 	"SELECT post_name FROM wp_posts WHERE post_name LIKE '%".$post_name."%' ORDER BY ID DESC LIMIT 1";
						$SQLPOSTSLUG 	= 	"SELECT post_name FROM wp_posts WHERE post_name LIKE '%".$post_name."%'  OR post_name = ''";
						$result_post 	= 	$connection->query( $SQLPOSTSLUG );
						$rsPostSlug 	= 	mysqli_fetch_assoc( $result_post );

						if(!empty($rsPostSlug)){

							file_put_contents(dirname(__FILE__)."/rsPostSlug.log", print_r($rsPostSlug, true),FILE_APPEND );

							/*delete Posts */
							$SQLPOSTSLUG 	= 	"DELETE FROM wp_posts WHERE post_name LIKE '%".$post_name."%' OR post_name = ''";
							$result_post 	= 	$connection->query( $SQLPOSTSLUG );

							file_put_contents(dirname(__FILE__)."/post_title1.log", print_r($post_name, true),FILE_APPEND );
							file_put_contents(dirname(__FILE__)."/post_title1.log", print_r("\n\n", true),FILE_APPEND );

							file_put_contents(dirname(__FILE__)."/msqlLog.log", print_r($blogdir, true),FILE_APPEND );
							file_put_contents(dirname(__FILE__)."/msqlLog.log", print_r("\n\n", true),FILE_APPEND );

							file_put_contents(dirname(__FILE__)."/msqlLog.log", print_r($SQLPOSTSLUG, true),FILE_APPEND );
							file_put_contents(dirname(__FILE__)."/msqlLog.log", print_r("\n\n", true),FILE_APPEND );
							// $rsPostSlug 	= 	mysqli_fetch_assoc( $result_post );


							/*$post_slug 		= explode("-",$rsPostSlug['post_name']);
							$lastelement 	= $post_slug[count($post_slug)-1];

							if(is_numeric($lastelement)){
								$increaseslug 	= 	$lastelement+1;
								$postslug 		= 	$post_name."-".$increaseslug;
							}else{
								$postslug 		= 	$post_name."-2";
							}*/
							$postslug = $post_name;
						}else{
							$postslug = $post_name;
						}
						$sqlFindPost 		= 	"SELECT ID FROM wp_posts WHERE post_title = '".addslashes($post_title)."'";
						$sqlFindResults 	=	$connection->query( $sqlFindPost );

						if( $sqlFindResults->num_rows >= 0 ){
						//** INSERT NEW POST **//
						mysqli_set_charset( $connection, 'utf8' );
						$insert_post_query 		= 'INSERT INTO wp_posts (post_author,post_date,post_date_gmt ,post_content, post_title, post_excerpt, post_status,comment_status,ping_status ,post_name,post_modified,post_modified_gmt,post_type) VALUES("'.$post_author.'","'.$post_date.'","'.$post_date.'","'.addslashes($description).'","'.addslashes($post_title).'","'.addslashes($excerpt).'","publish","closed","closed","'.$postslug.'","'.$post_date.'","'.$post_date.'","post")';
						$resultTermsTaxonomy 	=	$connection->query( $insert_post_query );
						$ID  					= 	mysqli_insert_id( $connection );
						//echo "-----------------------Added New Post : ".$post_title."\n";
						if( $ID ){
							//** update post guid.  **//
							$guid 	= 	$siteurl."?p=".$ID;
							mysqli_set_charset($connection, 'utf8');
							$update_post_query 	= "UPDATE wp_posts SET guid='".$guid."' WHERE ID='".$ID."'";
							$connection->query( $update_post_query );
							$postmetaarray = array();

							if(!empty($enclosure)){
								//$source = $enclosure."<br>";
								$source = $enclosure;
								$destination = ABSPATH.$blogdir."/wp-content/uploads/".basename($enclosure)."";

								$dir = $blogdir."/wp-content/uploads";
								setFolderPermissions( $dir );

								if(@copy($source,$destination)){

									foreach ($enclosure as $key_enclosure => $value_enclosure) {
									    $value_enclosure = trim($value_enclosure);
									    if (empty($value_enclosure)){
									        echo "$key_enclosure empty <br/>";
									    }
									    else{
											$change_owner = "artworld:testing";
											chown ($destination, $change_owner);
											chmod ($destination, 0664);
										}
									}

									$commandOwn = "chown artworld:testing ".$destination;
									$commandPer = "chmod 0664 ".$destination;

									shell_exec( $commandOwn );
									shell_exec( $commandPer );

									$postmetaarray["enclosure"] = $enclosure;
									//==Insert Image===//
									$imageurlguid 	= 	ABSPATH.$blogdir."/wp-content/uploads/".basename($enclosure)."";
									$posttitle 		= 	preg_replace('/\.[^.]+$/', '', basename($enclosure));
									$postname 		= 	sanitize_title($posttitle);

									mysqli_set_charset($connection, 'utf8');
									$insert_post_image_query 	= 'INSERT INTO wp_posts (post_author,post_date,post_date_gmt ,post_title,post_status,comment_status,ping_status,post_name,post_modified,post_modified_gmt,post_parent,guid,post_type,post_mime_type) VALUES("'.$post_author.'","'.$post_date.'","'.$post_date.'","'.$posttitle.'","inherit","closed","open","'.$postname.'","'.$post_date.'","'.$post_date.'","'.$ID.'","'.$imageurlguid.'","attachment","'.$post_mimie_type.'")';
									$connection->query($insert_post_image_query);
									$thumbnailid 	= mysqli_insert_id( $connection );
									//==Insert Image===//
									$postmetaarray["_thumbnail_id"] = $thumbnailid;

									/*file_put_contents(dirname(__FILE__)."/image.txt", print_r( "Blog Name: ".$blogdir ,true)."\n", FILE_APPEND);
									file_put_contents(dirname(__FILE__)."/image.txt", print_r( "Thumbnail Id: ".$thumbnailid ,true)."\n", FILE_APPEND);
									file_put_contents(dirname(__FILE__)."/image.txt", print_r( "\n\n" ,true)."\n", FILE_APPEND);*/
									/* BULK */
								}
							}

							$postmetaarray["sm:block"] = $smblock;

							if( !empty( $sourcelink ) ){
								$postmetaarray["syndication_permalink"] = $sourcelink;
							}

							foreach($postmetaarray as $key => $value ){
								mysqli_set_charset($connection, 'utf8');
								$querypostmeta 	= 'INSERT INTO wp_postmeta( post_id, meta_key, meta_value ) VALUES("'.$ID.'","'.$key.'","'.$value.'")';
								$connection->query($querypostmeta);
							}

							if(!empty($enclosure) && $thumbnailid != 0 ){
								mysqli_set_charset($connection, 'utf8');
								$querypostmeta1 	= 'INSERT INTO wp_postmeta(post_id,meta_key,meta_value) VALUES("'.$thumbnailid.'","_wp_attached_file","'.basename($enclosure).'")';
								$connection->query( $querypostmeta1 );
							}

							mysqli_set_charset($connection, 'utf8');
							$insertquerytermrels 	= 	'INSERT INTO wp_term_relationships(object_id ,term_taxonomy_id) VALUES("'.$ID.'","'.$term_taxonomy_id.'")';
							$connection->query($insertquerytermrels);

							mysqli_set_charset($connection, 'utf8');
							$updatecounter 			= 	"UPDATE wp_term_taxonomy SET count = count+1 WHERE term_taxonomy_id='".$term_taxonomy_id."'";
							$connection->query($updatecounter);
						}
						}
						//** Code ends here **//

					}else{

						$ID 			= $rsPosts['ID'];
					 	$post_modified 	= date('Y-m-d H:i:s');
					 	$postmetaarray 	= array();

					 	$postmetaarray["sm:block"] 	= 	$smblock;

					 	if( !empty( $ID ) ){

					 		if( !empty($description) || !empty($enclosure) ){

					 			if(strstr($actionstatus,"T")){
					 				mysqli_set_charset($connection, 'utf8');
									$update_post_query = 'UPDATE wp_posts SET post_content="'.addslashes($description).'",post_excerpt="'.addslashes($excerpt).'",post_modified="'.$post_modified.'",post_modified_gmt="'.$post_modified.'" WHERE ID="'.$ID .'"';
									$connection->query($update_post_query);
								}

								if(strstr($actionstatus,"B")){
									foreach($postmetaarray as $key => $value ){

										mysqli_set_charset($connection, 'utf8');
										$queryupdatepostmeta = 'UPDATE wp_postmeta SET meta_value="'.$value.'" WHERE meta_key="'.$key.'" AND post_id="'.$ID.'"';
										$connection->query($queryupdatepostmeta);

									}
								}

								//===Upadate post parent[images]=====//
							if(strstr($actionstatus,"P")){

								$SQLPOSTPARENT 	= 	"SELECT * FROM wp_posts WHERE post_parent='".$ID."' AND post_type='attachment'";
								$result_PM 		=	$connection->query($SQLPOSTPARENT);
								$rsPostParent 	= 	mysqli_fetch_assoc( $result_PM );
								$IDATTACHMENT 	= 	$rsPostParent['ID'];

								$destination = ABSPATH.$blogdir."/wp-content/uploads/".basename($enclosure)."";

								if( !empty($enclosure) && basename($enclosure)!=basename($rsPostParent['guid'])){
									if(!empty($IDATTACHMENT)){
									$source = $enclosure;
									$destination = ABSPATH.$blogdir."/wp-content/uploads/".basename($enclosure)."";

									$dir = $blogdir."/wp-content/uploads";
									setFolderPermissions( $dir );

									if(@copy($source,$destination)){

										foreach ($enclosure as $key_enclosure => $value_enclosure) {
										    $value_enclosure = trim($value_enclosure);
										    if (empty($value_enclosure)){
										        echo "$key_enclosure empty <br/>";
										    }
										    else{
												$change_owner = "artworld:testing";
												chown ($destination, $change_owner);
												chmod ($destination, 0664);
											}
										}

									$commandOwn = "chown artworld:testing ".$destination;
									$commandPer = "chmod 0664 ".$destination;

									shell_exec( $commandOwn );
									shell_exec( $commandPer );

									//$postmetaarray["enclosure"] = $enclosure;
									unset($postmetaarray);
									//Remove existing Image From Folder
									@unlink($rsPostParent['guid']);

									//==Update Image===//
									$imageurlguid 	= 	ABSPATH.$blogdir."/wp-content/uploads/".basename($enclosure)."";
									$posttitle 		= 	preg_replace('/\.[^.]+$/', '', basename($enclosure));
									$postname 		= 	sanitize_title($posttitle);


									mysqli_set_charset($connection, 'utf8');
									$update_post_image_query = 'UPDATE wp_posts SET post_title="'.$posttitle.'" ,post_name="'.$postname.'",post_modified="'.$post_modified.'",post_modified_gmt="'.$post_modified.'",guid="'.$imageurlguid.'",post_mime_type="'.$post_mimie_type.'" WHERE ID="'.$IDATTACHMENT.'"';
									$connection->query( $update_post_image_query );

									$thumbnailid 	= 	$IDATTACHMENT;
									//==Update Image===//
									$postmetaarray["enclosure"] 	= $enclosure;
									$postmetaarray["_thumbnail_id"] = $thumbnailid;

									foreach( $postmetaarray as $metakey => $metavalue ){
										mysqli_set_charset($connection, 'utf8');
										$queryupdatepostmeta = 'UPDATE wp_postmeta SET meta_value="'.$metavalue.'" WHERE meta_key="'.$metakey.'" AND post_id="'.$ID.'"';
										$connection->query( $queryupdatepostmeta );
									}
									//=======Check Attachment============//
									$SQLPOSTMETA 		= "SELECT meta_key FROM wp_postmeta WHERE meta_key = '_wp_attached_file' AND post_id='".$IDATTACHMENT."'";
									$RSWPATTACHMENT1 	= $connection->query( $SQLPOSTMETA );
									$RSWPATTACHMENT 	= mysqli_fetch_assoc( $RSWPATTACHMENT1 );

									// file_put_contents(dirname(__FILE__)."/test.txt", print_r($RSWPATTACHMENT,true));
									/*echo "<pre>";
									print_r( $RSWPATTACHMENT );
									echo "</pre>";*/

									if(!empty($RSWPATTACHMENT)){
										mysqli_set_charset($connection, 'utf8');
										$querypostmeta1 = 'UPDATE wp_postmeta SET meta_value="'.basename($enclosure).'" WHERE meta_key="_wp_attached_file" AND post_id="'.$IDATTACHMENT.'"';
									    $connection->query($querypostmeta1);
									}else{
										mysqli_set_charset($connection, 'utf8');
										$querypostmeta1 = 'INSERT INTO wp_postmeta(post_id,meta_key,meta_value) VALUES("'.$thumbnailid.'","_wp_attached_file","'.basename($enclosure).'")';
									    $connection->query($querypostmeta1);
									}
									//=======Check Attachment============//
									}
								}else{
									$source = $enclosure;
									$destination = ABSPATH.$blogdir."/wp-content/uploads/".basename($enclosure)."";

									$dir = $blogdir."/wp-content/uploads";
									setFolderPermissions( $dir );

									if(@copy($source,$destination)){

										foreach ($enclosure as $key_enclosure => $value_enclosure) {
									    $value_enclosure = trim($value_enclosure);
										    if (empty($value_enclosure)){
										        echo "$key_enclosure empty <br/>";
										    }
										    else{
												$change_owner = "artworld:testing";
												chown ($destination, $change_owner);
												chmod ($destination, 0664);
											}
										}
									$commandOwn = "chown artworld:testing ".$destination;
									$commandPer = "chmod 0664 ".$destination;

									shell_exec( $commandOwn );
									shell_exec( $commandPer );

									//==Insert Image===//
									$imageurlguid 	= ABSPATH.$blogdir."/wp-content/uploads/".basename($enclosure)."";
									$posttitle 		= preg_replace('/\.[^.]+$/', '', basename($enclosure));
									$postname 		= sanitize_title($posttitle);
									mysqli_set_charset($connection, 'utf8');
									$insert_post_image_query = 'INSERT INTO wp_posts (post_author,post_date,post_date_gmt ,post_title,post_status,comment_status,ping_status,post_name,post_modified,post_modified_gmt,post_parent,guid,post_type,post_mime_type) VALUES("'.$post_author.'","'.$post_date.'","'.$post_date.'","'.$posttitle.'","inherit","closed","open","'.$postname.'","'.$post_date.'","'.$post_date.'","'.$ID.'","'.$imageurlguid.'","attachment","'.$post_mimie_type.'")';
									$connection->query($insert_post_image_query);
									$thumbnailid = mysqli_insert_id($connection);
									//==Insert Image===//
									$postmetaarray["enclosure"] 		= $enclosure;
									$postmetaarray["_thumbnail_id"] 	= $thumbnailid;
									foreach($postmetaarray as $key=>$value){

										$SQLPOSTMETAEXISTS = "SELECT meta_key
													FROM wp_postmeta
													WHERE meta_key='".$key."'
													AND post_id='".$ID."'";
										$rsPostMetaExists1 = $connection->query($SQLPOSTMETAEXISTS);
										$rsPostMetaExists 	= 	mysqli_fetch_assoc( $rsPostMetaExists1 );
										if(empty($rsPostMetaExists)){
											mysqli_set_charset($connection, 'utf8');
											$querypostmeta = 'INSERT INTO wp_postmeta(post_id,meta_key,meta_value) VALUES("'.$ID.'","'.$key.'","'.$value.'")';
											$connection->query($querypostmeta);
										}else{
											mysqli_set_charset($connection, 'utf8');
											$querypostmeta = 'UPDATE wp_postmeta SET meta_value="'.$value.'" WHERE meta_key="'.$key.'" AND post_id="'.$ID.'"';
											$connection->query($querypostmeta);
										}
									}
									if($thumbnailid!=0){
										mysqli_set_charset($connection, 'utf8');
										$querypostmeta1 = 'INSERT INTO wp_postmeta(post_id,meta_key,meta_value) VALUES("'.$thumbnailid.'","_wp_attached_file","'.basename($enclosure).'")';
										$connection->query($querypostmeta1);
									}
									}
								}
							}else{
								//==Delete Posts==//
								if(!empty($IDATTACHMENT)){
									$postdeletemetaquery = "DELETE FROM wp_postmeta WHERE post_id='".$ID."' AND meta_key='_thumbnail_id'";
									$connection->query($postdeletemetaquery);

									$postdeletemetaquery1 = "DELETE FROM wp_postmeta WHERE post_id='".$IDATTACHMENT."' AND meta_key='_wp_attached_file'";
									$connection->query($postdeletemetaquery1);

									@unlink($rsPostParent['guid']);
									$postdeletequery = "DELETE FROM wp_posts WHERE ID='".$IDATTACHMENT."'";
									$connection->query($postdeletequery);
								}
									//==Delete Posts==//
							}
							}
						}else{

					 		}
					 	}
					}
			}//** Term Row Block Ends here.  **//
		} //**POST and WIDGETS  Ends   **//
	}
}

//** For Log File**//
$blogs_update = implode(" , ",$blogUpdate);

file_put_contents(dirname(__FILE__)."/".$log_filname, print_r("[", true),FILE_APPEND );
file_put_contents(dirname(__FILE__)."/".$log_filname, print_r($blogs_update, true),FILE_APPEND );
file_put_contents(dirname(__FILE__)."/".$log_filname, print_r("]\n", true),FILE_APPEND );

/*function deleteDuplicatePost($connection){

$delete_duplicate_qry = "DELETE wp_posts FROM wp_posts INNER JOIN (
  SELECT max(ID) AS lastId, post_title FROM wp_posts WHERE post_title IN ( SELECT post_title FROM wp_posts GROUP BY post_title HAVING count(*) > 1 )
   GROUP BY post_title ) duplic on duplic.post_title = wp_posts.post_title WHERE wp_posts.ID < duplic.lastId;";

$result_duplicate = $connection->query($delete_duplicate_qry);
	if ($result_duplicate) {
		echo "<h2 style='background:red;color:#FFF;'>Duplicate Post deleted successfully! ".$result_duplicate."</h2>";
	}else{
		echo "<h2 style='background:red;color:#FFF;'>Error, Posts are not deleted successfully! ".$result_duplicate."</h2>";
	}

}*/