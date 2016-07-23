<?php 
include '../../../wp-load.php';
require_once('ImageManipulator.php'); 

if ($_FILES['uploadfile']['error'] > 0) {
    echo "Error: " . $_FILES['uploadfile']['error'] . "<br />";
} else {
	  if(strpos($_FILES['uploadfile']['name'],"%")==true || strpos($_FILES['uploadfile']['name'],"#")==true){
		echo "Error--Inavalid image name<br />";
	  }else{
		// array of valid extensions
		$validExtensions = array('.jpg', '.jpeg', '.gif', '.png');
		// get extension of the uploaded file
		$fileExtension = strrchr($_FILES['uploadfile']['name'], ".");
		// check if file Extension is on the list of allowed ones	
		//console.log($fileExtension);
		
		if (in_array($fileExtension, $validExtensions)) {
			$path = explode("plugins",getcwd());
			$newNamePrefix = time() . '_';
			list($width, $height, $type, $attr) = getimagesize($_FILES['uploadfile']['tmp_name']);
			$manipulator = new ImageManipulator($_FILES['uploadfile']['tmp_name']);
			// resizing 
			if($height>291){
				$height_resize = 291;
			}else{
				$height_resize = $height;
			}
			if($width>485){
				$width_resize = 485;
			}else{
				$width_resize = $width;
			}
			$newImage = $manipulator->resample($width_resize,$height_resize);			 
			// saving file to uploads folder
			//$uploaddir = './uploads/';
			// $blog_id = $GLOBALS['current_blog']->blog_id;
			 //$uploaddir = $path[0]."blogs.dir/".$blog_id."/files/";
			  $uploaddir = $path[0]."uploads/";
			 
			//$manipulator->save($uploaddir. $newNamePrefix . $_FILES['uploadfile']['name']);
			$manipulator->save($uploaddir.$newNamePrefix.$_FILES['uploadfile']['name']);
			//echo 'Done ...';
			echo "success".'--'.$newNamePrefix.'--'.$_FILES['uploadfile']['type']; 
		} else {
		   // echo 'You must upload an image...';
			echo "error";
		}
	}
}

 
/*if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) { 
  echo "success".'--'.$_FILES['uploadfile']['type']; 
} else {
	echo "error";
}*/
?>