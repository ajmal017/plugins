<?php	
	$post_ID 	=  get_the_ID();
	$content 	=  get_the_content(); 
	$title 		=  get_the_title();
	$permalink 	=  get_permalink();	
	
	$pos 	= strpos( $content  , '<!--more-->'); 
	$link 	= '';


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


	if (  $pos  ) {			
		$link = get_the_permalink();
	}else{		
		$link = get_post_meta( $post_ID  , 'syndication_permalink', true );
	}

	$url = wp_get_attachment_url( get_post_thumbnail_id( $post_ID ) );

	if( stripos($url, '/var/www/html/') !== true ){
		$url = str_replace("/var/www/html/","http://iris.scanmine.com/", $url);
	}
	
	echo '<div class="aw_featured">';
		if( $url ){
	      echo  '<a href = "'.$link.'" rel = "bookmark" class=""><img src = "'.$url.'" style="width: 100%;" alt="" /></a>';	      
		}
		 echo  '<h2 class="aw_entry_title '.$sm_class.'"><a href = "'.$link.'">'.$title.'</a></h2>';
	echo '</div>';
	