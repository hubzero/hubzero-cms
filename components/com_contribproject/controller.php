<?php

ximport('Hubzero_Group');

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
	
	
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------
	protected function start() 
	{
		// Get the database object
		$database =& JFactory::getDBO();
		
		// Your code here
		if (!$this->_authorize()) {
			echo ContribProjectHtml::error('Not Authorized');
			return;
		}
		
		ContribProjectHtml::start($_GET);
	
	}
	
	
	public function dosubmit() {
		
		error_reporting(0);
		
		$projName = $_POST["projName"];
		$projTitle = $_POST["projTitle"];
		$projUsers = $_POST["projUsers"];
		$projDesc = $_POST["projGlance"];
		
		
		$database =& JFactory::getDBO();
		
//		


		// create a group in the group table with the listed researcher logins
        	// use groupname= np-shortname that has all logins
		$users = array_map('trim', explode(",", $projUsers));
		$hzg = Hubzero_Group::createInstance("np-" . $projName);		
		$hzg->set('members', $users);
		
		$hzg->add('tracperm','WIKI_ADMIN');
        $hzg->add('tracperm','MILESTONE_ADMIN');
        $hzg->add('tracperm','BROWSER_VIEW');
        $hzg->add('tracperm','LOG_VIEW');
        $hzg->add('tracperm','FILE_VIEW');
        $hzg->add('tracperm','CHANGESET_VIEW');
        $hzg->add('tracperm','ROADMAP_VIEW');
        $hzg->add('tracperm','TIMELINE_VIEW');
        $hzg->add('tracperm','SEARCH_VIEW');
		$hzg->update();
		$groupId = $hzg->gidNumber;

		
		
        // create a new entry in the jos_neesproject table		
		// id, shortname
		$database->setQuery("INSERT INTO jos_neesproject (name) VALUES ('" . $projName . "')");
		$database->query();
		$database->setQuery("select * from jos_neesproject where name = '" . $projName . "'");
		$res = $database->loadObjectList();
		$projId = $res[0]->id;

        // create a new entry in the jos_neesproject_group table
		// id, neeproject table entry id, group table entry id

		$database->setQuery("INSERT INTO jos_neesproject_group (neesproject_id, group_id) VALUES ('" . $projId . "','" . $groupId . "')" );
		$database->query();
		
		$database->setQuery("select * from jos_neesproject_group");
		$res = $database->loadObjectList();
		
		
		
		
		
		$xhub =& XFactory::getHub();
		$pw = $xhub->getCfg('hubLDAPSearchUserPW');
		
		
        $command = "/opt/addrepo/addrepo " . $projName .' -title "' . $projTitle.'" -description "'. $projDesc .'" -password "'.$pw.'"' . " -hubdir " . JPATH_ROOT . " -type neesprojects";
		exec($command.' 2>&1 </dev/null', $rawoutput, $status);
		
		//call script to create the repo
		// same call as contribtool but with -type neesprojects




		
		ContribProjectHtml::projCreated($command);
		
		
	}
	
	
	
	public function display() {
		if (!$this->_authorize()) {
			echo DviewerHtml::error('Not Authorized');
			return;
		}
		header("Content-Type: text/xml");
		$output = Service::process($_GET);
		$output = str_replace("<?xml version=\"1.0\"?>", "", $output);
		print $output;
		die ();
	}
	

	private function _authorize()
	{
		return true;
	}
}
?>
