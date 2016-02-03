<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Site\Controllers;

use Hubzero\User\Group;
use Hubzero\Utility;
use Filesystem;
use Component;
use Request;
use Route;
use User;
use Lang;
use App;

/**
 * Groups controller class
 */
class Media extends Base
{
	/**
	 * Override Execute Method
	 *
	 * @return 	void
	 */
	public function execute()
	{
		// disable default task
		$this->disableDefaultTask();

		// Check if they're logged in
		if (User::isGuest())
		{
			$this->loginTask(Lang::txt('COM_GROUPS_MEDIA_MUST_BE_LOGGED_IN'));
			return;
		}

		//get request vars
		$this->cn = Request::getVar('cn', '');

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->group = Group::getInstance($this->cn);

		// Ensure we found the group info
		if (!$this->group || !$this->group->get('gidNumber'))
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		// Check authorization
		//if ($this->_authorize() != 'manager' && !$this->_authorizedForTask('group.edit') && !$this->_authorizedForTask('group.pages'))
		if (!in_array(User::get('id'), $this->group->get('members')))
		{
			$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
		}

		//build path to the group folder
		$this->path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $this->group->get('gidNumber');

		//continue with parent execute method
		parent::execute();
	}


	/**
	 * Show File Browser method
	 *
	 * @return     array
	 */
	public function filebrowserTask()
	{
		// set the neeced layout
		$this->view->setLayout('filebrowser');

		//get rel path to start
		$this->view->activeFolder = Request::getVar('path', '/');

		// make sure we have an active folder
		if ($this->view->activeFolder == '')
		{
			$this->view->activeFolder = '/uploads';
		}

		$this->view->activeFolder = '/' . trim($this->view->activeFolder, '/');

		// regular groups can only access inside /uploads
		//if (!$this->group->isSuperGroup())
		//{
			$pathInfo = pathinfo($this->view->activeFolder);
			if ($pathInfo['dirname'] != '/uploads')
			{
				$this->view->activeFolder = '/uploads';
			}
		//}

		// make sure we have a path
		$this->_createGroupFolder($this->path);

		// get list of folders
		$folders = Filesystem::directoryTree($this->path, '.', 10);
		foreach ($folders as $i => $folder)
		{
			if ($folder['parent'] || (!$folder['parent'] && $folder['name'] == 'uploads'))
			{
				continue;
			}
			unset($folders[$i]);
		}

		// build recursive folder trees
		$folderTree             = $this->_buildFolderTree($folders);
		$this->view->folderTree = $this->_buildFolderTreeHtml($folderTree);
		$this->view->folderList = $this->_buildFolderTreeSelect($folderTree);

		// add base path if super group
		/*if ($this->group->isSuperGroup())
		{
			$this->view->folderTree = '<ul><li><a class="tree-folder-toggle" href="javascript:void(0);"></a><a data-folder="/" href="javascript:void(0);" class="tree-folder">/ (root)</a>'.$this->view->folderTree.'</li></ul>';
		}*/

		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$this->view->group         = $this->group;

		//display view
		$this->view->display();
	}

	/**
	 * Create group folder id doesnt exist
	 *
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	private function _createGroupFolder($path)
	{
		// create base group folder
		if (!Filesystem::exists($path))
		{
			Filesystem::makeDirectory($path);
		}

		// create uploads file
		if (!Filesystem::exists($path . DS . 'uploads'))
		{
			Filesystem::makeDirectory($path . DS . 'uploads');
		}
	}

	/**
	 * Build Folder tree based on path
	 *
	 * @param  [type]  $folders   [description]
	 * @param  integer $parent_id [description]
	 * @return [type]             [description]
	 */
	private function _buildFolderTree($folders, $parent_id = 0)
	{
		$branch = array();
		foreach ($folders as $folder)
		{
			if ($folder['parent'] == $parent_id)
			{
				$children = $this->_buildFolderTree($folders, $folder['id']);
				if ($children)
				{
					$folder['children'] = $children;
				}
				$branch[] = $folder;
			}
		}
		return $branch;
	}

	/**
	 * Build Folder tree in html ul list form
	 *
	 * @param  [type] $tree [description]
	 * @return [type]       [description]
	 */
	private function _buildFolderTreeHtml($tree)
	{
		//load group object
		$hubzeroGroup = Group::getInstance($this->cn);

		$base = substr(PATH_APP, strlen(PATH_ROOT));

		$html = '<ul>';
		foreach ($tree as $treeLevel)
		{
			$folder       = str_replace($base . '/site/groups/' . $hubzeroGroup->get('gidNumber'), '', $treeLevel['relname']);
			$nodeToggle   = '<span class="tree-folder-toggle-spacer"></span>';
			$childrenHtml = '';

			if (@is_array($treeLevel['children']))
			{
				$nodeToggle   = '<a class="tree-folder-toggle" href="javascript:void(0);"></a>';
				$childrenHtml = $this->_buildFolderTreeHtml($treeLevel['children']);
			}

			$html .= '<li>';
			$html .= $nodeToggle . '<a data-folder="'.$folder.'" href="javascript:void(0);" class="tree-folder">' . $treeLevel['name'].'</a>';
			$html .= $childrenHtml;
			$html .= '</li>';
		}
		$html .= '</ul>';
		return $html;
	}

	/**
	 * Build Folder tree in select list form
	 *
	 * @param  [type] $tree [description]
	 * @return [type]       [description]
	 */
	private function _buildFolderTreeSelect($tree)
	{
		// load group object
		$hubzeroGroup = Group::getInstance($this->cn);

		$html  = '<select class="" name="folder">';
		if ($hubzeroGroup->get('type') == 3)
		{
			$html .= '<option value="/">(root)</option>';
		}
		$html .= $this->_buildFolderTreeSelectOptionList($tree);
		$html .= '</select>';
		return $html;
	}

	/**
	 * Recursive function to create options for select list
	 *
	 * @param  [type] $tree [description]
	 * @return [type]       [description]
	 */
	private function _buildFolderTreeSelectOptionList($tree)
	{
		// load group object
		$hubzeroGroup = Group::getInstance($this->cn);

		$base = substr(PATH_APP, strlen(PATH_ROOT));

		$options = '';
		foreach ($tree as $treeLevel)
		{
			$value = str_replace($base . '/site/groups/' . $hubzeroGroup->get('gidNumber'), '', $treeLevel['relname']);
			$text  = str_repeat('&lfloor;', substr_count($value, '/'));
			$parts = explode('/', $value);
			$text .= ' ' . array_pop($parts);

			$options .= '<option value="'.$value.'">' . $text.'</option>';
			if (@is_array($treeLevel['children']))
			{
				$options .= $this->_buildFolderTreeSelectOptionList($treeLevel['children']);
			}
		}
		return $options;
	}

	/**
	 * List all group files
	 *
	 * @return  array
	 */
	public function listfilesTask()
	{
		// set the neeced layout
		$this->view->setLayout('filelist');

		//get request vars
		$this->view->folders = array();
		$this->view->files   = array();
		$this->view->type    = \Hubzero\Utility\Sanitize::paranoid(Request::getWord('type', ''));
		$this->view->relpath = Request::getVar('path', '/');

		// make sure we default to uploads folder for non-super groups
		if ($this->group->get('type') != 3 && (!$this->view->relpath || $this->view->relpath == '/'))
		{
			$this->view->relpath = '/uploads';
		}

		$this->view->relpath = \Hubzero\Filesystem\Util::normalizePath($this->view->relpath);
		$this->view->relpath = explode('/', $this->view->relpath);
		foreach ($this->view->relpath as $i => $p)
		{
			$this->view->relpath[$i] = preg_replace('/[^a-zA-Z0-9_\-]/', '', $p);
		}
		$this->view->relpath = implode(DS, $this->view->relpath);

		//build path to the group folder
		$this->path = rtrim($this->path, DS) . $this->view->relpath;

		// if we have a directory
		if (is_dir($this->path))
		{
			//get list of files
			$folders = Filesystem::directories($this->path, '.', false);
			$files   = Filesystem::files($this->path, '.', false);

			// filter by type
			if (isset($this->view->type) && $this->view->type != '')
			{
				foreach ($files as $k => $file)
				{
					$fileInfo = pathinfo($file);
					$ext      = strtolower($fileInfo['extension']);
					if ($this->view->type == 'images' && !in_array($ext, array('jpg','jpeg','png','gif','bmp','tiff')))
					{
						unset($files[$k]);
					}
					else if ($this->view->type == 'files' && in_array($ext, array('jpg','jpeg','png','gif','bmp','tiff')))
					{
						unset($files[$k]);
					}
				}
			}

			//reset array keys
			$this->view->folders = array_values($folders);
			$this->view->files   = array_values($files);
		}

		// pass vars to view
		//$this->view->config = $this->config;
		$this->view->group   = $this->group;
		$this->view->path    = $this->path;

		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

		//display view
		$this->view->display();
	}

	/**
	 * File upload - older browser support
	 *
	 * @return  array
	 */
	public function uploadTask()
	{
		// vars for later
		$message     = '';
		$messageType = 'error';

		// Incoming
		$listdir = Request::getInt('listdir', 0, 'post');

		// make sure user is not guest
		if (User::isGuest())
		{
			$message = Lang::txt('COM_GROUPS_MEDIA_MUST_BE_LOGGED_IN');
		}

		// make sure we have a directory to upload to
		if (!$listdir)
		{
			$message = Lang::txt('COM_GROUPS_ERROR_NO_ID');
		}

		// if dont have a message we are good to go
		if ($message == '')
		{
			// do upload
			$upload = $this->doUpload();

			// inform user
			$message     = $upload->message;
			$messageType = ($upload->error) ? 'error' : 'passed';
		}

		//push a success message
		$this->setNotification($message, $messageType);

		// Push through to the media view
		$this->filebrowserTask();
	}

	/**
	 * File upload - for CKeditor
	 *
	 * @return  array
	 */
	public function ckeditorUploadTask()
	{
		// vars for later
		$message = '';

		// get request vars
		$ckeditor     = Request::getVar('CKEditor', '', 'get');
		$ckeditorFunc = Request::getVar('CKEditorFuncNum', '', 'get');

		// if dont have a message we are good to go
		if ($message == '')
		{
			// do upload
			$upload = $this->doUpload();

			// get file name
			$file     = $upload->file;
			$fileInfo = pathinfo($file);

			//remvoe unnessary success message
			if (!$upload->error)
			{
				$upload->message = '';
			}

			// build url to return to ckeditor
			$url  = ($_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
			$url .= $_SERVER['HTTP_HOST'] . DS . 'groups' . DS . $this->group->get('cn') . DS . 'File:' . $fileInfo['basename'];
		}

		// do we have a message
		if (isset($upload) && $upload->message)
		{
			$message = $upload->message;
		}

		// return to ckeditor
		echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($ckeditorFunc, '$url', '$message');</script>";
		return;
	}

	/**
	 * Do basic file upload  (ajax upload in another function)
	 *
	 * @return  array
	 */
	public function doUpload()
	{
		Request::checkToken(['get', 'post']);

		// var to hold potential error
		$returnObj          = new \stdClass;
		$returnObj->error   = false;
		$returnObj->message = '';
		$returnObj->file    = null;

		// get config
		$mediaConfig       = Component::params('com_media');
		$allowedExtensions = array_values(array_filter(explode(',', $mediaConfig->get('upload_extensions'))));
		$sizeLimit         = $mediaConfig->get('upload_maxsize');
		$sizeLimit         = $sizeLimit * 1024 * 1024;

		// if super group allow archives
		if ($this->group->get('type') == 3)
		{
			$allowedExtensions[] = 'zip';
			$allowedExtensions[] = 'tar';
			$allowedExtensions[] = 'gz';
		}

		// get request vars
		$file = Request::getVar('upload', '', 'files', 'array');

		// make sure we have file
		if (!$file['name'] || $file['size'] == 0)
		{
			$returnObj->error   = true;
			$returnObj->message = Lang::txt('COM_GROUPS_MEDIA_NO_FILE');
			return $returnObj;
		}

		// file type is ok type
		$fileInfo = pathinfo($file['name']);
		if ($allowedExtensions && !in_array(strtolower($fileInfo['extension']), $allowedExtensions))
		{
			$these = implode(', ', $allowedExtensions);
			$returnObj->error   = true;
			$returnObj->message = Lang::txt('COM_GROUPS_MEDIA_INVALID_FILE', $these);
			return $returnObj;
		}

		// file is under file size limit
		if ($file['size'] > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			$returnObj->error   = true;
			$returnObj->message = Lang::txt('COM_GROUPS_MEDIA_FILE_TOO_BIG', $max);
			return $returnObj;
		}

		// create parent directories
		$groupFolder        = $this->path;
		$groupUploadsFolder = $this->path . DS . 'uploads';
		if (!is_dir($groupFolder))
		{
			if (!Filesystem::makeDirectory($groupFolder))
			{
				$returnObj->error   = true;
				$returnObj->message = Lang::txt('COM_GROUPS_MEDIA_UNABLE_TO_CREATE_UPLOAD_PATH');
				return $returnObj;
			}
		}
		if (!is_dir($groupUploadsFolder))
		{
			if (!Filesystem::makeDirectory($groupUploadsFolder))
			{
				$returnObj->error   = true;
				$returnObj->message = Lang::txt('COM_GROUPS_MEDIA_UNABLE_TO_CREATE_UPLOAD_PATH');
				return $returnObj;
			}
		}

		// clean file name & make unique
		$file['name'] = urldecode($file['name']);
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$fileInfo     = pathinfo($file['name']);
		$filename     = $fileInfo['filename'];
		$ext          = $fileInfo['extension'];

		while (file_exists($groupUploadsFolder . DS . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}

		// final file name
		$finalFileName = $groupUploadsFolder . DS . $filename . '.' . $ext;

		// upload
		if (!Filesystem::upload($file['tmp_name'], $finalFileName))
		{
			$returnObj->error   = true;
			$returnObj->message = Lang::txt('COM_GROUPS_MEDIA_ERROR_UPLOADING');
			return $returnObj;
		}

		// change file perm
		chmod($finalFileName, 0774);

		//scan file for virus if we have enabled scans
		if ($this->config->get('scan_uploads', 1))
		{
			//run scan on file
			//scan failed
			if (!Filesystem::isSafe($finalFileName))
			{
				//delete file
				unlink($finalFileName);

				//inform user
				$returnObj->error   = true;
				$returnObj->message = Lang::txt('COM_GROUPS_MEDIA_FILE_CONTAINS_VIRUS');
				return $returnObj;
			}
		}

		// return our final upload status
		$returnObj->file    = $finalFileName;
		$returnObj->message = Lang::txt('COM_GROUPS_MEDIA_UPLOAD_SUCCESS');
		return $returnObj;
	}

	/**
	 * Streaming file upload
	 * This is used by AJAX
	 *
	 * @return  void
	 */
	public function ajaxuploadTask()
	{
		Request::checkToken(['get', 'post']);

		//get config
		$config = Component::params('com_media');

		//allowed extensions for uplaod
		$allowedExtensions = array_values(array_filter(explode(',', $config->get('upload_extensions'))));

		// if super group allow archives
		if ($this->group->get('type') == 3)
		{
			$allowedExtensions[] = 'zip';
			$allowedExtensions[] = 'tar';
			$allowedExtensions[] = 'gz';
		}

		//max upload size
		$sizeLimit = $config->get('upload_maxsize');
		$sizeLimit = $sizeLimit * 1024 * 1024;

		//get the file
		if (isset($_GET['qqfile']))
		{
			$stream = true;
			$file = $_GET['qqfile'];
			$size = (int) $_SERVER["CONTENT_LENGTH"];
		}
		elseif (isset($_FILES['qqfile']))
		{
			$stream = false;
			$file = $_FILES['qqfile']['name'];
			$size = (int) $_FILES['qqfile']['size'];
		}
		else
		{
			return;
		}

		//get folder
		$folder = Request::getVar('folder', '');

		// make sure we have an active folder
		if ($folder == '')
		{
			$folder = '/uploads';
		}

		// regular groups can only access inside /uploads
		if ($this->group->get('type') != 3)
		{
			$pathInfo = pathinfo($folder);
			if ($pathInfo['dirname'] != '/uploads')
			{
				$folder = '/uploads';
			}
		}

		// Build the upload path if it doesn't exist
		$uploadDirectory = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $this->group->get('gidNumber') . DS . ltrim($folder, DS);

		//make sure upload directory is writable
		if (!is_dir($uploadDirectory))
		{
			if (!Filesystem::makeDirectory($uploadDirectory))
			{
				echo json_encode(array('error' => Lang::txt('COM_GROUPS_MEDIA_UNABLE_TO_CREATE_UPLOAD_PATH')));
				return;
			}
		}
		if (!is_writable($uploadDirectory))
		{
			echo json_encode(array('error' => Lang::txt('COM_GROUPS_MEDIA_PATH_NOT_WRITABLE')));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => Lang::txt('COM_GROUPS_MEDIA_FILE_EMPTY')));
			return;
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => Lang::txt('COM_GROUPS_MEDIA_FILE_TOO_BIG', $max)));
			return;
		}

		//check to make sure we have an allowable extension
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];
		$ext = $pathinfo['extension'];
		if ($allowedExtensions && !in_array(strtolower($ext), $allowedExtensions))
		{
			$these = implode(', ', $allowedExtensions);
			echo json_encode(array('error' => Lang::txt('COM_GROUPS_MEDIA_INVALID_FILE', $these)));
			return;
		}

		// clean file path
		$filename = urldecode($filename);
		$filename = Filesystem::clean($filename);
		$filename = str_replace(' ', '_', $filename);

		while (file_exists($uploadDirectory . DS . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}

		//final file
		$file = $uploadDirectory . DS . $filename . '.' . $ext;

		//save file
		if ($stream)
		{
			//read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			//move from temp location to target location which is user folder
			$target = fopen($file , "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $file);
		}

		// change file perm
		chmod($file, 0774);

		//scan file for virus if we have enabled scans
		if ($this->config->get('scan_uploads', 1))
		{
			//run scan on file
			//scan failed
			if (!Filesystem::isSafe($file))
			{
				//delete file
				unlink($file);

				//inform user
				echo json_encode(array('error' => Lang::txt('COM_GROUPS_MEDIA_FILE_CONTAINS_VIRUS')));
				return;
			}
		}

		//return success
		echo json_encode(array('success'=>true));
		return;
	}

	/**
	 * Display Move group file Form
	 *
	 * @return  array
	 */
	public function moveFileTask()
	{
		// set the neeced layout
		$this->view->setLayout('movefile');

		// get request vars
		$file = Request::getVar('file', '');

		// default folder to have open
		$this->view->activeFolder = '/uploads';
		if ($this->group->get('type') == 3)
		{
			$this->view->activeFolder = '/';
		}

		// get list of folders
		$folders    = Filesystem::directoryTree($this->path, '.', 10);
		$folderTree = $this->_buildFolderTree($folders);
		$folderList = $this->_buildFolderTreeSelect($folderTree);

		// pass vars to view
		$this->view->group      = $this->group;
		$this->view->folderList = $folderList;
		$this->view->file       = $file;

		//render layout
		$this->view->display();
	}

	/**
	 * Move group file
	 *
	 * @return  array
	 */
	public function doMoveFileTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// get request vars
		$file   = Request::getVar('file', '');
		$folder = Request::getVar('folder', '');

		// build source and destination folder
		$source      = $this->path . $file;
		$destination = $this->path . $folder;

		// make sure we have a file
		if (!file_exists($source))
		{
			return App::abort(500, Lang::txt('COM_GROUPS_MEDIA_UNABLE_TO_LOCATE_SOURCE'));
		}

		// make sure we have a destination
		if (!is_dir($destination) || !is_writable($destination))
		{
			return App::abort(500, Lang::txt('COM_GROUPS_MEDIA_DESTINATION_UNAVAILABLE'));
		}

		// add file name to destination for rename
		$bits = explode('/', $source);
		$destination .= DS . array_pop($bits);

		// move file
		Filesystem::move($source, $destination);

		// return folder were moving to
		echo $folder;
		exit();
	}

	/**
	 * Display Rename group file Form
	 *
	 * @return  void
	 */
	public function renameFileTask()
	{
		// set the neeced layout
		$this->view->setLayout('renamefile');

		// get request vars
		$file = Request::getVar('file', '');

		// pass vars to view
		$this->view->group = $this->group;
		$this->view->file  = $file;

		//render layout
		$this->view->display();
	}

	/**
	 * Rename group file
	 *
	 * @return  array
	 */
	public function doRenameFileTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// get request vars
		$file = Request::getVar('file', '');
		$name = Request::getVar('name', '');

		// get parts of original file
		$fileInfo = pathinfo($file);

		// make sure we have a name
		if ($name == '')
		{
			echo $fileInfo['dirname'];
			exit();
		}

		// build new file
		$newFile = $fileInfo['dirname'] . DS . $name;

		// full path source and destination
		$source = $this->path . $file;
		$destination = $this->path . $newFile;

		// make sure file doesnt already exist
		if (file_exists($destination))
		{
			header('HTTP/1.1 500 ' . Lang::txt('COM_GROUPS_MEDIA_NAME_CONFLICT'));
			exit();
		}

		// move file
		Filesystem::move($source, $destination);

		// return folder were moving to
		echo $fileInfo['dirname'];
		exit();
	}

	/**
	 * Extract group Archive
	 *
	 * @return  array
	 */
	public function extractFileTask()
	{
		// Incoming file
		$file = trim(Request::getVar('file', '', 'get'));
		$file = $this->path . $file;

		// get base path
		$fileInfo = pathinfo($file);

		// return folder
		$folder = str_replace($this->path, '', $fileInfo['dirname']);

		// if file exists extract
		if (file_exists($file))
		{
			jimport('joomla.filesystem.archive');
			\JArchive::extract($file, $fileInfo['dirname']);
		}

		// delete garbage folders

		//return folder
		echo $folder;
		exit();
	}

	/**
	 * Delete group file
	 *
	 * @return  array
	 */
	public function deletefileTask()
	{
		Request::checkToken(['get', 'post']);

		// Incoming file
		$file = trim(Request::getVar('file', '', 'get'));
		$file = $this->path . $file;

		// get folder to output to
		$fileInfo = pathinfo($file);
		$folder = str_replace($this->path, '', $fileInfo['dirname']);

		// if file exists delete
		if (file_exists($file))
		{
			Filesystem::delete($file);
		}

		//are we deleting through ckeditor
		$ckeditor     = Request::getVar('CKEditor', '', 'get');
		$ckeditorFunc = Request::getInt('CKEditorFuncNum', 0, 'get');
		$type         = Request::getVar('type', 'image', 'get');

		if (isset($ckeditor) && $ckeditor != '')
		{
			$base = ($_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
			$base .= $_SERVER['HTTP_HOST'];
			$listFilesUrl = $base . DS . 'index.php?option=com_groups&controller=media&task=listfiles&listdir='.$listdir.'&tmpl=component&type='.$type.'&CKEditor='.$ckeditor.'&CKEditorFuncNum='.$ckeditorFunc;

			App::redirect($listFilesUrl);
		}
		else
		{
			echo $folder;
			exit();
		}
	}

	/**
	 * Add group folder
	 *
	 * @return  array
	 */
	public function addFolderTask()
	{
		// get list of folders
		$folders    = Filesystem::directoryTree($this->path, '.', 10);
		$folderTree = $this->_buildFolderTree($folders);
		$folderList = $this->_buildFolderTreeSelect($folderTree);

		// pass vars to view
		$this->view->group      = $this->group;
		$this->view->folderList = $folderList;

		//render layout
		$this->view
			->setLayout('newfolder')
			->display();
	}

	/**
	 * Save group folder
	 *
	 * @return  array
	 */
	public function saveFolderTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		//get request vars
		$name   = Request::getCmd('name', '');
		$folder = Request::getVar('folder', '');

		//create return folder
		$returnFolder = $folder;

		// make sure we have a name
		if ($name == '')
		{
			echo $returnFolder;
			exit();
		}

		// remove slashes and dots
		$name = str_replace('/', '', $name);
		$name = str_replace('.', '', $name);

		// create folder path
		$newFolder = $this->path . rtrim($folder, DS) . DS . $name;

		// make sure new folder doesnt already exist
		if (Filesystem::exists($newFolder))
		{
			header('HTTP/1.1 500 ' . Lang::txt('COM_GROUPS_MEDIA_FOLDER_NAME_CONFLICT'));
			exit();
		}

		// create folder
		Filesystem::makeDirectory($newFolder);

		// output return folder
		echo $returnFolder;
		exit();
	}

	/**
	 * Display group folder rename form
	 *
	 * @return  array
	 */
	public function renameFolderTask()
	{
		// get request vars
		$folder = Request::getVar('folder', '');

		// pass vars to view
		$this->view->group  = $this->group;
		$this->view->folder = $folder;

		//render layout
		$this->view
			->setLayout('renamefolder')
			->display();
	}

	/**
	 * Rename Group Folder
	 *
	 * @return  array
	 */
	public function doRenameFolderTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// get request vars
		$folder = Request::getVar('folder', '');
		$name   = Request::getCmd('name', '');

		//get path info
		$folderInfo = pathinfo($folder);

		//create return folder
		$returnFolder = $folderInfo['dirname'];

		// make sure we have a name
		if ($name == '')
		{
			echo $returnFolder;
			exit();
		}

		// remove slashes and dots
		$name = str_replace('/', '', $name);
		$name = str_replace('.', '', $name);

		// build paths
		$oldFolder = $this->path . $folder;
		$newFolder = $this->path . $returnFolder . DS . $name;

		// make sure old folder exists
		if (!Filesystem::exists($oldFolder))
		{
			echo $returnFolder;
			exit();
		}

		// make sure new folder doesnt already exist
		if (Filesystem::exists($newFolder))
		{
			header('HTTP/1.1 500 ' . Lang::txt('COM_GROUPS_MEDIA_FOLDER_NAME_CONFLICT'));
			exit();
		}

		// rename folder
		Filesystem::move($oldFolder, $newFolder);

		// output return folder
		echo $returnFolder;
		exit();
	}

	/**
	 * Display Move group folder form
	 *
	 * @return  array
	 */
	public function moveFolderTask()
	{
		// get request vars
		$folder = Request::getVar('folder', '');

		// default folder to have open
		//$this->view->activeFolder = '/uploads';
		//if ($this->group->get('type') == 3)
		//{
		//	$this->view->activeFolder = '/';
		//}

		// get list of folders
		$folders    = Filesystem::directoryTree($this->path, '.', 10);
		$folderTree = $this->_buildFolderTree($folders);
		$folderList = $this->_buildFolderTreeSelect($folderTree);

		// pass vars to view
		$this->view->group      = $this->group;
		$this->view->folderList = $folderList;
		$this->view->folder     = $folder;

		//render layout
		$this->view
			->setLayout('movefolder')
			->display();
	}

	/**
	 * Move group folder
	 *
	 * @return  array
	 */
	public function doMoveFolderTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// get request vars
		$current = Request::getVar('current', '');
		$folder  = Request::getVar('folder', '');

		// return path
		$returnFolder = $this->path . $folder;

		// get file info
		$info = pathinfo($current);

		// build paths
		$oldPath = $this->path . $current;
		$newPath = rtrim($this->path . $folder, DS) . DS . $info['basename'];

		// make sure old folder exists
		if (!Filesystem::exists($oldPath))
		{
			echo $returnFolder;
			exit();
		}

		// make sure new folder doesnt already exist
		if (Filesystem::exists($newPath))
		{
			header('HTTP/1.1 500 ' . Lang::txt('COM_GROUPS_MEDIA_FOLDER_NAME_CONFLICT'));
			exit();
		}

		// move folder
		Filesystem::move($oldPath, $newPath);

		// output return folder
		echo $returnFolder;
		exit();
	}

	/**
	 * Delete group folder
	 *
	 * @return  array
	 */
	public function deleteFolderTask()
	{
		Request::checkToken(['get', 'post']);

		//get request vars
		$folder = Request::getVar('folder', '');

		// define where to return to
		$returnFolder = dirname($folder);

		// build path to folder
		$folder = $this->path . $folder;

		//delete folder
		if (is_dir($folder))
		{
			Filesystem::deleteDirectory($folder);
		}

		echo $returnFolder;
		exit();
	}
}
