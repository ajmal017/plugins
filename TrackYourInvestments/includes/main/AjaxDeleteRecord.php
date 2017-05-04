<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* This class is used to delete the different records.
*/
class AjaxDeleteRecord{

	public static function deleteStocks(){
		global $wpdb;
		if ($_POST['action'] == 'stock_delete_request') {
			$stockId = $_POST['stock_id'];

			$result = $wpdb->delete( 'wp_stocks', array( 'stock_id' => $stockId ), array( '%d' ) );
			if ($result != false) {
				echo "stockdeleted";
				die();
			}
		}
	}	

	public static function deleteDividend(){
		global $wpdb;
		if ($_POST['action'] == 'dividend_delete_request') {
			$dividendId = $_POST['dividend_id'];

			$result = $wpdb->delete( 'wp_dividend', array( 'dividend_id' => $dividendId ), array( '%d' ) );
			if ($result != false) {
				echo "dividenddeleted";
				die();
			}
		}
	}	

	public static function deleteExchangeRate(){
		global $wpdb;
		if ($_POST['action'] == 'exchangeRate_delete_request') {
			$dividendID = $_POST['id'];

			$result = $wpdb->delete( 'wp_currency_rate', array( 'ID' => $dividendID ), array( '%d' ) );
			if ($result != false) {
				echo "exchangeRatedeleted";
				die();
			}
		}
	}	
}