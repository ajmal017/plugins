<?php 

//* Link Post Format

	$post_id   	= 	get_the_ID();
	$title 		= 	get_the_title( );
	$permalink 	= 	get_permalink( );
	$content 	=  	get_the_content( ); 
	$title 		=  	get_the_title( );
	
	$pos 	= strpos( $content, '<!--more-->'); 
	$pos1 	= strpos( $content,'#more-');
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



	if (  $pos || $pos1  ) {			
		$link = get_the_permalink();
	}else{		
		$link = get_post_meta(  $post_id , 'syndication_permalink', true );
	}
	
	echo "<h2 class='aw_entry_title ".$sm_class."'>";
		echo '<a href="'.$link.'" title="'.$title.'">'.$title.'</a>';		
	echo "</h2>";
