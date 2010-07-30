<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
//Hack alert: This is to remove the empty space that is printed if we dont' use this line
//It causes a lot of trouble when getting the xml rendered out
class Controller
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $_error = NULL;
	
	//-----------
	
	public function __construct( $config=array() )
	{
		
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
		$this->_option = 'com_'.$this->_name;
		
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	
	//-----------
	
	public function execute()
	{
		// There are two ways of executing a task that we tend to use.
		// Method 1: switch case
		//-----------
		/*
		// Retrieve the task from the query string
		$task = strtolower(JRequest::getVar( 'task', $default ));
		
		switch($task)
		{
			case 'default': $this->default(); break;

			default: $this->default(); break;
		}
		*/
		
		// Method 2:
		//-----------
		
		$default = 'start';
		
		// Retrieve the task from the query string
		$task = strtolower(JRequest::getVar( 'task', $default ));
		
		// Get all the methods in this class
		$thisMethods = get_class_methods( get_class( $this ) );
		
		// Is the task an available method?
		if (!in_array($task, $thisMethods)) {
			// No! Use the default task
			$task = $default;
			// Is the default task an available method?
			if (!in_array($task, $thisMethods)) {
				// No! Return an error
				return JError::raiseError( 404, JText::_('Task ['.$task.'] not found') );
			}
		}
		
		// Set the task in our registry in case we need it later
		$this->_task = $task;
		
		// Execute the task
		$this->$task();
	}
	
	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$mainframe =& $this->mainframe;
			$mainframe->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
	
	
	public function process() {
		$list = $_GET["list"];
		LaunchAuthor::go($list);
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function start() 
	{
		// Get the database object
		$database =& JFactory::getDBO();
		
		// Your code here
		if (!$this->_authorize()) {
			echo LaunchAuthorHtml::error('Not Authorized');
			return;
		}
		
		LaunchAuthorHtml::start($_GET);
	
	}
	
	

	private function _authorize()
	{
//		// Check if they are logged in
//		$juser =& JFactory::getUser();
//		if ($juser->get('guest')) {
//			return false;
//		}
//		
//		// Check if they're a site admin (from Joomla)
//		if ($juser->authorize($this->_option, 'manage')) {
//			return true;
//		}
//
//		// Check if they're an allowed login
//		$allowed = array('baaraa','ruchith', 'lingyan','nabeel', 'dcregar', 'matburns', 'sampleacquisition', 'purduestatmodel', 'oxidatvestress', 'globalproteomics', 'proteomics', 'metabolomics');
//		if (in_array($juser->get('username'),$allowed)) {
//			return true;
//		}
//		
//		ximport('xgroup');
//		ximport('xuserhelper');
//		
//		// Check if they're a member of an allowed group
//		$groups = array('');
//		if (count($groups)) {
//			$ugs = XUserHelper::getGroups( $juser->get('id') );
//			if ($ugs && count($ugs) > 0) {
//				foreach ($ugs as $ug) 
//				{
//					if (in_array($ug->cn, $groups)) {
//						return true;
//					}
//				}
//			}
//		}
//		
//		$xuser =& XFactory::getUser();
//		if (is_object($xuser)) {
//			// Check if they're a site admin (from LDAP)
//			$mainframe =& $this->mainframe;
//			if (in_array(strtolower($mainframe->getCfg('sitename')), $xuser->get('admin'))) {
//				return true;
//			}
//		}
//
//		return false;
		return true;
	}
}
?>