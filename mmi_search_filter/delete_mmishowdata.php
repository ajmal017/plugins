<?php

require("/var/www/mindinmotion-online.com/wp-load.php");  	

//class Delete_MMIshowdata{
global $wpdb;
	function delete_record(){
		global $wpdb;
		
		
		if (isset($_POST['id'])) {
			$id 	= $_POST['id'];
			$del_id = $id;
			$query 	= $wpdb->query("DELETE FROM wp_mmi_save_search WHERE id=".$del_id);
			if($query){   // Just for testing
				return "Success";
			}else{
				return "Error deleting Data";
			}
		}	
	}
	delete_record();
//}
//$Delete_MMIshowdata = new Delete_MMIshowdata;
//$Delete_MMIshowdata->delete_record();

?>