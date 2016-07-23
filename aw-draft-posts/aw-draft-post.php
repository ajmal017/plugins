<?php

/*
Plugin Name: AW-Draft-Posts
Plugin URI: http://www.google.com
Description: Allow Admin to publish or Delete the Draft post of the Destination blogs. 
Version: 1.0
Author: Developer-G0947
Author URI: G0947@aw-developer.com
License:
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly Accessed.

class Aw_Draft_Posts{

	//** Consntructor **//
	function __construct() {

		//** Register menu. **//
		add_action('admin_menu', array(&$this, 'register_awdrafts_plugin_menu') );

		//** Load Style Sheet. **//
		add_action( 'admin_enqueue_scripts',  array(&$this, 'aw_load_plugin_css') );

		//** Load Admin Script. **//
		add_action( 'admin_enqueue_scripts',  array(&$this, 'aw_enqueue_script') );
	}

	//** Register menu Item. **//
	function register_awdrafts_plugin_menu(){
		add_menu_page( 'Drafts', 'AW-Draft-Posts', 'manage_options', 'AwDraftPosts', array(&$this, 'my_draft_page'), '', 28 );		
	}

	//** Load StyleSheet **//
	function aw_load_plugin_css() {
	    $plugin_url = plugin_dir_url( __FILE__ );
	    wp_enqueue_style( 'aw_drafts_style', $plugin_url . 'css/aw_draft_posts_style.css' );

	    //** Select 2 stylesheet.**//
	    wp_enqueue_style( 'aw_drafts_select2_style', $plugin_url . 'css/select2.min.css' );
	}

	//** Add Custom Css for Slider Function. **//
	function aw_enqueue_script(){
		wp_register_script('aw_drafts_script', plugins_url( '/js/aw_draft_posts_script.js' , __FILE__ ));
		wp_enqueue_script('aw_drafts_script');
	}


	//** widgetCopier page.  **//
	function my_draft_page(){
	
		global $wpdb;					
		$result  =	$wpdb->get_results("SELECT site_slug FROM wp_aw_blog_sites");	

		$options =  "<option value=''>Select Source Blog</option>";
		$optionsDestination =  "";
		foreach ($result as $value) {
			$options .=  "<option value='".$value->site_slug."'>".$value->site_slug."</option>";					
			$optionsDestination .=  "<option style='padding-bottom: 5px;' value='".$value->site_slug."'>".$value->site_slug."</option>";					
		}					   

	?>
		<div class="wrap">		
			<h2>AW Draft Posts</h2>
			<h4>Feature Allow You to Publish or Delete Draft Posts of a blog.</h4>
			<div class="aw_drafts_container">
				<div class="aw-basic-grey" >
					<form action="" method="post" class="aw-basic-grey">

					    <label>
					        <span>Source Blogs:</span>
					        <select name="aw_sourceBlog" id="aw_sourceBlog">
					        	<?php echo $options; ?>
					        </select>
					    </label>
					    <div class="clear"></div>
					    <input type="hidden" name="serverPath" id="serverPath" value="<?php echo plugins_url() ?>/aw-draft-posts/" />    				       

					</form>		

				</div>

				<div class="aw-basic-grey" id="aw_draft_table_list">
					<!-- block to display Messages. -->
					<div id="aw_Draft_Table_Message"></div>
					
					<!-- block to display Content -->
					<div id="aw_Draft_Table"></div>

				</div>
			</div>


		</div>	<!-- Wrap Div Ends here -->
	<?php
	}		


}//** Class ends here.  **//

$Aw_Draft_Posts = new Aw_Draft_Posts;

?>