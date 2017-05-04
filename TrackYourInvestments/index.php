<?php
/*
	Plugin Name: Track Your Investments
	Plugin URI:
	Description: This plugin used to track the investments.
	Version: 1.0.0
	Author: AW109
	Author URI:
	License:
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*error_reporting(E_ALL);
ini_set('display_errors',1);*/

/* include classes*/

include('includes/frontend/InvestmentTransactionRecord.php');
include('includes/frontend/ReportingPortfolioValue.php');
include('includes/frontend/Accounts.php');

include('includes/ajax/AjaxInvestmentTransactionRecord.php');
include('includes/ajax/AjaxStockPriceCSVUpload.php');
include('includes/ajax/AjaxExchangeCSVUpload.php');
include('includes/ajax/AjaxDividendCSVUpload.php');
include('includes/ajax/AjaxReportPortfolio.php');
include('includes/ajax/AjaxStockCSVUpload.php');
include('includes/ajax/AjaxCreateAccount.php');

include('includes/main/Transactions.php');
include('includes/main/ExchangeRate.php');
include('includes/main/StockPrice.php');
include('includes/main/Dividend.php');
include('includes/main/EditPost.php');
include('includes/main/Setting.php');
include('includes/main/AddNew.php');
include('includes/main/Stocks.php');
include('includes/main/TrackInvestment.php');

/*  create plugin object. */
new TrackInvestment;

Class CJSM{

	public static function install() {
		TrackInvestment::cjStocksManagerTable();
		TrackInvestment::createMenu();
	}

	public static function remove() {
		TrackInvestment::remove_cjStocksManagerTable();
		TrackInvestment::deleteMenu();
	}	
}
/*Activation hook*/
register_activation_hook( __FILE__, array('CJSM', 'install') );

/* Deactivation hook*/
register_deactivation_hook( __FILE__, array('CJSM','remove') );

add_action('admin_menu',array('TrackInvestment','cjPluginMenus'));
add_action( 'wp_enqueue_scripts', array('TrackInvestment','assetsFrontend') );


?>