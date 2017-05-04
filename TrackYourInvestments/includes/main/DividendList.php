<?php
ob_start();
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class DividendList extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'Site', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Sites', 'sp' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		) );

	}

	/**0
	 * Retrieve customers data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_customers( $per_page = 10, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}dividend";

		if ( ! empty( $_REQUEST['s'] ) ) {
			$sql .= ' WHERE company_name LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `dividend_id` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `siblings` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `key` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'			
			OR `record_date` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `ex_date` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `annc_type` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `datepaid_payable` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `currency` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `value` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `tax` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `interest_start` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `interest_start` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `interest_end` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `interest_rate` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `remarks` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `base` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `giving` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `price` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `before_consolidation` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `after_consolidation` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `particulars` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\' ';
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}else{
			// $sql .= ' ORDER BY site_name ';
			$sql .= ' ORDER BY dividend_id DESC ';
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

		$wpdb->delete(
			"{$wpdb->prefix}dividend",
			array( 'dividend_id' => $id ),
			array( '%d' )
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}dividend";


		if ( ! empty( $_REQUEST['s'] ) ) {
			$sql .= ' WHERE company_name LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `dividend_id` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `siblings` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `key` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'			
			OR `record_date` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `ex_date` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `annc_type` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `datepaid_payable` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `currency` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `value` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `tax` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `interest_start` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `interest_start` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `interest_end` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `interest_rate` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `remarks` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `base` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `giving` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `price` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `before_consolidation` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `after_consolidation` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\'
			OR `particulars` LIKE \'%' . esc_sql( $_REQUEST['s'] ) .  '%\' ';
		}

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no customer data is available */
	public function no_items() {
		_e( 'No Records Found.', 'sp' );
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
		switch ( $column_name ) {
			case 'dividend_id':
        			return $item[ 'dividend_id' ];
			case 'siblings':
			        return $item[ 'siblings' ];
			case 'key':
			        return $item[ 'key' ];
			case 'company_name':
			        return $item[ 'company_name' ];
			case 'record_date':
			        return $item[ 'record_date' ];
			case 'ex_date':
			        return $item[ 'ex_date' ];
			case 'annc_type':
			        return $item[ 'annc_type' ];
			case 'datepaid_payable':
			        return $item[ 'datepaid_payable' ];
			case 'currency':
			        return $item[ 'currency' ];
			case 'value':
			        return $item[ 'value' ];
			case 'tax':
			        return $item[ 'tax' ];
			case 'interest_start':
			        return $item[ 'interest_start' ];
			case 'interest_start':
			        return $item[ 'interest_start' ];
			case 'interest_end':
			        return $item[ 'interest_end' ];
			case 'interest_rate':
			        return $item[ 'interest_rate' ];
			case 'remarks':
			        return $item[ 'remarks' ];
			case 'base':
			        return $item[ 'base' ];
			case 'giving':
			        return $item[ 'giving' ];
			case 'price':
			        return $item[ 'price' ];
			case 'before_consolidation':
			        return $item[ 'before_consolidation' ];
			case 'after_consolidation':
			        return $item[ 'after_consolidation' ];
			case 'particulars':
			        return $item[ 'particulars' ];
			case 'actions':
				$deleteMesage 		= 	"'Are you sure you want to DELETE Divident?'";
				$deletUrl 			=	$_SERVER['REQUEST_URI']."&action=delete&id=".$item['dividend_id'];
				$dividend_id 		= 	$item['dividend_id'];
				
				return '<button name="dividend-del" type="button" value="'.$dividend_id.'" onclick="return confirm('.$deleteMesage.')" style="color: red;" class="button dividend-del" title="Delete this Dividend"><b>DELETE</b></button>';

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
		//return sprintf( $item['dividend_id'] );
		return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['dividend_id']); 
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_dividend_id( $item ) {

		$delete_nonce = wp_create_nonce( 'sp_delete_customer' );

		$title = '<strong>'. $item['dividend_id'] .'</strong>';

		$actions = array(
			'edit'=>sprintf('<a href="?page=%s&id=%s">Edit</a>','edit_dividend', absint( $item['dividend_id']) ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Trash</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['dividend_id'] ), $delete_nonce )
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
			'cb' 					=> '<input type="checkbox" />',
			'dividend_id'           => __( 'Dividend Id', 'sp' ),
			'siblings'              => __( 'Siblings', 'sp' ),
			'key'                   => __( 'Key', 'sp' ),
			'company_name'          => __( 'Company name', 'sp' ),
			'record_date'           => __( 'Record_Date', 'sp' ),
			'ex_date'               => __( 'Ex Date', 'sp' ),
			'annc_type'             => __( 'Annc Type', 'sp' ),
			'datepaid_payable'      => __( 'Datepaid payable', 'sp' ),
			'currency'              => __( 'Currency', 'sp' ),
			'value'                 => __( 'Value', 'sp' ),
			'tax'                   => __( 'tax', 'sp' ),
			'interest_start'        => __( 'Interest Start', 'sp' ),
			'interest_end'          => __( 'Interest End', 'sp' ),
			'interest_rate'         => __( 'Interest Rate', 'sp' ),
			'remarks'               => __( 'Remarks', 'sp' ),
			'base'                  => __( 'Base', 'sp' ),
			'giving'                => __( 'Giving', 'sp' ),
			'price'                 => __( 'Price', 'sp' ),
			'before_consolidation'  => __( 'Before Consolidation', 'sp' ),
			'after_consolidation'   => __( 'After Consolidation', 'sp' ),
			'particulars'           => __( 'Particulars', 'sp' ),
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
			'dividend_id'           => array( 'dividend_id', false ),
			'siblings'              => array( 'siblings', false ),
			'key'                   => array( 'key', false ),
			'company_name'          => array( 'company_name', false ),
			'record_date'           => array( 'record_date', false ),
			'ex_date'               => array( 'ex_date', false ),
			'annc_type'             => array( 'annc_type', false ),
			'datepaid_payable'      => array( 'datepaid_payable', false ),
			'currency'              => array( 'currency', false ),
			'value'                 => array( 'value', false ),
			'tax'                   => array( 'tax', false ),
			'interest_start'        => array( 'interest_start', false ),
			'interest_end'          => array( 'interest_end', false ),
			'interest_rate'         => array( 'interest_rate', false ),
			'remarks'               => array( 'remarks', false ),
			'base'                  => array( 'base', false ),
			'giving'                => array( 'giving', false ),
			'price'                 => array( 'price', false ),
			'before_consolidation'  => array( 'before_consolidation', false ),
			'after_consolidation'   => array( 'after_consolidation', false ),
			'particulars'           => array( 'particulars', false )
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
		 	'bulk-delete' => 'Delete'
		 ];

		 return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items($search = '') {
		
		add_query_arg('s',$search,$_SERVER['QUERY_STRING']);
		$columns 				= $this->get_columns();
		$hidden 				=  get_hidden_columns( $this->screen );
			
  		$sortable 				= $this->get_sortable_columns();
  		$this->_column_headers 	= array($columns, $hidden, $sortable);
		$this->process_bulk_action();

		$per_page     			= $this->get_items_per_page( 'dividend_per_page', 10 );
		$current_page 			= $this->get_pagenum();
		$total_items  			= self::record_count();

		$this->set_pagination_args( array(
			'total_items' => $total_items, 
			'per_page'    => $per_page, 
			'total_pages' => ceil( $total_items / $per_page ),
		) );

		$this->items 			= self::get_customers( $per_page, $current_page );

	}

	/*public function search_box( $text, $input_id ) {
        if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
            return;
 
        $input_id = $input_id . '-search-input';
 
        if ( ! empty( $_REQUEST['orderby'] ) )
            echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
        if ( ! empty( $_REQUEST['order'] ) )
            echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
        if ( ! empty( $_REQUEST['post_mime_type'] ) )
            echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
        if ( ! empty( $_REQUEST['detached'] ) )
            echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
        ?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
            <?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
        </p>
        <?php
    }*/

	/*function set_pagination_args( $args ) {
	    $args = wp_parse_args( $args, array() );
	 	if ( !$args['total_pages'] && $args['per_page'] > 0 )
	        $args['total_pages'] = ceil( $args['total_items'] / $args['per_page'] );
	 
	    // Redirect if page number is invalid and headers are not already sent.
	    if ( ! headers_sent() && ! wp_doing_ajax() && $args['total_pages'] > 0 && $this->get_pagenum() > $args['total_pages'] ) {
	        wp_redirect( add_query_arg( 'paged',$args['total_pages']) ); 			 
	        exit;
	    }
	 	
	    $this->_pagination_args = $args;

	}*/
	
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
				wp_redirect( esc_url( add_query_arg() ) );
				exit;
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
