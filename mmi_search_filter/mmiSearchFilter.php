<?php
/*
Plugin Name: MMI Search Filter
Plugin URI: 
Description: Advanced Search Functionlity for MMI POSTS.
Version: 1.0
Author: Developer-G0947
Author URI: 
License:
*/

session_start();

/* File Created on 02.07.2015. */
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once('/var/www/mmi.mindinmotion-online.com/infusionsoft-api/isdk.php');
require("/var/www/mmi.mindinmotion-online.com/infusionsoft-api/conn.cfg.php");  	

class MMISearchFilter {

		//** Constructor **//
		function __construct() {		

			//** search from shortcode. **//	
			add_shortcode( 'MMI_search_box',array(&$this, 'getSearchFrom') );
			
			add_shortcode( 'MMI_default_list',array(&$this, 'getDefaultList') );
			
			add_shortcode( 'MMI_getUserTags',array(&$this, 'getUserTags') );

			//** This filter will jump into the loop and arrange our results before they're returned **//		
			add_filter('pre_get_posts',array(&$this, 'SearchFilter') );

			//** Action to load Assets Css **//
			add_action( 'wp_enqueue_scripts',  array(&$this, 'loadAssectCss') );

			/* Apply Relevanssi Filter */

			add_filter( 'relevanssi_hits_filter', array( &$this, 'reviews_first') );

			//** Searh Filter for the Search Post. **//
			// add_filter('posts_where', array(&$this, 'filter_where') );
		}

		//**  function to laod css **//
		function loadAssectCss(){
			$plugin_url = plugin_dir_url( __FILE__ );

		    //** Load  Styling. **//
		    wp_enqueue_style( 'aw_MMI_style', $plugin_url . 'css/mmi_search_style.css' );
		    wp_enqueue_style( 'aw_MMI_table_style', $plugin_url . 'css/mmi_table_style.css' );
		    
		    /*wp_enqueue_style( 'aw_MMI_table_style', $plugin_url . 'css/mmi_table_style.css' );*/
		    
		    wp_enqueue_style( 'jquery-comiseo-daterangepicker-style', $plugin_url . 'css/daterangepicker.css' );

		    // $my_title = page_name();
		    $slug 		= get_post_field( 'post_name', get_post() );
		    $searchFlag = false;
		    if( is_search() ){			
				$searchFlag = true;
			}		

		    if( ($slug == 'mmi-index' ||  $searchFlag ) ):
		    	wp_enqueue_script('momentjs',plugins_url() . '/mmi_search_filter/js/moment.min.js',array('jquery'), '1.0.0', true );
		    	wp_enqueue_script('jquery-daterangepicker',plugins_url() . '/mmi_search_filter/js/jquery.daterangepicker.js',array(), '1.0.0', true );
		    	wp_enqueue_script('custom-script',plugins_url() . '/mmi_search_filter/js/custom.js',array('jquery'), '1.0.0', true );    
		    endif;
		    // wp_enqueue_script('daterange',plugins_url() . '/mmi_search_filter/js/jquery.daterange.js',array(), '1.0.0', true );
		}

		//** function to create a search form. **//
		function getSearchFrom(){
			
			global $wpdb;

			$site_url      =	site_url();
			$mmi_index_url = 	get_option('mmi_index_url', true );

			/* get the data if set in URL  */
			$title       =	(empty($_GET['title']))? '': $_GET['title'];
			$teacher_id  =	(empty($_GET['teacher_id']))? '': $_GET['teacher_id'];
			$record_type =	(empty($_GET['record_type']))? '': $_GET['record_type'];
			$mmi_date    =	(empty($_GET['mmi_date']))? '': $_GET['mmi_date'];
			$record_id   =	(empty($_GET['record_id']))? '': $_GET['record_id'];
			$course      =	(empty($_GET['course']))? '': $_GET['course'];
			$segment     =	(empty($_GET['segment']))? '': $_GET['segment'];


			echo '<span class="span_search" id="pre_title" value="'.$title.'"></span>';
			echo '<span class="span_search" id="pre_teacher_id" value="'.$teacher_id.'"></span>';
			echo '<span class="span_search" id="pre_record_type" value="'.$record_type.'"></span>';
			echo '<span class="span_search" id="pre_mmi_date" value="'.$mmi_date.'"></span>';
			echo '<span class="span_search" id="pre_record_id" value="'.$record_id.'"></span>';
			echo '<span class="span_search" id="pre_course" value="'.$course.'"></span>';
			echo '<span class="span_search" id="pre_segment" value="'.$segment.'"></span>';
			/* section ends here*/

			$resultPrograms = $wpdb->get_results("SELECT * FROM wp_mmi_programs ORDER BY program_name asc");

			$programOptions = '';
			foreach ($resultPrograms as $row){								
				$programOptions .= '<option value="'.$row->program_value.'">'.$row->program_name.'</option>';		
			}

			//$sql = "SELECT `wp_mmi_segment`.`ID`, segment_name, segment_value, segment_description, program_name,program_value FROM wp_mmi_segment, wp_mmi_programs WHERE `wp_mmi_segment`.`program_ID` = `wp_mmi_programs`.`ID` ORDER BY LENGTH(segment_name),segment_name";
			$sql = "SELECT `wp_mmi_segment`.`ID`, segment_name, segment_value, segment_description, program_name,program_value FROM wp_mmi_segment, wp_mmi_programs WHERE `wp_mmi_segment`.`program_ID` = `wp_mmi_programs`.`ID` ORDER BY ID ASC";
			$resultSegment 	= $wpdb->get_results($sql);

			//sort array based on segment name.
			/*usort($resultSegment, 'my_sort_function');

			function my_sort_function($a, $b){
			    //return $a->segment_name < $b->segment_name;
			    echo "<script>working points.</script>";
			    return strnatcmp($a->segment_name,$b->segment_name);
			}*/

			/*$segmentOptions = '';
			
			foreach ( $resultSegment as $row ){
				$program_name_value = str_replace(" ", "_", $row->program_value);
				$segmentOptions .= '<option class="'.$program_name_value.' segmentItem" value="'.$row->segment_value.'" label="'.$row->segment_name.'">'.$row->segment_name.'</option>';
			}*/
			
			// Sort Segment from result set and return the options in string...
			function getSortedSegments($resultSegment){
				$arr_top = array();

				foreach ( $resultSegment as $row ){
					
					$program_name_value = str_replace(" ", "_", $row->program_value);

					$segmentOptions = '<option class="'.$program_name_value.' segmentItem" value="'.$row->segment_value.'" label="'.$row->segment_name.'">'.$row->segment_name.'</option>';

					$segment = preg_replace("/ {2,}/", " ", $row->segment_value);

					$arr_segment = explode(" ",$segment);

					$segment_number = explode("-",$arr_segment[1]);					

					$segment_number_part =  intval($segment_number[0]);

					if (array_key_exists($program_name_value, $arr_top ))
					{
						if (array_key_exists($segment_number_part, $arr_top[$program_name_value] )){
							$array_key = $segment_number_part+99999;
						}else{
							$array_key = $segment_number_part;
						}

						$arr_top[$program_name_value][$array_key] = $segmentOptions;						
					}
					else
					{
						$arr_top[$program_name_value] =  array();

						$arr_top[$program_name_value][$segment_number_part] = $segmentOptions;
					}
				}

				foreach ( $arr_top as $key => $program) {					
					$aa = $program;
					ksort($aa);
					$arr_top[$key] = $aa;
				}

				$segmentOptions = '';

				foreach ( $arr_top as $key => $program) {
					foreach ($program as $option ) {
						$segmentOptions .= $option;
					}
				}

				return $segmentOptions; 
			}
			// sort segment function ends here....

			// get sorted segments...
			$segmentOptions = getSortedSegments($resultSegment);


			$html = '';
			//$html .= '<style> .element_color{ color: #999999}</style>';

			/*Saved Search list goes here. */
			$html .= MMISearchFilter::getSaveSearchList();

			/*echo "<style>.page-id-19258 .site-container .site-inner .content .entry-header h1{ text-align: center; }
			.field-msg span{ background-color:#fff; font-size:12px;margin-left:5px;font-weight:bold;} .field-msg{margin-top:20px;} </style>";*/

			/*------------------CSV File download Start------------------*/
			$actual_linkMMI 	= "http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
			$new_url = $actual_linkMMI."&download_csv_MMI=1&action=downloadMMI";
			if($_GET['download_csv_MMI'] !== "" && $_GET['action'] == "downloadMMI"){
				result_to_csv_download_MMI();
			}
			/*------------------CSV File Download End------------------*/

			$html .= '<div class="mmi-custom-search-filters">';
			$html .= 	'<form role="search" method="get" id="searchform" action="'.$site_url.'">';
			$html .= 		'<input type="hidden" value="" name="s" id="s" />';	
			$html .= 		'<input type="hidden" name="MMI_seach" value="1"/>';	
			$html .= 		'<div class="three-box-row">';		
			$html .= 			'<div class="three-box-row">';		
			$html .= 				'<div class="one-third-box course_box">';
			$html .=          			'<div class="field-msg"><span>To find a class, workshop, training, or eproduct:</span></div>';
			$html .= 					'<div class="inner-colmn">';
			$html .= 						'<select class="element_color" id="course" name="course" title="In order to search by Course, you must also select a Segment or Module"> 
							                	<option value="">1. Select product or program</option>
							              		'.$programOptions.'
								        	</select>';
			$html .= 						'<span id="courseMessage"></span>';		        
			$html .= 					'</div>';
			$html .= 				'</div>';			
			$html .= 				'<div class="one-third-box segment_box">';
			$html .=        			'<div class="field-msg"><span>Then:</span></div>';
			$html .= 					'<div class="inner-colmn">';
			$html .= 						'<select class="element_color" id="segment" name="segment" title="In order to search by segment/module, you must also select a Program"> 
							                	<option value="">2. Select segment or module</option>
							                	'.$segmentOptions.'						                		
							            	</select>';
			$html .= 						'<span id="segmentMessage"></span>';
			$html .= 					'</div>';
				$html .= 			'</div>';
				$html .= 			'<div class="clear"></div>';
				$html .= 		'</div>';
				$html .= 		'<div class="one-third-box">';
				$html .=          	'<div class="field-msg word-phrase"><span>If you are looking for a specific file in the MIM library:</span></div>';
				$html .= 			'<div class="inner-colmn">';
				$html .= 				'<input class="search_title_bar" type="text" value="'.$title.'" placeholder="Enter word or phrase" name="title" id="title"/>';
				$html .= 			'</div>';
				$html .= 		'</div>';

				$html .= 		'<div class="three-box-row advance_form">';	
				$html .= 			'<div class="one-third-box record_id_box">';
				$html .=          		'<div class="field-msg file-number"><span></span></div>';
				$html .= 				'<div class="inner-colmn">';						
				$html .= 					'<input placeholder="Enter a file number" type="text" value="'.$record_id.'" name="record_id" id="record_id" />';
				$html .= 				'</div>';
				$html .= 			'</div>';
				$html .= 			'<div class="clear"></div>';
				
				$html .= 			'<div class="one-third-box teacher_id_box">';
				$html .=          		'<div class="field-msg search-teacher"><span></span></div>';
				$html .= 				'<div class="inner-colmn">';
				$html .= 					'<select id="teacher_id" class="element_color" name="teacher_id" >
						                	<option value="" class="element_color">Select a teacher</option>
						                	<option value="L Goldfarb">L Goldfarb</option>
						                	<option value="A Siotas">A Siotas</option>
						                	<option value="Guest Teacher">Guest Teacher</option>
						                	<option value="C van Iersel">C van Iersel</option>
						                	<option value="A Feldmann">A Feldmann</option>
						                	<option value="A Zones">A Zones</option>
						                	<option value="K Smithback">K Smithback</option>
						                	<option value="B Walterspiel">B Walterspiel</option>
						                	<option value="D Blank">D Blank</option>
						                	<option value="D Bowes">D Bowes</option>
						                	<option value="P Newton">P Newton</option>
						                	<option value="M Feldenkrais">M Feldenkrais</option>
						                	<option value="C Smyth">C Smyth</option>
						                	<option value="A Johnson-Chase">A Johnson-Chase</option>
						                	<option value="R Russell">R Russell</option>
						                	<option value="SIFT2">SIFT2</option>
						                	<option value="R Wallace">R Wallace</option>
						                	<option value="S Hillier">S Hillier</option>
						                	<option value="S Clark">S Clark</option>
						                	<option value="AM Caponecchi">AM Caponecchi</option>
						                	<option value="I Dowd">I Dowd</option>
						                	<option value="D New">D New</option>
						                	<option value="A Cone">A Cone</option>
						                	<option value="P Wilkinson">P Wilkinson</option>
						                	<option value="T Wilson">T Wilson</option>
						                	<option value="E Barker">E Barker</option>
						                	<option value="R Ehrman">R Ehrman</option>
						                	<option value="A Questel">A Questel</option>
						                	<option value="S Spink">S Spink</option>
						                	<option value="L Pessach">L Pessach</option>
						           			</select>';				
				$html .= 				'</div>';
				$html .= 			'</div>';
				
				$html .= 			'<div class="one-third-box record_type_box">';
				$html .=          		'<div class="field-msg select-activity"><span></span></div>';
				$html .= 					'<div class="inner-colmn">';
				$html .= 					'<select id="record_type" class="element_color" name="record_type"> 
						                		<option value="" >Select an activity</option>
						                		<option value="ATM">ATM</option>
						                		<option value="ATM+Demo">ATM+Demo</option>
						                		<option value="ATM+Discussion">ATM+Discussion</option>
						                		<option value="ATM+Hands On">ATM+Hands On</option>
						                		<option value="ATM+Lab">ATM+Lab</option>
						                		<option value="ATM+Talk">ATM+Talk</option>
						                		<option value="Class">Class</option>
						                		<option value="Demo">Demo</option>
						                		<option value="Discussion">Discussion</option>
						                		<option value="Document">Document</option>
						                		<option value="Guest">Guest</option>
						                		<option value="Hands On Practice">Hands On Practice</option>
						                		<option value="Lab">Lab</option>
						                		<option value="Lab+Discussion">Lab+Discussion</option>
						                		<option value="Talk">Talk</option>
						                		<option value="Talk+Discussion">Talk+Discussion</option>
						                		<option value="Talk+Lab">Talk+Lab</option>
						            		</select>';
				$html .= 				'</div>';
				$html .= 			'</div>';

				$html .= 			'<div class="one-third-box mmi_date_box">';
				$html .= 				'<div class="inner-colmn">';
				$html .= 					'<input type="text" value="'.$mmi_date.'" name="mmi_date" id="mmi_date" placeholder="-Select date range-" />';
				$html .= 				'</div>';
				$html .= 			'</div>';		
				$html .= 			'<div class="clear"></div>';
				$html .= 		'</div>';

				/*$html .= 		'<div class="three-box-row advance_form">';		
				$html .= 			'<div class="clear"></div>';
				$html .= 		'</div>';*/

				$html .= 		'<div class="one-third-box ll_res_hide_block">';
				$html .=          	'<div class="field-msg search-now"><span></span></div>';
				$html .= 			'<div class="inner-colmn">';
				$html .= 				'<input type="submit" id="searchsubmit" value="Search now" class="smi"/>';
				$html .= 			'</div>';
				$html .=			'<div class="links-col export_result_link">';
				$html .=				'<a href="javascript:void(0);" id="show_advance_form"><strong>Go to advanced search</strong></a>';
				$html .=				'<a class="export_result" href="'.$new_url.'">Export search results</a>';
				$html .=		 	'</div>';
				$html .= 		'</div>';
				$html .= 		'<div class="one-third-box ll_res_hide_block">';
				$html .=          	'<div class="field-msg start-over"><span></span></div>';
				$html .= 			'<div class="inner-colmn">';
				//$html .= 				'<a class="button mmi_button" href="'.$mmi_index_url.'">Reset</a>';
				$html .=				'<a class="mmi_modify_Data" href="'.$mmi_index_url.'">Start over</a>';
				$html .= 			'</div>';
				$html .= 		'</div>';
				$html .= 		'<div class="clear"></div>';
				$html .= 	'</div>';
			
			
			$html .=		'<div class="three-box-row">';
			//$html .=		 	'<div class="links-col">';
			//$html .=		' 		<ul>';
			//$html .=		'    		<li><a href="javascript:void(0);" id="show_advance_form"><strong>Go to advanced search</strong></a></li>';
			//$html .=		'         	<li><a class="export" href="'.$new_url.'">Export search results</a></li>';
			//$html .=		'    	</ul>';
			//$html .=		 	'</div>';

			$html .=			'<div class="one-third-box ll_res_hide_block">';
			$html .=			'</div>';
			
			$html .=			'<div class="one-third-box ll_res_hide_block">';
			$html .=			'</div>';
			$html .=			'<div class="clear"></div>';
			$html .=		'</div>';

			$html .=		'<div class="three-box-row ll_res_show_block" style="display: none;">';
			$html .=			'<div class="one-third-box">';
			$html .=		 		'<div class="inner-colmn">';
			$html .=		 			'<label>&nbsp;</label><input type="submit" id="searchsubmit" class="smi_search" value="Search now"/>';
			$html .=		 		'</div>';
			$html .=		 	'</div>';
			$html .=		 	'<div class="one-third-box">';
			$html .=		 		'<div class="inner-colmn">';
			$html .=		 			'<label>&nbsp;</label><a class="button mmi_button" href="'.$mmi_index_url.'">Start over</a>';
			$html .=		 		'</div>';
			
			$html .=			'<div class="links-col export_result_link">';
				$html .=				'<a class="mob" href="javascript:void(0);" id="show_advance_form_mob"><strong>Go to advanced search</strong></a>';
				$html .=				'<a class="export_result export_result_mob" href="'.$new_url.'">Export search results</a>';
				$html .=		 	'</div>';

			$html .=		 	'</div>';
			$html .=		 	'<div class="clear"></div>';			
			$html .=		 '</div>';
			$html .=	'</form>';
			$html .='</div>';
			$html .='<div class="clear"></div>';
			
			return $html;
		}



		function reviews_first($hits) {


			file_put_contents(dirname(__FILE__).'/relevanssi.log', print_r( $hits, true));


			global $wpdb;

			$segment = trim($_REQUEST['segment']);

			$course  = 	trim($_GET['course']);

			$segment = trim($_GET['segment']);			

			if( ( $course !== ''  && $segment !== '' ) ) {

				$segment      = str_replace( trim( $_GET['course']), "", $segment );
				$segmantParts = explode('-', $segment);

				if(count($segmantParts) == 2 ){

					$segmantFirstPart = trim($segmantParts[0]);

					$sub_part = substr($segmantFirstPart, 0,2);

					$_record_number = $course . ' '.$sub_part;

					$args = array(
					    'post_type'  => 'multimedia-index',
					    'posts_per_page' => -1,
					    'meta_query' => array(
					        array(
					            'key' => '_record_number',
					            'value' => $_record_number,			            
					            'compare' => 'LIKE'
					        )
					    )
					);

					$query = new WP_Query($args);  

					if( $query->have_posts() ){

						$posts = $query->posts;
					
						$my_posts = array_merge($hits[0], $posts);

						$filtered_posts = array();

						foreach ( $my_posts as $post) {
							
							if( !array_key_exists($post->ID, $filtered_posts) ){
								$filtered_posts[$post->ID] = $post;
							}
						}

						$hits[0] = $filtered_posts;
					}
				}
			}

			return $hits;
		}

	

	
		//** function to update the search Query. **//
		function SearchFilter( $query ) {
		  		
		  	global $wpdb;
		  	if ($query->is_search) {

		  		if( !isset($_GET['MMI_seach']) ){
		  			return $query;
		  		}

		  		//** word that will be excluded form search. **//
		  		$definite_articles = array('the','this','then','there','from', 'a','for','to','as','and','or','is','was','be','can','could','would','isn\'t','wasn\'t', 'until','should','give','has','have','are','some','it','in','if','so','of','on','at','an','who','what','when','where','why','we','been','maybe','further','that');

		  		$taxquery 	= array();
		  		$meta_query_args 	= array();

		  		//** set the custom Post Type. **//
				$query->set('post_type', array('multimedia-index') );

				$querySting = '';

		        $title = trim($_GET['title']);
		        if( $title !== '' ){
		        	$querySting  = $title."+";
		        }

		         //** Date search  **//	
		        $mmi_date = trim($_GET['mmi_date']);
		        
		       // if( $mmi_date !== '' ){
					
					//** check if user search for date range **//
					/*if (preg_match('/-/',$mmi_date)){
						
						$mmi_date_array = explode('-', $mmi_date );
			        	$querySting .= 	$mmi_date_array[0].'+';		        	
			        	$querySting .= 	$mmi_date_array[1].'+';

			        	
						$meta_query_args[] =   array(
				            'key' 		=> '_date',
							'value' 	=> array($mmi_date_array[0] ,$mmi_date_array[1]),
							'compare' 	=> 'BETWEEN',					
							
				        );
					}else{
						$querySting .= 	$mmi_date.'+';
						$meta_query_args[] =   array(
				            'key' 		=> '_date',
							'value' 	=> $mmi_date,
							'compare' 	=> 'LIKE',
							
				        );
					}*/
					
		        //}


				$teacher_id = trim($_GET['teacher_id']);
		        if( $teacher_id !== '' ){
		        	
		        	$querySting .= $teacher_id.'+';
	        	  	$meta_query_args[] =   array(
			            'key' 		=> '_teacher_id',
						'value' 	=> $teacher_id ,
						'compare' 	=> 'LIKE',
			        );
		        }
		        
		        $record_id = trim($_GET['record_id']);
		        if( $record_id !== '' || $title !== '' ){
		        	$querySting .= $record_id.'+';	
	        	  	$meta_query_args[] =   array(
			            'key' 		=> '_record_number',
						'value' 	=> $record_id,
						'compare' 	=> 'LIKE',
			        );
		        }
				
				$course  = 	trim($_GET['course']);
				$segment = trim($_GET['segment']);

				if( ( $course !== ''  && $segment !== '' ) ) {

					$segment      = str_replace( trim( $_GET['course']), "", $segment );
					$segmantParts = explode('-', $segment);
					
					if (strpos($segmantParts[0], 'SIFTII') !== false) {
					    $segment      = str_replace( 'SIFTII', "", $segment );
					    $segmantParts = explode('-', $segment);
					    
					    $querySting .= 'SIFTII'.'+';
						$querySting .= $segmantParts[0].'+';
						$querySting .= $segmantParts[1].'+';
					}else{					
		        		$querySting .= $course.'+';
		        		$querySting .= $segmantParts[0].'+';
						$querySting .= $segmantParts[1].'+';
					}
	        	}
	        	
		        $record_type = trim($_GET['record_type']);
		        if( $record_type !== '' ){
		        	$querySting .= $record_type.'+';	
	        	  	$meta_query_args[] =   array(
			            'key' 		=> '_record_type',
						'value' 	=> $record_type,
						'compare' 	=> 'LIKE',
			        );
		        }

		        $summary = trim($_GET['title']);

		        $query->set('s', $querySting);
		       	
		    	//** set the mumber of the post to be shown on the page. **//
		    	$query->set('posts_per_page', -1 );

		    	//** set the post meta relation. **//
				$meta_query_args['relation'] = 'AND';

				//** set the post meta arguments. **//
				$query->set('meta_query', $meta_query_args);		
			}

		  	return $query;
		}

		//** function to get the list of Teachers. **//
		function getTeachers(){
			global  $wpdb;
			$mmi_query = $wpdb->get_results('SELECT * FROM wp_posts WHERE post_type = "teacher" AND post_status = "publish" ORDER BY post_date DESC', OBJECT);

			return $mmi_query;
		}

		//** function to create the select box**//
		function createSelectElemet( $args ){
			
			$html  = '';
			$html .=	'<select id="'.$args['id'].'" name="'.$args['name'].'" class="create_select_element">';
			$html .=	'<option value="">-Select-</option>';
			
			foreach ( $args['data'] as $key => $value) {
			        $html .= '<option value="'.$value->post_title.'">'.$value->post_title.'</option>';		    
		    }

		    $html .= '</select>';

		    return $html;

		}


		function getUserTags(){
			    $email = $current_user->user_email;
			    // $email = "ud@powerhat.com";

			    $app = new iSDK;
			    $app->cfgCon("connectionName");
			    $returnFields = array('Id');
			    $contact = $app->findByEmail($email, $returnFields);
			   
			    $fields = array('ContactGroup','GroupId');
			    $query = array('ContactId' => $contact[0]['Id']);
			    $tags =  $app->dsQuery("ContactGroupAssign",1000,0,$query,$fields);

			    $userTags = array();
			    $userTagString = '';

			    foreach ($tags as $key => $value) {
			        $userTags[] = $value['GroupId'];
			        $userTagString .= $value['GroupId']."|";
			    }

		    	return $userTagString;
		}


		static function UserTags(){
		    global $display_name , $user_email;
      		get_currentuserinfo();

		    $email = $user_email;
		    // $email = "ud@powerhat.com";

		    $app = new iSDK;
		    $app->cfgCon("connectionName");
		    $returnFields = array('Id');
		    $contact = $app->findByEmail($email, $returnFields);
		   
		    $fields = array('ContactGroup','GroupId');
		    $query = array('ContactId' => $contact[0]['Id']);
		    $tags =  $app->dsQuery("ContactGroupAssign",1000,0,$query,$fields);

		    $userTags = array();
		    $userTagString = '';

		    foreach ($tags as $key => $value) {
		        $userTags[] = $value['GroupId'];
		        $userTagString .= $value['GroupId']."|";
		    }

	    	return $userTags;
		}


		//** function to show the default list of the MMI posts **//
		function getDefaultList(){
			global $posts, $post;

			$postArray	=	array();

			$userTags 	=  MMISearchFilter::userTags();
			$args 		= array(	'post_type' => 'multimedia-index',
									'posts_per_page' =>  -1,
								);

			query_posts( $args );
			

			$count  = 0;
			echo '<div class="mmi_search_content">';
				echo do_shortcode('[MMI_search_box]');
				echo '<div class="multi_div">';
				//echo '<h2>Available Multimedia Files</h2>';
				echo '<div class="fright_pos">';
				echo '<div class="fleft_pos">Show Posts On Page</div>';
				echo '<div class="fleft_pos">';
				$this->showPostsPerPage();
				
				echo '</div></div><div class="clear"></div>';
				
				
				if ( have_posts() ) : 				
					
					while ( have_posts() ) : the_post(); 

				/*$mediafile = get_post_meta( get_the_ID(), '_mediafile', true );
				$canAccess = file_get_contents($mediafile);
				if(!$canAccess){
					
					$result_file['post_title'] = $post->post_title;
					$result_file['file_url'] = $mediafile;

					$resultArray[] = $result_file;
				}*/

						//** get post tags**//
						$postTags = explode(',', $post->tag_list ) ;

						//** check if user has acces to the post**//
						if(  !array_intersect($userTags , $postTags ) ){
							// echo '<hr><li><h4>NO ACCESS</h4></li>';
							continue;
						}


						$tempArray['post_link'] 	= get_permalink();
						$tempArray['post_title'] 	= get_the_title();
						$tempArray['record_number'] = get_post_meta( get_the_ID(), '_record_number', true );
						$tempArray['teacher_id'] 	= get_post_meta( get_the_ID(), '_teacher_id', true );
						$tempArray['summary'] 		= get_post_meta( get_the_ID(), '_summary', true );
						
						//** Update date code. **//
						// $tempArray['date'] 			= get_the_date();
						$tempArray['date'] 			= get_post_meta( get_the_ID(), '_date', true );
						$tempArray['record_type'] 	= get_post_meta( get_the_ID(), '_record_type', true );
						$tempArray['section'] 		= get_post_meta( get_the_ID(), '_section', true );


						//** Add Record to Post Array. **//
						$postArray[] = $tempArray;
					endwhile;									
				endif;
				
			
			wp_reset_query();
			
			/*$file_list = fopen("files_list.csv","w");
			foreach($resultArray as $result){

				fputcsv($file_list, $result);
			}	
			fclose($file_list);*/
			
			//** Function to redefine structure **//
			if( !empty( $postArray )){
				$limit = $this->getPostPerPage( $postArray );
				
				if( isset( $_GET['ortitle'] ) ){
					if( $_GET['ortitle'] == 'mtitle' ){
						$postArray = $this->MMI_sort( $postArray, 'post_title', $_GET['order'] );		
					}else if( $_GET['ortitle'] == 'mclass' ){
						$postArray = $this->MMI_sort( $postArray, 'record_number', $_GET['order'] );		
					}else if( $_GET['ortitle'] == 'mteacher' ){
						$postArray = $this->MMI_sort( $postArray, 'teacher_id', $_GET['order'] );		
					}else if( $_GET['ortitle'] == 'mactivity' ){
						$postArray = $this->MMI_sort( $postArray, 'record_type', $_GET['order'] );		
					}else if( $_GET['ortitle'] == 'mdate' ){
						$postArray = $this->MMI_sort( $postArray, 'date', $_GET['order'] );		
					}
					
				}
				//gurjeet
				
				
				$this->showPostsListTable( $postArray, $limit );
				
				// $this->responsiveTable( $postArray, 10  );
				
			}else{
				echo "<hr><h3>Sorry! No Posts Found!</h3><hr>";
			}
			echo '</div>';
			
		}


		function MMI_sort( $postArray, $orderby, $order  = 'asc' ){
			
			$orderType = '';
			if( $order  == 'desc') {
				$orderType = SORT_DESC;
			}else{
				$orderType	= SORT_ASC;
			}
			$sortArray = array(); 

			foreach($postArray as $item){ 
			    foreach($item as $key=>$value){ 
			        if(!isset($sortArray[$key])){ 
			            $sortArray[$key] = array(); 
			        } 
			        $sortArray[$key][] = $value; 
			    } 
			} 

			array_multisort( $sortArray[$orderby], $orderType , $postArray ); 
			return $postArray;
		}

		
		//** function to get the number of posts to be shown on the page.**//
		function showPostsPerPage(){
		
			$mmi_index_url = get_option('mmi_index_url', true );	
			if(isset($_POST['mmiPosts'])){
				
				global $wpdb;
				$postspage = $_POST['postspage'];
				
				//update_option( 'mmi_posts_page', $postspage);
				
			} 	
			echo '<form action="'.$mmi_index_url.'" method="post">
			
			<input type="hidden" name="mmiPosts" value="1">';
				
			//$selected = get_option('mmi_posts_page', true);
			
			if( $postspage ){
				$_SESSION['mmi_posts_page'] = $postspage;
				$selected =  $_SESSION['mmi_posts_page'];
			}else{
				$_SESSION['mmi_posts_page'] = 10;
				$selected =  $_SESSION['mmi_posts_page'];
			}	
				
			echo '<select id="postspage" name="postspage" onchange="this.form.submit()">
				
				<option value="5"';if($selected == "5") echo "selected";echo '>5</option>						
				<option value="10"';if($selected == "10") echo "selected";echo '>10</option>
				<option value="25"';if($selected == "25") echo "selected";echo '>25</option>
				<option value="50"';if($selected == "50") echo "selected";echo '>50</option>						
				<option value="100"';if($selected == "100") echo "selected";echo '>100</option>
				<option value="all"';if($selected == "all") echo "selected";echo '>All</option>
			</select>
			</form>';
		} 
		
		function getPostPerPage( $postArray ){
			
			//$limit = get_option('mmi_posts_page', true );
			
			$limit = $_SESSION['mmi_posts_page'];

			if($limit == "all"){
				$limit = count( $postArray);
			}

			return $limit;		
		}
		

		//** show posts. **//
		static function showPostsList( $postArray, $limit = 10 ){

			$site_url      =	site_url();
			$page = get_query_var( 'page', 1 );

			$totalPosts 	= count($postArray);
			
			$totalPages 	= ceil($totalPosts / $limit );
			
			if( isset( $page )){				
				if( $page == 0 ){
					$offset = 0;
				}else if( $page == 1 ){
					$offset = 0;
				}else{
					$offset = ($page - 1) * $limit;
				}
			}else{
				$offset = 0;
			}

			$intialStart	= $offset;
			$postLimit 		= $intialStart + $limit;
			
			echo '<hr><ul>';
			//$intialStart = $intialStart - 1;

			for ( $i = $intialStart; $i < $postLimit ; $i++ ) {
				
				if( !isset($postArray[$i])){
					continue;
				} 
				
				echo '<li class="li_searchtable"><table class="search_table">
				<tr>';
				//echo '<td class="td_searchtable">';
				//if($postArray[$i]['downloadLink']!=""){					
				//	echo '<p><input id="single_check" title="Maximum download file size 400mb" type="checkbox" name="file_download[]" value='.$postArray[$i]['downloadLink'].' /></p>';
				//}
				//echo '</td>';
				echo '<td style="padding-left:0px;"><h4><a href="'.$postArray[$i]['post_link'].'">' .$postArray[$i]['post_title'] . '</a></h4>';
				echo '<p><strong>Class:</strong> '.$postArray[$i]['record_number'].'</p>';								
				echo '<p><strong>Teacher: </strong>'.$postArray[$i]['teacher_id'].'</p>';					
				//echo '<p><strong>Section: </strong>'.$postArray[$i]['section'].'</p>';
				echo '<p><strong>Activity:</strong> '.$postArray[$i]['record_type'].'</p>';					
				//echo '<p><strong>Date: </strong>'.$postArray[$i]['date'].'</p>';
				//echo '<p><input type="checkbox" name="file_download" value="" />Download File</p>';				

				$post_summary = $postArray[$i]['summary'];

				$search_title = $_GET['title'];

				$pos = strpos($post_summary, $search_title);
				if($pos > 50){
					
					$exp_summ = explode(".",$post_summary);
					$summary_result = $exp_summ[0].'.'.$exp_summ[1].'.'.$exp_summ[2];


					/*$content_result1 = substr($post_summary,$pos-50,50);
					$content_result2 = "<span>".$search_title."</span>";
					$content_result3 = substr($post_summary,$pos+strlen($search_title),50);
				
					$summary_result = $content_result1.$content_result2.$content_result3;*/
					echo '<p><strong>Summary: </strong>'.$summary_result.'</p>';

					
				}else{

					$summary_result = substr($post_summary,0,300);
					echo '<p><strong>Summary: </strong>'.$summary_result.'...</p>';
				}
				//echo '<p><strong>post_summary: </strong>'.$post_summary.'</p>';
				/*echo '<p><strong>TEST: </strong>'.$pos.'</p>';
				echo '<p><strong>search_title: </strong>'.$search_title.'</p>'; */
				 
				
				/*if($postArray[$i]['downloadLink']!=""){					
				echo '<p><strong>Download File</strong>  <input title="Maximum download file size 400mb" type="checkbox" name="file_download[]" value='.$postArray[$i]['downloadLink'].' /></p>';
				}*/
				echo '</td></tr></table><hr></li>';
			}
			echo '</ul>';



			//** Add Pagination to the Page. **//
			// $this->mmi_pagination( $totalPages,  $page );
			//echo "<br>".$page."<br>".$totalPages."<br>";
			if($page >=  ( $totalPages - 6) ){
				$startPointer =  ($totalPages - 6);
				if( $startPointer < 1 ){
					$startPointer = 1;	
				}
			}else{
				if( $page == 0)
					$startPointer = 1;	
				else
					$startPointer = $page;	
			}

			//** =====================================  pervoius and next url =======================**//
			if( $page >= 2 ){
				$pagePrevious 	=  	$page - 1;
				$urlPrevious 	= 	esc_url( add_query_arg( 'page', $pagePrevious) );	
			}else{
				$urlPrevious 	=	esc_url( add_query_arg( 'page', '1') );
			} 

			if( $page == $totalPages ){
				$urlNext 	= 	esc_url( add_query_arg( 'page', $totalPages ) );	
			}else{
				$pageNext 	=  	$page + 1;
				$urlNext 	=	esc_url( add_query_arg( 'page', $pageNext ) );
			} 
			//** ===================================================================================**//

			$pageCount = 0;
 			if( $totalPages > 8){
				$url = esc_url( add_query_arg( 'page', '1') );			
				
				echo "<center>";
				
				if( $page == 0 || $page == 1 ){
					echo "<a class='mmi_page_selected' href='$url'>1</a> ";
					for ($i=$startPointer+1; $i<=$totalPages; $i++) {
						$url = esc_url( add_query_arg( 'page', $i) );
						
						if( $page == $i ){
							echo "<a class='mmi_page_selected' href='$url'>".$i."</a> ";
						}else{
							echo "<a class='mmi_page' href='$url'>".$i."</a> ";
						}

						if( $pageCount == 6){
							break;
						}else{
							$pageCount++;
						}
					}
					$url = esc_url( add_query_arg( 'page', $totalPages) );			
				echo "&nbsp;&nbsp;...<a class='mmi_page' href='$url'>".$totalPages."</a>";
				echo "<a disabled class='mmi_page' href='$urlNext'>".'>'."</a>";
				}
				elseif($page >= $totalPages-6 && $page < $totalPages){
					echo "<a class='mmi_page' href='$urlPrevious'>".'<'."</a> ";
					echo "<a class='mmi_page' href='$url'>1</a>...&nbsp;&nbsp;";
					for ($i=$startPointer; $i<=$totalPages; $i++) {
						$url = esc_url( add_query_arg( 'page', $i) );
						
						if( $page == $i ){
							echo "<a class='mmi_page_selected' href='$url'>".$i."</a> ";
						}else{
							echo "<a class='mmi_page' href='$url'>".$i."</a> ";
						}

						if( $pageCount == 6){
							break;
						}else{
							$pageCount++;
						}
					}
					$url = esc_url( add_query_arg( 'page', $totalPages) );
					echo "<a disabled class='mmi_page' href='$urlNext'>".'>'."</a>";			
				//echo "&nbsp;&nbsp;...<a class='mmi_page' href='$url'>".$totalPages."</a>";
				}
				elseif($page > 1 && $page < $totalPages-6){
					echo "<a class='mmi_page' href='$urlPrevious'>".'<'."</a> ";
					echo "<a class='mmi_page' href='$url'>1</a>...&nbsp;&nbsp;";
					for ($i=$startPointer; $i<=$totalPages; $i++) {
						$url = esc_url( add_query_arg( 'page', $i) );
						
						if( $page == $i ){
							echo "<a class='mmi_page_selected' href='$url'>".$i."</a> ";
						}else{
							echo "<a class='mmi_page' href='$url'>".$i."</a> ";
						}

						if( $pageCount == 6){
							break;
						}else{
							$pageCount++;
						}
					}
					$url = esc_url( add_query_arg( 'page', $totalPages) );
					echo "&nbsp;&nbsp;...<a class='mmi_page' href='$url'>".$totalPages."</a>";
					echo "<a disabled class='mmi_page' href='$urlNext'>".'>'."</a>";		
				}
				else{
					echo "<a class='mmi_page' href='$urlPrevious'>".'<'."</a> ";
					echo "<a class='mmi_page' href='$url'>1</a>...&nbsp;&nbsp;";
					for ($i=$startPointer; $i<=$totalPages; $i++) {
						$url = esc_url( add_query_arg( 'page', $i) );
						
						if( $page == $i ){
							echo "<a class='mmi_page_selected' href='$url'>".$i."</a> ";
						}else{
							echo "<a class='mmi_page' href='$url'>".$i."</a> ";
						}

						if( $pageCount == 6){
							break;
						}else{
							$pageCount++;
						}
					}
					$url = esc_url( add_query_arg( 'page', $totalPages) );			
				//echo "&nbsp;&nbsp;...<a class='mmi_page' href='$url'>".$totalPages."</a>";
				}
				
				//echo "<a disabled class='mmi_page' href='$urlNext'>".'>'."</a>";
				echo "</center>";
			}else{
				$url = esc_url( add_query_arg( 'page', '1') );			
				echo "<center>";
				if( $page == 0 || $page == 1 && $page < 2 ){
					echo "<a class='mmi_page_selected' href='$url'>1</a> ";
					for ($i=$startPointer+1; $i<=$totalPages; $i++) {
						$url = esc_url( add_query_arg( 'page', $i) );
						
						if( $page == $i ){
							echo "<a class='mmi_page_selected' href='$url'>".$i."</a> ";
						}else{
							echo "<a class='mmi_page' href='$url'>".$i."</a> ";
						}

						if( $pageCount == 6){
							break;
						}else{
							$pageCount++;
						}
					//echo "<style>.mmi_page{ display:none;}</style>";
					}
				}elseif( $page >= 2  && $page < $totalPages ){
					//echo "<a class='mmi_page_selected' href='$url'>1</a> ";
					echo "<a class='mmi_page' href='$urlPrevious'>".'<'."</a> ";
					for ($i=$startPointer; $i<=$totalPages; $i++) {
						$url = esc_url( add_query_arg( 'page', $i) );
						
						if( $page == $i ){
							echo "<a class='mmi_page_selected' href='$url'>".$i."</a> ";
						}else{
							echo "<a class='mmi_page' href='$url'>".$i."</a> ";
						}

						if( $pageCount == 6){
							break;
						}else{
							$pageCount++;
						}
					}
					echo "<a disabled class='mmi_page' href='$urlNext'>".'>'."</a>";
				}
				else{
					echo "<a class='mmi_page' href='$urlPrevious'>".'<'."</a> ";
					for ($i=$startPointer; $i<=$totalPages; $i++) {
						$url = esc_url( add_query_arg( 'page', $i) );
						
						if( $page == $i ){
							echo "<a class='mmi_page_selected' href='$url'>".$i."</a> ";
						}else{
							echo "<a class='mmi_page' href='$url'>".$i."</a> ";
						}

						if( $pageCount == 6){
							break;
						}else{
							$pageCount++;
						}
					}
					//echo "<a disabled class='mmi_page' href='$urlNext'>".'>'."</a>";
				}
				$url = esc_url( add_query_arg( 'page', $totalPages) );			
				//echo "<a class='mmi_page' href='$url'>".$totalPages."</a>";
				
				echo "</center>";
			}
		}


		//** Function to show the post in the Table Format. **//
		static function showPostsListTable( $postArray, $limit = 10 ){

			$site_url      =	site_url();
			$page = get_query_var( 'page', 1 );

			$totalPosts 	= count($postArray);
			$totalPages 	= ceil($totalPosts / $limit );
			// $totalPages = $totalPages - 1;

			if( isset( $page )){				
				if( $page == 0 ){
					$offset = 0;
				}else if( $page == 1 ){
					$offset = 0;
				}else{
					$offset = ($page - 1) * $limit;
				}
			}else{
				$offset = 0;
			}

			$intialStart	= $offset;
			$postLimit 		= $intialStart + $limit;

			//** Code to process the sort functionality **//
			// $current_url 	= home_url(add_query_arg(array(),$wp->request));
			$current_url 		= $site_url."/mmi-index/";
			
			$pageUrlTitle 		= $current_url.'?ortitle=asc';
			$pageUrlClass 		= $current_url.'?ortitle=mclass&order=asc';
			$pageUrlTeacher 	= $current_url.'?ortitle=mteacher&order=asc';
			$pageUrlActivity 	= $current_url.'?ortitle=mactivity&order=asc';
			$pageUrlDate 		= $current_url.'?ortitle=mdate&order=asc';

			if( isset( $_GET['ortitle'] ) ){
				if( $_GET['ortitle'] == 'mtitle'){
					if( $_GET['order'] == 'asc'){
						$pageUrlTitle 	= $current_url.'?ortitle=mtitle&order=desc';	
					}else{
						$pageUrlTitle 	= $current_url.'?ortitle=mtitle&order=asc';
					}				
				}

				if( $_GET['ortitle'] == 'mclass'){
					if( $_GET['order'] == 'asc'){
						$pageUrlClass 	= $current_url.'?ortitle=mclass&order=desc';	
					}else{
						$pageUrlClass 	= $current_url.'?ortitle=mclass&order=asc';
					}				
				}

				if( $_GET['ortitle'] == 'mteacher'){
					if( $_GET['order'] == 'asc'){
						$pageUrlTeacher 	= $current_url.'?ortitle=mteacher&order=desc';	
					}else{
						$pageUrlTeacher 	= $current_url.'?ortitle=mteacher&order=asc';
					}				
				}

				if( $_GET['ortitle'] == 'mactivity'){
					if( $_GET['order'] == 'asc'){
						$pageUrlActivity 	= $current_url.'?ortitle=mactivity&order=desc';	
					}else{
						$pageUrlActivity 	= $current_url.'?ortitle=mactivity&order=asc';
					}				
				}	

				if( $_GET['ortitle'] == 'mdate'){
					if( $_GET['order'] == 'asc'){
						$pageUrlDate 	= $current_url.'?ortitle=mdate&order=desc';	
					}else{
						$pageUrlDate 	= $current_url.'?ortitle=mdate&order=asc';
					}				
				}	

			}
			//** code block ends here**//

			
			//** Table Section Start Here**//
			//echo '<style>.mmi-table td{ vertical-align: text-top; padding-right: 10px;}</style>';
			//echo '<style>.mmi-table th{ vertical-align: text-top; }</style>';
			//echo '<style>.mmi-table {font-size: 14px; }</style>';
			echo '<table class="mmi-table">';
				 echo '<thead>
			        <tr>
			            <th style="width: 10%;"><a href="'.$pageUrlClass.'">Class</a></th>			            
			            
			            <th style="width: 25%;"><a href="'.$pageUrlTitle.'">Title</a></th>
			            
			            <th style="width: 10%;"><a href="'.$pageUrlTeacher.'">Teacher</a></th>

			            <th style="width: 10%;"><a href="'.$pageUrlActivity.'">Activity</a></th>
			            
			            <th style="width: 20%; min-height: 40px"><a href="javascript:void(0);">Summary</a></th>
			        </tr>
			    </thead>';

			    echo '<tbody>';
					for ( $i = $intialStart; $i < $postLimit; $i++ ) {
						
						if( !isset($postArray[$i])){
							continue;
						} 

						echo '<tr>';
							echo '<td>'.$postArray[$i]['record_number'].'</td>';		
							echo '<td><a href="'.$postArray[$i]['post_link'].'">'. $postArray[$i]['post_title'] .'</a></td>';
							echo '<td>'.$postArray[$i]['teacher_id'].'</td>';		
							echo '<td>'.$postArray[$i]['record_type'].'</td>';						
							//echo '<td>'.$postArray[$i]['date'].'</td>';								
							//echo '<td>'.$postArray[$i]['section'].'</td>';															
							echo '<td style="min-height: 40px">'.MMISearchFilter::limitString( $postArray[$i]['summary'], 20).'</td>';
						echo '</tr>';						 
					}
			    echo '</tbody>';
			echo '</table>';



			//** Add Pagination to the Page. **//
			// $this->mmi_pagination( $totalPages,  $page );
			if($page >=  ( $totalPages - 6) ){
				$startPointer =  ($totalPages - 6);
				if( $startPointer < 1 ){
					$startPointer = 1;	
				}
			}else{
				if( $page == 0){
					$startPointer = 1;
				}else{
					$startPointer = $page;	
				}
			}

			//** =====================================  pervoius and next url =======================**//
			if( $page >= 2 ){
				$pagePrevious 	=  	$page - 1;
				$urlPrevious 	= 	esc_url( add_query_arg( 'page', $pagePrevious) );	
			}else{
				$urlPrevious 	=	esc_url( add_query_arg( 'page', '1') );
			} 

			if( $page == $totalPages ){
				$urlNext 	= 	esc_url( add_query_arg( 'page', $totalPages ) );	
			}else{
				$pageNext 	=  	$page + 1;
				$urlNext 	=	esc_url( add_query_arg( 'page', $pageNext ) );
			} 
			//** ===================================================================================**//

			$pageCount = 0;
 			if( $totalPages > 1):
				$url = esc_url( add_query_arg( 'page', '1') );			
				
				echo "<center>";
				echo "<a class='mmi_page' href='$urlPrevious'>".'<'."</a> ";
				if( $page == 0 || $page == 1 ){
					echo "<a class='mmi_page_selected' href='$url'>1</a> ";
					for ($i=$startPointer+1; $i<=$totalPages; $i++) {
						$url = esc_url( add_query_arg( 'page', $i) );
						
						if( $page == $i ){
							echo "<a class='mmi_page_selected' href='$url'>".$i."</a> ";
						}else{
							echo "<a class='mmi_page' href='$url'>".$i."</a> ";
						}

						if( $pageCount == 6){
							break;
						}else{
							$pageCount++;
						}
					}
				}/*else if( $totalPages <=7 && $page != 0 && $page != 1){
					echo "<a class='mmi_page' href='$url'>1</a> ";
					for ($i=$startPointer+1; $i<=$totalPages; $i++) {
						$url = esc_url( add_query_arg( 'page', $i) );
						
						if( $page == $i ){
							echo "<a class='mmi_page_selected' href='$url'>".$i."</a> ";
						}else{
							echo "<a class='mmi_page' href='$url'>".$i."</a> ";
						}

						if( $pageCount == 6){
							break;
						}else{
							$pageCount++;
						}
					}
				}*/
				else{
					echo "<a class='mmi_page' href='$url'>1</a>...&nbsp;&nbsp;";
					for ($i=$startPointer; $i<=$totalPages; $i++) {
						$url = esc_url( add_query_arg( 'page', $i) );
						
						if( $page == $i ){
							echo "<a class='mmi_page_selected' href='$url'>".$i."</a> ";
						}else{
							echo "<a class='mmi_page' href='$url'>".$i."</a> ";
						}

						if( $pageCount == 6){
							break;
						}else{
							$pageCount++;
						}
					}
				}

				
				$url = esc_url( add_query_arg( 'page', $totalPages) );			
				// Going to last page
				echo "&nbsp;&nbsp;...<a class='mmi_page' href='$url'>".$totalPages."</a>";
				echo "<a disabled class='mmi_page' href='$urlNext'>".'>'."</a>";
				//echo "&nbsp;&nbsp;&nbsp;";
				//echo "<a class='button mmi_page' href='$url'>".'Last'."</a></center> ";
				echo "</center>";

			endif;

		}

		static function limitString($string, $limit = 100) {
		    
		    $words = explode(" ",$string);
		    
		    if( count($words)  < $limit ){
				 return implode(" ",$words);	    	
		    }else{
		    	$str = implode(" ",array_splice($words,0,$limit));	
		    	$str .= '...';	
    			return $str;
    		}
		}

		static function responsiveTable(  $postArray, $limit = 10 ){
			
			$page = get_query_var( 'page', 1 );

			$totalPosts 	= count($postArray);
			$totalPages 	= ceil($totalPosts / $limit );
			// $totalPages = $totalPages - 1;

			if( isset( $page )){				
				if( $page == 0 ){
					$offset = 0;
				}else if( $page == 1 ){
					$offset = 0;
				}else{
					$offset = ($page - 1) * $limit;
				}
			}else{
				$offset = 0;
			}

			$intialStart	= $offset;
			$postLimit 		= $intialStart + $limit;
			
			//** Table Section Start Here**//
			//echo '<style>.mmi-table td{ vertical-align: text-top; padding-right: 10px; text-align: center;}</style>';
			//echo '<style>.mmi-table th{ vertical-align: text-top; text-align: center;}</style>';
			//echo '<style>.mmi-table {font-size: 14px; }</style>';
			echo '<table class="mmi-table">';
				 echo '<thead>
			        <tr>
			            <th style="width: 10%;"><a href="javascript:void(0);">Class</a></th>			            
			            
			            <th style="width: 25%;"><a href="javascript:void(0);">Title</a></th>
			            
			            <th style="width: 10%;"><a href="javascript:void(0);">Teacher</a></th>
			          
			            <th style="width: 10%;"><a href="javascript:void(0);">Activity</a></th>

			            <th style="width: 15%;"><a href="javascript:void(0);">Date</a></th>
            						            
			            
			            <th style="width: 20%;"><a href="javascript:void(0);">Summary</a></th>
			        </tr>
			    </thead>';

			    echo '<tbody>';
					for ( $i = $intialStart; $i < $postLimit - 1; $i++ ) {
						
						if( !isset($postArray[$i])){
							continue;
						} 

						echo '<tr>';
							echo '<td>'.$postArray[$i]['record_number'].'</td>';	
							echo '<td><a href="'.$postArray[$i]['post_link'].'">'. $postArray[$i]['post_title'] .'</a></td>';
							echo '<td>'.$postArray[$i]['teacher_id'].'</td>';
							echo '<td>'.$postArray[$i]['record_type'].'</td>';								
							echo '<td>'.$postArray[$i]['date'].'</td>';								
							//echo '<td>'.$postArray[$i]['section'].'</td>';															
							echo '<td>'.MMISearchFilter::limitString( $postArray[$i]['summary'], 20).'</td>';

						echo '</tr>';						 
					}
			    echo '</tbody>';
			echo '</table>';
		}


		/*function get Save Search List Button*/
		
		function getSaveSearchList(){
			global $wpdb;

			$html = '';

			$user_id   = get_current_user_id();
			$base_path = 'http://mmi.mindinmotion-online.com';
			$sql       = "SELECT * FROM wp_mmi_save_search WHERE user_id = ".$user_id;
			$results   = $wpdb->get_results( $sql, OBJECT );

			/*block to show the list of records*/
			$html .= '<div id="mmi_saved_searches" style="display:none; width:730px;">';
					$html .= '<table>
						<thead>
							<th style="width: 70%;">Keyword(s)</th>
							<th style="width: 30%;">Actions</th>
						</thead>
						<tbody>';
						foreach ($results as $row) {
							$html .= '<tr id="data_'.$row->id.'">';
					            $html .= '<td>';
					            	$html .=  $row->keyword;
					            $html .= '</td>';
					            $html .= '<td>';
					            	$url  = $base_path.$row->url;
					            	$html .= '<a class="button btn-mmi-save" href="'.$url.'" id="mmi_show_data">view search</a>';
					            	$html .= '<a class="button btn-mmi-save delete_btn" href="" id="'.$row->id.'" style="margin-left:10px;">Delete</a>';					    					
					            	
					            $html .= '</td>';
					        $html .= '</tr>';
						}
					$html .= '</tbody>				        
				    </table>';
			$html .= '</div>';
			/*$html .= '<select class="savesearchselect cust-top-space save-search-custom-position" name="mmi_show_list" id="mmi_show_list" onchange="location = this.options[this.selectedIndex].value;">
					  <option>Saved searches</option>
					  </select>';*/
			//$html .=		'<a class="thickbox button" name="mmi_show_list" id="mmi_show_list" id_link="0" href="http://mmi.mindinmotion-online.com/wp-content/plugins/mmi_search_filter/getMMISavedSearch.php?height=306&width=730" style="float: right;margin-right: 5px;margin-top: -55px; z-index: 1000px;text-decoration: none; position: relative;">Search List</a>';
			
			
			return $html;
		}
}//** Class ends here. **//

//** Cearte a Object of the class. **//
$MMISearchFilter = new MMISearchFilter;

?>
