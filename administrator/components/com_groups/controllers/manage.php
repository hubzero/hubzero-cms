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

/**
 * Groups controller class for managing membership and group info
 */
class GroupsControllerManage extends \Hubzero\Component\AdminController
{
	/**
	 * Displays a list of groups
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['type']    = array(trim($app->getUserStateFromRequest(
			$this->_option . '.browse.type',
			'type',
			'all'
		)));
		$this->view->filters['search']  = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.browse.search',
			'search',
			''
		)));
		$this->view->filters['discoverability'] = trim($app->getUserStateFromRequest(
			$this->_option . '.browse.discoverability',
			'discoverability',
			''
		));
		$this->view->filters['policy']  = trim($app->getUserStateFromRequest(
			$this->_option . '.browse.policy',
			'policy',
			''
		));
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.browse.sort',
			'filter_order',
			'cn'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.browse.sortdir',
			'filter_order_Dir',
			'ASC'
		));
		$this->view->filters['sortby'] = $this->view->filters['sort'] . ' ' . $this->view->filters['sort_Dir'];

		// Filters for getting a result count
		$this->view->filters['limit'] = 'all';
		$this->view->filters['fields'] = array('COUNT(*)');
		$this->view->filters['authorized'] = 'admin';

		$canDo = GroupsHelper::getActions('group');
		if (!$canDo->get('core.admin'))
		{
			if ($this->view->filters['type'][0] == 'system' || $this->view->filters['type'][0] == 0)
			{
				$this->view->filters['type'] = array('all');
			}

			if ($this->view->filters['type'][0] == 'all')
			{
				$this->view->filters['type'] = array(
					//0,  No system groups
					1,  // hub
					2,  // project
					3   // super
				);
			}
		}

		//approved filter
		$this->view->filters['approved'] = JRequest::getVar('approved');

		//published filter
		//$this->view->filters['published'] = JRequest::getVar('published', 1);

		//created filter
		$this->view->filters['created'] = JRequest::getVar('created', '');

		// Get a record count
		$this->view->total = \Hubzero\User\Group::find($this->view->filters);

		// Filters for returning results
		$this->view->filters['limit']  = $app->getUserStateFromRequest(
			$this->_option . '.browse.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']  = $app->getUserStateFromRequest(
			$this->_option . '.browse.limitstart',
			'limitstart',
			0,
			'int'
		);
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);
		$this->view->filters['fields'] = array('cn', 'description', 'published', 'gidNumber', 'type');

		// Get a list of all groups
		$this->view->rows = null;
		if ($this->view->total > 0)
		{
			$this->view->rows = \Hubzero\User\Group::find($this->view->filters);
		}

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// pass config to view
		$this->view->config = $this->config;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new group
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Displays an edit form
	 *
	 * @return	void
	 */
	public function editTask()
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Incoming
		$id = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (is_array($id))
		{
			$id = (!empty($id)) ? $id[0] : 0;
		}

		// determine task
		$task = ($id == '') ? 'create' : 'edit';

		$this->view->group = new \Hubzero\User\Group();
		$this->view->group->read($id);

		// make sure we are organized
		if (!$this->authorize($task, $this->view->group))
		{
			return;
		}

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Recursive array_map
	 *
	 * @param  $func string Function to map
	 * @param  $arr  array  Array to process
	 * @return array
	 */
	protected function _multiArrayMap($func, $arr)
	{
		$newArr = array();

		foreach ($arr as $key => $value)
		{
			$newArr[$key] = (is_array($value) ? $this->_multiArrayMap($func, $value) : $func($value));
		}

		return $newArr;
	}

	/**
	 * Saves changes to a group or saves a new entry if creating
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$g = JRequest::getVar('group', array(), 'post', 'none', 2);
		$g = $this->_multiArrayMap('trim', $g);

		// Instantiate an \Hubzero\User\Group object
		$group = new \Hubzero\User\Group();

		// Is this a new entry or updating?
		$isNew = false;
		if (!$g['gidNumber'])
		{
			$isNew = true;

			// Set the task - if anything fails and we re-enter edit mode
			// we need to know if we were creating new or editing existing
			$this->_task = 'new';
			$before = new \Hubzero\User\Group();
		}
		else
		{
			$this->_task = 'edit';

			// Load the group
			$group->read($g['gidNumber']);
			$before = clone($group);
		}

		$task = ($this->_task == 'edit') ? 'edit' : 'create';
		if (!$this->authorize($task, $group))
		{
			return;
		}

		// Check for any missing info
		if (!$g['cn'])
		{
			$this->setError(JText::_('COM_GROUPS_ERROR_MISSING_INFORMATION') . ': ' . JText::_('COM_GROUPS_ID'));
		}
		if (!$g['description'])
		{
			$this->setError(JText::_('COM_GROUPS_ERROR_MISSING_INFORMATION') . ': ' . JText::_('COM_GROUPS_TITLE'));
		}

		// Push back into edit mode if any errors
		if ($this->getError())
		{
			$this->view->setLayout('edit');
			$this->view->group = $group;

			// Set any errors
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}

			// Output the HTML
			$this->view->display();
			return;
		}

		$g['cn'] = strtolower($g['cn']);

		// Ensure the data passed is valid
		if (!$this->_validCn($g['cn'], true))
		{
			$this->setError(JText::_('COM_GROUPS_ERROR_INVALID_ID'));
		}

		//only check if cn exists if we are creating or have changed the cn
		if ($this->_task == 'new' || $group->get('cn') != $g['cn'])
		{
			if (\Hubzero\User\Group::exists($g['cn'], true))
			{
				$this->setError(JText::_('COM_GROUPS_ERROR_GROUP_ALREADY_EXIST'));
			}
		}

		// Push back into edit mode if any errors
		if ($this->getError())
		{
			$this->view->setLayout('edit');
			$this->view->group = $group;

			// Set any errors
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}

			// Output the HTML
			$this->view->display();
			return;
		}

		// group params
		$gparams = new JRegistry($group->get('params'));

		// set membership control param
		$membership_control = (isset($g['params']['membership_control'])) ? 1 : 0;
		$gparams->set('membership_control', $membership_control);

		// make array of params
		$gparams = $gparams->toArray();

		// array to hold params
		$params = array();

		// create key=val from each param
		foreach ($gparams as $key => $val)
		{
			$params[] = $key . '=' . $val;
		}

		//each param must have its own line
		$params = implode("\n", $params);

		// Set the group changes and save
		$group->set('cn', $g['cn']);
		$group->set('type', $g['type']);
		if ($isNew)
		{
			$group->create();

			$group->set('published', 1);
			$group->set('approved', 1);
			$group->set('created', date("Y-m-d H:i:s"));
			$group->set('created_by', $this->juser->get('id'));

			$group->add('managers', array($this->juser->get('id')));
			$group->add('members', array($this->juser->get('id')));
		}
		$group->set('description', $g['description']);
		$group->set('discoverability', $g['discoverability']);
		$group->set('join_policy', $g['join_policy']);
		$group->set('public_desc', $g['public_desc']);
		$group->set('private_desc', $g['private_desc']);
		$group->set('restrict_msg', $g['restrict_msg']);
		$group->set('logo', $g['logo']);
		$group->set('plugins', $g['plugins']);
		$group->set('params', $params);
		$group->update();

		// Get plugins
		JPluginHelper::importPlugin('groups');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onGroupAfterSave', array($before, $group));

		// log edit
		GroupsModelLog::log(array(
			'gidNumber' => $group->get('gidNumber'),
			'action'    => 'group_edited',
			'comments'  => 'edited by administrator'
		));

		// handle special groups
		if ($group->isSuperGroup())
		{
			$this->_handleSuperGroup($group);

			// git lab stuff
			$this->_handSuperGroupGitlab($group);
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_GROUPS_SAVED')
		);
	}

	/**
	 * Generate default template files for special groups
	 *
	 * @param     object $group \Hubzero\User\Group
	 * @return    void
	 */
	private function _handleSuperGroup($group)
	{
		//get the upload path for groups
		$uploadPath = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $group->get('gidNumber');

		// get the source path
		$srcPath = JPATH_COMPONENT . DS . 'super' . DS . 'default' . DS . '.';

		// create group folder if one doesnt exist
		if (!is_dir($uploadPath))
		{
			if (!JFolder::create($uploadPath))
			{
				JFactory::getApplication()
					->enqueueMessage(JText::_('COM_GROUPS_SUPER_UNABLE_TO_CREATE'), 'error');
			}
		}

		// make sure folder is writable
		if (!is_writable($uploadPath))
		{
			JFactory::getApplication()
					->enqueueMessage(JText::sprintf('COM_GROUPS_SUPER_FOLDER_NOT_WRITABLE', $uploadpath), 'error');
			return;
		}

		// copy over default template recursively
		// must have  /. at the end of source path to get all items in that directory
		// also doesnt overwrite already existing files/folders
		shell_exec("cp -rn $srcPath $uploadPath");

		// make sure files are group read and writable
		// make sure files are all group owned properly
		shell_exec("chmod -R 2770 $uploadPath");
		shell_exec("chgrp -R " . $this->config->get('super_group_file_owner', 'access-content') . " " . $uploadPath);

		// get all current users granted permissionss
		$this->database->setQuery("SHOW GRANTS FOR CURRENT_USER();");
		$grants = $this->database->loadResultArray();

		// look at all current users granted permissions
		$canCreateSuperGroupDB = false;
		foreach ($grants as $grant)
		{
			if (preg_match('/sg\\\\_%/', $grant))
			{
				$canCreateSuperGroupDB = true;
			}
		}

		// create super group DB if doesnt already exist
		if ($canCreateSuperGroupDB)
		{
			$this->database->setQuery("CREATE DATABASE IF NOT EXISTS `sg_{$group->get('cn')}`;");
			if (!$this->database->query())
			{
				JFactory::getApplication()
					->enqueueMessage(JText::_('COM_GROUPS_SUPER_UNABLE_TO_CREATE_DB'), 'error');
			}
		}
		else
		{
			JFactory::getApplication()
					->enqueueMessage(JText::_('COM_GROUPS_SUPER_UNABLE_TO_CREATE_DB'), 'error');
		}

		// check to see if we have a super group db config
		$supergroupDbConfigFile = DS . 'etc' . DS . 'supergroup.conf';
		if (!file_exists($supergroupDbConfigFile))
		{
			JFactory::getApplication()
				->enqueueMessage(JText::_('COM_GROUPS_SUPER_UNABLE_TO_LOAD_CONFIG'), 'error');
		}
		else
		{
			// get hub super group database config file
			$supergroupDbConfig = include $supergroupDbConfigFile;

			// define username, password, and database to be written in config
			$username = (isset($supergroupDbConfig['username'])) ? $supergroupDbConfig['username'] : '';
			$password = (isset($supergroupDbConfig['password'])) ? $supergroupDbConfig['password'] : '';
			$database = 'sg_' . $group->get('cn');

			//write db config in super group
			$dbConfigFile     = $uploadPath . DS . 'config' . DS . 'db.php';
			$dbConfigContents = "<?php\n\treturn array(\n\t\t'host'     => 'localhost',\n\t\t'port'     => '',\n\t\t'user' => '{$username}',\n\t\t'password' => '{$password}',\n\t\t'database' => '{$database}',\n\t\t'prefix'   => ''\n\t);";

			// write db config file
			if (!file_exists($dbConfigFile))
			{
				if (!file_put_contents($dbConfigFile, $dbConfigContents))
				{
					JFactory::getApplication()
						->enqueueMessage(JText::_('COM_GROUPS_SUPER_UNABLE_TO_WRITE_CONFIG'), 'error');
				}
			}
		}

		// log super group change
		GroupsModelLog::log(array(
			'gidNumber' => $group->get('gidNumber'),
			'action'    => 'super_group_created',
			'comments'  => ''
		));
	}

	/**
	 * [_handSuperGroupGitlab description]
	 * @param  [type] $group [description]
	 * @return [type]        [description]
	 */
	private function _handSuperGroupGitlab($group)
	{
		// get needed config vars
		$gitlabManagement = $this->config->get('super_gitlab', 0);
		$gitlabUrl        = $this->config->get('super_gitlab_url', '');
		$gitlabKey        = $this->config->get('super_gitlab_key', '');

		// do we have repo management on
		// make sure we have a url and key
		if (!$gitlabManagement || $gitlabUrl == '' || $gitlabKey == '')
		{
			JFactory::getApplication()
					->enqueueMessage(JText::_('COM_GROUPS_GITLAB_NOT_SETUP'), 'warning');
			return;
		}

		// make sure this is production hub
		$environment = strtolower(JFactory::getConfig()->get('application_env', 'development'));
		if ($environment != 'production')
		{
			return;
		}

		// build group & project names
		$host        = explode('.', $_SERVER['HTTP_HOST']);
		$groupName   = strtolower($host[0]);
		$projectName = $group->get('cn');

		// instantiate new gitlab client
		$client = new GroupsHelperGitlab($gitlabUrl, $gitlabKey);

		// get list of groups
		$groups = $client->groups();

		// attempt to get already existing group
		$gitLabGroup = null;
		foreach ($groups as $g)
		{
			if ($groupName == $g['name'])
			{
				$gitLabGroup = $g;
				break;
			}
		}

		// create group if doesnt exist
		if ($gitLabGroup == null)
		{
			$gitLabGroup = $client->createGroup(array(
				'name' => $groupName,
				'path' => strtolower($groupName)
			));
		}

		//get groups projects
		$projects = $client->projects();

		// attempt to get already existing project
		$gitLabProject = null;
		foreach ($projects as $p)
		{
			if ($projectName == $p['name'] && $p['namespace']['id'] == $gitLabGroup['id'])
			{
				$gitLabProject = $p;
				break;
			}
		}

		// create project if doesnt exist
		if ($gitLabProject == null)
		{
			$gitLabProject = $client->createProject(array(
				'namespace_id'           => $gitLabGroup['id'],
				'name'                   => $projectName,
				'description'            => $group->get('description'),
				'issues_enabled'         => true,
				'merge_requests_enabled' => true,
				'wiki_enabled'           => true,
				'snippets_enabled'       => true,
			));
		}

		// path to group folder
		$uploadPath = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $group->get('gidNumber');

		// build author info for making first commit
		$authorInfo = '"' . JFactory::getConfig()->get('sitename') . ' Groups <groups@' . $_SERVER['HTTP_HOST'] . '>"';

		// check to see if we already have git repo
		// only run gitlab setup once.
		if (is_dir($uploadPath . DS . '.git'))
		{
			return;
		}

		// build command to run via shell
		// this will init the git repo, make the inital commit and push to the repo management machine
		$cmd  = 'sh ' . JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_groups' . DS . 'assets' . DS . 'scripts' . DS . 'gitlab_setup.sh ';
		$cmd .= $uploadPath  . ' ' . $authorInfo . ' ' . $gitLabProject['ssh_url_to_repo'] . ' 2>&1';

		// execute command
		$output = shell_exec($cmd);

		// make sure everything went well
		if (preg_match("/Host key verification failed/uis", $output))
		{
			JFactory::getApplication()
					->enqueueMessage(JText::_('COM_GROUPS_GITLAB_NOT_SETUP_SSH'), 'warning');
			return;
		}

		// protect master branch
		// allows only admins to accept Merge Requests
		$protected = $client->protectBranch(array(
			'id' => $gitLabProject['id'],
			'branch' => 'master'
		));
	}

	/**
	 * Pull from Gitlab
	 * @return void
	 */
	public function pullTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// vars to hold results of pull
		$success = array();
		$failed  = array();

		// loop through each group and pull code from repos
		foreach ($ids as $id)
		{
			// Load the group page
			$group = \Hubzero\User\Group::getInstance($id);

			// Ensure we found the group info
			if (!$group)
			{
				continue;
			}

			// make sure its a super group
			if (!$group->isSuperGroup())
			{
				$failed[] = array('group' => $group->get('cn'), 'message' => JText::_('COM_GROUPS_GITLAB_NOT_SUPER_GROUP'));
				continue;
			}

			// path to group folder
			$uploadPath = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $group->get('gidNumber');

			// make sure we have an upload path
			if (!is_dir($uploadPath))
			{
				$failed[] = array('group' => $group->get('cn'), 'message' => JText::_('COM_GROUPS_GITLAB_UPLOAD_PATH_DOESNT_EXIST'));
				continue;
			}

			// make sure we have a git repo
			if (!is_dir($uploadPath . DS . '.git'))
			{
				// only do stage setup on stage
				$environment = strtolower(JFactory::getConfig()->get('application_env', 'development'));
				if ($environment != 'stage')
				{
					$failed[] = array('group' => $group->get('cn'), 'message' => JText::_('COM_GROUPS_GITLAB_NOT_MANAGED_BY_GIT'));
					continue;
				}

				// build group & project names
				$host        = explode('.', $_SERVER['HTTP_HOST']);
				$groupName   = strtolower($host[0]);
				$projectName = $group->get('cn');

				// get gitlab config
				$gitlabUrl = $this->config->get('super_gitlab_url', '');
				$gitlabKey = $this->config->get('super_gitlab_key', '');

				// instantiate new gitlab client
				$client        = new GroupsHelperGitlab($gitlabUrl, $gitlabKey);
				$gitlabGroup   = $client->group($groupName);
				$gitlabProject = $client->project($projectName);

				// if we didnt get both a matching project & group continue
				if (!$gitlabGroup || !$gitlabProject)
				{
					$failed[] = array('group' => $group->get('cn'), 'message' => JText::_('COM_GROUPS_GITLAB_NOT_MANAGED_BY_GIT'));
					continue;
				}

				// setup stage environment
				$cmd  = 'sh ' . JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_groups' . DS . 'assets' . DS . 'scripts' . DS . 'gitlab_setup_stage.sh ';
				$cmd .= str_replace('/' . $group->get('gidNumber'), '', $uploadPath) . ' ' . $group->get('gidNumber') . ' ' . $gitlabProject['ssh_url_to_repo'] . ' 2>&1';

				// execute command
				$output = shell_exec($cmd);
			}

			// build command to run via shell
			// this will run a "git pull --rebase origin master"
			$cmd  = 'sh ' . JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_groups' . DS . 'assets' . DS . 'scripts' . DS . 'gitlab_pull_and_migrate.sh ';
			$cmd .= $uploadPath . ' ' . JPATH_ROOT . ' 2>&1';

			// execute command
			$output = shell_exec($cmd);

			// did we succeed
			if (preg_match("/Updating the repository...complete/uis", $output))
			{
				// add success message
				$success[] = array('group' => $group->get('cn'), 'message' => $output);
			}
			else
			{
				// add failed message
				$failed[] = array('group' => $group->get('cn'), 'message' => $output);
			}
		}

		// display view
		$this->view->setLayout('pull');
		$this->view->success = $success;
		$this->view->failed  = $failed;
		$this->view->config  = $this->config;
		$this->view->display();
	}

	/**
	 * Removes a group and all associated information
	 *
	 * @return	void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Do we have any IDs?
		if (!empty($ids))
		{
			// Get plugins
			JPluginHelper::importPlugin('groups');
			$dispatcher = JDispatcher::getInstance();

			foreach ($ids as $id)
			{
				// Load the group page
				$group = \Hubzero\User\Group::getInstance($id);

				// Ensure we found the group info
				if (!$group)
				{
					continue;
				}
				if (!$this->authorize('delete', $group))
				{
					continue;
				}

				// Get number of group members
				$groupusers    = $group->get('members');
				$groupmanagers = $group->get('managers');
				$members = array_merge($groupusers, $groupmanagers);

				// Start log
				$log  = JText::_('COM_GROUPS_SUBJECT_GROUP_DELETED');
				$log .= JText::_('COM_GROUPS_TITLE') . ': ' . $group->get('description') . "\n";
				$log .= JText::_('COM_GROUPS_ID') . ': ' . $group->get('cn') . "\n";
				$log .= JText::_('COM_GROUPS_PUBLIC_TEXT') . ': ' . stripslashes($group->get('public_desc')) . "\n";
				$log .= JText::_('COM_GROUPS_PRIVATE_TEXT') . ': ' . stripslashes($group->get('private_desc')) . "\n";
				$log .= JText::_('COM_GROUPS_RESTRICTED_MESSAGE') . ': ' . stripslashes($group->get('restrict_msg')) . "\n";

				// Log ids of group members
				if ($groupusers)
				{
					$log .= JText::_('COM_GROUPS_MEMBERS') . ': ';
					foreach ($groupusers as $gu)
					{
						$log .= $gu . ' ';
					}
					$log .=  "\n";
				}
				$log .= JText::_('COM_GROUPS_MANAGERS') . ': ';
				foreach ($groupmanagers as $gm)
				{
					$log .= $gm . ' ';
				}
				$log .= "\n";

				// Trigger the functions that delete associated content
				// Should return logs of what was deleted
				$logs = $dispatcher->trigger('onGroupDelete', array($group));
				if (count($logs) > 0)
				{
					$log .= implode('', $logs);
				}

				// Delete group
				if (!$group->delete())
				{
					JError::raiseError(500, 'Unable to delete group');
					return;
				}

				// log publishing
				GroupsModelLog::log(array(
					'gidNumber' => $group->get('gidNumber'),
					'action'    => 'group_deleted',
					'comments'  => $log
				));
			}
		}

		// Redirect back to the groups page
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_GROUPS_REMOVED')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Publish a group
	 *
	 * @return void
	 */
	public function publishTask()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Do we have any IDs?
		if (!empty($ids))
		{
			//foreach group id passed in
			foreach ($ids as $id)
			{
				// Load the group page
				$group = new \Hubzero\User\Group();
				$group->read($id);

				// Ensure we found the group info
				if (!$group)
				{
					continue;
				}

				//set the group to be published and update
				$group->set('published', 1);
				$group->update();

				// log publishing
				GroupsModelLog::log(array(
					'gidNumber' => $group->get('gidNumber'),
					'action'    => 'group_published',
					'comments'  => 'published by administrator'
				));

				// Output messsage and redirect
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('COM_GROUPS_PUBLISHED')
				);
			}
		}
	}

	/**
	 * Unpublish a group
	 *
	 * @return void
	 */
	public function unpublishTask()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Do we have any IDs?
		if (!empty($ids))
		{
			// foreach group id passed in
			foreach ($ids as $id)
			{
				// Load the group page
				$group = new \Hubzero\User\Group();
				$group->read($id);

				// Ensure we found the group info
				if (!$group)
				{
					continue;
				}

				//set the group to be published and update
				$group->set('published', 0);
				$group->update();

				// log unpublishing
				GroupsModelLog::log(array(
					'gidNumber' => $group->get('gidNumber'),
					'action'    => 'group_unpublished',
					'comments'  => 'unpublished by administrator'
				));

				// Output messsage and redirect
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('COM_GROUPS_UNPUBLISHED')
				);
			}
		}
	}

	/**
	 * Approve a group
	 *
	 * @return void
	 */
	public function approveTask()
	{
		// Incoming
		$ids = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Do we have any IDs?
		if (!empty($ids))
		{
			// foreach group id passed in
			foreach ($ids as $id)
			{
				// Load the group page
				$group = new \Hubzero\User\Group();
				$group->read($id);

				// Ensure we found the group info
				if (!$group)
				{
					continue;
				}

				//set the group to be published and update
				$group->set('approved', 1);
				$group->update();

				// log publishing
				GroupsModelLog::log(array(
					'gidNumber' => $group->get('gidNumber'),
					'action'    => 'group_approved',
					'comments'  => 'approved by administrator'
				));
			}

			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_GROUPS_APPROVED')
			);
		}
	}

	/**
	 * Checks if a CN (alias) is valid
	 *
	 * @return boolean True if CN is valid
	 */
	/**
	 * Check if a group alias is valid
	 *
	 * @param 		integer 	$cname 			Group alias
	 * @param 		boolean		$allowDashes 	Allow dashes in cn
	 * @return 		boolean		True if valid, false if not
	 */
	private function _validCn( $cn, $allowDashes = false )
	{
		$regex = '/^[0-9a-zA-Z]+[_0-9a-zA-Z]*$/i';
		if ($allowDashes)
		{
			$regex = '/^[0-9a-zA-Z]+[-_0-9a-zA-Z]*$/i';
		}

		if (preg_match($regex, $cn))
		{
			if (is_numeric($cn) && intval($cn) == $cn && $cn >= 0)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Authorization check
	 * Checks if the group is a system group and the user has super admin access
	 *
	 * @param     object $group \Hubzero\User\Group
	 * @return    boolean True if authorized, false if not.
	 */
	protected function authorize($task, $group=null)
	{
		// get users actions
		$canDo = GroupsHelper::getActions('group');

		// build task name
		$taskName = 'core.' . $task;

		// can user perform task
		if (!$canDo->get($taskName) || (!$canDo->get('core.admin') && $task == 'edit' && $group->get('type') == 0))
		{
			// No access - redirect to main listing
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_GROUPS_NOT_AUTH'),
				'error'
			);
			return false;
		}

		return true;
	}
}
