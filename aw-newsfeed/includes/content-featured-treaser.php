<?php 
	
	$post_ID    = get_the_ID();
	$content    = get_the_content(); 
	$title      = get_the_title();
	$permalink  = get_permalink();	
	$pos        = strpos( $content, '<!--more-->'); 
	$pos1       = strpos( $content,'#more-');
	$link       = '';
	$head_len   = strlen($title);
	$head_co    = $head_len/10;
	$head_count = round($head_co);

	//* Set what goes inside the wrapping tags
	if($head_count == 1){
		$sm_class 		 = 'sm-head-1';
	}else if($head_count == 2){
		$sm_class 		 = 'sm-head-2';
	}else if($head_count == 3){
		$sm_class 		 = 'sm-head-3';
	}else if($head_count == 4){
		$sm_class 		 = 'sm-head-4';
	}else if($head_count == 5){
		$sm_class 		 = 'sm-head-5';
	}else if($head_count == 6){
		$sm_class 		 = 'sm-head-6';
	}else if($head_count == 7){
		$sm_class 		 = 'sm-head-7';
	}else if($head_count == 8){
		$sm_class 		 = 'sm-head-8';
	}else if($head_count == 9){
		$sm_class 		 = 'sm-head-9';
	}else{
		$sm_class 		 = 'sm-head-10';
	}



	if (  $pos || $pos1  ) {			
		$link  = get_the_permalink();
	}else{		
		$link  = get_post_meta(  $post_ID , 'syndication_permalink', true );
	}

	$url 	   = wp_get_attachment_url( get_post_thumbnail_id( $post_ID ) );

	if( stripos($url, '/var/www/html/') !== true ){
		$url   = str_replace("/var/www/html/","http://iris.scanmine.com/", $url);
	}	
	/*$url2 = get_site_url();
	echo "</br>".$url."</br>".$url2."</br>";
	$str = str_replace('/var/www/html/bilportalen', $url2, $url);
	echo $str;*/
	/*echo '<div class="aw_treaser">';
		echo '<div class="media_container">';
			if( $url ){
		       printf(  '<a href = "%s" rel = "bookmark" class=""><img class = "" src = "%s" alt="" /></a>', $link, $url );
			}
		echo '</div>';
		echo '<div class="post_content">';
			echo "<h2 class='aw_entry_title'>";
				echo '<a href="'.$link.'" title="'.$title.'">'.$title.'</a>';		
			echo "</h2>";
			printf( '<div class="%s">', 'aw_inner_content' );
				the_content();
			echo '</div>'; //* end .entry-content
		echo '</div>';
		echo '<div class="clear"></div>';
	echo '</div>';*/

	//** get the first menu item **//
	$top 				= wp_get_nav_menu_items('top-primary-menu');
	$link1 				= $top[0]->title;
	$aw_category_name 	= $link1;
	$sticky 			= get_option( 'sticky_posts' );
	rsort($sticky);
	$sticky 			= array_slice( $sticky, 0, 1 );
	$get_current_arr 	= get_the_category();
	$get_current_cat 	= $get_current_arr[0]->name;
		
		foreach ($sticky as  $stickies) {
			$block_value 			= get_post_meta($stickies , 'sm:block',true );

			if( is_sticky($stickies) && $block_value==$get_current_cat."/featured1"){	

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
				$head_len 			= strlen($sticky_title);
				$head_co 			= $head_len/10;
				$head_count 		= round($head_co);
				
				//* Set what goes inside the wrapping tags
				if($head_count == 1){
					$sm_class = 'sm-head-1';
				}else if($head_count == 2){
					$sm_class = 'sm-head-2';
				}else if($head_count == 3){
					$sm_class = 'sm-head-3';
				}else if($head_count == 4){
					$sm_class = 'sm-head-4';
				}else if($head_count == 5){
					$sm_class = 'sm-head-5';
				}else if($head_count == 6){
					$sm_class = 'sm-head-6';
				}else if($head_count == 7){
					$sm_class = 'sm-head-7';
				}else if($head_count == 8){
					$sm_class = 'sm-head-8';
				}else if($head_count == 9){
					$sm_class = 'sm-head-9';
				}else{
					$sm_class = 'sm-head-10';
				}

				echo '<div class="aw_treaser">';
				echo '<div class="post_content" style="width:100%; margin-bottom:6%;">';
					
					if( $sticky_url ){
				       printf(  '<a href = "%s" rel = "bookmark" class=""><img class = "img_set" src = "%s" alt="" /></a>', $sticky_permalink, $sticky_url );
					}
					echo "<h2 class='aw_entry_title ".$sm_class."'>";
						echo '<a href="'.$sticky_permalink.'" title="'.$sticky_title.'">'.$sticky_title.'</a>';		
					echo "</h2>";
					printf( '<div class="%s" style="font-size:1.2em;">', 'aw_inner_content' );
						echo $content;
					echo '</div>'; 
				echo '</div>';
				echo '<div class="clear"></div>';
				echo '</div>';
			}	 
		}
	
	//}
	
	echo '<div class="aw_treaser">';
		echo '<div class="post_content" style="width:100%;">';
			//echo '<div class="" style=" float: left; margin: 0 15px 0 0; max-width: 50%;">';
			if( $url ){
		       printf(  '<a href = "%s" rel = "bookmark" class=""><img class = "img_set" src = "%s" alt="" /></a>', $link, $url );
			}
			//echo '</div>';
			echo "<h2 class='aw_entry_title ".$sm_class."'>";
				echo '<a href="'.$link.'" title="'.$title.'">'.$title.'</a>';	
			echo "</h2>";
			printf( '<div class="%s">', 'aw_inner_content' );
				the_content();
			echo '</div>'; //* end .entry-content
		echo '</div>';
		echo '<div class="clear"></div>';
	echo '</div>';