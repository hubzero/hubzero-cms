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

require_once( JPATH_ROOT . DS . 'plugins' . DS . 'projects' . DS . 'files' . DS . 'files.php');	

/**
 * Extension to Projects Files plugin for out-of-project use
 */
class plgProjectsFilesExternal extends plgProjectsFiles
{	
	/**
	 * Event call to manage project files outside of projects
	 * 
	 * @return     mixed
	 */
	public function onProjectExternal ( $identifier = NULL, $action = '', $uid = NULL, $case = 'files')
	{
		$arr = array(
			'project' => $identifier,
			'action'  => $action,
			'output'  => '',
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
		
		ximport('Hubzero_Content_Mimetypes');
		
		// Load language file
		$this->loadLanguage();
			
		$this->_database =& JFactory::getDBO();		
		$this->_uid = $uid;
		
		if (!$this->_uid) 
		{
			$juser =& JFactory::getUser();
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
		
		$this->_case 	= $case ? $case : 'files';
		$this->_option  = 'com_projects';
		
		// Include Git Helper
		$this->getGitHelper();
				
		// Get path
		$this->path = $this->getProjectPath();
		
		// Something is wrong
		if (!$this->path)
		{
			$arr['error']	= true;
			$arr['message'] = JText::_('PLG_PROJECTS_FILES_ERROR_REPO_NOT_FOUND');
			return $arr;
		}
		
		// Initialize Git
		$this->_git->iniGit($this->path);
		
		// Incoming
		$this->subdir 	= trim(urldecode(JRequest::getVar('subdir', '')), DS);
				
		// File actions			
		switch ($action) 
		{								
			case 'list':
			default:
				$arr['output'] = plgProjectsFilesExternal::getList();				
				break;
				
			case 'get':
				$arr['output'] = plgProjectsFilesExternal::getMetadata();				
				break;
			
			case 'insert':
				$arr['output'] = plgProjectsFilesExternal::insertFile();			
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
			$arr['message']  =  (isset($this->_msg) && $this->_msg)  ? $this->_msg : JText::_('PLG_PROJECTS_FILES_MESSAGE_SUCESS');
		}
		
		// Return data
		return $arr;		
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
		$sortby  = JRequest::getVar( 'sortby', 'name' ); 
		$sortdir = JRequest::getVar( 'sortdir', 'ASC' ); 
				
		// Get list of files from repo
		$docs 	 = $this->_git->getFiles($this->path, $this->subdir);
		$folders = $this->getFolders($this->path, $this->subdir, $this->prefix);
	
		$items 		= array();
		$sorting 	= array();
		
		if ($docs)
		{
			foreach ($docs as $file)
			{
				// Skip .gitignore
				if (basename($file) == '.gitignore')
				{
					continue;
				}
				
				$metadata = plgProjectsFilesExternal::getItemMetadata(trim($file));
				if ($metadata)
				{
					$items[] 	= $metadata;
					$sorting[] 	= strtolower($metadata->localPath);
				}
			}
		}
		
		if ($folders)
		{
			foreach ($folders as $folder)
			{
				$obj 				= new stdClass;
				$obj->type			= 'folder';
				$obj->name			= $folder;
				
				$items[] 			= $obj;
				$sorting[] 			= strtolower($folder);
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
		$dataUrl  = JRequest::getVar( 'dataUrl', '' ); // path to local file to copy from		
		$results  = array();
		$assets   = array();
		
		// Via local copy
		if ($dataUrl && is_file($dataUrl))
		{
			// TBD
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
					$file = JFile::makeSafe($file);
					$assets[] = $this->subdir ? $this->subdir . DS . $file : $file;
				}
			}

			// Perform upload
			$this->save();

			// After upload actions	
			$this->getUploadStatus();			
		}
				
		// On success return uploaded file metadata
		if (!$this->getError()) 
		{
			return plgProjectsFilesExternal::getMetadata( $assets);			
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
	
		// Incoming
		$checked = $checked ? $checked : JRequest::getVar( 'asset', '', 'request', 'array' ); 
		
		if (empty($checked))
		{
			$this->setError(JText::_('PLG_PROJECTS_FILES_ERROR_NO_FILES_SELECTED'));
			return false;
		}
				
		$files = array();
		
		// Go through files and collect metadata
		foreach ($checked as $file)
		{
			$metadata = plgProjectsFilesExternal::getItemMetadata(trim($file));
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
	 * Get file metadata
	 * 
	 * @return     mixed
	 */
	public function getItemMetadata($file = '', $hash = '')
	{
		$file = trim($file) ? $file : JRequest::getVar( 'file', '' );
		$hash = trim($hash) ? $hash : JRequest::getVar( 'hash', '' );
		
		if ($file == '')
		{
			return false;
		}
		
		// Required
		$mt = new Hubzero_Content_Mimetypes();
			
		// Build file object
		$obj 				= new stdClass;
		$obj->type			= 'file';
		$obj->name			= basename($file);
		$obj->localPath		= $this->subdir ? $this->subdir . DS . $file : $file;
		$obj->fullPath		= $this->prefix . $this->path . DS . $file;
		
		if (!$hash && !file_exists($obj->fullPath) )
		{
			return false;
		}
		if ($hash)
		{
			$obj->size 		= $this->_git->gitLog($this->path, $obj->localPath, $hash, 'size');
		}
		else
		{
			$obj->size		= filesize($obj->fullPath);
		}
		
		$obj->ext			= end(explode('.', $file));

		$gitData 			= $this->_git->gitLog($this->path, $obj->localPath, $hash, 'combined');
		
		if (!$gitData)
		{
			return false;
		}
		$obj->date			= isset($gitData['date']) ? $gitData['date'] : NULL;
		$obj->author 		= isset($gitData['author']) ? $gitData['author'] : NULL;
		$obj->email 		= isset($gitData['email']) ? $gitData['email'] : NULL;
		$obj->md5			= hash_file('md5', $obj->fullPath);
		$obj->commitHash 	= $hash ? $hash : $this->_git->gitLog($this->path, $obj->localPath, '', 'hash');	

		return $obj;
	}	
}
