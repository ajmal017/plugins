<?php
include_once('../../../../wp-load.php');
$id = $_POST['post_id'];

if(isset($id) && !empty($id)){

	$res = "";
	global $wpdb;
	$result = $wpdb->get_row("SELECT * FROM  ".$wpdb->prefix."posts WHERE  `ID` = '".$id."' ");
		if($result){
			$res .= $result->post_title;
			$res .= "SEPUNALL";			
			$content = preg_replace("/<img[^>]+\>/i", "", $result->post_content); 
			$res .= str_replace("<p></p>","",$content);
			$res .= "SEPUNALL";
			$res .= $result->post_status;
			$res .= "SEPUNALL";
			$res .= $result->guid;
			$res .= "SEPUNALL";
		 //===========Fetch Post Meta=========================//
			 
			 $result_post_meta = $wpdb->get_row("SELECT * FROM  ".$wpdb->prefix."postmeta WHERE  `post_id` = '".$id."' AND meta_key='sm:block'");	
			 $res .= $result_post_meta->meta_value;	
			 $res .= "SEPUNALL";	
			 $result_post_meta_en = $wpdb->get_row("SELECT * FROM  ".$wpdb->prefix."postmeta WHERE  `post_id` = '".$id."' AND meta_key='enclosure1'");	
			 $res .= $result_post_meta_en->meta_value;				 
			//===================================================//
			echo $res;
		}else{
			echo 'no result found';
		}
		
		
}



?>