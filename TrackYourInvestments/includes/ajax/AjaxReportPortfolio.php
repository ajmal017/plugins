<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AjaxReportPortfolio {
    //** Constructor **//
    public static function getPortfolio(){
           
        if( isset($_POST['action']) ) {
            $result = self::getReportingRecords( $_POST );
            echo json_encode($result);
            die();
        }
    }

    public static function getReportingRecords( $details ) {
        
        $startDate              = $details['startDate'];
                
        $newStartDate           = date("Y-m-d", strtotime($startDate));
        
        $userId                 = $details['userId'];

        $investRecords          = self::getInvestRecord($newStartDate, $userId);

        return $investRecords;
    }

    /* get getInvestRecord table */
    public static function getInvestRecord($newStartDate, $userId){
        
        global $wpdb;
        
        $user_id = get_current_user_id();

        $investment_transations = $wpdb->prefix . 'investment_transations';

        /* account wise */
        $account_table = $wpdb->prefix . 'cj_user_portfolios';

        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';

        $sql_accounts = "SELECT `id`, `account_name`, `recording_currency` FROM $account_table WHERE `user_id`= $user_id";

        $accounts = $wpdb->get_results($sql_accounts);
        
        $arr_port_values = array();

        $data = array();

        $report_data = array();

        if(!empty($accounts)){

            foreach ( $accounts as $account ) {

                $account_id = $account->id;

                $sql_records = "SELECT t.*, SUM(num_of_shares) as total_shares FROM $investment_transations as t INNER JOIN $cj_user_portfolios as a ON t.account_id = a.id
                WHERE t.user_id = $user_id AND a.id = $account_id GROUP BY t.ticker_symbol, t.transaction_type";

                $records = $wpdb->get_results( $sql_records );

                $arr_records = array();

                foreach ( $records as $record ) {

                    if( array_key_exists($record->ticker_symbol, $arr_records) ){

                        $tmp_array = $arr_records[$record->ticker_symbol];

                        if( $tmp_array['transaction_type'] == 1 ){
                            $total_shares = $tmp_array['total_shares'] - $record->total_shares;
                        }else{
                            $total_shares = $record->total_shares - $tmp_array['total_shares'];
                        }

                        $tmp_new_array = array(
                                "transaction_type" => $tmp_array['transaction_type'],
                                "ticker_symbol" => $tmp_array['ticker_symbol'],
                                "total_shares" => $total_shares,
                                "account_name" => $tmp_array['account_name'],
                                "account_id" => $account_id,
                            );

                        $arr_records[$record->ticker_symbol] = $tmp_new_array;

                    }else{

                        $arr_records[$record->ticker_symbol] = array(
                            "transaction_type" => $record->transaction_type,
                            "ticker_symbol" => $record->ticker_symbol,
                            "total_shares" => $record->total_shares,
                            "account_name" => $account->account_name,
                            "account_id" => $account_id,
                        );
                    }
                }

                $arr_port_values[] = $arr_records; 

            }

            $grand_total_shares = 0;
            $grand_total_price = 0;
            $grand_total_value = 0;

            foreach ($arr_port_values as $rec ) {

                if($rec){                    

                    foreach ($rec as $sub_rec) {

                        $account_reporting_currency = self::getAccountReportingCurrency($sub_rec['account_id']);

                        $stock_currency = self::getStockCurrency($sub_rec['ticker_symbol']);

                        $grand_total_shares += $sub_rec['total_shares'];

                        $price = self::getStocksPrice($newStartDate, $sub_rec['ticker_symbol']);

                        $price = number_format($price, 2);

                        $price = self::currencyConverter( $stock_currency, $account_reporting_currency, $price);

                        $grand_total_price += $price;

                        $total_values = number_format( $price * $sub_rec['total_shares'], 2);

                        $grand_total_value += $total_values;

                        $compnay_name = self::getCompanyName($sub_rec['ticker_symbol']);

                        $ticker_price = array('price' => $account_reporting_currency.$price, 'total_values' => $account_reporting_currency.$total_values, 'company_name' => $compnay_name);

                        $data[] =  array_merge($sub_rec, $ticker_price );
                    }

                    $data_total = array(
                                    "grand_total_shares" => $grand_total_shares,
                                    "grand_total_price" => $grand_total_price,
                                    "grand_total_value" => $grand_total_value
                                );
                }                
            }

            $report_data['items'] = $data;
            $report_data['total'] = $data_total;
        }
        return $report_data;
    }

    public static function getSingleInvestRecord($newStartDate, $userId, $ids){
        
        global $wpdb;
        
        $user_id = get_current_user_id();

        $investment_transations = $wpdb->prefix . 'investment_transations';

        /* account wise */
        $account_table = $wpdb->prefix . 'cj_user_portfolios';

        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
        $report_data = array();
        $data = array();
        $accounts = array();
        foreach ($ids as $id) {
                                    
            $sql_accounts = "SELECT `id`,`account_name`,`recording_currency` FROM $account_table WHERE `user_id`= $user_id AND `id`=$id";
            
            $accounts[] = $wpdb->get_results($sql_accounts);
        }    
        
        $arr_port_values = array();
            
        if(!empty($accounts)){
            foreach ( $accounts as $account ) {

                $account_id = $account[0]->id;
                    
                $sql_records = "SELECT t.*, SUM(num_of_shares) as total_shares FROM $investment_transations as t INNER JOIN $cj_user_portfolios as a ON t.account_id = a.id
                WHERE t.user_id = $user_id AND a.id = $account_id GROUP BY t.ticker_symbol, t.transaction_type";

                $records = $wpdb->get_results( $sql_records );

                $arr_records = array();
                
                foreach ( $records as $record ) {

                    if( array_key_exists($record->ticker_symbol, $arr_records) ){

                        $tmp_array = $arr_records[$record->ticker_symbol];

                        if( $tmp_array['transaction_type'] == 1 ){
                            $total_shares = $tmp_array['total_shares'] - $record->total_shares;
                        }else{
                            $total_shares = $record->total_shares - $tmp_array['total_shares'];
                        }

                        $tmp_new_array = array(
                                "transaction_type" => $tmp_array['transaction_type'],
                                "ticker_symbol" => $tmp_array['ticker_symbol'],
                                "total_shares" => $total_shares,
                                "account_name" => $tmp_array['account_name'],
                                "account_id" => $account_id,
                            );

                        $arr_records[$record->ticker_symbol] = $tmp_new_array;

                    }else{

                        $arr_records[$record->ticker_symbol] = array(
                            "transaction_type" => $record->transaction_type,
                            "ticker_symbol" => $record->ticker_symbol,
                            "total_shares" => $record->total_shares,
                            "account_name" => $account[0]->account_name,
                            "account_id" => $account_id,
                        );
                    }
                }

                $arr_port_values[] = $arr_records; 
        
            }

            $grand_total_shares = 0;
            $grand_total_price = 0;
            $grand_total_value = 0;

            foreach ($arr_port_values as $rec ) {

                if($rec){                    

                    foreach ($rec as $sub_rec) {

                        $account_reporting_currency = self::getAccountReportingCurrency($sub_rec['account_id']);

                        $stock_currency = self::getStockCurrency($sub_rec['ticker_symbol']);

                        $grand_total_shares += $sub_rec['total_shares'];

                        $price = self::getStocksPrice($newStartDate, $sub_rec['ticker_symbol']);

                        $price = number_format($price, 2);

                        $price = self::currencyConverter( $stock_currency, $account_reporting_currency, $price);

                        $grand_total_price += $price;

                        $total_values = number_format( $price * $sub_rec['total_shares'], 2);

                        $grand_total_value += $total_values;

                        $compnay_name = self::getCompanyName($sub_rec['ticker_symbol']);

                        $ticker_price = array('price' => $account_reporting_currency.$price, 'total_values' => $account_reporting_currency.$total_values, 'company_name' => $compnay_name);

                        $data[] =  array_merge($sub_rec, $ticker_price );
                    }

                    $data_total = array(
                                    "grand_total_shares" => $grand_total_shares,
                                    "grand_total_price" => $grand_total_price,
                                    "grand_total_value" => $grand_total_value
                                );
                }                
            }

            $report_data['items'] = $data;
            $report_data['total'] = $data_total;
        }
            
        return $report_data;
    }

    public static function currencyConverter($from_Currency,$to_Currency,$amount) {

        if($from_Currency == $to_Currency){
            return $amount;
        }

        $from_Currency = urlencode($from_Currency);
        $to_Currency = urlencode($to_Currency);
        $encode_amount = $amount;
        $get = file_get_contents("https://www.google.com/finance/converter?a=$encode_amount&from=$from_Currency&to=$to_Currency");
        $get = explode("<span class=bld>",$get);
        $get = explode("</span>",$get[1]);
        $converted_currency = preg_replace("/[^0-9\.]/", null, $get[0]);

        $converted_currency = number_format($converted_currency,2);

        return $converted_currency;
    }

    // get account reporting currency
    public static function getAccountReportingCurrency($account_id){

        global $wpdb;

        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
        
        $sql = "SELECT `recording_currency` FROM $cj_user_portfolios WHERE id ='".$account_id."'";
            
        $result = $wpdb->get_results($sql);

        if(!empty($result) ){
            return $result[0]->recording_currency;
        }else{
            return false;
        }
    }

    public static function getCompanyName($ticker){

        global $wpdb;
        $stocks = $wpdb->prefix . 'stocks';
        
        $sql = "SELECT stock_name FROM $stocks WHERE ticker ='".$ticker."'";
            
        $result = $wpdb->get_results($sql);

        if(!empty($result) ){
            return $result[0]->stock_name;
        }else{
            return "NA";
        }
    }

    /*get stock currency*/
    public static function getStockCurrency($ticker){

        global $wpdb;
        $stocks = $wpdb->prefix . 'stocks';
        
        $sql = "SELECT `currency` FROM $stocks WHERE ticker ='".$ticker."'";
            
        $result = $wpdb->get_results($sql);

        if(!empty($result) ){
            return $result[0]->currency;
        }else{
            return "NA";
        }
    }

    /* get stocke price by selected date */
    public static function getStocksPrice($date, $ticker){
        global $wpdb;
        $stock_price    = $wpdb->prefix . 'stock_price';
        $sql            = "SELECT * FROM $stock_price WHERE `date` <= '".$date."' AND ticker='".$ticker."' ORDER BY `date` DESC LIMIT 0,1";
        $result         = $wpdb->get_results($sql); 
        
        return $result[0]->open;
    }


    public static function getAccountName($account_id){
        
        global $wpdb;

        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
        
        $sql = "SELECT `account_name` FROM $cj_user_portfolios WHERE id ='".$account_id."'";
            
        $result = $wpdb->get_results($sql);

        if(!empty($result) ){
            return $result[0]->account_name;
        }else{
            return "NA";
        }
    }

    public static function getChartPortfolio(){

        global $wpdb;

        $user_id = get_current_user_id();
        
        if( isset($_POST['action']) ) {

            $reporting_date_start = date('Y-m-d', strtotime($_POST['reporting_date_start']) );
            $reporting_date_end = date('Y-m-d', strtotime($_POST['reporting_date_end']) );
            $time_date = $_POST['time_date'];

            $tickers_data = self::getInvestRecord( date('Y-m-d'), $user_id);

            $time_diff = self::getTimeDiff($reporting_date_start, $reporting_date_end, $time_date);

            $dates = self::createDateRangeArray($reporting_date_start, $reporting_date_end, $time_diff);

            $chart_data = array();

            $chart_data_formatted = array();

            if( !empty($dates) ){

                foreach ( $dates as $date) {

                    $tmp = array();

                    foreach ( $tickers_data['items'] as $ticker) {

                        $account_reporting_currency = self::getAccountReportingCurrency($ticker['account_id']);

                        $stock_currency = self::getStockCurrency($ticker['ticker_symbol']);

                        $price = self::getStocksPrice($date, $ticker['ticker_symbol']);

                        $price = number_format($price, 2);

                        $price = self::currencyConverter( $stock_currency, $account_reporting_currency, $price);

                        $price = number_format($price, 2);                        

                        $total_value = $price*$ticker['total_shares'];

                        $total_value = number_format($total_value,2);

                        if( array_key_exists($ticker['account_id'], $tmp) ){

                            $tmp_array = $tmp[$ticker['account_id']];

                            $total_value =  $total_value + $tmp_array['values'];

                            $total_value = number_format($total_value,2);

                            //$total_shares =  $ticker['total_shares'] + $tmp_array['total_shares'];

                            $tmp[$ticker['account_id']] = array(
                                    "account_id" => $ticker['account_id'],
                                    "values" => $total_value,
                                    "date" => $date,
                                    "currency" => $account_reporting_currency
                                    //"total_shares" => $total_shares,
                                    //"price" => $price
                                );  
                        }else{

                            $tmp[$ticker['account_id']] = array(
                                    "account_id" => $ticker['account_id'],
                                    "values" => $total_value,
                                    "date" => $date,
                                    "currency" => $account_reporting_currency
                                    //"total_shares" => $ticker['total_shares'],
                                    //"price" => $price
                                );                            
                        }                        
                    }

                    $chart_data[] = $tmp ;
                }


                // format array as per google chart 
                if( !empty( $chart_data ) ){

                    foreach ($chart_data as $aa) {
                        
                        $tmp = array();

                        foreach ($aa as $key => $ac) {
                            
                            $tmp['date'] = $ac['date'];                                
                            $tmp[$ac['account_id']] = $ac['values'];                                
                        }

                        $chart_data_formatted[] = $tmp;
                    }
                }

                $user_accounts = array();

                foreach ($chart_data_formatted[0] as $key => $value) {
                    
                    if($key !== 'date'){
                        $user_accounts[] =  array("name" => self::getAccountName($key) ."(". self::getAccountReportingCurrency($key) .")" );
                    }
                }                
            }

            $results = array();
            $results["data"] = $chart_data_formatted;
            $results["accounts"] = $user_accounts;

            echo json_encode( $results );
            die();
        }
    }

    public static function getSingleChartPortfolio(){

        global $wpdb;

        $user_id = get_current_user_id();
        
        if( isset($_POST['action']) ) {

            $reporting_date_start = date('Y-m-d', strtotime($_POST['reporting_date_start']) );
            $reporting_date_end = date('Y-m-d', strtotime($_POST['reporting_date_end']) );
            $time_date = $_POST['time_date'];
            $accountList = $_POST['accountList'];
                
            $tickers_data = self::getSingleInvestRecord( date('Y-m-d'), $user_id, $accountList);
            
            $time_diff = self::getTimeDiff($reporting_date_start, $reporting_date_end, $time_date);
                
            $dates = self::createDateRangeArray($reporting_date_start, $reporting_date_end, $time_diff);
                
            $chart_data = array();

            $chart_data_formatted = array();

            if( !empty($dates) ){

                foreach ( $dates as $date) {

                    $tmp = array();

                    foreach ( $tickers_data['items'] as $ticker) {

                        $account_reporting_currency = self::getAccountReportingCurrency($ticker['account_id']);

                        $stock_currency = self::getStockCurrency($ticker['ticker_symbol']);

                        $price = self::getStocksPrice($date, $ticker['ticker_symbol']);

                        $price = number_format($price, 2);

                        $price = self::currencyConverter( $stock_currency, $account_reporting_currency, $price);

                        $price = number_format($price, 2);                        

                        $total_value = $price*$ticker['total_shares'];

                        $total_value = number_format($total_value,2);

                        if( array_key_exists($ticker['account_id'], $tmp) ){

                            $tmp_array = $tmp[$ticker['account_id']];

                            $total_value =  $total_value + $tmp_array['values'];

                            $total_value = number_format($total_value,2);

                            //$total_shares =  $ticker['total_shares'] + $tmp_array['total_shares'];

                            $tmp[$ticker['account_id']] = array(
                                    "account_id" => $ticker['account_id'],
                                    "values" => $total_value,
                                    "date" => $date,
                                    "currency" => $account_reporting_currency
                                    //"total_shares" => $total_shares,
                                    //"price" => $price
                                );  
                        }else{

                            $tmp[$ticker['account_id']] = array(
                                    "account_id" => $ticker['account_id'],
                                    "values" => $total_value,
                                    "date" => $date,
                                    "currency" => $account_reporting_currency
                                    //"total_shares" => $ticker['total_shares'],
                                    //"price" => $price
                                );                            
                        }                        
                    }

                    $chart_data[] = $tmp ;
                }


                // format array as per google chart 
                if( !empty( $chart_data ) ){

                    foreach ($chart_data as $aa) {
                        
                        $tmp = array();

                        foreach ($aa as $key => $ac) {
                            
                            $tmp['date'] = $ac['date'];                                
                            $tmp[$ac['account_id']] = $ac['values'];                                
                        }

                        $chart_data_formatted[] = $tmp;
                    }
                }

                $user_accounts = array();
                
                foreach ($chart_data_formatted[0] as $key => $value) {
                    
                    if($key !== 'date'){
                        $user_accounts[] =  array("name" => self::getAccountName($key) ."(". self::getAccountReportingCurrency($key) .")" );
                    }
                }                
            }

            $results = array();
            $results["data"] = $chart_data_formatted;
            $results["accounts"] = $user_accounts;

            echo json_encode( $results );
            die();
        }
    }

    public static function getTimeDiff($startDate, $endDate, $timeDiff){
        
        $datediff = strtotime($endDate) - strtotime($startDate);

        $number_of_days = floor($datediff / (60 * 60 * 24)) + 1;

        $timeDiff_tmp = $timeDiff;

        if($timeDiff==30){
            $timeDiff_tmp = 31;    
        }        

        if( $number_of_days > $timeDiff_tmp ){

            return $timeDiff;

        }elseif($number_of_days <= 7 ){
            return 1;
        }elseif($number_of_days <= 31  ){
            return 7;
        }elseif($number_of_days <= 90 ){
            return 30;
        }elseif($number_of_days <= 365 ){
            return 90;
        }else{
            return 7;
        }
    }


    // get all the accounts of a user

    public static function getAccountsData(){

        global $wpdb;

        $user_id = get_current_user_id();

        $investment_transations = $wpdb->prefix . 'investment_transations';

        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';        

        $sql = "SELECT i.ticker_symbol, p.account_name, SUM(i.`num_of_shares`) as `total_shares`
        FROM $investment_transations as i 
        INNER JOIN $cj_user_portfolios as p
        ON i.account_id = p.id
        WHERE p.user_id = $user_id
        GROUP BY account_id";

        $result = $wpdb->get_results($sql); 

        self::getStocksPrice($newStartDate, $sendTicker);

    }


    public static function createDateRangeArray($strDateFrom,$strDateTo, $diff)
    {
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.

        // could test validity of dates here but I'm already doing
        // that in the main script

        $aryRange=array();

        $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2), substr($strDateFrom,8,2),substr($strDateFrom,0,4));
        $iDateTo=mktime(1,0,0,substr($strDateTo,5,2), substr($strDateTo,8,2),substr($strDateTo,0,4));

        if ($iDateTo>=$iDateFrom)
        {

            $time_period = 86400*$diff;

            array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
            while ($iDateFrom<$iDateTo)
            {
                //$iDateFrom+=86400; // add 24 hours
                $iDateFrom+=$time_period; 

                $date = date('Y-m-d',$iDateFrom);

                if( strtotime($date) <= strtotime($strDateTo) ){
                    array_push($aryRange,date('Y-m-d',$iDateFrom));
                }                
            }
        }
        return $aryRange;
    }



    public static function getTotalReturns(){

        if( isset($_POST['action']) ) {

            global $wpdb;

            $user_id = get_current_user_id();

            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];

            $tickers = self::getUserTickers($start_date, $end_date);

            //$dividends = self::getDividendRates( $start_date, $end_date, $companyName );
            
            /*if(!empty($tickers)) {

                $ticker_rates = self::getUserTickersRate($tickers);

            }*/

            //file_put_contents(dirname(__FILE__).'/dividends.log', print_r($dividends, true));

            file_put_contents( dirname(__FILE__).'/tickers.log', print_r( $tickers, true) );




            echo json_encode($tickers);

            die();    
        }        
    } 

    private static function getUserTickersRate($tickers){

        if(!empty($tickers)) {

            $soldTickers = self::getUserSoldTickers($tickers);

            file_put_contents( dirname(__FILE__).'/soldTickers.log', print_r( $soldTickers, true) );

            $ticker_rates = array();

            foreach ( $tickers as $ticker ) {


                $ticker_rates[] = array(



                    );

            }
        }

    }



    private static function getUserTickers($start_date, $end_date){

        global $wpdb;
        
        $user_id = get_current_user_id();

        $investment_transations = $wpdb->prefix . 'investment_transations';

        $account_table = $wpdb->prefix . 'cj_user_portfolios';

        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';

        $sql_accounts = "SELECT `id`, `account_name`, `recording_currency` FROM $account_table WHERE `user_id`= $user_id";

        $accounts = $wpdb->get_results($sql_accounts);

        if(!empty($accounts)){

            $arr_records = array();

            foreach ( $accounts as $account ) {

                $account_id = $account->id;

                $sql_records = "SELECT t.* FROM $investment_transations as t INNER JOIN $cj_user_portfolios as a ON t.account_id = a.id WHERE t.trade_date >= '".$start_date."' AND t.trade_date <= '".$end_date."' AND t.user_id = $user_id AND a.id = $account_id";

                $records = $wpdb->get_results( $sql_records );

                foreach ( $records as $record ) {


                    // calculate current value of the shares....
                    $account_reporting_currency = self::getAccountReportingCurrency($account_id);

                    $stock_currency = self::getStockCurrency($record->ticker_symbol);

                    $total_value = 0;

                    $company_name = self::getCompanyName( $record->ticker_symbol );   


                    if( $record->transaction_type == '1' ){

                        $dividend_income = self::getDividendRates( $record->trade_date, $end_date, $company_name );

                        $dividend_income = self::currencyConverter( $stock_currency, $account_reporting_currency, $dividend_income);


                        $ticker_total_price = ( ( $record->num_of_shares * $record->price ) * $record->currency ) + ( $record->transaction_fees * $record->currency );                    


                        $price = self::getStocksPrice( $end_date, $record->ticker_symbol );

                        $price = number_format($price, 2);

                        $price = self::currencyConverter( $stock_currency, $account_reporting_currency, $price);

                        $price = number_format($price, 2);                        

                        $total_value = $price*$record->num_of_shares;

                        $total_value = number_format($total_value,2);

                    }else{

                        $dividend_income = self::getDividendRates( $start_date, $record->trade_date, $company_name );

                        $dividend_income = self::currencyConverter( $stock_currency, $account_reporting_currency, $dividend_income);

                        $ticker_total_price = ( ( $record->num_of_shares * $record->price ) * $record->currency ) - ( $record->transaction_fees * $record->currency );
                    }            


                    $arr_records[] = array(
                            "transaction_type" => $record->transaction_type,
                            "ticker_symbol" => $record->ticker_symbol,
                            "price" => $record->price,
                            "transaction_fees" => $record->transaction_fees,
                            "currency" => $record->currency,
                            "num_of_shares" => $record->num_of_shares,
                            "total_report_curr_price" => $account_reporting_currency.$ticker_total_price,
                            "account_name" => $account->account_name,
                            "account_id" => $account_id,
                            "trade_date" => $record->trade_date,
                            "company_name" => $company_name,
                            "total_value" => $account_reporting_currency.$total_value,
                            "dividend_income" => $account_reporting_currency.$dividend_income
                        );


                    

                }
            }
        }

        return $arr_records;
    }






    private static function getDividendRates( $firstDate, $secondDate, $companyName ){
            
        global $wpdb;

        $sql = "SELECT * FROM `wp_dividend` WHERE company_name = '$companyName' AND datepaid_payable >= '$firstDate' AND datepaid_payable <= '$secondDate'";

        $dividends = $wpdb->get_results( $sql );

        $dividends_array = array();

        if( !empty( $dividends ) ){
            
            $dividends_value = 0;

            $dividends_array = array();

            $currency_code = $dividends[0]->currency;

            foreach ($dividends as $dividend ) {

                $dividends_value += $dividend->value;
            }

            $dividends_array[$currency_code] = $dividends_value;
        }

        return $dividends_value;
    }

    private static function getUserSoldTickers($tickers){

        $soldTickers = array();

        foreach ( $tickers as $ticker) {

            if($ticker['transaction_type'] == "2" ){

                $soldTickers.push($ticker);

            }            
        }

        return $soldTickers;        
    }












}/*class ends here*/
?>