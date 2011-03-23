<?php
defined('_JEXEC') or die( 'Restricted access' );

Class GroupModules extends JTable
{
	var $id = NULL;
	var $gid = NULL;
	var $type = NULL;
	var $content = NULL;
	var $morder = NULL;
	var $active = NULL;
	
	function __construct( &$db)
	{
		parent::__construct( '#__xgroups_modules', 'id', $db );
	}
	
	//-------
	
	public function getModules( $gid, $active = false ) 
	{
		if($active) {
			$sql = "SELECT * FROM $this->_tbl WHERE gid='".$gid."' AND active=1 ORDER BY morder ASC";	
		} else {
			$sql = "SELECT * FROM $this->_tbl WHERE gid='".$gid."' ORDER BY morder ASC";	
		}
		
		$this->_db->setQuery($sql);
		$modules = $this->_db->loadAssocList();
		
		return $modules;
	}
	
	//-----
	
	public function getHighestModuleOrder( $gid )
	{
		$sql = "SELECT morder from $this->_tbl WHERE gid='".$gid."' ORDER BY morder DESC LIMIT 1";
		$this->_db->setQuery($sql);
		$high = $this->_db->loadAssoc();
		
		return $high['morder'];
	}
	
	//-----
	
	function renderModules ( $group, $wiki_parser )
	{
		//array of modules
		$raw_modules = array();
		
		//all modules even deactivated modules
		$raw_modules_all = $this->getModules($group->get('gidNumber'), false);
		
		//get the modules for this group
		$raw_modules = $this->getModules($group->get('gidNumber'), true);
		
		//if there are no modules sent use default
		if(count($raw_modules_all) < 1) {
			$default_module = array();
			$default_module['type'] = 'information';
			$default_module['content'] = '';
			array_push($raw_modules, $default_module);
		}
		
		//base path to modules folder
		$path = JPATH_COMPONENT . DS . 'modules' . DS;

		//module content
		$return = '';
		
		
		//foreach of the group modules in the db render them
		foreach($raw_modules as $mod) {
			//check to make sure group module has php file to render it
			if(is_file($path . $mod['type'].'.php')) {
				//include the php file
				include_once($path . $mod['type'].'.php');
				
				//class name is module type + Macro (Ex. CustomModule)
				$class_name = ucfirst($mod['type'].'Module');
				
				//if class name exists then instantiate and push the module content to the file to render out final module
				if(class_exists($class_name)) {
					$module = new $class_name( $group );
					$module->content = $mod['content'];
					$output = $module->render();
				} else {
					$output = '';
				}
				
				//append out of render to final output
				$return .= $output;
			} else {
				$return .= "<div class=\"group_module_custom\"><small>".JTEXT::sprintf('GROUP_MODULE_NOT_INSTALLED', ucfirst($mod['type']))."</small></div>";
			}
		}
		
		//return rendered modules
		return $return;
	}
	
	
}
?>