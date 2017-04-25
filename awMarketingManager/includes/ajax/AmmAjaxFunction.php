<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AmmAjaxFunction {

    public static function formatAmount( $price ) {

        /* check if $ exist in cover price */
        $findDoller     = strpos($price[0],'$');
        $appendCover    = "";
        if($findDoller === false){
            $appendCover =    "$".$price;
        }else{
            $appendCover =    $price;
        }


        /* check if . exist in orig size */
        $findDot    = strpos($appendCover,'.');
        $appendOrig = "";
        if($findDot === false){
            $appendOrig =    $appendCover.".00";
        }else{
            $appendOrig =    $appendCover;
        }

        return $appendOrig;
    }

	public static function ammMarketBid() {

        $user_id        = get_current_user_id();
        $bond_id        = $_POST['object']["id"];
        $bid_price      = $_POST['object']["bid_price"];
        $offer_price    = $_POST['object']['offer_price'];
        $checker        = $_POST['object']['acceptChecker'];

        if ($checker == "acceptChecker") {
            /*update postmeta */
            $meta_key       = 'market_placed_bid_'.$bond_id;
            $userPlacedBid  = get_post_meta( $bond_id, $meta_key, true );
            
            $userPlacedBid[$user_id][$bond_id] = $bid_price;
            update_post_meta( $bond_id, $meta_key,$userPlacedBid );

            /*winner functionality */
            $trade_winner   = array($bond_id=>array('amount'=>$bid_price,'user_id'=>$user_id));
            $meta_key2      = 'market_trade_bid_'.$bond_id;
            update_post_meta( $bond_id, $meta_key2,$trade_winner );

            /*update usermeta*/
            $meta_key           = 'market_placed_bid';
            $userMetaPlacedBid  = get_user_meta( $user_id, $meta_key, true );
            $userMetaPlacedBid[$bond_id] = $bid_price;
            update_user_meta( $user_id, $meta_key,$userMetaPlacedBid );
        }else{
                    /*update postmeta */
            $meta_key       = 'market_placed_bid_'.$bond_id;
            $userPlacedBid  = get_post_meta( $bond_id, $meta_key, true );
            
            $userPlacedBid[$user_id][$bond_id] = $bid_price;
            update_post_meta( $bond_id, $meta_key,$userPlacedBid );

            /*update usermeta*/
            $meta_key           = 'market_placed_bid';
            $userMetaPlacedBid  = get_user_meta( $user_id, $meta_key, true );
            $userMetaPlacedBid[$bond_id] = $bid_price;
            update_user_meta( $user_id, $meta_key,$userMetaPlacedBid );
        }

        if($bid_price < $offer_price){

            AmmEmailNotifications::sendNotificationBidLessThanOffer($bond_id, $bid_price, $offer_price);
        }
        if($bid_price == $offer_price){

            AmmEmailNotifications::sendNotificationBidderonTrade($bond_id, $bid_price, $user_id);

        }
        
        
        echo json_encode($userPlacedBid);
        die;
    }

    public static function ammTradeBid(){

        $user_id    = get_current_user_id();
        $bon_id     = $_POST["bon_id"];
        $bid_price  = $_POST["bid_price"];
        $bid_user   = $_POST["bid_user"];

        $trade_winner = array($bon_id=>array('amount'=>$bid_price,'user_id'=>$bid_user));

        $meta_key   = 'market_trade_bid_'.$bon_id;
        update_post_meta( $bon_id, $meta_key,$trade_winner );

        AmmEmailNotifications::sendNotificationBidderonTrade($bon_id, $bid_price, $bid_user);

        echo json_encode( $trade_winner);
        die();

    }


}
