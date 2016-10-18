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

namespace Components\Projects\Site\Controllers;

use Components\Projects\Tables;
use Components\Projects\Models;
use Components\Projects\Helpers;
use Components\Projects\Models\Orm\Description;
use Components\Projects\Models\Orm\Description\Field;
use Exception;
use stdClass;

require_once Component::path('com_projects') . DS . 'models' . DS . 'orm' . DS . 'description.php';
require_once Component::path('com_projects') . DS . 'models' . DS . 'orm' . DS . 'description' . DS . 'field.php';

/**
 * Primary component controller
 */
class Projects extends Base
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		// Set the default task
		$this->registerTask('__default', 'intro');

		// Register tasks
		$this->registerTask('suspend', 'changestate');
		$this->registerTask('reinstate', 'changestate');
		$this->registerTask('fixownership', 'changestate');
		$this->registerTask('delete', 'changestate');

		parent::execute();
	}

	/**
	 * Return results for autocompleter
	 *
	 * @return  string  JSON
	 */
	public function autocompleteTask()
	{
		$filters = array(
			'limit'    => 20,
			'start'    => 0,
			'admin'    => 0,
			'search'   => trim(Request::getString('value', '')),
			'getowner' => 1
		);

		// Get records
		$rows = $this->model->entries('list', $this->view->filters, false);

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$title = str_replace("\n", '', stripslashes(trim($row->get('title'))));
				$title = str_replace("\r", '', $title);

				$item = array(
					'id'   => $row->get('alias'),
					'name' => $title
				);

				// Push exact matches to the front
				if ($row->get('alias') == $filters['search'])
				{
					array_unshift($json, $item);
				}
				else
				{
					$json[] = $item;
				}
			}
		}

		echo json_encode($json);
	}

	/**
	 * Intro to projects (main view)
	 *
	 * @return  void
	 */
	public function introTask()
	{
		// Set task
		$this->_task = 'intro';

		// Incoming
		$action  = Request::getVar('action', '');

		// When logging in
		if (User::isGuest() && $action == 'login')
		{
			$this->_msg = Lang::txt('COM_PROJECTS_LOGIN_TO_VIEW_YOUR_PROJECTS');
			$this->_login();
			return;
		}

		// Filters
		$this->view->filters = array();
		$this->view->filters['mine']    = 1;
		$this->view->filters['updates'] = 1;
		$this->view->filters['sortby']  = 'myprojects';

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();
		$this->view->title = $this->title;

		// Log activity
		$this->_logActivity();

		// Output HTML
		$this->view->option     = $this->_option;
		$this->view->model      = $this->model;
		$this->view->publishing = $this->_publishing;
		$this->view->msg        = isset($this->_msg) && $this->_msg
								? $this->_msg
								: $this->_getNotifications('success');

		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->setLayout('intro')
					->display();
	}

	/**
	 * Features page
	 *
	 * @return     void
	 */
	public function featuresTask()
	{
		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();
		$this->view->title = $this->title;

		// Log activity
		$this->_logActivity();

		// Output HTML
		$this->view->option     = $this->_option;
		$this->view->config     = $this->config;
		$this->view->publishing = $this->_publishing;

		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		$this->view->display();
	}

	/**
	 * Browse projects
	 *
	 * @return     void
	 */
	public function browseTask()
	{
		// Incoming
		$reviewer = Request::getWord('reviewer', '');
		$action   = Request::getVar('action', '');

		// Set the pathway
		$this->_task = 'browse';
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Check reviewer authorization
		if ($reviewer && User::isGuest())
		{
			$this->_msg = Lang::txt('COM_PROJECTS_LOGIN_REVIEWER');
			$this->_login();
			return;
		}
		if ($reviewer && !$this->model->reviewerAccess($reviewer))
		{
			$this->view = new \Hubzero\Component\View(array('name'=>'error', 'layout' =>'default'));
			$this->view->error  = Lang::txt('COM_PROJECTS_REVIEWER_RESTRICTED_ACCESS');
			$this->view->title = $reviewer == 'sponsored'
						 ? Lang::txt('COM_PROJECTS_REVIEWER_SPS')
						 : Lang::txt('COM_PROJECTS_REVIEWER_HIPAA');
			$this->view->display();
			return;
		}

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit'] = Request::getInt(
			'limit',
			intval($this->config->get('limit', 25)),
			'request'
		);
		$this->view->filters['start']    = Request::getInt('limitstart', 0, 'get');
		$this->view->filters['sortby']   = strtolower(Request::getWord('sortby', 'title'));
		$this->view->filters['search']   = Request::getVar('search', '');
		$this->view->filters['sortdir']  = strtoupper(Request::getWord('sortdir', 'ASC'));
		$this->view->filters['reviewer'] = $reviewer;
		$this->view->filters['filterby'] = 'all';

		if (!in_array($this->view->filters['sortby'], array('title', 'id', 'myprojects', 'owner', 'created', 'type', 'role', 'privacy', 'status')))
		{
			$this->view->filters['sortby'] = 'title';
		}

		if (!in_array($this->view->filters['sortdir'], array('DESC', 'ASC')))
		{
			$this->view->filters['sortdir'] = 'ASC';
		}

		if ($reviewer == 'sensitive' || $reviewer == 'sponsored')
		{
			$this->view->filters['filterby'] = Request::getWord('filterby', 'pending');
		}

		// Login for private projects
		if (User::isGuest() && $action == 'login')
		{
			$this->_msg = Lang::txt('COM_PROJECTS_LOGIN_TO_VIEW_PRIVATE_PROJECTS');
			$this->_login();
			return;
		}

		// Log activity
		$this->_logActivity(0, 'general', 'browse');

		// Output HTML
		$this->view->model    = $this->model;
		$this->view->option   = $this->_option;
		$this->view->config   = $this->config;
		$this->view->title    = $this->title;
		$this->view->reviewer = $reviewer;
		$this->view->msg      = $this->_getNotifications('success');
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->display();
	}

	/**
	 * Project view
	 *
	 * @return     void
	 */
	public function viewTask()
	{
		// Incoming
		$preview      = Request::getInt('preview', 0);
		$this->active = Request::getVar('active', '');
		$ajax         = Request::getInt('ajax', 0);
		$action       = Request::getVar('action', '');
		$confirmcode  = Request::getVar('confirm', '');
		$email        = Request::getVar('email', '');
		$sync         = false;

		// Stop ajax action if user got logged out
		if ($ajax && User::isGuest())
		{
			// Project on hold
			$this->view = new \Hubzero\Component\View(array('name' => 'error', 'layout' => 'default'));
			$this->view->error  = Lang::txt('COM_PROJECTS_PROJECT_RELOGIN');
			$this->view->title  = Lang::txt('COM_PROJECTS_PROJECT_RELOGIN_REQUIRED');
			$this->view->display();
			return;
		}

		// Check that project exists
		if (!$this->model->exists())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_NOT_FOUND'), 404);
		}

		// Is this a group project?
		$this->group = $this->model->groupOwner();
		if ($this->model->get('owned_by_group') && !$this->group)
		{
			$this->_buildPathway();
			$this->_buildTitle();

			// Options for project creator
			if ($this->model->access('owner'))
			{
				$view = new \Hubzero\Component\View(
					array('name' => 'changeowner', 'layout' => 'default')
				);
				$view->project = $this->model;
				$view->task    = $this->_task;
				$view->option  = $this->_option;
				$view->display();
				return;
			}
			else
			{
				// Error
				$this->setError(Lang::txt('COM_PROJECTS_PROJECT_OWNER_DELETED'));
				$this->title = Lang::txt('COM_PROJECTS_PROJECT_OWNERSHIP_ERROR');
				$this->_showError();
				return;
			}
		}

		// Load acting team member
		$member = $this->model->member();

		// Reconcile members of project groups
		if (!$ajax)
		{
			if ($this->model->_tblOwner->reconcileGroups(
				$this->model->get('id'),
				$this->model->get('owned_by_group')
			))
			{
				$sync = true;
			}
		}

		// Is project deleted?
		if ($this->model->isDeleted())
		{
			$this->setError(Lang::txt('COM_PROJECTS_PROJECT_DELETED'));
			$this->introTask();
			return;
		}

		// Check if project is in setup
		if ($this->model->inSetup() && !$ajax)
		{
			App::redirect(Route::url('index.php?option=' . $this->_option . '&task=setup&alias=' . $this->model->get('alias')));
			return;
		}

		// Sync with system group in case of changes
		if ($sync == true)
		{
			$this->model->_tblOwner->sysGroup(
				$this->model->get('alias'),
				$this->config->get('group_prefix', 'pr-')
			);

			// Reload member
			$this->model->member(true);
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Do we need to login?
		if (User::isGuest() && $action == 'login')
		{
			$this->_msg = Lang::txt('COM_PROJECTS_LOGIN_TO_VIEW_PROJECT');
			$this->_login();
			return;
		}

		// Determine layout to load
		$layout = ($this->model->access('member')) ? 'internal' : 'external';
		$layout = $this->model->access('member')
				&& $preview && $this->model->isPublic()
				? 'external' : $layout;

		// Is this a provisioned project?
		if ($this->model->isProvisioned())
		{
			if (!$this->_publishing)
			{
				$this->setError(Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD'));
				$this->introTask();
				return;
			}

			// Redirect to publication
			$pub = $this->model->getPublication();
			if ($pub && $pub->id)
			{
				App::redirect(Route::url('index.php?option=com_publications&task=submit&pid=' . $pub->id));
				return;
			}
			else
			{
				throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_NOT_FOUND'), 404);
			}
			$this->view->pub       = $pub;
			$this->view->team      = $this->model->_tblOwner->getOwnerNames($this->model->get('id'));
			$this->view->suggested = Helpers\Html::suggestAlias($pub->title);
			$this->view->verified  = $this->model->check($this->view->suggested, $this->model->get('id'), 0);
			$this->view->suggested = $this->view->verified ? $this->view->suggested : '';
		}

		// Check if they are a reviewer
		$reviewer = false;
		if (!$this->model->access('member'))
		{
			if ($this->model->reviewerAccess('sensitive'))
			{
				$reviewer = 'sensitive';
			}
			if ($this->model->reviewerAccess('sponsored'))
			{
				$reviewer = 'sponsored';
			}
		}

		// Invitation view
		if ($confirmcode && (!$member or $member->status != 1))
		{
			$match = $this->model->_tblOwner->matchInvite(
				$this->model->get('id'),
				$confirmcode,
				$email
			);

			if (User::isGuest() && $match)
			{
				$layout = 'invited';
			}
			elseif ($match && $this->model->_tblOwner->load($match))
			{
				if (User::get('email') == $email)
				{
					// Confirm user
					$this->model->_tblOwner->status = 1;
					$this->model->_tblOwner->userid = User::get('id');

					if (!$this->model->_tblOwner->store())
					{
						$this->setError($this->model->_tblOwner->getError());
						return false;
					}
					else
					{
						// Sync with system group
						$this->model->_tblOwner->sysGroup(
							$this->model->get('alias'),
							$this->config->get('group_prefix', 'pr-')
						);

						// Go to project page
						App::redirect(Route::url($this->model->link()));
						return;
					}
				}
				else
				{
					// Error - different email
					$this->setError(Lang::txt('COM_PROJECTS_INVITE_DIFFERENT_EMAIL'));
					$this->_showError();
					return;
				}
			}
		}

		// Private project
		if (!$this->model->isPublic() && $layout != 'invited')
		{
			// Login required
			if (User::isGuest())
			{
				$this->_msg = Lang::txt('COM_PROJECTS_LOGIN_PRIVATE_PROJECT_AREA');
				$this->_login();
				return;
			}
			if (!$this->model->access('member') && !$reviewer)
			{
				throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			}
		}

		// Is project suspended?
		if ($this->model->isInactive())
		{
			if (!$this->model->access('member'))
			{
				throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			}
			$layout = 'suspended';
		}

		// Is project pending approval?
		if ($this->model->isPending())
		{
			if ($reviewer)
			{
				$layout = 'external';
			}
			elseif (!$this->model->access('owner'))
			{
				throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			}
			else
			{
				$layout = 'pending';
			}
		}

		// Set layout
		$this->view->setLayout($layout);

		// Get available plugins
		$plugins = Event::trigger('projects.onProjectAreas', array($this->model->get('alias')));

		// Get tabbed plugins
		$this->view->tabs = Helpers\Html::getTabs($plugins);

		// Go through plugins
		$this->view->content = '';
		if ($layout == 'internal')
		{
			$plugin = $this->active;

			// Get active plugins (some may not be in tabs)
			$activePlugins = Helpers\Html::getPluginNames($plugins);

			if (!$plugin && !empty($activePlugins))
			{
				$plugin = $activePlugins[0];
				$this->active = $plugin;
			}

			// Get plugin content
			// Do not go further if plugin is inactive or does not exist
			if (!in_array($plugin, $activePlugins))
			{
				if ($ajax)
				{
					// Plugin not active in this project
					echo '<p class="error">' . Lang::txt('COM_PROJECTS_ERROR_CONTENT_CANNOT_LOAD') . '</p>';
					return;
				}

				App::redirect(Route::url($this->model->link()));
				return;
			}

			// Plugin params
			$plugin_params = array(
				$this->model,
				$action,
				array($plugin)
			);

			// Get plugin content
			$sections = Event::trigger('projects.onProject', $plugin_params);

			// Output
			if (!empty($sections))
			{
				foreach ($sections as $section)
				{
					if (isset($section['html']) && $section['html'])
					{
						if ($ajax)
						{
							// AJAX output
							echo $section['html'];
							return;
						}
						else
						{
							// Normal output
							$this->view->content .= $section['html'];
						}
					}
				}
			}
			/*else
			{
				// No html output
				App::redirect(Route::url($this->model->link()));
				return;
			}*/

			// Get item counts
			$counts = Event::trigger('projects.onProjectCount', array($this->model));
			$this->model->set('counts', Helpers\Html::getCountArray($counts));
		}

		// Output HTML
		$this->view->params   = $this->model->params;
		$this->view->model    = $this->model;
		$this->view->reviewer = $reviewer;
		$this->view->title    = $this->title;
		$this->view->active   = $this->active;
		$this->view->task     = $this->_task;
		$this->view->option   = $this->_option;
		$this->view->config   = $this->config;
		$this->view->msg      = $this->_getNotifications('success');

		/// Info block moved to info plugin
		$this->view->info = array();


		if ($layout == 'invited')
		{
			$this->view->confirmcode = $confirmcode;
			$this->view->email       = $email;
		}

		$error = $this->getError() ? $this->getError() : $this->_getNotifications('error');
		if ($error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
		return;
	}

	/**
	 * Activate provisioned project
	 *
	 * @return  void
	 */
	public function activateTask()
	{
		// Cannot proceed without project id/alias
		if (!$this->model->exists())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_NOT_FOUND'), 404);
		}

		// Must be project creator
		if (!$this->model->access('owner'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		// Must be a provisioned project to be activated
		if (!$this->model->isProvisioned())
		{
			// Redirect to project page
			App::redirect(Route::url($this->model->link()));
			return;
		}

		// Redirect to setup if activation not complete
		if ($this->model->inSetup())
		{
			App::redirect(Route::url($this->model->link('setup')));
			return;
		}

		// Get publication of a provisioned project
		$pub = $this->model->getPublication();

		if (empty($pub))
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_NOT_FOUND'), 404);
		}

		// Incoming
		$name    = trim(Request::getVar('new-alias', '', 'post'));
		$title   = trim(Request::getVar('title', '', 'post'));
		$confirm = trim(Request::getInt('confirm', 0, 'post'));

		$name = preg_replace('/ /', '', $name);
		$name = strtolower($name);

		// Check incoming data
		$verified = $this->model->check($name, $this->model->get('id'));
		if ($confirm && !$verified)
		{
			$error = $this->model->getError() ? $this->model->getError() : Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID_OR_EMPTY');
			$this->setError($error);
		}
		elseif ($confirm && ($title == '' || strlen($title) < 3))
		{
			$this->setError(Lang::txt('COM_PROJECTS_ERROR_TITLE_SHORT_OR_EMPTY'));
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Display page
		if (!$confirm || $this->getError())
		{
			$this->view->setLayout('provisioned');
			$this->view->model = $this->model;

			$this->view->team  = $this->model->_tblOwner->getOwnerNames($this->model->get('alias'));

			// Output HTML
			$this->view->pub        = isset($pub) ? $pub : '';
			$this->view->suggested  = $name;
			$this->view->verified   = $verified;
			$this->view->suggested  = $verified ? $this->view->suggested : '';
			$this->view->title      = $this->title;
			$this->view->active     = $this->active;
			$this->view->task       = $this->_task;
			$this->view->authorized = 1;
			$this->view->option     = $this->_option;
			$this->view->msg        = $this->_getNotifications('success');

			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}

			$this->view->display();
			return;
		}

		// Save new alias & title
		if (!$this->getError())
		{
			$this->model->set('title', \Hubzero\Utility\String::truncate($title, 250));
			$this->model->set('alias', $name);

			$state = $this->_setupComplete == 3 ? 0 : 1;

			$this->model->set('state', $state);
			$this->model->set('setup_stage', 2);
			$this->model->set('provisioned', 0);
			$this->model->set('modified', Date::toSql());
			$this->model->set('modified_by', User::get('id'));

			// Save changes
			if (!$this->model->store())
			{
				$this->setError($this->model->getError());
			}
		}

		// Get project parent directory
		$path    =  Helpers\Html::getProjectRepoPath($this->model->get('alias'));
		$newpath =  Helpers\Html::getProjectRepoPath($name, 'files', true);

		// Rename project parent directory
		if ($path && is_dir($path))
		{
			if (!Filesystem::copyDirectory($path, $newpath))
			{
				$this->setError(Lang::txt('COM_PROJECTS_FAILED_TO_COPY_FILES'));
			}
			else
			{
				// Correct permissions to 0755
				Filesystem::setPermissions($newpath);

				// Delete original repo
				Filesystem::deleteDirectory($path);
			}
		}

		// Log activity
		$this->_logActivity($this->model->get('id'), 'provisioned', 'activate', 'save', 1);

		// Send to continue setup
		App::redirect(Route::url($this->model->link('setup')));
		return;
	}

	/**
	 * Change project status
	 *
	 * @return  void
	 */
	public function changestateTask()
	{
		// Cannot proceed without project id/alias
		if (!$this->model->exists() || $this->model->isDeleted())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_NOT_FOUND'), 404);
		}

		// Already suspended
		if ($this->_task == 'suspend' && $this->model->isInactive())
		{
			App::redirect(Route::url($this->model->link()));
			return;
		}

		// Suspended by admin: manager cannot activate
		if ($this->_task == 'reinstate')
		{
			$suspended = $this->model->checkActivity(Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED'));
			if ($suspended == 1)
			{
				throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			}
		}

		// Login required
		if (User::isGuest())
		{
			$this->_msg = Lang::txt('COM_PROJECTS_LOGIN_PRIVATE_PROJECT_AREA');
			$this->_login();
			return;
		}

		// Only managers can change project state
		if (!$this->model->access('manager'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		// Fix ownership?
		if ($this->_task == 'fixownership')
		{
			$keep = Request::getInt('keep', 0);

			if (!$this->model->access('owner'))
			{
				throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			}
			if (!$this->model->groupOwner('id'))
			{
				// Nothing to fix
				App::redirect(Route::url($this->model->link()));
				return;
			}
			$this->model->set('owned_by_group', 0);

			// Make sure creator is still in team
			$objO = $this->model->table('Owner');
			$objO->saveOwners($this->model->get('id'), User::get('id'), User::get('id'), 0, 1, 1, 1);

			// Remove owner group affiliation for all team members
			$objO->removeGroupDependence($this->model->get('id'), $this->model->groupOwner('id'));

			if ($keep)
			{
				$this->model->set('owned_by_user', User::get('id'));
			}
			else
			{
				$this->model->set('state', 2);
			}
		}

		// Update project
		$this->model->set('modified', Date::toSql());
		$this->model->set('modified_by', User::get('id'));

		if ($this->_task != 'fixownership')
		{
			$state = $this->_task == 'suspend' ? 0 : 1;
			$state = $this->_task == 'delete' ? 2 : $this->model->get('state');
			$this->model->set('state', $state);
		}

		if (!$this->model->store())
		{
			$this->setError($this->model->getError());
			return false;
		}

		// Log activity
		$this->_logActivity($this->model->get('id'), 'project', 'status', $this->_task, 1);

		if ($this->_task != 'fixownership')
		{
			// Add activity
			$activity = ($this->_task == 'suspend')
				? Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED')
				: Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT_REINSTATED');

			if ($this->_task == 'delete')
			{
				$activity = Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT_DELETED');
			}
			$this->model->recordActivity($activity);

			// Send to project page
			$this->_msg = $this->_task == 'suspend'
				? Lang::txt('COM_PROJECTS_PROJECT_SUSPENDED')
				: Lang::txt('COM_PROJECTS_PROJECT_REINSTATED');

			if ($this->_task == 'delete')
			{
				$this->setError(Lang::txt('COM_PROJECTS_PROJECT_DELETED'));
				$this->introTask();
				return;
			}
		}

		$this->_task = 'view';
		$this->viewTask();
		return;
	}

	/**
	 * Authenticate for outside services
	 *
	 * @return  void
	 */
	public function authTask()
	{
		// Incoming
		$error  = Request::getVar('error', '', 'get');
		$code   = Request::getVar('code', '', 'get');

		$state  = Request::getVar('state', '', 'get');
		$json	=  base64_decode($state);
		$json 	=  json_decode($json);

		$this->_identifier = $json->alias;
		$this->model = new Models\Project($this->_identifier);

		$service = $json->service ? $json->service : 'google';

		// Successful authorization grant, fetch the access token
		if ($code)
		{
			if (!$this->model->exists())
			{
				throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_NOT_FOUND'), 404);
			}
			$return  = Route::url('index.php?option=' . $this->_option . '&alias=' . $this->model->get('alias') . '&active=files&action=connect');
			$return .= '?service=' . $service;
			$return .= '&code=' . $code;
		}
		elseif (isset($json->return))
		{
			$return = $json->return . '&service=' . $service;
		}

		// Catch errors
		if ($error)
		{
			$error =  $error == 'access_denied'
				? Lang::txt('COM_PROJECTS_FILES_ERROR_CONNECT_PERMISSION')
				: Lang::txt('COM_PROJECTS_FILES_ERROR_CONNECT_NOW');
			$this->_setNotification($error, 'error');
			$return = $json->return;
		}

		App::redirect($return);
		return;
	}

	/**
	 * Reviewers actions (sensitive data, sponsored research)
	 *
	 * @return     void
	 */
	public function processTask()
	{
		// Incoming
		$reviewer = Request::getWord('reviewer', '');
		$action   = Request::getVar('action', '');
		$comment  = Request::getVar('comment', '');
		$approve  = Request::getInt('approve', 0);
		$filterby = Request::getVar('filterby', 'pending');
		$notify   = Request::getVar('notify', 0, 'post');

		// Cannot proceed without project id/alias
		if (!$this->model->exists() || $this->model->isDeleted())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_NOT_FOUND'), 404);
		}

		// Authorize
		if (!$this->model->reviewerAccess($reviewer))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Get project params
		$params = $this->model->params;

		if ($action == 'save' && !$this->getError())
		{
			$cbase = $this->model->get('admin_notes');

			// Meta data for comment
			$meta = '<meta>' . Date::of('now')->toLocal('M d, Y') . ' - ' . User::get('name') . '</meta>';

			// Save approval
			if ($reviewer == 'sensitive')
			{
				$approve = $approve == 1 && $this->model->get('state') == 5 ? 1 : 0; // can only approve pending project
				$state = $approve ? 1 : $this->model->get('state');
				$this->model->set('state', $state);
			}
			elseif ($reviewer == 'sponsored')
			{
				$grant_agency   = Request::getVar('grant_agency', '');
				$grant_title    = Request::getVar('grant_title', '');
				$grant_PI       = Request::getVar('grant_PI', '');
				$grant_budget   = Request::getVar('grant_budget', '');
				$grant_approval = Request::getVar('grant_approval', '');
				$rejected       = Request::getVar('rejected', 0);

				// New approval
				if (trim($params->get('grant_approval')) == '' && trim($grant_approval) != ''
				&& $params->get('grant_status') != 1 && $rejected != 1)
				{
					// Increase
					$approve = 1;

					// Bump up quota
					$premiumQuota = Helpers\Html::convertSize(floatval($this->config->get('premiumQuota', '30')), 'GB', 'b');
					$this->model->saveParam('quota', $premiumQuota);

					// Bump up publication quota
					$premiumPubQuota = Helpers\Html::convertSize(floatval($this->config->get('premiumPubQuota', '10')), 'GB', 'b');
					$this->model->saveParam('pubQuota', $premiumPubQuota);
				}

				// Reject
				if ($rejected == 1 && $params->get('grant_status') != 2)
				{
					$approve = 2;
				}

				$this->model->saveParam('grant_budget', $grant_budget);
				$this->model->saveParam('grant_agency', $grant_agency);
				$this->model->saveParam('grant_title', $grant_title);
				$this->model->saveParam('grant_PI', $grant_PI);
				$this->model->saveParam('grant_approval', $grant_approval);
				if ($approve)
				{
					$this->model->saveParam('grant_status', $approve);
				}
			}

			// Save comment
			if (trim($comment) != '')
			{
				$comment = \Hubzero\Utility\String::truncate($comment, 500);
				$comment = \Hubzero\Utility\Sanitize::stripAll($comment);
				if (!$approve)
				{
					$cbase  .= '<nb:' . $reviewer . '>' . $comment . $meta . '</nb:' . $reviewer . '>';
				}
			}
			if ($approve)
			{
				if ($reviewer == 'sensitive')
				{
					$cbase  .= '<nb:' . $reviewer . '>' . Lang::txt('COM_PROJECTS_PROJECT_APPROVED_HIPAA');
					$cbase  .= (trim($comment) != '') ? ' ' . $comment : '';
					$cbase  .= $meta . '</nb:' . $reviewer . '>';
				}
				if ($reviewer == 'sponsored')
				{
					if ($approve == 1)
					{
						$cbase  .= '<nb:' . $reviewer . '>' . Lang::txt('COM_PROJECTS_PROJECT_APPROVED_SPS') . ' '
						. ucfirst(Lang::txt('COM_PROJECTS_APPROVAL_CODE')) . ': ' . $grant_approval;
						$cbase  .= (trim($comment) != '') ? '. ' . $comment : '';
						$cbase  .= $meta . '</nb:' . $reviewer . '>';
					}
					elseif ($approve == 2)
					{
						$cbase  .= '<nb:' . $reviewer . '>' . Lang::txt('COM_PROJECTS_PROJECT_REJECTED_SPS');
						$cbase  .= (trim($comment) != '') ? ' ' . $comment : '';
						$cbase  .= $meta . '</nb:' . $reviewer . '>';
					}
				}
			}

			$this->model->set('admin_notes', $cbase);

			// Save changes
			if ($approve || $comment)
			{
				if (!$this->model->store())
				{
					$this->setError($this->model->getError());
				}

				$admingroup = $reviewer == 'sensitive'
					? $this->config->get('sdata_group', '')
					: $this->config->get('ginfo_group', '');

				if (\Hubzero\User\Group::getInstance($admingroup))
				{
					$admins = Helpers\Html::getGroupMembers($admingroup);
					$admincomment = $comment
						? User::get('name') . ' ' . Lang::txt('COM_PROJECTS_SAID') . ': ' . $comment
						: '';

					// Send out email to admins
					if (!empty($admins))
					{
						Helpers\Html::sendHUBMessage(
							$this->_option,
							$this->model,
							$admins,
							Lang::txt('COM_PROJECTS_EMAIL_ADMIN_REVIEWER_NOTIFICATION'),
							'projects_new_project_admin',
							'admin',
							$admincomment,
							$reviewer
						);
					}
				}
			}

			// Pass success or error message
			if ($this->getError())
			{
				$this->_setNotification($this->getError(), 'error');
			}
			else
			{
				if ($approve)
				{
					if ($reviewer == 'sensitive')
					{
						$this->_setNotification(Lang::txt('COM_PROJECTS_PROJECT_APPROVED_HIPAA_MSG'));

						// Send out emails to team members
						$this->_notifyTeam();
					}
					if ($reviewer == 'sponsored')
					{
						$notification =  $approve == 2
								? Lang::txt('COM_PROJECTS_PROJECT_REJECTED_SPS_MSG')
								: Lang::txt('COM_PROJECTS_PROJECT_APPROVED_SPS_MSG');
						$this->_setNotification($notification);
					}
				}
				elseif ($comment)
				{
					$this->_setNotification(Lang::txt('COM_PROJECTS_REVIEWER_COMMENT_POSTED'));
				}

				// Add to project activity feed
				if ($notify)
				{
					$activity = '';
					if ($approve && $reviewer == 'sponsored')
					{
						$activity = $approve == 2
								? Lang::txt('COM_PROJECTS_PROJECT_REJECTED_SPS_ACTIVITY')
								: Lang::txt('COM_PROJECTS_PROJECT_APPROVED_SPS_ACTIVITY');
					}
					elseif ($comment)
					{
						$activity = Lang::txt('COM_PROJECTS_PROJECT_REVIEWER_COMMENTED');
					}

					if ($activity)
					{
						$aid = $this->model->recordActivity($activity, $this->model->get('id'), '', '', 'admin', 0, 1, 1);

						// Append comment to activity
						if ($comment && $aid)
						{
							$objC = new Tables\Comment($this->database);
							$cid = $objC->addComment($aid, 'activity', $comment, User::get('id'), $aid, 1);

							if ($cid)
							{
								$caid = $this->model->recordActivity(
									Lang::txt('COM_PROJECTS_COMMENTED') . ' '
									. Lang::txt('COM_PROJECTS_ON') . ' '
									.  Lang::txt('COM_PROJECTS_AN_ACTIVITY'),
									$cid, '', '', 'quote', 0, 1, 1);

								if ($caid)
								{
									$objC->storeCommentActivityId($cid, $caid);
								}
							}
						}
					}
				}
			}

			// Go back to project listing
			App::redirect(Route::url('index.php?option=' . $this->_option . '&task=browse&reviewer=' . $reviewer . '&filterby=' . $filterby));
			return;
		}
		else
		{
			// Instantiate a new view
			$this->view->setLayout('review');

			// Output HTML
			$this->view->reviewer = $reviewer;
			$this->view->ajax     = Request::getInt('ajax', 0);
			$this->view->title    = $this->title;
			$this->view->option   = $this->_option;
			$this->view->model    = $this->model;
			$this->view->params   = $params;
			$this->view->config   = $this->config;
			$this->view->database = $this->database;
			$this->view->action   = $action;
			$this->view->filterby = $filterby;
			$this->view->uid      = User::get('id');
			$this->view->msg      = $this->_getNotifications('success');
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}
			$this->view->display();
		}
	}
}
