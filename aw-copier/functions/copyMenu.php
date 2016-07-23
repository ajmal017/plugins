<?php
	


	//** Incldue the wp-config.php of the Source wordpress blog form where menu(s) will be copied **//	
	//** This file is included so a to gettge database crediantials. **//
	$serverPath = $_SERVER['DOCUMENT_ROOT']."/".$_POST['menuSource']."/wp-config.php";
	require( $serverPath );

	//** File is used get all the Menu data form the soruce url.  **// 
	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$mysql_database =  	str_replace('-', '_', $_POST['menuSource'] );		//** As Received form Request. **//

	//** Make a database connection. **//
	$connection  	= 	new mysqli($servername, $username, $password, $mysql_database);

	if ($connection->connect_error) {
		//** show Error message if database in connections fails. **//
		echo "Connection Error!";
	}else{
		
		//** get the source blog template.**//
		$sourceTempate 	= 	getBlogTemplate( $connection );

		//** get the source blog language **//
		$sourceLanguage =	getBlogLanguage( $connection );

		//** Code To Copy Widget to Destination Widgets Start. **//
		$menuDestinations   = $_POST['menuDestination'];
		$menuTitles  		= $_POST['menuTitle'];

		//** Array to store all the data of the selected menus. **//
		$formArray		=	array();


		
		//** traverse through all the selected menus.  **//
		foreach ($menuTitles as $menuTitle) {

			//** Select all the menu item defined under the current menu item. **//
			$SqlTermsTaxonomy = "SELECT wp_terms.*,wp_term_taxonomy.term_taxonomy_id,wp_term_taxonomy.description,wp_term_taxonomy.parent AS parent_id,wp_term_taxonomy.count AS total FROM wp_terms,wp_term_taxonomy WHERE wp_terms.term_id = wp_term_taxonomy.term_id AND wp_terms.name='".$menuTitle."' AND wp_term_taxonomy.taxonomy = 'nav_menu'";	

				$result 		=	$connection->query( $SqlTermsTaxonomy );
				
				//**traverse all the menu items.  **//
				while( $row	=	mysqli_fetch_assoc($result) ){

				//** get the term name of the current item **//	
				$TermName	 = $row['name'];
				
				//** save the current Item in $formArray with Key as term Name; Term name is Menu title. **//
				$formArray[$TermName] = $row;
				
				//** get the id of the current menu.  **//	
				$id = 	 $row['term_id'];
				
				//** Fetch all the post under the current menu item. menu Item are stored in the Post table as posts with Post type as "nav_menu_item". **//
				$sqlTermRelationships	 =	"SELECT wp_posts.* FROM  wp_posts,wp_term_relationships WHERE  wp_posts.ID = wp_term_relationships.object_id AND wp_term_relationships.term_taxonomy_id = $id  AND wp_posts.post_type='nav_menu_item' ORDER BY wp_posts.ID ASC";

					$resultTR =	$connection->query( $sqlTermRelationships );
					
					//** travere all the menu items obtained form the select operation. **//
					while( $row	=	mysqli_fetch_assoc( $resultTR ) ){
						
						//** save the post in form array as $formArray[{menu_title}]['TermRelationships'] **//
						$formArray[$TermName]['TermRelationships'][] = $row;	
						
						//** Get the ID of Current Menu item; **//
						$id = $row['ID'];

						//** Fetch PostMeta related to the post. **//
						$SqlPostMeta =	"SELECT * FROM `wp_postmeta` WHERE post_id = $id";
						$resultPM =	$connection->query( $SqlPostMeta );
						
						$idPM	= '';
						
						$typePM = '';

						//** traverse all the Post Meta Item of the Curren menu item. **//
						while ($row =  mysqli_fetch_assoc( $resultPM ) ) {
							
							//** Store the post meta related to the post in $formArray[{menu_title}]['postMeta'][{POST_ID}][] **//
							$formArray[$TermName]['PostMeta'][$id][]  = $row;	

							//** store the type of the post if if type if Category. **//
							if( $row['meta_key'] == '_menu_item_object' &&  $row['meta_value'] == 'category'  ){
								$typePM =  $row['meta_value'];
							}

							//** Stores the id of the Object.(Object is POST.) **//
							if( $row['meta_key'] == '_menu_item_object_id' ){	
								$idPM =  $row['meta_value'];
							}


							//** Check if the Menu is refering to the Page. **//							
							if( $row['meta_key'] == '_menu_item_object' &&  $row['meta_value'] == 'page'  ){
								$typePM =  $row['meta_value'];
								
							}

						}


						//** Check if the Menu is refering to the Category. If this is refering to the category. get the Information about the Category**//
						if(  $typePM == 'category' && $idPM !== '' ){
							
							//** Get all information of the term related to the Current Post. Term is Category. **//
							$SqlCategories 	=	"SELECT * FROM `wp_terms` WHERE term_id = $idPM";
							$resultCT		=	$connection->query( $SqlCategories );

							//** Stores the term information in the $fromArray[{menu_title}]['TermData']['POST_META_ID']**//
							while ($row =  mysqli_fetch_assoc( $resultCT ) ) {
								$formArray[$TermName]['TermData'][$idPM][] = $row;	
							}
						}

						//** Check if current item is Refering to the Page, Menu item is linked to the Page. **//
						if(  $typePM == 'page' && $idPM !== '' ){
							
							//** get the page Data form post Table. **//
							$SqlCategories 	=	"SELECT * FROM `wp_posts` WHERE ID = $idPM";
							$resultPage		=	$connection->query( $SqlCategories );
							
							//** Stores the related page information in the $formArray[{menu_title}]['PageData']['POST_META_ID'] **//	
							while ($row =  mysqli_fetch_assoc( $resultPage ) ) {
								$formArray[$TermName]['PageData'][$idPM][] = $row;									
							}
						}
					}
				}; //while ends here.
		}

		

		//** Stores The slug and id of the created Terms( Catergories. Data stored as arrayName['name'] = {term_id} **//
		$termCategoryNew 	= array();

		//** Store the category id(Term Id), syntax:  $termCategoryID[{Old_term_id}] = {new_term_id} **//
		$termCategoryID 	= array();

		//** Store the menu Details as $termMenuNew['Menu_title'] = array( {'term_id'} => {new_term_id}, {TaxonomyID} => {term_taxonomy_id} )**//			
		$termMenuNew 		= array();

		//** Store Post IDs: Syntax: $postNew[{Old_POST_ID}] = {New_Post_ID} **//
		$postOldTONewID		= array();


		//** Copy Opration Flags. **//
		$menuCopyStatus 	=	false;
		
		$menuSuccessInCopy 	= 0;
		$menuErrorInCopy 	= 0;
		$menuErrorAlready 	= 0;
		
			
		//** Traverse all the Selected Destination  blogs. **//
		foreach ($menuDestinations as $menuDestination ) {	
			
			$mysql_database_destination 	=  	str_replace('-', '_', $menuDestination );		//** As Received form Request. **//

			//** make databese connection to the destination Blog.  **//
			$connectionDestination  	= 	new mysqli($servername, $username, $password, $mysql_database_destination );

			if ($connectionDestination->connect_error) {
				echo "Connection Error!";
			}else{
				
				//** get the source blog template.**//
				$destinationTempate 	= 	getBlogTemplate( $connectionDestination );

				//** get the source blog language **//
				$destinationLanguage =	getBlogLanguage( $connectionDestination );



				//** check if source and destination language match. **//
				if( $destinationLanguage !== $sourceLanguage){
					$menuErrorLanguage	=	$menuErrorLanguage + 1;	
					continue;
				}

				//** check if source and destination theme is same. **//
				if( $destinationTempate !== $sourceTempate){
					$menuErrorTemplate	=	$menuErrorTemplate + 1;	
					continue;
				}


				//**  Traversre All Selected Menu Title. **//	
				foreach ($menuTitles as $menuTitle ) {

					//** Reterive the data form Source Data Array Form the Currnet Menu ITem. **//
					$menuItems = $formArray[$menuTitle]['TermRelationships'];

					//** Traverse all the menu Item Under the Current Menu. **//
					foreach ($menuItems as $menuItem ) {
						
						//** Get the ID Of Current Menu Item, {POST_ID} **//	
						$menuItemID = $menuItem['ID'];

						//** Get All the Post Meta Information of the current Post. **//
						$postMetas 	= $formArray[$menuTitle]['PostMeta'][$menuItemID];
						
						//** Check if menu item is refering to a Category.  **//
						if(  $formArray[$menuTitle]['PostMeta'][$menuItemID]['3']['meta_value'] == 'category'){ 
							//** Function to add the Categories, if categories not exist. **//
							insertTermCategories( $menuTitle, $menuItemID, $formArray, $connectionDestination );
						}

						
						//** function call to insert menu and associated term_taxonomy.**//
						insertMenu( $menuTitle, $formArray, $connectionDestination );

						//** Function Call to insert Posts. **//
						$pageData =  $formArray[$menuTitle]['PageData'];

						$postResult 	=	insertPosts( $connectionDestination, $menuItem, $postMetas, $menuTitle, $pageData );
						if($postResult){
							$menuCopyStatus	=	true;
						}else{
							$menuErrorInCopy++;	
						}
						
					}
				}

			}//** Databse else Closing. **//
		} //** Destination Loop Ends here. **//
			

		$connection->close();

		$menuSuccessInCopy++;
	}
	





	//** Funtion to insert terms(catergories.) 								**//
	//** @param: $menuTitle 			= 	Menu Title. 					**//
	//** @param: $menuItemID 			= 	menus term id.	 				**//
	//** @param: $formArray  			= 	Array  of Selected menu data 	**//
	//** Return: ID of inserted Term.	=	New Page Id. 					**//	
	function insertTermCategories( $menuTitle, $menuItemID, $formArray, $connectionDestination ){
		global $termCategoryNew;
		global $termCategoryID;

		$termID = $formArray[$menuTitle]['PostMeta'][$menuItemID]['2']['meta_value'];
		

		//** Code to check categories, if not present then insert category to the wp_terms of the destination table **//
		$category 				=	$formArray[$menuTitle]['TermData'][$termID][0];
		$categorySlug			= 	$category['slug'];
		$categoryName			= 	strtolower( $category['slug'] );

		$sqlCategory 			= 	"SELECT * FROM wp_terms WHERE slug ='$categorySlug'";		
		$resultCategory			=	$connectionDestination->query($sqlCategory);



		if( $resultCategory->num_rows > 0 ){
			$row  = 	mysqli_fetch_assoc($resultCategory);
			 
			 //** Save Category name as key and ID as value.  **//
			 $termCategoryNew[$categoryName]	=	$row['term_id'];

			 //** Assign Old Term Id AS array Key, and New Id as Value. **//
			 $termCategoryID[$termID] 		=	$row['term_id'];
			 
		}else{
			
			$categoryName 		=	$category['name'];
			$categorySlug 		=	$category['slug'];
			$categoryTermGroup 	=	$category['term_group'];


			$sqlTerm 			= 	"INSERT INTO `wp_terms` (`name`,`slug`,`term_group`) VALUES ('$categoryName','$categorySlug','$categoryTermGroup')";

			$resultTerm 		=	$connectionDestination->query( $sqlTerm );	
			
			if( $resultTerm ){
				
				$new_Term_ID 				= 	mysqli_insert_id( $connectionDestination );
				
				$categoryName	=	strtolower($categoryName );

				$termCategoryNew[$categoryName]	=	$new_Term_ID;
				//** Assign Old Term Id AS array Key, and New Id as Value. **//
			 	$termCategoryID[$termID] 		=	$new_Term_ID;
				
				$SqlTermsTaxonomy		= 	"INSERT INTO `wp_term_taxonomy`(`term_id`, `taxonomy`, `parent`, `count` ) VALUES( '$new_Term_ID', 'category', '0','1')";
				$resultTermsTaxonomy 	=	$connectionDestination->query($SqlTermsTaxonomy);	

				if( $resultTermsTaxonomy ){
					$new_cat_ID 						= 	mysqli_insert_id( $connectionDestination );
					$termCategoryNew[$categoryName]		=	$new_cat_ID;
				}
			}	

		}
	}

	//** Function to add menu titles in wp_terms. **//

	function insertMenu( $menuTitle, $formArray, $connectionDestination ){
		global $termMenuNew;

		
		$menuTermName 	= 	$formArray[$menuTitle][name];
    	$menuTermSlug	=	$formArray[$menuTitle][slug];
    	$menuTermGroup	=	$formArray[$menuTitle][term_group];
  		

  		$menuTermTaxonomyID 			= ""; //** Will Be the Last insert Id **//
 		$menuTermTaxonomyDescription 	= $formArray[$menuTitle][term_group];
 		$menuTermTaxonomyParentID 		= $formArray[$menuTitle][parent_id];
 		$menuTermTaxonomyTotal 			= $formArray[$menuTitle][total];

 		
 		$sqlMenu			= 	"SELECT * FROM wp_terms, wp_term_taxonomy WHERE name ='$menuTitle' AND wp_terms.term_id =  wp_term_taxonomy.term_id ";

		$resultMenu			=	$connectionDestination->query( $sqlMenu );

		if( $resultMenu->num_rows > 0 ){
			$row  = 	mysqli_fetch_assoc( $resultMenu );

			$menuName 									=	strtolower($menuTermName);
			$termMenuNew[$menuName]['term_id']		=	$row['term_id'];
			$termMenuNew[$menuName]['TaxonomyID'] 	=	$row['term_taxonomy_id'];
			
		}else{
			
			$sqlTerm 			= 	"INSERT INTO `wp_terms` (`name`,`slug`,`term_group`) VALUES ('$menuTermName','$menuTermSlug','$menuTermGroup')";
			$resultTerm 		=	$connectionDestination->query( $sqlTerm );	
			
			if( $resultTerm ){
				$menuName 									=	strtolower($menuTermName);

				$menuTermTaxonomyID 						= 	mysqli_insert_id( $connectionDestination );
				$termMenuNew[$menuName]['term_id']		=	$menuTaxonomyID;
			
				
				$SqlTermsTaxonomy		= 	"INSERT INTO `wp_term_taxonomy`(`term_id`, `taxonomy`, `parent`, `count` ) VALUES( '$menuTermTaxonomyID', 'nav_menu', '$menuTermTaxonomyParentID','$menuTermTaxonomyTotal')";
				$resultTermsTaxonomy 	=	$connectionDestination->query($SqlTermsTaxonomy);	

				if( $resultTermsTaxonomy ){
					$menuTaxonomyID 								= 	mysqli_insert_id( $connectionDestination );
					$termMenuNew[$menuName]['TaxonomyID']		=	$menuTaxonomyID;
					
				}
			}
			

		}
	}

	
	

	//** function to update post table **//
	function insertPosts( $connectionDestination, $menuItem, $postMetas, $menuTitle, $pageData ){
		
		global $termMenuNew;
		global $termCategoryNew;
		global $termCategoryID;		
		global $postOldTONewID;

	    $post_author			=	 $menuItem['post_author'];
	    $post_date				=	 $menuItem['post_date'];
	    $post_date_gmt			=	 $menuItem['post_date_gmt'];
	    $post_content			=	 $menuItem['post_content'];
	    $post_title				=	 $menuItem['post_title'];
	    $post_excerpt			=	 $menuItem['post_excerpt'];
	    $post_status			=	 $menuItem['post_status'];
	    $comment_status			=	 $menuItem['comment_status'];
	    $ping_status			=	 $menuItem['ping_status'];
	    $post_password			=	 $menuItem['post_password'];
	    $post_name				=	 $menuItem['post_name'];
	    $to_ping				=	 $menuItem['to_ping'];
	    $pinged					=	 $menuItem['pinged'];
	    $post_modified			=	 $menuItem['post_modified'];
	    $post_modified_gmt		=	 $menuItem['post_modified_gmt'];
	    $post_content_filtered	=	 $menuItem['post_content_filtered'];
	    $post_parent			=	 $menuItem['post_parent'];
	    $guid					=	 getSiteURL( $connectionDestination );
	    $menu_order				=	 $menuItem['menu_order'];
	    $post_type				=	 $menuItem['post_type'];
	    $post_mime_type			=	 $menuItem['post_mime_type'];
	    $comment_count			=	 $menuItem['comment_count'];
	  
	   
	   $sqlPost = "INSERT INTO `wp_posts`( `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) 
			VALUES ('$post_author','$post_date','$post_date_gmt','$post_content','$post_title','$post_excerpt','$post_status','$comment_status','$ping_status','$post_password','$post_name','$to_ping','$pinged','$post_modified','$post_modified_gmt','$post_content_filtered','$post_parent','$guid','$menu_order','$post_type','$post_mime_type','$comment_count')";
		
		$resultCustomInsert =	$connectionDestination->query( $sqlPost );

		
		if( $resultCustomInsert ){
			
			$oldID 		=	$menuItem['ID'];
			  
			//** update Guid of ther post and post name.**//
			$postID 	=	mysqli_insert_id( $connectionDestination );

			//** Update global array for Post Entry  **//
			$postOldTONewID[$oldID] 	=  	$postID;

			$siteURL 	= 	getSiteURL( $connectionDestination );
			$postName	=	$postID;
			$guid 		=	$siteURL."?p=$postID";

			$sqlUpdatePost 		= "UPDATE  `wp_posts` SET  `post_name` = '$postName',  `guid` = '$guid' WHERE ID = $postID";			
			$resultUpdatePost 	= $connectionDestination->query($sqlUpdatePost);
			
			//** Update Post Meta for the post. **//
			updatePostMeta( $postMetas, $connectionDestination, $postID, $pageData );
			
			//** Update RelationShip Table. **//
			updateTermRelationship( $menuTitle, $postID , $connectionDestination );

			return true;
		}else{
			// echo "Some Error Occured Inserting Post";
			return false;
		}
		
	}
	

	//** Function to get the Site URL **//
	function getSiteURL( $con ){

		$sql  	= 	'SELECT option_value FROM wp_options WHERE option_name="siteurl"';
		$result =	$con->query($sql);

		if( $result->num_rows > 0 ){
			$row = mysqli_fetch_assoc( $result);
			return $row['option_value'];
		}else{
			return null;
		}

	}

	//** Function to update the post meta. **//
	function updatePostMeta( $postMetas, $connectionDestination, $postID, $pageData ){
		 
		
		global $termCategoryID;
		global $postOldTONewID;


		$parentID 		= 	$postMetas['1']['meta_value'];
		$parentIDNew 	= 	0;

		if( $parentID !== 0 ){
			$parentIDNew = $postOldTONewID[$parentID];
		}


		if( $postMetas['3']['meta_value']  == 'category'){
			
			$CatID 		= 	$postMetas['2']['meta_value'];
			$newCatID 	=	$termCategoryID[$CatID];

			$sql  = "INSERT INTO wp_postmeta (`post_id`,`meta_key`,`meta_value`) VALUES ( '$postID', '_menu_item_type', 'taxonomy'),
																						( '$postID', '_menu_item_menu_item_parent', '$parentIDNew'), 
																						( '$postID', '_menu_item_object_id', '$newCatID'), 
																						( '$postID', '_menu_item_object', 'category'), 
																						( '$postID', '_menu_item_target', ''), 																						
																						( '$postID', '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'), 
																						( '$postID', '_menu_item_xfn', ''), 
																						( '$postID', '_menu_item_url', '') ";
			
		}

		if( $postMetas['3']['meta_value']  == 'custom'){
			
			$url =  $postMetas['7']['meta_value'];

			$sql  = "INSERT INTO wp_postmeta (`post_id`,`meta_key`,`meta_value`) VALUES ( '$postID', '_menu_item_type', 'custom'),
																						( '$postID', '_menu_item_menu_item_parent', '$parentIDNew'), 
																						( '$postID', '_menu_item_object_id', '$postID'), 
																						( '$postID', '_menu_item_object', 'custom'), 
																						( '$postID', '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'), 
																						( '$postID', '_menu_item_xfn', ''), 
																						( '$postID', '_menu_item_url', '$url') ";
			
		}

		if( $postMetas['3']['meta_value']  == 'page'){

			$pageID 		=	$postMetas['2']['meta_value'];
			$pageDataNew 	= 	$pageData[$pageID][0];

			$PageID =   AddMenuItemPage( $pageDataNew, $connectionDestination );

			$sql  = "INSERT INTO wp_postmeta (`post_id`,`meta_key`,`meta_value`) VALUES ( '$postID', '_menu_item_type', 'post_type'),
																						( '$postID', '_menu_item_menu_item_parent', '$parentIDNew'), 
																						( '$postID', '_menu_item_object_id', '$PageID'), 
																						( '$postID', '_menu_item_object', 'page'), 
																						( '$postID', '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'), 
																						( '$postID', '_menu_item_xfn', ''), 
																						( '$postID', '_menu_item_url', '') ";
			
		}
		

		$result 	=	$connectionDestination->query( $sql );		

		if( $result ){
			return true;
		}else{
			return false;			
		}
		
	}	

	//** Function update Term_relationship.  **//
	function updateTermRelationship( $menuTitle, $postID, $connectionDestination ){
		global $termMenuNew;

		$menuItem 	= 	strtolower($menuTitle);

		$termID 	=	$termMenuNew[$menuItem]['TaxonomyID'];

		$sqlTR 		= 	"INSERT INTO `wp_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES ( '$postID', '$termID', '0')";
		
		$resultTR = $connectionDestination->query($sqlTR);

		if( $resultTR ){
			return true;
		}else{
			// echo mysqli_errno($connectionDestination); 
			return false;
		}

	}

	//** ================= 	Add Menu Item Page. ============================**//
	//** Function will Return the Id of the newly Created Page.  			**//
	//** @param: $pageData: Array containing all the details of the page. 	**//
	//** @param: $connectionDestionation: $database Connection Object. 		**//
	function AddMenuItemPage( $pageData, $connectionDestination ){
		 
		
		$post_author			=	 $pageData['post_author'];
	    $post_date				=	 $pageData['post_date'];
	    $post_date_gmt			=	 $pageData['post_date_gmt'];
	    $post_content			=	 $pageData['post_content'];
	    $post_title				=	 $pageData['post_title'];
	    $post_excerpt			=	 $pageData['post_excerpt'];
	    $post_status			=	 $pageData['post_status'];
	    $comment_status			=	 $pageData['comment_status'];
	    $ping_status			=	 $pageData['ping_status'];
	    $post_password			=	 $pageData['post_password'];
	    $post_name				=	 $pageData['post_name'];
	    $to_ping				=	 $pageData['to_ping'];
	    $pinged					=	 $pageData['pinged'];
	    $post_modified			=	 $pageData['post_modified'];
	    $post_modified_gmt		=	 $pageData['post_modified_gmt'];
	    $post_content_filtered	=	 $pageData['post_content_filtered'];
	    $post_parent			=	 $pageData['post_parent'];
	    $guid					=	 getSiteURL( $connectionDestination );
	    $menu_order				=	 $pageData['menu_order'];
	    $post_type				=	 $pageData['post_type'];
	    $post_mime_type			=	 $pageData['post_mime_type'];
	    $comment_count			=	 $pageData['comment_count'];
	  
	    
	    $sqlPost = "INSERT INTO `wp_posts`( `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) 
			VALUES ('$post_author','$post_date','$post_date_gmt','$post_content','$post_title','$post_excerpt','$post_status','$comment_status','$ping_status','$post_password','$post_name','$to_ping','$pinged','$post_modified','$post_modified_gmt','$post_content_filtered','$post_parent','$guid','$menu_order','$post_type','$post_mime_type','$comment_count')";
		
		$resultCustomPageInsert =	$connectionDestination->query( $sqlPost );

		if( $resultCustomPageInsert ){
			
			$postID 	=	mysqli_insert_id( $connectionDestination );

			$siteURL 	= 	getSiteURL( $connectionDestination );
			$postName	=	$postID;
			$guid 		=	$siteURL."?p=$postID";

			$sqlUpdatePost 		= "UPDATE  `wp_posts` SET `guid` = '$guid' WHERE ID = $postID";			
			$resultUpdatePost 	= $connectionDestination->query($sqlUpdatePost);

			return $postID;
			
		}else{

			return 0;
		}

	}

	
//**Function to get theme template. **//	
function getBlogTemplate( $connection ){

	$sql 		= "SELECT * FROM `wp_options` WHERE `option_name` = 'template';";

	$resulTemplate	=	$connection->query( $sql );

	if( $resulTemplate->num_rows > 0 ){
		$row = mysqli_fetch_assoc( $resulTemplate );
		
		if( $row['option_value'] == '') {
			return null;
		}else{
			return $row['option_value'];
		};

	}else{
		return null;
	}	

}


//**function to get the Blog Language**//
function getBlogLanguage( $connection ){

	$sql 		= "SELECT * FROM `wp_options` WHERE `option_name` = 'WPLANG';";

	$resultLang 	=	$connection->query( $sql );


	if( $resultLang->num_rows > 0 ){
		$row = mysqli_fetch_assoc( $resultLang );
		
		if( $row['option_value'] == '') {
			return "en_US";
		}else{
			return $row['option_value'];
		};

	}else{
		return "en_US";
	}	

}


?> 