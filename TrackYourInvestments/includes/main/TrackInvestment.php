<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TrackInvestment {
    
    function __construct(){
        /*All actions*/
        add_action("wp_ajax_uploadStockCSV", array( 'AjaxStockCSVUpload','stockUploadCSV'));
        add_action("wp_ajax_uploadStockPriceCSV", array( 'AjaxStockPriceCSVUpload','stockPriceUploadCSV'));
        add_action("wp_ajax_uploadDividendCSV", array( 'AjaxDividendCSVUpload','dividendUploadCSV'));
        add_action("wp_ajax_uploadExchangeCSV", array( 'AjaxExchangeCSVUpload','exchangeUploadCSV'));
        add_action("wp_ajax_stock_delete_request", array( 'AjaxDeleteRecord','deleteStocks'));
        add_action("wp_ajax_dividend_delete_request", array( 'AjaxDeleteRecord','deleteDividend'));
        add_action("wp_ajax_exchangeRate_delete_request", array( 'AjaxDeleteRecord','deleteExchangeRate'));
        add_action("wp_ajax_dividend_setting_request", array( 'AjaxSetting','dividendSetting'));
        add_action("wp_ajax_create_account_request", array( 'AjaxCreateAccount','createAccounts'));
        add_action("wp_ajax_update_account_request", array( 'AjaxCreateAccount','updateAccounts'));
        
        // Chart Portfolio values...

        add_action("wp_ajax_chart_portfolio", array( 'AjaxReportPortfolio','getChartPortfolio'));
        add_action("wp_ajax_single_chart_portfolio", array( 'AjaxReportPortfolio','getSingleChartPortfolio'));
        add_action("wp_ajax_get_total_returns", array( 'AjaxReportPortfolio','getTotalReturns'));

        add_action("wp_ajax_get_conversion_rate", array( 'AjaxInvestmentTransactionRecord','getConversionRateByAccountIdTicker'));



        add_action("wp_ajax_delete_account_request", array( 'Accounts','deleteAccount'));
        add_action("wp_ajax_create_investment_transaction_record_request", array( 'AjaxInvestmentTransactionRecord','createInvestment'));
        add_action("wp_ajax_update_investment_transaction_record_request", array( 'AjaxInvestmentTransactionRecord','updateInvestment'));
        add_action("wp_ajax_delete_investment_transaction_request", array('InvestmentTransactionRecord','deleteAccount'));
        add_action("wp_ajax_fill_other_fields", array('InvestmentTransactionRecord','getCurrencyValues'));
        add_action("wp_ajax_reporting_portfolio_value_request", array('AjaxReportPortfolio','getPortfolio'));

        /** All filters */
        add_filter( 'set-screen-option', array('Stocks','stock_set_screen'), 10, 3 );
        add_filter( 'set-screen-option', array('Transactions','set_screen'), 10, 3 );
        add_filter( 'set-screen-option', array('StockPrice','stock_set_screen'), 10, 3 );
        add_filter( 'set-screen-option', array('Dividend','set_screen'), 10, 3 );
        add_filter( 'set-screen-option', array('ExchangeRate','set_screen'), 10, 3 );

        /* All Shortcode */
        add_shortcode('ACCOUNT', array('Accounts','createAccount'));
        add_shortcode('INVESTMENTTRANSACTIONRECORD', array('InvestmentTransactionRecord','createInvestment'));
        add_shortcode('REPORTINGPORTFOLIOVALUE', array('ReportingPortfolioValue','portfolioValue'));

        add_shortcode('REPORT_TOTAL_RETURNS', array('ReportingPortfolioValue','getTotalReturnsPage'));

        //add_action( 'wp_footer', 'footerJS' );

    }
    /* Admin menu and submenu*/
    public static function cjPluginMenus(){
        global $stocklisting;
        add_menu_page('TYI', 'TYI', 'manage_options', 'cj_stocks', array('Stocks','stocksRate'));
        
        $stocklisting = add_submenu_page( 'cj_stocks', 'Stocks', 'Stocks', 'manage_options', 'stocks',array('Stocks','stocksRate'));
        
        add_action("load-$stocklisting", array('Stocks','stock_add_options'));

        $dividendlisting = add_submenu_page( 'cj_stocks','Dividend','Dividend','manage_options','dividend',array('Dividend','dividendRate'));

        add_action("load-$dividendlisting", array('Dividend','add_options'));
        
        $transactionlisting = add_submenu_page( 'cj_stocks', 'Transactions', 'Transactions', 'manage_options', 'transactions',array('Transactions','transactionsRate'));
        
        add_action("load-$transactionlisting", array('Transactions','add_options'));

        $exchangelisting = add_submenu_page( 'cj_stocks', 'Exchange Rate', 'Exchange Rate', 'manage_options', 'exchange_rate',array('ExchangeRate','exchange_Rate'));
        
        add_action("load-$exchangelisting", array('ExchangeRate','add_options'));

        $stockPricelisting = add_submenu_page( 'cj_stocks', 'Stock Price', 'Stock Price', 'manage_options', 'stock_price',array('StockPrice','stocksPrice'));
        
        add_action("load-$stockPricelisting", array('StockPrice','stock_add_options'));
        
        /* Register script and styles */
        add_action( 'admin_enqueue_scripts', array('TrackInvestment','assets'));
        

        //this is a submenu
        add_submenu_page(null,'Add New Stock','Add New Stock','manage_options','add_new_stock',array('AddNew','add_new_stock'));

        add_submenu_page(null,'Add New Stock Price','Add New Stock Price','manage_options','add_new_stockprice',array('AddNew','add_new_stockprice'));

        add_submenu_page(null,'Add New Dividend','Add New Dividend','manage_options','add_new_dividend',array('AddNew','add_new_dividend')); 

        add_submenu_page(null,'Add New Exchange','Add New Exchange','manage_options','add_new_exchange',array('AddNew','add_new_exchange')); 

        add_submenu_page(null,'Edit Stock','Edit Stock','manage_options','edit_stock',array('EditPost','edit_stock')); 

        add_submenu_page(null,'Edit Stock Price','Edit Stock Price','manage_options','edit_stockprice',array('EditPost','edit_stockprice')); 

        add_submenu_page(null,'Edit Exchange','Edit Exchange','manage_options','edit_currency',array('EditPost','edit_currency')); 

        add_submenu_page(null,'Edit Dividend','Edit Dividend','manage_options','edit_dividend',array('EditPost','edit_dividend')); 
        
    }

    /* admin assets */
    public static function assets(){
        wp_enqueue_style( 'CJstyle-css', plugins_url( '../assets/CJstyle.css', __FILE__ ) );
        wp_enqueue_script( 'CJcustom-js', plugins_url( '../assets/CJ-custom-js.js', __FILE__ ) );
    }

    /* frontend assets */
    public static function assetsFrontend(){
        
        $plugin_url    =   plugin_dir_url( __FILE__ );

        wp_enqueue_script( 'CJgooglechart-js', 'http://www.gstatic.com/charts/loader.js',array('jquery'));

        wp_enqueue_script( 'CJgooglecharts-js', 'http://www.google.com/jsapi',array('jquery'));

        wp_enqueue_style( 'CJfrontendstyle-css', plugins_url( '../assets/CJFrontendstyle.css', __FILE__ ) );

        /* Datatable CSS CDN URL */
        wp_enqueue_style( 'CJdatatable-css','http://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css',__FILE__);

        /* Date picker css live CDN*/
        wp_enqueue_style( 'CJdatepicker-css','http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',__FILE__);

        /* select 2 library*/
        wp_enqueue_style( 'CJselect2-css','http://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css',__FILE__);
        
        /* Datatable JS CDN URL */
        wp_enqueue_script( 'CJdatatables-js', 'http://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js',array('jquery'));

        /* Select 2 JS CDN URL */
        wp_enqueue_script( 'CJselect2-js', 'http://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',array('jquery'));

        /* Datepicker JS CDN URL */
        wp_enqueue_script( 'CJdatepicker-js', 'http://code.jquery.com/ui/1.12.1/jquery-ui.js',array('jquery'));

        wp_enqueue_script( 'CJfrontendcustom-js', plugin_dir_url( __FILE__ ).'../assets/CJ-frontend-custom.js',array('jquery'),'1.0.0');

    }

    function footerJS(){
        /* Google JS Chart CDN URL */
        wp_enqueue_script( 'CJgooglechart-js', 'https://www.gstatic.com/charts/loader.js',array('jquery'));

        wp_enqueue_script( 'CJgooglecharts-js', 'http://www.google.com/jsapi',array('jquery'));

        wp_enqueue_script( 'CJfrontendcustom-js', plugin_dir_url( __FILE__ ).'../assets/CJ-frontend-custom.js',array('jquery'),'1.0.0');

        /*wp_enqueue_script( 'CJfrontendcustom-js', site_url().'/chuanjie/wp-content/plugins/TrackYourInvestments/includes/assets/CJ-frontend-custom.js',array('jquery'),'1.0.0');*/
    }
    

    // function to create the DB / Options / Defaults                   
    public static function cjStocksManagerTable() {
        global $wpdb;

        $stock_table = $wpdb->prefix . 'stocks';
        // create the stock_table metabox database table
        if($wpdb->get_var("show tables like '$stock_table'") != $stock_table) {
            $sql1 = "CREATE TABLE $stock_table (
            `stock_id` int(9) NOT NULL AUTO_INCREMENT,
            `stock_name` mediumtext NOT NULL,
            `ticker` mediumtext NOT NULL,
            `exchange_name` mediumtext NOT NULL,
            `currency` mediumtext NOT NULL,
            `created_date` TIMESTAMP NOT NULL,
            PRIMARY KEY stock_id (stock_id)
            );";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql1);
        }

        $investment_transations = $wpdb->prefix . 'investment_transations';
        // create the investment_transations metabox database table
        if($wpdb->get_var("show tables like '$investment_transations'") != $investment_transations) {
            $sql2 = "CREATE TABLE $investment_transations (
            `transacation_id` int(9) NOT NULL AUTO_INCREMENT,
            `trade_date` date NOT NULL,
            `settlement_date` date NOT NULL,
            `transaction_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 for Buy, 2 for Sell',
            `equity` varchar(255) NOT NULL,
            `ticker_symbol` varchar(255) NOT NULL,
            `num_of_shares` mediumtext NOT NULL,
            `price` mediumtext NOT NULL,
            `transaction_fees` mediumtext NOT NULL,
            `currency` mediumtext NOT NULL,
            `user_id` int(9) NOT NULL,
            `stock_id` int(9) NOT NULL,
            `platform` mediumtext NOT NULL,
            `broker` varchar(255) NOT NULL,
            `account_id` tinyint(9) NOT NULL,
            `notes` text NOT NULL,
            PRIMARY KEY (`transacation_id`)
            );";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql2);
        }

        $dividend = $wpdb->prefix . 'dividend';
        // create the dividend metabox database table
        if($wpdb->get_var("show tables like '$dividend'") != $dividend) {
            $sql3 = "CREATE TABLE $dividend (
            `dividend_id` int(9) NOT NULL AUTO_INCREMENT,
            `siblings` mediumtext NOT NULL,
            `key` mediumtext NOT NULL,
            `company_name` mediumtext NOT NULL,
            `record_date` DATE NOT NULL,
            `ex_date` DATE NOT NULL,
            `annc_type` mediumtext NOT NULL,
            `datepaid_payable` DATE NOT NULL,
            `currency` mediumtext NOT NULL,
            `value` FLOAT(9) NOT NULL,
            `tax` mediumtext NOT NULL,
            `interest_start` mediumtext NOT NULL,
            `interest_end` mediumtext NOT NULL,
            `interest_rate` mediumtext NOT NULL,
            `remarks` mediumtext NOT NULL,
            `base` mediumtext NOT NULL,
            `giving` mediumtext NOT NULL,
            `price` mediumtext NOT NULL,
            `before_consolidation` mediumtext NOT NULL,
            `after_consolidation` mediumtext NOT NULL,
            `particulars` mediumtext NOT NULL,
            PRIMARY KEY dividend_id (dividend_id)
            );";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql3);
        }

        $daily_stock_rates = $wpdb->prefix . 'daily_stock_rates';
        // create the daily_stock_rates metabox database table
        if($wpdb->get_var("show tables like '$daily_stock_rates'") != $daily_stock_rates) {
            $sql4 = "CREATE TABLE $daily_stock_rates (
            `stock_rates_id` int(9) NOT NULL AUTO_INCREMENT,
            `stock_id` int(9) NOT NULL,
            `stock_rate_date` DATE NOT NULL,
            `stock_open_rate` mediumtext NOT NULL,
            `stock_high_rate` mediumtext NOT NULL,
            `stock_low_rate` mediumtext NOT NULL,
            `stock_close_rate` mediumtext NOT NULL,
            `stock_volume` mediumtext NOT NULL,
            `stock_adj_close` mediumtext NOT NULL,
            PRIMARY KEY stock_rates_id (stock_rates_id)
            );";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql4);
        }

        $currency_rate = $wpdb->prefix . 'currency_rate';
        // create the currency_rate database table
        if($wpdb->get_var("show tables like '$currency_rate'") != $currency_rate) {
            $sql5 = "CREATE TABLE $currency_rate (
            `ID`    int(9)  NOT NULL AUTO_INCREMENT,
            `date`  DATE    NOT NULL,
            `base_currency` mediumtext NOT NULL,
            `conversion_currency` mediumtext NOT NULL,
            `value` FLOAT(9) NOT NULL,
            PRIMARY KEY ID (ID)
            );";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql5);
        }

        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
        // create the cj_user_portfolios metabox database table
        if($wpdb->get_var("show tables like '$cj_user_portfolios'") != $cj_user_portfolios) {
            $sql6 = "CREATE TABLE $cj_user_portfolios (
            `id` int(9) NOT NULL AUTO_INCREMENT,
            `user_id` int(9) NOT NULL,
            `account_name` mediumtext NOT NULL,
            `description` mediumtext NOT NULL,
            `recording_currency` mediumtext NOT NULL,
            `updated_date` TIMESTAMP NOT NULL,
            `created_date` DATE NOT NULL,
            PRIMARY KEY id (id)
            );";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql6);
        }

        $stock_price = $wpdb->prefix . 'stock_price';
        // create the stock_price metabox database table
        if($wpdb->get_var("show tables like '$stock_price'") != $stock_price) {
            $sql7 = "CREATE TABLE $stock_price (
            `id` int(9) NOT NULL AUTO_INCREMENT,
            `ticker` mediumtext NOT NULL,
            `date` Date NOT NULL,
            `open` FLOAT(9) NOT NULL,
            `high` FLOAT(9) NOT NULL,
            `low` FLOAT(9) NOT NULL,
            `close` FLOAT(9) NOT NULL,
            `volume` FLOAT(9) NOT NULL,
            `adj_close` FLOAT(9) NOT NULL,
            PRIMARY KEY id (id)
            );";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql7);
        }
    }

    public static function remove_cjStocksManagerTable() {
        global $wpdb;
        
        $stock_table = $wpdb->prefix . 'stocks';
        $sql1 = "DROP TABLE IF EXISTS $stock_table;";
        $wpdb->query($sql1);

        $investment_transations = $wpdb->prefix . 'investment_transations';
        $sql2 = "DROP TABLE IF EXISTS $investment_transations;";
        $wpdb->query($sql2);

        $dividend = $wpdb->prefix . 'dividend';
        $sql3 = "DROP TABLE IF EXISTS $dividend;";
        $wpdb->query($sql3);

        $daily_stock_rates = $wpdb->prefix . 'daily_stock_rates';
        $sql4 = "DROP TABLE IF EXISTS $daily_stock_rates;";
        $wpdb->query($sql4);

        $currency_rate = $wpdb->prefix . 'currency_rate';
        $sql5 = "DROP TABLE IF EXISTS $currency_rate;";
        $wpdb->query($sql5);

        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
        $sql6 = "DROP TABLE IF EXISTS $cj_user_portfolios;";
        $wpdb->query($sql6);

        $stock_price = $wpdb->prefix . 'stock_price';
        $sql7 = "DROP TABLE IF EXISTS $stock_price;";
        $wpdb->query($sql7);
    } 

    /* Create Primary menus */
    public static function createMenu(){
        
        $menu_name      = 'TYI Menu';
        $menu_exists    = wp_get_nav_menu_object( $menu_name );
        
        // If it doesn't exist, let's create it.
        if( !$menu_exists){
            $menu_id = wp_create_nav_menu($menu_name);

            //Create wordpress pages
           
            $total_returns = array(
            'post_type'     => 'page',
            'post_title'    => 'Total Returns',
            'post_content'  => '[REPORT_TOTAL_RETURNS]',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_slug'     => 'total-returns');

            $accounts_page = array(
            'post_type'     => 'page',
            'post_title'    => 'Accounts',
            'post_content'  => '[ACCOUNT]',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_slug'     => 'accounts');

            $record_page = array(
            'post_type'     => 'page',
            'post_title'    => 'Record',
            'post_content'  => '[INVESTMENTTRANSACTIONRECORD]',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_slug'     => 'record');

            $report_page = array(
            'post_type'     => 'page',
            'post_title'    => 'Report',
            'post_content'  => '[REPORTINGPORTFOLIOVALUE]',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_slug'     => 'report');
            
            if( null == get_page_by_title( 'Total Returns' ) ) {
                wp_insert_post($total_returns);
            }
            if( null == get_page_by_title( 'Accounts' ) ) {
                $accounts_page_id   = wp_insert_post($accounts_page);    
            }
            if( null == get_page_by_title( 'Record' ) ) {
                $record_page_id     = wp_insert_post($record_page);    
            }
            if( null == get_page_by_title( 'Report' ) ) {
                $report_page_id     = wp_insert_post($report_page);    
            }
            if( null == get_page_by_title( 'Logout' ) ) {
                $logout_page_id     = wp_insert_post($logout_page);    
            }       
            
            
        }
    }

    /*Delete menu*/
    public static function deleteMenu(){
        $menu_name   = 'TYI Menu';
        wp_delete_nav_menu($menu_name);
    }

    /* validate csv file */
    public static function validateCSV( $db_columns, $csv_path ) {
        //$csv_columns_count ='';
        if (($handle = fopen($csv_path, "r")) !== FALSE) {
            
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) { 
                $csv_columns = array_map('trim',$data); 
                $csv_columns_count = count($data);
                break;
            }
            fclose($handle);
        }
        
        $result = array_diff($db_columns, $csv_columns);   
        if( empty($result) && $csv_columns_count == count($db_columns)  ){ 
            return true;
        }else{ 
            return false;
        }
    }
}/*class ends here*/
?>