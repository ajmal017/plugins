<?php
	
	//** Incldue the wp-config.php of the Source wordpress blog form where pages(s) will be copied **//	
	//** This file is included so a to gettge database crediantials. **//
	$serverPath = $_SERVER['DOCUMENT_ROOT']."/".$_POST['pageSource']."/wp-config.php";
	require( $serverPath );

	//** File is used get all the Menu data form the soruce url.  **// 
	$servername 	=	DB_HOST; 			//** As Defined in Wp_config. **//
	$username 		= 	DB_USER;			//** As Defined in Wp_config. **//
	$password 		= 	DB_PASSWORD; 		//** As Defined in Wp_config. **//
	$mysql_database =  	str_replace('-', '_', $_POST['pageSource'] );		//** As Received form Request. **//

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
		$pageDestinations   = $_POST['pageDestination'];
		$pageTitles  		= $_POST['pageTitle'];


		//** Get the Source Blog Language **//
		$sourceBlogLang = getBlogLanguage( $connection );


		//** Array to store all the data of the selected menus. **//
		$formArray		=	array();


		
		//** traverse through all the selected menus.  **//
		foreach ($pageTitles as $pageTitle ) {

				//** Select all the menu item defined under the current menu item. **//
				$Sqlpage = "SELECT * FROM wp_posts WHERE wp_posts.ID = $pageTitle";	

				$result  =	$connection->query( $Sqlpage );
				

				//**traverse all the menu items.  **//
				while( $row	=	mysqli_fetch_assoc( $result ) ){

					$post_id 		=		$row['ID'];
				 	$sqlPostmeta  	= "SELECT * FROM `wp_postmeta` WHERE `post_id` = $post_id";
					$resultPostmeta =	$connection->query( $sqlPostmeta );

					$tempPostMeta	=	array(); 
					while( $row1	=	mysqli_fetch_assoc($resultPostmeta) ){

						$tempP['meta_key']  	= (string) $row1['meta_key'];
						$tempP['meta_value']  	= (string) $row1['meta_value'];
						$tempPostMeta[] 		=	$tempP;
					}

					$tempData['Post_content'] 	  	= 	$row;
					$tempData['Post_meta'] 	  		= 	$tempPostMeta;

					$formArray[$row['ID']] 	=	$tempData;

				}; //while ends here.


		}


		//** Copy Opration Flags. **//
		$pageCopyStatus 	=	false;
		
		$pageSuccessInCopy 	= 0;
		$pageErrorInCopy 	= 0;
		$pageErrorAlready 	= 0;
		
			
		//** Traverse all the Selected Destination  blogs. **//
		foreach ($pageDestinations as $pageDestination ) {	
			
			$mysql_database_destination 	=  	str_replace('-', '_', $pageDestination );		//** As Received form Request. **//

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
					$pageErrorLanguage	=	$pageErrorLanguage + 1;	
					continue;
				}

				//** check if source and destination theme is same. **//
				if( $destinationTempate !== $sourceTempate){
					$pageErrorTemplate	=	$pageErrorTemplate + 1;	
					continue;
				}

				//**  Traversre All Selected page Title. **//	
				foreach ($pageTitles as $pageTitle ) {

					//** Reterive the data form Source Data Array Form the Currnet Menu ITem. **//
					$PageData = $formArray[$pageTitle];

					$blogPageTitle = $PageData['Post_content']['post_title'];
					//code to check if page already Exist.
					$pageID = checkPageExist($connectionDestination, $blogPageTitle );
					
					if( $pageID ){
						
						$resultUpdate = updatePage($connectionDestination, $pageID, $PageData );
						
						if($resultUpdate){
							$pageCopyStatus = true;								
						}

					}else{
						
						//** insert Post**//
						$postID 	=	insertPosts( $connectionDestination, $PageData );
						if( $postID ){
							$pageCopyStatus = true;								
						}	
					}
				}
				$pageSuccessInCopy++;
				
				continue;
				
				

			}//** Databse else Closing. **//
		} //** Destination Loop Ends here. **//
			

		$connection->close();
	}
	

	

	//** function to update post table **//
	function insertPosts( $connectionDestination,  $pageData ){

	    $post_author			=	 $pageData['Post_content']['post_author'];
	    $post_date				=	 $pageData['Post_content']['post_date'];
	    $post_date_gmt			=	 $pageData['Post_content']['post_date_gmt'];
	    $post_content			=	 (string) $pageData['Post_content']['post_content'];
	    $post_title				=	 (string) $pageData['Post_content']['post_title'];
	    $post_excerpt			=	 (string) $pageData['Post_content']['post_excerpt'];
	    $post_status			=	 $pageData['Post_content']['post_status'];
	    $comment_status			=	 $pageData['Post_content']['comment_status'];
	    $ping_status			=	 $pageData['Post_content']['ping_status'];
	    $post_password			=	 $pageData['Post_content']['post_password'];
	    $post_name				=	 $pageData['Post_content']['post_name'];
	    $to_ping				=	 $pageData['Post_content']['to_ping'];
	    $pinged					=	 $pageData['Post_content']['pinged'];
	    $post_modified			=	 $pageData['Post_content']['post_modified'];
	    $post_modified_gmt		=	 $pageData['Post_content']['post_modified_gmt'];
	    $post_content_filtered	=	 $pageData['Post_content']['post_content_filtered'];
	    $post_parent			=	 $pageData['Post_content']['post_parent'];
	    $guid					=	 getSiteURL( $connectionDestination );
	    $menu_order				=	 $pageData['Post_content']['menu_order'];
	    $post_type				=	 $pageData['Post_content']['post_type'];
	    $post_mime_type			=	 $pageData['Post_content']['post_mime_type'];
	    $comment_count			=	 $pageData['Post_content']['comment_count'];
	  
	   
	    $sqlPost = "INSERT INTO `wp_posts`( `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) 
			VALUES ('$post_author','$post_date','$post_date_gmt','$post_content','$post_title','$post_excerpt','$post_status','$comment_status','$ping_status','$post_password','$post_name','$to_ping','$pinged','$post_modified','$post_modified_gmt','$post_content_filtered','$post_parent','$guid','$menu_order','$post_type','$post_mime_type','$comment_count')";
		
		$resultCustomInsert =	$connectionDestination->query( $sqlPost );

		
		if( $resultCustomInsert ){
			
			
			  
			//** update Guid of ther post and post name.**//
			$postID 	=	mysqli_insert_id( $connectionDestination );

		

			$siteURL 	= 	getSiteURL( $connectionDestination );
			$postName	=	$postID;
			$guid 		=	$siteURL."?p=$postID";

			$sqlUpdatePost 		= "UPDATE  `wp_posts` SET  `post_name` = '$postName',  `guid` = '$guid' WHERE ID = $postID";			
			$resultUpdatePost 	= $connectionDestination->query($sqlUpdatePost);
			

			$postMetas 	=	$pageData['Post_meta'];
			//** Update Post Meta for the post. **//
			updatePostMeta( $connectionDestination, $postID, $postMetas );
			
			
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
	function updatePostMeta(  $connectionDestination, $postID, $postMetas ){
		 
		 $status = false;
		
		foreach ($postMetas as $postMeta ) {

			$post_id  	=	$postID;
			$meta_key 	=  	$postMeta['meta_key'];
			$meta_value	=	$postMeta['meta_value'];
			
			$sql  		= "INSERT INTO wp_postmeta (`post_id`,`meta_key`,`meta_value`) VALUES ( '$postID', '$meta_key ', '$meta_value')";
			$result 	=	$connectionDestination->query( $sql );		

			if( $result ){
				 $status =  true;
			}
		}

		return $status;
		
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

	//** function to check if page already exists **//
	function checkPageExist($connection, $PageTitle ){
		
		$PageTitle 	= 	trim($PageTitle);

		$sql = "SELECT ID FROM `wp_posts` WHERE `post_title` = '$PageTitle' AND `post_type` = 'page'";
		$resultPage 	=	$connection->query( $sql );

		if( $resultPage->num_rows > 0 ){
			$row = mysqli_fetch_assoc( $resultPage );
			return $row['ID'];
		}else{
			return false;
		}	
	}


	//** function to update page content. **//
	function updatePage($connection, $pageID, $pageData ){

	    $post_date				=	 $pageData['Post_content']['post_date'];
	    $post_date_gmt			=	 $pageData['Post_content']['post_date_gmt'];
	    $post_content			=	 (string) $pageData['Post_content']['post_content'];
	    $post_title				=	 (string) $pageData['Post_content']['post_title'];
	    $post_excerpt			=	 (string) $pageData['Post_content']['post_excerpt'];
	    $post_status			=	 $pageData['Post_content']['post_status'];
	    $comment_status			=	 $pageData['Post_content']['comment_status'];
	    $ping_status			=	 $pageData['Post_content']['ping_status'];
	    $post_password			=	 $pageData['Post_content']['post_password'];
	    $post_name				=	 $pageData['Post_content']['post_name'];
	    $to_ping				=	 $pageData['Post_content']['to_ping'];
	    $pinged					=	 $pageData['Post_content']['pinged'];
	    $post_modified			=	 $pageData['Post_content']['post_modified'];
	    $post_modified_gmt		=	 $pageData['Post_content']['post_modified_gmt'];
	    $post_content_filtered	=	 $pageData['Post_content']['post_content_filtered'];
	    $post_parent			=	 $pageData['Post_content']['post_parent'];	  
	    $menu_order				=	 $pageData['Post_content']['menu_order'];
	    $post_type				=	 $pageData['Post_content']['post_type'];
	    $post_mime_type			=	 $pageData['Post_content']['post_mime_type'];
	    $comment_count			=	 $pageData['Post_content']['comment_count'];

	    $sql = "UPDATE `wp_posts` SET  `post_date` 					= 	'$post_date',
	    								 `post_date_gmt`			=	'$post_date_gmt',
	    								 `post_content`				=	'$post_content',
	    								 `post_title`				=	'$post_title',		
	    								 `post_excerpt`				=	'$post_excerpt',
	    								 `post_status`				=	'$post_status',
	    								 `comment_status`			=	'$comment_status',
	    								 `ping_status`				=	'$ping_status',
	    								 `post_password`			=	'$post_password',
	    								 `post_name`				=	'$post_name',
	    								 `to_ping`					=	'$to_ping',
	    								 `pinged`					=	'$pinged',
	    								 `post_modified`			=	'$post_modified',
	    								 `post_modified_gmt`		=	'$post_modified_gmt',
	    								 `post_content_filtered`	= 	'$post_content_filtered',	    										 	    								 
	    								 `post_type` 				=	'$post_type',
	    								 `post_mime_type`			=	'$post_mime_type'

	    								  WHERE `ID` = $pageID";

		
		$resultCustomUpdate =	$connection->query(  $sql );

		if( $resultCustomUpdate ){
			return true;
		}else{			
			return false;
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



?> 