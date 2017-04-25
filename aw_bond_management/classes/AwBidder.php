<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AwBidder {

    public static function bidHistory(){

        if (!is_user_logged_in()) {
        	echo "<h2>You are not logged in user. Please login.</h2>";
        }else{
            $args   = array(
                    'post_type'     => 'auction',
                    'post_per_page' => -1,
                    'meta_query'    => array(
                            array( 
                                    'key' => 'status', 
                                    'value' => 'completed', 
                                )
                        )
                    );
            $posts  = get_posts($args);

            echo self::getHTML( $posts );
            die;
        }

    }

    public static function getHTML( $postData ){
        $userID             = get_current_user_id();
        $user_placed_bid    = get_user_meta($userID, 'user_placed_bid', true);
        $auction_bid_placed = array_keys( $user_placed_bid);

        $html  = '';
        $html .= '<table class="table-front" cellpadding="0" cellspacing="0">';
        $html .=    '<thead class="thead-inverse">';
        $html .=        '<tr class="success">';
        $html .=           '<th>BWIC Title</th>';
        $html .=           '<th>Start Date</th>';
        $html .=           '<th>Due Date and Time</th>';
        $html .=           '<th>Status</th>';
        $html .=        '</tr>';
        $html .=    '</thead>';
        $html .=    '<tbody>';


        foreach ($postData as $auction) {
            if (!in_array($auction->ID, $auction_bid_placed) ) {
                continue;
            }
            $html .=    '<tr data-toggle="collapse" data-target="#accordion-'.$auction->ID.'" class="clickable">';
            $html .=        '<td>';
            $html .=			'<a style="cursor:pointer; font-weight:bold;">'.$auction->post_title.'</a>';
            $html .=		'</td>';
            $html .=        '<td>'.date('m-d-Y h:i A', $auction->start_date).'</td>';
            $html .=        '<td>'.date('m-d-Y h:i A', $auction->end_date).'</td>';
            $html .=        '<td><strong>'.$auction->status.'</strong></td>';
            $html .=    '</tr>';
            $html .=    '<tr id="accordion-'.$auction->ID.'" class="collapse">
                            <td colspan="4">
                                <div>'.self::getBonds($auction->ID ).'</div>
                            </td>
                        </tr>';
        }
        $html .=    '</tbody>';
        $html .= '</table>';

        return $html;
    }

    public static function getBonds( $auction_ID ) {

        $winner_meta    = "user_winning_bid_".$auction_ID."_".$bond_ID;
        $winner_val     = get_post_meta($auction_ID,$winner_meta ,true);

        $html  = '';
        $html .= '<table class="table-front">';
        $html .=    '<thead>';
        $html .=        '<tr class="success">';
        $html .=           '<th>Type</th>';
        $html .=           '<th>Orig Rating</th>';
        $html .=           '<th>CU SIP/ISIN</th>';
        $html .=           '<th>Ticker</th>';
        $html .=           '<th>Orig Size (MM)</th>';
        $html .=           '<th>Cover Price</th>';
        $html .=           '<th>Bids Placed</th>';
        $html .=           '<th>Result</th>';
        $html .=        '</tr>';
        $html .=    '</thead>';
        $html .=    '<tbody>';

        $postData           = get_post_meta($auction_ID,"_auction_meta_field_value",true);
        $userID             = get_current_user_id();
        $user_placed_bid    = get_user_meta($userID, 'user_placed_bid', true);
        $bond_bid_placed    = array_keys($user_placed_bid[$auction_ID]);

        $secondhighPrice    = get_post_meta($auction_ID,"user_placed_bid",true);            
        $secondPrice1       = array();
        foreach($secondhighPrice as $key=>$value){
            $secondPrice = $value;
            foreach($secondPrice as $val){
                $secondPrice1[] = $val['bid_price'];
            }
        }

        rsort($secondPrice);

        foreach ( $postData as $bond ) {
            
            if (!in_array($bond, $bond_bid_placed) ) {
                continue;
            }

            $winner_meta    = "user_winning_bid_".$auction_ID."_".$bond;
            $winner_val     = get_post_meta($auction_ID,$winner_meta ,true);
           
            $list_id        = $bond;
            $type           = get_post_meta($bond,"type",true);
            $orig_rating    = get_post_meta($bond,"orig_rating",true);
            $cusip_isin     = get_post_meta($bond,"cusip/isin",true);
            $ticker         = get_post_meta($bond,"ticker",true);
            $orig_size      = get_post_meta($bond,"orig_size_(mm)",true);
            $curr_size      = get_post_meta($bond,"curr_size_(mm)",true);
            $c_e            = get_post_meta($bond,"c/e",true);
            $manager        = get_post_meta($bond,"manager",true);
            $reserve_price  = get_post_meta($bond,"reserve_price",true);
            $bid_amount     = '$'.$user_placed_bid[$auction_ID][$bond];

            /* check if . exist in orig size */
            $findDot    = strpos($orig_size,'.');
            $appendOrig = "";
            if($findDot === false){
                $appendOrig =    $orig_size.".00";
            }else{
                $appendOrig =    $orig_size;
            }

            /* check if $ exist in cover price */
            $findDoller     = strpos($secondPrice1[0],'$');
            $appendCover    = "";
            if($findDoller === false){
                $appendCover =    "$".$secondPrice1[0];
            }else{
                $appendCover =    $secondPrice1[0];
            }

            if( !empty($winner_val) ){
                if(  $winner_val[$auction_ID][$bond]['user_id'] == $userID ){
                    $result         = '<div style="background: #00FF00;color: white; font-weight: bold">Won</div>';
                }else{
                    $result         = '<div style="background: #FF0000;color: white; font-weight: bold">Lost</div>';                    
                }
            }else{
                $result         = '<div style="background:#FF0000;color: white;font-weight: bold">Lost</div>';
            }    

            $html .=        '<tr class="warning"  data-toggle="collapse" data-target="#accordion-'.$bond.'" class="clickable">';
            $html .=            '<td>'.$type.'</td>';
            $html .=            '<td>'.$orig_rating.'</td>';
            $html .=            '<td>'.$cusip_isin.'</td>';
            $html .=            '<td>'.$ticker.'</td>';
            $html .=            '<td>'.$appendOrig.'</td>';
            $html .=            '<td>'.$appendCover.'</td>';
            $html .=            '<td>'.$bid_amount.'</td>';
            $html .=            '<td>'.$result.'</td>';
            $html .=        '</tr>';

        }

        $html .=    '</tbody>';
        $html .= '</table>';

        return $html;

    }


	public static function getBidUser( $auction_ID, $bond_ID ){

        $meta_key = "user_winning_bid_".$auction_ID."_".$bond_ID;
              //$winner_data[] = get_post_meta($value2,$meta_key,true);
        echo $placedBids     = get_post_meta($auction_ID,$meta_key,true);

        die();
		$html = '';
        $html .= '<table class="table-front">';
        $html .=    '<thead>';
        $html .=        '<tr class="success">';
        $html .=           '<th>Bid Price</th>';
        $html .=           '<th></th>';
        $html .=        '</tr>';
        $html .=    '</thead>';
        $html .=    '<tbody>';

        $meta_key       = 'user_placed_bid_'.$auction_ID.'_'.$bond_ID;
        $placedBids     = get_post_meta($auction_ID,$meta_key,true);
        $reserve_price  = get_post_meta($bond,"reserve_price",true);
        $user_id    = get_current_user_id();
        $flag       = true;

        /* arrange the order of bids in decreasing order */
        /*foreach ($placedBids as $key => $row) {
            $volume[$key]   = $placedBids[$key][$auction_ID][$bond_ID];
            $edition[$key]  = $placedBids[$key][$auction_ID][$bond_ID];
        }*/
        //array_multisort($volume, SORT_DESC, $edition, SORT_DESC, $placedBids);

        foreach ($placedBids as $key => $bid ) {

            if( !isset($placedBids[$key][$auction_ID][$bond_ID]) ){
                continue;
            }

            $flag = false;
            $reserve_price_btn = '';
            $reserve_price  = get_post_meta($bond_ID,"reserve_price",true);
            /* check if reserve price is blank or not */
            if ($reserve_price !== '') {

                $winner_meta = "user_winning_bid_".$auction_ID."_".$bond_ID;
                $winner_val = get_post_meta($auction_ID,$winner_meta ,true);

                if ($winner_val == "") {

                }
                $reserve_price_btn = '<button data-auction="'.$auction_ID.'" data-price="'.$placedBids[$key][$auction_ID][$bond_ID].'" data-bond="'.$bond_ID.'" class="trade_btn btn-default" data-user="'.$key.'" id="trade_'.$auction_ID.'_'.$bond_ID.'">Trade</button>';
            }

            $html .=        '<tr class="warning user_'.$key.'">';
            $html .=            '<td>$'.$placedBids[$key][$auction_ID][$bond_ID].'</td>';
            $html .=            '<td style="text-align:right;">'.$reserve_price_btn.'</td>';
            $html .=        '</tr>';

        }

        if( $flag ) {
            $html .=        '<tr class="warning">';
            $html .=            '<td colspan="4" style="text-align:center"><strong>No Bid Placed.</strong></td>';
            $html .=        '</tr>';
        }

        $html .=    '</tbody>';
        $html .= '</table>';

        return $html;
	}

    function jsScripts(){
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                jQuery(".trade_btn").click(function(){
                    var   auction_id  =  jQuery(this).attr("data-auction");
                    var   bid_price   =  jQuery(this).attr("data-price");
                    var   bon_id      =  jQuery(this).attr("data-bond");
                    var   bid_user    =  jQuery(this).attr("data-user");
                    console.log(auction_id);console.log(bon_id);console.log(bid_price);console.log(bid_user);
                    jQuery.ajax({
                        type: "POST",
                        data : {action:"trade_bid",id:auction_id,bid_price:bid_price,bon_id:bon_id,bid_user:bid_user},
                        url: "/wp-admin/admin-ajax.php",
                        success: function(result){
                            console.log(result);
                            var data    = jQuery.parseJSON(result);
                            var auc_id  = data.auction_id;
                            var bond_id = data.bon_id;
                            var user_id = data.bid_user;
                            //accordion-154576
                            jQuery("#accordion-"+bond_id+" td div table tbody").find("tr td").find("button").remove();
                            jQuery("#accordion-"+bond_id+" td div table tbody").find("tr.user_"+user_id).find("td").eq(1).html("<h5 id='winner'>Winner</h5>");

                        }
                    });
                });
            });
        </script>

        <?php
    }

    function bid_history(){

        if (is_user_logged_in()) {

            $current_user       = wp_get_current_user();
            $current_user_id    = $current_user->ID;
            //user_placed_bid
            $the_query = new WP_Query( array( 'author' => $current_user_id, 'post_type' => 'auction') );

            if ( $the_query->have_posts() ) {

                while ( $the_query->have_posts() ) {
                    $the_query->the_post();
                    $postid  = get_the_ID();
                    $meta_data = get_post_meta($postid,'user_placed_bid',true);
                }

                /* Restore original Post Data */
                wp_reset_postdata();
            } else {
                //echo "<div><h2>You didn't placed any Bid.</h2></div>";
            }

        }

    }

}/* class ends here */

?>
