<?php
set_time_limit(0);
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class aw_bond_management {

    //** Constructor **//
    function __construct() {

        //** Action to load Assets Css **//
        add_action( 'wp_enqueue_scripts',  array(&$this, 'loadAssectCss') );

        add_action( 'admin_enqueue_scripts',  array(&$this, 'loadAdminAssects') );

        //** Register menu. **//
        add_action('admin_menu', array(&$this, 'register_plugin_menu') );

       // add_action( 'wp_ajax_csvUpload', array(&$this, 'csvUpload') );

        add_shortcode('csv_upload', array( &$this, 'csvUpload'));

        add_shortcode('BID_HISTORY', array( 'AwBidder', 'bidHistory'));


        /* Shortcode for Auction Listing */
        add_shortcode('aw_live_auctions', array( 'AwAuction', 'awLiveAuctions'));
        add_shortcode('aw_upcoming_auctions', array( 'AwAuction', 'awUpcomingAuctions'));

        /* shordtcode for seller dashboard*/
        add_shortcode('aw_seller', array( 'AwSeller', 'sellerDashBoard'));

        /* shordtcode for Tied dashboard*/
        add_shortcode('aw_tied_bwic', array( 'AwTiedBids', 'awTiedAuctions'));

        /* shordtcode for tied auctions*/
        add_shortcode('aw_tied', array( 'AwAuction', 'tiedAuctions'));

        /*Ajax functions filters */
        add_action('wp_ajax_aw_place_bid', array('AwAjax', 'awPlaceBid'));

        /*Ajax functions filters */
        add_action('wp_ajax_trade_bid', array('AwAjax', 'awTradeBid'));


        add_action('wp_footer', array($this, 'temp_add_footer'));
        add_action('wp_footer', array('AwSeller', 'jsScripts'));

        /* Fire our meta box setup function on the post editor screen. */
        // add_action( 'load-post.php', array('AwMetaBoxes', 'auctionWinnerMetaBoxSetup') );
        // add_action( 'load-post-new.php', array('AwMetaBoxes', 'auctionWinnerMetaBoxSetup') );

        add_action( 'add_meta_boxes', array('AwMetaBoxes', 'auctionWinnerMetaBox') );


    }

    function temp_add_footer() {
        ?>
            <Script>
                jQuery( document ).ready(function() {
                    console.log( "ready Test!" );

                    jQuery("#aw_place_bid_btn").click(function(){
                        var   auction_id        = jQuery("#auct_id").val();
                        var   bid_price         = jQuery("#bid_price").val();
                        var   bon_id            = jQuery("#bon_id").val();
                        var   col_id            = 'bon_'+bon_id;

                      //jQuery("#post_"+auction_id).find("td:contains("+bon_id+")").addClass(col_id);
                      jQuery("#post_"+auction_id).find("#accordion").find("#data_"+auction_id).find(".card-block").find(".all_detail").find("div:contains("+bon_id+")").addClass(col_id);
                      jQuery('.bon_'+bon_id).next().next().next().next().next().next().next().next().next().find("a").attr("data-bid_price",bid_price);
                      jQuery('.bon_'+bon_id).next().next().next().next().next().next().next().next().next().find("a").text("Edit Bid");

                      jQuery('.loader-img').show();
                        jQuery.ajax({
                            type: "POST",
                            data : {action:"aw_place_bid",id:auction_id,bid_price:bid_price,bon_id:bon_id},
                            url: "/wp-admin/admin-ajax.php",
                            success: function(result){
                              console.log(result);
                              var res = result;
                              res = "success";
                              if (res == "success") {
                                  jQuery("#bid_price").val("");
                                  jQuery(".place_bid").hide();
                                  jQuery(".bid_success_msg").css("display","inline");
                                  //jQuery(".rem").remove();
                                  jQuery('.loader-img').hide();
                              }
                            }
                        });
                    });

                    jQuery('#ss').on('click', function(){
                        console.log( "Testing..." );
                        jQuery.ajax({
                            type:"POST",
                            url: "http://jonnathan.artworldwebsolutions.com/wp-admin/admin-ajax.php",
                            data: {action:'aw_place_bid',post_id:"hello", post_like:"World"},
                            success:function(data){
                                console.log(data);
                            }
                        });
                    });

                });
            </Script>
        <?php
    }

    function loadAssectCss(){
        $plugin_url    =   plugin_dir_url( __FILE__ );

        //** Load  Styling. **//
        wp_enqueue_style( 'aw_bond_management_style', $plugin_url . 'css/awst_style.css' );
        wp_enqueue_style('aw_bond_management-font-awesome','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css');

        /*load frontend script. */
        wp_enqueue_script( 'awst_custom_script', plugin_dir_url( __FILE__ ).'js/awst_custom_script.js', array('jquery'), '1.0.0' );


    }

    function loadAdminAssects( $hook ){
        //** Load  Styling. **//
        $plugin_url = plugin_dir_url( __FILE__ );
        wp_enqueue_style( 'awsocialtabs_style', $plugin_url . 'css/aw_admin_style.css' );

        //wp_enqueue_style( 'bootstrapadmin_style', get_template_directory_uri().'/style.css' );

        //wp_enqueue_style('awsocialtabs_style_font_awesome','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css');
        /*load admin script. */
       // wp_enqueue_script( 'awst_admin_custom_script', plugin_dir_url( __FILE__ ) . '/js/awst_admin_custom_script.js', array('jquery'), '1.0.0' );

        //wp_enqueue_script( 'bootstraoadmin_js', get_template_directory_uri(). '/js/bootstrap.js', array('jquery'), '1.0.0' );
    }

    //** Register menu Item. **//
    function register_plugin_menu(){
        add_menu_page( 'Bonds Manager', 'Bonds Manager', 'manage_options', 'awsocialtabs', array('AwstAdminPages', 'plugin_homepage'), 'dashicons-share', 6 );
        add_submenu_page('awsocialtabs', 'Bonds Manager | settings', 'Settings', 'manage_options','awst_settings', array('AwstAdminPages', 'awst_settings'));
        add_submenu_page('awsocialtabs', 'All Auctions', 'All Auctions', 'manage_options','all_auctions', array('AwAdminAuctionList', 'adminAuctionDashboard'));
    }
    

    function csvUpload(){

        $ss = dirname(__FILE__)."/uploads";

        if(isset($_POST['submit'])){

            $bondIDs = array();
            
            //header('Content-Type: text/plain');

            //$csv_name = $_POST['csv_file'];

            $tmp_name = $_FILES["csv_file"]["tmp_name"];

            $name = $_FILES["csv_file"]["name"];

            $ext = pathinfo($name, PATHINFO_EXTENSION);

            move_uploaded_file($tmp_name, "$ss/$name");

            //$Filepath = $ss."/test.xls";

            $Filepath = $ss."/".$name;

            require('classes/spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
            require('classes/spreadsheet-reader-master/SpreadsheetReader.php');

            date_default_timezone_set('UTC');

            $StartMem = memory_get_usage();

            try{
                
                $Spreadsheet = new SpreadsheetReader($Filepath);

                $BaseMem = memory_get_usage();

                $Sheets = $Spreadsheet -> Sheets();

                foreach ($Sheets as $Index => $Name){

                    $Time = microtime(true);

                    $Spreadsheet -> ChangeSheet($Index);

                    foreach ($Spreadsheet as $Key => $Row){

                        if ($Row[0] == "") {
                            continue;
                        }

                        if( $Row ){

                            if( ($ext == "xlsx") && ($Key === 0) ){

                                $value[] = $Row;

                            }elseif( ($ext == "xls") && ($Key === 1) ){

                                $value[] = $Row;

                            }else{

                                $my_post = array(
                                'post_title'    => $Row[0],
                                'post_status'   => 'publish',
                                'post_author'   => get_current_user_id(),
                                'post_type'     => 'bond'
                                );

                                $lastid = wp_insert_post( $my_post );

                                /* add bond IDs to array  */
                                $bondIDs[] = $lastid;

                                $combine = array_combine($value[0], $Row);

                                $uid = $value[0];
                                $gnm = $Row;
                                $result1 = array();
                                foreach($uid as $k => $u) {
                                    if (!isset($result1[$u])) {
                                        $result1[$u] = array();
                                    }
                                    $result1[$u] = $gnm[$k];
                                }
        

                                foreach ($result1 as $key => $val) {

                                    $key_str = strtolower($key);

                                    $key_name = str_replace(' ', '_', $key_str);
                                    update_post_meta($lastid,$key_name,$val);
                                }
                            }
                        }

                        $CurrentMem = memory_get_usage();
                    }

                }


            }
            catch (Exception $E)
            {
                echo $E -> getMessage();
            }

            /* create auctions */
            $start_date = strtotime($_POST['start_date']);
            $end_date   = strtotime($_POST['end_date']);
            $bwic_title = $_POST['bwic_title'];

            $result = AwAuction::createAuction( $bondIDs, $start_date, $end_date, $bwic_title);

            if( $result ) {
                $html = '<div class="alert alert-success">';
                $html .=  '<strong>Success!</strong> Data Uploaded Successfully.';
                $html .= '</div>';

                echo $html;

            }else{

                /*delete bonds of auction is not create successfully.*/
                AwBond::rollback( $bondIDs );

                $html = '<div class="alert alert-danger">';
                $html .= '<strong>Error!</strong> Error in uploading data.';
                $html .= '</div>';

                echo $html;
            }
        }
        ?>

        <div class="bwic_upload_section container col-md-12">
        <form name="form" id="form" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="usr">BWIC Title:</label>
                        <div class='input-group' id='bwic_title'>
                            <input type='text' name="bwic_title" class="form-control" />
                            <span class="input-group-addon">
                                <span class="glyphicons glyphicons-text-underline"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="usr">BWIC Post Date/Time:</label>
                        <div class='input-group date' id='datetimepicker6'>
                            <input type='text' name="start_date" class="form-control" required />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="usr">BWIC Due Date/Time:</label>
                        <div class='input-group date' id='datetimepicker7'>
                            <input type='text' name="end_date" class="form-control" required />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="usr">Upload CSV:</label>
                        <input type="file" name="csv_file" class="filestyle" id="csv_file" required />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class='col-md-6'>
                    <div class="form-group">
                        <input type="submit" name="submit" value="Submit" class="bwic_submit btn btn-default" />
                    </div>
                </div>
            </div>
        </form>
        </div>
        <?php
    }

    function bwicUpload(){

        $ss = dirname(__FILE__);
        ?>
        <form name="form" id="form" method="post" enctype="multipart/form-data">
            <div>Upload CSV</div>
            <input type="hidden" name="test" value="abc">
            <div><input type="file" name="csv_file" id="csv_file" /><img id="loading-image" class="loading-image" height="20" width="20" src="<?php echo $ss; ?>/ajax-loading-big.gif" /></div>
            <div><input type="submit" name="submit" value="Submit" class="bwic_submit" /></div>
            <!-- <div><a  onclick="dataUpload()" class="bwic_btn">Submit</a></div> -->
        </form>
            <?php
    }

}/*class ends here*/
?>
