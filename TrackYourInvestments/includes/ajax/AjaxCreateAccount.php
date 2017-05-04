<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AjaxCreateAccount {
    //** Constructor **//
    public static function createAccounts(){
           
        if( isset($_POST['action']) ) {
            
            self::insertAccountDetails( $_POST );
            echo "accountcreated";
            die();
        }
    }

    public static function insertAccountDetails( $details ) {
        global $wpdb;
        
        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
        $wpdb->insert(
            $cj_user_portfolios,
            array(
                    'user_id'           =>  $details['userID'],
                    'account_name'      =>  $details['accountName'],
                    'description'       =>  $details['description'],
                    'recording_currency'=>  $details['recordingCurr'],
                    'created_date'      =>  $details['createdDate']
                ),
            array(
                    '%d',
                    '%s', 
                    '%s', 
                    '%s', 
                    '%s'
                )
            );
    }

    /* Account update functionality */
    public static function updateAccounts(){
           
        if( isset($_POST['action']) ) {
            
            self::updateAccountDetails( $_POST );
            echo "accountupdate";
            die();
        }
    }

    public static function updateAccountDetails( $details ) {
        global $wpdb;
        
        $cj_user_portfolios = $wpdb->prefix . 'cj_user_portfolios';
        $wpdb->update(
            $cj_user_portfolios,
            array(
                    'user_id'           =>  $details['userID'],
                    'account_name'      =>  $details['accountName'],
                    'description'       =>  $details['description'],
                    'recording_currency'=>  $details['recordingCurr'],
                    //'created_date'      =>  $details['createdDate']
                ),
            array( 'id' => $details['id'] ), 
            array(
                    '%d',
                    '%s', 
                    '%s', 
                    '%s'
                ),
            array( '%d' )
            );
    }
}/*class ends here*/
?>