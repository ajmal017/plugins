<?php
/**
* AjaxSetting class
*/
class AjaxSetting{
	
	public static function dividendSetting(){
           
        if( isset($_POST['action']) ) {
            
            $data = $_POST['data'];
            update_option('divident_columns', $data);
            echo "dividendsettingsave";
            die();
        }
    }

   
}
/* class ends here */