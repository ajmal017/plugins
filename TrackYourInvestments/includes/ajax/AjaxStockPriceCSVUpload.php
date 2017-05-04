<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AjaxStockPriceCSVUpload {
    //** Constructor **//
    public static function stockPriceUploadCSV(){
           
        if( isset($_POST['action']) ) {
            
            $return = self::processUpload( $_POST, $_FILES );
            if ($return == "filenotvalid") {
                echo json_encode(array('stockpricesuccess'=>$return,'records'=>''));
            }else{
                echo json_encode(array('stockpricesuccess'=>'stockpricesuccess','records'=>$return));
            }
            die();
        }
    }

    public static function processUpload( $postArr, $filesArr ) {
        $files      = array();    
        $uploadPath = dirname(__FILE__)."/uploads";
        $tmp_name   = $filesArr['uploadStockPriceCSV']["tmp_name"];
        $name       = $filesArr['uploadStockPriceCSV']["name"];
        $ext        = pathinfo($name, PATHINFO_EXTENSION);

        move_uploaded_file($tmp_name, "$uploadPath/$name");

        $Filepath   = $uploadPath."/".$name;

        $files      = self::readXLS( $Filepath );
        if ($files == "filenotvalid") {
            return "filenotvalid";
        }else{
            $return = self::insertStockPriceCSV($files);
            return $return;
        }
    }

    public static function readXLS( $Filepath ) {
        $db_columns = array(
                            'ticker', 
                            'date',
                            'open',
                            'high',
                            'low',
                            'close',
                            'volume',
                            'adj_close',
                            );

        $isValid = TrackInvestment::validateCSV( $db_columns, $Filepath );
        
        if(!$isValid){
            return 'filenotvalid';
        }else{
            require('spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
            require('spreadsheet-reader-master/SpreadsheetReader.php');

            date_default_timezone_set('UTC');
            $StartMem = memory_get_usage();

            try{
                
                $files          = array();
                $Spreadsheet    = new SpreadsheetReader($Filepath);
                $BaseMem        = memory_get_usage();
                $Sheets         = $Spreadsheet -> Sheets();

                foreach ($Sheets as $Index => $Name){
                    $Time           = microtime(true);
                    $header         = true;
                    $headerRow      = array();
                    $Spreadsheet -> ChangeSheet($Index);              

                    foreach ( $Spreadsheet as $Key => $Row ) {
                        
                        if( $header ){
                            $header     = false;
                            $headerRow  = $Row;
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

    public static function insertStockPriceCSV( $files ) {
        global $wpdb;
        foreach ( $files as $k => $file) {
            if(empty($file)) {
                continue;
            }
            
            $originalDate   = $file['date'];
            $newDate        = date("Y-m-d", strtotime($originalDate));
            
            $wpdb->insert( 
                'wp_stock_price', 
                array( 
                   
                    'ticker'    => $file['ticker'],
                    'date'      => $newDate,
                    'open'      => $file['open'],
                    'high'      => $file['high'],
                    'low'       => $file['low'],
                    'close'     => $file['close'],
                    'volume'    => $file['volume'],
                    'adj_close' => $file['adj_close'],
                    
                ), 
                array( 
                    // /'%d',
                    '%s',
                    '%s', 
                    '%f', 
                    '%f', 
                    '%f', 
                    '%f', 
                    '%f', 
                    '%f'
                ) 
            );
        }
    }
}/*class ends here*/
?>