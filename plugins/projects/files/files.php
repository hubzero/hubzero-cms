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
			$this->mt = new \Hubzero\Content\Mimetypes();
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

			// Include Git Helper
			$this->_getGitHelper();

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
			$remotes  = $objRFile->getRemoteEditFiles(
				$this->model->get('id'),
				$this->_remoteService,
				$this->subdir
			);

			$view->sync 		 = $sync == 2 ? 0 : $view->oparams->get('google_sync_queue', 0);
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
		$view->publication->_project 	= $this->model->project();

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
		// Get incoming array of items
		$items = $this->_sortIncoming();

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

			$view->items 		= $items;
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

			return $view->loadTemplate();
		}

		// Set counts
		$deleted = 0;

		// Get stored remote connection to file
		if (!empty($this->_remoteService))
		{
			$objRFile = new \Components\Projects\Tables\RemoteFile ($this->_database);
			$remotes  = $objRFile->getRemoteEditFiles(
				$this->model->get('id'),
				$this->_remoteService,
				$this->subdir
			);
		}

		// Delete checked items
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

			// Start params
			$params = array(
				'subdir'            => $this->subdir,
				'item'              => $item,
				'type'              => $type
			);

			$localDirPath = $this->subdir ? $this->subdir . DS . $item : $item;

			// Is this item synced?
			$remote = !empty($remotes) && isset($remotes[$localDirPath]) ? $remotes[$localDirPath] : NULL;

			// Is this a remote synced item?
			if (!empty($remote) && $remote->remote_editing == 1)
			{
				// Delete remote converted file
				if ($this->_connect->deleteRemoteItem(
					$this->model->get('id'), $this->_remoteService, $this->model->get('owned_by_user'),
					$remote->remote_id, false))
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

		if ($deleted == 0)
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
	 * Rename
	 *
	 * @return     void, redirect
	 */
	protected function _rename()
	{
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
				$view->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_RENAME_NO_OLD_NAME'));
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
		// Get incoming array of items
		$items = $this->_sortIncoming();

		if (empty($items))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_MOVE'));
		}

		$url 	= Route::url($this->_route . '&active=files');

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

			$params = array(
				'subdir'               => $this->subdir,
				'sortby'               => 'localpath', // important for selector!
				'showFullMetadata'     => false,
				'getParents'           => true, // show folders
				'getChildren'          => true, // look inside directories
			);

			$view->list			= $this->repo->filelist($params);
			$view->path 		= $this->_path;
			$view->items 		= $items;
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
			$view->url			= $url;
			$view->msg 			= isset($this->_msg) ? $this->_msg : '';
			if ($this->getError())
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}
		else
		{
			// Set counts
			$moved  = array();
			$failed = 0;
			$sync 	= 0;

			// cd
			chdir($this->_path);

			// Get new path
			$newpath = trim(urldecode(Request::getVar('newpath', '')), DS);

			// New directory to be created?
			$newdir = Request::getVar('newdir', '');

			// Clean up directory name
			if ($newdir)
			{
				$newdir = stripslashes($newdir);
				$newdir = \Components\Projects\Helpers\Html::makeSafeDir($newdir);
				$newdir = $this->subdir ? $this->subdir . DS . $newdir : $newdir;
			}
			if ($newdir && !file_exists( $this->_path . DS . $newdir ))
			{
				// Create new directory
				if (!\JFolder::create( $this->_path . DS . $newdir ))
				{
					$this->setError( Lang::txt('PLG_PROJECTS_FILES_UNABLE_TO_CREATE_UPLOAD_PATH') );
				}
			}

			// Process request
			if (($newpath != $this->subdir || $newdir) && !$this->getError())
			{
				foreach ($items as $element)
				{
					foreach ($element as $type => $item)
					{
						// Get type and item name
					}

					// Must have a name
					if (trim($item) == '')
					{
						continue;
					}

					// Include subdir
					$from = $this->subdir ? $this->subdir . DS . $item : $item;

					// Set new path
					if ($newdir)
					{
						$where = $newdir . DS . $item;
					}
					else
					{
						$where = $newpath ? $newpath . DS . $item : $item;
					}

					if ($this->_git->gitMove($from, $where, $type, $commitMsg))
					{
						$moved[] = $where;
					}
				}
			}
			elseif (!$this->getError())
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_NEW_FILE_LOCATION'));
			}

			// After successful move actions
			if (!$this->getError())
			{
				// Delete original directory if empty
				if ($this->subdir && file_exists( $this->_path . DS . $this->subdir))
				{
					$contents = scandir($this->_path. DS . $this->subdir);
					if (count($contents) <= 2)
					{
						\JFolder::delete($this->_path. DS . $this->subdir);
					}
				}
			}

			// Success or failure message
			if ($moved)
			{
				// Commit changes
				$this->_git->gitCommit($commitMsg);

				// Force sync
				$sync = 1;

				// Output message
				$this->_msg = Lang::txt('PLG_PROJECTS_FILES_MOVED'). ' '
					. count($moved) . ' ' . Lang::txt('PLG_PROJECTS_FILES_S');
			}
			elseif (empty($moved))
			{
				$this->setError( Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_NEW_FILE_LOCATION') );
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
			$url .= $this->subdir ? '?subdir=' . urlencode($this->subdir) : '';

			if ($sync && $this->repo->isLocal())
			{
				$this->model->saveParam('google_sync_queue', 1);
			}

			$this->_referer = $url;
			return;
		}
	}

	/**
	 * Send file back or from to remote service for remote editing
	 *
	 * @return     void, redirect
	 */
	protected function _share()
	{
		// Incoming
		$converted  = Request::getInt('converted', 0);
		$service 	= Request::getVar('service', 'google');

		// Combine file and folder data
		$items = $this->_sortIncoming();

		if (empty($items))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_SHARE'));
		}

		if (empty($this->_rServices))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_REMOTE_NOT_ENABLED'));
		}

		$type 	= key($items[0]);
		$file 	= $items[0][$type];
		$remote = NULL;
		$fpath 	= $this->subdir ? $this->subdir. DS . $file : $file;
		$shared = array();
		$sync 	= 0;

		// Are we syncing project home directory or other?
		$localDir   = $this->_connect->getConfigParam($service, 'local_dir');
		$localDir   = $localDir == '#home' ? '' : $localDir;

		$localPath  =  $this->_path;
		$localPath .= $localDir ? DS . $localDir : '';

		// Check for remote connection
		if (!empty($this->_rServices) && $this->repo->isLocal())
		{
			foreach ($this->_rServices as $servicename)
			{
				// Get stored remote connection to file
				$remote = $this->_getRemoteConnection($fpath, '', $servicename, $converted);
				if ($remote)
				{
					// Check user is connected
					$connected = $this->_connect->getStoredParam($servicename . '_token', $this->_uid);
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

					break;
				}
			}
		}

		if (!$remote)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SHARING_NO_REMOTE'));
		}

		// Confirmation screen
		if ($this->_task == 'share')
		{
			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'=>'projects',
					'element'=>'files',
					'name'=>'share'
				)
			);

			$view->type 		= $type;
			$view->item 		= $file;
			$view->remote		= $remote;
			$view->connect		= $this->_connect;
			$view->services		= $this->_rServices;
			$view->option 		= $this->_option;
			$view->model 		= $this->model;
			$view->repo    		= $this->repo;
			$view->uid 			= $this->_uid;
			$view->subdir 		= $this->subdir;
			$view->path 		= $this->_path;
			$view->msg 			= isset($this->_msg) ? $this->_msg : '';

			if ($this->getError())
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}

		// Send file for remote editing on Google
		if ($this->_task == 'shareit' && !$this->getError() && $service == 'google')
		{
			// Get file extention
			$ext   = \Components\Projects\Helpers\Html::getFileExtension($file);
			$title = $file;

			// Get convertable formats
			$formats = \Components\Projects\Helpers\Google::getGoogleConversionExts();

			// Import remote file
			if ($remote['converted'] == 1)
			{
				// Load remote resource
				$resource = $this->_connect->loadRemoteResource($service, $this->_uid, $remote['id']);

				if (!$resource)
				{
					$this->setError(Lang::txt('PLG_PROJECTS_FILES_SHARING_NO_REMOTE'));
				}
				else
				{
					$originalPath   = $remote['original_path'];
					$originalFormat = $remote['original_format'];

					// Incoming
					$importExt 	= Request::getVar('format', 'pdf', 'post');

					// Remove Google native extension from title
					if (in_array($ext, array('gdoc', 'gsheet', 'gslides', 'gdraw')))
					{
						$title = preg_replace("/." . $ext . "\z/", "", $title);
					}

					// Do we have extention in name already? - take it out
					$n_parts = explode('.', $title);
					$n_ext   = count($n_parts) > 1 ? array_pop($n_parts) : '';
					$title   = implode($n_parts);
					$title 	.= '.' . $importExt;

					$newpath = $this->subdir ? $this->subdir. DS . $title : $title;

					// Do we have original file present?
					if ($originalPath && file_exists($localPath . DS . $originalPath))
					{
						// Rename in Git?
						if (basename($originalPath) != $title)
						{
							// TBD
						}
					}

					// Replacing file?
					$exists = file_exists($this->_path. DS . $newpath) ? 1 : 0;

					// Download remote file
					if ($this->_connect->importFile($service, $this->_uid, $resource,
						$newpath, $localPath, $importExt ))
					{
						// Git add & commit
						$commitMsg = Lang::txt('PLG_PROJECTS_FILES_SHARE_IMPORTED') . "\n";
						$this->_git->gitAdd($newpath, $commitMsg);
						$this->_git->gitCommit($commitMsg);

						$mTypeParts = explode(';', $this->mt->getMimeType($this->_path. DS . $newpath));

						// Get local file information
						$local = array(
							'local_path' => $newpath,
							'title'		 => $title,
							'fullPath'   => $localPath . DS . $newpath,
							'mimeType'	 => $mTypeParts[0],
							'md5'	 	 => ''
						);

						// Remove remote resource
						$deleted = $this->_connect->deleteRemoteItem(
							$this->model->get('id'), $service, $this->_uid,
							$remote['id'], false
						);

						// Create remote file for imported file
						$created = '';
						if (!$exists)
						{
							$created = $this->_connect->addRemoteFile(
								$this->model->get('id'), $service, $this->_uid,
								$local,  $remote['parent']
							);
						}

						// Update connection record
						$this->_connect->savePairing(
							$this->model->get('id'), $service, $created,
							$newpath, $remote['record_id'], $originalPath, $originalFormat, $remote['id']
						);
					}

					// Output message
					$this->_msg = Lang::txt('PLG_PROJECTS_FILES_UNSHARE_SUCCESS') . ' ' . $title;

					// Force sync
					$sync = 1;
				}
			}
			// Export local file
			else
			{
				// Check that local file exists
				if (!file_exists($localPath . DS . $remote['fpath']))
				{
					$this->setError(Lang::txt('PLG_PROJECTS_FILES_SHARING_LOCAL_FILE_MISSING'));
				}

				$mTypeParts = explode(';', $this->mt->getMimeType($localPath . DS . $remote['fpath']));
				$mimeType = $mTypeParts[0];

				// LaTeX?
				$tex = Components\Projects\Helpers\Compiler::isTexFile($file, $mimeType);

				// Check format
				if (!in_array($ext, $formats) && !$tex)
				{
					$this->setError(Lang::txt('PLG_PROJECTS_FILES_SHARING_NOT_CONVERTABLE'));
				}

				if (!$this->getError())
				{
					if ($tex)
					{
						// LaTeX? Convert to text file first
						$mimeType = 'text/plain';
					}
					if ($ext == 'wmf')
					{
						// WMF files need this mime type specified for conversion to Google drawing
						$mimeType = 'application/x-msmetafile';
					}
					if ($ext == 'ppt' || $ext == 'pps' || $ext == 'pptx')
					{
						$mimeType = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
					}

					// Get local file information
					$local = array(
						'local_path' => $remote['fpath'],
						'title'		 => $title,
						'fullPath'   => $localPath . DS . $remote['fpath'],
						'mimeType'	 => $mimeType,
						'md5'	 	 => ''
					);

					// Convert file
					$added = $this->_connect->addRemoteFile(
						$this->model->get('id'), $service, $this->_uid,
						$local, $remote['parent'], true
					);

					if ($added)
					{
						$shared[] = $added;

						// Remove original local file
						$commitMsg = Lang::txt('PLG_PROJECTS_FILES_SHARE_EXPORTED') . "\n";
						$deleted = $this->_git->gitDelete($remote['fpath'], 'file', $commitMsg);
						$this->_git->gitCommit($commitMsg);

						// Remove original remote file
						$deleted = $this->_connect->deleteRemoteItem(
							$this->model->get('id'), $service, $this->_uid,
							$remote['id'], false
						);

						$mTypeParts = explode(';', $this->mt->getMimeType( $localPath . DS . $remote['fpath']));
						$mimeType = $mTypeParts[0];

						// Update connection record
						$this->_connect->savePairing(
							$this->model->get('id'), $service, $added, '', $remote['record_id'],
							$remote['fpath'], $mimeType, $remote['id']
						);

						// Output message
						$this->_msg = Lang::txt('PLG_PROJECTS_FILES_SHARE_SUCCESS');

						// Force sync
						$sync = 1;
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
		$url  = Route::url('index.php?option=' . $this->_option
			. '&alias=' . $this->model->get('alias') . '&active=files');
		$url .= $this->subdir ? '?subdir=' . urlencode($this->subdir) : '';

		if ($sync && $this->repo->isLocal())
		{
			$this->model->saveParam('google_sync_queue', 1);
		}

		$this->_referer = $url;
		return;
	}

	/**
	 * Show revision diffs
	 *
	 * @return     void, redirect
	 */
	protected function _diff()
	{
		// Incoming
		$old 	 = urldecode(Request::getVar( 'old', ''));
		$new 	 = urldecode(Request::getVar( 'new', ''));
		$mode 	 = Request::getVar( 'mode', $this->params->get('diffmode', 'side-by-side'));
		$file 	 = urldecode(Request::getVar( 'file', ''));
		$full 	 = Request::getInt( 'full');

		$remote 		= NULL;
		$service		= NULL;
		$connected 		= false;

		$nParts = explode('@', $new);
		$oParts = explode('@', $old);
		$diff	= NULL;

		$fpath = $this->subdir ? $this->subdir. DS . $file : $file;

		// Binary file?
		$binary	= \Components\Projects\Helpers\Html::isBinary($this->_path . DS . $fpath);

		// Do some checks
		if (count($nParts) <= 2 || count($oParts) <= 2)
		{
			$fpath = NULL;
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_DIFF_NO_CONTENT'));
		}
		elseif (!$file)
		{
			$fpath = NULL;
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_SHOW_HISTORY'));
		}
		elseif ($binary)
		{
			$fpath = NULL;
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_DIFF_BINARY'));
		}
		else
		{
			$new = array('rev' => $nParts[0], 'hash' => $nParts[1], 'fpath' => $nParts[2], 'val' => urlencode($new) );
			$old = array('rev' => $oParts[0], 'hash' => $oParts[1], 'fpath' => $oParts[2], 'val' => urlencode($old) );

			// Check for remote connection
			if (!empty($this->_rServices) && $this->repo->isLocal())
			{
				foreach ($this->_rServices as $servicename)
				{
					// Get stored remote connection to file
					$remote = $this->_getRemoteConnection($fpath, '', $servicename);
					if ($remote)
					{
						$service   = $servicename;
						$connected = $this->_connect->getStoredParam($servicename . '_token', $this->_uid);
						break;
					}
				}
			}

			// Get text blobs
			$old['text'] = $this->_git->gitLog($old['fpath'], $old['hash'], 'blob');
			$new['text'] = $this->_git->gitLog($new['fpath'], $new['hash'], 'blob');

			// Diff class
			include_once( PATH_CORE . DS . 'plugins' . DS . 'projects' . DS
				. 'files' . DS . 'php-diff' . DS . 'Diff.php' );

			$context = ($old['text'] == $new['text'] || $full == 1) ? count($old['text']) : 10;
			$options = array(
				'context' => $context
			);

			// Run diff
			$objDiff = new Diff($old['text'], $new['text'], $options );

			if ($mode == 'side-by-side')
			{
				include_once( PATH_CORE . DS . 'plugins' . DS . 'projects' . DS . 'files'
					. DS . 'php-diff' . DS . 'Diff' . DS . 'Renderer' . DS . 'Html' . DS . 'hubSideBySide.php' );

				// Generate a side by side diff
				$renderer = new Diff_Renderer_Html_SideBySide;
				$diff = $objDiff->Render($renderer);
			}
			elseif ($mode == 'inline')
			{
				include_once( PATH_CORE . DS . 'plugins' . DS . 'projects' . DS . 'files'
					. DS . 'php-diff' . DS . 'Diff' . DS . 'Renderer' . DS . 'Html' . DS . 'hubInline.php' );

				// Generate inline diff
				$renderer = new Diff_Renderer_Html_Inline;
				$diff = $objDiff->Render($renderer);
			}
			else
			{
				// Print git diff
				$mode = 'git';
				$diff = $this->_git->gitDiff($old, $new);

				if (is_array($diff))
				{
					$diff = implode("\n", $diff);
				}
			}
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=> 'projects',
				'element'	=> 'files',
				'name'		=> 'history',
				'layout' 	=> 'diff'
			)
		);

		$view->url 	        = Route::url($this->_route . '&active=files');
		$view->config		= $this->model->config();
		$view->file 		= $file;
		$view->fpath 		= $fpath;
		$view->option 		= $this->_option;
		$view->model 		= $this->model;
		$view->repo    		= $this->repo;
		$view->uid 			= $this->_uid;
		$view->title		= $this->_area['title'];
		$view->subdir 		= $this->subdir;
		$view->ajax			= 0;
		$view->connected	= $connected;
		$view->remote		= $remote;
		$view->new			= $new;
		$view->old			= $old;
		$view->diff			= $diff;
		$view->mode			= $mode;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		$view->msg = isset($this->_msg) ? $this->_msg : '';
		return $view->loadTemplate();

	}

	/**
	 * Show file history
	 *
	 * @return     void, redirect
	 */
	protected function _history()
	{
		// Clean incoming data
		$this->_cleanData();

		// Incoming
		$checked = Request::getVar( 'asset', '', 'request', 'array' );
		$ajax 	 = Request::getInt('ajax', 0);

		// Can only view history of one file at a time
		if (empty($checked) or $checked[0] == '')
		{
			$file = urldecode(Request::getVar( 'asset', ''));
		}
		else
		{
			$file = urldecode($checked[0]);
		}

		// Collective vars
		$versions 		= array();
		$timestamps 	= array();
		$local 	 		= NULL;
		$remote 		= NULL;
		$service		= NULL;
		$connected 		= false;

		// Make sure we have a file to work with
		if (!$file)
		{
			$fpath = NULL;
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_SHOW_HISTORY'));
		}
		else
		{
			$fpath = $this->subdir ? $this->subdir. DS . $file : $file;

			// Check for remote connection
			if (!empty($this->_rServices) && $this->repo->isLocal())
			{
				foreach ($this->_rServices as $servicename)
				{
					// Get stored remote connection to file
					$remote = $this->_getRemoteConnection($fpath, '', $servicename);
					if ($remote)
					{
						$service   = $servicename;
						$connected = $this->_connect->getStoredParam($servicename . '_token', $this->_uid);
						break;
					}
				}
			}

			// Should history be paired with another file?
			$local_path = NULL;
			if ($remote && $remote['original_path'] && $remote['original_path'] != $fpath )
			{
				$local_path = $remote['original_path'];
			}

			// Local file present?
			if (file_exists( $this->_path . DS . $fpath))
			{
				$this->_git->sortLocalRevisions($fpath, $versions, $timestamps);
			}
			if ($local_path && $local_path != $fpath)
			{
				$this->_git->sortLocalRevisions($local_path, $versions, $timestamps, 1);
			}

			// Get remote revision history
			if ($remote && $remote['converted'] == 1)
			{
				$this->_connect->sortRemoteRevisions($remote['id'], $remote['converted'], $remote['author'],
					$this->_uid, $service, $file, $versions, $timestamps);
			}
			elseif ($remote && $remote['original_id'])
			{
				$this->_connect->sortRemoteRevisions($remote['original_id'], 0, '', $this->_uid, $service,
					$file, $versions, $timestamps, 1);
			}

			// Sort by time, most recent first
			array_multisort($timestamps, SORT_DESC, $versions);
		}

		// Get status for each version
		$versions = $this->_git->getVersionStatus($versions);

		// Get file previews
		$i = 0;
		foreach ($versions as $v)
		{
			$pr   		= $v['remote']  ? array('id' => $v['remote'],
						'modified' => gmdate('Y-m-d H:i:s', strtotime($v['date']))) : NULL;
			$hash 		= $v['remote'] ? NULL : $v['hash'];
			$preview 	= $this->getFilePreview($v['file'], $hash, $this->_path, $this->subdir, $pr);

			if ($preview)
			{
				$versions[$i]['preview'] = Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=media&media=' . basename($preview));
			}
			$i++;
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

		// Binary file?
		$view->binary		= \Components\Projects\Helpers\Html::isBinary($this->_path . DS . $fpath);

		$view->versions 	= $versions;
		$view->path 		= $this->_path;
		$view->file 		= $file;
		$view->fpath 		= $fpath;
		$view->option 		= $this->_option;
		$view->model 		= $this->model;
		$view->repo    		= $this->repo;
		$view->uid 			= $this->_uid;
		$view->ajax			= $ajax;
		$view->title		= $this->_area['title'];
		$view->subdir 		= $this->subdir;
		$view->remote		= $remote;
		$view->connected	= $connected;
		$view->config		= $this->model->config();

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
		// Incoming
		$file 	= urldecode(Request::getVar( 'asset', ''));
		$hash 	= Request::getVar('hash', '');

		// Make sure we have a file to work with
		if (!$file)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_RESTORE_NO_FILE_SELECTED'));
		}
		elseif (!is_file( $this->_path . DS . $file ))
		{
			// Checkout pre-delete revision
			$this->_git->gitCheckout( $file, $hash . '^ ' );

			// If restored
			if (is_file( $this->_path . DS . $file))
			{
				// Git add & commit
				$commitMsg = Lang::txt('PLG_PROJECTS_FILES_RESTORE_COMMIT_MESSAGE') . "\n";
				$this->_git->gitAdd($file, $commitMsg, $new = false);
				$this->_git->gitCommit($commitMsg);

				// Store in session
				$this->registerUpdate('restored', $file, false);
			}
			else
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_RESTORE_FAILED'));
			}
		}

		// After successful action
		if (!$this->getError())
		{
			// Force sync
			if ($this->repo->isLocal())
			{
				$this->model->saveParam('google_sync_queue', 1);
			}
		}
		else
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}

		// Redirect to file list
		$url 	= Route::url($this->_route . '&active=files');
		$url .= $this->subdir ? '?subdir=' . urlencode($this->subdir) : '';

		$this->_referer = $url;
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
		$render 	= Request::getVar('render', 'download');
		$items 		= $this->_sortIncoming();
		$file 	 	= trim(urldecode(Request::getVar('file', '')), DS);
		$multifile	= 0;
		$deleteTemp = 0;
		$remote 	= NULL;
		$revision 	= Request::getVar('revision', '');

		if (!$file)
		{
			if (empty($items))
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_SELECTIONS_TO_DOWNLOAD'));
			}
			elseif (count($items) == 1)
			{
				foreach ($items as $element)
				{
					foreach ($element as $type => $item)
					{
						if ($type == 'file')
						{
							$file = $item;
						}
					}
				}
			}
			elseif ($render == 'download')
			{
				// Multi-file download
				$multifile = 1;
				$archive = $this->_archiveFiles($items, $this->_path, $this->subdir);

				if (!$archive)
				{
					$this->setError($this->getError() . ' ' .Lang::txt('PLG_PROJECTS_FILES_ARCHIVE_ERROR'));
				}
			}
		}

		// Build file path and check for remote connection
		if ($file)
		{
			$fpath = $this->subdir ? $this->subdir. DS . $file : $file;
			// Check for remote connection
			if (!empty($this->_rServices) && $this->repo->isLocal())
			{
				foreach ($this->_rServices as $servicename)
				{
					// Get stored remote connection to file
					$remote = $this->_getRemoteConnection($fpath, '', $servicename);

					if ($remote)
					{
						break;
					}
				}
			}
		}

		// Are we previewing or downloading?
		if (($render == 'thumb' || $render == 'inline' || $render == 'medium') && $file
			&& file_exists($this->_path . DS . $fpath))
		{
			$hash   = ($remote && $remote['converted'] == 1) ? ''
					: $this->_git->gitLog($fpath, '' , 'hash');
			$medium = $render == 'medium' ? true : false;
			$image  = ($render == 'thumb' || $render == 'medium')
					? $this->getFilePreview($file, $hash, $this->_path, $this->subdir, $remote, $medium)
					: $this->_path . DS . $fpath;
			$image = ($render == 'thumb' || $render == 'medium') ? PATH_APP . $image : $image;

			// Serve image
			if ($image && file_exists($image))
			{
				$xserver = new \Hubzero\Content\Server();
				$xserver->filename($image);
				$xserver->serve_inline($image);
				exit;
			}
		}
		elseif ($render == 'preview')
		{
			$content  = '';
			$image	  = '';
			$hash     = '';
			$ok 	  = 1;
			$filesize = 0;

			// Need a file to preview
			if (!$file)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_FILE_INFO_NOT_FOUND'));

				// Output error
				$view = new \Hubzero\Plugin\View(
					array(
						'folder'=>'projects',
						'element'=>'files',
						'name'=>'error'
					)
				);

				$view->title  = '';
				$view->option = $this->_option;
				$view->setError( $this->getError() );
				return $view->loadTemplate();
			}

			// Need file in working tree
			if ((!$remote || $remote['converted'] == 0) && !file_exists($this->_path . DS . $fpath))
			{
				$ok = 0;
			}

			// Get file extention
			$ext = \Components\Projects\Helpers\Html::getFileExtension($fpath);

			if ((!$remote || $remote['converted'] == 0) && $ok == 1)
			{
				// Get git object
				$hash  	  =  $this->_git->gitLog($fpath, '' , 'hash');
				$filesize =  $this->_git->gitLog($fpath, '' , 'size');
			}

			// Get image preview
			if (!$this->getError() && $ok == 1)
			{
				$image = $this->getFilePreview($file, $hash, $this->_path, $this->subdir, $remote);
			}

			if ((!$remote || $remote['converted'] == 0) && $ok == 1)
			{
				$binary = \Components\Projects\Helpers\Html::isBinary($this->_path . DS . $fpath);

				// If non-binary and below 10MB
				if (!$binary && $filesize <= 10485760)
				{
					$content = $this->_git->showTextContent($fpath, 100);
					$content = $content ? \Components\Projects\Helpers\Html::shortenText($content, 200) : '';
				}
			}

			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'	=>'projects',
					'element'	=>'files',
					'name'		=>'preview'
				)
			);

			$view->image 		= $image;
			$view->ext 			= isset($ext) ? $ext : NULL;
			$view->title 		= $file;
			$view->content 		= $content;
			$view->option 		= $this->_option;
			$view->filesize		= isset($filesize) ? \Hubzero\Utility\Number::formatBytes($filesize) : NULL;
			$view->remote		= $remote;
			$view->model		= $this->model;

			if ($this->getError())
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}
		elseif (!$this->getError())
		{
			// Which revision are we downloading?
			$hash 	  = Request::getVar('hash', '');
			$serveas  = basename($file);

			// Multiple files selected
			if ($multifile)
			{
				$fullpath 	= $archive['path'];
				$file  		= $archive['name'];
				$serveas	= 'Project Files ' . Date::toSql() . '.zip';
				$deleteTemp = 1;
			}
			else
			{
				// Open converted file
				if ($remote && $this->_task == 'open')
				{
					// Is user connected?
					$connected = $this->_connect->getStoredParam($remote['service'] . '_token', $this->_uid);

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
					$resource = $this->_connect->loadRemoteResource($remote['service'],
						$this->model->get('owned_by_user'), $remote['id']);

					$openLink = $resource && isset($resource['alternateLink']) ? $resource['alternateLink'] : '';

					if (!$openLink)
					{
						// Throw error
						throw new Exception(Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND') . ' ' . $file, 404 );
						return;
					}
					$this->_referer = $openLink;
					return;
				}

				// Import & download converted file
				if ($remote && $remote['converted'] == 1 && $remote['service'] == 'google')
				{
					$temp_path = sys_get_temp_dir();

					// Load remote resource
					$this->_connect->setUser($this->model->get('owned_by_user'));
					$resource = $this->_connect->loadRemoteResource($remote['service'],
						$this->model->get('owned_by_user'), $remote['id']);

					// Tex file?
					$tex    = Components\Projects\Helpers\Compiler::isTexFile($remote['title'], $remote['original_format']);

					$cExt   = $tex ? 'tex' : \Components\Projects\Helpers\Google::getGoogleImportExt($resource['mimeType']);
					$url    = \Components\Projects\Helpers\Google::getDownloadUrl($resource, $cExt);

					$data = $this->_connect->sendHttpRequest($remote['service'], $this->model->get('owned_by_user'), $url);

					// Clean up data from Windows characters - important!
					$data = $tex ? preg_replace('/[^(\x20-\x7F)\x0A]*/','', $data) : $data;

					$ftname = \Components\Projects\Helpers\Google::getImportFilename($remote, $cExt);

					$this->_connect->fetchFile($data, $ftname, $temp_path);
					$fullpath = $temp_path . DS . $ftname;

					// Delete temp file after download
					$deleteTemp = 1;
				}
				// Download local revision
				elseif ($hash)
				{
					// Viewing revisions
					$parts = explode('/', $file);
					$serveas = trim(end($parts));

					$temppath = 'temp-' . \Components\Projects\Helpers\Html::generateCode (4 ,4 ,0 ,1 ,0 ) . $serveas;
					$fullpath = $this->_path . DS .$temppath;

					// Get file content
					$this->_git->getContent($file, $hash, $temppath);

					// Delete temp file after download
					$deleteTemp = 1;
				}
				else
				{
					// Viewing current file
					$fpath 		= $this->subdir ? $this->subdir. DS . $file : $file;
					$serveas 	= urldecode(Request::getVar('serveas', $file));
					$fullpath	= $this->_path . DS . $fpath;
				}
			}

			// Ensure the file exist
			if (!file_exists($fullpath))
			{
				// Throw error
				throw new Exception(Lang::txt('PLG_PROJECTS_FILES_FILE_NOT_FOUND'), 404);
				return;
			}

			// Cannot download zero byte files
			if (filesize($fullpath) == 0)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_ZERO_BYTE'));
				if ($deleteTemp)
				{
					// Delete downloaded temp file
					\JFile::delete($fullpath);
				}
			}

			// Proceed with download
			if (!$this->getError())
			{
				// Initiate a new content server and serve up the file
				$xserver = new \Hubzero\Content\Server();
				$xserver->filename($fullpath);
				$xserver->disposition('attachment');
				$xserver->acceptranges(false);
				$xserver->saveas($serveas);
				$result = $xserver->serve_attachment($fullpath, $serveas, false);

				if ($deleteTemp)
				{
					// Delete downloaded temp file
					\JFile::delete($fullpath);
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
		$url  = Route::url($this->_route . '&active=files');
		$url .= $this->subdir ? '?subdir=' . urlencode($this->subdir) : '';

		$this->_referer = $url;
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
		// Clean incoming data
		$this->_cleanData();

		// Incoming
		$checked 	= Request::getVar( 'asset', '', 'request', 'array' );
		$commit  	= Request::getInt( 'commit', 0 );
		$download  	= Request::getInt( 'download', 0 );

		if (!$this->params->get('latex'))
		{
			$this->setError( Lang::txt('PLG_PROJECTS_FILES_COMPILE_NOTALOWWED') );
			return;
		}

		// Can only view history of one file at a time
		if (empty($checked) or $checked[0] == '')
		{
			$file = urldecode(Request::getVar( 'file', ''));
		}
		else
		{
			$file = urldecode($checked[0]);
		}

		// Path for storing temp previews
		$imagepath = trim($this->model->config()->get('imagepath', '/site/projects'), DS);
		$outputDir = DS . $imagepath . DS . strtolower($this->model->get('alias')) . DS . 'compiled';

		// Make sure output dir exists
		if (!is_dir( PATH_APP . DS . $outputDir ))
		{
			if (!\JFolder::create( PATH_APP . DS . $outputDir ))
			{
				$this->setError( Lang::txt('PLG_PROJECTS_FILES_UNABLE_TO_CREATE_UPLOAD_PATH') );
				return;
			}
		}

		// Get LaTeX helper
		$compiler = new \Components\Projects\Helpers\Compiler();

		// Tex compiler path
		$texpath = DS . trim($this->params->get('texpath'), DS);

		$remote 	= NULL;
		$fpath 		= NULL;
		$content	= NULL;
		$filename 	= $file;
		$data 		= NULL;
		$tempBase 	= NULL;
		$log 		= NULL;
		$cType		= NULL;
		$cExt		= 'pdf';
		$ext 		= NULL;
		$tex		= NULL;
		$image		= NULL;
		$binary		= false;

		// Build URL
		$url 	= Route::url($this->_route . '&active=files');

		$formats = $compiler->getFormatsArray();

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'compiled'
			)
		);

		$view->oWidth = '780';
		$view->oHeight= '460';
		$view->url	  = $url;

		// Make sure we have a file to work with
		if (!$file)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_COMPILE'));
		}
		else
		{
			// Get file extention
			$ext   = \Components\Projects\Helpers\Html::getFileExtension($file);

			// Take out Google native extension
			$native = \Components\Projects\Helpers\Google::getGoogleNativeExts();
			if (in_array($ext, $native))
			{
				$filename = preg_replace("/.".$ext."\z/", "", $file);
			}

			$mTypeParts = explode(';', $this->mt->getMimeType($filename));
			$cType = $mTypeParts[0];

			// Include subdir in path
			$fpath = $this->subdir ? $this->subdir. DS . $file : $file;

			// Binary?
			$binary = \Components\Projects\Helpers\Html::isBinary($this->_path . DS . $fpath);

			// Check for remote connection
			if (!empty($this->_rServices) && $this->repo->isLocal())
			{
				foreach ($this->_rServices as $servicename)
				{
					// Get stored remote connection to file
					$remote = $this->_getRemoteConnection($fpath, '', $servicename);

					if ($remote)
					{
						break;
					}
				}
			}

			// Tex file?
			$tex = $compiler->isTexFile($filename);

			// Get file contents
			if ($remote && $remote['service'] == 'google' && $remote['converted'] == 1)
			{
				// Load remote resource
				$this->_connect->setUser($this->model->get('owned_by_user'));
				$resource = $this->_connect->loadRemoteResource($remote['service'], $$this->model->get('owned_by_user'), $remote['id']);

				$cExt   = $tex ? 'tex' : \Components\Projects\Helpers\Google::getGoogleImportExt($resource['mimeType']);
				$cExt  	= in_array($cExt, array('tex', 'jpeg')) ? $cExt : 'pdf';
				$url    = \Components\Projects\Helpers\Google::getDownloadUrl($resource, $cExt);

				// Get data
				$data = $this->_connect->sendHttpRequest($remote['service'], $this->model->get('owned_by_user'), $url);
			}
			elseif (file_exists($this->_path . DS . $fpath))
			{
				$data = file_get_contents($this->_path . DS . $fpath);
			}
			else
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_COMPILE_NO_DATA'));
			}

			// Build temp name
			$tempBase = $tex ? 'temp__' . \Components\Projects\Helpers\Html::takeOutExt($filename) : $filename;
			$tempBase = str_replace(' ', '_', $tempBase);

			// LaTeX file?
			if ($tex)
			{
				// Clean up data from Windows characters - important!
				$data = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $data);

				// Compile and get path to PDF
				$content = $compiler->compileTex ($this->_path . DS . $fpath,
					$data, $texpath, PATH_APP . $outputDir, 1, $tempBase);

				// Read log (to show in case of error)
				$logFile = $tempBase . '.log';
				if (file_exists(PATH_APP . $outputDir . DS . $logFile ))
				{
					$log = $this->_readFile(PATH_APP . $outputDir . DS . $logFile, '', true);
				}

				if (!$content)
				{
					$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_COMPILE_TEX_FAILED'));
				}
			}
			elseif ($remote && $remote['converted'] == 1)
			{
				$tempBase = \Components\Projects\Helpers\Google::getImportFilename($remote, $cExt);

				// Write content to temp file
				$this->_connect->fetchFile($data, $tempBase, PATH_APP . $outputDir);
				$content = $tempBase;
			}
			// Local file
			elseif (!$this->getError() && $data)
			{
				// Make sure we can handle preview of this type of file
				if ($ext == 'pdf' || in_array($cType, $formats['images']) || !$binary)
				{
					\JFile::copy($this->_path . DS . $fpath, PATH_APP . $outputDir . DS . $tempBase);
					$content = $tempBase;
				}
			}
		}

		if ($content && file_exists(PATH_APP . $outputDir . DS . $content))
		{
			$mTypeParts = explode(';', $this->mt->getMimeType(PATH_APP . $outputDir . DS . $content));
			$cType = $mTypeParts[0];

			// Fix up object width & height
			if (in_array($cType, $formats['images']))
			{
				list($width, $height, $type, $attr) = getimagesize(PATH_APP . $outputDir . DS . $content);

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
				$pdfName = $tex ? str_replace('temp__', '', basename($content)) : basename($content);

				// Serve up file
				$xserver = new \Hubzero\Content\Server();
				$xserver->filename(PATH_APP . $outputDir . DS . $content);
				$xserver->disposition('attachment');
				$xserver->acceptranges(false);
				$xserver->saveas($pdfName);
				$result = $xserver->serve_attachment(PATH_APP . $outputDir . DS . $content, $pdfName, false);

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
				$pdfName = str_replace('temp__', '', basename($content));
				$where 	 = $this->subdir ? $this->subdir. DS . $pdfName : $pdfName;

				if (\JFile::copy(PATH_APP . $outputDir . DS . $content, $this->_path . DS . $where))
				{
					// Git add & commit
					$commitMsg = Lang::txt('PLG_PROJECTS_FILES_COMPILED_COMMITTED') . "\n";
					$this->_git->gitAdd($where, $commitMsg);
					$this->_git->gitCommit($commitMsg);

					if ($this->repo->isLocal())
					{
						$this->model->saveParam('google_sync_queue', 1);
					}

					$this->_message = array(
						'message' => Lang::txt('PLG_PROJECTS_FILES_SUCCESS_COMPILED'),
						'type'    => 'success'
					);

					$url .= $this->subdir ? '?subdir=' . urlencode($this->subdir) : '';

					// Redirect to file list
					$this->_referer = $url;
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

					$pdfName 	= $tex ? str_replace('temp__', '', basename($content)) : basename($content);
					$pdfPath 	= PATH_APP . $outputDir . DS . $content;
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

		$view->item 		= $file;
		$view->outputDir	= $outputDir;
		$view->log			= $log;
		$view->embed		= $content;
		$view->data			= $data;
		$view->cType		= $cType;
		$view->formats		= $formats;
		$view->ext			= $ext;
		$view->remote		= $remote;
		$view->subdir 		= $this->subdir;
		$view->option 		= $this->_option;
		$view->image		= $image;
		$view->model		= $this->model;
		$view->repo    		= $this->repo;
		$view->binary		= is_file ( PATH_APP . $outputDir . DS . $content )
							? \Components\Projects\Helpers\Html::isBinary(PATH_APP . $outputDir . DS . $content)
							: $binary;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}

		return $view->loadTemplate();
	}

	// REMOTE SERVICES
	// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

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

	/**
	 * Get stored connection to remote file
	 *
	 *
	 * @return     array or false
	 */
	private function _getRemoteConnection($local_path = '', $id = 0, $service = '', $converted = 'na')
	{
		// Get remote connection
		if (!isset($this->_remoteObj))
		{
			$this->_remoteObj = new \Components\Projects\Tables\RemoteFile ($this->_database);
		}

		$remote   = $this->_remoteObj->getConnection(
			$this->model->get('id'),
			$id,
			$service,
			$local_path,
			$converted
		);

		return $remote;
	}

	// SUPPORTING FUNCTIONS

	/**
	 * Get file preview
	 *
	 * @param      string	$file
	 * @param      string  	$hash
	 * @param      object  	$remote
	 * @param      string  	$path
	 * @param      string  	$subdir
	 *
	 * @return     array or false
	 */
	public function getFilePreview(
		$file, $hash, $path = '', $subdir = '',
		$remote = NULL, $medium = false, $to_path = NULL,
		$hashed = NULL, $width = 180, $height = 180
	)
	{
		$image = NULL;

		$rthumb	= NULL;
		if ($remote)
		{
			$rthumb = substr($remote['id'], 0, 20) . '_' . strtotime($remote['modified']) . '.png';
		}
		$hash  	= $hash ? substr($hash, 0, 10) : '';

		if (!$hashed)
		{
			$filename = basename($file);
			$hashed = $hash ? \Components\Projects\Helpers\Html::createThumbName($filename, '-' . $hash, 'png') : NULL;
			$hashed = $medium ? md5($filename . '-' . $hash) . '.png' : $hashed;
		}

		if (!$to_path)
		{
			$imagepath = trim($this->model->config()->get('imagepath', '/site/projects'), DS);
			$to_path = PATH_APP . DS . $imagepath . DS . strtolower($this->model->get('alias')) . DS . 'preview';
		}

		$from_path = $path . DS;
		$from_path = $subdir ? $from_path . $subdir . DS : $from_path;

		$maxWidth 	= $medium == true ? 600 : $width;
		$maxHeight 	= $medium == true ? 600 : $height;

		if ($hashed && is_file($to_path . DS . $hashed))
		{
			// First check locally generated thumbnail
			$image = str_replace(PATH_APP, '', $to_path . DS . $hashed);
		}
		elseif ($rthumb && is_file($to_path . DS . $rthumb))
		{
			// Check remotely generated thumbnail
			$image = str_replace(PATH_APP, '', $to_path . DS . $rthumb);

			// Copy this over as local thumb
			if ($hashed && \JFile::copy($to_path . DS . $rthumb, $to_path . DS . $hashed))
			{
				\JFile::delete($to_path . DS . $rthumb);
			}
		}
		elseif ($hashed)
		{
			// Generate thumbnail locally
			if (!file_exists( $to_path ))
			{
				\JFolder::create( $to_path );
			}

			// Get file extention
			$ext = \Components\Projects\Helpers\Html::getFileExtension($file);

			// Image formats
			$image_formats = array('png', 'gif', 'jpg', 'jpeg', 'tiff', 'bmp');

			// Make sure it's an image file
			if (!in_array(strtolower($ext), $image_formats) || !is_file($from_path. $file))
			{
				return false;
			}

			if (!\JFile::copy($from_path. $file, $to_path . DS . $hashed))
			{
				return false;
			}

			// Resize the image if necessary
			$hi = new \Hubzero\Image\Processor($to_path . DS . $hashed);
			$hi->resize($maxWidth, false, false, true);
			$hi->save($to_path . DS . $hashed);
			$image = str_replace(PATH_APP, '', $to_path . DS . $hashed);
		}

		return $image;
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
		$tarpath =  $base_path . DS . $tarname;

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
	 * Get Git helper
	 *
	 *
	 * @return     void
	 */
	protected function _getGitHelper()
	{
		if (!isset($this->_git))
		{
			$this->_git = new \Components\Projects\Helpers\Git($this->_path);
		}
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
		$view->files = $this->_git->listDeleted();

		$view->option 	= $this->_option;
		$view->model 	= $this->model;
		$view->ajax 	= Request::getInt('ajax', 0);

		// Build URL
		$view->url = Route::url($this->_route . '&active=files');
		$view->subdir = $this->subdir;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
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
		$obj = new \Components\Projects\Tables\Project( $this->_database );
		$obj->load($this->model->get('id'));
		$view->params = new \JParameter( $obj->params );

		// Get connection details for user
		$objO = new \Components\Projects\Tables\Owner( $this->_database );
		$objO->loadOwner ($this->model->get('id'), $this->_uid);
		$view->oparams = new \JParameter( $objO->params );

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

		$pparams = $this->model->params;

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
				$synced = $pparams->get($servicename . '_sync');

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

		// Clean up status
		$this->_writeToFile('');

		// Record sync status
		$this->_writeToFile(ucfirst($service) . ' '. Lang::txt('PLG_PROJECTS_FILES_SYNC_STARTED') );

		// Get time of last sync
		$obj = new \Components\Projects\Tables\Project( $this->_database );
		$obj->load($this->model->get('id'));
		$pparams = new \JParameter( $obj->params );
		$synced = $pparams->get($service . '_sync', 1);

		// Get disk usage
		$diskUsage = $this->repo->call('getDiskUsage',
			$params = array(
				'working' => true,
				'history' => $this->params->get('disk_usage')
			)
		);

		$quota 	   = $pparams->get('quota')
					? $pparams->get('quota')
					: \Components\Projects\Helpers\Html::convertSize( floatval($this->model->config()->get('defaultQuota', '1')), 'GB', 'b');
		$avail 	   = $quota - $diskUsage;

		// Last synced remote/local change
		$lastRemoteChange = $pparams->get($service . '_last_remote_change', NULL);
		$lastLocalChange  = $pparams->get($service . '_last_local_change', NULL);

		// Get last change ID for project creator
		$lastSyncId = $pparams->get($service . '_sync_id', NULL);
		$prevSyncId = $pparams->get($service . '_prev_sync_id', NULL);

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
										$local,  $parentId
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
									$remote['url'],
									$this->_path . DS . $remote['local_path'])
								)
								{
									// Git add & commit
									$this->_git->gitAdd($filename, $commitMsg);
									$this->_git->gitCommit($commitMsg, $author, $cDate);

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

					// Generate local preview
					$pr  = array('id' => $remote['remoteid'], 'modified' => $remote['modified']);
					$hash = $this->_git->gitLog($filename, '' , 'hash');
					$this->getFilePreview($filename, $hash, $this->_path, '', $pr);
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

		// Save sync time and last sync ID
		$obj = new \Components\Projects\Tables\Project( $this->_database );

		// Save sync time
		$obj->saveParam($this->model->get('id'), $service . '_sync', $endTime);

		// Save change id for next sync
		$obj->saveParam($this->model->get('id'), $service . '_sync_id', ($nextSyncId));
		$output .= 'Next sync ID: ' . $nextSyncId . "\n";

		$obj->saveParam($this->model->get('id'), $service . '_prev_sync_id', $lastSyncId);

		$output .= 'Saving last synced remote change at: ' . $lastRemoteChange . "\n";
		$obj->saveParam($this->model->get('id'), $service . '_last_remote_change', $lastRemoteChange);

		$output .= 'Saving last synced local change at: ' . $lastLocalChange . "\n";
		$obj->saveParam($this->model->get('id'), $service . '_last_local_change', $lastLocalChange);

		// Debug output
		$temp = $this->_logPath;
		$this->_writeToFile($output, $temp . DS . 'sync.' . \Date::format('Y-m') . '.log', true);

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
			$obj = new \Components\Projects\Tables\Project( $this->_database );
			$obj->load($pid);
			$pparams 	= new \JParameter( $obj->params );
			$synced 	= $pparams->get($service . '_sync');
			$syncLock 	= $pparams->get($service . '_sync_lock', '');

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
		$pparams 	= $this->model->params;
		$syncLock 	= $pparams->get($service . '_sync_lock', '');

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
		$obj = new \Components\Projects\Tables\Project( $this->_database );
		$obj->load($this->model->get('id'));

		$pparams 	= new \JParameter( $obj->params );
		$synced 	= $pparams->get($service . '_sync');
		$syncLock 	= $pparams->get($service . '_sync_lock');
		$syncQueue 	= $pparams->get($service . '_sync_queue', 0);

		// Request to unlock sync
		if ($unlock == true)
		{
			$obj->saveParam($this->model->get('id'), $service . '_sync_lock', '');
			$this->_rSync['status'] = 'complete';

			// Clean up status
			$this->_writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_COMPLETE'));

			// Repeat sync? (another request in queue)
			if ($syncQueue > 0)
			{
				// Clean up queue
				$obj->saveParam($this->model->get('id'), $service . '_sync_queue', 0);
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
				$obj->saveParam($this->model->get('id'), $service . '_sync_queue', 1);
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
		$obj->saveParam($this->model->get('id'), $service . '_sync_lock', $this->_uid);
		$this->_rSync['status'] = 'progress';
		return true;
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
	public function onAfterUpdate()
	{
		$sync     = 0;
		$activity = '';
		$message  = '';
		$ref	  = '';

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

		// Provisioned project?
		if ($this->model->isProvisioned() || !$this->model->get('id'))
		{
			return false;
		}

		// Pass success or error message
		if ($failed && !$uploaded && !$uploaded)
		{
			$this->_message = array('message' => 'Failed to upload ' . $failed, 'type' => 'error');
		}
		elseif ($uploaded || $updated || $expanded)
		{
			$sync = 1;

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

				// Clean up session values
				$jsession->set('projects.' . $this->model->get('alias') . '.uploaded', '');

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

			$sync = 1;

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

			$sync = 1;

			$activity = 'restored deleted file ' . basename($resParts[0]);

			// Output message
			$this->_message = array('message' => Lang::txt('PLG_PROJECTS_FILES_SUCCESS_RESTORED')
				. ' ' . basename($resParts[0]), 'type' => 'success');
		}

		// Force sync
		if ($sync)
		{
			$this->model->saveParam('google_sync_queue', 1);
		}

		// Add activity to feed
		if ($activity && $this->repo->isLocal())
		{
			$refParts  = explode(',', $ref);
			$parsedRef = '';

			$selected = array();
			foreach ($refParts as $file)
			{
				if (is_file( $this->_path . DS . trim($file) ))
				{
					$hash   = $this->_git->gitLog(trim($file), '' , 'hash');
					if ($hash)
					{
						$selected[] = substr($hash, 0, 10) . ':' . trim($file);

						// Generate preview
						$this->getFilePreview(trim($file), $hash, $this->_path, '');
						// Generate medium-size preview
						$this->getFilePreview(trim($file), $hash, $this->_path, '', '', true);
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
			$aid = $this->model->recordActivity( $activity, $parsedRef, 'project files',
				Route::url('index.php?option=' . $this->_option
				. '&alias=' . $this->model->get('alias') . '&active=files'), 'files', 1
			);
		}
	}
}