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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Document;
use Pathway;
use stdClass;
use Component;
use Request;
use Route;
use Lang;
use User;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'middleware.php');
require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'vnc.php');

/**
 * Tools controller class for simulation sessions
 */
class Sessions extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->_authorize('session');

		$this->registerTask('__default', 'view');

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @param      integer $session Session ID
	 * @return     void
	 */
	protected function _buildPathway($session=null)
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if (isset($this->app) && $this->app->name)
		{
			if (strstr($this->app->name, '_dev') || strstr($this->app->name, '_r'))
			{
				$bits = explode('_', $this->app->name);
				$bit = array_pop($bits);
				$appname = implode('_', $bits);
			}
			else
			{
				$appname = $this->app->name;
			}
			Pathway::append(
				$this->app->caption,
				'index.php?option=' . $this->_option . '&app=' . $appname
			);
			Pathway::append(
				Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task . '&app=' . $appname . '&version=' . $this->app->version
			);
		}
		else
		{
			if ($this->_task && $this->_task != 'tools')
			{
				Pathway::append(
					Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
					Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task)
				);
			}
		}
		if (is_object($session))
		{
			Pathway::append(
				$title,
				Route::url('index.php?option=' . $this->_option . '&tag=' . $lnk)
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @param      integer $session Session ID
	 * @return     void
	 */
	protected function _buildTitle($session=null)
	{
		$this->_title = Lang::txt(strtoupper($this->_option));
		if ($this->app && $this->app->name)
		{
			$this->_title .= ': ' . $this->app->caption;
		}
		if ($this->_task && $this->_task != 'tools')
		{
			$this->_title .= ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		if (is_object($session))
		{
			$title .= ': ';
		}
		Document::setTitle($this->_title);
	}

	/**
	 * Show a login form
	 *
	 * @return     void
	 */
	public function loginTask($rtrn='')
	{
		if (!$rtrn)
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task), 'server');
		}
		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
		);
		return;
	}

	/**
	 * Show an Access Denied error
	 *
	 * @return     void
	 */
	public function accessdeniedTask()
	{
		$this->view->setLayout('accessdenied');

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Instantiate the view
		$this->view->title = $this->_title;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Show an Bad Parameters error
	 *
	 * @return     void
	 */
	public function badparamsTask($badparams = '')
	{
		$this->view->setLayout('badparams');

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Instantiate the view
		$this->view->title = $this->_title;

		$this->view->badparams = $badparams;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Show a quota exceeded warning and list of sessions
	 *
	 * @return     void
	 */
	public function quotaexceededTask()
	{
		$this->view->setLayout('quotaexceeded');

		// Check that the user is logged in
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}

		// Build the page title
		$title  = Lang::txt('COM_MEMBERS');
		$title .= ': ' . Lang::txt('COM_MEMBERS_VIEW');
		$title .= ': ' . stripslashes(User::get('name'));
		$title .= ': ' . Lang::txt(strtoupper($this->_option . '_' . $this->_task));

		// Set the page title
		Document::setTitle($title);

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt('COM_MEMBERS'),
				'index.php?option=com_members'
			);
		}
		Pathway::append(
			stripslashes(User::get('name')),
			'index.php?option=com_members&id=' . User::get('id')
		);
		Pathway::append(
			Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task
		);

		// Get the middleware database
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		// Get the user's sessions
		$ms = new \Components\Tools\Tables\Session($mwdb);
		$sessions = $ms->getRecords(User::get('username'), '', false);

		$this->view->sessions = $sessions;
		if ($this->config->get('access-manage-session'))
		{
			$this->view->allsessions = $ms->getRecords(User::get('username'), '', $this->config->get('access-manage-session'));
		}
		$this->view->active = Request::getVar('active', '');
		$this->view->config = $this->config;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Normalize a path
	 *
	 * @param   string  $path
	 * @param   boolean $isFile
	 * @return  string
	 */
	private function normalize_path($path, $isFile = false)
	{
		if (!isset($path[0]) || $path[0] != '/')
		{
			return false;
		}

		$parts = explode('/', $path);

		$result = array();

		foreach ($parts as $part)
		{
			if ($part === '' || $part == '.')
			{
				continue;
			}

			if ($part == '..')
			{
				array_pop($result);
			}
			else
			{
				$result[] = $part;
			}
		}

		if ($isFile) // Files can't end with directory separator or special directory names
		{
			if ($part == '' || $part == '.' || $part == '..')
			{
				return false;
			}
		}

		return '/' . implode('/', $result) . ($isFile ? '' : '/');
	}

	/**
	 * Invoke a tool session
	 *
	 * @return     void
	 */
	public function invokeTask()
	{
		// Check that the user is logged in
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}

		$params = Request::getString('params','','default',JREQUEST_ALLOWRAW);

		if (!empty($params))
		{
			$params_whitelist = explode(',', $this->config->get('params_whitelist',''));

			$separator = "\r\n";

			$line = trim(strtok($params, $separator));

			$verified_params = array();

			while ($line !== false)
			{
				$re = "/\s*(directory|file|int)\s*(?:\:|\(\s*(.*?)\s*\)\s*:)\s*(.*?)\s*$/";

				if (preg_match($re, $line, $matches) != false)
				{
					$type  = $matches[1];
					$key   = $matches[2];
					$value = $matches[3];

					if (($type == 'directory' || $type == 'file'))
					{
						// Replace ~/ prefix with user's home directory
						if (strncmp($value,"~/",2) === 0)
						{
							$xprofile = \Hubzero\User\Profile::getInstance(User::get('id'));

							$homeDirectory = rtrim($xprofile->get('homeDirectory'), '/');

							if (!isset($homeDirectory[0]) || $homeDirectory[0] !== '/')
							{
								break;
							}

							$value = substr_replace($value, $homeDirectory, 0, 1);
						}

						// Fail if $value doesn't start with '/'
						if ($value[0] != '/')
						{
							break;
						}

						// Fail if unable to normalize $value
						$value = $this->normalize_path($value, $type == 'file');

						if ($value === false)
						{
							break;
						}

						// Fail if $value contains a control charcater (0x00-0x1F) or an invalid utf-8 string
						if (preg_match('/^[^\x00-\x1f]*$/u', $value) == 0)
						{
							break;
						}

						// Fail if whitelist is empty
						if (empty($params_whitelist))
						{
							break;
						}

						// Fail if $value isn't prefixed with a whitelisted directory
						foreach ($params_whitelist as $wl)
						{
							if (empty($wl))
							{
								continue;
							}

							$wl = rtrim(trim($wl),'/') . '/';  // make sure we compare against a full path element

							if (strncmp($wl,$value,strlen($wl)) === 0)
							{
								$match = $wl;
								break;
							}
						}

						if (!isset($match))
						{
							break;
						}

						// Add verified parameter to array
						if ($key)
						{
							$verified_params[] = $type . '(' . $key . '):' . $value;
						}
						else
						{
							$verified_params[] = $type . ':' . $value;
						}
					}
					else if ($type == 'int')
					{
						// Fail if $value contains a control charcater (0x00-0x1F) or an invalid utf-8 string
						if (preg_match('/^[^\x00-\x1f]*$/u', $value) == 0)
						{
							break;
						}

						// Fail if $value not an integer
						if (preg_match('/^[-+]?[0-9]+$/', $value) == 0)
						{
							break;
						}
						// Add verified parameter to array
						if ($key)
						{
							$verified_params[] = $type . '(' . $key . '):' . $value;
						}
						else
						{
							$verified_params[] = $type . ':' . $value;
						}
					}
				}
				else if (!empty($line)) // Fail if unrecognized non-empty parameter line
				{
					break;
				}

				$line = strtok($separator);  // Get next line
			}

			if ($line !== false)
			{
				$this->badparamsTask($params);
				return;
			}
		}

		// Incoming
		$app = new stdClass;
		$app->name    = trim(str_replace(':', '-', Request::getVar('app', '')));
		//$app->number  = 0;
		$app->version = Request::getVar('version', 'default');

		// Get the user's IP address
		$app->ip      = Request::ip();

		// Make sure we have an app to invoke
		if (!$app->name)
		{
			App::redirect(
				Route::url($this->config->get('stopRedirect', 'index.php?option=com_members&task=myaccount'))
			);
			return;
		}

		// Get the parent toolname (appname without any revision number "_r423")
		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php');
		$tv = new \Components\Tools\Tables\Version($this->database);

		switch ($app->version)
		{
			case 1:
			case 'current':
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
		if ((!$app->version || $app->version == 'default' || $app->version == 'current') && !$r)
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
		$hasaccess = $this->_getToolAccess($app->name);
		//$status2 = ($hasaccess) ? "PASSED" : "FAILED";

		//Log::debug("mw::invoke " . $app->name . " by " . User::get('username') . " from " . $app->ip . " _getToolAccess " . $status2);

		if ($this->getError())
		{
			echo '<!-- ' . $this->getError() . ' -->';
		}
		if (!$hasaccess)
		{
			//$this->_redirect = Route::url('index.php?option=' . $this->_option . '&task=accessdenied');
			$this->app = $app;
			$this->accessdeniedTask();
			return;
		}

		$country = \Hubzero\Geocode\Geocode::ipcountry($app->ip);

		//die($app->ip . $country);

		// Log the launch attempt
		$this->_recordUsage($app->toolname, User::get('id'));

		// Get the middleware database
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		// Find out how many sessions the user is running.
		$ms = new \Components\Tools\Tables\Session($mwdb);
		$jobs = $ms->getCount(User::get('username'));

		// Find out how many sessions the user is ALLOWED to run.
		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'preferences.php');

		$preferences = new \Components\Tools\Tables\Preferences($this->database);
		$preferences->loadByUser(User::get('id'));
		if (!$preferences || !$preferences->id)
		{
			include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'sessionclass.php');
			$scls = new \Components\Tools\Tables\SessionClass($this->database);
			$default = $scls->find('one', array('alias' => 'default'));
			$preferences->user_id  = User::get('id');
			$preferences->class_id = $default->id;
			$preferences->jobs     = ($default->jobs ? $default->jobs : 3);
			$preferences->store();
		}

		$xprofile = \Hubzero\User\Profile::getInstance(User::get('id'));
		$remain = $preferences->jobs - $jobs;

		// Have they reached their session quota?
		if ($remain <= 0)
		{
			$this->quotaexceededTask();
			return;
		}

		if ($this->config->get('warn_multiples', 0) && !Request::getInt('newinstance', 0))
		{
			$sessions = $ms->getRecords(User::get('username'), $app->name, false);
			if ($sessions && count($sessions) > 0)
			{
				$this->view->setLayout('list');
				$this->view->app = $app;
				$this->view->config   = $this->config;
				$this->view->sessions = $sessions;

				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}

				$this->view->display();
				return;
			}
		}

		// Get their disk space usage
		$this->_getDiskUsage();
		$this->_redirect = '';

		$app->percent = 0;
		if ($this->config->get('show_storage', 1))
		{
			$app->percent = $this->percent;
		}
		if ($this->percent >= 100)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=storage')
			);
			return;
		}

		// Get plugins
		Plugin::import('mw', $app->toolname);

		// Trigger any events that need to be called before session invoke
		Event::trigger('mw.onBeforeSessionInvoke', array($app->toolname, $app->version));

		$toolparams = '';

		if (!empty($params))
		{
			$toolparams = " params=" . rawurlencode($params) . " ";
		}

		// Determine zone
		$app->zone_id = 0;
		if ($this->config->get('zones'))
		{
			$middleware = new \Components\Tools\Models\Middleware();

			$this->database->setQuery("SELECT zone_id FROM `#__tool_version_zone` WHERE tool_version_id=" . $this->database->quote($tv->id));
			$middleware->set('allowed', $this->database->loadColumn());

			if ($zone = $middleware->zoning($app->ip, $middleware->get('allowed')))
			{
				if ($zone->exists())
				{
					$toolparams .= ' zone=' . $zone->get('zone');
					$app->zone_id = $zone->get('id');
				}
			}
		}

		// We've passed all checks so let's actually start the session
		$status = $this->middleware("start user=" . User::get('username') . " ip=" . $app->ip . " app=" . $app->name . " version=" . $app->version . $toolparams, $output);
		if ($this->getError())
		{
			//App::abort(500, $this->getError());
			//return;
			App::redirect(
				Route::url($this->config->get('stopRedirect', 'index.php?option=com_members&task=myaccount')),
				Lang::txt('COM_TOOLS_ERROR_SESSION_INVOKE_FAILED'),
				'error'
			);
			return;
		}
		$app->sess = !empty($output->session) ? $output->session : '';

		// Trigger any events that need to be called after session invoke
		Event::trigger('mw.onAfterSessionInvoke', array($app->toolname, $app->version));

		// Get a count of the number of sessions of this specific tool
		$appcount = $ms->getCount(User::get('username'), $app->name);
		// Do we have more than one session of this tool?
		if ($appcount > 1)
		{
			// We do, so let's append a timestamp
			$app->caption .= ' (' . Date::toLocal('g:i a') . ')';
		}

		// Save the changed caption
		$ms->load($app->sess);
		$ms->sessname = $app->caption;
		$ms->params   = $params;
		if (!$ms->store())
		{
			echo $ms->getError();
			die();
		}

		$rtrn = Request::getVar('return', '');

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&app=' . $app->toolname . '&task=session&sess=' . $app->sess . '&return=' . $rtrn . (Request::getWord('viewer') ? '&viewer=' . Request::getWord('viewer') : ''), false)
		);
	}

	/**
	 * Invoke a tool session
	 *
	 * @return     void
	 */
	public function reinvokeTask()
	{
		// Check that the user is logged in
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}

		$middleware = new \Components\Tools\Models\Middleware();

		// Incoming
		$id = Request::getInt('sess', 0);

		// Try loading the session
		//$session = \Components\Tools\Models\Middleware\Session::getInstance($id, $this->config->get('access-manage-session'));
		$session = $middleware->session($id, $this->config->get('access-manage-session'));

		// Double-check that the user can view this session.
		if (!$session->exists())
		{
			App::abort(404, Lang::txt('COM_TOOLS_ERROR_SESSION_NOT_FOUND') . ': ' . $id);
			return;
		}

		// Stop the old session
		$status = $this->middleware("stop $id", $output);
		if ($status == 0)
		{
			$msg = '<p>Stopping ' . $id;
			if (is_array($output))
			{
				$msg .= '<br />';
				foreach ($output as $line)
				{
					$msg .= $line . "\n";
				}
			}
			$msg .= '</p>'."\n";
		}

		// Get tool params
		$toolparams = '';
		if ($params = $session->get('params'))
		{
			$toolparams = " params=" . rawurlencode($params) . " ";
		}

		// Set the zone
		if ($zone = Request::getInt('zone', 0))
		{
			$mwz = $middleware->zone($zone);
			if ($mwz->exists())
			{
				$toolparams .= ' zone=' . $mwz->get('zone');
			}
		}

		// We've passed all checks so let's actually start the new session
		$status = $this->middleware("start user=" . User::get('username') . " ip=" . Request::ip() . " app=" . $session->app() . " version=" . $session->app('version') . $toolparams, $output);
		if ($this->getError())
		{
			App::redirect(
				Route::url($this->config->get('stopRedirect', 'index.php?option=com_members&task=myaccount')),
				Lang::txt('COM_TOOLS_ERROR_SESSION_INVOKE_FAILED'),
				'error'
			);
			return;
		}

		// Do we have a session ID?
		$new_id = !empty($output->session) ? $output->session : '';

		// Load the new session and transfer some data
		//$reinvoked = \Components\Tools\Models\Middleware\Session::getInstance($new_id, $this->config->get('access-manage-session'));
		$reinvoked = $middleware->session($new_id, $this->config->get('access-manage-session'));
		$reinvoked->set('sessname', $session->get('sessname'));
		$reinvoked->set('params', $params);
		$reinvoked->set('zone_id', $zone);
		if (!$reinvoked->store())
		{
			App::abort(500, $reinvoked->getError());
			return;
		}

		// Redirect to the new session view
		$rtrn = Request::getVar('return', '');

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&app=' . $session->app() . '&task=session&sess=' . $new_id . '&return=' . $rtrn . (Request::getWord('viewer') ? '&viewer=' . Request::getWord('viewer') : ''), false)
		);
	}

	/**
	 * Share a session
	 *
	 * @return     void
	 */
	public function shareTask()
	{
		// Check that the user is logged in
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}

		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		// Incoming
		$sess     = Request::getVar('sess', '');
		$username = trim(Request::getVar('username', ''));
		$group    = Request::getInt('group', 0);
		$readonly = Request::getVar('readonly', '');
		$no_html  = Request::getInt('no_html', 0);

		$users = array();
		if (strstr($username, ','))
		{
			$users = explode(',', $username);
			$users = array_map('trim', $users);
		}
		elseif (strstr($username, ' '))
		{
			$users = explode(' ', $username);
			$users = array_map('trim', $users);
		}
		else
		{
			$users[] = $username;
		}

		//do we want to share with a group
		if (isset($group) && $group != 0)
		{
			$hg = \Hubzero\User\Group::getInstance( $group );
			$members = $hg->get('members');

			//merge group members with any passed in username field
			$users = array_values(array_unique(array_merge($users, $members)));

			//remove this user
			$isUserInArray = array_search(User::get('id'), $users);
			if (isset($isUserInArray))
			{
				unset($users[$isUserInArray]);
			}

			//fix array keys
			$users = array_values(array_filter($users));
		}

		// Double-check that the user can access this session.
		$ms = new \Components\Tools\Tables\Session($mwdb);
		$row = $ms->checkSession($sess, User::get('username'));

		// Ensure we found an active session
		if (!$row->sesstoken)
		{
			App::abort(404, Lang::txt('COM_TOOLS_ERROR_SESSION_NOT_FOUND') . ': ' . $sess);
			return;
		}

		//$row = $rows[0];
		$owner = $row->viewuser;

		if ($readonly != 'Yes')
		{
			$readonly = 'No';
		}

		$mv = new \Components\Tools\Tables\Viewperm($mwdb);
		$rows = $mv->loadViewperm($sess, $owner);
		if (count($rows) != 1)
		{
			App::abort(404, Lang::txt('COM_TOOLS_ERROR_UNABLE_TO_GET_ENTRY_FOR', $sess, $owner));
			break;
		}
		foreach ($users as $user)
		{
			// Check for invalid characters
			if (!preg_match("#^[0-9a-zA-Z]+[_0-9a-zA-Z]*$#i", $user))
			{
				$this->setError(Lang::txt('COM_TOOLS_ERROR_INVALID_USERNAME') . ': ' . $user);
				continue;
			}

			// Check that the user exist
			$zuser = User::getInstance($user);
			if (!$zuser || !is_object($zuser) || !$zuser->get('id'))
			{
				$this->setError(Lang::txt('COM_TOOLS_ERROR_INVALID_USERNAME') . ': ' . $user);
				continue;
			}

			//load current view perm
			$mwViewperm = new \Components\Tools\Tables\Viewperm($mwdb);
			$currentViewPerm = $mwViewperm->loadViewperm($sess, $zuser->get('username'));

			// If there are no matching entries in viewperm, add a new entry,
			// Otherwise, update the existing entry (e.g. readonly).
			if (count($currentViewPerm) == 0)
			{
				$mwViewperm->sessnum   = $sess;
				$mwViewperm->viewuser  = $zuser->get('username');
				$mwViewperm->viewtoken = md5(rand());
				$mwViewperm->geometry  = $rows[0]->geometry;
				$mwViewperm->fwhost    = $rows[0]->fwhost;
				$mwViewperm->fwport    = $rows[0]->fwport;
				$mwViewperm->vncpass   = $rows[0]->vncpass;
				$mwViewperm->readonly  = $readonly;
				$mwViewperm->insert();
			}
			else
			{
				$mwViewperm->sessnum   = $currentViewPerm[0]->sessnum;
				$mwViewperm->viewuser  = $currentViewPerm[0]->viewuser;
				$mwViewperm->viewtoken = $currentViewPerm[0]->viewtoken;
				$mwViewperm->geometry  = $currentViewPerm[0]->geometry;
				$mwViewperm->fwhost    = $currentViewPerm[0]->fwhost;
				$mwViewperm->fwport    = $currentViewPerm[0]->fwport;
				$mwViewperm->vncpass   = $currentViewPerm[0]->vncpass;
				$mwViewperm->readonly  = $readonly;
				$mwViewperm->updateViewPerm();
			}

			if ($mwViewperm->getError())
			{
				App::abort(500, $mwViewperm->getError());
				return;
			}
		}

		// Drop through and re-view the session...
		$this->viewTask();
	}

	/**
	 * Stop sharing a session
	 *
	 * @return     void
	 */
	public function unshareTask()
	{
		// Check that the user is logged in
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}

		// Needed objects
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		// Incoming
		$sess = Request::getVar('sess', '');
		$user = Request::getVar('username', '');
		$app = Request::getVar('app', '');

		// If a username is given, check that the user owns this session.
		if ($user != '')
		{
			$ms = new \Components\Tools\Tables\Session($mwdb);
			$ms->load($sess, User::get('username'));

			if (!$ms->sesstoken)
			{
				App::abort(404, Lang::txt('COM_TOOLS_ERROR_SESSION_NOT_FOUND') . ': ' . $sess);
				return;
			}
		}
		else
		{
			// Otherwise, assume that the user wants to disconnect a session that's been shared with them.
			$user = User::get('username');
		}

		// Delete the viewperm
		$mv = new \Components\Tools\Tables\Viewperm($mwdb);
		$mv->deleteViewperm($sess, $user);

		if ($user == User::get('username'))
		{
			// Take us back to the main page...
			App::redirect(
				Route::url($this->config->get('stopRedirect', 'index.php?option=com_members&task=myaccount'))
			);
			return;
		}

		// Drop through and re-view the session...
		//$this->viewTask();
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&app=' . $app . '&task=session&sess=' . $sess )
		);
	}

	/**
	 * View a session
	 *
	 * @return     void
	 */
	public function viewTask()
	{
		// Check that the user is logged in
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}

		// Incoming
		$app = new stdClass(); //array();
		$app->sess = Request::getInt('sess', 0);

		// Make sure we have an app to invoke
		if (!$app->sess)
		{
			App::redirect(
				Route::url($this->config->get('stopRedirect', 'index.php?option=com_members&task=myaccount')),
				Lang::txt('COM_TOOLS_ERROR_SESSION_NOT_FOUND'),
				'error'
			);
			return;
		}

		$this->view->rtrn = Request::getVar('return', '');

		// Get the user's IP address
		$app->ip = Request::ip(); //Request::getVar('REMOTE_ADDR', '', 'server');

		// Double-check that the user can view this session.
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		$ms = new \Components\Tools\Tables\Session($mwdb);
		$row = $ms->loadSession($app->sess, $this->config->get('access-manage-session'));

		if (!is_object($row) || !$row->appname)
		{
			App::abort(404, Lang::txt('COM_TOOLS_ERROR_SESSION_NOT_FOUND') . ': ' . $app->sess);
			return;
		}

		$this->view->middleware = new \Components\Tools\Models\Middleware();
		//$session = $this->view->middleware->session($app->sess);

		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php');
		$tv = new \Components\Tools\Tables\Version($this->database);
		$tv->loadFromInstance($row->appname);
		$this->database->setQuery("SELECT zone_id FROM `#__tool_version_zone` WHERE tool_version_id=" . $this->database->quote($tv->id));
		$this->view->middleware->set('allowed', $this->database->loadColumn());

		$this->view->zone = $this->view->middleware->zone($row->zone_id);

		if (strstr($row->appname, '_'))
		{
			$v = substr(strrchr($row->appname, '_'), 1);
			$v = str_replace('r', '', $v);
			Request::setVar('version', $v);
		}

		// Get parent tool name - to write correct links
		//$tv = new \Components\Tools\Tables\Version($this->database);
		$parent_toolname = $tv->getToolname($row->appname);
		$toolname = ($parent_toolname) ? $parent_toolname : $row->appname;

		// Get the tool's name
		//$tv->loadFromInstance($row->appname);
		$app->title = stripslashes($tv->title);
		$app->params = new \Hubzero\Config\Registry($tv->params);

		// Ensure we found an active session
		if (!$row->sesstoken)
		{
			App::abort(404, Lang::txt('MW_ERROR_SESSION_NOT_FOUND') . ': ' . $app->sess . '. ' . Lang::txt('MW_SESSION_NOT_FOUND_EXPLANATION'));
			return;
		}

		// Get their disk space usage
		$app->percent = 0;
		if ($this->config->get('show_storage'))
		{
			$this->_getDiskUsage('soft', false);
			$app->percent = $this->percent;
		}

		// Build the view command
		if ($this->config->get('access-manage-session'))
		{
			$command = "view user=" . $row->username . " ip=" . $app->ip . " sess=" . $app->sess;
		}
		else
		{
			$command = "view user=" . User::get('username') . " ip=" . $app->ip . " sess=" . $app->sess;
		}

		// Check if we have access to run this tool.
		// If not, force view to be read-only.
		// This will happen in the event of sharing.
		$noaccess = ($this->_getToolAccess($row->appname) == false);
		if ($this->getError())
		{
			echo '<!-- ' . $this->getError() . ' -->';
		}
		if ($noaccess)
		{
			$command .= " readonly=1";
		}

		$app->caption  = $row->sessname;
		$app->name     = $row->appname;
		$app->username = $row->username;
		$app->owns     = $ms->checkSession($app->sess);

		// Get plugins
		Plugin::import('mw', $app->name);

		// Trigger any events that need to be called before session start
		Event::trigger('mw.onBeforeSessionStart', array($toolname, $tv->revision));

		// Call the view command
		$status = $this->middleware($command, $output);

		if ($app->params->get('vncEncoding',0))
		{
			$output->encoding = trim($app->params->get('vncEncoding',''),'"');
		}

		if ($app->params->get('vncShowControls',0))
		{
			$output->show_controls = trim($app->params->get('vncShowControls',''),'"');
		}

		if ($app->params->get('vncShowLocalCursor',0))
		{
			$output->show_local_cursor = trim($app->params->get('vncShowLocalCursor',''),'"');
		}

		if ($app->params->get('vncDebug',0))
		{
			$output->debug = trim($app->params->get('vncDebug',''),'"');
		}

		foreach ($output as $key => $value)
		{
			$output->$key = strval($value);
		}

		$boolean_keys = array('debug','show_local_cursor','show_controls','view_only','trust_all_vnc_certs', 'view_only', 'wsproxy_encrypt');

		foreach ($boolean_keys as $key)
		{
			if (isset($output->$key))
			{
				$value = strtolower($output->$key);

				if (in_array($value, array('1', 'y', 'on', 'yes', 't', 'true')))
				{
					$output->$key = 'Yes';
				}
				else
				{
					$output->$key = 'No';
				}
			}
		}

		if (empty($output->wsproxy_host))
		{
			$output->wsproxy_host = $_SERVER['SERVER_NAME'];
		}

		if (empty($output->wsproxy_port))
		{
			$output->wsproxy_port = '8080';
		}

		if (!isset($output->wsproxy_encrypt))
		{
			$output->wsproxy_encrypt = 'No';
		}

		if (!isset($output->view_only))
		{
			$output->view_only = 'No';
		}

		if (!isset($output->trust_all_vnc_certs))
		{
			$output->trust_all_vnc_certs = 'Yes';
		}

		if (!isset($output->disableSSL))
		{
			$output->disable_ssl = 'No';
		}

		if (!isset($output->name))
		{
			$output->name = 'App Viewer';
		}

		if (!isset($output->offer_relogin))
		{
			$output->offer_relogin = 'Yes';
		}

		if (!isset($output->permissions))
		{
			$output->permissions = 'all-permissions';
		}

		if (!isset($output->code))
		{
			$output->code = 'VncViewer.class';
		}

		if (!isset($output->archive))
		{
			$output->archive =  rtrim(Request::base(true), '/') . '/core/components/com_tools/scripts/VncViewer-20150319-01.jar';
		}

		if (!isset($output->id))
		{
			$output->id = 'theapp';
		}

		if (!isset($output->host))
		{
			$output->host = $_SERVER['SERVER_NAME'];
		}

		if (!isset ($output->password) && !empty($output->encpassword))
		{
			$decpassword = pack("H*", $output->encpassword);
			$output->password = \Components\Tools\Helpers\Vnc::decrypt($decpassword);
		}

		if (!isset ($output->token) && !empty($output->connect))
		{
			if (strncmp($output->connect, 'vncsession:', 11) ==0)
			{
				$output->token = substr($output->connect, 11);
			}
		}

		if (empty($output->class))
		{
			$cls = array();
			if ($app->params->get('noResize', 0))
			{
				$cls[] = 'no-resize';
			}
			if ($app->params->get('noPopout', 0))
			{
				$cls[] = 'no-popout';
			}
			if ($app->params->get('noPopoutClose', 0))
			{
				$cls[] = 'no-popout-close';
			}
			if ($app->params->get('noPopoutMaximize', 0))
			{
				$cls[] = 'no-popout-maximize';
			}
			if ($app->params->get('noRefresh', 0))
			{
				$cls[] = 'no-refresh';
			}

			$output->class = "thisapp";

			if (!empty($cls))
			{
				$output->class .= ' ' . implode(' ', $cls);
			}
		}

		// Trigger any events that need to be called after session start
		Event::trigger('mw.onAfterSessionStart', array($toolname, $tv->revision));

		// Set the layout
		$sublayout = strtolower(Request::getWord('layout', ''));
		$sublayout = ($sublayout == 'display' ? '' : $sublayout);
		$this->view->setLayout('session' . ($sublayout ? '_' . $sublayout : ''));

		// Set the page title
		$title  = Lang::txt('COM_RESOURCES').': '.Lang::txt('COM_TOOLS');
		$title .= ($app->title) ? ': ' . $app->title : ': ' . $app->name;
		$title .= ': ' . Lang::txt('Session');
		$title .= ($app->caption) ? ': ' . $app->sess . ' "' . $app->caption . '"' : ': ' . $app->sess;

		Document::setTitle($title);

		// Set the breadcrumbs
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt('COM_RESOURCES'),
				'index.php?option=com_resources'
			);
		}
		Pathway::append(
			Lang::txt('COM_TOOLS'),
			'index.php?option=com_resources&type=tools'
		);
		Pathway::append(
			$app->title,
			'index.php?option=' . $this->_option . '&controller=' . $this->controller . '&app=' . $toolname
		);

		if ($this->_task)
		{
			$t = ($app->caption) ? $app->sess . ' "' . $app->caption . '"' : $app->sess;
			Pathway::append(
				Lang::txt('COM_TOOLS_SESSION_NUMBER', $t),
				'index.php?option=' . $this->_option . '&controller=' . $this->controller . '&app=' . $toolname . '&task=session&sess=' . $app->sess
			);
		}

		//get users groups
		$this->view->mygroups = \Hubzero\User\Helper::getGroups( User::get('id'), 'members', 1 );

		$this->view->app      = $app;
		$this->view->config   = $this->config;
		$this->view->output   = $output;
		$this->view->toolname = $toolname;
		$this->view->total    = $this->total;

		// Get everyone sharing this session
		if ($app->sess)
		{
			// Get the middleware database
			$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

			// Load the viewperm
			$ms = new \Components\Tools\Tables\Viewperm($mwdb);
			$this->view->shares = $ms->loadViewperm($app->sess);
		}

		// Set any error messages
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Stops a session and redirects upon success
	 *
	 * @return     void
	 */
	public function stopTask()
	{
		// Check that the user is logged in
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}

		// Incoming
		$sess = Request::getVar('sess', '');
		$rtrn = base64_decode(Request::getVar('return', '', 'method', 'base64'));

		$rediect = $this->config->get('stopRedirect', 'index.php?option=com_members&task=myaccount');

		// Ensure we have a session
		if (!$sess)
		{
			App::redirect(
				Route::url($redirect)
			);
			return;
		}

		// Double-check that the user owns this session.
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		$ms = new \Components\Tools\Tables\Session($mwdb);
		if ($this->config->get('access-admin-session'))
		{
			$ms->load($sess);
		}
		else
		{
			$ms->load($sess, User::get('username'));
		}

		// Did we get a result form the database?
		if (!$ms->username)
		{
			App::redirect(
				Route::url($rediect)
			);
			return;
		}

		// Get plugins
		Plugin::import('mw', $ms->appname);

		// Trigger any events that need to be called before session stop
		Event::trigger('mw.onBeforeSessionStop', array($ms->appname));

		// Stop the session
		$status = $this->middleware("stop $sess", $output);
		if ($status == 0)
		{
			echo '<p>Stopping ' . $sess . '<br />';
			if (is_array($output))
			{
				foreach ($output as $line)
				{
					echo $line . "\n";
				}
			}
			else if (is_string($output))
			{
				echo $output . "\n";
			}
			echo '</p>'."\n";
		}

		// Trigger any events that need to be called after session stop
		Event::trigger('mw.onAfterSessionStop', array($ms->appname));

		// Take us back to the main page...
		if ($rtrn)
		{
			App::redirect(
				$rtrn
			);
		}
		else
		{
			App::redirect(
				Route::url($rediect)
			);
		}
	}

	/**
	 * Calculates the amount of disk space used
	 * Redirects to storage exceeded view if amount is past limit
	 *
	 * @param   string  $type      Soft/Hard
	 * @param   bool    $redirect  Redirect if over quota?
	 * @return  void
	 */
	private function _getDiskUsage($type = 'soft', $redirect = true)
	{
		// Check that the user is logged in
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}

		bcscale(6);

		$du = \Components\Tools\Helpers\Utils::getDiskUsage(User::get('username'));
		if (isset($du['space']))
		{
			if (strtolower($type) == 'hard')
			{
				$val = ($du['hardspace'] != 0) ? bcdiv($du['space'], $du['hardspace']) : 0;
			}
			else
			{
				$val = ($du['softspace'] != 0) ? bcdiv($du['space'], $du['softspace']) : 0;
			}
		}
		else
		{
			$val = 0;
		}
		$percent = round($val * 100);
		$percent = ($percent > 100) ? 100 : $percent;

		if (isset($du['softspace']))
		{
			$total = $du['softspace'] / 1024000000;
		}
		else
		{
			$total = 0;
		}

		$this->remaining = (isset($du['remaining'])) ? $du['remaining'] : 0;
		$this->percent = $percent;
		$this->total = $total;

		//if ($this->percent >= 100 && $this->remaining == 0) {
		if ($this->percent >= 100 && $redirect)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=storageexceeded')
			);
		}
	}

	/**
	 * Saves the name of a session (AJAX)
	 *
	 * @return     void
	 */
	public function renameTask()
	{
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		$id = Request::getInt('id', 0);
		$name = trim(Request::getVar('name', ''));

		if ($id && $name)
		{
			$ms = new \Components\Tools\Tables\Session($mwdb);
			$ms->load($id);
			$ms->sessname = $name;
			$ms->store();
		}

		echo $name;
	}

	/**
	 * Records the event of the current tool having been used
	 * This is used for the favorites list of the My Tools module
	 *
	 * @param      string  $app Name of app called
	 * @param      integer $uid User ID
	 * @return     void
	 */
	private function _recordUsage($app, $uid)
	{
		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php');

		$tool = new \Components\Tools\Tables\Version($this->database);
		$tool->loadFromName($app);

		$created = Date::toSql();

		// Get a list of all their recent tools
		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'recent.php');

		$rt = new \Components\Tools\Tables\Recent($this->database);
		$rows = $rt->getRecords($uid);

		$thisapp = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			if ($app == trim($rows[$i]->tool))
			{
				$thisapp = $rows[$i]->id;
			}
		}

		// Get the oldest entry. We may need this later.
		$oldest = end($rows);

		// Check if any recent tools are the same as the one just launched
		if ($thisapp)
		{
			// There was one, so just update its creation time
			$rt->id = $thisapp;
			$rt->uid = $uid;
			$rt->tool = $app;
			$rt->created = $created;
		}
		else
		{
			// Check if we've reached 5 recent tools or not
			if (count($rows) < 5)
			{
				// Still under 5, so insert a new record
				$rt->uid = $uid;
				$rt->tool = $app;
				$rt->created = $created;
			}
			else
			{
				// We reached the limit, so update the oldest entry effectively replacing it
				$rt->id = $oldest->id;
				$rt->uid = $uid;
				$rt->tool = $app;
				$rt->created = $created;
			}
		}

		if (!$rt->store())
		{
			App::abort(500, $rt->getError());
			return;
		}
	}

	/**
	 * Invoke the Python script to do real work.
	 *
	 * @param      string  $comm Parameter description (if any) ...
	 * @param      array   &$output Parameter description (if any) ...
	 * @return     integer Session ID
	 */
	public function middleware($comm, &$output)
	{
		$retval = true; // Assume success.

		$comm = escapeshellcmd($comm);

		$cmd = "/bin/sh " . dirname(dirname(__DIR__)) . "/scripts/mw $comm 2>&1 </dev/null";

		exec($cmd, $results, $status);

		// Check exec status
		if ($status != 0)
		{
			// Uh-oh. Something went wrong...
			$retval = false;
			$this->setError($results[0]);
		}

		if (is_array($results))
		{
			$results = implode('', $results);
		}
		$results = trim($results);

		try
		{
			$output = @json_decode($results);

			if ($output === null && json_last_error() !== JSON_ERROR_NONE)
			{
				throw new \Exception(Lang::txt('COM_TOOLS_ERROR_BAD_DATA'));
			}
		}
		catch (\Exception $e)
		{
			$output = new stdClass();

			// If it's a new session, catch the session number...
			if ($retval && preg_match("/^Session is ([0-9]+)/", $results, $sess))
			{
				$retval = $sess[1];
				$output->session = $sess[1];
			}
			else
			{
				$patterns = array(
					'id' => 'applet id=(["\'])(?:(?=(\\?))\2.)*?\1',
					'code' => 'code=(["\'])(?:(?=(\\?))\2.)*?\1',
					'archive' => 'archive=(["\'])(?:(?=(\\?))\2.)*?\1',
					'class' => 'class=(["\'])(?:(?=(\\?))\2.)*?\1',
					'height' => 'height=\"(\d+)\"',
					'width' => 'width=\"(\d+)\"',
					'height' => 'height=\"(\d+)\"',
					'port' => '<param name=\"PORT\" value=\"?(\d+)\"?>',
					'host' => '<param name=\"HOST\" value=\"?([^>]+)\"?>',
					'encpassword' => '<param name=\"ENCPASSWORD\" value=\"?([^>]+)\"?>',
					'name' => '<param name=\"name\" value=\"?([^>]+)\"?>',
					'connect' => '<param name=\"CONNECT\" value=\"?([^>]+)\"?>',
					'encoding' => '<param name=\"ENCODING\" value=\"?([^>]+)\"?>',
					'show_local_cursor' => '<param name=\"ShowLocalCursor\" value=\"?([^>]+)\"?>',
					'trust_all_vnc_certs' => '<param name=\"trustAllVncCerts\" value=\"?([^>]+)\"?>',
					'offer_relogin' => '<param name=\"Offer relogin\" value=\"?([^>]+)\"?>',
					'disable_ssl' => '<param name=\"DisableSSL\" value=\"?([^>]+)\"?>',
					'permissions' => '<param name=\"permissions\" value=\"?([^>]+)\"?>',
					'view_only' => '<param name=\"View Only\" value=\"?([^>]+)\"?>',
					'show_controls' => '<param name=\"Show Controls\" value=\"?([^>]+)\"?>',
					'debug' => '<param name=\"Debug\" value=\"?([^>]+)\"?>'
				);
				foreach ($patterns as $key => $pattern)
				{
					if (preg_match("/$pattern/i", $results, $param))
					{
						$output->$key = trim($param[1], '"');
					}
				}
			}
		}

		if ($output == null || (is_object($output) && count(get_object_vars($output)) <= 0))
		{
			$retval = false;
		}

		return $retval;
	}

	/**
	 * Authorization checks
	 *
	 * @param      string $assetType Asset type
	 * @param      string $assetId   Asset id to check against
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (!User::isGuest())
		{
			$asset  = $this->_option;
			if ($assetId)
			{
				$asset .= ($assetType != 'component') ? '.' . $assetType : '';
				$asset .= ($assetId) ? '.' . $assetId : '';
			}

			$at = '';
			if ($assetType != 'component')
			{
				$at .= '.' . $assetType;
			}

			// Admin
			$this->config->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
			// Permissions
			$this->config->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
		}
	}

	/**
	 * Check export controls
	 * Is the user in a country that has access to this tool?
	 *
	 * @param      string $exportcontrol Control [us, d1, pu]
	 * @return     boolean False if user does NOT have access
	 */
	private function _getToolExportControl($exportcontrol)
	{
		$exportcontrol = strtolower($exportcontrol);

		$ip = Request::ip();

		$country = \Hubzero\Geocode\Geocode::ipcountry($ip);

		if (empty($country) && in_array($exportcontrol, array('us', 'd1', 'pu')))
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_ACCESS_DENIED_EXPORT_UNKNOWN'));
			Log::debug("mw::_getToolExportControl($exportcontrol) FAILED location export control check");
			return false;
		}

		if (\Hubzero\Geocode\Geocode::is_e1nation(\Hubzero\Geocode\Geocode::ipcountry($ip)))
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_ACCESS_DENIED_EXPORT_E1'));
			Log::debug("mw::_getToolExportControl($exportcontrol) FAILED E1 export control check");
			return false;
		}

		switch ($exportcontrol)
		{
			case 'us':
				if (\Hubzero\Geocode\Geocode::ipcountry($ip) != 'us')
				{
					$this->setError(Lang::txt('COM_TOOLS_ERROR_ACCESS_DENIED_EXPORT_USA_ONLY'));
					Log::debug("mw::_getToolExportControl($exportcontrol) FAILED US export control check");
					return false;
				}
			break;

			case 'd1':
				if (\Hubzero\Geocode\Geocode::is_d1nation(\Hubzero\Geocode\Geocode::ipcountry($ip)))
				{
					$this->setError(Lang::txt('COM_TOOLS_ERROR_ACCESS_DENIED_EXPORT_LICENSE'));
					Log::debug("mw::_getToolExportControl($exportcontrol) FAILED D1 export control check");
					return false;
				}
			break;

			case 'pu':
				if (!\Hubzero\Geocode\Geocode::is_iplocation($ip, $exportcontrol))
				{
					$this->setError(Lang::txt('COM_TOOLS_ERROR_ACCESS_DENIED_EXPORT_PURDUE_ONLY'));
					Log::debug("mw::_getToolExportControl($exportControl) FAILED PURDUE export control check");
					return false;
				}
			break;
		}

		return true;
	}

	/**
	 * Get the access level for this user and tool
	 *
	 * @param      string $tool  Tool name
	 * @param      string $login Username
	 * @return     boolean True if the user has access
	 */
	private function _getToolAccess($tool, $login='')
	{
		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'tool.php');
		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'group.php');
		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php');

		// Ensure we have a tool
		if (!$tool)
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_TOOL_NOT_FOUND'));
			Log::debug("mw::_getToolAccess($tool,$login) FAILED null tool check");
			return false;
		}

		// Ensure we have a login
		if ($login == '')
		{
			$login = User::get('username');
			if ($login == '')
			{
				Log::debug("mw::_getToolAccess($tool,$login) FAILED null user check");
				return false;
			}
		}

		$tv = new \Components\Tools\Tables\Version($this->database);

		$tv->loadFromInstance($tool);

		if (empty($tv->id))
		{
			Log::debug("mw::_getToolAccess($tool,$login) FAILED null tool version check");
			return false;
		}

		$tg = new \Components\Tools\Tables\Group($this->database);
		$this->database->setQuery("SELECT * FROM " . $tg->getTableName() . " WHERE toolid=" . $tv->toolid);
		$toolgroups = $this->database->loadObjectList();
		if (empty($toolgroups))
		{
			//Log::debug("mw::_getToolAccess($tool,$login) WARNING: no tool member groups");
		}

		$xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'members');
		if (empty($xgroups))
		{
			//Log::debug("mw::_getToolAccess($tool,$login) WARNING: user not in any groups");
		}

		// Check if the user is in any groups for this app
		$ingroup = false;
		$groups = array();
		$indevgroup = false;
		if ($xgroups)
		{
			foreach ($xgroups as $xgroup)
			{
				$groups[] = $xgroup->cn;
			}
			if ($toolgroups)
			{
				foreach ($toolgroups as $toolgroup)
				{
					if (in_array($toolgroup->cn, $groups))
					{
						$ingroup = true;
						if ($toolgroup->role == 1)
						{
							$indevgroup = true;
						}
					}
				}
			}
		}

		$admin = false;
		$ctconfig = Component::params('com_tools');
		if ($ctconfig->get('admingroup') != '' && in_array($ctconfig->get('admingroup'), $groups))
		{
			$admin = true;
		}

		$exportAllowed = $this->_getToolExportControl($tv->exportControl);
		$tisPublished = ($tv->state == 1);
		$tisDev = ($tv->state == 3);
		$tisGroupControlled = ($tv->toolaccess == '@GROUP');

		if ($tisDev)
		{
			if ($indevgroup)
			{
				//Log::debug("mw::_getToolAccess($tool,$login): DEV TOOL ACCESS GRANTED (USER IN DEVELOPMENT GROUP)");
				return true;
			}
			else if ($admin)
			{
				//Log::debug("mw::_getToolAccess($tool,$login): DEV TOOL ACCESS GRANTED (USER IN ADMIN GROUP)");
				return true;
			}
			else
			{
				Log::debug("mw::_getToolAccess($tool,$login): DEV TOOL ACCESS DENIED (USER NOT IN DEVELOPMENT OR ADMIN GROUPS)");
				$this->setError(Lang::txt('COM_TOOLS_ERROR_ACCESS_DENIED_DEV_GROUP'));
				return false;
			}
		}
		else if ($tisPublished)
		{
			if ($tisGroupControlled) {
				if ($ingroup)
				{
					//Log::debug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN ACCESS GROUP)");
					return true;
				}
				else if ($admin)
				{
					//Log::debug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN ADMIN GROUP)");
					return true;
				}
				else
				{
					Log::debug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS DENIED (USER NOT IN ACCESS OR ADMIN GROUPS)");
					$this->setError(Lang::txt('COM_TOOLS_ERROR_ACCESS_DENIED_ACCESS_GROUP'));
					return false;
				}
			}
			else
			{
				if (!$exportAllowed)
				{
					Log::debug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS DENIED (EXPORT DENIED)");
					return false;
				}
				else if ($admin)
				{
					//Log::debug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN ADMIN GROUP)");
					return true;
				}
				else if ($indevgroup)
				{
					//Log::debug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN DEVELOPMENT GROUP)");
					return true;
				}
				else
				{
					//Log::debug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED");
					return true;
				}
			}
		}
		else
		{
			Log::debug("mw::_getToolAccess($tool,$login): UNPUBLISHED TOOL ACCESS DENIED (TOOL NOT PUBLISHED)");
			$this->setError(Lang::txt('COM_TOOLS_ERROR_ACCESS_DENIED_VERSION_UNPUBLISHED'));
			return false;
		}

		return false;
	}
}

