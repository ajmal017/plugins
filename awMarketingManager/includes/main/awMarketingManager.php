<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class awMarketingManager {
    //** Constructor **//
    function __construct() {


        /*Uploader Section*/
        add_shortcode('amm_upload', array( 'AmmUploader', 'upload'));

        /*Buyer Uploader */
        add_shortcode('wire_bond_upload', array( 'BuyerUploader', 'upload'));
        
        /*Buyer section*/
        add_shortcode('amm_buyer', array( 'AmmBuyer', 'getBonds'));
        
        /*Wire Bond section*/
        add_shortcode('amm_wire_bond', array( 'amm_wire_bond', 'amm_wire_bond_function'));

        /*Seller section*/
        add_shortcode('amm_seller', array( 'AmmSeller', 'getSeller'));

        /*Wire Seller Bonds*/
        add_shortcode('buyer_Seller_Bonds', array( 'Wire_Seller_Bonds_clas', 'Wire_Seller_func'));

        /*Add Meta tag in header*/
        add_action('wp_head',array( $this, 'addMeta'));

        /*include js and css file*/
        add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts'));
		
		new AmmAjax;

        add_action( 'add_meta_boxes',  array( 'AmmMetaBoxes', 'auctionWinnerMetaBox') );

    }    


    public function addMeta(){

    	$url = admin_url().'admin-ajax.php';


    	echo "<meta key='url' value='$url' id='url' />";

    }

    public function add_scripts(){

    	$pluginpath = esc_url( plugins_url( 'awMarketingManager/assets/js/amm_custom.js'));
		 
		/*load frontend script. */
        wp_enqueue_script( 'awst_custom_script',$pluginpath, array('jquery'), '1.0.0' );
   
	}

}/*class ends here*/
?>
