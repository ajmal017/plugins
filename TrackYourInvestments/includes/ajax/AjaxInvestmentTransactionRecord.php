<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AjaxInvestmentTransactionRecord {
    //** Constructor **//
    public static function createInvestment(){
           
        if( isset($_POST['action']) ) {
            self::insertInvestmentDetails( $_POST );
            echo "investmenttransactioncreated";
            die();
        }
    }

    public static function insertInvestmentDetails( $details ) {
        global $wpdb;
                   
        
        $investment_transations = $wpdb->prefix . 'investment_transations';
        $ticker                 = $details['ticker_symbol'];
        $stockArray             = self::getStockId($ticker);
        $stickID                = $stockArray[0]->stock_id;
        
        $wpdb->insert(
            $investment_transations,
            array(
                    'trade_date'        =>  $details['tradeDate'],
                    'settlement_date'   =>  $details['settleDate'],
                    'transaction_type'  =>  $details['type'],
                    'equity'            =>  $details['equity'],
                    'ticker_symbol'     =>  $details['ticker_symbol'],
                    'num_of_shares'     =>  $details['shares'],
                    'price'             =>  $details['price'],
                    'transaction_fees'  =>  $details['fees'],
                    'currency'          =>  $details['trading_curr'],
                    'user_id'           =>  get_current_user_id(),
                    'stock_id'          =>  $stickID,
                    'platform'          =>  '',
                    'broker'            =>  $details['broker'],
                    'account_id'        =>  $details['account'],
                    'notes'             =>  $details['notes']
                ),
            array(
                    '%s',
                    '%s', 
                    '%d', 
                    '%s', 
                    '%s',
                    '%d',
                    '%f', 
                    '%f', 
                    '%f', 
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%d',
                    '%s'
                )
            );
    }

    /* get stock_id using ticker from stock table */
    public static function getStockId($ticker){
        global $wpdb;
        $stocks = $wpdb->prefix . 'stocks';
        $results = $wpdb->get_results("SELECT stock_id FROM wp_stocks WHERE ticker = '".$ticker."'");
                                      
        return $results;
    }



    /* Account update functionality */
    public static function updateInvestment(){
           
        if( isset($_POST['action']) ) {
            
            self::updateInvestmentTransaction( $_POST );
            echo "investmenttransactionupdated";
            die();
        }
    }

    public static function updateInvestmentTransaction( $details ) {
        global $wpdb;

        $investment_transations = $wpdb->prefix . 'investment_transations';
        $ticker                 = $details['ticker_symbol'];
        $stockArray             = self::getStockId($ticker);
        $stickID                = $stockArray[0]->stock_id;
        
        $wpdb->update(
            $investment_transations,
            array(
                    'trade_date'        =>  $details['tradeDate'],
                    'settlement_date'   =>  $details['settleDate'],
                    'transaction_type'  =>  $details['type'],
                    'equity'            =>  $details['equity'],
                    'ticker_symbol'     =>  $details['ticker_symbol'],
                    'num_of_shares'     =>  $details['shares'],
                    'price'             =>  $details['price'],
                    'transaction_fees'  =>  $details['fees'],
                    'currency'          =>  $details['trading_curr'],
                    'user_id'           =>  $details['userID'],
                    'stock_id'          =>  $stickID,
                    'platform'          =>  '',
                    'broker'            =>  $details['broker'],
                    'account_id'        =>  $details['account'],
                    'notes'             =>  $details['notes']
                ),
            array( 'transaction_id' => $details['id'] ), 
            array(
                    '%s',
                    '%s', 
                    '%d', 
                    '%s', 
                    '%s',
                    '%d',
                    '%f', 
                    '%f', 
                    '%f', 
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%d',
                    '%s'
                ),
            array( '%d' )
            );
    }

    public static function getConversionRateByAccountIdTicker(){

        $accountId = $_POST['account'];

        $tickerSymbol = $_POST['ticker'];
        
        $tradeDate = $_POST['tradeDate'];

        $account_reporting_currency = AjaxReportPortfolio::getAccountReportingCurrency($accountId);
        $stock_currency = AjaxReportPortfolio::getStockCurrency($tickerSymbol);
        
        $amount = self::getConversionRate($stock_currency,$account_reporting_currency,$tradeDate);

        echo $amount;
    }

    public static function getConversionRate($from_Currency,$to_Currency,$date) {

        if($from_Currency == $to_Currency){
            return 1;
        }

        $from_Currency = urlencode($from_Currency);
        $to_Currency = urlencode($to_Currency);
        $date = date('Y-m-d', strtotime( $date ) );
        
        $dom = new DOMDocument;
        $dom->loadHTMLFile("http://currencies.apps.grandtrunk.net/getrate/$date/$from_Currency/$to_Currency");

        $amount = $dom->textContent;

        $amount = number_format($amount,2);

        return $amount;
    }


}/*class ends here*/
?>