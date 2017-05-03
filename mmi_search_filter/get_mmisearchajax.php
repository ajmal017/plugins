<?php
require("/var/www/mindinmotion-online.com/wp-load.php");  	
	global $wpdb;
	echo "hello";
	function get_mmi_ajax(){
		global $wpdb;
		$html = '';
		$user_id   = get_current_user_id();
		$base_path = 'http://mindinmotion-online.com';
		$sql       = "SELECT * FROM wp_mmi_save_search WHERE user_id = ".$user_id;
		$results   = $wpdb->get_results( $sql, OBJECT );
		//file_put_contents(dirname(__FILE__).'/checkresult.log', print_r($results,true),FILE_APPEND);
		/*block to show the list of records*/
		$html .= '<div id="mmi_saved_searches" style="display:none; width:730px;">';
				$html .= '<table>
					<thead>
						<th style="width: 70%;">Keyword(s)</th>
						<th style="width: 30%;">Actions</th>
					</thead>
					<tbody>';
					foreach ($results as $row) {
						$html .= '<tr id="data_'.$row->id.'">';
				            $html .= '<td>';
				            	$html .=  $row->keyword;
				            $html .= '</td>';
				            $html .= '<td>';
				            	$url  = $base_path.$row->url;
				            	$html .= '<a class="button btn-mmi-save" href="'.$url.'" id="mmi_show_data">view search</a>';
				            	$html .= '<a class="button btn-mmi-save delete_btn" href="" id="'.$row->id.'" style="margin-left:10px;">Delete</a>';					    					
				            	
				            $html .= '</td>';
				        $html .= '</tr>';
					}
				$html .= '</tbody>				        
			    </table>';
		$html .= '</div>';
		echo $html;
	}
			
	get_mmi_ajax();
?>