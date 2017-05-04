<?php
/**
* Exchange class
*/
class AjaxExchangeCSVUpload{
	
	public static function exchangeUploadCSV(){
           
        if( isset($_POST['action']) ) {
            
            $return = self::processUpload( $_POST, $_FILES );
            if ($return == "filenotvalid") {
                echo json_encode(array('exchangesuccess'=>$return,'records'=>''));
            }else{
                echo json_encode(array('exchangesuccess'=>'exchangesuccess','records'=>$return));
            }
            die();
        }
    }

    public static function processUpload( $postArr, $filesArr ) {
        $files = array();    
        $uploadPath = dirname(__FILE__)."/uploads";
        $tmp_name = $filesArr['uploadExchangeCSV']["tmp_name"];
        $name     = $filesArr['uploadExchangeCSV']["name"];
        $ext      = pathinfo($name, PATHINFO_EXTENSION);
        
        move_uploaded_file($tmp_name, "$uploadPath/$name");

        $Filepath   = $uploadPath."/".$name;
        $files      = self::readXLS( $Filepath );
        if ($files == "filenotvalid") {
            return "filenotvalid";
        }else{
            $return = self::insertExchangeCSV($files);
            return $return;
        }
    }

    public static function readXLS( $Filepath ) {
        
        $db_columns = array(
                            'date', 
                            'base_currency',
                            'conversion_currency',
                            'value'
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
        $dups   = array();
        $orig   = array();
        $total  = array();
        
        foreach ($files as $val) {
            
            $originalDate       = $val['date'];
            $newDate            = date("Y-m-d", strtotime($originalDate));
            $date               = $newDate;
            $base_currency      = $val['base_currency'];
            $conversion_currency= $val['conversion_currency'];
            $value              = $val['value'];

            $results = $wpdb->get_results( 'SELECT * FROM wp_currency_rate WHERE `date` = "'.$date.'" AND `base_currency` = "'.$base_currency.'" AND `conversion_currency` = "'.$conversion_currency.'"' ,ARRAY_A);
            
            if (!empty($results)) {
                $dups[] = $results;
            }else{
                $orig[] = $val;
            }
        }
        
        $total['duplicate'] = $dups;
        $total['original']  = $orig;
        return $total;
    }

    public static function insertExchangeCSV( $files ) {
        global $wpdb;

        $records    = self::getRecords($files);

        $realRecord = $records['original'];
        $duplRecord = $records['duplicate'];

        foreach ( $realRecord as $k => $file) {
            if(empty($file)) {
                continue;
            }
            
            $originalDate = $file['date'];
            $newDate = date("Y-m-d", strtotime($originalDate));
            
            $wpdb->insert( 
                'wp_currency_rate', 
                array( 
                    'date'      		    => $newDate, 
                    'base_currency'    		=> $file['base_currency'],
                    'conversion_currency'   => $file['conversion_currency'],
                    'value' 		        => $file['value'] ,
                ), 
                array( 
                     '%s', '%s', '%s', '%f' 
                ) 
            );
        }
        return $duplRecord;
    }
}
/* class ends here */