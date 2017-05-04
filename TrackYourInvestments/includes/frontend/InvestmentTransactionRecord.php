<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class InvestmentTransactionRecord {

    /* Create Account*/
	public static function createInvestment(){
        self::investmentHTML();
        die();
    }

    public static function investmentHTML(){
    	$url            = admin_url().'admin-ajax.php';
        $loader         = site_url().'/wp-content/plugins/TrackYourInvestments/includes/images/ajax-loader.gif';
        if (!isset($_GET['page']) && $_GET['page'] != 'edit' && $_GET['page'] != 'trash') {
    ?>
        	<div class="investment-transaction">
                <!-- message -->
                <div class="success"><span>Investment has been created successfully.</span></div>
                <div class="errors"><span>Please fill all the fields.</span></div>
                <div class='loader'><img src='<?php echo $loader; ?>' height='25' width='25'></div>
                
                <!-- Create Investment Transaction Record Table Start -->
        		<table class="create-investment table-style">
        			<thead>
        				<tr>
                            <th colspan="2">
                                <h2>Investment Transaction Record</h2>
        						<input type="hidden" name="userId" class="userId" value="<?php echo get_current_user_id(); ?>">
        						<input type="hidden" name="createdDate" class="createdDate" value="<?php echo date("Y-m-d");?>">
                                <input type="hidden" name="url" class="url" value="<?php echo $url; ?>">
                            </th>
                        </tr>
        			</thead>
        			<tbody>
        				<tr>
        					<td colspan="2">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="top-cols top-cols-1">
                                                <div>
                                                    <input type="radio" name="buy-sell" id="buy-sell" value="1" checked>
                                                    <span>Buy</span>
                                                </div>
                                                <div>
                                                    <input type="radio" name="buy-sell" id="buy-sell" value="2">
                                                    <span>Sell</span>
                                                </div> 
                                            </td>
                                            <td class="top-cols top-cols-2">
                                                <h5>Trade date</h5>
                                                <input type="text" name="tradeDate" class="tradeDate datepicker" placeholder="">
                                            </td>
                                            <td class="top-cols top-cols-3">
                                                <h5>Settlement date</h5>
                                                <input type="text" name="settleDate" class="settleDate datepicker" placeholder="">
                                            </td>
                                            <td class="top-cols top-cols-4">
                                                <button type="button" name="save" class="btn" id="save-investment">Save</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
                                        <?php echo "<option value=".$id.">".$accountName."</option>"; ?>
                                    <?php } ?>
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><span>Ticker symbol</span></td>
                            <td><input type="text" name="ticker-symbol" class="ticker-symbol"></td>
                        </tr>
                        <tr>
                            <td><span>Equity name</span></td>
                            <td><input type="text" name="equity" class="equity"></td>
                        </tr>
                        <!-- <tr>
                            <td><span>Trading Currency</span></td>
                            <td>
                                <select name="trading-curr" class="trading-curr">
                                    <option value="0">Select Recording Currency</option>
                                    <option value="SGD">SGD</option>
                                    <option value="USD">USD</option>
                                    <option value="HKD">HKD</option>
                                    <option value="AED">AED</option>
                                </select>
                            </td>
                        </tr> -->
                        <tr>
                            <td><span>Number of shares</span></td>
                            <td><span class="num_error">Please enter numeric value</span><input type="text" name="shares" class="shares"></td>
                        </tr>
                        <tr>
                            <td><span>Exchange rate</span></td>
                            <td><span class="rate_error">Please enter numeric value</span><input type="text" name="rec-tra-curr" class="rec-tra-curr"></td>
                        </tr>
                        <tr>
                            <td><span>Transaction price</span></td>
                            <td><span class="price_error">Please enter numeric value</span><input type="text" name="transaction-price" class="transaction-price"></td>
                        </tr>
                        <tr>
                            <td><span>Transaction fees</span></td>
                            <td><span class="fees_error">Please enter numeric value</span><input type="text" name="transaction-fees" class="transaction-fees"></td>
                        </tr>
                        <tr>
                            <td><span>Broker</span></td>
                            <td><input type="text" name="broker" class="broker"></td>
                        </tr>
                        <tr>
        					<td colspan="2"><h5>Trading notes(optional)</h5>
                                <textarea rows="5" name="notes" class="notes" maxlength="250" placeholder="Notes"></textarea>
                            </td>
        				</tr>
        			</tbody>
        		</table>
        		<!-- Create Investment Transaction Record Table End -->

        		<!-- All Investment Transaction Record Table Start -->
        		<table class="all-inv-tran-rec table-style">
        			<thead>
        				<tr>
        					<th colspan="14">Transaction History</th>
        				</tr>
                        <tr>
                            <th class="trade-title"><span>Trade Date</span></th>
                            <th><span>Settlement Date</span></th>
                            <th><span>Type</span></th>
                            <th><span>Name</span></th>
                            <th><span>Ticker</span></th>
                            <th><span>No. of shares</span></th>
                            <th><span>Price</span></th>
                            <th><span>Fees</span></th>
                            <th><span>Exchange Rate</span></th>
                            <!-- <th><span>Stock ID</span></th>
                            <th><span>Broker</span></th> -->
                            <th><span>Account Name</span></th>
                            <!-- <th><span>Notes</span></th> -->
                            <th><span>Action</span></th>
                        </tr>
        			</thead>
        			<tbody>
                    <?php 

                        $t_type = array( 1 =>"Buy", 2 => "Sell");

                        $results = self::getAllInvestmentTransaction();
                        foreach ($results as $value) {

                            $account_Name       = self::getAccountName($value->account_id);

                            $transaction_id     = $value->transaction_id;
                            $trade_date         = $value->trade_date;
                            $settlement_date    = $value->settlement_date;
                            $transaction_type   = $t_type[$value->transaction_type];
                            $equity             = $value->equity;
                            $ticker_symbol      = $value->ticker_symbol;
                            $num_of_shares      = $value->num_of_shares;
                            $price              = $value->price;
                            $transaction_fees   = $value->transaction_fees;
                            $currency           = $value->currency;
                            $user_id            = $value->user_id;
                            $stock_id           = $value->stock_id;
                            $platform           = $value->platform;
                            $broker             = $value->broker;
                            $accountName        = $account_Name[0]->account_name;
                            $notes              = $value->notes;
                            
                            $edit = sprintf('<a href="?page=%s&id=%s">Edit</a>','edit', absint($transaction_id) );
                            $delete = sprintf('<a href="javascript:void(0);" class="trash-investment" id='.$transaction_id.'>Trash</a>');
                            
                            //echo "<tr id=".$transaction_id."><td>".$trade_date."</td><td>".$settlement_date."</td><td>".$transaction_type."</td><td>".$equity."</td><td>".$ticker_symbol."</td><td>".$num_of_shares."</td><td>".$price."</td><td>".$transaction_fees."</td><td>".$currency."</td><td>".$stock_id."</td><td>".$broker."</td><td>".$account_id."</td><td>".$notes."</td><td>".$edit." ".$delete."</td></tr>";

                            echo "<tr id=".$transaction_id."><td>".$trade_date."</td><td>".$settlement_date."</td><td>".$transaction_type."</td><td>".$equity."</td><td>".$ticker_symbol."</td><td>".$num_of_shares."</td><td>".$price."</td><td>".$transaction_fees."</td><td>".$currency."</td><td>".$accountName."</td><td>".$edit." ".$delete."</td></tr>";
                        }
                    ?>
        			</tbody>
        		</table>
        		<!-- All Account Table End -->
        	</div>
    <?php    
        }elseif (isset($_GET['page']) && $_GET['page'] == 'edit' ) {
            $editId = $_GET['id'];
            self::editInvestmentTransaction($editId);
        }else{
        }
    }

    /* fetch account name by account ID */
    public static function getAccountName($id){
        global $wpdb;
        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
        $results = $wpdb->get_results("SELECT account_name FROM $cj_user_portfolios WHERE id = ".$id);
        return $results;
    }

    /* fetch all investment transaction record */
    public static function getAllInvestmentTransaction(){
        
        global $wpdb;
        $investment_transations = $wpdb->prefix . 'investment_transations';
        $results = $wpdb->get_results("SELECT * FROM $investment_transations WHERE user_id = ".get_current_user_id());
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
                    <!-- <tr>
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
                    </tr> -->
                    <tr>
                        <td colspan="2">
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="top-cols top-cols-1">
                                            <div>
                                                <input type="radio" name="buy-sell" id="buy-sell" value="1" <?php echo $results[0]->transaction_type == '1'?'checked':''; ?>>
                                                <span>Buy</span>
                                            </div>
                                            <div>
                                                <input type="radio" name="buy-sell" id="buy-sell" value="2" <?php echo $results[0]->transaction_type == '2'?'checked':''; ?>>
                                                <span>Sell</span>
                                            </div> 
                                        </td>
                                        <td class="top-cols top-cols-2">
                                            <h5>Trade date</h5>
                                            <input type="text" name="tradeDate" class="tradeDate datepicker" value="<?php echo $results[0]->trade_date; ?>">
                                        </td>
                                        <td class="top-cols top-cols-3">
                                            <h5>Settlement date</h5>
                                            <input type="text" name="settleDate" class="settleDate datepicker" value="<?php echo $results[0]->settlement_date; ?>">
                                        </td>
                                        <td class="top-cols top-cols-4">
                                            <button type="button" name="update" class="btn" id="update-investment">Update</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
                        <td><input type="text" name="exchange_rate" class="rec-tra-curr" value="<?php echo $results[0]->currency; ?>"></td>
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
                            <textarea name="notes" class="notes" maxlength="250" rows="5"><?php echo $results[0]->notes; ?></textarea>
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
