<?php
class LaunchAuthor {

	public function go($list) {

		$dir_path_base = "/apps/projects/workingdir/";
		
		//Create user directory
		$juser =& JFactory::getUser();

		$dir_path = $dir_path_base . $juser->get('username');
		
		if(!is_dir($dir_path))
		{
			//$umask = umask(0007);
			mkdir($dir_path) or die ('Error in file system access');
			chmod($dir_path, 0777);
			//umask($umask);
		}
		
		
		$fp = fopen($dir_path . "/file", 'w') or die("Can't open rerun_files");
		fwrite($fp, $list);
		fclose($fp);
		chmod($dir_path . "/file", 0777);
		
		$ver = LaunchAuthor::get_latest_version();
		$url = "http://neeshub.org/tools/indeed/invoke/$ver";
		header("Location: $url");
		
		
	}

	public static function get_latest_version($tool = 'indeed') {
		$app_path = '/apps/';
		$latest_path = readlink($app_path . $tool . '/current');
		return ltrim(array_pop(explode('/', $latest_path)), 'r');
	}
	
}
?>
