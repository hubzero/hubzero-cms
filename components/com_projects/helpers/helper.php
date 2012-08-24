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

/**
 * Projects helper class
 */
class ProjectsHelper extends JObject {
		
	/**
	 * Project ID
	 * 
	 * @var mixed
	 */
	private $_id = 0;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_data = array();

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */	
	public function __construct( &$db )
	{
		$this->_db =& $db;
	}
		
	/**
	 * Get a list of project notes
	 * 
	 * @param      string $group cn of project group
	 * @param      string $masterscope
	 * @param      int $limit
	 * @param      string $orderby
	 * @return     void
	 */	
	public function getNotes( $group, $masterscope, $limit = 0, $orderby = 'p.scope, p.times_rated ASC, p.id' ) 
	{
		$query = "SELECT DISTINCT p.pagename, p.title, p.scope, p.times_rated 
		          FROM #__wiki_page AS p 
				  WHERE p.group_cn='" . $group . "' 
				  AND p.scope LIKE '" . $masterscope . "%' 
				  AND p.pagename NOT LIKE 'Template:%'
				  ORDER BY $orderby ";
		$query.= intval($limit) ? " LIMIT $limit" : '';
				
		$this->_db->setQuery($query);				
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Get count of project notes
	 * 
	 * @param      string $group cn of project group
	 * @return     void
	 */	
	public function getNoteCount( $group ) 
	{
		$query = "SELECT COUNT(*) FROM #__wiki_page AS p 
				  WHERE p.group_cn='" . $group . "' 
				  AND p.pagename NOT LIKE 'Template:%'";
				
		$this->_db->setQuery($query);				
		return $this->_db->loadResult();
	}
	
	/**
	 * Get a list of parent project notes
	 * 
	 * @param      string $group cn of project group
	 * @param      string $scope
	 * @param      string $task
	 * @return     void
	 */	
	public function getParentNotes( $group, $scope, $task = '' ) 
	{
		$parts = explode ( '/', $scope );	
		$remaining = array_slice($parts, 3);
		if ($remaining) 
		{
			$query = "SELECT DISTINCT p.pagename, p.title, p.scope ";
			$query.= "FROM #__wiki_page AS p ";
			$query.= "WHERE p.group_cn='" . $group . "' ";
			$k = 1;
			$where = '';
			foreach ($remaining as $r) 
			{
				$where .= "p.pagename='" . trim($r) . "'";
				$where .= $k == count($remaining) ? '' : ' OR ';
				$k++;
			}
			$query.= "AND (".$where.")" ;
			$this->_db->setQuery($query);				
			return $this->_db->loadObjectList();
		}
		else 
		{
			return array();
		}
	}
	
	/**
	 * Get default project note
	 * 
	 * @param      string $group cn of project group
	 * @param      string $masterscope
	 * @return     void
	 */	
	public function getFirstNote( $group, $masterscope ) 
	{
		$query = "SELECT p.pagename FROM #__wiki_page AS p 
				  WHERE p.group_cn='" . $group . "' 
				  AND p.scope='" . $masterscope . "' 
				  ORDER BY p.times_rated, p.id ASC LIMIT 1";
				
		$this->_db->setQuery($query);				
		return $this->_db->loadResult();
	}
	
	/**
	 * Get a last note
	 * 
	 * @param      string $group cn of project group
	 * @param      string $scope
	 * @return     void
	 */	
	public function getLastNote( $group, $scope ) 
	{
		$query = "SELECT p.pagename, p.title, p.scope, p.times_rated 
		          FROM #__wiki_page AS p 
				  WHERE p.group_cn='" . $group . "' 
				  AND p.scope='" . $scope . "' 
				  ORDER BY p.times_rated DESC LIMIT 1";
				
		$this->_db->setQuery($query);				
		return $this->_db->loadResult();
	}
	
	/**
	 * Get last note order
	 * 
	 * @param      string $group cn of project group
	 * @param      string $scope
	 * @return     void
	 */	
	public function getLastNoteOrder( $group, $scope ) 
	{
		$query = "SELECT p.times_rated FROM #__wiki_page AS p 
				  WHERE p.group_cn='" . $group . "' 
				  AND p.scope='" . $scope . "' 
				  ORDER BY p.times_rated DESC LIMIT 1";
				
		$this->_db->setQuery($query);				
		return $this->_db->loadResult();
	}
	
	/**
	 * Save note order
	 * 
	 * @param      string $group cn of project group
	 * @param      string $scope
	 * @param      int $order
	 * @return     void
	 */	
	public function saveNoteOrder( $group, $scope, $order = 0 ) 
	{
		$query = "UPDATE #__wiki_page AS p SET p.times_rated='" . $order . "' 
				  WHERE p.group_cn='" . $group . "' 
				  AND p.scope='" . $scope . "' 
				  AND p.times_rated='0'";
				
		$this->_db->setQuery($query);				
		if (!$this->_db->query()) 
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Fix scope paths after page rename
	 * 
	 * @param      string $group cn of project group
	 * @param      string $scope
	 * @param      string $oldpagename
	 * @param      string $newpagename
	 * @return     void
	 */	
	public function fixScopePaths( $group, $scope, $oldpagename, $newpagename ) 
	{		
		$query = "UPDATE #__wiki_page AS p SET p.scope=replace(p.scope, '/" . $oldpagename . "', '/" . $newpagename . "')
				  WHERE p.group_cn='" . $group . "'";
				
		$this->_db->setQuery($query);				
		if (!$this->_db->query()) 
		{
			return false;
		}
		return true;		
	}
		
	/**
	 * Save wiki attachment
	 * 
	 * @param      string $page
	 * @param      string $file
	 * @param      int $uid
	 * @return     void
	 */	
	public function saveWikiAttachment( $page, $file, $uid )
	{
		// Create database entry
		$attachment 				= new WikiPageAttachment($this->_db);
		$attachment->pageid      	= $page->id;
		$attachment->filename    	= $file;
		$attachment->description 	= '';
		$attachment->created     	= date('Y-m-d H:i:s', time());
		$attachment->created_by  	= $uid;

		if (!$attachment->check()) 
		{
			$this->setError($attachment->getError());
		}
		if (!$attachment->store()) 
		{
			$this->setError($attachment->getError());
		}
	}
	
	/**
	 * Get suggestions for new project members
	 * 
	 * @param      object $project
	 * @param      string $option
	 * @param      int $uid
	 * @param      array $config
	 * @param      array $params
	 * @return     void
	 */	
	public function getSuggestions( $project, $option, $uid = 0, $config, $params ) 
	{	
		$suggestions = array();
		$creator = $project->created_by_user == $uid ? 1 : 0;
		$goto  = 'alias=' . $project->alias;
		
		// Adding a picture
		if ($creator && !$project->picture) 
		{
			$suggestions[] = array(
				'class' => 's-picture', 
				'text' => JText::_('COM_PROJECTS_WELCOME_ADD_THUMB'), 
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&task=edit')
			);
		}
		
		// Adding grant information
		if ($creator && $config->get('grantinfo') && !$params->get('grant_title', '')) 
		{
			$suggestions[] = array(
				'class' => 's-about', 
				'text' => JText::_('COM_PROJECTS_WELCOME_ADD_GRANT_INFO'), 
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&task=edit') . '?edit=settings'
			);
		}
		
		// Adding about text
		if ($creator && !$project->about && $project->private == 0) 
		{
			$suggestions[] = array(
				'class' => 's-about', 
				'text' => JText::_('COM_PROJECTS_WELCOME_ADD_ABOUT'), 
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&task=edit')
			);
		}
		
		// File upload
		if ($project->counts['files'] == 0 || (!$creator && $project->num_visits < 5) 
			|| ($creator && $project->num_visits < 5 &&  $project->counts['files'] == 0 ) ) 
		{
			$text = $creator ? JText::_('COM_PROJECTS_WELCOME_UPLOAD_FILES') : JText::_('COM_PROJECTS_WELCOME_SHARE_FILES');
			$suggestions[] = array(
				'class' => 's-files', 
				'text' => $text, 
				'url' =>  JRoute::_('index.php?option=' . $option . a . $goto . '&active=files')
			);
		}
		
		// Inviting others
		if ($project->counts['team'] == 1 && $project->role == 1) 
		{
			$suggestions[] = array(
				'class' => 's-team', 
				'text' => JText::_('COM_PROJECTS_WELCOME_INVITE_USERS'), 
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&task=edit').'?edit=team'
			);
		}
		
		// Todo items
		if ($project->counts['todo'] == 0 || (!$creator && $project->num_visits < 5) 
			|| ($creator && $project->num_visits < 5 &&  $project->counts['todo'] == 0 ) ) 
		{
			$suggestions[] = array(
				'class' => 's-todo', 
				'text' => JText::_('COM_PROJECTS_WELCOME_ADD_TODO'), 
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&active=todo')
			);
		}
		
		// Notes
		if ($project->counts['notes'] == 0 || (!$creator && $project->num_visits < 5) 
			|| ($creator && $project->num_visits < 5 &&  $project->counts['notes'] == 0 )  ) 
		{
			$suggestions[] = array(
				'class' => 's-notes', 
				'text' => JText::_('COM_PROJECTS_WELCOME_START_NOTE'), 
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&active=notes')
			);
		}
		return $suggestions;
	}
	
	/**
	 * Get project path
	 * 
	 * @param      string $projectAlias
	 * @param      string $webdir
	 * @param      boolean $offroot
	 * @param      string $case
	 * @return     void
	 */	
	public function getProjectPath( $projectAlias = '', $webdir = '', $offroot = 0, $case = 'files' ) 
	{		
		if (!$projectAlias || ! $webdir)
		{
			return false;
		}
		
		// Build upload path for project files
		$dir = strtolower($projectAlias);
		
		if (substr($webdir, 0, 1) != DS) 
		{
			$webdir = DS.$webdir;
		}
		if (substr($webdir, -1, 1) == DS) 
		{
			$webdir = substr($webdir, 0, (strlen($webdir) - 1));
		}
		$path  = $case ? $webdir . DS . $dir. DS . $case : $webdir . DS . $dir ;
		$path  = $offroot ? $path : JPATH_ROOT . $path;
		return $path;		
	}
	
	/**
	 * Send hub message
	 * 
	 * @param      string $option
	 * @param      array $config
	 * @param      object $project
	 * @param      array $addressees
	 * @param      string $subject
	 * @param      string $component
	 * @param      string $layout
	 * @param      string $message
	 * @param      string $reviewer
	 * @return     void
	 */	
	public function sendHUBMessage( 
		$option, $config, $project, 
		$addressees = array(), $subject = '', 
		$component = '', $layout = '', 
		$message = '', $reviewer = '')
	{
		if (!$layout || !$subject || !$component || empty($addressees))
		{
			return false;
		}
		
		// Is messaging turned on?
		if ($config->get('messaging') != 1)
		{
			return false;
		}
		
		// Set up email config
		$jconfig =& JFactory::getConfig();
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('COM_PROJECTS');
		$from['email'] = $jconfig->getValue('config.mailfrom');
		
		// Get message body
		$eview 					= new JView( array('name'=>'emails', 'layout'=> $layout ) );
		$eview->option 			= $option;
		$eview->hubShortName 	= $jconfig->getValue('config.sitename');
		$eview->project 		= $project;
		$eview->params 			= new JParameter( $project->params );
		$eview->config 			= $config;
		$eview->message			= $message;	
		$eview->reviewer		= $reviewer;				

		// Get profile of author group
		if ($project->owned_by_group) 
		{
			$eview->nativegroup = Hubzero_Group::getInstance( $project->owned_by_group );	
		}
		$body = $eview->loadTemplate();
		$body = str_replace("\n", "\r\n", $body);

		// Send HUB message
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger( 'onSendMessage', 
			array( 
				$component, 
				$subject,
			 	$body, 
				$from, 
				$addressees, 
				$option 
			)
		);		
	}
	
	/**
	 * Show Git info
	 * 
	 * @param      string $gitpath
	 * @param      string $path
	 * @param      string $fhash
	 * @param      string $file
	 * @param      int $showstatus
	 * @return     void
	 */	
	public function showGitInfo ($gitpath = '', $path = '', $fhash = '', $file = '', $showstatus = 1) 
	{
		if (!$gitpath || !$path || !$file) 
		{
			return false;
		}
		chdir($path);
		
		// Get all file revisions
		exec ($gitpath . ' log --diff-filter=AM --pretty=format:%H ' . escapeshellarg($file) . ' 2>&1', $out);
		
		$versions = array();
		$hashes = array();
		$added = array();
		$latest = array();

		if (count($out) > 0 && $out[0] != '') 
		{
			foreach ($out as $line) 
			{
				if (preg_match("/[a-zA-Z0-9]/", $line) && strlen($line) == 40) 
				{
					$hashes[]  = $line;
				}
			}
			// Start with oldest commit
			$hashes = array_reverse($hashes);
		}
		
		if (!empty($hashes)) 
		{
			$i = 0;
			foreach ($hashes as $hash) 
			{
				$out1 = array();
				$out2 = array();
							
				exec($gitpath . ' log --pretty=format:%cd ' . escapeshellarg($hash) . ' 2>&1', $out1);
				$arr = explode("\t", $out1[0]);
				$timestamp = strtotime($arr[0]);
				$versions[$i]['date'] =  date ('m/d/Y g:i A', $timestamp);
					
				exec($gitpath . ' log --pretty=format:%an ' . escapeshellarg($hash) . ' 2>&1', $out2);
				$arr = explode("\t", $out2[0]);
				$versions[$i]['author'] = $arr[0];
				$versions[$i]['num']    = $i+1;
				$versions[$i]['latest'] = ($i == (count($hashes) - 1)) ? 1 : 0;

				$versions[$i]['hash'] = $hash;
				if ($hash == $fhash) 
				{
					$versions[$i]['changes'] = count($hashes) - 1 - $i;
					$added = $versions[$i];					
				}
				if ($i == (count($hashes) - 1)) 
				{
					$latest = $versions[$i];
				}

				$i++;
			}
			
			if ($showstatus == 1) 
			{
				// Review stage
				$status = '';
				if (empty($added)) 
				{
					$added = $latest;
				}
				if (!empty($added)) 
				{
					$status .= 'Added at revision #'.$added['num'];
					if ($added['latest'] == 1) 
					{
						$status .= ' (latest) ';
					}
					else 
					{
						$status .= ' ('.$added['changes'].' new update(s)';
						$status .= ' - last update by '.$latest['author'].' on '.$latest['date'].')';
					}
				}
				return $status;
			}
			elseif ($showstatus == 2) 
			{
				// Get latest commit
				return $latest;
			}
			elseif ($showstatus == 3) 
			{
				// Get added commit
				return $added;
			}
			else 
			{
				return $versions;
			}
		}
	}	
}
