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

/**
 * Members controller class for profiles
 */
class MembersControllerProfiles extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		// Get the view
		$this->_view = strtolower(JRequest::getVar('view', 'members'));

		// Get The task
		$task = strtolower(JRequest::getVar('task', ''));

		$id = JRequest::getInt('id', 0);
		if ($id && !$task) 
		{
			JRequest::setVar('task', 'view');
		}

		$file = array_pop(explode('/', $_SERVER['REQUEST_URI']));

		if (substr(strtolower($file), 0, 5) == 'image' 
		 || substr(strtolower($file), 0, 4) == 'file') 
		{
			$this->setRedirect(
				JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_controller . '&task=donwload'), 'server')
			);
			return;
		}

		//$this->registerTask('__default', 'browse');
		$this->registerTask('promo-opt-out', 'incremOptOut');

		parent::execute();
	}

	/**
	 * Opt out of a promotion
	 * 
	 * @return     void
	 */
	public function incremOptOutTask()
	{
		$profile = Hubzero_User_Profile::getInstance($this->juser->get('id'));
		if (!$profile)
		{
			return;
		}

		require_once JPATH_BASE . '/administrator/components/com_register/tables/incremental.php';
		$ia = new ModIncrementalRegistrationAwards($profile);
		$ia->optOut();

		$this->setRedirect(
			JRoute::_('index.php?option=com_members&id=' . $profile->get('uidNumber') . '&active=profile'),
			JText::_('You have been successfully opted out of this promotion.'),
			'passed'
		);
		return;
	}

	/**
	 * Return results for autocompleter
	 * 
	 * @return     string JSON
	 */
	public function autocompleteTask()
	{
		if ($this->juser->get('guest')) 
		{
			return;
		}

		$restrict = '';
		/*if ($this->_authorize() !== 'admin')
		{
			$profile = Hubzero_User_Profile::getInstance($this->juser->get('id'));
			$xgroups = $profile->getGroups('all');
			$usersgroups = array();
			if (!empty($xgroups)) 
			{
				foreach ($xgroups as $group)
				{
					if ($group->regconfirmed) 
					{
						$usersgroups[] = $group->gidNumber;
					}
				}
			}
			
			$members = null;
			if (!empty($usersgroups))
			{
				$query = "SELECT DISTINCT uidNumber 
						FROM #__xgroups_members
						WHERE gidNumber IN (" . implode(',', $usersgroups) . ")";

				$this->database->setQuery($query);
				$members = $this->database->loadResultArray();
			}

			if (!$members || empty($members))
			{
				$members = array($this->juser->get('id'));
			}
			$restrict = " AND (xp.public=1 OR xp.uidNumber IN (" . implode(',', $members) . "))";
		}*/

		$filters = array();
		$filters['limit']  = 20;
		$filters['start']  = 0;
		$filters['search'] = strtolower(trim(JRequest::getString('value', '')));

		// Fetch results
		/*$query = "SELECT u.id, u.name, u.username 
				FROM #__users AS u 
				WHERE LOWER(u.name) LIKE '%".$filters['search']."%' 
				OR LOWER(u.username) LIKE '%".$filters['search']."%'
				OR LOWER(u.email) LIKE '%".$filters['search']."%'
				ORDER BY u.name ASC";*/
		$query = "SELECT xp.uidNumber, xp.name, xp.username, xp.organization, xp.picture, xp.public 
				FROM #__xprofiles AS xp 
				INNER JOIN #__users u ON u.id = xp.uidNumber AND u.block = 0
				WHERE LOWER(xp.name) LIKE '%" . $this->database->getEscaped($filters['search']) . "%' AND xp.emailConfirmed>0 $restrict 
				ORDER BY xp.name ASC";

		$this->database->setQuery($query);
		$rows = $this->database->loadObjectList();

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0) 
		{
			ximport('Hubzero_User_Profile_Helper');

			$default = DS . trim($this->config->get('defaultpic', '/components/com_members/images/profile.gif'), DS);
			$default = Hubzero_User_Profile_Helper::thumbit($default);
			foreach ($rows as $row)
			{
				$picture = $default;

				$name = str_replace("\n", '', stripslashes(trim($row->name)));
				$name = str_replace("\r", '', $name);
				$name = str_replace('\\', '', $name);

				if ($row->public && $row->picture)
				{
					$thumb  = DS . trim($this->config->get('webpath', '/site/members'), DS);
					$thumb .= DS . Hubzero_User_Profile_Helper::niceidformat($row->uidNumber);
					$thumb .= DS . ltrim($row->picture, DS);
					$thumb = Hubzero_User_Profile_Helper::thumbit($thumb);

					if (file_exists(JPATH_ROOT . $thumb))
					{
						$picture = $thumb;
					}
				}

				$obj = array();
				$obj['id']      = $row->uidNumber;
				$obj['name']    = $name;
				$obj['org']     = ($row->public ? $row->organization : '');
				$obj['picture'] = $picture;

				$json[] = $obj;
			}
		}

		echo json_encode($json);
	}

	/**
	 * Display main page
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Include some needed styles and scripts
		$this->_getStyles('', 'introduction.css', true); // component, stylesheet name, look in media system dir
		$this->_getStyles();

		$this->view->title = JText::_('MEMBERS');

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle($this->view->title);

		// Set the document pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)), 
				'index.php?option=' . $this->_option
			);
		}

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->juser = $this->juser;

		$this->view->display();
	}

	/**
	 * Display a list of members
	 * 
	 * @return     void
	 */
	public function browseTask()
	{
		// Include some needed styles and scripts
		$this->_getStyles();
		$this->_getScripts('assets/js/' . $this->_name);

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']  = JRequest::getVar('limit', $jconfig->getValue('config.list_limit'), 'request');
		$this->view->filters['start']  = JRequest::getInt('limitstart', 0, 'get');
		$this->view->filters['show']   = strtolower(JRequest::getWord('show', $this->_view));
		$this->view->filters['sortby'] = strtolower(JRequest::getWord('sortby', 'name'));
		$this->view->filters['search'] = JRequest::getVar('search', '');
		$this->view->filters['index']  = JRequest::getWord('index', '');

		// Build the page title
		if ($this->view->filters['show'] == 'contributors') 
		{
			$this->view->title = JText::_('CONTRIBUTORS');
			$this->view->filters['sortby'] = strtolower(JRequest::getWord('sortby', 'contributions'));
		} 
		else 
		{
			$this->view->title = JText::_('MEMBERS');
		}
		$this->view->title .= ($this->_task) ? ': ' . JText::_(strtoupper($this->_task)) : '';

		if (!in_array($this->view->filters['sortby'], array('name', 'organization', 'contributions')))
		{
			$this->view->filters['sortby'] = ($this->view->filters['show'] == 'contributors') ? 'contributions' : 'name';
		}

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle($this->view->title);

		// Set the document pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)), 
				'index.php?option=' . $this->_option
			);
		}
		// Was a specific index (letter) set?
		if ($this->view->filters['index']) 
		{
			// Add to the pathway
			$pathway->addItem(
				strtoupper($this->view->filters['index']), 
				'index.php?option=' . $this->_option . '&index=' . $this->view->filters['index']
			);
		}

		// Check authorization
		$this->view->authorized = $this->_authorize();
		if ($this->view->authorized === 'admin') 
		{
			$admin = true;
		} 
		else 
		{
			$admin = false;
		}

		$this->view->filters['authorized'] = $this->view->authorized;

		// Initiate a contributor object
		$c = new MembersProfile($this->database);

		$cache =& JFactory::getCache('callback');
		$cache->setCaching(1);
		$cache->setLifeTime(intval($this->config->get('cache_time', 15)));
		$stats = $cache->call(array($this, 'stats'));

		// Get record count of ALL members
		$this->view->total_members = $stats->total_members; //$c->getCount(array('show' => ''), true);

		// Get record count of ALL members
		$this->view->total_public_members = $stats->total_public_members; //$c->getCount(array('show' => '', 'authorized' => false), false);

		// Get record count
		$this->view->total = $c->getCount($this->view->filters, $admin);

		// Get records
		$this->view->rows = $c->getRecords($this->view->filters, $admin);
		
		//get newly registered members (past day)
		//$this->database->setQuery("SELECT COUNT(*) FROM #__xprofiles WHERE registerDate > '" . date("Y-m-d H:i:s", strtotime('-1 DAY')) . "'");
		$this->view->past_day_members = $stats->past_day_members; //$this->database->loadResult();
		
		//get newly registered members (past month)
		//$this->database->setQuery("SELECT COUNT(*) FROM #__xprofiles WHERE registerDate > '" . date("Y-m-d H:i:s", strtotime('-1 MONTH')) . "'");
		$this->view->past_month_members = $stats->past_month_members; //$this->database->loadResult();
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Instantiate the view
		$this->view->config = $this->config;
		$this->view->view = $this->_view;

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
	 * Calculate stats
	 * 
	 * @return     object
	 */
	public function stats()
	{
		$c = new MembersProfile($this->database);

		$stats = new stdClass;

		// Get record count of ALL members
		$stats->total_members = $c->getCount(array('show' => ''), true);

		// Get record count of ALL members
		$stats->total_public_members = $c->getCount(array('show' => '', 'authorized' => false), false);

		//get newly registered members (past day)
		$this->database->setQuery("SELECT COUNT(*) FROM #__xprofiles WHERE registerDate > '" . date("Y-m-d H:i:s", strtotime('-1 DAY')) . "'");
		$stats->past_day_members = $this->database->loadResult();

		//get newly registered members (past month)
		$this->database->setQuery("SELECT COUNT(*) FROM #__xprofiles WHERE registerDate > '" . date("Y-m-d H:i:s", strtotime('-1 MONTH')) . "'");
		$stats->past_month_members = $this->database->loadResult();

		return $stats;
	}

	/**
	 * A shortcut task for displaying a logged-in user's account page
	 * 
	 * @return     void
	 */
	public function myaccountTask()
	{
		if ($this->juser->get('guest')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->_option . '&task=myaccount'))),
				JText::_('You must be a logged in to access this area.'),
				'warning'
			);
			return;
		} 

		JRequest::setVar('id', $this->juser->get('id'));
		$this->viewTask();
		return;
	}

	/**
	 * Display a user profile
	 * 
	 * @return     void
	 */
	public function viewTask()
	{
		$this->view->setLayout('view');

		// Build the page title 
		$this->view->title  = JText::_(strtoupper($this->_name));
		$this->view->title .= ($this->_task) ? ': ' . JText::_(strtoupper($this->_task)) : '';

		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)), 
				'index.php?option=' . $this->_option
			);
		}

		// Include some needed styles and scripts
		$this->_getStyles();
		$this->_getScripts('assets/js/' . $this->_name);

		// Incoming
		$id = JRequest::getInt('id', 0);
		$tab = JRequest::getVar('active', 'dashboard');  // The active tab (section)

		// Get plugins
		JPluginHelper::importPlugin('members');
		$dispatcher =& JDispatcher::getInstance();

		// Ensure we have an ID
		if (!$id) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_task)), 
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
			JError::raiseError(404, JText::_('MEMBERS_NO_ID'));
			return;
		}

		// Check administrative access
		$this->view->authorized = $this->_authorize($id);

		// Get the member's info
		$profile = Hubzero_User_Profile::getInstance($id);

		// Ensure we have a member
		if (!is_object($profile) || (!$profile->get('name') && !$profile->get('surname'))) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_task)), 
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
			JError::raiseError(404, JText::_('MEMBERS_NOT_FOUND'));
			return;
		}

		// Check subscription to Employer Services
		//   NOTE: This must occur after the initial plugins import and 
		//   do not specifically call JPluginHelper::importPlugin('members', 'resume');
		//   Doing so can have negative affects.
		if ($this->config->get('employeraccess') && $tab == 'resume') 
		{
			$checkemp   = $dispatcher->trigger('isEmployer', array());
			$emp        = is_array($checkemp) ? $checkemp[0] : 0;
			$this->view->authorized = $emp ? 1 : $this->view->authorized;
		}

		// Check if the profile is public/private and the user has access
		if ($profile->get('public') != 1 && !$this->view->authorized) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_task)), 
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
			JError::raiseError(403, JText::_('MEMBERS_NOT_PUBLIC'));
			return;
		}

		// Check for name
		if (!$profile->get('name')) 
		{
			$name  = $profile->get('givenName') . ' ';
			$name .= ($profile->get('middleName')) ? $profile->get('middleName') . ' ' : '';
			$name .= $profile->get('surname');
			$profile->set('name', $name);
		}

		// Trigger the functions that return the areas we'll be using
		$this->view->cats = $dispatcher->trigger('onMembersAreas', array($this->juser, $profile));

		$available = array();
		foreach ($this->view->cats as $cat)
		{
			$name = key($cat);
			if ($name != '') 
			{
				$available[] = $name;
			}
		}

		if ($tab != 'profile' && !in_array($tab, $available)) 
		{
			$tab = 'profile';
		}

		// Get the sections
		$this->view->sections = $dispatcher->trigger('onMembers', array($this->juser, $profile, $this->_option, array($tab)));

		// Merge profile params (take precendence) with the site config
		//  ** What is this for?
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}
		$rparams = new $paramsClass($profile->get('params'));
		$params = $this->config;
		$params->merge($rparams);

		// Set the page title
		$document = JFactory::getDocument();
	    $document->setTitle($this->view->title . ': ' . stripslashes($profile->get('name')));

		// Set the pathway
		$pathway->addItem(
			stripslashes($profile->get('name')), 
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber')
		);

		// Output HTML
		$this->view->config = $this->config;
		$this->view->tab = $tab;
		$this->view->profile = $profile;
		$this->view->overwrite_content = '';

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
	 * Show a form for changing user password
	 * 
	 * @return     void
	 */
	public function changepasswordTask()
	{
		if (!isset( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == 'off')
		{
			JFactory::getApplication()->redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			die('insecure connection and redirection failed');
		}

		ximport('Hubzero_User_Password');

		// Set the page title
		$title  = JText::_(strtoupper($this->_name));
		$title .= ($this->_task) ? ': ' . JText::_(strtoupper($this->_task)) : '';

		$document =& JFactory::getDocument();
		$document->setTitle($title);

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)), 
				'index.php?option=' . $this->_option
			);
		}

		// Incoming
		$id = JRequest::getInt('id', 0);

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_controller . '&task=changepassword'), 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn))
			);
			return;
		}

		if (!$id) 
		{
			$id = $this->juser->get('id');
		}

		// Ensure we have an ID
		if (!$id) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_task)), 
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			JError::raiseError(404, JText::_('MEMBERS_NO_ID'));
			return;
		}

		// Check authorization
		$authorized = $this->_authorize($id);
		if (!$authorized) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_task)), 
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			JError::raiseError(403, JText::_('MEMBERS_NOT_AUTH'));
			return;
		}

		// Initiate profile class
		$profile = Hubzero_User_Profile::getInstance($id);

		// Ensure we have a member
		if (!$profile->get('name')) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_task)), 
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			JError::raiseError(404, JText::_('MEMBERS_NOT_FOUND'));
			return;
		}

		// Include some needed styles and scripts
		$this->_getStyles();
		$this->_getScripts('assets/js/changepassword');

		// Add to the pathway
		$pathway->addItem(
			stripslashes($profile->get('name')), 
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber')
		);
		$pathway->addItem(
			JText::_(strtoupper($this->_task)), 
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber') . '&task=' . $this->_task
		);

		// Load some needed libraries
		ximport('Hubzero_Registration_Helper');
		ximport('Hubzero_User_Helper');

		if (Hubzero_User_Helper::isXDomainUser($this->juser->get('id'))) 
		{
			JError::raiseError(403, JText::_('MEMBERS_PASS_CHANGE_LINKED_ACCOUNT'));
			return;
		}

		// Incoming data
		$change   = JRequest::getVar('change', '', 'post');
		$oldpass  = JRequest::getVar('oldpass', '', 'post');
		$newpass  = JRequest::getVar('newpass', '', 'post');
		$newpass2 = JRequest::getVar('newpass2', '', 'post');
		$message  = JRequest::getVar('message', '');

		if (!empty($message))
		{
			$this->setError($message);
		}

		$this->view->title = $title;
		$this->view->profile = $profile;
		$this->view->change = $change;
		$this->view->oldpass = $oldpass;
		$this->view->newpass = $newpass;
		$this->view->newpass2 = $newpass2;
		$this->view->validated = true;

		ximport('Hubzero_Password_Rule');
		$password_rules = Hubzero_Password_Rule::getRules();

		$this->view->password_rules = array();

		foreach ($password_rules as $rule) 
		{
			if (!empty($rule['description'])) 
			{
				$this->view->password_rules[] = $rule['description'];
			}
		}

		if (!empty($newpass)) 
		{
			$msg = Hubzero_Password_Rule::validate($newpass, $password_rules, $profile->get('username'));
		}
		else 
		{
			$msg = array();
		}

		// Blank form request (no data submitted)
		if (empty($change))
		{
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}

			$this->view->display();
			return;
		}

		$passrules = false;

		if (!Hubzero_User_Password::passwordMatches($profile->get('uidNumber'), $oldpass, true)) 
		{
			$this->setError(JText::_('MEMBERS_PASS_INCORRECT'));
		} 
		elseif (!$newpass || !$newpass2) 
		{
			$this->setError(JText::_('MEMBERS_PASS_MUST_BE_ENTERED_TWICE'));
		} 
		elseif ($newpass != $newpass2) 
		{
			$this->setError(JText::_('MEMBERS_PASS_NEW_CONFIRMATION_MISMATCH'));
		} 
		elseif ($oldpass == $newpass)
		{
			// make sure the current password and new password are not the same
			// this should really be done in the password rules validation step
			$this->setError(JText::_('Your new password must be different from your current password'));
		}
		elseif (!empty($msg)) 
		{
			$this->setError(JText::_('Password does not meet site password requirements. Please choose a password meeting all the requirements listed below.'));
			$this->view->validated = $msg;
			$passrules = true;
		}

		if ($this->getError())
		{
			$change = array();
			$change['_missing']['password'] = $this->getError();

			if (!empty($msg) && $passrules) 
			{
				$change['_missing']['password'] .= '<ul>';
				foreach ($msg as $m) 
				{
					$change['_missing']['password'] .= '<li>';
					$change['_missing']['password'] .= $m;
					$change['_missing']['password'] .= '</li>';
				}
				$change['_missing']['password'] .= '</ul>';
			}

			if (JRequest::getInt('no_html', 0))
			{
				echo json_encode($change);
				exit();
			}
			else
			{
				$this->view->setError($this->getError());
				$this->view->display();
				return;
			}
		}

		// Encrypt the password and update the profile
		$result = Hubzero_User_Password::changePassword($profile->get('uidNumber'), $newpass);

		// Save the changes
		if (!$result)
		{
			$this->view->setError(JText::_('MEMBERS_PASS_CHANGE_FAILED'));
			$this->view->display();
			return;
		}

		// Redirect user back to main account page
		$return = base64_decode(JRequest::getVar('return', '',  'method', 'base64'));
		$this->_redirect = $return ? $return : JRoute::_('index.php?option=' . $this->_option . '&id=' . $id);
		$session =& JFactory::getSession();

		// Redirect user back to main account page
		if (JRequest::getInt('no_html', 0))
		{
			if ($session->get('badpassword','0') || $session->get('expiredpassword','0'))
			{
				$session->set('badpassword','0');
				$session->set('expiredpassword','0');
			}

			echo json_encode(array("success" => true));
			exit();
		}
		else
		{
			if ($session->get('badpassword','0') || $session->get('expiredpassword','0'))
			{
				$hconfig = &JComponentHelper::getParams('com_hub');
				$r = $hconfig->get('LoginReturn');
				$this->_redirect = ($r) ? $r : '/members/myaccount';
				$session->set('badpassword','0');
				$session->set('expiredpassword','0');
			}
		}
	}

	/**
	 * Show a form for raising a user's allowed sessions, storage, etc.
	 * 
	 * @return     void
	 */
	public function raiselimitTask()
	{
		// Set the page title
		$this->view->title  = JText::_(strtoupper($this->_name));
		$this->view->title .= ($this->_task) ? ': ' . JText::_(strtoupper($this->_task)) : '';

		$document =& JFactory::getDocument();
		$document->setTitle($this->view->title);

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)), 
				'index.php?option=' . $this->_option
			);
		}

		// Incoming
		$id = JRequest::getInt('id', 0);

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_controller . '&task=raiselimit'), 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn))
			);
			return;
		}

		// Ensure we have an ID
		if (!$id) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_task)), 
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			JError::raiseError(404, JText::_('MEMBERS_NO_ID'));
			return;
		}

		// Check authorization
		$this->view->authorized = $this->_authorize($id);
		if (!$this->view->authorized) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_task)), 
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			JError::raiseError(403, JText::_('MEMBERS_NOT_AUTH'));
			return;
		}

		// Include some needed styles and scripts
		$this->_getStyles();

		// Initiate profile class
		$profile = Hubzero_User_Profile::getInstance($id);

		// Ensure we have a member
		if (!$profile->get('name'))
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_task)), 
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			JError::raiseError(404, JText::_('MEMBERS_NOT_FOUND'));
			return;
		}

		$this->view->profile = $profile;

		// Add to the pathway
		$pathway->addItem(
			stripslashes($profile->get('name')), 
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber')
		);
		$pathway->addItem(
			JText::_(strtoupper($this->_task)), 
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber') . '&task=' . $this->_task
		);

		// Incoming
		$request = JRequest::getVar('request', null, 'post');
		$raiselimit = JRequest::getVar('raiselimit', null, 'post');

		if ($raiselimit) 
		{
			$k = '';
			if (is_array($raiselimit)) 
			{
				$k = key($raiselimit);
			}

			switch ($k)
			{
				case 'sessions':
					$oldlimit = intval($profile->get('jobsAllowed'));
					$newlimit = $oldlimit + 3;

					$resourcemessage = 'session limit from '. $oldlimit .' to '. $newlimit .' sessions ';

					if ($this->view->authorized == 'admin') 
					{
						$profile->set('jobsAllowed', $newlimit);
						$profile->update();
						$resourcemessage = 'The session limit for [' . $profile->get('username') . '] has been raised from ' . $oldlimit . ' to ' . $newlimit . ' sessions.';
					} 
					else if ($request === null) 
					{
						$this->view->resource = $k;
						$this->view->setLayout('select');
						$this->view->display();
						return;
					}
				break;

				case 'storage':
					$oldlimit = 'unknown'; // $profile->get('quota');
					$newlimit = 'unknown'; // $profile->get('quota') + 100;

					$resourcemessage = ' storage limit has been raised from '. $oldlimit .' to '. $newlimit .'.';

					if ($this->view->authorized == 'admin') 
					{
						// $profile->set('quota', $newlimit);
						// $profile->update();

						$resourcemessage = 'The storage limit for [' . $profile->get('username') . '] has been raised from '. $oldlimit .' to '. $newlimit .'.';
					} 
					else 
					{
						$this->view->resource = $k;
						$this->view->setLayout('select');
						$this->view->display();
						return;
					}
				break;

				case 'meetings':
					$oldlimit = 'unknown'; // $profile->get('max_meetings');
					$newlimit = 'unknown'; // $profile->get('max_meetings') + 3;

					$resourcemessage = ' meeting limit has been raised from '. $oldlimit .' to '. $newlimit .'.';

					if ($this->view->authorized == 'admin') 
					{
						// $profile->set('max_meetings', $newlimit);
						// $profile->update();

						$resourcemessage = 'The meeting limit for [' . $profile->get('username') . '] has been raised from '. $oldlimit .' to '. $newlimit .'.';
					} 
					else 
					{
						$this->view->resource = $k;
						$this->view->setLayout('select');
						$this->view->display();
						return;
					}
				break;

				default:
					// Show limit selection form
					$this->view->display();
					return;
				break;
			}
		}

		// Do we need to email admin?
		if ($request !== null && !empty($resourcemessage)) 
		{
			$juri =& JURI::getInstance();
			$jconfig = JFactory::getConfig();
			$sitename = $jconfig->getValue('config.sitename');
			$live_site = rtrim(JURI::base(),'/');

			// Email subject
			$subject = $hubName . " Account Resource Request";

			// Email message
			$message = 'Name: ' . $profile->get('name');
			if ($profile->get('organization')) 
			{
				$message .= " / " . $profile->get('organization');
			}
			$message .= "\r\n";
			$message .= "Email: " . $profile->get('email') . "\r\n";
			$message .= "Username: " . $profile->get('username') . "\r\n\r\n";
			$message .= 'Has requested an increases in their ' . $hubName;
			$message .= $resourcemessage . "\r\n\r\n";
			$message .= "Reason: ";
			if (empty($request)) 
			{
				$message .= "NONE GIVEN\r\n\r\n";
			} 
			else 
			{
				$message .= $request . "\r\n\r\n";
			}
			$message .= "Click the following link to grant this request:\r\n";

			$sef = JRoute::_('index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber') . '&task=' . $this->_task);
			$url = $juri->base() . ltrim($sef, DS);

			$message .= $url . "\r\n\r\n";
			$message .= "Click the following link to review this user's account:\r\n";

			$sef = JRoute::_('index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber'));
			$url = $juri->base() . ltrim($sef, DS);

			$message .= $url . "\r\n";

			// Get the administrator's email address
			$emailadmin = $jconfig->getValue('config.mailfrom');

			// Send an e-mail to admin
			if (!Hubzero_Toolbox::send_email($emailadmin, $subject, $message)) 
			{
				return JError::raiseError(500, 'xHUB Internal Error: Error mailing resource request to site administrator(s).');
			}

			// Output the view
			$this->view->resourcemessage = $resourcemessage;
			$this->view->setLayout('success');
			$this->view->display();
			return;
		} 
		else if ($this->view->authorized == 'admin' && !empty($resourcemessage)) 
		{
			// Output the view
			$this->view->resourcemessage = $resourcemessage;
			$this->view->setLayout('success');
			$this->view->display();
			return;
		}

		// Output the view
		$this->view->resource = null;
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
	 * Show a form for editing a profile
	 * 
	 * @param      object $xregistration Hubzero_Registration
	 * @param      object $profile       Hubzero_User_Profile
	 * @return     void
	 */
	public function editTask($xregistration=null, $profile=null)
	{
		// Set the page title
		$this->view->title  = JText::_(strtoupper($this->_name));
		$this->view->title .= ($this->_task) ? ': ' . JText::_(strtoupper($this->_task)) : '';

		$document =& JFactory::getDocument();
		$document->setTitle($this->view->title);

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)), 
				'index.php?option=' . $this->_option
			);
		}

		// Incoming
		$id = JRequest::getInt('id', 0);

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_controller . '&task=activity'), 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn))
			);
			return;
		}

		// Ensure we have an ID
		if (!$id) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_task)), 
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			JError::raiseError(404, JText::_('MEMBERS_NO_ID'));
			return;
		}
		// Check authorization
		$this->view->authorized = $this->_authorize($id);
		if ($id != $this->juser->get('id'))
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_task)), 
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			JError::raiseError(403, JText::_('MEMBERS_NOT_AUTH'));
			return;
		}

		// Include some needed styles and scripts
		$this->_getStyles();

		// Initiate profile class if we don't already have one and load info
		// Note: if we already have one then we just came from $this->save()
		if (!is_object($profile)) 
		{
			$profile = Hubzero_User_Profile::getInstance($id);
		}

		// Ensure we have a member
		if (!$profile->get('name') && !$profile->get('surname')) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_task)), 
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			JError::raiseError(404, JText::_('MEMBERS_NOT_FOUND'));
			return;
		}

		// Get the user's interests (tags)
		$mt = new MembersTags($this->database);
		$this->view->tags = $mt->get_tag_string($id);

		// Add to the pathway
		$pathway->addItem(
			stripslashes($profile->get('name')), 
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber')
		);
		$pathway->addItem(
			JText::_(strtoupper($this->_task)), 
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber') . '&task=' . $this->_task
		);

		// Load some needed libraries
		ximport('Hubzero_Toolbox');
		ximport('Hubzero_Registration');
		ximport('Hubzero_Registration_Helper');

		// Instantiate an xregistration object if we don't already have one
		// Note: if we already have one then we just came from $this->save()
		if (!is_object($xregistration)) 
		{
			$xregistration = new Hubzero_Registration();
		}
		$this->view->xregistration = $xregistration;

		// Find out which fields are hidden, optional, or required
		$registration = new JObject();
		$registration->Username        = $this->_registrationField('registrationUsername', 'RROO', $this->_task);
		$registration->Password        = $this->_registrationField('registrationPassword', 'RRHH', $this->_task);
		$registration->ConfirmPassword = $this->_registrationField('registrationConfirmPassword', 'RRHH', $this->_task);
		$registration->Fullname        = $this->_registrationField('registrationFullname', 'RRRR', $this->_task);
		$registration->Email           = $this->_registrationField('registrationEmail', 'RRRR', $this->_task);
		$registration->ConfirmEmail    = $this->_registrationField('registrationConfirmEmail', 'RRRR', $this->_task);
		$registration->URL             = $this->_registrationField('registrationURL', 'HHHH', $this->_task);
		$registration->Phone           = $this->_registrationField('registrationPhone', 'HHHH', $this->_task);
		$registration->Employment      = $this->_registrationField('registrationEmployment', 'HHHH', $this->_task);
		$registration->Organization    = $this->_registrationField('registrationOrganization', 'HHHH', $this->_task);
		$registration->Citizenship     = $this->_registrationField('registrationCitizenship', 'HHHH', $this->_task);
		$registration->Residency       = $this->_registrationField('registrationResidency', 'HHHH', $this->_task);
		$registration->Sex             = $this->_registrationField('registrationSex', 'HHHH', $this->_task);
		$registration->Disability      = $this->_registrationField('registrationDisability', 'HHHH', $this->_task);
		$registration->Hispanic        = $this->_registrationField('registrationHispanic', 'HHHH', $this->_task);
		$registration->Race            = $this->_registrationField('registrationRace', 'HHHH', $this->_task);
		$registration->Interests       = $this->_registrationField('registrationInterests', 'HHHH', $this->_task);
		$registration->Reason          = $this->_registrationField('registrationReason', 'HHHH', $this->_task);
		$registration->OptIn           = $this->_registrationField('registrationOptIn', 'HHHH', $this->_task);
		$registration->TOU             = $this->_registrationField('registrationTOU', 'HHHH', $this->_task);

		// Ouput HTML
		$this->view->profile = $profile;
		$this->view->registration = $registration;

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
	 * Get the settings for a particular field
	 *  H = hidden
	 *  O = optional
	 *  R = required
	 *  U = read only
	 * 
	 * @param      string $name    Field name
	 * @param      string $default Default setting
	 * @param      string $task    Task being executed
	 * @return     integer
	 */
	private function _registrationField($name, $default, $task = 'create')
	{
		switch ($task)
		{
			case 'register':
			case 'create': $index = 0; break;
			case 'proxy':  $index = 1; break;
			case 'update': $index = 2; break;
			case 'edit':   $index = 3; break;
			default:       $index = 0; break;
		}

		$hconfig =& JComponentHelper::getParams('com_register');

		$default = str_pad($default, 4, '-');
		$configured = $hconfig->get($name);
		if (empty($configured)) 
		{
			$configured = $default;
		}
		$length = strlen($configured);
		if ($length > $index) 
		{
			$value = substr($configured, $index, 1);
		} 
		else 
		{
			$value = substr($default, $index, 1);
		}

		switch ($value)
		{
			case 'R': return(REG_REQUIRED);
			case 'O': return(REG_OPTIONAL);
			case 'H': return(REG_HIDE);
			case '-': return(REG_HIDE);
			case 'U': return(REG_READONLY);
			default : return(REG_HIDE);
		}
	}

	/**
	 * Save changes to a profile
	 * Outputs JSON when called via AJAX, redirects to profile otherwise
	 * 
	 * @return     string JSON
	 */
	public function saveTask()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			return false;
		} 

		ximport('Hubzero_Toolbox');
		ximport('Hubzero_Registration');
		ximport('Hubzero_Registration_Helper');

		$no_html = JRequest::getVar("no_html", 0);

		// Incoming user ID
		$id = JRequest::getInt('id', 0, 'post');

		// Do we have an ID?
		if (!$id) 
		{
			JError::raiseError(500, JText::_('MEMBERS_NO_ID'));
			return;
		}

		// Incoming profile edits
		$p = JRequest::getVar('profile', array(), 'post');
		$n = JRequest::getVar('name', array(), 'post');
		$a = JRequest::getVar('access', array(), 'post');

		// Load the profile
		$profile = Hubzero_User_Profile::getInstance($id);

		$oldemail = $profile->get('email');

		if ($n)
		{
			$profile->set('givenName', trim($n['first']));
			$profile->set('middleName', trim($n['middle']));
			$profile->set('surname', trim($n['last']));
			$name  = trim($n['first']) . ' ';
			$name .= (trim($n['middle']) != '') ? trim($n['middle']) . ' ' : '';
			$name .= trim($n['last']);
			$profile->set('name', $name);
		}

		if (isset($p['bio']))
		{
			$profile->set('bio', trim($p['bio']));
		}

		if (is_array($a) && count($a) > 0)
		{
			foreach ($a as $k => $v)
			{
				$profile->setParam('access_' . $k, $v);
			}
		}

		if (isset($p['public'])) 
		{
			$profile->set('public', $p['public']);
		}

		// Set some post data for the xregistration class
		$tags = trim(JRequest::getVar('tags',''));
		if (isset($tags))
		{
			JRequest::setVar('interests', $tags, 'post');
		}

		// Instantiate a new Hubzero_Registration
		$xregistration = new Hubzero_Registration();
		$xregistration->loadPOST();

		// Push the posted data to the profile
		// Note: this is done before the required fields check so, if we need to display the edit form, it'll show all the new changes
		if (!is_null($xregistration->_registration['email']))
		{
			$profile->set('email', $xregistration->_registration['email']);

			// Unconfirm if the email address changed
			if ($oldemail != $xregistration->_registration['email']) 
			{
				// Get a new confirmation code
				$confirm = Hubzero_Registration_Helper::genemailconfirm();

				$profile->set('emailConfirmed', $confirm);
			}
		}

		if (!is_null($xregistration->_registration['countryresident']))
		{
			$profile->set('countryresident', $xregistration->_registration['countryresident']);
		}

		if (!is_null($xregistration->_registration['countryorigin']))
		{
			$profile->set('countryorigin', $xregistration->_registration['countryorigin']);
		}

		if (!is_null($xregistration->_registration['nativetribe']))
		{
			$profile->set('nativeTribe', $xregistration->_registration['nativetribe']);
		}

		if ($xregistration->_registration['org'] != '') 
		{
			$profile->set('organization', $xregistration->_registration['org']);
		} 
		elseif ($xregistration->_registration['orgtext'] != '')
		{
			$profile->set('organization', $xregistration->_registration['orgtext']);
		}

		if (!is_null($xregistration->_registration['web']))
		{
			$profile->set('url', $xregistration->_registration['web']);
		}

		if (!is_null($xregistration->_registration['phone']))
		{
			$profile->set('phone', $xregistration->_registration['phone']);
		}

		if (!is_null($xregistration->_registration['orgtype']))
		{
			$profile->set('orgtype', $xregistration->_registration['orgtype']);
		}

		if (!is_null($xregistration->_registration['sex']))
		{
			$profile->set('gender', $xregistration->_registration['sex']);
		}

		if (!is_null($xregistration->_registration['disability']))
		{
			$profile->set('disability', $xregistration->_registration['disability']);
		}

		if (!is_null($xregistration->_registration['hispanic']))
		{
			$profile->set('hispanic', $xregistration->_registration['hispanic']);
		}

		if (!is_null($xregistration->_registration['race']))
		{
			$profile->set('race', $xregistration->_registration['race']);
		}

		if (!is_null($xregistration->_registration['mailPreferenceOption']))
		{
			$profile->set('mailPreferenceOption', $xregistration->_registration['mailPreferenceOption']);
		}

		if (!is_null($xregistration->_registration['usageAgreement']))
		{
			$profile->set('usageAgreement', $xregistration->_registration['usageAgreement']);
		}
		
		$field_to_check = JRequest::getVar("field_to_check", array());
		
		// Check that required fields were filled in properly
		if (!$xregistration->check('edit', $profile->get('uidNumber'), $field_to_check))
		{
			if (!$no_html)
			{
				$this->_task = 'edit';
				$this->editTask($xregistration, $profile);
				return;
			}
			else
			{
				echo json_encode($xregistration);
				exit();
			}
		}

		//are we declining the terms of use
		//if yes we want to set the usage agreement to 0 and profile to private
		$declineTOU = JRequest::getVar('declinetou', 0);
		if ($declineTOU)
		{
			$profile->set('public', 0);
			$profile->set('usageAgreement', 0);
		}

		// Set the last modified datetime
		$profile->set('modifiedDate', date('Y-m-d H:i:s', time()));

		// Save the changes
		if (!$profile->update()) 
		{
			JError::raiseError(500, $profile->getError());
			return false;
		}

		// Process tags
		if (isset($tags) && in_array('interests', $field_to_check))
		{
			$mt = new MembersTags($this->database);
			$mt->tag_object($id, $id, $tags, 1, 1);
		}

		$email = $profile->get('email');
		$name  = $profile->get('name');

		// Make sure certain changes make it back to the Joomla user table
		if ($id > 0) 
		{
			$juser =& JUser::getInstance($id);
			$jname = $juser->get('name');
			$jemail = $juser->get('email');
			if ($name != trim($jname)) 
			{
				$juser->set('name', $name);
			}
			if ($email != trim($jemail)) 
			{
				$juser->set('email', $email);
			}
			if ($name != trim($jname) || $email != trim($jemail)) 
			{
				if (!$juser->save()) 
				{
					JError::raiseError(500, JText::_($juser->getError()));
					return false;
				}
			}
		}

		// Send a new confirmation code AFTER we've successfully saved the changes to the e-mail address
		if ($email != $oldemail) 
		{
			$this->_message = $this->_sendConfirmationCode($profile->get('username'), $email, $confirm);
		}

		//if were declinging the terms we want to logout user and tell the javascript
		if ($declineTOU)
		{
			$application =& JFactory::getApplication();
			$application->logout();
			echo json_encode(array('loggedout' => true));
			return;
		}

		if (!$no_html)
		{
			// Redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . ($id ? '&id=' . $id . '&active=profile' : '')),
				$this->_message
			);
		}
		else
		{
			// Output JSON
			echo json_encode(array('success' => true));
		}
	}

	/**
	 * Send a confirmation code to a user's email address
	 * 
	 * @param      strong $login   Username
	 * @param      string $email   User email address
	 * @param      string $confirm Confirmation code
	 * @return     string 
	 */
	private function _sendConfirmationCode($login, $email, $confirm)
	{
		$jconfig =& JFactory::getConfig();
		$juri = JURI::getInstance();

		// Email subject
		$subject = $jconfig->getValue('config.sitename') .' account email confirmation';

		// Email message
		$eview = new JView(array(
			'name'   => 'emails',
			'layout' => 'confirm'
		));
		$eview->option   = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->login    = $login;
		$eview->confirm  = $confirm;
		$eview->baseURL  = $juri->base();

		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Send the email
		if (Hubzero_Toolbox::send_email($email, $subject, $message)) 
		{
			$msg = 'A confirmation email has been sent to "'. htmlentities($email,ENT_COMPAT,'UTF-8') .'". You must click the link in that email to re-activate your account.';
		} 
		else 
		{
			$msg = 'An error occurred emailing "'. htmlentities($email,ENT_COMPAT,'UTF-8') .'" your confirmation.';
		}

		return $msg;
	}

	/**
	 * Save profile field access
	 * 
	 * @return     void
	 */
	public function saveaccessTask()
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($this->juser->get('guest')) 
		{
			return false;
		}

		// Incoming user ID
		$id = JRequest::getInt('id', 0);

		// Do we have an ID?
		if (!$id) 
		{
			JError::raiseError(500, JText::_('MEMBERS_NO_ID'));
			return;
		}

		// Incoming profile edits
		$p = JRequest::getVar('access', array(), 'post');
		if (is_array($p)) 
		{
			// Load the profile
			$profile = Hubzero_User_Profile::getInstance($id);

			foreach ($p as $k=>$v)
			{
				$profile->setParam('access_' . $k, $v);
			}

			// Save the changes
			if (!$profile->update()) 
			{
				JError::raiseWarning('', $profile->getError());
				return false;
			}
		}

		// Push through to the profile view
		$this->viewTask();
	}

	/**
	 * Show the current user activity
	 * 
	 * @return     void
	 */
	public function activityTask()
	{
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_name)) . ': ' . JText::_(strtoupper($this->_task)));

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		$pathway->addItem(
			JText::_(strtoupper($this->_task)),
			'index.php?option=' . $this->_option . '&task=' . $this->_task
		);

		// Push some styles to the template
		$this->_getStyles();
		$this->_getStyles('usage');

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_controller . '&task=activity'), 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn))
			);
			return;
		}
		if (!$this->juser->authorize($this->_option, 'manage'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
		}

		// Get logged-in users
		$prevuser = '';
		$user  = array();
		$users = array();

		$sql = "SELECT s.username, s.ip, (UNIX_TIMESTAMP(NOW()) - s.time) AS idle 
				FROM #__session AS s WHERE s.username <> '' 
				ORDER BY username, ip, idle DESC";

		$this->database->setQuery($sql);
		$result = $this->database->loadObjectList();

		if ($result && count($result) > 0) 
		{
			foreach ($result as $row)
			{
				if ($prevuser != $row->username) 
				{
					if ($user) 
					{
						$xprofile = Hubzero_User_Profile::getInstance($prevuser);

						$users[$prevuser] = $user;
						$users[$prevuser]['name'] = $xprofile->get('name');
						$users[$prevuser]['org'] = $xprofile->get('orginization');
						$users[$prevuser]['orgtype'] = $xprofile->get('orgtype');
						$users[$prevuser]['countryresident'] = $xprofile->get('countryresident');
					}
					$prevuser = $row->username;
					$user = array();
				}
				array_push($user, array('ip' => $row->ip, 'idle' => $row->idle));
			}
			if ($user) 
			{
				$xprofile = Hubzero_User_Profile::getInstance($prevuser);

				$users[$prevuser] = $user;
				$users[$prevuser]['name'] = $xprofile->get('name');
				$users[$prevuser]['org'] = $xprofile->get('orginization');
				$users[$prevuser]['orgtype'] = $xprofile->get('orgtype');
				$users[$prevuser]['countryresident'] = $xprofile->get('countryresident');
			}
		}

		$guests = array();
		$sql = "SELECT s.ip, (UNIX_TIMESTAMP(NOW()) - s.time) AS idle 
				FROM #__session AS s WHERE s.username = '' 
				ORDER BY ip, idle DESC";

		$this->database->setQuery($sql);
		$result = $this->database->loadObjectList();
		if ($result) 
		{
			if (count($result) > 0) 
			{
				foreach($result as $row)
				{
					array_push($guests, array('ip' => $row->ip, 'idle' => $row->idle));
				}
			}
		}

		// Output View
		$this->view->title = JText::_('Active Users and Guests');
		$this->view->users = $users;
		$this->view->guests = $guests;

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
	 * Cancel a task and redirect to profile
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		// Incoming
		$id = JRequest::getInt('id', 0);

		// Redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&id=' . $id . '&active=profile')
		);
	}

	/**
	 * Method to check admin access permission
	 *
	 * @param      integer $uid       User ID
	 * @param      string  $assetType Asset type
	 * @param      string  $assetId   Asset ID
	 * @return     boolean True on success
	 */
	protected function _authorize($uid=0, $assetType='component', $assetId=null)
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			return false;
		}

		// Check if they're a site admin (from Joomla)
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			// Admin
			$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $assetId));
			$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $assetId));

			if ($this->config->get('access-admin-' . $assetType))
			{
				return 'admin';
			}
		}
		else 
		{
			if ($this->juser->authorize($this->_option, 'manage'))
			{
				return 'admin';
			}
		}

		// Check if they're the member
		if (is_numeric($uid))
		{
			if ($this->juser->get('id') == $uid) 
			{
				return true;
			}
		} 
		else 
		{
			if ($this->juser->get('username') == $uid) 
			{
				return true;
			}
		}

		return false;
	}
}

