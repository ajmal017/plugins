<?php
/*
Plugin Name: AW Custom Data
Plugin URI: 
Description: Allow Admin To Set the Notification IDs.
Version: 1.0
Author: Developer-G0947
Author URI: G0947@aw-developer.com
License:
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly Accessed

class AW_customData{

	//** Consntructor **//
	function __construct() {
		
		//** Register menu. **//
		add_action('admin_menu', array(&$this, 'register_awjobinfo_plugin_menu') );

		//** Load Style Sheet. **//
		add_action( 'admin_enqueue_scripts',  array(&$this, 'aw_load_plugin_css') );

		//** Load Admin Script. **//
		add_action( 'admin_enqueue_scripts',  array(&$this, 'aw_enqueue_script') );
		
		/* Shortcodes */
		// add_shortcode( 'custom_data_url',  array(&$this, 'getCustomData') );		
		// add_shortcode( 'get_data_by_url',  array(&$this, 'getCustomDataUrl') );
		add_shortcode( 'custom_data',  array(&$this, 'getCustomDataUrl') );		
		
		// add_filter('widget_text', 'do_shortcode');

	}


	//** Register menu Item. **//
	function register_awjobinfo_plugin_menu(){
		// add_menu_page( 'Custom Data', 'Custom Data', 'manage_options', 'awData', array(&$this, 'my_jobinfo_page'), '', 27 );		
	}

	//** Add Custom Css for Slider Function. **//
	function aw_enqueue_script(){
		wp_register_script('aw_jobInfo_script', plugins_url( '/js/aw_jobInfo_script.js' , __FILE__ ));
		wp_enqueue_script('aw_jobInfo_script');
	}

	
	//** Load StyleSheet **//
	function aw_load_plugin_css() {
	    $plugin_url = plugin_dir_url( __FILE__ );
	    wp_enqueue_style( 'aw_style', $plugin_url . 'css/aw_style.css' );
	}


	
	//** widgetCopier page.  **//
	function my_jobinfo_page(){
	?>
		<div class="wrap">		
			<div class="icon32" id="icon-themes"><br></div>
			<h2 class="nav-tab-wrapper">
				<a href="?page=awEmail&amp;tab=awData" class="nav-tab">Custom Data Settings</a>				
			</h2>

			<?php if( $_GET['tab'] == 'awData' || !isset( $_GET['tab'] )): ?>

				<?php
					if( isset($_POST['updateUrl'])){											
						$url = trim($_POST['url']);
						update_option( 'custom_data_url', $url );
						echo '<div class="aw_post_message aw_post_success" style="width: 70%">Url Updated Successfully</div>';
					}
				?>
				<form method="post" name="aw_jobInfo_form" id="aw_jobInfo_form" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI'];?>">
					<div id="aw-settings-container"> <!-- <h3 class="aw-type-title">Widget Copy</h3>-->											
						<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
							<label class="aw-feature-title">Url: </label>
							<div class="aw-feature">
								<input name="url" type="text" id="url" value=" <?php echo get_option( 'custom_data_url', true ); ?> " placeholder="Enter url here" style="width: 400px; padding: 10px;" required/>
								<p id="jobInfoMessage"></p>
							</div>
							<div class="aw-feature-desc">								
							</div>
							<div style="clear:both;"></div>
						</div>						

						<div class="aw-button-container">
							<input type="hidden" name="updateUrl" value="1" />
							<input type="submit" value="Update Url" id="jobInfoSubmit" class="aw-save-settings" name="submit">							
						</div>
					</div><!-- // SETTINGS CONTAINER -->
				</form><!-- // END FORM -->	
			<?php endif; ?>

		</div>	

	<?php
	}


	/**/	
	function getCustomData(){
		$file_url = get_option( 'custom_data_url', true );
		if( !empty($file_url) ){
			$content  = file_get_contents($file_url);
		}else{
			$content  = '';
		}	
		return $content;
	}

	function getCustomDataUrl( $atts ) {
		
	    $attr = shortcode_atts( array(
	        'url' => '',	        
	    ), $atts );

	    $url = (string)$atts['url'];

		if( !empty($url) ){
			$file_url = (string)$url;
			$content  = file_get_contents( $file_url, true);
		}else{
			$content  = '';
		}

		return $content;
	}
	

}//** Class ends here. **//


$AW_customData = new AW_customData;


?>