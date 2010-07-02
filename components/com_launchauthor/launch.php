<?php
class LaunchAuthor {

	public function go($list) {

		$dir_path_base = "/apps/projects/workingdir/";
		
		//Create user directory
		$juser =& JFactory::getUser();

		$dir_path = $dir_path_base . $juser->get('username');
		
		if(!is_dir($dir_path))
		{
			$umask = umask(0007);
			mkdir($dir_path) or die ('Error in file system access');
			umask($umask);
		}
		
		
		$fp = fopen($dir_path . "/file", 'w') or die("Can't open rerun_files");
		fwrite($fp, $list);
		fclose($fp);
		
		header( 'Location: http://neeshub2.hubzero.org/tools/authortool/invoke/21' ) ;
		
		
	}
	
}
?>
