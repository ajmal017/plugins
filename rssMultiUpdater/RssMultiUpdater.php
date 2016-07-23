<?php 
/*
Plugin Name: Rss Multi Updater
Plugin URI: 
Description: This plugin allow Admin to Update RSS Feeds of multiple Blogs.
Version: 0.1
Author: G0947
Author URI:
License:
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class RssMultiUpdater {

    //** Constructor **//
    function __construct() {

        //** Action to load Assets Css **//     
        add_action( 'admin_enqueue_scripts',  array(&$this, 'loadAdminAssects') );

        //** Register menu. **//
        add_action('admin_menu', array(&$this, 'register_plugin_menu') );

    }

    function loadAdminAssects( $hook ){
        
        //** Load  Styling. **//
        /*wp_enqueue_style( 'RssMultiUpdater-datatables-css', 'https://cdn.datatables.net/1.10.10/css/jquery.dataTables.min.css' );
        wp_enqueue_script('RssMultiUpdater-datatables','https://cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js',array('jquery'), '1.0.0', true );*/
        wp_enqueue_script( 'RssMultiUpdater-custom-js', plugin_dir_url( __FILE__ ) . 'js/rmu_custom.js', array('jquery') );
        wp_enqueue_style( 'Rssmultiupdatercss-css',plugin_dir_url( __FILE__ ).'css/rssMultiUpdaterStyle.css' );
    }    

    //** Register menu Item. **//
    function register_plugin_menu(){
            add_menu_page( 'Rss Multi Updater', 'Rss Multi Updater', 'manage_options', 'RssMultiUpdater', array(&$this, 'admin_page'), '', 26 );            
            
    }

    
    /*function to show the page. */
    function admin_page(){
        
        $loaderImagePath = plugin_dir_url( __FILE__ ).'images/preloader-dots.gif';
        $this->processForm();

        $html = '';
        
        $html .= '<div class="wrap maincont" align="center">';
        $html .=    '<p class="heading_title">Rss Multi Updater</p>';        
        $html .=    '<div class="aw_content_Section">';

        $html .=        '<form action="" method="POST">';
        $html .=            $this->getBlogList();
        $html .=            '&nbsp;&nbsp;';
        $html .=            '<input type="hidden" value="1" name="rssMultiUpdate">';
        $html .=            '<div><input type="submit" id="rmu_submit" value="Update Rss" class="submit_btn"></div>';
        $html .=        '<form>';
        $html .=    '</div>';        
        $html .=    '<div class="aw_content_message">';
        $html .=        '<div class="rmu_loader"><img src="'.$loaderImagePath.'"/></div>';
        $html .=        '<div class="aw_message_Section" id="rmu_update_message">';
        $html .=            '<div> <h1>Blog(s) Updated</h1> </div>';
        $html .=        '</div>';
        $html .=    '</div>';
        $html .= '</div>';

        echo $html;
    }

    /*function to get the list of all the blogs. */
    function getBlogList(){
        global $wpdb;
        
        $sql     = "SELECT * FROM wp_aw_blog_sites";
        $results  = $wpdb->get_results( $sql, OBJECT );

        $html = '';        
        $html .= "<select name='siteTemplate' multiple id='directories' class='select_list'>";
        foreach ( $results as $directory ) {
            $html .= "<option value='".$directory->site_name."'>".$directory->site_name."</option>";
        }
        $html .= "</select>";

        $html .= "<script>jQuery('#directories').select2({placeholder: 'Please select blog(s)', allowClear: true});</script>";

        return $html;
    }

    /*function to process Form */
    function processForm(){
        /*Admin page content goes here */
        if( isset($_POST['rssMultiUpdate']) ){
            require "classes/RssUpdateSingle.php";
            
            $RssUpdateSingle = new RssUpdateSingle;
            $RssUpdateSingle->updateBlog();
        }
    }    
}

/*  create plugin object. */
$RssMultiUpdater = new RssMultiUpdater;
?>