<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'StockList' ) ) {
    include_once('StockList.php');
}
if ( ! class_exists( 'AjaxDeleteRecord' ) ) {
    include_once('AjaxDeleteRecord.php');
}

if ( ! class_exists( 'AjaxStockCSVUpload' ) ) {
    include_once('AjaxStockCSVUpload.php');
}

class Stocks {
   
    public static function stocksRate(){

        global $wpdb;
        $pageURL        = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $new_url        = $pageURL."&download_csv_stock=1&action=downloadstock";
        $delete_all_url = $pageURL."&delete_all=stock&action=delete_records";
        $downloadIcon   = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/downloading.gif';
        $deleteIcon     = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/remove-icon.png';
        $url            = admin_url().'admin-ajax.php';
        $loader         = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/loader.gif';

    	$html  = "";
        $html .=    "<form name='stockfrm' method='POST' enctype='multipart/form-data' action=''>";
        $html .=    	"<div class='uploadBtn'>";
        $html .=            "<div style='float:right'>";
        $html .=                "<input type='file' data-multiple-caption='{count} files selected' name='stockfile' accept='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' class='stockfile inputfile inputfile-1' placeholder='Import CSV' id='stockfile' required>";
        $html .=                 "<label for='file-1'><span>Import CSV</span></label>";
        $html .=                 "<input type='hidden' name='url' class='stockurl' value='$url'>";
        $html .=                 "<input type='hidden' name='stockuploader' class='stockuploader' value='1'>";
        $html .=                 "<input type='submit' name='stockBtn' class='stockBtn' value='Import CSV'>";
        $html .=            "</div>";
        $html .=            "<div style='float:right; margin-right: 20px;'>";
        $html .=                "<a href=".$new_url." class='downloadSample' title='Download sample file'><img src='".$downloadIcon."' height='40'></a>";
        $html .=            "</div>";
        $html .=            "<div style='float:right'>";
        $html .=                "<a href='".$delete_all_url."' class='downloadSample' title='Delete all records' onclick='return confirm(\"Are you sure you want to delete all records?\")'><img src='".$deleteIcon."' height='40'></a>";
        $html .=            "</div>";
        $html .=            "<div style='clear:both'></div>";
    	$html .=    	"</div>";
    	$html .=    "</form>";
        $html .="<div class='clear:both;'></div>";
        
        $html .="<div class='duplicates'>";
        $html .=    "<table>";
        $html .=        "<thead>";
        $html .=        "</thead>";
        $html .=        "<tbody>";
        $html .=        "</tbody>";
        $html .=    "</table>";
        $html .="</div>";

    	$html .= "<div class='success'><p>CSV has been uploaded successfully.</p></div>";
        $html .= "<div class='loader'><img src='$loader' height='100' width='100'></div>";
        $html .= "<div class='errors'><p>CSV file is not valid.</p></div>";
        $html .= "<div class='delMessage'><p>Stock has been deleted.</p></div>";
        
    	echo $html;
        Stocks::stockListing();
      
        if($_GET['download_csv_stock'] !== "" && $_GET['action'] == "downloadstock"){
            $fileName = "stock-sample.csv"; 
            Stocks::downloadStockSample($fileName);
        }

        if($_GET['delete_all'] == "stock" && $_GET['action'] == "delete_records"){
            Stocks::deleteAllRecords();
        }
    }

    public static function deleteAllRecords(){
        global $wpdb;
        $stocks = $wpdb->prefix . 'stocks';
        $wpdb->query("TRUNCATE TABLE ".$stocks);
        $stockpage = admin_url('admin.php?page=stocks');

        wp_redirect($stockpage);
        exit();
    }

    public static function downloadStockSample($fileName){
        
        $location = ABSPATH.'wp-content/plugins/TrackYourInvestments/includes/uploads/'.$fileName;
        
        if ( ! file_exists($location)){
            echo 'file missing';
        }else{
            $files = AjaxStockCSVUpload::readXLS( $location );

            ob_end_clean();
            ob_start();
            echo "stock_name;ticker;exchange_name;currency\n";
            foreach ($files as $result) {
                //$stock_id       = $result['stock_id'];
                $stock_name     = $result['stock_name'];
                $ticker         = $result['ticker'];
                $exchange_name  = $result['exchange_name'];
                $currency       = $result['currency'];
                
                echo $stock_name.";".$ticker.";".$exchange_name.";".$currency."\n";
            }
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename='.$fileName);
            exit;
        }
    }
 	
    public static function stockListing(){
        add_action( 'admin_head',array('Stocks','admin_header'));
        //add_filter( 'set-screen-option', array('Stocks','stock_set_screen'), 10, 3 );
        Stocks::newStockList();
    }

    public static function stock_set_screen( $status, $option, $value ) {
        if ( 'stocks_per_page' == $option ) return $value;
    }

    public static function stock_add_options() {
        global $stocklisting;
       
        $option     = 'per_page';
        $args       = array('label' => 'Number of Stocks showing', 'default' => 10, 'option' => 'stocks_per_page' );
        add_screen_option( $option, $args );

        $stocklisting = new StockList();
    }

    public static function admin_header() {
        $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;

        if( 'stocks' != $page ){
            return;
        }
    }

    //** New Function for the listing of the Stocks. **//
    public static function newStockList(){

        global $stocklisting;

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Stocks</h1>
            <a href="<?php echo admin_url('admin.php?page=add_new_stock') ?>" class="page-title-action">Add New</a>
            <hr class="wp-header-end">
            <div id="poststuff">
                <div id="post-body" class="">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <?php
                                $stocklisting = new StockList();
                                
                                $stocklisting->prepare_items();

                                echo '<input type="hidden" name="page" value="'.$_REQUEST['page'].'">';
                                $stocklisting->search_box( 'search', 'search_id_stocks' );

                                if( isset( $_POST['s'] ) ){
                                    $new_url = admin_url().'admin.php?s='.$_POST['s'].'&page='.$_POST['page'];

                                    header('location:'.$new_url);
                                }

                                ?>
                            </form>
                            <?php echo $stocklisting->display(); ?>                            
                        </div>
                    </div>
                </div>                
                <br class="clear">
            </div>
        </div>
        <?php
    }//** function newStockList ends **//
}/*class ends here*/
?>
