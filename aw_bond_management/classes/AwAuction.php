<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AwAuction {
    /**
     * [createAuction: function to create new Auction and attach Bonds to the auction ]
     * @param  array $bondIDs
     * @return boolean
     */
    public static function createAuction( $bondIDs = null, $start_date = null, $end_date = null, $bwic_title, $max_price = 10, $min_price = 10, $fixed_price = 10 ) {
        if( !$bondIDs ){
            return false;
        }
        if( !$start_date ){
            return false;
        }
        if( !$end_date ){
            return false;
        }

        /*create new Auction */
        $duedate        = date("m-d-Y h:i A", $end_date);
        $auctionTitle   = "BWIC-".$duedate;

        $my_post = array(
            'post_title'    =>  $auctionTitle,
            'post_status'   =>  'draft',
            'post_author'   =>  get_current_user_id(),
            'post_type'     =>  'auction'
        );

        $lastid = wp_insert_post( $my_post );

        if( $lastid ){
            $auction_meta_field_value = serialize($bondIDs);
            update_post_meta($lastid,'start_date',$start_date);
            update_post_meta($lastid,'end_date',$end_date);
            update_post_meta($lastid,'max_price',$max_price);
            update_post_meta($lastid,'min_price',$min_price);
            update_post_meta($lastid,'fixed_price',$fixed_price);
            update_post_meta($lastid,'status','pending');
            update_post_meta($lastid,'_auction_meta_field_value',$bondIDs);
            update_post_meta($lastid,'bwic_title',$bwic_title);

            /*contants*/
            update_post_meta($lastid,'_start_date', 'field_5804b6f30b775');
            update_post_meta($lastid,'_end_date', 'field_5804b72c0b776');
            update_post_meta($lastid,'_max_price', 'field_5804b74f0b777');
            update_post_meta($lastid,'_min_price', 'field_5804b75c0b778');
            update_post_meta($lastid,'_fixed_price', 'field_5804b7620b779');
            update_post_meta($lastid,'_status', 'field_582033017e9d9');

            return true;
        } else {
            return false;
        }
    }

    public static function awLiveAuctions() {


        if (!is_user_logged_in()) {
            echo '<div><h2 style="text-align:center;">You are not logged in user to view this page.</h2>
            <h4 style="text-align:center;"><a href="'.site_url().'">Click here to login</a></h4></div>';
        }else{
            echo "<hr />";
            $currentTime = date('m-d-Y h:i:s A', time());

            echo '<h1>Today\'s BWICs<span style="float: right;"> Current Time: '.$currentTime.'</h1>';
            $posts_auction = self::getTodayAuctions();
            self::showListingHTML($posts_auction);

        }

    }

    public static function awUpcomingAuctions() {

        if (!is_user_logged_in()) {
            echo '<div><h2 style="text-align:center;">You are not logged in user to view this page.</h2>
            <h4 style="text-align:center;"><a href="'.site_url().'">Click here to login</a></h4></div>';
        }else{
            echo "<hr />";
            echo "<h1>Upcoming BWICs</h1>";

            $posts_auction = self::getUpcomingAuctions();
            self::showListingHTML($posts_auction);
        }
    }


    public static function getLiveAuctions() {

        $auctionID   = '';
        $bidprice    = '';
        $bids_price  = '';
        $posts_auction = get_posts(
                                array(
                                     'post_type'        =>  'auction',
                                     'posts_per_page'   =>  -1,
                                     'post_status'      =>  'publish',
                                     'meta_query'       => array(
                                            array('start_date',   )
                                        )
                                   /*  'orderby'          =>  'start_date',
                                     'order'            =>  'ASC'*/
                                     )
                        );
            /* filter posts */
            $filteredPosts = array();
            foreach ($posts_auction as $key => $row) {
                $start  = $row->start_date;
                $end    = $row->end_date;


                if ((time() < $end && time() > $start)){
                    $filteredPosts[] = $row;
                }
            }

            $posts_auction = $filteredPosts;

            foreach ($posts_auction as $key => $row) {
                $volume[$key]   = $row->start_date;
                $edition[$key]  = $row->end_date;
            }
            array_multisort($volume, SORT_ASC, $edition, SORT_ASC, $posts_auction);

            return  $posts_auction;
    }

    public static function getTodayAuctions() {

        $auctionID      = '';
        $bidprice       = '';
        $bids_price     = '';
        $posts_auction  = get_posts(
                              array(
                                     'post_type'        =>  'auction',
                                     'posts_per_page'   =>  -1,
                                     'post_status'      =>  'publish',
                                     'meta_query'       => array(
                                            array('start_date')
                                        ),
                                    /* 'orderby'          =>  'start_date',
                                     'order'            =>  'ASC'*/
                                     )
                        );
            /* filter posts */
            $filteredPosts = array();
            foreach ($posts_auction as $key => $row) {
                $start          = $row->start_date;
                $end            = $row->end_date;
                $status         = $row->status;

                $cur_datee      = date('Y-m-d');
                $start_datee    = date('Y-m-d', $start);
                $end_datee      = date('Y-m-d', $end);

                //if ((time() < $end && time() > $start)){
                if( ($cur_datee == $end_datee &&  $status == 'pending') ){
                    $filteredPosts[] = $row;
                }

                //}
            }

            $posts_auction = $filteredPosts;

            if( $posts_auction ){
                foreach ($posts_auction as $key => $row) {
                    $volume[$key]   = $row->start_date;
                    $edition[$key]  = $row->end_date;
                }
                array_multisort($volume, SORT_ASC, $edition, SORT_ASC, $posts_auction);
            }

            return  $posts_auction;
    }


    public static function getUpcomingAuctions() {

        $auctionID   = '';
        $bidprice    = '';
        $bids_price  = '';
        $posts_auction = get_posts(
                                array(
                                     'post_type'        =>  'auction',
                                     'posts_per_page'   =>  -1,
                                     'post_status'      =>  'publish',
                                     'meta_query'       => array(
                                            array('start_date' )
                                        )
                                     /*'orderby'          =>  'start_date',
                                     'order'            =>  'ASC'*/
                                     )
                        );
            /* filter posts */
            $filteredPosts = array();
            foreach ($posts_auction as $key => $row) {
                $start  = $row->start_date;

                $cur_datee = date('Y-m-d');
                $start_datee = date('Y-m-d', $start);

                if( ($cur_datee < $start_datee ) ){
                    $filteredPosts[] = $row;
                }
            }

            $posts_auction = $filteredPosts;

            if ($posts_auction) {
                foreach ($posts_auction as $key => $row) {
                    $volume[$key]   = $row->start_date;
                    $edition[$key]  = $row->end_date;
                }
                array_multisort($volume, SORT_ASC, $edition, SORT_ASC, $posts_auction);
            }

            return  $posts_auction;
    }

    public static function showListingHTMLTied($posts_auction){

             ?>
            <!-- $html = "<table class='bond-listing'><tr>"; -->
            <div class="container-fluid bond-listing"> <?php
            
            foreach ($posts_auction as $key => $value) {

                $date_time1      = get_field('end_date', $value->ID, true);
                $date_time       = get_field('end_date', $value->ID, true);
                $split_date_time = explode(" ", $date_time);
                $due_date        = $split_date_time[0];
                $date_for        = new DateTime($due_date);
                $due_date2       = $date_for->format('m/d/Y');
                $fin_date        = str_replace("/", "-", $due_date2);
                $time            = $split_date_time[1]." ".$split_date_time[2];

                $start_daten_time= get_field('start_date', $value->ID, true);

                $auction_meta_id = get_post_meta($value->ID,"_auction_meta_field_value",true);
                $end_date        = get_field('end_date', $value->ID, true);
                $split_end_date  = explode(" ", $end_date);
                $due_end_date    = trim($split_end_date[0]);
                $fin_end_date    = str_replace("/", "-", $due_end_date);
                $fin_end_time    = trim($split_end_date[1]);

                $fin_end_timee   = explode('/', $split_end_date[0]);
                $fin_end_timeee  = explode(':', $split_end_date[1]);
                $fin_end_timeeee = explode(' ', $split_end_date[2]);

                $auctionID = $value->ID;
                $auction_title = $value->post_title;

                ?>
               
                <table class="bwic-table table-front" id='post_<?php echo $value->ID; ?>'>
                    <thead>
                        <tr class="clickable" data-target="#accordion-<?php echo $value->ID; ?>" data-toggle="collapse">
                            <th colspan="5" style="text-align: left;">
                                <!--<a href="#data_<?php //echo $value->ID; ?>" >-->
                                <a href="#">
                                    <i class="fa fa-angle-double-down" aria-hidden="true"></i>
                                    <span><?php echo $auction_title; ?></span>
                                </a>
                            </th>
                            <th colspan="5" style="text-align: right;">
                                <!--<a href="#data_<?php //echo $value->ID; ?>" > -->
                                <a href="#"> 
                                    <span><?php echo $fin_date." @".$time; ?></span>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="accordion-<?php echo $value->ID; ?>" class="collapse">
                        <tr id="data_<?php echo $value->ID; ?>">
                            <th>List ID</th>
                            <th>Type</th>
                            <th>Orig Rating</th>
                            <th>CU SIP/ISIN</th>
                            <th>TICKER</th>
                            <th>Orig Size(MM)</th>
                            <th>Curr Size(MM)</th>
                            <th>C/E</th>
                            <th>Manager</th>
                            <th>Bid</th>
                        </tr>
                        <?php
                            $awAuctionID = $value->ID;
                            foreach ($auction_meta_id as $value2) {
                                
                                $tiedBond = get_post_meta($value2,'status',true);
                                
                                if ($tiedBond != 'tied') {
                                    continue;
                                }

                                $list_id        = get_post_meta($value2,"list_id",true);
                                $type           = get_post_meta($value2,"type",true);
                                $orig_rating    = get_post_meta($value2,"orig_rating",true);
                                $cusip_isin     = get_post_meta($value2,"cusip/isin",true);
                                $ticker         = get_post_meta($value2,"ticker",true);
                                $orig_size      = get_post_meta($value2,"orig_size_(mm)",true);
                                $curr_size      = get_post_meta($value2,"curr_size_(mm)",true);
                                $c_e            = get_post_meta($value2,"c/e",true);
                                $manager        = get_post_meta($value2,"manager",true);
                                $auction_id_val = $auctionID;


                                $current_user       = wp_get_current_user();
                                $current_user_id    = $current_user->ID;

                                $meta_key   = 'user_placed_bid_'.$awAuctionID.'_'.$value2;
                                $get_place_bid      = get_post_meta($auctionID, $meta_key, true);
                                // $unserial_place_bid = unserialize($get_place_bid);

                                $bids_price = $get_place_bid[$current_user_id][$awAuctionID][$value2];

                                if (!$bids_price) {
                                    $bids_price = '';
                                }

                                //if((!in_array($current_user_id, $user_value)) || (!in_array($value2, $bond_value)) ){

                                $newDate        = date("m-d-Y h:i A", strtotime($date_time1));
                                $extr_dnt       = explode(" ", $newDate);
                                $final_date     = trim($extr_dnt[0]);
                                $final_time     = trim($extr_dnt[1]." ".$extr_dnt[2]);
                        ?>
                        <tr class="bond_details all_detail all_detail_<?php echo $value2; ?>">
                                   
                            <th class='even_col2'><?php echo $value2; ?></th>
                            <th class='odd_col2'><?php echo $type; ?></th>
                            <th class='even_col2'><?php echo $orig_rating; ?></th>
                            <th class='odd_col2'><?php echo $cusip_isin; ?></th>
                            <th class='even_col2'><?php echo $ticker; ?></th>
                            <th class='odd_col2'><?php echo $orig_size; ?></th>
                            <th class='even_col2'><?php echo $curr_size; ?></th>
                            <th class='odd_col2'><?php echo $c_e; ?></th>
                            <th class='even_col2'><?php echo $manager; ?></th>
                            <th class='odd_col2'>
                                <a href='#' class='create_bid btn-default btn' data-toggle='modal' data-target='#header_pop_desk' data-bon_id='<?php echo $value2; ?>' data-bon_orig='<?php echo $orig_size; ?>' data-auc_id='<?php echo $auctionID ?>' data-bon_title='<?php echo get_the_title($value2) ?>' data-bon_ticker='<?php echo $ticker; ?>' data-bon_dnt='<?php echo $final_date ." @".$final_time ?>' data-bid_price='<?php echo $bids_price ?>'>
                                        <?php echo ($bids_price == '' ? 'BID' : 'Edit Bid') ?>
                                </a>
                            </th>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
                <?php
                }
                ?>
            </div>
            <div class='modal fade' id='header_pop_desk' role='dialog' style='z-index: 1001;'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h2 class='modal-title'></h2>
                            <div class='modal-extra'>
                                <div style='float:left;'>
                                    <span class='modal_ticker'></span><br>
                                    <span class='modal_orig'></span>
                                </div>
                                <div style='float:right;' class='modal_dnt'></div>
                                <div style='clear:both;'></div>
                            </div>
                        </div>
                        <div class='modal-body'>
                            <div class='bid_success_msg'>
                                <h2>Bid has been placed successfully.</h2>
                            </div>
                            <div class='place_bid'>
                                <div class='bid_price_field'>
                                    <input type='text' name='bid_price' id='bid_price' placeholder='(Bid in Price terms.  For example, 85.00 or 85.25)' size="50">
                                </div>
                                <div class='loader-img' style='text-align:center;'>
                                    <img src='<?php echo site_url(); ?>/wp-content/plugins/auction-custom-fields/images/loader_img.gif' style="display: none;">
                                </div>
                                <div class='bid_btn_field'>
                                    <input type='button' value='Place Bid' name='aw_place_bid_btn' id='aw_place_bid_btn' class="btn bid_btn btn-primary">
                                    <input type='hidden' value='' name='auct_id' id='auct_id'>
                                    <input type='hidden' value='' name='bon_id' id='bon_id'>
                                </div>
                            </div>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
     
    }

     public static function showListingHTML( $posts_auction ){
             ?>
            <!-- $html = "<table class='bond-listing'><tr>"; -->
            <div class="container-fluid bond-listing"> <?php
            
            foreach ($posts_auction as $key => $value) {

                $date_time1      = get_field('end_date', $value->ID, true);
                $date_time       = get_field('end_date', $value->ID, true);
                $split_date_time = explode(" ", $date_time);
                $due_date        = $split_date_time[0];
                $date_for        = new DateTime($due_date);
                $due_date2       = $date_for->format('m/d/Y');
                $fin_date        = str_replace("/", "-", $due_date2);
                $time            = $split_date_time[1]." ".$split_date_time[2];

                $start_daten_time= get_field('start_date', $value->ID, true);

                $auction_meta_id = get_post_meta($value->ID,"_auction_meta_field_value",true);
                $end_date        = get_field('end_date', $value->ID, true);
                $split_end_date  = explode(" ", $end_date);
                $due_end_date    = trim($split_end_date[0]);
                $fin_end_date    = str_replace("/", "-", $due_end_date);
                $fin_end_time    = trim($split_end_date[1]);

                $fin_end_timee   = explode('/', $split_end_date[0]);
                $fin_end_timeee  = explode(':', $split_end_date[1]);
                $fin_end_timeeee = explode(' ', $split_end_date[2]);

                $auctionID = $value->ID;
                $auction_title = $value->post_title;

                ?>
               
                <table class="bwic-table table-front" id='post_<?php echo $value->ID; ?>'>
                    <thead>
                        <tr class="clickable" data-target="#accordion-<?php echo $value->ID; ?>" data-toggle="collapse">
                            <th colspan="5" style="text-align: left;">
                                <!--<a href="#data_<?php //echo $value->ID; ?>" >-->
                                <a href="#">
                                    <i class="fa fa-angle-double-down" aria-hidden="true"></i>
                                    <span><?php echo $auction_title; ?></span>
                                </a>
                            </th>
                            <th colspan="5" style="text-align: right;">
                                <!--<a href="#data_<?php //echo $value->ID; ?>" > -->
                                <a href="#"> 
                                    <span><?php echo $fin_date." @".$time; ?></span>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="accordion-<?php echo $value->ID; ?>" class="collapse">
                        <tr id="data_<?php echo $value->ID; ?>">
                            <th>List ID</th>
                            <th>Type</th>
                            <th>Orig Rating</th>
                            <th>CU SIP/ISIN</th>
                            <th>TICKER</th>
                            <th>Orig Size(MM)</th>
                            <th>Curr Size(MM)</th>
                            <th>C/E</th>
                            <th>Manager</th>
                            <th>Bid</th>
                        </tr>
                        <?php
                            $awAuctionID = $value->ID;
                            foreach ($auction_meta_id as $value2) {

                                $list_id        = get_post_meta($value2,"list_id",true);
                                $type           = get_post_meta($value2,"type",true);
                                $orig_rating    = get_post_meta($value2,"orig_rating",true);
                                $cusip_isin     = get_post_meta($value2,"cusip/isin",true);
                                $ticker         = get_post_meta($value2,"ticker",true);
                                $orig_size      = get_post_meta($value2,"orig_size_(mm)",true);
                                $curr_size      = get_post_meta($value2,"curr_size_(mm)",true);
                                $c_e            = get_post_meta($value2,"c/e",true);
                                $manager        = get_post_meta($value2,"manager",true);
                                $auction_id_val = $auctionID;


                                $current_user       = wp_get_current_user();
                                $current_user_id    = $current_user->ID;

                                $meta_key   = 'user_placed_bid_'.$awAuctionID.'_'.$value2;
                                $get_place_bid      = get_post_meta($auctionID, $meta_key, true);
                                // $unserial_place_bid = unserialize($get_place_bid);

                                $bids_price = $get_place_bid[$current_user_id][$awAuctionID][$value2];

                                if (!$bids_price) {
                                    $bids_price = '';
                                }

                                //if((!in_array($current_user_id, $user_value)) || (!in_array($value2, $bond_value)) ){

                                $newDate        = date("m-d-Y h:i A", strtotime($date_time1));
                                $extr_dnt       = explode(" ", $newDate);
                                $final_date     = trim($extr_dnt[0]);
                                $final_time     = trim($extr_dnt[1]." ".$extr_dnt[2]);
                        ?>
                        <tr class="bond_details all_detail all_detail_<?php echo $value2; ?>">
                                   
                            <th class='even_col2'><?php echo $value2; ?></th>
                            <th class='odd_col2'><?php echo $type; ?></th>
                            <th class='even_col2'><?php echo $orig_rating; ?></th>
                            <th class='odd_col2'><?php echo $cusip_isin; ?></th>
                            <th class='even_col2'><?php echo $ticker; ?></th>
                            <th class='odd_col2'><?php echo $orig_size; ?></th>
                            <th class='even_col2'><?php echo $curr_size; ?></th>
                            <th class='odd_col2'><?php echo $c_e; ?></th>
                            <th class='even_col2'><?php echo $manager; ?></th>
                            <th class='odd_col2'>
                                <a href='#' class='create_bid btn-default btn' data-toggle='modal' data-target='#header_pop_desk' data-bon_id='<?php echo $value2; ?>' data-bon_orig='<?php echo $orig_size; ?>' data-auc_id='<?php echo $auctionID ?>' data-bon_title='<?php echo get_the_title($value2) ?>' data-bon_ticker='<?php echo $ticker; ?>' data-bon_dnt='<?php echo $final_date ." @".$final_time ?>' data-bid_price='<?php echo $bids_price ?>'>
                                        <?php echo ($bids_price == '' ? 'BID' : 'Edit Bid') ?>
                                </a>
                            </th>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
                <?php
                }
                ?>
            </div>
            <div class='modal fade' id='header_pop_desk' role='dialog' style='z-index: 1001;'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h2 class='modal-title'></h2>
                            <div class='modal-extra'>
                                <div style='float:left;'>
                                    <span class='modal_ticker'></span><br>
                                    <span class='modal_orig'></span>
                                </div>
                                <div style='float:right;' class='modal_dnt'></div>
                                <div style='clear:both;'></div>
                            </div>
                        </div>
                        <div class='modal-body'>
                            <div class='bid_success_msg'>
                                <h2>Bid has been placed successfully.</h2>
                            </div>
                            <div class='place_bid'>
                                <div class='bid_price_field'>
                                    <input type='text' name='bid_price' id='bid_price' placeholder='(Bid in Price terms.  For example, 85.00 or 85.25)' size="50">
                                </div>
                                <div class='loader-img' style='text-align:center;'>
                                    <img src='<?php echo site_url(); ?>/wp-content/plugins/auction-custom-fields/images/loader_img.gif' style="display: none;">
                                </div>
                                <div class='bid_btn_field'>
                                    <input type='button' value='Place Bid' name='aw_place_bid_btn' id='aw_place_bid_btn' class="btn bid_btn btn-primary">
                                    <input type='hidden' value='' name='auct_id' id='auct_id'>
                                    <input type='hidden' value='' name='bon_id' id='bon_id'>
                                </div>
                            </div>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
     }


    public static function getAuctionsByUser( $user_id = null ){


        global $current_user;
        if( $user_id == null ){
            $current_user   = wp_get_current_user();
            //$user_id        = $current_user->ID;
            $user_id        = get_current_user_id();
        }

        $auctionID   = '';
        $bidprice    = '';
        $bids_price  = '';
        $posts_auction = get_posts(
                                array(
                                    'post_type'        =>  'auction',
                                    'posts_per_page'   =>  -1,
                                    'post_status'      =>  'publish',
                                    'author'           =>  $current_user->ID,
                                    'orderby'          =>  'ID',
                                    'order'            =>  'DESC',
                                    'meta_query'       => array(
                                            array('start_date' )
                                       ),
                                     )
                        );

            // foreach ($posts_auction as $key => $row) {
            //     $volume[$key]   = $row->start_date;
            //     $edition[$key]  = $row->end_date;
            // }
            // array_multisort($volume, SORT_ASC, $edition, SORT_ASC, $posts_auction);

            return  $posts_auction;

    }
    public static function tiedAuctions(){

        $posts_auction = self::getTiedAuctions();
        self::showListingHTMLTied($posts_auction);
    }

    public static function getTiedAuctions() {

        $auctionID   = '';
        $bidprice    = '';
        $bids_price  = '';
        $posts_auction = get_posts(
                              array(
                                     'post_type'        =>  'auction',
                                     'posts_per_page'   =>  -1,
                                     'post_status'      =>  'publish',
                                     'meta_query'       => array(
                                            array(
                                                'key'   => 'status',
                                                'value' => 'tied',
                                            )
                                        ),
                                     )
                        );

        /* filter posts */
        $filteredPosts = array();
        foreach ($posts_auction as $key => $row) {
            $start  = $row->start_date;
            $end    = $row->end_date;
            $status = $row->status;

            $cur_datee = date('Y-m-d');
            $start_datee = date('Y-m-d', $start);
            $ends =  date('Y-m-d H:i',$end);
            $minutes_to_add = 15;
            $time           = new DateTime($ends);
            
            $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
            $stamp = $time->format('Y-m-d H:i');
            
            if (time() > $stamp){
                $filteredPosts[] = $row;
            }

        }
        
        $posts_auction = $filteredPosts;

        if( $posts_auction ){
            foreach ($posts_auction as $key => $row) {
                $volume[$key]   = $row->start_date;
                $edition[$key]  = $row->end_date;
            }
            array_multisort($volume, SORT_ASC, $edition, SORT_ASC, $posts_auction);
        }

        return  $posts_auction;
    }



}/* class ends here */

?>
