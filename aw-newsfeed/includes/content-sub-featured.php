<?php	
	$post_ID 	=  get_the_ID();
	$content 	=  get_the_content(); 
	$title 		=  get_the_title();
	$permalink 	=  get_permalink();	
	// $image 		=  genesis_get_image(  array(  'format' => 'url', 'size' => genesis_get_option(  'image_size'  )   )   );

	$pos 		= strpos( $content, '<!--more-->'); 
	$pos1 		= strpos( $content,'#more-');
	$link 		= '';
		
	$head_len = strlen($title);
	$head_co = $head_len/10;
	$head_count = round($head_co);
	//* Set what goes inside the wrapping tags
	if($head_count == 1)
	{
		$sm_class = 'sm-head-1';
	}
	else if($head_count == 2)
	{
		$sm_class = 'sm-head-2';
	}
	else if($head_count == 3)
	{
		$sm_class = 'sm-head-3';
	}
	else if($head_count == 4)
	{
		$sm_class = 'sm-head-4';
	}
	else if($head_count == 5)
	{
		$sm_class = 'sm-head-5';
	}
	else if($head_count == 6)
	{
		$sm_class = 'sm-head-6';
	}
	else if($head_count == 7)
	{
		$sm_class = 'sm-head-7';
	}
	else if($head_count == 8)
	{
		$sm_class = 'sm-head-8';
	}
	else if($head_count == 9)
	{
		$sm_class = 'sm-head-9';
	}
	else
	{
		$sm_class = 'sm-head-10';
	}


	if (  $pos || $pos1  ) {		
		$link 	= get_the_permalink();
	}else{		
		$link 	= get_post_meta( $post_ID  , 'syndication_permalink', true );
	}

	$url 		= wp_get_attachment_url( get_post_thumbnail_id( $post_ID ) );

	if( stripos($url, '/var/www/html/') !== true ){
		$url 	= str_replace("/var/www/html/","http://iris.scanmine.com/", $url);
	}

	//** get the first menu item **//
	$top 				= wp_get_nav_menu_items('top-primary-menu');
	/*$link 				= $top[0]->title;
	$aw_category_name 	= $link;*/
	$sticky 			= get_option( 'sticky_posts' );
	rsort($sticky);
	$sticky 			= array_slice( $sticky, 0, 1 );
	$get_current_arr 	= get_the_category();
	$get_current_cat 	= $get_current_arr[0]->name;
	
		foreach ($sticky as  $stickies) {
			$block_value 		= get_post_meta($stickies , 'sm:block',true );

		if( is_sticky($stickies) && $block_value==$get_current_cat."/block12"){	
			//file_put_contents(dirname(__FILE__).'/block_value.log', print_r($block_value,true),FILE_APPEND);
			$sticky_url 		= wp_get_attachment_url( get_post_thumbnail_id( $stickies ) );
			if( stripos($sticky_url, '/var/www/html/') !== true ){
				$sticky_url 	= str_replace("/var/www/html/","http://iris.scanmine.com/", $sticky_url);
			}
			$sticky_permalink 	= get_permalink( $stickies );
			$sticky_title	 	= get_the_title( $stickies );
			$content_post 		= get_post( $stickies );
			$content 			= $content_post->post_content;
			$dot 				= ".";
			$position 			= stripos ($content, $dot); 
	        $offset 			= $position + 1; 
	        $position2 			= stripos ($content, $dot, $offset);
	        $first_two 			= substr($content, 0, $position2);
			$content 			= $first_two."."."<a href='".$sticky_permalink."#more-".$stickies."'>(more..)</a>"; 

			$head_len = strlen($sticky_title);
			$head_co = $head_len/10;
			$head_count = round($head_co);
			//* Set what goes inside the wrapping tags
			if($head_count == 1)
			{
				$sm_class = 'sm-head-1';
			}
			else if($head_count == 2)
			{
				$sm_class = 'sm-head-2';
			}
			else if($head_count == 3)
			{
				$sm_class = 'sm-head-3';
			}
			else if($head_count == 4)
			{
				$sm_class = 'sm-head-4';
			}
			else if($head_count == 5)
			{
				$sm_class = 'sm-head-5';
			}
			else if($head_count == 6)
			{
				$sm_class = 'sm-head-6';
			}
			else if($head_count == 7)
			{
				$sm_class = 'sm-head-7';
			}
			else if($head_count == 8)
			{
				$sm_class = 'sm-head-8';
			}
			else if($head_count == 9)
			{
				$sm_class = 'sm-head-9';
			}
			else
			{
				$sm_class = 'sm-head-10';
			}



			echo '<div class="aw_sub_featured">';
			if( $sticky_url ){
		      echo  '<a href = "'.$sticky_permalink.'" rel = "bookmark" class=""><img src = "'.$sticky_url.'" alt="" /></a>';	      
			}
			 echo  '<h2 class="aw_entry_title '.$sm_class.'"><a href = "'.$sticky_permalink.'">'.$sticky_title.'</a></h2>';
			echo '</div>';
		}	 
		}


	echo '<div class="aw_sub_featured">';
		if( $url ){
	      echo  '<a href = "'.$link.'" rel = "bookmark" class=""><img src = "'.$url.'" alt="" /></a>';	      
		}
		 echo  '<h2 class="aw_entry_title '.$sm_class.'"><a href = "'.$link.'">'.$title.'</a></h2>';
	echo '</div>';
	