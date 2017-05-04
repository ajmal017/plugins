<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'TransactionsList' ) ) {
    include_once('TransactionsList.php');
}

class Transactions {
    
    public static function transactionsRate(){
		global $wpdb;
        $pageURL        = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        Transactions::transactionsListing();     
    }

    public static function transactionsListing(){
    	add_action( 'admin_head',array('Transactions','admin_header'));
    	Transactions::newTransactionsList();
	}

    public static function set_screen( $status, $option, $value ) {
        if ( 'transactions_per_page' == $option ) 
            return $value;
        return $status;
    }

    public static function add_options() {
        global $transactionlisting;
       
        $option     = 'per_page';
        $args       = array(
                        'label' => 'Number of Transactions showing', 
                        'default' => 10, 
                        'option' => 'transactions_per_page' 
                    );
        add_screen_option( $option, $args );

        $transactionlisting = new TransactionsList();
    }

    public static function admin_header() {
        $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;

        if( 'transactions' != $page ){
            return;
        }
    }

    //** New Function for the listing of the Transactions. **//
    public static function newTransactionsList(){

        global $transactionlisting;

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Transactions</h1>
            <hr class="wp-header-end">
            <div id="poststuff">
                <div id="post-body" class="">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <?php
                                $transactionlisting = new TransactionsList();
                                $transactionlisting->prepare_items();
                                echo '<input type="hidden" name="page" value="'.$_REQUEST["page"].'">';
                                $transactionlisting->search_box( 'search', 'search_id_transactions' );

                                 if( isset( $_POST['s'] ) ){
                                    $new_url = admin_url().'admin.php?s='.$_POST['s'].'&page='.$_POST['page'];

                                    header('location:'.$new_url);
                                }

                                ?>
                            </form>
                             <?php echo $transactionlisting->display(); ?>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
        <?php
    }//** function newTransactionList ends **//


}/*class ends here*/
?>
