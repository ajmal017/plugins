<?php
/*
Plugin Name: Custom Article
Plugin URI: 
Description: This plugin allow to add your own article.
Version: 0.1
Author: G0947
Author URI:
License:
*/

require_once(ABSPATH . 'wp-load.php');

class Customarticle extends WP_Widget {

    function __construct() {
        
        $widget_ops = array('classname' => 'custom-article', 'description' => __('Custom Article', 'customarticle') );
        parent::__construct( 'custom-article', __('Custom Article','customarticle'),  $widget_ops);

    }

    function widget( $args, $instance ) {
        require_once(dirname(__FILE__)."/languages_text.php");
        ?>
        <link href="<?php echo plugins_url( 'css/custom_article.css' , __FILE__ )  ?>" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?php echo plugins_url( 'js/custom-js.js' , __FILE__ )  ?>"></script>
        <?php
        extract( $args );

        $title          = $instance['cat'];
        $get_url        = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $get_cat        = explode('/', $get_url);
        $get_cate       = $get_cat[5];
        $get_categ      = ucfirst($get_cate);
        $bloginfo       = get_bloginfo('home');
        $fb_like        = $bloginfo."/wp-content/plugins/CustomPost/uploads/facebook-like-button.png";
        $fb_share       = $bloginfo."/wp-content/plugins/CustomPost/uploads/button-fshare.png";
        $blogname       = str_replace("http://iris.scanmine.com/", "", $bloginfo);
        $register_link  = "http://iris.scanmine.com/".$blogname."/registrer-deg";
        $login_link     = "http://iris.scanmine.com/".$blogname."/login";
        $close_img      = "http://iris.scanmine.com/".$blogname."/wp-content/plugins/CustomPost/uploads/close.png";
        
        //$aa           = get_term_by('id', 12, 'category');
        /*$getall_cat     = get_the_category();
        $aa             = $getall_cat[0];*/

        $firstmenu      = "<script>document.writeln(jQuery('#menu-top-primary-menu').find('li').eq(0).find('a').find('span').text())</script>";
    
        $puain          = PUAIN;
        $pin            = PIN;
        $login          = LOGIN;
        $regis          = REGISTER;
        $logout         = LOGOUT; 
        $yahbp          = YAHBP;
        $lasuoa         = LASUOA;
        $yahbstom       = YAHBSTOM;
        $cbstsypa       = CBSTSYPA;
        
        
        echo "<div class='custom-editor-div' id='custom-editor-div'>
				<div class='left-custom-col home-cat' style='display:none;'>
                    <a href='#' class='custom-editor-link c_e_l_desk' id='custom-editor-link2' onclick='return theFunction2();'>".$puain." ".$firstmenu."
                    </a>
                    <a href='#' class='custom-editor-link c_e_l_mob' id='custom-editor-link2' onclick='return theFunction2();'>".$pin." ".$firstmenu." 
                    </a>";
        echo    "</div>
                <div class='left-custom-col cat-cat' style='display:none;'>
                    <a href='#' class='custom-editor-link c_e_l_desk' id='custom-editor-link' onclick='return theFunction();'>".$puain." ".$get_categ." 
                    </a>
                    <a href='#' class='custom-editor-link c_e_l_mob' id='custom-editor-link' onclick='return theFunction();'>".$pin." ".$get_categ." 
                    </a>";
		echo	"</div>
				<div class='right-custom-col'>
					<span class='login_text'>
                        <a href='".$login_link."' class='link_style'>".$login."</a>
                    </span>
                    <span class='register_text'>
                        <a href='".$register_link."' class='link_style'>".$regis."</a>
                    </span>
                    <span id='logout_butn'>
                        <a class='logout_btn' href='".wp_logout_url($bloginfo)."'>".$logout."</a>
                    </span>
                    <span id='close_butn'>
                        <a class='close_btn' href='#' onclick='close_custom_editor();'><img src='".$close_img."' height='20' width='20' topmargin='-6'>
                        </a>
                    </span>
				</div>
				<div style='clear:both;'></div>
              </div>";
        echo "<div class='visitor_msg_div' id='visitor_msg_div'>
                <h3 id='vistir_msg' style='color:#FFF;'>".$yahbstom." ".$cbstsypa." 
                    <span>
                        <a style='color:#FFF;' href='".$register_link."'>".$regis."</a>
                    </span>
                </h3>
              </div>";
        echo "<div class='register_msg_div' id='register_msg_div'>
                <h3 id='register_msg' style='color:#FFF; text-align:center; padding:5px 0;'>".$yahbp." ".$lasuoa." 
                    <span class='share_btn'>".do_shortcode('[huge_it_share]')."</span>
                </h3>
              </div>";
       

        $get_urls    = $_GET['abc'];
        
        if($get_urls == 'checkstatus'){
            ?>
            <script type="text/javascript"> reg_msg_div(); </script>
            <?php
        }
        if ( ! is_user_logged_in() ){
            ?>
            <script type="text/javascript"> login_sett(); </script>
            <?php
        }else{
            ?>
            <script type="text/javascript"> log_out(); </script>
            <?php
        }
       
    }

    function form( $instance ) {
        // Widget defaults       
    }
}
function custom_article() {
    register_widget('Customarticle');
}
add_action('widgets_init', 'custom_article');

?>