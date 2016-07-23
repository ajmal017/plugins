<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('memory_limit','1024M');

require '/var/www/html/wp-content/plugins/aw-blogger/class/utilitiesFunctions.php';
if(!class_exists('ConfigReader')){
  require_once '/var/www/html/wp-content/plugins/aw-blogger/configReader/ConfigReader.php';
}

if(isset($_POST['url'])){
     
    $blogName = utilitiesFunctions::getBlogNameFormConfig( $_POST['url'] ); 

    if( $blogName ){ 
      $delStatus = utilitiesFunctions::deleteSite( $blogName );
      
      if( $delStatus ){
        echo "DELETED Successfully $blogName <br>";
      }
    }else{
      echo "<br/> Error in Config:  $site<br/>";
    }
}

?>