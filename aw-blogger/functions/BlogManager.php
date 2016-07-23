<?php
/**
 * BlogManager - helper class for creating and modifying blogs
 *
 * @package WordPress
 * @subpackage Scanmine
 * @since 0.3
*/

require dirname(__FILE__)."/../configReader/ConfigReader.php";

class BlogManager {
	
	private $messages = array();
	private $current_site;

	public function __construct() {			
	}
	
	public function _utf8_decode($string){
	  
	  $tmp 		= $string;
	  $count 	= 0;
	  
	  while (mb_detect_encoding($tmp)=="UTF-8"){
		$tmp 	= utf8_decode($tmp);
		$count++;
	  }
	 
	  for ($i = 0; $i < $count-1 ; $i++){
		$string = utf8_decode($string);
	  }
	  return $string;
	}
	
	public function getMessages() { return $this->messages; }
	public function copyblog($template='',$blog_id=''){
			
		$template_table_id 	=	 $rsTemplate[0]->blog_id;
		/*start wigdet update */ 
		$jsql = "SELECT option_name,option_value FROM wp_".$template_table_id ."_options WHERE option_name LIKE 'widget_%' OR option_name LIKE 'sidebars_%' OR option_name LIKE 'newsletter%' OR option_name LIKE '%subscribe2_options%'";
        			
		foreach ($option_names as $option_name) {
			$up_option_name = $option_name->option_name;
            $up_option_val  = $option_name->option_value;
            $opt_serial_1 	= $up_option_val;
            $opt_serial 	= $opt_serial_1;
			
			if($check_option_names[0]->option_name){
                $update_query 	= "UPDATE wp_" . $blog_id ."_options SET option_value = '".addslashes($opt_serial)."' WHERE option_name='".$option_name->option_name."'";
            	$flag 			= true;
            }else{	
				$insert_query 	= "INSERT INTO wp_" . $blog_id ."_options (option_name,option_value) VALUES ('".$option_name->option_name."','".addslashes($opt_serial)."')";
            	$flag 			= true; 			  				
            }
        }
		/* end widget update */	
		/*===Create Newsletter tables=====*/
		$create_table_newsletters = "CREATE TABLE  wp_".$blog_id."_newsletter LIKE wp_".$template_table_id."_newsletter";
		$create_table_newsletters_emails = "CREATE TABLE  wp_".$blog_id."_newsletter_emails LIKE wp_".$template_table_id."_newsletter_emails";
		$create_table_newsletter_stats = "CREATE TABLE  wp_".$blog_id."_newsletter_stats LIKE wp_".$template_table_id."_newsletter_stats";
		/*===Create Newsletter tables=======*/
	}
	public function update_rssfeed($blogid,$site_slug){
		//global $wpdb;
		$rsLinks 		= array();
		$servername 	= DB_HOST;
		$username 		= DB_USER;
		$password 		= DB_PASSWORD;
		$mysql_database = str_replace('-', '_', $site_slug);

		//** Code to process UTF-8 characters. **//
		 mysqli_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $conn);
		
		$conn 			= new mysqli($servername, $username, $password, $mysql_database);	
		$rsLinks_query 	= "SELECT * FROM wp_links WHERE link_visible ='Y' AND link_rss!='' ORDER BY link_updated DESC";
		
		$resultRss 			= $conn->query( $rsLinks_query );
		$res_linksresults 	= array();
		
		while( $row = mysqli_fetch_assoc( $resultRss  ) ){
			array_push($rsLinks, $row );
		}
		
		if(count($rsLinks)>0){
			
			for($z = 0; $z < count($rsLinks); $z++ ){

				$rssfeed = $rsLinks[$z]['link_rss'];

				if( !empty( $rssfeed ) ){

					$options_query1 = "SELECT option_value FROM wp_options WHERE option_name='siteurl'";
					$rsSiteURL 		= mysqli_fetch_assoc($conn->query($options_query1));
					$siteurl 		= $rsSiteURL['option_value'];	
				
					$doc = new DOMDocument();
				
					$doc->load( $rssfeed );
					$cattitle 	= $doc->getElementsByTagName('title')->item(0)->nodeValue;
					$pubdate 	= $doc->getElementsByTagName('pubDate')->item(0)->nodeValue;
					
					foreach ($doc->getElementsByTagName('item') as $node ){
					
						$description 	=	preg_replace('~>\s+<~m', '><', $node->getElementsByTagName('description')->item(0)->nodeValue);  
						$description 	= 	trim($description);
						$chart_count	=	0;
						
						foreach ($node->getElementsByTagName('chart') as $chart){				
				
							$id		 		=	$node->getElementsByTagName('chart')->item($chart_count)->getAttribute('id');
							$title	 		=	$node->getElementsByTagName('chart')->item($chart_count)->getAttribute('title');
							$type	 		=	$node->getElementsByTagName('chart')->item($chart_count)->getAttribute('type');
							$link_title	 	=	$chart->getElementsByTagName('linktitle')->item(0)->nodeValue;
							$link	 		=	$chart->getElementsByTagName('link')->item(0)->nodeValue;
					
							foreach($chart->getElementsByTagName('design') as $design){
								
								$design_type=	$design->getAttribute('type');								
								$width		=	$design->getElementsByTagName('width')->item(0)->nodeValue;
								$height		=	$design->getElementsByTagName('height')->item(0)->nodeValue;
								
							}
							foreach($chart->getElementsByTagName('col') as $innercol){
								
								$string 			= str_replace('-', '', $innercol->getElementsByTagName('name')->item(0)->nodeValue); // Replaces all spaces with hyphens.
								$string 			= str_replace(' ', '', $string); // Replaces all spaces with hyphens.
					   			$col				= preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
								$col_name[]			= strtolower($col);
								$col_colour[]		= $innercol->getElementsByTagName('name')->item(0)->getAttribute('color');
							}
							foreach($chart->getElementsByTagName('row') as $innerrow){
						
								$row_length 		=	$innerrow->childNodes->length;
						
								for($i = 0; $i < $row_length;$i++){
									if($i == 0)
									$row_name[] 	=	$innerrow->getElementsByTagName('name')->item($i)->nodeValue;
								}	
								$row_length_value 	=	$row_length-1;
								for($i = 0; $i < $row_length_value;$i++){
									$row_value[$i][]=	$innerrow->getElementsByTagName('value')->item($i)->nodeValue;
								}
							}
						
							$max					=	sizeof($row_name);
							$last 					=	$max-1; 
							
							for($i = 0; $i < $max;$i++){
								$name 				.= $row_name[$i];
								if($i != $last)
									$name 			.= ',';
							}	
							
							foreach($row_value as $arr) {
								 $out[] 			.= implode(",", $arr);
							}
							
							$max_col				=	sizeof($col_name);
							$last_col				=	$max_col-1; 
							for($i = 0; $i < $max_col;$i++){
								if($i == 0 )
									$keys 			.= 'name,';
									$keys 			.= $col_name[$i];
								if($i != $last_col)
									$keys 			.= ',';
							}
							
							foreach($colors as $colors_arr) {
								 $colors_array[] 	.= implode(",", $colors_arr);
							}

							$shortcode 		 =	"[charts ";
							$shortcode 		.= 	" keys='".$keys."'";
							$shortcode 		.= 	' id="'.$id.'"';
							$shortcode 		.= 	' style="'.$width.'|'.$height.'"';
							$shortcode 		.= 	' title="'.$title.'"';
							$shortcode 		.=	' type="'.$type.'"';
							$shortcode 		.=	' link_title="'.$link_title.'"';
							$shortcode 		.=	' link="'.$link.'"';
							$shortcode 		.=	' design_type="'.$design_type.'"';
							$shortcode 		.=	' name="'.$name.'" ';
							
							for($i = 0; $i < $row_length_value;$i++){							
								$shortcode  .=  $col_name[$i].'="'.$out[$i].'" ' ;
							}
							
							$shortcode 		.=	']';
							$find			 =	'<div id="'.$id.'"></div>';
							$replace 		 = 	$shortcode.' '.$find;
							$description 	 = str_replace($find,$replace,$description);
							$row_name 		 = '';
							$name    		 = '';
							$col_name		 = '';
							$keys			 = '';
							$col			 = '';
							$out			 = '';
							$row_value		 = '';
							$chart_count++;
						}
				
						if( isset( $node->getElementsByTagName('block')->item(0)->nodeValue  ) ){
							$smblock 		= $node->getElementsByTagName('block')->item(0)->nodeValue;	
						}else{
							$smblock 		= '';
						}
						
						if( isset( $node->getElementsByTagName('widget')->item(0)->nodeValue ) ){
							$smwidget 		= $node->getElementsByTagName('widget')->item(0)->nodeValue;
						}else{
							$smwidget 		= '';
						}

						/* code added by AW109 on 07-08-16 start */
						if( isset( $node->getElementsByTagName('meta-title')->item(0)->nodeValue  ) ){
							$sm_meta_title	= $node->getElementsByTagName('meta-title')->item(0)->nodeValue;	
						}else{
							$sm_meta_title 	= '';
						}

						if( isset( $node->getElementsByTagName('meta-description')->item(0)->nodeValue  ) ){
							$sm_meta_desc	= $node->getElementsByTagName('meta-description')->item(0)->nodeValue;	
						}else{
							$sm_meta_desc 	= '';
						}

						if( isset( $node->getElementsByTagName('meta-image')->item(0)->nodeValue  ) ){
							$sm_meta_image	= $node->getElementsByTagName('meta-image')->item(0)->nodeValue;	
						}else{
							$sm_meta_image	= '';
						}

						/* code added by AW109 on 07-08-16 end   */					

						if($smwidget != '' && $smblock == ''){
							
							$parts			=	explode('/',$smwidget);
							$widget_title	=	$node->getElementsByTagName('title')->item(0)->nodeValue;	
							$tosidebar		=   $parts[1];					
							$fromarray 		= 	array("title"=>$widget_title,"text"=>$description,"filter"=>'');
							
							$option_names_query = "SELECT option_name,option_value FROM wp_options WHERE option_name='widget_text'";
							$option_names_1 = mysqli_fetch_assoc($conn->query($rsLinks_query));
							$widget_copy_to = unserialize($option_names_1['option_value']);						 
						
							foreach($widget_copy_to as $to_key=>$to_value){
								if(in_array($widget_title,$to_value)){
									$tokey 			= $to_key;
									$flag_exists 	= "Yes";
									break;
								} 	
							}
							$multiwidget = 1;
		
							if($flag_exists=="Yes"){
								unset($widget_copy_to[$tokey]);
							}
								
							$widget_copy_to[] = $fromarray;
							$widget_copy_to['_multiwidget'] = $multiwidget;
							
							$widget 	= serialize($widget_copy_to);
							$query 		= 'UPDATE wp_options SET option_value="'.addslashes($widget).'" WHERE option_name="widget_text"';
							$conn->query($query);
						
							$option_names_sidebars1query = $wpdb->get_results("SELECT option_name,option_value FROM wp_options WHERE option_name LIKE 'sidebars_%'");
							$option_names_sidebars1 	 = mysqli_fetch_assoc($conn->query($option_names_sidebars1query));
							$widget_copy_to 			 = unserialize($option_names_1['option_value']);
							$sidebar_copy_to 			 = unserialize($option_names_sidebars1['option_value']);
						
						    if($flag_exists=="Yes"){
								foreach($sidebar_copy_to[$tosidebar] as $keyto=>$valueto){
									 if(strstr($valueto,"text-".$tokey)){
										$keytoremove = $keyto;
										break;
									 }
								}
								unset($sidebar_copy_to[$tosidebar][$keytoremove]);
							}
	
							$position 			=  array_search("text-".$fromkey,$sidebar_copy_from[$fromsidebar]);
											
							unset($sidebar_copy_to[$tosidebar]['text']);
							$tosidebar_keys 	= end(array_keys($widget_copy_to));
							array_splice($sidebar_copy_to[$tosidebar],$position,0,"text-".$tosidebar_keys);
							$sidebar_copy_to[$tosidebar]['text']	=	"";
							$sidebar_copy 		= serialize($sidebar_copy_to);
						 	$sidebar_query 		= 'UPDATE wp_options SET option_value="'.addslashes($sidebar_copy).'" WHERE option_name="sidebars_widgets"';
							$conn->query($sidebar_query);
						
						}else{
							$enclosurelink = $node->getElementsByTagName('enclosure');
							if($enclosurelink->item(0)!=""){
								$URL 		= $enclosurelink->item(0)->getAttribute('url');
								$imagetype 	= $enclosurelink->item(0)->getAttribute('type');
							}else{
								$URL 		= "";
								$imagetype 	= "";
							}
							$itemRSS 	= array( 
									'title' 			=> $node->getElementsByTagName('title')->item(0)->nodeValue,
									'smblock' 			=> $node->getElementsByTagName('block')->item(0)->nodeValue ,
									'smmetatitle' 		=> $node->getElementsByTagName('meta-title')->item(0)->nodeValue ,
								   	'smmetadesc' 		=> $node->getElementsByTagName('meta-description')->item(0)->nodeValue ,
								   	'smmetaimage' 		=> $node->getElementsByTagName('meta-image')->item(0)->nodeValue,
									'enclosure' 		=> $URL,
									'sourcelink' 		=> $node->getElementsByTagName('link')->item(0)->nodeValue ,
									'post_mimie_type' 	=> $imagetype,
									'description' 		=> $description,
									'pubDate' 			=> $node->getElementsByTagName('pubDate')->item(0)->nodeValue				
								   	 
								   );

						   $post_mimie_type = $itemRSS['post_mimie_type'];
						   $post_title 		= preg_replace('/\s+/',' ',trim($itemRSS['title']));
						   $post_name 		= sanitize_title($post_title);
						   $smblock 		= $itemRSS['smblock'];
						   $smmetatitle		= $itemRSS['smmetatitle'];
						   $smmetadesc		= $itemRSS['smmetadesc'];
						   $smmetaimage		= $itemRSS['smmetaimage'];
						   $enclosure 		= $itemRSS['enclosure'];
						   $sourcelink 		= $itemRSS['sourcelink'];
						   $post_date 		= date("Y-m-d H:i:s",strtotime($itemRSS['pubDate']));
						   $description 	= $itemRSS['description'];
						   $excerpt 		= $itemRSS['description'];
						   $post_author 	= 1; 
						   //===========Add/Update Terms===========//
						   //==Search Terms Exists==================//
				  			$rsTerms = array();
				  			$rsTerms_query 	= "SELECT * FROM wp_terms WHERE name = '".mysql_real_escape_string( $cattitle)."'";	

							array_push($rsTerms,mysqli_fetch_assoc( $conn->query( $rsTerms_query) ) );

							//==Search Terms Exists==================//	
							//==Add Terms==//
							$term_taxonomy_id = 0;
						   	if(empty($rsTerms[0]) ){	

								$slug = sanitize_title($cattitle);
								$insertterms = "INSERT INTO wp_terms (name,slug) VALUES('".$cattitle."','".$slug."')";
								$conn->query($insertterms);
								$last_termquery = "SELECT max( term_id ) as last_term_id FROM wp_terms";
								$last_termresults = $conn->query($last_termquery);
								$last_termresults = mysqli_fetch_assoc($last_termresults);
								$termsid = $last_termresults['last_term_id'];	
								//===========Add/Update Terms Taxonomy===========//
								if($termsid!=0){						
									$inserttermstaxonomy = "INSERT INTO wp_term_taxonomy (term_id,taxonomy) VALUES('".$termsid."','category')";
									$conn->query($inserttermstaxonomy);
								
									$last_taxonomyrelidquery = "SELECT max( term_taxonomy_id ) as last_term_taxonomy_id FROM  wp_term_taxonomy";
									$last_taxonomyrelidresults = $conn->query($last_taxonomyrelidquery);
									$last_taxonomyrelidresults = mysqli_fetch_assoc($last_taxonomyrelidresults);
									$term_taxonomy_id = $last_taxonomyrelidresults['last_term_taxonomy_id'];
								}
								//===========Add/Update Terms Taxonomy===========//
							}else{
								$termsid = $rsTerms[0]['term_id'];
								if($termsid!=0){
									$termstaxquery = "SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE term_id='".$termsid."' AND taxonomy='category'";
									$last_termresults = mysqli_fetch_assoc($conn->query($termstaxquery));
									$term_taxonomy_id = $last_termresults['term_taxonomy_id'];
								}
							}
							//==Add Terms==//							
							//===========Add/Update Terms===========//
							//===========Add/Update Posts===========//
							if($term_taxonomy_id!=0){
								$SQL_POSTS = "SELECT wp_posts.ID,wp_posts.post_title FROM wp_posts LEFT JOIN wp_term_relationships ON wp_posts.ID=wp_term_relationships.object_id WHERE wp_posts.post_name LIKE '%".$post_name."%' AND wp_term_relationships.term_taxonomy_id='".$term_taxonomy_id."' AND wp_posts.post_type='post'";
								$rsPosts = array();
								if($postresults = $conn->query($SQL_POSTS)){
									while ($row = $postresults->fetch_assoc()) {
										array_push($rsPosts,$row);
									}							
								}
							}
							if(empty($rsPosts[0]['post_title']) && !empty($description)){
								//=====Check post name exists===//		
								$SQLPOSTSLUG = "SELECT post_name FROM wp_posts WHERE post_name LIKE '%".$post_name."%' ORDER BY ID DESC LIMIT 1";
								$rsPostSlug = array();
								if($postresults = $conn->query($SQLPOSTSLUG)){
									while ($row = $postresults->fetch_assoc()) {
										array_push($rsPostSlug,$row);
									}							
								}
								if(!empty($rsPostSlug)){
									$post_slug 		= explode("-",$rsPostSlug[0]['post_name']); 
									$lastelement 	= $post_slug[count($post_slug)-1];
									if(is_numeric($lastelement)){
										$increaseslug 	= $lastelement+1;
										$postslug 		= $post_name."-".$increaseslug;
									}else{
										$postslug 		= $post_name."-2";
									}
								}else{
									$postslug 			= $post_name;
								}

								//**code to process UTF-8 Caharacters. **//
								mysqli_set_charset($conn, 'utf8');

								//=====Check post name exists===//					
								$insert_post_query = 'INSERT INTO wp_posts (post_author,post_date,post_date_gmt ,post_content,post_title,post_excerpt 	,post_status,comment_status,ping_status ,post_name,post_modified,post_modified_gmt,post_type) VALUES("'.$post_author.'","'.$post_date.'","'.$post_date.'","'.addslashes($description).'","'.addslashes($post_title).'","'.addslashes($excerpt).'","publish","open","closed","'.$postslug.'","'.$post_date.'","'.$post_date.'","post")';
								$conn->query($insert_post_query);
		
								$last_postidquery 	= "SELECT max( ID ) as post_id FROM  wp_posts";
								$last_postidresults = $conn->query($last_postidquery);
								$last_postidresults = mysqli_fetch_assoc($last_postidresults);
								$ID 				= $last_postidresults['post_id'];
								if($ID!=0){
									//==UPDATE GUID==//
									$guid = $siteurl."?p=".$ID;
									$update_post_query = "UPDATE wp_posts SET guid='".$guid."' WHERE ID='".$ID."'";
									$conn->query($update_post_query);
									//===========Add/Update Postmeta===========//
									//===========Add Image======================//
									$postmetaarray = array();
									if(!empty($enclosure)){
										$source 	 = $enclosure;
										$destination = $_SERVER['DOCUMENT_ROOT']."/".$site_slug."/wp-content/uploads/".basename($enclosure)."";
								
										if(!is_dir($destination)){
											mkdir(dirname($destination), 0775, true);
										}	
										@copy($source,$destination);
										$postmetaarray["enclosure"] = $enclosure;
										//==Insert Image===//
										$imageurlguid 	= $siteurl."/wp-content/uploads/".basename($enclosure)."";
										$posttitle 		= preg_replace('/\.[^.]+$/', '', basename($enclosure));
										$postname 		= sanitize_title($posttitle);
										
										$insert_post_image_query = 'INSERT INTO wp_posts (post_author,post_date,post_date_gmt ,post_title,post_status,comment_status,ping_status,post_name,post_modified,post_modified_gmt,post_parent,guid,post_type,post_mime_type) VALUES("'.$post_author.'","'.$post_date.'","'.$post_date.'","'.$posttitle.'","inherit","open","open","'.$postname.'","'.$post_date.'","'.$post_date.'","'.$ID.'","'.$imageurlguid.'","attachment","'.$post_mimie_type.'")';
										$conn->query($insert_post_image_query);
										$last_post_imageidquery = "SELECT max( ID ) as post_id FROM  wp_posts";
										$last_post_imageidresults = $conn->query($last_post_imageidquery);
										$last_post_imageidresults = mysqli_fetch_assoc($last_post_imageidresults);
										$thumbnailid = $last_post_imageidresults['post_id'];
										//==Insert Image===//
										$postmetaarray["_thumbnail_id"] = $thumbnailid;
									
										//===========Add Image======================//
										$postmetaarray["sm:block"] 				= $smblock;
										$postmetaarray["sm:meta-title"] 		= $smmetatitle;
										$postmetaarray["sm:meta-description"] 	= $smmetadesc;
										$postmetaarray["sm:meta-image"] 		= $smmetaimage;

										if(!empty($sourcelink)){
											$postmetaarray["syndication_permalink"] = $sourcelink;
										}
										foreach($postmetaarray as $key=>$value){
											$querypostmeta = 'INSERT INTO wp_postmeta(post_id,meta_key,meta_value) VALUES("'.$ID.'","'.$key.'","'.$value.'")';
											$conn->query($querypostmeta);
										}
										if(!empty($enclosure) && $thumbnailid!=0){
											$querypostmeta1 = 'INSERT INTO wp_postmeta(post_id,meta_key,meta_value) VALUES("'.$thumbnailid.'","_wp_attached_file","'.basename($enclosure).'")';
											$conn->query($querypostmeta);
										}
								
										$insertquerytermrels = 'INSERT INTO wp_term_relationships(object_id ,term_taxonomy_id) VALUES("'.$ID.'","'.$term_taxonomy_id.'")';
										$conn->query($insertquerytermrels);
										$updatecounter = "UPDATE wp_term_taxonomy SET count=count+1 WHERE term_taxonomy_id='".$term_taxonomy_id."'";
										$conn->query($updatecounter);
										$updatelinks = "UPDATE wp_links SET link_updated='".date("Y-m-d H:i:s")."' WHERE link_rss='".$rssfeed."'";
										$conn->query($updatelinks);
				
									}
								}
							}	
							//===========Add/Update Posts===========//
						}   //else post create
					}	//for loop end
				}
				$flag = true;
				$rssfeeds .= "<u>".$rssfeed."</u> Updated<br/>"; 
			}
		
		    $feedupdates = "<strong>RSS Feed(s) Updated.....</strong>";	
		  	return $feedupdates;	
		}
	}
	//update feed function ends here. 
	public function createNewSite($address, $title, $email, $theme = '',$template='',$feeds) {
            
        $address 	= str_replace('_','-',$address);
    	$domain 	= '';
		if ( preg_match( '|^([a-zA-Z0-9-])+$|', $address ) ){
		    $domain = strtolower( $address );
        }    
        // If not a subdomain install, make sure the domain isn't a reserved word
		if ( ! is_subdomain_install() ) {
			$subdirectory_reserved_names = apply_filters( 'subdirectory_reserved_names', array( 'page', 'comments', 'blog', 'files', 'feed' ) );
			if ( in_array( $domain, $subdirectory_reserved_names ) )
				wp_die( sprintf( __('The following words are reserved for use by WordPress functions and cannot be used as blog names: <code>%s</code>' ),
					implode( '</code>, <code>', $subdirectory_reserved_names ) ) );
		}

		$email = sanitize_email( $email );

		if ( empty( $domain ) )
			wp_die( __( 'Missing or invalid site address.' ) );
		if ( empty( $email ) )
			wp_die( __( 'Missing email address.' ) );
		if ( !is_email( $email ) )
			wp_die( __( 'Invalid email address.' ) );

		if ( is_subdomain_install() ) {
			$newdomain = $domain . '.' . preg_replace( '|^www\.|', '', $this->current_site->domain );
			$path = $base;
		} else {
			$newdomain = $this->current_site->domain;
			$path = $base . '/' . $domain . '/';
		}

		$password 	= 'N/A';
		$user_id 	= email_exists($email);
		if ( !$user_id ) { // Create a new user with a random password
			$password 	= wp_generate_password( 12, false );
			$user_id 	= wpmu_create_user( $domain, $password, $email );
			if ( false == $user_id )
				wp_die( __( 'There was an error creating the user.' ) );
			else
				wp_new_user_notification( $user_id, $password );
		}

		$id 	= wpmu_create_blog( $newdomain, $path, $title, $user_id , array( 'public' => 1 ), $this->current_site->id );
		
		if ( !is_wp_error( $id ) ) {
			if ( !is_super_admin( $user_id ) && !get_user_option( 'primary_blog', $user_id ) )
				update_user_option( $user_id, 'primary_blog', $id, true );
			$content_mail 	= sprintf( __( "New site created by %1s\n\nAddress: %2s\nName: %3s"),
				$current_user->user_login , get_site_url( $id ), stripslashes( $title ) );
			$admin_url 		= esc_url( get_admin_url( absint( $id ) ) );
			$this->messages[] = sprintf( __( 'Site added. <a href="%1$s">Visit Dashboard</a> or <a href="%2$s">Edit Site</a>' ),
				$admin_url, network_admin_url( 'site-info.php?id=' . absint( $id ) ) );
		}else {
			//===Update existing configuration from the config file=====//
			$rssarray 		= explode("\n", $feeds);
			$doc 			= new DOMDocument();
			$rssFeedArray 	= array();
			
			foreach($rssarray as $value){
				 if(trim($value)!=""){
					$rssFeedArray[] = trim($value);
					$rssfeed 		= trim($value);					  
					$doc->load($rssfeed);
					$title 			= $doc->getElementsByTagName('title')->item(0)->nodeValue;
					$link_url 		= $doc->getElementsByTagName('link')->item(0)->nodeValue;
					$description 	= $doc->getElementsByTagName('description')->item(0)->nodeValue;
					$ttl 			= $doc->getElementsByTagName('ttl')->item(0)->nodeValue;	
					$pubDate 		= $doc->getElementsByTagName('pubDate')->item(0)->nodeValue;
					$rssFeedArrayUpdate[$rssfeed] = array("link_name"=>$title,"link_url"=>$link_url,"description"=>$description,"ttl"=>$ttl,"pubDate"=>$pubDate);
					//========Read Rss Feed Content=========//
				 }
			}
			//========Fetch BlogId=================//
			$BlogId 	= $rsBlogs[0]->blog_id;

			//========Fetch Link=================//
			if(count($rsLinks)>0){ 
				for($i=0;$i<count($rsLinks);$i++){
				
					if(!in_array($rsLinks[$i]->link_rss,$rssFeedArray)){
					 
						//==========Deactivate Rss Feed====================//
						$updatequery = "UPDATE wp_".$BlogId."_links SET link_visible='N',link_notes='' WHERE link_id='".$rsLinks[$i]->link_id."'";						
						//==========Deactivate Rss Feed====================//
						$doc->load($rsLinks[$i]->link_rss);
						$catname 	= $doc->getElementsByTagName('title')->item(0)->nodeValue;
						$termid 	= $rsTerms[0]->term_id;
						//===Fetch Taxonomy ID===============//
						if($termid!=0){
							$termtaxonomyid = $rsTexonomy[0]->term_taxonomy_id;
							//========Post Status Inactive===================//
							if($termtaxonomyid!=0){
								$SQL_POST = "SELECT wp_".$BlogId."_posts.ID FROM  wp_".$BlogId."_posts,wp_".$BlogId."_term_relationships WHERE  wp_".$BlogId."_posts.ID=wp_".$BlogId."_term_relationships.object_id	AND wp_".$BlogId."_term_relationships.term_taxonomy_id='".$termtaxonomyid."'";
								if(count($rsPOST)>0){
									for($k=0;$k<count($rsPOST);$k++){
										if($rsPOST[$k]->ID!=0){
											$updatepostquery = "UPDATE wp_".$BlogId."_posts SET post_status='draft' WHERE ID='".$rsPOST[$k]->ID."'";
										}
									}
								}	
							}
							//========Post Status Inactive===================//	
						}
						//===Fetch Taxonomy ID===============//					
					}
				}
			}
			
			//Check if link not exists in new XML Config file
			//Add new link from new XML Config file
			$created_menu 	=	 "";
			foreach($rssFeedArrayUpdate as $key=>$value){
				
				$SQL = 'SELECT * FROM wp_'.$BlogId.'_links WHERE link_rss = "'.$key.'"';
				
				if(empty($rsLinksExists)){	
					$link_name 		 = $value["link_name"];
					$link_url 		 = $value["link_url"];
					$description 	 = $value["description"];
					$ttl 			 = $value["ttl"];
					$pubDate 		 = $value["pubDate"];
					$link_rss 		 = $key;					
					$insertfeedquery = "INSERT INTO wp_".$BlogId."_links (link_url,link_name,link_description,link_rss) VALUES('".$link_url."','".$link_name."','".$description."','".$link_rss."')";
					
					//====Insert Into Taxonomy Relationships====//
					$SQLOPTIONS1 	 = 'SELECT option_value FROM wp_'.$BlogId.'_options WHERE option_name = "feedwordpress_cat_id"';
					$taxonomyrelid 	 = $rssfeedwordpress_cat_id[0]->option_value;
					$object_id 		 = mysql_insert_id();	
					$inserttermrelquery = "INSERT INTO wp_".$BlogId."_term_relationships (object_id ,term_taxonomy_id) VALUES('".$object_id."','".$taxonomyrelid."')";
					//====Insert Into Taxonomy Relationships====//
					$created_menu .= $key."<br>";
				}
				//===========Create Category Array=========//
				$rssTerms[$value["link_name"]] = $value["link_name"];				 
			}
			
			//==================Update Terms=========================//
			if(count($rssTerms)>0){
				$updatetermquery = "UPDATE wp_".$BlogId."_terms SET term_order=''";
				foreach($rssTerms as $terms){
					$SQLTERMS = 'SELECT * FROM wp_'.$BlogId.'_terms WHERE name = "'.addslashes($terms).'"';
					$slug = sanitize_title($terms);
					if(empty($rsTermsExists)){
						$insertterms = "INSERT INTO wp_".$BlogId."_terms (name,slug) VALUES('".$terms."','".$slug."')";
						$termsid = mysql_insert_id();
						if($termsid!=0){						
							$inserttermstaxonomy = "INSERT INTO wp_".$BlogId."_term_taxonomy (term_id,taxonomy) VALUES('".$termsid."','category')";
						}
					}
					 
				}
				//========Update Options==============//
			    $feedwordpress_pages = serialize(serialize($rssTerms));
			    $SQLOPTIONS = 'UPDATE wp_'.$BlogId.'_options SET option_value="'.addslashes($feedwordpress_pages).'" WHERE option_name = "feedwordpress_pages"';
				$updatepostquery = "UPDATE wp_".$BlogId."_posts SET post_status='publish' WHERE post_type='nav_menu_item'";
				
				if(isset($_POST['blogtemplate']) && !empty($_POST['blogtemplate'])){
				
					$blogtempid = $rsBlogTemplate[0]->blog_id;
			
					if($theme=="advanced-newspaper"){
						$theme_option_name = "theme_mods_advanced-newspaper";
					}elseif($theme=="wpnewspaper"){
						$theme_option_name = "theme_mods_wpnewspaper";
					}
					
					$themeOptionValue 	= $themeOptionFromTemplate[0]->option_value;
					$SQLOPTIONSMENU 	= "UPDATE wp_".$BlogId."_options SET option_value='".$themeOptionValue."' WHERE option_name = '".$theme_option_name."'";
				}
				
				//========Update Custom Menu Options===========//
			}			  
			//==================Update Terms==========================//			 
			//Add new link from new XML Config file
			//==Update existing configuration from the config file==//
			if(!empty($created_menu)){
				echo "<p style=\"font-size:15px;\">RSS Feeds Added.....</p>";
				echo "<p>".$created_menu."</p>";
			}else{
				echo "<p style=\"font-size:15px;font-weight:bold;\">RSS Feeds already exists.....</p>";
			}
			$SQSITEURL 	= 'SELECT option_value FROM wp_'.$BlogId.'_options WHERE option_name = "siteurl"';
			$SITEURL 	= $RSSITEURL[0]->option_value;
			$errormsg 	= "<p style=\"font-size:14px;\">".$SITEURL." already exists!</p>";			 
			wp_die($errormsg);
		}
		$this->copyblog($template,$id);
		switch_to_blog($id);
		$this->messages[] 	= "Deleting sample posts";
		$posts 				= get_posts(array('numberposts' => 0));
		foreach ($posts as $post) {
			wp_delete_post($post->ID);
		}

		$this->messages[] 	= "Deleting sample pages";
		$page_ids 			= get_all_page_ids();
		foreach ($page_ids as $page_id) {
			wp_delete_post($page_id);
		}

		$this->messages[] 	= "Deleting all links";
		$bookmarks 			= get_bookmarks();
		foreach ($bookmarks as $bm) {
			wp_delete_link($bm->link_id);
		}

		if ( !empty( $theme ) ) {
			$this->messages[] = "Switching to theme: $theme";
			switch_theme(wp_get_theme($theme)->get_template(), $theme);
			global $wp_rewrite;
			$wp_rewrite->set_permalink_structure('/%year%/%monthnum%/%day%/%postname%/');
			flush_rewrite_rules();
		}
		switch_to_blog($old_blog);
	}

	public function createNewFeedSite($address, $title, $email, $theme = '', $feeds = '', $pages ,$add_info, $updates = 'shutdown',$template) {
		if ( empty( $feeds ) )
			wp_die( __( 'Missing feed URLs.' ) );
		$this->createNewSite($address, $title, $email, $theme,$template,$feeds);
		$rss_user = email_exists('rss-feeds@scanmine.com');
		if ( !$rss_user )
		add_user_to_blog( $id, $rss_user, 'contributor' );
		switch_to_blog($id);
        update_option('feedwordpress_pages',$pages);
        update_option('Feed_Lang',$add_info['language']);
		update_option('WPLANG',$add_info['language']);
        update_option('meta_publisher',$add_info['meta_publisher']);
        update_option('meta_keywords',$add_info['meta_keywords']);
        update_option('_scanmine-domain_',$add_info['_scanmine-domain_']);
        update_option('_scanmine-topic_',$add_info['_scanmine-topic_']); 
		switch_to_blog($old_blog);
	}

	public function createNewFeedSiteFromConfig($url) {     
		
		if ( empty( $url ) ){
			wp_die( __( 'Missing config URL.' ) );
		}
                
		$reader = new ConfigReader($url);
		$reader->parse();
		foreach ($reader->getErrors() as $error){
			$this->messages[] = "<b style='color:red'>$error</b>";
		}
		
		$address 	= (string) $reader->getProperty('address');
		$site_slug 	= (string) $reader->getProperty('address'); 
		$title 		= (string) $reader->getProperty('title');
		$email 		= (string) $reader->getProperty('owner');
		$theme 		= (string) $reader->getProperty('theme');
		$template 	= (string) $reader->getProperty('template');
		$updates 	= $reader->getOption('update-method', 'shutdown');
		$aa 		= (array)$reader->getOptions();		
				
        for($ii=0;$ii<count($aa['option']);$ii++){
            $name 	= (array)$aa['option'][$ii]->name;
			
            if($name[0] == 'language'){
                $language = (array)$aa['option'][$ii]->value;
                if($language[0] == "en"){
                    $add_info['language'] = "en_US";
                } else if($language[0] == "no"){
                    $add_info['language'] = "nb_NO";
                } else if($language[0] == "sv"){
                    $add_info['language'] = "sv_SE";
                } else if($language[0] == "se"){
                    $add_info['language'] = "sv_SE";
                }else {
                    $add_info['language'] = $language[0];
                }
            }
            if($name[0] == 'keywords'){
                $keyword = (array)$aa['option'][$ii]->value;
                $add_info['meta_keywords'] = $keyword[0];
            }
            if($name[0] == 'publisher'){
                $publisher = (array)$aa['option'][$ii]->value;
                $add_info['meta_publisher'] = $publisher[0];
            }
            if($name[0] == '_scanmine-domain_'){
                $publisher = (array)$aa['option'][$ii]->value;
                $add_info['_scanmine-domain_'] = $publisher[0];
            }
            if($name[0] == '_scanmine-topic_'){
                $publisher = (array)$aa['option'][$ii]->value;
                $add_info['_scanmine-topic_'] = $publisher[0];
            }
			if($name[0] == 'logo1'){
                $logo1 = (array)$aa['option'][$ii]->value;
                $of_logo1 = $logo1[0];
            }
			if($name[0] == 'logo2'){
                $logo2 = (array)$aa['option'][$ii]->value;
                $of_logo2 = $logo2[0];
            }                    
        }
		$of_logo 	=  $of_logo1.",". $of_logo2;	
		$pub 		= explode('pub/',$url);
        $xml_rss 	= explode('/xml_rss',$pub[1]);	
	    /* end of copy setting */  
        //============Check templates themes=================//    
		$feeds 		= "";
		
        foreach ($reader->getProperty('feeds')->feed as $feed){
    		$feeds .= $feed . "\n";
        }
		$p1 		= (array)$reader->getProperty('options');
		$p1 		= (array)$reader->getProperty('pages');
		$AllPages 	= array();
				
        for($i=0;$i<count($p1['page']);$i++){
            $p2 	= (array)$p1['page'][$i];
            $p4 	= array();
            if($p2['page']){
                for($j=0;$j<count($p2['page']);$j++){
                  $p3 	=  (array)$p2['page'][$j];
                  $p4[] = $p3['title'];
                }
            }
			
            if(!empty($p4)){
	            $AllPages[$p2['title']] = $p4;
            } else {
    	        $AllPages[$p2['title']] = $p2['title'];    
            } 	
        }
		$pages 		= serialize($AllPages);
        
		/* SWITCHING TO NEW BLOG AND APPLYING SETTINGS */
		//========Add Links=============//
		$rssarray 	= explode("\n",$feeds);
		$doc 		= new DOMDocument();
		
		foreach($rssarray as $value){
			if(trim($value)!=""){
				$rssfeed 						= trim($value);					  
				$doc->load($rssfeed);
				$title 							= $doc->getElementsByTagName('title')->item(0)->nodeValue;
				$link_url 						= $doc->getElementsByTagName('link')->item(0)->nodeValue;
				$description 					= $doc->getElementsByTagName('description')->item(0)->nodeValue;
				$rssFeedArrayUpdate[$rssfeed] 	= array("link_name"=>$title,"link_url"=>$link_url,"description"=>$description);
			}
		} 
		//====Add Terms=========//
		$servername 	= DB_HOST;
		$username 		= DB_USER;
		$password 		= DB_PASSWORD;
		$mysql_database = str_replace('-', '_', $site_slug);
		
		$conn 			= new mysqli($servername, $username, $password, $mysql_database);		
		
		$SQLTERMS 		= 'SELECT * FROM wp_terms WHERE name ="Contributors"';
		$results 		= $conn->query($SQLTERMS);
		$rsTermsExists 	= array();
		if($results->num_rows!=0){
			array_push($rsTermsExists, mysqli_fetch_array($results)); 
		}

		$taxonomyrelid 	= 0;
		if(empty($rsTermsExists)){
			$inserttermsquery 	= "INSERT INTO wp_terms(name,slug) VALUES('Contributors','contributors')";
			$result 			= $conn->query($inserttermsquery);
			$last_termquery 	= "SELECT max( term_id ) as last_term_id FROM wp_terms";
			$last_termresults 	= $conn->query($last_termquery);
			$last_termresults 	= mysqli_fetch_assoc($last_termresults);
			$terms_id 			= $last_termresults['last_term_id'];	
			$inserttaxonomyquery 		= "INSERT INTO wp_term_taxonomy(term_id,taxonomy) VALUES('".$terms_id."','taxonomy')";
			$conn->query($inserttaxonomyquery);
			$last_taxonomyrelidquery 	= "SELECT max( term_taxonomy_id ) as last_term_taxonomy_id FROM  wp_term_taxonomy";
			$last_taxonomyrelidresults 	= $conn->query($last_taxonomyrelidquery);
			$last_taxonomyrelidresults 	= mysqli_fetch_assoc($last_taxonomyrelidresults);
			$taxonomyrelid 				= $last_taxonomyrelidresults['last_term_taxonomy_id'];	
			$insertfeedwordpress_cat_idquery = "INSERT INTO wp_options(option_name,option_value) VALUES('feedwordpress_cat_id',$taxonomyrelid)";
			$conn->query($insertfeedwordpress_cat_idquery);

			//Add Taxonomy=======//
		}else{
			
			$terms_id 			= $rsTermsExists[0]['term_id'];
			$SQLTERMSTAX 		= 'SELECT * FROM wp_term_taxonomy WHERE term_id ="'.$terms_id.'"';
			$rsTermsTaxresults 	= $conn->query($SQLTERMSTAX);
			$rsTermsTax 		= mysqli_fetch_assoc($rsTermsTaxresults);
			$taxonomyrelid 		= $rsTermsTax['term_taxonomy_id'];
		}
		
		//====Add Terms=========//
		$counetrfeeds 			= 0;
		foreach($rssFeedArrayUpdate as $key=>$value){
			$counetrfeeds 		= $counetrfeeds+1;
			$link_name 			= $value["link_name"];
			$link_url 			= $value["link_url"];
			$description 		= $value["description"];				 
			$link_rss 			= $key;	

			mysqli_set_charset($conn, 'utf8');				 
			$insertfeedquery 	= "INSERT INTO wp_links (link_url,link_name,link_description,link_rss) VALUES('".$link_url."','".$link_name."','".$description."','".$link_rss."')";
			$conn->query($insertfeedquery);
			
			$last_object_idquery 	= "SELECT max( link_id ) as last_link_id FROM  wp_links";
			$last_object_idresults 	= $conn->query($last_object_idquery);
			$last_object_idresults 	= mysqli_fetch_assoc($last_object_idresults);
			$object_id 				= $last_object_idresults['last_link_id'];
			//==Add Terms Taxonomy Relationships=======//
			$inserttermrelquery 	= "INSERT INTO wp_term_relationships (object_id ,term_taxonomy_id) VALUES('".$object_id."','".$taxonomyrelid."')";
			$conn->query($inserttermrelquery);
			
			//==Add Terms Taxonomy Relationships=======//			
			//===========Create Category Array=========//
			$addedfeeds 			= "Added ".$counetrfeeds." feed sources:<br/>";
			$feedsources 			.= "<a href=\"".$key."\" target=\"_blank\">".$key."</a><br/>";
		}
		
		//========Add Links=============//		
		$this->messages[] 			= $addedfeeds;
		$this->messages[] 			= $feedsources;
		$this->messages[] 			= "Storing config XML URL";
		$this->messages[] 			= $feedupdates;
		$this->messages[] 			= "<br/>";
		$insert_scanmine_config_urlquery = "INSERT INTO wp_options(option_name,option_value) VALUES('scanmine_config_url',$url)";
		$conn->query($insert_scanmine_config_urlquery);
		
		if ( $reader->hasProperty('description') )
			$insert_blogdescriptionquery = "INSERT INTO wp_options(option_name,option_value) VALUES('blogdescription','".$reader->getProperty('description')."')";
			$conn->query($insert_blogdescriptionquery);
		
			if( isset($_POST['blogtemplate']) && !empty($_POST['blogtemplate']) ){
				$insert_blogtemplatequery = "INSERT INTO wp_options(option_name,option_value) VALUES('blogtemplate','".$_POST['blogtemplate']."')";
				$conn->query($insert_blogtemplatequery);
			}else{	
				if ( $reader->hasProperty('template') ){
					$insert_blogtemplatequery = "INSERT INTO wp_options(option_name,option_value) VALUES('blogtemplate','".$reader->getProperty('template')."')";
					$conn->query($insert_blogtemplatequery);
				}
            }    
            
            if ( $reader->hasOption('keywords') ){
				$insert_metakeywordsquery = "INSERT INTO wp_options(option_name,option_value) VALUES('meta_keywords','".$reader->getOption('keywords')."')";
				$conn->query($insert_metakeywordsquery);
			}
			
			if ( $reader->hasOption('publisher') ){
				$insert_meta_publisherquery = "INSERT INTO wp_options(option_name,option_value) VALUES('meta_publisher','".$reader->getOption('publisher')."')";
				$conn->query($insert_meta_publisherquery);
	        }       
        
			if ($language){   }
 
			/* WE ARE DONE AND GO BACK TO NETWORK ADMIN */
			//=======RSS Feed Updates==============//
			$feedupdates = $this->update_rssfeed($old_blog,$site_slug);
			//=======RSS Feed Updates==============//
			//switch_to_blog($old_blog);
	}	
}