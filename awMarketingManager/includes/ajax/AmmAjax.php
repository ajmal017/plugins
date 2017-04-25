<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AmmAjax {

	//** Constructor **//
    function __construct() {
        /*Marketing PLace Bid*/
        add_action("wp_ajax_amm_market_bid", array( 'AmmAjaxFunction','ammMarketBid'));
        add_action("wp_ajax_nopriv_amm_market_bid", array( 'AmmAjaxFunction','ammMarketBid'));

        /*Ajax functions filters */
        add_action('wp_ajax_amm_trade_bid', array('AmmAjaxFunction', 'ammTradeBid'));
        add_action("wp_ajax_nopriv_amm_trade_bid", array( 'AmmAjaxFunction','ammTradeBid'));

        
    }    

}
