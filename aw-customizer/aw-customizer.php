<?php
/*
Plugin Name: AW-Customizer
Plugin URI: http://www.google.com
Description: Allow Editor to change theme options and Ads management.
Version: 0.3
Author: Developer-G0947
Author URI: G0947@aw-developer.com
License:
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AW_Customizer{

	//** Consntructor **//
	function __construct() {
		
		//** Register menu. **//
		add_action('admin_menu', array(&$this, 'register_awcustomizer_plugin_menu') );

		//** Load Style Sheet. **//
		add_action( 'admin_enqueue_scripts',  array(&$this, 'aw_load_plugin_css') );

		//** Load Admin Script. **//
		add_action( 'admin_enqueue_scripts',  array(&$this, 'aw_enqueue_script') );

		//** Load Style Sheet. **//
		add_action( 'wp_enqueue_scripts',  array(&$this, 'aw_enqueue_style') );

		//** Load slider script. **//
		add_action( 'wp_enqueue_scripts',  array(&$this, 'aw_enqueue_slider_script') );

		//** action for puting favicon in header**//
		add_action('wp_head', array(&$this, 'putCustomCssInHeader') );		

		//** action for puting favicon in header**//
		add_action('wp_head', array(&$this, 'putFaviconInHead') );

		//** action for puting Script In Footer**//
		add_action('wp_footer', array(&$this, 'putScriptInFooter') );
		

	}


	//** Add Custom Css for Slider Function. **//
	function aw_enqueue_script(){

		// wp_register_script('aw_jquery_min_js', plugins_url( '/js/jquery.min.js' , __FILE__ ));
		// wp_enqueue_script('aw_jquery_min_js');
		
		wp_register_script('aw_customizer_script', plugins_url( '/js/aw_customizer_script.js' , __FILE__ ));
		wp_enqueue_script('aw_customizer_script');
	}


	//** slider script **//
	function aw_enqueue_slider_script(){
		 
		// wp_register_script('aw_min_js', 'http://code.jquery.com/jquery-1.7.1.min.js' );
		// wp_enqueue_script('aw_min_js');
		  
		// wp_register_script('aw_bjqs_1_3_min_js', plugins_url( '/js/bjqs-1.3.js' , __FILE__ ));
		// wp_enqueue_script('aw_bjqs_1_3_min_js');

		wp_register_script('aw_customizer_slider_script', plugins_url( '/js/aw_customizer_slider_script.js' , __FILE__ ));
		wp_enqueue_script('aw_customizer_slider_script');
	

	}	
	
	//** function To Custom css In Header **//
	function putCustomCssInHeader(){
		$output 	= '';
		$customCss 	=  stripslashes( get_option('aw_custom_css') );

		//** prepare Output Data. **//
		// $output  = '<style>';
		$output = $customCss;
		// $output .= '</style>';

		//** OutPut Css. **//
		echo $output;
			
	}

	function aw_enqueue_style() {

	    //** slider css **//
	    // $plugin_url = plugin_dir_url( __FILE__ );
	    // wp_enqueue_style( 'aw_custom_Slider', $plugin_url . 'css/bjqs.css' );

	    //  //** slider css **//
	    // $plugin_url = plugin_dir_url( __FILE__ );
	    // wp_enqueue_style( 'aw_custom_Slider_demo', $plugin_url . 'css/demo.css' );
	}
	// add_action( 'wp_enqueue_scripts', 'themeslug_enqueue_style' );
	
	
	//** Register menu Item. **//
	function register_awcustomizer_plugin_menu(){
		add_menu_page( 'customizer', 'AW-Customizer', 'manage_options', 'awcustomizer', array(&$this, 'my_customizer_page'), '', 26 );		
	}

	//** Load StyleSheet **//
	function aw_load_plugin_css() {
	    $plugin_url = plugin_dir_url( __FILE__ );
	    wp_enqueue_style( 'aw_style', $plugin_url . 'css/aw_style.css' );
	}

	//** Load StyleSheet **//
	function aw_load_custom_css() {
	    $plugin_url = plugin_dir_url( __FILE__ );
	    wp_enqueue_style( 'aw_custom_style', $plugin_url . 'css/aw_custom_style.css' );	   
	}


	

	//** setup favicon, get the url/**//
	function getFavicon() {
		
		$filePath 	=	plugin_dir_path( __FILE__ )."icon/favicon.png";
		$icon 		= 	get_settings('siteurl').'/wp-content/plugins/aw-customizer/icon/favicon.png';
		
		if(file_exists( $filePath )){
			return $icon;
		}else{
			return null;
		}
	}

	//** setup favicon, get the url/**//
	function getLogo() {
		
		$filePath 	=	plugin_dir_path( __FILE__ )."logo/logo.png";
		$logo 		= 	get_settings('siteurl').'/wp-content/plugins/aw-customizer/logo/logo.png';		
		if(file_exists( $filePath )){			
			return $logo;
		}else{
			return null;
		}
	}

	//** Add script to the footer section.**//
	function putScriptInFooter(){
		echo  stripslashes(get_option( 'aw_footer' ));
	}

	//** Add Favicon tag to script.  **//
	function putFaviconInHead() {
		$path = $this->getFavicon();		
		if( $path != null ){
			echo '<link rel="shortcut icon" href="' .$path. '" type="image/x-icon" /><!-- Favi -->';
		}	
		
	}

	
	//** Customizer page.  **//
	function my_customizer_page(){
	?>
		<div class="wrap">		
		<h2>AW-Customizer Theme Settings</h2>

		<div class="icon32" id="icon-themes"><br></div>
		<h2 class="nav-tab-wrapper">
			<a href="?page=awcustomizer&amp;tab=general" class="nav-tab">General Settings</a>			
			<a href="?page=awcustomizer&amp;tab=style" class="nav-tab">Header Management</a>
			<!-- <a href="?page=awcustomizer&amp;tab=slider" class="nav-tab">Slider Management</a> -->
			<a href="?page=awcustomizer&amp;tab=ads" class="nav-tab">Ads Management</a>
			<a href="?page=awcustomizer&amp;tab=footer" class="nav-tab">Footer Management</a>
		</h2>		

		<?php if( $_GET['tab'] == 'general' || !isset( $_GET['tab'] )): ?>
			<?php 

			  	//** Section for Favicon**//
				if( isset($_POST['UpdateFavicon'])){
					$dir 	= 	plugin_dir_path( __FILE__ )."icon/";
				  	$moved 	=	move_uploaded_file($_FILES['aw_favicon']['tmp_name'], $dir .'favicon.png');
				  	
				  	if( $moved ) {
					  echo "Successfully uploaded";         
					} else {
					  echo "Not uploaded";
					}
				}
				//** Section To delete favicon**//
				if( isset($_GET['dlf']) ){

					$filePath 	=	plugin_dir_path( __FILE__ )."icon/favicon.png";
					
					if(file_exists( $filePath )){
						unlink($filePath);											
					}

					$url = site_url();
					$url1 =	$url."/wp-admin/admin.php?page=awcustomizer";						
					echo "<script>window.location = '".$url1."';</script>";		
					exit();
				}

				//** Section To delete Logo**//
				if( isset($_GET['dl']) ){


					$filePath 	=	plugin_dir_path( __FILE__ )."logo/logo.png";
					
					if(file_exists( $filePath )){
						unlink($filePath);											
					}

					$url = site_url();
					$url1 =	$url."/wp-admin/admin.php?page=awcustomizer";						
					echo "<script>window.location = '".$url1."';</script>";		
					exit();
				}

				//** Section For Logo. **//
				if( isset($_POST['UpdateLogo'])){
					$dir 	= 	plugin_dir_path( __FILE__ )."logo/";
				  	$moved 	=	move_uploaded_file($_FILES['aw_logo']['tmp_name'], $dir .'logo.png');
				  	
				  	if( $moved ) {
					  echo "Successfully uploaded";         
					} else {
					  echo "Not uploaded";
					}
				}

				

			?>
			<form method="post" name="aw_favicon_form" id="aw_favicon_form" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI'];?>">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Favicon Settings</h3>
					<div class="aw-button-container">						
						<a href="<?php echo $_SERVER['REQUEST_URI'];?>&dlf" id="aw_delete_favicon" class="aw-save-settings" style="text-decoration: none;color: black;">Delete Favicon</a>					
					</div>
					<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
						<label class="aw-feature-title">Custom Favicon</label>
						<div class="aw-feature">
							<input type="File" name="aw_favicon" id="aw_favicon" />
						</div>
						<div class="aw-feature-desc">
							<!-- This feature allows you to chage the site favicon. -->
							<?php		

								$filePath 	=	plugin_dir_path( __FILE__ )."icon/favicon.png";
						
								if(file_exists( $filePath )){
									$logo 		= 	get_settings('siteurl').'/wp-content/plugins/aw-customizer/icon/favicon.png';
			
									echo "<img style='height: 25px; width: 25px;'  src='".$logo."' alt='Your Logo'/>";
								}
							?>	
						</div>
						<div style="clear:both;"></div>
					</div>
					<div class="aw-button-container">
						<input type="hidden" name="UpdateFavicon" value="1" />
						<input type="submit" value="Update Favicon" class="aw-save-settings" name="submit">
						<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>
					</div>
				</div><!-- // SETTINGS CONTAINER -->
			</form><!-- // END FORM -->	

			<form method="post" name="aw_logo_form" id="aw_logo_form" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI'];?>">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Logo Settings</h3>
					<div class="aw-button-container">						
						<a href="<?php echo $_SERVER['REQUEST_URI'];?>&dl" id="aw_delete_logo" class="aw-save-settings" style="text-decoration: none;color: black;">Delete Logo</a>					
					</div>
					<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
						<label class="aw-feature-title">Your Logo</label>
						<div class="aw-feature">
							<input type="File" name="aw_logo" id="aw_logo" />
						</div>
						<div class="aw-feature-desc">
						<?php		

							$filePath 	=	plugin_dir_path( __FILE__ )."logo/logo.png";
					
							if(file_exists( $filePath )){
								$logo 		= 	get_settings('siteurl').'/wp-content/plugins/aw-customizer/logo/logo.png';		
								echo "<img style='height: 100px; width: 100px;'  src='".$logo."' alt='Your Logo'/>";
							}
						?>	
						</div>
						<div style="clear:both;"></div>
					</div>
					<div class="aw-button-container">
						<input type="hidden" name="UpdateLogo" value="1" />
						<input type="submit" value="Update Logo" class="aw-save-settings" name="submit">
						<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
					</div>
				</div><!-- // SETTINGS CONTAINER -->
			</form><!-- // END FORM -->				
		
		<?php elseif ($_GET['tab'] == 'style' ): ?>
			<?php
				//** Section For Custom. **//
				if( isset($_POST['UpdateCss'])){
					$dir 	= 	plugin_dir_path( __FILE__ )."css/";				  	
				  	
				  	$data =	trim( $_POST['aw-custom-css'] );
					
					update_option('aw_custom_css', $data);

				  	// file_put_contents($dir ."/aw_custom_style.css",$data);
				  	
				}
			?>
			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Header Management</h3>						
						<div class="aw-option-container">
							<div class="aw-feature-desc">
								This Feature Allow you to add custom styling, custom script in the header section. <br> For example<br> &lt;script&gt; Your code here &lt;/script&gt;<br />
&lt;style&gt; Your Css here &lt;/style&gt;
							</div>
							<div style="clear:both;"></div>
							<label class="aw-feature-title">Header Code</label>
							<div class="aw-feature">
								<textarea class="textarea" name="aw-custom-css"><?php /*$content = file_get_contents(plugin_dir_path( __FILE__ )."css/aw_custom_style.css");*/ echo  stripslashes( get_option('aw_custom_css') ); ?></textarea>
							</div>
							<div style="clear:both;"></div>
						</div>
						<div class="aw-button-container">
							<input type="hidden" name="UpdateCss" value="1" />
							<input type="submit" value="Update Css" class="aw-save-settings" name="submit">
							<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
						</div>
				</div><!-- // SETTINGS CONTAINER -->					
			</form><!-- // END FORM -->
			
			<?php elseif ($_GET['tab'] == 'slider' ): ?>
			<?php 
				//** Section For Enable/disable slider. **//
				if( isset($_POST['UpdateSlider'])){
 	
				  	$value =  trim($_POST['aw_slider']);					
					update_option('aw_slider_status', $value);	
	
				}

				//** Section For setting number of posts **//
				if( isset($_POST['UpdateSlideCounter'])){
 					
 					$value =  trim($_POST['aw_slide_counter']);					
					update_option('aw_slide_counter', $value);	
	
				}		
			?>	
			
			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" name="aw_slider_form" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Slider Settings</h3>						
						<div class="aw-option-container">
							<label class="aw-feature-title">Slider Options</label>
							<div class="aw-feature">								
								<select name="aw_slider">
									<?php  $aw_slider_status =  get_option( 'aw_slider_status' ) ?>
									
									<?php if( $aw_slider_status == 1 ): ?>
										<option value="1" selected>Enable Slider</option>	
										<option value="0" >Disable Slider</option>
									<?php else: ?>
										<option value="1" >Enable Slider</option>											
										<option value="0" selected>Disable Slider</option>
									<?php endif; ?>	
								</select>
							</div>
							<div class="aw-feature-desc">
								This feature allows you to Slider Enable/Disable On Home Page.
							</div>
							<div style="clear:both;"></div>
						</div>
						<div class="aw-button-container">
							<input type="hidden" name="UpdateSlider" value="1" />
							<input type="submit" value="Update Settings" class="aw-save-settings" name="submit">
							<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
						</div>
					</div><!-- // SETTINGS CONTAINER -->					
			</form><!-- // END FORM -->		

			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" name="aw_slider_form" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Number of Slide Settings</h3>						
						<div class="aw-option-container">
							<label class="aw-feature-title">Slider Options</label>
							<div class="aw-feature">								
								<select name="aw_slide_counter">
									<?php echo  $aw_slide_counter =  get_option( 'aw_slide_counter' ) ?>
									
									<?php for ($i = 1; $i <= 10; $i++): ?>
										<?php if( $aw_slide_counter == $i ): ?>
											<option value="<?php echo $i; ?>" selected><?php echo $i; ?></option>											
										<?php else: ?>
											<option value="<?php echo $i; ?>" ><?php echo $i; ?></option>																					
										<?php endif; ?>	

									<?php endfor; ?>	

								</select>
							</div>
							<div class="aw-feature-desc">
								This feature allows you to Set Number of slider to be shown.
							</div>
							<div style="clear:both;"></div>
						</div>
						<div class="aw-button-container">
							<input type="hidden" name="UpdateSlideCounter" value="1" />
							<input type="submit" value="Update Settings" class="aw-save-settings" name="submit">
							<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
						</div>
					</div><!-- // SETTINGS CONTAINER -->					
			</form><!-- // END FORM -->	
		<?php elseif ($_GET['tab'] == 'ads' ): ?>
			<?php
				
				//** Section For Top Ads. **//
				if( isset( $_POST['UpdateTopAd'])){
					
					$value =  trim($_POST['aw-top-ad']);
					
					update_option('aw_top_ad', $value);
				}


				//** Section For Right Ads. **//
				if( isset( $_POST['UpdateSidebarTopAd'])){
					
					echo $value =  trim($_POST['aw_sidebar_top_ad']);
					update_option('aw_sidebar_top_ad', $value);
				}
				

				//** ===================================================================================== **//
				//** Section For Right Ads. **//
				if( isset( $_POST['UpdateMainAreaAd'])){
					
					echo $value =  trim($_POST['aw_main_area_ad']);
					update_option('aw_main_area_ad', $value);
				}
				

				//** ===================================================================================== **//
				//** Section For Right Ads. **//
				if( isset( $_POST['UpdateSecondaryLeftAd'])){
					
					$value =  trim($_POST['aw_secondary_left_ad']);
					update_option('aw_secondary_left_ad', $value);
				}
				
				//** ===================================================================================== **//
				//** Section For Right Ads. **//
				if( isset( $_POST['UpdateSecondaryTopRightAd'])){
					
					$value =  trim($_POST['aw_secondary_top_right_ad']);
					update_option('aw_secondary_top_right_ad', $value);
				}


				//** ===================================================================================== **//
				//** Section For Right Ads. **//
				if( isset( $_POST['UpdateSecondaryTopRightAd'])){
					
					$value =  trim($_POST['aw_secondary_top_right_ad']);
					update_option('aw_secondary_top_right_ad', $value);
				}

				//** ===================================================================================== **//
				//** Section For Right Ads. **//
				if( isset( $_POST['UpdateSecondaryBottomRightAd'])){
					
					$value =  trim($_POST['aw_secondary_bottom_right_ad']);
					update_option('aw_secondary_bottom_right_ad', $value);
				}


				//** ==================================================================== **//				
				//** Section For Right Ads. **//
				if( isset( $_POST['UpdateTechsubAd'])){
					
					$value =  trim($_POST['aw_techsub_top_ad']);
					update_option('aw_techsub_top_ad', $value);
				}

				//** ==================================================================== **//
				//** Section For Footer Ads. **//
				if( isset( $_POST['UpdateFooterLeftAd'])){
					
					$value =  trim($_POST['aw_footer_left_ad']);
					update_option('aw_footer_left_ad', $value);
				}
				
				//** Section For Footer Ads. **//
				if( isset( $_POST['UpdateFooterRightAd'])){
					
					$value =  trim($_POST['aw_footer_right_ad']);
					update_option('aw_footer_right_ad', $value);
				}
				//** ==================================================================== **//

				//** Section For Footer Ads. **//
				if( isset( $_POST['UpdateSingleFooterAd'])){
					
					$value =  trim($_POST['aw-single-footer-ad']);
					update_option('aw_single_footer_ad', $value);

				}
				
			?>	

			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Top Header Ad Management</h3>
					<div class="aw-option-container">
						
						<div class="aw-feature-desc">
							This feature allows you to write or copy and paste your own code Ads code here.
						</div>
						<div style="clear:both;"></div>
						<label class="aw-feature-title">Ad Code</label>	
						<div class="aw-feature">
							<textarea class="textarea" name="aw-top-ad"><?php echo  stripslashes( get_option( 'aw_top_ad' )); ?></textarea>
						</div>					
						<div style="clear:both;"></div>
					</div>
					<div class="aw-button-container">
						<input type="hidden" name="UpdateTopAd" value="1" />
						<input type="submit" value="Update Top Ad" class="aw-save-settings" name="submit">
						<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
					</div>
				</div><!-- // SETTINGS CONTAINER -->
			</form><!-- // END FORM -->	

			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Sidebar Top Ad Management</h3>
					<div class="aw-option-container">
						<div class="aw-feature-desc">
						This feature allows you to write or copy and paste your own code Ads code here.</div>
						<div style="clear:both;"></div>
						<label class="aw-feature-title">Ad Code</label>
						<div class="aw-feature">
							<textarea class="textarea" name="aw_sidebar_top_ad"><?php echo  stripslashes( get_option( 'aw_sidebar_top_ad' )); ?></textarea>
						</div>						
						<div style="clear:both;"></div>
					</div>
					<div class="aw-button-container">
						<input type="hidden" name="UpdateSidebarTopAd" value="1" />
						<input type="submit" value="Update Right Ad" class="aw-save-settings" name="submit">
						<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
					</div>
				</div><!-- // SETTINGS CONTAINER -->
			</form><!-- // END FORM -->

			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Main Area Ad Management</h3>
					<div class="aw-option-container">
						<div class="aw-feature-desc">
						This feature allows you to write or copy and paste your own code Ads code here.</div>
						<div style="clear:both;"></div>
						<label class="aw-feature-title">Ad Code</label>
						<div class="aw-feature">
							<textarea class="textarea" name="aw_main_area_ad"><?php echo  stripslashes( get_option( 'aw_main_area_ad' )); ?></textarea>
						</div>						
						<div style="clear:both;"></div>
					</div>
					<div class="aw-button-container">
						<input type="hidden" name="UpdateMainAreaAd" value="1" />
						<input type="submit" value="Update Right Ad" class="aw-save-settings" name="submit">
						<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
					</div>
				</div><!-- // SETTINGS CONTAINER -->
			</form><!-- // END FORM -->

			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Secondary Left Ad Management</h3>
					<div class="aw-option-container">
						<div class="aw-feature-desc">
						This feature allows you to write or copy and paste your own code Ads code here.</div>
						<div style="clear:both;"></div>
						<label class="aw-feature-title">Ad Code</label>
						<div class="aw-feature">
							<textarea class="textarea" name="aw_secondary_left_ad"><?php echo  stripslashes( get_option( 'aw_secondary_left_ad' )); ?></textarea>
						</div>						
						<div style="clear:both;"></div>
					</div>
					<div class="aw-button-container">
						<input type="hidden" name="UpdateSecondaryLeftAd" value="1" />
						<input type="submit" value="Update Right Ad" class="aw-save-settings" name="submit">
						<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
					</div>
				</div><!-- // SETTINGS CONTAINER -->
			</form><!-- // END FORM -->

			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Secondary Top Right Ad Management</h3>
					<div class="aw-option-container">
						<div class="aw-feature-desc">
						This feature allows you to write or copy and paste your own code Ads code here.</div>
						<div style="clear:both;"></div>
						<label class="aw-feature-title">Ad Code</label>
						<div class="aw-feature">
							<textarea class="textarea" name="aw_secondary_top_right_ad"><?php echo  stripslashes( get_option( 'aw_secondary_top_right_ad' )); ?></textarea>
						</div>						
						<div style="clear:both;"></div>
					</div>
					<div class="aw-button-container">
						<input type="hidden" name="UpdateSecondaryTopRightAd" value="1" />
						<input type="submit" value="Update Right Ad" class="aw-save-settings" name="submit">
						<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
					</div>
				</div><!-- // SETTINGS CONTAINER -->
			</form><!-- // END FORM -->

			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Secondary Bottom Right Ad Management</h3>
					<div class="aw-option-container">
						<div class="aw-feature-desc">
							This feature allows you to write or copy and paste your own code Ads code here.
						</div>
						<div style="clear:both;"></div>
						<label class="aw-feature-title">Ad Code</label>
						<div class="aw-feature">
							<textarea class="textarea" name="aw_secondary_bottom_right_ad"><?php echo  stripslashes( get_option( 'aw_secondary_bottom_right_ad' )); ?></textarea>
						</div>						
						<div style="clear:both;"></div>
					</div>
					<div class="aw-button-container">
						<input type="hidden" name="UpdateSecondaryBottomRightAd" value="1" />
						<input type="submit" value="Update Right Ad" class="aw-save-settings" name="submit">
						<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
					</div>
				</div><!-- // SETTINGS CONTAINER -->
			</form><!-- // END FORM -->
			
			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Techsub Top Ad Management</h3>
					<div class="aw-option-container">
						<div class="aw-feature-desc">
							This feature allows you to write or copy and paste your own code Ads code here.
						</div>
						<div style="clear:both;"></div>
						<label class="aw-feature-title">Ad Code</label>
						<div class="aw-feature">
							<textarea class="textarea" name="aw_techsub_top_ad"><?php echo  stripslashes( get_option( 'aw_techsub_top_ad' )); ?></textarea>
						</div>						
						<div style="clear:both;"></div>
					</div>
					<div class="aw-button-container">
						<input type="hidden" name="UpdateTechsubAd" value="1" />
						<input type="submit" value="Update Right Ad" class="aw-save-settings" name="submit">
						<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
					</div>
				</div><!-- // SETTINGS CONTAINER -->
			</form><!-- // END FORM -->	

			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Footer Left Ad Management</h3>
					<div class="aw-option-container">
						<div class="aw-feature-desc">
							This feature allows you to write or copy and paste your own code Ads code here.
						</div>
						<div style="clear:both;"></div>
						<label class="aw-feature-title">Ad Code</label>
						<div class="aw-feature">
							<textarea class="textarea" name="aw_footer_left_ad"><?php echo  stripslashes( get_option( 'aw_footer_left_ad' )); ?></textarea>
						</div>						
						<div style="clear:both;"></div>
					</div>
					<div class="aw-button-container">
						<input type="hidden" name="UpdateFooterLeftAd" value="1" />
						<input type="submit" value="Update footer Ad" class="aw-save-settings" name="submit">
						<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
					</div>
				</div><!-- // SETTINGS CONTAINER -->
			</form><!-- // END FORM -->

			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Footer Right Ad Management</h3>
					<div class="aw-option-container">
						<div class="aw-feature-desc">
							This feature allows you to write or copy and paste your own code Ads code here.
						</div>
						<div style="clear:both;"></div>
						<label class="aw-feature-title">Ad Code</label>
						<div class="aw-feature">
							<textarea class="textarea" name="aw_footer_right_ad"><?php echo  stripslashes( get_option( 'aw_footer_right_ad' )); ?></textarea>
						</div>						
						<div style="clear:both;"></div>
					</div>
					<div class="aw-button-container">
						<input type="hidden" name="UpdateFooterRightAd" value="1" />
						<input type="submit" value="Update footer Ad" class="aw-save-settings" name="submit">
						<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
					</div>
				</div><!-- // SETTINGS CONTAINER -->
			</form><!-- // END FORM -->


			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Single Page Footer Ads Management</h3>
					<div class="aw-option-container">
						<div class="aw-feature-desc">
							This feature allows you to write or copy and paste your own code Ads code here.
						</div>
						<div style="clear:both;"></div>
						<label class="aw-feature-title">Ad Code</label>
						<div class="aw-feature">
							<textarea class="textarea" name="aw-single-footer-ad"><?php echo  stripslashes( get_option( 'aw_single_footer_ad' )); ?></textarea>
						</div>						
						<div style="clear:both;"></div>
					</div>
					<div class="aw-button-container">
						<input type="hidden" name="UpdateSingleFooterAd" value="1" />
						<input type="submit" value="Update single footer Ad" class="aw-save-settings" name="submit">
						<h4 class="aw-info">Make Changes And Use The Update Settings Button To Save! →</h4>						
					</div>
				</div><!-- // SETTINGS CONTAINER -->
			</form><!-- // END FORM -->		


		<?php elseif ($_GET['tab'] == 'footer' ): ?>

		<?php
		//** Section For footer. **//
				if( isset( $_POST['UpdateFooter'])){
					
					$value = trim($_POST['aw-footer']);
					
					update_option('aw_footer', $value);
					

				}
		?>	

			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
				<div id="aw-settings-container"> <h3 class="aw-type-title">Footer Management</h3>
				<div class="aw-option-container">
					<div class="aw-feature-desc">
						This feature allows you to write or copy and paste your own code directly to the footer. A lot of people use this feature to include external and internal Javascript files, for plugins and things of the sort. Use it as you wish! <br /> usage: &lt;script&gt; Your code here &lt;/script&gt;<br />
					</div>
					<div style="clear:both;"></div>
					<label class="aw-feature-title">Custom Footer Code</label>
					<div class="aw-feature">
						<textarea class="textarea" name="aw-footer"><?php echo  stripslashes(get_option( 'aw_footer' )); ?></textarea>
					</div>					
					<div style="clear:both;"></div>
				</div>
				<div class="aw-button-container">
					<input type="hidden" name="UpdateFooter" value="1" />
					<input type="submit" value="Update Footer" class="aw-save-settings" name="submit">
					<h4 class="info">Make Changes And Use The Update Settings Button To Save! →</h4>						
				</div>
				</div><!-- // SETTINGS CONTAINER -->
			</form><!-- // END FORM -->		
		<?php endif; ?>	
		</div>	
		


	<?php
	}
}//** Class ends here. **//


$AW_Customizer = new AW_Customizer;


?>