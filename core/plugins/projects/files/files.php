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

// Include external file connection
require_once Component::path('com_projects') . DS . 'tables' . DS . 'remotefile.php';
require_once Component::path('com_projects') . DS . 'helpers' . DS . 'connect.php';

// Include some helpers
require_once Component::path('com_projects') . DS . 'helpers' . DS . 'compiler.php';

// Get repo model
require_once Component::path('com_projects') . DS . 'models' . DS . 'repo.php';

require_once __DIR__ . '/helpers/sync.php';

// Include [temporary] ORM models (these will be merged with existing models at some point in the future)
require_once Component::path('com_projects') . DS . 'models' . DS . 'orm' . DS . 'project.php';
require_once Component::path('com_projects') . DS . 'models' . DS . 'orm' . DS . 'connection.php';
require_once Component::path('com_projects') . DS . 'models' . DS . 'orm' . DS . 'provider.php';

use Components\Projects\Models\Orm\Connection;

/**
 * Projects Files plugin
 */
class plgProjectsFiles extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Repository path
	 *
	 * @var  string
	 */
	protected $_path = NULL;

	/**
	 * Component name
	 *
	 * @var  string
	 */
	protected $_option = 'com_projects';

	/**
	 * Store internal message
	 *
	 * @var  array
	 */
	protected $_msg = NULL;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @return  array  Plugin name and title
	 */
	public function &onProjectAreas($alias = NULL)
	{
		$area = array(
			'name'    => $this->_name,
			'title'   => Lang::txt('COM_PROJECTS_TAB_FILES'),
			'submenu' => 'Assets',
			'show'    => true,
			'icon'    => 'f016'
		);

		if ($this->params->get('default_action', 'browse') == 'connections')
		{
			$model = new Components\Projects\Models\Project(Request::getVar('alias', ''));

			$active = Request::getInt('connection', 0);

			$area['children'] = array();
			$area['children'][] = array(
				'name'  => 'default',
				'title' => sprintf($this->params->get('default_connection_name', '%s Master Repository'), $model->get('title')),
				'url'   => $model->link('files') . '&action=browse',
				'class' => 'filesystem default' . (Request::getVar('action') == 'browse' && !$active ? ' active' : ''),
				//'image' => '/core/plugins/filesystem/local/assets/img/icon.png',
				'icon'  => 'f0a0'
			);

			if ($model->exists() && $model->access('member'))
			{
				$connections = Components\Projects\Models\Orm\Project::oneOrFail($model->get('id'))->connections()->thatICanView();

				if ($connections->count())
				{
					foreach ($connections as $connection)
					{
						//$imgRel = '/plugins/filesystem/' . $connection->provider->alias . '/assets/img/icon.png';
						//$img = (is_file(PATH_APP . DS . $imgRel)) ? '/app' . $imgRel : '/core' . $imgRel;

						$area['children'][] = array(
							'title' => $connection->name,
							'name'  => $connection->provider->alias,
							'url'   => $model->link('files') . '&action=browse&connection=' . $connection->id,
							'class' => 'filesystem ' . $connection->provider->alias . (!$connection->isShared() ? ' private' : '') . ($active == $connection->id ? ' active' : ''),
							//'image' => $img,
							'icon'  => 'f0a0'
						);
					}
				}
			}
		}

		return $area;
	}

	/**
	 * Event call to return count of items
	 *
	 * @param   object  $model   Project
	 * @return  array   integer
	 */
	public function &onProjectCount($model)
	{
		$repo  = new \Components\Projects\Models\Repo($model, 'local');
		$counts['files'] = $repo->count();

		return $counts;
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param   object  $model   Project model
	 * @param   string  $action  Plugin task
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onProject($model, $action = '', $areas = null, $params = array())
	{
		$returnhtml = true;

		$arr = array(
			'html'     =>'',
			'metadata' =>''
		);

		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas))
			{
				return;
			}
		}

		// Project model
		$this->model = $model;

		// Check authorization
		if ($this->model->exists() && !$this->model->access('member'))
		{
			return $arr;
		}

		// Are we returning HTML?
		if ($returnhtml)
		{
			// Load repo model
			$repoName   = !empty($params['repo']) ? $params['repo'] : Request::getVar('repo', 'local');
			$this->repo = new \Components\Projects\Models\Repo($this->model, $repoName);

			$default = $this->params->get('default_action', 'browse');

			$this->_publishing = Plugin::isEnabled('projects', 'publications') ? 1 : 0;
			$this->_database   = \App::get('db');
			$this->_uid        = User::get('id');
			$this->_task       = $action ? $action : Request::getVar('action', $default);
			$this->subdir      = trim(urldecode(Request::getVar('subdir', '')), DS);
			$this->publication = Request::getInt('pid', 0);

			// Set repo path
			if (!$this->model->exists())
			{
				// Contribute process outside of projects
				$this->model->set('provisioned', 1);
				$this->_path = $this->getMembersPath();
			}
			else
			{
				if (!$this->repo->exists())
				{
					// Default to local repo (will redirect to add repo page in the future)
					$this->repo = new \Components\Projects\Models\Repo($this->model, 'local');
				}
				$this->_path = $this->repo->get('path');
			}

			//  Establish connection to external services
			if ($this->model->exists() && !$this->model->isProvisioned())
			{
				$this->_connect = new \Components\Projects\Helpers\Connect(
					$this->model,
					$this->_uid,
					date_default_timezone_get()
				);

				// Sync service is Google
				if (!empty($this->_connect->_active) && $this->repo->isLocal())
				{
					$this->_remoteService = 'google';
				}
			}

			$ctask = 'connections';
			if (($connection = Request::getInt('connection', null))
			  || $this->_task == 'editconnection'
			  || $this->_task == 'saveconnection'
			  || $this->_task == 'newconnection'
			  || $this->_task == 'deleteconnection'
				|| $this->_task == 'refreshconnection')
			{
				$ctask       = $this->_task;
				$this->_task = 'connections';
			}

			// File actions
			switch ($this->_task)
			{
				// Basic file management
				case 'upload':
					$arr['html'] = $this->_upload();
					break;

				case 'save':
				case 'saveprov':
					$arr['html'] = $this->_save();
					break;

				case 'delete':
				case 'removeit':
					$arr['html'] = $this->_delete();
					break;

				case 'move':
				case 'moveit':
					$arr['html'] = $this->_move();
					break;

				case 'rename':
				case 'renameit':
					$arr['html'] = $this->_rename();
					break;

				case 'share':
				case 'shareit':
					$arr['html'] = $this->_share();
					break;

				// History
				case 'history':
					$arr['html'] = $this->_history();
					break;
				case 'diff':
					$arr['html'] = $this->_diff();
					break;

				// Serve/preview
				case 'compile':
					$arr['html'] = $this->_compile();
					break;
				case 'serve':
					$arr['html'] = $this->serve();
					break;
				case 'download':
				case 'open':
					$arr['html'] = $this->_download();
					break;

				// Manage directory
				case 'newdir':
					$arr['html'] = $this->_newDir();
					break;
				case 'deletedir':
					$arr['html'] = $this->_deleteDir();
					break;
				case 'savedir':
					$arr['html'] = $this->_saveDir();
					break;

				// Manage deleted
				case 'trash':
					$arr['html'] = $this->_showTrash();
					break;
				case 'restore':
					$arr['html'] = $this->_restore();
					break;

				// Disk space management
				case 'diskspace':
					$arr['html'] = $this->diskspace(
						$this->model, $this->repo->get('name'), $this->_uid
					);
					break;
				case 'optimize':
				case 'advoptimize':
					$arr['html'] = $this->optimize(
						$this->model, $this->repo->get('name'));
					break;

				// Publishing selectors
				case 'select':
				case 'filter':
					$arr['html'] = $this->_select();
					break;

				// Connections
				case 'connect':
				case 'disconnect':
					$arr['html'] = $this->_connect();
					break;

				// Sync with remote
				case 'sync':
					$arr['html'] = $this->_iniSync();
					break;
				case 'sync_status':
					$arr['html'] = $this->syncStatus();
					break;
				case 'sync_error':
					$arr['html'] = $this->syncError();
					break;

				// New connected methods
				case 'connections':
					require_once __DIR__ . DS . 'connections.php';
					$controller  = new connections($this, $this->_option, $connection);
					$arr['html'] = $controller->execute($ctask);
					break;
				// File browser
				case 'browse':
				default:
					$arr['html'] = $this->_browse();
					break;
			}
		}

		// Return data
		return $arr;

	}

	/**
	 * Event call to get side content for main project page
	 *
	 * @param   object  $model
	 * @return  string
	 */
	public function onProjectMiniList($model)
	{
		if (!$model->exists() || !$model->access('content'))
		{
			return false;
		}
		$this->repo = new \Components\Projects\Models\Repo($model, 'local');

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'files',
				'name'    => 'mini'
			)
		);

		// Set params
		$view->params = array(
			'limit'       => $model->config()->get('sidebox_limit', 5),
			'start'       => 0,
			'sortby'      => 'modified',
			'sortdir'     => 'DESC',
			'getParents'  => false,
			'getChildren' => false
		);

		// Retrieve items
		$view->files = $this->repo->filelist($view->params);
		$view->model = $model;
		return $view->loadTemplate();
	}

	/**
	 * View of project files
	 *
	 * @param   integer  $sync
	 * @return  string
	 */
	protected function _browse($sync = 0)
	{
		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'files',
				'name'    => 'browse',
				'layout'  => 'display'
			)
		);

		// Does subdirectory exist?
		if (!$this->repo->dirExists($this->subdir))
		{
			$this->subdir = '';
		}

		// Do we have any changes to report?
		$this->onAfterUpdate();

		// Load member params
		$member = $this->model->member(true);
		$view->oparams = new \Hubzero\Config\Registry($member->params);

		// Sync active?
		$remotes = array();
		$view->connect     = $this->_connect;
		$view->services    = $this->_connect->_active;
		$view->connections = $this->_connect->getConnections($this->_uid);

		// Get stored remote connections
		if (!empty($this->_remoteService))
		{
			$objRFile = new \Components\Projects\Tables\RemoteFile ($this->_database);
			$remotes  = $objRFile->getRemoteFiles(
				$this->model->get('id'),
				$this->_remoteService,
				$this->subdir
			);
			$forceSync = $this->get('forceSync', $this->model->params->get('google_sync_queue', 0));

			$view->sync    = $sync == 2 ? 0 : $forceSync;
			$view->rSync   = new Sync($this->_connect);
			$view->sharing = 1;
		}

		// Set params
		$view->params = array(
			'subdir'               => $this->subdir,
			'filter'               => Request::getVar('filter', ''),
			'limit'                => Request::getInt('limit', 0),
			'start'                => Request::getInt('limitstart', 0),
			'sortby'               => Request::getVar('sortby', 'name'),
			'sortdir'              => Request::getVar('sortdir', 'ASC'),
			'showFullMetadata'     => true,
			'showUntracked'        => true,
			'getPubConnections'    => false,
			'remoteConnections'    => $remotes,
		);

		// Retrieve items
		$view->items = $this->repo->filelist($view->params);

		$view->publishing = false; // do not show publishing info
		$view->title      = $this->_area['title'];
		$view->option     = $this->_option;
		$view->subdir     = $this->subdir;
		$view->model      = $this->model;
		$view->repo       = $this->repo;
		$view->uid        = $this->_uid;
		$view->fileparams = $this->params;
		$view->database   = $this->_database;

		return $view->loadTemplate();
	}

	/**
	 * Browser within publications (Curation)
	 *
	 * @return  string
	 */
	protected function _select()
	{
		// Incoming
		$props     = Request::getVar('p', '');
		$ajax      = Request::getInt('ajax', 0);
		$pid       = Request::getInt('pid', 0);
		$vid       = Request::getInt('vid', 0);
		$filter    = urldecode(Request::getVar('filter', ''));
		$directory = urldecode(Request::getVar('directory', ''));

		// Parse props for curation
		$parts   = explode('-', $props);
		$block   = (isset($parts[0]) && in_array($parts[0], array('content', 'extras')))
		            ? $parts[0]
		            : 'content';
		$step    = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
		$element = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 1;

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'files',
				'name'    => 'selector'
			)
		);

		$view->publication = new \Components\Publications\Models\Publication($pid, NULL, $vid);

		// On error
		if (!$view->publication->exists())
		{
			// Output error
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'projects',
					'element' => 'files',
					'name'    => 'error'
				)
			);

			$view->title  = '';
			$view->option = $this->_option;
			$view->setError(Lang::txt('PLG_PROJECTS_FILES_SELECTOR_ERROR_NO_PUBID'));
			return $view->loadTemplate();
		}

		$view->publication->attachments();

		// Get curation model
		$view->publication->setCuration();

		// Make sure block exists, else use default
		$view->publication->_curationModel->setBlock($block, $step);

		// Get file list
		$view->items = NULL;
		if ($this->model->get('id'))
		{
			// Set params
			$params = array(
				'sortby'           => 'localpath',
				'showFullMetadata' => false,
				'subdir'           => $directory
			);

			// Retrieve items
			if (($cid = Request::getInt('cid')) && $cid > 0)
			{
				// Get directory that we're interested in
				$con = Connection::oneOrFail($cid);
				$dir = \Hubzero\Filesystem\Entity::fromPath(($directory != '.' ? $directory : ''), $con->adapter());

				$view->items = $dir->listContents();
			}
			else
			{
				$view->items = $this->repo->filelist($params);
			}

			$view->directory = $directory;

			// Get directories
			$params = array(
				'subdir'           => NULL,
				'sortby'           => 'localpath',
				'showFullMetadata' => false,
				'dirsOnly'         => true,
			);

			$view->folders = $this->repo->filelist($params);
		}

		$view->option    = $this->model->isProvisioned() ? 'com_publications' : $this->_option;
		$view->database  = $this->_database;
		$view->model     = $this->model;
		$view->repo    	 = $this->repo;
		$view->uid       = $this->_uid;
		$view->ajax      = $ajax;
		$view->task      = $this->_task;
		$view->element   = $element;
		$view->block     = $block;
		$view->step      = $step;
		$view->props     = $props;
		$view->filter    = $filter;
		$view->sizelimit = $this->params->get('maxUpload', '104857600');
		$view->showCons  = ($this->params->get('default_action', 'browse') == 'connections') ? true : false;

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * Upload view
	 *
	 * @return  void  redirect
	 */
	protected function _upload()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			App::abort(403, Lang::txt('ALERTNOTAUTH'));
		}

		// Incoming
		$ajax = Request::getInt('ajax', 0);

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'files',
				'name'    =>'upload'
			)
		);

		$view->url       = Route::url($this->model->link('files'));
		$view->unused    = $this->model->repo()->getAvailableDiskSpace();
		$view->option    = $this->_option;
		$view->model     = $this->model;
		$view->repo      = $this->repo;
		$view->subdir    = $this->subdir;
		$view->ajax      = $ajax;
		$view->config    = $this->model->config();
		$view->sizelimit = $this->params->get('maxUpload', '104857600');
		$view->title     = $this->_area['title'];
		$view->params    = $this->params;

		return $view->loadTemplate();
	}

	/**
	 * Upload file(s) and add to repository
	 *
	 * @return  void  redirect
	 */
	protected function _save()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			App::abort(403, Lang::txt('ALERTNOTAUTH'));
		}

		// Incoming
		$json       = Request::getVar('json', 0);
		$no_html    = Request::getVar('no_html', 0);
		$view       = Request::getVar('view', 'view'); // where to redirect
		$ajaxUpload = $no_html && !$json ? true : false;

		if ($view == 'pub')
		{
			$url = Request::getVar('HTTP_REFERER', NULL, 'server');
		}
		else
		{
			$url  = $this->model->link('files');
			$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
			$url .= $this->subdir ? '?subdir=' . urlencode($this->subdir) : '';
			$url  = Route::url($url);
		}

		// Set params
		$params = array(
			'subdir'     => $this->subdir,
			'expand'     => Request::getInt('expand_zip', 0),
			'ajaxUpload' => $ajaxUpload,
			'path'       => $this->_path
		);

		// Upload file
		$results = $this->repo->insert($params);

		// Register changes for active projects
		if (!empty($results))
		{
			foreach ($results as $updateType => $files)
			{
				foreach ($files as $file)
				{
					if (!$this->model->isProvisioned())
					{
						$this->registerUpdate($updateType, $file);
					}

					// Ajax requires output right here
					if ($ajaxUpload)
					{
						if ($updateType == 'failed')
						{
							return json_encode(array(
								'error' => $this->repo->getError()
							));
						}
						else
						{
							return json_encode(array(
								'success' => 1,
								'file'    => $file,
								'isNew'   => $updateType == 'uploaded' ? true : false
								)
							);
						}
					}
				}
			}
		}

		// On error
		if ($this->repo->getError())
		{
			if ($ajaxUpload || $json)
			{
				return json_encode(array(
					'error' => $this->repo->getError()
				));
			}
			else
			{
				\Notify::message($this->repo->getError(), 'error', 'projects');
				App::redirect($url);
				return;
			}
		}

		// Return status in JSON
		if ($json)
		{
			// After upload actions
			$this->onAfterUpdate();

			return json_encode(array(
				'error'   => $this->getError(),
				'success' => $this->_msg
			));
		}

		if (!empty($this->_msg))
		{
			\Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect($url);
	}

	/**
	 * New directory form
	 *
	 * @return  void  redirect
	 */
	protected function _newDir()
	{
		// Incoming
		$newdir = Request::getVar('newdir', '', 'post');

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'files',
				'name'    =>'newfolder'
			)
		);

		$view->option = $this->_option;
		$view->model  = $this->model;
		$view->repo   = $this->repo;
		$view->uid    = $this->_uid;
		$view->ajax   = 1;
		$view->subdir = $this->subdir;
		$view->url    = Route::url($this->model->link('files'));

		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		return $view->loadTemplate();
	}

	/**
	 * Save new directory
	 *
	 * @return  void  redirect
	 */
	protected function _saveDir()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Set params
		$params = array(
			'subdir' => $this->subdir,
			'newDir' => trim(Request::getVar('newdir', '')),
			'path'   => $this->_path
		);

		// Create
		$success = $this->repo->makeDirectory($params);

		if ($this->repo->getError())
		{
			\Notify::message($this->repo->getError(), 'error', 'projects');
		}
		else
		{
			\Notify::message($this->_msg, 'success', 'projects');

			// Force sync
			if ($this->repo->isLocal())
			{
				$this->model->saveParam('google_sync_queue', 1);
			}
		}

		// Redirect to file list
		$url  = $this->model->link('files') . '&action=browse';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Redirect
		App::redirect(Route::url($url));
	}

	/**
	 * Delete directory
	 *
	 * @return     void, redirect
	 */
	protected function _deleteDir()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			App::abort(403, Lang::txt('ALERTNOTAUTH'));
		}

		// Set params
		$params = array(
			'subdir'  => $this->subdir,
			'item'    => trim(urldecode(Request::getVar('dir', '')), DS),
			'path'    => $this->_path
		);

		// Create
		$success = $this->repo->deleteDirectory($params);
		if ($success)
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_FILES_DELETED_DIRECTORY'), 'success', 'projects');

			// Force sync
			if ($this->repo->isLocal())
			{
				$this->model->saveParam('google_sync_queue', 1);
			}
		}
		elseif ($this->repo->getError())
		{
			\Notify::message($this->repo->getError(), 'error', 'projects');
		}

		// Redirect to file list
		$url  = $this->model->link('files') . '&action=browse';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Redirect
		App::redirect(Route::url($url));
	}

	/**
	 * Delete items
	 *
	 * @return  void  redirect
	 */
	protected function _delete()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			App::abort(403, Lang::txt('ALERTNOTAUTH'));
		}

		// Get incoming array of items
		$items = $this->_sortIncoming();

		// Get stored remote connections
		$remotes = $this->_getRemoteConnections();

		// Params for repo call
		$params = array(
			'subdir'            => $this->subdir,
			'remoteConnections' => $remotes
		);

		// Confirm or process request
		if ($this->_task == 'delete')
		{
			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'projects',
					'element' =>'files',
					'name'    =>'delete'
				)
			);

			$view->items       = array();
			$view->services    = $this->_connect->_active;
			$view->connections = $this->_connect->getConnections();
			$view->connect     = $this->_connect;
			$view->database    = $this->_database;
			$view->option      = $this->_option;
			$view->model       = $this->model;
			$view->repo        = $this->repo;
			$view->uid         = $this->_uid;
			$view->ajax        = Request::getInt('ajax', 0);
			$view->subdir      = $this->subdir;
			$view->url         = Route::url($this->model->link('files'));
			$view->path        = $this->_path;
			if (empty($items))
			{
				$view->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_DELETE'));
			}
			else
			{
				foreach ($items as $element)
				{
					foreach ($element as $type => $item)
					{
						// Get type and item name
						break;
					}
					// Build metadata object
					$view->items[] = $this->repo->getMetadata($item, $type, $params);
				}
			}

			return $view->loadTemplate();
		}

		// Set counts
		$deleted = 0;

		// Delete checked items
		if (!empty($items))
		{
			foreach ($items as $element)
			{
				foreach ($element as $type => $item)
				{
					// Get type and item name
					break;
				}

				// Must have a name
				if (trim($item) == '')
				{
					continue;
				}

				$params['item'] = $item;
				$params['type'] = $type;

				// Build metadata object
				$params['file'] = $this->repo->getMetadata($item, $type, $params);

				// Is this a remote edited item?
				if ($params['file']->get('converted'))
				{
					// Delete remote converted file
					if ($this->_connect->deleteRemoteItem(
						$this->model->get('id'), $this->_remoteService, $this->model->get('owned_by_user'),
						$params['file']->get('remoteId'), false))
					{
						$this->registerUpdate('deleted', $item);
						$deleted++;
					}
				}
				else
				{
					if ($this->repo->deleteItem($params))
					{
						// Store in session
						$this->registerUpdate('deleted', $item);
						$deleted++;
					}
				}
			}
		}

		// Resync
		if ($deleted && $this->repo->isLocal())
		{
			$this->model->saveParam('google_sync_queue', 1);
		}

		// Redirect to file list
		$url  = $this->model->link('files') . '&action=browse';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Redirect
		App::redirect(Route::url($url));
	}

	/**
	 * Rename
	 *
	 * @return  void  redirect
	 */
	protected function _rename()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			App::abort(403, Lang::txt('ALERTNOTAUTH'));
		}

		// Confirm request
		if ($this->_task == 'rename')
		{
			// Get incoming array of items
			$items = $this->_sortIncoming();

			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'projects',
					'element' =>'files',
					'name'    =>'rename'
				)
			);

			if (empty($items))
			{
				$view->setError(Lang::txt('COM_PROJECTS_FILES_ERROR_RENAME_NO_OLD_NAME'));
			}
			else
			{
				// Get selected item
				foreach ($items[0] as $type => $item)
				{
					$view->item = $item;
					$view->type = $type;
					break;
				}
			}

			$view->option = $this->_option;
			$view->model  = $this->model;
			$view->repo   = $this->repo;
			$view->uid    = $this->_uid;
			$view->ajax   = 1;
			$view->subdir = $this->subdir;
			$view->url    = Route::url($this->model->link('files'));
			$view->path   = $this->_path;
			return $view->loadTemplate();
		}

		// Set params
		$params = array(
			'subdir'  => $this->subdir,
			'path'    => $this->_path,
			'from'    => Request::getVar('oldname', ''),
			'to'      => Request::getVar('newname', ''),
			'type'    => Request::getVar('type', 'file')
		);

		// Rename
		$success = $this->repo->rename($params);
		if ($success)
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_FILES_RENAMED_SUCCESS'), 'success', 'projects');
			// Force sync
			if ($this->repo->isLocal())
			{
				$this->model->saveParam('google_sync_queue', 1);
			}
		}
		elseif ($this->repo->getError())
		{
			\Notify::message($this->repo->getError(), 'error', 'projects');
		}

		// Redirect to file list
		$url  = $this->model->link('files') . '&action=browse';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Redirect
		App::redirect(Route::url($url));
	}

	/**
	 * Move file(s)
	 *
	 * @return  void  redirect
	 */
	protected function _move()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			App::abort(403, Lang::txt('ALERTNOTAUTH'));
		}

		// Get incoming array of items
		$items = $this->_sortIncoming();

		// Get stored remote connections
		$remotes = $this->_getRemoteConnections();

		// Confirmation screen
		if ($this->_task == 'move')
		{
			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'projects',
					'element' =>'files',
					'name'    =>'move'
				)
			);

			$listParams = array(
				'subdir'           => NULL,
				'sortby'           => 'localpath', // important for selector!
				'showFullMetadata' => false,
				'dirsOnly'         => true,
			);

			$view->list        = $this->repo->filelist($listParams);
			$view->path        = $this->_path;
			$view->items       = array();
			$view->database    = $this->_database;
			$view->services    = $this->_connect->_active;
			$view->connections = $this->_connect->getConnections();
			$view->connect     = $this->_connect;
			$view->option      = $this->_option;
			$view->model       = $this->model;
			$view->repo        = $this->repo;
			$view->uid         = $this->_uid;
			$view->ajax        = Request::getInt('ajax', 0);
			$view->subdir      = $this->subdir;
			$view->url         = Route::url($this->model->link('files'));
			if (empty($items))
			{
				$view->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_MOVE'));
			}
			else
			{
				// Params for repo call
				$params = array(
					'subdir'            => $this->subdir,
					'remoteConnections' => $remotes
				);

				foreach ($items as $element)
				{
					foreach ($element as $type => $item)
					{
						// Get type and item name
						break;
					}
					// Build metadata object
					$view->items[] = $this->repo->getMetadata($item, $type, $params);
				}
			}
			return $view->loadTemplate();
		}

		// Set counts
		$moved  = 0;

		// Incoming
		$newpath = trim(urldecode(Request::getVar('newpath', '')), DS);
		$newdir  = Request::getVar('newdir', '');
		$target  = $newdir ? $newdir : $newpath;

		// Set params for the move
		$params = array(
			'subdir'          => $this->subdir,
			'path'            => $this->_path,
			'targetDir'       => $target,
			'createTargetDir' => $newdir ? true : false
		);

		// Move checked items
		if (!empty($items))
		{
			foreach ($items as $element)
			{
				foreach ($element as $type => $item)
				{
					// Get type and item name
					break;
				}

				// Must have a name
				if (trim($item) == '')
				{
					continue;
				}

				// Build metadata object
				$params['file'] = $this->repo->getMetadata($item, $type, $params);

				if ($this->repo->moveItem($params))
				{
					$moved++;
				}
			}
		}

		// Output message
		if ($moved > 0)
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_FILES_MOVED') . ' ' . $moved . ' ' . Lang::txt('PLG_PROJECTS_FILES_S'), 'success', 'projects');

			// Force sync
			if ($this->repo->isLocal())
			{
				$this->model->saveParam('google_sync_queue', 1);
			}
		}
		else
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_NEW_FILE_LOCATION'), 'error', 'projects');
		}

		// Redirect to file list
		$url  = $this->model->link('files') . '&action=browse';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Redirect
		App::redirect(Route::url($url));
	}

	/**
	 * Show file history
	 *
	 * @return  void  redirect
	 */
	protected function _history()
	{
		// Combine file and folder data
		$items = $this->_sortIncoming();

		// Get stored remote connections
		$remotes = $this->_getRemoteConnections();

		// Params for repo call
		$params = array(
			'subdir'            => $this->subdir,
			'remoteConnections' => $remotes
		);

		if (!$items)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_SHOW_HISTORY'));
		}
		else
		{
			// Get selected item
			foreach ($items[0] as $type => $item)
			{
				$params['file'] = $this->repo->getMetadata($item, $type, $params);
				break;
			}
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'files',
				'name'    => 'history',
				'layout'  => 'advanced'
			)
		);

		// Redirect to file list
		$view->url = Route::url($this->model->link('files'));

		// Collective vars
		$versions   = array();
		$timestamps = array();

		// Make sure we have a file to work with
		if (empty($params['file']))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_SHOW_HISTORY'));
		}

		if ($this->getError())
		{
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
			$view->setError($this->getError());
			return $view->loadTemplate();
		}

		$view->file = $params['file'];

		// Get remote revision history
		$view->connected = false;
		if (!empty($this->_remoteService))
		{
			if ($view->file->get('converted'))
			{
				$this->_connect->sortRemoteRevisions(
					$view->file->get('remoteId'),
					$view->file->get('converted'),
					$view->file->get('author'),
					$this->_uid,
					$this->_remoteService,
					$view->file->get('localPath'),
					$versions,
					$timestamps
				);
			}
			elseif ($view->file->get('originalId'))
			{
				$this->_connect->sortRemoteRevisions(
					$view->file->get('originalId'),
					0,
					'',
					$this->_uid,
					$this->_remoteService,
					$view->file->get('localPath'),
					$versions,
					$timestamps,
					1
				);
			}
			$view->connected = $this->_connect->getStoredParam($this->_remoteService . '_token', $this->_uid);
		}

		$this->repo->versions($params, $versions, $timestamps);

		// Get file previews
		$i = 0;
		foreach ($versions as $v)
		{
			$revision = $view->file;
			$revision->set('localPath', $v['base']);
			$revision->set('name', $v['file']);
			$revision->set('fullPath', $this->repo->get('path') . DS . $v['base']);
			if ($v['remote'])
			{
				$revision->set('remodeId', $v['remote']);
				$revision->set('modified', gmdate('Y-m-d H:i:s', strtotime($v['date'])));
			}

			$hash = $v['remote'] ? NULL : $v['hash'];

			$versions[$i]['preview'] = $revision->getPreview($this->model, $hash, 'url');
			$i++;
		}

		$view->versions = $versions;
		$view->path     = $this->_path;
		$view->option   = $this->_option;
		$view->model    = $this->model;
		$view->repo     = $this->repo;
		$view->uid      = $this->_uid;
		$view->ajax     = Request::getInt('ajax');
		$view->title    = $this->_area['title'];
		$view->subdir   = $this->subdir;

		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		$view->msg = isset($this->_msg) ? $this->_msg : '';
		return $view->loadTemplate();
	}

	/**
	 * Show revision diffs
	 *
	 * @return  void  redirect
	 */
	protected function _diff()
	{
		// Combine file and folder data
		$items = $this->_sortIncoming();

		// Repo call params
		$params = array('subdir' => $this->subdir);

		if (!$items)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_SHOW_HISTORY'));
		}
		else
		{
			// Get selected item
			foreach ($items[0] as $type => $item)
			{
				$params['file'] = $this->repo->getMetadata($item, $type, $params);
				break;
			}
		}

		$params['rev1']     = urldecode(Request::getVar('old', ''));
		$params['rev2']     = urldecode(Request::getVar('new', ''));
		$params['fullDiff'] = Request::getInt('full', 0);
		$params['mode']     = urldecode(Request::getVar('mode', 'side-by-side'));

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'files',
				'name'    => 'history',
				'layout'  => 'diff'
			)
		);

		// Run diff
		$view->diff = $this->repo->diff($params);

		$view->url    = Route::url($this->model->link('files'));
		$view->config = $this->model->config();
		$view->file   = $params['file'];
		$view->option = $this->_option;
		$view->model  = $this->model;
		$view->repo   = $this->repo;
		$view->uid    = $this->_uid;
		$view->title  = $this->_area['title'];
		$view->subdir = $this->subdir;
		$view->ajax   = 0;
		$view->params = $params;

		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		$view->msg = isset($this->_msg) ? $this->_msg : '';
		return $view->loadTemplate();

	}

	/**
	 * Serve file (usually via public link)
	 *
	 * @param   string   $type
	 * @param   integer  $projectid
	 * @param   string   $query
	 * @return  void
	 */
	public function serve($type = '', $projectid = 0, $query = '')
	{
		$this->_area = $this->onProjectAreas();
		if ($type != $this->_area['name'])
		{
			return false;
		}
		$data = json_decode($query);

		if (!isset($data->file) || !$projectid)
		{
			return false;
		}

		$file     = $data->file;
		$disp     = isset($data->disp) ? $data->disp : 'inline';
		$limited  = isset($data->limited) ? $data->limited : 0;
		$hash     = isset($data->hash) ? $data->hash : 0;
		$repoName = isset($data->repo) ? $data->repo : 'local';

		// Instantiate a project
		$model = new \Components\Projects\Models\Project($projectid);

		if (!$model->exists() || ($limited == 1 && !$model->access('member')))
		{
			// Throw error
			App::abort(403, Lang::txt('COM_PROJECTS_ERROR_ACTION_NOT_AUTHORIZED'));
		}

		// Load repo
		$repo = new \Components\Projects\Models\Repo($model, $repoName);

		$deleteTemp = false;
		if ($hash)
		{
			$tempPath = sys_get_temp_dir();
			$tempName = 'temp-' . \Components\Projects\Helpers\Html::generateCode(4, 4, 0, 1, 0) . basename($file);
			$serve    = $tempPath . DS . $tempName;

			// Get file content
			$repo->call('content', $params = array(
					'fileName' => $file,
					'hash'     => $hash,
					'target'   => $serve
				)
			);

			$deleteTemp = true;
		}
		else
		{
			$serve = $repo->get('path') . DS . $file;
		}

		// Ensure the file exist
		if (!file_exists($serve))
		{
			// Throw error
			App::abort(404, Lang::txt('COM_PROJECTS_FILE_NOT_FOUND'));
		}

		// Initiate a new content server and serve up the file
		$server = new \Hubzero\Content\Server();
		$server->filename($serve);
		$server->disposition($disp);
		$server->acceptranges(false); // @TODO fix byte range support
		$server->saveas(basename($file));

		$result = $server->serve();

		if ($deleteTemp)
		{
			// Delete downloaded temp file
			Filesystem::delete($serve);
		}

		if (!$result)
		{
			// Should only get here on error
			App::abort(404, Lang::txt('PLG_PROJECTS_FILES_SERVER_ERROR'));
		}
		else
		{
			exit;
		}

		return;
	}

	/**
	 * Restore deleted files
	 *
	 * @return  void  redirect
	 */
	protected function _restore()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			App::abort(403, Lang::txt('ALERTNOTAUTH'));
		}

		// Incoming
		$item = urldecode(Request::getVar('asset', ''));
		$hash = Request::getVar('hash', '');

		// Params for repo call
		$params = array('subdir'  => $this->subdir);
		$params['file']    = $this->repo->getMetadata($item, 'file', $params);
		$params['version'] = $hash;

		// After successful action
		if ($this->repo->restore($params))
		{
			// Store in session
			$this->registerUpdate('restored', $item, false);

			// Force sync
			if ($this->repo->isLocal())
			{
				$this->model->saveParam('google_sync_queue', 1);
			}
		}
		else
		{
			$error = $this->repo->getError()
				? $this->repo->getError()
				: $this->setError(Lang::txt('PLG_PROJECTS_FILES_RESTORE_FAILED'));
			\Notify::message($error, 'error', 'projects');
		}

		// Redirect to file list
		$url  = $this->model->link('files') . '&action=browse';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Redirect
		App::redirect(Route::url($url));
	}

	/**
	 * Download file(s)
	 *
	 * @return  void  redirect
	 */
	protected function _download()
	{
		// Incoming
		$render = Request::getVar('render', 'download');
		$hash   = Request::getVar('hash', '');

		// Metadata collector
		$collector = array();

		// Combine file and folder data
		$items = $this->_sortIncoming();

		// Get stored remote connections
		$remotes = $this->_getRemoteConnections(false);

		// Params for repo call
		$params = array(
			'subdir'            => $this->subdir,
			'remoteConnections' => $remotes
		);

		// Collect items
		if (!$items)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_SHOW_HISTORY'));
		}
		else
		{
			foreach ($items as $element)
			{
				foreach ($element as $type => $item)
				{
					// Get type and item name
					break;
				}

				// Must have a name
				if (trim($item) == '')
				{
					continue;
				}

				// Build metadata object
				$collector[] = $this->repo->getMetadata($item, $type, $params);
			}
		}

		// Check that we have item(s) to download
		if (empty($collector))
		{
			// Throw error
			App::abort(404, Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND'));
		}

		// File preview?
		if ($render == 'preview')
		{
			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'projects',
					'element' =>'files',
					'name'    =>'preview'
				)
			);

			$view->file = isset($collector[0]) ? $collector[0] : NULL;

			// Get last revision
			if (!$view->file->get('converted') && !$hash)
			{
				$params['file'] = $view->file;
				$hash = $this->repo->getLastRevision($params);
				$view->file->set('hash', $hash);
			}
			$view->option = $this->_option;
			$view->model  = $this->model;

			if (!($view->file instanceof \Components\Projects\Models\File))
			{
				$view->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_FILE_INFO_NOT_FOUND'));
			}
			return $view->loadTemplate();
		}

		// Other rendering?
		if ($render == 'thumb' || $render == 'inline' || $render == 'medium')
		{
			$file = isset($collector[0]) ? $collector[0] : NULL;
			if (!($file instanceof \Components\Projects\Models\File))
			{
				App::abort(404, Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND'));
			}
			// Get last revision
			if (!$file->get('converted') && !$hash)
			{
				$params['file'] = $file;
				$hash = $this->repo->getLastRevision($params);
			}

			$image = $file->getPreview($this->model, $hash, 'fullPath', $render);

			// Serve image
			if ($image && is_file($image))
			{
				$server = new \Hubzero\Content\Server();
				$server->filename($image);
				$server->serve_inline($image);
				exit;
			}
		}

		// File download
		if (count($items) > 1)
		{
			$archive = $this->_archiveFiles($items);

			if (!$archive)
			{
				$this->setError($this->getError() . ' ' .Lang::txt('PLG_PROJECTS_FILES_ARCHIVE_ERROR'));
			}
			else
			{
				$downloadPath   = $archive['path'];
				$serveas        = 'Project Files ' . Date::toSql() . '.zip';
			}
		}
		else
		{
			$file = isset($collector[0]) ? $collector[0] : NULL;
			if (!($file instanceof \Components\Projects\Models\File))
			{
				App::abort(404, Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND'));
			}
			$serveas = $file->get('name');

			// Open converted file
			if (!empty($this->_remoteService) && $file->get('converted') && $this->_task == 'open')
			{
				// Is user connected?
				$connected = $this->_connect->getStoredParam($this->_remoteService . '_token', $this->_uid);

				if (!$connected)
				{
					// Redirect to connect screen
					\Notify::message(Lang::txt('PLG_PROJECTS_FILES_REMOTE_PLEASE_CONNECT'), 'success', 'projects');

					// Redirect
					App::redirect(Route::url($this->model->link('files') . '&action=connect'));
				}

				// Load remote resource
				$this->_connect->setUser($this->model->get('owned_by_user'));
				$resource = $this->_connect->loadRemoteResource($this->_remoteService,
					$this->model->get('owned_by_user'), $file->get('remoteId'));

				$openLink = $resource && isset($resource['alternateLink']) ? $resource['alternateLink'] : '';

				if (!$openLink)
				{
					// Throw error
					App::abort(404, Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND') . ' ' . $file->get('name'));
				}

				// Redirect
				App::redirect($openLink);
			}

			// Import & download converted file
			if (!empty($this->_remoteService) && $file->get('converted'))
			{
				$temp_path = sys_get_temp_dir();

				// Load remote resource
				$this->_connect->setUser($this->model->get('owned_by_user'));
				$resource = $this->_connect->loadRemoteResource($this->_remoteService,
					$this->model->get('owned_by_user'), $file->get('remoteId'));

				// Tex file?
				$tex    = Components\Projects\Helpers\Compiler::isTexFile($file->get('remoteTitle'), $file->get('originalFormat'));

				$cExt   = $tex ? 'tex' : \Components\Projects\Helpers\Google::getGoogleImportExt($file->get('mimeType'));
				$url    = \Components\Projects\Helpers\Google::getDownloadUrl($resource, $cExt);

				$data = $this->_connect->sendHttpRequest(
					$this->_remoteService,
					$this->model->get('owned_by_user'),
					$url
				);

				// Clean up data from Windows characters - important!
				$data = $tex ? preg_replace('/[^(\x20-\x7F)\x0A]*/','', $data) : $data;

				$ftname = \Components\Projects\Helpers\Google::getImportFilename($file->get('remoteTitle'), $cExt);
				$serveas = $ftname;

				$this->_connect->fetchFile($data, $ftname, $temp_path);
				$downloadPath = $temp_path . DS . $ftname;
			}
			// Download local revision
			elseif ($hash)
			{
				$tempPath = 'temp-' . \Components\Projects\Helpers\Html::generateCode (4 ,4 ,0 ,1 ,0) . $serveas;
				$downloadPath = sys_get_temp_dir() . DS . $tempPath;

				// Get file content
				$params = array('fileName' => $file->get('localPath'), 'hash' => $hash, 'target' => $downloadPath);
				$this->repo->getFileContent($params);
			}
			else
			{
				// Viewing current file
				$serveas 	  = urldecode(Request::getVar('serveas', $file->get('name')));
				$downloadPath = $file->get('fullPath');
			}
		}

		// Now we can actually download
		if (!empty($downloadPath))
		{
			// Ensure the file exist
			if (!file_exists($downloadPath))
			{
				// Throw error
				App::abort(404, Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND'));
			}

			// Cannot download zero byte files
			if (filesize($downloadPath) == 0)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_ZERO_BYTE'));
			}

			// Proceed with download
			if (!$this->getError())
			{
				// Initiate a new content server and serve up the file
				$server = new \Hubzero\Content\Server();
				$server->filename($downloadPath);
				$server->disposition('attachment');
				$server->acceptranges(false);
				$server->saveas($serveas);
				$result = $server->serve_attachment($downloadPath, $serveas, false);

				if (!$result)
				{
					// Should only get here on error
					App::abort(404, Lang::txt('PLG_PROJECTS_FILES_SERVER_ERROR'));
				}
				else
				{
					// Clean up the /tmp directory from zip files (download multiple files)
					$temp_path = sys_get_temp_dir();
					$matches = array();
					preg_match('/^(\\/tmp.*?\\.zip)/is', $downloadPath, $matches);
					if (!empty($matches))
					{
						\Hubzero\Filesystem::delete($downloadPath);
					}

					exit;
				}
			}
		}

		// Pass success or error message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}

		// Redirect to file list
		$url  = $this->model->link('files') . '&action=browse';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Redirect
		App::redirect(Route::url($url));
	}

	/**
	 * Compile PDF/image preview for any kind of file
	 *
	 *
	 * @return  mixed  array or false
	 */
	protected function _compile()
	{
		// Combine file and folder data
		$items = $this->_sortIncoming();

		// Get stored remote connections
		$remotes = $this->_getRemoteConnections();

		// Params for repo call
		$params = array(
			'subdir'            => $this->subdir,
			'remoteConnections' => $remotes
		);

		// Incoming
		$commit     = Request::getInt('commit', 0);
		$download   = Request::getInt('download', 0);

		// Check that we have compile enabled
		if (!$this->params->get('latex'))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_COMPILE_NOTALLOWED'));
			return;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'files',
				'name'    =>'compiled'
			)
		);

		// Get selected item
		if (!$items)
		{
			$view->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_COMPILE'));
			$view->loadTemplate();
			return;
		}
		else
		{
			foreach ($items as $element)
			{
				foreach ($element as $type => $item)
				{
					// Get our metadata
					$file = $this->repo->getMetadata($item, 'file', $params);
					break;
				}
			}
		}

		// We need a file
		if (empty($file))
		{
			$view->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_COMPILE'));
			$view->loadTemplate();
			return;
		}

		// Path for storing temp previews
		$imagePath = trim($this->model->config()->get('imagepath', '/site/projects'), DS);
		$outputDir = DS . $imagePath . DS . strtolower($this->model->get('alias')) . DS . 'compiled';

		// Make sure output dir exists
		if (!is_dir(PATH_APP . $outputDir))
		{
			if (!Filesystem::makeDirectory(PATH_APP . $outputDir))
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return;
			}
		}

		// Get LaTeX helper
		$compiler = new \Components\Projects\Helpers\Compiler();

		// Tex compiler path
		$texPath = DS . trim($this->params->get('texpath'), DS);

		$view->file    = $file;
		$view->oWidth  = '780';
		$view->oHeight = '460';
		$view->url     = Route::url($this->model->link('files'));
		$cExt          = 'pdf';

		// Take out Google native extension if present
		$fileName = $file->get('name');
		if (in_array($file->get('ext'), \Components\Projects\Helpers\Google::getGoogleNativeExts()))
		{
			$fileName = preg_replace("/." . $file->get('ext') . "\z/", "", $file->get('name'));
		}

		// Tex file?
		$tex = $compiler->isTexFile($fileName);

		// Build temp name
		$tempBase = $tex ? 'temp__' . \Components\Projects\Helpers\Html::takeOutExt($fileName) : $fileName;
		$tempBase = str_replace(' ', '_', $tempBase);

		// Get file contents
		if (!empty($this->_remoteService) && $file->get('converted'))
		{
			// Load remote resource
			$this->_connect->setUser($this->model->get('owned_by_user'));
			$resource = $this->_connect->loadRemoteResource(
				$this->_remoteService,
				$this->model->get('owned_by_user'),
				$file->get('remoteId')
			);

			$cExt   = $tex ? 'tex' : \Components\Projects\Helpers\Google::getGoogleImportExt($resource['mimeType']);
			$cExt   = in_array($cExt, array('tex', 'jpeg')) ? $cExt : 'pdf';
			$url    = \Components\Projects\Helpers\Google::getDownloadUrl($resource, $cExt);

			// Get data
			$view->data = $this->_connect->sendHttpRequest(
				$this->_remoteService,
				$this->model->get('owned_by_user'),
				$url
			);
		}
		elseif ($file->exists())
		{
			$view->data = $file->isImage() ? NULL : $file->contents();
		}
		else
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_COMPILE_NO_DATA'));
		}

		// LaTeX file?
		if ($tex && !empty($view->data))
		{
			// Clean up data from Windows characters - important!
			$view->data = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $view->data);

			// Compile and get path to PDF
			$contentFile = $compiler->compileTex(
				$file->get('fullPath'),
				$view->data,
				$texPath,
				PATH_APP . $outputDir, 1, $tempBase
			);

			// Read log (to show in case of error)
			$logFile = $tempBase . '.log';
			if (file_exists(PATH_APP . $outputDir . DS . $logFile))
			{
				$view->log = Filesystem::read(PATH_APP . $outputDir . DS . $logFile);
			}

			if (!$contentFile)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_COMPILE_TEX_FAILED'));
			}
		}
		elseif ($file->get('converted') && !empty($view->data))
		{
			$tempBase = \Components\Projects\Helpers\Google::getImportFilename($file->get('name'), $cExt);

			// Write content to temp file
			$this->_connect->fetchFile($view->data, $tempBase, PATH_APP . $outputDir);
			$contentFile = $tempBase;
		}
		// Local file
		elseif (!$this->getError())
		{
			// Make sure we can handle preview of this type of file
			if ($file->get('ext') == 'pdf' || $file->isImage() || !$file->isBinary())
			{
				Filesystem::copy($file->get('fullPath'), PATH_APP . $outputDir . DS . $tempBase);
				$contentFile = $tempBase;
			}
		}

		$url  = $this->model->link('files');
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Parse output
		if (!empty($contentFile) && file_exists(PATH_APP . $outputDir . DS . $contentFile))
		{
			// Get compiled content mimetype
			$cType = Filesystem::mimetype(PATH_APP . $outputDir . DS . $contentFile);

			// Is image?
			if (strpos($cType, 'image/') !== false)
			{
				// Fix up object width & height
				list($width, $height, $type, $attr) = getimagesize(PATH_APP . $outputDir . DS . $contentFile);

				$xRatio	= $view->oWidth / $width;
				$yRatio	= $view->oHeight / $height;

				if ($xRatio * $height < $view->oHeight)
				{
					// Resize the image based on width
					$view->oHeight = ceil($xRatio * $height);
				}
				else
				{
					// Resize the image based on height
					$view->oWidth  = ceil($yRatio * $width);
				}
			}

			// Download compiled file?
			if ($download)
			{
				$pdfName = $tex ? str_replace('temp__', '', basename($contentFile)) : basename($contentFile);

				// Serve up file
				$server = new \Hubzero\Content\Server();
				$server->filename(PATH_APP . $outputDir . DS . $contentFile);
				$server->disposition('attachment');
				$server->acceptranges(false);
				$server->saveas($pdfName);
				$result = $server->serve_attachment(PATH_APP . $outputDir . DS . $contentFile, $pdfName, false);

				if (!$result)
				{
					// Should only get here on error
					App::abort(404, Lang::txt('PLG_PROJECTS_FILES_SERVER_ERROR'));
				}
				else
				{
					exit;
				}
			}

			// Add compiled PDF to repository?
			if ($commit && $tex)
			{
				$pdfName = str_replace('temp__', '', basename($contentFile));
				$where   = $this->subdir ? $this->subdir. DS . $pdfName : $pdfName;

				if (Filesystem::copy(PATH_APP . $outputDir . DS . $contentFile, $this->_path . DS . $where))
				{
					// Checkin into repo
					$params = array('subdir' => $this->subdir);
					$params['file'] = $this->repo->getMetadata($pdfName, 'file', $params);
					$this->repo->call('checkin', $params);

					if ($this->repo->isLocal())
					{
						$this->model->saveParam('google_sync_queue', 1);
					}

					\Notify::message(Lang::txt('PLG_PROJECTS_FILES_SUCCESS_COMPILED'), 'success', 'projects');

					// Redirect to file list
					App::redirect(Route::url($url));
					return;
				}
			}

			// Generate preview image for browsers that cannot embed pdf
			if ($cType == 'application/pdf')
			{
				// GS path
				$gspath = trim($this->params->get('gspath'), DS);
				if ($gspath && file_exists(DS . $gspath . DS . 'gs'))
				{
					$gspath = DS . $gspath . DS;

					$pdfName    = $tex ? str_replace('temp__', '', basename($contentFile)) : basename($contentFile);
					$pdfPath    = PATH_APP . $outputDir . DS . $contentFile;
					$exportPath = PATH_APP . $outputDir . DS . $tempBase . '%d.jpg';

					exec($gspath . "gs -dNOPAUSE -sDEVICE=jpeg -r300 -dFirstPage=1 -dLastPage=1 -sOutputFile=$exportPath $pdfPath 2>&1", $out);

					if (is_file(PATH_APP . $outputDir . DS . $tempBase . '1.jpg'))
					{
						$hi = new \Hubzero\Image\Processor(PATH_APP . $outputDir . DS . $tempBase . '1.jpg');
						if (count($hi->getErrors()) == 0)
						{
							$hi->resize($view->oWidth, false, false, true);
							$hi->save(PATH_APP . $outputDir . DS . $tempBase . '1.jpg');
						}
						else
						{
							return false;
						}
					}
					if (is_file(PATH_APP . $outputDir . DS . $tempBase . '1.jpg'))
					{
						$image = $tempBase . '1.jpg';
					}
				}
			}
		}
		elseif (!$this->getError())
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_COMPILE_PREVIEW_FAILED'));
		}

		$view->file      = $file;
		$view->outputDir = $outputDir;
		$view->embed     = $contentFile;
		$view->cType     = $cType;
		$view->subdir    = $this->subdir;
		$view->option    = $this->_option;
		$view->image     = !empty($image) ? $image : NULL;
		$view->model     = $this->model;
		$view->repo      = $this->repo;

		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * Send file back or from to remote service for remote editing
	 * Local repo only
	 *
	 * @return  void  redirect
	 */
	protected function _share()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			App::abort(403, Lang::txt('ALERTNOTAUTH'));
		}

		// Incoming
		$converted = Request::getInt('converted', 0);
		$service   = Request::getVar('service', 'google');
		$sync      = false;
		$shared    = 0;

		// Get stored remote connections
		$remotes = $this->_getRemoteConnections(false);

		// Params for repo call
		$params = array(
			'subdir'            => $this->subdir,
			'remoteConnections' => $remotes
		);

		// Combine file and folder data
		$items = $this->_sortIncoming();

		if (empty($items))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_SHARE'));
		}
		else
		{
			// Sharing for a single file
			$type = key($items[0]);
			$item = $items[0][$type];

			// Build metadata object
			$file = $this->repo->getMetadata($item, $type, $params);
		}

		// Build return url
		$url  = $this->model->link('files');
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Check user is connected
		if (!empty($this->_remoteService))
		{
			$connected = $this->_connect->getStoredParam($this->_remoteService . '_token', $this->_uid);
			if (!$connected)
			{
				// Redirect to connect screen
				\Notify::message(Lang::txt('PLG_PROJECTS_FILES_REMOTE_PLEASE_CONNECT'), 'success', 'projects');

				App::redirect(Route::url($this->model->link('files') . '&action=connect'));
			}
		}
		else
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_REMOTE_NOT_ENABLED'));
		}

		// Confirmation screen
		if ($this->_task == 'share')
		{
			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'projects',
					'element' =>'files',
					'name'    =>'share'
				)
			);

			$view->option = $this->_option;
			$view->model  = $this->model;
			$view->repo   = $this->repo;
			$view->uid    = $this->_uid;
			$view->subdir = $this->subdir;
			$view->path   = $this->_path;

			if ($this->getError())
			{
				$view->setError($this->getError());
			}
			else
			{
				$view->file    = !empty($file) ? $file : NULL;
				$view->connect = $this->_connect;
				$view->service = $this->_remoteService;
			}

			return $view->loadTemplate();
		}

		// On error
		if ($this->getError())
		{
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
			$view->setError($this->getError());
			return $view->loadTemplate();
		}

		// Send file for remote editing on Google
		if ($this->_task == 'shareit')
		{
			// Get convertable formats
			$formats = \Components\Projects\Helpers\Google::getGoogleConversionExts();

			// Import remote file
			if ($file->get('converted'))
			{
				// Load remote resource
				$resource = $this->_connect->loadRemoteResource($service, $this->_uid, $file->get('remoteId'));

				if (!$resource)
				{
					$this->setError(Lang::txt('PLG_PROJECTS_FILES_SHARING_NO_REMOTE'));
				}
				else
				{
					// Incoming
					$importExt = Request::getVar('format', 'pdf', 'post');

					// Remove Google native extension from title
					$title = $file->get('name');
					if (in_array($file->get('ext'), array('gdoc', 'gsheet', 'gslides', 'gdraw')))
					{
						$title = preg_replace("/." . $file->get('ext') . "\z/", "", $file->get('name'));
					}

					// Do we have extention in name already? - take it out
					$n_parts = explode('.', $title);
					$n_ext   = count($n_parts) > 1 ? array_pop($n_parts) : '';
					$title   = implode($n_parts);
					$title  .= '.' . $importExt;

					$newpath = $this->subdir ? $this->subdir. DS . $title : $title;
					$file->set('localPath', $newpath);
					$file->set('name', $title);
					$file->set('fullPath', $this->_path. DS . $newpath);

					// Replacing file?
					$exists = file_exists($file->get('fullPath')) ? 1 : 0;

					// Download remote file
					if ($this->_connect->importFile($this->_remoteService, $this->_uid, $resource,
						$file->get('localPath'), $this->repo->get('path'), $importExt))
					{
						// Checkin into repo
						$params['file'] = $file;
						$this->repo->call('checkin', $params);

						// Remove remote resource
						$deleted = $this->_connect->deleteRemoteItem(
							$this->model->get('id'),
							$service, $this->_uid,
							$file->get('remoteId'), false
						);

						// Create remote file for imported file
						$created = NULL;
						if (!$exists)
						{
							$created = $this->_connect->addRemoteFile(
								$this->model->get('id'),
								$this->_remoteService,
								$this->_uid,
								$file
							);
						}

						// Update connection record
						$this->_connect->savePairing(
							$this->model->get('id'),
							$this->_remoteService,
							$created,
							$file->get('localPath'),
							$file->get('recordId'),
							$file->get('originalPath'),
							$file->get('originalFormat'),
							$file->get('remoteId')
						);
					}

					// Output message
					\Notify::message(Lang::txt('PLG_PROJECTS_FILES_UNSHARE_SUCCESS') . ' ' . $title, 'success', 'projects');

					// Force sync
					$sync = true;
				}
			}
			// Export local file
			else
			{
				// Check that local file exists
				if (!$this->repo->fileExists($file->get('localPath')))
				{
					$this->setError(Lang::txt('PLG_PROJECTS_FILES_SHARING_LOCAL_FILE_MISSING'));
				}
				else
				{
					// LaTeX?
					$tex = Components\Projects\Helpers\Compiler::isTexFile($file->get('name'), $file->getMimeType());

					// Check format
					if (!in_array($file->get('ext'), $formats) && !$tex)
					{
						$this->setError(Lang::txt('PLG_PROJECTS_FILES_SHARING_NOT_CONVERTABLE'));
					}

					if (!$this->getError())
					{
						if ($tex)
						{
							// LaTeX? Convert to text file first
							$file->set('mimeType', 'text/plain');
						}
						if ($file->get('ext') == 'wmf')
						{
							// WMF files need this mime type specified for conversion to Google drawing
							$file->set('mimeType', 'application/x-msmetafile');
						}
						if ($file->get('ext') == 'ppt' || $file->get('ext') == 'pps' || $file->get('ext') == 'pptx')
						{
							$file->set('mimeType', 'application/vnd.openxmlformats-officedocument.presentationml.presentation');
						}

						// Convert file
						$added = $this->_connect->addRemoteFile(
							$this->model->get('id'),
							$this->_remoteService,
							$this->_uid,
							$file,
							$file->get('remoteParent'),
							true
						);

						if ($added)
						{
							// Remove original local file
							$params['file'] = $file;
							$this->repo->deleteItem($params);

							// Remove original remote file
							$deleted = $this->_connect->deleteRemoteItem(
								$this->model->get('id'),
								$this->_remoteService,
								$this->_uid,
								$file->get('remoteId'),
								false
							);

							$file->clear('mimeType');
							$file->setMimeType();

							// Update connection record
							$this->_connect->savePairing(
								$this->model->get('id'),
								$this->_remoteService,
								$added,
								'',
								$file->get('recordId'),
								$file->get('originalPath'),
								$file->get('mimeType'),
								$file->get('remoteId')
							);

							// Output message
							\Notify::message(Lang::txt('PLG_PROJECTS_FILES_SHARE_SUCCESS'), 'success', 'projects');

							// Force sync
							$sync = true;
						}
						else
						{
							// Something went wrong
							$this->setError(Lang::txt('PLG_PROJECTS_FILES_SHARE_ERROR_NO_CONVERT'));

							if ($this->_connect->getError())
							{
								$this->setError($this->_connect->getError());
							}
						}
					}
				}
			}
		}

		// Pass success or error message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}

		// Force sync
		if ($sync && $this->repo->isLocal())
		{
			$this->model->saveParam('google_sync_queue', 1);
		}

		// Redirect to file list
		App::redirect(Route::url($url));
	}

	/**
	 * Optimize repository
	 *
	 * @param   object  $model     Project model
	 * @param   string  $repoName
	 * @return  string
	 */
	public function optimize($model, $repoName = 'local')
	{
		if (!isset($this->repo))
		{
			$this->repo = new \Components\Projects\Models\Repo($model, $repoName);
		}
		$adv = $this->_task == 'advoptimize' ? true : false;
		$params = array(
			'path' => $this->_path,
			'adv'  => $adv
		);
		$this->repo->call('optimize', $params);
		return true;
	}

	/**
	 * Show disk usage
	 *
	 * @param   object   $model     Project model
	 * @param   string   $repoName
	 * @param   integer  $by
	 * @return  string
	 */
	public function diskspace($model, $repoName = 'local', $by = '')
	{
		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'files',
				'name'    =>'diskspace'
			)
		);

		if (!isset($this->repo))
		{
			$this->repo = new \Components\Projects\Models\Repo($model, $repoName);
		}

		$url = Route::url('index.php?option=' . $this->_option . '&alias=' . $model->get('alias') . '&active=files&action=diskspace');

		// Report usage with all history?
		if ($this->params->get('disk_usage') == true || $by == 'admin')
		{
			$view->dirsize = $this->repo->call('getDiskUsage',
				$params = array('working' => true, 'history' => true)
			);
			$view->totalspace = $this->repo->call('getDiskUsage',
				$params = array('working' => false, 'history' => false)
			);
		}
		else
		{
			if ($this->params->get('project_quota'))
			{
				// Report usage only with current files and not with history
				$view->totalspace = $this->repo->call('getDiskUsage',
				$params = array('working' => true, 'history' => false)   
			);
			}
			else
			{
				// Original Code
				$view->totalspace = $this->repo->call('getDiskUsage',
				$params = array('working' => false, 'history' => true)   
			);				
			}

			$view->dirsize = $view->totalspace;
		}

		// Get publication usage
		if (Plugin::isEnabled('projects', 'publications') && $by == 'admin')
		{
			require_once Component::path('com_publications') . DS . 'helpers' . DS . 'html.php';

			$filters = array();
			$filters['project']       = $model->get('id');
			$filters['ignore_access'] = 1;
			$filters['dev']           = 1;

			$database = \App::get('db');

			$objP = new \Components\Publications\Tables\Publication($database);
			$pubs = $objP->getRecords($filters);

			$view->pubDiskUsage = \Components\Publications\Helpers\Html::getDiskUsage($pubs);
			$view->pubQuota     = $model->params->get('pubQuota')
								? $model->params->get('pubQuota')
								: \Components\Projects\Helpers\Html::convertSize(floatval($model->config()->get('pubQuota', '1')), 'GB', 'b');
		}

		$view->total  = $this->repo->count();
		$view->quota  = $model->params->get('quota', \Components\Projects\Helpers\Html::convertSize(floatval($model->config()->get('defaultQuota', '1')), 'GB', 'b'));

		$view->by     = $by;
		$view->model  = $model;
		$view->option = $this->_option;
		$view->config = $model->config();
		$view->title  = isset($this->_area['title']) ? $this->_area['title'] : '';
		$view->params = $this->params;

		return $view->loadTemplate();
	}

	/**
	 * Show deleted files
	 *
	 * @return  string
	 */
	protected function _showTrash()
	{
		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'files',
				'name'    =>'trash'
			)
		);

		// Get deleted files
		$view->files  = $this->repo->getTrash();

		$view->option = $this->_option;
		$view->model  = $this->model;
		$view->ajax   = Request::getInt('ajax', 0);

		// Build URL
		$url          = $this->model->link('files');
		$url         .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url         .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
		$view->url    = $url;
		$view->subdir = $this->subdir;

		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		return $view->loadTemplate();
	}

	/**
	 * Manage connections to outside services
	 *
	 * @param   string  $service   Service name (google/dropbox)
	 * @param   string  $callback  URL to return to after authorization
	 * @return  string
	 */
	protected function _connect($service = '', $callback = '')
	{
		// Incoming
		$service    = $service ? $service : Request::getVar('service', '');
		$reauth     = Request::getInt('reauth', 0);
		$removeData = Request::getInt('removedata', 1);

		// Build pub url
		$url = Route::url($this->model->link('files'));

		// Build return URL
		$return = $callback ? $callback : $url . '?action=connect';

		// Handle authentication request for service
		if ($service)
		{
			$configs = $this->_connect->getConfigs($service, false);

			if ($this->_task == 'disconnect')
			{
				if ($this->_connect->disconnect($service, $removeData))
				{
					$this->_msg = Lang::txt('PLG_PROJECTS_FILES_DISCONNECT_SUCCESS')
						. ' ' . $configs['servicename'];
				}
				else
				{
					$this->setError($this->_connect->getError());
				}

				// Redirect to connect screen
				App::redirect(Route::url($this->model->link('files') . '&action=connect'));
				return;
			}
			elseif (!$this->_connect->makeConnection($service, $reauth, $return))
			{
				$this->setError($this->_connect->getError());
			}
			else
			{
				// Successful authentication
				if (!$this->_connect->afterConnect($service, $this->_uid))
				{
					$this->setError($this->_connect->getError());
				}
				else
				{
					$this->_msg = Lang::txt('PLG_PROJECTS_FILES_CONNECT_SUCCESS');
				}
			}

			// Refresh info
			$this->_connect->setConfigs();
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder' =>'projects',
				'element'=>'files',
				'name'   =>'connect'
			)
		);

		$view->option   = $this->_option;
		$view->database = $this->_database;
		$view->model    = $this->model;
		$view->uid      = $this->_uid;
		$view->url      = $url;
		$view->title    = $this->_area['title'];
		$view->services = $this->_connect->getServices();
		$view->connect  = $this->_connect;

		// Get refreshed params
		$this->model->reloadProject();
		$view->params = new \Hubzero\Config\Registry($this->model->table()->params);

		// Get connection details for user
		$member = $this->model->member(true);
		$view->oparams = new \Hubzero\Config\Registry($member->params);

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		return $view->loadTemplate();
	}

	/**
	 * Initiate sync
	 *
	 * @return  void
	 */
	protected function _iniSync()
	{
		// Incoming
		$ajax  = Request::getInt('ajax', 0);
		$auto  = Request::getInt('auto', 0);
		$queue = Request::getInt('queue', 0);

		// Timed sync?
		$autoSync = $this->params->get('auto_sync', 0);
		$this->_rSync = new Sync($this->_connect);

		// Remote service(s) active?
		if (!empty($this->_connect->_active) && $this->repo->isLocal())
		{
			// Get remote files for each active service
			foreach ($this->_connect->_active as $servicename)
			{
				// Set syncing service
				$this->_rSync->set('service', $servicename);

				// Get time of last sync
				$synced = $this->model->params->get($servicename . '_sync');

				// Stop if auto sync request and not enough time passed
				if ($auto && $autoSync && !$queue)
				{
					if ($autoSync < 1)
					{
						$hr = 60 * $autoSync;
						$timecheck = Date::of(time() - (1 * $hr * 60));
					}
					else
					{
						$timecheck = Date::of(time() - ($autoSync * 60 * 60));
					}

					if ($synced > $timecheck)
					{
						return json_encode(array('status' => 'waiting'));
					}
				}

				// Send sync request
				$success = $this->_rSync->sync($servicename, $queue, $auto);

				// Unlock sync
				if ($success)
				{
					$this->_rSync->lockSync($servicename, true);
				}

				// Success message
				$this->_rSync->set('message', Lang::txt('PLG_PROJECTS_FILES_SYNC_SUCCESS'));
			}
		}

		$this->_rSync->set('auto', $auto);

		if (!$ajax)
		{
			return $this->_browse();
		}
		else
		{
			$this->_rSync->set('output', $this->_browse());
			return json_encode($this->_rSync->getStatus());
		}
	}

	/**
	 * Sync error
	 *
	 * @return  string
	 */
	public function syncError()
	{
		$service = Request::getVar('service', 'google');

		$this->_rSync = new Sync($this->_connect);
		$this->_rSync->writeToFile('');
		$this->_rSync->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_ERROR'));
		$this->_rSync->lockSync($service, true);
	}

	/**
	 * Get sync status (AJAX call)
	 *
	 * @return  string
	 */
	public function syncStatus()
	{
		$this->_rSync = new Sync($this->_connect);

		// Incoming
		$pid     = Request::getInt('id', 0);
		$service = Request::getVar('service', 'google');
		$status  = array('status' => '', 'msg' => time(), 'output' => '');

		// Read status file
		$rFile = $this->_rSync->readFile();

		// Report sync progress
		if ($rFile && $rFile != Lang::txt('PLG_PROJECTS_FILES_SYNC_COMPLETE'))
		{
			$status = array('status' => 'progress', 'msg' => $rFile, 'output' => '');
		}
		elseif ($service)
		{
			// Get time of last sync
			$this->model->reloadProject();

			$synced   = $this->model->params->get($service . '_sync');
			$syncLock = $this->model->params->get($service . '_sync_lock', '');

			// Report last sync time
			$msg = $synced && $synced != 1
				? '<span class="faded">' . Lang::txt('PLG_PROJECTS_FILES_LAST_SYNC')
				. ' ' . \Components\Projects\Helpers\Html::timeAgo($synced, false)
				. ' ' . Lang::txt('COM_PROJECTS_AGO') . ' </span>'
				: '';
			$status = array('status' => 'complete', 'msg' => $msg);

			// Refresh view if sync happened recently
			$timecheck = date('c', time() - (1 * 1 * 60));
			if ($synced >= $timecheck)
			{
				$status['output'] = $this->_browse(2);
			}

			// Timed sync?
			$autoSync = $this->params->get('auto_sync', 0);
			if ($autoSync > 0)
			{
				if ($autoSync < 1)
				{
					$hr = 60 * $autoSync;
					$timecheck = date('c', time() - (1 * $hr * 60));
				}
				else
				{
					$timecheck = date('c', time() - ($autoSync * 60 * 60));
				}

				if ($synced <= $timecheck)
				{
					$status['auto'] = 1;
				}
			}
		}

		return json_encode($status);
	}

	/**
	 * Archive files
	 *
	 * @param   array  $items
	 * @return  mixed  array or false
	 */
	private function _archiveFiles($items)
	{
		if (!extension_loaded('zip'))
		{
			return false;
		}

		if (!$this->_path || !is_dir($this->_path))
		{
			return false;
		}

		if (empty($items))
		{
			return false;
		}

		$maxDownload  = intval($this->params->get('maxDownload', 104857600));

		// Get temp directory
		$base_path    = sys_get_temp_dir();
		$tarname      = 'project_files_' . \Components\Projects\Helpers\Html::generateCode(6, 6, 0, 1, 1) . '.zip';
		$path         = $this->subdir ? $this->_path . DS . $this->subdir : $this->_path;
		$combinedSize = 0;
		$tarpath      =  $base_path . DS . $tarname;

		$zip = new ZipArchive;

		if ($zip->open($tarpath, ZipArchive::OVERWRITE) === TRUE)
		{
			$i = 0;

			foreach ($items as $element)
			{
				foreach ($element as $type => $item)
				{
					if ($type != 'file')
					{
						continue;
					}
					else
					{
						$fpath = $path . DS . $item;

						if (!is_file($fpath))
						{
							continue;
						}

						$combinedSize = $combinedSize + filesize($fpath);

						// Check against maximum allowable size
						if ($combinedSize > $maxDownload)
						{
							$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_OVER_DOWNLOAD_LIMIT'));
							return false;
						}

						$zip->addFile($fpath, basename($item));
						$i++;
					}
				}
			}

			$zip->close();

			if ($i == 0)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_SERVER_ERROR'));
				return false;
			}

			$archive = array();
			$archive['path'] = $tarpath;
			$archive['name'] = $tarname;
			return $archive;
		}

		return false;
	}

	/**
	 * Get stored remote connections
	 *
	 * @param   bool   $remoteEdit
	 * @return  array
	 */
	protected function _getRemoteConnections($remoteEdit = true)
	{
		$remotes = array();
		if (!empty($this->_remoteService))
		{
			$objRFile = new \Components\Projects\Tables\RemoteFile($this->_database);
			$remotes  = $objRFile->getRemoteFiles(
				$this->model->get('id'),
				$this->_remoteService,
				$this->subdir,
				$remoteEdit
			);
		}

		return $remotes;
	}

	/**
	 * Get path to member dir (for provisioned projects)
	 *
	 * @return  string
	 */
	public function getMembersPath()
	{
		// Get members config
		$mconfig = Component::params('com_members');

		// Build upload path
		$dir  = \Hubzero\Utility\String::pad($this->_uid);
		$path = DS . trim($mconfig->get('webpath', '/site/members'), DS) . DS . $dir . DS . 'files';

		if (!is_dir(PATH_APP . $path))
		{
			if (!Filesystem::makeDirectory(PATH_APP . $path, 0755, true, true))
			{
				$this->setError(Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH'));
				return;
			}
		}

		return PATH_APP . $path;
	}

	/**
	 * Get member files (provisioned project)
	 *
	 * @param   boolean  $recurse
	 * @return  array
	 */
	protected function _getMemberFiles($recurse = true)
	{
		// Check path format
		$subdir = trim($this->subdir, DS);
		$fullpath = $subdir ? $this->_path . DS . $subdir : $this->_path;

		$get = Filesystem::files($fullpath);

		$files = array();
		if ($get)
		{
			foreach ($get as $file)
			{
				if (substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html')
				{
					$file = str_replace($this->_path . DS, '', $file);
					$entry = new \Components\Projects\Models\File(trim($file), $this->_path);
					$files[] = $entry;
				}
			}
		}

		return $files;
	}

	/**
	 * Register update info in user session
	 *
	 * @param   string  $type
	 * @param   string  $file
	 * @param   bool    $append
	 * @param   string  $appendMessage
	 * @return  void
	 */
	public function registerUpdate($type = '' , $file = '', $append = true, $appendMessage = '')
	{
		if (!$type || !$file)
		{
			return false;
		}

		$kind = 'projects.' . $this->model->get('alias') . '.' . $type;

		// Get session
		$jsession = App::get('session');

		if ($append == true)
		{
			$exVal = $jsession->get($kind);
			$val   = $exVal ? $exVal . ', ' . $file : $file;
		}
		else
		{
			$val = $file;
		}

		$val .= $appendMessage ? ' (' . $appendMessage . ') ' : '';

		$jsession->set($kind, $val);
		return true;
	}

	/**
	 * Event call after file update
	 *
	 * @param   object  $model
	 * @param   array   $changes
	 * @return  void
	 */
	public function onAfterUpdate($model = NULL, $changes = array())
	{
		$activity = '';
		$message  = '';
		$ref      = '';
		$sync     = 0;

		$model = $model ? $model : $this->model;

		if (empty($changes))
		{
			// Get session
			$jsession = App::get('session');

			// Get values from session
			$updated  = $jsession->get('projects.' . $model->get('alias') . '.updated');
			$uploaded = $jsession->get('projects.' . $model->get('alias') . '.uploaded');
			$failed   = $jsession->get('projects.' . $model->get('alias') . '.failed');
			$deleted  = $jsession->get('projects.' . $model->get('alias') . '.deleted');
			$restored = $jsession->get('projects.' . $model->get('alias') . '.restored');
			$expanded = $jsession->get('projects.' . $model->get('alias') . '.expanded');

			// Clean up session values
			$jsession->set('projects.' . $model->get('alias') . '.failed', '');
			$jsession->set('projects.' . $model->get('alias') . '.updated', '');
			$jsession->set('projects.' . $model->get('alias') . '.uploaded', '');
			$jsession->set('projects.' . $model->get('alias') . '.deleted', '');
			$jsession->set('projects.' . $model->get('alias') . '.restored', '');
			$jsession->set('projects.' . $model->get('alias') . '.expanded', '');
		}
		else
		{
			$updated  = !empty($changes['updated'])  ? $changes['updated']  : NULL;
			$uploaded = !empty($changes['uploaded']) ? $changes['uploaded'] : NULL;
			$failed   = !empty($changes['failed'])   ? $changes['failed']   : NULL;
			$deleted  = !empty($changes['deleted'])  ? $changes['deleted']  : NULL;
			$restored = !empty($changes['restored']) ? $changes['restored'] : NULL;
			$expanded = !empty($changes['expanded']) ? $changes['expanded'] : NULL;
		}

		// Provisioned project?
		if ($model->isProvisioned() || !$model->get('id'))
		{
			return false;
		}

		// Pass success or error message
		if (!empty($failed) && !$uploaded && !$uploaded)
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_FILES_ERROR_FAILED_TO_UPLOAD') . $failed, 'error', 'projects');
		}
		elseif ($uploaded || $updated || $expanded)
		{
			$uploadParts = explode(',', $uploaded);
			$updateParts = explode(',', $updated);
			$sync        = 1;

			if ($uploaded)
			{
				if (count($uploadParts) > 2)
				{
					$message = 'uploaded ' . basename($uploadParts[0]) . ' and ' . (count($uploadParts) - 1) . ' more files ' ;
				}
				else
				{
					$message = 'uploaded ';
					$u = 0;
					foreach ($uploadParts as $part)
					{
						$message .= basename($part);
						$u++;
						$message .= count($uploadParts) == $u ? '' : ', ';
					}
				}

				// Save referenced files
				$ref = $uploaded;
			}
			if ($updated)
			{
				$message .= $uploaded ? '. Updated ' : 'updated ';
				if (count($updateParts) > 2)
				{
					$message .= basename($updateParts[0]) . ' and ' . (count($updateParts) - 1) . ' more files ' ;
				}
				else
				{
					$u = 0;
					foreach ($updateParts as $part)
					{
						$message .= basename($part);
						$u++;
						$message .= count($updateParts) == $u ? '' : ', ';
					}
				}
			}

			$activity  = $message . ' ' . strtolower(Lang::txt('PLG_PROJECTS_FILES_IN_PROJECT_FILES')) ;

			$message = 'Successfully ' . $message;
			$message.= $failed ? ' There was a problem uploading ' . $failed : '';

			\Notify::message($message, 'success', 'projects');
		}
		elseif ($deleted)
		{
			// Save referenced files
			$ref = $deleted;
			$sync = 1;

			$delParts = explode(',', $deleted);

			$what = count($delParts) == 1 ? $deleted : count($delParts) . ' ' . Lang::txt('PLG_PROJECTS_FILES_ITEMS');

			// Output message
			\Notify::message(Lang::txt('PLG_PROJECTS_FILES_SUCCESS_DELETED') . ' ' . $what, 'success', 'projects');
		}
		elseif ($restored)
		{
			// Save referenced files
			$ref = $restored;
			$sync = 1;

			$resParts = explode(',', $restored);

			$activity = 'restored deleted file ' . basename($resParts[0]);

			// Output message
			\Notify::message(Lang::txt('PLG_PROJECTS_FILES_SUCCESS_RESTORED') . ' ' . basename($resParts[0]), 'success', 'projects');
		}

		// Add activity to feed
		if ($activity && $model->repo()->isLocal())
		{
			$refParts  = explode(',', $ref);
			$parsedRef = '';

			$selected = array();
			foreach ($refParts as $item)
			{
				$file = $model->repo()->getMetadata(trim($item));
				$params = array('file' => $file);
				if ($file->exists())
				{
					$hash = $model->repo()->getLastRevision($params);
					if ($hash)
					{
						$selected[] = substr($hash, 0, 10) . ':' . trim($file->get('localPath'));

						// Generate preview (regular and medium-size)
						$file->getPreview($model, $hash);
						$file->getPreview($model, $hash, '', 'medium');
					}
				}
			}

			// Save hash and file name in a reference
			if ($selected)
			{
				foreach ($selected as $sel)
				{
					if (((strlen($parsedRef) + strlen($sel)) <= 254))
					{
						$parsedRef .= $sel . ',';
					}
					else
					{
						break;
					}
				}
				$parsedRef = substr($parsedRef, 0, strlen($parsedRef) - 1);
			}

			// Check to make sure we are not over in char length
			if (strlen($parsedRef) > 255)
			{
				$parsedRef = \Components\Projects\Helpers\Html::shortenText($parsedRef);
			}

			// Force sync
			if ($sync)
			{
				//$this->model->saveParam('google_sync_queue', 1);
				$this->set('forceSync', 1);
			}

			// Record activity
			$aid = $model->recordActivity($activity, $parsedRef, 'files',
				Route::url($model->link('files')), 'files', 1
			);
		}
	}

	/**
	 * Clean incoming data
	 *
	 * @return  array
	 */
	protected function _cleanData()
	{
		// Clean up empty values
		$checked = Request::getVar('asset', array());
		$checked = (!is_array($checked) ? array($checked) : $checked);
		$folders = Request::getVar('folder', array());
		$folders = (!is_array($folders) ? array($folders) : $folders);

		foreach ($checked as $key => $value)
		{
			if (trim($value) == '')
			{
				unset($checked[$key]);
			}
			else
			{
				$checked[$key] = $value;
			}
		}

		foreach ($folders as $key => $value)
		{
			if (trim($value) == '')
			{
				unset($folders[$key]);
			}
			else
			{
				$folders[$key] = $value;
			}
		}

		Request::setVar('asset', $checked);
		Request::setVar('folder', $folders);
	}

	/**
	 * Sort incoming file/folder data
	 *
	 * @return  array
	 */
	protected function _sortIncoming()
	{
		// Clean incoming data
		$this->_cleanData();

		// Incoming
		$checked = Request::getVar('asset', array());
		$folders = Request::getVar('folder', array());

		$combined = array();
		if (!empty($checked) && is_array($checked))
		{
			foreach ($checked as $ch)
			{
				if (trim($ch) != '')
				{
					$combined[] = array('file' => urldecode($ch));
				}
			}
		}
		elseif ($file = Request::getVar('asset', ''))
		{
			$combined[] = array('file' => urldecode($file));
		}

		// [!] Legacy support
		$files = Request::getVar('file', array());
		if (!empty($files) && is_array($files))
		{
			foreach ($files as $ch)
			{
				if (trim($ch) != '')
				{
					$combined[] = array('file' => urldecode($ch));
				}
			}
		}
		elseif ($file = Request::getVar('file', ''))
		{
			$combined[] = array('file' => urldecode($file));
		}

		if (!empty($folders) && is_array($folders))
		{
			foreach ($folders as $f)
			{
				if (trim($f) != '')
				{
					$combined[] = array('folder' => urldecode($f));
				}
			}
		}
		elseif ($folder = Request::getVar('folder', ''))
		{
			$combined[] = array('folder' => urldecode($folder));
		}

		return $combined;
	}
}
