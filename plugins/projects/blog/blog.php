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
 * Projects Blog plugin
 */
class plgProjectsBlog extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function plgProjectsBlog(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'projects', 'blog' );
		$this->_params = new JParameter( $this->_plugin->params );
				
		// Output collectors
		$this->_referer = '';
		$this->_message = array();
		
		$this->_path	= NULL;
	}
	
	/**
	 * Event call to determine if this plugin should return data
	 * 
	 * @return     array   Plugin name and title
	 */
	public function &onProjectAreas() 
	{
		$area = array(
			'name' => 'blog',
			'title' => JText::_('COM_PROJECTS_TAB_FEED')
		);
		
		return $area;
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
		$uid, $msg = '', $error = '', $action = '', $areas = NULL )
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
		
		// Check authorization
		if ( !$authorized && !$project->owner ) 
		{
			return $arr;
		}
		
		// Do we have a project ID?
		if (!is_object($project) or !$project->id ) 
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
			// Load language file
			$this->loadLanguage();
			
			// Load component configs
			$this->_config = JComponentHelper::getParams( 'com_projects' );
			
			$database = JFactory::getDBO();
			
			// Add CSS and JS
			$document = JFactory::getDocument();

			\Hubzero\Document\Assets::addPluginScript('projects', 'blog');
			\Hubzero\Document\Assets::addPluginStylesheet('projects', 'blog');
																	
			// Set vars									
			$this->_task = JRequest::getVar( 'action', '' );
			$this->_database = $database;
			$this->_option = $option;
			$this->_authorized = $authorized;
			$this->_msg = $msg;
			if ($error) 
			{
				$this->setError( $error );	
			}
			
			$this->_uid = $uid;
			if (!$this->_uid) 
			{
				$juser = JFactory::getUser();
				$this->_uid = $juser->get('id');
			}
							
			switch ($this->_task)
			{
				case 'page': 
				default: 
					$arr['html'] = $this->view(); 
					break;
				case 'delete':
					$arr['html'] = $this->_delete(); 
					break;
				case 'save':
					$arr['html'] = $this->_save(); 
					break;
				case 'savecomment':
					$arr['html'] = $this->_saveComment(); 
					break;
				case 'deletecomment':
					$arr['html'] = $this->_deleteComment(); 
					break;
				case 'update': 
					$arr['html'] = $this->updateFeed(); 
					break;
			}			
		}
		
		$arr['referer'] = $this->_referer;
		$arr['msg'] = $this->_message;
		
		// Return data
		return $arr;
	}
	
	//----------------------------------------
	// Views
	//----------------------------------------
	
	/**
	 * View of project updates
	 * 
	 * @return     string
	 */	
	public function view() 
	{		
		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'=>'projects',
				'element'=>'blog',
				'name'=>'view'
			)
		);
		
		// Get activities
		$objAC = new ProjectActivity( $this->_database );
		$view->filters 			= array();
		$view->filters['role'] 	= $this->_project->role;
		$view->total 			= $objAC->getActivities( $this->_project->id, $view->filters, 1, $this->_uid );
		$view->limit 			= intval($this->_params->get('limit', 25));
		$view->filters['limit'] = JRequest::getVar( 'limit', $view->limit, 'request' );
		$view->activities 		= $this->_prepActivities ( $view->filters, $view->limit );	

		// Output html
		$view->params 		= new JParameter( $this->_project->params );
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->config 		= $this->_config;
		$view->title		= $this->_area['title'];
		
		// Get messages	and errors	
		$view->msg = $this->_msg;
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();		
	}
	
	//----------------------------------------
	// Processors
	//----------------------------------------
	
	/**
	 * Save new blog entry
	 * 
	 * @return     void, redirect
	 */
	protected function _save()
	{
		// Incoming
		$managers = JRequest::getInt( 'managers_only', 0 );
		$entry = trim(JRequest::getVar( 'blogentry', '' ));
		
		// Text clean-up
		$entry = \Hubzero\Utility\Sanitize::stripAll($entry);	
				
		// Instantiate project microblog entry
		$objM = new ProjectMicroblog( $this->_database );
		if ($entry) 
		{
			$objM->projectid = $this->_project->id;
			$objM->blogentry = $entry;
			$objM->managers_only = $managers;
			$objM->posted = JFactory::getDate()->toSql();
			$objM->posted_by = $this->_uid;
			
			// Save new blog entry
			if (!$objM->store()) 
			{
				$this->setError( $objM->getError() );
			}
			else 
			{
				$this->_msg = JText::_('COM_PROJECTS_NEW_BLOG_ENTRY_SAVED');
			}
			
			// Get new entry ID
			if (!$objM->id) 
			{
				$objM->checkin();
			}
			
			// Record activity
			$objAA = new ProjectActivity( $this->_database );
			if ($objM->id ) 
			{
				$aid = $objAA->recordActivity( 
					$this->_project->id, 
					$this->_uid, 
					JText::_('COM_PROJECTS_SAID'), 
					$objM->id, '', '', 'blog', 1 
				);
			}
			
			// Store activity ID
			if ($aid) 
			{
				$objM->activityid = $aid;
				$objM->store();
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

		// Redirect back to feed
		$this->_referer = JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias . a .'active=feed');
		return;						
	}
	
	
	/**
	 * Delete blog entry
	 * 
	 * @return     void, redirect
	 */
	protected function _delete()
	{
		// Incoming
		$tbl = trim(JRequest::getVar( 'tbl', 'activity' ));
		$eid = JRequest::getInt( 'eid', 0 );
				
		// Are we deleting a blog entry?
		if ($tbl == 'blog') 
		{
			$objM = new ProjectMicroblog( $this->_database );
			
			if ($eid && $objM->load($eid)) 
			{
				// Get associated commenting activities
				$objC = new ProjectComment( $this->_database );
				$activities = $objC->collectActivities($eid, $tbl );
				$activities[] = $objM->activityid;

				// Delete blog entry
				if ($objM->deletePost()) 
				{					
					$this->_msg = JText::_('COM_PROJECTS_ENTRY_DELETED');
					
					// Delete all associated comments
					$comments = $objC->deleteComments($eid, $tbl );
					
					// Delete all associated activities
					foreach($activities as $a) 
					{
						$objAA = new ProjectActivity( $this->_database );
						$objAA->loadActivity($a, $this->_project->id);
					    $objAA->deleteActivity();
					}
				}
			}
		}
		// Are we deleting activity?
		if ($tbl == 'activity') 
		{
			$objAA = new ProjectActivity( $this->_database );
			$objAA->loadActivity($eid, $this->_project->id);
			
			if ($this->_project->role == 1 or $this->_authorized or $objAA->userid == $this->_uid) 
			{			
				// Get associated commenting activities
				$objC = new ProjectComment( $this->_database );
				$activities = $objC->collectActivities($eid, $tbl );
			
				if ($objAA->deleteActivity()) 
				{
					$this->_msg = JText::_('COM_PROJECTS_ENTRY_DELETED');
				
					// Delete all associated comments
					$comments = $objC->deleteComments($eid, $tbl );
				
					// Delete all associated activities
					foreach($activities as $a) 
					{
						$objAA = new ProjectActivity( $this->_database );
						$objAA->loadActivity($a, $this->_project->id);
					    $objAA->deleteActivity();
					}
				}
			}
			else 
			{
				// unauthorized
				$this->setError( JText::_('COM_PROJECTS_ERROR_ACTION_NOT_AUTHORIZED') );
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

		// Redirect back to feed
		$this->_referer = JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias . a .'active=feed');
		return;		
		
	}
	
	//----------------------------------------
	// Retrievers & Prep
	//----------------------------------------
	
	/**
	 * Update activity feed (load more entries)
	 * 
	 * @return     string
	 */
	public function updateFeed()
	{		
		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'=>'projects',
				'element'=>'blog',
				'name'=>'activity'
			)
		);
		$objAC = new ProjectActivity( $this->_database );
		$view->filters 			= array();
		$view->total 			= $objAC->getActivities ($this->_project->id, $view->filters, 1, $this->_uid);
		$view->limit 			= intval($this->_params->get('limit', 25));
		$view->filters['limit'] = JRequest::getVar( 'limit', $view->limit, 'request' );
		$view->option 			= $this->_option;
		$view->project 			= $this->_project;
		$view->activities 		= $this->_prepActivities ( $view->filters, $view->limit );
		$view->goto  			= 'alias=' . $this->_project->alias;
		$view->uid 				= $this->_uid;
		$view->database 		= $this->_database;
		$view->title			= $this->_area['title'];
		return $view->loadTemplate();		
	}
	
	/**
	 * Collect activity data
	 * 
	 * @param      array 	$filters    Query filters
	 * @param      integer  $limit 		Number of entries
	 *
	 * @return     array
	 */
	protected function _prepActivities( $filters, $limit )
	{
		// Get latest activity
		$objAC = new ProjectActivity( $this->_database );
		$activities = $objAC->getActivities ($this->_project->id, $filters, 0, $this->_uid);	

		// Instantiate some classes
		$objM = new ProjectMicroblog( $this->_database );
		$objC = new ProjectComment( $this->_database );
		$objTD = new ProjectTodo( $this->_database );

		// Collectors
		$shown = array();
		$newc = array();
		$skipped = array();
		$prep = array();
		
		// Loop through activities
		if (count($activities) > 0 ) 
		{
			foreach ($activities as $a) 
			{ 
				// Is this a comment?
				if ($a->class == 'quote') 
				{				
					// Get comment
					$c = $objC->getComments(NULL, NULL, $a->id, $this->_project->lastvisit);
					
					if (!$c)
					{
						continue;
					}

					// Bring up commented item
					$needle 	= array('id' => $c->parent_activity);
					$key 		= ProjectsHtml::myArraySearch($needle, $activities);
					$shown[] 	= $a->id;
					if (!$key) 
					{
						// get and add parent activity
						$filters['id'] = $c->parent_activity;
						$pa = $objAC->getActivities ($this->_project->id, $filters, 0, $this->_uid);
						if ($pa && count($pa) > 0)
						{
							$a = $pa[0];
						}
					}
					else 
					{
						$a = $activities[$key]; 
					}					
					$a->new = isset($c->newcount) ? $c->newcount : 0;
				}

				if (!in_array($a->id, $shown)) 
				{
					$shown[] = $a->id;
					$class = $a->class ? $a->class : 'activity';
					$new = $this->_project->lastvisit && $this->_project->lastvisit <= $a->recorded ? true : false;

					// Display hyperlink
					if ($a->highlighted && $a->url) 
					{
						$a->activity = str_replace($a->highlighted, '<a href="'.$a->url.'">'.$a->highlighted.'</a>', $a->activity);
					}

					// Set vars
					$body 		= '';
					$eid 		= $a->id;
					$etbl 		= 'activity';
					$deletable 	= 0;

					// Get blog entry
					if ($class == 'blog') 
					{
						$blog = $objM->getEntries( $this->_project->id, $bfilters = array('activityid' => $a->id), $a->referenceid);
						if (!$blog) 
						{
							continue;
						}
						$body 		= $blog ? $blog[0]->blogentry : '';
						$eid 		= $blog[0]->id;
						$etbl 		= 'blog';
						$deletable 	= 1;
					}

					// Get todo item
					if ($class == 'todo') 
					{
						$todo = $objTD->getTodos( $this->_project->id, $tfilters = array('activityid' => $a->id), $a->referenceid);					
						if (!$todo) 
						{
							continue;						
						}
						$body 		= $todo ? $todo[0]->content : '';
						$eid 		= $todo[0]->id;
						$etbl 		= 'todo';
						$deletable 	= 0; // Cannot delete to-do related activity
					}
					
					// Get/parse item preview if available
					$ebody = $this->getItemPreview($class, $a, $body);
					
					// Get comments
					$comments = $objC->getComments($eid, $etbl );

					// Is user allowed to delete item?
					$deletable = $deletable && ($a->userid == $this->_uid or $this->_project->role == 1) ? 1 : 0;

					$prep[] = array('activity' => $a, 'eid' => $eid, 'etbl' => $etbl, 
						'body' => $ebody, 'deletable' => $deletable, 
						'comments' => $comments, 'class' => $class, 'new' => $new 
					);
				}
			}
		}	

		return $prep;
	} 
	
	/**
	 * Display 'more' link if text is too long
	 * 
	 * @param      string	$body   	Text body to shorten
	 * @param      object	$activity   Individual activity
	 * @return     HTML
	 */	
	public function drawBodyText ( $body = NULL )
	{
		if (!$body)
		{
			return false;
		}
		
		$shorten = ($body && strlen($body) > 250) ? 1 : 0;
		$shortBody = $shorten ? \Hubzero\Utility\String::truncate($body, 250) : $body;
		
		// Embed links
		$body 	   = ProjectsHtml::replaceUrls($body, 'external');
		$shortBody = ProjectsHtml::replaceUrls($shortBody, 'external');
		
		// Style body text
		$ebody = '<span class="body';
		$ebody.= strlen($shortBody) > 50 ? ' newline' : ' sameline';
		$ebody.= '">' . $shortBody;
		if ($shorten)
		{
			$ebody .= ' <a href="#" class="more-content">' . JText::_('COM_PROJECTS_MORE') . '</a>';
		}
		$ebody.= '</span>';	
		
		if ($shorten)
		{
			$ebody .= '<span class="fullbody hidden">' . $body . '</span>' ;
		}
		
		return $ebody;
		
	}
	
	/**
	 * Get preview
	 * 
	 * @param      string	$type    	Item type (files, notes etc.)
	 * @param      object	$activity   Individual activity
	 * @return     HTML
	 */	
	public function getItemPreview( $type = NULL, $activity = NULL, $body = NULL )
	{
		$ref = $activity->referenceid;
		
		if ($body) 
		{						
			return $this->drawBodyText($body);
		}
		
		if (!$ref || !$type)
		{
			return false;
		}
		
		$previewBody = NULL;
		
		switch ($type)
		{
			case 'files': 
				$previewBody = $this->_getFilesPreview($ref);
				break;
				
			case 'notes': 
				$previewBody = $this->_getNotesPreview($ref);
				break;
		
			case 'tools': 
				$previewBody = $this->_getToolPreview($activity);
				break;
		}
		
		return $previewBody;
	}
	
	/**
	 * Get Tool Preview
	 * 
	 * @param      object	$activity   Individual activity
	 * @return     void, redirect
	 */	
	protected function _getToolPreview( $activity = NULL, $body = NULL)
	{		
		if (!$activity)
		{
			return false;
		}
		
		// Get app log
		if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components' 
			. DS . 'com_tools' . DS . 'tables' . DS . 'project.tool.log.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' 
				. DS . 'com_tools' . DS . 'tables' . DS . 'project.tool.log.php');
				
			$objLog = new ProjectToolLog( $this->_database );
			$aLog = $objLog->getLog($activity->referenceid, $activity->id);
			
			if ($aLog)
			{
				$aLog = rtrim(stripslashes($aLog));
				$aLog = \Hubzero\Utility\Sanitize::stripAll($aLog);
				$body = $aLog;
			}
		}
		
		return $body;
	}
	
	/**
	 * Get Note Previews
	 * 
	 * @param      string	$ref   	 	 Reference to note
	 * @return     void, redirect
	 */	
	protected function _getNotesPreview( $ref = '')
	{		
		if (!$ref)
		{
			return false;
		}
		
		// Import some needed libraries
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'models' . DS . 'book.php');
				
		$page = new WikiTablePage( $this->_database );		
		if ($page->loadById( $ref )) 
		{			
			$revision = $page->getCurrentRevision();
			// TBD
			// return $revision->get('pagehtml');
		}
		
		return false;
	}
	
	/**
	 * Get File Previews
	 * 
	 * @param      string 	$ref    Reference to files
	 * @return     void, redirect
	 */	
	protected function _getFilesPreview( $ref = '')
	{		
		if (!$ref)
		{
			return false;
		}
		
		if (!$this->_path)
		{
			// Get project file path
			$this->_path = ProjectsHelper::getProjectPath($this->_project->alias, 
					$this->_config->get('webpath'), $this->_config->get('offroot', 0));
		}
		
		// We do need project file path
		if (!$this->_path)
		{
			return false;
		}
		
		$files 	  = explode(',', $ref);
		$selected = array();
				
		// Include Git Helper
		$this->getGitHelper();
		
		// Include image handler
		$ih = new ProjectsImgHandler();	
		
		$imagepath = trim($this->_config->get('imagepath', '/site/projects'), DS);
		$to_path = DS . $imagepath . DS . strtolower($this->_project->alias) . DS . 'preview';
		
		foreach ($files as $item)
		{
			$parts = explode(':', $item);
			$file  = count($parts) > 1 ? $parts[1] : $parts[0];
			$hash  = count($parts) > 1 ? $parts[0] : NULL;
			
			if (!$hash)
			{
				$hash   = $this->_git->gitLog($this->_path, $file, '' , 'hash');
				$hash  	= $hash ? substr($hash, 0, 10) : '';
			}
			
			if ($hash)
			{
				$hashed = $ih->createThumbName(basename($file), '-' . $hash, 'png');
				
				if (is_file(JPATH_ROOT. $to_path . DS . $hashed))
				{
					$preview['image'] = $to_path . DS . $hashed;
					$preview['url']   = NULL;				
					$preview['title'] = basename($file);			
					$selected[] = $preview;
				}
			}
		}
		
		// No files for preview
		if (empty($selected))
		{
			return false;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'blog',
				'name'		=>'preview',
				'layout' 	=> 'files'
			)
		);
		
		$view->selected		= $selected;
		return $view->loadTemplate();
	}
	
	/**
	 * Save comment
	 * 
	 * @return     void, redirect
	 */	
	protected function _saveComment()
	{
		// Incoming
		$itemid = JRequest::getInt( 'itemid', 0, 'post' );
		$tbl = trim(JRequest::getVar( 'tbl', 'activity', 'post' ));
		$comment = trim(JRequest::getVar( 'comment', '', 'post' ));
		$parent_activity = JRequest::getInt( 'parent_activity', 0, 'post' );

		// Clean-up
		$comment = \Hubzero\Utility\Sanitize::stripAll($comment);

		// Instantiate comment
		$objC = new ProjectComment( $this->_database );
		if ($comment) 
		{
			$objC->itemid 			= $itemid;
			$objC->tbl 				= $tbl;
			$objC->parent_activity 	= $parent_activity;
			$objC->comment 			= $comment;
			$objC->created 			= JFactory::getDate()->toSql();
			$objC->created_by 		= $this->_uid;
			if (!$objC->store()) 
			{
				$this->setError( $objC->getError() );
			}
			else 
			{
				$this->_msg = JText::_('COM_PROJECTS_COMMENT_POSTED');
			}			
			// Get new entry ID
			if (!$objC->id) {
				$objC->checkin();
			}

			// Record activity
			$objAA = new ProjectActivity( $this->_database );
			if ( $objC->id ) 
			{
				$what = $tbl == 'blog' ? JText::_('COM_PROJECTS_BLOG_POST') : JText::_('COM_PROJECTS_AN_ACTIVITY');
				$what = $tbl == 'todo' ? JText::_('COM_PROJECTS_TODO_ITEM') : $what;
				$url = '#tr_'.$parent_activity; // same-page link
				$aid = $objAA->recordActivity( 
					$this->_project->id, 
					$this->_uid, 
					JText::_('COM_PROJECTS_COMMENTED').' '.JText::_('COM_PROJECTS_ON').' '.$what, 
					$objC->id, $what, $url, 'quote', 0 
				);
			}

			// Store activity ID
			if ($aid) 
			{
				$objC->activityid = $aid;
				$objC->store();
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

		// Redirect back to feed
		$this->_referer = JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias . a .'active=feed');
		return;
	}

	/**
	 * Delete comment
	 * 
	 * @return     void, redirect
	 */
	protected function _deleteComment ()
	{
		// Incoming
		$cid  = JRequest::getInt( 'cid', 0 );

		// Instantiate comment
		$objC = new ProjectComment( $this->_database );	

		if ($objC->load($cid)) 
		{
			$activityid = $objC->activityid;

			// delete comment
			if ($objC->deleteComment()) 
			{
				$this->_msg = JText::_('COM_PROJECTS_COMMENT_DELETED');
			}

			// delete associated activity
			$objAA = new ProjectActivity( $this->_database );
			if ($activityid && $objAA->load($activityid)) 
			{
				$objAA->deleteActivity();
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

		// Redirect back to feed
		$this->_referer = JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias . a .'active=feed');
		return;		
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
}