<?php
include '../../../wp-load.php';
if($_GET['action'] == "delete"){
	$language = get_option("WPLANG");
	global $wpdb;
	$postid = $_GET['id'];
	$post_title = get_the_title($postid); 
	$_SESSION['posttitle'] = $post_title; 
	$deletepost = $wpdb->query('DELETE FROM '.$wpdb->prefix.'posts WHERE ID = '.$postid);
	$wpdb->query('DELETE FROM '.$wpdb->prefix.'posts WHERE post_parent = '.$postid);
	//====================Delete Image From Folder==========================//
	$result_post_meta_en = $wpdb->get_row("SELECT * FROM  ".$wpdb->prefix."postmeta WHERE  `post_id` = '".$postid."' AND meta_key='enclosure1'");
	//$file = './uploads/'.basename($result_post_meta_en->meta_value);
	$blog_id = $GLOBALS['current_blog']->blog_id;
	$file ="".ABSPATH."wp-content/".IMAGEDIR."/".basename($result_post_meta_en->meta_value);
			
	unlink($file);
	//====================Delete Image From Folder==========================//	
	//Delete Postmeta
	$wpdb->query('DELETE FROM '.$wpdb->prefix.'postmeta WHERE post_id = '.$postid);
	$wpdb->query('DELETE FROM '.$wpdb->prefix.'options WHERE option_name = "custom_post_frontend_'.$postid.'"');
	
	//Delete Post Comments
	$rsComments = $wpdb->get_results('SELECT comment_ID FROM '.$wpdb->prefix.'comments WHERE comment_post_ID = '.$postid);
	if(count($rsComments)>0){
		for($i=0;$i<count($rsComments);$i++){
			if($rsComments[$i]->comment_ID!=""){
				$wpdb->query('DELETE FROM '.$wpdb->prefix.'commentmeta WHERE comment_id="'.$rsComments[$i]->comment_ID.'"');
			}
		}
	}
	
	$wpdb->query('DELETE FROM '.$wpdb->prefix.'comments WHERE comment_post_ID="'.$postid.'"');
	
	$msg = ARTICLEDELETED;
	if($deletepost==1){
		$_SESSION["DELETESUCCMSG"] = "DELETEPOSTS";
	}else{
		$_SESSION["DELETEERRMSG"] = "ERROR DELETE POST";
	}
	
}
?>