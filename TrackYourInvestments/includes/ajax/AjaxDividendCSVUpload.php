<?php
/**
* Dividend class
*/
class AjaxDividendCSVUpload{
	
	public static function dividendUploadCSV(){
           
        if( isset($_POST['action']) ) {
            
            $return = self::processUpload( $_POST, $_FILES );
            if ($return == "filenotvalid") {
                echo json_encode(array('dividendsuccess'=>$return,'records'=>''));
            }else{
                echo json_encode(array('dividendsuccess'=>'dividendsuccess','records'=>$return));
            }
            die();
        }
    }

    public static function processUpload( $postArr, $filesArr ) {
        $files      = array();    
        $uploadPath = dirname(__FILE__)."/uploads";
        $tmp_name   = $filesArr['uploadDividendCSV']["tmp_name"];
        $name       = $filesArr['uploadDividendCSV']["name"];
        $ext        = pathinfo($name, PATHINFO_EXTENSION);
        
        move_uploaded_file($tmp_name, "$uploadPath/$name");

        $Filepath   = $uploadPath."/".$name;
        $files      = self::readXLS( $Filepath );
        if ($files == "filenotvalid") {
            return "filenotvalid";
        }else{
            $return = self::insertDividendCSV($files);
            return $return;
        }
    }

    public static function readXLS( $Filepath ) {
        
        $db_columns = array(
                            'siblings', 
                            'key',
                            'company_name',
                            'record_date',
                            'ex_date',
                            'annc_type',
                            'datepaid_payable',
                            'currency',
                            'value',
                            'tax',
                            'interest_start',
                            'interest_end',
                            'interest_rate',
                            'remarks',
                            'base',
                            'giving',
                            'price',
                            'before_consolidation',
                            'after_consolidation',
                            'particulars'
                            );

        $isValid = TrackInvestment::validateCSV( $db_columns, $Filepath );

        if(!$isValid){
            return 'filenotvalid';
        }else{
            
            require('spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
            require('spreadsheet-reader-master/SpreadsheetReader.php');

            date_default_timezone_set('UTC');
            $StartMem   = memory_get_usage();

            try{
                
                $files       = array();
                $Spreadsheet = new SpreadsheetReader($Filepath);
                $BaseMem     = memory_get_usage();
                $Sheets      = $Spreadsheet -> Sheets();
                
                foreach ($Sheets as $Index => $Name){
                    
                    $Time      = microtime(true);
                    $header    = true;
                    $headerRow = array();
                    $Spreadsheet -> ChangeSheet($Index);              
                    
                    foreach ( $Spreadsheet as $Key => $Row ) {
                        $Row = array_map('trim',$Row);
                        
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

    public static function getRecords($files){
        global $wpdb;
        $dups   = array();
        $orig   = array();
        $total  = array();
        
        foreach ($files as $key => $value) {

            $rDate                  = $value['record_date'];
            $RecordDate             = date("Y-m-d", strtotime($rDate));

            $eDate                  = $value['ex_date'];
            $ExDate                 = date("Y-m-d", strtotime($eDate));

            $DPDate                 = $value['datepaid_payable'];
            $DatePDate              = date("Y-m-d", strtotime($DPDate));
            
            $siblings                = $value['siblings'];
            $key                    = $value['key'];
            $company_name           = $value['company_name'];
            $record_date            = $RecordDate;
            $ex_date                = $ExDate;
            $annc_type              = $value['annc_type'];
            $datepaid_payable       = $DatePDate;
            $currency               = $value['currency'];
            $value2                 = $value['value'];
            $tax                    = $value['tax'];
            $interest_start         = $value['interest_start'];
            $interest_end           = $value['interest_end'];
            $interest_rate          = $value['interest_rate'];
            $remarks                = $value['remarks'];
            $base                   = $value['base'];
            $giving                 = $value['giving'];
            $price                  = $value['price'];
            $before_consolidation   = $value['before_consolidation'];
            $after_consolidation    = $value['after_consolidation'];
            $particulars            = $value['particulars'];

            $results = $wpdb->get_results( 'SELECT * FROM wp_dividend WHERE `company_name` = "'.$company_name.'" AND `ex_date` = "'.$ex_date.'" AND `annc_type` = "'.$annc_type.'" AND `interest_start` = "'.$interest_start.'" AND `interest_end` = "'.$interest_end.'"' ,ARRAY_A);
            
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

    public static function insertDividendCSV( $files ) {
        global $wpdb;

        $records    = self::getRecords($files);

        $realRecord = $records['original'];
        $duplRecord = $records['duplicate'];
        
        foreach ( $realRecord as $k => $file) {
            if(empty($file)) {
                continue;
            }

            $rDate                  = $file['record_date'];
            $RecordDate             = date("Y-m-d", strtotime($rDate));

            $eDate                  = $file['ex_date'];
            $ExDate                 = date("Y-m-d", strtotime($eDate));

            $DPDate                 = $file['datepaid_payable'];
            $DatePDate              = date("Y-m-d", strtotime($DPDate));

            $wpdb->insert( 
                'wp_dividend', 
                array( 
                    'siblings'    			=> mb_convert_encoding($file['siblings'], 'HTML-ENTITIES', "UTF-8"),
                    'key' 			        => mb_convert_encoding($file['key'], 'HTML-ENTITIES', "UTF-8"),
                    'company_name' 			=> mb_convert_encoding($file['company_name'], 'HTML-ENTITIES', "UTF-8"),
                    'record_date'           => mb_convert_encoding($RecordDate, 'HTML-ENTITIES', "UTF-8"),
                    'ex_date'               => mb_convert_encoding($ExDate, 'HTML-ENTITIES', "UTF-8"),
                    'annc_type' 			=> mb_convert_encoding($file['annc_type'] , 'HTML-ENTITIES', "UTF-8"),
                    'datepaid_payable'      => mb_convert_encoding($DatePDate, 'HTML-ENTITIES', "UTF-8"),
                    'currency'              => mb_convert_encoding($file['currency'], 'HTML-ENTITIES', "UTF-8"),
                    'value'                 => mb_convert_encoding($file['value'], 'HTML-ENTITIES', "UTF-8"),
                    'tax'                   => mb_convert_encoding($file['tax'], 'HTML-ENTITIES', "UTF-8"),
                    'interest_start' 		=> mb_convert_encoding($file['interest_start'], 'HTML-ENTITIES', "UTF-8"),
                    'interest_end' 			=> mb_convert_encoding($file['interest_end'], 'HTML-ENTITIES', "UTF-8"),
                    'interest_rate' 		=> mb_convert_encoding($file['interest_rate'], 'HTML-ENTITIES', "UTF-8"),
                    'remarks' 				=> mb_convert_encoding($file['remarks'], 'HTML-ENTITIES', "UTF-8"),
                    'base' 					=> mb_convert_encoding($file['base'], 'HTML-ENTITIES', "UTF-8"),
                    'giving' 				=> mb_convert_encoding($file['giving'], 'HTML-ENTITIES', "UTF-8"),
                    'price' 				=> mb_convert_encoding($file['price'], 'HTML-ENTITIES', "UTF-8"),
                    'before_consolidation' 	=> mb_convert_encoding($file['before_consolidation'], 'HTML-ENTITIES', "UTF-8"),
                    'after_consolidation' 	=> mb_convert_encoding($file['after_consolidation'], 'HTML-ENTITIES', "UTF-8"),
                    'particulars' 			=> mb_convert_encoding($file['particulars'], 'HTML-ENTITIES', "UTF-8"),
                ), 
                array( 
                    '%d','%d', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'
                ) 
            );
        }
        return $duplRecord;
        
    }

}/* class ends here */