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

jimport( 'joomla.plugin.plugin' );

/**
 * Projects Files plugin
 */
class plgProjectsFiles extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function plgProjectsFiles(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin 		= JPluginHelper::getPlugin( 'projects', 'files' );
		$this->_params 		= new JParameter($this->_plugin->params);

		// Load component configs
		$this->_config 		= JComponentHelper::getParams('com_projects');
		$this->_valid_cases = array('files');

		$this->gitpath 	  	= $this->_config->get('gitpath', '/opt/local/bin/git');
		$this->prefix     	= $this->_config->get('offroot', 0) ? '' : JPATH_ROOT ;

		$this->_usageGit	= $this->_params->get('disk_usage');

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

		// Output collectors
		$this->_referer 	= '';
		$this->_message 	= array();

		$this->_queue		= array();
	}

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @return     array   Plugin name and title
	 */
	public function &onProjectAreas()
	{
		$area = array(
			'name' => 'files',
			'title' => JText::_('COM_PROJECTS_TAB_FILES')
		);

		return $area;
	}

	/**
	 * Event call to return count of items
	 *
	 * @param      object  $project 		Project
	 * @param      integer &$counts
	 * @return     array   integer
	 */
	public function &onProjectCount( $project, &$counts )
	{
		$count =  $this->getCount($project->alias, 'files');
		$counts['files'] = $count;

		return $counts;
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param      object  $project 		Project
	 * @param      string  $option 			Component name
	 * @param      integer $authorized 		Authorization
	 * @param      integer $uid 			User ID
	 * @param      integer $msg 			Message
	 * @param      integer $error 			Error
	 * @param      string  $action			Plugin task
	 * @param      string  $areas  			Plugins to return data
	 * @param      string  $case			Directory where .git sits ('files' or 'tool:toolname')
	 * @return     array   Return array of html
	 */
	public function onProject ( $project = '', $option = '', $authorized = '',
		$uid = '', $msg = '', $error = '', $action = '',
		$areas = null, $case = 'files')
	{
		$returnhtml = true;

		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'msg'=>'',
			'referer'=>''
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

		// Publishing enabled?
		$this->_publishing =
			is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'publication.php')
			&& JPluginHelper::isEnabled('projects', 'publications')
			? 1 : 0;

		// Is the user logged in?
		if (!$authorized && !$project->owner)
		{
			return $arr;
		}

		$this->_project  = $project;
		$this->_tool	 = NULL;
		$this->_audience = 'internal';
		$this->_data	 = NULL;

		// MIME types
		$this->mt = new \Hubzero\Content\Mimetypes();

		// Are we returning HTML?
		if ($returnhtml)
		{
			// Load language file
			$this->loadLanguage();

			$database = JFactory::getDBO();

			// Get joomla libraries
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');

			// Tool repo ? Load tool
			if (preg_match("/tools:/", $case))
			{
				$reponame = preg_replace( "/tools:/", "", $case);

				// Get tool library
				require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
					. DS . 'com_tools' . DS . 'tables' . DS . 'project.tool.php');

				$objA = new ProjectTool( $database );
				$this->_tool = $objA->getFullRecord($reponame, $this->_project->id);

				\Hubzero\Document\Assets::addPluginStylesheet('projects', 'tools');
				$lang = JFactory::getLanguage();
				$lang->load('plg_projects_tools');
			}

			$this->_case = $case ? $case : 'files';

			// Set vars
			$task = $action ? $action : JRequest::getVar('action', '');
			$this->_msg = $msg;
			if ($error)
			{
				$this->setError($error);
			}
			$this->_task = $action ? $action : JRequest::getVar('action', 'browse');
			$this->_database = $database;
			$this->_option = $option;
			$this->_authorized = $authorized;
			$this->_uid = $uid;
			if (!$this->_uid)
			{
				$juser = JFactory::getUser();
				$this->_uid = $juser->get('id');
			}

			// Incoming
			$this->subdir 	= trim(urldecode(JRequest::getVar('subdir', '')), DS);

			// Get time zone
			$zone = date_default_timezone_get();

			// Get JS and CSS
			$document = JFactory::getDocument();

			if ($this->_task != 'browser')
			{
				\Hubzero\Document\Assets::addPluginScript('projects', 'files');
			}
			\Hubzero\Document\Assets::addPluginStylesheet('projects', 'files');

			//  Establish connection to external services (NEW)
			if (is_object($this->_project) && $this->_project->id && !$this->_project->provisioned)
			{
				$this->_connect = new ProjectsConnectHelper($this->_database, $this->_project, $this->_uid, $zone);

				// Get services the project is connected to
				$this->_rServices = $this->_connect->getActive();
			}

			// Include Git Helper
			$this->getGitHelper();

			// Contribute process outside of projects
			if (!is_object($this->_project) or !$this->_project->id)
			{
				$this->_project = new Project( $this->_database );
				$this->_project->provisioned = 1;
			}

			// Get path
			$this->path = $this->getProjectPath();

			// Compiler Helper
			include_once( JPATH_ROOT . DS . 'components' . DS .'com_projects' . DS . 'helpers' . DS . 'compiler.php' );

			// File actions
			switch ($this->_task)
			{
				case 'save':
				case 'saveprov':
					$arr['html'] 	= $this->save();
					break;

				case 'status':
					$arr['html'] 	= $this->status();
					break;

				case 'download':
				case 'open':
					$arr['html'] 	= $this->download();
					break;

				case 'delete':
				case 'removeit':
					$arr['html'] 	= $this->delete();
					break;

				case 'deletedir':
					$arr['html'] 	= $this->_deleteDir();
					break;

				case 'savedir':
					$arr['html'] 	= $this->_saveDir();
					break;

				case 'move':
				case 'moveit':
					$arr['html'] 	= $this->move();
					break;

				case 'rename':
				case 'renameit':
					$arr['html'] 	= $this->_rename();
					break;

				case 'share':
				case 'shareit':
					$arr['html'] 	= $this->share();
					break;

				case 'history':
					$arr['html'] 	= $this->history();
					break;

				case 'diff':
					$arr['html'] 	= $this->diff();
					break;

				case 'upload':
					$arr['html'] 	= $this->upload();
					break;

				case 'browser':
					$arr['html'] 	= $this->browser();
					break;

				case 'diskspace':
				case 'optimize':
				case 'advoptimize':
					$arr['html'] 	= $this->diskspace(
						$option, $project, $this->_case,
						$this->_uid, $this->_task, $this->_config, $this->_tool);
					break;

				case 'blank':
					$arr['html'] 	= $this->blank();
					break;

				case 'compile':
					$arr['html'] 	= $this->compile();
					break;

				case 'serve':
					$arr['html'] 	= $this->serve();
					break;

				// Connections
				case 'connect':
				case 'disconnect':
					$arr['html'] 	= $this->connect();
					break;

				case 'sync':
					$arr['html'] 	= $this->iniSync();
					break;
				case 'sync_status':
					$arr['html'] 	= $this->syncStatus();
					break;

				case 'newdir':
					$ajax 			= JRequest::getInt('ajax', 0);
					$arr['html'] 	= $ajax ? $this->_newDir() :  $this->view();
					break;

				case 'trash':
					$arr['html'] 	= $this->showTrash();
					break;

				case 'restore':
					$arr['html'] 	= $this->restore();
					break;

				case 'select':
				case 'filter':
					$arr['html'] 	= $this->select();
					break;

				case 'browse':
				default:

				$arr['html'] 	= $this->view();
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
	public function view ($sync = 0)
	{
		// Build query
		$filters = array();
		$filters['limit'] 	 = JRequest::getInt('limit', 100);
		$filters['start']    = JRequest::getInt( 'limitstart', 0);
		$filters['sortby']   = JRequest::getVar( 'sortby', 'filename');
		$filters['sortdir']  = JRequest::getVar( 'sortdir', 'ASC');

		$document = JFactory::getDocument();
		$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'files' . DS . 'css' . DS . 'uploader.css');
		$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'files' . DS . 'css' . DS . 'diskspace.css');
		$document->addScript('plugins' . DS . 'projects' . DS . 'files' . DS . 'js' . DS . 'diskspace.js');

		// Something is wrong
		if (!$this->path)
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

		// Does subdirectory exist?
		if ($this->subdir && !is_dir($this->prefix . $this->path . DS . $this->subdir))
		{
			$this->subdir = '';
		}

		// Write config file
		$this->writeGitConfig( $this->_project->alias, $this->_config, $this->_case);

		// Build URL
		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;
		$url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');
		$do  	= ($this->_case != 'files' && $this->_tool->name) ? 'do' : 'action';

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder' 	=> 'projects',
				'element' 	=> 'files',
				'name' 		=> 'browse',
				'layout' 	=> 'filelist'
			)
		);

		// Get used space in project directory
		$view->dirsize = $this->getDiskUsage($this->path, $this->prefix, $this->_usageGit);

		// Get connection details for user
		$objO = new ProjectOwner( $this->_database );
		$objO->loadOwner ($this->_project->id, $this->_uid);
		$view->oparams = new JParameter( $objO->params );

		// Do we have any changes to report?
		$this->onAfterUpdate();

		// Get fresh data
		$obj = new Project( $this->_database );
		$obj->load($this->_project->id);
		$view->params = new JParameter( $obj->params );

		// Get local files and folders
		$localFiles 		= $this->getFiles($this->path, $this->subdir,
								1, 0, 0, 0, $filters['sortby'], $filters['sortdir']);
		$localDirs 			= $this->getFolders($this->path, $this->subdir, $this->prefix);

		// Sharing with external services setup
		$view->connect		 = $this->_connect;
		$view->services 	 = $this->_rServices;
		$view->connections	 = $this->_connect->getConnections($this->_uid);
		$view->sharing 		 = 0;
		$remotes			 = array();

		$objRFile = new ProjectRemoteFile ($this->_database);

		// Remote service(s) active?
		if (!empty($this->_rServices) && $this->_case == 'files')
		{
			$view->sharing = 1;

			// Get stored connections
			foreach ($view->services as $servicename)
			{
				// Get stored remote connections
				$remotes[$servicename] = $objRFile->getRemoteEditFiles($this->_project->id, $servicename, $this->subdir);

				$sync	= $sync == 2 ? 0 : $view->params->get($servicename . '_sync_queue', 0);
			}
		}

		// Sort local and remote file info
		$view->items = $this->_sortItems(
			$localFiles,
			$localDirs,
			$remotes,
			$filters['sortby'],
			$filters['sortdir']
		);

		$view->rSync 		= $this->_rSync;
		$view->url			= $url;
		$view->sync			= $sync;
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->juser		= JFactory::getUser();
		$view->filters 		= $filters;
		$view->subdir 		= $this->subdir;
		$view->task			= $this->_task;
		$view->case 		= $this->_case;
		$view->tool			= $this->_tool;
		$view->do 			= $do;
		$view->config 		= $this->_config;
		$view->publishing	= $this->_publishing;
		$view->title		= $this->_area['title'];
		$view->quota 		= $view->params->get('quota')
							? $view->params->get('quota')
							: ProjectsHtml::convertSize(floatval($this->_config->get('defaultQuota', '1')), 'GB', 'b');
		$view->fileparams 	= $this->_params;
		$view->sizelimit 	= ProjectsHTML::formatSize($this->_params->get('maxUpload', '104857600'));

		return $view->loadTemplate();
	}

	/**
	 * Browser within publications NEW
	 *
	 * @return     string
	 */
	public function select()
	{
		// Incoming
		$props  = JRequest::getVar( 'p', '' );
		$ajax   = JRequest::getInt( 'ajax', 0 );
		$pid    = JRequest::getInt( 'pid', 0 );
		$vid    = JRequest::getInt( 'vid', 0 );
		$filter = urldecode(JRequest::getVar( 'filter', '' ));

		// Parse props for curation
		$parts   = explode('-', $props);
		$block   = (isset($parts[0]) && in_array($parts[0], array('content', 'extras'))) ? $parts[0] : 'content';
		$step    = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
		$element = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 1;

		// Provisioned project?
		$prov   = $this->_project->provisioned == 1 ? 1 : 0;

		// Make sure Git helper is included
		$this->getGitHelper();

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'files',
				'name'		=>'selector'
			)
		);

		// Load classes
		$objP  			= new Publication( $this->_database );
		$view->version 	= new PublicationVersion( $this->_database );

		// Load publication version
		$view->version->load($vid);
		if (!$view->version->id)
		{
			$this->setError(JText::_('PLG_PROJECTS_FILES_SELECTOR_ERROR_NO_PUBID'));
		}

		// Get publication
		$view->publication = $objP->getPublication($view->version->publication_id,
			$view->version->version_number, $this->_project->id);

		if (!$view->publication)
		{
			$this->setError(JText::_('PLG_PROJECTS_FILES_SELECTOR_ERROR_NO_PUBID'));
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
		$mt   				= new PublicationMasterType( $this->_database );
		$view->publication->_type   	= $mt->getType($view->publication->base);
		$view->publication->_project 	= $this->_project;

		// Get attachments
		$pContent = new PublicationAttachment( $this->_database );
		$view->publication->_attachments = $pContent->sortAttachments ( $vid );

		// Get curation model
		$view->publication->_curationModel = new PublicationsCuration(
			$this->_database,
			$view->publication->_type->curation
		);

		// Make sure block exists, else use default
		if (!$view->publication->_curationModel->setBlock( $block, $step ))
		{
			$block = 'content';
			$step  = 1;
		}

		// Set pub assoc and load curation
		$view->publication->_curationModel->setPubAssoc($view->publication);

		// Get file list
		$view->items = NULL;
		if ($this->_project->id)
		{
			$view->items = $this->getList();
		}

		if (!$ajax)
		{
			$document = JFactory::getDocument();
			$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'files' . DS . 'css' . DS . 'selector.css');
		}

		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->ajax			= $ajax;
		$view->task			= $this->_task;
		$view->element		= $element;
		$view->block		= $block;
		$view->step 		= $step;
		$view->props		= $props;
		$view->filter		= $filter;
		$view->sizelimit 	= $this->_params->get('maxUpload', '104857600');

		if ($prov)
		{
			$view->quota = ProjectsHtml::convertSize(floatval($this->_config->get('pubQuota', '1')), 'GB', 'b');
		}

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Browser for within publications
	 *
	 * @return     string
	 */
	public function browser()
	{
		// Incoming
		$content 	= JRequest::getVar('content', 'files');
		$ajax 		= JRequest::getInt('ajax', 0);
		$primary 	= JRequest::getInt('primary', 1);
		$images 	= JRequest::getInt('images', 0);
		$pid 		= JRequest::getInt('pid', 0);

		if (!$ajax)
		{
			return false;
		}

		// Contribute process outside of projects
		if (!is_object($this->_project) or !$this->_project->id)
		{
			$this->_project = new Project( $this->_database );
			$this->_project->provisioned = 1;
		}

		// Provisioned project?
		$prov   = $this->_project->provisioned == 1 ? 1 : 0;
		$prefix = $prov ? JPATH_ROOT : $this->prefix;

		// Does subdirectory exist?
		if (!is_dir($prefix. $this->path. DS . $this->subdir))
		{
			$this->subdir = '';
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
		if ($prov)
		{
			$view->files = $this->getMemberFiles($this->path, $this->subdir);
		}
		elseif (in_array($content, $this->_valid_cases))
		{
			$view->files = $this->getFiles($this->path, $this->subdir, 0, 0, 0, 0, '', 'ASC', true);
		}
		else
		{
			$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
			return;
		}

		// Does the publication exist?
		$versionid 	= JRequest::getInt('versionid', 0);
		$pContent 	= new PublicationAttachment( $this->_database );
		$role    	= $primary ? '1': '0';
		$other 		= $primary ? '0' : '1';

		if (!$images)
		{
			$view->attachments = $pContent->getAttachments($versionid, $filters = array('role' => $role));
		}
		else
		{
			// Common extensions (for gallery)
			$pubPlugin 	= JPluginHelper::getPlugin( 'projects', 'publications' );
			$pubparams  = new JParameter($pubPlugin->params);

			$view->image_ext = ProjectsHelper::getParamArray(
								$pubparams->get('image_types', 'bmp, jpeg, jpg, png' ));
			$view->video_ext = ProjectsHelper::getParamArray(
								$pubparams->get('video_types', 'avi, mpeg, mov, wmv' ));

			$other = 1;

			// Get current screenshots
			$pScreenshot = new PublicationScreenshot( $this->_database );
			$view->shots = $pScreenshot->getScreenshots($versionid);
		}

		$view->exclude = $pContent->getAttachments($versionid, $filters = array('role' => $other, 'select' => 'a.path'));
		if ($view->exclude && !$images)
		{
			$excude_files = array();
			foreach ($view->exclude as $exclude)
			{
				$excude_files[] = str_replace($this->path. DS, '', trim($exclude->path));
			}
			$view->exclude = $excude_files;
		}

		$view->primary 		= $primary;
		$view->images 		= $images;
		$view->total 		= 0;
		$view->params 		= new JParameter( $this->_project->params );
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->subdir 		= $this->subdir;
		$view->case 		= $this->_case;
		$view->base 		= $content;
		$view->config 		= $this->_config;
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

		$kind = 'projects.' . $this->_project->alias . '.' . $type;

		// Get session
		$jsession = JFactory::getSession();

		if ($append == true)
		{
			$exVal  = $jsession->get($kind);
			$val 	= $exVal ? $exVal . ', ' . $file : $file;
		}
		else
		{
			$val = $file;
		}

		$val .= $appendMessage ? $appendMessage : '';

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
		$jsession = JFactory::getSession();

		// Get values from session
		$updated 	= $jsession->get('projects.' . $this->_project->alias . '.updated');
		$uploaded 	= $jsession->get('projects.' . $this->_project->alias . '.uploaded');
		$failed 	= $jsession->get('projects.' . $this->_project->alias . '.failed');
		$deleted 	= $jsession->get('projects.' . $this->_project->alias . '.deleted');
		$restored 	= $jsession->get('projects.' . $this->_project->alias . '.restored');
		$extracted 	= $jsession->get('projects.' . $this->_project->alias . '.extracted');

		// Clean up session values
		$jsession->set('projects.' . $this->_project->alias . '.failed', '');
		$jsession->set('projects.' . $this->_project->alias . '.updated', '');
		$jsession->set('projects.' . $this->_project->alias . '.uploaded', '');
		$jsession->set('projects.' . $this->_project->alias . '.deleted', '');
		$jsession->set('projects.' . $this->_project->alias . '.restored', '');
		$jsession->set('projects.' . $this->_project->alias . '.extracted', '');

		// Provisioned project?
		if ($this->_project->provisioned || !$this->_project->id)
		{
			return false;
		}

		// Pass success or error message
		if ($failed && !$uploaded && !$uploaded)
		{
			$this->_message = array('message' => 'Failed to upload ' . $failed, 'type' => 'error');
		}
		elseif ($uploaded || $updated)
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
				$jsession->set('projects.' . $this->_project->alias . '.uploaded', '');

				// Save referenced files
				$ref = $extracted ? $extracted : $uploaded;
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

				// Save referenced files
				if ($extracted)
				{
					$ref .= $extracted;
				}
				else
				{
					$ref .= $uploaded ? ', ' . $updated : $updated;
				}
			}

			$activity  = $message . ' ' . strtolower(JText::_('COM_PROJECTS_IN_PROJECT_FILES')) ;

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
				. ' ' . JText::_('PLG_PROJECTS_FILES_ITEMS');

			// Output message
			$this->_message = array('message' => JText::_('PLG_PROJECTS_FILES_SUCCESS_DELETED')
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
			$this->_message = array('message' => JText::_('PLG_PROJECTS_FILES_SUCCESS_RESTORED')
				. ' ' . basename($resParts[0]), 'type' => 'success');
		}

		// Force sync
		if ($sync)
		{
			$obj = new Project( $this->_database );
			$obj->saveParam($this->_project->id, 'google_sync_queue', 1);
		}

		// Add activity to feed
		if ($activity && $this->_case == 'files')
		{
			$objAA = new ProjectActivity( $this->_database );

			$refParts  = explode(',', $ref);
			$parsedRef = '';

			$selected = array();
			foreach ($refParts as $file)
			{
				if (is_file( $this->path . DS . trim($file) ))
				{
					$hash   = $this->_git->gitLog($this->path, trim($file), '' , 'hash');
					if ($hash)
					{
						$selected[] = substr($hash, 0, 10) . ':' . trim($file);

						// Generate preview
						$this->getFilePreview(trim($file), $hash, $this->path, '');
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
				$parsedRef = substr($parsedRef,0,strlen($parsedRef) - 1);
			}

			// Check to make sure we are not over in char length
			if (strlen($parsedRef) > 255)
			{
				$parsedRef = ProjectsHtml::shortenText($parsedRef);
			}

			// Record activity
			$aid = $objAA->recordActivity( $this->_project->id,
				$this->_uid, $activity,
				$parsedRef, 'project files', JRoute::_('index.php?option=' . $this->_option . a .
				'alias=' . $this->_project->alias . a . 'active=files'), 'files', 1 );
		}
	}

	/**
	 * Upload view
	 *
	 * @return     void, redirect
	 */
	public function upload()
	{
		$prov 	= $this->_project->provisioned == 1 ? 1 : 0;

		// Incoming
		$ajax 	= JRequest::getInt('ajax', 0);
		$prefix = $prov ? JPATH_ROOT : $this->prefix;
		$pid 	= JRequest::getInt('pid', 0);

		// Add uploader css
		if (!$ajax)
		{
			$document = JFactory::getDocument();
			$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'files' . DS . 'css' . DS . 'uploader.css');
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'upload'
			)
		);

		// Get used space
		$usageGit = $prov ? false : $this->_usageGit;
		$dirsize = $this->getDiskUsage($this->path, $prefix, false);

		// Get quota & routing
		if ($prov)
		{
			$view->quota = ProjectsHtml::convertSize(floatval($this->_config->get('pubQuota', '1')), 'GB', 'b');
			$route 		 = 'index.php?option=com_publications' . a . 'task=submit';
			$view->url   = JRoute::_($route);
		}
		else
		{
			// Get quota
			$params 	 = new JParameter($this->_project->params);
			$quota 		 = $params->get('quota');
			$view->quota = $quota
				? $quota
				: ProjectsHtml::convertSize(floatval($this->_config->get('defaultQuota', '1')), 'GB', 'b');

			$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;
			$view->url 	= ($this->_case != 'files' && $this->_tool->name)
				? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
				: JRoute::_($route . a . 'active=files');
		}

		$view->do  			= ($this->_case != 'files' && $this->_tool->name) ? 'do' : 'action';
		$view->unused 		= $view->quota - $dirsize;
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->pid 			= $pid;
		$view->subdir 		= $this->subdir;
		$view->case 		= $this->_case;
		$view->ajax			= $ajax;
		$view->config 		= $this->_config;
		$view->sizelimit 	= $this->_params->get('maxUpload', '104857600');
		$view->title		= $this->_area['title'];
		$view->params 		= $this->_params;

		// Get messages	and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Upload file(s) via AJAX and check into Git
	 *
	 * @return     void, redirect
	 */
	public function ajaxSave()
	{
		// Incoming
		$expand  	= JRequest::getInt('expand_zip');
		$sizeLimit 	= $this->_params->get('maxUpload', '104857600');
		$pid 		= JRequest::getInt('pid', 0);

		$prov    	= ($this->_task == 'saveprov' || $this->_project->provisioned == 1) ? 1 : 0;
		$newProv	= ($prov && !$this->_project->id) ? 1 : 0;
		$this->_task= $newProv ? 'saveprov' : $this->_task;
		$dirsize 	= 0;
		$new 		= true;
		$exists	  	= 0;

		// Get temp path
		$temp_path 	 = $newProv ? 'temp' : $this->getProjectPath ($this->_project->alias, 'temp');
		$prefix 	 = $newProv ? JPATH_ROOT : $this->prefix;
		$tempFile	 = NULL;

		// Collect output
		$out      = array();
		$updated  = array();
		$uploaded = array();
		$skipped  = array();

		// get the file
		if (isset($_FILES['qqfile']))
		{
			$stream = false;
			$file = $_FILES['qqfile']['name'];
			$size = (int) $_FILES['qqfile']['size'];
		}
		elseif (isset($_GET['qqfile']))
		{
			$stream = true;
			$file = $_GET['qqfile'];
			$size = (int) $_SERVER["CONTENT_LENGTH"];
		}
		else
		{
			// Store in session
			$this->registerUpdate('failed', ' (File not found) ' , true);

			return json_encode(array('error' => JText::_('File not found')));
		}

		// Provisioned project scenario
		if ($newProv)
		{
			$quota 		= ProjectsHtml::convertSize(floatval($this->_config->get('pubQuota', '1')),
							'GB', 'b');
			$dirsize 	= $this->getDiskUsage($this->path, $prefix, false);
		}
		else
		{

			// Get quota
			$params 	= new JParameter($this->_project->params);
			$quota 		= $params->get('quota');
			$quota 		= $quota
						  ? $quota
						  : ProjectsHtml::convertSize(floatval($this->_config->get('defaultQuota', '1')),
							'GB', 'b');
			$dirsize 	= $this->getDiskUsage($this->path, $prefix, $this->_usageGit);
		}

		// Some checks
		if ($size > $sizeLimit)
		{
			// Store in session
			$this->registerUpdate('failed', $file, true, ' (File too large) ');

			return json_encode(array('error' => JText::sprintf('File too large')));
		}

		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];
		$ext 	  = $pathinfo['extension'];

		// Archive?
		$archive_formats = array('zip', 'tar', 'gz');
		$expand = in_array(strtolower($ext), $archive_formats) && $expand ? 1 : 0;

		// Make the filename safe
		$filename = urldecode($filename);
		$filename = ProjectsHtml::makeSafeFile($filename);

		$fName 	  = $ext ? $filename . '.' . $ext : $filename;

		$fpath = $this->subdir ? $this->subdir . DS . $fName : $fName;
		$file  = $prefix . $this->path . DS . $fpath;

		$tempFile = $prefix . $temp_path . DS . $fName;
		$repoFile = $prefix . $this->path . DS . $fpath;

		// Are we updating?
		if (file_exists($repoFile))
		{
			$exists = 1;
		}

		// Compute used space
		$unused = $quota - $dirsize;

		if ($size > $unused)
		{
			if (is_file($tempFile))
			{
				unlink($tempFile);
			}

			// Store in session
			$this->registerUpdate('failed', $fName, true, ' (No disk space left) ');

			return json_encode(array('error' => JText::_('No disk space left')));
		}

		// Upload temp file
		$where 	  = $expand ? $tempFile : $repoFile;
		if ($stream == true)
		{
			/*$input    = fopen("php://input", "r");
			$target   = fopen($where , "w");
			$realSize = stream_copy_to_stream($input, $target);

			fclose($input);
			fclose($target);
			*/
			copy("php://input", $where);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $where);
		}

		// Do virus check
		if (file_exists($where) && ProjectsHelper::virusCheck($where))
		{
			if ($exists && !$expand)
			{
				// Discard uploaded change
				$this->_git->callGit($this->path, 'checkout ' . $fpath);
			}
			else
			{
				unlink($where);
			}

			// Store in session
			$this->registerUpdate('failed', $fName, true, ' (Virus detected, refusing to upload) ');

			return json_encode(array('error' => JText::sprintf('Virus detected, refusing to upload')));
		}

		// Set commit message
		$commitMsgZip 	= 'Added as part of archive ' . $fName . "\n";
		$commitMsg 		= '';

		// Perform upload
		if ($expand)
		{
			if (!is_file($tempFile))
			{
				// Store in session
				$this->registerUpdate('failed', $fName, true, ' (Failed to upload temp file) ');

				return json_encode(array('error' => JText::sprintf('Failed to upload temp file')));
			}

			$z 	   = 0;
			$cSize = 0;
			$ext   = strtolower($ext);
			if ($ext == 'tar' || $ext == 'gz')
			{
				// Expand tar file
				$z = $this->untar($tempFile, $uploaded, $updated,
					$commitMsgZip, $cSize, $this->path, $prefix, $this->subdir,
					$unused, $fName );
			}
			elseif ($ext == 'zip')
			{
				// Expand zip using ZipArchiver
				$z = $this->unzip($tempFile, $uploaded, $updated,
					$commitMsgZip, $cSize, $this->path, $prefix, $this->subdir,
					$unused, $fName );
			}
			// Commit expanded files
			if ($z > 0)
			{
				if (!$newProv)
				{
					$this->_git->gitCommit($this->path, $commitMsgZip);
				}

				// Delete temp file
				if (is_file($tempFile)) { unlink($tempFile); }

				// Store in session
				if ($new)
				{
					$this->registerUpdate('uploaded', $fpath, true, ' ( ' . $z . ' item(s) extracted )');
				}
				else
				{
					$this->registerUpdate('updated', $fpath, true, ' ( ' . $z . ' item(s) extracted )');
				}

				// Success
				return json_encode(array(
					'success'   => $z,
					'file'      => $fName,
					'isNew'		=> $new
				));
			}
			else
			{
				$this->registerUpdate('failed', $fName);
				return json_encode(array('error' => JText::_('COM_PROJECT_FILES_ERROR_UNZIP_FAILED')));
			}
		}
		else
		{
			//JFile::copy($tempFile, $prefix . $path . DS . $fpath);
			//exec('cp ' . $tempFile . ' ' . $prefix . $path . DS . $fpath );

			if (file_exists($prefix . $this->path . DS . $fpath))
			{
				if ($exists)
				{
					$updated[] = $fpath;
				}
				else
				{
					$uploaded[] = $fpath;
				}

				$this->_queue[] = $fpath;

				if (!$newProv)
				{
					// Git add
					$new = in_array($fpath, $updated) ? false : true;

					$this->_git->gitAdd($this->path, $fpath, $commitMsg, $new);

					if ($commitMsg)
					{
						$this->_git->gitCommit($this->path, $commitMsg);

						// Store in session
						if ($new)
						{
							$this->registerUpdate('uploaded', $fpath);
						}
						else
						{
							$this->registerUpdate('updated', $fpath);
						}
					}
				}

				// Delete temp file
				//if (is_file($tempFile)) { unlink($tempFile); }
			}
			else
			{
				$this->registerUpdate('failed', $fName);
				return json_encode(array('error' => JText::_('Failed to copy temp file')));
			}
		}

		return json_encode(array(
			'success'   => 1,
			'file'      => $file,
			'isNew'		=> $new
		 )
		);
	}

	/**
	 * Upload file(s) and check into Git
	 *
	 * @return     void, redirect
	 */
	public function save()
	{
		// Incoming
		$view = JRequest::getVar('view', 'view'); // where to redirect
		$json = JRequest::getVar('json', 0); // give response in json?

		// AJAX uploader
		if (JRequest::getVar('no_html', 0) && !$json)
		{
			return $this->ajaxSave();
		}

		// Incoming files
		$files = JRequest::getVar( 'upload', '', 'files', 'array' );

		if (empty($files['name']) or $files['name'][0] == '')
		{
			$this->setError(JText::_('COM_PROJECTS_NO_FILES'));
		}

		// Collect output
		$out      = array();
		$updated  = array();
		$uploaded = array();
		$skipped  = array();
		$sync	  = 0;

		$prefix = $this->_task == 'saveprov' ? JPATH_ROOT : $this->prefix;

		// Archive formats
		$archive_formats = array('zip', 'tar', 'gz');

		// Start commit message
		$commitMsg = '';

		// Provisioned project scenario
		if ($this->_task == 'saveprov')
		{
			$quota 		= ProjectsHtml::convertSize(floatval($this->_config->get('defaultQuota', '1')), 'GB', 'b');
			$dirsize 	= $this->getDiskUsage($this->path, $prefix, false);
		}
		else
		{
			// Get quota
			$params 	= new JParameter($this->_project->params);
			$quota 		= $params->get('quota');
			$quota 		= $quota
						  ? $quota
						  : ProjectsHtml::convertSize(floatval($this->_config->get('defaultQuota', '1')), 'GB', 'b');
			$dirsize 	= $this->getDiskUsage($this->path, $prefix, $this->_usageGit);
		}

		// Compute used space
		$unused = $quota - $dirsize;
		$cSize  = 0;

		// Process each file
		if (!$this->getError())
		{
			// Go through uploaded files
			for ($i=0; $i < count($files['name']); $i++)
			{
				$file = $files['name'][$i];
				$tmp_name = $files['tmp_name'][$i];

				// Make the filename safe
				if ($file)
				{
					$file = ProjectsHtml::makeSafeFile($file);
				}

				// Get file extention
				$parts = explode('.', $file);
				$ext   = count($parts) > 1 ? array_pop($parts) : '';
				$base  = $parts[0];

				// Subdir?
				$file = $this->subdir ? $this->subdir . DS . $file : $file;

				// Check file size
				$sizelimit = ProjectsHtml::formatSize($this->_params->get('maxUpload', '104857600'));

				if ( $files['size'][$i] > intval($this->_params->get('maxUpload', '104857600')))
				{
					$this->setError( JText::_('COM_PROJECTS_FILES_ERROR_EXCEEDS_LIMIT') . ' '
						. $sizelimit . '. ' . JText::_('COM_PROJECTS_FILES_ERROR_TOO_LARGE_USE_OTHER_METHOD') );
				}

				// Combined size
				if ($files['size'][$i] > 0)
				{
					$cSize = $cSize + $files['size'][$i];
				}

				// Check against quota
				if ($cSize > $unused)
				{
					$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_OVER_QUOTA'));
					break;
				}

				// Expand archive?
				$expand  = JRequest::getInt('expand_zip', 0);
				$zipfile = in_array(strtolower($ext), $archive_formats) ? 1 : 0;

				if (!$this->getError() && $zipfile && $expand)
				{
					$commitMsgZip 	= 'Added as part of archive ' . basename($file) . "\n";
					$z				= 0;
					$ext   			= strtolower($ext);

					if ($ext == 'tar' || $ext == 'gz')
					{
						// Expand tar file
						$z = $this->untar($tmp_name, $uploaded, $updated,
							$commitMsgZip, $cSize, $this->path, $prefix, $subdir, $unused, $file );
					}
					elseif ($ext == 'zip')
					{
						// Expand zip using ZipArchiver
						$z = $this->unzip($tmp_name, $uploaded, $updated,
							$commitMsgZip, $cSize, $this->path, $prefix, $subdir, $unused, $file );
					}

					// Commit expanded files
					if ($z > 0)
					{
						if ($this->_task != 'saveprov')
						{
							$this->_git->gitCommit($this->path, $commitMsgZip);
						}
					}
					else
					{
						$this->setError(JText::_('COM_PROJECT_FILES_ERROR_UNZIP_FAILED'));
					}
				}

				// Upload file
				if (!$this->getError() && (!$zipfile || !$expand))
				{
					// cd
					chdir($prefix . $this->path);

					$exists = 0;
					if (file_exists($prefix . $this->path . DS . $file))
					{
						$exists    = 1;
						$updated[] = $file;
					}

					if (!JFile::upload($tmp_name, $prefix . $this->path . DS . $file))
					{
						$this->setError(JText::_('COM_PROJECTS_ERROR_UPLOADING'));
					}
					else
					{
						// Do virus check
						if (ProjectsHelper::virusCheck($prefix . $this->path . DS . $file))
						{
							if ($exists)
							{
								// Discard uploaded change
								$this->_git->callGit($this->path, 'checkout ' . $file);
							}
							else
							{
								unlink($prefix . $this->path . DS . $file);
							}

							$this->setError(JText::_('Virus detected, refusing to upload'));
						}
						else
						{
							$uploaded[] = $file;

							if ($this->_task != 'saveprov')
							{
								// Git add
								$new = isset($updated[$file]) ? false : true;
								$this->_git->gitAdd($this->path, $file, $commitMsg, $new);
								$this->_git->gitCommit($this->path, $commitMsg);

								// Store in session
								$updateType = $new ? 'uploaded' : 'updated';
								$this->registerUpdate($updateType, $file);

								// Generate preview
								$hash = $this->_git->gitLog($this->path, $file, '' , 'hash');
								$this->getFilePreview($file, $hash, $this->path, $this->subdir);
							}
						}
					}
				}
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

		// Display view
		if ($view == 'browser')
		{
			return $this->browser();
		}
		else
		{
			// Pass success or error message
			if ($this->getError())
			{
				$this->_message = array('message' => $this->getError(), 'type' => 'error');
			}
			elseif (isset($this->_msg) && $this->_msg)
			{
				$this->_message = array('message' => $this->_msg, 'type' => 'success');
			}

			$pid 	= JRequest::getInt('pid', 0);

			// Build pub url
			$route = $this->_project->provisioned
				? 'index.php?option=com_publications' . a . 'task=submit' . a . $pid
				: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias;

			$url 	= ($this->_case != 'files' && $this->_tool->name)
				? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
				: JRoute::_($route . a . 'active=files');

			// Redirect to file list
			$url .= $this->subdir ? '?subdir=' .urlencode($this->subdir) : '';

			if ($view == 'pub')
			{
				$url = JRequest::getVar('HTTP_REFERER', NULL, 'server');
			}

			$this->_referer = $url;
			return;
		}
	}

	/**
	 * Untar
	 *
	 * @return     void
	 */
	public function untar( $tmp_name = '', &$uploaded, &$updated,
		&$commitMsgZip, &$cSize,
		$path = '', $prefix = '', $subdir = '',
		$unused = 0, $file)
	{

		if (!$tmp_name)
		{
			$this->setError(JText::_('COM_PROJECT_FILES_ERROR_UNZIP_FAILED'));
			return false;
		}

		// Reserved names (service directories)
		$reserved = ProjectsHelper::getParamArray($this->_params->get('reservedNames'));

		$temp_path 	 = $this->_task == 'saveprov' ? 'temp' : $this->getProjectPath ($this->_project->alias, 'temp');
		$archive 	 = $prefix . $temp_path . DS . $file;
		$extractPath = $prefix . $temp_path . DS . ProjectsHtml::generateCode (4 ,4 ,0 ,1 ,0 );
		$z 			 = 0;
		$unzipto 	 = $subdir ? $prefix . $path . DS . $subdir : $prefix . $path;

		// Create dir to extract into
		if (!is_dir($extractPath))
		{
			JFolder::create($extractPath);
		}

		if (!is_file($tmp_name))
		{
			$this->setError(JText::_('COM_PROJECT_FILES_ERROR_UNZIP_FAILED'));
			return false;
		}

		// Do virus check
		if (ProjectsHelper::virusCheck($tmp_name))
		{
			$this->setError(JText::_('Virus detected, refusing to upload'));
			return false;
		}

		// Expand tar
		try
		{
			chdir($prefix . $temp_path);
			exec('tar xvf ' . $tmp_name . ' -C ' . $extractPath . ' 2>&1', $out );

			// Now copy extracted contents into project
			$extracted = JFolder::files($extractPath, '.', true, true,
				$exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX' ));

			foreach ($extracted as $e)
			{
				$fileinfo = pathinfo($e);
				$a_dir  = $fileinfo['dirname'];
				$a_dir	= str_replace($extractPath, '', $a_dir);
				$a_file = $fileinfo['basename'];

				// Skip certain system files
				if (preg_match("/__MACOSX/", $e) OR preg_match("/.DS_Store/", $e))
				{
					continue;
				}

				$fSize = filesize($e);

				// Combined size
				if ($fSize > 0)
				{
					$cSize = $cSize + $fSize;
				}

				// Check against quota
				if ($cSize > $unused)
				{
					$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_OVER_QUOTA'));
					break;
				}

				// Clean up filename
				$safe_dir = $a_dir && $a_dir != '.' ? ProjectsHtml::makeSafeDir($a_dir) : '';
				$safe_file= ProjectsHtml::makeSafeFile($a_file);

				$skipDir = 0;
				if ($safe_dir && in_array(strtolower($safe_dir), $reserved))
				{
					$skipDir = 1;
				}
				$safename = $safe_dir && !$skipDir ? $safe_dir . DS . $safe_file : $safe_file;
				$afile 	  = $subdir ? $subdir . DS . $safename : $safename;

				// Provision directory
				if ($safe_dir && !$skipDir && !is_dir($unzipto . DS . $safe_dir ))
				{
					if (JFolder::create( $unzipto . DS . $safe_dir ) && $this->_task != 'saveprov')
					{
						$this->_git->makeEmptyFolder($path, $afile);
						$commitMsgZip .= JText::_('COM_PROJECTS_CREATED_DIRECTORY')
							. '  ' . escapeshellarg($afile) ."\n";
					}
					$z++;
				}

				if (file_exists($prefix . $path . DS . $afile))
				{
					$updated[] = $afile;
				}

				// Copy file into project
				if (JFile::copy($e, $unzipto . DS . $safename))
				{
					// Add to Git
					if (is_file($prefix . $path . DS . $afile))
					{
						$uploaded[] = $afile;

						if ($this->_task != 'saveprov')
						{
							// Git add & commit
							$this->_git->gitAdd($path, $afile, $commitMsgZip);
							$this->_git->gitCommit($path, $commitMsgZip);

							// Store in session
							$this->registerUpdate('extracted', $afile);

							// Generate preview
							$hash = $this->_git->gitLog($path, $afile, '' , 'hash');
							$this->getFilePreview($afile, $hash, $path, $subdir);
						}

						$z++;
					}
				}
			}

			// Clean up
			JFolder::delete($extractPath);
			JFile::delete($archive);

			return $z;
		}
		catch (Exception $e)
		{
			$this->setError(JText::_('COM_PROJECT_FILES_ERROR_UNZIP_FAILED'));
			return false;
		}
	}

	/**
	 * Extract files using ZipArchive
	 *
	 * @return     void
	 */
	public function unzip(
		$tmp_name = '', &$uploaded, &$updated,
		&$commitMsgZip, &$cSize,
		$path = '', $prefix = '', $subdir = '',
		$unused = 0, $file)
	{
		if (!extension_loaded('zip'))
		{
			$this->setError(JText::_('COM_PROJECT_ZLIB_PACKAGE_REQUIRED'));
			return false;
		}

		if (!$tmp_name)
		{
			$this->setError(JText::_('COM_PROJECT_FILES_ERROR_UNZIP_FAILED'));
			return false;
		}

		$z = 0;
		$unzipto = $subdir ? $prefix . $path . DS . $subdir : $prefix . $path;

		// Reserved names (service directories)
		$reserved = ProjectsHelper::getParamArray(
			$this->_params->get('reservedNames'));

		// Do virus check
		if (ProjectsHelper::virusCheck($tmp_name))
		{
			$this->setError(JText::_('Virus detected, refusing to upload'));
			return false;
		}

		$zip = new ZipArchive;

		if ($zip->open($tmp_name) === true)
		{
			$stopLoop = 0;
			$skipDir  = 0;

			for ($a = 0; $a < $zip->numFiles; $a++)
			{
		        if ($stopLoop)
				{
					$this->setError(JText::_('COM_PROJECT_FILES_ERROR_UNZIP_FAILED'));
					break;
				}

				$filename = $zip->getNameIndex($a);
		        $fileinfo = pathinfo($filename);

				$a_dir  = $fileinfo['dirname'];
				$a_file = $fileinfo['basename'];

				// Skip certain system files
				if (preg_match("/__MACOSX/", $filename) OR preg_match("/.DS_Store/", $filename))
				{
					continue;
				}

				// Clean up filename
				$safe_dir = $a_dir ? ProjectsHtml::makeSafeDir($a_dir) : '';
				$safe_file= ProjectsHtml::makeSafeFile($a_file);
				$safename = $safe_dir && !$skipDir ? $safe_dir . DS . $safe_file : $safe_file;
				$afile 	  = $subdir ? $subdir . DS . $safename : $safename;
				$adir 	  = $subdir ? $subdir . DS . $safe_dir : $safe_dir;

				if (substr( $filename, -1 ) == '/' && !is_dir($unzipto . DS . $safename))
				{
					// Check that we directory name is not reserved for other purposes
					if (!$subdir && in_array(strtolower($safe_file), $reserved))
					{
						// extract to the current directory
						$skipDir = 1;
						continue;
					}

					if (JFolder::create( $unzipto . DS . $safename ))
					{
						if ($this->_task != 'saveprov')
						{
							$this->_git->makeEmptyFolder($path, $afile);
							$commitMsgZip .= JText::_('COM_PROJECTS_CREATED_DIRECTORY')
								. '  ' . escapeshellarg($afile) ."\n";
						}
						$z++;
					}
					else
					{
						$stopLoop = 1;
						continue;
					}
				}
				else
				{
					// Missing parent directory?
					if ($safe_dir && !is_dir($unzipto . DS . $safe_dir))
					{
						if (JFolder::create( $unzipto . DS . $safe_dir ))
						{
							if ($this->_task != 'saveprov')
							{
								$this->_git->makeEmptyFolder($path, $adir);
								$commitMsgZip .= JText::_('COM_PROJECTS_CREATED_DIRECTORY')
									. '  ' . escapeshellarg($adir) ."\n";
							}
							$z++;
						}
						else
						{
							$stopLoop = 1;
							continue;
						}
					}

					// Copy temp file into project
					if (substr( $filename, -1 ) != '/')
					{
						if (file_exists($prefix . $path . DS . $afile))
						{
							$updated[] = $afile;
						}

						copy("zip://" . $tmp_name . "#" . $filename, $unzipto . DS . $safename);

						$fSize = filesize($prefix . $path . DS . $afile);

						// Combined size
						if ($fSize > 0)
						{
							$cSize = $cSize + $fSize;
						}

						// Check against quota
						if ($cSize > $unused)
						{
							$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_OVER_QUOTA'));
							break;
						}
					}

					// Add to Git
					if (is_file($prefix . $path . DS . $afile))
					{
						$uploaded[] = $afile;

						if ($this->_task != 'saveprov')
						{
							// Git add & commit
							$this->_git->gitAdd($path, $afile, $commitMsgZip);
							$this->_git->gitCommit($path, $commitMsgZip);

							// Store in session
							$this->registerUpdate('extracted', $afile);

							// Generate preview
							$hash = $this->_git->gitLog($path, $afile, '' , 'hash');
							$this->getFilePreview($afile, $hash, $path, $subdir);
						}

						$z++;
					}
				}
		    }

		    $zip->close();
			return $z;
		}
		else
		{
			$this->setError(JText::_('COM_PROJECT_FILES_ERROR_UNZIP_FAILED'));
			return false;
		}
	}

	/**
	 * New directory form
	 *
	 * @return     void, redirect
	 */
	protected function _newDir()
	{
		// Incoming
		$newdir = JRequest::getVar('newdir', '', 'post');

		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;
		$url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'newfolder'
			)
		);

		$view->database 	= $this->_database;
		$view->option 		= $this->_option;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->ajax 		= 1;
		$view->subdir 		= $this->subdir;
		$view->case 		= $this->_case;
		$view->tool			= $this->_tool;
		$view->url			= $url;
		$view->do  			= ($this->_case != 'files' && $this->_tool->name) ? 'do' : 'action';
		$view->path 		= $this->prefix . $this->path;
		$view->msg 			= isset($this->_msg) ? $this->_msg : '';
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
		// Incoming
		$newdir = JRequest::getVar('newdir', '', 'post');
		$newdir = ProjectsHtml::makeSafeDir($newdir);
		$createdir = $this->subdir ? $this->subdir . DS . $newdir : $newdir;

		$sync = 0;

		// Reserved names (service directories)
		$reserved = ProjectsHelper::getParamArray(
			$this->_params->get('reservedNames', '' ));

		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;
		$url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');

		// Checks
		if (!$newdir)
		{
			// Check that we have directory to create
			$this->setError(JText::_('COM_PROJECTS_ERROR_NO_DIR_TO_CREATE'));
		}
		elseif (dirname($createdir) == '.' && in_array(strtolower($createdir), $reserved))
		{
			// Check that we directory name is not reserved for other purposes
			$this->setError( JText::_('COM_PROJECTS_FILES_ERROR_DIR_RESERVED_NAME') );
		}
		elseif (!is_dir($this->prefix . $this->path . DS . $createdir))
		{
			if (!JFolder::create( $this->prefix . $this->path . DS . $createdir ))
			{
				// Failed to create directory
				$this->setError( JText::_('COM_PROJECTS_FILES_ERROR_DIR_CREATE') );
			}
			else
			{
				// Success
				$created = $this->_git->makeEmptyFolder($this->path, $createdir);
				$commitMsg = JText::_('COM_PROJECTS_CREATED_DIRECTORY') . '  ' . escapeshellarg($createdir);
				$this->_git->gitCommit($this->path, $commitMsg);

				$this->_msg = JText::_('COM_PROJECTS_CREATED_DIRECTORY') . ': ' . $newdir;

				// Force sync
				$sync = 1;
			}
		}
		else
		{
			// Directory already exists
			$this->setError( JText::_('COM_PROJECTS_FILES_ERROR_DIR_CREATE') . ' "' . $newdir . '". '
			. JText::_('COM_PROJECTS_FILES_ERROR_DIRECTORY_EXISTS') );
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

		if ($sync && $this->_case == 'files')
		{
			$obj = new Project( $this->_database );
			$obj->saveParam($this->_project->id, 'google_sync_queue', 1);
		}

		$this->_referer = $url;
		return;
	}

	/**
	 * Delete directory
	 *
	 * @return     void, redirect
	 */
	protected function _deleteDir()
	{
		// Incoming
		$dir = trim(urldecode(JRequest::getVar('dir', '')), DS);

		// cd
		chdir($this->prefix . $this->path);

		$sync = 0;

		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;
		$url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');

		// Check that we have directory to delete
		if (!$dir || !is_dir($this->prefix . $this->path . DS . $dir) || $dir == '.git' || $dir == '.')
		{
			$this->setError(JText::_('COM_PROJECTS_ERROR_NO_DIR_TO_DELETE'));
		}
		else
		{
			$commitMsg = '';
			$deleted = $this->_git->gitDelete($this->path, $dir, 'folder', $commitMsg);
			$this->_git->gitCommit($this->path, $commitMsg);

			// If directory is still there (not in Git)
			if (file_exists($this->prefix . $this->path . DS . $dir))
			{
				JFolder::delete($this->prefix . $this->path . DS . $dir);
			}

			if (!file_exists($this->prefix . $this->path . DS . $dir))
			{
				$this->_msg = JText::_('COM_PROJECTS_DELETED_DIRECTORY');

				// Force sync
				$sync = 1;
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
		$url .= $this->subdir ? '?subdir=' . urlencode($this->subdir) : '';

		if ($sync && $this->_case == 'files')
		{
			$obj = new Project( $this->_database );
			$obj->saveParam($this->_project->id, 'google_sync_queue', 1);
		}

		$this->_referer = $url;
		return;
	}

	/**
	 * Delete items
	 *
	 * @return     void, redirect
	 */
	protected function delete()
	{
		// Get incoming array of items
		$items = $this->_sortIncoming();

		if (empty($items))
		{
			$this->setError(JText::_('COM_PROJECTS_ERROR_NO_FILES_TO_DELETE'));
		}

		// cd
		chdir($this->prefix . $this->path);

		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;
		$url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');

		// Get session
		$jsession = JFactory::getSession();

		// Confirm or process request
		if ($this->_task == 'delete')
		{
			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'=>'projects',
					'element'=>'files',
					'name'=>'delete'
				)
			);

			$view->items 		= $items;
			$view->services		= $this->_rServices;
			$view->connections	= $this->_connect->getConnections();
			$view->connect		= $this->_connect;
			$view->database 	= $this->_database;
			$view->option 		= $this->_option;
			$view->project 		= $this->_project;
			$view->authorized 	= $this->_authorized;
			$view->uid 			= $this->_uid;
			$view->ajax 		= JRequest::getInt('ajax', 0);
			$view->subdir 		= $this->subdir;
			$view->case 		= $this->_case;
			$view->tool			= $this->_tool;
			$view->url			= $url;
			$view->do  			= ($this->_case != 'files' && $this->_tool->name) ? 'do' : 'action';
			$view->path 		= $this->prefix . $this->path;
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
			$deleted 	= array();
			$skipped 	= 0;
			$failed 	= 0;
			$sync 		= 0;

			// Start commit message
			$commitMsg = '';

			// Delete checked items
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

				$item = $this->subdir ? $this->subdir . DS . $item : $item;

				$remote 	= NULL;
				$service 	= 'google';
				if (!empty($this->_rServices) && $this->_case == 'files')
				{
					foreach ($this->_rServices as $servicename)
					{
						// Get stored remote connection to file
						$remote = $this->_getRemoteConnection($item, '', $servicename);
						$service = $servicename;

						if ($remote)
						{
							break;
						}
					}
				}

				if ($remote && $remote['converted'] == 1)
				{
					// Delete remote converted file
					if ($this->_connect->deleteRemoteItem(
						$this->_project->id, $service, $this->_project->created_by_user,
						$remote['id'], false))
					{
						// Include for syncing
						$deleted[] = $item;
					}
				}
				elseif ($this->_git->gitDelete($this->path, $item, $type, $commitMsg))
				{
					$deleted[] = $item;

					// Store in session
					$this->registerUpdate('deleted', $item);
				}
			}

			// Success
			if (count($deleted) > 0)
			{
				// Commit changes
				$this->_git->gitCommit($this->path, $commitMsg);
			}

			// Pass success or error message
			if ($this->getError())
			{
				$this->_message = array('message' => $this->getError(), 'type' => 'error');
			}

			// Redirect to file list
			$url .= $this->subdir ? '?subdir=' . urlencode($this->subdir) : '';

			$this->_referer = $url;
			return;
		}
	}

	/**
	 * Move file(s)
	 *
	 * @return     void, redirect
	 */
	protected function move()
	{
		// Get incoming array of items
		$items = $this->_sortIncoming();

		if (empty($items))
		{
			$this->setError(JText::_('COM_PROJECTS_ERROR_NO_FILES_TO_MOVE'));
		}

		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;
		$url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');

		// Confirmation screen
		if ($this->_task == 'move')
		{
			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'=>'projects',
					'element'=>'files',
					'name'=>'move'
				)
			);

			$view->list			= $this->getList();
		//	$view->dirs 		= $this->getFolders($this->path, '', $this->prefix, 1, true);
			$view->path 		= $this->prefix. $this->path;
			$view->items 		= $items;
			$view->database 	= $this->_database;
			$view->services		= $this->_rServices;
			$view->connections	= $this->_connect->getConnections();
			$view->connect		= $this->_connect;
			$view->option 		= $this->_option;
			$view->project 		= $this->_project;
			$view->authorized 	= $this->_authorized;
			$view->uid 			= $this->_uid;
			$view->case 		= $this->_case;
			$view->ajax 		= JRequest::getInt('ajax', 0);
			$view->tool			= $this->_tool;
			$view->subdir 		= $this->subdir;
			$view->url			= $url;
			$view->do  			= ($this->_case != 'files' && $this->_tool->name) ? 'do' : 'action';
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
			chdir($this->prefix . $this->path);

			// Get new path
			$newpath = trim(urldecode(JRequest::getVar('newpath', '')), DS);

			// New directory to be created?
			$newdir = JRequest::getVar('newdir', '');

			// Clean up directory name
			if ($newdir)
			{
				$newdir = stripslashes($newdir);
				$newdir = ProjectsHtml::makeSafeDir($newdir);
				$newdir = $this->subdir ? $this->subdir . DS . $newdir : $newdir;
			}
			if ($newdir && !file_exists( $this->prefix . $this->path . DS . $newdir ))
			{
				// Create new directory
				if (!JFolder::create( $this->prefix . $this->path . DS . $newdir ))
				{
					$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_CREATE_UPLOAD_PATH') );
				}
			}

			// Start commit message
			$commitMsg = '';

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

					if ($this->_git->gitMove($this->path, $from, $where, $type, $commitMsg))
					{
						$moved[] = $where;
					}
				}
			}
			elseif (!$this->getError())
			{
				$this->setError(JText::_('COM_PROJECTS_ERROR_NO_NEW_FILE_LOCATION'));
			}

			// After successful move actions
			if (!$this->getError())
			{
				// Delete original directory if empty
				if ($this->subdir && file_exists($this->prefix. $this->path . DS . $this->subdir))
				{
					$contents = scandir($this->prefix. $this->path. DS . $this->subdir);
					if (count($contents) <= 2)
					{
						JFolder::delete($this->prefix. $this->path. DS . $this->subdir);
					}
				}
			}

			// Success or failure message
			if ($moved)
			{
				// Commit changes
				$this->_git->gitCommit($this->path, $commitMsg);

				// Force sync
				$sync = 1;

				// Output message
				$this->_msg = JText::_('COM_PROJECTS_MOVED'). ' '
					. count($moved) . ' ' . JText::_('COM_PROJECTS_FILES_S');
			}
			elseif (empty($moved))
			{
				$this->setError( JText::_('COM_PROJECTS_ERROR_NO_NEW_FILE_LOCATION') );
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

			if ($sync && $this->_case == 'files')
			{
				$obj = new Project( $this->_database );
				$obj->saveParam($this->_project->id, 'google_sync_queue', 1);
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
	protected function share()
	{
		// Incoming
		$converted  = JRequest::getInt('converted', 0);
		$service 	= JRequest::getVar('service', 'google');

		// Combine file and folder data
		$items = $this->_sortIncoming();

		if (empty($items))
		{
			$this->setError(JText::_('COM_PROJECTS_ERROR_NO_FILES_TO_SHARE'));
		}

		if (empty($this->_rServices))
		{
			$this->setError(JText::_('COM_PROJECTS_ERROR_REMOTE_NOT_ENABLED'));
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

		$localPath  =  $this->path;
		$localPath .= $localDir ? DS . $localDir : '';

		// Check for remote connection
		if (!empty($this->_rServices) && $this->_case == 'files')
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
						$this->_message = array('message' => JText::_('COM_PROJECTS_REMOTE_PLEASE_CONNECT'),
							'type' => 'success');
						$url  = JRoute::_('index.php?option=' . $this->_option
							 . a . 'alias=' . $this->_project->alias . a . 'active=files');
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
			$this->setError(JText::_('COM_PROJECTS_FILES_SHARING_NO_REMOTE'));
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
			$view->project 		= $this->_project;
			$view->authorized 	= $this->_authorized;
			$view->uid 			= $this->_uid;
			$view->subdir 		= $this->subdir;
			$view->case 		= $this->_case;
			$view->tool			= $this->_tool;
			$view->path 		= $this->prefix . $this->path;
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
			// Required
			$mt = new \Hubzero\Content\Mimetypes();

			// Get file extention
			$parts = explode('.', $file);
			$ext   = count($parts) > 1 ? array_pop($parts) : '';
			$ext   = strtolower($ext);
			$title = $file;

			// Get convertable formats
			$formats = ProjectsGoogleHelper::getGoogleConversionExts();

			// Import remote file
			if ($remote['converted'] == 1)
			{
				// Load remote resource
				$resource = $this->_connect->loadRemoteResource($service, $this->_uid, $remote['id']);

				if (!$resource)
				{
					$this->setError(JText::_('COM_PROJECTS_FILES_SHARING_NO_REMOTE'));
				}
				else
				{
					$originalPath   = $remote['original_path'];
					$originalFormat = $remote['original_format'];

					// Incoming
					$importExt 	= JRequest::getVar('format', 'pdf', 'post');

					// Remove Google native extension from title
					if (in_array($ext, array('gdoc', 'gsheet', 'gslides', 'gdraw')))
					{
						$title = preg_replace("/.".$ext."\z/", "", $title);
					}

					// Do we have extention in name already? - take it out
					$n_parts = explode('.', $title);
					$n_ext   = count($n_parts) > 1 ? array_pop($n_parts) : '';
					$title   = implode($n_parts);
					$title 	.= '.' . $importExt;

					$newpath = $this->subdir ? $this->subdir. DS . $title : $title;

					// Do we have original file present?
					if ($originalPath && file_exists($this->prefix . $localPath . DS . $originalPath))
					{
						// Rename in Git?
						if (basename($originalPath) != $title)
						{
							// TBD
						}
					}

					// Replacing file?
					$exists = file_exists($this->prefix . $this->path. DS . $newpath) ? 1 : 0;

					// Download remote file
					if ($this->_connect->importFile($service, $this->_uid, $resource,
						$newpath, $this->prefix . $localPath, $importExt ))
					{
						// Git add & commit
						$commitMsg = JText::_('COM_PROJECTS_FILES_SHARE_IMPORTED') . "\n";
						$this->_git->gitAdd($this->path, $newpath, $commitMsg);
						$this->_git->gitCommit($this->path, $commitMsg);

						$mTypeParts = explode(';', $mt->getMimeType($this->prefix . $this->path. DS . $newpath));

						// Get local file information
						$local = array(
							'local_path' => $newpath,
							'title'		 => $title,
							'fullPath'   => $this->prefix . $localPath . DS . $newpath,
							'mimeType'	 => $mTypeParts[0],
							'md5'	 	 => ''
						);

						// Remove remote resource
						$deleted = $this->_connect->deleteRemoteItem(
							$this->_project->id, $service, $this->_uid,
							$remote['id'], false
						);

						// Create remote file for imported file
						$created = '';
						if (!$exists)
						{
							$created = $this->_connect->addRemoteFile(
								$this->_project->id, $service, $this->_uid,
								$local,  $remote['parent']
							);
						}

						// Update connection record
						$this->_connect->savePairing(
							$this->_project->id, $service, $created,
							$newpath, $remote['record_id'], $originalPath, $originalFormat, $remote['id']
						);
					}

					// Output message
					$this->_msg = JText::_('COM_PROJECTS_FILES_UNSHARE_SUCCESS') . ' ' . $title;

					// Force sync
					$sync = 1;
				}
			}
			// Export local file
			else
			{
				// Check that local file exists
				if (!file_exists($this->prefix . $localPath . DS . $remote['fpath']))
				{
					$this->setError(JText::_('COM_PROJECTS_FILES_SHARING_LOCAL_FILE_MISSING'));
				}

				$mTypeParts = explode(';', $mt->getMimeType($this->prefix . $localPath . DS . $remote['fpath']));
				$mimeType = $mTypeParts[0];

				// LaTeX?
				$tex = ProjectsCompiler::isTexFile($file, $mimeType);

				// Check format
				if (!in_array($ext, $formats) && !$tex)
				{
					$this->setError(JText::_('COM_PROJECTS_FILES_SHARING_NOT_CONVERTABLE'));
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
						'fullPath'   => $this->prefix . $localPath . DS . $remote['fpath'],
						'mimeType'	 => $mimeType,
						'md5'	 	 => ''
					);

					// Convert file
					$added = $this->_connect->addRemoteFile(
						$this->_project->id, $service, $this->_uid,
						$local, $remote['parent'], true
					);

					if ($added)
					{
						$shared[] = $added;

						// Remove original local file
						$commitMsg = JText::_('COM_PROJECTS_FILES_SHARE_EXPORTED') . "\n";
						$deleted = $this->_git->gitDelete($localPath, $remote['fpath'], 'file', $commitMsg);
						$this->_git->gitCommit($this->path, $commitMsg);

						// Remove original remote file
						$deleted = $this->_connect->deleteRemoteItem(
							$this->_project->id, $service, $this->_uid,
							$remote['id'], false
						);

						$mTypeParts = explode(';', $mt->getMimeType($this->prefix . $localPath . DS . $remote['fpath']));
						$mimeType = $mTypeParts[0];

						// Update connection record
						$this->_connect->savePairing(
							$this->_project->id, $service, $added, '', $remote['record_id'],
							$remote['fpath'], $mimeType, $remote['id']
						);

						// Output message
						$this->_msg = JText::_('COM_PROJECTS_FILES_SHARE_SUCCESS');

						// Force sync
						$sync = 1;
					}
					else
					{
						// Something went wrong
						$this->setError(JText::_('COM_PROJECTS_FILES_SHARE_ERROR_NO_CONVERT'));

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
		$url  = JRoute::_('index.php?option=' . $this->_option . a
			. 'alias=' . $this->_project->alias . a . 'active=files');
		$url .= $this->subdir ? '?subdir=' . urlencode($this->subdir) : '';

		if ($sync && $this->_case == 'files')
		{
			$obj = new Project( $this->_database );
			$obj->saveParam($this->_project->id, 'google_sync_queue', 1);
		}

		$this->_referer = $url;
		return;
	}

	/**
	 * Show revision diffs
	 *
	 * @return     void, redirect
	 */
	protected function diff()
	{
		// Incoming
		$old 	 = urldecode(JRequest::getVar( 'old', ''));
		$new 	 = urldecode(JRequest::getVar( 'new', ''));
		$mode 	 = JRequest::getVar( 'mode', $this->params->get('diffmode', 'side-by-side'));
		$file 	 = urldecode(JRequest::getVar( 'file', ''));
		$full 	 = JRequest::getInt( 'full');

		$remote 		= NULL;
		$service		= NULL;
		$connected 		= false;

		$nParts = explode('@', $new);
		$oParts = explode('@', $old);
		$diff	= NULL;

		$fpath = $this->subdir ? $this->subdir. DS . $file : $file;

		// Binary file?
		$binary	= $this->_git->isBinary($this->prefix . $this->path . DS . $fpath);

		// Do some checks
		if (count($nParts) <= 2 || count($oParts) <= 2)
		{
			$fpath = NULL;
			$this->setError(JText::_('COM_PROJECTS_ERROR_DIFF_NO_CONTENT'));
		}
		elseif (!$file)
		{
			$fpath = NULL;
			$this->setError(JText::_('COM_PROJECTS_ERROR_NO_FILES_TO_SHOW_HISTORY'));
		}
		elseif ($binary)
		{
			$fpath = NULL;
			$this->setError(JText::_('COM_PROJECTS_ERROR_DIFF_BINARY'));
		}
		else
		{
			$new = array('rev' => $nParts[0], 'hash' => $nParts[1], 'fpath' => $nParts[2], 'val' => urlencode($new) );
			$old = array('rev' => $oParts[0], 'hash' => $oParts[1], 'fpath' => $oParts[2], 'val' => urlencode($old) );

			// Check for remote connection
			if (!empty($this->_rServices) && $this->_case == 'files')
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
			$old['text'] = $this->_git->gitLog($this->path, $old['fpath'], $old['hash'], 'blob');
			$new['text'] = $this->_git->gitLog($this->path, $new['fpath'], $new['hash'], 'blob');

			// Diff class
			include_once( JPATH_ROOT . DS . 'plugins' . DS . 'projects' . DS
				. 'files' . DS . 'php-diff' . DS . 'Diff.php' );

			$context = ($old['text'] == $new['text'] || $full == 1) ? count($old['text']) : 10;
			$options = array(
				'context' => $context
			);

			// Run diff
			$objDiff = new Diff($old['text'], $new['text'], $options );

			if ($mode == 'side-by-side')
			{
				include_once( JPATH_ROOT . DS . 'plugins' . DS . 'projects' . DS . 'files'
					. DS . 'php-diff' . DS . 'Diff' . DS . 'Renderer' . DS . 'Html' . DS . 'hubSideBySide.php' );

				// Generate a side by side diff
				$renderer = new Diff_Renderer_Html_SideBySide;
				$diff = $objDiff->Render($renderer);
			}
			elseif ($mode == 'inline')
			{
				include_once( JPATH_ROOT . DS . 'plugins' . DS . 'projects' . DS . 'files'
					. DS . 'php-diff' . DS . 'Diff' . DS . 'Renderer' . DS . 'Html' . DS . 'hubInline.php' );

				// Generate inline diff
				$renderer = new Diff_Renderer_Html_Inline;
				$diff = $objDiff->Render($renderer);
			}
			else
			{
				// Print git diff
				$mode = 'git';
				$diff = $this->_git->gitDiff($this->path, $old, $new);

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

		// Redirect to file list
		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;

		$view->url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');

		$view->do  			= ($this->_case != 'files' && $this->_tool->name) ? 'do' : 'action';
		$view->config		= $this->_config;
		$view->file 		= $file;
		$view->fpath 		= $fpath;
		$view->option 		= $this->_option;
		$view->project 		= $this->_project;
		$view->case 		= $this->_case;
		$view->tool			= $this->_tool;
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
	protected function history()
	{
		// Clean incoming data
		$this->cleanData();

		// Incoming
		$checked = JRequest::getVar( 'asset', '', 'request', 'array' );
		$ajax 	 = JRequest::getInt('ajax', 0);

		// Can only view history of one file at a time
		if (empty($checked) or $checked[0] == '')
		{
			$file = urldecode(JRequest::getVar( 'asset', ''));
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
			$this->setError(JText::_('COM_PROJECTS_ERROR_NO_FILES_TO_SHOW_HISTORY'));
		}
		else
		{
			$fpath = $this->subdir ? $this->subdir. DS . $file : $file;

			// Check for remote connection
			if (!empty($this->_rServices) && $this->_case == 'files')
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

			chdir($this->prefix . $this->path);

			// Should history be paired with another file?
			$local_path = NULL;
			if ($remote && $remote['original_path'] && $remote['original_path'] != $fpath )
			{
				$local_path = $remote['original_path'];
			}

			// Local file present?
			if (file_exists( $this->prefix . $this->path . DS . $fpath))
			{
				$this->_git->sortLocalRevisions($fpath, $this->path, $versions, $timestamps);
			}
			if ($local_path && $local_path != $fpath)
			{
				$this->_git->sortLocalRevisions($local_path, $this->path, $versions, $timestamps, 1);
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
			$preview 	= $this->getFilePreview($v['file'], $hash, $this->path, $this->subdir, $pr);

			if ($preview)
			{
				$versions[$i]['preview'] = $preview;
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
		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;

		$view->url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');

		// Binary file?
		$view->binary		= $this->_git->isBinary($this->prefix . $this->path . DS . $fpath);

		$view->versions 	= $versions;
		$view->path 		= $this->prefix. $this->path;
		$view->do  			= ($this->_case != 'files' && $this->_tool->name) ? 'do' : 'action';
		$view->file 		= $file;
		$view->fpath 		= $fpath;
		$view->option 		= $this->_option;
		$view->project 		= $this->_project;
		$view->case 		= $this->_case;
		$view->tool			= $this->_tool;
		$view->uid 			= $this->_uid;
		$view->ajax			= $ajax;
		$view->title		= $this->_area['title'];
		$view->subdir 		= $this->subdir;
		$view->remote		= $remote;
		$view->connected	= $connected;
		$view->config		= $this->_config;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		$view->msg = isset($this->_msg) ? $this->_msg : '';
		return $view->loadTemplate();
	}

	/**
	 * Rename
	 *
	 * @return     void, redirect
	 */
	protected function _rename()
	{
		// Incoming
		$newname = JRequest::getVar( 'newname', '', 'post');
		$oldname = JRequest::getVar( 'oldname', '', 'post');
		$rename  = JRequest::getVar( 'rename', 'file', 'post');

		if (!$newname)
		{
			$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_NO_NAME'));
		}

		if (!$oldname)
		{
			$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_NO_OLD_NAME'));
		}

		// Make dir/file name safe
		if ($rename == 'dir')
		{
			$newname = ProjectsHtml::makeSafeDir($newname);
		}
		else
		{
			$newname = ProjectsHtml::makeSafeFile($newname);
		}

		// Compare new and old name
		if ($newname == $oldname)
		{
			$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_SAME_NAMES'));
		}

		// Set paths
		$newpath = $this->subdir ? $this->subdir . DS . $newname : $newname;
		$oldpath = $this->subdir ? $this->subdir . DS . $oldname : $oldname;

		// cd
		chdir($this->prefix . $this->path);

		$sync = 0;

		// More checks
		if ( !$this->getError())
		{
			if ($rename == 'dir')
			{
				//$ret = system('find ' . escapeshellarg($newpath));

				//if (!empty($ret))
				if (file_exists($newpath))
				{
					$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_ALREADY_EXISTS_DIR') . ' ' . $newpath);
				}
				if (!is_dir($this->prefix . $this->path . DS . $oldpath))
				{
					$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_NO_OLD_NAME'));
				}
			}
			else
			{
				// Get extensions
				$newExt = explode('.', $newname);
				$newExt = count($newExt) > 1 ? end($newExt) : '';

				$oldExt = explode('.', $oldname);
				$oldExt = count($oldExt) > 1 ? end($oldExt) : '';

				// Do not remove extension
				$newpath = $newExt ? $newpath : $newpath . '.' . $oldExt;

				if (file_exists($newpath))
				{
					$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_ALREADY_EXISTS_FILE'));
				}

				if (!is_file($this->prefix . $this->path . DS . $oldpath))
				{
					$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_NO_OLD_NAME'));
				}
			}
		}

		// Proceed with renaming
		if (!$this->getError() && $this->_task == 'renameit')
		{
			$commitMsg = '';
			$type = $rename == 'dir' ? 'folder' : 'file';
			$this->_git->gitMove($this->path, $oldpath, $newpath, $type, $commitMsg);
			$this->_git->gitCommit($this->path, $commitMsg);

			// Output message
			$this->_msg = JText::_('COM_PROJECTS_FILES_RENAMED_SUCCESS');

			// Force sync
			$sync = 1;
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
		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;

		$url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');

		$url .= $this->subdir ? '?subdir=' . urlencode($this->subdir) : '';

		if ($sync && $this->_case == 'files')
		{
			$obj = new Project( $this->_database );
			$obj->saveParam($this->_project->id, 'google_sync_queue', 1);
		}

		$this->_referer = $url;
		return;
	}

	/**
	 * Serve file (usually via public link)
	 *
	 * @param   int  	$projectid
	 * @return  void
	 */
	public function serve( $projectid = 0, $query = '')
	{
		$data = json_decode($query);

		if (!isset($data->file) || !$projectid)
		{
			return false;
		}

		$file 		= $data->file;
		$disp 		= isset($data->disp) ? $data->disp : 'inline';
		$limited 	= isset($data->limited) ? $data->limited : 0;
		$hash 		= isset($data->hash) ? $data->hash : 0;

		$database = JFactory::getDBO();

		// Load language file
		$this->loadLanguage();

		// Instantiate a project
		$obj = new Project( $database );

		$juser = JFactory::getUser();
		$uid   = $juser->get('id');

		// Get Project
		$project = $obj->getProject($projectid, $uid);

		if (!$project || ($limited == 1 && !$project->owner))
		{
			// Throw error
			JError::raiseError( 403, JText::_('PLG_PROJECTS_FILES_ERROR_ANAUTHORIZED'));
			return;
		}

		// Load component configs
		$config = JComponentHelper::getParams('com_projects');

		// Get project path
		$path  		= ProjectsHelper::getProjectPath($project->alias,
					$config->get('webpath'), 1);
		$prefix 	= $config->get('offroot', 0) ? '' : JPATH_ROOT;

		$deleteTemp = 0;

		if ($hash)
		{
			$tempPath  	= ProjectsHelper::getProjectPath($project->alias,
						$config->get('webpath'), 1, 'temp');

			if (!is_dir( $tempPath ))
			{
				// Create path
				if (!JFolder::create( $tempPath ))
				{
					// Throw error
					JError::raiseError( 404, JText::_('COM_PROJECTS_FILE_NOT_FOUND'));
					return;
				}
			}

			// Include Git Helper
			$this->getGitHelper();

			$tempName = 'temp-' . ProjectsHtml::generateCode (4 ,4 ,0 ,1 ,0 ) . basename($file);
			$serve    = $prefix. $tempPath . DS . $tempName;

			// Get file content
			$this->_git->getContent($file, $hash, $serve);

			$deleteTemp = 1;
		}
		else
		{
			$serve = $prefix . $path . DS . $file;
		}

		// Ensure the file exist
		if (!file_exists($serve))
		{
			// Throw error
			JError::raiseError( 404, JText::_('COM_PROJECTS_FILE_NOT_FOUND'));
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
			JFile::delete($serve);
		}

		if (!$result)
		{
			// Should only get here on error
			JError::raiseError( 404, JText::_('COM_PROJECTS_SERVER_ERROR') );
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
	public function restore()
	{
		// Incoming
		$file 	= urldecode(JRequest::getVar( 'asset', ''));
		$hash 	= JRequest::getVar('hash', '');

		// cd
		chdir($this->prefix. $this->path);

		// Make sure we have a file to work with
		if (!$file)
		{
			$this->setError(JText::_('PLG_PROJECTS_FILES_RESTORE_NO_FILE_SELECTED'));
		}
		elseif (!is_file( $this->prefix . $this->path . DS . $file ))
		{
			// Checkout pre-delete revision
			$this->_git->gitCheckout( $this->path, $file, $hash . '^ ' );

			// If restored
			if (is_file( $this->prefix . $this->path . DS . $file))
			{
				// Git add & commit
				$commitMsg = JText::_('PLG_PROJECTS_FILES_RESTORE_COMMIT_MESSAGE') . "\n";
				$this->_git->gitAdd($this->path, $file, $commitMsg, $new = false);
				$this->_git->gitCommit($this->path, $commitMsg);

				// Store in session
				$this->registerUpdate('restored', $file, false);
			}
			else
			{
				$this->setError(JText::_('PLG_PROJECTS_FILES_RESTORE_FAILED'));
			}
		}

		// After successful action
		if (!$this->getError())
		{
			// Force sync
			if ($this->_case == 'files')
			{
				$obj = new Project( $this->_database );
				$obj->saveParam($this->_project->id, 'google_sync_queue', 1);
			}
		}
		else
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}

		// Redirect to file list
		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;

		$url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');

		$url .= $this->subdir ? '?subdir=' . urlencode($this->subdir) : '';

		$this->_referer = $url;
		return;

	}

	/**
	 * Download file(s)
	 *
	 * @return     void, redirect
	 */
	public function download()
	{
		// Incoming
		$render 	= JRequest::getVar('render', 'download');
		$items 		= $this->_sortIncoming();
		$file 	 	= trim(urldecode(JRequest::getVar('file', '')), DS);
		$multifile	= 0;
		$deleteTemp = 0;
		$remote 	= NULL;
		$revision 	= JRequest::getVar('revision', '');

		// cd
		chdir($this->prefix. $this->path);

		if (!$file)
		{
			if (empty($items))
			{
				$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_NO_SELECTIONS_TO_DOWNLOAD'));
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
				$archive = $this->_archiveFiles($items, $this->prefix . $this->path, $this->subdir);

				if (!$archive)
				{
					$this->setError($this->getError() . ' ' .JText::_('COM_PROJECTS_FILES_ARCHIVE_ERROR'));
				}
			}
		}

		// Build file path and check for remote connection
		if ($file)
		{
			$fpath = $this->subdir ? $this->subdir. DS . $file : $file;
			// Check for remote connection
			if (!empty($this->_rServices) && $this->_case == 'files')
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
			&& file_exists($this->prefix. $this->path . DS . $fpath))
		{
			$hash   = ($remote && $remote['converted'] == 1) ? ''
					: $this->_git->gitLog($this->path, $fpath, '' , 'hash');
			$medium = $render == 'medium' ? true : false;
			$image  = ($render == 'thumb' || $render == 'medium')
					? $this->getFilePreview($file, $hash, $this->path, $this->subdir, $remote, $medium)
					: $this->path . DS . $fpath;
			$image = ($render == 'thumb' || $render == 'medium') ? JPATH_ROOT . $image : $this->prefix . $image;

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
				$this->setError(JText::_('COM_PROJECTS_ERROR_FILE_INFO_NOT_FOUND'));

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
			if ((!$remote || $remote['converted'] == 0) && !file_exists($this->prefix. $this->path . DS . $fpath))
			{
				$ok = 0;
			}

			// Get file extention
			$ext = explode('.', $fpath);
			$ext = count($ext) > 1 ? end($ext) : '';

			if ((!$remote || $remote['converted'] == 0) && $ok == 1)
			{
				// Get git object
				$hash  	  =  $this->_git->gitLog($this->path, $fpath, '' , 'hash');
				$filesize =  $this->_git->gitLog($this->path, $fpath, '' , 'size');
			}

			// Get image preview
			if (!$this->getError() && $ok == 1)
			{
				$image = $this->getFilePreview($file, $hash, $this->path, $this->subdir, $remote);
			}

			if ((!$remote || $remote['converted'] == 0) && $ok == 1)
			{
				$binary = $this->_git->isBinary($this->prefix . $this->path . DS . $fpath);

				// If non-binary and below 10MB
				if (!$binary && $filesize <= 10485760)
				{
					$content = $this->_git->showTextContent($fpath, 100);
					$content = $content ? ProjectsHtml::shortenText($content, 200) : '';
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
			$view->filesize		= isset($filesize) ? ProjectsHtml::formatSize($filesize) : NULL;
			$view->remote		= $remote;

			if ($this->getError())
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}
		elseif (!$this->getError())
		{
			// Which revision are we downloading?
			$hash 	  = JRequest::getVar('hash', '');
			$servas   = basename($file);

			// Multiple files selected
			if ($multifile)
			{
				$fullpath 	= $this->prefix . $archive['path'];
				$file  		= $archive['name'];
				$serveas	= 'Project Files ' . JFactory::getDate()->toSql() . '.zip';
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
						$this->_message = array('message' => JText::_('COM_PROJECTS_REMOTE_PLEASE_CONNECT'),
							'type' => 'success');
						$url  = JRoute::_('index.php?option=' . $this->_option
							 . a . 'alias=' . $this->_project->alias . a . 'active=files');
						$url .= '/?action=connect';
						$this->_referer = $url;
						return;
					}

					// Load remote resource
					$this->_connect->setUser($this->_project->created_by_user);
					$resource = $this->_connect->loadRemoteResource($remote['service'],
						$this->_project->created_by_user, $remote['id']);

					$openLink = $resource && isset($resource['alternateLink']) ? $resource['alternateLink'] : '';

					if (!$openLink)
					{
						// Throw error
						JError::raiseError( 404, JText::_('COM_PROJECTS_FILE_NOT_FOUND') . ' ' . $file );
						return;
					}
					$this->_referer = $openLink;
					return;
				}

				// Import & download converted file
				if ($remote && $remote['converted'] == 1 && $remote['service'] == 'google')
				{
					$temp_path 	= $this->getProjectPath ($this->_project->alias, 'temp');

					// Load remote resource
					$this->_connect->setUser($this->_project->created_by_user);
					$resource = $this->_connect->loadRemoteResource($remote['service'],
						$this->_project->created_by_user, $remote['id']);

					// Tex file?
					$tex    = ProjectsCompiler::isTexFile($remote['title'], $remote['original_format']);

					$cExt   = $tex ? 'tex' : ProjectsGoogleHelper::getGoogleImportExt($resource['mimeType']);
					$url    = ProjectsGoogleHelper::getDownloadUrl($resource, $cExt);

					$data = $this->_connect->sendHttpRequest($remote['service'], $this->_project->created_by_user, $url);

					// Clean up data from Windows characters - important!
					$data = $tex ? preg_replace('/[^(\x20-\x7F)\x0A]*/','', $data) : $data;

					$ftname = ProjectsGoogleHelper::getImportFilename($remote, $cExt);

					$this->_connect->fetchFile($data, $ftname, $this->prefix . $temp_path);
					$fullpath = $this->prefix . $temp_path . DS . $ftname;

					// Delete temp file after download
					$deleteTemp = 1;
				}
				// Download local revision
				elseif ($hash)
				{
					// Viewing revisions
					$parts = explode('/', $file);
					$serveas = trim(end($parts));

					$temppath = 'temp-' . ProjectsHtml::generateCode (4 ,4 ,0 ,1 ,0 ) . $serveas;
					$fullpath = $this->prefix. $this->path . DS .$temppath;

					// Get file content
					$this->_git->getContent($file, $hash, $temppath);

					// Delete temp file after download
					$deleteTemp = 1;
				}
				else
				{
					// Viewing current file
					$fpath 		= $this->subdir ? $this->subdir. DS . $file : $file;
					$serveas 	= urldecode(JRequest::getVar('serveas', $file));
					$fullpath	= $this->prefix. $this->path . DS . $fpath;
				}
			}

			// Ensure the file exist
			if (!file_exists($fullpath))
			{
				// Throw error
				JError::raiseError( 404, JText::_('COM_PROJECTS_FILE_NOT_FOUND') . ' ' . $fullpath );
				return;
			}

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
				JFile::delete($fullpath);
			}

			if (!$result)
			{
				// Should only get here on error
				JError::raiseError( 404, JText::_('COM_PROJECTS_SERVER_ERROR') );
			}
			else
			{
				exit;
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
		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;

		$url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');

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
	public function compile()
	{
		// Clean incoming data
		$this->cleanData();

		// Incoming
		$checked 	= JRequest::getVar( 'asset', '', 'request', 'array' );
		$commit  	= JRequest::getInt( 'commit', 0 );
		$download  	= JRequest::getInt( 'download', 0 );

		if (!$this->_params->get('latex'))
		{
			$this->setError( JText::_('COM_PROJECTS_FILES_COMPILE_NOTALOWWED') );
			return;
		}

		// Can only view history of one file at a time
		if (empty($checked) or $checked[0] == '')
		{
			$file = urldecode(JRequest::getVar( 'file', ''));
		}
		else
		{
			$file = urldecode($checked[0]);
		}

		// Path for storing temp previews
		$imagepath = trim($this->_config->get('imagepath', '/site/projects'), DS);
		$outputDir = DS . $imagepath . DS . strtolower($this->_project->alias) . DS . 'compiled';

		// Make sure output dir exists
		if (!is_dir( JPATH_ROOT . DS . $outputDir ))
		{
			jimport('joomla.filesystem.folder');

			if (!JFolder::create( JPATH_ROOT . DS . $outputDir ))
			{
				$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_CREATE_UPLOAD_PATH') );
				return;
			}
		}

		// Get LaTeX helper
		$compiler = new ProjectsCompiler();

		// Tex compiler path
		$texpath = DS . trim($this->_params->get('texpath'), DS);

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
		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;

		$url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');

		// Required
		$mt = new \Hubzero\Content\Mimetypes();

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
			$this->setError(JText::_('COM_PROJECTS_ERROR_NO_FILES_TO_COMPILE'));
		}
		else
		{
			// Get file extention
			$parts = explode('.', $file);
			$ext   = count($parts) > 1 ? array_pop($parts) : '';

			// Take out Google native extension
			$native = ProjectsGoogleHelper::getGoogleNativeExts();
			if (in_array($ext, $native))
			{
				$filename = preg_replace("/.".$ext."\z/", "", $file);
			}

			$mTypeParts = explode(';', $mt->getMimeType($filename));
			$cType = $mTypeParts[0];

			// Include subdir in path
			$fpath = $this->subdir ? $this->subdir. DS . $file : $file;

			// Binary?
			$binary = $this->_git->isBinary($this->prefix . $this->path . DS . $fpath);

			// Check for remote connection
			if (!empty($this->_rServices) && $this->_case == 'files')
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
				$this->_connect->setUser($this->_project->created_by_user);
				$resource = $this->_connect->loadRemoteResource($remote['service'], $this->_project->created_by_user, $remote['id']);

				$cExt   = $tex ? 'tex' : ProjectsGoogleHelper::getGoogleImportExt($resource['mimeType']);
				$cExt  	= in_array($cExt, array('tex', 'jpeg')) ? $cExt : 'pdf';
				$url    = ProjectsGoogleHelper::getDownloadUrl($resource, $cExt);

				// Get data
				$data = $this->_connect->sendHttpRequest($remote['service'], $this->_project->created_by_user, $url);
			}
			elseif (file_exists($this->prefix. $this->path . DS . $fpath))
			{
				$data = file_get_contents($this->prefix. $this->path . DS . $fpath);
			}
			else
			{
				$this->setError(JText::_('COM_PROJECTS_ERROR_COMPILE_NO_DATA'));
			}

			// Build temp name
			$tempBase = $tex ? 'temp__' . ProjectsHtml::takeOutExt($filename) : $filename;

			// LaTeX file?
			if ($tex)
			{
				// Clean up data from Windows characters - important!
				$data = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $data);

				// Compile and get path to PDF
				$content = $compiler->compileTex ($this->prefix. $this->path . DS . $fpath,
					$data, $texpath, JPATH_ROOT . $outputDir, 1, $tempBase);

				// Read log (to show in case of error)
				$logFile = $tempBase . '.log';
				if (file_exists(JPATH_ROOT . $outputDir . DS . $logFile ))
				{
					$log = $this->_readFile(JPATH_ROOT . $outputDir . DS . $logFile, '', true);
				}

				if (!$content)
				{
					$this->setError(JText::_('COM_PROJECTS_ERROR_COMPILE_TEX_FAILED'));
				}
			}
			elseif ($remote && $remote['converted'] == 1)
			{
				$tempBase = ProjectsGoogleHelper::getImportFilename($remote, $cExt);

				// Write content to temp file
				$this->_connect->fetchFile($data, $tempBase, JPATH_ROOT . $outputDir);
				$content = $tempBase;
			}
			// Local file
			elseif (!$this->getError() && $data)
			{
				// Make sure we can handle preview of this type of file
				if ($ext == 'pdf' || in_array($cType, $formats['images']) || !$binary)
				{
					JFile::copy($this->prefix. $this->path . DS . $fpath, JPATH_ROOT . $outputDir . DS . $tempBase);
					$content = $tempBase;
				}
			}
		}

		if ($content && file_exists(JPATH_ROOT . $outputDir . DS . $content))
		{
			$mTypeParts = explode(';', $mt->getMimeType(JPATH_ROOT . $outputDir . DS . $content));
			$cType = $mTypeParts[0];

			// Fix up object width & height
			if (in_array($cType, $formats['images']))
			{
				list($width, $height, $type, $attr) = getimagesize(JPATH_ROOT . $outputDir . DS . $content);

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
				$xserver->filename(JPATH_ROOT . $outputDir . DS . $content);
				$xserver->disposition('attachment');
				$xserver->acceptranges(false);
				$xserver->saveas($pdfName);
				$result = $xserver->serve_attachment(JPATH_ROOT . $outputDir . DS . $content, $pdfName, false);

				if (!$result)
				{
					// Should only get here on error
					JError::raiseError( 404, JText::_('COM_PROJECTS_SERVER_ERROR') );
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

				if (JFile::copy(JPATH_ROOT . $outputDir . DS . $content, $this->prefix. $this->path . DS . $where))
				{
					// Git add & commit
					$commitMsg = JText::_('COM_PROJECTS_FILES_COMPILED_COMMITTED') . "\n";
					$this->_git->gitAdd($this->path, $where, $commitMsg);
					$this->_git->gitCommit($this->path, $commitMsg);

					if ($this->_case == 'files')
					{
						$obj = new Project( $this->_database );
						$obj->saveParam($this->_project->id, 'google_sync_queue', 1);
					}

					$this->_message = array(
						'message' => JText::_('COM_PROJECTS_FILES_SUCCESS_COMPILED'),
						'type' => 'success'
					);

					$url .= $this->subdir ? '?subdir=' . urlencode($this->subdir) : '';

					// Redirect to file list
					$this->_referer = $url;
					return;
				}
			}

			// Generate preview image for browsers that connot embed pdf
			if ($cType == 'application/pdf')
			{
				// GS path
				$gspath = trim($this->_params->get('gspath'), DS);
				if ($gspath && file_exists(DS . $gspath . DS . 'gs' ))
				{
					$gspath = DS . $gspath . DS;

					$pdfName 	= $tex ? str_replace('temp__', '', basename($content)) : basename($content);
					$pdfPath 	= JPATH_ROOT . $outputDir . DS . $content;
					$exportPath = JPATH_ROOT . $outputDir . DS . $tempBase . '%d.jpg';

					exec($gspath . "gs -dNOPAUSE -sDEVICE=jpeg -r300 -dFirstPage=1 -dLastPage=1 -sOutputFile=$exportPath $pdfPath 2>&1", $out );

					if (is_file(JPATH_ROOT . $outputDir . DS . $tempBase . '1.jpg'))
					{
						$ih = new ProjectsImgHandler();
						$ih->set('image', $tempBase . '1.jpg');
						$ih->set('path', JPATH_ROOT . $outputDir . DS . $tempBase . '1.jpg');
						$ih->set('maxWidth', $view->oWidth);
						$ih->set('maxHeight', $view->oHeight);
						$ih->process();
					}
					if (is_file(JPATH_ROOT . $outputDir . DS . $tempBase . '1.jpg'))
					{
						$image = $outputDir . DS . $tempBase . '1.jpg';
					}
				}
			}
		}
		elseif (!$this->getError())
		{
			$this->setError(JText::_('COM_PROJECTS_ERROR_COMPILE_PREVIEW_FAILED'));
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
		$view->case 		= $this->_case;
		$view->option 		= $this->_option;
		$view->image		= $image;
		$view->binary		= is_file ( JPATH_ROOT . $outputDir . DS . $content )
							? $this->_git->isBinary(JPATH_ROOT . $outputDir . DS . $content)
							: $binary;

		$view->do   = ($this->_case != 'files' && $this->_tool->name) ? 'do' : 'action';

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
			$temp  	 = $this->getProjectPath ($this->_project->alias, $dir);
			$sfile 	 = $this->prefix . $temp . DS . 'sync_' . $this->_project->alias . '.hidden';
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
			$temp  	 = $this->getProjectPath ($this->_project->alias, $dir);
			$sfile 	 = $this->prefix . $temp . DS . 'sync_' . $this->_project->alias . '.hidden';
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
		$objRFile = new ProjectRemoteFile ($this->_database);
		$remote   = $objRFile->getConnection($this->_project->id, $id, $service, $local_path, $converted);

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
	public function getFilePreview( $file, $hash, $path = '', $subdir = '', $remote = NULL, $medium = false )
	{
		$image = NULL;
		$ih = new ProjectsImgHandler();

		$rthumb	= NULL;
		if ($remote)
		{
			$rthumb = substr($remote['id'], 0, 20) . '_' . strtotime($remote['modified']) . '.png';
		}
		$hash  	= $hash ? substr($hash, 0, 10) : '';

		if ($medium)
		{
			$hash .= 'med';
		}

		$filename = basename($file);
		$hashed = $hash ? $ih->createThumbName($filename, '-' . $hash, 'png') : NULL;

		$imagepath = trim($this->_config->get('imagepath', '/site/projects'), DS);
		$to_path = DS . $imagepath . DS . strtolower($this->_project->alias) . DS . 'preview';

		$from_path = $this->prefix . $path . DS;
		$from_path = $subdir ? $from_path . $subdir . DS : $from_path;

		$maxWidth 	= $medium == true ? 800 : 180;
		$maxHeight 	= $medium == true ? 800 : 180;

		if ($hashed && is_file(JPATH_ROOT. $to_path . DS . $hashed))
		{
			// First check locally generated thumbnail
			$image = $to_path . DS . $hashed;
		}
		elseif ($rthumb && is_file(JPATH_ROOT. $to_path . DS . $rthumb))
		{
			// Check remotely generated thumbnail
			$image = $to_path . DS . $rthumb;

			// Copy this over as local thumb
			if ($hashed && JFile::copy(JPATH_ROOT. $to_path . DS . $rthumb, JPATH_ROOT . $to_path . DS . $hashed))
			{
				JFile::delete(JPATH_ROOT. $to_path . DS . $rthumb);
			}
		}
		elseif ($hashed)
		{
			// Generate thumbnail locally
			if (!file_exists( $to_path ))
			{
				jimport('joomla.filesystem.folder');
				JFolder::create( JPATH_ROOT. $to_path );
			}

			// Get file extention
			$ext = explode('.', $filename);
			$ext = count($ext) > 1 ? end($ext) : '';

			// Image formats
			$image_formats = array('png', 'gif', 'jpg', 'jpeg', 'tiff', 'bmp');

			// Make sure it's an image file
			if (!in_array(strtolower($ext), $image_formats) || !is_file($from_path. $file))
			{
				return false;
			}

			if (!JFile::copy($from_path. $file, JPATH_ROOT . $to_path . DS . $hashed))
			{
				return false;
			}

			// Resize the image if necessary
			$ih->set('image',$hashed);
			$ih->set('overwrite',true);
			$ih->set('path',JPATH_ROOT. $to_path . DS);
			$ih->set('maxWidth', $maxWidth);
			$ih->set('maxHeight', $maxHeight);
			if (!$ih->process())
			{
				//$this->setError( $ih->getError() );
			}
			else
			{
				$image = $to_path . DS . $hashed;
			}
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

		$maxDownload 	= intval($this->_params->get('maxDownload', 104857600));

		// Get temp directory
		$base_path 		= $this->getProjectPath ($this->_project->alias, 'temp');
		$tarname 		= 'project_files_' . ProjectsHtml::generateCode (6 , 6 , 0 , 1 , 1 ) . '.zip';
		$path 			= $subdir ? $projectPath. DS . $subdir : $projectPath;
		$combinedSize  	= 0;

		// Check that we have our temp directiry
		if (!is_dir( $this->prefix . $base_path ))
		{
			if (!JFolder::create( $this->prefix . $base_path ))
			{
				$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_CREATE_TEMP_PATH') );
				return false;
			}
		}

		$tarpath =  $base_path . DS . $tarname;

		$zip = new ZipArchive;

		if ($zip->open($this->prefix . $tarpath, ZipArchive::OVERWRITE) === TRUE)
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
							$this->setError( JText::_('COM_PROJECTS_FILES_ERROR_OVER_DOWNLOAD_LIMIT') );
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
				$this->setError( JText::_('COM_PROJECTS_SERVER_ERROR') );
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
	protected function getGitHelper()
	{
		if (!isset($this->_git))
		{
			// Git helper
			include_once( JPATH_ROOT . DS . 'components' . DS .'com_projects' . DS . 'helpers' . DS . 'githelper.php' );
			$this->_git = new ProjectsGitHelper(
				$this->_config->get('gitpath', '/opt/local/bin/git'),
				0,
				$this->_config->get('offroot', 0) ? '' : JPATH_ROOT
			);
		}
	}

	/**
	 * Get disk space
	 *
	 * @param      string	$option
	 * @param      object  	$project
	 * @param      string  	$case
	 * @param      integer  $by
	 * @param      string  	$action
	 * @param      object 	$config
	 * @param      string  	$tool
	 *
	 * @return     string
	 */
	public function diskspace( $option, $project, $case, $by, $action, $config, $tool )
	{
		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'diskspace'
			)
		);

		if ($by != 'admin')
		{
			$document = JFactory::getDocument();
			$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'files' . DS . 'css' . DS . 'diskspace.css');
			$document->addScript('plugins' . DS . 'projects' . DS . 'files' . DS . 'js' . DS . 'diskspace.js');
		}

		// Make sure Git helper is included
		$this->getGitHelper();

		// Get path and initialize Git
		if ($by == 'admin')
		{
			$this->_project = $project;
			$path = $this->getProjectPath($project->alias, $case);
		}
		else
		{
			$path = $this->path;
		}

		$route  = 'index.php?option=' . $option . a . 'alias=' . $project->alias;

		$url 	= ($case != 'files' && $tool && $tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $tool->name)
			: JRoute::_($route . a . 'active=files' . a . 'action=diskspace');

		// Run git-gc
		if ($action == 'optimize' || $action == 'advoptimize')
		{
			$command = $action == 'advoptimize' ? 'gc --aggressive' : 'gc';
			$this->_git->callGit($path, $command);

			if ($by != 'admin')
			{
				$this->_message = array('message' => 'Disk space optimized', 'type' => 'success');
				$this->_referer = $url;
				return;
			}

			return true;
		}

		// Report .git usage?
		if ($this->_usageGit == true || $by == 'admin')
		{
			$view->dirsize 	  = self::getDiskUsage($path, $this->prefix, true);
			$view->totalspace = self::getDiskUsage($path, $this->prefix, false, false);
		}
		else
		{
			$view->totalspace = self::getDiskUsage($path, $this->prefix, false);
			$view->dirsize = $view->totalspace;
		}

		// Project params
		$view->params = new JParameter( $project->params );

		// Get publication usage
		if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_publications' . DS . 'tables' . DS . 'publication.php')
			&& JPluginHelper::isEnabled('projects', 'publications') && $by == 'admin')
		{
			$filters 					= array();
			$filters['project']  		= $project->id;
			$filters['ignore_access']   = 1;
			$filters['dev']   	 		= 1;

			$database = JFactory::getDBO();

			$objP 				= new Publication( $database );
			$pubs 				= $objP->getRecords($filters);
			$helper 	        = new PublicationHelper($database);
			$view->pubDiskUsage = $helper->getDiskUsage($project->id, $pubs);
			$view->pubQuota 	= $view->params->get('pubQuota')
								? $view->params->get('pubQuota')
								: ProjectsHtml::convertSize( floatval($config->get('pubQuota', '1')), 'GB', 'b');
		}

		$view->total  = $this->getFiles($path, '', 0, 1);
		$quota 		  = $view->params->get('quota');
		$view->quota  = $quota
			? $quota
			: ProjectsHtml::convertSize( floatval($config->get('defaultQuota', '1')), 'GB', 'b');

		$view->case 	= $case;
		$view->tool		= $tool;
		$view->action 	= $action;
		$view->by 		= $by;
		$view->project 	= $project;
		$view->option 	= $option;
		$view->config 	= $config;
		$view->title	= isset($this->_area['title']) ? $this->_area['title'] : '';
		$view->pparams 	= $this->_params;
		$view->usageGit = $this->_usageGit;

		return $view->loadTemplate();
	}

	//----------------------------------------
	// Git calls
	//----------------------------------------

	/**
	 * Git status
	 *
	 * @return     string
	 */
	public function status()
	{
		$status = $this->_git->gitStatus($this->path);

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'status'
			)
		);

		$view->status 	= $status;
		$view->option 	= $this->_option;
		$view->project 	= $this->_project;
		$view->ajax 	= JRequest::getInt('ajax', 0);

		// Build URL
		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;
		$view->url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');
		$view->subdir = $this->subdir;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Show deleted files
	 *
	 * @return     string
	 */
	public function showTrash()
	{
		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'trash'
			)
		);

		// Get deleted files
		$view->files = $this->_git->listDeleted($this->path);

		$view->option 	= $this->_option;
		$view->project 	= $this->_project;
		$view->ajax 	= JRequest::getInt('ajax', 0);
		$view->do  		= ($this->_case != 'files' && $this->_tool->name) ? 'do' : 'action';

		// Build URL
		$route  = 'index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias;
		$view->url 	= ($this->_case != 'files' && $this->_tool->name)
			? JRoute::_($route . a . 'active=tools' . a . 'action=source' . a . 'tool=' . $this->_tool->name)
			: JRoute::_($route . a . 'active=files');
		$view->subdir = $this->subdir;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}


	//----------------------------------------
	// Retrievers
	//----------------------------------------

	/**
	 * Get folders
	 *
	 * @param      string	$path
	 * @param      string  	$subdir
	 * @param      boolean  $recurse
	 * @param      boolean  $fullpath
	 * @param      array 	$exclude
	 *
	 * @return     array
	 */
	public function getFolders($path = '', $subdir = '',
		$prefix = '', $recurse = false,
		$fullpath = false, $exclude = array('.git'))
	{
		// Check path format
		$subdir = trim($subdir, DS);

		// Make full path
		$path = $path . DS . $subdir;

		// Use Joomla to get folder list
		$folders = JFolder::folders( $prefix . $path, '.', $recurse, $fullpath, $exclude);

		return $folders;
	}

	/**
	 * Get file info
	 *
	 * @param      string	$fpath
	 * @param      string  	$path
	 * @param      boolean  $fullpath
	 * @param      boolean 	$count
	 * @param      boolean  $norecurse
	 *
	 * @return     array
	 */
	public function getFileInfo($fpath = '', $path = '', $fullpath = '', $count = 0, $norecurse = 1 )
	{
		$entry = array();
		$entry['name']	= basename($fpath);
		if (!$count)
		{
			$entry['fpath']		= $fpath;
			$e 					= $norecurse ? $entry['name'] : $entry['fpath'];
			$entry['bytes']		= filesize($this->prefix . $fullpath . DS . $e);
			$entry['size']		= ProjectsHtml::formatSize($entry['bytes']);
			$entry['ext']		= ProjectsHtml::getFileAttribs( $e, $fullpath, 'ext', $this->prefix );

			// Get last commit data
			if ($this->_fileinfo && isset($this->_fileinfo[$fpath]))
			{
				$gitData = $this->_fileinfo[$fpath];
			}
			else
			{
				$gitData = $this->_git->gitLog($path, $fpath, '', 'combined');
			}
			$entry['date']  	= isset($gitData['date']) ? $gitData['date'] : NULL;
			$entry['author'] 	= isset($gitData['author']) ? $gitData['author'] : NULL;
			$entry['email'] 	= isset($gitData['email']) ? $gitData['email'] : NULL;

			// Publishing
			$entry['pid'] 				= '';
			$entry['pub_title'] 		= '';
			$entry['pub_version'] 		= '';
			$entry['pub_version_label'] = '';
			$entry['pub_num']			= 0;

			// Is file linked with a publication?
			if ($this->_publishing && $this->_pubassoc && isset($this->_pubassoc[$fpath]))
			{
				$pub = $this->_pubassoc[$fpath][0];
				$entry['pid'] 				= $pub['id'];
				$entry['pub_title']	 		= $pub['title'];
				$entry['pub_version'] 		= $pub['version'];
				$entry['pub_version_label'] = $pub['version_label'];
				$entry['pub_num']			= count($this->_pubassoc[$fpath]);
			}
		}
		return $entry;
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
	public function getMemberFiles($path = '', $subdir = '', $recurse = true)
	{
		// Check path format
		$subdir = trim($subdir, DS);
		$fullpath = $subdir ? JPATH_ROOT . $path. DS . $subdir : JPATH_ROOT . $path;

		$files = array();

		$get = $this->_readDir($fullpath, $fullpath);

		if ($get)
		{
			foreach ($get as $file)
			{
				if (substr($file,0,1) != '.' && strtolower($file) !== 'index.html')
				{
					$entry = array();
					$entry['name']	= basename($file);
					$entry['fpath']	= $file;
					$ext = explode('.', $entry['name']);
					$entry['ext'] = end($ext);
					$files[] = $entry;
				}
			}
		}

		return $files;
	}

	/**
	 * Combine file and folder data
	 *
	 * @param      array	$files
	 * @param      array  	$dirs
	 * @param      array  	$shared
	 * @param      string   $sortby
	 * @param      string   $sortdir
	 *
	 * @return     array
	 */
	protected function _sortItems($files, $dirs, $remotes, $sortby = '', $sortdir = 'ASC' )
	{
		$combined = array();
		$sorting  = array();
		$follow	  = array();
		$sortOrder = $sortdir == 'ASC' ? SORT_ASC : SORT_DESC;

		// Go through Git files
		if (count($files) > 0)
		{
			foreach ($files as $file)
			{
				if ($file['name'] == '.gitignore')
				{
					continue;
				}

				$item 				= array();
				$item['type'] 		= 'document';
				$item['item'] 		= $file;
				$item['name'] 		= $file['name'];
				$item['remote'] 	= NULL;

				if ($sortby == 'sizes')
				{
					$sorting[] = $file['bytes'];
				}
				elseif ($sortby == 'modified')
				{
					$sorting[] = strtotime(JFactory::getDate(strtotime($file['date']))); // Make UTC for comparison
				}
				else
				{
					$sorting[] = strtolower($file['name']);
				}
				$combined[]	= $item;
			}
		}

		// Go through remote files (with remote editing on)
		if (count($remotes) > 0 && !empty($remotes))
		{
			foreach ($remotes as $servicename => $remote)
			{
				if (!empty($remote))
				{
					foreach ($remote as $fpath => $r)
					{
						if ($sortby == 'sizes')
						{
							$sorting[] = NULL;
						}
						if ($sortby == 'modified')
						{
							//$sorting[] = strtotime(JFactory::getDate(strtotime($r->remote_modified))->format('Y-m-d H:i:s'));
							$sorting[] = strtotime($r->remote_modified); // already UTC
						}
						else
						{
							$sorting[] = strtolower($r->remote_title);
						}

						$item 				= array();
						$item['type'] 		= 'remote';
						$item['item'] 		= $r;
						$item['name'] 		= $r->remote_title;
						$item['remote'] 	= $servicename;

						$combined[] = $item;
					}
				}
			}

			array_multisort($sorting, $sortOrder, $combined );
		}

		// Go through directories
		if (count($dirs) > 0 && !empty($dirs))
		{
			foreach ($dirs as $dir)
			{
				$item 				= array();
				$item['type'] 		= 'folder';
				$item['item'] 		= $dir;
				$item['name'] 		= $dir;
				$item['remote'] 	= NULL;

				if ($sortby == 'sizes')
				{
					$sorting[] = NULL;
				}
				if ($sortby == 'modified')
				{
					$sorting[] = NULL;
				}
				if ($sortby == 'filename')
				{
					$sorting[]  = strtolower($dir);
				}
				$combined[] = $item;
			}
		}

		// Sort by name
		if (!empty($combined))
		{
			array_multisort($sorting, $sortOrder, $combined );
		}

		return $combined;
	}

	/**
	 * Clean incoming data
	 *
	 * @return     array
	 */
	public function cleanData()
	{
		// Clean up empty values
		$checked 	= JRequest::getVar( 'asset', '', 'request', 'array' );
		$folders 	= JRequest::getVar( 'folder', '', 'request', 'array' );

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

		JRequest::setVar( 'asset', $checked);
		JRequest::setVar( 'folder', $folders);
	}

	/**
	 * Read directory
	 *
	 * @param      string	$path
	 * @param      string  	$dirpath
	 * @param      string   $filter
	 * @param      boolean  $recurse
	 * @param      array 	$exclude
	 *
	 * @return     array
	 */
	protected function _readDir($path, $dirpath = '', $filter = '.', $recurse = true, $exclude = array(' .svn', 'CVS'))
	{
		$arr = array();

		if (!is_dir($path))
		{
			return $arr;
		}

		if ($handle = opendir($path))
		{
			while (false !== ($file = readdir($handle)))
			{
				if (($file != '.') && ($file != '..') && (!in_array($file, $exclude)))
				{
					$dir = $path . DS . $file;
					$isDir = is_dir($dir);
					if ($isDir)
					{
						$arr2 = $this->_readDir($dir, $dirpath);
						$arr = array_merge($arr, $arr2);
					}
					else
					{
						if (preg_match("/$filter/", $file)) {
							$file = $path . DS . $file;
							$file = str_replace($dirpath . DS, '', $file);
							$arr[] = $file;
						}
					}
				}
			}

			closedir($handle);
		}

		return $arr;
	}

	/**
	 * Get files from Git repository
	 *
	 * @param      string	$path
	 * @param      string  	$subdir
	 * @param      boolean  $norecurse
	 * @param      boolean  $get_count
	 * @param      integer  $limit
	 * @param      integer  $rand
	 * @param      string   $sortby
	 * @param      string 	$sortdir
	 *
	 * @return     array
	 */
	public function getFiles ($path = '', $subdir = '', $norecurse = true,
		$get_count = false, $limit = 0, $rand = 0,
		$sortby = '', $sortdir = 'ASC', $limited = false, $showUntracked = true)
	{
		// Check path format
		$subdir = trim($subdir, DS);
		$fullpath = $subdir ? $path . DS . $subdir : $path;

		$files 		= array();
		$sorting 	= array();
		$i			= 0;

		if (!is_dir($this->prefix . $path))
		{
			return $get_count ? count($files) : $files;
		}

		// Make sure Git helper is included
		$this->getGitHelper();

		// Get files
		$out = $this->_git->getFiles($path, $subdir);

		// Show untracked files?
		$untracked = array();
		if ($showUntracked)
		{
			$untracked = $this->_git->getFiles($path, $subdir, true);
		}

		// Return count
		if ($get_count)
		{
			return (count($out) + count($untracked));
		}

		// Get pub associations
		if ($this->_publishing)
		{
			$pA = new PublicationAttachment( $this->_database );
			$this->_pubassoc = $pA->getPubAssociations($this->_project->id, 'file');
		}

		// Get detailed info for all commits (much faster than individual git log)
		$this->_fileinfo = $this->_git->gitLogAll($path, $subdir);

		// Return files
		if (count($out) > 0)
		{
			if ($rand)
			{
				shuffle($out);
			}
			foreach ($out as $line)
			{
				if ($limit && $i >= $limit)
				{
					break;
				}

				$arr = explode("\t", $line);
	            $fpath = $arr[0];
				$base = basename($fpath);

				// Do not show files in child directories
				if ($norecurse == true)
				{
					$dirname = dirname($fpath);
					if ($dirname != '.' && $dirname != $subdir)
					{
						continue;
					}
				}
				else
				{
					$base = $fpath;
				}

				if (file_exists($this->prefix . $fullpath . DS . $base))
				{
					if ($limited == true)
					{
						// Get only basic file information (for quick browsing)
						$file = array();
						$file['name']	= basename($fpath);
						$file['fpath']	= $fpath;
						$file['ext']	= ProjectsHtml::getFileAttribs( basename($fpath), $fullpath, 'ext' );
					}
					else
					{
						$file = $this->getFileInfo($fpath, $path, $fullpath, $get_count, $norecurse);
						$file['untracked'] = 0;

						// Skip uncommitted files
						if (!$file['date'])
						{
							continue;
						}
					}

					if (!in_array($file, $files))
					{
						$files[] =  $file;

						if ($file['name'] != '.gitignore')
						{
							$i++;
						}
					}
				}
			}
		}

		// Go through untracked files
		if (count($untracked) > 0)
		{
			foreach ($untracked as $ut)
			{
				if ($limit && $i >= $limit)
				{
					break;
				}

				$dirname = dirname($ut);
				if ($dirname != '.' && $dirname != $subdir)
				{
					continue;
				}

				$file 						= array();
				$file['name']				= basename($ut);
				$file['fpath']				= $ut;
				$file['ext']				= ProjectsHtml::getFileAttribs( basename($ut), $fullpath, 'ext' );
				$file['date']  				= NULL;
				$file['author'] 			= NULL;
				$file['email'] 				= NULL;
				$file['bytes']				= filesize($this->prefix . $path . DS . $ut);
				$file['size']				= ProjectsHtml::formatSize($file['bytes']);
				$file['untracked'] 			= 1;
				$file['pid'] 				= '';
				$file['pub_title'] 			= '';
				$file['pub_version'] 		= '';
				$file['pub_version_label'] 	= '';
				$file['pub_num']			= 0;

				$files[] =  $file;

				$i++;
			}
		}

		return $files;
	}

	//----------------------------------------
	// Misc
	//----------------------------------------

	/**
	 * Sort incoming file/folder data
	 *
	 * @return     array
	 */
	protected function _sortIncoming()
	{
		// Clean incoming data
		$this->cleanData();

		// Incoming
		$checked = JRequest::getVar( 'asset', '', 'request', 'array' );
		$folders = JRequest::getVar( 'folder', '', 'request', 'array' );

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
		elseif ($file = JRequest::getVar( 'asset', ''))
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
		elseif ($folder = JRequest::getVar( 'folder', ''))
		{
			$combined[] = array('folder' => urldecode($folder));
		}

		return $combined;
	}

	/**
	 * Blank screen (for iframe)
	 *
	 * @return     string
	 */
	public function blank()
	{
		return 'blank';
	}

	/**
	 * Get file count
	 *
	 * @param      string	$identifier
	 * @param      string  	$case
	 *
	 * @return     integer
	 */
	public function getCount($identifier, $case = 'files')
	{
		if (!$identifier)
		{
			return 0;
		}

		$database = JFactory::getDBO();

		$obj = new Project( $database );
		if (!$obj->loadProject($identifier))
		{
			return 0;
		}

		// Include remote files?
		$pparams 	= new JParameter( $obj->params );
		$connected 	= $pparams->get('google_dir_id');
		$converted  = 0;

		if ($connected)
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
				. DS . 'com_projects' . DS . 'tables' . DS . 'project.remote.file.php');
			$objRFile = new ProjectRemoteFile ($database);
			$converted = $objRFile->getFileCount($obj->id, '', '1');
		}

		// Load component configs
		$config = JComponentHelper::getParams( 'com_projects' );

		// Get project path
		$path = ProjectsHelper::getProjectPath(
			$identifier,
			$config->get('webpath', 0),
			$config->get('offroot', 0)
		);

		// Get local file count
		$count = $this->getFiles($path, '', false, 1);

		return ($count + $converted);
	}

	/**
	 * Erase repo
	 *
	 * @param      string	$identifier
	 * @param      string  	$case
	 *
	 * @return     void
	 */
	public function eraseRepo($identifier, $case = 'files')
	{
		if (!$identifier)
		{
			return 0;
		}

		// Get project path
		$path = $this->getProjectPath($identifier, $case);

		if ($path && is_dir( $this->prefix . $path . DS . '.git' ))
		{
			// cd
			chdir($this->prefix . $path);

			// Wipe out .git directory
			exec('rm -rf .git', $out);

			return 1;
		}
		return 0;
	}

	/**
	 * Get files stats for all projects
	 *
	 * @param      array 	$aliases	Project aliases for which to compute stats
	 * @param      string 	$get
	 *
	 * @return     mixed
	 */
	public function getStats($aliases = array(), $get = 'total')
	{
		if (empty($aliases))
		{
			return false;
		}

		$files = 0;
		$diskSpace = 0;
		$commits = 0;
		$usage = 0;

		$config = JComponentHelper::getParams( 'com_projects' );
		$prefix = $config->get('offroot', 0) ? '' : JPATH_ROOT ;

		// Make sure Git helper is included
		$this->getGitHelper();

		$this->_project = new Project($this->_database);
		$this->_project->provisioned = 0;

		// Publication space
		if ($get == 'pubspace')
		{
			// Load publications component configs
			$pubconfig = JComponentHelper::getParams( 'com_publications' );
			$base_path = $pubconfig->get('webpath');

			chdir(JPATH_ROOT . $base_path);
			exec('du -sk ', $out);
			$used = 0;

			if ($out && isset($out[0]))
			{
				$kb = str_replace('.', '', trim($out[0]));
				$used = $kb * 1024;
			}

			return $used;
		}

		foreach ($aliases as $alias)
		{
			$this->_project->alias = $alias;
			$path = $this->getProjectPath($alias, 'files');

			// Make sure there is .git directory
			if (!is_dir($prefix . $path . DS . '.git'))
			{
				continue;
			}

			if ($get == 'diskspace')
			{
				$diskSpace = $diskSpace + $this->getDiskUsage($path, $prefix, $this->_usageGit);
			}
			elseif ($get == 'commitCount')
			{
				$nf = $this->_git->callGit( $path, 'ls-files --full-name ');

				if ($nf && substr($nf[0], 0, 5) != 'fatal')
				{
					$out = $this->_git->callGit($path, 'log | grep "^commit" | wc -l' );

					if (is_array($out))
					{
						$c =  end($out);
						$commits = $commits + $c;
					}
				}
			}
			else
			{
				$count = $this->getFiles($path, '', 0, true);
				$files = $files + $count;

				if ($count > 1)
				{
					$usage++;
				}
			}
		}

		if ($get == 'total')
		{
			return $files;
		}
		elseif ($get == 'usage')
		{
			return $usage;
		}
		elseif ($get == 'diskspace')
		{
			return $diskSpace;
		}
		elseif ($get == 'commitCount')
		{
			return $commits;
		}
	}

	/**
	 * Get used disk space in path
	 *
	 * @param      string 	$path
	 * @param      string 	$prefix
	 * @param      boolean 	$git
	 * @param      boolean 	$working
	 *
	 * @return     integer
	 */
	public function getDiskUsage($path = '', $prefix = '', $git = true, $working = true)
	{
		$used = 0;

		if ($path && is_dir($prefix . $path))
		{
			chdir($prefix . $path);

			$where = $git == true ? ' .[!.]*' : '';

			// Make sure there is .git directory
			if ($git == true && !is_dir($prefix . $path . DS . '.git'))
			{
				return 0;
			}

			exec('du -sk ' . $where, $out);

			if ($out && isset($out[0]))
			{
				$dir = $git == true ? '.git' : '.';
				$kb = str_replace($dir, '', trim($out[0]));
				$used = $kb * 1024;
			}
		}

		if ($git == false && $working == true)
		{
			$gitUsage = $this->getDiskUsage($path, $prefix, true);
			$used = $used - $gitUsage;
		}

		return $used;
	}

	/**
	 * Write config for direct Git access
	 *
	 * @param      string	$alias
	 * @param      object 	$config
	 * @param      string  	$case
	 *
	 * @return     void
	 */
	public function writeGitConfig($alias = '', $config = NULL, $case = 'files')
	{
		if (!$alias || !$config)
		{
			return false;
		}

		// Get site name
		$jconfig = JFactory::getConfig();
		$sitename = $jconfig->getValue('config.sitename') ? $jconfig->getValue('config.sitename') : 'myhub';

		// Get configs
		$gitConfigPath 	= '/etc/apache2/' . $sitename . '.conf.d/projects';
		$prefix 		= $config->get('offroot', 0) ? '' : JPATH_ROOT;
		$webpath 		= $prefix . DS . trim($config->get('webpath'), DS);
		$group 			= $config->get('group_prefix', 'pr-') . $alias;
		$configFile 	= $gitConfigPath . DS . 'projects.conf';

		// Load psystem configs
		$sysconfig = JComponentHelper::getParams( 'com_system' );

		// We need the config path set up by admin beforehand
		if ( !is_dir($gitConfigPath) )
		{
			return false;
		}

		// Make sure the config for this project wasn't written already
		if (is_file($configFile))
		{
			$read = $this->_readFile($configFile, '', true);

			if (preg_match("/\/" . $alias . "\/git\/" . $case . "/", $read))
			{
				return true;
			}
		}

		// Config text
		$ctext  = '############' . "\n";
		$ctext .= 'ScriptAlias /projects/' . $alias . '/git/ /usr/lib/git-core/git-http-backend/' . "\n";
		$ctext .= '<Location /projects/' . $alias . '/git/' . $case . '>' . "\n";
		$ctext .= "\t" . 'SetEnv GIT_PROJECT_ROOT ' . $webpath . DS . $alias . "\n";
		$ctext .= "\t" . 'SetEnv GIT_HTTP_EXPORT_ALL' . "\n";
		$ctext .= "\t" . 'Options +ExecCGI' . "\n";
		$ctext .= "\t" . 'AuthType Basic' . "\n";
		$ctext .= "\t" . 'AuthBasicProvider ldap' . "\n";
		$ctext .= "\t" . 'AuthName "' . $sitename . ' Project \'' . $alias . '\'"' . "\n";
		$ctext .= "\t" . 'AuthzLDAPAuthoritative on' . "\n";
		$ctext .= "\t" . 'AuthLDAPBindDN "' . $sysconfig->get('ldap_managerdn') . '"' . "\n";
		$ctext .= "\t" . 'AuthLDAPBindPassword "' . $sysconfig->get('ldap_managerpw') . '"' . "\n";
		$ctext .= "\t" . 'AuthLDAPGroupAttributeIsDN off' . "\n";
		$ctext .= "\t" . 'AuthLDAPGroupAttribute memberUid' . "\n";
		$ctext .= "\t" . 'AuthLDAPURL ' . $sysconfig->get('ldap_primary') . '/ou=users,'
			   . $sysconfig->get('ldap_basedn') . "\n";
		$ctext .= "\t" . 'Require ldap-group cn=' . $group . ',ou=groups,' . $sysconfig->get('ldap_basedn') . "\n";
		$ctext .= "\t" . 'Allow from all' . "\n";
		$ctext .= '</Location>' . "\n";
		$ctext .= '############' . "\n";

		//echo '<pre>' . $ctext . '</pre>';

		// Write to config file
		$this->_writeToFile($ctext, $configFile, true);

		// Restart apache
		exec('/etc/init.d/apache2 restart');

		return true;
	}

	/* External services */

	/**
	 * Manage connections to outside services
	 *
	 * @param      string	$service	Service name (google/dropbox)
	 * @param      string	$callback	URL to return to after authorization
	 * @return     string
	 */
	public function connect($service = '', $callback = '')
	{
		// Incoming
		$service 	= $service ? $service : JRequest::getVar('service', '');
		$reauth 	= JRequest::getInt('reauth', 0);
		$removeData = JRequest::getInt('removedata', 1);

		// Build pub url
		$route = 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias;
		$url = JRoute::_($route . a . 'active=files');

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
					$this->_msg = JText::_('You got disconnected from ') . $configs['servicename'];
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
					$this->_msg = JText::_('Successfully connected');
				}
			}

			// Refresh info
			$this->_connect->setConfigs();
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'connect'
			)
		);

		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->route		= $route;
		$view->url 			= $url;
		$view->title		= $this->_area['title'];
		$view->services		= $this->_connect->getVar('_services');
		$view->connect		= $this->_connect;

		// Get refreshed params
		$obj = new Project( $this->_database );
		$obj->load($this->_project->id);
		$view->params = new JParameter( $obj->params );

		// Get connection details for user
		$objO = new ProjectOwner( $this->_database );
		$objO->loadOwner ($this->_project->id, $this->_uid);
		$view->oparams = new JParameter( $objO->params );

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
	public function iniSync()
	{
		// Get path
		$path = $this->getProjectPath();

		// Incoming
		$ajax 	 = JRequest::getInt('ajax', 0);
		$auto 	 = JRequest::getInt('auto', 0);
		$queue 	 = JRequest::getInt('queue', 0);

		$pparams = new JParameter( $this->_project->params );

		// Timed sync?
		$autoSync = $this->_params->get('auto_sync', 0);

		// Remote service(s) active?
		if (!empty($this->_rServices) && $this->_case == 'files')
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
						$timecheck = JFactory::getDate(time() - (1 * $hr * 60));
					}
					else
					{
						$timecheck = JFactory::getDate(time() - ($autoSync * 60 * 60));
					}

					if ($synced > $timecheck)
					{
						return json_encode(array('status' => 'waiting'));
					}
				}

				// Send sync request
				$success = $this->_sync( $servicename, $path, $queue, $auto);

				// Unlock sync
				if ($success)
				{
					$this->lockSync($servicename, true);
				}

				// Success message
				$this->_rSync['message'] = JText::_('Successfully synced');
			}
		}

		$this->_rSync['auto'] = $auto;

		if (!$ajax)
		{
			return $this->view();
		}
		else
		{
			$this->_rSync['output'] = $this->view();
			return json_encode($this->_rSync);
		}
	}

	/**
	 * Sync local and remote changes since last sync
	 *
	 * @param    string		$service	Remote service name
	 * @param    string		$path		Local project path
	 * @return   void
	 */
	protected function _sync ($service = 'google', $path = '', $queue = false, $auto = false)
	{
		$path = $path ? $path : $this->getProjectPath();

		// Lock sync
		if (!$this->lockSync($service, false, $queue))
		{
			// Return error
			if ($auto == false)
			{
				$this->_rSync['error'] = JText::_('PLG_PROJECTS_FILES_SYNC_DELAYED');
			}

			return false;
		}

		// Clean up status
		$this->_writeToFile('');

		// Record sync status
		$this->_writeToFile(ucfirst($service) . ' '. JText::_('PLG_PROJECTS_FILES_SYNC_STARTED') );

		// Get time of last sync
		$obj = new Project( $this->_database );
		$obj->load($this->_project->id);
		$pparams = new JParameter( $obj->params );
		$synced = $pparams->get($service . '_sync', 1);

		// Get disk usage
		$diskUsage = $this->getDiskUsage($path, $this->prefix, $this->_usageGit);
		$quota 	   = $pparams->get('quota')
					? $pparams->get('quota')
					: ProjectsHtml::convertSize( floatval($this->_config->get('defaultQuota', '1')), 'GB', 'b');
		$avail 	   = $quota - $diskUsage;

		// Last synced remote/local change
		$lastRemoteChange = $pparams->get($service . '_last_remote_change', NULL);
		$lastLocalChange  = $pparams->get($service . '_last_local_change', NULL);

		// Get last change ID for project creator
		$lastSyncId = $pparams->get($service . '_sync_id', NULL);
		$prevSyncId = $pparams->get($service . '_prev_sync_id', NULL);

		// User ID of project creator
		$projectCreator = $this->_project->created_by_user;

		// Are we syncing project home directory or other?
		$localDir   = $this->_connect->getConfigParam($service, 'local_dir');
		$localDir   = $localDir == '#home' ? '' : $localDir;

		$localPath  = $this->prefix . $path;
		$localPath .= $localDir ? DS . $localDir : '';

		// Record sync status
		$this->_writeToFile(JText::_('PLG_PROJECTS_FILES_SYNC_ESTABLISH_REMOTE_CONNECT') );

		// Get service API - allways project creator!
		$this->_connect->getAPI($service, $projectCreator);

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
		$passed    = $synced != 1 ? ProjectsHtml::timeDifference(strtotime($startTime) - strtotime($synced)) : 'N/A';

		// Start debug output
		$output  = ucfirst($service) . "\n";
		$output .= $synced != 1 ? 'Last sync (local): ' . $synced . ' | (UTC): ' . gmdate('Y-m-d H:i:s', strtotime($synced)) . "\n" : "";
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
		$this->_writeToFile(JText::_('Getting remote directory structure') );

		// Get stored remote connections
		$objRFile = new ProjectRemoteFile ($this->_database);
		$connections = $objRFile->getRemoteConnections($this->_project->id, $service);

		// Get remote folder structure (to find out remote ids)
		$this->_connect->getFolderStructure($service, $projectCreator, $remoteFolders);

		// Record sync status
		$this->_writeToFile( JText::_('Collecting local changes') );

		// Collector for local renames
		$localRenames = array();

		$fromLocal = ($synced == $lastLocalChange || !$lastLocalChange) ? $synced : $lastLocalChange;

		// Get all local changes since last sync
		$locals = $this->_git->getChanges($path, $localPath, $fromLocal, $localDir, $localRenames, $connections );

		// Record sync status
		$this->_writeToFile( JText::_('Collecting remote changes') );

		// Get all remote files that changed since last sync
		$newSyncId  = 0;
		$nextSyncId = 0;
		if ($lastSyncId > 1)
		{
			// Via Changes feed
			$newSyncId = $this->_connect->getChangedItems($service, $projectCreator,
				$lastSyncId, $remotes, $deletes, $connections);
		}
		else
		{
			// Via List feed
			$remotes = $this->_connect->getRemoteItems($service, $projectCreator, '', $connections);
			$newSyncId = 1;
		}

		// Record sync status
		$this->_writeToFile( JText::_('Verifying remote changes') );

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
			$timedRemotes = $this->_connect->getRemoteItems($service, $projectCreator, $from, $connections);
		}

		// Record timed remote changes (for debugging)
		if (!empty($timedRemotes))
		{
			$output .= 'Timed remote changes since ' . $from . ' (' . count($timedRemotes) . '):' . "\n";
			foreach ($timedRemotes as $tr => $trinfo)
			{
				$output .= $tr . ' changed ' . date("c", $trinfo['time']) . ' status ' . $trinfo['status'] . ' ' . $trinfo['fileSize'] . "\n";
			}

			// Pick up missed changes
			if ($remotes != $timedRemotes)
			{
				$output .= empty($remotes)
					? 'Using exclusively timed changes ' . "\n"
					: 'Mixing in timed changes ' . "\n";

				$remotes = array_merge($remotes, $timedRemotes);
				array_unique($remotes);
			}
		}
		else
		{
			$output .= 'No timed changes since ' . $from . "\n";
		}

		// Error!
		if ($lastSyncId > 1 && !$newSyncId)
		{
			$this->_writeToFile( '' );
			$this->_rSync['error'] = 'Oups! Unknown sync error. Please try again at a later time.';
			$this->lockSync($service, true);
			return false;
		}

		if ($this->_connect->getError())
		{
			$this->_writeToFile( '' );
			$this->_rSync['error'] = 'Oups! Sync error: ' . $this->_connect->getError();
			$this->lockSync($service, true);
			return false;
		}

		// Collector arrays for processed files
		$processedLocal 	= array();
		$processedRemote 	= array();
		$conflicts			= array();

		// Record sync status
		$this->_writeToFile( JText::_('Exporting local changes') );

		$output .= 'Local changes:' . "\n";

		// Go through local changes
		if (count($locals) > 0)
		{
			$lChange = NULL;
			foreach ($locals as $filename => $local)
			{
				// Record sync status
				$this->_writeToFile(JText::_('Syncing ') . ' ' . ProjectsHTML::shortenFileName($filename, 30) );

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

				// Item renamed
				if ($local['status'] == 'R')
				{
					if ($local['remoteid'])
					{
						// Rename remote item
						$renamed = $this->_connect->renameRemoteItem(
							$this->_project->id, $service, $projectCreator,
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
						$parentId = $this->_connect->prepRemoteParent($this->_project->id, $service, $projectCreator, $local, $remoteFolders);

						if ($parentId != $local['rParent'])
						{
							// Move to new parent
							$moved = $this->_connect->moveRemoteItem(
								$this->_project->id, $service, $projectCreator,
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
								$this->_project->id, $service, $projectCreator,
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
							$objRFile = new ProjectRemoteFile ($this->_database);
							$objRFile->deleteRecord( $this->_project->id, $service, $local['remoteid'], $filename);
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
							$parentId = $this->_connect->prepRemoteParent($this->_project->id, $service, $projectCreator, $local, $remoteFolders);

							// Add/update operation
							if ($local['remoteid'])
							{
								// Update remote file
								$updated = $this->_connect->updateRemoteFile(
									$this->_project->id, $service, $projectCreator,
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
										$this->_project->id, $service, $projectCreator,
										basename($filename), $filename,  $parentId, $remoteFolders
									);

									$output .= '++ created remote folder: '. $filename . "\n";

								}
								elseif ($local['type'] == 'file')
								{
									// Create remote file
									$created = $this->_connect->addRemoteFile(
										$this->_project->id, $service, $projectCreator,
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
		$this->_writeToFile( JText::_('Refreshing remote file list') );

		// Get new change ID after local changes got sent to remote
		if (!empty($locals))
		{
			$newSyncId = $this->_connect->getChangedItems($service, $projectCreator,
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

		// Image handler for generating thumbnails
		$ih = new ProjectsImgHandler();

		// Make sure we have thumbnails for updates from local repo
		if (!empty($newRemotes) && $synced != 1)
		{
			$tChange = strtotime($lastRemoteChange);
			foreach ($newRemotes as $filename => $nR)
			{
				// Generate local thumbnail
				if ($nR['thumb'])
				{
					$this->_writeToFile(JText::_('Getting thumbnail for ') . ' ' . ProjectsHTML::shortenFileName($filename, 15) );
					$this->_connect->generateThumbnail($service, $projectCreator,
						$nR, $this->_config, $this->_project->alias, $ih);
				}

				$tChange = $nR['time'] > $tChange ? $nR['time'] : $tChange;
			}

			// Pick up last remote change
			$lastRemoteChange = $tChange ? date('c', $tChange) : NULL;
		}

		// Record sync status
		$this->_writeToFile( JText::_('Importing remote changes') );

		$output .= 'Remote changes:' . "\n";

		// Go through remote changes
		if (count($remotes) > 0 && $synced != 1)
		{
			// Get email/name pairs of connected project owners
			$objO = new ProjectOwner( $this->_database );
			$connected = $objO->getConnected($this->_project->id, $service);

			// Examine each change
			foreach ($remotes as $filename => $remote)
			{
				// Record sync status
				$this->_writeToFile(JText::_('Syncing ') . ' ' . ProjectsHTML::shortenFileName($filename, 30) );

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
				$commitMsg 	= 'Sync with ' . $service . ' (from change ID ' . $lastSyncId . ')' . "\n";

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
					$email = $objO->getProfileEmail($name, $this->_project->id);
				}
				$author = $this->_git->getGitAuthor($name, $email);

				// Change acting user to whoever did the remote change
				$uid = $objO->getProfileId( $email, $this->_project->id);
				if ($uid)
				{
					$this->_uid = $uid;
				}

				// Set Git author date (GIT_AUTHOR_DATE)
				$cDate = date('c', $remote['time']); // Important! Needs to be local time, NOT UTC

				// Item in directory? Make sure we have correct local dir structure
				$local_dir = dirname($filename) != '.' ? dirname($filename) : '';
				if ($remote['status'] != 'D' && $local_dir && !JFolder::exists( $this->prefix . $path . DS . $local_dir ))
				{
					if (JFolder::create( $this->prefix . $path . DS . $local_dir ))
					{
						$created = $this->_git->makeEmptyFolder($path, $local_dir);
						$commitMsg = JText::_('COM_PROJECTS_CREATED_DIRECTORY') . '  ' . escapeshellarg($local_dir);
						$this->_git->gitCommit($path, $commitMsg, $author, $cDate);
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
					if (file_exists($this->prefix . $path . DS . $filename))
					{
						// Delete in Git
						$deleted = $this->_git->gitDelete($path, $filename, $remote['type'], $commitMsg);
						if ($deleted)
						{
							$this->_git->gitCommit($path, $commitMsg, $author, $cDate);

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
						$objRFile = new ProjectRemoteFile ($this->_database);
						$objRFile->deleteRecord( $this->_project->id, $service, $remote['remoteid']);
					}
				}
				elseif ($remote['status'] == 'R' || $remote['status'] == 'W')
				{
					// Rename/move in Git
					if (file_exists($this->prefix . $path . DS . $remote['rename']))
					{
						$output .= '>> rename from: '. $remote['rename'] . ' to ' . $filename . "\n";

						if ($this->_git->gitMove($path, $remote['rename'], $filename, $remote['type'], $commitMsg))
						{
							$this->_git->gitCommit($path, $commitMsg, $author, $cDate);
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
					elseif (file_exists($this->prefix . $path . DS . $filename))
					{
						// Update
						if ($remote['type'] == 'file')
						{
							// Check md5 hash - do we have identical files?
							$md5Checksum = hash_file('md5', $this->prefix . $path . DS . $filename);
							if ($remote['md5'] == $md5Checksum)
							{
								// Skip update
								$output .= '## update skipped: local and remote versions identical: '
										. $filename . "\n";
								$updated = 1;
							}
							else
							{
								// Check file size against quota ??

								// Download remote file
								if ($this->_connect->downloadFileCurl($service, $remote['url'], $this->prefix . $path . DS . $remote['local_path']))
								//if ($this->_connect->downloadFile($service, $projectCreator, $remote, $this->prefix . $path ))
								{
									// Git add & commit
									$this->_git->gitAdd($path, $filename, $commitMsg);
									$this->_git->gitCommit($path, $commitMsg, $author, $cDate);

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
							if (JFolder::create( $this->prefix . $path . DS . $filename ))
							{
								$created = $this->_git->makeEmptyFolder($path, $filename);
								$commitMsg = JText::_('COM_PROJECTS_CREATED_DIRECTORY')
									. '  ' . escapeshellarg($filename);
								$this->_git->gitCommit($path, $commitMsg, $author, $cDate);
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

								// Record sync status
								$this->_writeToFile(JText::_('Skipping (size over limit)')
									. ' ' . ProjectsHTML::shortenFileName($filename, 30) );

								continue;
							}
							else
							{
								$avail   = $checkAvail;
								$output .= 'file size ok: ' . $remote['fileSize'] . ' bytes ' . "\n";
							}

							// Download remote file
							if ($this->_connect->downloadFileCurl($service, $remote['url'], $this->prefix
								. $path . DS . $remote['local_path']))
							//if ($this->_connect->downloadFile($service, $projectCreator, $remote, $this->prefix . $path ))
							{
								// Git add & commit
								$this->_git->gitAdd($path, $filename, $commitMsg);
								$this->_git->gitCommit($path, $commitMsg, $author, $cDate);

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
					$objRFile = new ProjectRemoteFile ($this->_database);
					$objRFile->updateSyncRecord(
						$this->_project->id, $service, $this->_uid,
						$remote['type'], $remote['remoteid'], $filename,
						$match, $remote
					);

					$lastLocalChange = date('c', time() + 1);

					// Generate local thumbnail
					if ($remote['thumb'] && $remote['status'] != 'D')
					{
						$this->_writeToFile(JText::_('Getting thumbnail for ') . ' '
						. ProjectsHTML::shortenFileName($filename, 15) );
						$this->_connect->generateThumbnail($service, $projectCreator, $remote,
							$this->_config, $this->_project->alias, $ih);
					}

					// Generate local preview
					$pr  = array('id' => $remote['remoteid'], 'modified' => $remote['modified']);
					$hash = $this->_git->gitLog($path, $filename, '' , 'hash');
					$this->getFilePreview($filename, $hash, $path, '', $pr);
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
		$length  = ProjectsHtml::timeDifference(strtotime($endTime) - strtotime($startTime));

		$output .= 'Sync complete:' . "\n";
		$output .= 'Local time: '. $endTime . "\n";
		$output .= 'UTC time: '.  JFactory::getDate()->toSql() . "\n";
		$output .= 'Sync completed in: '.  $length . "\n";

		// Determine next sync ID
		if (!$nextSyncId)
		{
			$nextSyncId  = ($newSyncId > $lastSyncId || count($remotes) > 0) ? ($newSyncId + 1) : $lastSyncId;
		}

		// Save sync time and last sync ID
		$obj = new Project( $this->_database );

		// Save sync time
		$obj->saveParam($this->_project->id, $service . '_sync', $endTime);

		// Save change id for next sync
		$obj->saveParam($this->_project->id, $service . '_sync_id', ($nextSyncId));
		$output .= 'Next sync ID: ' . $nextSyncId . "\n";

		$obj->saveParam($this->_project->id, $service . '_prev_sync_id', $lastSyncId);

		$output .= 'Saving last synced remote change at: ' . $lastRemoteChange . "\n";
		$obj->saveParam($this->_project->id, $service . '_last_remote_change', $lastRemoteChange);

		$output .= 'Saving last synced local change at: ' . $lastLocalChange . "\n";
		$obj->saveParam($this->_project->id, $service . '_last_local_change', $lastLocalChange);

		// Debug output
		$temp = $this->getProjectPath ($this->_project->alias, 'logs');
		$this->_writeToFile($output, $this->prefix . $temp . DS . 'sync.' . JFactory::getDate()->format('Y-m') . '.log', true);

		// Record sync status
		$this->_writeToFile( JText::_('Sync complete! Updating view...') );

		// Unlock sync
		$this->lockSync($service, true);

		// Clean up status
		$this->_writeToFile('Sync complete');

		$this->_rSync['status'] = 'success';
		return true;
	}

	/**
	 * Get sync status (AJAX call)
	 *
	 * @return     string
	 */
	public function syncStatus()
	{
		// Incoming
		$pid 		= JRequest::getInt('id', 0);
		$service 	= JRequest::getVar('service', 'google');
		$status 	= array('status' => '', 'msg' => time(), 'output' => '');

		// Read status file
		$rFile = $this->_readFile();

		// Report sync progress
		if ($rFile && $rFile != 'Sync complete')
		{
			$status = array('status' => 'progress', 'msg' => $rFile, 'output' => '');
		}
		elseif ($service)
		{
			// Get time of last sync
			$obj = new Project( $this->_database );
			$obj->load($pid);
			$pparams 	= new JParameter( $obj->params );
			$synced 	= $pparams->get($service . '_sync');
			$syncLock 	= $pparams->get($service . '_sync_lock', '');

			// Report last sync time
			$msg = $synced && $synced != 1
				? '<span class="faded">Last sync: ' . ProjectsHtml::timeAgo($synced, false)
				. ' ' . JText::_('COM_PROJECTS_AGO') . '</span>'
				: '';
			$status = array('status' => 'complete', 'msg' => $msg);

			// Refresh view if sync happened recently
			$timecheck = date('c', time() - (1 * 1 * 60));
			if ($synced >= $timecheck)
			{
				$status['output'] = $this->view(2);
			}

			// Timed sync?
			$autoSync = $this->_params->get('auto_sync', 0);
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
	protected function checkSyncLock ($service = 'google')
	{
		$pparams 	= new JParameter( $this->_project->params );
		$syncLock 	= $pparams->get($service . '_sync_lock', '');

		return $syncLock ? true : false;
	}

	/**
	 * Lock/unlock sync operation
	 *
	 * @param    string		$service	Remote service name
	 * @return   void
	 */
	protected function lockSync ($service = 'google', $unlock = false, $queue = 0 )
	{
		$obj = new Project( $this->_database );
		$obj->load($this->_project->id);

		$pparams 	= new JParameter( $obj->params );
		$synced 	= $pparams->get($service . '_sync');
		$syncLock 	= $pparams->get($service . '_sync_lock');
		$syncQueue 	= $pparams->get($service . '_sync_queue', 0);

		// Request to unlock sync
		if ($unlock == true)
		{
			$obj->saveParam($this->_project->id, $service . '_sync_lock', '');
			$this->_rSync['status'] = 'complete';

			// Clean up status
			$this->_writeToFile('Sync complete');

			// Repeat sync? (another request in queue)
			if ($syncQueue > 0)
			{
				// Clean up queue
				$obj->saveParam($this->_project->id, $service . '_sync_queue', 0);

				// Sync request
				//$this->_sync( $service, '', false, true);
			}

			return true;
		}

		// Is there time lock?
		$timeLock = $this->_params->get('sync_lock', 0);
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
				$obj->saveParam($this->_project->id, $service . '_sync_queue', 1);
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
		$obj->saveParam($this->_project->id, $service . '_sync_lock', $this->_uid);
		$this->_rSync['status'] = 'progress';
		return true;
	}

	/* External calls */

	/**
	 * Event call to return data for a specific project
	 *
	 * @param      string  $identifier 		Project alias OR ID
	 * @param      string  $action 			Action
	 * @param      integer $uid 			User ID
	 * @param      json    $data			Data object
	 * @param      string  $case			Directory where .git sits ('files' or 'tool:toolname')
	 * @param      string  $format
	 * @return     array   Return array of html
	 */
	public function onProjectExternal (
		$identifier = NULL, $action = '', $uid = NULL,
		$data = NULL, $case = 'files', $format = 'json'
	)
	{
		$arr = array(
			'project' => $identifier,
			'action'  => $action,
			'results' => '',
			'error'   => false,
			'message' => ''
		);

		// We do need a project id
		if ($identifier === NULL)
		{
			$arr['error']	= true;
			$arr['message'] = JText::_('PLG_PROJECTS_FILES_ERROR_NO_PROJECT_ID');
			return $arr;
		}

		// Include
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
			.'com_projects' . DS . 'tables' . DS . 'project.php');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
			.'com_projects' . DS . 'tables' . DS . 'project.activity.php');

		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'helper.php');
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'html.php');

		// Get joomla libraries
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// Load language file
		$this->loadLanguage();

		$this->_database = JFactory::getDBO();
		$this->_uid = $uid;

		if (!$this->_uid)
		{
			$juser = JFactory::getUser();
			$this->_uid = $juser->get('id');
		}

		// Get project and check authorization
		$objP = new Project( $this->_database );
		$this->_project = $objP->getProject($identifier, $this->_uid);
		if (!$this->_project || !$this->_project->owner)
		{
			$arr['error']	= true;
			$arr['message'] = !$this->_project
							  ? JText::_('PLG_PROJECTS_FILES_ERROR_UNABLE_TO_LOAD_PROJECT')
							  : JText::_('PLG_PROJECTS_FILES_ERROR_ANAUTHORIZED');
			return $arr;
		}

		$this->_case 	 = $case ? $case : 'files';
		$this->_option   = 'com_projects';
		$this->_audience = 'external';
		$this->_data	 = $data;
		$this->_format	 = $format;

		// Include Git Helper
		$this->getGitHelper();

		// MIME types
		$this->mt = new \Hubzero\Content\Mimetypes();

		// Get path
		$this->path = $this->getProjectPath();

		// Something is wrong
		if (!$this->path)
		{
			$arr['error']	= true;
			$arr['message'] = JText::_('PLG_PROJECTS_FILES_ERROR_REPO_NOT_FOUND');
			return $arr;
		}

		// Incoming
		$this->subdir 	= isset($this->_data->subdir) ? $this->_data->subdir : trim(urldecode(JRequest::getVar('subdir', '')), DS);

		$juri = JURI::getInstance();
		$base = rtrim($juri->base(), DS);
		if (substr($base, -13) == 'administrator')
		{
			$base = substr($base, 0, strlen($base)-13);
		}
		$this->base = $base;

		// File actions
		switch ($action)
		{
			case 'list':
			default:
				$arr['results'] = $this->getList();
				break;

			case 'get':
				$arr['results'] = $this->getMetadata();
				break;

			case 'insert':
				$arr['results'] = $this->insertFile();
				break;

			case 'rename':
				$arr['results'] = $this->renameFile();
				break;
		}

		// Pass success or error message
		if ($this->getError())
		{
			$arr['error']	 = true;
			$arr['message']  = $this->getError();
		}
		else
		{
			$arr['message']  =  (isset($this->_msg) && $this->_msg)
							? $this->_msg : JText::_('PLG_PROJECTS_FILES_MESSAGE_SUCCESS');
		}

		// Return data
		return $format == 'json' ? json_encode($arr) : $arr;
	}

	/**
	 * Rename
	 *
	 * Rename file
	 *
	 * @return     mixed
	 */
	public function renameFile()
	{
		// Incoming
		$oldpath  	= isset($this->_data->oldpath) ? $this->_data->oldpath : urldecode(JRequest::getVar( 'oldpath', '' ));
		$newpath   	= isset($this->_data->newpath) ? $this->_data->newpath : urldecode(JRequest::getVar( 'newpath', '' ));

		if (!$oldpath || !$newpath)
		{
			$this->setError(JText::_('Cannot rename without valid paths'));
			return false;
		}

		// Get extensions
		$newExt = explode('.', $newpath);
		$newExt = count($newExt) > 1 ? end($newExt) : '';

		$oldExt = explode('.', $oldpath);
		$oldExt = count($oldExt) > 1 ? end($oldExt) : '';

		// Keep original extension (important)
		$newpath = $newExt ? $newpath : $newpath . '.' . $oldExt;

		$newdir  = dirname($newpath) == '.' ? '' : dirname($newpath) . DS;
		$newname = ProjectsHtml::makeSafeFile(basename($newpath));
		$newpath = $newdir . $newname;

		// Compare new and old name
		if ($newpath == $oldpath)
		{
			$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_SAME_NAMES'));
			return false;
		}

		// If another file with the same name exists in this path
		if (is_file($this->prefix . $this->path . DS . $newpath))
		{
			$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_ALREADY_EXISTS_FILE'));
			return false;
		}

		if (!is_file($this->prefix . $this->path . DS . $oldpath))
		{
			$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_NO_OLD_NAME'));
			return false;
		}

		// Move file
		$commitMsg = '';
		$this->_git->gitMove($this->path, $oldpath, $newpath, 'file', $commitMsg);
		$this->_git->gitCommit($this->path, $commitMsg);

		// On success return uploaded file metadata
		if (!$this->getError())
		{
			return $this->getMetadata( array($newpath) );
		}

		return false;
	}

	/**
	 * Get file list
	 *
	 * List of items in project or project subdirectory
	 *
	 * @return     mixed
	 */
	public function getList()
	{
		// Incoming
		$sortby  = isset($this->_data->sortby) ? $this->_data->sortby : JRequest::getVar( 'sortby', 'name' );
		$sortdir = isset($this->_data->sortdir) ? $this->_data->sortdir : JRequest::getVar( 'sortdir', 'ASC' );
		$filter  = isset($this->_data->filter) ? $this->_data->filter : urldecode(JRequest::getVar( 'filter', '' ));

		// Get list of files from repo
		$docs 	 = $this->_git->getFiles($this->path, $this->subdir);

		// Get detailed info for all commits (much faster than individual git log)
		$this->_fileinfo = $this->_git->gitLogAll($this->path);

		$items 		= array();
		$sorting 	= array();
		$parents	= array();

		if ($docs)
		{
			foreach ($docs as $file)
			{
				$metadata = $this->getItemMetadata(trim($file));
				if ($metadata)
				{
					// Search filter applied
					$getParents = 1;
					if ($filter
						&& strpos(trim($metadata->name), trim($filter)) === false
						&& strpos(trim($metadata->dirname), trim($filter)) === false)
					{
						continue;
					}
					elseif ($filter)
					{
						$getParents = 0;
					}

					// Do we have a parent?
					if ($getParents && $metadata->dirname && !in_array($metadata->dirname, $parents))
					{
						$obj 				= new stdClass;
						$obj->type			= 'folder';
						$obj->name			= basename($metadata->dirname);
						$obj->localPath		= $metadata->dirname;
						$obj->dirname 		= dirname($metadata->dirname) == '.' ? NULL : dirname($metadata->dirname);
						$obj->parents 		= $this->getParents($obj->dirname);

						$items[] 			= $obj;
						$sorting[] 			= strtolower($metadata->dirname);
						$parents[]			= $metadata->dirname;
					}

					if (basename($file) == '.gitignore')
					{
						continue;
					}

					$items[] 	= $metadata;
					$sorting[] 	= strtolower($metadata->localPath);
				}
			}
		}

		$sortOrder = $sortdir == 'ASC' ? SORT_ASC : SORT_DESC;
		array_multisort($sorting, $sortOrder, $items );

		return $items;
	}

	/**
	 * Insert file(s) into project via upload or copy (TBD)
	 *
	 * @return     returns array with inserted file(s) info
	 */
	public function insertFile()
	{
		// Incoming
		$dataUrl  = isset($this->_data->dataUrl)
					? $this->_data->dataUrl
					: JRequest::getVar( 'dataUrl', '' ); // path to local file to copy from
		$results  = array();
		$assets   = array();

		// Via local copy
		if ($dataUrl && is_file($dataUrl))
		{
			$file 		= ProjectsHtml::makeSafeFile(basename($dataUrl));
			$localPath	= $this->subdir ? $this->subdir . DS . $file : $file;
			$fullPath	= $this->prefix . $this->path . DS . $localPath;

			if (!JFile::copy($dataUrl, $fullPath))
			{
				$this->setError(JText::_('Error inserting file into project'));
				return false;
			}

			// Git add & commit
			$commitMsg 		= '';
			$this->_git->gitAdd($this->path, $localPath, $commitMsg);
			$this->_git->gitCommit($this->path, $commitMsg);

			// Store in session
			$this->registerUpdate('uploaded', $localPath);

			// After upload actions
			$this->onAfterUpdate();

			$assets[] = $localPath;
		}
		else
		{
			// Via upload
			$this->_task = 'save';

			// Incoming files
			$files = JRequest::getVar( 'upload', '', 'files', 'array' );

			// Get file paths
			if (!empty($files['name']))
			{
				for ($i=0; $i < count($files['name']); $i++)
				{
					$file = $files['name'][$i];
					$file = ProjectsHtml::makeSafeFile($file);
					$assets[] = $this->subdir ? $this->subdir . DS . $file : $file;
				}
			}

			// Perform upload
			$this->save();

			// After upload actions
			$this->onAfterUpdate();
		}

		// On success return uploaded file metadata
		if (!$this->getError())
		{
			return $this->getMetadata( $assets);
		}

		return $results;
	}

	/**
	 * Get file metadata
	 *
	 * Get metadata on requested file(s)
	 *
	 * @return     mixed
	 */
	public function getMetadata( $checked = NULL)
	{
		// Clean incoming data
		$this->cleanData();

		$assets = isset($this->_data->assets) ? $this->_data->assets : JRequest::getVar( 'asset', '', 'request', 'array' );

		// Incoming
		$checked = $checked ? $checked : $assets;

		if (empty($checked))
		{
			$this->setError(JText::_('PLG_PROJECTS_FILES_ERROR_NO_FILES_SELECTED'));
			return false;
		}

		$files = array();

		// Go through files and collect metadata
		foreach ($checked as $file)
		{
			$metadata = $this->getItemMetadata(trim($file));
			if ($metadata)
			{
				$files[] = $metadata;
			}
		}

		if (empty($files))
		{
			$this->setError(JText::_('PLG_PROJECTS_FILES_ERROR_NO_FILES_RETRIEVED'));
			return false;
		}

		return $files;
	}

	/**
	 * Get item parent directories
	 *
	 * @return     mixed
	 */
	public function getParents($dirname = '')
	{
		$parents = new stdClass;

		$dirParts = explode('/', $dirname);

		$i = 1;
		$collect = '';

		foreach ($dirParts as $part)
		{
			$collect .= DS . $part;
			$parents->$i = trim($collect, DS);
			$i++;
		}

		return $parents;
	}

	/**
	 * Get file metadata
	 *
	 * @return     mixed
	 */
	public function getItemMetadata($file = '', $hash = '')
	{
		$rFile = isset($this->_data->file) ? $this->_data->file : JRequest::getVar( 'file', '' );
		$rHash = isset($this->_data->hash) ? $this->_data->hash : JRequest::getVar( 'hash', '' );

		$file = trim($file) ? $file : $rFile;
		$hash = trim($hash) ? $hash : $rHash;

		if ($file == '')
		{
			return false;
		}

		// Build file object
		$obj 				= new stdClass;
		$obj->type			= 'file';
		$obj->name			= basename($file);
		$obj->localPath		= $this->subdir ? $this->subdir . DS . $file : $file;
		$fullPath			= $this->prefix . $this->path . DS . $file;

		// Dir path
		$obj->dirname 		= dirname($obj->localPath) == '.' ? NULL : dirname($obj->localPath);

		// Get all parents
		$obj->parents		= $this->getParents($obj->dirname);

		if (!$hash && !file_exists($fullPath) )
		{
			return false;
		}
		if ($hash && !file_exists($fullPath))
		{
			$obj->size 		= $this->_git->gitLog($this->path, $obj->localPath, $hash, 'size');
		}
		else
		{
			$obj->size		= filesize($fullPath);
		}

		$obj->formattedSize = $obj->size ? ProjectsHtml::formatSize($obj->size) : NULL;

		// Get file extention
		$parts 		= explode('.', $file);
		$ext   		= count($parts) > 1 ? array_pop($parts) : '';
		$obj->ext   = strtolower($ext);

		// Get last commit data
		if (isset($this->_fileinfo) && $this->_fileinfo && isset($this->_fileinfo[$obj->localPath]))
		{
			$gitData = $this->_fileinfo[$obj->localPath];
		}
		else
		{
			$gitData = $this->_git->gitLog($this->path, $obj->localPath, $hash, 'combined');
		}

		if (!$gitData)
		{
			return false;
		}
		$obj->date			= isset($gitData['date']) ? $gitData['date'] : NULL;
		$obj->author 		= isset($gitData['author']) ? $gitData['author'] : NULL;
		$obj->email 		= isset($gitData['email']) ? $gitData['email'] : NULL;
		$obj->md5hash		= hash_file('md5', $fullPath);
		$obj->commitHash 	= $hash ? $hash : $gitData['hash'];

		// Get public link
		if ($this->_audience == 'external')
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php');

			$objSt = new ProjectPubStamp( $this->_database );

			// Build reference for download URL
			$reference = array(
				'file' 		=> $obj->localPath,
				'disp' 		=> 'attachment',
				'hash' 		=> $obj->commitHash,
				'limited' 	=> 2
			);

			$expires = JFactory::getDate('+15 minutes')->toSql();

			// Get short lived download URL
			$stamp = $objSt->registerStamp($this->_project->id, json_encode($reference), 'files', 0, $expires);
			if ($stamp)
			{
				$obj->downloadUrl = $this->base . DS . 'projects' . DS . 'get?s=' . $stamp;
			}
		}
		else
		{
			$obj->fullPath = $fullPath;
		}

		// Mime type
		if (isset($this->mt))
		{
			$mTypeParts = explode(';', $this->mt->getMimeType($fullPath));
			$obj->mimeType   = ProjectsHtml::fixUpMimeType($obj->name, $mTypeParts[0]);
		}

		return $obj;
	}

	/* Get Paths */

	/**
	 * Get project path
	 *
	 * @param      string	$identifier
	 * @param      string  	$case
	 *
	 * @return     string
	 */
	public function getProjectPath($identifier = NULL, $case = NULL)
	{
		if (!isset($this->_project) || !is_object($this->_project))
		{
			return NULL;
		}

		// Provisioned project with no repo
		if ($this->_project->provisioned == 1 && !$this->_project->id)
		{
			return $this->getMembersPath();
		}

		if (!$identifier)
		{
			$identifier = $this->_project->alias;
		}
		if (!$case)
		{
			$case = isset($this->_case) && $this->_case ? $this->_case : 'files';
		}

		if (!$case || !$identifier )
		{
			$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_GET_PROJECT_PATH') );
			return false;
		}

		// Build upload path for project files
		$dir = strtolower($identifier);
		$webdir = DS . trim($this->_config->get('webpath'), DS);

		// Do we need to create master directory off the web root?
		if (!$this->prefix && !is_dir($webdir))
		{
			$this->setError( JText::_('Master directory does not exist. Administrator must fix this! ')  . $webdir );
			return false;
		}

		// Do we have an tool repo?
		if (preg_match("/tools:/", $case))
		{
			// Get tools params
			$aPlugin = JPluginHelper::getPlugin( 'projects', 'tools' );
			$aParams = new JParameter($aPlugin->params);

			$reponame = isset($this->_tool->name) && $this->_tool->name
				? $this->_tool->name : preg_replace("/tools:/", "", $case);
			$path     = ($aParams->get('repo_location') == 1)
						? str_replace('/projects', '/tools', $webdir)
						: $webdir . DS . $dir. DS . 'tools';
			$path    .= DS . strtolower($reponame);
		}
		else
		{
			$path  = $webdir. DS . $dir. DS . $case;
		}

		if (!is_dir( $this->prefix. $path ))
		{
			// Create path
			if (!JFolder::create( $this->prefix. $path ))
			{
				$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_CREATE_UPLOAD_PATH') ) . $this->prefix. $path ;
			}
		}

		// Initialize Git
		if (($case == 'files' || preg_match("/tools:/", $case)) && is_dir( $this->prefix. $path ))
		{
			$this->_git->iniGit($path);
		}

		return $path;
	}

	/**
	 * Get path to member dir (for provisioned projects)
	 *
	 * @return     string
	 */
	public function getMembersPath()
	{
		// Get members config
		$mconfig = JComponentHelper::getParams( 'com_members' );

		// Build upload path
		$dir  = \Hubzero\Utility\String::pad( $this->_uid );
		$path = DS . trim($mconfig->get('webpath', '/site/members'), DS) . DS . $dir . DS . 'files';

		if (!is_dir( JPATH_ROOT . $path ))
		{
			if (!JFolder::create( JPATH_ROOT . $path ))
			{
				$this->setError(JText::_('UNABLE_TO_CREATE_UPLOAD_PATH'));
				return;
			}
		}

		return $path;
	}
}
