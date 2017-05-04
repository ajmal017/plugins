<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AjaxStockCSVUpload {
    //** Constructor **//
    public static function stockUploadCSV(){
           
        if( isset($_POST['action']) ) {
            $return = self::processUpload( $_POST, $_FILES );
            if ($return == "filenotvalid") {
                echo json_encode(array('stocksuccess'=>$return,'records'=>''));
            }else{
                echo json_encode(array('stocksuccess'=>'stocksuccess','records'=>$return));
            }
            die();
        }
    }

    public static function processUpload( $postArr, $filesArr ) {
        $files = array();    
        $uploadPath = dirname(__FILE__)."/uploads";
        
        $filename = $filesArr['uploadStockCSV']['name'];
        
        $tmp_name = $filesArr['uploadStockCSV']["tmp_name"];
        $name     = $filesArr['uploadStockCSV']["name"];
        $ext      = pathinfo($name, PATHINFO_EXTENSION);

        move_uploaded_file($tmp_name, "$uploadPath/$name");

        $Filepath = $uploadPath."/".$name;

        $files = self::readXLS( $Filepath );
        if ($files == "filenotvalid") {
            return "filenotvalid";
        }else{
            $return = self::insertStockCSV($files);
            return $return;
        }
    }

    public static function readXLS( $Filepath ) {

        $db_columns = array(
                            'stock_name', 
                            'ticker',
                            'exchange_name',
                            'currency'
                            );

        $isValid = TrackInvestment::validateCSV( $db_columns, $Filepath );
        file_put_contents(dirname(__FILE__).'/isValid.log', print_r($isValid,true),FILE_APPEND);
        if(!$isValid){
            return 'filenotvalid';
        }else{
            require('spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
            require('spreadsheet-reader-master/SpreadsheetReader.php');

            date_default_timezone_set('UTC');
            $StartMem = memory_get_usage();

            try{
                
                $files = array();
                $Spreadsheet = new SpreadsheetReader($Filepath);
                $BaseMem     = memory_get_usage();
                $Sheets      = $Spreadsheet -> Sheets();

                foreach ($Sheets as $Index => $Name){
                    $Time      = microtime(true);
                    $header    = true;
                    $headerRow = array();
                    $Spreadsheet -> ChangeSheet($Index);              

                    foreach ( $Spreadsheet as $Key => $Row ) {
                        
                        /*if( $Row[0] == "" ) {
                                continue;
                        }*/
                        if( $header ){
                            $header = false;
                            $headerRow = $Row;
                            continue;
                        }

                        $tempData   = array_combine ( $headerRow, $Row );
                        $files[]    = $tempData;
                    }
                }
            } catch (Exception $E) {
                echo $E -> getMessage();
            }      
        return $files;
        }
    }

    public static function getRecords($files){
        global $wpdb;
        $dups = array();
        $orig = array();
        $total = array();
        
        foreach ($files as $value) {
            $stockName      = $value['stock_name'];
            $ticker         = $value['ticker'];
            $exchangeName   = $value['exchange_name'];
            $currency       = $value['currency'];

            $results = $wpdb->get_results( 'SELECT * FROM wp_stocks WHERE `stock_name` = "'.$stockName.'" AND `ticker` = "'.$ticker.'" AND `exchange_name` = "'.$exchangeName.'" AND `currency` = "'.$currency.'"' ,ARRAY_A);
            if (!empty($results)) {
                $dups[] = $results;
            }else{
                $orig[] = $value;
            }
        }
        $total['duplicate'] = $dups;
        $total['original']  = $orig;
        return $total;
    }

    public static function insertStockCSV( $files ) {
        global $wpdb;
        
        $records    = self::getRecords($files);

        $realRecord = $records['original'];
        $duplRecord = $records['duplicate'];

        $stock_table = $wpdb->prefix . 'stocks';

        //$wpdb->query("alter table $stock_table convert to character set utf8 collate utf8_general_ci");
        
        foreach ( $realRecord as $k => $file) {
            if(empty($file)) { continue; }
            $wpdb->insert( 
                $stock_table, 
                array( 
                    'stock_name'    => mb_convert_encoding($file['stock_name'], 'HTML-ENTITIES', "UTF-8"),
                    'ticker'        => $file['ticker'],
                    'exchange_name' => $file['exchange_name'],
                    'currency'      => $file['currency'],
                ), 
                array( 
                    '%s',
                    '%s',
                    '%s',
                    '%s' 
                ) 
            );
        }
        return $duplRecord;
    }
}/*class ends here*/
?>