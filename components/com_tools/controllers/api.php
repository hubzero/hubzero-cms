<?php
JLoader::import('Hubzero.Api.Controller');

class ToolsApiController extends Hubzero_Api_Controller
{
	function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		switch($this->segments[0]) 
		{
			case 'storage':		$this->storage();				break;
			case 'purge':		$this->purge();					break;
			
			case 'info':		$this->info();					break;
			case 'list':		$this->listTools();				break;
			case 'screenshot':	$this->sessionScreenshot();		break;
			case 'invoke':		$this->invoke();				break;
			case 'view':		$this->view();					break;
			case 'stop':		$this->stop();					break;
			
			default:			$this->listTools();
		}
	}
	
	/**
	 * Short description for 'not_found'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	private function not_found()
	{
		$response = $this->getResponse();
	
		$response->setErrorMessage(404,'Not Found');
	}
	
	private function storage()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);
		
		//get request vars
		$format = JRequest::getVar('format', 'application/json');
		$type = JRequest::getVar('type', 'soft');
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//get storage quota
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php' );
		$disk_usage = ToolsHelperUtils::getDiskUsage( $result->get('username') );
		
		$com_tools_params = JComponentHelper::getParams('com_tools');
		$path = DS . $com_tools_params->get('storagepath', 'webdav' . DS . 'home') . DS . $result->get('username');
		
		jimport('joomla.filesystem.folder');
		$files = array();
		//$files = JFolder::files( $path, '.', true, true, array('.svn', 'CVS') );
		
		//return result
		$object = new stdClass();
		$object->storage = array('quota' => $disk_usage, 'files' => $files);
		$this->setMessageType( $format );
		$this->setMessage( $object );
	}
	
	private function purge()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//get request vars
		$format = JRequest::getVar('format', 'application/json');
		$degree = JRequest::getVar('degree', '');
		
		//get the hubs storage host
		$tool_params = JComponentHelper::getParams('com_tools');
		$storage_host = $tool_params->get('storagehost', '');
		
		//check to make sure we have a storage host
		if($storage_host == '')
		{
			die('no storage host');
		}
		
		//check to make sure we have a degree
		$accepted_degrees = array(
			'default' => 'Minimally',
			'olderthan1' => 'Older than 1 Day',
			'olderthan7' => 'Older than 7 Days',
			'olderthan30' => 'Older than 30 Days',
			'all' => 'All'
		);
		if($degree == '' || !in_array($degree, array_keys($accepted_degrees)))
		{
			die('no degree supplied');
		}
		
		//var to hold purge info
		$info = array();
		
		//open stream
		if (!$fp = stream_socket_client($storage_host, $error_num, $error_str, 30)) 
		{
			die("$error_str ($error_num)");
		}
		else 
		{
			fwrite($fp, 'purge user=' . $result->get('username') . ",degree=$degree \n");
			while (!feof($fp))
			{
				$info[] = fgets($fp, 1024) . "\n";
			}
			fclose($fp);
		}
		
		//trim array values
		$info = array_map("trim", $info);
		
		//
		if(in_array('Success.', $info))
		{
			//return result
			$object = new stdClass();
			$object->purge = array('degree' => $accepted_degrees[$degree], 'success' => 1);
			$this->setMessageType( $format );
			$this->setMessage( $object );
		}
	}
	
	
	private function info()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//get request vars
		$format = JRequest::getVar('format', 'json');
		$tool = JRequest::getVar('tool', '');
		$version = JRequest::getVar('version', 'current');
		
		//we need a tool
		if($tool == '')
		{
			$response = $this->getResponse();
			$response->setErrorMessage( 400, 'Bad Request: Tool Alias Required' );
			return;
		}
		
		//load database object
		JLoader::import("joomla.database.table");
		$database =& JFactory::getDBO();
		
		//poll database for tool matching alias
		$sql = "SELECT r.id, r.title, r.introtext as description, r.fulltxt as abstract, r.created FROM jos_resources as r WHERE r.published=1 AND r.alias='{$tool}'";
		$database->setQuery($sql);
		$t = $database->loadObject();
		
		//add alias
		$t->alias = $tool;
		
		//veryify we have result
		if($t == null)
		{
			$response = $this->getResponse();
			$response->setErrorMessage( 404, 'Not Found: No Tool Found Matching the Alias: "' . $tool . '"' );
			return;
		}
		
		//remove tags and slashes
		$t->abstract = stripslashes(strip_tags($t->abstract));
		
		//get the supported tag
		$rconfig = JComponentHelper::getParams('com_resources');
		$supportedtag = $rconfig->get('supportedtag', '');
		
		//get supportedtag usage
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');
		$this->rt = new ResourcesTags($database);
		$supportedtagusage = $this->rt->getTagUsage($supportedtag, 'alias');
		$t->supported = (in_array($t->alias, $supportedtagusage)) ? 1 : 0;
		
		//get screenshots
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'screenshot.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');
		$ts = new ResourcesScreenshot($database);
		$tv = new ToolVersion($database);
		$vid = $tv->getVersionIdFromResource($t->id, $version);
		$shots = $ts->getScreenshots($t->id, $vid);
		
		//get base path
		$path = $this->getResourcePath( $t->created, $t->id, $vid );
		
		//add full path to screenshot
		$s = array();
		foreach($shots as $shot)
		{
			$s[] = $path . DS . $shot->filename;
		}
		$t->screenshots = $s;
		
		//return result
		$object = new stdClass();
		$object->tool = $t;
		$this->setMessageType( $format );
		$this->setMessage( $object );
	}
	
	private function getResourcePath( $createdDate, $resourceId, $versionId )
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');
		
		//get resource upload path
		$resourceParams = JComponentHelper::getParams('com_resources');
		$path = DS . trim($resourceParams->get("uploadpath"), DS);
		
		//build path based on resource creation date and id
		$path .= ResourcesHtml::build_path( $createdDate, $resourceId, '');
		
		//append version id if we have one
		if($versionId)
		{
			$path .= DS . $versionId;
		}
		
		return $path;
	}
	
	private function listTools()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//get any request vars
		$format = JRequest::getVar('format', 'json');
		
		ximport('Hubzero_Tool');
		ximport('Hubzero_Tool_Version');
		
		// Create a Tool object
		$database = JFactory::getDBO();
		$tools = Hubzero_Tool::getMyTools();
		
		//get the supported tag
		$rconfig = JComponentHelper::getParams('com_resources');
		$supportedtag = $rconfig->get('supportedtag', '');
		
		//get supportedtag usage
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');
		$this->rt = new ResourcesTags($database);
		$supportedtagusage = $this->rt->getTagUsage($supportedtag, 'alias');
		
		$t = array();
		foreach($tools as $k => $tool)
		{
			$t[$k]['alias'] = $tool->alias;
			$t[$k]['name'] = $tool->title;
			$t[$k]['description'] = $tool->description;
			$t[$k]['supported'] = (in_array($tool->alias, $supportedtagusage)) ? 1 : 0;
		}
		
		//encode and return result
		$object = new stdClass();
		$object->tools = $t;
		$this->setMessageType( $format );
		$this->setMessage( $object );
	}
	
	private function sessionScreenshot()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//get any request vars
		$format = JRequest::getVar('format', 'png');
		$sessionid = JRequest::getVar('sessionid', '');
		
		$f = IMAGETYPE_PNG;
		if($format == 'jpeg' || $format == 'jpg')
		{
			$f = IMAGETYPE_JPEG;
		}
		else if($format == 'gif')
		{
			$f = IMAGETYPE_GIF;
		}
		
		//check to make sure we have a valid sessionid
		if($sessionid == '' || !is_numeric($sessionid))
		{
			die("no session id");
		}
		
		//import HUBzero image lib
		ximport('Hubzero_Image');
		
		// check to make sure we have a home directory
		$home_directory = DS .'webdav' . DS . 'home' . DS . strtolower($result->get('username'));
		if(!is_dir($home_directory))
		{
			die("no home dir");
		}
		
		//check to make sure we have a sessions dir
		$home_directory .= DS . 'data' . DS . 'sessions';
		if(!is_dir($home_directory))
		{
			die("no sessions dir");
		}
		
		//check to make sure we have an active session with the ID supplied
		$home_directory .= DS . $sessionid . 'L';
		if(!is_dir($home_directory))
		{
			$response = $this->getResponse();
			$response->setErrorMessage(404,"No Session with the ID: " . $sessionid);
			return;
		}
		
		// check to make sure we have a screenshot
		$screenshot = $home_directory . DS . 'screenshot.png';
		if(!file_exists($screenshot))
		{
			$response = $this->getResponse();
			$response->setErrorMessage(404, 'No screenshot Found');
			return;
		}
		
		//load image and serve up
		$image = new Hubzero_image( $screenshot );
		$this->setMessageType( 'image/' . $format );
		$image->setImageType( $f );
		$image->display();
	}
	
	private function invoke()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//get request vars
		$tool_name 			= JRequest::getVar('app', '');
		$tool_version 		= JRequest::getVar('version', 'default');
		$response_format 	= JRequest::getVar('format', 'json');
		
		//build application object
		$app		 	= new stdClass;
		$app->name		= trim(str_replace(':', '-', $tool_name));
		$app->version 	= $tool_version;
		$app->ip 		= $_SERVER["REMOTE_ADDR"];
		
		//check to make sure we have an app to invoke
		if (!$app->name)
		{
			//build error code and message
			$object = new stdClass();
			$object->error->code = 400;
			$object->error->message = 'You Must Supply a Valid Tool Name to Invoke.';
			
			//set http status code and reason
			$response = $this->getResponse();
			$response->setErrorMessage( $object->error->code, $object->error->message );
			
			//add error to message body
			$this->setMessageType( $response_format );
			$this->setMessage( $object );
			return;
		}
		
		//include needed tool libraries
		include_once( JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.session.php' );
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.viewperm.php');
		
		//create database object
		JLoader::import("joomla.database.table");
		$database =& JFactory::getDBO();
		
		//load the tool version
		$tv = new ToolVersion($database);
		switch ($app->version)
		{
			case 1:
			case 'default':
				$app->name = $tv->getCurrentVersionProperty($app->name, 'instance');
			break;
			case 'test':
			case 'dev':
				$app->name .= '_dev';
			break;
			default:
				$app->name .= '_r' . $app->version;
			break;
		}
		
		$app->toolname = $app->name;
		if ($parent = $tv->getToolname($app->name)) 
		{
			$app->toolname = $parent;
		}
		
		// Check of the toolname has a revision indicator
		$r = substr(strrchr($app->name, '_'), 1);
		if (substr($r, 0, 1) != 'r' && substr($r, 0, 3) != 'dev') 
		{
			$r = '';
		}
		// No version passed and no revision
		if ((!$app->version || $app->version == 'default') && !$r) 
		{
			// Get the latest version
			$app->version = $tv->getCurrentVersionProperty($app->toolname, 'revision');
			$app->name    = $app->toolname . '_r' . $app->version;
		}
		
		// Get the caption/session title
		$tv->loadFromInstance($app->name);
		$app->caption = stripslashes($tv->title);
		$app->title   = stripslashes($tv->title);
		
		//make sure we have a valid tool
		if ($app->title == '' || $app->toolname == '')
		{
			//build error code and message
			$object = new stdClass();
			$object->error->code = 400;
			$object->error->message = 'The Tool "' . $tool_name . '" does not exist on the HUB.';
			
			//set http status code and reason
			$response = $this->getResponse();
			$response->setErrorMessage( $object->error->code, $object->error->message );
			
			//add error to message body
			$this->setMessageType( $response_format );
			$this->setMessage( $object );
			return;
		}
		
		//get tool access
		$toolAccess = ToolsHelperUtils::getToolAccess( $app->name, $result->get('username') );
		
		//do we have access
		if($toolAccess->valid != 1)
		{
			//build error code and message
			$object = new stdClass();
			$object->error->code = 400;
			$object->error->message = $toolAccess->error->message;
			
			//set http status code and reason
			$response = $this->getResponse();
			$response->setErrorMessage( $object->error->code, $object->error->message );
			
			//add error to message body
			$this->setMessageType( $response_format );
			$this->setMessage( $object );
			return;
		}
		
		// Log the launch attempt
		//$this->_recordUsage($app->toolname, $result->get('id'));

		// Get the middleware database
		$mwdb =& ToolsHelperUtils::getMWDBO();

		// Find out how many sessions the user is running.
		$ms = new MwSession($mwdb);
		$jobs = $ms->getCount($result->get('username'));
		
		// Find out how many sessions the user is ALLOWED to run.
		$remain = $result->get('jobsAllowed') - $jobs;
		
		//can we open another session
		if($remain <= 0)
		{
			//build error code and message
			$object = new stdClass();
			$object->error->code = 401;
			$object->error->message = 'You are using all (' . $jobs . ') your available job slots.';
			
			//set http status code and reason
			$response = $this->getResponse();
			$response->setErrorMessage( $object->error->code, $object->error->message );
			
			//add error to message body
			$this->setMessageType( $response_format );
			$this->setMessage( $object );
			return;
		}
		
		//import joomla plugin helpers
		jimport('joomla.plugin.helper');
		
		// Get plugins
		JPluginHelper::importPlugin('mw', $app->toolname);
		$dispatcher =& JDispatcher::getInstance();
		
		// Trigger any events that need to be called before session invoke
		$dispatcher->trigger('onBeforeSessionInvoke', array($app->toolname, $app->version));
		
		// We've passed all checks so let's actually start the session
		$status = $this->middleware("start user=" . $result->get('username') . " ip=" . $app->ip . " app=" . $app->name . " version=" . $app->version, $output);
		
		//make sure we got a valid session back from the middleware
		if(!isset($output->session))
		{
			//build error code and message
			$object = new stdClass();
			$object->error->code = 500;
			$object->error->message = 'There was a issue while trying to start the tool session. Please try again later.';
			
			//set http status code and reason
			$response = $this->getResponse();
			$response->setErrorMessage( $object->error->code, $object->error->message );
			
			//add error to message body
			$this->setMessageType( $response_format );
			$this->setMessage( $object );
			return;
		}
		
		//set session output
		$app->sess = $output->session;
		
		// Trigger any events that need to be called after session invoke
		$dispatcher->trigger('onAfterSessionInvoke', array($app->toolname, $app->version));
		
		// Get a count of the number of sessions of this specific tool
		$appcount = $ms->getCount($result->get('username'), $app->name);
		
		// Do we have more than one session of this tool?
		if ($appcount > 1) 
		{
			// We do, so let's append a timestamp
			$app->caption .= ' (' . date("g:i a") . ')';
		}

		// Save the changed caption
		$ms->load($app->sess);
		$ms->sessname = $app->caption;
		if (!$ms->store()) 
		{
			//build error code and message
			$object = new stdClass();
			$object->error->code = 500;
			$object->error->message = 'There was a issue while trying to start the tool session. Please try again later.';
			
			//set http status code and reason
			$response = $this->getResponse();
			$response->setErrorMessage( $object->error->code, $object->error->message );
			
			//add error to message body
			$this->setMessageType( $response_format );
			$this->setMessage( $object );
			return;
		}
		
		//add tool title to output
		//add session title to ouput
		$output->tool = $app->title;
		$output->session_title = $app->caption;
		
		//return result
		if( $status )
		{
			$object = new stdClass();
			$this->setMessageType( $response_format );
			$this->setMessage( $output );
		}
	}
	
	private function view()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//get the session id we want to stop
		$session = JRequest::getVar("session", "");
		
		//make sure we have the session
		if(!$session)
		{
			$response = $this->getResponse();
			$response->setErrorMessage( 400, 'Bad Request: Session ID Needed' );
			return;
		}
		
		//
		JLoader::import("joomla.database.table");
		include_once( JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.session.php' );
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.viewperm.php');
		
		//get request vars
		$app = new stdClass;
		$app->sess	= $session;
		$app->ip 	= $_SERVER["REMOTE_ADDR"];
		
		//
		$database =& JFactory::getDBO();
		$mwdb =& ToolsHelperUtils::getMWDBO();
		
		//
		$ms = new MwSession($mwdb);
		$row = $ms->loadSession($app->sess, 'admin'); // fix this shit
		
		//
		if (!is_object($row) || !$row->appname) 
		{
			$response = $this->getResponse();
			$response->setErrorMessage( 400, 'Bad Request: Session Doesn\'t Exist' );
			return;
		}
		
		//
		if (strstr($row->appname, '_')) 
		{
			$v = substr(strrchr($row->appname, '_'), 1);
			$v = str_replace('r', '', $v);
			JRequest::setVar('version', $v);
		}
		
		//
		$tv = new ToolVersion($database);
		$parent_toolname = $tv->getToolname($row->appname);
		$toolname = ($parent_toolname) ? $parent_toolname : $row->appname;
		$tv->loadFromInstance($row->appname);
		
		//
		$command = "view user=" . $result->get('username') . " ip=" . $app->ip . " sess=" . $app->sess;
		//error_log(var_export($command, true));
		//
		$app->caption  = $row->sessname;
		$app->name     = $row->appname;
		$app->username = $row->username;
		
		//import joomla plugin helpers
		jimport('joomla.plugin.helper');
		
		// Get plugins
		JPluginHelper::importPlugin('mw', $app->name);
		$dispatcher =& JDispatcher::getInstance();

		// Trigger any events that need to be called before session start
		$dispatcher->trigger('onBeforeSessionStart', array($toolname, $tv->revision));

		// Call the view command
		$status = $this->middleware($command, $output);
		
		// Trigger any events that need to be called after session start
		$dispatcher->trigger('onAfterSessionStart', array($toolname, $tv->revision));
		
		//add the session id to the result
		$output->session = $session;
		
		//add tool title to result
		$output->tool = $tv->title;
		$output->session_title = $app->caption;
		
		//return result
		if($status)
		{
			$obj = new stdClass();
			$this->setMessageType("application/json");
			$this->setMessage($output);
		}
	}
	
	private function stop()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//get the session id we want to stop
		$session = JRequest::getVar("session", "");
		
		//make sure we have the session
		if(!$session)
		{
			$response = $this->getResponse();
			$response->setErrorMessage( 400, 'Bad Request: Session ID Needed' );
			return;
		}
		
		//include needed libraries
		include_once( JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.session.php' );
		
		//instantiate middleware database object
		$mwdb =& ToolsHelperUtils::getMWDBO();
		
		//load the session we are trying to stop
		$ms = new MwSession($mwdb);
		$ms->load($session, $result->get("username"));
		
		//check to make sure session exists and it belongs to the user
		if(!$ms->username || $ms->username != $result->get("username"))
		{
			$response = $this->getResponse();
			$response->setErrorMessage( 400, 'Bad Request: Session Doesn\'t Exist or Does Not Belong to User' );
			return;
		}
		
		//import joomla plugin helpers
		jimport('joomla.plugin.helper');
		
		//get middleware plugins
		JPluginHelper::importPlugin('mw', $ms->appname);
		$dispatcher =& JDispatcher::getInstance();
		
		// Trigger any events that need to be called before session stop
		$dispatcher->trigger('onBeforeSessionStop', array($ms->appname));
		
		//run command to stop session
		$status = $this->middleware("stop $session", $out);
		
		// Trigger any events that need to be called after session stop
		$dispatcher->trigger('onAfterSessionStop', array($ms->appname));
		
		// was the session stopped successfully
		if($status == 1)
		{
			$obj = new stdClass();
			$obj->session = array("session" => $session, "status" => "stopped", "stopped" => date("Y-m-d H:i:s"));
			$this->setMessageType("application/json");
			$this->setMessage($obj);
		}
	}
	
	public function middleware($comm, &$output)
	{
		$retval = true; // Assume success.
		$output = new stdClass();
		$cmd = "/bin/sh ". JPATH_SITE . "/components/com_tools/scripts/mw $comm 2>&1 </dev/null";

		exec($cmd, $results, $status);

		// Check exec status
		if ($status != 0) 
		{
			// Uh-oh. Something went wrong...
			$retval = false;
			//$this->setError($results[0]);
		}

		if (is_array($results))
		{
			// HTML
			// Print out the applet tags or the error message, as the case may be.
			foreach ($results as $line)
			{
				$line = trim($line);

				// If it's a new session, catch the session number...
				if ($retval && preg_match("/^Session is ([0-9]+)/", $line, $sess)) 
				{
					$retval = $sess[1];
					$output->session = $sess[1];
				} 
				else 
				{
					if (preg_match("/width=\"(\d+)\"/i", $line, $param))
					{
						$output->width = trim($param[1], '"');
					}
					if (preg_match("/height=\"(\d+)\"/i", $line, $param))
					{
						$output->height = trim($param[1], '"');
					}
					if (preg_match("/^<param name=\"PORT\" value=\"?(\d+)\"?>/i", $line, $param))
					{
						$output->port = trim($param[1], '"');
					}
					if (preg_match("/^<param name=\"ENCPASSWORD\" value=\"?(.+)\"?>/i", $line, $param))
					{
						$output->password = trim($param[1], '"');
					}
					if (preg_match("/^<param name=\"CONNECT\" value=\"?(.+)\"?>/i", $line, $param))
					{
						$output->connect = trim($param[1], '"');
					}
					if (preg_match("/^<param name=\"ENCODING\" value=\"?(.+)\"?>/i", $line, $param))
					{
						$output->encoding = trim($param[1], '"');
					}
				}
			}
		}
		else 
		{
			// JSON
			$output = json_decode($results);
			if ($output == null)
			{
				$retval = false;
			}
		}

		return $retval;
	}
}