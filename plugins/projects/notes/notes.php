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
 * Projects Notes (wiki) plugin
 */
class plgProjectsNotes extends JPlugin
{	
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function plgProjectsNotes(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'projects', 'notes' );
		$this->_params = new JParameter( $this->_plugin->params );
		
		// Load component configs
		$this->_config =& JComponentHelper::getParams( 'com_projects' );
		
		// Load wiki configs
		$this->_wiki_config =& JComponentHelper::getParams( 'com_wiki' ); 			
				
		$this->_task 	= '';
		$this->_msg 	= '';
		$this->_group 	= ''; // project group
		
		$this->_controllerName = '';
		
		// Output collectors
		$this->_referer = '';
		$this->_message = array();
	}
	
	/**
	 * Event call to determine if this plugin should return data
	 * 
	 * @return     array   Plugin name and title
	 */
	public function &onProjectAreas() 
	{
		$area = array(
			'name' => 'notes',
			'title' => JText::_('COM_PROJECTS_TAB_NOTES')
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
		$database =& JFactory::getDBO();
				
		// Get helper
		$projectsHelper = new ProjectsHelper( $database );
		
		$group_prefix = $this->_config->get('group_prefix', 'pr-');
		$groupname = $group_prefix . $project->alias;
		
		$counts['notes'] = $projectsHelper->getNoteCount( $groupname );
		
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
	 * @return     array   Return array of html
	 */
	public function onProject ( $project, $option, $authorized, 
		$uid, $msg = '', $error = '', $action = '', $areas = null )
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
		
		// Is the user logged in?
		if ( !$authorized && !$project->owner ) 
		{
			return $arr;
		}
		
		// Do we have a project ID?
		if ( !is_object($project) or !$project->id ) 
		{
			return $arr;
		}
		else 
		{
			$this->_project = $project;
		}
		
		// Are we returning HTML?
		if ($returnhtml) 
		{				
			// Load wiki language file
			JPlugin::loadLanguage( 'plg_groups_wiki' );
			JPlugin::loadLanguage( 'plg_projects_notes' );
						
			// Get database
			$database =& JFactory::getDBO();
			
			// Set vars
			$this->_database 	= $database;
			$this->_option 		= $option;
			$this->_authorized 	= $authorized;
			$this->_uid 		= $uid;
			
			if ( !$this->_uid) 
			{
				$juser =& JFactory::getUser();
				$this->_uid = $juser->get('id');
			}
			$this->_msg = $msg;
			if ( $error) 
			{
				$this->setError($error);	
			}
			
			// Get JS
			$document =& JFactory::getDocument();
			$document->addStyleSheet('plugins' . DS . 'groups' . DS . 'wiki' . DS . 'wiki.css');
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginScript('projects', 'notes');
			Hubzero_Document::addPluginStylesheet('projects', 'notes');

			// Import some needed libraries
			ximport('Hubzero_User_Helper');
			
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'attachment.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'author.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'comment.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'log.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'revision.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'page.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'html.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'setup.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'tags.php');
				
			// What's the task?						
			$this->_task = $action ? $action : JRequest::getVar('action', 'view');
			switch ($this->_task)
			{
				case 'upload':
				case 'download':
				case 'deletefolder':
				case 'deletefile':
				case 'media':
					$controllerName = 'media';
				break;

				case 'history':
				case 'compare':
				case 'approve':
					$controllerName = 'history';
				break;

				case 'addcomment':
				case 'savecomment':
				case 'reportcomment':
				case 'removecomment':
				case 'comments':
					$controllerName = 'comments';
				break;

				case 'delete':
				case 'edit':
				case 'save':
				case 'rename':
				case 'saverename':
				default:
					$controllerName = 'page';
				break;
			}
			
			$pagename = trim(JRequest::getVar('pagename', ''));

			if (substr(strtolower($pagename), 0, strlen('image:')) == 'image:'
			 || substr(strtolower($pagename), 0, strlen('file:')) == 'file:') 
			{
				$controllerName = 'media';
				$this->_task = 'download';
			}
			
			if (!file_exists(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'controllers' . DS . $controllerName . '.php'))
			{
				$controllerName = 'page';
			}
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'controllers' . DS . $controllerName . '.php');
			$controllerName = 'WikiController' . ucfirst($controllerName);
			
			// Save controller name
			$this->_controllerName = $controllerName;
			
			// Display page
			$arr['html'] = $this->page(); 		
		}
		
		// Return data
		return $arr;
	}
	
	//----------------------------------------
	// Views
	//----------------------------------------

	/**
	 * View of project note
	 * 
	 * @return     string
	 */
	public function page() 
	{		
		// Incoming
		$preview = trim(JRequest::getVar( 'preview', '' ));	
		$note = JRequest::getVar('page', array(), 'post', 'none', 2);
			
		// Set wiki scope
		$scope = trim(JRequest::getVar( 'scope', '' ));
		$masterscope = 'projects' . DS . $this->_project->alias . DS . 'notes';
		if (!$scope) 
		{
			$scope = $masterscope;
		}
		
		// Get helper
		$projectsHelper = new ProjectsHelper( $this->_database );
		
		// Set project (system) group	
		$group_prefix = $this->_config->get('group_prefix', 'pr-');
		if ( !$this->_group)
		$this->_group = $group_prefix . $this->_project->alias;
		
		// Get the page name
		$pagename = trim(JRequest::getVar( 'pagename', ''));
		$exists = 0;
		
		// Get first project note
		$firstnote = $projectsHelper->getFirstNote( $this->_group, $masterscope );
		if ( !$pagename) 
		{
			// Default view to first available note if no page is requested
			$pagename = ($firstnote && $this->_task != 'new' && $this->_task != 'save') ? $firstnote : 'NewNote';
		}
		
		// Cannot save page with default name
		if ( $this->_task == 'save' && $pagename == 'NewNote') 
		{
			$pagename = '';
			$pagetitle = (isset($note['title']))  ? trim($note['title'])    : '';
		}
		
		// Are we saving?
		$save = $this->_task == 'save' ? 1 : 0;
		$rename = $this->_task == 'saverename' ? 1 : 0;
	
		// Load requested page
		$page = new WikiPage( $this->_database );
		$page->load( $pagename, $scope );
		if ($page->exist()) 
		{
			$exists = 1;
			$_REQUEST['listdir'] = $page->id;
			
			// Check that we have a version
			$revision = new WikiPageRevision($this->_database);
			
			// Create version if does not exists
			if (!$revision->loadByVersion($page->id))
			{
				$revision->pageid     	= $page->id;
				$revision->created    	= date('Y-m-d H:i:s', time());
				$revision->created_by 	= $this->_uid;
				$revision->version 		= 1;
				$revision->approved 	= 1;
				$revision->store();
			}
			
			// Make sure images/files get correct references
			if ($this->_task == 'view')
			{
				// Get wiki upload path
				$previewPath = $this->getWikiPath($page);
				
				// Get project path
				$projectPath = ProjectsHelper::getProjectPath(
					$this->_project->alias, 
					$this->_config->get('webpath', 0),
					$this->_config->get('offroot', 0)
				);
				
				// Include needed library
				include_once(JPATH_ROOT . DS . 'components' . DS 
					. 'com_wiki' . DS . 'tables' . DS . 'attachment.php');
				$ih = new ProjectsImgHandler();
				
				// Get joomla libraries
				jimport('joomla.filesystem.folder');
				jimport('joomla.filesystem.file');
				
				// Get image extensions
				$imgs = explode(',', $this->_wiki_config->get('img_ext'));
				array_map('trim', $imgs);
				array_map('strtolower', $imgs);
					
				// Parse files
				preg_match_all("'File\\(.*?\\)'si", $revision->pagetext, $files);
				if (!empty($files) && $previewPath)
				{
					$files = $files[0];
										
					foreach ($files as $file)
					{
						$ibody = str_replace('File(' , '', $file);
						$ibody = str_replace(')' , '', $ibody);
						$args  = explode(',', $ibody);
						$file  = array_shift($args);

						$fpath = $projectPath . DS . $file;
						
						// Replace reference by link
						if (is_file( $fpath ))
						{
							$ext = strtolower(JFile::getExt($file));
							
							// Is this an image?
							if (in_array(strtolower($ext), $imgs)) 
							{
								$attachment = new WikiPageAttachment($this->_database);
								$atid = $attachment->getID($file, $page->id);
								
								// Copy file to wiki dir if not there
								if (is_file( $fpath ) && !$atid)
								{
									$filename = basename($file);
									JFile::copy($fpath, $previewPath . DS . $filename);
									$revision->pagetext = preg_replace("'\\[\\File\\(". $file ."'si", 
										'[Image('.$filename, $revision->pagetext);
									$revision->store();

									$projectsHelper->saveWikiAttachment($page, $file, $this->_uid);
								}				
							}
							else
							{
								$link = JRoute::_('index.php?option=' . $this->_option . a . 'active=files' 
								. a . 'alias=' . $this->_project->alias) 
								. '/?action=download&file='.urlencode($file);
								$link = $link . ' ' . basename($file);
								$revision->pagetext = preg_replace("'\\[\\File\\(". $file .".*?\\)\\]'si", 
									$link, $revision->pagetext);
								$revision->store();
							}							
						}
					}
				}
				
				// Parse images
				preg_match_all("'Image\\(.*?\\)'si", $revision->pagetext, $images);
				if (!empty($images))
				{
					$images = $images[0];
										
					foreach ($images as $image)
					{
						$ibody = str_replace('Image(' , '', $image);
						$ibody = str_replace(')' , '', $ibody);
						$args  = explode(',', $ibody);
						$file  = array_shift($args);

						$fpath = $projectPath . DS . $file;
						
						$attachment = new WikiPageAttachment($this->_database);
						$atid = $attachment->getID($file, $page->id);
						
						// Copy file to wiki dir if not there
						if (is_file( $fpath ))
						{
							$filename = basename($file);
	
							JFile::copy($fpath, $previewPath . DS . $filename);
							$revision->pagetext = preg_replace("'\\[\\Image\\(". $file ."'si", 
								'[Image('.$filename, $revision->pagetext);
							$revision->store();
							
							if ( !$atid)
							{
								$projectsHelper->saveWikiAttachment($page, $file, $this->_uid);
							}
						}						
					}
				}
			}
		}
					
		// Set some variables for the wiki
		$pagename = $this->_task == 'new' ? 'New Note' : $pagename;
		JRequest::setVar('pagename', $pagename);
		JRequest::setVar('task', $this->_task);
		JRequest::setVar('scope', $scope);
		
		// Instantiate controller
		$controller = new $this->_controllerName(array(
			'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_wiki',
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
			$objAA = new ProjectActivity( $this->_database );
			$what  = $exists ? JText::_('COM_PROJECTS_NOTE_EDITED') : JText::_('COM_PROJECTS_NOTE_ADDED');
			$what .= $exists ? ' "' . $page->title . '" ' : '';
			$what .= ' '.JText::_('COM_PROJECTS_NOTE_IN_NOTES');
			$aid = $objAA->recordActivity($this->_project->id, $this->_uid, $what, 
				'', 'notes', JRoute::_('index.php?option=' . $this->_option . a
				. 'alias=' . $this->_project->alias . a . 'active=notes') , 'notes', 0);
			
			// Record page order for new pages
			$lastorder = $projectsHelper->getLastNoteOrder($this->_group, $scope);
			$order = intval($lastorder + 1);
			$projectsHelper->saveNoteOrder($this->_group, $scope, $order);
		}
		
		// Make sure all scopes of subpages are valid after rename
		if ($rename) 
		{
			// Incoming
			$oldpagename = trim(JRequest::getVar( 'oldpagename', '', 'post' ));
			$newpagename = trim(JRequest::getVar( 'newpagename', '', 'post' ));
			$projectsHelper->fixScopePaths($this->_group, $scope, $oldpagename, $newpagename);
		}
		
		$controller->redirect();
		$content = ob_get_contents();
		ob_end_clean();
			
		// Output HTML
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'notes',
				'name'=>'wrap'
			)
		);
		
		// Fix pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pnames = $pathway->getPathwayNames();
		if (isset($pnames[4]) && $pnames[4] == 'New PROJECTS') 
		{
			$pathway->setItemName(3, 'New Note');
		}	
		
		// Get all notes
		$view->notes = $projectsHelper->getNotes($this->_group, $masterscope);
		
		// Get parent notes
		$view->parent_notes = $projectsHelper->getParentNotes($this->_group, $scope, $this->_task);
		
		$view->templates 	= $page->getTemplates();		
		$view->params 		= new JParameter($this->_project->params);
		$view->task 		= $this->_task;
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->pagename 	= $pagename;
		$view->scope 		= $scope;
		$view->preview 		= $preview;
		$view->group 		= $this->_group;
		$view->content 		= $content;
		$view->firstnote 	= $firstnote;
		$view->page 		= $exists ? $page : '';
		$view->title		= $this->_area['title'];
		
		// Get messages	and errors	
		$view->msg = $this->_msg;
		if ( $this->getError()) 
		{
			$view->setError( $this->getError() );
		}
				
		return $view->loadTemplate();	
	}
	
	/**
	 * Get path to wiki page images and files
	 * 
	 * @param      object  	$page
	 *
	 * @return     string
	 */
	public function getWikiPath( $page)
	{				
		// Get the upload path
		$path = JPATH_ROOT . DS . trim($this->_wiki_config->get('filepath', '/site/wiki'), DS) . DS . $page->id;
		
		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				return false;
			}
		}
		return $path;
	}	
}