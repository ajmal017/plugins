<?php
/*
Plugin Name: AW-AddFeed
Plugin URI: http://iris.scanmine.com/
Description: This plugin allows to include Adds in your blogs using Widget.
Version: 1.0.0
Author: Developer-G0947
Author URI:
License:
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('DEFAULT_PATH', '/var/www/html/go/pub/external/widgets');

class AW_AddFeed extends WP_Widget{

	//** Consntructor **//
	function __construct() {

		parent::__construct(
		// Base ID of your widget
		'aw_add_widget',

		// Widget name will appear in UI
		__('AW Add Feed', 'aw_add_widget_domain'),

		// Widget description
		array( 'description' => __( 'Widget for includeing Add Feed Files', 'aw_add_widget_domain' ), ) );

		//** Action to register Widget. **//
		add_action( 'widgets_init', array(&$this, 'aw_add_load_widget') );

	}


	function AddFeed( $attr  ){

		$structure    = trim( get_option('topic', true) );
		$AddFilePath  = trim($attr['AddFilePath']);
		$contentFound = false;

		$structureFileterd = $this->filterPath($structure, $AddFilePath);

		echo '<div class="aw_add_blog">';
			foreach( $structureFileterd as $currentPath ) {
				if ( file_exists($currentPath) ){
					$contentFound = true;
					include( $currentPath );
				}else{

					$alternatepath = substr_replace($currentPath,"-",-4,0);
					if ( file_exists($alternatepath) ){
						if( !$contentFound ){
							$contentFound  = true;
							include( $alternatepath );
						}
					}
				}
			}
		echo "</div>"; /*Add div ends here. */
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {

		apply_filters( 'widget_title', $instance['AddFilePath'] );
		// before and after widget arguments are defined by themes
		$AddFilePath 			= 	$instance['AddFilePath'];
		$args['AddFilePath'] 	=	$AddFilePath;

		$this->addFeed($args);
	}


	// Widget Backend
	public function form( $instance ) {
		// Widget admin form
		$AddFilePath = $instance['AddFilePath'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'AddFilePath' ); ?>"><?php _e( 'Add File Path:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'AddFilePath' ); ?>" name="<?php echo $this->get_field_name( 'AddFilePath' ); ?>" type="text" value="<?php echo esc_attr( $AddFilePath ); ?>" />
		</p>
		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['AddFilePath'] = ( ! empty( $new_instance['AddFilePath'] ) ) ? strip_tags( $new_instance['AddFilePath'] ) : '';

		return $instance;
	}

	// Register and load the widget
	function aw_add_load_widget() {
		register_widget( 'AW_AddFeed' );
	}

	function filterPath( $topic, $filename ){

		$outputData          = array();
		$segmentPart         = explode('/',$topic );
		$segmentPartFiltered = array_filter($segmentPart);
		$count               = count($segmentPartFiltered);

		foreach ($segmentPartFiltered as $items ) {
			$current = implode('/', $segmentPartFiltered);
			unset( $segmentPartFiltered[$count] );
			$count--;
			$outputData[] = DEFAULT_PATH."/".$current."/".$filename;
		}

		return $outputData;
	}
}//** Class ends here. **//

$AW_AddFeed = new AW_AddFeed;

?>