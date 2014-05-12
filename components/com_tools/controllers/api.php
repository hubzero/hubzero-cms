<?php
JLoader::import('Hubzero.Api.Controller');

class ToolsControllerApi extends \Hubzero\Component\ApiController
{
	function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');
		
		//include tool utils
		include_once( JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php' );
		
		switch($this->segments[0]) 
		{
			case 'index':		$this->index();				break;
			case 'info':		$this->info();				break;
			case 'screenshot':	$this->screenshot();		break;
			case 'screenshots':	$this->screenshots();		break;
			case 'invoke':		$this->invoke();			break;
			case 'view':		$this->view();				break;
			case 'stop':		$this->stop();				break;
			case 'unshare':		$this->unshare();			break;
			
			case 'storage':		$this->storage();			break;
			case 'purge':		$this->purge();				break;
			
			default:			$this->index();
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
	
	/**
	 * Method to report errors. creates error node for response body as well
	 *
	 * @param	$code		Error Code
	 * @param	$message	Error Message
	 * @param	$format		Error Response Format
	 *
	 * @return     void
	 */
	private function errorMessage( $code, $message, $format = 'json' )
	{
		//build error code and message
		$object = new stdClass();
		$object->error->code = $code;
		$object->error->message = $message;
		
		//set http status code and reason
		$response = $this->getResponse();
		$response->setErrorMessage( $object->error->code, $object->error->message );
		
		//add error to message body
		$this->setMessageType( $format );
		$this->setMessage( $object );
	}
	
	/**
	 * Method to get list of tools
	 *
	 * @return     void
	 */
	private function index()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//instantiate database object
		$database = JFactory::getDBO();
		
		//get any request vars
		$format = JRequest::getVar('format', 'json');
		
		//get list of tools
		$tools = ToolsModelTool::getMyTools();
		
		//get the supported tag
		$rconfig = JComponentHelper::getParams('com_resources');
		$supportedtag = $rconfig->get('supportedtag', '');
		
		//get supportedtag usage
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');
		$resource_tags = new ResourcesTags($database);
		$supportedtagusage = $resource_tags->getTagUsage($supportedtag, 'alias');
		
		//create list of tools
		$t = array();
		foreach($tools as $k => $tool)
		{
			if (isset($t[$tool->alias]))
			{
				$t[$tool->alias]['versions'][] = $tool->revision;
				continue;
			}
			
			$t[$tool->alias]['alias']			= $tool->alias;
			$t[$tool->alias]['title']			= $tool->title;
			$t[$tool->alias]['description'] 	= $tool->description;
			$t[$tool->alias]['versions'] 		= array($tool->revision);
			$t[$tool->alias]['supported'] 	    = (in_array($tool->alias, $supportedtagusage)) ? 1 : 0;
		}
		
		//encode and return result
		$object = new stdClass();
		$object->tools = array_values($t);
		$this->setMessageType( $format );
		$this->setMessage( $object );
	}
	
	/**
	 * Method to get tool information
	 *
	 * @return     void
	 */
	private function info()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//instantiate database object
		$database = JFactory::getDBO();
		
		//get request vars
		$tool 		= JRequest::getVar('tool', '');
		$version 	= JRequest::getVar('version', 'current');
		$format 	= JRequest::getVar('format', 'json');
		
		//we need a tool to continue
		if($tool == '')
		{
			$this->errorMessage(400, 'Tool Alias Required.');
			return;
		}
		
		//poll database for tool matching alias
		$sql = "SELECT r.id, r.alias, tv.toolname, tv.title, tv.description, tv.toolaccess as access, tv.mw, tv.instance, tv.revision, r.fulltxt as abstract, r.created 
				FROM #__resources as r, #__tool_version as tv
				WHERE r.published=1
				AND r.type=7
				AND r.standalone=1
				AND r.access!=4
				AND r.alias=tv.toolname
				AND tv.state=1
				AND r.alias='{$tool}'
				ORDER BY revision DESC";
		$database->setQuery($sql);
		$tool_info = $database->loadObject();
		
		//veryify we have result
		if($tool_info == null)
		{
			$this->errorMessage(404, 'No Tool Found Matching the Alias: "' . $tool . '"');
			return;
		}
		
		//add tool alias to tool info from db
		$tool_info->alias = $tool;
		
		//remove tags and slashes from abastract
		$tool_info->abstract = stripslashes(strip_tags($tool_info->abstract));
		
		//get the supported tag
		$rconfig = JComponentHelper::getParams('com_resources');
		$supportedtag = $rconfig->get('supportedtag', '');
		
		//get supportedtag usage
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');
		$this->rt = new ResourcesTags($database);
		$supportedtagusage = $this->rt->getTagUsage($supportedtag, 'alias');
		$tool_info->supported = (in_array($tool_info->alias, $supportedtagusage)) ? 1 : 0;
		
		//get screenshots
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'screenshot.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');
		$ts = new ResourcesScreenshot($database);
		$tv = new ToolVersion($database);
		$vid = $tv->getVersionIdFromResource($tool_info->id, $version);
		$shots = $ts->getScreenshots($tool_info->id, $vid);
		
		//get base path
		$path = ToolsHelperUtils::getResourcePath( $tool_info->created, $tool_info->id, $vid );
		
		//add full path to screenshot
		$s = array();
		foreach($shots as $shot)
		{
			$s[] = $path . DS . $shot->filename;
		}
		$tool_info->screenshots = $s;
		
		//return result
		$object = new stdClass();
		$object->tool = $tool_info;
		$this->setMessageType( $format );
		$this->setMessage( $object );
	}

	/**
	 * Method to take session screenshots for user
	 *
	 * @return     void
	 */
	private function screenshots()
	{
		// get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		// make sure we have a user
		if ($result === false)	return $this->not_found();

		// request params
		$format = JRequest::getVar('format', 'json');

		// take new screenshots for user
		$cmd = "/bin/sh ". JPATH_SITE . "/components/com_tools/scripts/mw screenshot " . $result->get('username') . " 2>&1 </dev/null";
		exec($cmd, $results, $status);

		// object to return
		$object = new stdClass();
		$object->screenshots_taken = true;

		// set format & return
		$this->setMessageType( $format );
		$this->setMessage( $object );
	}
	
	/**
	 * Method to return session screenshot
	 *
	 * @return     void
	 */
	private function screenshot()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//mw session lib
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.session.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.viewperm.php' );
		
		//instantiate middleware database object
		$mwdb = ToolsHelperUtils::getMWDBO();
		
		//get any request vars
		$type 		= JRequest::getVar('type', 'png');
		$sessionid 	= JRequest::getVar('sessionid', '');
		$notFound	= JRequest::getVar('notfound', 0);
		$format 	= JRequest::getVar('format', 'json');
		
		$image_type = IMAGETYPE_PNG;
		if($type == 'jpeg' || $type == 'jpg')
		{
			$image_type = IMAGETYPE_JPEG;
		}
		else if($type == 'gif')
		{
			$image_type = IMAGETYPE_GIF;
		}
		
		//check to make sure we have a valid sessionid
		if($sessionid == '' || !is_numeric($sessionid))
		{
			$this->errorMessage(401, 'No session ID Specified.');
			return;
		}
		
		//load session
		$ms = new MwSession($mwdb);
		$sess = $ms->loadSession( $sessionid );
		
		//check to make sure we have a sessions dir
		$home_directory = DS .'webdav' . DS . 'home' . DS . strtolower($sess->username) . DS . 'data' . DS . 'sessions';
		if(!is_dir($home_directory))
		{
			clearstatcache();
			if(!is_dir($home_directory))
			{
				$this->errorMessage(500, 'Unable to find users sessions directory. - ' . $home_directory);
				return;
			}
		}
		
		//check to make sure we have an active session with the ID supplied
		$home_directory .= DS . $sessionid . '{,L,D}';
		$directories = glob($home_directory, GLOB_BRACE);
		if(empty($directories))
		{
			$this->errorMessage(404, "No Session directory with the ID: " . $sessionid);
			return;
		}
		else
		{
			$home_directory = $directories[0];
		}
		
		// check to make sure we have a screenshot
		$screenshot = $home_directory . DS . 'screenshot.png';
		
		if(!file_exists($screenshot))
		{
			if($notFound)
			{
				$screenshot = JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'assets' . DS . 'img' . DS . 'screenshot-notfound.png';
			}
			else
			{
				$this->errorMessage(404,'No screenshot Found.');
				return;	
			}
		}
		
		//load image and serve up
		$image = new \Hubzero\Image\Processor( $screenshot );
		$this->setMessageType( 'image/' . $type );
		$image->setImageType( $image_type );
		$image->display();
	}
	
	
	/**
	 * Method to invoke new tools session
	 *
	 * @return     void
	 */
	private function invoke()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//get request vars
		$tool_name 			= JRequest::getVar('app', '');
		$tool_version 		= JRequest::getVar('version', 'default');
		$format 			= JRequest::getVar('format', 'json');
		
		//build application object
		$app		 	= new stdClass;
		$app->name		= trim(str_replace(':', '-', $tool_name));
		$app->version 	= $tool_version;
		$app->ip 		= $_SERVER["REMOTE_ADDR"];
		
		//check to make sure we have an app to invoke
		if (!$app->name)
		{
			$this->errorMessage(400, 'You Must Supply a Valid Tool Name to Invoke.');
			return;
		}
		
		//include needed tool libraries
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.session.php' );
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.viewperm.php');
		
		//create database object
		JLoader::import("joomla.database.table");
		$database = JFactory::getDBO();
		
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
			$this->errorMessage(400, 'The tool "' . $tool_name . '" does not exist on the HUB.');
			return;
		}
		
		//get tool access
		$toolAccess = ToolsHelperUtils::getToolAccess( $app->name, $result->get('username') );
		
		//do we have access
		if($toolAccess->valid != 1)
		{
			$this->errorMessage(400, $toolAccess->error->message);
			return;
		}
		
		// Log the launch attempt
		ToolsHelperUtils::recordToolUsage( $app->toolname, $result->get('id') );

		// Get the middleware database
		$mwdb = ToolsHelperUtils::getMWDBO();

		// Find out how many sessions the user is running.
		$ms = new MwSession($mwdb);
		$jobs = $ms->getCount($result->get('username'));
		
		// Find out how many sessions the user is ALLOWED to run.
		$remain = $result->get('jobsAllowed') - $jobs;
		
		//can we open another session
		if($remain <= 0)
		{
			$this->errorMessage(401, 'You are using all (' . $jobs . ') your available job slots.');
			return;
		}
		
		//import joomla plugin helpers
		jimport('joomla.plugin.helper');
		
		// Get plugins
		JPluginHelper::importPlugin('mw', $app->toolname);
		$dispatcher = JDispatcher::getInstance();
		
		// Trigger any events that need to be called before session invoke
		$dispatcher->trigger('onBeforeSessionInvoke', array($app->toolname, $app->version));
		
		// We've passed all checks so let's actually start the session
		$status = ToolsHelperUtils::middleware("start user=" . $result->get('username') . " ip=" . $app->ip . " app=" . $app->name . " version=" . $app->version, $output);
		
		//make sure we got a valid session back from the middleware
		if(!isset($output->session))
		{
			$this->errorMessage(500, 'There was a issue while trying to start the tool session. Please try again later.');
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
			$app->caption .= ' (' . JFactory::getDate()->format("g:i a") . ')';
		}

		// Save the changed caption
		$ms->load($app->sess);
		$ms->sessname = $app->caption;
		if (!$ms->store()) 
		{
			$this->errorMessage(500, 'There was a issue while trying to start the tool session. Please try again later.');
			return;
		}
		
		//add tool title to output
		//add session title to ouput
		$output->tool = $app->title;
		$output->session_title = $app->caption;
		$output->owner = 1;
		$output->readonly = 0;
		
		//return result
		if( $status )
		{
			$this->setMessageType( $format );
			$this->setMessage( $output );
		}
	}
	
	
	/**
	 * Method to view tool session
	 *
	 * @return     void
	 */
	private function view()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//include needed tool libs
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.session.php' );
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.viewperm.php');
		
		//instantiate db objects
		$database = JFactory::getDBO();
		$mwdb = ToolsHelperUtils::getMWDBO();
		
		//get request vars
		$sessionid 	= JRequest::getVar('sessionid', '');
		$format 	= JRequest::getVar('format', 'json');
		$ip			= $_SERVER["REMOTE_ADDR"];
		
		//make sure we have the session
		if(!$sessionid)
		{
			$this->errorMessage(400, 'Session ID Needed');
			return;
		}
		
		//create app object
		$app = new stdClass;
		$app->sess	= $sessionid;
		$app->ip 	= $ip;
		
		//load the session
		$ms = new MwSession( $mwdb );
		$row = $ms->loadSession( $app->sess );
		
		//if we didnt find a session
		if (!is_object($row) || !$row->appname) 
		{
			$this->errorMessage(400, 'Session Doesn\'t Exist.');
			return;
		}
		
		//get the version
		if (strstr($row->appname, '_')) 
		{
			$v = substr(strrchr($row->appname, '_'), 1);
			$v = str_replace('r', '', $v);
			JRequest::setVar('version', $v);
		}
		
		//load tool version
		$tv = new ToolVersion($database);
		$parent_toolname = $tv->getToolname($row->appname);
		$toolname = ($parent_toolname) ? $parent_toolname : $row->appname;
		$tv->loadFromInstance($row->appname);
		
		//command to run on middleware
		$command = "view user=" . $result->get('username') . " ip=" . $app->ip . " sess=" . $app->sess;
		
		//app vars
		$app->caption  = $row->sessname;
		$app->name     = $row->appname;
		$app->username = $row->username;
		
		//import joomla plugin helpers
		jimport('joomla.plugin.helper');
		
		// Get plugins
		JPluginHelper::importPlugin('mw', $app->name);
		$dispatcher = JDispatcher::getInstance();

		// Trigger any events that need to be called before session start
		$dispatcher->trigger('onBeforeSessionStart', array($toolname, $tv->revision));

		// Call the view command
		$status = ToolsHelperUtils::middleware($command, $output);
		
		// Trigger any events that need to be called after session start
		$dispatcher->trigger('onAfterSessionStart', array($toolname, $tv->revision));
		
		//add the session id to the result
		$output->session = $sessionid;
		
		//add tool title to result
		$output->tool = $tv->title;
		$output->session_title = $app->caption;
		$output->owner = ($row->viewuser == $row->username) ? 1 : 0;
		$output->readonly = ($row->readonly == 'Yes') ? 1 : 0;
		
		//return result
		if($status)
		{
			$this->setMessageType( $format );
			$this->setMessage( $output );
		}
	}
	
	
	/**
	 * Method to stop tool session
	 *
	 * @return     void
	 */
	private function stop()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//include needed libraries
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.session.php' );
		
		//instantiate middleware database object
		$mwdb = ToolsHelperUtils::getMWDBO();
		
		//get request vars
		$sessionid 	= JRequest::getVar('sessionid', '');
		$format 	= JRequest::getVar('format', 'json');
		
		//make sure we have the session
		if(!$sessionid)
		{
			$this->errorMessage(400, 'Missing session ID.');
			return;
		}
		
		//load the session we are trying to stop
		$ms = new MwSession($mwdb);
		$ms->load($sessionid, $result->get("username"));
		
		//check to make sure session exists and it belongs to the user
		if(!$ms->username || $ms->username != $result->get("username"))
		{
			$this->errorMessage(400, 'Session Doesn\'t Exist or Does Not Belong to User');
			return;
		}
		
		//import joomla plugin helpers
		jimport('joomla.plugin.helper');
		
		//get middleware plugins
		JPluginHelper::importPlugin('mw', $ms->appname);
		$dispatcher = JDispatcher::getInstance();
		
		// Trigger any events that need to be called before session stop
		$dispatcher->trigger('onBeforeSessionStop', array($ms->appname));
		
		//run command to stop session
		$status = ToolsHelperUtils::middleware("stop $sessionid", $out);
		
		// Trigger any events that need to be called after session stop
		$dispatcher->trigger('onAfterSessionStop', array($ms->appname));
		
		// was the session stopped successfully
		if($status == 1)
		{
			$object = new stdClass();
			$object->session = array("session" => $sessionid, "status" => "stopped", "stopped" => JFactory::getDate()->toSql());
			$this->setMessageType( $format );
			$this->setMessage( $object );
		}
	}
	
	
	/**
	 * Method to disconnect from shared tool session
	 *
	 * @return     void
	 */
	private function unshare()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//include needed libraries
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.viewperm.php' );
		
		//instantiate middleware database object
		$mwdb = ToolsHelperUtils::getMWDBO();
		
		//get request vars
		$sessionid 	= JRequest::getVar('sessionid', '');
		$format 	= JRequest::getVar('format', 'json');
		
		//check to make sure we have session id
		if(!$sessionid)
		{
			$this->errorMessage(400, 'Missing session ID.');
			return;
		}
		
		// Delete the viewperm
		$mv = new MwViewperm( $mwdb );
		$mv->deleteViewperm( $sessionid, $result->get("username") );
		
		//make sure we didnt have error disconnecting
		if(!$mv->getError())
		{
			$object = new stdClass();
			$object->session = array("session" => $sessionid, "status" => "disconnected", "disconnected" => JFactory::getDate()->toSql());
			$this->setMessageType( $format );
			$this->setMessage( $object );
		}
	}
	
	
	/**
	 * Method to return users storage results
	 *
	 * @return     void
	 */
	private function storage()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//get request vars
		$type 	= JRequest::getVar('type', 'soft');
		$format = JRequest::getVar('format', 'json');
		
		//get storage quota
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php' );
		$disk_usage = ToolsHelperUtils::getDiskUsage( $result->get('username') );
		
		//get the tools storage path
		$com_tools_params = JComponentHelper::getParams('com_tools');
		$path = DS . $com_tools_params->get('storagepath', 'webdav' . DS . 'home') . DS . $result->get('username');
		
		//get a list of files
		jimport('joomla.filesystem.folder');
		$files = array();
		//$files = JFolder::files( $path, '.', true, true, array('.svn', 'CVS') );
		
		//return result
		$object = new stdClass();
		$object->storage = array('quota' => $disk_usage, 'files' => $files);
		$this->setMessageType( $format );
		$this->setMessage( $object );
	}
	
	
	/**
	 * Method to purge users storage
	 *
	 * @return     void
	 */
	private function purge()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);
		
		//make sure we have a user
		if($result === false)	return $this->not_found();
		
		//get request vars
		$degree = JRequest::getVar('degree', '');
		$format = JRequest::getVar('format', 'json');
		
		//get the hubs storage host
		$tool_params = JComponentHelper::getParams('com_tools');
		$storage_host = $tool_params->get('storagehost', '');
		
		//check to make sure we have a storage host
		if($storage_host == '')
		{
			$this->errorMessage(500, 'Unable to find storage host.');
			return;
		}
		
		//list of acceptable purge degrees
		$accepted_degrees = array(
			'default' => 'Minimally',
			'olderthan1' => 'Older than 1 Day',
			'olderthan7' => 'Older than 7 Days',
			'olderthan30' => 'Older than 30 Days',
			'all' => 'All'
		);
		
		//check to make sure we have a degree
		if($degree == '' || !in_array($degree, array_keys($accepted_degrees)))
		{
			$this->errorMessage(401, 'No purge level supplied.');
			return;
		}
		
		//var to hold purge info
		$purge_info = array();
		
		//open stream to purge files
		if (!$fp = stream_socket_client($storage_host, $error_num, $error_str, 30)) 
		{
			die("$error_str ($error_num)");
		}
		else 
		{
			fwrite($fp, 'purge user=' . $result->get('username') . ",degree=$degree \n");
			while (!feof($fp))
			{
				$purge_info[] = fgets($fp, 1024) . "\n";
			}
			fclose($fp);
		}
		
		//trim array values
		$purge_info = array_map("trim", $purge_info);
		
		//check to make sure the purge was successful
		if(in_array('Success.', $purge_info))
		{
			//return result
			$object = new stdClass();
			$object->purge = array('degree' => $accepted_degrees[$degree], 'success' => 1);
			$this->setMessageType( $format );
			$this->setMessage( $object );
		}
	}
}
