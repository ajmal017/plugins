<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'StockPriceList' ) ) {
    include_once('StockPriceList.php');
}
if ( ! class_exists( 'AjaxDeleteRecord' ) ) {
    include_once('AjaxDeleteRecord.php');
}

if ( ! class_exists( 'AjaxStockPriceCSVUpload' ) ) {
    include_once('AjaxStockPriceCSVUpload.php');
}

class StockPrice {
   
    public static function stocksPrice(){

        global $wpdb;
        $pageURL        = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $new_url        = $pageURL."&download_csv_stockprice=1&action=downloadstockprice";
        $delete_all_url = $pageURL."&delete_all=stock-price&action=delete_records";
        $downloadIcon   = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/downloading.gif';
        $deleteIcon     = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/remove-icon.png';
        $url            = admin_url().'admin-ajax.php';
        $loader         = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/loader.gif';

        $html  = "";
        $html .=    "<form name='stockpricefrm' method='POST' enctype='multipart/form-data' action=''>";
        $html .=        "<div class='uploadBtn'>";
        $html .=            "<div style='float:right'>";
        $html .=                "<input type='file' data-multiple-caption='{count} files selected' name='stockpricefile' accept='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' class='stockpricefile inputfile inputfile-1' placeholder='Import CSV' id='stockpricefile' required>";
        $html .=                "<label for='file-1'><span>Import CSV</span></label>";
        $html .=                "<input type='hidden' name='url' class='stockpriceurl' value='$url'>";
        $html .=                "<input type='hidden' name='stockpriceuploader' class='stockpriceuploader' value='1'>";
        $html .=                "<input type='submit' name='stockpriceBtn' class='stockpriceBtn' value='Import CSV'>";
        $html .=            "</div>";
        $html .=            "<div style='float:right; margin-right: 20px;'>";
        $html .=                "<a href=".$new_url." class='downloadSample' title='Download sample file'><img src='".$downloadIcon."' height='40'></a>";
        $html .=            "</div>";
        $html .=            "<div style='float:right'>";
        $html .=                "<a href='".$delete_all_url."' class='downloadSample' title='Delete all records' onclick='return confirm(\"Are you sure you want to delete all records?\")'><img src='".$deleteIcon."' height='40'></a>";
        $html .=            "</div>";
        $html .=        "<div style='clear:both'></div>";
        $html .=        "</div>";
        $html .=    "</form>";
        $html .="<div class='clear:both;'></div>";
        
        $html .= "<div class='success'><p>CSV has been uploaded successfully.</p></div>";
        $html .= "<div class='loader'><img src='$loader' height='100' width='100'></div>";
        $html .= "<div class='errors'><p>CSV file is not valid.</p></div>";
        $html .= "<div class='delMessage'><p>Stock price has been deleted.</p></div>";
        
        echo $html;
        StockPrice::stockPriceListing();
      
        if($_GET['download_csv_stockprice'] !== "" && $_GET['action'] == "downloadstockprice"){
            $fileName = "stock-price-sample.csv"; 
            StockPrice::downloadStockSample($fileName);
        }

        if($_GET['delete_all'] == "stock-price" && $_GET['action'] == "delete_records"){
            StockPrice::deleteAllRecords();
        }
    }

    public static function deleteAllRecords(){
        global $wpdb;
        $stock_price = $wpdb->prefix . 'stock_price';
        $wpdb->query("TRUNCATE TABLE ".$stock_price);
        $stockpricepage = admin_url('admin.php?page=stock_price');

        wp_redirect($stockpricepage);
        exit();
    }

    public static function downloadStockSample($fileName){
        
        $location = ABSPATH.'wp-content/plugins/TrackYourInvestments/includes/uploads/'.$fileName;
        
        if ( ! file_exists($location)){
            echo 'file missing';
        }else{
            $files = AjaxStockPriceCSVUpload::readXLS( $location );
                     
            ob_end_clean();
            ob_start();
            echo "ticker;date;open;high;low;close;volume;adj_close\n";
            foreach ($files as $result) {

                $originalDate = $result['date'];
                $newDate = date("Y-m-d", strtotime($originalDate));

                $ticker     = $result['ticker'];
                $date       = $newDate;
                $open       = $result['open'];
                $high       = $result['high'];
                $low        = $result['low'];
                $close      = $result['close'];
                $volume     = $result['volume'];
                $adj_close  = $result['adj_close'];
                echo $ticker.";".$date.";".$open.";".$high.";".$low.";".$close.";".$volume.";".$adj_close."\n";
            }
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename='.$fileName);
            exit;
        }
    }
    
    public static function stockPriceListing(){
        add_action( 'admin_head',array('Stocks','admin_header'));
        //add_filter( 'set-screen-option', array('Stocks','stock_set_screen'), 10, 3 );
        StockPrice::newStockList();
    }

    public static function stock_set_screen( $status, $option, $value ) {
        if ( 'stocksprice_per_page' == $option ) return $value;
    }

    public static function stock_add_options() {
        global $stockPricelisting;
       
        $option     = 'per_page';
        $args       = array('label' => 'Number of Stocks showing', 'default' => 10, 'option' => 'stocksprice_per_page' );
        add_screen_option( $option, $args );

        $stockPricelisting = new StockPriceList();
    }

    public static function admin_header() {
        $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;

        if( 'stock_price' != $page ){
            return;
        }
    }

    //** New Function for the listing of the Stocks. **//
    public static function newStockList(){

        global $stockPricelisting;

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Stock Prices</h1>
            <a href="<?php echo admin_url('admin.php?page=add_new_stockprice') ?>" class="page-title-action">Add New</a>
            <hr class="wp-header-end">
            <div id="poststuff">
                <div id="post-body" class="">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <?php
                                $stockPricelisting = new StockPriceList();
                                $stockPricelisting->prepare_items();
                                echo '<input type="hidden" name="page" value="'.$_REQUEST["page"].'">';
                                $stockPricelisting->search_box( 'search', 'search_id' );
                                
                                if( isset( $_POST['s'] ) ){
                                    $new_url = admin_url().'admin.php?s='.$_POST['s'].'&page='.$_POST['page'];

                                    header('location:'.$new_url);
                                }

                                ?>
                            </form>
                                <?php $stockPricelisting->display(); ?>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
        <?php
    }//** function newStockPriceList ends **//
}/*class ends here*/
?>