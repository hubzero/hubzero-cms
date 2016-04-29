<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Api\Controllers;

use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Exception;
use Component;
use stdClass;
use Request;
use Lang;
use User;

include_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'utils.php';
include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'tool.php';

/**
 * API controller class for tool sessions
 */
class Sessionsv1_0 extends ApiController
{
	/**
	 * Method to get list of tools
	 *
	 * @apiMethod GET
	 * @apiUri    /tools/list
	 * @apiParameter {
	 * 		"name":          "user_id",
	 * 		"description":   "Member identifier",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       0
	 * }
	 * @return    void
	 */
	public function listTask()
	{
		//get the userid and attempt to load user profile
		$userid = Request::getInt('user_id', 0);
		$result = User::getInstance($userid);

		//make sure we have a user
		//if ($result === false) return $this->not_found();

		//instantiate database object
		$database = \App::get('db');

		//get list of tools
		$tools = \Components\Tools\Models\Tool::getMyTools();

		//get the supported tag
		$rconfig = Component::params('com_resources');
		$supportedtag = $rconfig->get('supportedtag', '');

		//get supportedtag usage
		include_once(Component::path('com_resources') . DS . 'helpers' . DS . 'tags.php');
		$resource_tags = new \Components\Resources\Helpers\Tags(0);
		$supportedtagusage = $resource_tags->getTagUsage($supportedtag, 'alias');

		//create list of tools
		$t = array();
		foreach ($tools as $k => $tool)
		{
			if (isset($t[$tool->alias]))
			{
				$t[$tool->alias]['versions'][] = $tool->revision;
				continue;
			}

			$t[$tool->alias]['alias']       = $tool->alias;
			$t[$tool->alias]['title']       = $tool->title;
			$t[$tool->alias]['description'] = $tool->description;
			$t[$tool->alias]['versions']    = array($tool->revision);
			$t[$tool->alias]['supported']   = (in_array($tool->alias, $supportedtagusage)) ? 1 : 0;
		}

		//encode and return result
		$object = new stdClass();
		$object->tools = array_values($t);

		$this->send($object);
	}

	/**
	 * Method to get tool information
	 *
	 * @apiMethod GET
	 * @apiUri    /tools/{tool}
	 * @apiParameter {
	 * 		"name":          "tool",
	 * 		"description":   "Tool identifier",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "version",
	 * 		"description":   "Tool version",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       "current"
	 * }
	 * @return     void
	 */
	public function infoTask()
	{
		$database = \App::get('db');

		$tool    = Request::getVar('tool', '');
		$version = Request::getVar('version', 'current');

		//we need a tool to continue
		if ($tool == '')
		{
			throw new Exception(Lang::txt('Tool Alias Required.'), 400);
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
		if ($tool_info == null)
		{
			throw new Exception(Lang::txt('No Tool Found Matching the Alias: "%s"', $tool), 404);
		}

		//add tool alias to tool info from db
		$tool_info->alias = $tool;

		//remove tags and slashes from abastract
		$tool_info->abstract = stripslashes(strip_tags($tool_info->abstract));

		//get the supported tag
		$rconfig = Component::params('com_resources');
		$supportedtag = $rconfig->get('supportedtag', '');

		//get supportedtag usage
		include_once(Component::path('com_resources') . DS . 'helpers' . DS . 'tags.php');
		$this->rt = new \Components\Resources\Helpers\Tags(0);
		$supportedtagusage = $this->rt->getTagUsage($supportedtag, 'alias');
		$tool_info->supported = (in_array($tool_info->alias, $supportedtagusage)) ? 1 : 0;

		//get screenshots
		include_once(Component::path('com_resources') . DS . 'tables' . DS . 'screenshot.php');
		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php');
		$ts = new \Components\Resources\Tables\Screenshot($database);
		$tv = new \Components\Tools\Tables\Version($database);
		$vid   = $tv->getVersionIdFromResource($tool_info->id, $version);
		$shots = $ts->getScreenshots($tool_info->id, $vid);

		//get base path
		$path = \Components\Tools\Helpers\Utils::getResourcePath($tool_info->created, $tool_info->id, $vid);

		//add full path to screenshot
		$s = array();
		foreach ($shots as $shot)
		{
			$s[] = $path . DS . $shot->filename;
		}
		$tool_info->screenshots = $s;

		$object = new stdClass();
		$object->tool = $tool_info;

		$this->send($object);
	}

	/**
	 * Method to take session screenshots for user
	 *
	 * @apiMethod GET
	 * @apiUri    /tools/screenshots/{user_id}
	 * @apiParameter {
	 * 		"name":          "user_id",
	 * 		"description":   "Member identifier",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       0
	 * }
	 * @return     void
	 */
	public function screenshotsTask()
	{
		$userid = App::get('authn')['user_id'];
		$result = User::getInstance($userid);

		// make sure we have a user
		if ($result === false)
		{
			throw new Exception(Lang::txt('COM_TOOLS_ERROR_USER_NOT_FOUND'), 404);
		}

		// take new screenshots for user
		$cmd = "/bin/sh ". dirname(dirname(__DIR__)) . "/scripts/mw screenshot " . $result->get('username') . " 2>&1 </dev/null";
		exec($cmd, $results, $status);

		// object to return
		$object = new stdClass();
		$object->screenshots_taken = true;

		$this->send($object);
	}

	/**
	 * Method to return session screenshot
	 *
	 * @apiMethod GET
	 * @apiUri    /tools/{sessionid}/screenshot
	 * @apiParameter {
	 * 		"name":          "sessionid",
	 * 		"description":   "Tool session identifier",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "type",
	 * 		"description":   "Image format",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "png",
	 * 		"allowedValues": "png, jpg, jpeg, gif"
	 * }
	 * @apiParameter {
	 * 		"name":          "notfound",
	 * 		"description":   "Not found",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @return     void
	 */
	public function screenshotTask()
	{
		//$this->requiresAuthentication();

		require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'session.php');
		require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'viewperm.php');

		//instantiate middleware database object
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		//get any request vars
		$type      = Request::getVar('type', 'png');
		$sessionid = Request::getVar('sessionid', '');
		$notFound  = Request::getVar('notfound', 0);

		$image_type = IMAGETYPE_PNG;
		if ($type == 'jpeg' || $type == 'jpg')
		{
			$image_type = IMAGETYPE_JPEG;
		}
		else if ($type == 'gif')
		{
			$image_type = IMAGETYPE_GIF;
		}

		//check to make sure we have a valid sessionid
		if ($sessionid == '' || !is_numeric($sessionid))
		{
			throw new Exception(Lang::txt('No session ID Specified.'), 401);
		}

		//load session
		$ms = new \Components\Tools\Tables\Session($mwdb);
		$sess = $ms->loadSession($sessionid);

		//check to make sure we have a sessions dir
		$home_directory = DS .'webdav' . DS . 'home' . DS . strtolower($sess->username) . DS . 'data' . DS . 'sessions';
		if (!is_dir($home_directory))
		{
			clearstatcache();
			if (!is_dir($home_directory))
			{
				throw new Exception(Lang::txt('Unable to find users sessions directory: %s', $home_directory), 404);
			}
		}

		//check to make sure we have an active session with the ID supplied
		$home_directory .= DS . $sessionid . '{,L,D}';
		$directories = glob($home_directory, GLOB_BRACE);
		if (empty($directories))
		{
			throw new Exception(Lang::txt('No Session directory with the ID: %s', $sessionid), 404);
		}
		else
		{
			$home_directory = $directories[0];
		}

		// check to make sure we have a screenshot
		$screenshot = $home_directory . DS . 'screenshot.png';

		if (!file_exists($screenshot))
		{
			if ($notFound)
			{
				$screenshot = dirname(dirname(__DIR__)) . DS . 'site' . DS . 'assets' . DS . 'img' . DS . 'screenshot-notfound.png';
			}
			else
			{
				throw new Exception(Lang::txt('No screenshot Found.'), 404);
			}
		}

		// Load image and serve up
		$image = new \Hubzero\Image\Processor($screenshot);
		$image->setImageType($image_type);
		$image->display();
		exit();
	}


	/**
	 * Method to invoke new tools session
	 *
	 * @apiMethod GET
	 * @apiUri    /tools/{tool}/invoke
	 * @return    void
	 */
	public function invokeTask()
	{
		//get the userid and attempt to load user profile
		$userid = App::get('authn')['user_id'];
		$result = User::getInstance($userid);

		//make sure we have a user
		if ($result === false) return $this->not_found();

		//get request vars
		$tool_name    = Request::getVar('app', '');
		$tool_version = Request::getVar('version', 'default');

		//build application object
		$app = new stdClass;
		$app->name    = trim(str_replace(':', '-', $tool_name));
		$app->version = $tool_version;
		$app->ip      = $_SERVER["REMOTE_ADDR"];

		//check to make sure we have an app to invoke
		if (!$app->name)
		{
			$this->errorMessage(400, 'You Must Supply a Valid Tool Name to Invoke.');
			return;
		}

		//include needed tool libraries
		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php');
		require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'session.php');
		require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'viewperm.php');

		//create database object
		$database = \App::get('db');

		//load the tool version
		$tv = new \Components\Tools\Tables\Version($database);
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
			throw new Exception(Lang::txt('The tool "%s" does not exist on the HUB.', $tool_name), 400);
		}

		//get tool access
		$toolAccess = \Components\Tools\Helpers\Utils::getToolAccess($app->name, $result->get('username'));

		//do we have access
		if ($toolAccess->valid != 1)
		{
			throw new Exception($toolAccess->error->message, 400);
		}

		// Log the launch attempt
		\Components\Tools\Helpers\Utils::recordToolUsage($app->toolname, $result->get('id'));

		// Get the middleware database
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		// Find out how many sessions the user is running.
		$ms = new \Components\Tools\Models\Middleware\Session($mwdb);
		$jobs = $ms->getCount($result->get('username'));

		// Find out how many sessions the user is ALLOWED to run.
		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'preferences.php');

		$preferences = new \Components\Tools\Tables\Preferences($database);
		$preferences->loadByUser($result->get('uidNumber'));
		if (!$preferences || !$preferences->id)
		{
			include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'sessionclass.php');
			$scls = new \Components\Tools\Tables\SessionClass($this->database);
			$default = $scls->find('one', array('alias' => 'default'));
			$preferences->user_id  = $result->get('uidNumber');
			$preferences->class_id = $default->id;
			$preferences->jobs     = ($default->jobs ? $default->jobs : 3);
			$preferences->store();
		}
		$remain = $preferences->jobs - $jobs;

		//can we open another session
		if ($remain <= 0)
		{
			throw new Exception(Lang::txt('You are using all (%s) your available job slots.', $jobs), 401);
		}

		// Get plugins
		Plugin::import('mw', $app->name);

		// Trigger any events that need to be called before session invoke
		Event::trigger('mw.onBeforeSessionInvoke', array($app->toolname, $app->version));

		// We've passed all checks so let's actually start the session
		$status = \Components\Tools\Helpers\Utils::middleware("start user=" . $result->get('username') . " ip=" . $app->ip . " app=" . $app->name . " version=" . $app->version, $output);

		//make sure we got a valid session back from the middleware
		if (!isset($output->session))
		{
			throw new Exception(Lang::txt('There was a issue while trying to start the tool session. Please try again later.'), 500);
		}

		//set session output
		$app->sess = $output->session;

		// Trigger any events that need to be called after session invoke
		Event::trigger('mw.onAfterSessionInvoke', array($app->toolname, $app->version));

		// Get a count of the number of sessions of this specific tool
		$appcount = $ms->getCount($result->get('username'), $app->name);

		// Do we have more than one session of this tool?
		if ($appcount > 1)
		{
			// We do, so let's append a timestamp
			$app->caption .= ' (' . Date::format("g:i a") . ')';
		}

		// Save the changed caption
		$ms->load($app->sess);
		$ms->sessname = $app->caption;
		if (!$ms->store())
		{
			throw new Exception(Lang::txt('There was a issue while trying to start the tool session. Please try again later.'), 500);
		}

		//add tool title to output
		//add session title to ouput
		$output->tool = $app->title;
		$output->session_title = $app->caption;
		$output->owner = 1;
		$output->readonly = 0;

		//return result
		if ($status)
		{
			$this->send($output);
		}
	}

	/**
	 * Runs a rappture job.
	 *
	 * This is more than just invoking a tool. We're expecting a driver file to pass to the
	 * tool to be picked up and automatically run by rappture.
	 *
	 * @apiMethod POST
	 * @apiUri    /tools/run
	 * @apiParameter {
	 * 		"name":          "app",
	 * 		"description":   "Name of app installed as a tool in the hub",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * }
	 * @apiParameter {
	 * 		"name":          "revision",
	 * 		"description":   "The specific requested revision of the app",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "default",
	 * }
	 * @apiParameter {
	 * 		"name":          "xml",
	 * 		"description":   "Content of the driver file that rappture will use to invoke the given app",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * }
	 * @return     void
	 */
	public function runTask()
	{
		$this->requiresAuthentication();

		// Get the user_id and attempt to load user profile
		$userid  = App::get('authn')['user_id'];
		$profile = User::getInstance($userid);

		// Make sure we have a user
		if ($profile === false) return $this->not_found();

		// Grab tool name and version
		$tool_name    = Request::getVar('app', '');
		$tool_version = Request::getVar('revision', 'default');

		// Build application object
		$app          = new stdClass;
		$app->name    = trim(str_replace(':', '-', $tool_name));
		$app->version = $tool_version;
		$app->ip      = $_SERVER["REMOTE_ADDR"];

		// Check to make sure we have an app to invoke
		if (!$app->name)
		{
			throw new Exception(Lang::txt('A valid app name must be provided'), 404);
		}

		// Include needed tool libraries
		require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php';
		require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'session.php';
		require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'viewperm.php';

		// Create database object
		$database = \App::get('db');

		// Load the tool version
		$tv = new \Components\Tools\Tables\Version($database);
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

		// Make sure we have a valid tool
		if ($app->title == '' || $app->toolname == '')
		{
			throw new Exception(Lang::txt('The tool "%s" does not exist on the HUB.', $tool_name), 404);
		}

		// Get tool access
		$toolAccess = \Components\Tools\Helpers\Utils::getToolAccess($app->name, $profile->get('username'));

		// Do we have access
		if ($toolAccess->valid != 1)
		{
			throw new Exception($toolAccess->error->message, 500);
		}

		// Log the launch attempt
		\Components\Tools\Helpers\Utils::recordToolUsage($app->toolname, $profile->get('id'));

		// Get the middleware database
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		// Find out how many sessions the user is running
		$ms = new \Components\Tools\Tables\Session($mwdb);
		$jobs = $ms->getCount($profile->get('username'));

		// Find out how many sessions the user is ALLOWED to run.
		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'preferences.php');

		$preferences = new \Components\Tools\Tables\Preferences($database);
		$preferences->loadByUser($profile->get('id'));
		if (!$preferences || !$preferences->id)
		{
			$default = $preferences->find('one', array('alias' => 'default'));
			$preferences->user_id  = $profile->get('id');
			$preferences->class_id = $default->id;
			$preferences->jobs     = $default->jobs;
			$preferences->store();
		}
		$remain = $preferences->jobs - $jobs;

		//can we open another session
		if ($remain <= 0)
		{
			throw new Exception(Lang::txt('You are using all (%s) your available job slots.', $jobs), 401);
		}

		// Check for an incoming driver file
		if ($driver = Request::getVar('xml', false, 'post', 'none', 2))
		{
			// Build a path to where the driver file will go through webdav
			$base = DS . 'webdav' . DS . 'home';
			$user = DS . $profile->get('username');
			$data = DS . 'data';
			$drvr = DS . '.queued_drivers';
			$inst = DS . md5(time()) . '.xml';

			// Real home directory
			$homeDir = $profile->get('homeDirectory');

			// First, make sure webdav is there and that the necessary folders are there
			if (!\Filesystem::exists($base))
			{
				throw new Exception(Lang::txt('Home directories are unavailable'), 500);
			}

			// Now see if the user has a home directory yet
			if (!\Filesystem::exists($homeDir))
			{
				// Try to create their home directory
				require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'utils.php');

				if (!\Components\Tools\Helpers\Utils::createHomeDirectory($profile->get('username')))
				{
					throw new Exception(Lang::txt('Failed to create user home directory'), 500);
				}
			}

			// Check for, and create if needed a session data directory
			if (!\Filesystem::exists($base . $user . $data) && !\Filesystem::makeDirectory($base . $user . $data, 0700))
			{
				throw new Exception(Lang::txt('Failed to create data directory'), 500);
			}

			// Check for, and create if needed a queued drivers directory
			if (!\Filesystem::exists($base . $user . $data . $drvr) && !\Filesystem::makeDirectory($base . $user . $data . $drvr, 0700))
			{
				throw new Exception(Lang::txt('Failed to create drivers directory'), 500);
			}

			// Write the driver file out
			if (!\Filesystem::write($base . $user . $data . $drvr . $inst, $driver))
			{
				throw new Exception(Lang::txt('Failed to create driver file'), 500);
			}
		}
		else
		{
			throw new Exception(Lang::txt('No driver file provided'), 404);
		}

		// Now build params path that will be included with tool execution
		// We know from the checks above that this directory already exists
		$params  = 'file(execute):' . $homeDir . DS . 'data' . DS . '.queued_drivers' . $inst;
		$encoded = ' params=' . rawurlencode($params) . ' ';
		$command = 'start user=' . $profile->get('username') . " ip={$app->ip} app={$app->name} version={$app->version}" . $encoded;
		$status  = \Components\Tools\Helpers\Utils::middleware($command, $output);

		if (!$status)
		{
			throw new Exception(Lang::txt('Tool invocation failed'), 500);
		}

		$this->send(array(
			'success' => true,
			'session' => $output->session
		));
	}

	/**
	 * Gets the status of the session identified
	 *
	 * @apiMethod GET
	 * @apiUri    /tools/status
	 * @apiParameter {
	 * 		"name":          "session_num",
	 * 		"description":   "a valid hub tool session number",
	 * 		"type":          "string",
	 * 		"required":      true,
	 *  	"default":       0 
	 * }
	 * @return void
	 **/
	public function statusTask()
	{
		$this->requiresAuthentication();

		// Get profile instance and session number
		$profile = User::getInstance(App::get('authn')['user_id']);
		$session = Request::getInt('session_num', 0);

		// Require authorization
		if ($profile === false) return $this->not_found();

		// Get the middleware database
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		// Make sure it's a valid sesssion number and the user is/was the owner of it
		require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'session.php';
		require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'viewperm.php';

		// This next part is probably redundant since we can only access the session directories that
		// are in the user's webdav filesystem, anyway.  Also, it seems that the entry in the session
		// table exists only as long as the session is alive.  As soon as the session ends, the entry
		// is zapped and we end up getting a lot of 401 errors even for sessions that we own(ed). (NTD 2015-12-15)
		/*$ms = new \Components\Tools\Tables\Session($mwdb);
		if (!$ms->checkSession($session))
		{
			throw new Exception(Lang::txt('You can only check the status of your sessions.'), 401);
		}*/

		// Check for specific sesssion entry, either sesssion# or session#-expired
		$dir = DS . 'webdav' . DS . 'home' . DS . $profile->get('username') . DS . 'data' . DS .'sessions' . DS . $session;

		// If the active session dir doesn't exist, look for an expired one
		if (!is_dir($dir))
		{
			$dir .= '-expired';

			if (!is_dir($dir))
			{
				throw new Exception(Lang::txt('No session directory found.'), 404);
			}
		}

		// Look for a rappture.status file in that dir
		$statusFile = $dir . DS . 'rappture.status';
		if (!is_file($statusFile))
		{
			throw new Exception(Lang::txt('No status file found.'), 404);
		}

		// Read the file
		$status   = file_get_contents($statusFile);
		$parsed   = explode("\n", trim($status));
		$finished = (strpos(end($parsed), '[status] exit') !== false) ? true : false;
		$runFile  = '';

		if ($finished)
		{
			$count = count($parsed);
			preg_match('/\[status\] output saved in [a-zA-Z0-9\/]*\/(run[0-9]*\.xml)/', $parsed[($count-2)], $matches);
			$runFile = (isset($matches[1])) ? $matches[1] : '';
		}

		$this->send(array(
			'success'  => true,
			'status'   => $parsed,
			'finished' => $finished,
			'run_file' => $runFile
		));
	}

	/**
	 * Grabs the output from a tool session
	 *
	 * @apiMethod GET
	 * @apiUri    /tools/output
	 * @apiParameter {
	 * 		"name":          "session_num",
	 * 		"description":   "a valid hub tool session number",
	 * 		"type":          "string",
	 * 		"required":      true,
	 *  	"default":       0 
	 * }
	 * @apiParameter {
	 * 		"name":          "run_file",
	 * 		"description":   "the name of the run file that contains the desired output",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * }
	 * @return void
	 */
	public function outputTask()
	{
		$this->requiresAuthentication();

		$session = Request::getInt('session_num', 0);
		$runFile = Request::getVar('run_file', false);

		if (!$session)
		{
			throw new Exception(Lang::txt('Session not found.'), 404);
		}

		// Get the middleware database
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		// Make sure it's a valid sesssion number and the user is/was the owner of it
		require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'session.php';
		require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'viewperm.php';


		// This next part is probably redundant since we can only access the session directories that
		// are in the user's webdav filesystem, anyway.  Also, it seems that the entry in the session
		// table exists only as long as the session is alive.  As soon as the session ends, the entry
		// is zapped and we end up getting a lot of 401 errors even for sessions that we own(ed). (NTD 2015-12-15)
		/*$ms = new \Components\Tools\Tables\Session($mwdb);
		if (!$ms->checkSession($session))
		{
			throw new Exception(Lang::txt('You can only check the status of your sessions.'), 401);
		}*/

		// Check for specific sesssion entry
		$dir = DS . 'webdav' . DS . 'home' . DS . User::get('username') . DS . 'data' . DS .'results' . DS . $session;

		if (!is_dir($dir))
		{
			throw new Exception(Lang::txt('No results directory found.'), 404);
		}

		$outputFile = $dir . DS . $runFile;

		if (!is_file($outputFile))
		{
			throw new Exception(Lang::txt('No run file found.'), 404);
		}

		$output = file_get_contents($outputFile);

		$this->send(array(
			'success' => true,
			'output'  => $output
		));
	}

	/**
	 * Method to view tool session
	 *
	 * @apiMethod GET
	 * @apiUri    /tools/{session}
	 * @return    void
	 */
	public function readTask()
	{
		//get the userid and attempt to load user profile
		$userid = App::get('authn')['user_id'];
		$result = User::getInstance($userid);

		//make sure we have a user
		if ($result === false) return $this->not_found();

		//include needed tool libs
		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php');
		require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'session.php');
		require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'viewperm.php');

		//instantiate db objects
		$database = \App::get('db');
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		//get request vars
		$sessionid = Request::getVar('sessionid', '');
		$ip        = Request::ip();

		//make sure we have the session
		if (!$sessionid)
		{
			throw new Exception(Lang::txt('Session ID Needed'), 400);
		}

		//create app object
		$app = new stdClass;
		$app->sess = $sessionid;
		$app->ip   = $ip;

		//load the session
		$ms = new \Components\Tools\Models\Middleware\Session($mwdb);
		$row = $ms->loadSession($app->sess);

		//if we didnt find a session
		if (!is_object($row) || !$row->appname)
		{
			throw new Exception(Lang::txt('Session Doesn\'t Exist.'), 404);
		}

		//get the version
		if (strstr($row->appname, '_'))
		{
			$v = substr(strrchr($row->appname, '_'), 1);
			$v = str_replace('r', '', $v);
			Request::setVar('version', $v);
		}

		//load tool version
		$tv = new \Components\Tools\Tables\Version($database);
		$parent_toolname = $tv->getToolname($row->appname);
		$toolname = ($parent_toolname) ? $parent_toolname : $row->appname;
		$tv->loadFromInstance($row->appname);

		//command to run on middleware
		$command = "view user=" . $result->get('username') . " ip=" . $app->ip . " sess=" . $app->sess;

		//app vars
		$app->caption  = $row->sessname;
		$app->name     = $row->appname;
		$app->username = $row->username;

		// Get plugins
		Plugin::import('mw', $app->name);

		// Trigger any events that need to be called before session start
		Event::trigger('mw.onBeforeSessionStart', array($toolname, $tv->revision));

		// Call the view command
		$status = \Components\Tools\Helpers\Utils::middleware($command, $output);

		// Trigger any events that need to be called after session start
		Event::trigger('mw.onAfterSessionStart', array($toolname, $tv->revision));

		//add the session id to the result
		$output->session = $sessionid;

		//add tool title to result
		$output->tool = $tv->title;
		$output->session_title = $app->caption;
		$output->owner = ($row->viewuser == $row->username) ? 1 : 0;
		$output->readonly = ($row->readonly == 'Yes') ? 1 : 0;

		//return result
		if ($status)
		{
			$this->send($output);
		}
	}

	/**
	 * Method to stop tool session
	 *
	 * @apiMethod DELETE
	 * @apiUri    /tools/{session}
	 * @return    void
	 */
	public function deleteTask()
	{
		//get the userid and attempt to load user profile
		$userid = App::get('authn')['user_id'];
		$result = User::getInstance($userid);

		//make sure we have a user
		if ($result === false) return $this->not_found();

		//include needed libraries
		require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'mw.session.php');

		//instantiate middleware database object
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		//get request vars
		$sessionid = Request::getVar('sessionid', '');

		//make sure we have the session
		if (!$sessionid)
		{
			throw new Exception('Missing session ID.', 400);
		}

		//load the session we are trying to stop
		$ms = new \Components\Tools\Models\Middleware\Session($mwdb);
		$ms->load($sessionid, $result->get("username"));

		//check to make sure session exists and it belongs to the user
		if (!$ms->username || $ms->username != $result->get("username"))
		{
			throw new Exception('Session Doesn\'t Exist or Does Not Belong to User', 400);
		}

		//get middleware plugins
		Plugin::import('mw', $ms->appname);

		// Trigger any events that need to be called before session stop
		Event::trigger('mw.onBeforeSessionStop', array($ms->appname));

		//run command to stop session
		$status = \Components\Tools\Helpers\Utils::middleware("stop $sessionid", $out);

		// Trigger any events that need to be called after session stop
		Event::trigger('mw.onAfterSessionStop', array($ms->appname));

		// was the session stopped successfully
		if ($status == 1)
		{
			$object = new stdClass();
			$object->session = array(
				'session' => $sessionid,
				'status'  => 'stopped',
				'stopped' => with(new Date)->toSql()
			);

			$this->send($object);
		}
	}


	/**
	 * Method to disconnect from shared tool session
	 *
	 * @apiMethod GET
	 * @apiUri    /tools/{session}/unshare
	 * @return    void
	 */
	public function unshareTask()
	{
		$this->requiresAuthentication();

		//get the userid and attempt to load user profile
		$userid = App::get('authn')['user_id'];
		$result = User::getInstance($userid);

		//make sure we have a user
		if ($result === false) return $this->not_found();

		//include needed libraries
		require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'mw.viewperm.php');

		//instantiate middleware database object
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		//get request vars
		$sessionid = Request::getVar('sessionid', '');

		//check to make sure we have session id
		if (!$sessionid)
		{
			throw new Exception(Lang::txt('Missing session ID.'), 404);
		}

		// Delete the viewperm
		$mv = new \Components\Tools\Models\Middleware\Viewperm($mwdb);
		$mv->deleteViewperm($sessionid, $result->get('username'));

		//make sure we didnt have error disconnecting
		if (!$mv->getError())
		{
			$object = new stdClass();
			$object->session = array(
				'session'      => $sessionid,
				'status'       => 'disconnected',
				'disconnected' => with(new Date)->toSql()
			);

			$this->send($object);
		}
	}

	/**
	 * Method to return users storage results
	 *
	 * @apiMethod GET
	 * @apiUri    /tools/{user_id}
	 * @return    void
	 */
	public function storageTask()
	{
		$this->requiresAuthentication();

		//get the userid and attempt to load user profile
		$userid = App::get('authn')['user_id'];
		$result = User::getInstance($userid);

		//make sure we have a user
		if ($result === false) return $this->not_found();

		//get request vars
		$type   = Request::getVar('type', 'soft');

		//get storage quota
		require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'utils.php');
		$disk_usage = \Components\Tools\Helpers\Utils::getDiskUsage($result->get('username'));

		//get the tools storage path
		$com_tools_params = Component::params('com_tools');
		$path = DS . $com_tools_params->get('storagepath', 'webdav' . DS . 'home') . DS . $result->get('username');

		//get a list of files
		$files = array();
		//$files = Filesystem::files($path, '.', true, true, array('.svn', 'CVS'));

		//return result
		$object = new stdClass();
		$object->storage = array('quota' => $disk_usage, 'files' => $files);

		$this->send($object);
	}


	/**
	 * Method to purge users storage
	 *
	 * @apiMethod DELETE
	 * @apiUri    /tools/{user_id}
	 * @return    void
	 */
	public function purgeTask()
	{
		$this->requiresAuthentication();

		//get the userid and attempt to load user profile
		$userid = App::get('authn')['user_id'];
		$result = User::getInstance($userid);

		//make sure we have a user
		if ($result === false) return $this->not_found();

		//get request vars
		$degree = Request::getVar('degree', '');

		//get the hubs storage host
		$tool_params = Component::params('com_tools');
		$storage_host = $tool_params->get('storagehost', '');

		//check to make sure we have a storage host
		if ($storage_host == '')
		{
			throw new Exception(Lang::txt('Unable to find storage host.'), 500);
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
		if ($degree == '' || !in_array($degree, array_keys($accepted_degrees)))
		{
			throw new Exception(Lang::txt('No purge level supplied.'), 401);
		}

		//var to hold purge info
		$purge_info = array();

		//open stream to purge files
		if (!$fp = stream_socket_client($storage_host, $error_num, $error_str, 30))
		{
			throw new Exception("$error_str ($error_num)", 500);
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
		if (in_array('Success.', $purge_info))
		{
			//return result
			$object = new stdClass();
			$object->purge = array('degree' => $accepted_degrees[$degree], 'success' => 1);

			$this->send($object);
		}
	}
}
