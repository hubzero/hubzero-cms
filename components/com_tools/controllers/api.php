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
			case 'info':		$this->info();		break;
			case 'invoke':		$this->invoke();	break;
			case 'stop':		$this->stop();		break;
			default:		$this->not_found();
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
	
	
	private function info()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();
		
		//get request vars
		$tool = JRequest::getVar('tool', '');
		
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
		$sql = "SELECT r.id, r.title, r.introtext as description, r.fulltxt as abstract FROM jos_resources as r WHERE r.published=1 AND r.alias='{$tool}'";
		$database->setQuery($sql);
		$t = $database->loadObject();
		
		//veryify we have result
		if($t == null)
		{
			$response = $this->getResponse();
			$response->setErrorMessage( 404, 'Not Found: No Tool Found Matching the Alias: "' . $tool . '"' );
			return;
		}
		
		//remove tags and slashes
		$t->abstract = stripslashes(strip_tags($t->abstract));
		
		//return result
		$obj = new stdClass();
		$obj->tool = $t;
		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}
	
	
	private function invoke()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);
		
		//make sure we have a user
		if ($result === false)	return $this->not_found();

		//get request vars
		$app = new stdClass;
		$app->name    = trim(str_replace(':', '-', JRequest::getVar('app', '')));
		$app->version = JRequest::getVar('version', 'default');
		$app->ip = $_SERVER["REMOTE_ADDR"];
		error_log($app->ip);
		
		//check to make sure we have an app
		if (!$app->name)
		{
			$response = $this->getResponse();
			$response->setErrorMessage( 400, 'Bad Request: Tool Doesn\'t Exist' );
			return;
		}
		
		//
		JLoader::import("joomla.database.table");
		include_once( JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.session.php' );
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.viewperm.php');
		
		//
		$database =& JFactory::getDBO();
		
		//
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

		// Check if they have access to run this tool
		//$hasaccess = $this->_getToolAccess($app->name);
		
		// Log the launch attempt
		//$this->_recordUsage($app->toolname, $this->juser->get('id'));

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
			echo $ms->getError();
		}
		
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