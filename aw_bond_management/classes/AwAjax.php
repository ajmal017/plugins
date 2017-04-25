<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require "AwTradeEmailTemplates.php";

class AwAjax {
    public static function awPlaceBid() {

        $user_id    = get_current_user_id();
        $auction_id = $_POST["id"];
        $bon_id     = $_POST["bon_id"];
        $bid_price  = $_POST["bid_price"];

        /*update postmeta */
        $meta_key       = 'user_placed_bid_'.$auction_id.'_'.$bon_id;
        $userPlacedBid  = get_post_meta( $auction_id, $meta_key, true );
        $userPlacedBid[$user_id][$auction_id][$bon_id] = $bid_price;
        update_post_meta( $auction_id, $meta_key,$userPlacedBid );


        /*update usermeta*/
        $meta_key           = 'user_placed_bid';
        $userMetaPlacedBid  = get_user_meta( $user_id, $meta_key, true );
        $userMetaPlacedBid[$auction_id][$bon_id] = $bid_price;
        update_user_meta( $user_id, $meta_key,$userMetaPlacedBid );


        echo json_encode($userPlacedBid);
        die;
    }


    public static function awTradeBid() {

        $user_id    = get_current_user_id();
        $auction_id = $_POST["id"];
        $bon_id     = $_POST["bon_id"];
        $bid_price  = $_POST["bid_price"];
        $bid_user   = $_POST["bid_user"];

        $trade_winner = array($auction_id=>array($bon_id=>array('amount'=>$bid_price,'user_id'=>$bid_user)));

        $meta_key   = 'user_trade_bid_'.$auction_id.'_'.$bon_id;
        update_post_meta( $auction_id, $meta_key,$trade_winner );
        echo json_encode( $trade_winner);
        die();
    }

    public static function awTradeBid1() {

        $user_id    = get_current_user_id();
        $auction_id = $_POST["id"];
        $bon_id     = $_POST["bon_id"];
        $bid_price  = $_POST["bid_price"];
        $bid_user   = $_POST["bid_user"];

        $trade_winner = array($auction_id=>array($bon_id=>array('amount'=>$bid_price,'user_id'=>$bid_user)));

        $meta_key   = 'user_trade_bid_'.$auction_id.'_'.$bon_id;
        update_post_meta( $auction_id, $meta_key,$trade_winner );

        /* code added by AW109 start */
        $winner         = array();
        $higestAmount   = 0;
        $flag           = true;
        $bidAmounts     = array();

        $bid_key       = 'user_placed_bid_'.$auction_id.'_'.$bon_id;
        $placedBids     = get_post_meta($auction_id,$bid_key,true);

        $auction_ID     = $auction_id;
        $bond_ID        = $bon_id;
        //$reservePrice   = get_post_meta($bond_ID,'reserve_price', true);

        if (empty($placedBids)) {
            return;
        }
        foreach ($placedBids as $key => $bid ) {

            if( !isset($placedBids[$key][$auction_ID][$bond_ID]) ){
                continue;
            }
            $flag = false;

            $bidAmounts[$key] = $placedBids[$key][$auction_ID][$bond_ID];

            if( $higestAmount <  $placedBids[$key][$auction_ID][$bond_ID] ){
                $winner[$auction_ID][$bond_ID]['amount'] = $placedBids[$key][$auction_ID][$bond_ID];
                $winner[$auction_ID][$bond_ID]['user_id'] = $key;
                $higestAmount  = $placedBids[$key][$auction_ID][$bond_ID];
            }
        }

        $tmp            = array_count_values($bidAmounts);
        $cnt            = $tmp[$higestAmount];

        $auction_status = get_post_meta($auction_ID, "status", true);

        $winner_id      =   $winner[$auction_ID][$bond_ID]['user_id'];
        $seller_id      =   $auction->post_author;

        $meta_key2      = 'user_winning_bid_'.$auction_ID.'_'.$bond_ID;
        $check_winner   = metadata_exists('post', $auction_ID, $meta_key2 );

        $reservePrice   = get_post_meta($bond_ID, 'reserve_price', true);

        if( $cnt > 1 ){
            foreach ($bidAmounts as $user_id => $amount ) {
                if( $amount ==  $higestAmount ){
                   AwTradeEmailTemplates::ifTied($user_id, $seller_id, $auction_ID);
                }
            }
            /*update status */
            self::updateStatus( $auction_ID , 'tied' );
            return;
        }

        if( $higestAmount > 0 ){
            $meta_key   = 'user_winning_bid_'.$auction_ID.'_'.$bond_ID;
            $placedBids = update_post_meta($auction_ID,$meta_key, $winner);
            echo "Updated Winner For ".$auction_ID.'_'.$bond_ID."<br>";
            AwTradeEmailTemplates::ifWinner($winner_id, $seller_id, $auction_ID);
        }else{
            if ($check_winner === false) {
                AwTradeEmailTemplates::ifNoWinner($winner_id, $seller_id, $auction_ID);
            }
        }

        /*  code added by AW109 end  */

        $return     = array('auction_id'=>$auction_id,'bon_id'=>$bon_id,'bid_user'=>$bid_user);
        echo json_encode($return);
        die;
    }
}/* class ends here */

?>