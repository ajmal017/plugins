<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if ( ! class_exists( 'DividendList' ) ) {
    include_once('DividendList.php');
}

if ( ! class_exists( 'AjaxDividendCSVUpload' ) ) {
    include_once('AjaxDividendCSVUpload.php');
}

class Dividend {

	public static function dividendRate(){

        $pageURL        = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $new_url        = $pageURL."&download_csv_dividend=1&action=downloaddividend";
        $delete_all_url = $pageURL."&delete_all=dividend&action=delete_records";
        $downloadIcon   = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/downloading.gif';
		$url            = admin_url().'admin-ajax.php';
		$loader         = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/loader.gif';
        $deleteIcon     = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/remove-icon.png';

		$html  = "";
    	$html .="<form name='dividendfrm' method='POST' enctype='multipart/form-data' action=''>";
    	$html .= 	"<div class='uploadBtn'>";
        $html .=        "<div style='float:right'>";
    	$html .= 		    "<input type='file' data-multiple-caption='{count} files selected' name='dividendfile' accept='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' class='dividendfile inputfile inputfile-1' placeholder='Import CSV' id='dividendfile' required>";
    	$html .= 		    "<label for='file-1'><span>Import CSV</span></label>";
    	$html .= 		    "<input type='hidden' name='url' class='dividendurl' value='$url'>";
    	$html .= 		    "<input type='hidden' name='dividenduploader' class='dividenduploader' value='1'>";
    	$html .= 		    "<input type='submit' name='dividendBtn' class='dividendBtn' value='Import CSV'>";
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
    	$html .= "<div class='errors'><p>CSV file is not valid.</p></div>";
        $html .= "<div class='delMessage'><p>Dividend has been deleted.</p></div>";
    	echo $html;
        //Dividend::hideColumns();
        Dividend::dividendListing();

        if( isset($_GET['download_csv_dividend']) ){
            if($_GET['download_csv_dividend'] !== "" && $_GET['action'] == "downloaddividend"){
                $fileName = "dividend-sample.csv"; 
                Dividend::downloadDividendSample($fileName);
            }
        }

        if( isset($_GET['delete_all']) ){            
            if($_GET['delete_all'] == "dividend" && $_GET['action'] == "delete_records"){
                Dividend::deleteAllRecords();
            }
        }
    }

    public static function deleteAllRecords(){
        global $wpdb;
        $dividend = $wpdb->prefix . 'dividend';
        $wpdb->query("TRUNCATE TABLE ".$dividend);
        $dividendpage = admin_url('admin.php?page=dividend');
        wp_redirect($dividendpage);
        exit();
    }

    public static function downloadDividendSample($fileName){
        
        $location = ABSPATH.'wp-content/plugins/TrackYourInvestments/includes/uploads/'.$fileName;
        
        if ( ! file_exists($location)){
            echo 'file missing';
        }else{
            $files = AjaxDividendCSVUpload::readXLS( $location );

            ob_end_clean();
            ob_start();
            echo "siblings;key;company_name;record_date;ex_date;annc_type;datepaid_payable;currency;value;tax;interest_start;interest_end;interest_rate;remarks;base;giving;price;before_consolidation;after_consolidation;particulars\n";
            //file_put_contents(dirname(__FILE__).'/file.log', print_r($files,true),FILE_APPEND);
            foreach ($files as $result) {
                $siblings               = $result['siblings'];      
                $key                    = $result['key'];      
                $company_name           = $result['company_name'];     
                $record_date            = $result['record_date'];            
                $ex_date                = $result['ex_date'];         
                $annc_type              = $result['annc_type'];          
                $datepaid_payable       = $result['datepaid_payable'];              
                $currency               = $result['currency'];          
                $value                  = $result['value'];              
                $tax                    = $result['tax'];                 
                $interest_start         = $result['interest_start'];            
                $interest_end           = $result['interest_end'];          
                $interest_rate          = $result['interest_rate'];             
                $remarks                = $result['remarks'];               
                $base                   = $result['base'];                  
                $giving                 = $result['giving'];                    
                $price                  = $result['price'];                     
                $before_consolidation   = $result['before_consolidation'];  
                $after_consolidation    = $result['after_consolidation'];       
                $particulars            = $result['particulars'];              

                echo $siblings.";".$key.";".$company_name.";".$record_date.";".$ex_date.";".$annc_type.";".$datepaid_payable.";".$currency.";".$value.";".$tax.";".$interest_start.";".$interest_end.";".$interest_rate.";".$remarks.";".$base.";".$giving.";".$price.";".$before_consolidation.";".$after_consolidation.";".$particulars."\n";
            }
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename='.$fileName);
            exit;
        }
    }

    public static function dividendListing(){

        add_action( 'admin_head',array('Dividend','admin_header'));
            
        //add_filter( 'set-screen-option', 'set_screen', 10, 3 );
            
        Dividend::newDividendList();

    }

    public static function set_screen( $status, $option, $value ) {
        if ( 'dividend_per_page' == $option ) 
            return $value;
        return $status;
        //return $value;
    }

    public static function add_options() {
        global $dividendlistobj;
        $option = 'per_page';
        $args = array(
            'label' => 'Number of Dividend showing',
            'default' => 10,
             'option' => 'dividend_per_page'
        );
        add_screen_option( $option, $args );
        $dividendlistobj = new DividendList();
    }

    public static function admin_header() {
        $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
        if( 'dividend' != $page ){
            return;
        }
       /* $getColumns  =  get_option('divident_columns');
        echo '<style type="text/css">';
        echo '.wp-list-table .column-cb { width: 5%; }';
        foreach ($getColumns as $colname) {
            echo '.wp-list-table .column-$colname { display:none; }';    
        }
        echo '</style>';*/
    }

    //** New Function for the listing of the Stocks. **//
    public static function newDividendList(){

        global $dividendlistobj;

        ?>
        <div class="wrap">
            <h2 class="wp-heading-inline">Dividend List</h2>
            <a href="<?php echo admin_url('admin.php?page=add_new_dividend') ?>" class="page-title-action">Add New</a>
            <hr/>
            <div id="poststuff">
                <div id="post-body" class="">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <?php
                                $dividendlistobj = new DividendList();
                                
                                $dividendlistobj->prepare_items();
                                
                                echo '<input type="hidden" name="page" value="'.$_REQUEST['page'] .'">';
                                $dividendlistobj->search_box( 'search', 'search_id' );
                                
                                 if( isset( $_POST['s'] ) ){
                                    $new_url = admin_url().'admin.php?s='.$_POST['s'].'&page='.$_POST['page'];

                                    header('location:'.$new_url);
                                }
                                
                                ?>

                            </form>
                                <?php $dividendlistobj->display(); ?>

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
