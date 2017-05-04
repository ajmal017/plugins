<?php

require("/var/www/mindinmotion-online.com/wp-load.php");  	

class GetMMISavedSearchList{

	public static  function getList(){
		global $wpdb;

		$user_id   = get_current_user_id();
		$base_path = 'http://mindinmotion-online.com';
		$sql       = "SELECT * FROM wp_mmi_save_search WHERE user_id = ".$user_id ." ORDER BY id DESC";
		$results   = $wpdb->get_results( $sql, OBJECT );

		/*block to show the list of records*/

		if( !empty($results)){
				foreach ($results as $row) {
					
					$result[] = array(
				      'id' => $row->id,
				      'name' => $row->search_name,
				      'url'  => $base_path.$row->url

				    );
				}						

		}else{
				echo '<h2 style="text-align: center;">No saved search results.</h2>';
		}		
		
		echo json_encode($result);
	}
}/*class ends here. */

GetMMISavedSearchList::getList();
?>