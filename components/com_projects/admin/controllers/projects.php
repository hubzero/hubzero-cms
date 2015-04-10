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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Projects\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Projects\Tables;
use Components\Projects\Models;
use Components\Projects\Helpers;

/**
 * Manage projects
 */
class Projects extends AdminController
{
	/**
	 * Executes a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		// Publishing enabled?
		$this->_publishing = \JPluginHelper::isEnabled('projects', 'publications') ? 1 : 0;

		// Include scripts
		$this->_includeScripts();

		parent::execute();
	}

	/**
	 * Include necessary scripts
	 *
	 * @return     void
	 */
	protected function _includeScripts()
	{
		// Enable publication management
		if ($this->_publishing)
		{
			require_once(PATH_CORE . DS . 'components' . DS . 'com_publications'
				. DS . 'models' . DS . 'publication.php');
		}
	}

	/**
	 * Lists projects
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->config = $this->config;

		// Get configuration
		$app = \JFactory::getApplication();

		// Get quotas
		$this->view->defaultQuota = Helpers\Html::convertSize(floatval($this->config->get('defaultQuota', 1)), 'GB', 'b');
		$this->view->premiumQuota = Helpers\Html::convertSize(floatval($this->config->get('premiumQuota', 30)), 'GB', 'b');

		// Get filters
		$this->view->filters = array(
			'limit' => $app->getUserStateFromRequest(
				$this->_option . '.projects.limit',
				'limit',
				Config::get('config.list_limit'),
				'int'
			),
			'start' => $app->getUserStateFromRequest(
				$this->_option . '.projects.limitstart',
				'limitstart',
				0,
				'int'
			),
			'search' => urldecode($app->getUserStateFromRequest(
				$this->_option . '.projects.search',
				'search',
				''
			)),
			'sortby' => $app->getUserStateFromRequest(
				$this->_option . '.projects.sort',
				'filter_order',
				'id'
			),
			'sortdir' => $app->getUserStateFromRequest(
				$this->_option . '.projects.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			'authorized' => true,
			'getowner'   => 1,
			'activity'   => 1,
			'quota'      => Request::getVar('quota', 'all', 'post')
		);

		// Retrieve all records when filtering by quota (no paging)
		if ($this->view->filters['quota'] != 'all')
		{
			$this->view->filters['limit'] = 'all';
			$this->view->filters['start'] = 0;
		}

		$obj = new Tables\Project( $this->database );

		// Get records
		$this->view->rows = $obj->getRecords( $this->view->filters, true, 0, 1 );

		// Get a record count
		$this->view->total = $obj->getCount( $this->view->filters, true, 0, 1 );

		// Filtering by quota
		if ($this->view->filters['quota'] != 'all' && $this->view->rows)
		{
			$counter = $this->view->total;
			$rows = $this->view->rows;

			for ($i=0, $n=count( $rows ); $i < $n; $i++)
			{
				$params = new \JParameter( $rows[$i]->params );
				$quota = $params->get('quota', 0);
				if (($this->view->filters['quota'] == 'premium' && $quota < $this->view->premiumQuota )
					|| ($this->view->filters['quota'] == 'regular' && $quota > $this->view->defaultQuota))
				{
					$counter--;
					unset($rows[$i]);
				}
			}

			$rows = array_values($rows);
			$this->view->total = $counter > 0 ? $counter : 0;

			// Fix up paging after filter
			if (count($rows) > $limit)
			{
				$k = 0;

				for ($i=0, $n=count( $rows ); $i < $n; $i++)
				{
					if ($k < $start || $k >= ($limit + $start))
					{
						unset($rows[$i]);
					}

					$k++;
				}
			}

			$this->view->rows = array_values($rows);
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}

		// Check that master path is there
		if ($this->config->get('offroot') && !is_dir($this->config->get('webpath')))
		{
			$this->view->setError( Lang::txt('Master directory does not exist. Administrator must fix this! ') . $this->config->get('webpath') );
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit project info
	 *
	 * @return     void
	 */
	public function editTask()
	{
		// Incoming project ID
		$id = Request::getVar( 'id', array(0) );
		if (is_array( $id ))
		{
			$id = $id[0];
		}

		// Push some styles to the template
		$document = \JFactory::getDocument();
		$document->addStyleSheet(DS . 'plugins' . DS . 'projects' . DS . 'files' . DS . 'css' . DS . 'diskspace.css');
		$document->addScript(DS . 'plugins' . DS . 'projects' . DS . 'files' . DS . 'js' . DS . 'diskspace.js');
		$document->addScript(DS . 'plugins' . DS . 'projects' . DS . 'files' . DS . 'files.js');

		$this->view = $this->view;
		$this->view->config = $this->config;

		$model = new Models\Project( $id );
		$objAC = $model->table('Activity');

		if ($id)
		{
			if (!$model->exists())
			{
				$this->setRedirect(Route::url('index.php?option=' . $this->_option, false),
					Lang::txt('COM_PROJECTS_NOTICE_ID_NOT_FOUND'),
					'error');
				return;
			}
		}
		if (!$id)
		{
			$this->setRedirect(Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('COM_PROJECTS_NOTICE_NEW_PROJECT_FRONT_END'),
				'error');
			return;
		}

		// Get project types
		$objT = $model->table('Type');
		$this->view->types = $objT->getTypes();

		// Get plugin
		\JPluginHelper::importPlugin( 'projects');
		$dispatcher = \JDispatcher::getInstance();

		// Get activity counts
		$dispatcher->trigger( 'onProjectCount', array( $model, &$counts, 1) );
		$counts['activity'] = $objAC->getActivityCount( $model->get('id'), User:: get('id'));
		$this->view->counts = $counts;

		// Get team
		$objO = $model->table('Owner');

		// Sync with system group
		$objO->sysGroup($model->get('alias'), $this->config->get('group_prefix', 'pr-'));

		// Get members and managers
		$this->view->managers = $objO->getOwnerNames($id, 0, '1', 1);
		$this->view->members = $objO->getOwnerNames($id, 0, '0', 1);
		$this->view->authors = $objO->getOwnerNames($id, 0, '2', 1);

		// Get last activity
		$afilters = array('limit' => 1);
		$last_activity = $objAC->getActivities ($id, $afilters);
		$this->view->last_activity = count($last_activity) > 0 ? $last_activity[0] : '';

		// Was project suspended?
		$this->view->suspended = false;
		$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;
		if ($model->isInactive())
		{
			$this->view->suspended = $objAC->checkActivity( $id, Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED'));
		}

		// Get project params
		$this->view->params = $model->params;

		// Get Disk Usage
		\JPluginHelper::importPlugin( 'projects', 'files' );
		$dispatcher = \JDispatcher::getInstance();

		$content = $dispatcher->trigger( 'diskspace', array( $model, 'local', 'admin'));
		$this->view->diskusage = isset($content[0])  ? $content[0]: '';

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}

		// Get tags on this item
		$cloud = new Models\Tags($id);
		$this->view->tags = $cloud->render('string');

		// Output the HTML
		$this->view->obj = $model->project();
		$this->view->publishing	= $this->_publishing;
		$this->view->display();
	}

	/**
	 * Save a project and fall through to edit view
	 *
	 * @return void
	 */
	public function applyTask()
	{
		$this->saveTask(true);
	}

	/**
	 * Saves a project
	 * Redirects to main listing
	 *
	 * @return     void
	 */
	public function saveTask($redirect = false)
	{
		// Check for request forgeries
		Request::checkToken() or jexit( 'Invalid Token' );

		// Config
		$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;

		// Get some needed classes
		$objAA = new Tables\Activity ( $this->database );

		// Incoming
		$formdata 	= $_POST;
		$id 		= Request::getVar( 'id', 0 );
		$action 	= Request::getVar( 'admin_action', '' );
		$message 	= rtrim(\Hubzero\Utility\Sanitize::clean(Request::getVar( 'message', '' )));

		// Initiate extended database class
		$obj = new Tables\Project( $this->database );
		if (!$id or !$obj->loadProject($id))
		{
			$this->setRedirect('index.php?option=' . $this->_option,
				Lang::txt('COM_PROJECTS_NOTICE_ID_NOT_FOUND'),
				'error');
			return;
		}

		$obj->title 		= $formdata['title'] ? rtrim($formdata['title']) : $obj->title;
		$obj->about 		= rtrim(\Hubzero\Utility\Sanitize::clean($formdata['about']));
		$obj->type 			= isset($formdata['type']) ? $formdata['type'] : 1;
		$obj->modified 		= \JFactory::getDate()->toSql();
		$obj->modified_by 	= User::get('id');
		$obj->private 		= Request::getVar( 'private', 0 );

		$this->_message = Lang::txt('COM_PROJECTS_SUCCESS_SAVED');

		// Was project suspended?
		$suspended = false;
		if ($obj->state == 0 && $obj->setup_stage >= $setup_complete)
		{
			$suspended = $objAA->checkActivity( $id, Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED'));
		}

		$subject 		= Lang::txt('COM_PROJECTS_PROJECT').' "'.$obj->alias.'" ';
		$sendmail 		= 0;
		$project 		= $obj->getProject($id, User::get('id'));

		// Get project managers
		$objO = new Tables\Owner( $this->database );
		$managers = $objO->getIds( $id, 1, 1 );

		// Admin actions
		if ($action)
		{
			switch ($action)
			{
				case 'delete':
					$obj->state = 2;
					$what = Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT_DELETED');
					$subject .= Lang::txt('COM_PROJECTS_MSG_ADMIN_DELETED');
					$this->_message = Lang::txt('COM_PROJECTS_SUCCESS_DELETED');
				break;

				case 'suspend':
					$obj->state = 0;
					$what = Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED');
					$subject .= Lang::txt('COM_PROJECTS_MSG_ADMIN_SUSPENDED');
					$this->_message = Lang::txt('COM_PROJECTS_SUCCESS_SUSPENDED');
				break;

				case 'reinstate':
					$obj->state = 1;
					$what = $suspended
						? Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT_REINSTATED')
						: Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT_ACTIVATED');
					$subject .= $suspended
						? Lang::txt('COM_PROJECTS_MSG_ADMIN_REINSTATED')
						: Lang::txt('COM_PROJECTS_MSG_ADMIN_ACTIVATED');

					$this->_message = $suspended
						? Lang::txt('COM_PROJECTS_SUCCESS_REINSTATED')
						: Lang::txt('COM_PROJECTS_SUCCESS_ACTIVATED');
				break;
			}

			// Add activity
			$objAA->recordActivity( $obj->id, User::get('id'), $what, 0, '', '', 'project', 0, $admin = 1 );
			$sendmail = 1;
		}
		elseif ($message)
		{
			$subject .= ' - ' . Lang::txt('COM_PROJECTS_MSG_ADMIN_NEW_MESSAGE');
			$sendmail = 1;
			$this->_message = Lang::txt('COM_PROJECTS_SUCCESS_MESSAGE_SENT');
		}

		// Save changes
		if (!$obj->store())
		{
			$this->setError( $obj->getError() );
			return false;
		}

		// Incoming tags
		$tags = Request::getVar('tags', '', 'post');

		// Save the tags
		$cloud = new Models\Tags($obj->id);
		$cloud->setTags($tags, User::get('id'), 1);

		// Save params
		$incoming   = Request::getVar( 'params', array() );
		if (!empty($incoming))
		{
			foreach ($incoming as $key=>$value)
			{
				if ($key == 'quota' || $key == 'pubQuota')
				{
					// convert GB to bytes
					$value = Helpers\Html::convertSize( floatval($value), 'GB', 'b');
				}

				$obj->saveParam($id, $key, htmlentities($value));
			}
		}

		// Add members if specified
		$this->_saveMember();

		// Send message
		if ($this->config->get('messaging', 0) && $sendmail && count($managers) > 0)
		{
			// Email config
			$from 			= array();
			$from['name']  	= Config::get('config.sitename').' ' . Lang::txt('COM_PROJECTS');
			$from['email'] 	= Config::get('config.mailfrom');

			// Html email
			$from['multipart'] = md5(date('U'));

			// Get message body
			$eview 					= new \Hubzero\Component\View( array('name'=>'emails', 'layout' => 'admin_plain' ) );
			$eview->option 			= $this->_option;
			$eview->subject 		= $subject;
			$eview->action 			= $action;
			$eview->project 		= $project;
			$eview->message			= $message;

			$body = array();
			$body['plaintext'] 	= $eview->loadTemplate();
			$body['plaintext'] 	= str_replace("\n", "\r\n", $body['plaintext']);

			// HTML email
			$eview->setLayout('admin_html');
			$body['multipart'] = $eview->loadTemplate();
			$body['multipart'] = str_replace("\n", "\r\n", $body['multipart']);

			// Send HUB message
			\JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher = \JDispatcher::getInstance();
			$dispatcher->trigger( 'onSendMessage',
				array( 'projects_admin_notice', $subject, $body, $from, $managers, $this->_option ));
		}

		// Redirect to edit view?
		if ($redirect)
		{
			$this->_redirect = Route::url('index.php?option=' . $this->_option . '&task=edit&id=' . $id, false);
		}
		else
		{
			$this->_redirect = Route::url('index.php?option=' . $this->_option, false);
		}
	}

	/**
	 * Save member
	 *
	 * @return     void
	 */
	protected function _saveMember()
	{
		// New member added?
		$members 	= urldecode(trim(Request::getVar( 'newmember', '', 'post'  )));
		$role 		= Request::getInt( 'role', 0 );
		$id 		= Request::getVar( 'id', 0 );

		// Get owner class
		$objO = new Tables\Owner($this->database);

		$mbrs = explode(',', $members);

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$profile = \Hubzero\User\Profile::getInstance( trim($mbr) );

			// Ensure we found an account
			if ($profile)
			{
				$objO->saveOwners ( $id, User::get('id'), $profile->get('uidNumber'), 0, $role, $status = 1, 0);
			}
		}
	}

	/**
	 * Redirects
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option, false)
		);
	}

	/**
	 * Erases all project information (to be used for test projects only)
	 *
	 * @return     void
	 */
	public function eraseTask()
	{
		$id = Request::getVar( 'id', 0 );
		$permanent = 1;
		jimport('joomla.filesystem.folder');

		// Initiate extended database class
		$obj = new Tables\Project( $this->database );
		if (!$id or !$obj->loadProject($id))
		{
			$this->setRedirect(Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('COM_PROJECTS_NOTICE_ID_NOT_FOUND'),
				'error');
			return;
		}

		// Get project group
		$group_prefix = $this->config->get('group_prefix', 'pr-');
		$prgroup = $group_prefix.$obj->alias;

		// Store project info
		$alias = $obj->alias;
		$identifier = $alias;

		// Delete project
		$obj->delete();

		// Erase all owners
		$objO = new Tables\Owner ($this->database );
		$objO->removeOwners ( $id, '', 0, $permanent, '', $all = 1 );

		// Erase owner group
		$group = new \Hubzero\User\Group();
		$group->read( $prgroup );
		if ($group)
		{
			$group->delete();
		}

		// Erase all comments
		$objC = new Tables\Comment ($this->database );
		$objC->deleteProjectComments ( $id, $permanent );

		// Erase all activities
		$objA = new Tables\Activity( $this->database );
		$objA->deleteActivities( $id, $permanent );

		// Erase all todos
		$objTD = new Tables\Todo( $this->database );
		$objTD->deleteTodos( $id, '', $permanent );

		// Erase all blog entries
		$objB = new Tables\Blog( $this->database );
		$objB->deletePosts( $id, $permanent );

		// Erase all notes
		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'attachment.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'author.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'comment.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'log.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'revision.php');

		if (is_file(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'config.php'))
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'config.php');
		}
		$masterscope = 'projects' . DS . $alias . DS . 'notes';

		// Get all notes
		$this->database->setQuery( "SELECT DISTINCT p.id FROM #__wiki_page AS p
			WHERE p.group_cn='" . $prgroup . "' AND p.scope LIKE '" . $masterscope . "%' " );
		$notes = $this->database->loadObjectList();

		if ($notes)
		{
			foreach ($notes as $note)
			{
				$page = new \Components\Wiki\Tables\Page( $this->database );

				// Delete the page's history, tags, comments, etc.
				$page->deleteBits( $note->id );

				// Finally, delete the page itself
				$page->delete( $note->id );
			}
		}

		// Erase all files, remove files repository
		if ($alias)
		{
			\JPluginHelper::importPlugin( 'projects', 'files' );
			$dispatcher = \JDispatcher::getInstance();
			$dispatcher->trigger( 'eraseRepo', array($alias) );

			// Delete base dir for .git repos
			$dir 		= $alias;
			$prefix 	= $this->config->get('offroot', 0) ? '' : PATH_CORE ;
			$repodir 	= DS . trim($this->config->get('webpath'), DS);
			$path 		= $prefix . $repodir . DS . $dir;

			if (is_dir($path))
			{
				\JFolder::delete($path);
			}

			// Delete images/preview directories
			$webdir = DS . trim($this->config->get('imagepath', '/site/projects'), DS);
			$webpath = PATH_APP . $webdir . DS . $dir;

			if (is_dir($webpath))
			{
				\JFolder::delete($webpath);
			}
		}

		// Erase all publications
		if ($this->_publishing)
		{
			// TBD
		}

		// Redirect
		$this->setRedirect(
			Route::url('index.php?option='.$this->_option, false),
			Lang::txt('COM_PROJECTS_PROJECT') . ' #' . $id . ' ('.$alias.') ' . Lang::txt('COM_PROJECTS_PROJECT_ERASED')
		);
	}

	/**
	 * Add and commit untracked/changed files
	 *
	 * This is helpful in case git add/commit failed during file upload
	 *
	 * @return     void
	 */
	public function gitaddTask()
	{
		$id   = Request::getVar( 'id', 0 );
		$file = Request::getVar( 'file', '' );

		// Initiate extended database class
		$obj = new Tables\Project( $this->database );
		if (!$id or !$obj->loadProject($id))
		{
			$this->setRedirect(Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('COM_PROJECTS_NOTICE_ID_NOT_FOUND'),
				'error'
			);
			return;
		}

		$url = Route::url('index.php?option=' . $this->_option . '&task=edit&id=' . $id, false);

		if (!$file)
		{
			$this->setRedirect($url,
				Lang::txt('Please specify a file/directory path to add and commit into project'),
				'error'
			);
			return;
		}

		// Delete base dir for .git repos
		$prefix  = $this->config->get('offroot', 0) ? '' : PATH_APP ;
		$repodir = trim($this->config->get('webpath'), DS);
		$path    = $prefix . DS . $repodir . DS . $obj->alias . DS . 'files';

		if (!is_file($path . DS . $file))
		{
			$this->setRedirect($url,
				Lang::txt('Error: File not found in the project, cannot add and commit'),
				'error');
			return;
		}

		// Git helper
		require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'githelper.php');
		$gitHelper = new Helpers\Git($path);

		$commitMsg = '';

		// Git add & commit
		$gitHelper->gitAdd($file, $commitMsg);
		$gitHelper->gitCommit($commitMsg);

		// Redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&task=edit&id=' . $id, false),
			Lang::txt('File checked into project Git repo')
		);
	}

	/**
	 * Optimize git repo
	 *
	 * @return     void
	 */
	public function gitgcTask()
	{
		$id = Request::getVar( 'id', 0 );

		$project = new Models\Project($id);
		if (!$project->exists())
		{
			$this->setRedirect(Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('COM_PROJECTS_NOTICE_ID_NOT_FOUND'),
				'error');
			return;
		}

		// Get Disk Usage
		\JPluginHelper::importPlugin( 'projects', 'files' );
		$dispatcher = \JDispatcher::getInstance();

		$content = $dispatcher->trigger( 'advoptimize', array( $project, 'local'));

		// Redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&task=edit&id=' . $id, false),
			Lang::txt('Git repo optimized')
		);
	}

	/**
	 * Unlock sync and view sync log for project
	 *
	 * @return     void
	 */
	public function fixsyncTask()
	{
		$id = Request::getVar( 'id', 0 );
		$service = 'google';

		// Initiate extended database class
		$obj = new Tables\Project( $this->database );
		if (!$id or !$obj->loadProject($id))
		{
			$this->setRedirect(Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('COM_PROJECTS_NOTICE_ID_NOT_FOUND'),
				'error');
			return;
		}

		// Unlock sync
		$obj->saveParam($id, $service . '_sync_lock', '');

		// Get log file
		$repodir = Helpers\Html::getProjectRepoPath($obj->alias, 'logs');
		$sfile 	 = $repodir . DS . 'sync.' . \JFactory::getDate()->format('Y-m') . '.log';

		if (file_exists($sfile))
		{
			// Serve up file
			$xserver = new \Hubzero\Content\Server();
			$xserver->filename($sfile);
			$xserver->disposition('attachment');
			$xserver->acceptranges(false);
			$xserver->saveas('sync.' . \JFactory::getDate()->format('Y-m') . '.txt');
			$result = $xserver->serve_attachment($sfile, 'sync.' . \JFactory::getDate()->format('Y-m') . '.txt', false);
			exit;
		}

		// Redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&task=edit&id=' . $id, false),
			Lang::txt('Sync log unavailable')
		);
	}
}
