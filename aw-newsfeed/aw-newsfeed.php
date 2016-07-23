<?php
/*
Plugin Name: AW-NewsFeed
Plugin URI: http://iris.scanmine.com/
Description: This plugin allows to include instances of NewsFeed in your blogs using Widget.
Version: 1.0
Author: Developer-G0947
Author URI: http://www.artworldwebsolutions.com/
License:
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AW_NewsFeed extends WP_Widget{

	//** Consntructor **//
	function __construct() {
		
		// add_filter('widget_text', 'do_shortcode');
		// add_shortcode( 'newsfeed',array(&$this, 'newsFeed') );

		parent::__construct(
		// Base ID of your widget
		'aw_widget',

		// Widget name will appear in UI
		__('AW News Feed', 'aw_widget_domain'), 

		// Widget description
		array( 'description' => __( 'Widget to Add New Feed Block', 'aw_widget_domain' ), ) );

		//** Action to register Widget. **//
		add_action( 'widgets_init', array(&$this, 'aw_load_widget') );

		//** Action to load Assets Css **//
		add_action( 'wp_enqueue_scripts',  array(&$this, 'loadAssectCss') );
		
	}

	//**  function to laod css **//
	function loadAssectCss(){
		$plugin_url = plugin_dir_url( __FILE__ );

	    //** Load  Styling. **//
	    wp_enqueue_style( 'aw_newsfeed_style', $plugin_url . 'css/aw_newsfeed_style.css' );

	}


	function newsFeed( $attr  ){
		
		$aw_category_name = get_query_var('cat');
		if( is_numeric($aw_category_name) ){
			$aw_category_name = get_the_category_by_ID( get_query_var('cat') );
		}else{
			$aw_category_name = get_query_var('cat');
		}

		if( $aw_category_name == '' ){
			//** get the first Category. **//		
			$category 			= get_the_category(); 
			$aw_category_name 	= $category[0]->name;
		}	

		if( strpos($attr['block'], '/') == true ){
			$sm_bock 	= 	$attr['block'];			
			file_put_contents(dirname(__FILE__)."/sm_bock.log", print_r("\n", true),FILE_APPEND);
			file_put_contents(dirname(__FILE__)."/sm_bock.log", print_r( "In if condatio \n",true),FILE_APPEND);
			file_put_contents(dirname(__FILE__)."/sm_bock.log", print_r($sm_bock,true),FILE_APPEND);
			file_put_contents(dirname(__FILE__)."/sm_bock.log", print_r("\n", true),FILE_APPEND);
		}else{
			$sm_bock 	= 	$aw_category_name.'/'.$attr['block'];
			file_put_contents(dirname(__FILE__)."/sm_bock.log", print_r("\n", true),FILE_APPEND);
			file_put_contents(dirname(__FILE__)."/sm_bock.log", print_r( "In else condatio \n",true),FILE_APPEND);
			file_put_contents(dirname(__FILE__)."/sm_bock.log", print_r($sm_bock,true),FILE_APPEND);
			file_put_contents(dirname(__FILE__)."/sm_bock.log", print_r("\n", true),FILE_APPEND);
		}	
		
		$blockType 	= 	$attr['blockType'];
		$NoOfPosts 	= 	$attr['noofposts'];

			
		$args = 	array(						
				'posts_per_page'   	=> $NoOfPosts,
				'meta_key' 		   	=> 'sm:block', 
				'meta_value' 		=> $sm_bock,
				'meta_compare'		=> 'LIKE',
				'ignore_sticky_posts' => 1,
		);
		
		

		//** argument list for sticky post **//
		$blockName 	= $attr['block'];
		$sticky 	= get_option( 'sticky_posts' );

		$argsSticky = array(			
			'post__in'  => $sticky,
			'orderby' => 'date',
			'order' => 'DESC',
			'ignore_sticky_posts' => 1,
		);

		if( $blockType == 'featured' ){
			$templateLayout = 'includes/content-featured.php';
		}else if( $blockType == 'sub_featured' ){
			$templateLayout = 'includes/content-sub-featured.php';
		}else if( $blockType == 'standard' ){
			$templateLayout = 'includes/content-standard.php';
		}else if( $blockType == 'link' ){
			$templateLayout = 	'includes/content-link.php';
		}else if( $blockType == 'treaser' ){
			$templateLayout = 	'includes/content-featured-treaser.php';
		}else{
			$templateLayout = 	'';
		}



		
		echo '<div class="aw_article_content">';
			
			//** sticky post block **//
			if( !empty($sticky[0] ) ):
				query_posts( $argsSticky );				
				echo '<div class="aw_sticky_post">';
					if ( have_posts() ) : while ( have_posts() ) : the_post();
							printf( '<article class="%s">', $blockType  );							
							$block = get_post_meta( get_the_ID(), 'block', true );							
							if( $block == $blockName ):
								include($templateLayout);
							endif;
							echo '<div class="clear"></div>';
							echo '</article>';
						endwhile; 					
					endif; 	
				echo '</div>';
				wp_reset_query();
			endif;
			//** sticky post block ends **//

			query_posts( $args );		
			if ( have_posts() ) : while ( have_posts() ) : the_post();

					printf( '<article class="%s">', $blockType  );

					include($templateLayout);
					echo '<div class="clear"></div>';
					echo '</article>';
				endwhile; 					
			endif; 				
			//* Restore original query
			wp_reset_query();
		echo "</div>";
	}


	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {

		apply_filters( 'widget_title', $instance['blockID'] );
		
		// before and after widget arguments are defined by themes
		// echo $args['before_widget'];
		// if ( ! empty( $title ) )
		// 	echo $args['before_title'] . $title . $args['after_title'];



		
		$blockID 			= 	$instance['blockID'];
		$blockType 			= 	$instance['blockType'];
		$noofposts 			= 	$instance['noofposts'];
		
		$args['block'] 		=	$blockID; 
		$args['blockType'] 	=	$blockType;
		$args['noofposts'] 	=	$noofposts;




		$this->newsFeed($args);


	}

		
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'blockID' ] ) ) {
			$blockID = $instance[ 'blockID' ];
		}else{
			$blockID = __( 'featured1', 'aw_widget_domain' );
		}

		if ( isset( $instance[ 'blockType' ] ) ) {
			$blockType = $instance[ 'blockType' ];
		}else{
			$blockType = __( 'standard', 'aw_widget_domain' );
		}

		if ( isset( $instance[ 'noofposts' ] ) ) {
			$noofposts = $instance[ 'noofposts' ];
		}else{
			$noofposts = __( '1', 'aw_widget_domain' );
		}

		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'blockID' ); ?>"><?php _e( 'BLock ID:' ); ?></label> 		
			<input class="widefat" id="<?php echo $this->get_field_id( 'blockID' ); ?>" name="<?php echo $this->get_field_name( 'blockID' ); ?>" type="text" value="<?php echo esc_attr( $blockID ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'blockType' ); ?>"><?php _e( 'Block type:' ); ?></label> 					
			<select class="widefat" id="<?php echo $this->get_field_id( 'blockType' ); ?>" name="<?php echo $this->get_field_name( 'blockType' );?> " >
				<?php if(  $blockType == 'featured' ): ?>
					<option value="featured"  selected>Featured</option>
				<?php else: ?>
					<option value="featured">Featured</option>					
				<?php endif; ?>

				<?php if(  $blockType == 'treaser' ): ?>
					<option value="treaser"  selected>Treaser</option>
				<?php else: ?>
					<option value="treaser">Treaser</option>					
				<?php endif; ?>

				<?php if(  $blockType == 'sub_featured' ): ?>
					<option value="sub_featured"  selected>Sub Featured</option>
				<?php else: ?>
					<option value="sub_featured">Sub Featured</option>					
				<?php endif; ?>
				

				<?php if(  $blockType == 'standard' ): ?>
					<option value="standard" selected>Standard</option>
				<?php else: ?>
					<option value="standard">Standard</option>					
				<?php endif; ?>

				<?php if(  $blockType == 'link' ): ?>
					<option value="link"  selected>Link</option>
				<?php else: ?>
					<option value="link">Link</option>					
				<?php endif; ?>
						
			</select>	
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'noofposts' ); ?>"><?php _e( 'Number Of Posts:' ); ?></label> 		
			<input class="widefat" id="<?php echo $this->get_field_id( 'noofposts' ); ?>" name="<?php echo $this->get_field_name( 'noofposts' ); ?>" type="number" min="1" max="100" required value="<?php echo esc_attr( $noofposts ); ?>" />
			
		</p>
		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['blockID'] 		= ( ! empty( $new_instance['blockID'] ) ) ? strip_tags( $new_instance['blockID'] ) : '';
		$instance['blockType']		= ( ! empty( $new_instance['blockType'] ) ) ? strip_tags( $new_instance['blockType'] ) : '';
		$instance['noofposts'] 		= ( ! empty( $new_instance['noofposts'] ) ) ? strip_tags( $new_instance['noofposts'] ) : '';

		return $instance;
	}

	// Register and load the widget
	function aw_load_widget() {
		register_widget( 'AW_NewsFeed' );
	}

}//** Class ends here. **//



$AW_NewsFeed = new AW_NewsFeed;

?>