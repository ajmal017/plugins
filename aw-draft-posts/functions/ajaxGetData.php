<?php
	
	$serverPath = $_SERVER['DOCUMENT_ROOT']."/".$_GET['source']."/wp-config.php";
	require( $serverPath );

	//** File is used get all the wigets data form the soruce url.  **// 
	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$mysql_database =  	str_replace('-', '_', $_GET['source'] );		//** As Received form Request. **//
	
	$connection  	= 	new mysqli($servername, $username, $password, $mysql_database);

	if ($connection->connect_error) {
		echo "Connection Error!";
	}else{
		
		// $sql = 'SELECT wp_posts.*, wp_term_relationships.term_taxonomy_id FROM wp_posts, wp_term_relationships WHERE wp_posts.ID = wp_term_relationships.object_id AND post_type = "post" AND post_status= "draft" AND post_title!="" ORDER BY post_date ASC';
		$sql 		=	'SELECT wp_posts.*,wp_term_relationships.term_taxonomy_id, wp_users.user_nicename FROM wp_posts,wp_term_relationships,wp_users WHERE wp_posts.ID=wp_term_relationships.object_id AND post_type="post" AND post_status="draft"AND wp_posts.post_author = wp_users.ID	AND post_title!="" ORDER BY post_date ASC';
		$result 	=	$connection->query($sql);

		$category   = ''; 

		if( $result->num_rows > 0 ){
			
			//**Drafts Option **//
			$drafts  = array();
			
			while( $row	= mysqli_fetch_assoc($result) ){

				$category   = ''; 

				$sqlT 		=	'SELECT wt.* FROM wp_posts p INNER JOIN wp_term_relationships r ON r.object_id=p.ID INNER JOIN wp_term_taxonomy t ON t.term_taxonomy_id = r.term_taxonomy_id INNER JOIN wp_terms wt on wt.term_id = t.term_id WHERE p.ID = '.$row['ID'].' AND t.taxonomy="category"';
				$resultT 	=	$connection->query($sqlT);
				
				if( $resultT->num_rows > 0 ){
					while( $rowT	= mysqli_fetch_assoc($resultT) ){						
						$category .= ($category == '' ? '	' : ', ');
						$category .= $rowT['name'];				
					}
				}else{
					$category = "Uncategorized";					
				}	
				
				$row['aw_categories'] = $category;
				$drafts[] = $row;
				
				
			}
			
			if(count($drafts) > 0 ){ 
				$table 	=	'<div id="widgetoptiontable">';								
				$table 	=	'<div class="aw_header_button_area"> 
								<input type="button" value="Publish All" id="aw_publish_all_button" class="button button-primary" /> 
								<input type="button" value="Delete All" id="aw_delete_all_button" class="button" />  
							</div>';

				$table .=  	'<table class="wp-list-table widefat fixed posts">';
				$table .= '<thead>
								<tr>						
									<th class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox" id="aw_select_all" name="aw_select_all"></th>
									<th style="" class="manage-column column-title sortable desc" id="title" scope="col">&nbsp;Title</th>
									<th style="" class="manage-column column-author" id="author" scope="col">Author</th>
									<th style="" class="manage-column column-categories" id="categories" scope="col">Categories</th>
									<th style="width: 15%; text-align: center;" class="manage-column column-date sortable asc" id="date" scope="col">Date</th>
									<th style="width: 15%; text-align: center;"  class="manage-column column-date sortable asc" id="action" scope="col">Actions</th>	
								</tr>
							</thead>';
				
				$table .=	'<tbody id="the-list">';
					for($i = 0; $i < count( $drafts ); $i++){

						
						$dateTime =	 date("jS M Y",strtotime($drafts[$i]['post_date'] ));
						
						$table .= 	'<tr class="post-aw-drafts" id="post-'.$drafts[$i]['ID'].'">';

						$table .= 	'<th class="check-column" scope="row">								
										<input type="checkbox" value="'.$drafts[$i]['ID'].'" name="post[]" class="aw_checkboxes" id="cb-select-'.$drafts[$i]['ID'].'">								
									</th>';
						
						$table .= 	'<td class="post-title page-title column-title"><strong><a href="javascript:void(0);">'.$drafts[$i]['post_title'].'<a></strong></td>';
						$table .= 	'<td class="author column-author"><a href="javascript:void(0);">'.$drafts[$i]['user_nicename'].'</a></td>';
						$table .= 	'<td class="categories column-categories"><a href="javascript:void(0);">'.$drafts[$i]['aw_categories'].'</a></td>';
						$table .= 	'<td class="date column-date" style="text-align: center;">'.$dateTime.'</td>';
						
						$table .=	'<td class="categories">							
										<div class="row">
											<span class="aw_publish_row" data-val="'.$drafts[$i]['ID'].'"><a href="javascript:void(0);" class="button button-primary">Publish</a></span>&nbsp;|&nbsp;
											<span class="aw_delete_row" data-val="'.$drafts[$i]['ID'].'"><a href="javascript:void(0);" style="color: red;" class="button">Delete</a></span>									
										</div>
									</td>';				

						$table .= 	'</tr>';
					}  
				$table .=	'</tbody>'; 
				
				$table .= '</table>';
				$table .= 	'</div>';

				echo $table;
			}else{

				$table 	=	'<div id="widgetoptiontable" style="color: red; font-size: 18px; text-align: center; ">';
					$table .=	'No Drafts Saved for the site.';		
				$table .= 	'</div>';
				
				echo $table;	
			}		


		}else{
			$table 	=	'<div id="widgetoptiontable" style="color: red; font-size: 18px; text-align: center; ">';
				$table .=	'No Drafts Saved for the site.';		
			$table .= 	'</div>';
			echo $table;	
		}
		
		$connection->close();
	}
	

	
?>