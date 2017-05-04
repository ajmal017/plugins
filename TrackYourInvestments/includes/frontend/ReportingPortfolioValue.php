<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ReportingPortfolioValue{

    /* Create Account*/
	public static function portfolioValue(){
        self::portfolioValueHTML();
        die();
    }

    public static function portfolioValueHTML(){
    	$url            = admin_url().'admin-ajax.php';
        $loader         = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/loading.gif';
        //if (!isset($_GET['page']) && $_GET['page'] != 'edit' && $_GET['page'] != 'trash') {
    ?>
        	<div class="reporting-portfolio">
                <!-- message -->
                <div class="success"><span>Investment has been created successfully.</span></div>
                <div class="errors"><span>Please fill all the fields.</span></div>
                <div class='loader'><img src='<?php echo $loader; ?>'></div>
                <div class='url'>
                    <input type="hidden" name="url"  class="wp_url" value="<?php echo $url; ?>">
                    <input type="hidden" name="userId" class="userId" value="<?php echo get_current_user_id();?>">
                    <input type="hidden" name="pageURL" class="pageURL" value="<?php echo site_url();?>">
                </div>
                
                <!-- Create Reporting Portfolio Value Table Start -->
                <div class="reporing-title"><h2>Portfolio Value</h2></div>
                <div class="reporing-sub-title"><h3>Select the Period of Interest</h3></div>
                <div class="date-ranger">
                    <div>
                        <input type="text" name="date1" class="date1">                       
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                </div>
        		<table class="reporting-portfolio-value table-style">
        			<thead>
        				<tr>
                            <td>Account Name</td>
                            <td>Stock Name</td>
                            <td>Ticker</td>
                            <td>No. of shares</td>
                            <td>Price</td>
                            <td>Value</td>
                        </tr>
        			</thead>
        			<tbody></tbody>
        		</table>

                <div class="reporting-portfolio-chart">
                    <h3>Portfolio Value over Time</h3>
                    <div class="account-empty"><h4>There are no transactions in selected account(s).</h4></div>
                    <div class="time-interval">
                        <select name="account-list" multiple="multiple">
                        <?php 
                            $accountList = self::getAccountDetails( get_current_user_id() );
                            foreach ($accountList as $value) {
                                $accountName    = $value->account_name;
                                $accountId      = $value->id;
                                echo "<option value='".$accountId."'>".$accountName."</option>";          
                            }
                        ?>                        
                        </select>
                        <select name="time-date">
                            <option value="1">Daily</option>                            
                        </select>
                    </div>
                    <div id="curve_chart"></div>
                    <div class="reporting-portfolio-dates">
                        <ul>
                            <li class="fl-left">
                                <input type="text" name="reporting-date-start" class="reporting-date-start">
                            </li>
                            <li class="fl-right">
                                <input type="text" name="reporting-date-end" class="reporting-date-end">
                            </li>                            
                        </ul>
                    </div>
                    <div class="reporting-portfolio-dates clear chart-button-container">
                        <ul>
                            <li>
                                <input type="button" name="reporting-submit" class="reporting-submit" value="Check">
                            </li>
                        </ul>
                    </div>
                </div>

        		<!-- Create Reporting portfolio value Table End -->
        	</div>
    <?php    
        //}elseif (isset($_GET['page']) && $_GET['page'] == 'edit' ) {
          /*  $editId = $_GET['id'];
            self::editInvestmentTransaction($editId);*/
        /*}else{
        }*/
    }

    public static function getTotalReturnsPage(){

        $url            = admin_url().'admin-ajax.php';
        $loader         = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/loading.gif';
        //if (!isset($_GET['page']) && $_GET['page'] != 'edit' && $_GET['page'] != 'trash') {
    ?>
            <div class="reporting-portfolio">                                
                <div class="errors"><span>Please fill all the fields.</span></div>
                <div class='loader'><img src='<?php echo $loader; ?>'></div>
                <div class='url'>
                    <input type="hidden" name="url"  class="wp_url" value="<?php echo $url; ?>">
                    <input type="hidden" name="pageURL" class="pageURL" value="<?php echo site_url();?>">
                </div>
                
                <!-- Create Reporting Portfolio Value Table Start -->
                <!-- <div class="reporing-title"><h2>Portfolio Value</h2></div> -->
                <div class="reporing-sub-title"><h3>Select the Period of Interest</h3></div>
                <div class="date-ranger">
                    <div>
                        <input type="text" name="end_date" class="datepicker">
                        <input type="text" name="start_date" class="datepicker">
                        <input type="button" name="get_total_returns" value="SUBMIT">
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                </div>
                <table id="tbl_total_returns" class="reporting-portfolio-value table-style">
                    <!-- <thead>
                        <tr>
                            <td>Name</td>
                            <td>Ticker</td>
                            <td>Portfolio gains/losses</td>
                            <td>Dividend income</td>                            
                        </tr>
                    </thead> -->
                    <thead>
                        <tr>
                            <td>Name</td>
                            <td>Ticker</td>
                            <td>No. of shares</td>
                            <td>Trade Date</td>
                            <td>Type</td>
                            <td>Total Price</td>
                            <td>Current Value</td>
                            <td>Dividend income</td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>                
            </div>
    <?php    
    }


    /* fetch account name by account ID */
    public static function getAccountName($id){
        global $wpdb;
        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
        $results = $wpdb->get_results("SELECT account_name FROM $cj_user_portfolios WHERE id = ".$id);
        return $results;
    }

    /* fetch account details by account ID */
    public static function getAccountDetails($id){
        global $wpdb;
        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
        $results = $wpdb->get_results("SELECT * FROM $cj_user_portfolios WHERE user_id = ".$id);
        return $results;
    }

    /* fetch all investment transaction record */
    public static function getAllInvestmentTransaction(){
        
        global $wpdb;
        $investment_transations = $wpdb->prefix . 'investment_transations';
        $results = $wpdb->get_results("SELECT * FROM $investment_transations WHERE user_id = ".get_current_user_id()." GROUP BY `ticker_symbol`" );
        return $results;
    }

    /* fetch all current user account */
    public static function getAllUserAccount(){
        
        global $wpdb;
        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
        $results = $wpdb->get_results("SELECT * FROM $cj_user_portfolios WHERE user_id = ".get_current_user_id());
        return $results;
    }

    /* edit page functionality */
    public static function editInvestmentTransaction($id){

        global $wpdb;
        $loader                 = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/ajax-loader.gif';
        $url                    = admin_url().'admin-ajax.php';
        $investment_transations = $wpdb->prefix . 'investment_transations';
        $results                = $wpdb->get_results("SELECT * FROM $investment_transations WHERE transaction_id = ".$id);
    ?>
        <div class="investment-update">
            <!-- message -->
            <div class="success"><span>Investment Transaction has been updated successfully.</span></div>
            <div class="errors"><span>Please fill all the fields.</span></div>
            <div class='loader'><img src='<?php echo $loader; ?>' height='25' width='25'></div>

            <!-- update investment transaction record table Start -->
            <table class="update-investment table-style">
                <thead>
                    <tr>
                        <th><span>Update Account</span></th>
                        <th>
                            <input type="hidden" name="userId" class="userId" value="<?php echo get_current_user_id();?>">
                            <input type="hidden" name="url" class="url" value="<?php echo $url; ?>">
                            <input type="hidden" name="id" class="id" value="<?php echo $id; ?>">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2">
                            <ul>
                                <li>
                                   <div><input type="radio" name="buy-sell" id="buy-sell" value="1" <?php echo $results[0]->transaction_type == '1'?'checked':''; ?>>
                                   <span>Buy</span></div>
                                   <div><input type="radio" name="buy-sell" id="buy-sell" value="2" <?php echo $results[0]->transaction_type == '2'?'checked':''; ?>>
                                   <span>Sell</span></div> 
                                </li>
                                <li>
                                    <h5>Trade date</h5>
                                    <input type="date" name="tradeDate" class="tradeDate" value="<?php echo $results[0]->trade_date; ?>">
                                </li>
                                <li>
                                    <h5>Settlement date</h5>
                                    <input type="date" name="settleDate" class="settleDate" value="<?php echo $results[0]->settlement_date; ?>">
                                </li>
                                <li>
                                    <button type="button" name="update" class="btn" id="update-investment">Update</button>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td><span>Account</span></td>
                        <td>
                            <select name="account-name" class="account-name">
                                <option value="0">Select Account</option>
                                <?php $accountResult = self::getAllUserAccount(); ?>
                                <?php foreach ($accountResult as $value) { ?>
                                    <?php $id             = $value->id; ?>
                                    <?php $accountName    = $value->account_name; ?>
                                    <?php 
                                        $val = $results[0]->account_id == $id?"selected":"";
                                        echo "<option value='".$id."' ".$val.">".$accountName."</option>"; 
                                    ?>
                                <?php } ?>
                                ?>
                            </select>
                        </td>
                    </tr>
                    <!-- <tr>
                        <td><span>Trading Currency</span></td>
                        <td>
                            <select name="trading-curr" class="trading-curr">
                                <option value="0">Select Recording Currency</option>
                                <option value="SGD" <?php //echo $results[0]->currency=="SGD"?"selected":"" ?>>SGD</option>
                                <option value="USD" <?php //echo $results[0]->currency=="USD"?"selected":"" ?>>USD</option>
                                <option value="HKD" <?php //echo $results[0]->currency=="HKD"?"selected":"" ?>>HKD</option>
                                <option value="AED" <?php //echo $results[0]->currency=="AED"?"selected":"" ?>>AED</option>
                            </select>
                        </td>
                    </tr> -->
                    <tr>
                        <td><span>Ticker symbol</span></td>
                        <td><input type="text" name="ticker-symbol" class="ticker-symbol" value="<?php echo $results[0]->ticker_symbol?>"></td>
                    </tr>
                    <tr>
                        <td><span>Equity name</span></td>
                        <td><input type="text" name="equity" class="equity" value="<?php echo $results[0]->equity; ?>"></td>
                    </tr>
                    <tr>
                        <td><span>Number of shares</span></td>
                        <td><input type="text" name="shares" class="shares" value="<?php echo $results[0]->num_of_shares; ?>"></td>
                    </tr>
                    <tr>
                        <td><span>Exchange Rate</span></td>
                        <td><input type="text" name="rec-tra-curr" class="rec-tra-curr"></td>
                    </tr>
                    <tr>
                        <td><span>Transaction price</span></td>
                        <td><input type="text" name="transaction-price" class="transaction-price" value="<?php echo $results[0]->price; ?>"></td>
                    </tr>
                    <tr>
                        <td><span>Transaction fees</span></td>
                        <td><input type="text" name="transaction-fees" class="transaction-fees" value="<?php echo $results[0]->transaction_fees; ?>"></td>
                    </tr>
                    <tr>
                        <td><span>Broker</span></td>
                        <td><input type="text" name="broker" class="broker" value="<?php echo $results[0]->broker; ?>"></td>
                    </tr>
                    <tr>
                        <td colspan="2"><h5>Trading notes(optional)</h5>
                            <textarea name="notes" class="notes" maxlength="250"><?php echo $results[0]->notes; ?></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="back"><a href="<?php echo site_url()?>/index.php/record/">Back</a></div>
        </div>
    <?php
    }

    /* Delete Investment transaction functionality */
    public static function deleteAccount(){
        if( isset($_POST['action']) ) {
            global $wpdb;
            $investment_transations = $wpdb->prefix . 'investment_transations';
            $wpdb->delete( $investment_transations, array( 'transaction_id' => $_POST['id'] ), array( '%d' ) );
            echo "investmenttransactiondeleted";
            die();
        }
    }

    /*get currency based on ticker */
    public static function getCurrencyValues(){
        global $wpdb;
        $ticker             = $_POST['ticker'];
        $accountID          = $_POST['accountID'];
        
        if ($ticker != '' && $accountID != '' ) {
            
            $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
            $results            = $wpdb->get_results("SELECT * FROM $cj_user_portfolios WHERE id = ".$accountID);
            $recording_currency = $results[0]->recording_currency;

            $stock_table        = $wpdb->prefix . 'stocks';
            $stock_results      = $wpdb->get_results("SELECT * FROM $stock_table WHERE ticker = '".$ticker."'");
            $trade_currency     = $stock_results[0]->currency;
            $stock_name         = $stock_results[0]->stock_name;
                
            $return             = array('recording_currency'=>$recording_currency,'trade_currency'=>$trade_currency, 'stock_name' => $stock_name);
            echo json_encode($return);
            die();
        }
    }

}/*class ends here*/
?>