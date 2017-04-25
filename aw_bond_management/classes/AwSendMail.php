<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AwSendMail {

    public static function send_mail_to_winner($bidder_email, $bidder_msg){              
        $subject = "BWIC Result";   		   
        $email = wp_mail($bidder_email,$subject,$bidder_msg,'Content-type:text/html;charset=iso-8859-1',array()); 
        return $email;
    }

    public static function send_mail_to_loser(){
        $subject = "BWIC Result";    
        $email = wp_mail($to,$subject,$message,'Content-type:text/html;charset=iso-8859-1',array()); 
        return $email;
    }

    public static function send_mail_to_admin($admin_email, $admin_msg) {
        $subject = "BWIC Result";    
        $email = wp_mail($admin_email,$subject,$admin_msg,'Content-type:text/html;charset=iso-8859-1',array()); 
        return $email;
    }

	public static function send_mail_to_seller($seller_email, $seller_msg){
        $subject = "BWIC Result";    
        $email = wp_mail($seller_email,$subject,$seller_msg,'Content-type:text/html;charset=iso-8859-1',array());
        return $email;
    }
}/* class ends here */
?>