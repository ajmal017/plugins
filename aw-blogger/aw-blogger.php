<?php
/*
Plugin Name: AW-Blogger
Plugin URI: http://www.google.com
Description: A collection of creating the multiple blogs through one site
Version: 0.3
Author: Developer-A0060
Author URI: a0060@aw-developer.com
License:
*/

require('aw-listpage.php');
require('class/utilitiesFunctions.php');


add_action('admin_menu', 'register_awblogger_plugin_menu');

function register_awblogger_plugin_menu(){

	add_menu_page( 'blogger', 'AW-Blogger', 'manage_options', 'awblogger','my_blogger_page', '', 6 );
	// add_submenu_page('awblogger', 'AW-Blogger-List', 'Blog List', 'manage_options','aw_blogger_listaw', 'my_blogger_list_page');

	$hook = add_submenu_page('awblogger', 'AW Blog List', 'Blog List', 'manage_options','aw_blogger_list', 'newSiteList');
	add_action( "load-$hook", 'add_options' );

	/*$hook2 = add_submenu_page ('awblogger', 'Domain-Transfer', 'Domain Transfer', 'manage_options', 'aw_domain_transfer','domain_Transfer');
	add_action( "load-$hook2", 'add_options' );
	*/
	add_submenu_page('awblogger', 'AW-Test-Config', 'Test Config File', 'manage_options','aw_blogger_config_test', 'test_config');
	add_submenu_page('awblogger', 'AW-Test-Domain', 'Test Config domain', 'manage_options','aw_blogger_test_domain', 'test_domain');

	add_action( 'admin_head','admin_header');
}

add_filter( 'set-screen-option', 'set_screen', 10, 3 );
function set_screen( $status, $option, $value ) {
	return $value;
}

/*
function domain_Transfer(){

	include "domain_transfer.php";

}*/

//** function to Add options **//
function add_options() {
  global $myListTable;
  $option = 'per_page';
  $args = array(
         'label' => 'Number of sites showing',
         'default' => 100,
         'option' => 'sites_per_page'
         );
  add_screen_option( $option, $args );

  $myListTable = new Site_List();
}


function test_config(){
	include('site-config-test.php');
}

function admin_header() {
    $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
    if( 'aw_blogger_list' != $page )
    return;
    echo '<style type="text/css">';
    echo '.wp-list-table .column-ID { width: 5%; }';
    echo '.wp-list-table .column-site_name { width: 20%; }';
    echo '.wp-list-table .column-site_url { width: 20%; }';
    echo '.wp-list-table .column-site_language { width: 12%;text-align: center;}';
    echo '.wp-list-table .column-registered_at { width: 15%;}';
    echo '.wp-list-table .column-actions { width: 30%;}';
    echo '</style>';
}


//** New Function for the listing of the Sites. **//
function newSiteList(){

	global $myListTable;

	//**############ code section to delete site. #############3**//
	if( isset($_GET['action']) && $_GET['action'] == 'delete'){

		require dirname(__FILE__)."/functions/delete_site.php";
	}

	?>
		<div class="wrap">
			<h2>Aw Blog List</h2>
			<hr/>
			<div id="poststuff">
				<div id="post-body" class="">
					<div id="post-body-content">
					<?php

						//** Error Message Block **//
						$message = '';

						if( $_GET['error'] == 'true'){

							$message = '<div style="color: red; text-align: center; margin-bottom:10px;"> Error Occured While trying to delete site, Try again later.</div>';

						}elseif( $_GET['success'] == 'true'){

							$message = '<div style="color: green; text-align: center; margin-bottom:10px;"> Site Deleted Successfully.</div>';

						}elseif( $_GET['dbErr'] == 'true'){

							$message = '<div style="color: red; text-align: center; margin-bottom:10px;"> Error Connecting to database, Try again later.</div>';

						}elseif( $_GET['siteErr'] == 'true'){

							$message = '<div style="color: red; text-align: center; margin-bottom:10px;"> No Such Site Found.</div>';

						}

						echo $message;
					?>
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$myListTable->prepare_items();
								echo '<input type="hidden" name="page" value="ttest_list_table">';
    							$myListTable->search_box( 'search', 'search_id' );
								$myListTable->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
			<?php
				$defaultUser	 	=	base64_decode('c2Nhbm1pbmU=');
				$defaultPass		=	base64_decode('QmxvR3MkU2NhbiEyMw==');

				echo '<form action="#" target="_blank" method="post" name="myWpForm" style="display: none;">
						<input type="hidden" name="log" value="'.$defaultUser.'" id="log">
					 	<input type="hidden" name="pwd" value="'.$defaultPass.'" id="pwd">
					</form>';

			?>
			<script>
				function AutoWpLogin( url ){

					document.forms["myWpForm"].action = url; //will set it

					document.forms["myWpForm"].submit();

					return false;
				}
			</script>
		</div>

	<?php
}
//** function newSiteList ends **//



/*test functon*/


function test_domain(){
	error_reporting(0);
	global $site_created;
	$themes = wp_get_themes();
	$all_plugins = get_plugins();
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	//** code section to delete site. **//


	if(isset($_POST['action']) && $_POST['action']=="add_site")
	{
		//require dirname(__FILE__)."/functions/create-site.php";
		require dirname(__FILE__)."/functions/createsite_domain.php";
		if($site_created)
		{
				//echo "Feeds updated successfully";
			//require dirname(__FILE__)."/functions/site-config.php";
		}
	}else{
		$html	= 	'<div class="wrap">
						<div id="add-new-feed-site">Add New Feed Based Sites</div>
						<form enctype="multipart/form-data" action="" method="post">';
		/*$html	.=		'<table class="form-table">';
		$html	.= 			'<tr class="form-field form-required">
								 <th scope="row">Select Template</th>
								 <td>';
								$html	.= 	utilitiesFunctions::getBloglist();
		$html	.= 				'</td>
							</tr>';
		$html	.= 			'<tr class="form-field form-required">
								<th scope="row">Enter Config url</th>
									<td>
										<textarea  name="site_config" id="site_config" placeholder="Enter Config Url."></textarea>
										<p>Note: Enter Multiple Congif files url. Seperated by comma(,).</p>
									</td>
							</tr>
						</table>*/
		$html   .=  '<div class="main_dom">
						<div class="field_row">';
								$html	.= 	utilitiesFunctions::getBloglist();
		$html   .=		'</div>
						<div class="field_row">
							<textarea  name="site_config" id="site_config" placeholder="Enter Config Url."></textarea>
							<p>Note: Enter Single Congif files url.</p>
						</div>
					</div>';

		$html   .=	'<div class="main_dom">
						<div class="field_row">
							<input type="text" name="domain_name_url" id="domain_name_url" class="domain_name" placeholder="Domain URL">
							<span class="error_msg_domain_name_url"> Please Enter Domain Name. </span>
						</div>
						<div class="field_row">
							<input type="text" name="dom_alias" id="dom_alias" class="domain_name" placeholder="Domain Alias">
							<span class="error_msg_domain_alias"> Please Enter Domain ALias. </span>
						</div>
						<div class="field_row">
							<input type="text" name="config_name" id="config_name" class="domain_name" placeholder="Config File Name">
							<span class="error_msg_config_name"> Please Enter Config File Name. </span>
						</div>
					</div>

					  <p class="submit submt_p">
					  	<input type="submit" value="Add Site" class="submt_btn button button-primary" id="add-aw-blog-site" name="add-aw-blog-site">
					  	<input type="hidden" name="action" value="add_site" />
					  	<input type="hidden" name="site_hurl" value="" />
					  	<input type="hidden" name="site-s-slug" value="" />
					  </p>
					  </form>
					</div>';

		echo $html;
	}
}

//** Code Blog to create the List Functionalty. **//


function my_blogger_page()
{
	error_reporting(0);
	global $site_created;
	$themes = wp_get_themes();
	$all_plugins = get_plugins();
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	//** code section to delete site. **//


	if(isset($_POST['action']) && $_POST['action']=="add_site")
	{
		require dirname(__FILE__)."/functions/create-site.php";
		if($site_created)
		{
				//echo "Feeds updated successfully";
			//require dirname(__FILE__)."/functions/site-config.php";
		}
	}else{
		$html	= 	'<div class="wrap">
						<h2 id="add-new-feed-site">Add New Feed Based Sites</h2>
						<form enctype="multipart/form-data" action="" method="post">';
		/*$html	.=		'<table class="form-table">';

		$html	.= 	'	<tr class="form-field form-required">

							<th scope="row">Select Template</th>
							<td>';
								$html	.= 	utilitiesFunctions::getBloglist();
					$html	.= 	'</td>

						</tr>';
// <input type="text" name="site_config" id="site_config" placeholder="Enter Config Url." />
		$html	.= 	'<tr class="form-field form-required">

						<th scope="row">Enter Config url</th>

						<td>
							<textarea  name="site_config" id="site_config" placeholder="Enter Config Url."></textarea>
							<p>Note: Enter Multiple Congif files url. Seperated by comma(,).</p>
						</td>
							</tr>

		</table>*/

		$html   .= '<div class="main_dom">
						<div class="field_row">';
		$html	.= 			utilitiesFunctions::getBloglist();
		$html   .=  	'</div>
						<div class="field_row">
							<textarea  name="site_config" id="site_config" placeholder="Enter Config Url."></textarea>
							<p>Note: Enter Single Congif files url.</p>
						</div>
					</div>
					<p class="submit submt_p"><input type="submit" value="Add Site" class="submt_btn button button-primary " id="add-aw-blog-site" name="add-aw-blog-site"><input type="hidden" name="action" value="add_site" /><input type="hidden" name="site_hurl" value="" /><input type="hidden" name="site-s-slug" value="" /></p>	</form>
				</div>';

		echo $html;
	}
}
////*****ADDING THE CUSTOM JAVASCRIPTS*****////////////////////////

function js_scripts_callback() {
	wp_enqueue_script(
		'js-script',
		plugins_url() . '/aw-blogger/js/js-script.js',
		array( 'jquery' )
	);

	wp_register_script('aw_select2_script', plugins_url( '/js/select2.min.js' , __FILE__ ));
	wp_enqueue_script('aw_select2_script');

	//** Load Select2 Styling. **//
	wp_enqueue_style( 'aw_select2', $plugin_url . 'css/select2.min.css' );
	wp_enqueue_style( 'custom-style-css', $plugin_url . '/wp-content/plugins/aw-blogger/css/custom-style-css.css' );
}

///***************************************/////////////////////////


add_action( 'init', 'js_scripts_callback' );
/*
function aw_blogger_tables_install() {
   global $wpdb;

   $tableName = $wpdb->prefix."aw_blog_sites";
	$charset_collate = $wpdb->get_charset_collate();
	// create the ECPT metabox database table
	if($wpdb->get_var("show tables like '$tableName'") != $tableName)
	{
		$sql = "CREATE TABLE `wordpress281114`.`".$tableName."` (
				`ID` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`site_name` VARCHAR( 200 ) NOT NULL ,
				`site_slug` VARCHAR( 25 ) NOT NULL ,
				`site_theme` VARCHAR( 50 ) NOT NULL ,
				`site_theme_options` TEXT NOT NULL ,
				`site_plugins` TEXT NOT NULL ,
				`site_plugins_options` TEXT NOT NULL ,
				`site_status` SMALLINT NOT NULL DEFAULT '1'";



		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'aw_blogger_tables_install');  */



//** Function for listing all blogs **//
function my_blogger_list_page(){
	// global $myListTable;
	//   echo '</pre><div class="wrap"><h2>My List Table Test</h2>';
	//   $myListTable->prepare_items();
	// return true;

	global $wpdb;
	error_reporting(0);
	global $site_created;
	$themes = wp_get_themes();
	$all_plugins = get_plugins();
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	//**############ code section to delete site. #############3**//
	if( isset($_GET['action']) && $_GET['action'] == 'delete'){

		require dirname(__FILE__)."/functions/delete_site.php";


	}
	//** ####### delete section ends here ######################**//

	if(isset($_POST['action']) && $_POST['action']=="add_site")
	{
		require dirname(__FILE__)."/functions/create-site.php";
		if($site_created)
		{
				echo "Feeds updated successfully";
			//require dirname(__FILE__)."/functions/site-config.php";
		}
	}else{

		if( $_POST['aw_page'] ){
			$page = trim($_POST['aw_page']);
			$url  =  add_query_arg( array( 'aw_page' =>  $page ), admin_url( 'admin.php?page=aw_blogger_listaw' ) );
			// echo add_query_arg( array( 'page' => 'asc', 'orderby' => 'title' ), admin_url( 'admin.php?page=aw_blogger_listaw' ) );

			echo "<script> window.location = '$url' </script>";
			exit;


		};

		if( isset( $_POST['s_term'] ) ){

			$searchTerm =  $_POST['s_term'];
			$sql = "SELECT * FROM wp_aw_blog_sites WHERE `site_name` LIKE '".$searchTerm."%' OR `site_slug` LIKE '".$searchTerm."%' ORDER BY 'site_name' ASC";
		}else{

			$limit = 25;
			if( isset( $_GET['aw_page'])){
				$page = trim( $_GET['aw_page'] );
			}else{
				$page = 0;
			}

			$start  =  	$page  	* $limit;
			$end 	=	$start + $limit;
			$sql 	=  	"SELECT * FROM wp_aw_blog_sites ORDER BY `site_name` ASC LIMIT $start, $limit ";
		}

		$result =	$wpdb->get_results($sql);

		$sql 		= 	"SELECT * FROM wp_aw_blog_sites";
		$result1 	=	$wpdb->get_results($sql);

		$count = count($result1);
		$page  = 1;
		$option =  array();
		$limit  = 25;
		while( $count > 0 ){
			$pageID 			= 	$page - 1;
			$option[$pageID] 	= 	"<option selected value='$pageID'>Page $page</option>";
			$count 				= 	$count - $limit;
			$page++;
		}




		//** Error Message Block **//
		$message = '';

		if( $_GET['error'] == 'true'){

			$message = '<div style="color: red; text-align: center; margin-bottom:10px;"> Error Occured While trying to delete site, Try again later.</div>';

		}elseif( $_GET['success'] == 'true'){

			$message = '<div style="color: green; text-align: center; margin-bottom:10px;"> Site Deleted Successfully.</div>';

		}elseif( $_GET['dbErr'] == 'true'){

			$message = '<div style="color: red; text-align: center; margin-bottom:10px;"> Error Connecting to database, Try again later.</div>';

		}elseif( $_GET['siteErr'] == 'true'){

			$message = '<div style="color: red; text-align: center; margin-bottom:10px;"> No Such Site Found.</div>';

		}

		$optionList = '';

		foreach ($option as $key => $value) {
			if( $key == $_GET['aw_page'] ){
				$optionList	.= 	$value;
			}else{
				$opt  = str_replace("selected", "", $value );
				$optionList	.= $opt;
			}
		}


		$html	= 	'<div class="wrap">
						<h2>List of Sites</h2>';

		$admin_link = admin_url( 'admin.php?page=aw_blogger_listaw' );
		//** pagination Form. **//
		$html	.=		'<div style="float: left;">';

		$html	.=		'<form action="'.$admin_link.'" method="POST">';

		$html	.=		'<select name="aw_page">
							'.$optionList.'
						</select> &nbsp;';
		$html	.=		'<input type="submit" class="button" value="GO">';

		$html	.=		'</form>';

		$html	.=		'</div>';

		$html	.=		'<div style="float: right;">';
		$html	.=		'<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
		$html	.=		'<input type="search" placeholder="Enter site title here" name="s_term">';
		$html	.=		'<input type="submit" class="button" value="Search">';
		$html	.=		'<form action="#">';
		$html	.=		'</form>';
		$html	.=		'</div>';

		$html	.=		'<table style="text-align: center;" class="wp-list-table widefat fixed users">';
		$html	.=		$message;

		$html 	.=	'<thead >
						<tr style="text-align: center;" class="manage-column column-cb check-column" >
							<th style="width: 5%; text-align: center;">#</th>
							<th style="width: 25%; text-align: center;">Site Name </th>
							<th style="width: 20%; text-align: center;">Site Language </th>
							<th style="width: 20%; text-align: center;">Registered on</th>
							<th style="width: 30%; text-align: center;">  Actions </th>
						</tr>

					</thead>';
		$html 	.=	'<tbody data-wp-lists="list:user" id="the-list">';


		//** Build serial Number as per the page. **//

		if( isset( $_GET['aw_page']) ){
			$limit = 25;
			$pageIndex = trim( $_GET['aw_page'] );
			$i =  ( $pageIndex * $limit ) + 1;
		}else{
			$i = 1;
		}

		if( empty( $result )){
			$html 	.=	'<tr><td colspan="4"><p style="text-align: center; font-weight: bold;">No Record Found.</td></tr>';
		}else{


			foreach ($result as  $value) {

				if( $value->site_status == 1 ){
					$status =	"Active";
				}else{
					$status =	"Inactive";
				}

				$Registered_date 	= 	date("d-F-Y", strtotime( $value->registered_at ));
				$deleteMesage 		= 	"'Are you sure you want to Site?'";
				$deletUrl 			=	$_SERVER['REQUEST_URI']."&action=delete&id=".$value->ID;
				$SiteUrlAdmin 		=	$value->site_url."wp-login.php";

				$jsFunction			=	"AutoWpLogin('".$SiteUrlAdmin."')";

				$defaultUser	 	=	base64_decode('c2Nhbm1pbmU=');
				$defaultPass		=	base64_decode('QmxvR3MkU2NhbiEyMw==');

				$html 	.=	'<tr>
									<td><b>'.$i++.'</b></td>
									<td>'.$value->site_name.'</td>
									<td>'.$value->site_language.'</td>
									<td>'.$Registered_date.'</td>
									<td>
									<a href="'.$SiteUrlAdmin.'" onclick="return '.$jsFunction.'" class="button button-primary" title="Go to Dashboard"><b>Dashboard</b></a> &nbsp; | &nbsp;
									<a href="'.$value->site_url.'" target="_blank" class="button" title="Go to Blog"><b>Visit Site</b></a> &nbsp; | &nbsp;
									<a href="'.$deletUrl.'"  onclick="return confirm('.$deleteMesage.')" style="color: red;" class="button" title="Delete this Blog"><b>Delete</b></a>

									</td>
								</tr>';
			}
		}

		$html	.= 	'</tbody>';

		$html	.= 	'</table>';

		// $admin_link = admin_url();
		// //** Div For Pagination **//
		// $html	.= 	'<div style="text-align: center;">';
		// $html	.= 	'<a href="'.$admin_link.'admin.php?page=aw_blogger_listaw&p=1">page 1</a> &nbsp;';
		// $html	.= 	'<a href="'.$admin_link.'admin.php?page=aw_blogger_listaw&p=2">page 2</a> &nbsp;';
		// $html	.= 	'<a href="'.$admin_link.'admin.php?page=aw_blogger_listaw&p=3">page 3</a> &nbsp;';
		// $html	.= 	'</div>';

		$html  	.= '</div>';

		$html   .= '<form action="#" target="_blank" method="post" name="myWpForm" style="display: none;">
						<input type="hidden" name="log" value="'.$defaultUser.'" id="log">
					 	<input type="hidden" name="pwd" value="'.$defaultPass.'" id="pwd">
					</form>';
		echo $html;
	}
}//** Fucntion ends here. **//



// function getOPtionList(){

// }

?>