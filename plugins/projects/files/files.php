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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Include some helpers
include_once( PATH_CORE . DS . 'components' . DS .'com_projects'
	. DS . 'helpers' . DS . 'githelper.php' );
include_once( PATH_CORE . DS . 'components' . DS .'com_projects'
	. DS . 'helpers' . DS . 'compiler.php' );

// Get repo model
require_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
	. DS . 'models' . DS . 'repo.php');

/**
 * Projects Files plugin
 */
class plgProjectsFiles extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Store redirect URL
	 *
	 * @var	   string
	 */
	protected $_referer = NULL;

	/**
	 * Store output message
	 *
	 * @var	   array
	 */
	protected $_message = NULL;

	/**
	 * Sync queue
	 *
	 * @var	   array
	 */
	protected $_queue = NULL;

	/**
	 * Repository path
	 *
	 * @var	   array
	 */
	protected $_path = NULL;

	/**
	 * Repository name (used to build path)
	 *
	 * @var	   array
	 */
	protected $_case = 'files';

	/**
	 * Url for project
	 *
	 * @var	   array
	 */
	protected $_route = NULL;

	/**
	 * Component name
	 *
	 * @var  string
	 */
	protected $_option = 'com_projects';

	/**
	 * Store internal message
	 *
	 * @var	   array
	 */
	protected $_msg = NULL;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @return     array   Plugin name and title
	 */
	public function &onProjectAreas($alias = NULL)
	{
		$area = array(
			'name'    => 'files',
			'title'   => Lang::txt('COM_PROJECTS_TAB_FILES'),
			'submenu' => 'Assets',
			'show'    => true
		);

		return $area;
	}

	/**
	 * Event call to return count of items
	 *
	 * @param      object  $model		Project
	 * @return     array   integer
	 */
	public function &onProjectCount( $model )
	{
		$repo  = new \Components\Projects\Models\Repo ($model, 'local');
		$counts['files'] = $repo->count();

		return $counts;
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param      object  $model           Project model
	 * @param      string  $action			Plugin task
	 * @param      string  $areas  			Plugins to return data
	 * @return     array   Return array of html
	 */
	public function onProject ( $model, $action = '', $areas = null, $params = array())
	{
		$returnhtml = true;

		$arr = array(
			'html'     =>'',
			'metadata' =>'',
			'msg'      =>'',
			'referer'  =>''
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
			$repoName   = !empty($params['repo']) ? $params['repo'] : Request::getVar( 'repo', 'local');
			$this->repo = new \Components\Projects\Models\Repo ($this->model, $repoName);

			$this->_publishing = Plugin::isEnabled('projects', 'publications') ? 1 : 0;
			$this->_database   = \JFactory::getDBO();
			$this->_uid 	   = User::get('id');
			$this->_task       = $action ? $action : Request::getVar('action', 'browse');
			$this->subdir 	   = trim(urldecode(Request::getVar('subdir', '')), DS);
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
					$this->repo = new \Components\Projects\Models\Repo ($this->model, 'local');
				}
				$this->_path = $this->repo->get('path');
			}

			// Remote connections
			$this->_connect		= NULL;
			$this->_rServices	= array();
			$this->_rSync		= array('service'	=> NULL,
										'status' 	=> NULL,
										'message' 	=> NULL,
										'debug' 	=> NULL,
										'error' 	=> NULL,
										'output' 	=> NULL,
										'auto'		=> NULL
										);

			// Hubzero library classes
			$this->fileSystem = new \Hubzero\Filesystem\Filesystem();

			// Set routing
			$this->_route = 'index.php?option=' . $this->_option . '&alias=' . $this->model->get('alias');

			//  Establish connection to external services
			if ($this->model->exists() && !$this->model->isProvisioned())
			{
				$this->_connect = new \Components\Projects\Helpers\Connect(
					$this->model,
					$this->_uid,
					date_default_timezone_get()
				);
				$this->_logPath = \Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias'), 'logs');

				// Get services the project is connected to
				$this->_rServices = $this->_connect->getActive();

				// Sync service is Google
				if (!empty($this->_rServices) && $this->repo->isLocal())
				{
					$this->_remoteService = 'google';
				}
			}

			// File actions
			switch ($this->_task)
			{
				// File browser
				case 'browse':
				default:
					$arr['html'] 	= $this->_browse();
					break;

				// Basic file management
				case 'upload':
					$arr['html'] 	= $this->_upload();
					break;

				case 'save':
				case 'saveprov':
					$arr['html'] 	= $this->_save();
					break;

				case 'delete':
				case 'removeit':
					$arr['html'] 	= $this->_delete();
					break;

				case 'move':
				case 'moveit':
					$arr['html'] 	= $this->_move();
					break;

				case 'rename':
				case 'renameit':
					$arr['html'] 	= $this->_rename();
					break;

				case 'share':
				case 'shareit':
					$arr['html'] 	= $this->_share();
					break;

				// History
				case 'history':
					$arr['html'] 	= $this->_history();
					break;
				case 'diff':
					$arr['html'] 	= $this->_diff();
					break;

				// Serve/preview
				case 'compile':
					$arr['html'] 	= $this->_compile();
					break;
				case 'serve':
					$arr['html'] 	= $this->serve();
					break;
				case 'download':
				case 'open':
					$arr['html'] 	= $this->_download();
					break;

				// Manage directory
				case 'newdir':
					$arr['html'] 	= $this->_newDir();
					break;
				case 'deletedir':
					$arr['html'] 	= $this->_deleteDir();
					break;
				case 'savedir':
					$arr['html'] 	= $this->_saveDir();
					break;

				// Manage deleted
				case 'trash':
					$arr['html'] 	= $this->_showTrash();
					break;
				case 'restore':
					$arr['html'] 	= $this->_restore();
					break;

				// Disk space management
				case 'diskspace':
					$arr['html'] 	= $this->diskspace(
						$this->model, $this->repo->get('name'), $this->_uid
					);
					break;
				case 'optimize':
				case 'advoptimize':
					$arr['html'] 	= $this->optimize(
						$this->model, $this->repo->get('name'));
					break;

				// Publishing selectors
				case 'select':
				case 'filter':
					$arr['html'] 	= $this->_select();
					break;
				case 'browser':
					$arr['html'] 	= $this->_browser();
					break;

				// Connections
				case 'connect':
				case 'disconnect':
					$arr['html'] 	= $this->_connect();
					break;

				// Sync with remote
				case 'sync':
					$arr['html'] 	= $this->_iniSync();
					break;
				case 'sync_status':
					$arr['html'] 	= $this->syncStatus();
					break;
				case 'sync_error':
					$arr['html'] 	= $this->_syncError();
					break;
			}
		}

		$arr['referer'] = $this->_referer;
		$arr['msg'] = $this->_message;

		// Return data
		return $arr;

	}

	//----------------------------------------
	// Views and Processors
	//----------------------------------------
	/**
	 * View of project files
	 *
	 * @return     string
	 */
	protected function _browse($sync = 0)
	{
		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder' 	=> 'projects',
				'element' 	=> 'files',
				'name' 		=> 'browse',
				'layout' 	=> 'display'
			)
		);

		// Does subdirectory exist?
		if (!$this->repo->dirExists($this->subdir))
		{
			$this->subdir = '';
		}

		// Load member params
		$member = $this->model->member(true);
		$view->oparams = new \JParameter($member->params);

		// Sync active?
		$remotes = array();
		$view->connect       = $this->_connect;
		$view->services      = $this->_rServices;
		$view->connections	 = $this->_connect->getConnections($this->_uid);

		// Get stored remote connections
		if (!empty($this->_remoteService))
		{
			$objRFile = new \Components\Projects\Tables\RemoteFile ($this->_database);
			$remotes  = $objRFile->getRemoteFiles(
				$this->model->get('id'),
				$this->_remoteService,
				$this->subdir
			);

			$view->sync 		 = $sync == 2 ? 0 : $this->model->params->get('google_sync_queue', 0);
			$view->rSync 		 = $this->_rSync;
			$view->sharing 		 = 1;
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
			'getParents'           => true, // show folders
			'getChildren'          => false, // do not recurse
			'showUntracked'        => true,
			'getPubConnections'    => false,
			'remoteConnections'    => $remotes,
		);

		// Retrieve items
		$view->items = $this->repo->filelist($view->params);

		// Do we have any changes to report?
		$this->onAfterUpdate();

		$view->publishing	= false; // do not show publishing info
		$view->title		= $this->_area['title'];
		$view->url 			= Route::url($this->_route . '&active=files');
		$view->option 		= $this->_option;
		$view->subdir 		= $this->subdir;
		$view->model 		= $this->model;
		$view->repo    		= $this->repo;
		$view->uid 			= $this->_uid;
		$view->fileparams 	= $this->params;
		$view->database		= $this->_database;

		return $view->loadTemplate();
	}

	/**
	 * Browser within publications (Curation)
	 *
	 * @return     string
	 */
	protected function _select()
	{
		// Incoming
		$props  = Request::getVar( 'p', '' );
		$ajax   = Request::getInt( 'ajax', 0 );
		$pid    = Request::getInt( 'pid', 0 );
		$vid    = Request::getInt( 'vid', 0 );
		$filter = urldecode(Request::getVar( 'filter', '' ));

		// Parse props for curation
		$parts   = explode('-', $props);
		$block   = (isset($parts[0]) && in_array($parts[0], array('content', 'extras')))
					? $parts[0] : 'content';
		$step    = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
		$element = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 1;

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'files',
				'name'		=>'selector'
			)
		);

		// Load publication classes
		$objP  			= new \Components\Publications\Tables\Publication( $this->_database );
		$view->version 	= new \Components\Publications\Tables\Version( $this->_database );

		// Load publication version
		$view->version->load($vid);
		if (!$view->version->id)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SELECTOR_ERROR_NO_PUBID'));
		}

		// Get publication
		$view->publication = $objP->getPublication($view->version->publication_id,
			$view->version->version_number, $this->model->get('id'));

		if (!$view->publication)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SELECTOR_ERROR_NO_PUBID'));
		}

		// On error
		if ($this->getError())
		{
			// Output error
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'	=>'projects',
					'element'	=>'files',
					'name'		=>'error'
				)
			);

			$view->title  = '';
			$view->option = $this->_option;
			$view->setError( $this->getError() );
			return $view->loadTemplate();
		}

		// Load master type
		$mt   				= new \Components\Publications\Tables\MasterType( $this->_database );
		$view->publication->_type   	= $mt->getType($view->publication->base);
		$view->publication->_project 	= $this->model;

		// Get attachments
		$pContent = new \Components\Publications\Tables\Attachment( $this->_database );
		$view->publication->_attachments = $pContent->sortAttachments ( $vid );

		// Get curation model
		$view->publication->_curationModel = new \Components\Publications\Models\Curation(
			$view->publication->_type->curation
		);

		// Make sure block exists, else use default
		$view->publication->_curationModel->setBlock( $block, $step );

		// Set pub assoc and load curation
		$view->publication->_curationModel->setPubAssoc($view->publication);

		// Get file list
		$view->items = NULL;
		if ($this->model->get('id'))
		{
			// Set params
			$params = array(
				'filter'               => Request::getVar('filter', ''),
				'limit'                => Request::getInt('limit', 0),
				'start'                => Request::getInt('limitstart', 0),
				'sortby'               => 'localpath', // important for selector!
				'showFullMetadata'     => false,
				'getParents'           => true, // show folders
				'getChildren'          => true, // look inside directories
			);

			// Retrieve items
			$view->items = $this->repo->filelist($params);
		}

		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->model 		= $this->model;
		$view->repo    		= $this->repo;
		$view->uid 			= $this->_uid;
		$view->ajax			= $ajax;
		$view->task			= $this->_task;
		$view->element		= $element;
		$view->block		= $block;
		$view->step 		= $step;
		$view->props		= $props;
		$view->filter		= $filter;
		$view->sizelimit 	= $this->params->get('maxUpload', '104857600');

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Browser for within publications (Old flow)
	 *
	 * @return     string
	 */
	protected function _browser()
	{
		// Incoming
		$content 	= Request::getVar('content', 'files');
		$ajax 		= Request::getInt('ajax', 0);
		$primary 	= Request::getInt('primary', 1);
		$images 	= Request::getInt('images', 0);
		$pid 		= Request::getInt('pid', 0);

		if (!$ajax)
		{
			return false;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'files',
				'name'		=>'browser'
			)
		);

		// Get file list
		if (!$this->model->exists())
		{
			$view->files = $this->_getMemberFiles();
		}
		elseif ($content == 'files')
		{
			// Set query params
			$params = array(
				'limit'                => Request::getInt('limit', 0),
				'start'                => Request::getInt('limitstart', 0),
				'sortby'               => Request::getVar('sortby', 'localpath'),
				'sortdir'              => Request::getVar('sortdir', 'ASC'),
				'showFullMetadata'     => false,
				'getParents'           => false, // show folders
				'getChildren'          => true,
			);

			$view->files = $this->repo->call('filelist', $params);
		}
		else
		{
			$this->setError( Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH') );
			return;
		}

		// Does the publication exist?
		$versionid 	= Request::getInt('versionid', 0);
		$pContent 	= new \Components\Publications\Tables\Attachment( $this->_database );
		$role    	= $primary ? '1': '0';
		$other 		= $primary ? '0' : '1';

		if (!$images)
		{
			$view->attachments = $pContent->getAttachments($versionid, $filters = array('role' => $role));
		}
		else
		{
			// Common extensions (for gallery)
			$pubparams 	= Plugin::params( 'projects', 'publications' );

			$view->image_ext = \Components\Projects\Helpers\Html::getParamArray(
								$pubparams->get('image_types', 'bmp, jpeg, jpg, png, gif' ));
			$view->video_ext = \Components\Projects\Helpers\Html::getParamArray(
								$pubparams->get('video_types', 'avi, mpeg, mov, wmv' ));

			$other = 1;

			// Get current screenshots
			$pScreenshot = new \Components\Publications\Tables\Screenshot( $this->_database );
			$view->shots = $pScreenshot->getScreenshots($versionid);
		}

		$view->exclude = $pContent->getAttachments(
			$versionid,
			$filters = array('role' => $other, 'select' => 'a.path')
		);

		if ($view->exclude && !$images)
		{
			$excude_files = array();
			foreach ($view->exclude as $exclude)
			{
				$excude_files[] = str_replace($this->_path. DS, '', trim($exclude->path));
			}
			$view->exclude = $excude_files;
		}

		$view->primary 		= $primary;
		$view->images 		= $images;
		$view->total 		= 0;
		$view->params 		= $this->model->params;
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->model 		= $this->model;
		$view->repo    		= $this->repo;
		$view->uid 			= $this->_uid;
		$view->subdir 		= $this->subdir;
		$view->base 		= $content;
		$view->config 		= $this->model->config();
		$view->pid 			= $pid;
		$view->title		= $this->_area['title'];

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Upload view
	 *
	 * @return     void, redirect
	 */
	protected function _upload()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Incoming
		$ajax 	= Request::getInt('ajax', 0);

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'files',
				'name'    =>'upload'
			)
		);

		// Get used space
		$dirsize  = $this->repo->call('getDiskUsage',
			$params = array(
				'history' => $this->params->get('disk_usage')
			)
		);

		// Get quota
		$quota 		 = $this->model->params->get('quota');
		$view->quota = $quota
			? $quota
			: \Components\Projects\Helpers\Html::convertSize(floatval($this->model->config()->get('defaultQuota', '1')), 'GB', 'b');
		$view->url = Route::url($this->_route . '&active=files');

		$view->unused 		= $view->quota - $dirsize;
		$view->option 		= $this->_option;
		$view->model 		= $this->model;
		$view->repo			= $this->repo;
		$view->subdir 		= $this->subdir;
		$view->ajax			= $ajax;
		$view->config 		= $this->model->config();
		$view->sizelimit 	= $this->params->get('maxUpload', '104857600');
		$view->title		= $this->_area['title'];
		$view->params 		= $this->params;

		return $view->loadTemplate();
	}

	/**
	 * Upload file(s) and add to repository
	 *
	 * @return     void, redirect
	 */
	protected function _save()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
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
			$url  = $this->_route . '&active=files';
			$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
			$url .= $this->subdir ? '?subdir=' . urlencode($this->subdir) : '';
			$url  = Route::url($url);
		}

		// Get quota
		$quota = $this->model->exists() ? $this->model->params->get('quota') : \Components\Projects\Helpers\Html::convertSize(floatval($this->model->config()->get('defaultQuota', '1')), 'GB', 'b');

		// Get used space
		$dirsize = $this->repo->exists() ? $this->repo->call('getDiskUsage', $params = array('history' => $this->params->get('disk_usage'))) : 0;

		// Set params
		$params = array(
			'subdir'      => $this->subdir,
			'dataPath'    => Request::getVar('data_path', ''),
			'expand'      => Request::getInt('expand_zip', 0),
			'ajaxUpload'  => $ajaxUpload,
			'path'        => $this->_path,
			'quota'       => $quota,
			'dirsize'     => $dirsize,
			'sizelimit'   => $this->params->get('maxUpload', '104857600')
		);

		// Upload file
		$results = $this->repo->insert($params);

		// Register changes for active projects
		if (!empty($results) && !$this->model->isProvisioned())
		{
			foreach ($results as $updateType => $files)
			{
				foreach ($files as $file)
				{
					$this->registerUpdate($updateType, $file);
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
								'success'   => 1,
								'file'      => $file,
								'isNew'		=> $updateType == 'uploaded' ? true : false
								)
							);
						}
					}
				}
			}

			// Force sync
			if ($this->repo->isLocal())
			{
				$this->model->saveParam('google_sync_queue', 1);
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
				$this->_message = array('message' => $this->repo->getError(), 'type' => 'error');
				$this->_referer = $url;
				return;
			}
		}

		// Return status in JSON
		if ($json)
		{
			// After upload actions
			$this->onAfterUpdate();

			return json_encode(array(
				'error'     => $this->getError(),
				'success'	=> $this->_msg
			));
		}

		// Go back
		$this->_referer = $url;

		// Set success message
		if (isset($this->_msg) && $this->_msg)
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}

		return;
	}

	/**
	 * New directory form
	 *
	 * @return     void, redirect
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

		$view->option 		= $this->_option;
		$view->model 		= $this->model;
		$view->repo			= $this->repo;
		$view->uid 			= $this->_uid;
		$view->ajax 		= 1;
		$view->subdir 		= $this->subdir;
		$view->url			= Route::url($this->_route . '&active=files');
		$view->path 		= $this->_path;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Save new directory
	 *
	 * @return     void, redirect
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
			'subdir'  => $this->subdir,
			'newDir'  => trim(Request::getVar('newdir', '')),
			'path'    => $this->_path
		);

		// Create
		$success = $this->repo->makeDirectory($params);
		if ($success)
		{
			$this->_message = array('message' => Lang::txt('PLG_PROJECTS_FILES_CREATED_DIRECTORY'), 'type' => 'success');
			// Force sync
			if ($this->repo->isLocal())
			{
				$this->model->saveParam('google_sync_queue', 1);
			}
		}
		elseif ($this->repo->getError())
		{
			$this->_message = array('message' => $this->repo->getError(), 'type' => 'error');
		}

		// Redirect to file list
		$url  = $this->_route . '&active=files';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
		$this->_referer = Route::url($url);
		return;
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
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Set params
		$params = array(
			'subdir'  => $this->subdir,
			'dir'     => trim(urldecode(Request::getVar('dir', '')), DS),
			'path'    => $this->_path
		);

		// Create
		$success = $this->repo->deleteDirectory($params);
		if ($success)
		{
			$this->_message = array('message' => Lang::txt('PLG_PROJECTS_FILES_DELETED_DIRECTORY'), 'type' => 'success');
			// Force sync
			if ($this->repo->isLocal())
			{
				$this->model->saveParam('google_sync_queue', 1);
			}
		}
		elseif ($this->repo->getError())
		{
			$this->_message = array('message' => $this->repo->getError(), 'type' => 'error');
		}

		// Redirect to file list
		$url  = $this->_route . '&active=files';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
		$this->_referer = Route::url($url);
		return;
	}

	/**
	 * Delete items
	 *
	 * @return     void, redirect
	 */
	protected function _delete()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
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

			$view->items 		= array();
			$view->services		= $this->_rServices;
			$view->connections	= $this->_connect->getConnections();
			$view->connect		= $this->_connect;
			$view->database 	= $this->_database;
			$view->option 		= $this->_option;
			$view->model 		= $this->model;
			$view->repo    		= $this->repo;
			$view->uid 			= $this->_uid;
			$view->ajax 		= Request::getInt('ajax', 0);
			$view->subdir 		= $this->subdir;
			$view->url			= Route::url($this->_route . '&active=files');
			$view->path 		= $this->_path;
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
		$url  = $this->_route . '&active=files';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
		$this->_referer = Route::url($url);
		return;
	}

	/**
	 * Rename
	 *
	 * @return     void, redirect
	 */
	protected function _rename()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
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

			$view->option 		= $this->_option;
			$view->model 		= $this->model;
			$view->repo			= $this->repo;
			$view->uid 			= $this->_uid;
			$view->ajax 		= 1;
			$view->subdir 		= $this->subdir;
			$view->url			= Route::url($this->_route . '&active=files');
			$view->path 		= $this->_path;
			return $view->loadTemplate();
		}

		// Set params
		$params = array(
			'subdir'  => $this->subdir,
			'path'    => $this->_path,
			'from'    => Request::getVar( 'oldname', ''),
			'to'      => Request::getVar( 'newname', ''),
			'type'    => Request::getVar( 'type', 'file')
		);

		// Create
		$success = $this->repo->rename($params);
		if ($success)
		{
			$this->_message = array('message' => Lang::txt('PLG_PROJECTS_FILES_RENAMED_SUCCESS'), 'type' => 'success');
			// Force sync
			if ($this->repo->isLocal())
			{
				$this->model->saveParam('google_sync_queue', 1);
			}
		}
		elseif ($this->repo->getError())
		{
			$this->_message = array('message' => $this->repo->getError(), 'type' => 'error');
		}

		// Redirect to file list
		$url  = $this->_route . '&active=files';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
		$this->_referer = Route::url($url);
		return;
	}

	/**
	 * Move file(s)
	 *
	 * @return     void, redirect
	 */
	protected function _move()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
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
				'subdir'               => NULL,
				'sortby'               => 'localpath', // important for selector!
				'showFullMetadata'     => false,
				'getParents'           => true, // show folders
				'getChildren'          => true, // look inside directories
			);

			$view->list			= $this->repo->filelist($listParams);
			$view->path 		= $this->_path;
			$view->items 		= array();
			$view->database 	= $this->_database;
			$view->services		= $this->_rServices;
			$view->connections	= $this->_connect->getConnections();
			$view->connect		= $this->_connect;
			$view->option 		= $this->_option;
			$view->model 		= $this->model;
			$view->repo    		= $this->repo;
			$view->uid 			= $this->_uid;
			$view->ajax 		= Request::getInt('ajax', 0);
			$view->subdir 		= $this->subdir;
			$view->url			= Route::url($this->_route . '&active=files');
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
			$this->_msg = Lang::txt('PLG_PROJECTS_FILES_MOVED'). ' '
				. $moved . ' ' . Lang::txt('PLG_PROJECTS_FILES_S');
			$this->_message = array('message' => $this->_msg, 'type' => 'success');

			// Force sync
			if ($this->repo->isLocal())
			{
				$this->model->saveParam('google_sync_queue', 1);
			}
		}
		else
		{
			$this->_message = array('message' => Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_NEW_FILE_LOCATION'), 'type' => 'error');
		}

		// Redirect to file list
		$url  = $this->_route . '&active=files';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
		$this->_referer = Route::url($url);
		return;
	}

	/**
	 * Show file history
	 *
	 * @return     void, redirect
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
				'folder'	=> 'projects',
				'element'	=> 'files',
				'name'		=> 'history',
				'layout' 	=> 'advanced'
			)
		);

		// Redirect to file list
		$view->url = Route::url($this->_route . '&active=files');

		// Collective vars
		$versions 		= array();
		$timestamps 	= array();

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
			$view->setError( $this->getError() );
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

			$hash 		= $v['remote'] ? NULL : $v['hash'];

			$versions[$i]['preview'] = $revision->getPreview($this->model, $hash, 'url');
			$i++;
		}

		$view->versions     = $versions;
		$view->path 		= $this->_path;
		$view->option 		= $this->_option;
		$view->model 		= $this->model;
		$view->repo    		= $this->repo;
		$view->uid 			= $this->_uid;
		$view->ajax			= Request::getInt( 'ajax');
		$view->title		= $this->_area['title'];
		$view->subdir 		= $this->subdir;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		$view->msg = isset($this->_msg) ? $this->_msg : '';
		return $view->loadTemplate();
	}

	/**
	 * Show revision diffs
	 *
	 * @return     void, redirect
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

		$params['rev1']      = urldecode(Request::getVar( 'old', ''));
		$params['rev2']      = urldecode(Request::getVar( 'new', ''));
		$params['fullDiff']  = Request::getInt( 'full', 0);
		$params['mode']      = urldecode(Request::getVar( 'mode', 'side-by-side'));

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=> 'projects',
				'element'	=> 'files',
				'name'		=> 'history',
				'layout' 	=> 'diff'
			)
		);

		// Run diff
		$view->diff = $this->repo->diff($params);

		$view->url 	        = Route::url($this->_route . '&active=files');
		$view->config		= $this->model->config();
		$view->file 		= $params['file'];
		$view->option 		= $this->_option;
		$view->model 		= $this->model;
		$view->repo    		= $this->repo;
		$view->uid 			= $this->_uid;
		$view->title		= $this->_area['title'];
		$view->subdir 		= $this->subdir;
		$view->ajax			= 0;
		$view->params		= $params;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		$view->msg = isset($this->_msg) ? $this->_msg : '';
		return $view->loadTemplate();

	}

	/**
	 * Serve file (usually via public link)
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

		if (!isset($data->file) || !$projectid)
		{
			return false;
		}

		$file 		= $data->file;
		$disp 		= isset($data->disp) ? $data->disp : 'inline';
		$limited 	= isset($data->limited) ? $data->limited : 0;
		$hash 		= isset($data->hash) ? $data->hash : 0;
		$repoName	= isset($data->repo) ? $data->repo : 'local';

		// Instantiate a project
		$model = new \Components\Projects\Models\Project($projectid);

		if (!$model->exists() || ($limited == 1 && !$model->access('member')))
		{
			// Throw error
			throw new Exception(Lang::txt('COM_PROJECTS_ERROR_ACTION_NOT_AUTHORIZED'), 403);
			return;
		}

		// Load repo
		$repo = new \Components\Projects\Models\Repo ($model, $repoName);

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
			throw new Exception(Lang::txt('COM_PROJECTS_FILE_NOT_FOUND'), 404);
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($serve);
		$xserver->disposition($disp);
		$xserver->acceptranges(false); // @TODO fix byte range support
		$xserver->saveas(basename($file));

		$result = $xserver->serve();

		if ($deleteTemp)
		{
			// Delete downloaded temp file
			$fileSystem = new \Hubzero\Filesystem\Filesystem();
			$fileSystem->delete($serve);
		}

		if (!$result)
		{
			// Should only get here on error
			throw new Exception(Lang::txt('PLG_PROJECTS_FILES_SERVER_ERROR'), 404);
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
	 * @return     void, redirect
	 */
	protected function _restore()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Incoming
		$item = urldecode(Request::getVar( 'asset', ''));
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
			$this->_message = array('message' => $error, 'type' => 'error');
		}

		// Redirect to file list
		$url  = $this->_route . '&active=files';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
		$this->_referer = Route::url($url);
		return;
	}

	/**
	 * Download file(s)
	 *
	 * @return     void, redirect
	 */
	protected function _download()
	{
		// Incoming
		$render    = Request::getVar('render', 'download');
		$hash      = Request::getVar('hash', '');

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
			throw new Exception(Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND'), 404 );
			return;
		}

		// File preview?
		if ($render == 'preview')
		{
			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'	=>'projects',
					'element'	=>'files',
					'name'		=>'preview'
				)
			);

			$view->file			= isset($collector[0]) ? $collector[0] : NULL;

			// Get last revision
			if (!$view->file->get('converted') && !$hash)
			{
				$params['file'] = $view->file;
				$hash = $this->repo->getLastRevision($params);
				$view->file->set('hash', $hash);
			}
			$view->option 		= $this->_option;
			$view->model		= $this->model;

			if (!($view->file instanceof \Components\Projects\Models\File))
			{
				$view->setError( Lang::txt('PLG_PROJECTS_FILES_ERROR_FILE_INFO_NOT_FOUND') );
			}
			return $view->loadTemplate();
		}

		// Other rendering?
		if ($render == 'thumb' || $render == 'inline' || $render == 'medium')
		{
			$file = isset($collector[0]) ? $collector[0] : NULL;
			if (!($file instanceof \Components\Projects\Models\File))
			{
				throw new Exception(Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND'), 404 );
				return;
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
				$xserver = new \Hubzero\Content\Server();
				$xserver->filename($image);
				$xserver->serve_inline($image);
				exit;
			}
		}

		// File download
		if (count($items) > 1)
		{
			$archive = $this->_archiveFiles($items, $this->_path, $this->subdir);

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
				throw new Exception(Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND'), 404 );
				return;
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
					$this->_message = array('message' => Lang::txt('PLG_PROJECTS_FILES_REMOTE_PLEASE_CONNECT'),
						'type' => 'success');
					$url  = Route::url('index.php?option=' . $this->_option
						 . '&alias=' . $this->model->get('alias') . '&active=files');
					$url .= '/?action=connect';
					$this->_referer = $url;
					return;
				}

				// Load remote resource
				$this->_connect->setUser($this->model->get('owned_by_user'));
				$resource = $this->_connect->loadRemoteResource($this->_remoteService,
					$this->model->get('owned_by_user'), $file->get('remoteId'));

				$openLink = $resource && isset($resource['alternateLink']) ? $resource['alternateLink'] : '';

				if (!$openLink)
				{
					// Throw error
					throw new Exception(Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND') . ' ' . $file->get('name'), 404 );
					return;
				}
				$this->_referer = $openLink;
				return;
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
				$tempPath = 'temp-' . \Components\Projects\Helpers\Html::generateCode (4 ,4 ,0 ,1 ,0 ) . $serveas;
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
				throw new Exception(Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND'), 404);
				return;
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
				$xserver = new \Hubzero\Content\Server();
				$xserver->filename($downloadPath);
				$xserver->disposition('attachment');
				$xserver->acceptranges(false);
				$xserver->saveas($serveas);
				$result = $xserver->serve_attachment($downloadPath, $serveas, false);

				if (!$result)
				{
					// Should only get here on error
					throw new Exception(Lang::txt('PLG_PROJECTS_FILES_SERVER_ERROR'), 404);
				}
				else
				{
					exit;
				}
			}
		}

		// Pass success or error message
		if ($this->getError())
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg)
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}

		// Redirect to file list
		$url  = $this->_route . '&active=files';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
		$this->_referer = Route::url($url);
		return;
	}

	/**
	 * Compile PDF/image preview for any kind of file
	 *
	 *
	 * @return     array or false
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
		$commit     = Request::getInt( 'commit', 0 );
		$download   = Request::getInt( 'download', 0 );

		// Check that we have compile enabled
		if (!$this->params->get('latex'))
		{
			$this->setError( Lang::txt('PLG_PROJECTS_FILES_COMPILE_NOTALLOWED') );
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
			return;
		}

		// Path for storing temp previews
		$imagePath = trim($this->model->config()->get('imagepath', '/site/projects'), DS);
		$outputDir = DS . $imagePath . DS . strtolower($this->model->get('alias')) . DS . 'compiled';

		// Make sure output dir exists
		if (!is_dir( PATH_APP . DS . $outputDir ))
		{
			if (!$this->fileSystem->makeDirectory( PATH_APP . DS . $outputDir ))
			{
				$this->setError( Lang::txt('PLG_PROJECTS_FILES_UNABLE_TO_CREATE_UPLOAD_PATH') );
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
		$view->url	   = Route::url($this->_route . '&active=files');
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
			$data = $this->_connect->sendHttpRequest(
				$this->_remoteService,
				$this->model->get('owned_by_user'),
				$url
			);
		}
		elseif ($file->exists())
		{
			$data = $file->contents();
		}
		else
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_COMPILE_NO_DATA'));
		}

		// LaTeX file?
		if ($tex && !empty($data))
		{
			// Clean up data from Windows characters - important!
			$data = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $data);

			// Compile and get path to PDF
			$contentFile = $compiler->compileTex(
				$file->get('fullPath'),
				$data,
				$texPath,
				PATH_APP . $outputDir, 1, $tempBase
			);

			// Read log (to show in case of error)
			$logFile = $tempBase . '.log';
			if (file_exists(PATH_APP . $outputDir . DS . $logFile ))
			{
				$log = $this->_readFile(PATH_APP . $outputDir . DS . $logFile, '', true);
			}

			if (!$contentFile)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_COMPILE_TEX_FAILED'));
			}
		}
		elseif ($file->get('converted') && !empty($data))
		{
			$tempBase = \Components\Projects\Helpers\Google::getImportFilename($file->get('name'), $cExt);

			// Write content to temp file
			$this->_connect->fetchFile($data, $tempBase, PATH_APP . $outputDir);
			$contentFile = $tempBase;
		}
		// Local file
		elseif (!$this->getError() && !empty($data))
		{
			// Make sure we can handle preview of this type of file
			if ($file->get('ext') == 'pdf' || $file->isImage() || !$file->isBinary())
			{
				$this->fileSystem->copy($file->get('fullPath'), PATH_APP . $outputDir . DS . $tempBase);
				$contentFile = $tempBase;
			}
		}

		$url  = $this->_route . '&active=files';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Parse output
		if (!empty($contentFile) && file_exists(PATH_APP . $outputDir . DS . $contentFile))
		{
			// Get compiled content mimetype
			$helper = new \Hubzero\Content\Mimetypes();
			$mTypeParts = explode(';', $helper->getMimeType(PATH_APP . $outputDir . DS . $contentFile));
			$cType = $mTypeParts[0];

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
				$xserver = new \Hubzero\Content\Server();
				$xserver->filename(PATH_APP . $outputDir . DS . $contentFile);
				$xserver->disposition('attachment');
				$xserver->acceptranges(false);
				$xserver->saveas($pdfName);
				$result = $xserver->serve_attachment(PATH_APP . $outputDir . DS . $contentFile, $pdfName, false);

				if (!$result)
				{
					// Should only get here on error
					throw new Exception(Lang::txt('PLG_PROJECTS_FILES_SERVER_ERROR'), 404);
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
				$where 	 = $this->subdir ? $this->subdir. DS . $pdfName : $pdfName;

				if ($this->fileSystem->copy(PATH_APP . $outputDir . DS . $contentFile, $this->_path . DS . $where))
				{
					// Checkin into repo
					$params = array('subdir' => $this->subdir);
					$params['file'] = $this->repo->getMetadata($pdfName, 'file', $params);
					$this->repo->call('checkin', $params);

					if ($this->repo->isLocal())
					{
						$this->model->saveParam('google_sync_queue', 1);
					}

					$this->_message = array(
						'message' => Lang::txt('PLG_PROJECTS_FILES_SUCCESS_COMPILED'),
						'type'    => 'success'
					);

					// Redirect to file list
					$this->_referer = Route::url($url);
					return;
				}
			}

			// Generate preview image for browsers that cannot embed pdf
			if ($cType == 'application/pdf')
			{
				// GS path
				$gspath = trim($this->params->get('gspath'), DS);
				if ($gspath && file_exists(DS . $gspath . DS . 'gs' ))
				{
					$gspath = DS . $gspath . DS;

					$pdfName 	= $tex ? str_replace('temp__', '', basename($contentFile)) : basename($contentFile);
					$pdfPath 	= PATH_APP . $outputDir . DS . $contentFile;
					$exportPath = PATH_APP . $outputDir . DS . $tempBase . '%d.jpg';

					exec($gspath . "gs -dNOPAUSE -sDEVICE=jpeg -r300 -dFirstPage=1 -dLastPage=1 -sOutputFile=$exportPath $pdfPath 2>&1", $out );

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

		$view->file 		= $file;
		$view->outputDir	= $outputDir;
		$view->log			= $log;
		$view->embed		= $contentFile;
		$view->data			= $data;
		$view->cType		= $cType;
		$view->subdir 		= $this->subdir;
		$view->option 		= $this->_option;
		$view->image		= !empty($image) ? $image : NULL;
		$view->model		= $this->model;
		$view->repo    		= $this->repo;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}

		return $view->loadTemplate();
	}

	/**
	 * Send file back or from to remote service for remote editing
	 * Local repo only
	 *
	 * @return     void, redirect
	 */
	protected function _share()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Incoming
		$converted  = Request::getInt('converted', 0);
		$service 	= Request::getVar('service', 'google');
		$sync		= false;
		$shared     = 0;

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
			$type 	= key($items[0]);
			$item 	= $items[0][$type];

			// Build metadata object
			$file = $this->repo->getMetadata($item, $type, $params);
		}

		// Build return url
		$url  = $this->_route . '&active=files';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

		// Check user is connected
		if (!empty($this->_remoteService))
		{
			$connected = $this->_connect->getStoredParam($this->_remoteService . '_token', $this->_uid);
			if (!$connected)
			{
				// Redirect to connect screen
				$this->_message = array('message' => Lang::txt('PLG_PROJECTS_FILES_REMOTE_PLEASE_CONNECT'),
					'type' => 'success');
				$url  = Route::url('index.php?option=' . $this->_option
					 . '&alias=' . $this->model->get('alias') . '&active=files');
				$url .= '/?action=connect';
				$this->_referer = $url;
				return;
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

			$view->option 		= $this->_option;
			$view->model 		= $this->model;
			$view->repo    		= $this->repo;
			$view->uid 			= $this->_uid;
			$view->subdir 		= $this->subdir;
			$view->path 		= $this->_path;

			if ($this->getError())
			{
				$view->setError( $this->getError() );
			}
			else
			{
				$view->file 		= !empty($file) ? $file : NULL;
				$view->connect		= $this->_connect;
				$view->service		= $this->_remoteService;
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
			$view->setError( $this->getError() );
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
						$file->get('localPath'), $this->repo->get('path'), $importExt ))
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
					$this->_msg = Lang::txt('PLG_PROJECTS_FILES_UNSHARE_SUCCESS') . ' ' . $title;

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
							$this->_msg = Lang::txt('PLG_PROJECTS_FILES_SHARE_SUCCESS');

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
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg)
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}

		// Force sync
		if ($sync && $this->repo->isLocal())
		{
			$this->model->saveParam('google_sync_queue', 1);
		}

		// Redirect to file list
		$this->_referer = Route::url($url);
		return;
	}

	/**
	 * Optimize repository
	 *
	 * @param      object  	$model Project model
	 * @param      string  	$repoName
	 *
	 * @return     string
	 */
	public function optimize($model, $repoName = 'local')
	{
		if (!isset($this->repo))
		{
			$this->repo = new \Components\Projects\Models\Repo ($model, $repoName);
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
	 * @param      object  	$model Project model
	 * @param      string  	$repoName
	 * @param      integer  $by
	 *
	 * @return     string
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
			$this->repo = new \Components\Projects\Models\Repo ($model, $repoName);
		}

		$url = Route::url('index.php?option=' . $this->_option
			. '&alias=' . $model->get('alias') . '&active=files&action=diskspace');

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
			$view->totalspace = $this->repo->call('getDiskUsage',
				$params = array('working' => false, 'history' => true)
			);
			$view->dirsize = $view->totalspace;
		}

		// Get publication usage
		if (Plugin::isEnabled('projects', 'publications') && $by == 'admin')
		{
			require_once(PATH_CORE . DS . 'components' . DS . 'com_publications'
				. DS . 'helpers' . DS . 'html.php');

			$filters 					= array();
			$filters['project']  		= $model->get('id');
			$filters['ignore_access']   = 1;
			$filters['dev']   	 		= 1;

			$database = \JFactory::getDBO();

			$objP 				= new \Components\Publications\Tables\Publication( $database );
			$pubs 				= $objP->getRecords($filters);

			$view->pubDiskUsage = \Components\Publications\Helpers\Html::getDiskUsage($pubs);
			$view->pubQuota 	= $model->params->get('pubQuota')
								? $model->params->get('pubQuota')
								: \Components\Projects\Helpers\Html::convertSize( floatval($model->config()->get('pubQuota', '1')), 'GB', 'b');
		}

		$view->total  = $this->repo->count();
		$quota 		  = $model->params->get('quota');
		$view->quota  = $quota
			? $quota
			: \Components\Projects\Helpers\Html::convertSize( floatval($model->config()->get('defaultQuota', '1')), 'GB', 'b');

		$view->by 		= $by;
		$view->model 	= $model;
		$view->option 	= $this->_option;
		$view->config 	= $model->config();
		$view->title	= isset($this->_area['title']) ? $this->_area['title'] : '';
		$view->params 	= $this->params;

		return $view->loadTemplate();
	}

	/**
	 * Show deleted files
	 *
	 * @return     string
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
		$url  = $this->_route . '&active=files';
		$url .= $this->repo->isLocal() ? '' : '&repo=' . $this->repo->get('name');
		$url .= $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
		$view->url    = $url;
		$view->subdir = $this->subdir;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Manage connections to outside services
	 *
	 * @param      string	$service	Service name (google/dropbox)
	 * @param      string	$callback	URL to return to after authorization
	 * @return     string
	 */
	protected function _connect($service = '', $callback = '')
	{
		// Incoming
		$service 	= $service ? $service : Request::getVar('service', '');
		$reauth 	= Request::getInt('reauth', 0);
		$removeData = Request::getInt('removedata', 1);

		// Build pub url
		$url = Route::url($this->_route . '&active=files');

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
				$this->_referer = $url . '?action=connect';
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

		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->model 		= $this->model;
		$view->uid 			= $this->_uid;
		$view->route		= $this->_route;
		$view->url 			= $url;
		$view->title		= $this->_area['title'];
		$view->services		= $this->_connect->getVar('_services');
		$view->connect		= $this->_connect;

		// Get refreshed params
		$this->model->reloadProject();
		$view->params = new \JParameter( $this->model->table()->params );

		// Get connection details for user
		$member = $this->model->member();
		$view->oparams = new \JParameter( $member->params );

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Initiate sync
	 *
	 * @return   void
	 */
	protected function _iniSync()
	{
		// Incoming
		$ajax 	 = Request::getInt('ajax', 0);
		$auto 	 = Request::getInt('auto', 0);
		$queue 	 = Request::getInt('queue', 0);

		// Timed sync?
		$autoSync = $this->params->get('auto_sync', 0);

		// Remote service(s) active?
		if (!empty($this->_rServices) && $this->repo->isLocal())
		{
			// Get remote files for each active service
			foreach ($this->_rServices as $servicename)
			{
				// Set syncing service
				$this->_rSync['service'] = $servicename;

				// Get time of last sync
				$synced = $this->model->params->get($servicename . '_sync');

				// Stop if auto sync request and not enough time passed
				if ($auto && $autoSync && !$queue)
				{
					if ($autoSync < 1)
					{
						$hr = 60 * $autoSync;
						$timecheck = \JFactory::getDate(time() - (1 * $hr * 60));
					}
					else
					{
						$timecheck = \JFactory::getDate(time() - ($autoSync * 60 * 60));
					}

					if ($synced > $timecheck)
					{
						return json_encode(array('status' => 'waiting'));
					}
				}

				// Send sync request
				$success = $this->_sync( $servicename, $queue, $auto);

				// Unlock sync
				if ($success)
				{
					$this->_lockSync($servicename, true);
				}

				// Success message
				$this->_rSync['message'] = Lang::txt('PLG_PROJECTS_FILES_SYNC_SUCCESS');
			}
		}

		$this->_rSync['auto'] = $auto;

		if (!$ajax)
		{
			return $this->_browse();
		}
		else
		{
			$this->_rSync['output'] = $this->_browse();
			return json_encode($this->_rSync);
		}
	}

	/**
	 * Sync local and remote changes since last sync
	 *
	 * @param    string		$service	Remote service name
	 * @return   void
	 */
	protected function _sync ($service = 'google', $queue = false, $auto = false)
	{
		// Lock sync
		if (!$this->_lockSync($service, false, $queue))
		{
			// Return error
			if ($auto == false)
			{
				$this->_rSync['error'] = Lang::txt('PLG_PROJECTS_FILES_SYNC_DELAYED');
			}

			return false;
		}

		if (!isset($this->_git))
		{
			$this->_git = new \Components\Projects\Helpers\Git($this->_path);
		}

		// Clean up status
		$this->_writeToFile('');

		// Record sync status
		$this->_writeToFile(ucfirst($service) . ' '. Lang::txt('PLG_PROJECTS_FILES_SYNC_STARTED') );

		// Get time of last sync
		$synced = $this->model->params->get($service . '_sync', 1);

		// Get disk usage
		$diskUsage = $this->repo->call('getDiskUsage',
			$params = array(
				'working' => true,
				'history' => $this->params->get('disk_usage')
			)
		);

		$quota 	   = $this->model->params->get('quota')
					? $this->model->params->get('quota')
					: \Components\Projects\Helpers\Html::convertSize( floatval($this->model->config()->get('defaultQuota', '1')), 'GB', 'b');
		$avail 	   = $quota - $diskUsage;

		// Last synced remote/local change
		$lastRemoteChange = $this->model->params->get($service . '_last_remote_change', NULL);
		$lastLocalChange  = $this->model->params->get($service . '_last_local_change', NULL);

		// Get last change ID for project creator
		$lastSyncId = $this->model->params->get($service . '_sync_id', NULL);
		$prevSyncId = $this->model->params->get($service . '_prev_sync_id', NULL);

		// User ID of project owner
		$projectOwner = $this->model->get('owned_by_user');

		// Are we syncing project home directory or other?
		$localDir   = $this->_connect->getConfigParam($service, 'local_dir');
		$localDir   = $localDir == '#home' ? '' : $localDir;

		$localPath  = $this->_path;
		$localPath .= $localDir ? DS . $localDir : '';

		// Record sync status
		$this->_writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_ESTABLISH_REMOTE_CONNECT') );

		// Get service API - always project owner!
		$this->_connect->getAPI($service, $projectOwner);

		// Collector arrays
		$locals 		= array();
		$remotes 		= array();
		$localFolders 	= array();
		$remoteFolders 	= array();
		$failed			= array();
		$deletes		= array();
		$timedRemotes	= array();

		// Sync start time
		$startTime =  date('c');
		$passed    = $synced != 1 ? \Components\Projects\Helpers\Html::timeDifference(strtotime($startTime) - strtotime($synced)) : 'N/A';

		// Start debug output
		$output  = ucfirst($service) . "\n";
		$output .= $synced != 1 ? 'Last sync (local): ' . $synced
				. ' | (UTC): ' . gmdate('Y-m-d H:i:s', strtotime($synced)) . "\n" : "";
		$output .= 'Previous sync ID: ' . $prevSyncId . "\n";
		$output .= 'Current sync ID: ' . $lastSyncId . "\n";
		$output .= 'Last synced remote change: '.  $lastRemoteChange . "\n";
		$output .= 'Last synced local change: '.  $lastLocalChange . "\n";
		$output .= 'Time passed since last sync: ' . $passed . "\n";
		$output .= 'Local sync start time: '.  $startTime . "\n";
		$output .= 'Initiated by (user ID): '.  $this->_uid . ' [';
		$output .= ($auto == true) ? 'Auto sync' : 'Manual sync request';
		$output .= ']' . "\n";

		// Record sync status
		$this->_writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_STRUCTURE_REMOTE') );

		// Get stored remote connections
		$objRFile = new \Components\Projects\Tables\RemoteFile ($this->_database);
		$connections = $objRFile->getRemoteConnections($this->model->get('id'), $service);

		// Get remote folder structure (to find out remote ids)
		$this->_connect->getFolderStructure($service, $projectOwner, $remoteFolders);

		// Record sync status
		$this->_writeToFile( Lang::txt('PLG_PROJECTS_FILES_SYNC_COLLECT_LOCAL') );

		// Collector for local renames
		$localRenames = array();

		$fromLocal = ($synced == $lastLocalChange || !$lastLocalChange) ? $synced : $lastLocalChange;

		// Get all local changes since last sync
		$locals = $this->_git->getChanges($localPath, $fromLocal, $localDir, $localRenames, $connections );

		// Record sync status
		$this->_writeToFile( Lang::txt('PLG_PROJECTS_FILES_SYNC_COLLECT_REMOTE') );

		// Get all remote files that changed since last sync
		$newSyncId  = 0;
		$nextSyncId = 0;
		if ($lastSyncId > 1)
		{
			// Via Changes feed
			$newSyncId = $this->_connect->getChangedItems(
				$service, $projectOwner,
				$lastSyncId, $remotes,
				$deletes, $connections
			);
		}
		else
		{
			// Via List feed
			$remotes = $this->_connect->getRemoteItems($service, $projectOwner, '', $connections);
			$newSyncId = 1;
		}

		// Record sync status
		$this->_writeToFile( Lang::txt('PLG_PROJECTS_FILES_SYNC_VERIFY_REMOTE') );

		// Possible that we've missed a change?
		if ( $newSyncId > $lastSyncId )
		{
			$output .= '!!! Changes detected - new change ID: ' . $newSyncId . "\n";
		}
		else
		{
			$output .= '>>> Returned change ID: ' . $newSyncId . "\n";
		}

		$output .= empty($remotes)
				? 'No changes brought in by Changes feed' . "\n"
				: 'Changes feed has ' . count($remotes) . ' changes' . "\n";

		$from = ($synced == $lastRemoteChange || !$lastRemoteChange)
				? date("c", strtotime($synced) - (1)) : date("c", strtotime($lastRemoteChange));

		// Get changes via List feed (to make sure we get ALL changes)
		// We need this because Changes feed is not 100% reliable
		if ( $newSyncId > $lastSyncId)
		{
			$timedRemotes = $this->_connect->getRemoteItems($service, $projectOwner, $from, $connections);
		}

		// Record timed remote changes (for debugging)
		if (!empty($timedRemotes))
		{
			$output .= 'Timed remote changes since ' . $from . ' (' . count($timedRemotes) . '):' . "\n";
			foreach ($timedRemotes as $tr => $trinfo)
			{
				$output .= $tr . ' changed ' . date("c", $trinfo['time'])
						. ' status ' . $trinfo['status'] . ' ' . $trinfo['fileSize'] . "\n";
			}

			// Pick up missed changes
			if ($remotes != $timedRemotes)
			{
				$output .= empty($remotes)
					? 'Using exclusively timed changes ' . "\n"
					: 'Mixing in timed changes ' . "\n";

				$remotes = array_merge($timedRemotes, $remotes);
			}
		}
		else
		{
			$output .= 'No timed changes since ' . $from . "\n";
		}

		if ($this->_connect->getError())
		{
			$this->_writeToFile( '' );
			$this->_rSync['error'] = Lang::txt('PLG_PROJECTS_FILES_SYNC_ERROR_OUPS') . ' ' . $this->_connect->getError();
			$this->_lockSync($service, true);
			return false;
		}

		// Collector arrays for processed files
		$processedLocal 	= array();
		$processedRemote 	= array();
		$conflicts			= array();

		// Record sync status
		$this->_writeToFile( Lang::txt('PLG_PROJECTS_FILES_SYNC_EXPORTING_LOCAL') );

		$output .= 'Local changes:' . "\n";

		// Go through local changes
		if (count($locals) > 0)
		{
			$lChange = NULL;
			foreach ($locals as $filename => $local)
			{
				$output .= ' * Local change ' . $filename . ' - ' . $local['status'] . ' - ' . $local['modified'] . ' - ' . $local['time'] . "\n";

				// Get latest change
				$lChange = $local['time'] > $lChange ? $local['time'] : $lChange;

				// Skip renamed files (local renames are handled later)
				if (in_array($filename, $localRenames) && !file_exists($local['fullPath']))
				{
					$output .= '## skipped rename from '. $filename . "\n";
					continue;
				}

				// Do we have a matching remote change?
				$match = !empty($remotes)
					&& isset($remotes[$filename])
					&& $local['type'] == $remotes[$filename]['type']
					? $remotes[$filename] : NULL;

				// Check against individual item sync time (to avoid repeat sync)
				if ($local['synced'] && ($local['synced']  > $local['modified']))
				{
					$output .= '## item in sync: '. $filename . ' local: '
						. $local['modified'] . ' synced: ' . $local['synced'] . "\n";
					$processedLocal[$filename] = $local;
					continue;
				}
				// Record sync status
				$this->_writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_SYNCING') . ' '
					. \Components\Projects\Helpers\Html::shortenFileName($filename, 30) );

				// Item renamed
				if ($local['status'] == 'R')
				{
					if ($local['remoteid'])
					{
						// Rename remote item
						$renamed = $this->_connect->renameRemoteItem(
							$this->model->get('id'), $service, $projectOwner,
							$local['remoteid'], $local,  $local['rParent']
						);

						$output .= '>> renamed ' . $local['rename'] . ' to ' . $filename . "\n";
						$processedLocal[$filename] = $local;

						if ($local['type'] == 'folder')
						{
							$this->_connect->fixConvertedItems($service, $this->_uid, $local['rename'], 'R', $filename);
						}

						continue;
					}
				}
				// Item moved
				if ($local['status'] == 'W')
				{
					if ($local['remoteid'])
					{
						// Determine new remote parent
						$parentId = $this->_connect->prepRemoteParent($this->model->get('id'), $service, $projectOwner, $local, $remoteFolders);

						if ($parentId != $local['rParent'])
						{
							// Move to new parent
							$moved = $this->_connect->moveRemoteItem(
								$this->model->get('id'), $service, $projectOwner,
								$local['remoteid'], $local,  $parentId
							);

							$output .= '>> moved ' . $local['rename'] . ' to ' . $filename . ' (new parent id '
								. $parentId . ')' . "\n";
							$processedLocal[$filename] = $local;

							if ($local['type'] == 'folder')
							{
								$this->_connect->fixConvertedItems($service, $this->_uid, $local['rename'], 'W', $filename, $parentId);
							}

							continue;
						}
					}
				}

				// Check for match in remote changes
				if ($match && (($match['time'] - strtotime($from)) > 0))
				{
					// skip - remote change prevails
					$output .= '== local and remote change match (choosing remote over local): '. $filename . "\n";
					$conflicts[$filename] = $local['remoteid'];
				}
				else
				{
					// Local change needs to be transferred
					if ($local['status'] == 'D')
					{
						$deleted   = 0;

						// Delete operation
						if ($local['remoteid'])
						{
							// Delete remote file
							$deleted = $this->_connect->deleteRemoteItem(
								$this->model->get('id'), $service, $projectOwner,
								$local['remoteid'], false
							);

							// Delete from remote
							$output .= '-- deleted from remote: '. $filename . "\n";
						}
						else
						{
							// skip (deleted non-synced file)
							$output .= '## skipped deleted non-synced item: '. $filename . "\n";
							$deleted = 1;
						}

						if ($local['type'] == 'folder')
						{
							$this->_connect->fixConvertedItems($service, $this->_uid, $filename, 'D');
						}

						// Delete connection record if exists
						if ($deleted)
						{
							$objRFile = new \Components\Projects\Tables\RemoteFile ($this->_database);
							$objRFile->deleteRecord( $this->model->get('id'), $service, $local['remoteid'], $filename);
						}
					}
					else
					{
						// Not updating converted files via sync
						if ($local['converted'] == 1)
						{
							$output .= '## skipped locally changed converted file: '. $filename . "\n";
						}
						else
						{
							// Item in directory? Make sure we have correct remote dir structure in place
							$parentId = $this->_connect->prepRemoteParent($this->model->get('id'), $service, $projectOwner, $local, $remoteFolders);

							// Add/update operation
							if ($local['remoteid'])
							{
								// Update remote file
								$updated = $this->_connect->updateRemoteFile(
									$this->model->get('id'), $service, $projectOwner,
									$local['remoteid'], $local, $parentId
								);

								$output .= '++ sent update from local to remote: '. $filename . "\n";
							}
							else
							{
								// Add item from local to remote (new)
								if ($local['type'] == 'folder')
								{
									// Create remote folder
									$created = $this->_connect->createRemoteFolder(
										$this->model->get('id'), $service, $projectOwner,
										basename($filename), $filename,  $parentId, $remoteFolders
									);

									$output .= '++ created remote folder: '. $filename . "\n";

								}
								elseif ($local['type'] == 'file')
								{
									// Create remote file
									$created = $this->_connect->addRemoteFile(
										$this->model->get('id'), $service, $projectOwner,
										$local, $parentId
									);

									$output .= '++ added new file to remote: '. $filename . "\n";
								}
							}
						}
					}
				}

				$processedLocal[$filename] = $local;
				$lastLocalChange = $lChange ? date('c', $lChange + 1) : NULL;
			}
		}
		else
		{
			$output .= 'No local changes since last sync'. "\n";
		}

		$newRemotes   = array();

		// Record sync status
		$this->_writeToFile( Lang::txt('PLG_PROJECTS_FILES_SYNC_REFRESHING_REMOTE') );

		// Get new change ID after local changes got sent to remote
		if (!empty($locals))
		{
			$newSyncId = $this->_connect->getChangedItems($service, $projectOwner,
				$newSyncId, $newRemotes, $deletes, $connections);
		}

		// Get very last received remote change
		if (!empty($remotes))
		{
			$tChange = strtotime($lastRemoteChange);
			foreach ($remotes as $r => $ri)
			{
				$tChange = $ri['time'] > $tChange ? $ri['time'] : $tChange;
			}

			$lastRemoteChange = $tChange ? date('c', $tChange) : NULL;
		}

		// Make sure we have thumbnails for updates from local repo
		if (!empty($newRemotes) && $synced != 1)
		{
			$tChange = strtotime($lastRemoteChange);
			foreach ($newRemotes as $filename => $nR)
			{
				// Generate local thumbnail
				if ($nR['thumb'])
				{
					$this->_writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_GET_THUMB') . ' ' . \Components\Projects\Helpers\Html::shortenFileName($filename, 15) );
					$this->_connect->generateThumbnail($service, $projectOwner,
						$nR, $this->model->config(), $this->model->get('alias'));
				}

				$tChange = $nR['time'] > $tChange ? $nR['time'] : $tChange;
			}

			// Pick up last remote change
			$lastRemoteChange = $tChange ? date('c', $tChange) : NULL;
		}

		// Record sync status
		$this->_writeToFile( Lang::txt('PLG_PROJECTS_FILES_SYNC_IMPORTING_REMOTE') );

		$output .= 'Remote changes:' . "\n";

		// Go through remote changes
		if (count($remotes) > 0 && $synced != 1)
		{
			// Get email/name pairs of connected project owners
			$objO = new \Components\Projects\Tables\Owner( $this->_database );
			$connected = $objO->getConnected($this->model->get('id'), $service);

			// Examine each change
			foreach ($remotes as $filename => $remote)
			{
				$output .= ' * Remote change ' . $filename . ' - ' . $remote['status'] . ' - ' . $remote['modified'];
				$output .= $remote['fileSize'] ? ' - ' . $remote['fileSize'] . ' bytes' : '';
				$output .= "\n";

				// Do we have a matching local change?
				$match = !empty($locals)
					&& isset($locals[$filename])
					&& $remote['type'] == $locals[$filename]['type']
					? $locals[$filename] : array();

				// Check for match in local changes
				// Remote usually prevails, unless it's older than last synced remote change
				if ($match && (($match['modified'] > $remote['modified']) > 0))
				{
					// skip
					$output .= '== local and remote change match, but remote is older, picking local: '. $filename . "\n";
					$conflicts[$filename] = $local['remoteid'];
					continue;
				}

				$updated 	= 0;
				$deleted   	= 0;

				// Get change author for Git
				$email = 'sync@sync.org';
				$name = utf8_decode($remote['author']);
				if ($connected && isset($connected[$name]))
				{
					$email = $connected[$name];
				}
				else
				{
					// Email from profile?
					$email = $objO->getProfileEmail($name, $this->model->get('id'));
				}
				$author = $this->_git->getGitAuthor($name, $email);

				// Change acting user to whoever did the remote change
				$uid = $objO->getProfileId( $email, $this->model->get('id'));
				if ($uid)
				{
					$this->_uid = $uid;
				}

				// Set Git author date (GIT_AUTHOR_DATE)
				$cDate = date('c', $remote['time']); // Important! Needs to be local time, NOT UTC

				// Record sync status
				$this->_writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_SYNCING') . ' ' . \Components\Projects\Helpers\Html::shortenFileName($filename, 30) );

				// Item in directory? Make sure we have correct local dir structure
				$local_dir = dirname($filename) != '.' ? dirname($filename) : '';
				if ($remote['status'] != 'D' && $local_dir && !\JFolder::exists( $this->_path . DS . $local_dir ))
				{
					if (\JFolder::create( $this->_path . DS . $local_dir ))
					{
						$created = $this->_git->makeEmptyFolder($local_dir, false);
						$commitMsg = Lang::txt('PLG_PROJECTS_FILES_CREATED_DIRECTORY')
							. '  ' . escapeshellarg($local_dir);
						$this->_git->gitCommit($commitMsg, $author, $cDate);
					}
					else
					{
						// Error
						$output .= '[error] failed to provision local directory for: '. $filename . "\n";
						$failed[] = $filename;
						continue;
					}
				}

				// Send remote change to local (whether or not there is local change)
				// Remote version always prevails
				if ($remote['status'] == 'D')
				{
					if (file_exists($this->_path . DS . $filename))
					{
						// Delete in Git
						$deleted = $this->_git->gitDelete($filename, $remote['type'], $commitMsg);
						if ($deleted)
						{
							$this->_git->gitCommit($commitMsg, $author, $cDate);

							// Delete local file or directory
							$output .= '-- deleted from local: '. $filename . "\n";
						}
						else
						{
							// Error
							$output .= '[error] failed to delete from local: '. $filename . "\n";
							$failed[] = $filename;
							continue;
						}
					}
					else
					{
						// skip (deleted non-synced file)
						$output .= $remote['converted'] == 1
									? '-- deleted converted: '. $filename . "\n"
									: '## skipped deleted non-synced item: '. $filename . "\n";
						$deleted = 1;
					}

					// Delete connection record if exists
					if ($deleted)
					{
						$objRFile = new \Components\Projects\Tables\RemoteFile ($this->_database);
						$objRFile->deleteRecord( $this->model->get('id'), $service, $remote['remoteid']);
					}
				}
				elseif ($remote['status'] == 'R' || $remote['status'] == 'W')
				{
					// Rename/move in Git
					if (file_exists($this->_path . DS . $remote['rename']))
					{
						$output .= '>> rename from: '. $remote['rename'] . ' to ' . $filename . "\n";

						if ($this->_git->gitMove($remote['rename'], $filename, $remote['type'], $commitMsg))
						{
							$this->_git->gitCommit($commitMsg, $author, $cDate);
							$output .= '>> renamed/moved item locally: '. $filename . "\n";
							$updated = 1;
						}
						else
						{
							// Error
							$output .= '[error] failed to rename/move item locally: '. $filename . "\n";
							$failed[] = $filename;
							continue;
						}
					}

					if ($remote['converted'] == 1)
					{
						$output .= '>> renamed/moved item locally converted: '. $filename . "\n";
						$updated = 1;
					}
				}
				else
				{
					if ($remote['converted'] == 1)
					{
						// Not updating converted files via sync
						$output .= '## skipped converted remotely changed file: '. $filename . "\n";
						$updated = 1;
					}
					elseif (file_exists($this->_path . DS . $filename))
					{
						// Update
						if ($remote['type'] == 'file')
						{
							// Check md5 hash - do we have identical files?
							$md5Checksum = hash_file('md5', $this->_path . DS . $filename);
							if ($remote['md5'] == $md5Checksum)
							{
								// Skip update
								$output .= '## update skipped: local and remote versions identical: '
										. $filename . "\n";
								$updated = 1;
							}
							else
							{
								// Download remote file
								if ($this->_connect->downloadFileCurl(
									$service,
									$projectOwner,
									$remote['url'],
									$this->_path . DS . $remote['local_path'])
								)
								{
									// Checkin into repo
									$this->repo->call('checkin', array(
										'file'   => $this->repo->getMetadata($filename, 'file', array()),
										'author' => $author,
										'date'   => $cDate
										)
									);

									$output .= ' ! versions differ: remote md5 ' . $remote['md5'] . ', local md5' . $md5Checksum . "\n";
									$output .= '++ sent update from remote to local: '. $filename . "\n";
									$updated = 1;
								}
								else
								{
									// Error
									$output .= '[error] failed to update local file with remote change: '. $filename . "\n";
									$failed[] = $filename;
									continue;
								}
							}
						}
						else
						{
							$output .= '## skipped folder in sync: '. $filename . "\n";
							$updated = 1;
						}
					}
					else
					{
						// Add item from remote to local (new)
						if ($remote['type'] == 'folder')
						{
							if (\JFolder::create( $this->_path . DS . $filename ))
							{
								$created = $this->_git->makeEmptyFolder($filename, false);
								$commitMsg = Lang::txt('PLG_PROJECTS_FILES_CREATED_DIRECTORY')
									. '  ' . escapeshellarg($filename);
								$this->_git->gitCommit($commitMsg, $author, $cDate);
								$output .= '++ created local folder: '. $filename . "\n";
								$updated = 1;
							}
							else
							{
								// error
								$output .= '[error] failed to create local folder: '. $filename . "\n";
								$failed[] = $filename;
								continue;
							}
						}
						else
						{
							// Check against quota
							$checkAvail = $avail - $remote['fileSize'];
							if ($checkAvail <= 0)
							{
								// Error
								$output .= '[error] not enough space for '. $filename . ' (' . $remote['fileSize']
										. ' bytes) avail space:' . $checkAvail . "\n";
								$failed[] = $filename;
								continue;
							}
							else
							{
								$avail   = $checkAvail;
								$output .= 'file size ok: ' . $remote['fileSize'] . ' bytes ' . "\n";
							}

							// Download remote file
							if ($this->_connect->downloadFileCurl(
								$service,
								$projectOwner,
								$remote['url'],
								$this->_path . DS . $remote['local_path'])
							)
							{
								// Git add & commit
								$this->_git->gitAdd($filename, $commitMsg);
								$this->_git->gitCommit($commitMsg, $author, $cDate);

								$output .= '++ added new file to local: '. $filename . "\n";
								$updated = 1;

								// Store in session
								$this->registerUpdate('uploaded', $filename);
							}
							else
							{
								// Error
								$output .= '[error] failed to add new local file: '. $filename . "\n";
								$failed[] = $filename;
								continue;
							}
						}
					}
				}

				// Update connection record
				if ($updated)
				{
					$objRFile = new \Components\Projects\Tables\RemoteFile ($this->_database);
					$objRFile->updateSyncRecord(
						$this->model->get('id'), $service, $this->_uid,
						$remote['type'], $remote['remoteid'], $filename,
						$match, $remote
					);

					$lastLocalChange = date('c', time() + 1);

					// Generate local thumbnail
					if ($remote['thumb'] && $remote['status'] != 'D')
					{
						$this->_writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_GET_THUMB') . ' '
						. \Components\Projects\Helpers\Html::shortenFileName($filename, 15) );
						$this->_connect->generateThumbnail($service, $projectOwner, $remote,
							$this->model->config(), $this->model->get('alias'));
					}
				}

				$processedRemote[$filename] = $remote;
			}
		}
		else
		{
			$output .= 'No remote changes since last sync' . "\n";
		}

		// Hold on by one second (required as a forced breather before next sync request)
		sleep(1);

		// Log time
		$endTime = date('c');
		$length  = \Components\Projects\Helpers\Html::timeDifference(strtotime($endTime) - strtotime($startTime));

		$output .= 'Sync complete:' . "\n";
		$output .= 'Local time: '. $endTime . "\n";
		$output .= 'UTC time: '.  Date::toSql() . "\n";
		$output .= 'Sync completed in: '.  $length . "\n";

		// Determine next sync ID
		if (!$nextSyncId)
		{
			$nextSyncId  = ($newSyncId > $lastSyncId || count($remotes) > 0) ? ($newSyncId + 1) : $lastSyncId;
		}

		// Save sync time
		$this->model->saveParam($service . '_sync', $endTime);

		// Save change id for next sync
		$this->model->saveParam($service . '_sync_id', ($nextSyncId));
		$output .= 'Next sync ID: ' . $nextSyncId . "\n";

		$this->model->saveParam($service . '_prev_sync_id', $lastSyncId);

		$output .= 'Saving last synced remote change at: ' . $lastRemoteChange . "\n";
		$this->model->saveParam($service . '_last_remote_change', $lastRemoteChange);

		$output .= 'Saving last synced local change at: ' . $lastLocalChange . "\n";
		$this->model->saveParam($service . '_last_local_change', $lastLocalChange);

		// Debug output
		$temp = $this->_logPath;
		$this->_writeToFile($output, $temp . DS . 'sync.' . \JFactory::getDate()->format('Y-m') . '.log', true);

		// Record sync status
		$this->_writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_COMPLETE_UPDATE_VIEW') );

		// Unlock sync
		$this->_lockSync($service, true);

		// Clean up status
		$this->_writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_COMPLETE'));

		$this->_rSync['status'] = 'success';
		return true;
	}

	/**
	 * Sync error
	 *
	 * @return     string
	 */
	public function syncError()
	{
		$service 	= Request::getVar('service', 'google');

		$this->_writeToFile( '' );
		$this->_rSync['error'] = Lang::txt('PLG_PROJECTS_FILES_SYNC_ERROR');
		$this->_lockSync($service, true);
		return;
	}

	/**
	 * Get sync status (AJAX call)
	 *
	 * @return     string
	 */
	public function syncStatus()
	{
		// Incoming
		$pid 		= Request::getInt('id', 0);
		$service 	= Request::getVar('service', 'google');
		$status 	= array('status' => '', 'msg' => time(), 'output' => '');

		// Read status file
		$rFile = $this->_readFile();

		// Report sync progress
		if ($rFile && $rFile != Lang::txt('PLG_PROJECTS_FILES_SYNC_COMPLETE'))
		{
			$status = array('status' => 'progress', 'msg' => $rFile, 'output' => '');
		}
		elseif ($service)
		{
			// Get time of last sync
			$this->model->reloadProject();

			$synced 	= $this->model->params->get($service . '_sync');
			$syncLock 	= $this->model->params->get($service . '_sync_lock', '');

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
	 * Check if sync operation is in progress
	 *
	 * @param    string		$service	Remote service name
	 * @return   Boolean
	 */
	protected function _checkSyncLock ($service = 'google')
	{
		$syncLock = $this->model->params->get($service . '_sync_lock', '');
		return $syncLock ? true : false;
	}

	/**
	 * Lock/unlock sync operation
	 *
	 * @param    string		$service	Remote service name
	 * @return   void
	 */
	protected function _lockSync ($service = 'google', $unlock = false, $queue = 0 )
	{
		$pparams 	= $this->model->params;
		$synced 	= $pparams->get($service . '_sync');
		$syncLock 	= $pparams->get($service . '_sync_lock');
		$syncQueue 	= $pparams->get($service . '_sync_queue', 0);

		// Request to unlock sync
		if ($unlock == true)
		{
			$this->model->saveParam($service . '_sync_lock', '');
			$this->_rSync['status'] = 'complete';

			// Clean up status
			$this->_writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_COMPLETE'));

			// Repeat sync? (another request in queue)
			if ($syncQueue > 0)
			{
				// Clean up queue
				$this->model->saveParam($service . '_sync_queue', 0);
			}

			return true;
		}

		// Is there time lock?
		$timeLock = $this->params->get('sync_lock', 0);
		if ($timeLock)
		{
			$timecheck = date('c', time() - (1 * $timeLock * 60));
		}

		// Can't run sync - too soon
		if ($timeLock && $synced && $synced > $timecheck && !$queue)
		{
			$this->_rSync['status'] = 'locked';
			return false;
		}
		elseif ($syncLock)
		{
			// Add request to queue
			if ($queue && $syncQueue == 0)
			{
				$this->model->saveParam($service . '_sync_queue', 1);
				return false;
			}

			// Past hour - sync should have been complete, unlock
			$timecheck = date('c', time() - (1 * 60 * 60));

			if ($synced && $synced >= $timecheck)
			{
				$this->_rSync['status'] = 'locked';
				return false;
			}
		}

		// Lock sync
		$this->model->saveParam($service . '_sync_lock', $this->_uid);
		$this->_rSync['status'] = 'progress';
		return true;
	}

	/**
	 * Archive files
	 *
	 * @param      array 	$files
	 * @param      array  	$folders
	 * @param      string  	$projectPath
	 * @param      string  	$subdir
	 *
	 * @return     array or false
	 */
	private function _archiveFiles( $items, $projectPath = '', $subdir = '' )
	{
		if (!extension_loaded('zip'))
		{
			return false;
		}

		if (!$projectPath || !is_dir($projectPath))
		{
			return false;
		}

		if (empty($items))
		{
			return false;
		}

		$maxDownload 	= intval($this->params->get('maxDownload', 104857600));

		// Get temp directory
		$base_path 		= sys_get_temp_dir();
		$tarname 		= 'project_files_' . \Components\Projects\Helpers\Html::generateCode (6 , 6 , 0 , 1 , 1 ) . '.zip';
		$path 			= $subdir ? $projectPath. DS . $subdir : $projectPath;
		$combinedSize  	= 0;
		$tarpath        =  $base_path . DS . $tarname;

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
							$this->setError( Lang::txt('PLG_PROJECTS_FILES_ERROR_OVER_DOWNLOAD_LIMIT') );
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
				$this->setError( Lang::txt('PLG_PROJECTS_FILES_SERVER_ERROR') );
				return false;
			}

			$archive = array();
			$archive['path'] = $tarpath;
			$archive['name'] = $tarname;
			return $archive;
		}
		else
		{
		    return false;
		}
	}

	/**
	 * Get stored remote connections
	 *
	 * @return     array
	 */
	protected function _getRemoteConnections($remoteEdit = true)
	{
		$remotes = array();
		if (!empty($this->_remoteService))
		{
			$objRFile = new \Components\Projects\Tables\RemoteFile ($this->_database);
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
	 * @return     string
	 */
	public function getMembersPath()
	{
		// Get members config
		$mconfig = Component::params( 'com_members' );

		// Build upload path
		$dir  = \Hubzero\Utility\String::pad( $this->_uid );
		$path = DS . trim($mconfig->get('webpath', '/site/members'), DS) . DS . $dir . DS . 'files';

		if (!is_dir( PATH_APP . $path ))
		{
			if (!\JFolder::create( PATH_APP . $path ))
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
	 * @param      string	$path
	 * @param      string  	$subdir
	 * @param      boolean  $recurse
	 *
	 * @return     array
	 */
	protected function _getMemberFiles($recurse = true)
	{
		// Check path format
		$subdir = trim($this->subdir, DS);
		$fullpath = $subdir ? $this->_path. DS . $subdir : $this->_path;

		$get = $this->fileSystem->files($fullpath);

		$files = array();
		if ($get)
		{
			foreach ($get as $file)
			{
				if (substr($file,0,1) != '.' && strtolower($file) !== 'index.html')
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
	 * @return     void
	 */
	public function registerUpdate( $type = '' , $file = '', $append = true, $appendMessage = '')
	{
		if (!$type || !$file)
		{
			return false;
		}

		$kind = 'projects.' . $this->model->get('alias') . '.' . $type;

		// Get session
		$jsession = \JFactory::getSession();

		if ($append == true)
		{
			$exVal  = $jsession->get($kind);
			$val 	= $exVal ? $exVal . ', ' . $file : $file;
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
	 * @return     void
	 */
	public function onAfterUpdate($model = NULL, $changes = array())
	{
		$activity = '';
		$message  = '';
		$ref	  = '';

		$model = $model ? $model : $this->model;

		if (empty($changes))
		{
			// Get session
			$jsession = \JFactory::getSession();

			// Get values from session
			$updated 	= $jsession->get('projects.' . $this->model->get('alias') . '.updated');
			$uploaded 	= $jsession->get('projects.' . $this->model->get('alias') . '.uploaded');
			$failed 	= $jsession->get('projects.' . $this->model->get('alias') . '.failed');
			$deleted 	= $jsession->get('projects.' . $this->model->get('alias') . '.deleted');
			$restored 	= $jsession->get('projects.' . $this->model->get('alias') . '.restored');
			$expanded 	= $jsession->get('projects.' . $this->model->get('alias') . '.expanded');

			// Clean up session values
			$jsession->set('projects.' . $this->model->get('alias') . '.failed', '');
			$jsession->set('projects.' . $this->model->get('alias') . '.updated', '');
			$jsession->set('projects.' . $this->model->get('alias') . '.uploaded', '');
			$jsession->set('projects.' . $this->model->get('alias') . '.deleted', '');
			$jsession->set('projects.' . $this->model->get('alias') . '.restored', '');
			$jsession->set('projects.' . $this->model->get('alias') . '.expanded', '');
		}
		else
		{
			$updated 	= !empty($changes['updated']) ? $changes['updated'] : NULL;
			$uploaded 	= !empty($changes['uploaded']) ? $changes['uploaded'] : NULL;
			$failed 	= !empty($changes['failed']) ? $changes['failed'] : NULL;
			$deleted 	= !empty($changes['deleted']) ? $changes['deleted'] : NULL;
			$restored 	= !empty($changes['restored']) ? $changes['restored'] : NULL;
			$expanded 	= !empty($changes['expanded']) ? $changes['expanded'] : NULL;
		}

		// Provisioned project?
		if ($model->isProvisioned() || !$model->get('id'))
		{
			return false;
		}

		// Pass success or error message
		if (!empty($failed) && !$uploaded && !$uploaded)
		{
			$this->_message = array('message' => 'Failed to upload ' . $failed, 'type' => 'error');
		}
		elseif ($uploaded || $updated || $expanded)
		{
			$uploadParts = explode(',', $uploaded);
			$updateParts = explode(',', $updated);

			if ($uploaded)
			{
				if (count($uploadParts) > 2)
				{
					$message = 'uploaded ' . basename($uploadParts[0]) . ' and '
						. (count($uploadParts) - 1) . ' more files ' ;
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
					$message.= basename($updateParts[0]) . ' and ' . (count($updateParts) - 1) . ' more files ' ;
				}
				else
				{
					$message = 'updated ';
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
			$this->_message = array('message' => $message, 'type' => 'success');
		}
		elseif ($deleted)
		{
			// Save referenced files
			$ref = $deleted;

			$delParts = explode(',', $deleted);

			$what = count($delParts) == 1 ? $deleted : count($delParts)
				. ' ' . Lang::txt('PLG_PROJECTS_FILES_ITEMS');

			// Output message
			$this->_message = array('message' => Lang::txt('PLG_PROJECTS_FILES_SUCCESS_DELETED')
				. ' ' . $what, 'type' => 'success');
		}
		elseif ($restored)
		{
			// Save referenced files
			$ref = $restored;

			$resParts = explode(',', $restored);

			$activity = 'restored deleted file ' . basename($resParts[0]);

			// Output message
			$this->_message = array('message' => Lang::txt('PLG_PROJECTS_FILES_SUCCESS_RESTORED')
				. ' ' . basename($resParts[0]), 'type' => 'success');
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

			// Record activity
			$aid = $model->recordActivity( $activity, $parsedRef, 'project files',
				Route::url('index.php?option=' . $this->_option
				. '&alias=' . $model->get('alias') . '&active=files'), 'files', 1
			);
		}
	}

	/**
	 * Clean incoming data
	 *
	 * @return     array
	 */
	protected function _cleanData()
	{
		// Clean up empty values
		$checked 	= Request::getVar( 'asset', '', 'request', 'array' );
		$folders 	= Request::getVar( 'folder', '', 'request', 'array' );

		foreach ($checked as $key => $value)
		{
			$value = trim($value);
			if ($value == '')
			{
				unset($checked[$key]);
			}
			else
			{
				$checked[$key] = trim($value);
			}
		}

		foreach ($folders as $key => $value)
		{
			$value = trim($value);
			if ($value == '')
			{
				unset($folders[$key]);
			}
			else
			{
				$folders[$key] = trim($value);
			}
		}

		Request::setVar( 'asset', $checked);
		Request::setVar( 'folder', $folders);
	}

	/**
	 * Sort incoming file/folder data
	 *
	 * @return     array
	 */
	protected function _sortIncoming()
	{
		// Clean incoming data
		$this->_cleanData();

		// Incoming
		$checked = Request::getVar( 'asset', '', 'request', 'array' );
		$folders = Request::getVar( 'folder', '', 'request', 'array' );

		$combined = array();
		if (!empty($checked))
		{
			foreach ($checked as $ch)
			{
				if (trim($ch) != '')
				{
					$combined[] = array('file' => urldecode($ch));
				}
			}
		}
		elseif ($file = Request::getVar( 'asset', ''))
		{
			$combined[] = array('file' => urldecode($file));
		}
		if (!empty($folders))
		{
			foreach ($folders as $f)
			{
				if (trim($f) != '')
				{
					$combined[] = array('folder' => urldecode($f));
				}
			}
		}
		elseif ($folder = Request::getVar( 'folder', ''))
		{
			$combined[] = array('folder' => urldecode($folder));
		}

		return $combined;
	}

	/**
	 * Write sync status to file
	 *
	 * @return   void
	 */
	protected function _writeToFile($content = '', $filename = '', $append = false, $dir = 'logs' )
	{
		// Get temp path
		if (!$filename)
		{
			if (empty($this->_logPath))
			{
				return false;
			}
			if (!is_dir($this->_logPath))
			{
				$this->fileSystem->makeDirectory($this->_logPath);
			}
			$sfile 	 = $this->_logPath . DS . 'sync_' . $this->model->get('alias') . '.hidden';
		}
		else
		{
			$sfile = $filename;
		}

		$place   = $append == true ? 'a' : 'w';
		$content = $append ? $content . "\n" : $content;

		$handle  = fopen($sfile, $place);
		fwrite($handle, $content);
		fclose($handle);
	}

	/**
	 * Read sync status from file (last line)
	 *
	 * @return   void
	 */
	protected function _readFile($filename = '', $dir = 'logs', $readAll = false)
	{
		// Get temp path
		if (!$filename)
		{
			$sfile = $this->_logPath . DS . 'sync_' . $this->model->get('alias') . '.hidden';
		}
		else
		{
			$sfile = $filename;
		}

		if (is_file($sfile))
		{
			if ($readAll == true)
			{
				return file_get_contents($sfile);
			}
			else
			{
				// Return last line
				return exec('tail -n 1 ' . $sfile);
			}
		}

		return NULL;
	}
}