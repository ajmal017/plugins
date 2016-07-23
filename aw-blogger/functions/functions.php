<?php
	function recursive_move($dirsource, $dirdest){

		// recursive function to copy
		// all subdirectories and contents:
		if(is_dir($dirsource)){
			$dir_handle=opendir($dirsource);
			//echo "directory handle". $dir_handle. "<br/>";
		}
			$dirname = substr($dirsource,strrpos($dirsource,"/")+1);
			//echo "dirname is :".$dirname;
			mkdir($dirdest."/".$dirname, 0777);
		while($file=readdir($dir_handle))
		{
			//echo "working2";
			if($file!="." && $file!="..")
			{
				if(!is_dir($dirsource."/".$file))
				{
					//echo "FILE will move to::::::::".$dirsource."/".$file."::::::: to :::::   ".$dirdest."/".$dirname."/".$file."<br/>";
					rename ($dirsource."/".$file, $dirdest."/".$dirname."/".$file);
					unlink($dirsource."/".$file);
				}else
				{
					$dirdest1 = $dirdest."/".$dirname;
					recursive_move($dirsource."/".$file, $dirdest1);
				}
			}
		}
		closedir($dir_handle);
		rmdir($dirsource);
	}
	
?>