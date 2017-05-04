<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'ExchangeList' ) ) {
    include_once('ExchangeList.php');
}

if ( ! class_exists( 'AjaxExchangeCSVUpload' ) ) {
    include_once('AjaxExchangeCSVUpload.php');
}

class ExchangeRate {

    public static function exchange_Rate(){

        global $wpdb;
        $pageURL        = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $new_url        = $pageURL."&download_csv_exchange=1&action=downloadexchange";
        $delete_all_url = $pageURL."&delete_all=exchange&action=delete_records";
        $downloadIcon   = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/downloading.gif';
        $deleteIcon     = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/remove-icon.png';
		$url            = admin_url().'admin-ajax.php';
		$loader         = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/loader.gif';

		$html  = "";
    	$html .="<form name='exchangefrm' method='POST' enctype='multipart/form-data' action=''>";
        $html .=    "<div class='uploadBtn'>";
    	$html .=        "<div style='float:right'>";
        $html .= 		    "<input type='file' data-multiple-caption='{count} files selected' name='exchangefile' accept='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' class='exchangefile inputfile inputfile-1' placeholder='Import CSV' id='exchangefile' required>";
    	$html .= 		    "<label for='file-1'><span>Import CSV</span></label>";
    	$html .= 		    "<input type='hidden' name='url' class='exchangeurl' value='$url'>";
    	$html .= 		    "<input type='hidden' name='exchangeuploader' class='exchangeuploader' value='1'>";
    	$html .= 		    "<input type='submit' name='exchangeBtn' class='exchangeBtn' value='Import CSV'>";
        $html .=        "</div>";
        $html .=        "<div style='float:right; margin-right: 20px;'>";
        $html .=            "<a href=".$new_url." class='downloadSample' title='Download sample file'><img src='".$downloadIcon."' height='40'></a>";
        $html .=        "</div>";
        $html .=        "<div style='float:right'>";
        $html .=            "<a href='".$delete_all_url."' class='downloadSample' title='Delete all records' onclick='return confirm(\"Are you sure you want to delete all records?\")'><img src='".$deleteIcon."' height='40'></a>";
        $html .=        "</div>";
        $html .=        "<div style='clear:both'></div>";
    	$html .= 	"</div>";
    	$html .= "</form>";
        
        $html .="<div class='duplicates'>";
        $html .=    "<table>";
        $html .=        "<thead>";
        $html .=        "</thead>";
        $html .=        "<tbody>";
        $html .=        "</tbody>";
        $html .=    "</table>";
        $html .="</div>";

    	$html .= "<div class='success'>";
    	$html .= 	"<p>CSV has been uploaded successfully.</p>";
    	$html .= "</div>";
    	$html .= "<div class='loader'><img src='$loader' height='100' width='100'></div>";
    	$html .= "<div class='errors'><p>CSV  file is not valid.</p></div>";
        $html .= "<div class='delMessage'><p>Currency Rate has been deleted.</p></div>";
    	echo $html;
        ExchangeRate::exchangeListings();

        if($_GET['download_csv_exchange'] !== "" && $_GET['action'] == "downloadexchange"){
            $fileName = "ExchangeRate-sample.csv"; 
            ExchangeRate::downloadExchangeSample($fileName);
        }

        if($_GET['delete_all'] == "exchange" && $_GET['action'] == "delete_records"){
            ExchangeRate::deleteAllRecords();
        }
    }

    public static function deleteAllRecords(){
        global $wpdb;
        $currency_rate = $wpdb->prefix . 'currency_rate';
        $wpdb->query("TRUNCATE TABLE ".$currency_rate);
        $currency_ratepage = admin_url('admin.php?page=exchange_rate');
        wp_redirect($currency_ratepage);
        exit();
    }

    public static function downloadExchangeSample($fileName){
        
        $location = ABSPATH.'wp-content/plugins/TrackYourInvestments/includes/uploads/'.$fileName;
        
        if ( ! file_exists($location)){
            echo 'file missing';
        }else{
            $files = AjaxExchangeCSVUpload::readXLS( $location );
            ob_end_clean();
            ob_start();
            echo "date;base_currency;conversion_currency;value\n";
            
            foreach ($files as $result) {
                $date                   = $result['date'];                  
                $base_currency          = $result['base_currency'];      
                $conversion_currency    = $result['conversion_currency'];      
                $value                  = $result['value'];     
                
                echo $date.";".$base_currency.";".$conversion_currency.";".$value."\n";
            }
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename='.$fileName);
            exit;
        }
    }


    public static function exchangeListings(){

        add_action( 'admin_head',array('ExchangeList','admin_header'));
        
        //add_filter( 'set-screen-option', 'set_screen', 10, 3 );
        
        ExchangeRate::newExchangeList();

    }

    public static function set_screen( $status, $option, $value ) {
        return $value;
    }

    public static function add_options() {
        global $exchangelistobj;
        $option = 'per_page';
        $args = array(
            'label' => 'Number of Exchange Currency showing',
            'default' => 10,
            'option' => 'exchange_per_page'
        );
        add_screen_option( $option, $args );
        $exchangelistobj = new ExchangeList();
    }

    public static function admin_header() {
        $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
        if( 'exchange_rate' != $page ){
            return;
        }
        echo '<style type="text/css">';
        echo '.wp-list-table .column-ID { width: 5%; }';
        echo '.wp-list-table .column-date { width: 20%; }';
        echo '.wp-list-table .column-base_currency { width: 15%; }';
        echo '.wp-list-table .column-conversion_currency { width: 20%; }';
        echo '.wp-list-table .column-value { width: 20%;}';
        echo '.wp-list-table .column-actions { width: 20%;}';
        echo '</style>';
    }

    //** New Function for the listing of the Stocks. **//
    public static function newExchangeList(){

        global $exchangelistobj;

        ?>
        <div class="wrap">
            <h2 class="wp-heading-inline">Currency List</h2>
            <a href="<?php echo admin_url('admin.php?page=add_new_exchange') ?>" class="page-title-action">Add New</a>
            <hr/>
            <div id="poststuff">
                <div id="post-body" class="">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post"><?php
                                $exchangelistobj = new ExchangeList();
                                $exchangelistobj->prepare_items();
                                echo '<input type="hidden" name="page" value="'.$_REQUEST["page"].'">';
                                $exchangelistobj->search_box( 'search', 'search_id' );
                                 if( isset( $_POST['s'] ) ){
                                    $new_url = admin_url().'admin.php?s='.$_POST['s'].'&page='.$_POST['page'];

                                    header('location:'.$new_url);
                                }
                                ?>
                            </form>
                                <?php $exchangelistobj->display(); ?>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div><?php
    }//** function newExchangeList ends **//
}/*class ends here*/
?>