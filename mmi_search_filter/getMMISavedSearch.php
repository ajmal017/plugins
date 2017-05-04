<?php

require("/var/www/mindinmotion-online.com/wp-load.php");  	

class GetMMISavedSearch{

	public static  function getList(){
		global $wpdb;


		$html = '';

		$html .= "<style> #TB_window{width: 731px !important;}</style>";
		$user_id   = get_current_user_id();
		$base_path = 'http://mindinmotion-online.com';
		$sql       = "SELECT * FROM wp_mmi_save_search WHERE user_id = ".$user_id ." ORDER BY id DESC";
		$results   = $wpdb->get_results( $sql, OBJECT );

		/*block to show the list of records*/
		$html .= '<div id="mmi_saved_searches">';
				$html .= '<table>';
				if( !empty($results)){

				$html .= '<thead>
						
						<th style="width: 30%;">Title</th>
						<th style="width: 40%;">Keyword(s)</th>
						<th style="width: 30%;">Actions</th>
					</thead>
					<tbody>';
					
						foreach ($results as $row) {
							$html .= '<tr id="data_'.$row->id.'">';
					             $html .= '<td>';
					            	$html .=  $row->search_name;
					            $html .= '</td>';
					            
					            $html .= '<td>';
					            	$html .=  $row->keyword;
					            $html .= '</td>';


					            $html .= '<td>';
					            	$url  = $base_path.$row->url;
					            	$html .= '<a class="button btn-mmi-save" href="'.$url.'" id="mmi_show_data">Go to search</a>';
					            	$html .= '<a class="button btn-mmi-save delete_btn" href="" id="'.$row->id.'" style="margin-left:10px;">Delete</a>';					    					
					            	
					            $html .= '</td>';
					        $html .= '</tr>';
						}						
						$html .= '</tbody>';

					}else{
						$html .= '<tr>';
							$html .= '<td colspan="2">';
								$html .= '<h2 style="text-align: center;">No saved search results.</h2>';
							$html .= '</td>';
						$html .= '</tr>';
					}	
				
			    $html .= '</table>';
		$html .= '</div>';

		echo $html;

	}
}/*class ends here. */

GetMMISavedSearch::getList();
?>