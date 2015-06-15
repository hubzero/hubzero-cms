<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Resources\Site\Controllers;

use Components\Resources\Tables\Resource;
use Components\Resources\Tables\Assoc;
use Components\Resources\Helpers\Helper;
use Hubzero\Component\SiteController;
use Hubzero\Utility\Validate;
use Hubzero\Utility\String;
use Hubzero\Utility\Number;
use Component;
use Request;
use Date;
use Lang;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'resource.php');
include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'assoc.php');
include_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'helper.php');

/**
 * Controller class for contributing a tool
 */
class Attachments extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return     void
	 */
	public function execute()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			App::abort(403, Lang::txt('You must be logged in to access.'));
			return;
		}

		parent::execute();
	}

	/**
	 * Upload a file to the wiki
	 *
	 * @return     void
	 */
	public function createTask()
	{
		if (Request::getVar('no_html', 0))
		{
			return $this->ajaxCreateTask();
		}

		// Check if they're logged in
		if (User::isGuest())
		{
			$this->displayTask();
			return;
		}

		// Ensure we have an ID to work with
		$pid = Request::getInt('pid', 0, 'post');
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_NO_ID'));
			$this->displayTask();
			return;
		}

		// Create database entry
		$asset = new Resource($this->database);
		$asset->title        = 'A link';
		$asset->introtext    = $row->title;
		$asset->created      = Date::toSql();
		$asset->created_by   = User::get('id');
		$asset->published    = 1;
		$asset->publish_up   = Date::toSql();
		$asset->publish_down = '0000-00-00 00:00:00';
		$asset->standalone   = 0;
		$asset->path         = 'http://'; // make sure no path is specified just yet
		$asset->type         = 11;
		if (!$asset->check())
		{
			$this->setError($asset->getError());
			$this->displayTask();
			return;
		}
		if (!$asset->store())
		{
			$this->setError($asset->getError());
			$this->displayTask();
			return;
		}

		// Instantiate a Resources Assoc object
		$assoc = new Assoc($this->database);

		// Get the last child in the ordering
		$assoc->ordering = $assoc->getLastOrder($pid);
		$assoc->ordering = ($assoc->ordering) ? $assoc->ordering : 0;

		// Increase the ordering - new items are always last
		$assoc->ordering++;

		// Create new parent/child association
		$assoc->parent_id = $pid;
		$assoc->child_id  = $asset->id;
		$assoc->grouping  = 0;
		if (!$assoc->check())
		{
			$this->setError($assoc->getError());
		}
		if (!$assoc->store(true))
		{
			$this->setError($assoc->getError());
		}

		$this->displayTask();
	}

	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return     string
	 */
	public function ajaxCreateTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			echo json_encode(array('error' => Lang::txt('Must be logged in.')));
			return;
		}

		// Ensure we have an ID to work with
		$pid = strtolower(Request::getInt('pid', 0));
		if (!$pid)
		{
			echo json_encode(array('error' => Lang::txt('COM_RESOURCES_NO_ID')));
			return;
		}

		// Create database entry
		$asset = new Resource($this->database);
		$asset->title        = 'A link';
		$asset->introtext    = $asset->title;
		$asset->created      = Date::toSql();
		$asset->created_by   = User::get('id');
		$asset->published    = 1;
		$asset->publish_up   = Date::toSql();
		$asset->publish_down = '0000-00-00 00:00:00';
		$asset->standalone   = 0;
		$asset->access       = 0;
		$asset->path         = Request::getVar('url', 'http://');
		$asset->type         = 11;

		$asset->path = str_replace(array('|', '\\', '{', '}', '^'), array('%7C', '%5C', '%7B', '%7D', '%5E'), $asset->path);
		if (!Validate::url($asset->path))
		{
			echo json_encode(array(
				'success'   => false,
				'errors'    => array(Lang::txt('Link provided is not a valid URL.')),
				'file'      => $asset->path,
				'directory' => '',
				'parent'    => $pid,
				'id'        => 0
			));
			return;
		}
		if (!$asset->check())
		{
			echo json_encode(array(
				'success'   => false,
				'errors'    => $asset->getErrors(),
				'file'      => $asset->path,
				'directory' => '',
				'parent'    => $pid,
				'id'        => 0
			));
			return;
		}
		if (!$asset->store())
		{
			echo json_encode(array(
				'success'   => false,
				'errors'    => $asset->getErrors(),
				'file'      => 'http://',
				'directory' => '',
				'parent'    => $pid,
				'id'        => 0
			));
			return;
		}

		// Instantiate a Resources Assoc object
		$assoc = new Assoc($this->database);

		// Get the last child in the ordering
		$assoc->ordering = $assoc->getLastOrder($pid);
		$assoc->ordering = ($assoc->ordering) ? $assoc->ordering : 0;

		// Increase the ordering - new items are always last
		$assoc->ordering++;

		// Create new parent/child association
		$assoc->parent_id = $pid;
		$assoc->child_id  = $asset->id;
		$assoc->grouping  = 0;
		if (!$assoc->check())
		{
			echo json_encode(array(
				'success'   => false,
				'errors'    => $assoc->getErrors(),
				'file'      => $asset->path,
				'directory' => '',
				'parent'    => $pid,
				'id'        => $asset->id
			));
			return;
		}
		if (!$assoc->store(true))
		{
			echo json_encode(array(
				'success'   => false,
				'errors'    => $assoc->getErrors(),
				'file'      => $asset->path,
				'directory' => '',
				'parent'    => $pid,
				'id'        => $asset->id
			));
			return;
		}

		//echo result
		echo json_encode(array(
			'success'   => true,
			'errors'    => array(),
			'file'      => $asset->path,
			'directory' => '',
			'parent'    => $pid,
			'id'        => $asset->id
		));
	}

	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return     string
	 */
	public function ajaxUploadTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			echo json_encode(array('error' => Lang::txt('Must be logged in.')));
			return;
		}

		// Ensure we have an ID to work with
		$pid = strtolower(Request::getInt('pid', 0));
		if (!$pid)
		{
			echo json_encode(array('error' => Lang::txt('COM_RESOURCES_NO_ID')));
			return;
		}

		//max upload size
		$sizeLimit = $this->config->get('maxAllowed', 40000000);

		// get the file
		if (isset($_GET['qqfile']))
		{
			$stream = true;
			$file = $_GET['qqfile'];
			$size = (int) $_SERVER["CONTENT_LENGTH"];
		}
		elseif (isset($_FILES['qqfile']))
		{
			//$files = Request::getVar('qqfile', '', 'files', 'array');

			$stream = false;
			$file = $_FILES['qqfile']['name'];
			$size = (int) $_FILES['qqfile']['size'];
		}
		else
		{
			echo json_encode(array('error' => Lang::txt('File not found')));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array(
				'error' => Lang::txt('File is empty')
			));
			return;
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', Number::formatBytes($sizeLimit));
			echo json_encode(array(
				'error' => Lang::txt('File is too large. Max file upload size is %s', $max)
			));
			return;
		}

		// don't overwrite previous files that were uploaded
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];

		// Make the filename safe
		$filename = urldecode($filename);
		$filename = \Filesystem::clean($filename);
		$filename = str_replace(' ', '_', $filename);

		$ext = $pathinfo['extension'];
		/*while (file_exists($path . DS . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}*/

		// Instantiate a new resource object
		$row = new Resource($this->database);
		$row->title        = $filename . '.' . $ext;
		$row->introtext    = $row->title;
		$row->created      = Date::toSql();
		$row->created_by   = User::get('id');
		$row->published    = 1;
		$row->publish_up   = Date::toSql();
		$row->publish_down = '0000-00-00 00:00:00';
		$row->standalone   = 0;
		$row->access       = 0;
		$row->path         = ''; // make sure no path is specified just yet
		$row->type         = $this->_getChildType($filename . '.' . $ext);

		// setup videos to auto-play in hub
		if ($this->config->get('file_video_html5', 1))
		{
			if (in_array($ext, array('mp4', 'webm', 'ogv')))
			{
				$row->type = 41; // Video type
			}
		}

		// Check content
		if (!$row->check())
		{
			echo json_encode(array(
				'error' => $row->getError()
			));
			return;
		}

		// File already exists
		if ($row->loadByFile($filename, $pid))
		{
			echo json_encode(array(
				'error' => Lang::txt('A file with this name and type appears to already exist.')
			));
			return;
		}

		// Store new content
		if (!$row->store())
		{
			echo json_encode(array(
				'error' => $row->getError()
			));
			return;
		}

		if (!$row->id)
		{
			$row->id = $row->insertid();
		}

		//define upload directory and make sure its writable
		$listdir = $this->_buildPathFromDate($row->created, $row->id, '');
		$path = $this->_buildUploadPath($listdir, '');
		if (!is_dir($path))
		{
			if (!\Filesystem::makeDirectory($path))
			{
				echo json_encode(array(
					'error' => Lang::txt('Error uploading. Unable to create path.')
				));
				return;
			}
		}

		if (!is_writable($path))
		{
			echo json_encode(array(
				'error' => Lang::txt('Server error. Upload directory isn\'t writable.')
			));
			return;
		}

		$file = $path . DS . $filename . '.' . $ext;

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

		$assoc = new Assoc($this->database);

		// Get the last child in the ordering
		$assoc->ordering = $assoc->getLastOrder($pid);
		$assoc->ordering = ($assoc->ordering) ? $assoc->ordering : 0;

		// Increase the ordering - new items are always last
		$assoc->ordering++;

		// Create new parent/child association
		$assoc->parent_id = $pid;
		$assoc->child_id  = $row->id;
		$assoc->grouping  = 0;
		if (!$assoc->check())
		{
			echo json_encode(array(
				'success'   => false,
				'errors'    => $assoc->getErrors(),
				'file'      => $filename . '.' . $ext,
				'directory' => '',
				'parent'    => $pid
			));
			return;
		}
		if (!$assoc->store(true))
		{
			echo json_encode(array(
				'success'   => false,
				'errors'    => $assoc->getErrors(),
				'file'      => $filename . '.' . $ext,
				'directory' => '',
				'parent'    => $pid
			));
			return;
		}

		if (!\Filesystem::isSafe($file))
		{
			if (\Filesystem::delete($file))
			{
				// Delete associations to the resource
				$row->deleteExistence();

				// Delete resource
				$row->delete();
			}

			$this->setError(Lang::txt('File rejected because the anti-virus scan failed.'));

			echo json_encode(array(
				'success'   => false,
				'errors'    => $this->getErrors(),
				'file'      => $filename . '.' . $ext,
				'directory' => str_replace(PATH_APP, '', $path),
				'parent'    => $pid
			));
			return;
		}

		if (!$row->path)
		{
			$row->path = $listdir . DS . $filename . '.' . $ext;
		}
		$row->path = ltrim($row->path, DS);
		$row->store();

		if (is_readable($file))
		{
			$hash = @sha1_file($file);

			if (!empty($hash))
			{
				$this->database->setQuery('SELECT id FROM `#__document_text_data` WHERE hash = \'' . $hash . '\'');
				if (!($doc_id = $this->database->loadResult()))
				{
					$this->database->execute('INSERT INTO `#__document_text_data` (hash) VALUES (\'' . $hash . '\')');
					$doc_id = $this->database->insertId();
				}

				$this->database->execute('INSERT IGNORE INTO `#__document_resource_rel` (document_id, resource_id) VALUES (' . (int)$doc_id . ', ' . (int)$row->id . ')');
				system('/usr/bin/textifier ' . escapeshellarg($file) . ' >/dev/null');
			}
		}

		echo json_encode(array(
			'success'   => true,
			'errors'    => $this->getErrors(),
			'file'      => $filename . '.' . $ext,
			'directory' => str_replace(PATH_APP, '', $path),
			'parent'    => $pid
		));
	}

	/**
	 * Reorder an attachment
	 *
	 * @return     void
	 */
	public function reorderTask()
	{
		// Incoming
		$id   = Request::getInt('id', 0);
		$pid  = Request::getInt('pid', 0);
		$move = 'order' . Request::getVar('move', 'down');

		// Ensure we have an ID to work with
		if (!$id)
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_CHILD_ID'));
			$this->displayTask($pid);
			return;
		}

		// Ensure we have a parent ID to work with
		if (!$pid)
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_ID'));
			$this->displayTask($pid);
			return;
		}

		// Get the element moving down - item 1
		$resource1 = new Assoc($this->database);
		$resource1->loadAssoc($pid, $id);

		// Get the element directly after it in ordering - item 2
		$resource2 = clone($resource1);
		$resource2->getNeighbor($move);

		switch ($move)
		{
			case 'orderup':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $resource2->ordering;
				$orderdn = $resource1->ordering;

				$resource1->ordering = $orderup;
				$resource2->ordering = $orderdn;
			break;

			case 'orderdown':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $resource1->ordering;
				$orderdn = $resource2->ordering;

				$resource1->ordering = $orderdn;
				$resource2->ordering = $orderup;
			break;
		}

		// Save changes
		$resource1->store();
		$resource2->store();

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Rename an attachment
	 *
	 * @return     string
	 */
	public function renameTask()
	{
		// Incoming
		$id   = Request::getInt('id', 0);
		$name = Request::getVar('name', '');

		// Ensure we have everything we need
		if ($id && $name != '')
		{
			$r = new Resource($this->database);
			$r->load($id);
			$r->title = $name;
			$r->store();
		}

		// Echo the name
		echo $name;
	}

	/**
	 * Save an attachment
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		if (Request::getVar('no_html', 0))
		{
			return $this->ajaxUploadTask();
		}

		// Incoming
		$pid = Request::getInt('pid', 0);
		if (!$pid)
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_ID'));
			$this->displayTask($pid);
			return;
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_FILE'));
			$this->displayTask($pid);
			return;
		}

		// Make the filename safe
		$file['name'] = \Filesystem::clean($file['name']);
		// Ensure file names fit.
		$ext = \Filesystem::extension($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		if (strlen($file['name']) > 230)
		{
			$file['name'] = substr($file['name'], 0, 230);
			$file['name'] .= '.' . $ext;
		}

		// Instantiate a new resource object
		$row = new Resource($this->database);
		if (!$row->bind($_POST))
		{
			$this->setError($row->getError());
			$this->displayTask($pid);
			return;
		}
		$row->title        = ($row->title) ? $row->title : $file['name'];
		$row->introtext    = $row->title;
		$row->created      = Date::toSql();
		$row->created_by   = User::get('id');
		$row->published    = 1;
		$row->publish_up   = Date::toSql();
		$row->publish_down = '0000-00-00 00:00:00';
		$row->standalone   = 0;
		$row->path         = ''; // make sure no path is specified just yet

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			$this->displayTask($pid);
			return;
		}

		// File already exists
		if ($row->loadByFile($file['name'], $pid))
		{
			$this->setError(Lang::txt('A file with this name and type appears to already exist.'));
			$this->displayTask($pid);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			$this->displayTask($pid);
			return;
		}

		if (!$row->id)
		{
			$row->id = $row->insertid();
		}

		// Build the path
		$listdir = $this->_buildPathFromDate($row->created, $row->id, '');
		$path = $this->_buildUploadPath($listdir, '');

		// Make sure the upload path exist
		if (!is_dir($path))
		{
			if (!\Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_CONTRIBUTE_UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->displayTask($pid);
				return;
			}
		}

		// Perform the upload
		if (!\Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_CONTRIBUTE_ERROR_UPLOADING'));
		}
		else
		{
			// File was uploaded
			// Check the file type
			$row->type = $this->_getChildType($file['name']);

			// If it's a package (ZIP, etc) ...
			/*
			Breeze presentations haven't been used for some time.
			Completely unnecessary code?
			if ($row->type == 38)
			{
				require_once(PATH_CORE . DS . 'includes' . DS . 'pcl' . DS . 'pclzip.lib.php');

				if (!extension_loaded('zlib'))
				{
					$this->setError(Lang::txt('COM_CONTRIBUTE_ZLIB_PACKAGE_REQUIRED'));
				}
				else
				{
					// Check the table of contents and look for a Breeze viewer.swf file
					$isbreeze = 0;

					$zip = new PclZip($path . DS . $file['name']);

					$file_to_unzip = preg_replace('/(.+)\..*$/', '$1', $path . DS . $file['name']);

					if (($list = $zip->listContent()) == 0)
					{
						die('Error: '.$zip->errorInfo(true));
					}

					for ($i=0; $i<sizeof($list); $i++)
					{
						if (substr($list[$i]['filename'], strlen($list[$i]['filename']) - 10, strlen($list[$i]['filename'])) == 'viewer.swf')
						{
							$isbreeze = $list[$i]['filename'];
							break;
						}
						//$this->setError(substr($list[$i]['filename'], strlen($list[$i]['filename']), -4).' '.substr($file['name'], strlen($file['name']), -4));
					}
					if (!$isbreeze)
					{
						for ($i=0; $i<sizeof($list); $i++)
						{
							if (strtolower(substr($list[$i]['filename'], -3)) == 'swf'
							 && substr($list[$i]['filename'], strlen($list[$i]['filename']), -4) == substr($file['name'], strlen($file['name']), -4))
							{
								$isbreeze = $list[$i]['filename'];
								break;
							}
							//$this->setError(substr($list[$i]['filename'], strlen($list[$i]['filename']), -4).' '.substr($file['name'], strlen($file['name']), -4));
						}
					}

					// It IS a breeze presentation
					if ($isbreeze)
					{
						// unzip the file
						$do = $zip->extract($path);
						if (!$do)
						{
							$this->setError(Lang::txt('COM_CONTRIBUTE_UNABLE_TO_EXTRACT_PACKAGE'));
						}
						else
						{
							$row->path = $listdir . DS . $isbreeze;

							@unlink($path . DS . $file['name']);
						}
						$row->type = $this->_getChildType($row->path);
						$row->title = $isbreeze;
					}
				}
			}*/
		}

		// Scan for viruses
		$fpath = $path . DS . $file['name'];

		if (!\Filesystem::isSafe($fpath))
		{
			if (\Filesystem::delete($fpath))
			{
				// Delete associations to the resource
				$row->deleteExistence();

				// Delete resource
				$row->delete();
			}

			$this->setError(Lang::txt('File rejected because the anti-virus scan failed.'));
			$this->displayTask($pid);
			return;
		}

		if (!$row->path)
		{
			$row->path = $listdir . DS . $file['name'];
		}
		$row->path = ltrim($row->path, DS);

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			$this->displayTask($pid);
			return;
		}

		// Instantiate a Resources Assoc object
		$assoc = new Assoc($this->database);

		// Get the last child in the ordering
		$assoc->ordering = $assoc->getLastOrder($pid);
		$assoc->ordering = ($assoc->ordering) ? $assoc->ordering : 0;

		// Increase the ordering - new items are always last
		$assoc->ordering++;

		// Create new parent/child association
		$assoc->parent_id = $pid;
		$assoc->child_id  = $row->id;
		$assoc->grouping  = 0;
		if (!$assoc->check())
		{
			$this->setError($assoc->getError());
		}
		if (!$assoc->store(true))
		{
			$this->setError($assoc->getError());
		}
		else
		{
			if (is_readable($path . DS . $file['name']))
			{
				$hash = @sha1_file($path . DS . $file['name']);

				if (!empty($hash))
				{
					$this->database->setQuery('SELECT id FROM `#__document_text_data` WHERE hash = \'' . $hash . '\'');
					if (!($doc_id = $this->database->loadResult()))
					{
						$this->database->execute('INSERT INTO `#__document_text_data` (hash) VALUES (\'' . $hash . '\')');
						$doc_id = $this->database->insertId();
					}

					$this->database->execute('INSERT IGNORE INTO `#__document_resource_rel` (document_id, resource_id) VALUES (' . (int)$doc_id . ', ' . (int)$row->id . ')');
					system('/usr/bin/textifier ' . escapeshellarg($path . DS . $file['name']) . ' >/dev/null');
				}
			}
		}

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Delete a file
	 *
	 * @return     void
	 */
	public function deleteTask()
	{
		// Incoming parent ID
		$pid = Request::getInt('pid', 0);
		if (!$pid)
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_ID'));
			$this->displayTask($pid);
			return;
		}

		// Incoming child ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_CHILD_ID'));
			$this->displayTask($pid);
			return;
		}

		// Load resource info
		$row = new Resource($this->database);
		$row->load($id);

		// Check for stored file
		if ($row->path != '')
		{
			$listdir = $row->path;
		}
		else
		{
			// No stored path, derive from created date
			$listdir = $this->_buildPathFromDate($row->created, $id, '');
		}

		// Build the path
		$path = $this->_buildUploadPath($listdir, '');

		$base  = PATH_APP . '/' . trim($this->config->get('webpath', '/site/resources'), '/');
		$baseY = $base . '/'. Date::of($row->created)->format("Y");
		$baseM = $baseY . '/' . Date::of($row->created)->format("m");

		// Check if the folder even exists
		if (!file_exists($path) or !$path or substr($row->path, 0, strlen('http')) == 'http')
		{
			//$this->setError(Lang::txt('COM_CONTRIBUTE_FILE_NOT_FOUND'));
		}
		else
		{
			if ($path == $base
			 || $path == $baseY
			 || $path == $baseM)
			{
				$this->setError(Lang::txt('Invalid directory.'));
			}
			else
			{
				// Attempt to delete the folder
				if (!\Filesystem::delete($path))
				{
					$this->setError(Lang::txt('COM_CONTRIBUTE_UNABLE_TO_DELETE_FILE'));
				}
			}
		}

		if (!$this->getError())
		{
			/*
			WTF? What is all this for? -- zooley 04/01/2014

			$uploadPath = DS . trim($this->config->get('uploadpath', '/site/resources'), DS);

			$year  = substr(trim($row->created), 0, 4);
			$month = substr(trim($row->created), 5, 2);

			$file  = basename($path);
			$path  = substr($path, 0, (strlen($path) - strlen($file)));
			$path  = str_replace(PATH_APP, '', $path);
			$path  = str_replace($uploadPath, '', $path);

			$bits  = explode('/', $path);
			$p = array();
			$b = '';
			$g = array_pop($bits);
			foreach ($bits as $bit)
			{
				if ($bit == '/' || $bit == $year || $bit == $month || $bit == \Hubzero\Utility\String::pad($id))
				{
					$b .= ($bit != DS) ? DS . $bit : '';
				}
				else if ($bit != DS)
				{
					$p[] = $bit;
				}
			}

			if (count($p) > 1)
			{
				$p = array_reverse($p);
				foreach ($p as $v)
				{
					if (!trim($v))
					{
						continue;
					}

					$npath = PATH_APP . $uploadPath . $b . DS . $v;

					// Check if the folder even exists
					if (!is_dir($npath)
					 or !$npath
					 or rtrim($npath, '/') == $base
					 or rtrim($npath, '/') == $baseY
					 or rtrim($npath, '/') == $baseM)
					{
						$this->setError(Lang::txt('COM_CONTRIBUTE_DIRECTORY_NOT_FOUND'));
					}
					else
					{
						// Attempt to delete the folder
						if (!Filesystem::deleteDirectory($npath))
						{
							$this->setError(Lang::txt('COM_CONTRIBUTE_UNABLE_TO_DELETE_DIRECTORY'));
						}
					}
				}
			}
			*/

			// Delete associations to the resource
			$row->deleteExistence();

			// Delete resource
			$row->delete();
		}

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Display a list of attachments
	 *
	 * @param      integer $id Resource ID
	 * @return     void
	 */
	public function displayTask($id=null)
	{
		// Incoming
		if (!$id)
		{
			$id = Request::getInt('id', 0);
		}

		// Ensure we have an ID to work with
		if (!$id)
		{
			throw new Exception(Lang::txt('CONTRIBUTE_NO_ID'), 500);
		}

		// Initiate a resource helper class
		$helper = new Helper($id, $this->database);
		$helper->getChildren();

		// get config
		$this->view->config   = $this->config;
		$this->view->children = $helper->children;
		$this->view->path     = '';
		$this->view->id       = $id;

		// Set errors to view
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output HTML
		$this->view
			->setLayout('display')
			->display();
	}

	/**
	 * Build the absolute path to a resource's file upload
	 *
	 * @param      string $listdir Primary upload directory
	 * @param      string $subdir  Sub directory of $listdir
	 * @return     string
	 */
	private function _buildUploadPath($listdir, $subdir='')
	{
		if ($subdir)
		{
			$subdir = DS . trim($subdir, DS);
		}

		// Get the configured upload path
		$base = DS . trim($this->config->get('uploadpath', '/site/resources'), DS);

		// Make sure the path doesn't end with a slash
		$listdir = DS . trim($listdir, DS);

		// Does the beginning of the $listdir match the config path?
		if (substr($listdir, 0, strlen($base)) == $base)
		{
			// Yes - ... this really shouldn't happen
		}
		else
		{
			// No - append it
			$listdir = $base . $listdir;
		}

		// Build the path
		return PATH_APP . $listdir . $subdir;
	}

	/**
	 * Get the child's type ID based on file extension
	 *
	 * @param      string $filename File name
	 * @return     integer
	 */
	private function _getChildType($filename)
	{
		$ftype = strtolower(\Filesystem::extension($filename));

		switch ($ftype)
		{
			case 'mov': $type = 15; break;
			case 'swf': $type = 32; break;
			case 'ppt': $type = 35; break;
			case 'asf': $type = 37; break;
			case 'asx': $type = 37; break;
			case 'wmv': $type = 37; break;
			case 'zip': $type = 38; break;
			case 'tar': $type = 38; break;
			case 'pdf': $type = 33; break;
			default:    $type = 13; break;
		}

		return $type;
	}

	/**
	 * Build a path from a creation date (0000-00-00 00:00:00)
	 *
	 * @param      string  $date Resource created date
	 * @param      integer $id   Resource ID
	 * @param      string  $base Base path to prepend
	 * @return     string
	 */
	private function _buildPathFromDate($date, $id, $base='')
	{
		if ($date && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs))
		{
			$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		if ($date)
		{
			$dir_year  = Date::of($date)->format('Y');
			$dir_month = Date::of($date)->format('m');
		}
		else
		{
			$dir_year  = Date::of('now')->format('Y');
			$dir_month = Date::of('now')->format('m');
		}
		$dir_id = String::pad($id);

		$path = $base . DS . $dir_year . DS . $dir_month . DS . $dir_id;

		return $path;
	}
}
