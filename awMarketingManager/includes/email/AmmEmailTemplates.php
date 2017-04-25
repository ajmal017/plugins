<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AmmEmailTemplates {
    
   public static function emmailTemplateHeader( $author_name = '',$message ) {
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
            $html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:font-normal;color:#000000;margin:0;Margin-bottom:15px;text-align:left;">Dear '.$author_name.', ';

            $html .=                    $message['header'];
            $html .= '                          </p>';
            //$html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;text-align:left;"><strong>'.$bwic_title.'</strong> has closed and below are the results:</p>';
            $html .= '                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;box-sizing:border-box;width:100%; margin:auto;">';
            $html .= '                          <tbody>';
            $html .= '                            <tr>';
            $html .= '                              <td align="left" style="font-family:sans-serif;font-size:14px;vertical-align:top;padding-bottom:15px;">';
            $html .= '                                <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;width:auto; margin:auto;">';


            return $html;
    }


    public static function emmailTemplateFooter($message) {
        $html = '';        
        $html .= '                                </table>';
        $html .= '                              </td>';
        $html .= '                            </tr>';
        $html .= '                          </tbody>';
        $html .= '                        </table>';
        $html .= '                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;text-align:left;">';

        $html .=                            $message['footer'];

        $html .= '                          .</p>';
        $html .= '                       </td>';
        $html .= '                    </tr>';
        $html .= '                  </table>';
        $html .= '                </td>';
        $html .= '              </tr>';
        $html .= '            </table>';
        $html .= '            <div class="footer" style="clear:both;padding-top:10px;text-align:center;width:100%;">';
        $html .= '              <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;">';
        $html .= '                <tr>';
        $html .= '                  <td class="content-block" style="font-family:sans-serif;font-size:14px;vertical-align:top;color:#999999;font-size:12px;text-align:center;">';
        $html .= '                    <span class="apple-link" style="color:#999999;font-size:12px;text-align:center;">The BEX Markets Team</span>';
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

        return $html;

        
    }

    public static function emmailTemplateThead($thead) {

        $html .= '<thead>';

        foreach($thead as $th){

                 $html .= '<th style="padding:0 15px">'.$th.'</th>';
        }
       
        
        $html .= '</thead>';

        return $html;

    }

    public static function emmailTemplateTbody($tbody) {

        $html .= '<tr>';

        foreach($tbody as $td){

                 $html .= '<td style="padding:0 15px">'.$td.'</td>';
        }
       
        
        $html .= '</tr>';

        return $html;




    }



}/* class ends here */

?>
