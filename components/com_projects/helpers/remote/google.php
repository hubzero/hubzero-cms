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
 * Projects Google Drive helper class
 */
class ProjectsGoogleHelper extends JObject 
{				
	/**
	 * Load file metadata 
	 * 
	 * @param    Google_DriveService  	$apiService 	Drive API service instance
	 * @param    string					$id				Remote id
	 *
	 * @return   array or false
	 */
	public function loadFile ($apiService, $id = '') 
	{
		// Check for what we need
		if (!$apiService || !$id)
		{
			return false;
		}
		
		try
		{
			// Patch remote file
		 	$resource = $apiService->files->get($id);
			return $resource;
		}
		catch (Exception $e)
		{
			return false;
		}		
	}
	
	/**
	 * Patch file metadata (SYNC)
	 * 
	 * @param    Google_DriveService  	$apiService 	Drive API service instance
	 * @param    string					$id				Remote id
	 * @param    string					$title			File title
	 * @param    string					$parentId		Parent id
	 * @param    array					&$metadata		Collector array
	 *
	 * @return   string (id) or false
	 */
	public function patchFile ($apiService, $id = '', $title = '', $parentId = '', &$metadata) 
	{
		// Check for what we need
		if (!$apiService || !$id || (!$title && !$parentId && !$convert))
		{
			return false;
		}
		
		// Create file instance
		$file = new Google_DriveFile;
				
		if ($title)
		{
			$file->setTitle($title);
		}
		
		if ($parentId)
		{
			$parent = new Google_ParentReference;
			$parent->setId($parentId);
			$file->setParents(array($parent));
		}
				
		try
		{
			// Patch remote file
		 	$updatedFile = $apiService->files->patch($id, $file);
			$metadata = $updatedFile;
			return $updatedFile['id'];
		}
		catch (Exception $e)
		{
			return false;
		}	
	}
	
	/**
	 * Insert new file in remote (SYNC)
	 * 
	 * @param    Google_DriveService  	$apiService 	Drive API service instance
	 * @param    string					$title			File title
	 * @param    string					$data			File content
	 * @param    string					$mimeType		MIME type
	 * @param    string					$parentId		Parent id
	 * @param    array					&$metadata		Collector array
	 * @param    boolean				$convert		Convert for remote editing?
	 *
	 * @return   string (id) or false
	 */
	public function insertFile ($apiService, $title = '', $data = NULL, $mimeType = NULL, $parentId = 0, &$metadata, $convert = false) 
	{
		// Check for what we need
		if (!$apiService || !$title || !$parentId || !$data || !$mimeType)
		{
			return false;
		}
		
		// Create file instance
		$file = new Google_DriveFile;
		$file->setMimeType($mimeType);
		$file->setTitle($title);
		
		$parent = new Google_ParentReference;
		$parent->setId($parentId);
		$file->setParents(array($parent));
		
		$fparams = array();
		$fparams['mimeType'] = $mimeType;
		$fparams['data'] = $data;
		
		// Are we converting to Google format?
		if ($convert == true) 
		{			
			$fparams['convert'] = true;
			
			// OCR conversion
			if ($mimeType == 'application/pdf' || $mimeType == 'image/png' 
				|| $mimeType == 'image/jpeg' || $mimeType == 'image/gif' )
			{
				$fparams['ocr'] = true;
			}
		}
		
		try
		{
			// Create remote file
		 	$createdFile = $apiService->files->insert($file, $fparams);
			$metadata = $createdFile;
			return $createdFile['id'];
		}
		catch (Exception $e)
		{
			return false;
		}		
	}
	
	/**
	 * Update remote file with local change (SYNC)
	 * 
	 * @param    Google_DriveService  	$apiService 	Drive API service instance
	 * @param    string					$id				Remote id
	 * @param    string					$title			File title
	 * @param    string					$data			File content
	 * @param    string					$mimeType		MIME type
	 * @param    string					$parentId		Parent id
	 * @param    array					&$metadata		Collector array
	 * @param    boolean				$convert		Convert for remote editing?
	 *
	 * @return   string (id) or false
	 */
	public function updateFile ($apiService, $id = 0, $title = '', $data = NULL, $mimeType = NULL, $parentId = 0, &$metadata, $convert = false) 
	{
		// Check for what we need
		if (!$apiService || !$id)
		{
			return false;
		}
		
		// Create file instance
		$file = new Google_DriveFile;
		$file->setMimeType($mimeType);
		$file->setTitle($title);
		
		if ($parentId)
		{
			$parent = new Google_ParentReference;
			$parent->setId($parentId);
			$file->setParents(array($parent));
		}
		
		$fparams = array();
		$fparams['mimeType'] = $mimeType;
		$fparams['data'] = $data;
		
		// Are we converting to Google format?
		if ($convert == true) 
		{			
			$fparams['convert'] = true;
		}
		
		try
		{
			// Update remote file
		 	$createdFile = $apiService->files->update($id, $file, $fparams);
			$metadata = $createdFile;
			return $createdFile['id'];
		}
		catch (Exception $e)
		{
			return false;
		}		
	}
	
	/**
	 * Untrash remote item
	 * 
	 * @param    Google_DriveService  	$apiService 	Drive API service instance
	 * @param    string					$id				Remote id
	 * @return   boolean
	 */
	public function untrashItem ($apiService, $id = 0) 
	{
		// Check for what we need
		if (!$apiService || !$id)
		{
			return false;
		}
		
		try 
		{
			$success = $apiService->files->untrash($id);
			return true;
		}
		catch (Exception $e) 
		{
	    	return false;
	  	}	
	}
	
	/**
	 * Delete remote item
	 *
	 * @param    Google_DriveService  	$apiService 	Drive API service instance
	 * @param    string					$id				Remote id
	 * @param    boolean				$permanent		Delete permanently? (or trash) 
	 *
	 * @return   void
	 */
	public function deleteItem ($apiService, $id = 0, $permanent = false) 
	{
		// Check for what we need
		if (!$apiService || !$id)
		{
			return false;
		}
		
		try 
		{
			if ($permanent == true)
			{
				$success = $apiService->files->delete($id);
			}
			else
			{
				$success = $apiService->files->trash($id);
			}
			return true;
		}
		catch (Exception $e) 
		{
	    	return false;
	  	}		
	}
	
	/**
	 * Create remote folder
	 * 
	 * @param    Google_DriveService  	$apiService 	Drive API service instance
	 * @param    string					$title			Folder name
	 * @param    string					$parentId		Parent id
	 * @param    array					&$metadata		Collector array 
	 *
	 * @return   string (new folder id) or false
	 */
	public function createFolder ($apiService, $title = '', $parentId = 0, &$metadata) 
	{				
		// Check for what we need
		if (!$apiService || !$title || !$parentId)
		{
			return false;
		}
		
		$file = new Google_DriveFile;
		$file->setMimeType('application/vnd.google-apps.folder');
		$file->setTitle($title);
		
		if ($parentId != null) 
		{
		    $parent = new Google_ParentReference;
		    $parent->setId($parentId);
		    $file->setParents(array($parent));
		}		
		
		try
		{
			$createdFolder = $apiService->files->insert($file, array(
			      'mimeType' => 'application/vnd.google-apps.folder'
			));
			
			$metadata = $createdFolder;
			
			return $createdFolder['id'];
		}
		catch (Exception $e)
		{
			return false;
		}						
	}
	
	/**
	 * Get and sort through remote changes since last sync
	 * 
	 * @param      Google_DriveService  $apiService 	Drive API service instance
	 * @param      string				$folderID		Folder ID
	 * @param      array				&$remotes		Collector array for active items
	 * @param      array				&$deletes		Collector array for deleted items
	 * @param      string				$path			Path
	 * @param      string				$startChangeId	Last Change ID
	 * @param      array				$connections	Array of local-remote connections
	 *
	 * @return   int (new change ID) or false
	 */
	public function collectChanges ($apiService, $folderID = 0, &$remotes, &$deletes, $path = '', $startChangeId = NULL, $connections = array()) 
	{				
		// Check for what we need
		if (!$apiService || !$folderID)
		{
			return false;
		}
		
		// Collect remote items with duplicate names
		$duplicates = array();
		
		// Params for API call
		$parameters = array();
		
		if ($startChangeId)
		{
			$parameters['startChangeId'] = $startChangeId;
		}
		
		$newChangeID = NULL;
				
		// Get a list of files in remote folder
		try
		{			
			$data = $apiService->changes->listChanges($parameters);
			
			if (!empty($data['items']))
			{
				ProjectsGoogleHelper::getFolderChange($data['items'], $folderID, $remotes, $deletes, $path, $connections, $duplicates);	
			}
			$newChangeID = $data['largestChangeId'];	
		}
		catch (Exception $e)
		{
			$this->setError('Failed to retrieve remote content');
		}
		
		return $newChangeID;
	}
	
	/**
	 * Get remote folder changes
	 * 
	 * @param      array  		$items 			Remote items
	 * @param      string		$folderID		Folder ID
	 * @param      array		&$remotes		Collector array for active items
	 * @param      array		&$deletes		Collector array for deleted items
	 * @param      string		$path			Path
	 * @param      array		$connections	Array of local-remote connections
	 * @param      array		&$duplicates	Collector array for duplicates
	 *
	 * @return   void
	 */
	public function getFolderChange ($items, $folderID = 0, &$remotes, &$deletes, $path = '', $connections, &$duplicates)
	{
		$lpath = $path ? $path : '';
		
		$conIds   = $connections['ids'];
		$conPaths = $connections['paths'];
		
		// Get all changes in a folder
		foreach ($items as $item)
		{															
			if ($item['deleted'] && $item['fileId'])
			{
				$deletes[] = $item['fileId'];
			}			
			elseif (!$item['deleted'] && $item['file'])
			{
				$doc = $item['file'];
									
				if ($doc['kind'] != 'drive#file')
				{
					continue;
				}
				if (empty($doc['parents']))
				{
					continue;
				}
				
				foreach ($doc['parents'] as $parent)
				{
					if ($parent['id'] != $folderID)
					{
						continue;
					}
					
					$status 	= $doc['labels']['trashed'] ? 'D' : 'A';
					$converted 	= preg_match("/google-apps/", $doc['mimeType']) && !preg_match("/.folder/", $doc['mimeType']) ? 1 : 0;
					$url		= isset($doc['downloadUrl']) ? $doc['downloadUrl'] : '';
					$original	= isset($doc['originalFilename']) ? $doc['originalFilename'] : '';
					$time 		= strtotime($doc['modifiedDate']);
					$thumb		= isset($doc['thumbnailLink']) ? $doc['thumbnailLink'] : NULL;
					
					$author		= isset($doc['lastModifyingUserName']) 
											? utf8_encode($doc['lastModifyingUserName']) 
											: utf8_encode($doc['ownerNames'][0]);			
				
					if (!preg_match("/.folder/", $doc['mimeType']))
					{
						$title = JFile::makeSafe($doc['title']);
						
						// Get file extention
						$ext = explode('.', $title);
						$ext = count($ext) > 1 ? end($ext) : '';
						
						if ($converted)
						{
							$g_ext = ProjectsGoogleHelper::getGoogleConversionFormat($doc['mimeType'], false, true);
							if ($g_ext && $ext != $g_ext)
							{
								$title = $title . '.' . $g_ext;
							}
						}
						
						$type = 'file';
					}
					else
					{
						$title = JFolder::makeSafe($doc['title']);
						$type = 'folder';
					}

					$fpath = $lpath ? $lpath . DS . $title : $title;
						
					$synced			= isset($conIds[$doc['id']]) ? $conIds[$doc['id']]['synced'] : NULL;
					$md5Checksum 	= isset($doc['md5Checksum']) ? $doc['md5Checksum'] : NULL;
					$fileSize 		= isset($doc['fileSize']) ? $doc['fileSize'] : NULL;
										
					// Make sure path is not already used (Google allows files with same name in same dir, Git doesn't)
					$fpath = ProjectsGoogleHelper::buildDuplicatePath($doc['id'], $fpath, $doc['mimeType'], $connections, $remotes, $duplicates);
					
					// Detect a rename or move
					$rename = '';
					if (isset($conIds[$doc['id']]))
					{
						$oFilePath = $conIds[$doc['id']]['path'];
						$oDirPath  = $conIds[$doc['id']]['dirpath'];
						$nDirPath  = dirname($fpath) == '.' ? '' : dirname($fpath);
						$nFilePath = $fpath;
						
						if ($oDirPath != $nDirPath && $oFilePath != $nFilePath)
						{
							$status = 'W';
							$rename = $oFilePath;
						}
						elseif ( $oFilePath != $nFilePath )
						{
							$status = 'R';
							$rename = $oFilePath;
						}
					}
												
					$remotes[$fpath] = array(
						'status' 	=> $status,
						'time' 	 	=> $time,
						'modified'	=> gmdate('Y-m-d H:i:s', $time),
						'type'   	=> $type,
						'local_path'=> $fpath,
						'remoteid' 	=> $doc['id'],
						'title'		=> $doc['title'],
						'converted' => $converted,
						'rParent'	=> ProjectsGoogleHelper::getParentID($doc['parents']),
						'url'		=> $url,
						'original' 	=> $original,
						'author'	=> $author,
						'synced'	=> $synced,
						'md5' 		=> $md5Checksum,
						'mimeType'	=> $doc['mimeType'],
						'thumb'		=> $thumb,
						'rename'	=> $rename,
						'fileSize'	=> $fileSize
					);
					
					if (preg_match("/.folder/", $doc['mimeType']))
					{
						// Recurse
						ProjectsGoogleHelper::getFolderChange($items, $doc['id'], $remotes,  $deletes, $fpath, $connections, $duplicates );
					}	
				}
			}
		}
	}
	
	/**
	 * Get download URL
	 * 
	 * @param      array  		$resource 	Remote resource
	 * @param      string		$ext		Export ext
	 * @return     url string
	 */
	public function getDownloadUrl($resource = array(), $ext = 'pdf') 
	{
		$url = '';
		if (empty($resource))
		{
			return false;
		}
		if (isset($resource['exportLinks']))
		{
			$default_type = ProjectsGoogleHelper::getGoogleExportType($ext);
			foreach ($resource['exportLinks'] as $type => $link)
			{
				if ($type == $default_type)
				{
					$url = $link;
				}
			}
		}
		if (isset($resource['downloadUrl']))
		{
			$url = $resource['downloadUrl'];
		}
		
		return $url;
		
	}
	
	/**
	 * Build local path for remote items with the same name
	 * 
	 * @param      string		$id				Remote ID
	 * @param      string		$fpath			File path
	 * @param      string		$format			mime type
	 * @param      array		$connections	Array of local-remote connections
	 * @param      array		&$remotes		Collector array for active items
	 * @param      array		&$duplicates	Collector array for duplicates
	 *
	 * @return   void
	 */
	public function buildDuplicatePath ($id = 0, $fpath, $format = '', $connections, &$remotes, &$duplicates) 
	{
		// Do we have a record with another ID linked to the same path?
		$pathTaken = isset($connections['paths'][$fpath]) 
					&& $connections['paths'][$fpath]['remote_id'] != $id
					&& $connections['paths'][$fpath]['format'] == $format  
					? true : false;
		
		// Deal with duplicate names
		if ((isset($remotes[$fpath]) && $remotes[$fpath]['mimeType'] == $format) || $pathTaken == true)
		{						
			if (isset($duplicates[$fpath]))
			{
				$duplicates[$fpath][] = $id;
			}
			else
			{
				$duplicates[$fpath] = array();
				$duplicates[$fpath][] = $id;
			}

			// Append duplicate count to file name
			$appended = ProjectsHtml::getAppendedNumber($fpath);
			$num = $appended ? $appended + 1 : 1;
			
			if ($appended)
			{
				$fpath = ProjectsHtml::cleanFileNum($fpath, $appended);
			}
						
			$fpath = ProjectsHtml::fixFileName($fpath, '-' . $num);
			
			// Check that new path isn't used either
			return ProjectsGoogleHelper::buildDuplicatePath($id, $fpath, $format, $connections, $remotes, $duplicates);						
		}
		else
		{
			return $fpath;
		}		
	}
	
	/**
	 * Get folders
	 * 
	 * @param      Google_DriveService  $apiService 	Drive API service instance
	 * @param      string				$folderID		Folder ID
	 * @param      array				&$remoteFolders	Collector array for remote folders
	 * @param      string				$path			Path
	 *
	 * @return   void
	 */
	public function getFolders ($apiService, $folderID = 0, &$remoteFolders, $path = '') 
	{
		// Check for what we need
		if (!$apiService || !$folderID)
		{
			return false;
		}
		
		// Search param
		$q = "'" . $folderID . "' in parents";
		$q .= " and mimeType = 'application/vnd.google-apps.folder' ";
				
		$parameters = array(
			'q' => $q,
			'fields' => 'items(id,title,labels/trashed,parents/id)'
		);
		
		// Get a list of files in remote folder
		try
		{
			$data = $apiService->files->listFiles($parameters);	
			if (!empty($data['items']))
			{
				$lpath = $path ? $path : '';
				foreach ($data['items'] as $item)
				{
					// Skip deleted items
					if ($item['labels']['trashed'])
					{
						continue;
					}
					
					$title = JFolder::makeSafe($item['title']);
					$fpath = $lpath ? $lpath . DS . $title : $title;
					$status = $item['labels']['trashed'] ? 'D' : 'A';
										
					$remoteFolders[$fpath] = array(
						'remoteid' => $item['id'], 
						'status' => $status,
						'rParent'=> ProjectsGoogleHelper::getParentID($item['parents'])
					);
					
					// Recurse
					ProjectsGoogleHelper::getFolders($apiService, $item['id'], $remoteFolders, $fpath);
				}
			}	
		}
		catch (Exception $e)
		{
			$this->setError('Failed to retrieve remote content');
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get remote folder content
	 * 
	 * @param      array $parents
	 *
	 * @return   string or false
	 */
	public function getParentID ($parents = array()) 
	{
		if (!empty($parents))
		{
			return $parents[0]['id'];
		}
		return NULL;
	}
	
	/**
	 * Get remote folder content
	 * 
	 * @param      Google_DriveService  $apiService 	Drive API service instance
	 * @param      string				$folderID		Folder ID
	 * @param      array				$remotes		Array of remote items
	 * @param      string				$path			Path
	 * @param      array				$connections	Array of local-remote connections
	 * @param      array				&$duplicates	Collector array for duplicates
	 *
	 * @return   void
	 */
	public function getFolderContent ($apiService, $folderID = 0, $remotes, $path = '', $since, $connections, &$duplicates) 
	{		
		// Check for what we need
		if (!$apiService || !$folderID)
		{
			return false;
		}
		
		$conIds   = $connections['ids'];
		$conPaths = $connections['paths'];
		
		// Search param
		$q = "'" . $folderID . "' in parents";
		
		$parameters = array(
			'q' => $q,
			'fields' => 'items(id,title,mimeType,downloadUrl,md5Checksum,labels/trashed,fileSize,thumbnailLink,modifiedDate,parents/id,originalFilename,lastModifyingUserName,ownerNames)'
		);
		
		// Get a list of files in remote folder
		try
		{
			$data = $apiService->files->listFiles($parameters);
			
			if (!empty($data['items']))
			{
				$lpath = $path ? $path : '';
				foreach ($data['items'] as $item)
				{					
					$time 		= strtotime($item['modifiedDate']);
					$status 	= $item['labels']['trashed'] ? 'D' : 'A';
					$skip 		= 0;
					
					// Check against modified date
					$changed = (strtotime(date("c", strtotime($item['modifiedDate'])))  - strtotime($since));
					if ($since && $changed <= 0 )
					{
						$skip = 1;
					}
										
					$converted 	= preg_match("/google-apps/", $item['mimeType']) && !preg_match("/.folder/", $item['mimeType']) ? 1 : 0;
					$url		= isset($item['downloadUrl']) ? $item['downloadUrl'] : '';
					$original	= isset($item['originalFilename']) ? $item['originalFilename'] : '';
					$thumb		= isset($item['thumbnailLink']) ? $item['thumbnailLink'] : NULL;
					
					$author		= isset($item['lastModifyingUserName']) 
											? utf8_encode($item['lastModifyingUserName']) 
											: utf8_encode($item['ownerNames'][0]);
					
					if (!preg_match("/.folder/", $item['mimeType']))
					{
						$title = JFile::makeSafe($item['title']);
						
						if ($converted)
						{
							$ext = ProjectsGoogleHelper::getGoogleConversionFormat($item['mimeType'], false, true);
							if ($ext)
							{
								$title = $title . '.' . $ext;
							}
						}
						
						$type = 'file';
					}
					else
					{
						$title = JFolder::makeSafe($item['title']);
						$type = 'folder';
					}

					$fpath = $lpath ? $lpath . DS . $title : $title;
						
					$synced		 = isset($conIds[$item['id']]) ? $conIds[$item['id']]['synced'] : NULL;
					$md5Checksum = isset($item['md5Checksum']) ? $item['md5Checksum'] : NULL;
					$fileSize	 = isset($item['fileSize']) ? $item['fileSize'] : NULL;
					
					/// Make sure path is not already used (Google allows files with same name in same dir, Git doesn't)
					$fpath = ProjectsGoogleHelper::buildDuplicatePath($item['id'], $fpath, $item['mimeType'], $connections, $remotes, $duplicates);
					
					// Detect a rename or move
					$rename = '';
					if (isset($conIds[$item['id']]))
					{
						$oFilePath = $conIds[$item['id']]['path'];
						$oDirPath  = $conIds[$item['id']]['dirpath'];
						$nDirPath  = dirname($fpath) == '.' ? '' : dirname($fpath);
						$nFilePath = $fpath;
						
						if ($oDirPath != $nDirPath && $oFilePath != $nFilePath)
						{
							$status = 'W';
							$rename = $oFilePath;
						}
						elseif ( $oFilePath != $nFilePath )
						{
							$status = 'R';
							$rename = $oFilePath;
						}
					}
					
					// Check that file was last synced after modified date	
					// (important to pick up failed updates)
					if (isset($conIds[$item['id']]))
					{
						if ($conIds[$item['id']]['modified'] < gmdate('Y-m-d H:i:s', $time))
						{
							$skip = 0;
						}
					}	
					elseif ($status == 'A')
					{
						// Never skip new files
						$skip = 0;
					}		
					
					if (!$skip)
					{
						$remotes[$fpath] = array(
							'status' 	=> $status,
							'time' 	 	=> $time,
							'modified'	=> gmdate('Y-m-d H:i:s', $time),
							'type'   	=> $type,
							'local_path'=> $fpath,
							'remoteid' 	=> $item['id'],
							'title'		=> $item['title'],
							'converted' => $converted,
							'rParent'	=> ProjectsGoogleHelper::getParentID($item['parents']),
							'url'		=> $url,
							'original' 	=> $original,
							'author'	=> $author,
							'synced'	=> $synced,
							'md5' 		=> $md5Checksum,
							'mimeType'	=> $item['mimeType'],
							'thumb'		=> $thumb,
							'rename'	=> $rename,
							'fileSize'	=> $fileSize
						);
					}	
										
					if (preg_match("/.folder/", $item['mimeType']))
					{
						// Recurse
						$remotes = ProjectsGoogleHelper::getFolderContent($apiService, $item['id'], 
							$remotes, $fpath, $since, $connections, $duplicates);
					}
				}					
			}
		}
		catch (Exception $e)
		{
			$this->setError('Failed to retrieve remote content');
			return $remotes;
			return false;
		}
		
		return $remotes;
	}
	
	/**
	 * Get Google export format(s)
	 * 
	 * @param      string	$mimeType
	 * @param      boolean	$getAll
	 * @param      boolean	$getExt
	 * @param      boolean	$getPaired
	 * @param      string	$original_ext
	 *
	 * @return     mixed, string or array
	 */
	public function getGoogleConversionFormat ($mimeType = '', $getAll = false, $getExt = false, $getPaired = 0, $original_ext = '') 
	{
		$formats = array();
		$ext = '';
		
		switch ( $mimeType ) 
		{				
			case 'application/vnd.google-apps.document':
			default:				
				$formats = array('MS Word document', 'PDF', 'HTML', 'Plain text', 'Rich text', 'Open Office doc', 'LaTeX');
				
				// LaTeX files get special treatment, hey!
				if ($original_ext == 'tex' && $getPaired)
				{
					return array('tex' => 'LaTeX', 'pdf' => 'PDF');
				}
				
				if ($getPaired)
				{
					$exts 	= array('docx', 'pdf', 'html', 'txt', 'rtf', 'otd', 'tex');
					return array_combine($exts, $formats);
				}	
				$ext = 'gdoc';
				break;
				
			case 'application/vnd.google-apps.presentation':				
				$formats = array('MS PowerPoint', 'PDF');	
				
				if ($getPaired)
				{
					$exts = array('pptx', 'pdf');
					return array_combine($exts, $formats);
				}
				
				$ext = 'gslides';
				break;
				
			case 'application/vnd.google-apps.spreadsheet':	
			case 'application/vnd.google-apps.form':				
				$formats = array('MS Excel', 'Open Office sheet' , 'PDF');
				
				if ($getPaired)
				{
					$exts = array('xlsx', 'ods', 'pdf');
					return array_combine($exts, $formats);
				}
				
				$ext = 'gsheet';	
				break;
				
			case 'application/vnd.google-apps.drawing':				
				$formats = array('JPEG', 'PNG', 'SVG', 'PDF');	
				
				if ($getPaired)
				{
					$exts = array('jpeg', 'png', 'svg', 'pdf');
					return array_combine($exts, $formats);
				}
					
				$ext = 'gdraw';
				break;
		}
		
		if ($getExt == true)
		{
			return $ext;
		}
		
		if (empty($formats))
		{
			$formats[0] = 'PDF';
		}
		
		return $getAll == true ? $formats : $formats[0];
	}
	
	/**
	 * Get file name for import
	 * 
	 * @param      array	$remote
	 * @param      string	$importExt
	 *
	 * @return     string
	 */
	public function getImportFilename ($remote = array(), $importExt = '') 
	{
		if (empty($remote))
		{
			return false;
		}
		
		$name = basename($remote['fpath']);
		
		// Get file extention
		$parts = explode('.', $name);
		$ext   = count($parts) > 1 ? array_pop($parts) : '';
		
		// Strip all endings
		$ext   = count($parts) > 1 ? array_pop($parts) : '';
		$parts[] = $importExt;
		
		$result = implode('.', $parts);	
		
		return $result ? $result : $remote['title'];	
	}
	
	/**
	 * Get file ext for import
	 * 
	 * @param      string	$file
	 *
	 * @return     string
	 */
	public function getImportExt ($file = '') 
	{
		$ext = '';
		
		// Get file extention
		$parts = explode('.', $file);
		$ext   = count($parts) > 1 ? array_pop($parts) : '';
		
		// Latest MS Office formats
		switch ( strtolower($ext) ) 
		{				
			case 'doc':
				$ext = 'docx';
				break;
			case 'xls':
				$ext = 'xlsx';
				break;
			case 'ppt':
				$ext = 'pptx';
				break;
			default:
				// Leave ext as is
				break;
		}
		
		return $ext;
	}
	
	/**
	 * Get all formats that Google can work with and convert
	 * 
	 * @return     array
	 */
	public function getGoogleConversionExts() 
	{
		$formats = array('doc', 'docx', 'html', 'txt', 'rtf',
			'xls', 'xlsx', 'ods', 'csv', 'tsv', 'tab', 
			'ppt', 'pps', 'pptx', 'wmf', 'jpg', 'gif', 'png', 'pdf', 'tex'
		);
		
		return $formats;
	} 
	
	/**
	 * Get Google native formats
	 * 
	 * @return     array
	 */
	public function getGoogleNativeExts() 
	{
		$formats = array('gdoc', 'gsheet', 'gslides', 
			'gdraw', 'gform', 'gtable', 'gvi', 'glink', 'gvp'
		);
		
		return $formats;
	}
	
	/**
	 * Get Google import extension
	 * 
	 * @param      string	$mimeType
	 *
	 * @return     string
	 */
	public function getGoogleImportExt ($mimeType = '') 
	{
		$ext = 'pdf';
		
		switch ( $mimeType ) 
		{				
			case 'application/vnd.google-apps.document':				
				$ext = 'docx';
				break;

			case 'application/vnd.google-apps.presentation':				
				$ext = 'pptx';
				break;

			case 'application/vnd.google-apps.spreadsheet':	
			case 'application/vnd.google-apps.form':				
				$ext = 'xlsx';	
				break;

			case 'application/vnd.google-apps.drawing':				
				$ext = 'jpeg';
				break;
		}
		
		return $ext;
	}
	
	/**
	 * Get default Google export format
	 * 
	 * @param      string	$ext	
	 * @param      string	$type
	 *
	 * @return     string
	 */
	public function getGoogleExportType ($ext = 'pdf', $type = '') 
	{
		switch ( strtolower($ext) ) 
		{				
			case 'pdf':
			default: 				
				$type 	= 'application/pdf';		
				break;
				
			case 'html': 				
				$type 	= 'text/html';		
				break;
				
			case 'txt':
			case 'tex': 				
				$type 	= 'text/plain';		
				break;
				
			case 'rtf': 				
				$type 	= 'application/rtf'; 		
				break;
				
			case 'doc':				
				$type 	= 'application/msword'; 		
				break;
								
			case 'xlsx': 				
				$type 	= 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'; 	
				break;
				
			case 'docx': 				
				$type 	= 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'; 		
				break;
				
			case 'otd': 				
				$type 	= 'application/vnd.oasis.opendocument.text'; 			
				break;
				
			case 'ods': 				
				$type 	= 'application/x-vnd.oasis.opendocument.spreadsheet';	 		
				break;
				
			case 'jpeg': 				
				$type 	= 'image/jpeg'; 		
				break;
				
			case 'png': 				
				$type 	= 'image/png'; 			
				break;
				
			case 'svg': 				
				$type 	= 'image/svg+xml'; 			
				break;
				
			case 'pptx': 				
				$type 	= 'application/vnd.openxmlformats-officedocument.presentationml.presentation'; 			
				break;
		}
	
		return $type;
	}
}
