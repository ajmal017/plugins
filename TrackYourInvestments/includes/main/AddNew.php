<?php
/**
* Add new Stock functionality
*/
class AddNew{

    /* Add new Stock functionality */
    public static function add_new_stock(){

        $message        = '';
        $stock_name     = '';
        $ticker         = '';
        $exchange_name  = '';
        $currency       = '';
        

        if (isset($_POST['insert'])) {
            global $wpdb;
            $table_name     = $wpdb->prefix . "stocks";
            $stock_name     = $_POST["stock_name"];
            $ticker         = $_POST["ticker"];
            $exchange_name  = $_POST["exchange_name"];
            $currency       = $_POST["currency"];
            
            if($stock_name != '' && $ticker != '' && $exchange_name != '' && $currency != ''){
                
                $wpdb->insert(
                    $table_name, //table
                    array('stock_name' => $stock_name,'ticker' => $ticker,'exchange_name' => $exchange_name,'currency' => $currency), //data
                    array('%s', '%s', '%s', '%s') //data format
                );

                $lastID     =   $wpdb->insert_id;
                $adminURL   =   admin_url();
                $message    =   "Stock has successfully been saved!";
                $editPageURL =  $adminURL.'admin.php?page=edit_stock&id='.$lastID;
                
                wp_redirect($editPageURL);

            }else{

                 $message ="Please fill in all fields";
            }
           
        }
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Add New Stock</h1>        
            <?php if (isset($message) && $message !="" ): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <div id="poststuff">
                    <table class='wp-list-table widefat fixed'>
                            <tr>
                                <th>Stock Name</th>
                                <td>
                                    <input type="text" name="stock_name" value="<?php echo $stock_name; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Ticker</th>
                                <td>
                                    <input type="text" name="ticker" value="<?php echo $ticker; ?>"/>
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
                        <?php $stockpage =  admin_url().'admin.php?page=stocks'; ?>
                        <a href='<?php echo $stockpage; ?>' class="stockPageURL">Back</a>
                    </div>
                    <div id="publishing-action">
                        <span class="spinner"></span>
                        <input class="button button-primary button-large" type='submit' name="insert" value='Save'>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    <?php
    }


    public static function add_new_stockprice(){

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
            $originalDate   = $file['date'];
            $newDate        = date("Y-m-d", strtotime($originalDate));
            $table_name     = $wpdb->prefix . "stock_price";
            $ticker         = $_POST['ticker'];
            $date           = $newDate;
            $open           = $_POST['open'];
            $high           = $_POST['high'];
            $low            = $_POST['low'];
            $close          = $_POST['close'];
            $volume         = $_POST['volume'];
            $adj_close      = $_POST['adj_close'];
            
            if($ticker != '' && $date != '' ){
                
                $wpdb->insert(
                    $table_name, //table
                    array('ticker' => $ticker,'date' => $date,'open' => $open,'high' => $high,'low' => $low,'close' => $close,'volume' => $volume,'adj_close' => $adj_close),
                    array('%s', '%s','%f','%f','%f','%f','%f','%f')//data format
                );

                $lastID     =   $wpdb->insert_id;
                $adminURL   =   admin_url();
                $message    =   "Stock has successfully been saved!";
                $editPageURL =  $adminURL.'admin.php?page=edit_stocpricek&id='.$lastID;
                
                wp_redirect($editPageURL);

            }else{

                 $message ="Please fill in all fields";
            }
           
        }
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Add New Stock </h1>        
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
                        <?php $stockpricedpage =  admin_url().'admin.php?page=stock_price'; ?>
                        <a href='<?php echo $stockpricedpage; ?>' class="stockPricePageURL">Back</a>
                    </div>
                    <div id="publishing-action">
                        <span class="spinner"></span>
                        <input class="button button-primary button-large" type='submit' name="insert" value='Save'>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    <?php
    }

    /* Add new Dividend functionality */
    public static function add_new_dividend(){

        $message                    = '';
        $dividend_id                = '';
        $dividend_record_date       = '';
        $dividend_ex_date           = '';
        $dividend_payable_date      = '';
        $dividend_value             = '';
        $dividend_currency          = '';
        $dividend_tax               = '';
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

            
            if( $siblings != '' && $key != '' &&  $company_name != '' &&  $record_date != '' &&  $ex_date != '' &&  $annc_type != '' &&  $datepaid_payable != '' &&  $currency != '' &&  $value != '' &&  $tax != '' &&  $interest_start != '' &&  $interest_end != '' &&  $interest_rate != '' &&  $remarks != '' &&  $base != '' &&  $giving != '' &&  $price != '' &&  $before_consolidation != '' &&  $after_consolidation != '' &&  $particulars != '' ){
                
                $wpdb->insert(
                    $table_name, //table
                   array(
                         "siblings"                 =>  $siblings,  
                         "key"                      =>  $key,  
                         "company_name"             =>  $company_name,  
                         "record_date"              =>  $record_date,  
                         "ex_date"                  =>  $ex_date,  
                         "annc_type"                =>  $annc_type, 
                         "datepaid_payable"         =>  $datepaid_payable, 
                         "currency"                 =>  $currency, 
                         "value"                    =>  $value,    
                         "tax"                      =>  $tax,  
                         "interest_start"           =>  $interest_start,    
                         "interest_end"             =>  $interest_end,  
                         "interest_rate"            =>  $interest_rate, 
                         "remarks"                  =>  $remarks,   
                         "base"                     =>  $base,  
                         "giving"                   =>  $giving,    
                         "price"                    =>  $price, 
                         "before_consolidation"     =>  $before_consolidation,  
                         "after_consolidation"      =>  $after_consolidation,   
                         "particulars"              =>  $particulars    
                        ),
                        array('%d', '%d','%s', '%s', '%s', '%s', '%s','%s', '%f', '%s', '%s', '%s','%s', '%s', '%s', '%s', '%s','%s', '%s', '%s')
                );
                
                $lastID         =   $wpdb->insert_id;
                $adminURL       =   admin_url();
                $message        =   "Dividend has successfully been saved!";
                $editPageURL    =   $adminURL.'admin.php?page=edit_dividend&id='.$lastID;
                
                wp_redirect($editPageURL);
                

            }else{

                 $message ="Please fill in all fields";
            }
           
        }
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
                                    <input type="text" name="siblings" value="<?php echo $siblings; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Key</th>
                                <td>
                                    <input type="text" name="key" value="<?php echo $key; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Company Name</th>
                                <td>
                                    <input type="text" name="company_name" value="<?php echo $company_name; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Record Date</th>
                                <td>
                                    <input type="date" name="record_date" value="<?php echo $record_date; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Ex Date</th>
                                <td>
                                    <input type="date" name="ex_date" value="<?php echo $ex_date; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Annc Type</th>
                                <td>
                                    <input type="text" name="annc_type" value="<?php echo $annc_type; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Date Paid Payable</th>
                                <td>
                                    <input type="date" name="datepaid_payable" value="<?php echo $datepaid_payable; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Currency</th>
                                <td>
                                    <input type="text" name="currency" value="<?php echo $currency; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Value</th>
                                <td>
                                    <input type="text" name="value" value="<?php echo $value; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Tax</th>
                                <td>
                                    <input type="text" name="tax" value="<?php echo $tax; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Interest Start</th>
                                <td>
                                    <input type="text" name="interest_start" value="<?php echo $interest_start; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Interest End</th>
                                <td>
                                    <input type="text" name="interest_end" value="<?php echo $interest_end; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Interest Rate</th>
                                <td>
                                    <input type="text" name="interest_rate" value="<?php echo $interest_rate; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Remarks</th>
                                <td>
                                    <input type="text" name="remarks" value="<?php echo $remarks; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Base</th>
                                <td>
                                    <input type="text" name="base" value="<?php echo $base; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Giving</th>
                                <td>
                                    <input type="text" name="giving" value="<?php echo $giving; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Price</th>
                                <td>
                                    <input type="text" name="price" value="<?php echo $price; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Before Consolidation</th>
                                <td>
                                    <input type="text" name="before_consolidation" value="<?php echo $before_consolidation; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>After Consolidation</th>
                                <td>
                                    <input type="text" name="after_consolidation" value="<?php echo $after_consolidation; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Particulars</th>
                                <td>
                                    <input type="text" name="particulars" value="<?php echo $particulars; ?>"/>
                                </td>
                            </tr>                    
                    </table>
                </div>
                <div id="major-publishing-actions">
                    <div id="back-action">
                        <span class="spinner"></span>
                        <?php $stockpage =  admin_url().'admin.php?page=dividend'; ?>
                        <a href='<?php echo $stockpage; ?>' class="stockPageURL">Back</a>
                    </div>
                    <div id="publishing-action">
                        <span class="spinner"></span>
                        <input class="button button-primary button-large" type='submit' name="insert" value='Save'>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    <?php
    }

    /* Add new Stock functionality */
    public static function add_new_exchange(){

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
                
                $wpdb->insert(
                    $table_name, //table
                    array('date' => $date,'base_currency' => $base_currency,'conversion_currency' => $conversion_currency,'value' => $value), //data
                    array('%s', '%s', '%s', '%s') //data format
                );

                $lastID         =   $wpdb->insert_id;
                $adminURL       =   admin_url();
                $message        =   "Exchange rate has successfully been saved!";
                $editPageURL    =   $adminURL.'admin.php?page=edit_currency&id='.$lastID;
                
                wp_redirect($editPageURL);
                
            }else{

                 $message ="Please fill in all fields";
            }
           
        }
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
                                    <input type="date" name="date" value="<?php echo $date; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Base Currency</th>
                                <td>
                                    <input type="text" name="base_currency" value="<?php echo $base_currency; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>Conversion Currency</th>
                                <td>
                                    <input type="text" name="conversion_currency" value="<?php echo $conversion_currency; ?>"/>
                                </td>
                            </tr> 
                            <tr>
                                <th>Value</th>
                                <td>
                                    <input type="text" name="value" value="<?php echo $value; ?>"/>
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
                        <input class="button button-primary button-large" type='submit' name="insert" value='Save'>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    <?php
    }

}