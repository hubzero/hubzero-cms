<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.	 If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package	  hubzero-cms
 * @author	  Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license	  http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Helpers;

use Exception;
use Google_Service_Drive_Permission;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_ParentReference;
use Google_Http_MediaFileUpload;
use Hubzero\Base\Object;

/**
 * Projects Google Drive helper class
 */
class Google extends Object
{
	/**
	 * Load file metadata
	 *
	 * @param   object  $apiService  Drive API service instance
	 * @param   string  $id          Remote id
	 * @return  mixed   array or false
	 */
	public static function loadFile($apiService, $id = '')
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
	 * Insert permission
	 *
	 * @param   object  $apiService  Drive API service instance
	 * @param   string  $id          Remote id
	 * @param   string  $title       File title
	 * @param   string  $parentId    Parent id
	 * @param   array   &$metadata   Collector array
	 * @return  mixed   string (id) or false
	 */
	public static function insertPermission($apiService, $fileId, $value, $type, $role)
	{
		$newPermission = new Google_Service_Drive_Permission();
		$newPermission->setValue($value);
		$newPermission->setType($type);
		$newPermission->setRole($role);

		try
		{
			return $apiService->permissions->insert($fileId, $newPermission);
		}
		catch (Exception $e)
		{
			echo 'An error occurred: ' . $e->getMessage();
		}
		return null;
	}

	/**
	 * Clear permission for user
	 *
	 * @param   object   $apiService  Drive API service instance
	 * @param   array    $shared
	 * @param   string   $itemId
	 * @return  boolean
	 */
	public static function clearPermissions($apiService, $shared = array(), $itemId)
	{
		if (!$itemId || empty($shared))
		{
			return false;
		}

		// Get current permissions
		$permlist = $apiService->permissions->listPermissions($itemId);

		// Collect permission names
		foreach ($permlist as $p)
		{
			$pName = $p->getDisplayName();
			if (!$pName)
			{
				continue;
			}

			$permissionId = $p->getId();

			// Go through array of connected users
			foreach ($shared as $name => $email)
			{
				if ($pName == $name)
				{
					try
					{
						$apiService->permissions->delete($itemId, $permissionId);
					}
					catch (Exception $e)
					{
						// error
					}
				}
			}
		}

		return true;
	}

	/**
	 * Patch file metadata (SYNC)
	 *
	 * @param   object  $apiService  Drive API service instance
	 * @param   string  $id          Remote id
	 * @param   string  $title       File title
	 * @param   string  $parentId    Parent id
	 * @param   array   &$metadata   Collector array
	 * @return  mixed   (id) or false
	 */
	public static function patchFile($apiService, $id = '', $title = '', $parentId = '', &$metadata)
	{
		// Check for what we need
		if (!$apiService || !$id || (!$title && !$parentId && !$convert))
		{
			return false;
		}

		// Create file instance
		$file = new Google_Service_Drive_DriveFile;

		if ($title)
		{
			$file->setName($title);
		}

		if ($parentId)
		{
			$file->setParents(array($parentId));
		}

		try
		{
			// Patch remote file
			$updatedFile = $apiService->files->update($id, $file);
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
	 * @param   object   $apiService  Drive API service instance
	 * @param   string   $title       File title
	 * @param   string   $data        File content
	 * @param   string   $mimeType    MIME type
	 * @param   string   $parentId    Parent id
	 * @param   array    &$metadata   Collector array
	 * @param   boolean  $convert     Convert for remote editing?
	 * @return  string   (id) or false
	 */
	public static function insertFile($apiService, $client, $title = '', $localPath = null, $mimeType = null, $parentId = 0, &$metadata, $convert = false)
	{
		// Check for what we need
		if (!$apiService || !$title || !$parentId || !file_exists($localPath) || !$mimeType)
		{
			return false;
		}

		// Create file instance
		$file = new Google_Service_Drive_DriveFile;
		$file->setMimeType($mimeType);
		$file->setName($title);
		$file->setParents(array($parentId));

		// Determine file size
		$size = filesize($localPath);

		$fparams = array();
		$fparams['mimeType'] = $mimeType;
		$fparams['uploadType'] = 'media';

		// Are we converting to Google format?
		if ($convert == true)
		{
			//$fparams['convert'] = true;
			$file->setMimeType(self::mimetypeToGoogle($mimeType));

			// OCR conversion
			if ($mimeType == 'application/pdf' || $mimeType == 'image/png' || $mimeType == 'image/jpeg' || $mimeType == 'image/gif')
			{
				$fparams['ocr'] = true;
			}
		}

		// For files below 5MB use standard upload method
		if ($size < 5000000 || $convert == true)
		{
			$fparams['data'] = file_get_contents($localPath);

			if (!$fparams['data'])
			{
				return false;
			}

			try
			{
				// Create remote file
				$createdFile = $apiService->files->create($file, $fparams);
				$metadata = $createdFile;
				return $createdFile['id'];
			}
			catch (Exception $e)
			{
				return false;
			}
		}

		// Use chunked upload for larger files
		try
		{
			$chunkSizeBytes = 1 * 1024 * 1024;

			// Call the API with the media upload, defer so it doesn't immediately return.
			$client->setDefer(true);
			$request = $apiService->files->create($file);

			$media = new Google_Http_MediaFileUpload(
				$client,
				$request,
				$mimeType,
				null,
				true,
				$chunkSizeBytes
			);

			$media->setFileSize($size);

			$status = false;
			$handle = fopen($localPath, "rb");
			while (!$status && !feof($handle))
			{
				$chunk  = fread($handle, $chunkSizeBytes);
				$status = $media->nextChunk($chunk);
			}

			$result = false;
			if ($status != false)
			{
				$result = $status;
			}

			fclose($handle);

			// Reset to the client to execute requests immediately in the future.
			$client->setDefer(false);

			$metadata = $result;
			return isset($result['id']) ? $result['id'] : null;
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	 * Update remote file with local change (SYNC)
	 *
	 * @param   object   $apiService  Drive API service instance
	 * @param   string   $id          Remote id
	 * @param   string   $title       File title
	 * @param   string   $data        File content
	 * @param   string   $mimeType    MIME type
	 * @param   string   $parentId    Parent id
	 * @param   array    &$metadata   Collector array
	 * @param   boolean  $convert     Convert for remote editing?
	 * @return  mixed  string (id) or false
	 */
	public static function updateFile($apiService, $client, $id = 0, $title = '', $localPath = null, $mimeType = null, $parentId = 0, &$metadata, $convert = false)
	{
		// Check for what we need
		if (!$apiService || !$id)
		{
			return false;
		}

		// Create file instance
		$file = new Google_Service_Drive_DriveFile;
		$file->setMimeType($mimeType);
		$file->setName($title);

		if ($parentId)
		{
			$file->setParents(array($parentId));
		}

		// Determine file size
		$size = filesize($localPath);

		$fparams = array();
		$fparams['mimeType'] = $mimeType;

		// Are we converting to Google format?
		if ($convert == true)
		{
			$fparams['convert'] = true;
		}

		// For files below 5MB use standard upload method
		if ($size < 5000000 || $convert == true)
		{
			$fparams['data'] = file_get_contents($localPath);

			if (!$fparams['data'])
			{
				return false;
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

		// Use chunked upload for larger files
		try
		{
			$chunkSizeBytes = 1 * 1024 * 1024;

			// Call the API with the media upload, defer so it doesn't immediately return.
			$client->setDefer(true);
			$request = $apiService->files->update($id, $file);

			$media = new Google_Http_MediaFileUpload(
				$client,
				$request,
				$mimeType,
				null,
				true,
				$chunkSizeBytes
			);
			$media->setFileSize($size);

			$status = false;
			$handle = fopen($localPath, "rb");
			while (!$status && !feof($handle))
			{
				$chunk  = fread($handle, $chunkSizeBytes);
				$status = $media->nextChunk($chunk);
			}

			$result = false;
			if ($status != false)
			{
				$result = $status;
			}

			fclose($handle);

			// Reset to the client to execute requests immediately in the future.
			$client->setDefer(false);

			$metadata = $result;
			return isset($result['id']) ? $result['id'] : null;
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	 * Untrash remote item
	 *
	 * @param   object   $apiService  Drive API service instance
	 * @param   string   $id          Remote id
	 * @return  boolean
	 */
	public static function untrashItem($apiService, $id = 0)
	{
		// Check for what we need
		if (!$apiService || !$id)
		{
			return false;
		}

		try
		{
			$file = $apiService->files->get($id);
			$success = $apiService->files->update($id, $file, array('trash' => false));
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	 * Delete parent
	 *
	 * @param   object   $apiService  Drive API service instance
	 * @param   string   $id          Remote id
	 * @return  boolean
	 */
	public static function deleteParent($apiService, $id = 0, $folderId = 0)
	{
		// Check for what we need
		if (!$apiService || !$id || !$folderId)
		{
			return false;
		}

		// Removing parent ID from file so that the file gets removed from project folder
		try
		{
			$apiService->parents->delete($id, $folderId);
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	 * Delete all parents
	 *
	 * @param   object   $apiService  Drive API service instance
	 * @param   string   $id          Remote id
	 * @return  boolean
	 */
	public static function deleteAllParents($apiService, $id = 0)
	{
		// Check for what we need
		if (!$apiService || !$id)
		{
			return false;
		}

		try
		{
			$parents = $apiService->parents->listParents($id);

			if (!empty($parents['items']))
			{
				foreach ($parents['items'] as $parent)
				{
					$folderId = $parent['id'];

					try
					{
						$apiService->parents->delete($id, $folderId);
						return true;
					}
					catch (Exception $e)
					{
						return false;
					}
				}
			}
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	 * Delete remote item
	 *
	 * @param   object   $apiService  Drive API service instance
	 * @param   string   $id          Remote id
	 * @param   boolean  $permanent   Delete permanently? (or trash)
	 * @return  boolean
	 */
	public static function deleteItem($apiService, $id = 0, $permanent = false)
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
				$file = $apiService->files->get($id);
				$success = $apiService->files->update($id, $file, array('trashed' => true));
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
	 * @param   object  $apiService  Drive API service instance
	 * @param   string  $title       Folder name
	 * @param   string  $parentId    Parent id
	 * @param   array   &$metadata   Collector array
	 * @return  mixed   string (new folder id) or false
	 */
	public static function createFolder($apiService, $title = '', $parentId = 0, &$metadata)
	{
		// Check for what we need
		if (!$apiService || !$title || !$parentId)
		{
			return false;
		}

		$file = new Google_Service_Drive_DriveFile;
		$file->setMimeType('application/vnd.google-apps.folder');
		$file->setName($title);

		if ($parentId != null)
		{
			$file->setParents(array($parentId));
		}

		try
		{
			$createdFolder = $apiService->files->create($file, array(
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
	 * @param   object  $apiService     Drive API service instance
	 * @param   string  $folderID       Folder ID
	 * @param   array   &$remotes       Collector array for active items
	 * @param   array   &$deletes       Collector array for deleted items
	 * @param   string  $path           Path
	 * @param   string  $startChangeId  Last Change ID
	 * @param   array   $connections    Array of local-remote connections
	 * @return  mixed   int (new change ID) or false
	 */
	public static function collectChanges($apiService, $folderID = 0, &$remotes, &$deletes, $path = '', $startChangeId = null, $connections = array())
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

		$newChangeID = null;

		// Get a list of files in remote folder
		try
		{
			$data = $apiService->changes->listChanges($parameters);

			if (!empty($data['items']))
			{
				self::getFolderChange($data['items'], $folderID, $remotes, $deletes, $path, $connections, $duplicates);
			}
			$newChangeID = $data['largestChangeId'];
		}
		catch (Exception $e)
		{
			return null;
		}

		return $newChangeID;
	}

	/**
	 * Get remote folder changes
	 *
	 * @param   array   $items        Remote items
	 * @param   string  $folderID     Folder ID
	 * @param   array   &$remotes     Collector array for active items
	 * @param   array   &$deletes     Collector array for deleted items
	 * @param   string  $path         Path
	 * @param   array   $connections  Array of local-remote connections
	 * @param   array   &$duplicates  Collector array for duplicates
	 * @return  void
	 */
	public static function getFolderChange($items, $folderID = 0, &$remotes, &$deletes, $path = '', $connections, &$duplicates)
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

					$status    = $doc['labels']['trashed'] ? 'D' : 'A';
					$converted = preg_match("/google-apps/", $doc['mimeType']) && !preg_match("/.folder/", $doc['mimeType']) ? 1 : 0;
					$url       = isset($doc['downloadUrl']) ? $doc['downloadUrl'] : '';
					$original  = isset($doc['originalFilename']) ? $doc['originalFilename'] : '';
					$time      = strtotime($doc['modifiedDate']);
					$thumb     = isset($doc['thumbnailLink']) ? $doc['thumbnailLink'] : null;

					$author    = isset($doc['lastModifyingUserName'])
								? utf8_encode($doc['lastModifyingUserName'])
								: utf8_encode($doc['ownerNames'][0]);

					if (!preg_match("/.folder/", $doc['mimeType']))
					{
						$title = Filesystem::clean($doc['title']);

						// Get file extention
						$ext = Filesystem::extension($title);

						if ($converted)
						{
							$g_ext = self::getGoogleConversionFormat($doc['mimeType'], false, true);
							if ($g_ext && $ext != $g_ext)
							{
								$title = $title . '.' . $g_ext;
							}
						}

						$type = 'file';
					}
					else
					{
						$title = Filesystem::cleanPath($doc['title']);
						$type = 'folder';
					}

					$fpath = $lpath ? $lpath . DS . $title : $title;

					$synced      = isset($conIds[$doc['id']]) ? $conIds[$doc['id']]['synced'] : null;
					$md5Checksum = isset($doc['md5Checksum']) ? $doc['md5Checksum'] : null;
					$fileSize    = isset($doc['fileSize']) ? $doc['fileSize'] : null;

					// Make sure path is not already used (Google allows files with same name in same dir, Git doesn't)
					$fpath = self::buildDuplicatePath($doc['id'], $fpath, $doc['mimeType'], $connections, $remotes, $duplicates);

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
						elseif ($oFilePath != $nFilePath)
						{
							$status = 'R';
							$rename = $oFilePath;
						}
					}

					$remotes[$fpath] = array(
						'status'     => $status,
						'time'       => $time,
						'modified'   => gmdate('Y-m-d H:i:s', $time),
						'type'       => $type,
						'local_path' => $fpath,
						'remoteid'   => $doc['id'],
						'title'      => $doc['title'],
						'converted'  => $converted,
						'rParent'    => self::getParentID($doc['parents']),
						'url'        => $url,
						'original'   => $original,
						'author'     => $author,
						'synced'     => $synced,
						'md5'        => $md5Checksum,
						'mimeType'   => $doc['mimeType'],
						'thumb'      => $thumb,
						'rename'     => $rename,
						'fileSize'   => $fileSize
					);

					if (preg_match("/.folder/", $doc['mimeType']))
					{
						// Recurse
						self::getFolderChange($items, $doc['id'], $remotes, $deletes, $fpath, $connections, $duplicates);
					}
				}
			}
		}
	}

	/**
	 * Get download URL
	 *
	 * @param   array   $resource  Remote resource
	 * @param   string  $ext       Export ext
	 * @return  string
	 */
	public static function getDownloadUrl($resource = array(), $ext = 'pdf')
	{
		$url = '';
		if (empty($resource))
		{
			return false;
		}
		if (isset($resource['exportLinks']))
		{
			$default_type = self::getGoogleExportType($ext);
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
	 * @param   string  $id           Remote ID
	 * @param   string  $fpath        File path
	 * @param   string  $format       mime type
	 * @param   array   $connections  Array of local-remote connections
	 * @param   array   &$remotes     Collector array for active items
	 * @param   array   &$duplicates  Collector array for duplicates
	 * @return  string
	 */
	public static function buildDuplicatePath($id = 0, $fpath, $format = '', $connections, &$remotes, &$duplicates)
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
			$appended = \Components\Projects\Helpers\Html::getAppendedNumber($fpath);
			$num = $appended ? $appended + 1 : 1;

			if ($appended)
			{
				$fpath = \Components\Projects\Helpers\Html::cleanFileNum($fpath, $appended);
			}

			$fpath = \Components\Projects\Helpers\Html::fixFileName($fpath, '-' . $num);

			// Check that new path isn't used either
			return self::buildDuplicatePath($id, $fpath, $format, $connections, $remotes, $duplicates);
		}

		return $fpath;
	}

	/**
	 * Get folders
	 *
	 * @param   object  $apiService      Drive API service instance
	 * @param   string  $folderID        Folder ID
	 * @param   array   &$remoteFolders  Collector array for remote folders
	 * @param   string  $path            Path
	 * @return  mixed
	 */
	public static function getFolders($apiService, $folderID = 0, &$remoteFolders, $path = '')
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
			'q' => $q//,
			//'fields' => 'items(id,title,labels/trashed,parents/id)'
		);

		// Get a list of files in remote folder
		try
		{
			$data = $apiService->files->listFiles($parameters);

			$items = $items = $data->getFiles();
			if (!empty($items))
			{
				$lpath = $path ? $path : '';
				foreach ($items as $item)
				{
					// Skip deleted items
					if ($item->getTrashed())
					{
						continue;
					}

					$title  = Filesystem::cleanPath($item->getName());
					$fpath  = $lpath ? $lpath . DS . $title : $title;
					$status = $item->getTrashed() ? 'D' : 'A';

					$remoteFolders[$fpath] = array(
						'remoteid' => $item->getId(),
						'status'   => $status,
						'rParent'  => self::getParentID($item->getParents())
					);

					// Recurse
					self::getFolders($apiService, $item->getId(), $remoteFolders, $fpath);
				}
			}

			/*
			$data = $apiService->files->listFiles($parameters);
			if (\User::get('username') == 'zooley')
			{
				var_dump($data->getFiles()); die();
			}
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

					$title  = Filesystem::cleanPath($item['title']);
					$fpath  = $lpath ? $lpath . DS . $title : $title;
					$status = $item['labels']['trashed'] ? 'D' : 'A';

					$remoteFolders[$fpath] = array(
						'remoteid' => $item['id'],
						'status'   => $status,
						'rParent'  => self::getParentID($item['parents'])
					);

					// Recurse
					self::getFolders($apiService, $item['id'], $remoteFolders, $fpath);
				}
			}
			*/
		}
		catch (Exception $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Get remote folder content
	 *
	 * @param   array  $parents
	 * @return  mixed  string or false
	 */
	public static function getParentID($parents = array())
	{
		if (\User::get('username') == 'zooley')
		{
			if (!empty($parents))
			{
				return $parents[0]->getId();
			}
		}
		else
		{
		if (!empty($parents))
		{
			return $parents[0]['id'];
		}
	}
		return null;
	}

	/**
	 * Get remote folder content
	 *
	 * @param   object  $apiService   Drive API service instance
	 * @param   string  $folderID     Folder ID
	 * @param   array   $remotes      Array of remote items
	 * @param   string  $path         Path
	 * @param   array   $connections  Array of local-remote connections
	 * @param   array   &$duplicates  Collector array for duplicates
	 * @return  mixed
	 */
	public static function getFolderContent($apiService, $folderID = 0, $remotes, $path = '', $since, $connections, &$duplicates)
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
			'q' => $q//,
			//'fields' => 'items(id,title,mimeType,downloadUrl,md5Checksum,labels,fileSize,thumbnailLink,modifiedDate,parents/id,originalFilename,lastModifyingUserName,ownerNames)'
		);

		// Get a list of files in remote folder
		try
		{
			$data = $apiService->files->listFiles($parameters);
			$items = $data->getFiles();
			if (!empty($items))
			{
				$lpath = $path ? $path : '';
				foreach ($items as $item)
				{
					$time   = strtotime($item->getModifiedTime());
					$status = $item->getTrashed() ? 'D' : 'A';
					$skip   = 0;

					// Check against modified date
					$changed = (strtotime(date("c", strtotime($item->getModifiedTime()))) - strtotime($since));
					if ($since && $changed <= 0 && $item->getTrashed() != 1)
					{
						$skip = 1;
					}

					$converted = preg_match("/google-apps/", $item->getMimeType()) && !preg_match("/.folder/", $item->getMimeType()) ? 1 : 0;
					$url       = $item->getWebContentLink() ? $item->getWebContentLink() : $item->getWebViewLink();
					//$url = $url ?: 'https://www.googleapis.com/drive/v3/files/' . $item->getId() . '?alt=media';
					$original  = $item->getOriginalFilename();
					$thumb     = $item->getThumbnailLink();

					$author    = null; /*isset($item['lastModifyingUserName'])
											? utf8_encode($item['lastModifyingUserName'])
											: utf8_encode($item['ownerNames'][0]);*/

					if (!preg_match("/.folder/", $item->getMimeType()))
					{
						$title = Filesystem::clean($item->getName());

						if ($converted)
						{
							$ext = self::getGoogleConversionFormat($item->getMimeType(), false, true);
							if ($ext)
							{
								$title = $title . '.' . $ext;
							}
						}

						$type = 'file';
					}
					else
					{
						$title = Filesystem::cleanPath($item->getName());
						$type = 'folder';
					}

					$fpath = $lpath ? $lpath . DS . $title : $title;

					$synced      = isset($conIds[$item->getId()]) ? $conIds[$item->getId()]['synced'] : null;
					$md5Checksum = $item->getMd5Checksum();
					$fileSize    = $item->getSize();

					/// Make sure path is not already used (Google allows files with same name in same dir, Git doesn't)
					$fpath = self::buildDuplicatePath($item->getId(), $fpath, $item->getMimeType(), $connections, $remotes, $duplicates);

					// Detect a rename or move
					$rename = '';
					if (isset($conIds[$item->getId()]))
					{
						$oFilePath = $conIds[$item->getId()]['path'];
						$oDirPath  = $conIds[$item->getId()]['dirpath'];
						$nDirPath  = dirname($fpath) == '.' ? '' : dirname($fpath);
						$nFilePath = $fpath;

						if ($oDirPath != $nDirPath && $oFilePath != $nFilePath)
						{
							$status = 'W';
							$rename = $oFilePath;
						}
						elseif ($oFilePath != $nFilePath)
						{
							$status = 'R';
							$rename = $oFilePath;
						}
					}

					// Check that file was last synced after modified date
					// (important to pick up failed updates)
					if (isset($conIds[$item->getId()]))
					{
						if ($conIds[$item->getId()]['modified'] < gmdate('Y-m-d H:i:s', $time))
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
							'status'     => $status,
							'time'       => $time,
							'modified'   => gmdate('Y-m-d H:i:s', $time),
							'type'       => $type,
							'local_path' => $fpath,
							'remoteid'   => $item->getId(),
							'title'      => $item->getName(),
							'converted'  => $converted,
							'rParent'    => self::getParentID($item->getParents()),
							'url'        => $url,
							'original'   => $original,
							'author'     => $author,
							'synced'     => $synced,
							'md5'        => $md5Checksum,
							'mimeType'   => $item->getMimeType(),
							'thumb'      => $thumb,
							'rename'     => $rename,
							'fileSize'   => $fileSize
						);
					}

					if (preg_match("/.folder/", $item->getMimeType()))
					{
						// Recurse
						$remotes = self::getFolderContent($apiService, $item->getId(), $remotes, $fpath, $since, $connections, $duplicates);
					}
				}
			}

			/*$data = $apiService->files->listFiles($parameters);

			if (!empty($data['items']))
			{
				$lpath = $path ? $path : '';
				foreach ($data['items'] as $item)
				{
					$time   = strtotime($item['modifiedDate']);
					$status = $item['labels']['trashed'] ? 'D' : 'A';
					$skip   = 0;

					// Check against modified date
					$changed = (strtotime(date("c", strtotime($item['modifiedDate'])))	- strtotime($since));
					if ($since && $changed <= 0 && $item['labels']['trashed'] != 1)
					{
						$skip = 1;
					}

					$converted = preg_match("/google-apps/", $item['mimeType']) && !preg_match("/.folder/", $item['mimeType']) ? 1 : 0;
					$url       = isset($item['downloadUrl']) ? $item['downloadUrl'] : '';
					$original  = isset($item['originalFilename']) ? $item['originalFilename'] : '';
					$thumb     = isset($item['thumbnailLink']) ? $item['thumbnailLink'] : null;

					$author    = isset($item['lastModifyingUserName'])
											? utf8_encode($item['lastModifyingUserName'])
											: utf8_encode($item['ownerNames'][0]);

					if (!preg_match("/.folder/", $item['mimeType']))
					{
						$title = Filesystem::clean($item['title']);

						if ($converted)
						{
							$ext = self::getGoogleConversionFormat($item['mimeType'], false, true);
							if ($ext)
							{
								$title = $title . '.' . $ext;
							}
						}

						$type = 'file';
					}
					else
					{
						$title = Filesystem::cleanPath($item['title']);
						$type = 'folder';
					}

					$fpath = $lpath ? $lpath . DS . $title : $title;

					$synced      = isset($conIds[$item['id']]) ? $conIds[$item['id']]['synced'] : null;
					$md5Checksum = isset($item['md5Checksum']) ? $item['md5Checksum'] : null;
					$fileSize    = isset($item['fileSize']) ? $item['fileSize'] : null;

					/// Make sure path is not already used (Google allows files with same name in same dir, Git doesn't)
					$fpath = self::buildDuplicatePath($item['id'], $fpath, $item['mimeType'], $connections, $remotes, $duplicates);

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
						elseif ($oFilePath != $nFilePath)
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
							'status'     => $status,
							'time'       => $time,
							'modified'   => gmdate('Y-m-d H:i:s', $time),
							'type'       => $type,
							'local_path' => $fpath,
							'remoteid'   => $item['id'],
							'title'      => $item['title'],
							'converted'  => $converted,
							'rParent'    => self::getParentID($item['parents']),
							'url'        => $url,
							'original'   => $original,
							'author'     => $author,
							'synced'     => $synced,
							'md5'        => $md5Checksum,
							'mimeType'   => $item['mimeType'],
							'thumb'      => $thumb,
							'rename'     => $rename,
							'fileSize'   => $fileSize
						);
					}

					if (preg_match("/.folder/", $item['mimeType']))
					{
						// Recurse
						$remotes = self::getFolderContent($apiService, $item['id'], $remotes, $fpath, $since, $connections, $duplicates);
					}
				}
			}*/
		}
		catch (Exception $e)
		{
			return $remotes;
		}

		return $remotes;
	}

	/**
	 * Get Google export format(s)
	 *
	 * @param   string   $mimeType
	 * @param   boolean  $getAll
	 * @param   boolean  $getExt
	 * @param   boolean  $getPaired
	 * @param   string   $original_ext
	 * @return  mixed    string or array
	 */
	public static function getGoogleConversionFormat($mimeType = '', $getAll = false, $getExt = false, $getPaired = 0, $original_ext = '')
	{
		$formats = array();
		$ext = '';

		switch ($mimeType)
		{
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
					$exts = array('docx', 'pdf', 'html', 'txt', 'rtf', 'otd', 'tex');
					return array_combine($exts, $formats);
				}
				$ext = 'gdoc';
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
	 * @param   array   $remote
	 * @param   string  $importExt
	 * @return  string
	 */
	public static function getImportFilename($name = '', $importExt = '')
	{
		// Get file extention
		$parts = explode('.', $name);
		$ext   = count($parts) > 1 ? array_pop($parts) : '';

		// Strip all endings
		$ext   = count($parts) > 1 ? array_pop($parts) : '';
		$parts[] = $importExt;

		$result = implode('.', $parts);

		return $result;
	}

	/**
	 * Get file ext for import
	 *
	 * @param   string  $file
	 * @return  string
	 */
	public static function getImportExt($file = '')
	{
		$ext = '';

		// Get file extention
		$parts = explode('.', $file);
		$ext   = count($parts) > 1 ? array_pop($parts) : '';

		// Latest MS Office formats
		switch (strtolower($ext))
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
	 * @return  array
	 */
	public static function getGoogleConversionExts()
	{
		$formats = array(
			'doc',
			'docx',
			'html',
			'txt',
			'rtf',
			'xls',
			'xlsx',
			'ods',
			'csv',
			'tsv',
			'tab',
			'ppt',
			'pps',
			'pptx',
			'wmf',
			'jpg',
			'gif',
			'png',
			'pdf',
			'tex'
		);

		return $formats;
	}

	/**
	 * Get Google native formats
	 *
	 * @return  array
	 */
	public static function getGoogleNativeExts()
	{
		$formats = array(
			'gdoc',
			'gsheet',
			'gslides',
			'gdraw',
			'gform',
			'gtable',
			'gvi',
			'glink',
			'gvp'
		);

		return $formats;
	}

	/**
	 * Get Google import extension
	 *
	 * @param   string  $mimeType
	 * @return  string
	 */
	public static function getGoogleImportExt($mimeType = '')
	{
		$ext = 'pdf';

		switch ($mimeType)
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
	 * Mimetype to Google mimetype conversion
	 *
	 * @param   string  $mimeType
	 * @return  string
	 */
	public static function mimetypeToGoogle($mimeType = '')
	{
		$ext = $mimeType;

		switch ($mimeType)
		{
			// Documents
			case 'text/plain':
			case 'application/rtf':
			case 'application/msword':
			case 'application/x-rtf':
			case 'text/rtf':
			case 'text/richtext':
			case 'application/doc':
			case 'application/x-soffice':
			case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
				$ext = 'application/vnd.google-apps.document';
				break;

			// Slides
			case 'application/vnd.ms-powerpoint':
			case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
			case 'application/vnd.oasis.opendocument.presentation':
				$ext = 'application/vnd.google-apps.presentation';
				break;

			// Spreadsheets
			case 'text/x-comma-separated-values':
			case 'application/vnd.ms-excel':
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
			case 'application/vnd.oasis.opendocument.spreadsheet':
			case 'application/x-vnd.oasis.opendocument.spreadsheet':
				$ext = 'application/vnd.google-apps.spreadsheet';
				break;

			case 'image/png':
			case 'image/jpeg':
			case 'image/svg+xml':
				$ext = 'application/vnd.google-apps.drawing';
				break;
		}

		return $ext;
	}

	/**
	 * Get default Google export format
	 *
	 * @param   string  $ext
	 * @param   string  $type
	 * @return  string
	 */
	public static function getGoogleExportType($ext = 'pdf', $type = '')
	{
		switch (strtolower($ext))
		{
			case 'html':
				$type = 'text/html';
				break;

			case 'txt':
			case 'tex':
				$type = 'text/plain';
				break;

			case 'rtf':
				$type = 'application/rtf';
				break;

			case 'doc':
				$type = 'application/msword';
				break;

			case 'xlsx':
				$type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
				break;

			case 'docx':
				$type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
				break;

			case 'otd':
				$type = 'application/vnd.oasis.opendocument.text';
				break;

			case 'ods':
				$type = 'application/x-vnd.oasis.opendocument.spreadsheet';
				break;

			case 'jpeg':
				$type = 'image/jpeg';
				break;

			case 'png':
				$type = 'image/png';
				break;

			case 'svg':
				$type = 'image/svg+xml';
				break;

			case 'pptx':
				$type = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
				break;

			case 'pdf':
			default:
				$type = 'application/pdf';
				break;
		}

		return $type;
	}
}
