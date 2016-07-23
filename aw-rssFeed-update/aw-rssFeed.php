<?php

/*
Plugin Name: AW-rssFeed
Plugin URI: http://www.artworldwebsolutions.com
Description:  Update RSS Feed List.
Version: 1.0
Author: Developer-G0947
Author URI: G0947@aw-developer.com
License:
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly Accessed

class Aw_rssFeed{

	//** Consntructor **//
	function __construct() {
		
		//** Register menu. **//
		add_action('admin_menu', array(&$this, 'register_awtemplate_plugin_menu') );

		//** Load Style Sheet. **//
		//add_action( 'admin_enqueue_scripts',  array(&$this, 'aw_load_plugin_css') );

		//** Load Admin Script. **//
		//add_action( 'admin_enqueue_scripts',  array(&$this, 'aw_enqueue_script') );

	}


	//** Register menu Item. **//
	function register_awtemplate_plugin_menu(){
		add_menu_page( 'RSS FEED', 'AW-RssFeedList', 'manage_options', 	'awRssList', 	array(&$this, 'my_template_page'), '', 29 );
		add_submenu_page( 'awRssList', 'RSS FEED ADD', 'AW-RssFeedAdd', 'manage_options', 'awRssListAdd', 	array(&$this, 'my_template_add_page'));		
	}

	
	//** Function to Display the List of Lists **//
	function my_template_page(){

		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		global $wpdb;

		//** *******************************Actions *****************************//
		
		//** Delete Action  **//
		if( isset($_GET['action']) && $_GET['action'] == 'delete'){
			$id	 	= 	trim($_GET['id']);
			$sql 	= 	"DELETE FROM wp_rssfilelist WHERE id = $id";
			$Update	= 	$wpdb->query($sql);
		}

		//** Activate Action  **//
		if( isset($_GET['action']) && $_GET['action'] == 'activate'){
			$id	 	= 	trim($_GET['id']);
			$sql 	= 	"UPDATE wp_rssfilelist SET `status` = 'Active' WHERE id = $id";
			$Update	= 	$wpdb->query($sql);
		}

		//** Deactivate Action  **//
		if( isset($_GET['action']) && $_GET['action'] == 'deactivate'){
			$id	 	= 	trim($_GET['id']);
			$sql 	= 	"UPDATE wp_rssfilelist SET `status` = 'Inactive' WHERE id = $id";
			$Update	= 	$wpdb->query($sql);
		}

		//***********************************************************************//
		
		$rssFileList 	= $wpdb->get_results( "SELECT * FROM wp_rssfilelist" );			

		$html	= 	'<div class="wrap">
						<b><p id="updateMessage" align="center"></p></b>
						<h2 style="float:left">List of RSS FEEDS.</h2>
						<p style="float:right;margin-right: 20px;">
						<button class="button button-primary"  id="updateRSSfeed">Update RSS Feed</button></p>';
		$html	.=		'</form>';		
		// $html	.=		'</div>';

		$html	.=		'<table style="text-align: center; width: 98%;" class="wp-list-table widefat fixed users">';
		$html	.=		$message;

		$html 	.=	'<thead > 
						<tr style="text-align: center;"" class="manage-column column-cb check-column" >
							<th style="width: 5%; text-align: center;"><b>#</b></th> 							
							<th style="width: 65%; text-align: center;"><b> Url </b></th>
							<th style="width: 10%; text-align: center;"><b>status </b></th>
							<th style="width: 25%; text-align: center;"><b>Actions </b></th>							
						</tr>
						
					</thead>';
		$html 	.=	'<tbody data-wp-lists="list:user" id="the-list">';


		$i = 1;
		foreach ( $rssFileList  as $feed ) {
			$html 	.=	'<tr>
				<td>'.$i++.'</td>											
				<td><strong>'.$feed->fileurl.'</strong></td>';
			if($feed->status == 'Inactive' ){
				$html 	.=	'<td><p style="background-color: red; color: white; font-weight: bold">'.$feed->status.'</p></td>';
			}else{
				$html 	.=	'<td><p style="background-color: green; color: white; font-weight: bold">'.$feed->status.'</p></td>';	
			}

			if( $feed->status == 'Inactive' ){
				$action 	= 	admin_url( 'admin.php?page=awRssList' )."&action=activate&id=".$feed->id;
				$caption 	= 	"ACTIVATE";
			}else{
				$action 	= 	admin_url( 'admin.php?page=awRssList' )."&action=deactivate&id=".$feed->id;
				$caption 	=	"DEACTIVATE";
			}
			$deletUrl 			=	admin_url( 'admin.php?page=awRssList' )."&action=delete&id=".$feed->id;
			$deleteMesage 		= 	"'Are you sure you want to Site?'";

			$html 	.=	'<td>
					<a href="'.$action.'" class="button button-primary">'.$caption.'</a> &nbsp; | &nbsp;
					<a href="'.$deletUrl.'"  onclick="return confirm('.$deleteMesage.')" style="color:red;" class="button" title="Delete this URL"><b>Delete</b></a> &nbsp;
				</td> 								
			</tr>';
		}
		
		$html	.= 	'</tbody>';
		
		$html	.= 	'</table>';

		$html  	.= '</div>';
		
		$html .='<script>
					jQuery(document).ready(function(){
						jQuery("#updateRSSfeed").click(function(){
							jQuery.get( "http://iris.scanmine.com/wp-content/plugins/aw-rssFeed-update/aw-rssFeedUpdate.php", function(data) {
								jQuery("#updateMessage").html("Success").css({ "background-color": "green", "border-left": "5px solid #ccc", "color": "yellow", "font-size" : "18px" })
     
							})						
							.fail(function() {
								jQuery("#updateMessage").html("Error").css({ "background-color": "red", "border-left": "5px solid #ccc", "color": "yellow", "font-size" : "18px", })
							});	
						});											
					});
			</script>';
		
		echo $html;
	}



	function my_template_add_page(){
		global $wpdb;
 
		if( isset( $_POST['addFeed'] ) ){

			$dateTime 	=	date("Y-m-d H:i:s");
			$url 		=	trim($_POST['feed_url']);
			$status 	=	'Inactive';
			$sql 		=	"INSERT INTO `wp_rssfilelist`( `fileurl`, `create_date`, `status`) VALUES ( '$url', '$dateTime', '$status')";
			$wpdb->query( $sql );
			
			echo "<script>alert('NEW Feed URL ADDED')</script>";
		}	

		$html	= 	'<div class="wrap">
						<h2 id="add-new-feed-site">Add New Feed Url</h2>
						<form action="http://iris.scanmine.com/wp-admin/admin.php?page=awRssListAdd"enctype="multipart/form-data" action="" method="post">';
		$html	.=		'<table class="form-table">';	

		$html	.= 	'	<tr class="form-field form-required">
						
						<th scope="row">Enter Feed url</th>
		
						<td>
							<textarea name="feed_url" id="feed_url" placeholder="Enter Feed Url."></textarea>							
						</td>                        
							</tr>
							
		</table>
			<input type="hidden" name="addFeed" value="1" />
			<p class="submit"><input type="submit" value="Add Feed Url" class="button button-primary" id="add-aw-blog-site" name="add-aw-blog-site">
		</form>
		</div>';

		
		echo $html;
	}

}//** Class ends here. **//


$Aw_rssFeed = new Aw_rssFeed;

?>