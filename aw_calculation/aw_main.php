<?php
set_time_limit(0);
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class AWCalculations {

    //** Constructor **//
    function __construct() {
        add_action("wp_ajax_aw_main_section", array( 'AwAjax','getMainSection'));
        add_action("wp_ajax_nopriv_aw_main_section", array( 'AwAjax','getMainSection'));

        add_action("wp_ajax_aw_inner_main_section", array( 'AwAjax','getInnerMainSection'));
        add_action("wp_ajax_nopriv_aw_inner_main_section", array( 'AwAjax','getInnerMainSection'));

        add_action("wp_ajax_aw_area_select", array( 'AwAjax','getAreaSelect'));
        add_action("wp_ajax_nopriv_aw_area_select", array( 'AwAjax','getAreaSelect'));

        add_action("wp_ajax_aw_area_section", array( 'AwAjax','getAreaSection'));
        add_action("wp_ajax_nopriv_aw_area_section", array( 'AwAjax','getAreaSection'));

        add_action("wp_ajax_aw_floor_section", array( 'AwAjax','getFloorSection'));
        add_action("wp_ajax_nopriv_aw_floor_section", array( 'AwAjax','getFloorSection'));

        add_action("wp_ajax_aw_save_project", array( 'AwAjax','saveProject'));
        add_action("wp_ajax_nopriv_aw_save_project", array( 'AwAjax','saveProject'));


        add_action("wp_ajax_aw_brand_section", array( 'AwAjax','getBrandSection'));
        add_action("wp_ajax_nopriv_aw_brand_section", array( 'AwAjax','getBrandSection'));


        add_action("wp_ajax_aw_pattern_section", array( 'AwAjax','getPatternSection'));
        add_action("wp_ajax_nopriv_aw_pattern_section", array( 'AwAjax','getPatternSection'));
        
        add_action("wp_ajax_aw_transitions_section", array( 'AwAjax','getTransitionsSection'));
        add_action("wp_ajax_nopriv_aw_transitions_section", array( 'AwAjax','getTransitionsSection'));

        add_action("wp_ajax_aw_thinset_section", array( 'AwAjax','getThinsetSection'));
        add_action("wp_ajax_nopriv_aw_thinset_section", array( 'AwAjax','getThinsetSection'));

        add_action("wp_ajax_aw_project_section", array( 'AwAjax','getProjectSectionData'));
        add_action("wp_ajax_nopriv_aw_project_section", array( 'AwAjax','getProjectSectionData'));

        /*----------Save Location-----------*/

        add_action("wp_ajax_aw_save_location", array( 'AwAjax','saveLocation'));
        add_action("wp_ajax_nopriv_aw_save_location", array( 'AwAjax','saveLocation'));

        /*----------Get Locations-----------*/

        add_action("wp_ajax_aw_location_section", array( 'AwAjax','getLocations'));
        add_action("wp_ajax_nopriv_aw_location_section", array( 'AwAjax','getLocations'));


        /*----------Get Project Navigation-----------*/

        add_action("wp_ajax_aw_project_navigation", array( 'AwAjax','getProjectNavigation'));
        add_action("wp_ajax_nopriv_aw_project_navigation", array( 'AwAjax','getProjectNavigation'));

        /*----------Delete Project -----------*/

        add_action("wp_ajax_aw_delete_project", array( 'AwAjax','deleteProject'));
        add_action("wp_ajax_nopriv_aw_delete_project", array( 'AwAjax','deleteProject'));

        /*----------Save Project As Archive-----------*/

        add_action("wp_ajax_aw_save_archive_project", array( 'AwAjax','saveArchiveProject'));
        add_action("wp_ajax_nopriv_aw_save_archive_project", array( 'AwAjax','saveArchiveProject'));
        
    }

}/*class ends here*/
?>
