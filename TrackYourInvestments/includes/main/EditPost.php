<?php
/**
* Edit class
*/
class EditPost{
	
	/* edit Stock functionality */
    public static function edit_stock(){
    	
        $message    = '';
        $stock_name = '';
        $ticker     = '';
        $exchange_name = '';

        if (isset($_POST['insert'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . "stocks";
            $stock_name = $_POST["stock_name"];
            $ticker = $_POST["ticker"];
            $exchange_name = $_POST["exchange_name"];
            $currency = $_POST["currency"];
            

            if($stock_name != '' && $ticker != '' && $exchange_name != '' && $currency != '' ){
                
                $wpdb->update( 
					$table_name, 
					array( 
						'stock_name' => $stock_name,
						'ticker' => $ticker,	
						'exchange_name' => $exchange_name,
                        'currency' => $currency
					), 
					array( 'stock_id' => $_GET['id'] ), 
					array( 
						'%s',	// value1
						'%s',	// value1
						'%s',	// value2
                        '%s'
					), 
					array( '%d' ) 
				);
                
                $message ="Stock has successfully been Updated!";

            }else{

                 $message ="Please fill in all fields";
            }
           
        }
        global $wpdb;
        $stockId = $_GET['id'];
        $results = $wpdb->get_results( 'SELECT * FROM wp_stocks WHERE stock_id = '.$stockId );
        $stockName = $results[0]->stock_name;
        $stockTicker = $results[0]->ticker;
        $exchange_name = $results[0]->exchange_name;
        $currency = $results[0]->currency;

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Edit Stock</h1>        
            <?php if (isset($message) && $message !="" ): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <div id="poststuff">
                    <table class='wp-list-table widefat fixed'>
                            <tr>
                                <th>Stock Name</th>
                                <td>
                                    <input type="text" name="stock_name" value="<?php echo $stockName; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Ticker</th>
                                <td>
                                    <input type="text" name="ticker" value="<?php echo $stockTicker; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Exchange Name</th>
                                <td>
                                    <input type="text" name="exchange_name" value="<?php echo $exchange_name; ?>"/>
                                </td>
                            </tr> 
                            <tr>
                                <th>Currency</th>
                                <td>
                                    <input type="text" name="currency" value="<?php echo $currency; ?>"/>
                                </td>
                            </tr>                 
                    </table>
                </div>
                <div id="major-publishing-actions">
                    <div id="back-action">
                        <span class="spinner"></span>
                        <?php $dividendpage =  admin_url().'admin.php?page=stocks'; ?>
                        <a href='<?php echo $dividendpage; ?>' class="stockPageURL">Back</a>
                    </div>
                    <div id="publishing-action">
                        <span class="spinner"></span>
                        <input class="button button-primary button-large" type='submit' name="insert" value='Update'>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    <?php
    }

    /* edit Stock price functionality */
    public static function edit_stockprice(){
        
        $message    = '';
        $ticker     = '';
        $date       = '';
        $open       = '';
        $high       = '';
        $low        = '';
        $close      = '';
        $volume     = '';
        $adj_close  = '';



        if (isset($_POST['insert'])) {
            global $wpdb;

            $originalDate = $_POST['date'];
            $newDate = date("Y-m-d", strtotime($originalDate));

            $table_name = $wpdb->prefix . "stock_price";
            $ticker     = $_POST['ticker'];
            $date       = $newDate;
            $open       = $_POST['open'];
            $high       = $_POST['high'];
            $low        = $_POST['low'];
            $close      = $_POST['close'];
            $volume     = $_POST['volume'];
            $adj_close  = $_POST['adj_close'];
            
            
            if($table_name != '' && $ticker != '' && $currency != '' ){
                
                $wpdb->update( 
                    $table_name, 
                    array( 
                        'ticker'    => $ticker,
                        'date'      => $date,
                        'open'      => $open,
                        'high'      => $high,
                        'low'       => $low,
                        'close'     => $close,
                        'volume'    => $volume,
                        'adj_close' => $adj_close,
                    ), 
                    array( 'id' => $_GET['id'] ), 
                    array( 
                        '%s',
                        '%s',
                        '%f',
                        '%f',
                        '%f',
                        '%f',
                        '%f',
                        '%f',
                    ), 
                    array( '%d' ) 
                );
                
                $message ="Record has successfully been Updated!";

            }else{

                 $message ="Please fill in all fields";
            }
           
        }
        global $wpdb;
        $stockpriceId = $_GET['id'];
        $results = $wpdb->get_results( 'SELECT * FROM wp_stock_price WHERE id = '.$stockpriceId );
        
        $ticker      = $results[0]->ticker;
        $date        = $results[0]->date;
        $open        = $results[0]->open;
        $high        = $results[0]->high;
        $low         = $results[0]->low;
        $close       = $results[0]->close;
        $volume      = $results[0]->volume;
        $adj_close   = $results[0]->adj_close;

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Edit Stock Price</h1>        
            <?php if (isset($message) && $message !="" ): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <div id="poststuff">
                    <table class='wp-list-table widefat fixed'>
                            <tr>
                                <th>Ticker</th>
                                <td>
                                    <input type="text" name="ticker" value="<?php echo $ticker; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td>
                                    <input type="date" name="date" value="<?php echo $date; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Open</th>
                                <td>
                                    <input type="text" name="open" value="<?php echo $open; ?>"/>
                                </td>
                            </tr>                 
                            <tr>
                                <th>High</th>
                                <td>
                                    <input type="text" name="high" value="<?php echo $high; ?>"/>
                                </td>
                            </tr>                 
                            <tr>
                                <th>Low</th>
                                <td>
                                    <input type="text" name="low" value="<?php echo $low; ?>"/>
                                </td>
                            </tr>   
                            <tr>
                                <th>Close</th>
                                <td>
                                    <input type="text" name="close" value="<?php echo $close; ?>"/>
                                </td>
                            </tr>   
                            <tr>
                                <th>Volume</th>
                                <td>
                                    <input type="text" name="volume" value="<?php echo $volume; ?>"/>
                                </td>
                            </tr> 
                            <tr>
                                <th>Adj Close</th>
                                <td>
                                    <input type="text" name="adj_close" value="<?php echo $adj_close; ?>"/>
                                </td>
                            </tr>                 
                    </table>
                </div>
                <div id="major-publishing-actions">
                    <div id="back-action">
                        <span class="spinner"></span>
                        <?php $dividendpage =  admin_url().'admin.php?page=stock_price'; ?>
                        <a href='<?php echo $dividendpage; ?>' class="stockPageURL">Back</a>
                    </div>
                    <div id="publishing-action">
                        <span class="spinner"></span>
                        <input class="button button-primary button-large" type='submit' name="insert" value='Update'>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    <?php
    }

    /* edit Exchange rate functionality */
    public static function edit_currency(){

    	$message                = '';
        $date                   = '';
        $base_currency          = '';
        $conversion_currency    = '';
        $value                  = '';

        if (isset($_POST['insert'])) {
            global $wpdb;
            $table_name             = $wpdb->prefix . "currency_rate";
            $date                   = $_POST["date"];
            $base_currency          = $_POST["base_currency"];
            $conversion_currency    = $_POST["conversion_currency"];
            $value                  = $_POST["value"];
            
            if($date != '' && $base_currency != '' && $conversion_currency != '' && $value != '' ){
                
                $wpdb->update( 
					$table_name, 
					array( 
						'date' => $date,	// string
						'base_currency' => $base_currency,	// integer (number) 
						'conversion_currency' => $conversion_currency,	// integer (number) 
						'value' => $value	// integer (number) 
					), 
					array( 'ID' => $_GET['id'] ), 
					array( 
						'%s',	// value1
						'%s',	// value1
						'%s',	// value2
						'%s',	// value2
					), 
					array( '%d' ) 
				);
                
                $message ="Exchange rate has successfully been Updated!";

            }else{

                 $message ="Please fill in all fields";
            }
           
        }
        
        global $wpdb;
        $exchangeId = $_GET['id'];
        $results = $wpdb->get_results( 'SELECT * FROM wp_currency_rate WHERE ID = '.$exchangeId );
        $date2 = $results[0]->date;
        $baseCurrency = $results[0]->base_currency;
        $currencyConversion = $results[0]->conversion_currency;
        $value2 = $results[0]->value;

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Add New Exchange Rate</h1>        
            <?php if (isset($message) && $message !="" ): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <div id="poststuff">
                    <table class='wp-list-table widefat fixed'>
                            <tr>
                                <th>Date</th>
                                <td>
                                    <input type="date" name="date" value="<?php echo $date2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Base Currency</th>
                                <td>
                                    <input type="text" name="base_currency" value="<?php echo $baseCurrency; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Conversion Currency</th>
                                <td>
                                    <input type="text" name="conversion_currency" value="<?php echo $currencyConversion; ?>"/>
                                </td>
                            </tr> 
                            <tr>
                                <th>Value</th>
                                <td>
                                    <input type="text" name="value" value="<?php echo $value2; ?>"/>
                                </td>
                            </tr>                 
                    </table>
                </div>
                <div id="major-publishing-actions">
                    <div id="back-action">
                        <span class="spinner"></span>
                        <?php $exchangepage =  admin_url().'admin.php?page=exchange_rate'; ?>
                        <a href='<?php echo $exchangepage; ?>' class="stockPageURL">Back</a>
                    </div>
                    <div id="publishing-action">
                        <span class="spinner"></span>
                        <input class="button button-primary button-large" type='submit' name="insert" value='Update'>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    	<?php

    }

    public static function edit_dividend(){

    	$message                    = '';
        $dividend_id                = '';
        $record_date                = '';
        $ex_date                    = '';
        $payable_date               = '';
        $value                      = '';
        $currency                   = '';
        $tax                        = '';
        $siblings                   = '';
        $dividend_key               = '';
        $company_name               = '';
        $annc_type                  = '';
        $interest_start             = '';
        $interest_end               = '';
        $interest_rate              = '';
        $remarks                    = '';
        $base                       = '';
        $giving                     = '';
        $price                      = '';
        $before_consolidation       = '';
        $after_consolidation        = '';
        $particulars                = '';
        

        if (isset($_POST['insert'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . "dividend";

            $rDate                  = $_POST["record_date"];
            $RecordDate             = date("Y-m-d", strtotime($rDate));

            $eDate                  = $_POST["ex_date"];
            $ExDate                 = date("Y-m-d", strtotime($eDate));

            $DPDate                 = $_POST["datepaid_payable"];
            $DatePDate              = date("Y-m-d", strtotime($DPDate));
            
            $siblings               = $_POST["siblings"];
            $key                    = $_POST["key"];
            $company_name           = $_POST["company_name"];
            $record_date            = $RecordDate;
            $ex_date                = $ExDate;
            $annc_type              = $_POST["annc_type"];
            $datepaid_payable       = $DatePDate;
            $currency               = $_POST["currency"];
            $value                  = $_POST["value"];
            $tax                    = $_POST["tax"];
            $interest_start         = $_POST["interest_start"];
            $interest_end           = $_POST["interest_end"];
            $interest_rate          = $_POST["interest_rate"];
            $remarks                = $_POST["remarks"];
            $base                   = $_POST["base"];
            $giving                 = $_POST["giving"];
            $price                  = $_POST["price"];
            $before_consolidation   = $_POST["before_consolidation"];
            $after_consolidation    = $_POST["after_consolidation"];
            $particulars            = $_POST["particulars"];
            
            if( $record_date != '' && $ex_date != '' &&  $datepaid_payable != '' &&  $value != '' &&  $currency != '' &&  $tax != '' &&  $siblings != '' &&  $key != '' &&  $company_name != '' &&  $annc_type != '' &&  $interest_start != '' &&  $interest_end != '' &&  $interest_rate != '' &&  $remarks != '' &&  $base != '' &&  $giving != '' &&  $price != '' &&  $before_consolidation != '' &&  $after_consolidation != '' &&  $particulars != '' ){
                
                $wpdb->update(
                    $table_name, //table
                   array(
                        "siblings"              =>  $siblings,  
                        "key"                   =>  $key,  
                        "company_name"          =>  $company_name, 
                        "record_date"           =>  $record_date,    
                        "ex_date"               =>  $ex_date, 
                        "annc_type"             =>  $annc_type,  
                        "datepaid_payable"      =>  $datepaid_payable,  
                        "currency"              =>  $currency,  
                        "value"                 =>  $value,  
                        "tax"                   =>  $tax, 
                        "interest_start"        =>  $interest_start,    
                        "interest_end"          =>  $interest_end,  
                        "interest_rate"         =>  $interest_rate, 
                        "remarks"               =>  $remarks,   
                        "base"                  =>  $base,  
                        "giving"                =>  $giving,    
                        "price"                 =>  $price, 
                        "before_consolidation"  =>  $before_consolidation,  
                        "after_consolidation"   =>  $after_consolidation,   
                        "particulars"           =>  $particulars    
                        ),
                   		array( 'dividend_id' => $_GET['id'] ), 
                        array('%d', '%d','%s', '%s', '%s', '%s', '%s','%s', '%f', '%s', '%s', '%s','%s', '%s', '%s', '%s', '%s','%s', '%s', '%s'),
                        array('%d')
                );
                
                $message ="Dividend has successfully been updated!";

            }else{

                 $message ="Please fill in all fields";
            }
           
        }
        global $wpdb;
        $dividendId = $_GET['id'];
        $results = $wpdb->get_results( 'SELECT * FROM wp_dividend WHERE dividend_id = '.$dividendId );

		$siblings2              =       $results[0]->siblings;
        $key2                   =       $results[0]->key;
        $company_name2          =       $results[0]->company_name;
        $record_date2           =       $results[0]->record_date;
        $ex_date2               =       $results[0]->ex_date;
        $annc_type2             =       $results[0]->annc_type;
        $datepaid_payable2      =       $results[0]->datepaid_payable;
        $currency2              =       $results[0]->currency;
        $value2                 =       $results[0]->value;
        $tax2                   =       $results[0]->tax;
        $interest_start2        =       $results[0]->interest_start;
        $interest_end2          =       $results[0]->interest_end;
        $interest_rate2         =       $results[0]->interest_rate;
        $remarks2               =       $results[0]->remarks;
        $base2                  =       $results[0]->base;
        $giving2                =       $results[0]->giving;
        $price2                 =       $results[0]->price;
        $before_consolidation2  =       $results[0]->before_consolidation;
        $after_consolidation2   =       $results[0]->after_consolidation;
        $particulars2           =       $results[0]->particulars; 
       

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Add New Dividend</h1>        
            <?php if (isset($message) && $message !="" ): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <div id="poststuff">
                    <table class='wp-list-table widefat fixed'>
                            <tr>
                                <th>Siblings</th>
                                <td>
                                    <input type="text" name="siblings" value="<?php echo $siblings2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Key</th>
                                <td>
                                    <input type="text" name="key" value="<?php echo $key2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Company Name</th>
                                <td>
                                    <input type="text" name="company_name" value="<?php echo $company_name2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Record Date</th>
                                <td>
                                    <input type="date" name="record_date" value="<?php echo $record_date2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Ex Date</th>
                                <td>
                                    <input type="date" name="ex_date" value="<?php echo $ex_date2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Annc Type</th>
                                <td>
                                    <input type="text" name="annc_type" value="<?php echo $annc_type2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Dividend Payable Date</th>
                                <td>
                                    <input type="date" name="datepaid_payable" value="<?php echo $datepaid_payable2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Currency</th>
                                <td>
                                    <input type="text" name="currency" value="<?php echo $currency2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Value</th>
                                <td>
                                    <input type="text" name="value" value="<?php echo $value2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Tax</th>
                                <td>
                                    <input type="text" name="tax" value="<?php echo $tax2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Interest Start</th>
                                <td>
                                    <input type="text" name="interest_start" value="<?php echo $interest_start2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Interest End</th>
                                <td>
                                    <input type="text" name="interest_end" value="<?php echo $interest_end2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Interest Rate</th>
                                <td>
                                    <input type="text" name="interest_rate" value="<?php echo $interest_rate2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Remarks</th>
                                <td>
                                    <input type="text" name="remarks" value="<?php echo $remarks2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Base</th>
                                <td>
                                    <input type="text" name="base" value="<?php echo $base2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Giving</th>
                                <td>
                                    <input type="text" name="giving" value="<?php echo $giving2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Price</th>
                                <td>
                                    <input type="text" name="price" value="<?php echo $price2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Before Consolidation</th>
                                <td>
                                    <input type="text" name="before_consolidation" value="<?php echo $before_consolidation2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>After Consolidation</th>
                                <td>
                                    <input type="text" name="after_consolidation" value="<?php echo $after_consolidation2; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Particulars</th>
                                <td>
                                    <input type="text" name="particulars" value="<?php echo $particulars2; ?>"/>
                                </td>
                            </tr>                    
                    </table>
                </div>
                <div id="major-publishing-actions">
                    <div id="back-action">
                        <span class="spinner"></span>
                        <?php $dividendpage =  admin_url().'admin.php?page=dividend'; ?>
                        <a href='<?php echo $dividendpage; ?>' class="stockPageURL">Back</a>
                    </div>
                    <div id="publishing-action">
                        <span class="spinner"></span>
                        <input class="button button-primary button-large" type='submit' name="insert" value='Update'>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    <?php
    }

}