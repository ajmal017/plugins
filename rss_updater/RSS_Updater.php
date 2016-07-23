<?php
/*
Plugin Name: RSS-Updater
Plugin URI: http://www.artworldwebsolutions.com
Description:  Update RSS Feed List.
Version: 1.2
Author: Developer-AW109
Author URI: AW109@aw-developer.com
License:
*/
require '/var/www/html/bilport/wp-load.php';
global $wpdb;

class RSS_Updater 
{
	function __construct() {
		add_action('admin_menu', array(&$this, 'register_plugin_menu') );
	}

	function register_plugin_menu(){
		add_menu_page( 'RSS UPDATER', 'RSS UPDATER', 'manage_options', 	'rssupdater', 	array(&$this, 'rss_updater_button'), '', 29 );
	}

	function rss_updater_button(){

		if(isset($_GET['action'])){
			global $wpdb;				
			
			$rssFeed = $wpdb->get_results( "SELECT link_url FROM wp_links; ");
			foreach ($rssFeed as $rssFeeds) {
					$rss = $rssFeeds->link_url;
					//file_put_contents(dirname(__FILE__).'/rss.log', print_r($rss,true),FILE_APPEND);
					$doc 	=	new DOMDocument();
					//file_put_contents(dirname(__FILE__).'/doc.log', print_r($doc,true),FILE_APPEND);
					if( empty( $rss) ){
						echo "Blog Rss Feed Empty";
					}else{
						$doc->load($rss);
					}
					
					foreach ( $doc->getElementsByTagName('item') as $node ){

						$description 	=	preg_replace('~>\s+<~m', '><', $node->getElementsByTagName('description')->item(0)->nodeValue);  
						$description 	= 	trim($description);
						$chart_count	=	0;
						$title			=	$node->getElementsByTagName('title')->item(0)->nodeValue;
						$smblock 		= 	$node->getElementsByTagName('block')->item(0)->nodeValue;
						$guid 			= 	$node->getElementsByTagName('guid')->item(0)->nodeValue;
						$link 			= 	$node->getElementsByTagName('link')->item(0)->nodeValue;
						$pubDate 		= 	$node->getElementsByTagName('pubDate')->item(0)->nodeValue;
						$category 		= 	$node->getElementsByTagName('category')->item(0)->nodeValue;
						
						$dot 			= 	' ';
						$position 		= 	stripos ($guid, $dot); 
				        $offset 		= 	$position; 
				        $position2 		=	stripos ($guid, $dot, $offset);
				        $content2 		= 	substr($guid, 0, $position2); 
				        $content 		= 	str_replace($content2, '', $guid);
				        $enclosure 		=	$node->getElementsByTagName('enclosure');

				        
				        if( $enclosure->item(0) != "" ){
							
							$URL 		= $enclosure->item(0)->getAttribute('url');
							$imagetype 	= $enclosure->item(0)->getAttribute('type');
						
						}else{
							$URL 		= "";
							$imagetype 	= "";					
						}
						
						$post_id 		= $wpdb->get_results("SELECT * FROM wp_postmeta WHERE meta_value ='".$link."'");
						//file_put_contents(dirname(__FILE__).'/post_id.log', print_r($post_id,true),FILE_APPEND);
						if(mysql_num_rows($post_ids=='0')){
					        echo "No Record Found";
					    }else{
						   	foreach ($post_id as $value) {
					       		  
					       			$metaid 	= 	$value->meta_id;
					       			$postid 	= 	$value->post_id;
					       			$metakey 	= 	$value->meta_key;
					       			$metavalue 	= 	$value->meta_value;
					       			//file_put_contents(dirname(__FILE__).'/metavalue.log', print_r($metavalue,true),FILE_APPEND);
					       			$link_data  =	get_post_meta( $postid, 'syndication_permalink' , false);
					       			//file_put_contents(dirname(__FILE__).'/link_data.log', print_r($link_data,true),FILE_APPEND);
					       		 	
					       		foreach ($link_data as $links) {
					       		 	
					       		 	$link1		= $links->meta_value;

					       		 	file_put_contents(dirname(__FILE__).'/link1.log', print_r($link1,true),FILE_APPEND);
					       		 	if( $link1 === $link ){

					       		 		$cat_id			=	get_cat_ID( $category );
					       		 		//file_put_contents(dirname(__FILE__).'/cat_id.log', print_r($cat_id,true),FILE_APPEND);

					       		 		$del_qry 		= 	"DELETE FROM wp_posts WHERE ID =".$postid;
					       		 		$del_post		= 	$wpdb->query($del_qry);
					       		 		if ($del_post) {
					       		 			file_put_contents(dirname(__FILE__).'/delif.log', print_r(count($del_post),true),FILE_APPEND);
					       		 		}else{
					       		 			file_put_contents(dirname(__FILE__).'/delelse.log', print_r(count($del_post),true),FILE_APPEND);
					       		 		}

					       		 		$del_meta_qry 	= 	"DELETE FROM wp_postmeta WHERE post_id =".$postid;
					       		 		$del_post_meta	= 	$wpdb->query($del_meta_qry);
					       		 		if ($del_post_meta) {
					       		 			file_put_contents(dirname(__FILE__).'/del_post_metaif.log', print_r(count($del_post_meta),true),FILE_APPEND);
					       		 		}else{
					       		 			file_put_contents(dirname(__FILE__).'/del_post_metaelse.log', print_r(count($del_post_meta),true),FILE_APPEND);
					       		 		}

					       		 		$postdate 		=	date("Y-m-d H:i:s");

					       		 		$insrt_post 	= array(
					       		 		  'post_author'	  	  => 1,	
					       		 		  'post_date'	  	  => date("Y-m-d H:i:s"),
					       		 		  'post_date_gmt' 	  => date("Y-m-d H:i:s"),
					       		 		  'post_content'  	  => $description,
										  'post_title'    	  => $title,
										  'post_excerpt'  	  => $description,
										  'post_status'  	  => 'publish',
										  'comment_status'	  => 'closed',
										  'ping_status'	  	  => 'closed',
										  'guid'			  => $content,
										  'post_name'	  	  => sanitize_title($title),
										  'post_modified' 	  => date("Y-m-d H:i:s"),
										  'post_modified_gmt' => date("Y-m-d H:i:s"),
										  'post_type'		  => 'post'
										);

										$postid_id  		  = 	wp_insert_post( $insrt_post );
										if ($postid_id) {
											//file_put_contents(dirname(__FILE__).'/postid_idif.log', print_r($postid_id,true),FILE_APPEND);
										}else{
											//file_put_contents(dirname(__FILE__).'/postid_idelse.log', print_r($postid_id,true),FILE_APPEND);
										}
										
										wp_set_post_categories( $postid_id, $cat_id, false );
										add_post_meta( $postid_id, 'sm:block', $smblock, false );
										add_post_meta( $postid_id, 'syndication_permalink', $link, false );
										add_post_meta( $postid_id, 'enclosure', $URL, false );
										echo "Post Deleted and Created.";

										$postmetaarray 		  =  array();
										
										$siteurl		   	  =  get_site_url();
										$img_name			  =  str_replace("http://iris.scanmine.com/go/pub/bilport/imgpb/", "", $URL);

										if(!empty($enclosure)){
											
											$source 			= $URL;
											$destination 		= $siteurl."/wp-content/uploads/".$img_name;
											
											if(@copy($source,$destination)){
												
												$postmetaarray["enclosure"] = $enclosure;
												//==Insert Image===//
												$imageurlguid 	= 	$siteurl."/wp-content/uploads/".$img_name."";
												$posttitle 		= 	preg_replace('/\.[^.]+$/', '', $img_name);
												$postname 		= 	sanitize_title($posttitle);
												
												$insrt_post_img	= array(
								       		 		  
								       		 		  'post_author'	  	  => 1,	
								       		 		  'post_date'	  	  => $postdate,
								       		 		  'post_date_gmt' 	  => $postdate,
								       		 		  'post_title'    	  => $posttitle,
													  'post_status'  	  => 'inherit',
													  'comment_status'	  => 'closed',
													  'ping_status'	  	  => 'open',
													  'guid'			  => $imageurlguid,
													  'post_name'	  	  => $postname,
													  'post_modified' 	  => $postdate,
													  'post_modified_gmt' => $postdate,
													  'post_parent'		  => $postid_id,	
													  'post_type'		  => 'attachment',
													  'post_mime_type' 	  => $imagetype	
												);
												$thumbnailid 			  =	 wp_insert_post( $insrt_post_img );
												//==Insert Image===//
												$postmetaarray["_thumbnail_id"] = $thumbnailid;

												file_put_contents(dirname(__FILE__)."/image.txt", print_r( "Thumbnail Id: ".$thumbnailid ,true)."\n", FILE_APPEND);
											}
										}


					       		 	}else{
					       		 		$postdate 		=	date("Y-m-d H:i:s");

					       		 		$insrt_post 	= array(
					       		 		  'post_author'	  	  => 1,	
					       		 		  'post_date'	  	  => date("Y-m-d H:i:s"),
					       		 		  'post_date_gmt' 	  => date("Y-m-d H:i:s"),
					       		 		  'post_content'  	  => $description,
										  'post_title'    	  => $title,
										  'post_excerpt'  	  => $description,
										  'post_status'  	  => 'publish',
										  'comment_status'	  => 'closed',
										  'ping_status'	  	  => 'closed',
										  'guid'			  => $content,
										  'post_name'	  	  => sanitize_title($title),
										  'post_modified' 	  => date("Y-m-d H:i:s"),
										  'post_modified_gmt' => date("Y-m-d H:i:s"),
										  'post_type'		  => 'post'
										);

										$postid_id  		  = 	wp_insert_post( $insrt_post );
										if ($postid_id) {
											//file_put_contents(dirname(__FILE__).'/postid_idif.log', print_r($postid_id,true),FILE_APPEND);
										}else{
											//file_put_contents(dirname(__FILE__).'/postid_idelse.log', print_r($postid_id,true),FILE_APPEND);
										}
										$cat_id			=	get_cat_ID( $category );
					       		 		//file_put_contents(dirname(__FILE__).'/cat_id.log', print_r($cat_id,true),FILE_APPEND);
					       		 		wp_set_post_categories( $postid_id, $cat_id, false );

										add_post_meta( $postid_id, 'sm:block', $smblock, false );
										add_post_meta( $postid_id, 'syndication_permalink', $link, false );
										add_post_meta( $postid_id, 'enclosure', $URL, false );
										echo "Post Deleted and Created.";

										$postmetaarray 		  =  array();
										
										$siteurl		   	  =  get_site_url();
										$img_name			  =  str_replace("http://iris.scanmine.com/go/pub/bilport/imgpb/", "", $URL);

										if(!empty($enclosure)){
											
											$source 			= $URL;
											$destination 		= $siteurl."/wp-content/uploads/".$img_name;
											
											if(@copy($source,$destination)){
												
												$postmetaarray["enclosure"] = $enclosure;
												//==Insert Image===//
												$imageurlguid 	= 	$siteurl."/wp-content/uploads/".$img_name."";
												$posttitle 		= 	preg_replace('/\.[^.]+$/', '', $img_name);
												$postname 		= 	sanitize_title($posttitle);
												
												$insrt_post_img	= array(
								       		 		  
								       		 		  'post_author'	  	  => 1,	
								       		 		  'post_date'	  	  => $postdate,
								       		 		  'post_date_gmt' 	  => $postdate,
								       		 		  'post_title'    	  => $posttitle,
													  'post_status'  	  => 'inherit',
													  'comment_status'	  => 'closed',
													  'ping_status'	  	  => 'open',
													  'guid'			  => $imageurlguid,
													  'post_name'	  	  => $postname,
													  'post_modified' 	  => $postdate,
													  'post_modified_gmt' => $postdate,
													  'post_parent'		  => $postid_id,	
													  'post_type'		  => 'attachment',
													  'post_mime_type' 	  => $imagetype	
												);
												$thumbnailid 			  =	 wp_insert_post( $insrt_post_img );
												//==Insert Image===//
												$postmetaarray["_thumbnail_id"] = $thumbnailid;

												file_put_contents(dirname(__FILE__)."/image.txt", print_r( "Thumbnail Id: ".$thumbnailid ,true)."\n", FILE_APPEND);
											}
										}
					       		 	}

					       		}
					        }
				    	}
					}
			}
		}

		echo "<div class='update-div'><a href='http://iris.scanmine.com/bilport/wp-admin/admin.php?page=rssupdater&action=update' name='update' id='update' class='update-button' />Update Rss</a></div>";

	}
}
$rss_updater 	=	new RSS_Updater;

?>
<style type="text/css">
.update-button{ padding: 2px 7px 2px 7px; color: #FFF; background: #00BFFF; font-weight: bold;
 border-radius: 5px; border-color: #00BFFF; font-size: 24px; border-style: solid; text-decoration: none;}
 .update-button:hover{ background-color: #04A5DB; color: #E6E7E8; border-color: #04A5DB;}
.update-div{margin-top:20px; text-align:center; margin-top: 250px;}
</style>