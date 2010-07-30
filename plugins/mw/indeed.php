<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_mw_indeed' );

//-----------

class plgMwIndeed extends JPlugin
{
	public function plgMwIndeed(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'mw', 'indeed' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	//-----------

	public function onBeforeSessionInvoke($toolname, $toolversion) 
	{
		if (strtolower(trim($toolname)) != 'indeed') {
			return;
		}
		
		$list = JRequest::getVar('list', '', 'get');
		
		$dir_path_base = $this->_params->get('base_path');
		$dir_path_base = ($dir_path_base) ? $dir_path_base : "/apps/projects/workingdir/";

		//Create user directory
		$juser =& JFactory::getUser();

		$dir_path = $dir_path_base . $juser->get('username');
		
		if (!is_dir($dir_path)) {
			//$umask = umask(0007);
			mkdir($dir_path) or die('Error in file system access');
			chmod($dir_path, 0777);
			//umask($umask);
		}
		
		
		$fp = fopen($dir_path . "/file", 'w') or die("Can't open rerun_files");
		fwrite($fp, $list);
		fclose($fp);
		chmod($dir_path . "/file", 0777);
	}
	
	//-----------
	
	public function onAfterSessionInvoke($toolname, $toolversion) 
	{
		
	}
	
	//-----------
	
	public function onBeforeSessionStart($toolname, $toolversion) 
	{
		
	}
	
	//-----------
	
	public function onAfterSessionStart($toolname, $toolversion) 
	{
		
	}
}
