<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'StockList' ) ) {
    include_once('StockList.php');
}
if ( ! class_exists( 'AjaxDeleteRecord' ) ) {
    include_once('AjaxDeleteRecord.php');
}

class Setting {
   
    public static function settingDividend(){
    	
        global $wpdb;
    	$url        = admin_url().'admin-ajax.php';
        $loader     = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/loader.gif';
        
        $results    = $wpdb->get_results( "SHOW COLUMNS FROM wp_dividend" );
        $getColumns =  get_option('divident_columns');

        echo "<div class='setting-page'><h1>Setting</h1></div>";
        echo "<div class='dividend-setting'><h3>Mark dividend columns to show.</h3>";
        echo "<input type='hidden' value='$url' class='dividend-hidden'>";
        
        foreach ($results as $column) {
            $column_name  = $column->Field;
            $explodename  = explode('_', $column_name);
            $showname     = ucwords($explodename[0]." ".$explodename[1]." ".$explodename[2]);

            if (!empty($getColumns)) {
                if (in_array($column_name, $getColumns)) {
                    echo "<input type='checkbox' name='dividendchecklist[]' checked class='dividendchecklist' value='".$column_name."'>"."  ".$showname."<br>";    
                }else{
                    echo "<input type='checkbox' name='dividendchecklist[]' class='dividendchecklist' value='".$column_name."'>"."  ".$showname."<br>";
                }
            }
        }
        
        echo "<button type='button' name='dividendChecklistBtn' class='dividendChecklistBtn' value='save'>Save</button>";
        echo "</div>";
        echo "<div class='success'><p>Dividend fields has been saved.</p></div>";
        echo "<div class='loader'><img src='$loader' height='100' width='100'></div>";
        
    }

    
}/*class ends here*/
?>