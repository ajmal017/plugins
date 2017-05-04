<?php
ob_start();
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class TransactionsList extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'Site', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Sites', 'sp' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		) );
	}


	/**
	 * Retrieve customers data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_customers( $per_page = 10, $page_number = 1 ) {

		global $wpdb;
		$investment_transations = $wpdb->prefix . 'investment_transations';
		$sql = "SELECT * FROM $investment_transations";

		if ( ! empty( $_REQUEST['s'] ) ) {
			$sql .= ' WHERE trade_date LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\' 
			OR `settlement_date` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\' 
			OR `equity` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\' 
			OR `ticker_symbol` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `currency` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\' 
			OR `broker` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'' ;
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}else{
			// $sql .= ' ORDER BY site_name ';
			$sql .= ' ORDER BY transaction_id DESC ';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		//print_r($result);
		return $result;
	}

	/**
	* Delete a customer record.
	*
	* @param int $id customer ID
	*/
	public static function delete_customer( $id ) {
		global $wpdb;

		/*$wpdb->delete(
			"{$wpdb->prefix}investment_transations",
			array( 'transaction_id' => $id ),
			array( '%d' )
		);*/
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$investment_transations = $wpdb->prefix . 'investment_transations';
		$sql = "SELECT COUNT(*) FROM $investment_transations";

		//$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}stocks";

		if ( ! empty( $_REQUEST['s'] ) ) {
			$sql .= ' WHERE trade_date LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\' 
			OR `settlement_date` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\' 
			OR `equity` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\' 
			OR `ticker_symbol` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `currency` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\' 
			OR `broker` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'' ;
		}

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no customer data is available */
	public function no_items() {
		_e( 'No Transactions Found.', 'sp' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {

		$t_type = array( 1 =>"Buy", 2 => "Sell");

		switch ( $column_name ) {
			case 'transaction_id':
				return $item[ 'transaction_id' ];
			case 'trade_date':
				return $item[ 'trade_date' ];
			case 'settlement_date':
				return $item[ 'settlement_date' ];
			case 'transaction_type':
				return $t_type[$item[ 'transaction_type' ]];
			case 'equity':
				return $item[ 'equity' ];
			case 'ticker_symbol':
				return $item[ 'ticker_symbol' ];
			case 'num_of_shares':
				return $item[ 'num_of_shares' ];
			case 'price':
				return $item[ 'price' ];
			case 'transaction_fees':
				return $item[ 'transaction_fees' ];
			case 'currency':
				return $item[ 'currency' ];
			case 'user_id':
				return $item[ 'user_id' ];
			case 'stock_id':
				return $item[ 'stock_id' ];
			case 'platform':
				return $item[ 'platform' ];
			case 'broker':
				return $item[ 'broker' ];
			case 'account_id':
				return $item[ 'account_id' ];
			case 'notes':
				return $item[ 'notes' ];
			/*case 'actions':
				$deleteMesage 		= 	"'Are you sure you want to DELETE Stock?'";
				$deletUrl 			=	$_SERVER['REQUEST_URI']."&action=delete&id=".$item['stock_id'];
				$stock_id 			= 	$item['stock_id'];*/
				//$SiteUrlAdmin 		=	$item['site_url']."wp-login.php";

				//return '<a href="'.$deletUrl.'"  onclick="return confirm('.$deleteMesage.')" style="color: red;" class="button" title="Delete this Blog"><b>Delete</b></a>';
				//return '<button name="stock-del" type="button" value="'.$stock_id.'" onclick="return confirm('.$deleteMesage.')" style="color: red;" class="button stock-del" title="Delete this Stock"><b>DELETE</b></button>';

			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		//return sprintf( $item['stock_id'] );
		//return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['transaction_id']); 
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_user_id( $item ) {

		$delete_nonce = wp_create_nonce( 'sp_delete_customer' );

		$title = '<strong>' . $item['user_id'] . '</strong>';

		$userPage = admin_url()."user-edit.php?user_id=".$item['user_id']."&wp_http_referer=/chuanjie/wp-admin/users.php?update=add";
		//$userPage = "http://ourgarage.in/chuanjie/wp-admin/user-edit.php?user_id=".$item['user_id']."&wp_http_referer=/chuanjie/wp-admin/users.php?update=add";
		$actions = array(
			'view'=>sprintf('<a href="'.$userPage.'">view</a>' )
		);
		return $title . $this->row_actions( $actions );
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			//'cb' 					=> '<input type="checkbox" />',
			'transaction_id' 		=> __('Transaction ID', 'sp'),	
			'trade_date' 			=> __('Trade Date', 'sp'),	
			'settlement_date' 		=> __('Settlement Date', 'sp'),	
			'transaction_type' 		=> __('Transaction Type', 'sp'),	
			'equity' 				=> __('Equity', 'sp'),	
			'ticker_symbol' 		=> __('Ticker Symbol', 'sp'),	
			'num_of_shares' 		=> __('Num. of Shares', 'sp'),	
			'price' 				=> __('Price', 'sp'),	
			'transaction_fees' 		=> __('Transaction Fees', 'sp'),	
			'currency' 				=> __('Currency', 'sp'),	
			'user_id' 				=> __('User ID', 'sp'),	
			'stock_id' 				=> __('Stock ID', 'sp'),	
			'platform' 				=> __('Platform', 'sp'),	
			'broker' 				=> __('Broker', 'sp'),	
			'account_id' 			=> __('Account ID', 'sp'),	
			'notes' 				=> __('Notes', 'sp'),
		);

		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'transaction_id'     	=> array( 'transaction_id', true ),
			'trade_date'     		=> array( 'trade_date', true ),
			'settlement_date'     	=> array( 'settlement_date', true ),
			'transaction_type'     	=> array( 'transaction_type', true ),
			'equity'     			=> array( 'equity', true ),
			'ticker_symbol'     	=> array( 'ticker_symbol', true ),
			'num_of_shares'     	=> array( 'num_of_shares', true ),
			'price'     			=> array( 'price', true ),
			'transaction_fees'     	=> array( 'transaction_fees', true ),
			'currency'     			=> array( 'currency', true ),
			'user_id'     			=> array( 'user_id', true ),
			'stock_id'     			=> array( 'stock_id', true ),
			'platform'     			=> array( 'platform', true ),
			'broker'     			=> array( 'broker', true ),
			'account_id'     		=> array( 'account_id', true ),
			'notes'     			=> array( 'notes', true ),
			//'exchange_name' => array( 'exchange_name', false )
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		 $actions = [
		 	'bulk-delete' => 'Delete',
		 ];

		 //return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$columns = $this->get_columns();
		$hidden =  get_hidden_columns( $this->screen );
  		$sortable = $this->get_sortable_columns();
  		$this->_column_headers = array($columns, $hidden, $sortable);
  		//$this->_column_headers = $this->get_column_info();
		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'transactions_per_page', 10 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		));

		$this->items = self::get_customers( $per_page, $current_page );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...

		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
			
			if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
				// die( 'Go get a life script kiddies' );
				// require dirname(__FILE__)."/functions/delete_site.php";
				echo("Deleted Successfully!");
			}
			else {
				self::delete_customer( absint( $_GET['customer'] ) );
				ob_clean();
				wp_redirect(esc_url(add_query_arg()));
				exit();
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_customer( $id );

			}

			wp_redirect( esc_url( add_query_arg() ) );
			exit;
		}
	}
}
?>
