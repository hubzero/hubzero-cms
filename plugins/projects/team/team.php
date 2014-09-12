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
 * Projects team
 */
class plgProjectsTeam extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function plgProjectsTeam (&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'projects', 'team' );
		$this->_params = new JParameter( $this->_plugin->params );
				
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
			'name' => 'team',
			'title' => JText::_('COM_PROJECTS_TAB_TEAM')
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
				
		$objO = new ProjectOwner($database);		
		$counts['team'] = $objO->countOwners($project->id, $filters = array());
		
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
		if (!$authorized && !$project->owner) 
		{
			return $arr;
		}
								
		// Are we returning HTML?
		if ($returnhtml) 
		{
			// Load language file
			$this->loadLanguage();

			// Load component configs
			$this->_config =& JComponentHelper::getParams( 'com_projects' );
			
			$database =& JFactory::getDBO();
			
			// Enable views
			ximport('Hubzero_View_Helper_Html');
			ximport('Hubzero_Plugin_View');
			
			// Set vars									
			$this->_task 		= $action ? $action : JRequest::getVar('action','');
			$this->_project 	= $project;
			$this->_database 	= $database;
			$this->_option 		= $option;
			$this->_authorized 	= $authorized;
			$this->_uid 		= $uid;
			if (!$this->_uid) 
			{
				$juser =& JFactory::getUser();
				$this->_uid = $juser->get('id');
			}
			$this->_msg = $msg;

			if ($error) 
			{
				$this->setError($error);	
			}
			
			// Add CSS and JS
			$document =& JFactory::getDocument();
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginScript('projects', 'team');
			Hubzero_Document::addPluginStylesheet('projects', 'team');
			
			switch($this->_task) 
			{	
				case 'edit': 
				case 'setup': 	
				
					// Do we need to incule extra scripts?
					$plugin 		= JPluginHelper::getPlugin( 'system', 'jquery' );
					$p_params 		= $plugin ? new JParameter($plugin->params) : NULL;

					if (!$plugin || $p_params->get('noconflictSite'))
					{
						$document->addScript('plugins' . DS . 'hubzero' . DS . 'autocompleter' . DS . 'observer.js');
						$document->addScript('plugins' . DS . 'hubzero' . DS . 'autocompleter' . DS . 'textboxlist.js');
						$document->addScript('plugins' . DS . 'hubzero' . DS . 'autocompleter' . DS . 'autocompleter.js');
						$document->addStyleSheet('plugins' . DS . 'hubzero' . DS . 'autocompleter' . DS . 'autocompleter.css');
					}
								
					$arr['html'] = $this->view( 1 ); 
					break;
				
				case 'invite': 
				case 'sendinvite': 
					$arr['html'] = $this->invite(); 
					break;
				
				case 'delete': 
				case 'removeowner': 
					$arr['html'] = $this->delete(); 
					break;
				
				case 'changerole': 
				case 'assignrole': 
					$arr['html'] = $this->_changeRole(); 
					break;
				
				case 'save': 
					$arr['html'] = $this->_save(); 
					break;
				
				case 'quit': 
					$arr['html'] = $this->_quit(); 
					break;
				
				case 'authors': 
					$arr['html'] = $this->authors(); 
					break;
					
				case 'view': 
				default: 
					$arr['html'] = $this->view(); 
					break;	
				
				case 'editauthors': 
				case 'saveauthors': 
					$arr['html'] = $this->_publicationAuthors(); 
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
	 * View team
	 * 
	 * @param      integer  	$edit
	 *
	 * @return     string
	 */
	public function view( $edit = 0 ) 
	{
		$layout = $edit ? 'edit' : 'view';
		$setup  = $this->_task == 'setup' ? 1 : 0;
				
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'team',
				'name'=>$layout
			)
		);
		
		// Instantiate project owner
		$objO = new ProjectOwner($this->_database);
		$view->filters['limit']    =  intval($this->_params->get('limit', 0));
		$view->filters['start']    = JRequest::getInt( 't_limitstart', 0);
		$view->filters['sortby']   = JRequest::getVar( 't_sortby', 'name');
		$view->filters['sortdir']  = JRequest::getVar( 't_sortdir', 'ASC');
		$view->filters['status']   = JRequest::getVar( 't_status', 'active');
		if(!$edit)
		{
			$view->filters['online']   = 1;
		}
		
		// Get all active team members
		$view->team = $objO->getOwners($this->_project->id, $view->filters);

		// Get total count
		$count_filters = $view->filters;
		$count_filters['limit'] = 0;
		$view->total = $objO->countOwners($this->_project->id, $count_filters);
		
		// Get native count
		$count_filters['native'] = 1;
		$view->native_count = $objO->countOwners($this->_project->id, $count_filters );
		
		// Get managers count
		$view->managers_count = count($objO->getIds($this->_project->id, $role = 1));
		
		// Get count of project groups
		$groups = $objO->getProjectGroups( $this->_project->id );
		$view->count_groups = $groups ? count($groups) : 0; 	
		
		$view->params 		= new JParameter($this->_project->params);
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;		
		$view->setup 		= $setup;
		$view->config 		= $this->_config;
		$view->task 		= $this->_task;
		$view->edit 		= $edit;
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
	 * View team that may be selected as publication authors
	 * 
	 * @return     string
	 */
	public function authors() 
	{
		// Incoming
		$versionid 	= JRequest::getInt('versionid', 0);
		$ajax 		= JRequest::getInt('ajax', 0);
		$pid 		= JRequest::getInt('pid', 0);
		
		if (!$ajax or !$versionid) 
		{
			return false;
		}
				
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'team',
				'name'=>'view',
				'layout' => 'authors'
			)
		);
				
		// Instantiate project owner
		$objO = new ProjectOwner($this->_database);
		$view->filters['limit']    		=  0;
		$view->filters['start']    		= JRequest::getInt( 't_limitstart', 0);
		$view->filters['sortby']   		= JRequest::getVar( 't_sortby', 'name');
		$view->filters['sortdir']  		= JRequest::getVar( 't_sortdir', 'ASC');
		$view->filters['status']   		= 'active';
		$view->filters['pub_versionid'] = $versionid;
		
		// Get all active team members
		$view->team = $objO->getOwners($this->_project->id, $view->filters);
		
		// Get current authors
		$pa = new PublicationAuthor($this->_database);
		$view->authors = $pa->getAuthors($versionid);
							
		// Exclude any owners?
		$view->exclude = array();
		
		$view->params 		= new JParameter( $this->_project->params );
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->versionid 	= $versionid;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;		
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
	
	//----------------------------------------
	// Processors
	//----------------------------------------
	
	/**
	 * Save member
	 * 
	 * @return     void, redirect
	 */
	protected function _save()
	{		
		// Incoming
		$members 	= urldecode(trim(JRequest::getVar( 'newmember', '', 'post'  )));
		$groups 	= urldecode(trim(JRequest::getVar( 'newgroup', '' )));
		$role 		= JRequest::getInt( 'role', 0 );
		$pid 		= JRequest::getInt('pid', 0);
		$authors  	= JRequest::getVar('authors', 0, 'post');
		
		// Result collectors
		$m_added 	= 0; // count of individual members added
		$m_invited 	= 0; // count of individuals invited
		$g_added 	= 0; // count of members from new group
		$uids 		= array(); // ids/emails of added people
		$names 		= array(); // names/emails of added people
		$invalid 	= array(); // collector for invalid names
		
		// Setup stage?
		$setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;
		$setup = $this->_project->setup_stage == $setup_complete ? 0 : 1; 
								
		// Get owner class		
		$objO = new ProjectOwner($this->_database);
		
		// Instantiate a new registration object
		ximport('Hubzero_Registration');
		$xregistration = new Hubzero_Registration();
							
		// Owner names not supplied
		if (!$members && !$groups) 
		{
			$this->setError( JText::_('COM_PROJECTS_NO_NAMES_SUPPLIED') );
		}
		else 
		{
			if ($members) 
			{
				$newm = explode(',' ,$members);
				
				// Do we have new authors?
				if ($newm) 
				{
					for ($i=0, $n=count( $newm ); $i < $n; $i++)
					{
						$cid = strtolower(trim($newm[$i]));
						$uid = 0;
						if ($cid == '')
						{
							continue;
						}
						$parts =  preg_split("/[(]/", $cid);
						if (count($parts) == 2) 
						{
							$name = $parts[0];
							$uid = preg_replace('/[)]/', '', $parts[1]);
						}
						elseif (intval($cid))
						{
							$uid = $cid;	
						}
						else 
						{
							$regex = '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/'; 
							if (preg_match($regex, $cid)) 
							{
								// This is an email - check if user with the email exists
								$uid = $xregistration->getEmailId($cid);
								if (!$uid) 
								{
									// Make sure we aren't inviting twice
									$invitee = $objO->checkInvited($this->_project->id, $cid);
									if (!$invitee) 
									{
										// Generate invitation code
										$code = ProjectsHtml::generateCode();

										// Add invitee record
										if ($objO->saveInvite ($this->_project->id, $cid, $code, '', $role)) 
										{
											$uids[] = $cid;	
											$m_invited++;
											if (!$setup && $this->_config->get('messaging') == 1) 
											{
												$this->sendInviteEmail(0, $cid, $code, $role);
											}					
										}
									}
									elseif ($objO->load($invitee)) 
									{
										// Previously deleted invite
										if ($objO->status == 2) 
										{
											$objO->status = 0;
											$objO->role = $role;
											$uids[] = $cid;
											$objO->store();
											$m_invited++;
											if (!$setup && $this->_config->get('messaging') == 1) 
											{
												$this->sendInviteEmail(0, $cid, $objO->invited_code, $objO->role);
											}
										}
									}
								}
							}
							else 
							{
								$invalid[] = $cid;
							}
						}

						if (!$uid or !is_numeric($uid)) 
						{ 
							continue; 
						}
						else 
						{
							$juser =& JUser::getInstance( $uid );
							if (!is_object($juser)) 
							{
								$invalid[] = $uid;
								continue;
							}	
						}						
						
						// Save new author
						$native = ($uid == $this->_project->created_by_user) ? 1 : 0;
						if ($objO->saveOwners ( $this->_project->id, $this->_uid, $uid, 
							0, $role, $status = 1, $native)) 
						{
							$uids[] = $uid;
						}					
					}
				}
			}
			if ($groups) 
			{				
				// Save new authors from group
				$g_added = $objO->saveOwners ($this->_project->id, $this->_uid, 0, $groups, $role, $status = 1, $native = 0);
				if ($objO->getError()) 
				{
					$this->setError($objO->getError());
				}
			
				if ($g_added) 
				{
					$uids = array_merge($uids, $g_added);
				}
			}
		}
		
		// Did we add anyone new?
		$uids = array_unique($uids);
		if (count($uids) > 0) 
		{
			$this->_msg = JText::_('COM_PROJECTS_SUCCESS_ADDED_OR_INVITED').' '.count($uids).' '.JText::_('COM_PROJECTS_NEW').' '.JText::_('COM_PROJECTS_TEAM_MEMBERS');
		
			if (count($invalid) > 0) 
			{
				$this->_msg .= '<br />'.JText::_('COM_PROJECTS_TEAM_MEMBERS_INVALID_NAMES');
			}			
			
			if (!$setup) {
				
				$note  = strtolower(JText::_('COM_PROJECTS_SUCCESS_ADDED_OR_INVITED')).' ';
				for ( $i=0; $i< count($uids); $i++) 
				{
					$uu = $uids[$i];
					if ($uu && is_numeric($uu) ) 
					{
						$xuser =& JUser::getInstance( $uids[$i] );	
						$note  .= is_numeric($uids[$i]) && is_object($xuser) ? $xuser->get('name') : $uids[$i];
					}
					else 
					{
						$note  .= $uids[$i];
					}
				
					if ($i > 1 ) 
					{
						$left = count($uids) - 3; 
						if ($left) 
						{
							$note .= ' '.JText::_('COM_PROJECTS_AND').' '.$left.' '.JText::_('COM_PROJECTS_MORE').' ';
							$note .= $left == 1 ? JText::_('COM_PROJECTS_ACTIVITY_PERSON') : JText::_('COM_PROJECTS_ACTIVITY_PERSONS');
						}	
						break;
					}
					$note  .= $i == (count($uids) - 1) ? '' : ', ';					
				}
				$note .= ' '.JText::_('COM_PROJECTS_TO_PROJECT_TEAM');
				
				// Send out emails
				if ($this->_config->get('messaging') == 1) 
				{
					foreach($uids as $user)	
					{
						$this->sendInviteEmail( $user, '', '', $role );
					}
				}			
			}
			
			// Sync with system group 
			$objO->sysGroup($this->_project->alias, $this->_config->get('group_prefix', 'pr-'));
		} 
		elseif (count($invalid) > 0) 
		{
			 $this->setError(JText::_('COM_PROJECTS_TEAM_MEMBERS_INVALID_NAMES')
				. '<br />'.JText::_('COM_PROJECTS_TEAM_MEMBERS_INVALID_NAMES_EXPLAIN'));
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
		
		if ($authors && $pid)
		{
			// Build pub url
			$route = $this->_project->provisioned 
				? 'index.php?option=com_publications' . a . 'task=submit'
				: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
			$url = JRoute::_($route . a . 'pid=' . $pid).'/?edit=authors';
			$this->_redirect = $url;
			return;			
		}
		else
		{
			$this->_redirect = $setup 
							? JRoute::_('index.php?option=' . $this->_option 
							. a . 'alias=' . $this->_project->alias . a . 'task=setup' . a . 'step=1')
							: JRoute::_('index.php?option=' . $this->_option 
							. a . 'alias=' . $this->_project->alias . a . 'task=edit' . a . 'edit=team');
		}
		
		return; // redirect				
	}
	
	/**
	 * Delete member(s)
	 * 
	 * @return     void, redirect
	 */
	protected function delete()
	{		
		// Incoming
		$checked = JRequest::getVar( 'owner', '', 'request', 'array' );
		$groups = JRequest::getVar( 'group', '', 'request', 'array' );
		
		// Are we setting up project?
		$setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;
		$setup = $this->_project->setup_stage == $setup_complete ? 0 : 1; // setup stage?
		
		// Instantiate project owner
		$objO = new ProjectOwner($this->_database);
	
		if (!empty($checked)) 
		{		
			// Get owners info from owner id(s)
			$selected = $objO->getInfo($this->_project->id, $checked, $groups);
		}
		else 
		{
			$selected = array();
			$this->setError(JText::_('COM_PROJECTS_ERROR_NOONE_TO_DELETE'));
		}
		
		if ($this->_task == 'delete') 
		{
			// Output HTML
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'projects',
					'element'=>'team',
					'name'=>'delete'
				)
			);
			
			$view->selected 	= $selected;
			$view->checked 		= $checked;
			$view->option 		= $this->_option;
			$view->project 		= $this->_project;
			$view->authorized 	= $this->_authorized;
			$view->uid 			= $this->_uid;
			$view->setup 		= $setup;	
			$view->aid 			= $objO->getOwnerID($this->_project->id, $this->_uid);	
			$view->msg 			= isset($this->_msg) ? $this->_msg : '';
			if ($this->getError()) 
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();			
		}
		else 
		{
			// Get all managers
			$all = $objO->getIds($this->_project->id, $role = 1);
			$remaining = array_diff($all, $checked);
			$deleted = 0;
			
			// Cannot delete if no managers remain
			if ($remaining) 
			{
				// Perform delete
				$deleted = $objO->removeOwners ( $this->_project->id, $checked, 1);
				if ($deleted) 
				{
					$this->_msg = JText::_('COM_PROJECTS_OWNERS_DELETED');
				}
			}
			else {
				if (count($all) > 0) 
				{
					$left = array_diff($checked, array($all[0])); // leave one manager
					$deleted = $objO->removeOwners ( $this->_project->id, $left, 1);
				}
				$this->setError( JText::_('COM_PROJECTS_OWNERS_DELETE_NOMANAGERS') );
			}
			
			if ($deleted) 
			{
				// Sync with system group 
				$objO->sysGroup($this->_project->alias, $this->_config->get('group_prefix', 'pr-'));
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
			
			return; // redirect			
		}							
	}

	/**
	 * Quit project
	 * 
	 * @return     void, redirect
	 */
	protected function _quit()
	{
		$html = '';
		
		// Incoming
		$confirm = JRequest::getInt( 'confirm', 0, 'post' );
		
		// Instantiate project owner
		$objO = new ProjectOwner($this->_database);
		
		// Check to make sure we are not deleting last manager
		$onlymanager = 0;
		if ($this->_project->role == 1) 
		{								
			$managers = $objO->getIds($this->_project->id, $role = 1);
			if (count($managers) == 1) 
			{
				$onlymanager = 1;
			}
		}
			
		if ($confirm && !$onlymanager) 
		{		
			$deleted = $objO->removeOwners ( $this->_project->id, array($this->_uid));
			if ($deleted) 
			{
				$this->_msg = JText::_('COM_PROJECTS_TEAM_MEMBER_QUIT_SUCCESS');
				
				// Record activity
				$objAA = new ProjectActivity($this->_database);
				$aid = $objAA->recordActivity( $this->_project->id, $this->_uid, 
					JText::_('COM_PROJECTS_TEAM_PROJECT_QUIT') , 0, '', '', 'team', 0 );
				
				// Sync with system group 
				$objO->sysGroup($this->_project->alias, $this->_config->get('group_prefix', 'pr-'));				
			}
		} 
		else 
		{
			// Output HTML
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'projects',
					'element'=>'team',
					'name'=>'quit'
				)
			);
			
			$objO->loadOwner($this->_project->id, $this->_uid);
			$view->group 		= $objO->groupid;
			$view->onlymanager 	= $onlymanager;
			$view->option 		= $this->_option;
			$view->database 	= $this->_database;
			$view->project 		= $this->_project;
			$view->authorized 	= $this->_authorized;
			$view->uid 			= $this->_uid;	
			$view->config 		= $this->_config;	
			$view->msg 			= isset($this->_msg) ? $this->_msg : '';
			$view->title		= $this->_area['title'];
			if ($this->getError()) 
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();			
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
		
		$this->_referer = JRoute::_('index.php?option='.$this->_option);
		return;		
	}
	
	/**
	 * Change member role
	 * 
	 * @return     void, redirect
	 */
	protected function _changeRole()
	{		
		// Incoming
		$checked 	= JRequest::getVar( 'owner', '', 'request', 'array' );
		$groups 	= JRequest::getVar( 'group', '', 'request', 'array' );
		$owner 		= JRequest::getVar( 'owner', '');
		$role 		= JRequest::getInt ( 'role', 0 );
		
		if ($owner)
		{
			$checked = array($owner);
		}
		
		// Are we setting up project?
		$setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;
		$s = $this->_project->setup_stage == $setup_complete ? 0 : 1; // setup stage?
		
		// Instantiate project owner
		$objO = new ProjectOwner($this->_database);
	
		if (!empty($checked)) 
		{		
			// Get owners info from owner id(s)
			$selected = $objO->getInfo($this->_project->id, $checked, $groups);
		}
		else 
		{
			$selected = array();
			$this->setError(JText::_('COM_PROJECTS_ERROR_NOONE_TO_REASSIGN_ROLE'));
		}
		
		if ($this->_task == 'changerole') 
		{
			// Output HTML
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'projects',
					'element'=>'team',
					'name'=>'role'
				)
			);
			
			$view->selected 	= $selected;
			$view->checked 		= $checked;
			$view->option 		= $this->_option;
			$view->project 		= $this->_project;
			$view->authorized 	= $this->_authorized;
			$view->uid 			= $this->_uid;	
			$view->aid 			= $objO->getOwnerID($this->_project->id, $this->_uid);	
			$view->msg 			= isset($this->_msg) ? $this->_msg : '';
			if ($this->getError()) 
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();			
		}
		else 
		{
			// Changing role(s) to collaborator
			$left = $checked;
			if ($role == 0) 
			{
				// Get all managers
				$all = $objO->getIds($this->_project->id, 1);
				$remaining = array_diff($all, $checked);
				if (!$remaining && count($all) > 0) 
				{
					$left = array_diff($checked, array($all[0])); // leave one manager
					$this->setError(JText::_('COM_PROJECTS_OWNERS_REASSIGN_NOMANAGERS'));
				}
			}
			
			if ($objO->reassignRole ( $this->_project->id, $left, 1, $role)) 
			{
				$this->_msg = JText::_('COM_PROJECTS_OWNERS_ROLE_CHANGED');
				
				// Sync with system group 
				$objO->sysGroup($this->_project->alias, $this->_config->get('group_prefix', 'pr-'));
			}
			
			$this->_task = $s ? 'setup' : 'edit';		
			return $this->view ( $edit = 1 ); 
		}							
	}

	//----------------------------------------
	// Messaging
	//----------------------------------------

	/**
	 * Send invite email
	 * 
	 * @param  integer 	$uid
	 * @param  string 	$email
	 * @param  string 	$code
	 * @param  integer 	$role
	 * @param  object 	$project
	 *
	 * @return boolean True on success
	 */			
	public function sendInviteEmail( $uid = 0, $email = '', $code = '', $role = 0, $project = '' ) 
	{		
		if ($uid && !$email) 
		{
			$user =& JUser::getInstance( $uid );
			$email = is_object($user) ? $user->get('email') : '';
		}
		
		if (!$email || (!$uid && !$code)) 
		{
			return false;
		}
		
		if (!$project)
		{
			$project = $this->_project;
		}
		
		// Validate email
		$regex = '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/'; 
		if (!preg_match($regex, $email)) 
		{
			return false;
		}
				
		// Set up email config
		$jconfig =& JFactory::getConfig();
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_option));
		$from['email'] = $jconfig->getValue('config.mailfrom');
				
		// Email message subject
		if ($this->_project->provisioned == 1)
		{
			$objPub 	= new Publication($this->_database);
			$pub 		= $objPub->getProvPublication($this->_project->id);
			
			if (!$pub || !$pub->id)
			{
				return false;
			}
			$subject 	= $uid 
						? JText::_('COM_PROJECTS_EMAIL_SUBJECT_ADDED_PROV')
						: JText::_('COM_PROJECTS_EMAIL_SUBJECT_INVITE_PROV');	
		}
		else 
		{
			$subject = $uid 
					? JText::_('COM_PROJECTS_EMAIL_SUBJECT_ADDED').' '.$this->_project->alias
					: JText::_('COM_PROJECTS_EMAIL_SUBJECT_INVITE').' '.$this->_project->alias;
		}
		
		// Message body for HUB user
		$eview = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'team',
				'name'=>'emails',
				'layout'=>'invite'
			)
		);	
		
		// Get profile of author group
		if ($this->_project->owned_by_group) {			
			$eview->nativegroup = Hubzero_Group::getInstance( $this->_project->owned_by_group);
		}
		
		$eview->option 			= $this->_option;
		$eview->hubShortName 	= $jconfig->getValue('config.sitename');
		$eview->by 				= $this->_uid;
		$actor 					=& JUser::getInstance( $this->_uid );
		$eview->actor 			= $actor;
		$eview->uid 			= $uid;		
		$eview->project 		= $this->_project;
		$eview->code 			= $code;
		$eview->email 			= $email;
		$eview->role 			= $role;
		$eview->pub 			= isset($pub) ? $pub : '';
		$message 				= $eview->loadTemplate();
		$message 				= str_replace("\n", "\r\n", $message);
		
		if ($uid) 
		{
			// Send HUB message
			JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher =& JDispatcher::getInstance();
			if ($dispatcher->trigger( 'onSendMessage', array( 'projects_member_added', 
				$subject, $message, $from, 
				array($uid), $this->_option ))) {
				return true;
			}
		}
		else {			
			if (ProjectsHtml::email($email, $jconfig->getValue('config.sitename').': '.$subject, $message, $from)) {
				return true;
			}
		}
					
		return false;
	}
	
	//----------------------------------------
	// Publication authors management
	//----------------------------------------	

	/**
	 * View publication authors (provisioned project)
	 *
	 * @return string
	 */			
	public function _publicationAuthors() 
	{						
		// Incoming
		$version 	= JRequest::getVar( 'version', 'default' );
		$pid 		= JRequest::getInt( 'pid', 0 );
		$ajax 		= JRequest::getInt('ajax', 0);
		
		// Load language file
		$lang = JFactory::getLanguage();
		$lang->load('plg_projects_publications');
		
		// Load publication & version classes
		$objP = new Publication($this->_database);
		$row  = new PublicationVersion($this->_database);		
		
		// Make sure we have publication id
		if (!$pid && $this->_project->provisioned == 1)
		{
			$pid = $objPub->getProvPublication($this->_project->id);	
		}
		if (!$pid)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			return;
		}

		// Check that version exists
		if ($version != 'default')
		{
			$version = $row->checkVersion($pid, $version) ? $version : 'default';			
		}
				
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'team',
				'name'=>'edit',
				'layout'=>'authors'
			)
		);
		
		// Load publication
		$view->pub = $objP->getPublication($pid, $version, $this->_project->id);

		// Instantiate project owner
		$objO = new ProjectOwner($this->_database);
		
		// Set filters
		$view->filters['limit']    =  0;
		$view->filters['start']    = JRequest::getInt( 't_limitstart', 0);
		$view->filters['sortby']   = JRequest::getVar( 't_sortby', 'name');
		$view->filters['sortdir']  = JRequest::getVar( 't_sortdir', 'ASC');
		$view->filters['status']   = JRequest::getVar( 't_status', 'active');

		// Get all active team members
		$view->team = $objO->getOwners( $this->_project->id, $view->filters );
		
		// Build pub url
		$view->route = $this->_project->provisioned 
					? 'index.php?option=com_publications' . a . 'task=submit'
					: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$view->url = JRoute::_($view->route . a . 'pid=' . $pid) . '?version=' . $version ;
		
		// Save incoming settings
		if ($this->_task == 'saveauthors')
		{		
			if ($view->team && count($view->team) > 0)
			{
				$deleted = 0;
				$changed = 0;
				
				foreach($view->team as $member)
				{
					$role = JRequest::getInt('role_' . $member->id, 0);
					
					if ($role == 9)
					{
						// Delete user
						$deleted = $objO->removeOwners ( $this->_project->id, array($member->id), 1);						
					}
					elseif ($role != 0)
					{
						$changed = $objO->reassignRole( $this->_project->id, array($member->id), 1, $role);
					}
				}
				
				if ($deleted || $changed)
				{
					$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_AUTHORS_CHANGED');
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
				
				// Redirect
				$this->_referer = $view->url . '&section=authors';
				return;					
			}
			
			// Get all team members after changes
			$view->team = $objO->getOwners( $this->_project->id, $view->filters );	
		}		

		$view->params 		= new JParameter($this->_project->params);
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;		
		$view->config 		= $this->_config;
		$view->task 		= $this->_task;
		$view->version 		= $version;
		$view->ajax 		= $ajax;

		// Get messages	and errors	
		$view->msg = $this->_msg;
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}
}