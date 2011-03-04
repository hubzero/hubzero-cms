<?php
/**
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class Controller
{
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $_error = NULL;

	public function __construct($config=array())
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';

		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower($r[1]);
			}
		}
		$this->_option = 'com_'.$this->_name;
	}

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	public function execute()
	{
		$task = strtolower(JRequest::getVar('task'));

		if (!isset($task) || $task == '') {
			$task = 'view';
		}

		switch($task) {
			case 'data':
				$this->data();
				break;

			case 'view':
				$u =& JFactory::getURI();
				$path = explode('/', $u->_path);
				$view = (isset($path[2]) && $path[2] != '')? $path[2]: 'spreadsheet';	// Setting default view as 'spreadsheet'
				$name = (isset($path[3]) && $path[3] != '')? $path[3]: 'spdscc';	// Setting default spreadsheet name as 'spdscc'
				$this->view($view, $name);
				break;

			case 'file':
				$hash = JRequest::getVar('hash');
				stream_file($hash);
				break;

			case 'zip':
				$hash_list = JRequest::getVar('hash_list');
				zip_files($hash_list);
				break;

			default:
				$this->start();
		}
	}

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$mainframe =& $this->mainframe;
			$mainframe->redirect($this->_redirect, $this->_message, $this->_messageType);
		}
	}

	protected function start() {
		// Redirect to databases page
		$url = "/databases";
		header('Location: ' . $url);
	}

	public function data()
	{
		if (!$this->_authorize()) {
			print ('<strong>Not Authorized</strong>');
			return;
		}

		global $dv_conf;

		$name = $_GET['obj'];
		
		$dd = false;
		$dd_json_file = (isset($dv_conf['dd_json']) && file_exists($dv_conf['dd_json'] . '/' . $name . '.json'))? $dv_conf['dd_json'] . '/' . $name . '.json': false;
		$dd_php_file = (file_exists(JPATH_COMPONENT.DS."data".DS."$name.php"))? JPATH_COMPONENT.DS."data".DS."$name.php": false;

		if ($dd_json_file) {
			$dd = json_decode(file_get_contents($dd_json_file), true);
		} elseif ($dd_php_file) {
			require_once ($dd_php_file);
			$dd_func = 'get_' . $name;
			if (function_exists($dd_func)) {
				$dd = $dd_func();
			}
		}

		$filter = strtolower(JRequest::getVar('format', 'json'));
		require_once(JPATH_COMPONENT.DS."filter/$filter.php");

		if ($dd) {
			$link = get_db();
			$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

			if ($id) {
				$dd['where'][] = array('field'=>$dd['pk'], 'value'=>$id);
				$dd['single'] = true;
			}
	
			$sql = query_gen($dd);

			$res = get_results($sql, $dd);
	
			print filter($res);
			exit(0);
		} else {
			print "<h3>Error: Not Implemented</h3>";
			exit(1);
		}
	}

	public function view($view, $name)
	{
		if (!$this->_authorize()) {
			print ('Not Authorized');
			return;
		}

		$filter = strtolower(JRequest::getVar( 'format', 'json' ));
		$file = (JPATH_COMPONENT.DS."filter/$filter.php");
		if (file_exists($file)) {
			require_once ($file);
		}

		$file = (JPATH_COMPONENT.DS."view".DS."$view.php");
		if (file_exists($file)) {
			require_once ($file);
			view($name);
		}
	}

	private function _authorize()
	{
		/*** Public ***/
		return true;

		$juser =& JFactory::getUser();

		if ($juser->get('guest')) {
			return false;
		} else {
			return true;
		}
	}
}
?>
