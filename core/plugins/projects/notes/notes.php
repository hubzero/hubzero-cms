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

// No direct access
defined('_HZEXEC_') or die();

// Include note model
include_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
	. DS . 'models' . DS . 'note.php');

/**
 * Projects Notes (wiki) plugin
 */
class plgProjectsNotes extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var	   boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Name of project group
	 *
	 * @var	   array
	 */
	protected $_group = NULL;

	/**
	 * Name of master scope
	 *
	 * @var	   array
	 */
	protected $_masterScope = NULL;

	/**
	 * Name of page
	 *
	 * @var	   array
	 */
	protected $_pagename = NULL;

	/**
	 * Tool record (tool wiki)
	 *
	 * @var	   array
	 */
	protected $_tool = NULL;

	/**
	 * Controller name
	 *
	 * @var	   array
	 */
	protected $_controllerName = NULL;

	/**
	 * Store internal message
	 *
	 * @var	   array
	 */
	protected $_msg = NULL;

	/**
	 * Component name
	 *
	 * @var  string
	 */
	protected $_option = 'com_projects';

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @return     array   Plugin name and title
	 */
	public function &onProjectAreas($alias = NULL)
	{
		$area = array(
			'name'    => 'notes',
			'title'   => Lang::txt('COM_PROJECTS_TAB_NOTES'),
			'submenu' => NULL,
			'show'    => true
		);

		return $area;
	}

	/**
	 * Event call to return count of items
	 *
	 * @param      object  $model 		Project
	 * @return     array   integer
	 */
	public function &onProjectCount( $model )
	{
		$group_prefix = $model->config()->get('group_prefix', 'pr-');
		$groupname = $group_prefix . $model->get('alias');
		$scope = 'projects' . DS . $model->get('alias') . DS . 'notes';

		// Get our model
		$note = new \Components\Projects\Models\Note($scope, $groupname, $model->get('id'));

		$counts['notes'] = $note->getNoteCount();

		return $counts;
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param      object  $model           Project model
	 * @param      string  $action			Plugin task
	 * @param      string  $areas  			Plugins to return data
	 * @param      string  $tool			Name of tool wiki belongs to
	 * @return     array   Return array of html
	 */
	public function onProject ( $model, $action = '', $areas = null, $tool = NULL )
	{
		$returnhtml = true;

		$arr = array(
			'html'     =>'',
			'metadata' =>''
		);

		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ))
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas))
			{
				return;
			}
		}
		// Check that project exists
		if (!$model->exists())
		{
			return $arr;
		}

		// Check authorization
		if (!$model->access('member'))
		{
			return $arr;
		}

		// Model
		$this->model = $model;

		// Are we returning HTML?
		if ($returnhtml)
		{
			// Load wiki language file
			Lang::load('com_wiki') || Lang::load('com_wiki', PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'site');

			// Set vars
			$this->_database 	= App::get('db');
			$this->_uid 		= User::get('id');

			// Load component configs
			$this->_config = $model->config();
			$this->_group = $this->_config->get('group_prefix', 'pr-') . $this->model->get('alias');

			// Incoming
			$this->_pagename = trim(Request::getVar('pagename', '', 'default', 'none', 2));
			$this->_masterScope = 'projects' . DS . $this->model->get('alias') . DS . 'notes';

			// Get our model
			$this->note = new \Components\Projects\Models\Note(
				$this->_masterScope,
				$this->_group,
				$this->model->get('id')
			);

			// What's the task?
			$this->_task = $action ? $action : Request::getVar('action', 'view');

			// Publishing?
			if ($this->_task == 'browser')
			{
				return $this->browser();
			}

			// Import some needed libraries
			switch ($this->_task)
			{
				case 'upload':
				case 'download':
				case 'deletefolder':
				case 'deletefile':
				case 'media':
				case 'list':
					$this->_controllerName = 'media';
				break;

				case 'history':
				case 'compare':
				case 'approve':
				case 'deleterevision':
					$this->_controllerName = 'history';
				break;

				case 'editcomment':
				case 'addcomment':
				case 'savecomment':
				case 'reportcomment':
				case 'removecomment':
				case 'comments':
					$this->_controllerName = 'comments';
					$cid = Request::getVar('cid', 0);
					if ($cid)
					{
						Request::setVar('comment', $cid);
					}
				break;

				case 'delete':
				case 'edit':
				case 'save':
				case 'rename':
				case 'saverename':
				default:
					$this->_controllerName = 'page';
				break;
			}

			if (substr(strtolower($this->_pagename), 0, strlen('image:')) == 'image:'
			 || substr(strtolower($this->_pagename), 0, strlen('file:')) == 'file:')
			{
				$this->_controllerName = 'media';
				$this->_task = 'download';
			}

			if (!file_exists(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS
				. 'site' . DS . 'controllers' . DS . $this->_controllerName . '.php'))
			{
				$this->_controllerName = 'page';
			}
			// Include controller
			require_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS
				. 'site' . DS . 'controllers' . DS . $this->_controllerName . '.php');

			// Listing/unlisting?
			if ($this->_task == 'publist' || $this->_task == 'unlist')
			{
				$arr['html'] = $this->_list();
			}
			elseif ($this->_task == 'share')
			{
				$arr['html'] = $this->_share();
			}
			else
			{
				// Display page
				$arr['html'] = $this->page();
			}
		}

		// Return data
		return $arr;
	}

	/**
	 * View of project note
	 *
	 * @return     string
	 */
	public function page()
	{
		// Incoming
		$preview 	= trim(Request::getVar( 'preview', '' ));
		$note 		= Request::getVar('page', array(), 'post', 'none', 2);
		$scope 		= trim(Request::getVar( 'scope', $this->_masterScope ), DS);

		$pagePrefix = '';

		// Output HTML (wrap for notes)
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'notes',
				'name'		=>'wrap',
				'layout' 	=>'wrap'
			)
		);

		// Get first project note
		$view->firstNote = $this->note->getFirstNote( $pagePrefix);

		// Default view to first available note if no page is requested
		if (!$this->_pagename && $this->_task != 'new' && $this->_task != 'save')
		{
			$this->_pagename = $view->firstNote ? $view->firstNote : '';
		}

		// Are we saving?
		$save 	= $this->_task == 'save' ? 1 : 0;
		$rename = $this->_task == 'saverename' ? 1 : 0;
		$canDelete = 1;

		// Get page
		$view->page    = $this->note->page($this->_pagename, $scope);
		$view->content = NULL;
		$exists = $view->page->get('id') ? true : false;

		Request::setVar('pagename', $this->_pagename);
		Request::setVar('task', $this->_task);
		Request::setVar('scope', $scope);
		Request::setVar('group_cn', $this->_group);

		Request::setVar('tool', $this->_tool);
		Request::setVar('project', $this->model);
		Request::setVar('candelete', $canDelete);

		if (!$view->page->get('id') && $this->_task == 'view' && $view->page->get('namespace') != 'special')
		{
			// Output HTML (wrap for notes)
			$nview = new \Hubzero\Plugin\View(
				array(
					'folder'	=>'projects',
					'element'	=>'notes',
					'name'		=>'page',
					'layout' 	=>'doesnotexist'
				)
			);
			$nview->scope 		= $scope;
			$nview->option 		= $this->_option;
			$nview->project 	= $this->model;
			$view->content 		= $nview->loadTemplate();
		}

		$basePath = PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'site';
		if ($this->_task == 'edit' || $this->_task == 'new' || $this->_task == 'save')
		{
			$basePath = PATH_CORE . DS . 'plugins' . DS . 'projects' . DS . 'notes';
			if (!$this->model->access('content'))
			{
				throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
				return;
			}
		}
		if (!$view->content)
		{
			$controllerName = "Components\Wiki\Site\Controllers\\"  . ucfirst($this->_controllerName);
			// Instantiate controller
			$controller = new $controllerName(array(
				'base_path' => $basePath,
				'name'      => 'projects',
				'sub'       => 'notes',
				'group'     => $this->_group
			));

			// Catch any echoed content with ob
			ob_start();
			$controller->execute();

			// Record activity
			if ($save && !$preview && !$this->getError() && !$controller->getError())
			{
				$what  = $exists
					? Lang::txt('COM_PROJECTS_NOTE_EDITED')
					: Lang::txt('COM_PROJECTS_NOTE_ADDED');
				$what .= $exists ? ' "' . $controller->page->get('title') . '" ' : '';
				$what .= ' '.Lang::txt('COM_PROJECTS_NOTE_IN_NOTES');
				$aid = $this->model->recordActivity($what,
					$controller->page->get('id'), 'notes', Route::url('index.php?option=' . $this->_option . '&alias=' . $this->model->get('alias') . '&active=notes') , 'notes', 0);

				// Record page order for new pages
				$lastorder = $this->note->getLastNoteOrder($scope);
				$order = intval($lastorder + 1);
				$this->note->saveNoteOrder($scope, $order);
			}

			// Make sure all scopes of subpages are valid after rename
			if ($rename)
			{
				// Incoming
				$oldpagename = trim(Request::getVar( 'oldpagename', '', 'post' ));
				$newpagename = trim(Request::getVar( 'newpagename', '', 'post' ));
				$this->note->fixScopePaths($scope, $oldpagename, $newpagename);
			}

			$controller->redirect();
			$view->content = ob_get_contents();
			ob_end_clean();
		}

		// Fix pathway (com_wiki screws it up)
		$this->fixupPathway();

		// Get messages	and errors
		$view->msg = isset($this->_msg) ? $this->_msg : NULL;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}

		$view->title 		= $this->_area['title'];
		$view->note 		= $this->note;
		$view->task 		= $this->_task;
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->model;
		$view->uid 			= $this->_uid;
		$view->pagename 	= $this->_pagename;
		$view->scope 		= $scope;
		$view->preview 		= $preview;
		$view->group 		= $this->_group;
		$view->params		= $this->params;

		return $view->loadTemplate();

	}

	/**
	 * List/unlist on public project page
	 *
	 * @return     string
	 */
	protected function _list()
	{
		// Incoming
		$id = trim(Request::getInt( 'p', 0 ));

		// Load requested page
		$page = $this->note->page($id);
		if (!$page->get('id'))
		{
			App::redirect(Route::url($this->model->link('notes')));
			return;
		}

		$listed = $this->_task == 'publist' ? 1 : 0;

		// Get/update public stamp for page
		if ($this->note->getPublicStamp($page->get('id'), true, $listed))
		{
			$this->_msg = $this->_task == 'publist' ? Lang::txt('COM_PROJECTS_NOTE_MSG_LISTED') : Lang::txt('COM_PROJECTS_NOTE_MSG_UNLISTED');

			\Notify::message($this->_msg, 'success', 'projects');

			App::redirect(Route::url('index.php?option=' . $this->_option . '&scope=' . $page->get('scope') . '&pagename=' . $page->get('pagename')));
			return;
		}

		App::redirect(Route::url($this->model->link('notes')));
		return;
	}

	/**
	 * Get public link and list/unlist
	 *
	 *
	 * @return     string
	 */
	protected function _share()
	{
		// Incoming
		$id = trim(Request::getInt( 'p', 0 ));

		// Load requested page
		$page = $this->note->page($id);
		if (!$page->get('id'))
		{
			App::redirect(Route::url($this->model->link('notes')));
			return;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'notes',
				'name'    =>'pubsettings'
			)
		);

		// Get/update public stamp for page
		$view->publicStamp = $this->note->getPublicStamp($page->get('id'), true);

		if (!$view->publicStamp)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_NOTES_ERROR_SHARE'));

			// Output error
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'projects',
					'element' =>'files',
					'name'    =>'error'
				)
			);

			$view->title  = '';
			$view->option = $this->_option;
			$view->setError( $this->getError() );
			return $view->loadTemplate();
		}

		$view->option 			= $this->_option;
		$view->project			= $this->model;
		$view->url				= $url;
		$view->config 			= $this->model->config();
		$view->page				= $page;
		$view->revision 		= $page->revision('current');
		$view->masterscope 		= 'projects' . DS . $this->model->get('alias') . DS . 'notes';
		$view->params			= $this->params;
		$view->ajax				= Request::getInt('ajax', 0);

		// Output HTML
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}

		return $view->loadTemplate();
	}

	/**
	 * Fix pathway
	 *
	 * @param      object  	$page
	 *
	 * @return     string
	 */
	public function fixupPathway()
	{
		Pathway::clear();

		// Add group
		if ($this->model->groupOwner())
		{
			Pathway::append(
				Lang::txt('COM_PROJECTS_GROUPS_COMPONENT'),
				Route::url('index.php?option=com_groups')
			);
			Pathway::append(
				\Hubzero\Utility\String::truncate($this->model->groupOwner('description'), 50),
				Route::url('index.php?option=com_groups&cn=' . $this->model->groupOwner('cn'))
			);
			Pathway::append(
				Lang::txt('COM_PROJECTS_PROJECTS'),
				Route::url('index.php?option=com_groups&cn=' . $this->model->groupOwner('cn') . '&active=projects')
			);
		}
		else
		{
			Pathway::append(
				Lang::txt('COMPONENT_LONG_NAME'),
				Route::url('index.php?option=' . $this->_option)
			);
		}

		if ($this->model->exists())
		{
			Pathway::append(
				stripslashes($this->model->get('title')),
				Route::url('index.php?option=' . $this->_option . '&alias=' . $this->model->get('alias'))
			);
		}

		if ($this->_tool && $this->_tool->id)
		{
			Pathway::append(
				ucfirst(Lang::txt('COM_PROJECTS_PANEL_TOOLS')),
				Route::url('index.php?option=' . $this->_option . '&alias='
				. $this->model->get('alias') . '&active=tools')
			);

			Pathway::append(
				\Hubzero\Utility\String::truncate($this->_tool->title, 50),
				Route::url('index.php?option=' . $this->_option . '&alias='
				. $this->model->get('alias') . '&active=tools&tool=' . $this->_tool->id)
			);

			Pathway::append(
				ucfirst(Lang::txt('COM_PROJECTS_TOOLS_TAB_WIKI')),
				Route::url('index.php?option=' . $this->_option . '&alias='
				. $this->model->get('alias') . '&active=tools&tool=' . $this->_tool->id . '&action=wiki')
			);
		}
		else
		{
			Pathway::append(
				ucfirst(Lang::txt('COM_PROJECTS_TAB_NOTES')),
				Route::url('index.php?option=' . $this->_option . '&alias='
				. $this->model->get('alias') . '&active=notes')
			);
		}
	}

	/**
	 * List project notes available for publishing
	 *
	 * @return     array
	 */
	public function browser()
	{
		// Incoming
		$ajax 		= Request::getInt('ajax', 0);
		$primary 	= Request::getInt('primary', 1);
		$versionid  = Request::getInt('versionid', 0);

		if (!$ajax)
		{
			return false;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'notes',
				'name'    =>'browser'
			)
		);

		// Get current attachments
		$pContent = new \Components\Publications\Tables\Attachment( $this->_database );
		$role 	= $primary ? '1' : '0';
		$other 	= $primary ? '0' : '1';

		$view->attachments = $pContent->getAttachments(
			$versionid,
			$filters = array('role' => $role, 'type' => 'note')
		);

		// Output HTML
		$view->params 		= $this->model->params;
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->model 		= $this->model;
		$view->uid 			= $this->_uid;
		$view->config 		= $this->_config;
		$view->title		= $this->_area['title'];
		$view->primary		= $primary;
		$view->versionid	= $versionid;

		// Get messages	and errors
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		$html =  $view->loadTemplate();

		$arr = array(
			'html'     => $html,
			'metadata' => ''
		);

		return $arr;
	}

	/**
	 * Serve wiki page (usually via public link)
	 *
	 * @param   int  	$projectid
	 * @return  void
	 */
	public function serve( $type = '', $projectid = 0, $query = '')
	{
		$this->_area = $this->onProjectAreas();
		if ($type != $this->_area['name'])
		{
			return false;
		}
		$data = json_decode($query);

		if (!isset($data->pageid) || !$projectid)
		{
			return false;
		}

		$this->loadLanguage();

		$database = App::get('db');
		$this->_option 	= 'com_projects';

		// Instantiate a project
		$this->model = new \Components\Projects\Models\Project($projectid);

		if (!$this->model->exists())
		{
			return false;
		}

		$groupname = $this->model->config()->get('group_prefix', 'pr-') . $this->model->get('alias');
		$scope = 'projects' . DS . $this->model->get('alias') . DS . 'notes';

		// Include note model
		include_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
			. DS . 'models' . DS . 'note.php');

		// Get our model
		$this->note = new \Components\Projects\Models\Note($scope, $groupname, $projectid);

		// Fix pathway (com_wiki screws it up)
		$this->fixupPathway();

		// Load requested page
		$page = $this->note->page($data->pageid);
		if (!$page->get('id'))
		{
			return false;
		}

		// Write title & build pathway
		Document::setTitle( Lang::txt(strtoupper($this->_option)) . ': '
			. stripslashes($this->model->get('title')) . ' - ' . stripslashes($page->get('title')) );

		// Instantiate a new view
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'notes',
				'name'		=>'pubview'
			)
		);
		$view->option 			= $this->_option;
		$view->model			= $this->model;
		$view->page				= $page;
		$view->revision 		= $page->revision('current');
		$view->masterscope 		= 'projects' . DS . $this->model->get('alias') . DS . 'notes';

		// Output HTML
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}

		$view->display();
		return true;
	}

	/**
	 * Event call to get side content for main project page
	 *
	 * @return
	 */
	public function onProjectMiniList($model)
	{
		if (!$model->exists() || !$model->access('content'))
		{
			return false;
		}

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'notes',
				'name'    => 'mini'
			)
		);

		$group = $model->config()->get('group_prefix', 'pr-') . $model->get('alias');
		$masterScope = 'projects' . DS . $model->get('alias') . DS . 'notes';

		// Get our model
		$note = new \Components\Projects\Models\Note(
			$masterScope,
			$group,
			$model->get('id')
		);
		$view->notes = $note->getNotes();
		$view->model = $model;
		return $view->loadTemplate();
	}

	/**
	 * Event call to get content for public project page
	 *
	 * @return
	 */
	public function onProjectPublicList($model)
	{
		if (!$model->exists() || !$model->isPublic())
		{
			return false;
		}

		if (!$model->params->get('notes_public', 0))
		{
			return false;
		}

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'notes',
				'name'    => 'publist'
			)
		);

		require_once( PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'publicstamp.php');

		$database 	= App::get('db');
		$objSt 		= new \Components\Projects\Tables\Stamp( $database );

		$view->items = $objSt->getPubList($model->get('id'), 'notes');
		$view->page  = \Components\Wiki\Models\Page::blank();
		$view->model = $model;
		return $view->loadTemplate();
	}
}
