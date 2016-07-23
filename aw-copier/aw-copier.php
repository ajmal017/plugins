<?php
/*
Plugin Name: AW-Copier
Plugin URI: http://www.artworldwebsolutions.com/
Description: Allow Editor to Copy Widgets, Custom Options, Custom Ads and Menus. Form One Blog To Other.
Version: 1.2
Author: Developer-G0947
Author URI: G0947@aw-developer.com
License:
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly Accessed

class Aw_Copier{

	//** Consntructor **//
	function __construct() {
		
		//** Register menu. **//
		add_action('admin_menu', array(&$this, 'register_awcopier_plugin_menu') );

		//** Load Style Sheet. **//
		add_action( 'admin_enqueue_scripts',  array(&$this, 'aw_load_plugin_css') );

		//** Load Admin Script. **//
		add_action( 'admin_enqueue_scripts',  array(&$this, 'aw_enqueue_script') );

	}


	//** Add Custom Css for Slider Function. **//
	function aw_enqueue_script(){
		wp_register_script('aw_customizer_script', plugins_url( '/js/aw_customizer_script.js' , __FILE__ ));
		wp_enqueue_script('aw_customizer_script');

		wp_register_script('aw_select2_script', plugins_url( '/js/select2.min.js' , __FILE__ ));
		wp_enqueue_script('aw_select2_script');

		/* wp_register_script('aw_multiselect_jQuery', plugins_url( '/js/jquery.min.js' , __FILE__ )); */
		wp_enqueue_script('aw_multiselect_jQuery');


		wp_register_script('aw_multiselect_script', plugins_url( '/js/jquery.multiple.select.js' , __FILE__ ));
		wp_enqueue_script('aw_multiselect_script');

	}

	
	//** Register menu Item. **//
	function register_awcopier_plugin_menu(){
		add_menu_page( 'Copier', 'AW-Copier', 'manage_options', 'awCopier', array(&$this, 'my_copier_page'), '', 27 );		
	}

	//** Load StyleSheet **//
	function aw_load_plugin_css() {
	    $plugin_url = plugin_dir_url( __FILE__ );

	    //** Load AW_Copier Styling. **//
	    wp_enqueue_style( 'aw_style', $plugin_url . 'css/aw_style.css' );

	    //** Load Select2 Styling. **//
	    wp_enqueue_style( 'aw_select2', $plugin_url . 'css/select2.min.css' );

	    //** Load multiselect Styling. **//
	    wp_enqueue_style( 'aw_multiselect_style', $plugin_url . 'css/multiple-select.css' );
	    // wp_enqueue_style( 'aw_multiselect_style_bootstrap', $plugin_url . 'css/bootstrap.css' );
	    

	}


	
	//** widgetCopier page.  **//
	function my_copier_page(){
	?>
		<div class="wrap">		
			<h2>AW Copier</h2>
			
			<?php
				$activeWidgets 			= '';
				$activeCustomOptions 	= '';
				$activeAds 				= '';
				$activeMenu				= '';
				$activePage				= '';
				$activeBlogroll			= '';


				if( $_GET['tab'] == 'textwidgets' || !isset( $_GET['tab'] )):
				
					$activeWidgets 			= 'nav-tab-active';
					$activeCustomOptions 	= '';
					$activeAds 				= '';
					$activeMenu				= '';
					$activePage				= '';
					$activeBlogroll			= '';

				
				elseif( $_GET['tab'] == 'customoptions' ):
				
					$activeWidgets 			= '';
					$activeCustomOptions 	= 'nav-tab-active';
					$activeAds 				= '';
					$activeMenu				= '';
					$activePage				= '';
					$activeBlogroll			= '';
				
				elseif( $_GET['tab'] == 'customads' ):
				
					$activeWidgets 			= '';
					$activeCustomOptions 	= '';
					$activeAds 				= 'nav-tab-active';
					$activeMenu				= '';
					$activePage				= '';
					$activeBlogroll			= '';

				elseif( $_GET['tab'] == 'menus' ):
				
					$activeWidgets 			= '';
					$activeCustomOptions 	= '';
					$activeAds 				= '';
					$activeMenu				= 'nav-tab-active';	
					$activePage				= '';
					$activeBlogroll			= '';

				elseif( $_GET['tab'] == 'pages' ):
				
					$activeWidgets 			= '';
					$activeCustomOptions 	= '';
					$activeAds 				= '';
					$activeMenu				= '';		
					$activePage				= 'nav-tab-active';
					$activeBlogroll			= '';
				
				elseif( $_GET['tab'] == 'blogroll' ):
				
					$activeWidgets 			= '';
					$activeCustomOptions 	= '';
					$activeAds 				= '';
					$activeMenu				= '';		
					$activePage				= '';
					$activeBlogroll			= 'nav-tab-active';
				else:
				
					$activeWidgets 			= 'nav-tab-active';
					$activeCustomOptions 	= '';
					$activeAds 				= '';
					$activeMenu				= '';
					$activePage				= '';
				endif;	

			?>

			<div class="icon32" id="icon-themes"><br></div>
			<h2 class="nav-tab-wrapper">
				<a href="?page=awCopier&amp;tab=textwidgets" class="nav-tab <?php echo $activeWidgets; ?>">Copy Text Widget(s)</a>
				<a href="?page=awCopier&amp;tab=customoptions" class="nav-tab <?php echo $activeCustomOptions; ?>">Copy Custom Option(s)</a>
				<a href="?page=awCopier&amp;tab=customads" class="nav-tab <?php echo $activeAds; ?>">Copy Custom Ad(s)</a>
				<a href="?page=awCopier&amp;tab=menus" class="nav-tab <?php echo $activeMenu; ?>">Copy Menu(s)</a>
				<a href="?page=awCopier&amp;tab=pages" class="nav-tab <?php echo $activePage; ?>">Copy Page(s)</a>
				<a href="?page=awCopier&amp;tab=blogroll" class="nav-tab <?php echo $activeBlogroll; ?>">Copy Blogroll(s)</a>

			</h2>		
			
			<!-- Code Section to fetch list of all Registerd Blog(s). -->
			<?php
				global $wpdb;					
				$result  =	$wpdb->get_results("SELECT site_slug FROM wp_aw_blog_sites");	

				//** Select Option for the Source Blog(s). **//
				$options =  "<option value=''>Select Source Blog</option>";

				//** Select Option for the DEstination Blog(s). **//
				$optionsDestination =  "";

				foreach ($result as $value) {
					$options .=  "<option value='".$value->site_slug."'>".$value->site_slug."</option>";					
					$optionsDestination .=  "<option style='padding-bottom: 5px;' value='".$value->site_slug."'>".$value->site_slug."</option>";					
				}					   

			?>

			<!-- Code block for text Widget.  -->
			<?php if( $_GET['tab'] == 'textwidgets' || !isset( $_GET['tab'] )): ?>

				<?php
					if( isset($_POST['copyWidgets'])){
						//** Copy widget operation will be handled by "copyWidgets.php" **//
						require dirname(__FILE__)."/functions/copyWidgets.php";
						
					}
				?>

				<form method="post" name="aw_copywidget_form" id="aw_copywidget_form" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI'];?>">
					<div id="aw-settings-container"> <!-- <h3 class="aw-type-title">Widget Copy</h3>-->
					
					<?php
						if( $copyStatus ){
							//** Success Message Show The Number Of Times Widgets is Copied. **//
							echo '<div id="PostError">';
								if( $successInCopy > 0 ){
									echo '<div  class="aw_post_message aw_post_success">('.$successInCopy.') Blog(s) Updated Successfully.</div>';
								}

								if( $ErrorAlready > 0 ){
									echo '<div  class="aw_post_message aw_post_notice">Notice! Widget(s) Not Copied To ('.$successInCopy.')  Selected Blog(s). Widget(s) Already Defined In Destination Blogs(s).</div>';								
								}

								if( $ErrorInCopy > 0 ){
									echo '<div  class="aw_post_message aw_post_error">Error! Widget(s) Not Copied To ('.$successInCopy.') Selected Blog(s).</div>';							
								}

								if( $ErrorTemplate > 0 ){
									echo '<div  class="aw_post_message aw_post_notice">Notice! Widget(s) Not Copied To ('.$ErrorTemplate.')  Selected Blog(s). Destination Blog(s) Theme Mismatch.</div>';								
								}

								if( $ErrorLanguage > 0 ){
									echo '<div  class="aw_post_message aw_post_notice">Notice! Widget(s) Not Copied To ('.$ErrorLanguage.')  Selected Blog(s). Destination Blogs(s) Language Mismatch.</div>';								
								}
							echo '</div>';


						}else{
								echo '<div id="PostError">';	
	
									if( $ErrorInCopy > 0 ){
										echo '<div  class="aw_post_message aw_post_error">Error! Widget(s) Not Copied To ('.$ErrorInCopy.') Selected Blog(s).</div>';							
									}

									if( $ErrorTemplate > 0 ){
										echo '<div  class="aw_post_message aw_post_notice">Notice! Widget(s) Not Copied To ('.$ErrorTemplate.')  Selected Blog(s). Destination Blog(s) Theme Mismatch.</div>';								
									}

									if( $ErrorLanguage > 0 ){
										echo '<div  class="aw_post_message aw_post_notice">Notice! Widget(s) Not Copied To ('.$ErrorLanguage.')  Selected Blog(s). Destination Blogs(s) Language Mismatch.</div>';								
									}
								echo '</div>';

						}
					?>


						<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
							<label class="aw-feature-title">Source Blog</label>
							<div class="aw-feature">
								<input type="hidden" name="serverPath" id="serverPath" value="<?php echo plugins_url() ?>/aw-copier/" />
								<select class="text" name="widgetSource" id="widgetSource" style="margin: 0 !important; padding: 5px 5px 5px !important; height: 40px">
									<?php echo $options; ?>
								</select>
								<p id="widgetSourceMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Select Source Blog to Copy Widgets.
							</div>
							<div style="clear:both;"></div>
						</div>
						<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
							<label class="aw-feature-title" id="aw_show_hide_tag" title="show / hide widget(s)">Widget Title</label>
							<div class="aw-feature">
								<div id="awOptionsCheck" style="min-height: 10px;">
									
								</div>
								<!-- <input type="text" class="text" name="widgetTitle" id="widgetTitle" /> -->
								<p id="widgetTitleMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Feature allow you to select widgets to be copied .
							</div>
							<div style="clear:both;"></div>
						</div>

						<div class="aw-option-container">
							<label class="aw-feature-title">Destination Blog(s)</label>
							<div class="aw-feature">
								<!-- <textarea class="textarea" name="widgetDestination" id="widgetDestination"></textarea> -->
								<select class="text" name="widgetDestination[]" multiple id="widgetDestination" style="">
									<?php echo $optionsDestination; ?>									
								</select>
								<p id="widgetDestinationMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Feature allow you to copy selected widgets to the destination blog(s). You can select multiple blog(s).
							</div>
							<div style="clear:both;"></div>
						</div>

						<div class="aw-button-container">
							<input type="hidden" name="copyWidgets" value="1" />
							<input type="submit" value="Copy Widgets" id="copyWidgetSubmit" class="aw-save-settings" name="submit">
							<h4 class="aw-info">Make Selection And Use The Copy Widgets Button To Copy Widget(s)! →</h4>
						</div>
					</div><!-- // SETTINGS CONTAINER -->
				</form><!-- // END FORM -->	
			<?php endif; ?>


			<!-- Code Block for Custom Options, this options include {custom header, logo, favicon} -->
			<?php if( $_GET['tab'] == 'customoptions' ): ?>

				<?php
					if( isset($_POST['CopyCustomOptions'])){
						//** Copy Custom Options operation will be handled by "copyCustomOptions.php" **//
						require dirname(__FILE__)."/functions/copyCustomOptions.php";
						
					}
				?>

				<form method="post" name="aw_copycustom_form" id="aw_copycustom_form" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI'];?>">
					<div id="aw-settings-container"> <!-- <h3 class="aw-type-title">Widget Copy</h3>-->
					
					<?php
						echo '<div id="PostError">';
						if( $customCopyStatus ){
							//** Success Message Show The Number Of Times Widgets is Copied. **//
							
								if( $customSuccessInCopy > 0 ){
									echo '<div class="aw_post_message aw_post_success">('.$customSuccessInCopy.') Blog(s) Updated Successfully.</div>';
								}
								
								if( $customErrorAlready > 0 ){
									echo '<div class="aw_post_message aw_post_notice" >Notice! Custom Option(s) Not Copied To ('.$customErrorAlready.')  Selected Blog(s). Custom Option(s) Already Defined In Destination Blogs(s).</div>';								
								}


						}else{
							
							if( $customErrorAlready > 0 ){
									echo '<div class="aw_post_message aw_post_notice">Notice! Custom Option(s) Not Copied To ('.$customErrorAlready.')  Selected Blog(s). Custom Option(s) Already Defined In Destination Blogs(s).</div>';								
							}

							if( $customErrorInCopy > 0 ){
								echo '<div class="aw_post_message aw_post_error">Error! Custom Option(s) Not Copied To ('.$customErrorInCopy.') Selected Blog(s).</div>';							
							}
						}

						if( $logoCopySuccess > 0){
							echo '<div class="aw_post_message aw_post_success">Logo Updated in ('.$logoCopySuccess.') Selected Blog(s) Successfully.</div>';	
						}

						if(  $logoCopyError > 0){
							echo '<div class="aw_post_message aw_post_Error" >Error! Logo Not Copied To ('.$logoCopyError.')  Selected Blog(s).</div>';									
						}

						if(  $logoCopyNotice > 0){
							echo '<div class="aw_post_message aw_post_notice" >Notice! Logo Not Copied To ('.$logoCopyNotice.')  Selected Blog(s). Logo Already Uploaded In Destination Blogs(s).</div>';									
						}

						if( $faviconCopySuccess > 0){
							echo '<div class="aw_post_message aw_post_success">Favicon Updated in ('.$faviconCopySuccess.') Selected Blog(s) Successfully.</div>';	
						}

						if(  $faviconCopyError > 0){
							echo '<div class="aw_post_message aw_post_Error" >Error! Favicon Not Copied To ('.$faviconCopyError.')  Selected Blog(s).</div>';									
						}

						if(  $favicoCopyNotice > 0){
							echo '<div class="aw_post_message aw_post_notice" >Notice! Favicon Not Copied To ('.$favicoCopyNotice.')  Selected Blog(s). Favicon Already Uploaded In Destination Blogs(s).</div>';									
						}

						if( $customErrorTemplate > 0 ){
							echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$customErrorTemplate.')  Selected Blog(s). Destination Blog(s) Theme Mismatch.</div>';								
						}

						if( $customErrorLanguage > 0 ){
							echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$customErrorLanguage.')  Selected Blog(s). Destination Blogs(s) Language Mismatch.</div>';								
						}

						echo '</div>';	
					?>


						<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
							<label class="aw-feature-title">Source Blog</label>
							<div class="aw-feature">
								<input type="hidden" name="serverPath" id="serverPath" value="<?php echo plugins_url() ?>/aw-copier/" />
								<select class="text" name="customSource" id="customSource" style="margin: 0 !important; padding: 5px 5px 5px !important; height: 40px">
									<?php echo $options; ?>
								</select>
								<p id="customSourceMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Select Source Blog to Copy Options( Favicon, Logo, Custom css, and Footer script.).
							</div>
							<div style="clear:both;"></div>
						</div>
						<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
							<label class="aw-feature-title" title="">Custom Options</label>
							<div class="aw-feature">
								<div id="awCustomOptionsCheck" style="min-height: 10px;">
									
								</div>
								<p id="customTitleMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Feature allow you to select custom options to be copied .
							</div>
							<div style="clear:both;"></div>
						</div>

						<div class="aw-option-container">
							<label class="aw-feature-title">Destination Blog(s)</label>
							<div class="aw-feature">
								<!-- <textarea class="textarea" name="widgetDestination" id="widgetDestination"></textarea> -->
								<select class="text" name="customDestination[]" multiple id="customDestination" style="margin: 0 !important; padding: 0 !important; height: 120px;">
									<?php echo $optionsDestination; ?>									
								</select>
								<p id="customDestinationMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Feature allow you to copy selected Custom Option(s) to the destination blog(s). You can select multiple blog(s).
							</div>
							<div style="clear:both;"></div>
						</div>

						<div class="aw-button-container">
							<input type="hidden" name="CopyCustomOptions" value="1" />
							<input type="submit" value="Copy Custom Options" id="copyCustomSubmit" class="aw-save-settings" name="submit">
							<h4 class="aw-info">Make Selection And Use The Copy Custom Options Button To Copy Custom Option(s)! →</h4>
						</div>
					</div><!-- // SETTINGS CONTAINER -->
				</form><!-- // END FORM -->	
			<?php endif; ?>

			<?php if( $_GET['tab'] == 'customads' ): ?>

				<?php
					if( isset($_POST['copyAds'])){
						//** Copy Custom Options operation will be handled by "copyCustomOptions.php" **//
						require dirname(__FILE__)."/functions/copyAds.php";
						
					}
				?>

				<form method="post" name="aw_copyads_form" id="aw_copyads_form" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI'];?>">
					<div id="aw-settings-container"> <!-- <h3 class="aw-type-title">Widget Copy</h3>-->
					
					<?php
						echo '<div id="PostError">';
						if( $adsCopyStatus ){
							//** Success Message Show The Number Of Times Widgets is Copied. **//
							
								if( $adsSuccessInCopy > 0 ){
									echo '<div class="aw_post_message aw_post_success">Success! Ad(s) Copied To ('.$adsSuccessInCopy.') Selected Blog(s).</div>';
								}
								
								if( $adsErrorAlready > 0 ){
									echo '<div class="aw_post_message aw_post_notice" >Notice! ads Option(s) Not Copied To ('.$adsErrorAlready.')  Selected Blog(s). ads Option(s) Already Defined In Destination Blogs(s).</div>';								
								}


								if( $adsErrorTemplate > 0 ){
									echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$adsErrorTemplate.')  Selected Blog(s). Destination Blog(s) Theme Mismatch.</div>';								
								}

								if( $adsErrorLanguage > 0 ){
									echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$adsErrorLanguage.')  Selected Blog(s). Destination Blogs(s) Language Mismatch.</div>';								
								}


						}else{
							
							if( $adsErrorAlready > 0 ){
									echo '<div class="aw_post_message aw_post_notice">Notice! ads Option(s) Not Copied To ('.$adsErrorAlready.')  Selected Blog(s). ads Option(s) Already Defined In Destination Blogs(s).</div>';								
							}

							if( $adsErrorInCopy > 0 ){
								echo '<div class="aw_post_message aw_post_error">Error! Custom Option(s) Not Copied To ('.$customErrorInCopy.') Selected Blog(s).</div>';							
							}

							if( $adsErrorTemplate > 0 ){
								echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$adsErrorTemplate.')  Selected Blog(s). Destination Blog(s) Theme Mismatch.</div>';								
							}

							if( $adsErrorLanguage > 0 ){
								echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$adsErrorLanguage.')  Selected Blog(s). Destination Blogs(s) Language Mismatch.</div>';								
							}

						}
						echo '</div>';	
					?>


						<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
							<label class="aw-feature-title">Source Blog</label>
							<div class="aw-feature">
								<input type="hidden" name="serverPath" id="serverPath" value="<?php echo plugins_url() ?>/aw-copier/" />
								<select class="text" name="adsSource" id="adsSource" style="margin: 0 !important; padding: 5px 5px 5px !important; height: 40px">
									<?php echo $options; ?>
								</select>
								<p id="adsSourceMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Select Source Blog to Copy Ads.
							</div>
							<div style="clear:both;"></div>
						</div>
						<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
							<label class="aw-feature-title" title="">Ads Options</label>
							<div class="aw-feature">
								<div id="awAdsOptionsCheck" style="min-height: 10px;">
									
								</div>
								<p id="adsTitleMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Feature allow you to select ads to be copied .
							</div>
							<div style="clear:both;"></div>
						</div>

						<div class="aw-option-container">
							<label class="aw-feature-title">Destination Blog(s)</label>
							<div class="aw-feature">
								<!-- <textarea class="textarea" name="widgetDestination" id="widgetDestination"></textarea> -->
								<select class="text" name="adsDestination[]" multiple id="adsDestination" style="margin: 0 !important; padding: 0 !important; height: 120px;">
									<?php echo $optionsDestination; ?>									
								</select>
								<p id="adsDestinationMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Feature allow you to copy selected ads Option(s) to the destination blog(s). You can select multiple blog(s).
							</div>
							<div style="clear:both;"></div>
						</div>

						<div class="aw-button-container">
							<input type="hidden" name="copyAds" value="1" />
							<input type="submit" value="Copy ads Options" id="copyAdsSubmit" class="aw-save-settings" name="submit">
							<h4 class="aw-info">Make Selection And Use The Copy Custom Options Button To Copy Custom Option(s)! →</h4>
						</div>
					</div><!-- // SETTINGS CONTAINER -->
				</form><!-- // END FORM -->	

			<?php endif; ?>

			<!-- Code for the Menus, this option include all the Menus defined by the Admin. -->
			<?php if( $_GET['tab'] == 'menus' ): ?>

				<?php
					if( isset($_POST['copyMenus'])){
						//** Copy Custom Options operation will be handled by "copyCustomOptions.php" **//
						require dirname(__FILE__)."/functions/copyMenu.php";
						
					}
				?>    

				<form method="post" name="aw_copymenus_form" id="aw_copymenus_form" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI'];?>">
					<div id="aw-settings-container"> <!-- <h3 class="aw-type-title">Widget Copy</h3>-->
					
					<?php
						echo '<div id="PostError">';
						if( $menuCopyStatus ){
							//** Success Message Show The Number Of Times Widgets is Copied. **//
							if( $menuSuccessInCopy > 0 ){
								echo '<div class="aw_post_message aw_post_success">Success! Menu(s) Copied To ('.$menuSuccessInCopy.') Selected Blog(s).</div>';
							}
							if( $menuErrorTemplate > 0 ){
								echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$menuErrorTemplate.')  Selected Blog(s). Destination Blog(s) Theme Mismatch.</div>';								
							}

							if( $menuErrorLanguage > 0 ){
								echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$menuErrorLanguage.')  Selected Blog(s). Destination Blogs(s) Language Mismatch.</div>';								
							}

						}else{
							if( $menuErrorInCopy > 0 ){
								echo '<div class="aw_post_message aw_post_error">Error! Menu(s) Not Copied To Some Selected Blog(s).</div>';							
							}
							if( $menuErrorTemplate > 0 ){
								echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$menuErrorTemplate.')  Selected Blog(s). Destination Blog(s) Theme Mismatch.</div>';								
							}

							if( $menuErrorLanguage > 0 ){
								echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$menuErrorLanguage.')  Selected Blog(s). Destination Blogs(s) Language Mismatch.</div>';								
							}
						}	
						
						echo '</div>';	
					?>


						<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
							<label class="aw-feature-title">Source Blog</label>
							<div class="aw-feature">
								<input type="hidden" name="serverPath" id="serverPath" value="<?php echo plugins_url() ?>/aw-copier/" />
								<select class="text" name="menuSource" id="menuSource" style="margin: 0 !important; padding: 5px 5px 5px !important; height: 40px">
									<?php echo $options; ?>
								</select>
								<p id="menuSourceMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Select Source Blog to Copy Menu(s).  
							</div>
							<div style="clear:both;"></div>
						</div>
						<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
							<label class="aw-feature-title" title="">Menu(s) Title</label>
							<div class="aw-feature">
								<div id="awMenuOptionsCheck" style="min-height: 10px;">
									
								</div>
								<p id="menuTitleMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Feature allow you to select Menu(s) to be copied.
							</div>
							<div style="clear:both;"></div>
						</div>

						<div class="aw-option-container">
							<label class="aw-feature-title">Destination Blog(s)</label>
							<div class="aw-feature">
								<!-- <textarea class="textarea" name="widgetDestination" id="widgetDestination"></textarea> -->
								<select class="text" name="menuDestination[]" multiple id="menuDestination" style="margin: 0 !important; padding: 0 !important; height: 120px;">
									<?php echo $optionsDestination; ?>									
								</select>
								<p id="menuDestinationMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Feature allow you to copy selected Menu(s) to the destination blog(s). You can select multiple blog(s).
							</div>
							<div style="clear:both;"></div>
						</div>

						<div class="aw-button-container">
							<input type="hidden" name="copyMenus" value="1" />
							<input type="submit" value="Copy Menus" id="copyMenuSubmit" class="aw-save-settings" name="submit">
							<h4 class="aw-info">Make Selection And Use The Copy Menus Button To Copy Menu(s)! →</h4>
						</div>
					</div><!-- // SETTINGS CONTAINER -->
				</form><!-- // END FORM -->	
				
			<?php endif; ?>

				<!-- Code for the Menus, this option include all the Menus defined by the Admin. -->
			<?php if( $_GET['tab'] == 'pages' ): ?>

				<?php
					if( isset($_POST['copyPages'])){
						//** Copy pages  operation will be handled by "copypages.php" **//
						require dirname(__FILE__)."/functions/copyPages.php";
						
					}
				?>    

				<form method="post" name="aw_copypages_form" id="aw_copypages_form" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI'];?>">
					<div id="aw-settings-container"> <!-- <h3 class="aw-type-title">Widget Copy</h3>-->
					
					<?php
						echo '<div id="PostError">';
						if( $pageCopyStatus ){
							//** Success Message Show The Number Of Times Widgets is Copied. **//
							if( $pageSuccessInCopy > 0 ){
								echo '<div class="aw_post_message aw_post_success">Success! page(s) Copied To '.$pageSuccessInCopy.' Selected Blog(s).</div>';
							}
							if( $pageErrorInCopy > 0 ){
								echo '<div class="aw_post_message aw_post_error">Error! pages(s) Not Copied To '.$pageErrorInCopy.' Selected Blog(s).</div>';							
							}
							if( $pageErrorTemplate > 0 ){
								echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$pageErrorTemplate.')  Selected Blog(s). Destination Blog(s) Theme Mismatch.</div>';								
							}

							if( $pageErrorLanguage > 0 ){
								echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$pageErrorLanguage.')  Selected Blog(s). Destination Blogs(s) Language Mismatch.</div>';								
							}

						}else{
							if( $pageErrorInCopy > 0 ){
								echo '<div class="aw_post_message aw_post_error">Error! pages(s) Not Copied To '.$pageErrorInCopy.' Selected Blog(s).</div>';							
							}
							if( $pageErrorTemplate > 0 ){
								echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$pageErrorTemplate.')  Selected Blog(s). Destination Blog(s) Theme Mismatch.</div>';								
							}

							if( $pageErrorLanguage > 0 ){
								echo '<div  class="aw_post_message aw_post_notice">Notice!  ('.$pageErrorLanguage.')  Selected Blog(s). Destination Blogs(s) Language Mismatch.</div>';								
							}
						}	
						
						echo '</div>';	
					?>


						<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
							<label class="aw-feature-title">Source Blog</label>
							<div class="aw-feature">
								<input type="hidden" name="serverPath" id="serverPath" value="<?php echo plugins_url() ?>/aw-copier/" />
								<select class="text" name="pageSource" id="pageSource" style="margin: 0 !important; padding: 5px 5px 5px !important; height: 40px">
									<?php echo $options; ?>
								</select>
								<p id="pageSourceMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Select Source Blog to Copy page(s).  
							</div>
							<div style="clear:both;"></div>
						</div>
						<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
							<label class="aw-feature-title" title="">page(s) Title</label>
							<div class="aw-feature">
								<div id="awPageOptionsCheck" style="min-height: 10px;">
									
								</div>
								<p id="pageTitleMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Feature allow you to select Page(s) to be copied.
							</div>
							<div style="clear:both;"></div>
						</div>

						<div class="aw-option-container">
							<label class="aw-feature-title">Destination Blog(s)</label>
							<div class="aw-feature">
								<!-- <textarea class="textarea" name="widgetDestination" id="widgetDestination"></textarea> -->
								<select class="text" name="pageDestination[]" multiple id="pageDestination" style="margin: 0 !important; padding: 0 !important; height: 120px;">
									<?php echo $optionsDestination; ?>									
								</select>
								<p id="pageDestinationMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Feature allow you to copy selected Page(s) to the destination blog(s). You can select multiple blog(s).
							</div>
							<div style="clear:both;"></div>
						</div>

						<div class="aw-button-container">
							<input type="hidden" name="copyPages" value="1" />
							<input type="submit" value="Copy Pages" id="copyPageSubmit" class="aw-save-settings" name="submit">
							<h4 class="aw-info">Make Selection And Use The Copy Pages Button To Copy page(s)! →</h4>
						</div>
					</div><!-- // SETTINGS CONTAINER -->
				</form><!-- // END FORM -->	
				
			<?php endif; ?>	

			<!-- Code block for text Widget.  -->
			<?php if( $_GET['tab'] == 'blogroll' ): ?>

				<?php
					if( isset($_POST['copyblogrolls'])){
						//** Copy widget operation will be handled by "copyBlogroll.php" **//
						require dirname(__FILE__)."/functions/copyBlogroll.php";						
					}
				?>

				<form method="post" name="aw_copyblogroll_form" id="aw_copyblogroll_form" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI'];?>">
					<div id="aw-settings-container"> <!-- <h3 class="aw-type-title">Widget Copy</h3>-->
					
					<?php
						if( $copyStatus ){
							//** Success Message Show The Number Of Times Widgets is Copied. **//
							echo '<div id="PostError">';
								if( $successInCopy > 0 ){
									echo '<div  class="aw_post_message aw_post_success">('.$successInCopy.') Blog(s) Updated Successfully.</div>';
								}

								if( $ErrorAlready > 0 ){
									echo '<div  class="aw_post_message aw_post_notice">Notice! Blogroll(s) Not Copied To ('.$ErrorAlready.')  Selected Blog(s).</div>';								
								}

								if( $ErrorInCopy > 0 ){
									echo '<div  class="aw_post_message aw_post_error">Error! Blogroll(s) Not Copied To ('.$ErrorInCopy.') Selected Blog(s).</div>';							
								}

								if( $ErrorTemplate > 0 ){
									echo '<div  class="aw_post_message aw_post_notice">Notice! Blogroll(s) Not Copied To ('.$ErrorTemplate.')  Selected Blog(s). Destination Blogroll(s) Theme Mismatch.</div>';								
								}

								if( $ErrorLanguage > 0 ){
									echo '<div  class="aw_post_message aw_post_notice">Notice! Blogroll(s) Not Copied To ('.$ErrorLanguage.')  Selected Blog(s). Destination Blogs(s) Language Mismatch.</div>';								
								}
	
							echo '</div>';


						}else{

							echo '<div id="PostError">';	
							
								if( $ErrorInCopy > 0 ){
									echo '<div  class="aw_post_message aw_post_error">Error! Blogroll(s) Not Copied To ('.$ErrorInCopy.') Selected Blog(s).</div>';							
								}

								if( $ErrorTemplate > 0 ){
									echo '<div  class="aw_post_message aw_post_notice">Notice! Blogroll(s) Not Copied To ('.$ErrorTemplate.')  Selected Blog(s). Destination Blogroll(s) Theme Mismatch.</div>';								
								}

								if( $ErrorLanguage > 0 ){
									echo '<div  class="aw_post_message aw_post_notice">Notice! Blogroll(s) Not Copied To ('.$ErrorLanguage.')  Selected Blog(s). Destination Blogs(s) Language Mismatch.</div>';								
								}
							echo '</div>';
						}
					?>


						<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
							<label class="aw-feature-title">Source Blog</label>
							<div class="aw-feature">
								<input type="hidden" name="serverPath" id="serverPath" value="<?php echo plugins_url() ?>/aw-copier/" />
								<select class="text" name="blogrollSource" id="blogrollSource" style="margin: 0 !important; padding: 5px 5px 5px !important; height: 40px">
									<?php echo $options; ?>
								</select>
								<p id="blogrollSourceMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Select Source Blog to Copy blogRoll(s).
							</div>
							<div style="clear:both;"></div>
						</div>
						<div style="margin-top: 5px;" class="aw-option-container" id="aw-first-option">
							<label class="aw-feature-title" id="aw_show_hide_tag" title="show / hide blogroll(s)">BlogRolls</label>
							<div class="aw-feature">
								<div id="awOptionsCheck" style="min-height: 10px;">
									
								</div>
								<!-- <input type="text" class="text" name="widgetTitle" id="widgetTitle" /> -->
								<p id="blogrollTitleMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Feature allow you to select BlogRolls to be copied .
							</div>
							<div style="clear:both;"></div>
						</div>

						<div class="aw-option-container">
							<label class="aw-feature-title">Destination Blog(s)</label>
							<div class="aw-feature">
								<!-- <textarea class="textarea" name="blogrollDestination" id="blogrollDestination"></textarea> -->
								<select class="text" name="blogrollDestination[]" multiple id="blogrollDestination" style="">
									<?php echo $optionsDestination; ?>									
								</select>
								<p id="blogrollDestinationMessage"></p>
							</div>
							<div class="aw-feature-desc">
								Feature allow you to copy selected blogrolls to the destination blog(s). You can select multiple blog(s).
							</div>
							<div style="clear:both;"></div>
						</div>

						<div class="aw-button-container">
							<input type="hidden" name="copyblogrolls" value="1" />
							<input type="submit" value="Copy blogrolls" id="copyBlogrollSubmit" class="aw-save-settings" name="submit">
							<h4 class="aw-info">Make Selection And Use The Copy Widgets Button To Copy Widget(s)! →</h4>
						</div>
					</div><!-- // SETTINGS CONTAINER -->
				</form><!-- // END FORM -->	
			<?php endif; ?>							

		</div>	

	<?php
	}
}//** Class ends here. **//


$Aw_Copier = new Aw_Copier;

?>