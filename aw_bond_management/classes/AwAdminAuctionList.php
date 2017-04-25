<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AwAdminAuctionList {



    public static function adminAuctionDashboard(){
        if (!is_user_logged_in()) {
        	echo "<h2>You are not logged in user. Please login.</h2>";
        }else{
        	$posts = AwAuction::getAuctionsByUser();
        	echo self::getHTML($posts );
        die;
        }

    }

    public static function getHTML( $postData ){
        $html = '';
        $html .= '<table class="table" style="max-width:98%; margin-top:1%;">';
        $html .=    '<thead class="thead-inverse">';
        $html .=        '<tr class="success">';
        $html .=           '<th>BWIC Title</th>';
        $html .=           '<th>Start Date</th>';
        $html .=           '<th>End Date</th>';
        $html .=           '<th>Status</th>';
        $html .=        '</tr>';
        $html .=    '</thead>';
        $html .=    '<tbody>';

        foreach ($postData as $auction) {
            $html .=        '<tr>';
            $html .=            '<td data-toggle="collapse" data-target="#accordion-'.$auction->ID.'" class="clickable">';
            $html .=				'<a style="cursor:pointer;">'.$auction->post_title.'</a>';
            $html .=			'</td>';
            $html .=            '<td>'.date('Y-m-d H:i:s A', $auction->start_date).'</td>';
            $html .=            '<td>'.date('Y-m-d H:i:s A', $auction->end_date).'</td>';
            $html .=            '<td><strong>Pending</strong></td>';
            $html .=        '</tr>';
            $html .=        '<tr>
                                <td colspan="4">
                                    <div id="accordion-'.$auction->ID.'" class="collapse">
                                        '.self::getBonds($auction->ID ).'
                                    </div>
                                </td>
                            </tr>';
        }

        $html .=    '</tbody>';
        $html .= '</table>';

        return $html;
    }

    public static function getBonds( $auction_ID ) {

        $html  = '';
        $html .= '<table class="table">';
        $html .=    '<thead>';
        $html .=        '<tr class="success">';
        $html .=           '<th>Type</th>';
        $html .=           '<th>Orig Rating</th>';
        $html .=           '<th>CU SIP/ISIN</th>';
        $html .=           '<th>Ticker</th>';
        $html .=           '<th>Orig Size (MM)</th>';
        $html .=           '<th>Bidders</th>';
        $html .=        '</tr>';
        $html .=    '</thead>';
        $html .=    '<tbody>';

        $postData = get_post_meta($auction_ID,"_auction_meta_field_value",true);

        foreach ( $postData as $bond ) {

            $list_id        = get_post_meta($bond,"list_id",true);
            $type           = get_post_meta($bond,"type",true);
            $orig_rating    = get_post_meta($bond,"orig_rating",true);
            $cusip_isin     = get_post_meta($bond,"cusip/isin",true);
            $ticker         = get_post_meta($bond,"ticker",true);
            $orig_size      = get_post_meta($bond,"orig_size_(mm)",true);
            $curr_size      = get_post_meta($bond,"curr_size_(mm)",true);
            $c_e            = get_post_meta($bond,"c/e",true);
            $manager        = get_post_meta($bond,"manager",true);


            $html .=        '<tr class="warning">';
            $html .=            '<td>'.$type.'</td>';
            $html .=            '<td>'.$orig_rating.'</td>';
            $html .=            '<td>'.$cusip_isin.'</td>';
            $html .=            '<td>'.$ticker.'</td>';
            $html .=            '<td>'.$orig_size.'</td>';
            $html .=            '<td data-toggle="collapse" data-target="#accordion-'.$bond.'" class="clickable">';
            $html .= 				'<button type="button" title="Show Bids" class="btn btn-success" style="padding:0 10px;">';
            $html .= 					'<i class="glyphicon glyphicon-list"></i> Show Bids';
            $html .= 				'</button>';
            $html .=			'</td>';
            $html .=        '</tr>';
            $html .=        '<tr>';
            $html .= 			'<td colspan="6">';
            $html .=				'<div id="accordion-'.$bond.'" class="collapse">';
            $html .=					self::getBidUser($auction_ID, $bond);
            $html .=				'</div>';
			$html .=			'</td>';
            $html .=		'</tr>';
        }

        $html .=    '</tbody>';
        $html .= '</table>';

        return $html;

    }


	public static function getBidUser( $auction_ID, $bond_ID ){

		$html = '';
        $html .= '<table class="table">';
        $html .=    '<thead>';
        $html .=        '<tr class="success">';
        $html .=           '<th>Bidder</th>';
        $html .=           '<th>Email</th>';
        $html .=           '<th>Bid Price</th>';
        //$html .=           '<th style="text-align:center;">Make Winner</th>';
        $html .=        '</tr>';
        $html .=    '</thead>';
        $html .=    '<tbody>';

        //$postData = get_post_meta($auction_ID,"_auction_meta_field_value",true);
        $meta_key   = 'user_placed_bid_'.$auction_ID.'_'.$bond_ID;
        $placedBids = get_post_meta($auction_ID,$meta_key,true);

        // $post_author_id = 	get_post_field( 'post_author', $bond );
		// $user_email 	= 	get_the_author_meta('user_email',$post_author_id);
        $user_id    = get_current_user_id();
        $flag = true;
        foreach ($placedBids as $key => $bid ) {

            if( !isset($placedBids[$key][$auction_ID][$bond_ID]) ){
                continue;
            }

            $flag = false;

            $userData = get_userdata($key);

            $html .=        '<tr class="warning">';
            $html .=            '<td>'.$userData->display_name.'</td>';
            $html .=            '<td>'.$userData->user_email.'</td>';
            $html .=            '<td>$'.$placedBids[$key][$auction_ID][$bond_ID].'</td>';
            // $html .=            '<td style="text-align:center;">';
            // $html .=                '<button type="button" name="select_winner" id="select_winner" title="Make this user winner" class="btn btn-success" style="padding:0 10px;">';
            // $html .=                    '<i class="glyphicon glyphicon-ok"></i>';
            // $html .=                '</button>';
            // $html .=            '</td>';
            $html .=        '</tr>';
        }

        if( $flag ) {
            $html .=        '<tr class="warning">';
            $html .=            '<td colspan="4" style="text-align: center"><strong>No Bid Placed.</strong></td>';
            $html .=        '</tr>';
        }

        $html .=    '</tbody>';
        $html .= '</table>';

        return $html;

	}

}/* class ends here */

?>