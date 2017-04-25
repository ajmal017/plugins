<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AmmMetaBoxes {

    public static function auctionWinnerMetaBox() {
       /* error_reporting(E_ALL);
        ini_set('display_errors', 1);*/


        echo '<style>#edit-slug-box{display: none;}</style>';
        /* If not administrator Go back*/
        if ( !is_admin() ) {
            return;
        }

        add_meta_box(
            'AmmMetaBoxesBids',
            esc_html__( 'Detail   ', 'AwBondManagement' ),
            array( 'AmmMetaBoxes', 'bondWinnerMetaBoxBids' ),
            'bond',
            'advanced',
            'default'
        );

    }

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

    public function bondWinnerMetaBoxBids( $bond ) {
            echo '<div class="aw-bidders aw-bonds" style="cursor: pointer; ">';
               echo AmmMetaBoxes::getBidUserBond($bond->ID);
            echo '</div>';
            ?>
            <script>
            jQuery( document ).ready(function() {
                console.log( "ready Bonds!" );
                jQuery(".aw-bidders").on("click", function(event) {
                    event.preventDefault();
                    jQuery(this).children(".awBidUser").slideToggle(300);
                });
            });
            </script>
            <?php
        
    }

    public static function getBidUserBond( $bond_ID ){

        $reserve_price  = get_post_meta($bond_ID,"offer_price",true);

        $html = '';
        $html .= '<table class="table awBidUser" style="width: 100%;text-align: left;">';
        $html .=    '<thead>';
        $html .=        '<tr class="success">';
        $html .=           '<th style="width: 35%">Bidder</th>';
        $html .=           '<th style="width: 35%">Email</th>';
        $html .=           '<th style="width: 15%">Bid Price</th>';
        $html .=           '<th style="width: 15%">Offer Price</th>';
        $html .=        '</tr>';
        $html .=    '</thead>';
        $html .=    '<tbody>';


        $meta_key   = 'market_placed_bid_'.$bond_ID;
        $placedBids = get_post_meta($bond_ID,$meta_key,true);

        $user_id    = get_current_user_id();
        $flag   = true;
        foreach ($placedBids as $key => $bid ) {

            if( !isset($placedBids[$key][$bond_ID]) ){
                continue;
            }

            $flag = false;

            $userData = get_userdata($key);

            $html .=        '<tr class="warning">';
            $html .=            '<td>'.$userData->display_name.'</td>';
            $html .=            '<td>'.$userData->user_email.'</td>';
            $html .=            '<td>$'.$placedBids[$key][$bond_ID].'</td>';
            $html .=            '<td>'.self::formatAmount($reserve_price).'</td>';
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