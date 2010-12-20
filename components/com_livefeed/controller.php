<?php

defined( '_JEXEC' ) or die( 'Restricted access' );



class LivefeedController extends JObject
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
		//--------
		$default = 'default';
		// Retrieve the task from the query string
		$task = strtolower(JRequest::getVar( 'task', $default ));
		
		switch($task)
		{
			//almalgamation
			case 'default': $this->main(); break;
			
			//Individual views
			case 'feed': $this->feed(); break;
			case 'chat': $this->chat(); break;

			default: $this->feed(); break;
		}
		
		
	}
	
	//-----------
	public function main()
	{
		//model and controller for default view
		$view = new JView( array('name'=>'feed','layout'=>'default')  );
		$view->option = $this->_option;
		$view->config = $this->config;
		$view->title = $this->_title;
		
		$juser =& JFactory::getUser();
		$view->user = $juser;
		//$database =& JFactory::getDBO();
		
		// Output HTML		
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
		
	}
	
	//-----------
	public function feed()
	{
		//model and controller for default view
		$view = new JView( array('name'=>'feed','layout'=>'justintv')  );
		$view->option = $this->_option;
		$view->config = $this->config;
		$view->title = $this->_title;
		
		$juser =& JFactory::getUser();
		$view->user = $juser;
		//$database =& JFactory::getDBO();
		
		// Output HTML		
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
		
	}
	
	//-----------
	public function chat()
	{
		//model and controller for default view
		$view = new JView( array('name'=>'feed','layout'=>'chat')  );
		$view->option = $this->_option;
		$view->config = $this->config;
		$view->title = $this->_title;
		$view->area = array();
		$view->area['name'] = JRequest::getVar( 'area', 'Lobby' );
		$view->area['users'] =  JRequest::getInt( 'users', '200' );
		
		$juser =& JFactory::getUser();
		$view->user = $juser;
		//$database =& JFactory::getDBO();
		
		// Output HTML		
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
		
	}
	
	//-----------
	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$mainframe =& $this->mainframe;
			$mainframe->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
}
?>