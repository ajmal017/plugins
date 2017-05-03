<?php
require('/home/mimo/wp-load.php');

$args 		= array(	'post_type' => 'multimedia-index',
						'posts_per_page' =>  -1,
					);

query_posts( $args );

if ( have_posts() ) : 				
	
	while ( have_posts() ) : the_post(); 
		
		// $tempArray['post_link'] 	= get_permalink();
		$tempArray['post_title'] 	= get_the_title();
		$tempArray['record_number'] = get_post_meta( get_the_ID(), '_record_number', true );
		$tempArray['teacher_id'] 	= get_post_meta( get_the_ID(), '_teacher_id', true );
		// $tempArray['summary'] 		= get_post_meta( get_the_ID(), '_summary', true );
		
		$tempArray['date'] 			= get_post_meta( get_the_ID(), '_date', true );
		/*$tempArray['record_type'] 	= get_post_meta( get_the_ID(), '_record_type', true );
		$tempArray['section'] 		= get_post_meta( get_the_ID(), '_section', true );*/

		if( empty( $tempArray['date'] ) ){
			//** Add Record to Post Array. **//
			$postArray[] = $tempArray;
		} 

	endwhile;									
endif;

wp_reset_query();

 
// echo "<pre>";
// print_r( $postArray );
// echo "</pre>";


ob_get_clean ();

$filename = "mmi_entries_list.csv";

header('Content-Type: application/csv');
header('Content-Disposition: attachement; filename="'.$filename.'"');

echo "Record Number, Title, Teacher\n"; 

foreach ( $postArray as $MMI_post ) {
	$title = str_replace(',', ';', $MMI_post['post_title'] );

	echo $MMI_post['record_number'].",".$title.",".$MMI_post['teacher_id']."\n"; 	
}

exit();


?>
