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
		$this->_config 		=& JComponentHelper::getParams('com_projects');
		$this->_valid_cases = array('files');

		$this->_use_alias 	= $this->_config->get('use_alias', 0);
		$this->gitpath 	  	= $this->_config->get('gitpath', '/opt/local/bin/git');	
		$this->prefix     	= $this->_config->get('offroot', 0) ? '' : JPATH_ROOT ;
		
		$this->subdir		= '';
		$this->filters		= array();
		
		// Output collectors
		$this->_referer 	= '';
		$this->_message 	= array();		
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
		// Get project path
		$path = $this->getProjectPath($project->alias);
	
		// Get file count
		$counts['files'] = $path ? $this->getFiles($path, '', 0, 1) : 0;
		
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
	 * @param      string  $case			Directory where .git sits ('files' or 'app:appname')
	 * @return     array   Return array of html
	 */
	public function onProject ( $project, $option, $authorized, 
		$uid, $msg = '', $error = '', $action = '', 
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
		if (is_array( $areas )) {
			if(empty($this->_area) || !in_array($this->_area['name'], $areas)) {
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
			return;
		}
		
		$this->_project = $project;	
		$this->_app		= NULL;
		
		// Are we returning HTML?
		if ($returnhtml) 
		{
			// Load language file
			JPlugin::loadLanguage( 'plg_projects_files' );
			
			// Enable views
			ximport('Hubzero_View_Helper_Html');
			ximport('Hubzero_Plugin_View');
			
			$database =& JFactory::getDBO();
								
			// Get joomla libraries
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');
			
			// App repo ? Load app
			if (preg_match("/apps:/", $case))
			{
				$reponame = preg_replace( "/apps:/", "", $case);
				
				// Get app library
				require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' 
					. DS . 'com_apps' . DS . 'tables' . DS . 'app.php');

				$objA = new App( $database );
				$this->_app = $objA->getFullRecord($reponame, $this->_project->id);
				
				Hubzero_Document::addPluginStylesheet('projects', 'apps');
				JPlugin::loadLanguage( 'plg_projects_apps' );
			}

			$this->_case = $case ? $case : 'files';
																
			// Set vars									
			$task = $action ? $action : JRequest::getVar('action', '');
			$this->_msg = $msg;
			if ($error) 
			{
				$this->setError($error);	
			}								
			$this->_task = $action ? $action : JRequest::getVar('action','');
			$this->_database = $database;
			$this->_option = $option;
			$this->_authorized = $authorized;
			$this->_uid = $uid;
			if (!$this->_uid) 
			{
				$juser =& JFactory::getUser();
				$this->_uid = $juser->get('id');
			}
			
			// Get JS and CSS
			$document =& JFactory::getDocument();
			ximport('Hubzero_Document');
			if($this->_task != 'browser')
			{
				Hubzero_Document::addPluginScript('projects', 'files');
			}
			Hubzero_Document::addPluginStylesheet('projects', 'files');
						
			switch ($this->_task) 
			{				
				case 'save':
				case 'saveprov':  
					$arr['html'] = $this->save(); 
					break;
			
				case 'status': 
					$arr['html'] = $this->status(); 
					break;
				
				case 'download': 
					$arr['html'] = $this->download(); 
					break;
													
				case 'delete':
				case 'removeit':  
					$arr['html'] = $this->delete(); 
					break;
					
				case 'deletedir':
					$arr['html'] = $this->_deleteDir(); 
					break;
				
				case 'savedir':
					$arr['html'] = $this->_saveDir(); 
					break;
				
				case 'move':
				case 'moveit':  
					$arr['html'] = $this->move(); 
					break;
				
				case 'rename':
				case 'renameit':  
					$arr['html'] = $this->_rename(); 
					break;
				
				case 'history':
					$arr['html'] = $this->history(); 
					break;
				
				case 'browser': 
					$arr['html'] = $this->browser(); 
					break;
				
				case 'diskspace':
					$arr['html'] = $this->diskspace( 
						$option, $project, $this->_case, 
						$this->_uid, $this->_task, $this->_config, $this->_app); 
					break;				
				
				case 'blank': 
					$arr['html'] = $this->blank(); 
					break;
				
				case 'page': 
				case 'browse':
				case 'newdir':
				default: 
					$arr['html'] = $this->view(); 
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
	 * View of project updates
	 * 
	 * @return     string
	 */
	public function view() 
	{
		// Build query
		$filters = array();
		$filters['limit'] 	 = JRequest::getInt('limit', 100);
		$filters['start']    = JRequest::getInt( 'limitstart', 0);
		$filters['sortby']   = JRequest::getVar( 'sortby', 'filename');
		$filters['sortdir']  = JRequest::getVar( 'sortdir', 'ASC');
						
		// Get path and initialize Git
		$path = $this->getProjectPath();
		
		// Something is wrong
		if (!$path)
		{
			// Output error
			$view = new Hubzero_Plugin_View(
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
		
		$this->iniGit($path);
			
		// Are we in a subdirectory?
		$subdir = $this->subdir ? $this->subdir : urldecode(JRequest::getVar('subdir', ''));
		$subdir = $this->formatPath($subdir);
		
		// Does subdirectory exist?
		if (!is_dir($this->prefix . $path . DS . $subdir)) 
		{
			$subdir = '';
		}
		
		// Catch error when uploading file over server limit
		$sizelimit = ProjectsHtml::formatSize($this->_config->get('maxUpload', '104857600'));
		if (isset($_SERVER['CONTENT_LENGTH']) 
			&& $_SERVER['CONTENT_LENGTH'] >= $this->_config->get('maxUpload', '104857600')) 
		{			
			$error = JText::_('COM_PROJECTS_FILES_ERROR_EXCEEDS_LIMIT') . ' ' . $sizelimit 
				. '. ' . JText::_('COM_PROJECTS_FILES_ERROR_TOO_LARGE_USE_OTHER_METHOD');
			
			// Set error message
			$this->_message = array('message' => $error, 'type' => 'error');
				
			// Redirect to file list
			$url  = JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias
				 . a . 'active=files');
			$url .= $subdir ? '/?subdir=' .urlencode($subdir) : '';
			$this->_referer = $url;
			return;
		}
								
		// Get contents of current dir
		$files = $this->getFiles($path, $subdir, 1, 0, 0, 0, $filters['sortby'], $filters['sortdir']);
				
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'browse'
			)
		);
		
		// Get used space 
		if (is_dir($this->prefix . $path))
		{
			chdir($this->prefix . $path);
			exec('du -sk .git', $out);
			$kb = str_replace('.git', '', trim($out[0]));
			$view->dirsize = $kb * 1024;
		}
		else
		{
			$view->dirsize = 0;
		}
				
		$view->total 		= $this->getFiles($path, '', 0, 1);		
		$view->dirs 		= $this->getFolders($path, $subdir);
		$view->files 		= $files;
		$view->combined 	= $this->_combineFilesAndDirs($view->files, $view->dirs, $filters['sortby'], $filters['sortdir']);
		
		$view->params 		= new JParameter($this->_project->params);
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->filters 		= $filters;
		$view->subdir 		= $subdir;
		$view->task			= $this->_task;
		$view->case 		= $this->_case;
		$view->app			= $this->_app;
		$view->config 		= $this->_config;
		$view->sizelimit 	= $sizelimit;
		$view->publishing	= $this->_publishing;
		$view->title		= $this->_area['title'];
		$view->quota 		= $view->params->get('quota') 
							? $view->params->get('quota') 
							: ProjectsHtml::convertSize(floatval($this->_config->get('defaultQuota', '1')), 'GB', 'b');
		$view->fileparams 	= $this->_params;		
		
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
		if ($this->_project->provisioned == 1 && !$this->_project->id)
		{
			// Get members config
			$mconfig =& JComponentHelper::getParams( 'com_members' );
				
			// Build upload path
			$dir  = Hubzero_View_Helper_Html::niceidformat( $this->_uid );
			$path = JPATH_ROOT;
			
			if (substr($mconfig->get('webpath', '/site/members'), 0, 1) != DS) 
			{
				$path .= DS;
			}
			$path .= $mconfig->get('webpath', '/site/members') . DS . $dir . DS . 'files';

			if (!is_dir( $path )) 
			{
				if (!JFolder::create( $path, 0777 )) 
				{
					$this->setError(JText::_('UNABLE_TO_CREATE_UPLOAD_PATH'));
					return;
				}
			}
		}
		else 
		{		
			// Get path and initialize Git
			$path = $this->getProjectPath($this->_project->alias, $this->_case);
			$this->iniGit($path);
		}	

		// Are we in a subdirectory?
		$subdir = urldecode(JRequest::getVar('subdir', ''));
		$subdir = $this->formatPath($subdir);
		
		// Does subdirectory exist?
		if (!is_dir($this->prefix. $path. DS . $subdir)) 
		{
			$subdir = '';
		}
														
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'upload',
				'layout' => $content
			)
		);
		
		if ($this->_project->provisioned == 1 && !$this->_project->id)
		{
			$view->dirs = $this->getFolders($path, $subdir);
			$view->files = $this->getMemberFiles($path, $subdir);
		}		
		elseif (in_array($content, $this->_valid_cases)) 
		{	
			$view->dirs = $this->getFolders($path, $subdir);
			$view->files = $this->getFiles($path, $subdir, 0);
		}
		else 
		{
			$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
			return;
		}
		
		// Does the publication exist?
		$versionid = JRequest::getInt('versionid', 0);
		$pContent = new PublicationAttachment( $this->_database );
		$role = $primary ? '1': '0';
		$other = $primary ? '0' : '1';

		if (!$images) 
		{
			$view->attachments = $pContent->getAttachments($versionid, $filters = array('role' => $role));
		}
		else 
		{
			$view->image_ext = array('bmp', 'jpg', 'jpeg', 'gif', 'png', 'jpe', 'tif', 'tiff');
			$view->video_ext = array('avi', 'mpeg', 'mov', 'mpg', 'wmv', 'rm', 'mp4');
			$other = 1;
				
			// Get current screenshots
			$pScreenshot = new PublicationScreenshot( $this->_database );
			$view->shots = $pScreenshot->getScreenshots($versionid);
		}
		
		$view->exclude = $pContent->getAttachments($versionid, $filters = array('role' => $other, 'select' => 'a .path'));

		if ($view->exclude) 
		{
			$excude_files = array();
			foreach($view->exclude as $exclude) 
			{
				$excude_files[] = str_replace($path. DS, '', trim($exclude->path));
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
		$view->subdir 		= $subdir;
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
	 * Upload file(s) and check into Git
	 * 
	 * @return     void, redirect
	 */
	public function save() 
	{
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
		
		// Are we in a subdirectory?
		$subdir = urldecode(JRequest::getVar('subdir', ''));
		$subdir = $this->formatPath($subdir);
		
		$prefix = $this->_task == 'saveprov' ? '' : $this->prefix;
		
		// Archive formats
		$archive_formats = array('zip', 'tar', 'gz', 'sit', 'rar', 'zipx');
		
		// Provisioned project scenario
		if ($this->_task == 'saveprov')
		{
			// Get members config
			$mconfig =& JComponentHelper::getParams('com_members');

			// Build upload path
			$dir  = Hubzero_View_Helper_Html::niceidformat( $this->_uid );
			$path = JPATH_ROOT;
			if (substr($mconfig->get('webpath', '/site/members'), 0, 1) != DS) 
			{
				$path .= DS;
			}
			$path .= $mconfig->get('webpath', '/site/members') . DS . $dir . DS . 'files';

			if (!is_dir( $path )) 
			{
				if (!JFolder::create($path, 0777)) 
				{
					$this->setError(JText::_('UNABLE_TO_CREATE_UPLOAD_PATH'));
				}
			}
			
			$quota = ProjectsHtml::convertSize(floatval($this->_config->get('defaultQuota', '1')), 'GB', 'b');			
		}
		else 
		{
			// Get path and initialize Git
			$path = $this->getProjectPath();
			$this->iniGit($path);

			// Get quota
			$params = new JParameter($this->_project->params);
			$quota = $params->get('quota');
			$quota = $quota 
					? $quota 
					: ProjectsHtml::convertSize(floatval($this->_config->get('defaultQuota', '1')), 'GB', 'b');			
		}
			 		
		// Process each file
		if (!$this->getError()) 
		{
			if ($this->_task != 'saveprov')
			{
				// Get author profile
				$author  = $this->getGitAuthor();				
			}
						
			for($i=0; $i < count($files['name']); $i++) 
			{
				$file = $files['name'][$i];
				$tmp_name = $files['tmp_name'][$i];
					
				// Make the filename safe			
				if ($file) 
				{	
					$file = JFile::makeSafe($file);
					$file = str_replace(' ' ,'_', $file);
				}
				
				// Get file extention
				$ext = explode('.', $file);
				$ext = count($ext) > 1 ? end($ext) : '';
				
				// Subdir?
				$file = $subdir ? $subdir . DS . $file : $file;
				
				// Check file size
				$sizelimit = ProjectsHtml::formatSize($this->_config->get('maxUpload', '104857600'));
				if ($files['size'][$i] == 0 
					|| ($files['size'][$i] > intval($this->_config->get('maxUpload', '104857600')))
				) 
				{
					$this->setError( JText::_('COM_PROJECTS_FILES_ERROR_EXCEEDS_LIMIT') . ' '
						. $sizelimit . '. ' . JText::_('COM_PROJECTS_FILES_ERROR_TOO_LARGE_USE_OTHER_METHOD') );
				}
				
				// Check against quota
				if ($files['size'][$i] > 0) 
				{
					// Get used space 
					chdir($prefix . $path);
					exec('du -sk .[!.]*', $out);
					
					$kb = str_replace('.git', '', trim($out[0]));
					$dirsize = $kb * 1024;
					$unused = $quota - $dirsize;
					
					if ($files['size'][$i] > $unused) {
						$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_OVER_QUOTA'));
					}
				}
							
				// Expand archive?
				$expand  = JRequest::getInt('expand_zip', 0);
				$zipfile = in_array(strtolower($ext), $archive_formats) ? 1 : 0;
				if (!$this->getError() && $zipfile && $expand) 
				{
					require_once(JPATH_ROOT. DS . 'administrator' . DS . 'includes' . DS . 'pcl' . DS . 'pclzip.lib.php');
					if (!extension_loaded('zlib')) 
					{
						$this->setError(JText::_('COM_PROJECT_ZLIB_PACKAGE_REQUIRED'));
					}
					else 
					{
						$archive = $prefix . $path . DS . $file;
						$temp_archive = is_file($archive) ? 0 : 1;
						$zip = new PclZip($archive);
						$unzipto = $subdir ? $prefix . $path . DS . $subdir : $prefix . $path;
						
						// Upload archive
						if (!JFile::upload($tmp_name, $archive)) 
						{
							$this->setError(JText::_('COM_PROJECTS_ERROR_UPLOADING'));
						}
						elseif (($list = $zip->listContent()) == 0) 
						{
							$this->setError('Error: ' . $zip->errorInfo(true));
						}
						
						// Expand
						$do = $zip->extract(PCLZIP_OPT_PATH, $unzipto, PCLZIP_CB_PRE_EXTRACT, 'zipPreExtractCallBack');

						if (!$do) 
						{
							$this->setError(JText::_('COM_PROJECT_FILES_ERROR_UNZIP_FAILED'));
						}
										
						// Remove archive 
						if ($temp_archive) {
							JFile::delete($archive);
						}
						
						// add each uploaded file to Git
						if (!$this->getError() && $this->_task != 'saveprov') 
						{
							for ($a = 0; $a < sizeof($list); $a++) 
							{
								$afile = $list[$a]['filename'];

								// Make name safe
								$filename 	= basename($afile);
								$dirname 	= dirname($afile);
								$safename 	= JFile::makeSafe($filename);
								$safename 	= str_replace(' ', '_', $safename);
								$safename 	= $dirname ? $dirname . DS . $safename : $safename;
								
								$afile = $subdir ? $subdir . DS . $afile : $afile;
								$safename = $subdir ? $subdir . DS . $safename : $safename;
																			
								// Make sure expanded files are safe
								$afile = ($afile == $safename) ? $afile : $safename;
								
								if (is_file($prefix . $path . DS . $afile)) 
								{
									// cd
									chdir($prefix . $path);

									// Git add
									exec($this->gitpath . ' add ' . escapeshellarg($afile) . ' 2>&1', $out);

									// Git commit
									exec($this->gitpath . ' commit -m "Added file '
										. escapeshellarg($afile) . '" --author="' . $author . '" 2>&1', $out);								
									$uploaded[] = $afile;
								}																						
							}							
						}
					}				
				}
				
				// Upload file
				if (!$this->getError() && (!$zipfile || !$expand)) 
				{				
					// cd
					chdir($prefix . $path);

					if (file_exists($prefix . $path . DS . $file)) 
					{
						$updated[] = $file;
					}

					if (!JFile::upload($tmp_name, $prefix . $path . DS . $file)) 
					{
						$this->setError(JText::_('COM_PROJECTS_ERROR_UPLOADING'));
					}
					else
					{
						$uploaded[] = $file;
						if ($this->_task != 'saveprov') 
						{
							// Git add
							exec($this->gitpath . ' add ' . escapeshellarg($file) . ' 2>&1', $out);

							// Git commit
							$commit_action = isset($updated[$file])
								? JText::_('COM_PROJECTS_UPDATED') 
								: JText::_('COM_PROJECTS_UPLOADED');

							exec($this->gitpath . ' commit -m "' . $commit_action
								. ' file ' . escapeshellarg($file) . '" --author="' . $author . '" 2>&1', $out);	
						}
					}
				}
			}
		}
		
		// Success message
		if(count($uploaded) > 0)
		{
			// Output status message
			$this->_msg = JText::_('COM_PROJECTS_FILE_UPLOADED') . ' ' . count($uploaded) 
				. ' ' . JText::_('COM_PROJECTS_FILES_S');	
			
			// Record activity
			if ($this->_task != 'saveprov') 
			{
				$objAA = new ProjectActivity( $this->_database );
				$activity_action = count($updated) == count($uploaded)
					? strtolower(JText::_('COM_PROJECTS_UPDATED')) 
					: strtolower(JText::_('COM_PROJECTS_UPLOADED'));
				
				$ref = count($uploaded) == 1 ? $uploaded[0] : 0;
				
				if (count($uploaded) == 1) 
				{
					$activity_action .= ' ' . JText::_('COM_PROJECTS_FILE') . ' "' . basename($uploaded[0]) . '" ';
				}
				else 
				{
					$activity_action .= ' ' . count($uploaded) . ' ' . JText::_('COM_PROJECTS_FILES_S');
				}
				
				$activity_action .= count($updated) == count($uploaded)
					? ' '.strtolower(JText::_('COM_PROJECTS_IN_PROJECT_FILES')) 
					: ' '.strtolower(JText::_('COM_PROJECTS_INTO_PROJECT_FILES'));	
								
				$aid = $objAA->recordActivity( $this->_project->id, 
					$this->_uid, $activity_action, 
					$ref, 'files', JRoute::_('index.php?option=' . $this->_option . a . 
					'alias=' . $this->_project->alias . a . 'active=files'), 'files', 1 );
			}
		}	
				
		$view = JRequest::getVar('view', 'view');
		$return_status  = JRequest::getVar('return_status', 0);
		if ($return_status) 
		{
			// AJAX return
			if ($this->getError()) 
			{
				return 'na';
			}
			elseif (!$updated) 
			{
				$ext = explode('.', $file);
				$ext = end($ext);
				$icon = ProjectsHtml::getFileIcon($ext);
				return $file. '|' . $icon;
			}
			else 
			{
				return 'updated';
			}
		}	

		// Display view
		if ($view == 'browser') 
		{
			return $this->browser();
		}
		elseif ($this->_task != 'saveprov')  
		{			
			// Pass success or error message
			if ($this->getError()) {
				$this->_message = array('message' => $this->getError(), 'type' => 'error');
			}
			elseif (isset($this->_msg) && $this->_msg) {
				$this->_message = array('message' => $this->_msg, 'type' => 'success');
			}
						
			$pid 	= JRequest::getInt('pid', 0);
						
			// Build pub url
			$route = $this->_project->provisioned 
				? 'index.php?option=com_publications' . a . 'task=submit' . a . $pid
				: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias;
								
			$url = JRoute::_($route . a . 'active=files');
			
			// Redirect to file list
			$url .= $subdir ? '/?subdir=' .urlencode($subdir) : '';
			$url .= ($this->_case == 'apps' && $this->_app->name) ? a . 'case=apps' . a . 'app=' . $this->_app->name : '';
			
			if ($view == 'pub')
			{
				$url = JRequest::getVar('HTTP_REFERER', NULL, 'server');
			}				
			
			$this->_referer = $url;
			return;
		}
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
		
		// Are we in a subdirectory?
		$subdir = urldecode(JRequest::getVar('subdir', ''));
		$subdir = $this->formatPath($subdir);
		
		// Get path and initialize Git
		$path = $this->getProjectPath();
		$this->iniGit($path);
		
		$newdir = JFolder::makeSafe($newdir);
		$createdir = $subdir ? $subdir . DS . $newdir : $newdir;
		
		// Check that we have directory to create
		if (!$newdir)
		{
			$this->setError(JText::_('COM_PROJECTS_ERROR_NO_DIR_TO_CREATE'));	
		}	
		elseif (!is_dir($this->prefix . $path . DS . $createdir))
		{			
			if (!JFolder::create( $this->prefix . $path . DS . $createdir )) 
			{
				$this->setError( JText::_('COM_PROJECTS_FILES_ERROR_DIR_CREATE') );
			}
			else 
			{
				// cd
				chdir($this->prefix . $path);

				// Create an empty file
				exec('touch ' . escapeshellarg($createdir) . '/.gitignore ' . ' 2>&1', $out);

				// Git add
				exec($this->gitpath . ' add ' . escapeshellarg($createdir) . ' 2>&1', $out);
				
				// Get author profile
				$author  = $this->getGitAuthor();
				
				// Git commit
				exec($this->gitpath . ' commit -m "' . JText::_('COM_PROJECTS_CREATED_DIRECTORY')
					. '  ' . escapeshellarg($newdir) . '" --author="' . $author . '" 2>&1', $out);


				$this->_msg = JText::_('COM_PROJECTS_CREATED_DIRECTORY') . ': ' . $newdir;
			}
		}
		else 
		{
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
		$url  = JRoute::_('index.php?option=' . $this->_option
			 . a . 'alias=' . $this->_project->alias . a . 'active=files');
		$url .= $subdir ? '/?subdir=' . urlencode($subdir) : '';
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
		$dir = urldecode(JRequest::getVar('dir', ''));
		$dir = $this->formatPath($dir);
		
		// Get path and initialize Git
		$path = $this->getProjectPath();
		$this->iniGit($path);
		
		// Are we in a subdirectory?
		$subdir = urldecode(JRequest::getVar('subdir', ''));
		$subdir = $this->formatPath($subdir);
		
		// cd
		chdir($this->prefix . $path);

		// Check that we have directory to delete
		if (!$dir || !is_dir($this->prefix . $path . DS . $dir) || $dir == '.git' || $dir == '.')
		{
			$this->setError(JText::_('COM_PROJECTS_ERROR_NO_DIR_TO_DELETE'));	
		}
		else
		{
			exec($this->gitpath . ' rm -r ' . escapeshellarg($dir) . ' 2>&1', $out);
			
			// Get author profile
			$author  = $this->getGitAuthor();
			
			// Git commit
			exec($this->gitpath . ' commit -m "' . JText::_('COM_PROJECTS_CREATED_DIRECTORY')
				. '  ' . escapeshellarg($dir) . '" --author="' . $author . '" 2>&1', $out);
						
			// If directory is still there (not in Git)			
			if (is_dir($this->prefix . $path . DS . $dir))
			{
				JFolder::delete($this->prefix . $path . DS . $dir);
			}
			
			if (!is_dir($this->prefix . $path . DS . $dir))
			{
				$this->_msg = JText::_('COM_PROJECTS_DELETED_DIRECTORY');
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
		$url  = JRoute::_('index.php?option=' . $this->_option
			 . a . 'alias=' . $this->_project->alias . a . 'active=files');
		$url .= $subdir ? '/?subdir=' . urlencode($subdir) : '';
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
		// Incoming
		$checked = JRequest::getVar( 'asset', '', 'request', 'array' );
		$folders = JRequest::getVar( 'folder', '', 'request', 'array' );
			
		if ((empty($checked) or $checked[0] == '') && (empty($folders) or $folders[0] == '')) 
		{		
			$this->setError(JText::_('COM_PROJECTS_ERROR_NO_FILES_TO_DELETE'));
		}
				
		// Get path and initialize Git
		$path = $this->getProjectPath();
		$this->iniGit($path);
						
		// cd
		chdir($this->prefix . $path);
		
		// Are we in a subdirectory?
		$subdir = urldecode(JRequest::getVar('subdir', ''));
		$subdir = $this->formatPath($subdir);
		
		if ($this->_task == 'delete') 
		{
			// Output HTML
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'projects',
					'element'=>'files',
					'name'=>'delete'
				)
			);
			
			$view->folders 		= $folders;
			$view->checked 		= $checked;
			$view->option 		= $this->_option;
			$view->project 		= $this->_project;
			$view->authorized 	= $this->_authorized;
			$view->uid 			= $this->_uid;		
			$view->subdir 		= $subdir;
			$view->case 		= $this->_case;
			$view->app			= $this->_app;
			$view->path 		= $this->prefix . $path;
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
			$fdel = 0;
			$del  = 0;
			
			// Get author profile
			$author  = $this->getGitAuthor();

			// Delete checked folders
			foreach($folders as $folder) 
			{
				$folder = urldecode($folder);
				$folder = $subdir ? $subdir . DS . $folder : $folder;
				if ($folder != '' && is_dir($this->prefix . $path . DS . $folder)) 
				{
					exec($this->gitpath . ' rm -r ' . escapeshellarg($folder) . ' 2>&1', $out);
					$fdel ++;
				}
			}		
			
			// Delete checked files
			foreach($checked as $file) 
			{
				$file = urldecode($file);
				$file = $subdir ? $subdir . DS . $file : $file;
				if ($file != '' && file_exists($this->prefix . $path . DS . $file)) 
				{
					exec($this->gitpath . ' rm ' . escapeshellarg($file) . ' 2>&1', $out);
					$del ++;
				}
			}
			
			// Commit changes
			exec($this->gitpath . ' commit -a -m "Deleted ' . $del
				. ' ' . JText::_('COM_PROJECTS_FILES_S')
				. ' ' . JText::_('COM_PROJECTS_AND') . $fdel
				. ' ' . JText::_('COM_PROJECTS_FOLDERS')  
				. '" --author="' . $author . '" 2>&1', $out);
						
			// Output message
			$msg  = JText::_('COM_PROJECTS_DELETED');
			$msg .= $del > 0 ? ' ' . $del . ' ' . JText::_('COM_PROJECTS_FILES_S') : '';
			$msg .= $del > 0 && $fdel > 0 ? ' ' . JText::_('COM_PROJECTS_AND') : '';
			$msg .= $fdel > 0 ? ' ' . $fdel . ' ' . JText::_('COM_PROJECTS_FOLDERS') : '';
			$this->_msg = $msg;
			
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
			$url  = JRoute::_('index.php?option=' . $this->_option
				 . a . 'alias=' . $this->_project->alias . a . 'active=files');
			$url .= $subdir ? '/?subdir=' . urlencode($subdir) : '';
			$this->_referer = $url;
			return;
		}							
	}
	
	/**
	 * Show file history
	 * 
	 * @return     void, redirect
	 */
	protected function history()
	{		
		// Incoming
		$checked = JRequest::getVar( 'asset', '', 'request', 'array' );
		
		if ((empty($checked) or $checked[0] == '')) 
		{		
			$this->setError(JText::_('COM_PROJECTS_ERROR_NO_FILES_TO_SHOW_HISTORY'));
		}
		$file = urldecode($checked[0]); // can only show history for one file
				
		// Get path and initialize Git
		$path = $this->getProjectPath();
		$this->iniGit($path);
		
		// Are we in a subdirectory?
		$subdir = urldecode(JRequest::getVar('subdir', ''));
		$subdir = $this->formatPath($subdir);
		
		chdir($this->prefix . $path);
		$fpath = $subdir ? $subdir . DS . $file : $file;
        exec($this->gitpath . ' log --diff-filter=AM --pretty=format:%H ' 
			. escapeshellarg($fpath) . ' 2>&1', $out);
		$versions = array();
		$hashes = array();

		if (count($out) > 0 && $out[0] != '') 
		{
			foreach ($out as $line) 
			{
				if (preg_match("/[a-zA-Z0-9]/", $line) && strlen($line) == 40) 
				{
					$hashes[]  = $line;
				}
			}
		}
		
		if (!empty($hashes)) 
		{
			$i = 0;
			foreach ($hashes as $hash) 
			{
				$out1 = array();
				$out2 = array();
				$versions[$i]['date']   = $this->whatChanged($path, $hash, 'date');
				$versions[$i]['author'] = $this->whatChanged($path, $hash, 'author');
				$versions[$i]['hash']   = $hash;
				
				// Was file restored?
				exec($this->gitpath . ' diff --name-status '
				. $hash . '^ ' . $hash . ' -- ' . escapeshellarg($fpath) . ' 2>&1', $out1);
				$status = '';
				
				switch (substr($out1[0], 0, 1)) 
				{
					case 'A': $status = ($i + 1) == count($hashes) 
							  ? JText::_('COM_PROJECTS_FILE_STATUS_ADDED') 
							  : JText::_('COM_PROJECTS_FILE_STATUS_RESTORED');
					break;
					case 'M': $status = JText::_('COM_PROJECTS_FILE_STATUS_MODIFIED');
					break;
				}
				$versions[$i]['status'] = $status;
				
				// Exctract file content
				exec($this->gitpath . ' show '
					. $hash . ':' . escapeshellarg($fpath) . ' 2>&1', $out2);
				$content = $this->filterASCII($out2);
				$content = $content ? Hubzero_View_Helper_Html::shortenText($content, 200, 0) : '';
				$versions[$i]['content'] = $content;
				$i++;
			}
		} else 
		{
			$this->setError(JText::_('COM_PROJECTS_ERROR_FILES_SHOW_HISTORY'));
		}
				
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'history'
			)
		);

		$view->versions 	= $versions;
		$view->path 		= $this->prefix. $path;
		$view->file 		= $file; 
		$view->option 		= $this->_option;
		$view->project 		= $this->_project;
		$view->case 		= $this->_case;
		$view->app			= $this->_app;
		$view->uid 			= $this->_uid;		
		$view->subdir 		= $subdir;
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
		
		// Get path and initialize Git
		$path = $this->getProjectPath();
		$this->iniGit($path);
		
		// Are we in a subdirectory?
		$subdir = urldecode(JRequest::getVar('subdir', ''));
		$subdir = $this->formatPath($subdir);
		
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
			$newname = JFolder::makeSafe($newname);
		}
		else
		{
			$newname = JFile::makeSafe($newname);
		}
		
		// Compare new and old name
		if ($newname == $oldname)
		{
			$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_SAME_NAMES'));
		}
		
		// Set paths
		$newpath = $subdir ? $subdir . DS . $newname : $newname;
		$oldpath = $subdir ? $subdir . DS . $oldname : $oldname;
		
		// More checks
		if ( !$this->getError())
		{
			if ($rename == 'dir')
			{
				if (is_dir($this->prefix . $path . DS . $newpath))
				{
					$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_ALREADY_EXISTS_DIR'));
				}
				if (!is_dir($this->prefix . $path . DS . $oldpath))
				{
					$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_NO_OLD_NAME'));
				}
			}
			else
			{
				if (is_file($this->prefix . $path . DS . $newpath))
				{
					$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_ALREADY_EXISTS_FILE'));
				}
				if (!is_file($this->prefix . $path . DS . $oldpath))
				{
					$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_RENAME_NO_OLD_NAME'));
				}
			}	
		}
		
		// Proceed with renaming
		if (!$this->getError() && $this->_task == 'renameit') 
		{	
			// cd
			chdir($this->prefix . $path);
			
			// Get author profile
			$author  = $this->getGitAuthor();
			
			// Move
			exec($this->gitpath . ' mv ' . escapeshellarg($oldpath)
				. ' ' . escapeshellarg($newpath) . ' -f 2>&1', $out);
				
			// Commit changes
			exec($this->gitpath. ' commit -a -m "Renamed ' . escapeshellarg($oldpath) . ' to ' 
				. escapeshellarg($newpath) . '" --author="' . $author. '" 2>&1', $out);
			
			// Output message
			$this->_msg = JText::_('COM_PROJECTS_FILES_RENAMED_SUCCESS');							
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
		$url .= $subdir ? '/?subdir=' . urlencode($subdir) : '';
		$this->_referer = $url;
		return;
	}
	
	/**
	 * Move file(s)
	 * 
	 * @return     void, redirect
	 */
	protected function move()
	{		
		// Incoming
		$checked = JRequest::getVar( 'asset', '', 'request', 'array' );
		$folders = JRequest::getVar( 'folder', '', 'request', 'array' );
			
		if ((empty($checked) or $checked[0] == '') && (empty($folders) or $folders[0] == '')) 
		{		
			$this->setError(JText::_('COM_PROJECTS_ERROR_NO_FILES_TO_MOVE'));
		}
				
		// Get path and initialize Git
		$path = $this->getProjectPath();
		$this->iniGit($path);
		
		// Are we in a subdirectory?
		$subdir = urldecode(JRequest::getVar('subdir', ''));
		$subdir = $this->formatPath($subdir);
				
		if ($this->_task == 'move') 
		{
			// Output HTML
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'projects',
					'element'=>'files',
					'name'=>'move'
				)
			);
			
			$view->dirs 		= $this->getFolders($path, '', 1, true);
			$view->path 		= $this->prefix. $path;
			$view->folders 		= $folders;
			$view->checked 		= $checked;
			$view->option 		= $this->_option;
			$view->project 		= $this->_project;
			$view->authorized 	= $this->_authorized;
			$view->uid 			= $this->_uid;		
			$view->case 		= $this->_case;
			$view->app			= $this->_app;
			$view->subdir 		= $subdir;
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
			$mv = 0;
			
			// cd
			chdir($this->prefix . $path);
			
			// Get new path
			$newpath = urldecode(JRequest::getVar('newpath', ''));
			$newpath = $this->formatPath($newpath);
			
			// New directory to be created?
			$newdir = JRequest::getVar('newdir', '');
			
			// Clean up directory name
			if ($newdir) 
			{
				$newdir = stripslashes($newdir);
				$newdir = JFolder::makeSafe($newdir);
				$newdir = $subdir ? $subdir . DS . $newdir : $newdir;
			}
			if ($newdir && !is_dir( $this->prefix . $path . DS . $newdir )) 
			{
				// Create new directory
				if (!JFolder::create( $this->prefix . $path . DS . $newdir, 0777 )) 
				{
					$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_CREATE_UPLOAD_PATH') );
				}
			}
			
			// Get author profile
			$author  = $this->getGitAuthor();
				
			if (($newpath != $subdir or $newdir) && !$this->getError()) 
			{
				// Move checked files
				foreach($checked as $file) 
				{
					$file = urldecode($file);
					if ($newdir) 
					{
						$where = $newdir . DS . $file;
					}
					else 
					{
						$where = $newpath ? $newpath . DS . $file : $file;				
					}
					
					$file = $subdir ? $subdir . DS . $file : $file;
					if ($file != $where && $file != '' && file_exists($this->prefix . $path . DS . $file)) 
					{				
						exec($this->gitpath . ' mv ' . escapeshellarg($file)
							. ' ' . escapeshellarg($where) . ' -f 2>&1', $out);
						$mv ++;
					}
				}
				
				// Move checked folders
				foreach($folders as $folder) 
				{
					$folder = urldecode($folder);
					
					if ($newdir) 
					{
						$where = $newdir . DS . $folder;
					}
					else 
					{
						$where = $newpath ? $newpath . DS . $folder : $folder;				
					}
					
					$folder = $subdir ? $subdir . DS . $folder : $folder;
					
					if ($folder != $where && $folder != '' && file_exists($this->prefix . $path . DS . $folder)) 
					{				
						exec($this->gitpath . ' mv ' . escapeshellarg($folder)
							. ' ' . escapeshellarg($where) . ' -f 2>&1', $out);
						$mv ++;
					}
				}
				
				// Delete original directory if empty
				if ($subdir && is_dir($this->prefix. $path . DS . $subdir)) 
				{
					$contents = scandir($this->prefix. $path. DS . $subdir);
					if (count($contents) <= 2) 
					{
						JFolder::delete($this->prefix. $path. DS . $subdir);
					}
				}				
			} 
			elseif (!$this->getError()) 
			{
				$this->setError(JText::_('COM_PROJECTS_ERROR_NO_NEW_FILE_LOCATION'));
			}
			
			if ($mv) 
			{
				// Commit changes
				exec($this->gitpath. ' commit -a -m "Moved ' . $mv . ' ' . JText::_('COM_PROJECTS_FILES_S'). '" --author="' . $author. '" 2>&1', $out);
				
				// Output message
				$this->_msg = JText::_('COM_PROJECTS_MOVED'). ' ' . $mv . ' ' . JText::_('COM_PROJECTS_FILES_S');
			}			
			elseif ($mv == 0) 
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
			$url  = JRoute::_('index.php?option=' . $this->_option . a 
				. 'alias=' . $this->_project->alias . a . 'active=files');
			$url .= $subdir ? '/?subdir=' . urlencode($subdir) : '';
			$this->_referer = $url;
			return;
		}							
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
		$checked 	= JRequest::getVar( 'asset', '', 'request', 'array' );
		$folders 	= JRequest::getVar( 'folder', '', 'request', 'array' );
		$file 	 	= urldecode(JRequest::getVar('file', '')); 
		$multifile	= 0; 
				
		// Get path and initialize Git
		$path = $this->getProjectPath();
		$this->iniGit($path);
		
		// cd
		chdir($this->prefix. $path);

		// Are we in a subdirectory?
		$subdir = urldecode(JRequest::getVar('subdir', ''));
		$subdir = $this->formatPath($subdir);

		if (!$file) 
		{
			if ((empty($checked) or $checked[0] == '') && (empty($folders) or $folders[0] == ''))
			{
				$this->setError(JText::_('COM_PROJECTS_FILES_ERROR_NO_SELECTIONS_TO_DOWNLOAD'));
			}
			elseif (count($checked) == 1 && (empty($folders) or $folders[0] == ''))
			{
				$file = urldecode($checked[0]);
			}
			elseif ($render == 'download')
			{
				// Multi-file download
				$multifile = 1;
				$archive = $this->_archiveFiles($checked, $folders, $this->prefix . $path, $subdir);
				
				if(!$archive)
				{
					$this->setError($this->getError() . ' ' .JText::_('COM_PROJECTS_FILES_ARCHIVE_ERROR'));
				}
			}
		}
									
		// Are we previewing or downloading?
		if ($render == 'preview') 
		{						
			if (!$file) 
			{
				$this->setError(JText::_('COM_PROJECTS_ERROR_FILE_INFO_NOT_FOUND'));

				// Output error
				$view = new Hubzero_Plugin_View(
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
						
			$fpath = $subdir ? $subdir. DS . $file : $file;
			
			// Get git object
			$hash = $this->whatChanged($path, $fpath, 'hash');
			
			// Get file extention
			$ext = explode('.', $fpath);
			$ext = count($ext) > 1 ? end($ext) : '';
			
			// Image formats
			$image_formats = array('png', 'gif', 'jpg', 'jpeg', 'tiff', 'bmp');
			$image = in_array(strtolower($ext), $image_formats) ? 1 : 0;
			
			// Make a temp image copy
			if ($image) 
			{
				$ih = new ProjectsImgHandler();
				$hashed = $ih->createThumbName($file, '-' . substr($hash, 0, 10));
							
				$from_path = $this->prefix . $path . DS;
				$from_path = $subdir ? $from_path . $subdir . DS : $from_path;

				$imagepath = $this->_config->get('imagepath', '/site/projects');
				if (substr($imagepath, 0, 1) != DS) 
				{
					$imagepath = DS . $imagepath;
				}
				if (substr($imagepath, -1, 1) == DS) 
				{
					$imagepath = substr($imagepath, 0, (strlen($imagepath) - 1));
				}
				$to_dir = $this->_use_alias 
					? strtolower($this->_project->alias) 
					: Hubzero_View_Helper_Html::niceidformat( $this->_project->id );
				$to_path = $imagepath . DS . $to_dir . DS . 'preview';
				
				if (is_file(JPATH_ROOT. $to_path . DS . $hashed)) 
				{
					$image = $to_path . DS . $hashed;
				}
				else 
				{
					// Make sure the path exist
					if (!is_dir( $to_path )) 
					{
						jimport('joomla.filesystem.folder');
						JFolder::create( JPATH_ROOT. $to_path, 0777 );
					}
					JFile::copy($from_path. $file, JPATH_ROOT . $to_path . DS . $hashed);
					
					// Resize the image if necessary
					$ih->set('image',$hashed);
					$ih->set('overwrite',true);
					$ih->set('path',JPATH_ROOT. $to_path . DS);
					$ih->set('maxWidth', 180);
					$ih->set('maxHeight', 180);
					if (!$ih->process()) {
						$this->setError( $ih->getError() );
					}
					else {
						$image = $to_path . DS . $hashed;
					}
				}				
			}
			
			// Non ASCII formats
			$non_ASCII_formats = array( 'doc', 'docx', 'pdf', 'eps', 'ai');
			$non_ASCII = in_array(strtolower($ext), $non_ASCII_formats) ? 1 : 0;
			
			// Get non-binary object content
			$content = '';
			exec($this->gitpath . ' show  HEAD:' . escapeshellarg($fpath) . ' 2>&1', $out);

			// Reformat text content
			if (!$non_ASCII && !$image && count($out) > 0) 
			{
				$content = $this->filterASCII($out);
				$content = $content ? Hubzero_View_Helper_Html::shortenText($content, 200, 0) : '';
			}
			
			// Output HTML
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'projects',
					'element'=>'files',
					'name'=>'preview'
				)
			);
						
			$view->non_ASCII 	= $this->prefix . DS . $non_ASCII;
			$view->image 		= $image;
			$view->ext 			= $ext;
			$view->title 		= $file;
			$view->content 		= $content;
			$view->option 		= $this->_option;
			$view->filesize		= ProjectsHtml::getFileAttribs( $fpath, $path, 'size', $this->prefix );
			if ($this->getError()) 
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}
		elseif (!$this->getError())
		{ 
			// Which revision are we downloading?
			$hash = JRequest::getVar('hash', '');

			if ($multifile)
			{
				$fullpath 	= JPATH_ROOT . $archive['path'];
				$file  		= $archive['name'];
				$serveas	= 'Project Files ' . date('Y-m-d H:i:s');
			}			
			else
			{
				$fpath = $subdir ? $subdir. DS . $file : $file;
				
				// Get latest commit hash
				$last = $this->whatChanged($path, $fpath, 'hash');

				if ($hash) 
				{
					exec($this->gitpath . ' checkout ' . $hash . ' ' . escapeshellarg($fpath) . ' 2>&1', $out);
				}

				// Download file
				$fullpath = $this->prefix. $path . DS . $fpath;
				$serveas = $file;
			}
			
			// Ensure the file exist
			if (!file_exists($fullpath)) 
			{
				JError::raiseError( 404, JText::_('COM_PROJECTS_FILE_NOT_FOUND') . ' ' . $file );
				return ;
			}			
			
			// Get some needed libraries
			ximport('Hubzero_Content_Server');

			// Initiate a new content server and serve up the file
			$xserver = new Hubzero_Content_Server();
			$xserver->filename($fullpath);
			$xserver->disposition('attachment');
			$xserver->acceptranges(false);
			$xserver->saveas($file);
			$result = $xserver->serve_attachment($fullpath, $serveas, false);
			
			if($multifile)
			{
				// Delete downloaded zip			
				JFile::delete($fullpath);
			}

			if (!$result) 
			{
				// Should only get here on error
				JError::raiseError( 404, JText::_('COM_PROJECTS_SERVER_ERROR') );
			} 
			else 
			{
				if ($hash) 
				{
					exec($this->gitpath . ' checkout ' . $last . ' ' . escapeshellarg($fpath) . ' 2>&1', $out);
				}
				
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
		$url  = JRoute::_('index.php?option=' . $this->_option 
			. a . 'alias=' . $this->_project->alias. a . 'active=files');
		$url .= $subdir ? '/?subdir=' .urlencode($subdir) : '';
		$this->_referer = $url;
		return;
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
	private function _archiveFiles( $files, $folders, $projectPath = '', $subdir = '' )
	{
		if (!$projectPath || !is_dir($projectPath))
		{
			return false;
		}
		
		$maxDownload 	= intval($this->_params->get('maxDownload', 104857600));		
		$base_path 		= $this->_params->get('tempPath', '/site/projects/temp');
		$tarname 		= 'project_files_' . ProjectsHtml::generateCode (6 , 6 , 0 , 1 , 1 ) . '.zip';
		$path 			= $subdir ? $projectPath. DS . $subdir : $projectPath;
		$combinedSize  	= 0;

		if ($base_path) {
			// Make sure the path doesn't end with a slash
			if (substr($base_path, -1) == DS) {
				$base_path = substr($base_path, 0, strlen($base_path) - 1);
			}
			// Ensure the path starts with a slash
			if (substr($base_path, 0, 1) != DS) {
				$base_path = DS.$base_path;
			}
		}		
		$tarpath = $base_path . DS . $tarname;
		
		// Check that we have our temp directiry
		if (!is_dir( JPATH_ROOT . $base_path )) 
		{
			if (!JFolder::create( JPATH_ROOT . $base_path, 0777 )) 
			{
				$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_CREATE_TEMP_PATH') );
				return false;
			}
		}
		
		// Zip files		
		if (count($files) > 0 || count($folders) > 0) 
		{
			require_once( JPATH_ROOT.DS.'administrator'.DS.'includes'.DS.'pcl'.DS.'pclzip.lib.php' );
			if (!extension_loaded('zlib')) 
			{
				$this->setError( JText::_('COM_PROJECTS_FILES_ERROR_MISSING_PHP_LIBRARY') );
				return false;
			}
			
			$archive = new PclZip(JPATH_ROOT . $tarpath);
			$rfiles  = array();
			$i = 0;
			
			// Go through file names and get full paths
			foreach ($files as $p) 
			{
				$file  = urldecode($p);
				$fpath = $path . DS . $p;
				
				if(!is_file($fpath))
				{
					continue;
				}
				
				$combinedSize = $combinedSize + filesize($fpath);
				$i++;
				
				$rfiles[] = $fpath;
			}
			
			// Go through file names and get full paths
			/*
			foreach ($folders as $fo) 
			{
				$folder  = urldecode($fo);
				$fpath 	 = $path . DS . $folder;
				
				if ($folder == '' || !is_dir($fpath))
				{
					continue;
				}
				
				// Get dir size
				chdir($fpath);
				exec('du -sk', $out);
				$dirsize = intval($out[1]) * 1024;
								
				$combinedSize = $combinedSize + $dirsize;
				$i++;

				$rfiles[] = $fpath;
			}
			*/
			
			// Check against maximum allowable size
			if ($combinedSize > $maxDownload)
			{
				$this->setError( JText::_('COM_PROJECTS_FILES_ERROR_OVER_DOWNLOAD_LIMIT') );
				return false;
			}
			
			if ($i == 0)
			{
				$this->setError( JText::_('COM_PROJECTS_SERVER_ERROR') );
				return false;
			}
			
			// Create archive
			$v_list = $archive->create($rfiles, PCLZIP_OPT_REMOVE_ALL_PATH);
			
			// Result
			if ($v_list == 0) 
			{
				$this->setError( $archive->errorInfo(true)  );
				return false;
			} 
			else 
			{
				$archive = array();
				$archive['path'] = $tarpath;
				$archive['name'] = $tarname;
				return $archive;
			} 		
		}
		return false;
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
	 * @param      string  	$app
	 *
	 * @return     string
	 */
	protected function diskspace( $option, $project, $case, $by, $action, $config, $app )
	{
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'diskspace'
			)
		);
		
		// Get path and initialize Git
		$path = $this->getProjectPath($project->alias, $case);
		$this->iniGit($path);
		
		// Get used space 
		if (is_dir($this->prefix . $path)) 
		{
			chdir($this->prefix. $path);
			exec('du -sk .git', $out);

			if (!empty($out)) 
			{
				$kb = str_replace('.git', '', trim($out[0]));
				$view->dirsize = $kb*1024;
			}
			else 
			{
				$view->dirsize = 0;
			}			
		}
		else 
		{
			$view->dirsize = 0;
		}
		
		// Get all publications in project	
		// TBD
		
		// Get used published space
		//TBD
		
		$view->total = $this->getFiles($path, '', 0, 1);		
		$view->params = new JParameter( $project->params );
		$quota = $view->params->get('quota');
		$view->quota = $quota 
			? $quota 
			: ProjectsHtml::convertSize( floatval($config->get('defaultQuota', '1')), 'GB', 'b');
		
		$view->case 	= $case;
		$view->app		= $app;
		$view->action 	= $action;
		$view->project 	= $project;
		$view->option 	= $option;
		$view->config 	= $config;
		$view->title	= isset($this->_area['title']) ? $this->_area['title'] : '';
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();		
	}

	//----------------------------------------
	// Git calls
	//----------------------------------------
	
	/**
	 * Init Git repository
	 * 
	 * @param      string	$path
	 *
	 * @return     string
	 */
	public function iniGit( $path = '') 
	{							
		// Build .git repo
		$gitRepoBase = $this->prefix . $path . DS . '.git';
			
		// Need to create .git repository if not yet there
		if (!is_dir($gitRepoBase)) 
		{			
			$webdir = $this->_config->get('webpath');
			if (substr($webdir, 0, 1) != DS) 
			{
				$webdir = DS . $webdir;
			}
			if (substr($webdir, -1, 1) == DS) 
			{
				$webdir = substr($webdir, 0, (strlen($webdir) - 1));
			}
			$clone = $this->_config->get('gitclone');
			$clone = $clone ? JPATH_ROOT . $clone : $this->prefix . $webdir . DS . 'clone' . DS . '.git';
			
			/*
			if (!is_dir($clone))
			{
				$this->setError( JText::_('COM_PROJECTS_CLONE_DIR_DOES_NOT_EXIST') );
				return false;	
			}

			if (!JFolder::copy($clone, $gitRepoBase)) 
			{
				$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_CREATE_GIT_REPO') );
				return false;
			}*/
			if (!is_dir($this->prefix . $path))
			{
				return false;
			}
			chdir($this->prefix . $path);
			exec($this->gitpath . ' init 2>&1', $out);
		}
							
		return true;			
	}
	
	/**
	 * Get info on file in working directory
	 * 
	 * @param      string	$path
	 * @param      string  	$file
	 * @param      string  	$return
	 *
	 * @return     string
	 */
	public function whatChanged ($path = '', $file = '', $return = 'date') 
	{
		chdir($this->prefix . $path);

		if ($return == 'date') 
		{
			exec($this->gitpath . ' whatchanged --pretty=format:%cd ' . escapeshellarg($file). ' 2>&1', $out);
			$arr = explode("\t", $out[0]);
			$timestamp = strtotime($arr[0]);
			return date ('m/d/Y g:i A', $timestamp);
		}
		elseif ($return == 'num') 
		{
			exec($this->gitpath . ' log --diff-filter=AMR --pretty=format:%H ' . escapeshellarg($file). ' 2>&1', $out);
			return count($out);
		}
		elseif ($return == 'author') 
		{
			exec($this->gitpath . ' whatchanged --pretty=format:%an ' . escapeshellarg($file). ' 2>&1', $out);
			$arr = explode("\t", $out[0]);
			return $arr[0];
		}		
		elseif ($return == 'hash') 
		{
			exec($this->gitpath . ' whatchanged --pretty=format:%H ' . escapeshellarg($file). ' 2>&1', $out);
			$arr = explode("\t", $out[0]);
			return $arr[0];
		}
	}
	
	/**
	 * Get date of last commit
	 * 
	 * @param      string	$path
	 * @param      array  	$out
	 *
	 * @return     string
	 */
	function getLastCommit( $path = '', $out = array() )    
	{
		chdir($this->prefix. $path);
        $date = exec($this->gitpath 
			. " rev-list  --header --max-count=1 HEAD | grep -a committer | cut -f5-6 -d' '", &$out);
        return date("D n/j/y G:i", (int)$date);
	}
	
	/**
	 * Git status
	 *
	 * @return     string
	 */
	public function status()  
	{
		// Get project path
		$path = $this->getProjectPath();
		
		// Get Git status
		chdir($this->prefix . $path);
        exec($this->gitpath . ' status 2>&1', $out);
		$status = '';
		
		if (count($out) > 0 && $out[0] != '') 
		{
			foreach ($out as $line) {
				$status.=  '<br />' . $line;
			}
		}
		
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'status'
			)
		);

		$view->status 	= $status;
		$view->option 	= $this->_option;
		$view->project 	= $this->_project;
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
	public function getFolders($path = '', $subdir = '', $recurse = false, $fullpath = false, $exclude = array('.git')) 
	{
		// Check path format
		$subdir = $this->formatPath($subdir);
		
		// Make full path
		$path = $path . DS . $subdir;
		
		// Use Joomla to get folder list
		$folders = JFolder::folders( $this->prefix . $path, '.', $recurse, $fullpath, $exclude);
				
		return $folders;		
	}
	
	/**
	 * Get file info
	 * 
	 * @param      string	$fpath
	 * @param      string  	$path
	 * @param      boolean  $fullpath
	 * @param      boolean 	$count
	 * @param      object  $pA
	 * @param      boolean  $norecurse
	 *
	 * @return     array
	 */
	public function getFileInfo($fpath = '', $path = '', $fullpath = '', $count = 0, $pA = '', $norecurse = 1 ) 
	{		
		$entry = array();
		$entry['name']	= basename($fpath);
		if (!$count) 
		{
			$entry['fpath']		= $fpath;
			$e 					= $norecurse ? $entry['name'] : $entry['fpath'];
			$entry['bites']		= filesize($this->prefix . $fullpath . DS . $e);
			$entry['size']		= ProjectsHtml::getFileAttribs( $e, $fullpath, 'size', $this->prefix );
			$entry['ext']		= ProjectsHtml::getFileAttribs( $entry['name'], $fullpath, 'ext' );
			$entry['date']  	= $this->whatChanged($path, $fpath, 'date');
			$entry['revisions'] = $this->whatChanged($path, $fpath, 'num');
			$entry['author'] 	= $this->whatChanged($path, $fpath, 'author');
			
			// Is file linked with a publication?
			if ($pA) 
			{
				$pub = $pA->getPubAssociation($this->_project->id, $fpath);
				$entry['pid'] = $pub['id'];
				$entry['pub_title'] = $pub['title'];
				$entry['pub_version'] = $pub['version'];
				$entry['pub_version_label'] = $pub['version_label'];
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
		$subdir = $this->formatPath($subdir);
		$fullpath = $subdir ? $path. DS . $subdir : $path;
		
		$files = array();
		
		$get = $this->_readDir($fullpath, $fullpath);
		
		if ($get) 
		{
			foreach($get as $file)
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
	 * @param      string   $sortby
	 * @param      string   $sortdir
	 *
	 * @return     array
	 */
	protected function _combineFilesAndDirs($files, $dirs, $sortby = '', $sortdir = 'ASC' )
	{
		$combined = array();
		$sorting  = array();
		$follow	  = array();
		$sortOrder = $sortdir == 'ASC' ? SORT_ASC : SORT_DESC;
		
		// Go through files
		if (count($files) > 0)
		{
			foreach ($files as $file)
			{
				$item 				= array();
				$item['type'] 		= 'document';
				$item['item'] 		= $file;
				$item['name'] 		= $file['name'];
				
				if ($sortby == 'sizes') 
				{
					$sorting[] = $file['bites'];
				}
				elseif ($sortby == 'modified') 
				{
					$sorting[] = $file['date'];
				}
				else
				{
					$sorting[] = strtolower($file['name']);
				}
				$combined[]	= $item;
			}
		}
		
		// Go through directories
		if (count($dirs) > 0 && !empty($dirs))
		{
			if ($sortby != 'filename')
			{
				array_multisort($sorting, $sortOrder, $combined );
			}
			foreach ($dirs as $dir)
			{
				$item 				= array();
				$item['type'] 		= 'folder';
				$item['item'] 		= $dir;
				$item['name'] 		= $dir;
				
				if ($sortby == 'filename')
				{
					$sorting[]  = strtolower($dir);					
				}
				$combined[] = $item;				
			}
		}
		
		// Sort by name
		if (!empty($combined) && $sortby == 'filename') 
		{
			array_multisort($sorting, $sortOrder, $combined );
		}
		
		return $combined;				
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
		$handle = opendir($path);

		while (($file = readdir($handle)) !== false)
		{
			if (($file != '.') && ($file != '..') && (!in_array($file, $exclude))) {
				$dir = $path . DS . $file;
				$isDir = is_dir($dir);
				if ($isDir) 
				{
					$arr2 = $this->_readDir($dir, $dirpath);
					$arr = array_merge($arr, $arr2);
				} else 
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
		
		return $arr;
	}

	/**
	 * Get folders
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
	public function getFiles($path = '', $subdir = '', $norecurse = true, 
		$get_count = false, $limit = 0, $rand = 0, 
		$sortby = '', $sortdir = 'ASC') 
	{					
		// Check path format
		$subdir = $this->formatPath($subdir);
		$fullpath = $subdir ? $path . DS . $subdir : $path;
		
		$files 		= array();
		$sorting 	= array();
		$i			= 0;
		
		if(!is_dir($this->prefix . $path))
		{
			return $get_count ? count($files) : $files;	
		}

		// cd
		chdir($this->prefix . $path);
		
		// Publication Attachment class
		if (!$get_count && $this->_publishing) 
		{
			$pA = new PublicationAttachment( $this->_database );
		}
		else 
		{
			$pA = NULL;
		}
			
		exec($this->gitpath . ' ls-files --exclude-standard ' . escapeshellarg($subdir) . ' 2>&1', $out);
		
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
				if ($norecurse) 
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
					$file = $this->getFileInfo($fpath, $path, $fullpath, $get_count, $pA, $norecurse);
					$files[] = $file;
					if ($file['name'] != '.gitignore')
					{
						$i++;
					}
				}						
			}
		}
	
		return $get_count ? $i : $files;			
	}
		
	//----------------------------------------
	// Misc
	//----------------------------------------
	
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
		$webdir = $this->_config->get('webpath');
		
		if (substr($webdir, 0, 1) != DS) 
		{
			$webdir = DS . $webdir;
		}
		if (substr($webdir, -1, 1) == DS) 
		{
			$webdir = substr($webdir, 0, (strlen($webdir) - 1));
		}
		
		// Do we need to create master directory off the web root?
		if (!$this->prefix && !is_dir($webdir))
		{
			$this->setError( JText::_('Master directory does not exist. Administrator must fix this! ')  . $webdir );
			return false;
		}
		
		// Do we have an app repo?		
		if (preg_match("/apps:/", $case))
		{			
			$reponame = isset($this->_app->name) && $this->_app->name ? $this->_app->name : preg_replace("/apps:/", $case);
			$path = $webdir. DS . $dir. DS . 'apps' . DS . strtolower($reponame);
		}
		else 
		{
			$path  = $webdir. DS . $dir. DS . $case;
		}
		
		if (!is_dir( $this->prefix. $path )) 
		{
			// Do not create if app repo
			if (preg_match("/apps:/", $case) && (!isset($this->_app->name) || !$this->_app->name))
			{
				$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_GET_APP_REPO_PATH') );
				return false;
			}
			
			// Create path
			if (!JFolder::create( $this->prefix. $path, 0777 )) 
			{
				$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_CREATE_UPLOAD_PATH') ) . $this->prefix. $path ;
			}
		}
		
		return $path;		
	}
	
	/**
	 * Filter ASCII
	 * 
	 * @param      array	$out
	 *
	 * @return     string
	 */
	public function filterASCII($out =  array()) 
	{		
		$text = '';
		foreach ($out as $line) 
		{
			$encoding = mb_detect_encoding($line);
			
			if ($encoding != "ASCII") 
			{
				break;
			}
			else 
			{
				$text.=  $line != '' ? $line . "\n" : '';
			}
		}		
		return $text;
	}
	
	/**
	 * Get author for Git commits
	 * 
	 * @return     string
	 */
	public function getGitAuthor()
	{
		if (!$this->_uid)
		{
			return false;
		}
		
		// Get author profile
		$profile =& Hubzero_Factory::getProfile();
		$profile->load( $this->_uid );
		
		$name    = $profile->get('name');
		$email   = $profile->get('email');
		$author  = escapeshellarg($name . ' <' . $email . '> ');
		
		return $author;
	}
	
	/**
	 * Format path
	 * 
	 * @param      string	$subdir
	 *
	 * @return     string
	 */
	public function formatPath($subdir = '') 
	{					
		if ($subdir) 
		{
			if (substr($subdir, 0, 1) == DS) 
			{
				$subdir = substr($subdir, 1, (strlen($subdir)));
			}
			if (substr($subdir, -1, 1) == DS) 
			{
				$subdir = substr($subdir, 0, (strlen($subdir) - 1));
			}
		}
		return $subdir;
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
		
		// Get project path
		$path = $this->getProjectPath($identifier, $case);
	
		// Get file count
		$count = $this->getFiles($path, '', 0, 1);
		
		return $count;
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
}

//--------------------------------------
// ZIP extraction
//--------------------------------------

/**
 * ZIP extraction - making file names safe
 * 
 * @param      string	$p_event
 * @param      array 	&$p_header
 *
 * @return     string
 */
function zipPreExtractCallBack($p_event, &$p_header) 
{
    $info = pathinfo($p_header['filename']);

	$file = $p_header['filename'];	
	$filename = basename($file);
	$dirname = dirname($file);	
	
	// Exclude hidden MAC OS files
	if (preg_match("/__MACOSX/", $file) OR preg_match("/.DS_Store/", $file)) 
	{
		return 0;
	}
	else 
	{
		// Make file name safe
		if (!$p_header['folder']) 
		{
			$file = JFile::makeSafe($filename);
			$file = str_replace(' ', '_', $file);
			$file = $dirname ? $dirname . DS . $file : $file;
		}

		$p_header['filename'] = $file;
	    return 1;
	}
}
