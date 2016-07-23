<?php
/*
Plugin Name: AW-Templates
Plugin URI: http://www.google.com
Description: List of template Blogs.
Version: 1.0
Author: Developer-G0947
Author URI: G0947@aw-developer.com
License:
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly Accessed

class Aw_Templates{

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
		add_menu_page( 'Blog Templates', 'AW-Templates', 'manage_options', 'awTemplate', array(&$this, 'my_template_page'), '', 5 );		
	}

	//** Add Custom Css for Slider Function. **//
	// function aw_enqueue_script(){
	// 	wp_register_script('aw_jobInfo_script', plugins_url( '/js/aw_jobInfo_script.js' , __FILE__ ));
	// 	wp_enqueue_script('aw_jobInfo_script');
	// }

	
	//** Load StyleSheet **//
	// function aw_load_plugin_css() {
	//     $plugin_url = plugin_dir_url( __FILE__ );
	//     wp_enqueue_style( 'aw_style', $plugin_url . 'css/aw_style.css' );
	// }


	
	//** widgetCopier page.  **//
	function my_template_page(){

		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}

		$BlogArray = array( array( 'type' => 'One Column Template', 'language' => 'English', 'url'   => 'http://iris.scanmine.com/templates/english/template_one_en/'),
							array( 'type' => 'One Column Template', 'language' => 'Swedish', 'url'   =>	'http://iris.scanmine.com/templates/swedish/template_one_sw/'),
							array( 'type' => 'One Column Template', 'language' => 'Norwegian', 'url' => 'http://iris.scanmine.com/templates/norwegian/template_one_no/'),

							array( 'type' => 'Two Column Template', 'language' => 'English', 'url'	=>	'http://iris.scanmine.com/templates/english/template_two_en/'),
							array( 'type' => 'Two Column Template', 'language' => 'Swedish', 'url'	=> 	'http://iris.scanmine.com/templates/swedish/template_two_sw/'),
							array( 'type' => 'Two Column Template', 'language' => 'Norwegian', 'url' 	=>	'http://iris.scanmine.com/templates/norwegian/template_two_no/'),

							array( 'type' => 'Three Column Template', 'language' => 'English', 'url' => 'http://iris.scanmine.com/templates/english/template_three_en/'),
							array( 'type' => 'Three Column Template', 'language' => 'Swedish', 'url' => 'http://iris.scanmine.com/templates/swedish/template_three_sw/'),
							array( 'type' => 'Three Column Template', 'language' => 'Norwegian', 'url' => 'http://iris.scanmine.com/templates/norwegian/template_three_no/'),

							array( 'type' => 'Multi Column Template', 'language' => 'English', 'url' => 'http://iris.scanmine.com/templates/english/template_multi_en/'),
							array( 'type' => 'Multi Column Template', 'language' => 'Swedish', 'url' => 'http://iris.scanmine.com/templates/swedish/template_multi_sw/'),
							array( 'type' => 'Multi Column Template', 'language' => 'Norwegian', 'url' => 'http://iris.scanmine.com/templates/norwegian/template_multi_no/'),
							
							array( 'type' => 'Genesis Newspro Template', 'language' => 'English', 'url' => 'http://iris.scanmine.com/templates/genesis/newspro/'),
							array( 'type' => 'Genesis Newsfeed Widget Template', 'language' => 'English', 'url' => 'http://iris.scanmine.com/templates/genesis/widget_template/'),
					);

		$html	= 	'<div class="wrap">
						<h2>List of Blog Templates.</h2><hr><br>';

		$html	.=		'</form>';		
		// $html	.=		'</div>';

		$html	.=		'<table style="text-align: center; width: 98%;" class="wp-list-table widefat fixed users">';
		$html	.=		$message;

		$html 	.=	'<thead > 
						<tr style="text-align: center;"" class="manage-column column-cb check-column" >
							<th style="width: 5%; text-align: center;"><b>#</b></th> 
							<th style="width: 25%; text-align: center;"><b>Template Type </b></th>							
							<th style="width: 20%; text-align: center;"><b>Template Language</b></th>							
							<th style="width: 50%; text-align: center;"><b>Template Url </b></th>							
						</tr>
						
					</thead>';
		$html 	.=	'<tbody data-wp-lists="list:user" id="the-list">';

		$i = 1;
		foreach ( $BlogArray  as $blog ) {
			$html 	.=	'<tr>
				<td>'.$i++.'</td> 
				<td>'.$blog['type'].'</td> 								
				<td>'.$blog['language'].'</td> 								
				<td>
				<a href="'.$blog['url'].'wp-login.php" class="button button-primary">Dashboard</a> &nbsp; | &nbsp;
				<a href="'.$blog['url'].'" target="_blank" class="button">Visit Site</a> &nbsp;
				

				</td> 								
			</tr>';
		}
		
		$html	.= 	'</tbody>';
		
		$html	.= 	'</table>';

		$html  	.= '</div>';
		
		echo $html;
	}
}//** Class ends here. **//


$Aw_Templates = new Aw_Templates;


?>