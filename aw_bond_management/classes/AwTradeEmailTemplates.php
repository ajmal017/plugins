<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AwTradeEmailTemplates {
    
    public static function ifWinner($winner_id, $seller_id, $auctionID){

        $winner_email   =   get_the_author_meta('user_email', $winner_id);
        $seller_email   =   get_the_author_meta('user_email', $seller_id);
        $admin_email    =   get_option( 'admin_email' );
        
        $subject        = "BWIC Result";
        $bwic_title     = get_post_meta($auctionID, 'bwic_title', true);

        $seller_bonds   =   get_post_meta($auctionID, '_auction_meta_field_value', true);

        if (!empty($seller_bonds)) {
            
            $seller_msg     = '<table>';
            $seller_msg    .=   '<thead>';
            $seller_msg    .=       '<tr>';
            $seller_msg    .=           '<th colspan="5">';
            $seller_msg    .=               '<h3>'.$seller_id.', Your '.$bwic_title.' has closed and we wanted to inform you of the maximum bids received for the following bonds.</h3>';
            $seller_msg    .=           '</th>';
            $seller_msg    .=       '</tr>';
            $seller_msg    .=   '</thead>';
            $seller_msg    .=   '<tbody>';
            $seller_msg    .=       '<tr>';
            $seller_msg    .=           '<td>List ID</td>';
            $seller_msg    .=           '<td>Type</td>';
            $seller_msg    .=           '<td>Orig Rating</td>';
            $seller_msg    .=           '<td>Ticker</td>';
            $seller_msg    .=           '<td>C/E</td>';
            $seller_msg    .=       '</tr>';

            foreach ($seller_bonds as $bond) {
                $list_id        =    $bond;
                $type           =    get_post_meta($bond,"type",true);
                $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                $ticker         =    get_post_meta($bond,"ticker",true);
                $c_e            =    get_post_meta($bond,"c/e",true);
                
                $seller_msg    .=   '<tr>';
                $seller_msg    .=       '<td>'.$list_id.'</td>';
                $seller_msg    .=       '<td>'.$type.'</td>';
                $seller_msg    .=       '<td>'.$orig_rating.'</td>';
                $seller_msg    .=       '<td>'.$ticker.'</td>';
                $seller_msg    .=       '<td>'.$c_e.'</td>';
                $seller_msg    .=   '</tr>';
            }
            
            $seller_msg   .= '<tr><td colspan="5"><h3> You can view the bids in your <a href="'.site_url().'/seller-dashboard/" target="_blank">Dashboard</a>. Please do not hesitate to contact us if you have any questions or concerns.</h3><h3> Thank you,</h3><h3> BEX Markets</h3></td></tr>';
            $seller_msg    .=   '</tbody>';
            $seller_msg    .=  '</table>';
            
            wp_mail($seller_email,$subject,$seller_msg,'Content-type:text/html;charset=iso-8859-1',array());
        }


        $winner_bonds   =   get_post_meta($auctionID, '_auction_meta_field_value', true);
        if (!empty($winner_bonds)) {
            
            $winner_msg     = '<table>';
            $winner_msg    .=   '<thead>';
            $winner_msg    .=       '<tr>';
            $winner_msg    .=           '<th colspan="5">';
            $winner_msg    .=               '<h3>'.$winner_id.', '.$bwic_title.' has closed and we wanted to inform you that you won the following bonds.</h3>';
            $winner_msg    .=           '</th>';
            $winner_msg    .=       '</tr>';
            $winner_msg    .=   '</thead>';
            $winner_msg    .=   '<tbody>';
            $winner_msg    .=       '<tr>';
            $winner_msg    .=           '<td>List ID</td>';
            $winner_msg    .=           '<td>Type</td>';
            $winner_msg    .=           '<td>Orig Rating</td>';
            $winner_msg    .=           '<td>Ticker</td>';
            $winner_msg    .=           '<td>C/E</td>';
            $winner_msg    .=       '</tr>';


            foreach ($winner_bonds as $bond) {
                $list_id        =    $bond;
                $type           =    get_post_meta($bond,"type",true);
                $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                $ticker         =    get_post_meta($bond,"ticker",true);
                $c_e            =    get_post_meta($bond,"c/e",true);
                
                $winner_msg    .=   '<tr>';
                $winner_msg    .=       '<td>'.$list_id.'</td>';
                $winner_msg    .=       '<td>'.$type.'</td>';
                $winner_msg    .=       '<td>'.$orig_rating.'</td>';
                $winner_msg    .=       '<td>'.$ticker.'</td>';
                $winner_msg    .=       '<td>'.$c_e.'</td>';
                $winner_msg    .=   '</tr>';
            }

            $winner_msg   .= '<tr><td colspan="5"><h3>We will provide you with final trade confirmation and settlement instructions shortly.  Please do not hesitate to contact us if you have any questions or concerns.</h3><h3> Thank you,</h3><h3> BEX Markets</h3></td></tr>';
            $winner_msg    .=   '</tbody>';
            $winner_msg    .=  '</table>';
            
            wp_mail($winner_email,$subject,$winner_msg,'Content-type:text/html;charset=iso-8859-1',array());
        }

        $admin_bonds   =   get_post_meta($auctionID, '_auction_meta_field_value', true);

        if (!empty($admin_bonds)) {
            
            $admin_msg     = '<table>';
            $admin_msg    .=   '<thead>';
            $admin_msg    .=       '<tr>';
            $admin_msg    .=           '<th colspan="5">';
            $admin_msg    .=               '<h3>'.$bwic_title.' has closed and there are winners for the following bonds.</h3>';
            $admin_msg    .=           '</th>';
            $admin_msg    .=       '</tr>';
            $admin_msg    .=   '</thead>';
            $admin_msg    .=   '<tbody>';
            $admin_msg    .=       '<tr>';
            $admin_msg    .=           '<td>List ID</td>';
            $admin_msg    .=           '<td>Type</td>';
            $admin_msg    .=           '<td>Orig Rating</td>';
            $admin_msg    .=           '<td>Ticker</td>';
            $admin_msg    .=           '<td>C/E</td>';
            $admin_msg    .=       '</tr>';

            foreach ($admin_bonds as $bond) {
                $list_id        =    $bond;
                $type           =    get_post_meta($bond,"type",true);
                $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                $ticker         =    get_post_meta($bond,"ticker",true);
                $c_e            =    get_post_meta($bond,"c/e",true);
                
                $admin_msg    .=   '<tr>';
                $admin_msg    .=       '<td>'.$list_id.'</td>';
                $admin_msg    .=       '<td>'.$type.'</td>';
                $admin_msg    .=       '<td>'.$orig_rating.'</td>';
                $admin_msg    .=       '<td>'.$ticker.'</td>';
                $admin_msg    .=       '<td>'.$c_e.'</td>';
                $admin_msg    .=   '</tr>';
            }

            $admin_msg   .= '<tr><td colspan="5"><h3> View details here:<a target="_blank" href="'.site_url().'/wp-admin/edit.php?post_type=auction.">Dashboard</a></h3></td></tr>';
            $admin_msg    .=   '</tbody>';
            $admin_msg    .=  '</table>';
            
            wp_mail($admin_email,$subject,$admin_msg,'Content-type:text/html;charset=iso-8859-1',array());
        }

    } 

    public static function ifNoWinner($winner_id, $seller_id, $auctionID){
        
        $winner_email   =   get_the_author_meta('user_email', $winner_id);
        $seller_email   =   get_the_author_meta('user_email', $seller_id);
        $admin_email    =   get_option( 'admin_email' );
        
        $subject        = "BWIC Result";
        $bwic_title     = get_post_meta($auctionID, 'bwic_title', true);

        $admin_bonds   =   get_post_meta($auctionID, '_auction_meta_field_value', true);

        if (!empty($admin_bonds)) {
            
            $admin_msg     = '<table>';
            $admin_msg    .=   '<thead>';
            $admin_msg    .=       '<tr>';
            $admin_msg    .=           '<th colspan="5">';
            $admin_msg    .=               '<h3>'.$bwic_title.' has closed and there were no winners for the following bonds </h3>';
            $admin_msg    .=           '</th>';
            $admin_msg    .=       '</tr>';
            $admin_msg    .=   '</thead>';
            $admin_msg    .=   '<tbody>';
            $admin_msg    .=       '<tr>';
            $admin_msg    .=           '<td>List ID</td>';
            $admin_msg    .=           '<td>Type</td>';
            $admin_msg    .=           '<td>Orig Rating</td>';
            $admin_msg    .=           '<td>Ticker</td>';
            $admin_msg    .=           '<td>C/E</td>';
            $admin_msg    .=       '</tr>';

            foreach ($admin_bonds as $bond) {
                $list_id        =    $bond;
                $type           =    get_post_meta($bond,"type",true);
                $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                $ticker         =    get_post_meta($bond,"ticker",true);
                $c_e            =    get_post_meta($bond,"c/e",true);
                
                $admin_msg    .=   '<tr>';
                $admin_msg    .=       '<td>'.$list_id.'</td>';
                $admin_msg    .=       '<td>'.$type.'</td>';
                $admin_msg    .=       '<td>'.$orig_rating.'</td>';
                $admin_msg    .=       '<td>'.$ticker.'</td>';
                $admin_msg    .=       '<td>'.$c_e.'</td>';
                $admin_msg    .=   '</tr>';
            }

            $admin_msg   .= '<tr><td colspan="5"><h3> View details here:<a target="_blank" href="'.site_url().'/wp-admin/edit.php?post_type=auction.">Dashboard</a></h3></td></tr>';
            $admin_msg    .=   '</tbody>';
            $admin_msg    .=  '</table>';
            
            wp_mail($admin_email,$subject,$admin_msg,'Content-type:text/html;charset=iso-8859-1',array());
        }

        $user_bonds   =   get_post_meta($auctionID, '_auction_meta_field_value', true);

        if (!empty($user_bonds)) {
            
            $user_query         = get_users( array( 'role' => 'administrator' ) );
            $administrator_id   = wp_list_pluck( $user_query, 'ID' );
            $non_administrator  = get_users( array( 'exclude' => $administrator_id ) );

            foreach ($non_administrator as $all_user) {
                $userID     = $all_user->ID;
                $useremail  = $all_user->user_email;

                $user_msg     = '<table>';
                $user_msg    .=   '<thead>';
                $user_msg    .=       '<tr>';
                $user_msg    .=           '<th colspan="5">';
                $user_msg    .=               '<h3>We wanted to inform you that you did not win any of the following bonds you bid on </h3>';
                $user_msg    .=           '</th>';
                $user_msg    .=       '</tr>';
                $user_msg    .=   '</thead>';
                $user_msg    .=   '<tbody>';
                $user_msg    .=       '<tr>';
                $user_msg    .=           '<td>List ID</td>';
                $user_msg    .=           '<td>Type</td>';
                $user_msg    .=           '<td>Orig Rating</td>';
                $user_msg    .=           '<td>Ticker</td>';
                $user_msg    .=           '<td>C/E</td>';
                $user_msg    .=       '</tr>';

                foreach ($user_bonds as $bond) {
                    $list_id        =    $bond;
                    $type           =    get_post_meta($bond,"type",true);
                    $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                    $ticker         =    get_post_meta($bond,"ticker",true);
                    $c_e            =    get_post_meta($bond,"c/e",true);
                    
                    $user_msg    .=   '<tr>';
                    $user_msg    .=       '<td>'.$list_id.'</td>';
                    $user_msg    .=       '<td>'.$type.'</td>';
                    $user_msg    .=       '<td>'.$orig_rating.'</td>';
                    $user_msg    .=       '<td>'.$ticker.'</td>';
                    $user_msg    .=       '<td>'.$c_e.'</td>';
                    $user_msg    .=   '</tr>';
                }

                $user_msg   .= '<tr><td colspan="5"><h3>  You can review your bids in your  <a href="'.site_url().'/bid-history/" target="_blank">Dashboard</a>. Please do not hesitate to contact us if you have any questions or concerns.</h3><h3>Thank you,</h3> BEX Markets.</h3></td></tr>';
                $user_msg    .=   '</tbody>';
                $user_msg    .=  '</table>';

                wp_mail($useremail,$subject,$user_msg,'Content-type:text/html;charset=iso-8859-1',array());

            }
        }

    }

    public static function ifTied($user_id, $seller_id, $auctionID){
        
        $user_email     =   get_the_author_meta('user_email', $user_id);
        $seller_email   =   get_the_author_meta('user_email', $seller_id);
        $admin_email    =   get_option( 'admin_email' );
        
        $subject        = "BWIC Result";
        $bwic_title     = get_post_meta($auctionID, 'bwic_title', true);

        $seller_bonds   =   get_post_meta($auctionID, '_auction_meta_field_value', true);
        if (!empty($seller_bonds)) {
            
            $seller_msg     = '<table>';
            $seller_msg    .=   '<thead>';
            $seller_msg    .=       '<tr>';
            $seller_msg    .=           '<th colspan="5">';
            $seller_msg    .=               '<h3>'.$seller_id.', Your '.$bwic_title.' has closed and we wanted to inform you that there was a tie between the highest bidders for the following bonds. </h3>';
            $seller_msg    .=           '</th>';
            $seller_msg    .=       '</tr>';
            $seller_msg    .=   '</thead>';
            $seller_msg    .=   '<tbody>';
            $seller_msg    .=       '<tr>';
            $seller_msg    .=           '<td>List ID</td>';
            $seller_msg    .=           '<td>Type</td>';
            $seller_msg    .=           '<td>Orig Rating</td>';
            $seller_msg    .=           '<td>Ticker</td>';
            $seller_msg    .=           '<td>C/E</td>';
            $seller_msg    .=       '</tr>';

            foreach ($seller_bonds as $bond) {
                $list_id        =    $bond;
                $type           =    get_post_meta($bond,"type",true);
                $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                $ticker         =    get_post_meta($bond,"ticker",true);
                $c_e            =    get_post_meta($bond,"c/e",true);
                
                $seller_msg    .=   '<tr>';
                $seller_msg    .=       '<td>'.$list_id.'</td>';
                $seller_msg    .=       '<td>'.$type.'</td>';
                $seller_msg    .=       '<td>'.$orig_rating.'</td>';
                $seller_msg    .=       '<td>'.$ticker.'</td>';
                $seller_msg    .=       '<td>'.$c_e.'</td>';
                $seller_msg    .=   '</tr>';
            }

            $seller_msg   .= '<tr><td colspan="5"><h3>  A best and final bid has been requested and you will be notified shortly of the results.  Please do not hesitate to contact us if you have any questions or concerns.</h3><h3>Thank you,</h3><h3>BEX Markets.</h3></td></tr>';
            $seller_msg    .=   '</tbody>';
            $seller_msg    .=  '</table>';

            wp_mail($seller_email,$subject,$seller_msg,'Content-type:text/html;charset=iso-8859-1',array());
        }

        $bidagain_bonds   =   get_post_meta($auctionID, '_auction_meta_field_value', true);

        if (!empty($bidagain_bonds)) {
            
            $minutes_to_add = 15;
            $time           = new DateTime();
            $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
            $stamp = $time->format('m-d-Y H:i');

            $bidagain_msg     = '<table>';
            $bidagain_msg    .=   '<thead>';
            $bidagain_msg    .=       '<tr>';
            $bidagain_msg    .=           '<th colspan="5">';
            $bidagain_msg    .=               '<h3>'.$user_id.', Your '.$bwic_title.' has closed and we wanted to inform you that you were tied for the maximum bid for the following bonds.</h3>';
            $bidagain_msg    .=           '</th>';
            $bidagain_msg    .=       '</tr>';
            $bidagain_msg    .=   '</thead>';
            $bidagain_msg    .=   '<tbody>';
            $bidagain_msg    .=       '<tr>';
            $bidagain_msg    .=           '<td>List ID</td>';
            $bidagain_msg    .=           '<td>Type</td>';
            $bidagain_msg    .=           '<td>Orig Rating</td>';
            $bidagain_msg    .=           '<td>Ticker</td>';
            $bidagain_msg    .=           '<td>C/E</td>';
            $bidagain_msg    .=       '</tr>';

            foreach ($bidagain_bonds as $bond) {
                $list_id        =    $bond;
                $type           =    get_post_meta($bond,"type",true);
                $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                $ticker         =    get_post_meta($bond,"ticker",true);
                $c_e            =    get_post_meta($bond,"c/e",true);
                
                $bidagain_msg    .=   '<tr>';
                $bidagain_msg    .=       '<td>'.$list_id.'</td>';
                $bidagain_msg    .=       '<td>'.$type.'</td>';
                $bidagain_msg    .=       '<td>'.$orig_rating.'</td>';
                $bidagain_msg    .=       '<td>'.$ticker.'</td>';
                $bidagain_msg    .=       '<td>'.$c_e.'</td>';
                $bidagain_msg    .=   '</tr>';
            }

            $bidagain_msg   .= '<tr><td colspan="5"><h3>Please submit your best and final bid by '.$stamp.' at <a href="'.site_url().'/auctions/">Dashboard</a>Please do not hesitate to contact us if you have any questions or concerns.</h3><h3>Thank you,</h3><h3> BEX Markets</h3></td></tr>';
            $bidagain_msg    .=   '</tbody>';
            $bidagain_msg    .=  '</table>';

            wp_mail($user_email,$subject,$bidagain_msg,'Content-type:text/html;charset=iso-8859-1',array());
        }

        $admin_bonds   =   get_post_meta($auctionID, '_auction_meta_field_value', true);

        if (!empty($admin_bonds)) {
         
            $admin_msg     = '<table>';
            $admin_msg    .=   '<thead>';
            $admin_msg    .=       '<tr>';
            $admin_msg    .=           '<th colspan="5">';
            $admin_msg    .=               '<h3>'.$bwic_title.' has closed and there were ties for the following bonds </h3>';
            $admin_msg    .=           '</th>';
            $admin_msg    .=       '</tr>';
            $admin_msg    .=   '</thead>';
            $admin_msg    .=   '<tbody>';
            $admin_msg    .=       '<tr>';
            $admin_msg    .=           '<td>List ID</td>';
            $admin_msg    .=           '<td>Type</td>';
            $admin_msg    .=           '<td>Orig Rating</td>';
            $admin_msg    .=           '<td>Ticker</td>';
            $admin_msg    .=           '<td>C/E</td>';
            $admin_msg    .=       '</tr>';

            foreach ($admin_bonds as $bond) {
                $list_id        =    $bond;
                $type           =    get_post_meta($bond,"type",true);
                $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                $ticker         =    get_post_meta($bond,"ticker",true);
                $c_e            =    get_post_meta($bond,"c/e",true);
                
                $admin_msg    .=   '<tr>';
                $admin_msg    .=       '<td>'.$list_id.'</td>';
                $admin_msg    .=       '<td>'.$type.'</td>';
                $admin_msg    .=       '<td>'.$orig_rating.'</td>';
                $admin_msg    .=       '<td>'.$ticker.'</td>';
                $admin_msg    .=       '<td>'.$c_e.'</td>';
                $admin_msg    .=   '</tr>';
            }

            $admin_msg   .= '<tr><td colspan="5"><h3>The tied bidders have been requested to provide best and final bids.   View details here:<a target="_blank" href="'.site_url().'/wp-admin/edit.php?post_type=auction.">Dashboard</a></h3></td></tr>';
            $admin_msg    .=   '</tbody>';
            $admin_msg    .=  '</table>';
            
            wp_mail($admin_email,$subject,$admin_msg,'Content-type:text/html;charset=iso-8859-1',array());

        }

    }

}/* class ends here */

?>
