<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.job.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.session.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.view.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.viewperm.php');

/**
 * Tools controller class for simulation sessions
 */
class ToolsControllerSessions extends Hubzero_Controller
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
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
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
			$pathway->addItem(
				$this->app->caption,
				'index.php?option=' . $this->_option . '&app=' . $appname
			);
			$pathway->addItem(
				JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task . '&app=' . $appname . '&version=' . $this->app->version
			);
		} 
		else 
		{
			if ($this->_task && $this->_task != 'tools') 
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
					JRoute::_('index.php?option=' . $this->_option . '&task=' . $this->_task)
				);
			}
		}
		if (is_object($session)) 
		{
			$pathway->addItem(
				$title,
				JRoute::_('index.php?option=' . $this->_option . '&tag=' . $lnk)
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
		$this->_title = JText::_(strtoupper($this->_option));
		if ($this->app && $this->app->name) 
		{
			$this->_title .= ': ' . $this->app->caption;
		}
		if ($this->_task && $this->_task != 'tools') 
		{
			$this->_title .= ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		if (is_object($session)) 
		{
			$title .= ': ';
		}
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
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
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task), 'server');
		}
		$this->setRedirect(
			JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn))
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

		$this->_getStyles($this->_option, 'assets/css/tools.css');

		// Instantiate the view
		$this->view->title = $this->_title;

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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

		$this->_getStyles($this->_option, 'assets/css/tools.css');

		// Instantiate the view
		$this->view->title = $this->_title;

		$this->view->badparams = $badparams;

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
		if ($this->juser->get('guest')) 
		{
			$this->loginTask();
			return;
		}

		// Build the page title
		$title  = JText::_('Members');
		$title .= ': ' . JText::_('View');
		$title .= ': ' . stripslashes($this->juser->get('name'));
		$title .= ': ' . JText::_(strtoupper($this->_option . '_' . $this->_task));

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle($title);

		$this->_getStyles($this->_option, 'assets/css/tools.css');

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_('Members'), 
				'index.php?option=com_members'
			);
		}
		$pathway->addItem(
			stripslashes($this->juser->get('name')), 
			'index.php?option=com_members&id=' . $this->juser->get('id')
		);
		$pathway->addItem(
			JText::_(strtoupper($this->_option . '_' . $this->_task)), 
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task
		);

		// Get the middleware database
		$mwdb =& ToolsHelperUtils::getMWDBO();

		// Get the user's sessions
		$ms = new MwSession($mwdb);
		$sessions = $ms->getRecords($this->juser->get('username'), '', false);

		$this->view->sessions = $sessions;
		if ($this->config->get('access-manage-session')) 
		{
			$this->view->allsessions = $ms->getRecords($this->juser->get('username'), '', $this->config->get('access-manage-session'));
		}
		$this->view->active = JRequest::getVar('active', '');
		$this->view->config = $this->config;
		
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		
		$this->view->display();
	}

	function normalize_path($path, $isFile = false) 
	{
		if (!isset($path[0]) || $path[0] != '/')
			return false;

		$parts = explode('/', $path);

		$result = array();

		foreach($parts as $part) 
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
				return false;
		}

		return "/" . implode('/', $result) . ($isFile ? '' : '/');

	}

	/**
	 * Invoke a tool session
	 * 
	 * @return     void
	 */
	public function invokeTask()
	{
		// Check that the user is logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask();
			return;
		}

		ximport('Hubzero_Environment');

		$params = JRequest::getString('params','','default',JREQUEST_ALLOWRAW);

		if (!empty($params))
		{
			$params_whitelist = explode(',',$this->config->get('params_whitelist',''));
	
			$separator = "\r\n";
	
			$line = trim( strtok($params, $separator) );
	
			$verified_params = array();
	
			while ($line !== false) 
			{
				$re = "/\s*(directory|file)\s*(?:\:|\(\s*(.*?)\s*\)\s*:)\s*(.*?)\s*$/";
				
				if (preg_match($re, $line, $matches) !== false)
				{
					$type = $matches[1];
					$key  = $matches[2];
					$value = $matches[3];
	
					// Replace ~/ prefix with user's home directory
					if (strncmp($value,"~/",2) === 0)
					{
						$xprofile = Hubzero_User_Profile::getInstance($this->juser->get('id'));
		
						$homeDirectory = rtrim($xprofile->get('homeDirectory'),"/");
		
						if (!isset($homeDirectory[0]) || $homeDirectory[0] !== '/')
						{
							break;
						}
	
						$value = substr_replace($value,$homeDirectory,0,1);
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
	
					// Fail if $value isn't prefixed with a whitelisted directory
					foreach($params_whitelist as $wl)
					{
						$wl = rtrim($wl,'/') . '/'; 	// make sure we compare against a full path element
	
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
						$verified_params[] = $type . "(" . $key . "):" .$value;
					}
					else
					{
						$verified_params[] = $type . ":" . $value;
					}
				} 
				else if (!empty($line)) // Fail if unrecognized non-empty parameter line
				{
					break;
				}
	
				$line = strtok( $separator );  // Get next line
			}
	
			if ($line !== false)
			{
				$this->badparamsTask($params);
				return;
			}
		}
		
		// Incoming
		$app = new stdClass;
		$app->name    = trim(str_replace(':', '-', JRequest::getVar('app', '')));
		//$app->number  = 0;
		$app->version = JRequest::getVar('version', 'default');

		// Get the user's IP address
		$app->ip      = Hubzero_Environment::ipAddress();

		//$xlog->logDebug("mw::invoke URL: $url : " . $app->name . " by " . $this->juser->get('username') . " from " . $app->ip);
		//$xlog->logDebug("mw::invoke REFERER:" . (array_key_exists('HTTP_REFERER',$_SERVER)) ? $_SERVER['HTTP_REFERER'] : 'none');

		// Make sure we have an app to invoke
		if (!$app->name) 
		{
			$this->setRedirect(
				JRoute::_($this->config->get('stopRedirect', 'index.php?option=com_members&task=myaccount'))
			);
			return;
		}

		// Get the parent toolname (appname without any revision number "_r423")
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $this->_option . DS . 'tables' . DS . 'version.php');
		$tv = new ToolVersion($this->database);

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
		$hasaccess = $this->_getToolAccess($app->name);
		//$status2 = ($hasaccess) ? "PASSED" : "FAILED";

		//$xlog->logDebug("mw::invoke " . $app->name . " by " . $this->juser->get('username') . " from " . $app->ip . " _getToolAccess " . $status2);

		if ($this->getError()) 
		{
			echo '<!-- ' . $this->getError() . ' -->';
		}
		if (!$hasaccess) 
		{
			//$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=accessdenied');
			$this->app = $app;
			$this->accessdeniedTask();
			return;
		}

		// Log the launch attempt
		$this->_recordUsage($app->toolname, $this->juser->get('id'));

		// Get the middleware database
		$mwdb =& ToolsHelperUtils::getMWDBO();

		// Find out how many sessions the user is running.
		$ms = new MwSession($mwdb);
		$jobs = $ms->getCount($this->juser->get('username'));

		// Find out how many sessions the user is ALLOWED to run.
		$xprofile =& Hubzero_Factory::getProfile();
		$remain = $xprofile->get('jobsAllowed') - $jobs;

		// Have they reached their session quota?
		if ($remain <= 0) 
		{
			$this->quotaexceededTask();
			return;
		}

		if ($this->config->get('warn_multiples', 0) && !JRequest::getInt('newinstance', 0))
		{
			$sessions = $ms->getRecords($this->juser->get('username'), $app->name, false);
			if ($sessions && count($sessions) > 0)
			{
				$this->view->setLayout('list');
				$this->view->app = $app;
				$this->view->config   = $this->config;
				$this->view->sessions = $sessions;

				if ($this->getError()) 
				{
					foreach ($this->getErrors() as $error)
					{
						$view->setError($error);
					}
				}
				$this->view->display();
				return;
			}
		}

		// Get their disk space usage
		$this->_getDiskUsage('hard');  // Check their hardspace limit instead of the softspace
		$this->_redirect = '';

		$app->percent = 0;
		if ($this->config->get('show_storage', 1)) 
		{
			$app->percent = $this->percent;
		}
		if ($this->percent >= 100) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=storage')
			);
			return;
		}

		// Get plugins
		JPluginHelper::importPlugin('mw', $app->toolname);
		$dispatcher =& JDispatcher::getInstance();

		// Trigger any events that need to be called before session invoke
		$dispatcher->trigger('onBeforeSessionInvoke', array($app->toolname, $app->version));

		$toolparams = '';

		if (!empty($params))
		{
			$toolparams = " params=" . rawurlencode($params) . " ";
		}

		// We've passed all checks so let's actually start the session
		$status = $this->middleware("start user=" . $this->juser->get('username') . " ip=" . $app->ip . " app=" . $app->name . " version=" . $app->version . $toolparams, $output);
		if ($this->getError())
		{
			//JError::raiseError(500, $this->getError());
			//return;
			$this->setRedirect(
				JRoute::_($this->config->get('stopRedirect', 'index.php?option=com_members&task=myaccount')),
				JText::_('Failed to invoke session'),
				'error'
			);
			return;
		}
		$app->sess = !empty($output->session) ? $output->session : '';

		// Trigger any events that need to be called after session invoke
		$dispatcher->trigger('onAfterSessionInvoke', array($app->toolname, $app->version));

		// Get a count of the number of sessions of this specific tool
		$appcount = $ms->getCount($this->juser->get('username'), $app->name);
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

		$rtrn = JRequest::getVar('return', '');

		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&app=' . $app->toolname . '&task=session&sess=' . $app->sess . '&return=' . $rtrn, false)
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
		if ($this->juser->get('guest')) 
		{
			$this->loginTask();
			return;
		}

		$mwdb =& ToolsHelperUtils::getMWDBO();

		// Incoming
		$sess     = JRequest::getVar('sess', '');
		$username = trim(JRequest::getVar('username', ''));
		$group    = JRequest::getInt('group', 0);
		$readonly = JRequest::getVar('readonly', '');
		$no_html  = JRequest::getInt('no_html', 0);
		
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
			ximport('Hubzero_Group');
			$hg = Hubzero_Group::getInstance( $group );
			$members = $hg->get('members');
			
			//merge group members with any passed in username field
			$users = array_values(array_unique(array_merge($users, $members)));
			
			//remove this user
			$isUserInArray = array_search($this->juser->get('id'), $users);
			if(isset($isUserInArray))
			{
				unset($users[$isUserInArray]);
			}
			
			//fix array keys
			$users = array_values(array_filter($users));
		}
		
		// Double-check that the user can access this session.
		$ms = new MwSession($mwdb);
		$row = $ms->checkSession($sess, $this->juser->get('username'));

		// Ensure we found an active session
		if (!$row->sesstoken) 
		{
			JError::raiseError(500, JText::_('MW_ERROR_SESSION_NOT_FOUND') . ': ' . $sess);
			return;
		}
		
		//$row = $rows[0];
		$owner = $row->viewuser;
		
		if ($readonly != 'Yes') 
		{
			$readonly = 'No';
		}

		$mv = new MwViewperm($mwdb);
		$rows = $mv->loadViewperm($sess, $owner);
		if (count($rows) != 1) 
		{
			JError::raiseError(500, JText::sprintf('Unable to get entry for %s, %s', $sess, $owner));
			break;
		}
		foreach ($users as $user)
		{
			// Check for invalid characters
			if (!preg_match("#^[0-9a-zA-Z]+[_0-9a-zA-Z]*$#i", $user)) 
			{
				$this->setError(JText::_('MW_ERROR_INVALID_USERNAME') . ': ' . $user);
				continue;
			}

			// Check that the user exist
			$zuser =& JUser::getInstance($user);
			if (!$zuser || !is_object($zuser) || !$zuser->get('id')) 
			{
				$this->setError(JText::_('MW_ERROR_INVALID_USERNAME') . ': ' . $user);
				continue;
			}
			
			//load current view perm
			$mwViewperm = new MwViewperm($mwdb);
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
				JError::raiseError(500, $mwViewperm->getError());
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
		if ($this->juser->get('guest')) 
		{
			$this->loginTask();
			return;
		}

		// Needed objects
		$mwdb =& ToolsHelperUtils::getMWDBO();

		// Incoming
		$sess = JRequest::getVar('sess', '');
		$user = JRequest::getVar('username', '');
		$app = JRequest::getVar('app', '');

		// If a username is given, check that the user owns this session.
		if ($user != '') 
		{
			$ms = new MwSession($mwdb);
			$ms->load($sess, $this->juser->get('username'));

			if (!$ms->sesstoken) 
			{
				JError::raiseError(500, JText::_('COM_TOOLS_ERROR_SESSION_NOT_FOUND') . ': ' . $sess);
				return;
			}
		} 
		else 
		{
			// Otherwise, assume that the user wants to disconnect a session that's been shared with them.
			$user = $this->juser->get('username');
		}

		// Delete the viewperm
		$mv = new MwViewperm($mwdb);
		$mv->deleteViewperm($sess, $user);

		if ($user == $this->juser->get('username')) 
		{
			// Take us back to the main page...
			$this->setRedirect(
				JRoute::_($this->config->get('stopRedirect', 'index.php?option=com_members&task=myaccount'))
			);
			return;
		}

		// Drop through and re-view the session...
		//$this->viewTask();
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&app=' . $app . '&task=session&sess=' . $sess )
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
		if ($this->juser->get('guest')) 
		{
			$this->loginTask();
			return;
		}

		// Incoming
		$app = new stdClass(); //array();
		$app->sess = JRequest::getInt('sess', 0);

		// Make sure we have an app to invoke
		if (!$app->sess) 
		{
			$this->setRedirect(
				JRoute::_($this->config->get('stopRedirect', 'index.php?option=com_members&task=myaccount')),
				JText::_('COM_TOOLS_ERROR_SESSION_NOT_FOUND'),
				'error'
			);
			return;
		}

		$this->view->rtrn = JRequest::getVar('return', '');

		// Get the user's IP address
		ximport('Hubzero_Environment');
		$app->ip = Hubzero_Environment::ipAddress(); //JRequest::getVar('REMOTE_ADDR', '', 'server');

		// Double-check that the user can view this session.
		$mwdb =& ToolsHelperUtils::getMWDBO();

		$ms = new MwSession($mwdb);
		$row = $ms->loadSession($app->sess, $this->config->get('access-manage-session'));

		if (!is_object($row) || !$row->appname) 
		{
			JError::raiseError(500, JText::_('COM_TOOLS_ERROR_SESSION_NOT_FOUND') . ': ' . $app->sess);
			return;
		}

		if (strstr($row->appname, '_')) 
		{
			$v = substr(strrchr($row->appname, '_'), 1);
			$v = str_replace('r', '', $v);
			JRequest::setVar('version', $v);
		}

		// Get parent tool name - to write correct links
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $this->_option . DS . 'tables' . DS . 'version.php');
		$tv = new ToolVersion($this->database);
		$parent_toolname = $tv->getToolname($row->appname);
		$toolname = ($parent_toolname) ? $parent_toolname : $row->appname;

		// Get the tool's name
		$tv->loadFromInstance($row->appname);
		$app->title = stripslashes($tv->title);

		$paramClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramClass = 'JRegistry';
		}
		$app->params = new $paramClass($tv->params);

		// Ensure we found an active session
		if (!$row->sesstoken) 
		{
			JError::raiseError(500, JText::_('MW_ERROR_SESSION_NOT_FOUND') . ': ' . $app->sess . '. ' . JText::_('MW_SESSION_NOT_FOUND_EXPLANATION'));
			return;
		}

		// Get their disk space usage
		$app->percent = 0;
		if ($this->config->get('show_storage')) 
		{
			$this->_getDiskUsage();
			$app->percent = $this->percent;
		}

		// Build the view command
		if ($this->config->get('access-manage-session')) 
		{
			$command = "view user=" . $row->username . " ip=" . $app->ip . " sess=" . $app->sess;
		} 
		else 
		{
			$command = "view user=" . $this->juser->get('username') . " ip=" . $app->ip . " sess=" . $app->sess;
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
		JPluginHelper::importPlugin('mw', $app->name);
		$dispatcher =& JDispatcher::getInstance();

		// Trigger any events that need to be called before session start
		$dispatcher->trigger('onBeforeSessionStart', array($toolname, $tv->revision));

		// Call the view command
		$status = $this->middleware($command, $output);

		// Trigger any events that need to be called after session start
		$dispatcher->trigger('onAfterSessionStart', array($toolname, $tv->revision));

		// Set the layout
		$sublayout = strtolower(JRequest::getWord('layout', ''));
		$this->view->setLayout('session' . ($sublayout ? '_' . $sublayout : ''));

		// Set the page title
		$title  = JText::_('Resources').': '.JText::_('Tools');
		$title .= ($app->title) ? ': ' . $app->title : ': ' . $app->name;
		$title .= ': ' . JText::_('Session');
		$title .= ($app->caption) ? ': ' . $app->sess . ' "' . $app->caption . '"' : ': ' . $app->sess;

		$document =& JFactory::getDocument();
		$document->setTitle($title);

		// Set the breadcrumbs
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_('Resources'), 
				'index.php?option=com_resources'
			);
		}
		$pathway->addItem(
			JText::_('Tools'), 
			'index.php?option=com_resources&type=tools'
		);
		$pathway->addItem(
			$app->title, 
			'index.php?option=' . $this->_option . '&controller=' . $this->controller . '&app=' . $toolname
		);

		if ($this->_task) 
		{
			$t = ($app->caption) ? $app->sess . ' "' . $app->caption . '"' : $app->sess;
			$pathway->addItem(
				JText::sprintf('Session: %s', $t), 
				'index.php?option=' . $this->_option . '&controller=' . $this->controller . '&app=' . $toolname . '&task=session&sess=' . $app->sess
			);
		}
		
		//get users groups
		ximport('Hubzero_User_Helper');
		$this->view->mygroups = Hubzero_User_Helper::getGroups( $this->juser->get('id'), 'members', 1 );

		// Push styles to the document
		$this->_getStyles($this->_option, 'assets/css/tools.css');

		// Push scripts to the document
		$this->_getScripts('assets/js/' . $this->_controller);
		
		//add editable plugin
		ximport('Hubzero_Document');
		Hubzero_Document::addSystemScript('jquery.editable.min');

		$this->view->app      = $app;
		$this->view->config   = $this->config;
		$this->view->output   = $output;
		$this->view->toolname = $toolname;
		$this->view->total    = $this->total;

		// Get everyone sharing this session
		if ($app->sess) 
		{
			// Get the middleware database
			$mwdb =& ToolsHelperUtils::getMWDBO();

			// Load the viewperm
			$ms = new MwViewperm($mwdb);
			$this->view->shares = $ms->loadViewperm($app->sess);
		}

		// Set any error messages
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
		if ($this->juser->get('guest')) 
		{
			$this->loginTask();
			return;
		}

		// Incoming
		$sess = JRequest::getVar('sess', '');
		$rtrn = base64_decode(JRequest::getVar('return', '', 'method', 'base64'));

		$rediect = $this->config->get('stopRedirect', 'index.php?option=com_members&task=myaccount');

		// Ensure we have a session
		if (!$sess) 
		{
			$this->setRedirect(
				JRoute::_($redirect)
			);
			return;
		}

		// Double-check that the user owns this session.
		$mwdb =& ToolsHelperUtils::getMWDBO();

		$ms = new MwSession($mwdb);
		if ($this->config->get('access-admin-session')) 
		{
			$ms->load($sess);
		} 
		else 
		{
			$ms->load($sess, $this->juser->get('username'));
		}

		// Did we get a result form the database?
		if (!$ms->username) 
		{
			$this->setRedirect(
				JRoute::_($rediect)
			);
			return;
		}

		// Get plugins
		JPluginHelper::importPlugin('mw', $ms->appname);
		$dispatcher =& JDispatcher::getInstance();

		// Trigger any events that need to be called before session stop
		$dispatcher->trigger('onBeforeSessionStop', array($ms->appname));

		// Stop the session
		$status = $this->middleware("stop $sess", $output);
		if ($status == 0) 
		{
			echo '<p>Stopping ' . $sess . '<br />';
			foreach ($output as $line)
			{
				echo $line . "\n";
			}
			echo '</p>'."\n";
		}

		// Trigger any events that need to be called after session stop
		$dispatcher->trigger('onAfterSessionStop', array($ms->appname));

		// Take us back to the main page...
		if ($rtrn) 
		{
			$this->setRedirect(
				$rtrn
			);
		} 
		else 
		{
			$this->setRedirect(
				JRoute::_($rediect)
			);
		}
	}

	/**
	 * Calculates the amount of disk space used
	 * Redirects to storage exceeded view if amount is past limit
	 * 
	 * @param      string $type Soft/Hard
	 * @return     void
	 */
	private function _getDiskUsage($type='soft')
	{
		// Check that the user is logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask();
			return;
		}

		bcscale(6);

		$du = ToolsHelperUtils::getDiskUsage($this->juser->get('username'));
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
		if ($this->percent >= 100) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=storageexceeded')
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
		$mwdb =& ToolsHelperUtils::getMWDBO();

		$id = JRequest::getInt('id', 0);
		$name = trim(JRequest::getVar('name', ''));

		if ($id && $name) 
		{
			$ms = new MwSession($mwdb);
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
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $this->_option . DS . 'tables' . DS . 'version.php');

		$tool = new ToolVersion($this->database);
		$tool->loadFromName($app);

		$created = date('Y-m-d H:i:s', time());

		// Get a list of all their recent tools
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $this->_option . DS . 'tables' . DS . 'recent.php');

		$rt = new ToolRecent($this->database);
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
			JError::raiseError(500, $rt->getError());
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

		$cmd = "/bin/sh components/" . $this->_option . "/scripts/mw $comm 2>&1 </dev/null";

		exec($cmd, $results, $status);

		// Check exec status
		if ($status != 0) 
		{
			// Uh-oh. Something went wrong...
			$retval = false;
			$this->setError($results[0]);
		}

		$output = new stdClass();

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
					if (preg_match("/^<param name=\"ShowLocalCursor\" value=\"?(.+)\"?>/i", $line, $param))
					{
						$output->showlocalcursor = trim($param[1], '"');
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
		if (!$this->juser->get('guest')) 
		{
			if (version_compare(JVERSION, '1.6', 'ge'))
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
				$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
				$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
				// Permissions
				$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
				$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
				$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
				$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
				$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
			}
			else 
			{
				if ($this->juser->authorize($this->_option, 'manage'))
				{
					$this->config->set('access-manage-' . $assetType, true);
					$this->config->set('access-admin-' . $assetType, true);
					$this->config->set('access-create-' . $assetType, true);
					$this->config->set('access-delete-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
				}
			}
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
		$xlog =& Hubzero_Factory::getLogger();
		$exportcontrol = strtolower($exportcontrol);

		ximport('Hubzero_Environment');
		$ip = Hubzero_Environment::ipAddress();

		$country = Hubzero_Geo::ipcountry($ip);

		if (empty($country) && in_array($exportcontrol, array('us', 'd1', 'pu')))
		{
			$this->setError('This tool may not be accessed from your unknown current location due to export/license restrictions.');
			$xlog->logDebug("mw::_getToolExportControl($exportcontrol) FAILED location export control check");
			return false;
		}

		if (Hubzero_Geo::is_e1nation(Hubzero_Geo::ipcountry($ip))) 
		{
			$this->setError('This tool may not be accessed from your current location due to E1 export/license restrictions.');
			$xlog->logDebug("mw::_getToolExportControl($exportcontrol) FAILED E1 export control check");
			return false;
		}

		switch ($exportcontrol)
		{
			case 'us':
				if (Hubzero_Geo::ipcountry($ip) != 'us') 
				{
					$this->setError('This tool may only be accessed from within the U.S. due to export/licensing restrictions.');
					$xlog->logDebug("mw::_getToolExportControl($exportcontrol) FAILED US export control check");
					return false;
				}
			break;

			case 'd1':
				if (Hubzero_Geo::is_d1nation(Hubzero_Geo::ipcountry($ip))) 
				{
					$this->setError('This tool may not be accessed from your current location due to export/license restrictions.');
					$xlog->logDebug("mw::_getToolExportControl($exportcontrol) FAILED D1 export control check");
					return false;
				}
			break;

			case 'pu':
				if (!Hubzero_Geo::is_iplocation($ip, $exportcontrol)) 
				{
					$this->setError('This tool may only be accessed by authorized users while on the West Lafayette campus of Purdue University due to license restrictions.');
					$xlog->logDebug("mw::_getToolExportControl($exportControl) FAILED PURDUE export control check");
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
		ximport('Hubzero_User_Helper');
		ximport('Hubzero_Geo');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'tool.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'group.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');

		$xlog =& Hubzero_Factory::getLogger();

		// Ensure we have a tool
		if (!$tool) 
		{
			$this->setError('No tool provided.');
			$xlog->logDebug("mw::_getToolAccess($tool,$login) FAILED null tool check");
			return false;
		}

		// Ensure we have a login
		if ($login == '') 
		{
			$login = $this->juser->get('username');
			if ($login == '') 
			{
				$xlog->logDebug("mw::_getToolAccess($tool,$login) FAILED null user check");
				return false;
			}
		}

		$tv = new ToolVersion($this->database);
		$tv->loadFromInstance($tool);

		if (empty($tv)) 
		{
			$xlog->logDebug("mw::_getToolAccess($tool,$login) FAILED null tool version check");
			return false;
		}

		$tg = new ToolGroup($this->database);
		$this->database->setQuery("SELECT * FROM " . $tg->getTableName() . " WHERE toolid=" . $tv->toolid);
		$toolgroups = $this->database->loadObjectList();
		if (empty($toolgroups)) 
		{
			//$xlog->logDebug("mw::_getToolAccess($tool,$login) WARNING: no tool member groups");
		}

		$xgroups = Hubzero_User_Helper::getGroups($this->juser->get('id'), 'members');
		if (empty($xgroups)) 
		{
			//$xlog->logDebug("mw::_getToolAccess($tool,$login) WARNING: user not in any groups");
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
		$ctconfig =& JComponentHelper::getParams('com_tools');
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
				//$xlog->logDebug("mw::_getToolAccess($tool,$login): DEV TOOL ACCESS GRANTED (USER IN DEVELOPMENT GROUP)");
				return true;
			}
			else if ($admin) 
			{
				//$xlog->logDebug("mw::_getToolAccess($tool,$login): DEV TOOL ACCESS GRANTED (USER IN ADMIN GROUP)");
				return true;
			}
			else
			{
				$xlog->logDebug("mw::_getToolAccess($tool,$login): DEV TOOL ACCESS DENIED (USER NOT IN DEVELOPMENT OR ADMIN GROUPS)");
				$this->setError("The development version of this tool may only be accessed by members of it's development group.");
				return false;
			}
		}
		else if ($tisPublished) 
		{
			if ($tisGroupControlled) {
				if ($ingroup) 
				{
					//$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN ACCESS GROUP)");
					return true;
				}
				else if ($admin) 
				{
					//$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN ADMIN GROUP)");
					return true;
				}
				else 
				{
					$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS DENIED (USER NOT IN ACCESS OR ADMIN GROUPS)");
					$this->setError("This tool may only be accessed by members of it's access control groups.");
					return false;
				}
			}
			else 
			{
				if (!$exportAllowed) 
				{
					$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS DENIED (EXPORT DENIED)");
					return false;
				}
				else if ($admin) 
				{
					//$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN ADMIN GROUP)");
					return true;
				}
				else if ($indevgroup) 
				{
					//$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED (USER IN DEVELOPMENT GROUP)");
					return true;
				}
				else 
				{
					//$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS GRANTED");
					return true;
				}
			}
		}
		else 
		{
			$xlog->logDebug("mw::_getToolAccess($tool,$login): UNPUBLISHED TOOL ACCESS DENIED (TOOL NOT PUBLISHED)");
			$this->setError('This tool version is not published.');
			return false;
		}

		return false;
	}
}

