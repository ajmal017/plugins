<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AwEmailNotifications {

    public static  function sendNotificationToWinner($auctionID, $bondID, $winnerID ){

        $subject   = "Notification: BWIC Won";
        $winner_id      =   $winnerID;
        $winner_email   =   get_the_author_meta('user_email', $winner_id);

        $list_id        =   $bondID;
        $bwic_title     =   get_post_meta($auctionID, 'bwic_title', true);

        $type           =   get_post_meta($bondID,"type",true);
        $orig_rating    =   get_post_meta($bondID,"orig_rating",true);
        $ticker         =   get_post_meta($bondID,"ticker",true);
        $c_e            =   get_post_meta($bondID,"c/e",true);

        $winner_msg     = '<table>';
        $winner_msg    .=   '<thead>';
        $winner_msg    .=       '<tr>';
        $winner_msg    .=           '<th colspan="5">';
        $winner_msg    .=               '<h3>'.$winner_id.', Your '.$bwic_title.' has closed and we wanted to inform you that you won the following bonds.</h3>';
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

        $winner_msg    .=   '<tr>';
        $winner_msg    .=       '<td>'.$list_id.'</td>';
        $winner_msg    .=       '<td>'.$type.'</td>';
        $winner_msg    .=       '<td>'.$orig_rating.'</td>';
        $winner_msg    .=       '<td>'.$ticker.'</td>';
        $winner_msg    .=       '<td>'.$c_e.'</td>';
        $winner_msg    .=   '</tr>';

        $winner_msg   .= '<tr><td colspan="5"><h3> We will provide you with final trade confirmation and settlement instructions shortly.  Please do not hesitate to contact us if you have any questions or concerns.   Thank you, BEX Markets</h3></td></tr>';
        $winner_msg    .=   '</tbody>';
        $winner_msg    .=  '</table>';

        wp_mail($winner_email,$subject,$winner_msg,'Content-type:text/html;charset=iso-8859-1',array());

        echo "<p>Sent Notification To Winner: $winner_email</p>";
    }

    public static  function sendNotificationToTied( $auctionID, $bondID, $tiedusers ){

        $tied_page = site_url().'/tiedbid/'; 

        
        
        foreach ( $tiedusers as $user_id) {

            $user_email     =   get_the_author_meta('user_email', $user_id);
            $list_id        =   $bondID;
            $bwic_title     =   get_post_meta($auctionID, 'bwic_title', true);

            $tiedBond = get_post_meta($list_id,'status',true);    
            if ($tiedBond != 'tied') {
                continue;
            }

            
            $type           =   get_post_meta($bondID,"type",true);
            $orig_rating    =   get_post_meta($bondID,"orig_rating",true);
            $ticker         =   get_post_meta($bondID,"ticker",true);
            $c_e            =   get_post_meta($bondID,"c/e",true);

            $subject        =   "Notification: BWIC Tied";

            $time           =   get_post_meta($auctionID, 'end_date', true);
            $stamp          =   date('Y-m-d H:i',strtotime('+15 minutes',$time));

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

            $bidagain_msg    .=   '<tr>';
            $bidagain_msg    .=       '<td>'.$list_id.'</td>';
            $bidagain_msg    .=       '<td>'.$type.'</td>';
            $bidagain_msg    .=       '<td>'.$orig_rating.'</td>';
            $bidagain_msg    .=       '<td>'.$ticker.'</td>';
            $bidagain_msg    .=       '<td>'.$c_e.'</td>';
            $bidagain_msg    .=   '</tr>';

            $bidagain_msg   .= '<tr><td colspan="5"><h4>Please submit your best and final bid by '.$stamp.' at <h4><a href="'.$tied_page.'"><h3>Dashboard</h3></a><h4>Please do not hesitate to contact us if you have any questions or concerns.   </h4><br><h3>Thank you,<br> BEX Markets</h3></td></tr>';
            $bidagain_msg    .=   '</tbody>';
            $bidagain_msg    .=  '</table>';

            wp_mail($user_email,$subject,$bidagain_msg,'Content-type:text/html;charset=iso-8859-1',array());

            echo "<p>Sent Notification To User: $user_email</p>";

        }
    }


    public static  function sendNotificationToSeller( $auction ){
            $seller_id      =   $auction->post_author;

            $seller_email   =   get_the_author_meta('user_email', $seller_id);
            $bwic_title     =   get_post_meta($auction->ID, 'bwic_title', true);
            $subject       =    "BWIC ( ".$bwic_title." ) summary.";

            $seller_msg     = '<table>';
            $seller_msg    .=   '<thead>';
            $seller_msg    .=       '<tr>';
            $seller_msg    .=           '<th colspan="5">';
            $seller_msg    .=               '<h3>'.$bwic_title.' has closed following is summary of bonds. </h3>';
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
            $seller_msg    .=           '<td>Result</td>';
            $seller_msg    .=       '</tr>';

            $seller_bonds   =   get_post_meta($auction->ID, '_auction_meta_field_value', true);

            foreach ( $seller_bonds as $bond ) {

                $list_id        =    $bond;
                $type           =    get_post_meta($bond,"type",true);
                $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                $ticker         =    get_post_meta($bond,"ticker",true);
                $c_e            =    get_post_meta($bond,"c/e",true);
                $status         =    get_post_meta($bond,"status",true);

                $seller_msg    .=   '<tr>';
                $seller_msg    .=       '<td>'.$list_id.'</td>';
                $seller_msg    .=       '<td>'.$type.'</td>';
                $seller_msg    .=       '<td>'.$orig_rating.'</td>';
                $seller_msg    .=       '<td>'.$ticker.'</td>';
                $seller_msg    .=       '<td>'.$c_e.'</td>';
                $seller_msg    .=       '<td>'.$status.'</td>';
                $seller_msg    .=   '</tr>';
            }

            $seller_msg   .= '<tr><td colspan="5"><h3><br><br>Thank you, BEX Markets.</h3></td></tr>';
            $seller_msg    .=   '</tbody>';
            $seller_msg    .=  '</table>';

            wp_mail($seller_email,$subject,$seller_msg,'Content-type:text/html;charset=iso-8859-1',array());

            echo "<p>Sent Notification To Seller: $seller_email</p>";

    }

    public static  function sendNotificationToAdmin($auction ){

            $admin_email   =   get_option( 'admin_email' );
            $bwic_title    =   get_post_meta($auction->ID, 'bwic_title', true);

            $subject       =    "BWIC ( ".$bwic_title." ) summary.";

            $admin_msg     = '<table>';
            $admin_msg    .=   '<thead>';
            $admin_msg    .=       '<tr>';
            $admin_msg    .=           '<th colspan="5">';
            $admin_msg    .=               '<h3>'.$bwic_title.' has closed following is summary of bonds. </h3>';
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
            $admin_msg    .=           '<td>Result</td>';
            $admin_msg    .=       '</tr>';

            $seller_bonds   =   get_post_meta($auction->ID, '_auction_meta_field_value', true);

            foreach ( $seller_bonds as $bond ) {

                $list_id        =    $bond;
                $type           =    get_post_meta($bond,"type",true);
                $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                $ticker         =    get_post_meta($bond,"ticker",true);
                $c_e            =    get_post_meta($bond,"c/e",true);
                $status         =    get_post_meta($bond,"status",true);

                $admin_msg    .=   '<tr>';
                $admin_msg    .=       '<td>'.$list_id.'</td>';
                $admin_msg    .=       '<td>'.$type.'</td>';
                $admin_msg    .=       '<td>'.$orig_rating.'</td>';
                $admin_msg    .=       '<td>'.$ticker.'</td>';
                $admin_msg    .=       '<td>'.$c_e.'</td>';
                $admin_msg    .=       '<td>'.$status.'</td>';
                $admin_msg    .=   '</tr>';
            }

            $admin_msg   .= '<tr><td colspan="5"><h3><br><br>Thank you, <br/>BEX Markets.</h3></td></tr>';
            $admin_msg    .=   '</tbody>';
            $admin_msg    .=  '</table>';

            wp_mail( $admin_email, $subject, $admin_msg,'Content-type:text/html;charset=iso-8859-1',array());

            echo "<p>Sent Notification To admin: $admin_email</p>";
    }

    public static  function sendNotificationToLosers($auctionID, $bondID, $winnerID, $coverPrice, $bidders ){

        $subject        = "Notification: BWIC Summary";
        $winner_id      =   $winnerID;


        $list_id        =   $bondID;
        $bwic_title     =   get_post_meta($auctionID, 'bwic_title', true);

        $type           =   get_post_meta($bondID,"type",true);
        $orig_rating    =   get_post_meta($bondID,"orig_rating",true);
        $ticker         =   get_post_meta($bondID,"ticker",true);
        $c_e            =   get_post_meta($bondID,"c/e",true);


        foreach ($bidders as $user_id => $value) {

            if( $user_id == $winnerID ){
                continue;
            }

            $user_email   =   get_the_author_meta('user_email', $user_id);

            $winner_msg     = '<table>';
            $winner_msg    .=   '<thead>';
            $winner_msg    .=       '<tr>';
            $winner_msg    .=           '<th colspan="5">';
            $winner_msg    .=               '<h3>'.$user_id.', Your '.$bwic_title.' has closed and we wanted to inform you that you Lost the following bond.</h3>';
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
            $winner_msg    .=           '<td>Cover Price</td>';
            $winner_msg    .=       '</tr>';

            $winner_msg    .=   '<tr>';
            $winner_msg    .=       '<td>'.$list_id.'</td>';
            $winner_msg    .=       '<td>'.$type.'</td>';
            $winner_msg    .=       '<td>'.$orig_rating.'</td>';
            $winner_msg    .=       '<td>'.$ticker.'</td>';
            $winner_msg    .=       '<td>'.$c_e.'</td>';
            $winner_msg    .=       '<td>$'.$coverPrice.'</td>';
            $winner_msg    .=   '</tr>';

            $winner_msg   .= '<tr>
                                    <td colspan="5">
                                        <h3> We will provide you with final trade confirmation and settlement instructions shortly.<br>
                                            Please do not hesitate to contact us if you have any questions or concerns.   <br>
                                            Thank you, <br>
                                            BEX Markets
                                        </h3>
                                    </td>
                                </tr>';
            $winner_msg    .=   '</tbody>';
            $winner_msg    .=  '</table>';

            wp_mail($user_email, $subject,$winner_msg,'Content-type:text/html;charset=iso-8859-1',array());

            echo "<p>Sent Notification To Loser: $user_email</p>";
        }

    }

    public static  function sendNotificationSummaryUsers1($auctionID, $bondID, $winnerID ){

            $admin_email   =   get_option( 'admin_email' );
            $bwic_title    =   get_post_meta($auction->ID, 'bwic_title', true);

            $subject       =    "BWIC ( ".$bwic_title." ) summary.";

            $admin_msg     = '<table>';
            $admin_msg    .=   '<thead>';
            $admin_msg    .=       '<tr>';
            $admin_msg    .=           '<th colspan="5">';
            $admin_msg    .=               '<h3>'.$bwic_title.' has closed following is summary of bonds. </h3>';
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
            $admin_msg    .=           '<td>Result</td>';
            $admin_msg    .=       '</tr>';

            $seller_bonds   =   get_post_meta($auction->ID, '_auction_meta_field_value', true);

            foreach ( $seller_bonds as $bond ) {

                $list_id        =    $bond;
                $type           =    get_post_meta($bond,"type",true);
                $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                $ticker         =    get_post_meta($bond,"ticker",true);
                $c_e            =    get_post_meta($bond,"c/e",true);
                $status         =    get_post_meta($bond,"status",true);

                $admin_msg    .=   '<tr>';
                $admin_msg    .=       '<td>'.$list_id.'</td>';
                $admin_msg    .=       '<td>'.$type.'</td>';
                $admin_msg    .=       '<td>'.$orig_rating.'</td>';
                $admin_msg    .=       '<td>'.$ticker.'</td>';
                $admin_msg    .=       '<td>'.$c_e.'</td>';
                $admin_msg    .=       '<td>'.$status.'</td>';
                $admin_msg    .=   '</tr>';
            }

            $admin_msg   .= '<tr><td colspan="5"><h3><br><br>Thank you, <br/>BEX Markets.</h3></td></tr>';
            $admin_msg    .=   '</tbody>';
            $admin_msg    .=  '</table>';

            wp_mail( $admin_email, $subject, $admin_msg,'Content-type:text/html;charset=iso-8859-1',array());

            echo "<p>Sent Notification To admin: $admin_email</p>";

    }
    // public static  function sendNotificationSummaryAdmin($auctionID, $bondID, $winnerID ){}
    public static  function sendNotificationSummarySeller($auction){
        $seller_id      =   $auction->post_author;

        $seller_email       =   get_the_author_meta('user_email', $seller_id);
        $seller_name        =   get_the_author_meta('display_name', $seller_id);
        $seller_company     =   get_the_author_meta('user_url', $seller_id);

        $bwic_title     =   get_post_meta($auction->ID, 'bwic_title', true);

        $subject        = "Notification: Summary BWIC Completed | ".$bwic_title.".";

        $html  = '';
        $html .= '<!DOCTYPE html>';
        $html .= '<html>';
        $html .= '  <head>';
        $html .= '    <meta name="viewport" content="width=device-width">';
        $html .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $html .= '    <title>BWIC Notification</title>';
        $html .= '    <style type="text/css">';
        $html .= '    @media only screen and (max-width: 620px) {';
        $html .= '      table[class=body] h1 {';
        $html .= '        font-size: 28px !important;';
        $html .= '        margin-bottom: 10px !important; }';
        $html .= '      table[class=body] p,';
        $html .= '      table[class=body] ul,';
        $html .= '      table[class=body] ol,';
        $html .= '      table[class=body] td,';
        $html .= '      table[class=body] span,';
        $html .= '      table[class=body] a {';
        $html .= '        font-size: 16px !important; }';
        $html .= '      table[class=body] .wrapper,';
        $html .= '      table[class=body] .article {';
        $html .= '        padding: 10px !important; }';
        $html .= '      table[class=body] .content {';
        $html .= '        padding: 0 !important; }';
        $html .= '      table[class=body] .container {';
        $html .= '        padding: 0 !important;';
        $html .= '        width: 100% !important; }';
        $html .= '      table[class=body] .main {';
        $html .= '        border-left-width: 0 !important;';
        $html .= '        border-radius: 0 !important;';
        $html .= '        border-right-width: 0 !important; }';
        $html .= '      table[class=body] .btn table {';
        $html .= '        width: 100% !important; }';
        $html .= '      table[class=body] .btn a {';
        $html .= '        width: 100% !important; }';
        $html .= '      table[class=body] .img-responsive {';
        $html .= '        height: auto !important;';
        $html .= '        max-width: 100% !important;';
        $html .= '        width: auto !important; }}';
        $html .= '    @media all {';
        $html .= '      .ExternalClass {';
        $html .= '        width: 100%; }';
        $html .= '      .ExternalClass,';
        $html .= '      .ExternalClass p,';
        $html .= '      .ExternalClass span,';
        $html .= '      .ExternalClass font,';
        $html .= '      .ExternalClass td,';
        $html .= '      .ExternalClass div {';
        $html .= '        line-height: 100%; }';
        $html .= '      .apple-link a {';
        $html .= '        color: inherit !important;';
        $html .= '        font-family: inherit !important;';
        $html .= '        font-size: inherit !important;';
        $html .= '        font-weight: inherit !important;';
        $html .= '        line-height: inherit !important;';
        $html .= '        text-decoration: none !important; }';
        $html .= '      .btn-primary a:hover {';
        $html .= '        background-color: #34495e !important;';
        $html .= '        border-color: #34495e !important; } }';
        $html .= '    </style>';
        $html .= '  </head>';
        $html .= '  <body class="" style="background-color:#f6f6f6;font-family:sans-serif;-webkit-font-smoothing:antialiased;font-size:14px;line-height:1.4;margin:0;padding:0;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;">';
        $html .= '    <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;background-color:#f6f6f6;width:95%;margin:0 auto!mportant;">';
        $html .= '      <tr>';
        $html .= '        <td style="font-family:sans-serif;font-size:14px;vertical-align:top;">&nbsp;</td>';
        $html .= '        <td class="container" style="font-family:sans-serif;font-size:14px;vertical-align:top;display:inline;max-width:80%;padding:10px;width:80%;Margin:0 auto !important;">';
        $html .= '          <div class="content" style="box-sizing:border-box;display:inline;Margin:0 auto;max-width:100%;padding:10px;">';
        $html .= '            <!-- START CENTERED WHITE CONTAINER -->';
        $html .= '            <span class="preheader" style="color:transparent;display:none;height:0;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;visibility:hidden;width:0;">BWIC NOTIFICATION</span>';
        $html .= '            <table class="main" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;background:#fff;border-radius:3px;width:100%;">';
        $html .= '              <!-- START MAIN CONTENT AREA -->';
        $html .= '              <tr>';
        $html .= '                <td class="wrapper" style="font-family:sans-serif;font-size:14px;vertical-align:top;box-sizing:border-box;padding:20px;">';
        $html .= '                  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;">';
        $html .= '                    <tr>';
        $html .= '                      <td style="font-family:sans-serif;font-size:14px;vertical-align:top;text-align:center;">';
        $html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;text-align:left;">Dear '.$seller_name.',</p>';
        $html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;text-align:left;"><strong>'.$bwic_title.'</strong> has closed and below is the summary.  You will receive trade confirmation(s) settlement instructions for any bonds that traded.</p>';
        $html .= '                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;box-sizing:border-box;width:100%; margin:auto;">';
        $html .= '                          <tbody>';
        $html .= '                            <tr>';
        $html .= '                              <td align="left" style="font-family:sans-serif;font-size:14px;vertical-align:top;padding-bottom:15px;">';
        $html .= '                                <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;width:auto; margin:auto;">';
        $html .= '                                  <thead>';
        $html .= '                                      <th style="padding:0 15px">Type</th>';
        $html .= '                                      <th style="padding:0 15px">Orig Rating</th>';
        $html .= '                                      <th style="padding:0 15px">CUSIP/ISIN</th>';
        $html .= '                                      <th style="padding:0 15px">TICKER</th>';
        $html .= '                                      <th style="padding:0 15px">Orig Size (MM)</th>';
        $html .= '                                      <th style="padding:0 15px">Curr Size (MM)</th>';
        $html .= '                                      <th style="padding:0 15px">Winning Bid</th>';
        $html .= '                                  </thead>';
        $html .= '                                  <tbody>';

        $seller_bonds   =   get_post_meta($auction->ID, '_auction_meta_field_value', true);
        foreach ( $seller_bonds as $bond ) {

                $list_id        =    $bond;
                $type           =    get_post_meta($bond,"type",true);
                $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                $ticker         =    get_post_meta($bond,"ticker",true);
                $c_e            =    get_post_meta($bond,"c/e",true);
                $cusip_isin     =    get_post_meta($bond,"cusip/isin",true);
                $orig_size      =    get_post_meta($bond,"orig_size_(mm)",true);
                $curr_size      =    get_post_meta($bond,"curr_size_(mm)",true);
                $manager        =    get_post_meta($bond,"manager",true);
                $status         =    get_post_meta($bond,"status",true);

                $meta_key       =   'user_winning_bid_'.$auction->ID.'_'.$bond;
                $winner         =   get_post_meta($auction->ID,$meta_key, true);

                $winnerID       =   $winner[$auction->ID][$bond]['user_id'];
                $winningAmount  =   $winner[$auction->ID][$bond]['amount'];

                if($winningAmount){
                    $winningAmount = '$'.$winningAmount;
                }else{
                    $winningAmount = 'DNT';
                }

                $pos = strpos($orig_size , "$");

                $pos1 = strpos($curr_size , "$");

                if($pos === false){
                    $orig_size = '$'.$orig_size;
                }

                if($pos1 === false){
                    $curr_size = '$'.$curr_size;
                }

                $html    .=   '<tr>';
                $html     .=                            '<td style="padding:0 15px">'.$type.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$orig_rating.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$cusip_isin.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$ticker.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$orig_size.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$curr_size.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$winningAmount.'</td>';
                $html    .=   '</tr>';
        }

        $html .= '                                  </tbody>';
        $html .= '                                </table>';
        $html .= '                              </td>';
        $html .= '                            </tr>';
        $html .= '                          </tbody>';
        $html .= '                        </table>';
        $html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;text-align:left;">Thanks for posting,</p>';
        $html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;text-align:left;">BEX Markets Team</p>';
        $html .= '                      </td>';
        $html .= '                    </tr>';
        $html .= '                  </table>';
        $html .= '                </td>';
        $html .= '              </tr>';
        $html .= '            </table>';
        $html .= '            <div class="footer" style="clear:both;padding-top:10px;text-align:center;width:100%;">';
        $html .= '              <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;">';
        $html .= '                <tr>';
        $html .= '                  <td class="content-block" style="font-family:sans-serif;font-size:14px;vertical-align:top;color:#999999;font-size:12px;text-align:center;">';
        $html .= '                    <span class="apple-link" style="color:#999999;font-size:12px;text-align:center;">BEX Market</span>';
        $html .= '                  </td>';
        $html .= '                </tr>';
        $html .= '              </table>';
        $html .= '            </div>';
        $html .= '          </div>';
        $html .= '        </td>';
        $html .= '        <td style="font-family:sans-serif;font-size:14px;vertical-align:top;">&nbsp;</td>';
        $html .= '      </tr>';
        $html .= '    </table>';
        $html .= '  </body>';
        $html .= '</html>';

        wp_mail( $seller_email, $subject, $html,'Content-type:text/html;charset=iso-8859-1',array());
        echo "<p>Notification sent to Seller1: $seller_email</p>";
    }


    public static  function sendNotificationSummaryAdmin($auction){

        $seller_id          =   $auction->post_author;
        $seller_email       =   get_the_author_meta('user_email', $seller_id);
        $seller_name        =   get_the_author_meta('display_name', $seller_id);
        $seller_company     =   get_the_author_meta('user_url', $seller_id);

        $admin_email    =   get_option( 'admin_email' );

        $bwic_title     =   get_post_meta($auction->ID, 'bwic_title', true);

        $subject        = "Notification: Summary BWIC Completed | ".$bwic_title.".";

        $html  = '';
        $html .= '<!DOCTYPE html>';
        $html .= '<html>';
        $html .= '  <head>';
        $html .= '    <meta name="viewport" content="width=device-width">';
        $html .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $html .= '    <title>BWIC Notification</title>';
        $html .= '    <style type="text/css">';
        $html .= '    @media only screen and (max-width: 620px) {';
        $html .= '      table[class=body] h1 {';
        $html .= '        font-size: 28px !important;';
        $html .= '        margin-bottom: 10px !important; }';
        $html .= '      table[class=body] p,';
        $html .= '      table[class=body] ul,';
        $html .= '      table[class=body] ol,';
        $html .= '      table[class=body] td,';
        $html .= '      table[class=body] span,';
        $html .= '      table[class=body] a {';
        $html .= '        font-size: 16px !important; }';
        $html .= '      table[class=body] .wrapper,';
        $html .= '      table[class=body] .article {';
        $html .= '        padding: 10px !important; }';
        $html .= '      table[class=body] .content {';
        $html .= '        padding: 0 !important; }';
        $html .= '      table[class=body] .container {';
        $html .= '        padding: 0 !important;';
        $html .= '        width: 100% !important; }';
        $html .= '      table[class=body] .main {';
        $html .= '        border-left-width: 0 !important;';
        $html .= '        border-radius: 0 !important;';
        $html .= '        border-right-width: 0 !important; }';
        $html .= '      table[class=body] .btn table {';
        $html .= '        width: 100% !important; }';
        $html .= '      table[class=body] .btn a {';
        $html .= '        width: 100% !important; }';
        $html .= '      table[class=body] .img-responsive {';
        $html .= '        height: auto !important;';
        $html .= '        max-width: 100% !important;';
        $html .= '        width: auto !important; }}';
        $html .= '    @media all {';
        $html .= '      .ExternalClass {';
        $html .= '        width: 100%; }';
        $html .= '      .ExternalClass,';
        $html .= '      .ExternalClass p,';
        $html .= '      .ExternalClass span,';
        $html .= '      .ExternalClass font,';
        $html .= '      .ExternalClass td,';
        $html .= '      .ExternalClass div {';
        $html .= '        line-height: 100%; }';
        $html .= '      .apple-link a {';
        $html .= '        color: inherit !important;';
        $html .= '        font-family: inherit !important;';
        $html .= '        font-size: inherit !important;';
        $html .= '        font-weight: inherit !important;';
        $html .= '        line-height: inherit !important;';
        $html .= '        text-decoration: none !important; }';
        $html .= '      .btn-primary a:hover {';
        $html .= '        background-color: #34495e !important;';
        $html .= '        border-color: #34495e !important; } }';
        $html .= '    </style>';
        $html .= '  </head>';
        $html .= '  <body class="" style="background-color:#f6f6f6;font-family:sans-serif;-webkit-font-smoothing:antialiased;font-size:14px;line-height:1.4;margin:0;padding:0;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;">';
        $html .= '    <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;background-color:#f6f6f6;width:95%;margin:0 auto!mportant;">';
        $html .= '      <tr>';
        $html .= '        <td style="font-family:sans-serif;font-size:14px;vertical-align:top;">&nbsp;</td>';
        $html .= '        <td class="container" style="font-family:sans-serif;font-size:14px;vertical-align:top;display:inline;max-width:80%;padding:10px;width:80%;Margin:0 auto !important;">';
        $html .= '          <div class="content" style="box-sizing:border-box;display:inline;Margin:0 auto;max-width:100%;padding:10px;">';
        $html .= '            <!-- START CENTERED WHITE CONTAINER -->';
        $html .= '            <span class="preheader" style="color:transparent;display:none;height:0;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;visibility:hidden;width:0;">BWIC NOTIFICATION</span>';
        $html .= '            <table class="main" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;background:#fff;border-radius:3px;width:100%;">';
        $html .= '              <!-- START MAIN CONTENT AREA -->';
        $html .= '              <tr>';
        $html .= '                <td class="wrapper" style="font-family:sans-serif;font-size:14px;vertical-align:top;box-sizing:border-box;padding:20px;">';
        $html .= '                  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;">';
        $html .= '                    <tr>';
        $html .= '                      <td style="font-family:sans-serif;font-size:14px;vertical-align:top;text-align:center;">';
        $html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;text-align:left;">Dear '.$admin_email.',</p>';
        $html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;text-align:left;"><strong>'.$bwic_title.'</strong> has closed and below are the results:</p>';
        $html .= '                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;box-sizing:border-box;width:100%; margin:auto;">';
        $html .= '                          <tbody>';
        $html .= '                            <tr>';
        $html .= '                              <td align="left" style="font-family:sans-serif;font-size:14px;vertical-align:top;padding-bottom:15px;">';
        $html .= '                                <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;width:auto; margin:auto;">';
        $html .= '                                  <thead>';
        $html .= '                                      <th style="padding:0 15px">Type</th>';
        $html .= '                                      <th style="padding:0 15px">Orig Rating</th>';
        $html .= '                                      <th style="padding:0 15px">CUSIP/ISIN</th>';
        $html .= '                                      <th style="padding:0 15px">TICKER</th>';
        $html .= '                                      <th style="padding:0 15px">Orig Size (MM)</th>';
        $html .= '                                      <th style="padding:0 15px">Curr Size (MM)</th>';
        $html .= '                                      <th style="padding:0 15px">Winning Bid</th>';

        $html .= '                                      <th style="padding:0 15px;background-color: yellow;">Seller Name</th>';
        $html .= '                                      <th style="padding:0 15px;background-color: yellow;">Seller Email</th>';
        $html .= '                                      <th style="padding:0 15px;background-color: yellow;">Seller Company</th>';

        $html .= '                                      <th style="padding:0 15px;background-color: #EFF0F1;">User Name</th>';
        $html .= '                                      <th style="padding:0 15px;background-color: #EFF0F1;">User Email</th>';
        $html .= '                                      <th style="padding:0 15px;background-color: #EFF0F1;">User Company</th>';

        $html .= '                                  </thead>';
        $html .= '                                  <tbody>';

        $seller_bonds   =   get_post_meta($auction->ID, '_auction_meta_field_value', true);
        foreach ( $seller_bonds as $bond ) {

                $list_id        =    $bond;
                $type           =    get_post_meta($bond,"type",true);
                $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                $ticker         =    get_post_meta($bond,"ticker",true);
                $c_e            =    get_post_meta($bond,"c/e",true);
                $cusip_isin     =    get_post_meta($bond,"cusip/isin",true);
                $orig_size      =    get_post_meta($bond,"orig_size_(mm)",true);
                $curr_size      =    get_post_meta($bond,"curr_size_(mm)",true);
                $manager        =    get_post_meta($bond,"manager",true);
                $status         =    get_post_meta($bond,"status",true);

                $user_email     =   'None';
                $user_name      =   'None';
                $user_company   =   'None';

                $meta_key       =   'user_winning_bid_'.$auction->ID.'_'.$bond;
                $winner         =   get_post_meta($auction->ID,$meta_key, true);

                $winnerID       =   $winner[$auction->ID][$bond]['user_id'];
                $winningAmount  =   $winner[$auction->ID][$bond]['amount'];

                if($winningAmount){
                    $winningAmount = '$'.$winningAmount;

                    $user_email       =   get_the_author_meta('user_email', $winnerID);
                    $user_name        =   get_the_author_meta('display_name', $winnerID);
                    $user_company     =   get_the_author_meta('user_url', $winnerID);

                }else{
                    $winningAmount = 'DNT';
                }

                $pos = strpos($orig_size , "$");

                $pos1 = strpos($curr_size , "$");

                if($pos === false){
                    $orig_size = '$'.$orig_size;
                }

                if($pos1 === false){
                    $curr_size = '$'.$curr_size;
                }

                $html    .=   '<tr>';
                $html     .=                            '<td style="padding:0 15px">'.$type.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$orig_rating.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$cusip_isin.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$ticker.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$orig_size.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$curr_size.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$winningAmount.'</td>';

                $html     .=                            '<td style="padding:0 15px;">'.$seller_name.'</td>';
                $html     .=                            '<td style="padding:0 15px;">'.$seller_email.'</td>';
                $html     .=                            '<td style="padding:0 15px;">'.$seller_company.'</td>';

                $html     .=                            '<td style="padding:0 15px">'.$user_name.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$user_email.'</td>';
                $html     .=                            '<td style="padding:0 15px">'.$user_company.'</td>';
                $html    .=   '</tr>';
        }

        $html .= '                                  </tbody>';
        $html .= '                                </table>';
        $html .= '                              </td>';
        $html .= '                            </tr>';
        $html .= '                          </tbody>';
        $html .= '                        </table>';
        $html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;text-align:left;">Please send trade confirmations and settlement instructions to appropriate parties.</p>';
        $html .= '                      </td>';
        $html .= '                    </tr>';
        $html .= '                  </table>';
        $html .= '                </td>';
        $html .= '              </tr>';
        $html .= '            </table>';
        $html .= '            <div class="footer" style="clear:both;padding-top:10px;text-align:center;width:100%;">';
        $html .= '              <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;">';
        $html .= '                <tr>';
        $html .= '                  <td class="content-block" style="font-family:sans-serif;font-size:14px;vertical-align:top;color:#999999;font-size:12px;text-align:center;">';
        $html .= '                    <span class="apple-link" style="color:#999999;font-size:12px;text-align:center;">BEX Market</span>';
        $html .= '                  </td>';
        $html .= '                </tr>';
        $html .= '              </table>';
        $html .= '            </div>';
        $html .= '          </div>';
        $html .= '        </td>';
        $html .= '        <td style="font-family:sans-serif;font-size:14px;vertical-align:top;">&nbsp;</td>';
        $html .= '      </tr>';
        $html .= '    </table>';
        $html .= '  </body>';
        $html .= '</html>';

        wp_mail( $admin_email, $subject, $html,'Content-type:text/html;charset=iso-8859-1',array());
        echo "<p>Notification sent to admin1: $admin_email</p>";
    }


    public static  function sendNotificationSummaryUsers($auction, $user_id ){

        $seller_id      =   $auction->post_author;

        $user_email     =   get_the_author_meta('user_email', $user_id);
        $user_name      =   get_the_author_meta('display_name', $user_id);

        $bwic_title     =   get_post_meta($auction->ID, 'bwic_title', true);

          $subject      =   "Notification: Summary BWIC Completed | ".$bwic_title.".";

        $html  = '';
        $html .= '<!DOCTYPE html>';
        $html .= '<html>';
        $html .= '  <head>';
        $html .= '    <meta name="viewport" content="width=device-width">';
        $html .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $html .= '    <title>BWIC Notification</title>';
        $html .= '    <style type="text/css">';
        $html .= '    @media only screen and (max-width: 620px) {';
        $html .= '      table[class=body] h1 {';
        $html .= '        font-size: 28px !important;';
        $html .= '        margin-bottom: 10px !important; }';
        $html .= '      table[class=body] p,';
        $html .= '      table[class=body] ul,';
        $html .= '      table[class=body] ol,';
        $html .= '      table[class=body] td,';
        $html .= '      table[class=body] span,';
        $html .= '      table[class=body] a {';
        $html .= '        font-size: 16px !important; }';
        $html .= '      table[class=body] .wrapper,';
        $html .= '      table[class=body] .article {';
        $html .= '        padding: 10px !important; }';
        $html .= '      table[class=body] .content {';
        $html .= '        padding: 0 !important; }';
        $html .= '      table[class=body] .container {';
        $html .= '        padding: 0 !important;';
        $html .= '        width: 100% !important; }';
        $html .= '      table[class=body] .main {';
        $html .= '        border-left-width: 0 !important;';
        $html .= '        border-radius: 0 !important;';
        $html .= '        border-right-width: 0 !important; }';
        $html .= '      table[class=body] .btn table {';
        $html .= '        width: 100% !important; }';
        $html .= '      table[class=body] .btn a {';
        $html .= '        width: 100% !important; }';
        $html .= '      table[class=body] .img-responsive {';
        $html .= '        height: auto !important;';
        $html .= '        max-width: 100% !important;';
        $html .= '        width: auto !important; }}';
        $html .= '    @media all {';
        $html .= '      .ExternalClass {';
        $html .= '        width: 100%; }';
        $html .= '      .ExternalClass,';
        $html .= '      .ExternalClass p,';
        $html .= '      .ExternalClass span,';
        $html .= '      .ExternalClass font,';
        $html .= '      .ExternalClass td,';
        $html .= '      .ExternalClass div {';
        $html .= '        line-height: 100%; }';
        $html .= '      .apple-link a {';
        $html .= '        color: inherit !important;';
        $html .= '        font-family: inherit !important;';
        $html .= '        font-size: inherit !important;';
        $html .= '        font-weight: inherit !important;';
        $html .= '        line-height: inherit !important;';
        $html .= '        text-decoration: none !important; }';
        $html .= '      .btn-primary a:hover {';
        $html .= '        background-color: #34495e !important;';
        $html .= '        border-color: #34495e !important; } }';
        $html .= '    </style>';
        $html .= '  </head>';
        $html .= '  <body class="" style="background-color:#f6f6f6;font-family:sans-serif;-webkit-font-smoothing:antialiased;font-size:14px;line-height:1.4;margin:0;padding:0;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;">';
        $html .= '    <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;background-color:#f6f6f6;width:95%;margin:0 auto!mportant;">';
        $html .= '      <tr>';
        $html .= '        <td style="font-family:sans-serif;font-size:14px;vertical-align:top;">&nbsp;</td>';
        $html .= '        <td class="container" style="font-family:sans-serif;font-size:14px;vertical-align:top;display:inline;max-width:80%;padding:10px;width:80%;Margin:0 auto !important;">';
        $html .= '          <div class="content" style="box-sizing:border-box;display:inline;Margin:0 auto;max-width:100%;padding:10px;">';
        $html .= '            <!-- START CENTERED WHITE CONTAINER -->';
        $html .= '            <span class="preheader" style="color:transparent;display:none;height:0;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;visibility:hidden;width:0;">BWIC NOTIFICATION</span>';
        $html .= '            <table class="main" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;background:#fff;border-radius:3px;width:100%;">';
        $html .= '              <!-- START MAIN CONTENT AREA -->';
        $html .= '              <tr>';
        $html .= '                <td class="wrapper" style="font-family:sans-serif;font-size:14px;vertical-align:top;box-sizing:border-box;padding:20px;">';
        $html .= '                  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;">';
        $html .= '                    <tr>';
        $html .= '                      <td style="font-family:sans-serif;font-size:14px;vertical-align:top;text-align:center;">';
        $html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;text-align:left;">Dear '.$user_name.',</p>';
        $html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;text-align:left;"><strong>'.$bwic_title.'</strong> has closed and below is the summary.  If you won any of the bonds, the BEX Markets team will follow up with trade confirmation(s) and settlement instructions.</p>';
        $html .= '                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;box-sizing:border-box;width:100%; margin:auto;">';
        $html .= '                          <tbody>';
        $html .= '                            <tr>';
        $html .= '                              <td align="left" style="font-family:sans-serif;font-size:14px;vertical-align:top;padding-bottom:15px;">';
        $html .= '                                <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;width:auto; margin:auto;">';
        $html .= '                                  <thead>';
        $html .= '                                      <th style="padding:0 15px">Type</th>';
        $html .= '                                      <th style="padding:0 15px">Orig Rating</th>';
        $html .= '                                      <th style="padding:0 15px">CUSIP/ISIN</th>';
        $html .= '                                      <th style="padding:0 15px">TICKER</th>';
        $html .= '                                      <th style="padding:0 15px">Orig Size (MM)</th>';
        $html .= '                                      <th style="padding:0 15px">Curr Size (MM)</th>';
        $html .= '                                      <th style="padding:0 15px">Winning Bid</th>';
        $html .= '                                  </thead>';
        $html .= '                                  <tbody>';

        $seller_bonds   =   get_post_meta($auction->ID, '_auction_meta_field_value', true);
        foreach ( $seller_bonds as $bond ) {

                $list_id        =    $bond;
                $type           =    get_post_meta($bond,"type",true);
                $orig_rating    =    get_post_meta($bond,"orig_rating",true);
                $ticker         =    get_post_meta($bond,"ticker",true);
                $c_e            =    get_post_meta($bond,"c/e",true);
                $cusip_isin     =    get_post_meta($bond,"cusip/isin",true);
                $orig_size      =    get_post_meta($bond,"orig_size_(mm)",true);
                $curr_size      =    get_post_meta($bond,"curr_size_(mm)",true);
                $manager        =    get_post_meta($bond,"manager",true);


                $status         =   get_post_meta($bond,"status",true);

                $meta_key       =   'user_winning_bid_'.$auction->ID.'_'.$bond;
                $winner         =   get_post_meta($auction->ID,$meta_key, true);

                $winnerID       =   $winner[$auction->ID][$bond]['user_id'];
                $winningAmount  =   $winner[$auction->ID][$bond]['amount'];

                if($winningAmount){
                    $winningAmount = '$'.$winningAmount;
                }else{
                    $winningAmount = 'DNT';
                }

                $html    .=   '<tr>';
                $html     .=      '<td style="padding:0 15px">'.$type.'</td>';
                $html     .=      '<td style="padding:0 15px">'.$orig_rating.'</td>';
                $html     .=      '<td style="padding:0 15px">'.$cusip_isin.'</td>';
                $html     .=      '<td style="padding:0 15px">'.$ticker.'</td>';
                $html     .=      '<td style="padding:0 15px">$'.$orig_size.'</td>';
                $html     .=      '<td style="padding:0 15px">$'.$curr_size.'</td>';
                $html     .=      '<td style="padding:0 15px">'.$winningAmount.'</td>';
                $html    .=   '</tr>';
        }

        $html .= '                                  </tbody>';
        $html .= '                                </table>';
        $html .= '                              </td>';
        $html .= '                            </tr>';
        $html .= '                          </tbody>';
        $html .= '                        </table>';
        $html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;text-align:left;">Thanks for bidding,</p>';
        $html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;text-align:left;">BEX Markets Team</p>';
        $html .= '                      </td>';
        $html .= '                    </tr>';
        $html .= '                  </table>';
        $html .= '                </td>';
        $html .= '              </tr>';
        $html .= '            </table>';
        $html .= '            <div class="footer" style="clear:both;padding-top:10px;text-align:center;width:100%;">';
        $html .= '              <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;">';
        $html .= '                <tr>';
        $html .= '                  <td class="content-block" style="font-family:sans-serif;font-size:14px;vertical-align:top;color:#999999;font-size:12px;text-align:center;">';
        $html .= '                    <span class="apple-link" style="color:#999999;font-size:12px;text-align:center;">BEX Market</span>';
        $html .= '                  </td>';
        $html .= '                </tr>';
        $html .= '              </table>';
        $html .= '            </div>';
        $html .= '          </div>';
        $html .= '        </td>';
        $html .= '        <td style="font-family:sans-serif;font-size:14px;vertical-align:top;">&nbsp;</td>';
        $html .= '      </tr>';
        $html .= '    </table>';
        $html .= '  </body>';
        $html .= '</html>';

        wp_mail( $user_email, $subject, $html,'Content-type:text/html;charset=iso-8859-1',array());
        echo "<p>Notification sent to user1: $user_email</p>";
    }

}/* class ends here */

?>
