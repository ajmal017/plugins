<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../../wp-load.php';
require 'AwEmailNotifications.php';

class AwCronJob {

    public static  function getAutions( ) {
        echo date('Y-m-d h:i:s a')."<br />";

        $auctionID     = '';
        $bidprice      = '';
        $bids_price    = '';
        $startDateTime = time();
        $posts_auction = get_posts(
                                array(
                                     'post_type'        =>  'auction',
                                     'posts_per_page'   =>  -1,
                                     'post_status'      =>  'publish',
                                     'meta_query' => array(
                                            'relation' => 'AND',
                                            array(
                                                'key' => 'end_date',
                                                'value' => time(),
                                                'compare' => '<',
                                            ),
                                            array(
                                                'key' => 'status',
                                                'value' => 'pending'
                                            ),
                                        ),
                                    )
                        );


            /* filter posts */
            $filteredPosts = array();
            foreach ($posts_auction as $key => $row) {
                $start  = $row->start_date;
                $end    = $row->end_date;
                $status = $row->status;

                echo "<br> End Time ".date('Y-m-d h:i:s a',$end)."<br>";
                
                if ((time() > $end && $status == 'pending')) {
                    $filteredPosts[] = $row;
                }
            }
            $posts_auction = $filteredPosts;

            return  $posts_auction;
    }

     public static  function getTiedAutions( ) {
        $auctionID     = '';
        $bidprice      = '';
        $bids_price    = '';
        $startDateTime = time();
        $posts_auction = get_posts(
                                array(
                                     'post_type'        =>  'auction',
                                     'posts_per_page'   =>  -1,
                                     'post_status'      =>  'publish',
                                     'meta_query' => array(
                                            'relation' => 'AND',
                                            array(
                                                'key' => 'end_date',
                                                'value' => time(),
                                                'compare' => '<',
                                            ),
                                            array(
                                                'key' => 'status',
                                                'value' => 'tied'
                                            ),
                                        ),
                                    )
                        );

            /* filter posts */
            $filteredPosts = array();
            foreach ($posts_auction as $key => $row) {
                $start  = $row->start_date;
                $end    = $row->end_date;
                $status = $row->status;

                //$endTime = date('Y-m-d H:i',strtotime('+15 minutes',$end));

                if ((time() > $end && $status == 'tied')) {

                    $filteredPosts[] = $row;
                }
            }
            $posts_auction = $filteredPosts;

            return  $posts_auction;
    }



    public static  function processAuctions() {
        $postAuctions   = self::getAutions();

        if( empty($postAuctions)){
            echo "<p>No Auction to process</p>";
        }

        foreach($postAuctions as $auction ) {
            echo "<h1>Auction ID: ".$auction->ID."</h1>";
            AwCronJob::processPendingAuctions( $auction );
        }
    }

    /******************************* Section addded 08.12.2016 ***************************************/

    public static  function processPendingAuctions( $auction ) {

        $status         = array();
        $bonds          = get_post_meta($auction->ID, '_auction_meta_field_value', true);
        $auction_ID     = $auction->ID;

        foreach ( $bonds as $bondID ) {
            $tempStatus = AwCronJob::processPendingBonds( $auction->ID, $bondID );
            $status[$bondID]   = $tempStatus;
        }

        $flagTied = false;

        foreach ($status as $bond_ID => $bondStatus) {

            if( $bondStatus['status'] == 'tied' ){
                $flagTied = true;

                $tiedusers = $bondStatus['tiedusers'];
                AwEmailNotifications::sendNotificationToTied( $auction_ID, $bond_ID, $tiedusers );
            }else if( $bondStatus['status'] == 'winner' ){

                update_post_meta($auction_ID,'winning_user', $bondStatus['winner'][$auction->ID][$bond_ID]['user_id']);

                $meta_key   = 'user_winning_bid_'.$auction_ID.'_'.$bond_ID;
                update_post_meta($auction_ID,$meta_key, $bondStatus['winner']);

                // $winnerID = $bondStatus['winner'][$auction->ID][$bond_ID]['user_id'];
                // AwEmailNotifications::sendNotificationToWinner( $auction->ID, $bond_ID, $winnerID );

                // $biddingAmounts =   $bondStatus['bidAmounts'];
                // $coverPrice     =   AwCronJob::getCoverPrice( $biddingAmounts );
                // AwEmailNotifications::sendNotificationToLosers( $auction->ID, $bond_ID, $winnerID, $coverPrice, $biddingAmounts );
            }

        }


        if( $flagTied ){
            /*update auction status to tied*/
            AwCronJob::updateStatus( $auction->ID ,'tied' );
            AwCronJob::updateEndTime( $auction->ID );
            
        }else{
            echo "<p> Sending Notifications1</p>";
            /*update auction status to completed*/
            AwCronJob::updateStatus( $auction->ID );

            /* Send Notifications. */
            AwEmailNotifications::sendNotificationSummarySeller($auction);
            AwEmailNotifications::sendNotificationSummaryAdmin($auction);

            $bidusers = AwCronJob::getBidUsers( $status );
            foreach ($bidusers as $key => $user_id) {
                AwEmailNotifications::sendNotificationSummaryUsers($auction, $user_id);
            }
        }

        echo "<br/> Updated Auction: ".$auction->ID;
        return true;
    }

    public static function getCoverPrice( $biddingAmounts ){
        $numbers = array_unique($biddingAmounts);
        rsort($numbers);
        return $numbers[1];
    }


    public static  function processPendingBonds( $auctionID, $bondID ) {

        $bids           = AwCronJob::getUserPlacedBids( $auctionID, $bondID );
        $reservePrice   = AwCronJob::getBondReservePrice( $auctionID, $bondID );

        $winner         = array();
        $higestAmount   = 0;
        $flag           = true;
        $bidAmounts     = array();

        if( $reservePrice ){
            $result = AwCronJob::processBidsItems( $auctionID, $bondID, $reservePrice, $bids );

            /*return bond status */
            return $result;
        }else{
            echo " Don't Process: $auctionID | $bondID <br> ";
            /*No Bids Placed() */
            $returnData  = AwCronJob::processTradedBidsItems($auctionID, $bondID );
            return $returnData;
        }

        return true;
    }

    public static  function processTradedBidsItems( $auctionID, $bondID) {

        $meta_key  =   'user_trade_bid_'.$auctionID.'_'.$bondID;
        $result    =   get_post_meta($auctionID, $meta_key, true);

        $meta_key           =   'user_placed_bid_'.$auctionID.'_'.$bondID;
        $user_placed_bid    =   get_post_meta($auctionID, $meta_key, true);

        if( !empty( $result ) ){
            $returnData['status']       = 'winner';
            $returnData['winner']       = $result;
            $returnData['higestAmount'] = $result[$auctionID][$bondID]['amount'];
            $returnData['bidAmounts']   = $user_placed_bid;
        }else{
            $returnData['status']       = 'DNT';
            $returnData['winner']       = array();
            $returnData['higestAmount'] = 0;
            $returnData['bidAmounts']   = array();
        }

        return $returnData;

    }

    public static  function processBidsItems( $auctionID, $bondID, $reservePrice,  $bids ) {

        $winner         = array();
        $higestAmount   = 0;
        $bidAmounts     = array();
        $flag           = true;

        $returnData     = array();

        foreach ($bids as $key => $bid ) {

            if( !isset($bids[$key][$auctionID][$bondID]) ){
                continue;
            }

            $bidAmounts[$key] = $bids[$key][$auctionID][$bondID];

            /* check if the bid amount is greater than reservePrice */
            if( $bids[$key][$auctionID][$bondID] >=  $reservePrice ){
                if( $higestAmount <  $bids[$key][$auctionID][$bondID] ){
                    $winner[$auctionID][$bondID]['amount']  = $bids[$key][$auctionID][$bondID];
                    $winner[$auctionID][$bondID]['user_id'] = $key;
                    $higestAmount  = $bids[$key][$auctionID][$bondID];
                }
            }
        }

        /*check if there is tie */
        $checkTired = AwCronJob::checkTied( $bidAmounts, $higestAmount, $reservePrice  );

        if( !$checkTired ){
            /*initialize return data */
            $returnData['status']       = AwCronJob::getBidStatus($winner);
            $returnData['winner']       = $winner;
            $returnData['higestAmount'] = $higestAmount;
            $returnData['bidAmounts']   = $bidAmounts;
        }else{
            $returnData['status']       = 'tied';
            $returnData['winner']       = array();
            $returnData['tiedusers']    = $checkTired;
            $returnData['higestAmount'] = $higestAmount;
            $returnData['bidAmounts']   = $bidAmounts;
        }

        /*Check if no bid placed*/
        if( empty($bidAmounts) ){
            $returnData['status']       = 'DNT';
            $returnData['winner']       = array();
            $returnData['higestAmount'] = $higestAmount;
            $returnData['bidAmounts']   = $bidAmounts;
        }

        AwCronJob::setBondStatus($bondID, $returnData['status'] );

        /*update Bond status */
        return $returnData;
    }

    public static  function checkTied( $bidAmounts, $higestAmount, $reservePrice  ) {
        $tmp            = array_count_values($bidAmounts);
        $cnt            = $tmp[$higestAmount];

        if( $cnt > 1  && $reservePrice < $higestAmount){
            return $tiedUsers      =   array_keys($bidAmounts,$higestAmount);
        }else{
            return false;
        }
    }

    public static  function getBidStatus( $winner ) {
        if (empty($winner)) {
            return 'loser';
        }else{
            return 'winner';
        }
    }

    public static  function getUserPlacedBids( $auctionID, $bondID ) {
        $auction_ID     = $auctionID;
        $bond_ID        = $bondID;

        $meta_key       = 'user_placed_bid_'.$auction_ID.'_'.$bond_ID;
        $placedBids     = get_post_meta($auctionID,$meta_key,true);

        if (empty($placedBids)) {
            return false;
        }else{
            return $placedBids;
        }
    }

    public static  function getBondReservePrice( $auctionID, $bondID ) {
        $auction_ID     = $auctionID;
        $bond_ID        = $bondID;

        $reservePrice   = get_post_meta($bond_ID,'reserve_price', true);

        if ( ($reservePrice == '' ||  $reservePrice == 0) ) {
            return false;
        }else{
            return $reservePrice;
        }
    }

    public static  function isBidValid( $reservePrice, $bidPrice ) {
        if(  $bidPrice > $reservePrice ){
            return true;
        }else{
            return false;
        }
    }

    public static  function hasBondWinner( $auctionID, $bondID ) {}

    public static  function setBondStatus( $bondID, $status = 'winner' ) {
        update_post_meta($bondID , 'status', $status);
    }

    /******************************* Section added 08.12.2016 end ***********************************/

    /******************************* Section for Tied auctions added 08.12.2016 start ***********************************/
    public static  function processTiedAuctions() {
        $getTiedAutions = self::getTiedAutions();

        if( empty($getTiedAutions)){
            echo "<p>No Tied Auction to process</p>";
        }

        foreach($getTiedAutions as $auction ) {
            echo "<h1>Auction ID: ".$auction->ID."</h1>";
            AwCronJob::processTiedAuctionsPosts( $auction );
        }
    }

    public static  function processTiedAuctionsPosts( $auction ) {

        $status         = array();
        $bonds          = get_post_meta($auction->ID, '_auction_meta_field_value', true);
        $auction_ID     = $auction->ID;

        foreach ( $bonds as $bondID ) {

            if(!(AwCronJob::checkIfTiredBond( $bondID ))){
                continue;
            }

            $tempStatus = AwCronJob::processTiedBonds( $auction->ID, $bondID );
            $status[$bondID]   = $tempStatus;
        }


        foreach ($status as $bond_ID => $bondStatus) {

            if( $bondStatus['status'] == 'winner' ){

                update_post_meta($auction_ID,'winning_user', $bondStatus['winner'][$auction->ID][$bond_ID]['user_id']);
                $meta_key   = 'user_winning_bid_'.$auction_ID.'_'.$bond_ID;
                update_post_meta($auction_ID,$meta_key, $bondStatus['winner']);

              /*  $winnerID = $bondStatus['winner'][$auction->ID][$bond_ID]['user_id'];
                AwEmailNotifications::sendNotificationToWinner( $auction->ID, $bond_ID, $winnerID );


                $biddingAmounts =   $bondStatus['bidAmounts'];
                $coverPrice     =   AwCronJob::getCoverPrice( $biddingAmounts );
                AwEmailNotifications::sendNotificationToLosers( $auction->ID, $bond_ID, $winnerID, $coverPrice, $biddingAmounts );*/
            }
        }

        /* Mark Auction as completed. */
        AwCronJob::updateStatus( $auction->ID );

        /* Send Notifications. */
        AwEmailNotifications::sendNotificationSummarySeller($auction);
        AwEmailNotifications::sendNotificationSummaryAdmin($auction);

        $bidusers = AwCronJob::getBidUsers( $status );
        foreach ($bidusers as $key => $user_id) {
            AwEmailNotifications::sendNotificationSummaryUsers($auction, $user_id);
        }

        echo "<br/> Updated Tied Auction: ".$auction->ID;
        return true;
    }

    public static function updateEndTime( $auctionID ){
        /* add 15 minutest to due date*/
        $dueTime    = get_post_meta($auctionID,'end_date',true);            
        $endTime    = date('Y-m-d H:i:s',strtotime('+15 minutes',$dueTime));
        $updateTime = strtotime($endTime);
        update_post_meta($auctionID , 'end_date', $updateTime);
    }

    public static  function checkIfTiredBond( $bondID ) {
        $status =  get_post_meta($bondID , "status", true);

        if( $status == 'tied' ){
            return true;
        }else{
            return false;
        }
    }

    public static  function processTiedBonds( $auctionID, $bondID ) {

        $bids           = AwCronJob::getUserPlacedBids( $auctionID, $bondID );
        $reservePrice   = AwCronJob::getBondReservePrice( $auctionID, $bondID );

        $winner         = array();
        $higestAmount   = 0;
        $flag           = true;
        $bidAmounts     = array();

        if( $reservePrice ){
            // echo "Reserve set Process: $auctionID | $bondID <br>";
            $result = AwCronJob::processTiedBidsItems( $auctionID, $bondID, $reservePrice, $bids );

            /*return bond status */
            return $result;
        }else{
            /*No Bids Placed() */
            $returnData  = AwCronJob::processTradedBidsItems($auctionID, $bondID );
            return $returnData;
        }

        return true;
    }


    public static  function processTiedBidsItems( $auctionID, $bondID, $reservePrice,  $bids ) {

        $winner         = array();
        $higestAmount   = 0;
        $bidAmounts     = array();
        $flag           = true;

        $returnData     = array();

        foreach ($bids as $key => $bid ) {

            if( !isset($bids[$key][$auctionID][$bondID]) ){
                continue;
            }

            $bidAmounts[$key] = $bids[$key][$auctionID][$bondID];

            /* check if the bid amount is greater than reservePrice */
            if( $bids[$key][$auctionID][$bondID] >  $reservePrice ){
                if( $higestAmount <  $bids[$key][$auctionID][$bondID] ){
                    $winner[$auctionID][$bondID]['amount']  = $bids[$key][$auctionID][$bondID];
                    $winner[$auctionID][$bondID]['user_id'] = $key;
                    $higestAmount  = $bids[$key][$auctionID][$bondID];
                }
            }
        }

        /*check if there is tie */
        $checkTired = AwCronJob::checkTied( $bidAmounts, $higestAmount, $reservePrice  );

        if( !$checkTired ){
            /*initialize return data */
            $returnData['status']       = AwCronJob::getBidStatus($winner);
            $returnData['winner']       = $winner;
            $returnData['higestAmount'] = $higestAmount;
            $returnData['bidAmounts']   = $bidAmounts;
        }else{
            $returnData['status']       = 'DNT';
            $returnData['winner']       = array();
            $returnData['higestAmount'] = $higestAmount;
            $returnData['bidAmounts']   = $bidAmounts;
        }

        /*Check if no bid placed*/
        if( empty($bidAmounts) ){
            $returnData['status']       = 'DNT';
            $returnData['winner']       = array();
            $returnData['higestAmount'] = $higestAmount;
            $returnData['bidAmounts']   = $bidAmounts;
        }

        AwCronJob::setBondStatus($bondID, $returnData['status'] );

        /*update Bond status */
        return $returnData;
    }


    /******************************* Section for Tied auctions added 08.12.2016 ends ************************************/


    public static  function checkIfTired( $auctionID ) {
        $auction_status = get_post_meta($auctionID , "status", true);

        if( $auction_status == 'tied' ){
            return true;
        }else{
            return false;
        }
    }

    public static  function processTiredAuctions( $auction ) {}

    public static  function updateStatus( $auctionID , $status =  'completed' ) {
        echo "Updated status: $auctionID to $status <br />";
        update_post_meta($auctionID,'status',$status);
    }

    public static function makeWinner(  $auction )  {}

    public static function getBidUsers( $result )  {
        $biddingUserIDs  = array();
        $biddingUsers    = array();

        foreach ($result as $key => $bids ) {
            $biduser = $bids['bidAmounts'];
            foreach ($biduser as $key => $user_id ) {
                $biddingUserIDs[] = $key;
            }
        }
        $biddingUsers = array_unique($biddingUserIDs);
        return $biddingUsers;
    }


}/* class ends here */

// $AwCronJob = new AwCronJob;


// echo "<pre>";
// print_r( $AwCronJob->processAuctions() );
// echo "</pre>";

?>
