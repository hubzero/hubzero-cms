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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Site\Controllers;

use Components\Resources\Models\Orm\Resource;
use Components\Resources\Models\Orm\Association;
use Hubzero\Component\SiteController;
use Hubzero\Utility\Validate;
use Hubzero\Utility\String;
use Hubzero\Utility\Number;
use Filesystem;
use Component;
use Request;
use Date;
use Lang;
use User;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'resource.php');

/**
 * Controller class for adding attachments to a parent resource
 */
class Attachments extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			App::abort(403, Lang::txt('COM_RESOURCES_ALERTLOGIN_REQUIRED'));
		}

		parent::execute();
	}

	/**
	 * Create a placeholder attachment for a URL
	 *
	 * @return  void
	 */
	public function createTask()
	{
		// Push through to a different method if
		// being created via AJAX
		if (Request::getVar('no_html', 0))
		{
			return $this->ajaxCreateTask();
		}

		// Ensure we have an ID to work with
		$pid = Request::getInt('pid', 0, 'post');
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_RESOURCES_NO_ID'));
			return $this->displayTask();
		}

		// Create new record
		$resource = Resource::blank()->set(array(
			'title'        => 'A link',
			'introtext'    => 'A link',
			'created'      => Date::toSql(),
			'created_by'   => User::get('id'),
			'published'    => 1,
			'publish_up'   => Date::toSql(),
			'publish_down' => '0000-00-00 00:00:00',
			'standalone'   => 0,
			'access'       => 0,
			'path'         => 'http://', // make sure no path is specified just yet
			'type'         =>  11
		));

		// Save record
		if (!$resource->save())
		{
			$this->setError($resource->getError());
			return $this->displayTask();
		}

		// Create new parent/child association
		if (!$resource->makeChildOf($pid))
		{
			$this->setError($resource->getError());
		}

		// Display attachments list
		$this->displayTask();
	}

	/**
	 * Create a URL attachment via AJAX
	 *
	 * @return  void
	 */
	public function ajaxCreateTask()
	{
		// Ensure we have an ID to work with
		$pid = strtolower(Request::getInt('pid', 0));
		if (!$pid)
		{
			echo json_encode(array('error' => Lang::txt('COM_RESOURCES_NO_ID')));
			return;
		}

		// Create new record
		$resource = Resource::blank()->set(array(
			'title'        => 'A link',
			'introtext'    => 'A link',
			'created'      => Date::toSql(),
			'created_by'   => User::get('id'),
			'published'    => 1,
			'publish_up'   => Date::toSql(),
			'publish_down' => '0000-00-00 00:00:00',
			'standalone'   => 0,
			'access'       => 0,
			'path'         => Request::getVar('url', 'http://'),
			'type'         => 11
		));

		// Clean and validate path
		$resource->path = str_replace(array('|', '\\', '{', '}', '^'), array('%7C', '%5C', '%7B', '%7D', '%5E'), $resource->path);

		if (!Validate::url($resource->path))
		{
			echo json_encode(array(
				'success'   => false,
				'errors'    => array(Lang::txt('Link provided is not a valid URL.')),
				'file'      => $resource->path,
				'directory' => '',
				'parent'    => $pid,
				'id'        => 0
			));
			return;
		}

		// Save record
		if (!$resource->save())
		{
			echo json_encode(array(
				'success'   => false,
				'errors'    => $resource->getErrors(),
				'file'      => 'http://',
				'directory' => '',
				'parent'    => $pid,
				'id'        => 0
			));
			return;
		}

		// Create new parent/child association
		if (!$resource->makeChildOf($pid))
		{
			echo json_encode(array(
				'success'   => false,
				'errors'    => $resource->getErrors(),
				'file'      => $resource->path,
				'directory' => '',
				'parent'    => $pid,
				'id'        => $resource->id
			));
			return;
		}

		// Output results
		echo json_encode(array(
			'success'   => true,
			'errors'    => array(),
			'file'      => $resource->path,
			'directory' => '',
			'parent'    => $pid,
			'id'        => $resource->id
		));
	}

	/**
	 * Upload a file via AJAX
	 *
	 * @return  string
	 */
	public function ajaxUploadTask()
	{
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
		if (isset($_GET['qqfile']) && isset($_SERVER["CONTENT_LENGTH"])) // make sure we actually have a file
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
		$resource = Resource::blank()->set(array(
			'title'        => $filename . '.' . $ext,
			'introtext'    => $filename . '.' . $ext,
			'created'      => Date::toSql(),
			'created_by'   => User::get('id'),
			'published'    => 1,
			'publish_up'   => Date::toSql(),
			'publish_down' => '0000-00-00 00:00:00',
			'standalone'   => 0,
			'access'       => 0,
			'path'         => '', // make sure no path is specified just yet
			'type'         => $this->_getChildType($filename . '.' . $ext)
		));

		// Setup videos to auto-play in hub
		if ($this->config->get('file_video_html5', 1))
		{
			if (in_array($ext, array('mp4', 'webm', 'ogv')))
			{
				$resource->type = 41; // Video type
			}
		}

		// File already exists
		$parent = Resource::oneOrFail($pid);

		if ($parent->hasChild($filename))
		{
			echo json_encode(array(
				'error' => Lang::txt('A file with this name and type appears to already exist.')
			));
			return;
		}

		// Store new content
		if (!$resource->save())
		{
			echo json_encode(array(
				'error' => $resource->getError()
			));
			return;
		}

		// Define upload directory and make sure its writable
		$path = $resource->filespace();

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
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
			// Read the php input stream to upload file
			$input    = fopen("php://input", "r");
			$temp     = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			// Move from temp location to target location which is user folder
			$target = fopen($file , "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $file);
		}

		// Create new parent/child association
		if (!$resource->makeChildOf($pid))
		{
			echo json_encode(array(
				'success'   => false,
				'errors'    => $resource->getErrors(),
				'file'      => $filename . '.' . $ext,
				'directory' => '',
				'parent'    => $pid
			));
			return;
		}

		// Virus scan
		if (!Filesystem::isSafe($file))
		{
			if (Filesystem::delete($file))
			{
				// Delete resource
				$resource->destroy();
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

		// Set the path
		if (!$resource->get('path'))
		{
			$resource->set('path', $resource->relativepath() . DS . $filename . '.' . $ext);
		}
		$resource->set('path', ltrim($resource->get('path'), DS));
		$resource->save();

		// Textifier
		$this->textifier($file, $resource->get('id'));

		// Output results
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
	 * @return  void
	 */
	public function reorderTask()
	{
		// Incoming
		$id   = Request::getInt('id', 0);
		$pid  = Request::getInt('pid', 0);
		$move = Request::getVar('move', 'down');

		// Ensure we have an ID to work with
		if (!$id)
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_CHILD_ID'));
			return $this->displayTask($pid);
		}

		// Ensure we have a parent ID to work with
		if (!$pid)
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_ID'));
			return $this->displayTask($pid);
		}

		switch ($move)
		{
			case 'up':
				$move = -1;
			break;

			case 'down':
				$move = 1;
			break;
		}

		// Move the record
		$association = Association::oneByRelationship($pid, $id);

		if (!$association->move($move))
		{
			$this->setError($association->getError());
		}

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Rename an attachment
	 *
	 * @return  void
	 */
	public function renameTask()
	{
		// Incoming
		$id   = Request::getInt('id', 0);
		$name = Request::getVar('name', '');

		// Ensure we have everything we need
		if ($id && $name)
		{
			$resource = Resource::oneOrFail($id);
			$resource->set('title', (string)$name);
			$resource->save();
		}

		// Echo the name
		echo $name;
	}

	/**
	 * Save an attachment
	 *
	 * @return  void
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
			return $this->displayTask($pid);
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_FILE'));
			return $this->displayTask($pid);
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);

		// Ensure file names fit.
		$ext = Filesystem::extension($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		if (strlen($file['name']) > 230)
		{
			$file['name']  = substr($file['name'], 0, 230);
			$file['name'] .= '.' . $ext;
		}

		// Instantiate a new resource object
		$resource = Resource::blank()->set(array(
			'title'        => $file['name'],
			'introtext'    => $file['name'],
			'created'      => Date::toSql(),
			'created_by'   => User::get('id'),
			'published'    => 1,
			'publish_up'   => Date::toSql(),
			'publish_down' => '0000-00-00 00:00:00',
			'standalone'   => 0,
			'access'       => 0,
			'path'         => '', // make sure no path is specified just yet
			'type'         => $this->_getChildType($file['name'])
		));

		// File already exists
		$parent = Resource::oneOrFail($pid);

		if ($parent->hasChild($file['name']))
		{
			$this->setError(Lang::txt('A file with this name and type appears to already exist.'));
			return $this->displayTask($pid);
		}

		// Store new content
		if (!$resource->save())
		{
			$this->setError($resource->getError());
			return $this->displayTask($pid);
		}

		// Build the path
		$listdir = $this->_buildPathFromDate($resource->get('created'), $resource->get('id'), '');
		$path    = $this->_buildUploadPath($listdir, '');

		// Make sure the upload path exist
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_CONTRIBUTE_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return $this->displayTask($pid);
			}
		}

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_CONTRIBUTE_ERROR_UPLOADING'));
		}
		else
		{
			// File was uploaded
			// Check the file type
			$resource->set('type', $this->_getChildType($file['name']));
		}

		// Scan for viruses
		$fpath = $path . DS . $file['name'];

		if (!Filesystem::isSafe($fpath))
		{
			if (Filesystem::delete($fpath))
			{
				// Delete resource
				$resource->destroy();
			}

			$this->setError(Lang::txt('File rejected because the anti-virus scan failed.'));
			return $this->displayTask($pid);
		}

		// Set path value
		//
		// NOTE: This is relative to the base resources upload path
		if (!$resource->get('path'))
		{
			$resource->set('path', $listdir . DS . $file['name']);
		}
		$resource->set('path', ltrim($resource->get('path'), DS));

		// Store new content
		if (!$resource->save())
		{
			$this->setError($resource->getError());
			return $this->displayTask($pid);
		}

		// Create new parent/child association
		if (!$resource->makeChildOf($pid))
		{
			$this->setError($resource->getError());
			return $this->displayTask($pid);
		}

		// Textifier
		$this->textifier($fpath, $resource->get('id'));

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Textifier
	 *
	 * This scans files, such as PDFs, for
	 * search results.
	 *
	 * @param   string   $path
	 * @param   integer  $id
	 * @return  void
	 */
	protected function textifier($path, $id)
	{
		if (is_readable($path))
		{
			$hash = @sha1_file($path);

			if (!empty($hash))
			{
				$this->database->setQuery("SELECT id FROM `#__document_text_data` WHERE hash = " . $this->database->quote($hash));
				$doc_id = $this->database->loadResult();

				if (!$doc_id)
				{
					$this->database->execute("INSERT INTO `#__document_text_data` (hash) VALUES (" . $this->database->quote($hash) . ")");
					$doc_id = $this->database->insertId();
				}

				$this->database->execute("INSERT IGNORE INTO `#__document_resource_rel` (document_id, resource_id) VALUES (" . (int)$doc_id . ", " . (int)$id . ")");

				system('/usr/bin/textifier ' . escapeshellarg($path) . ' >/dev/null');
			}
		}
	}

	/**
	 * Delete a file
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Incoming parent ID
		$pid = Request::getInt('pid', 0);
		if (!$pid)
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_ID'));
			return $this->displayTask($pid);
		}

		// Incoming child ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_CHILD_ID'));
			return $this->displayTask($pid);
		}

		// Load resource info
		$resource = Resource::oneOrFail($id);

		// Check for stored file
		if ($resource->get('path') != '')
		{
			$listdir = $resource->get('path');
		}
		else
		{
			// No stored path, derive from created date
			$listdir = $resource->relativepath();
		}

		// Build the path
		$path = $resource->basepath() . DS . $listdir;

		// Check if the path is a URL or exists
		if (!file_exists($path) or !$path or substr($resource->get('path'), 0, strlen('http')) == 'http')
		{
			//$this->setError(Lang::txt('COM_CONTRIBUTE_FILE_NOT_FOUND'));
		}
		else
		{
			if ($path == $resource->basepath()
			 || $path == $resource->relativepath())
			{
				$this->setError(Lang::txt('Invalid file path.'));
			}
			else
			{
				// Attempt to delete the file
				if (!Filesystem::delete($path))
				{
					$this->setError(Lang::txt('COM_CONTRIBUTE_UNABLE_TO_DELETE_FILE'));
				}
			}
		}

		if (!$this->getError())
		{
			// Delete resource
			$resource->destroy();
		}

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Change access on an entry
	 *
	 * @return  void
	 */
	public function accessTask()
	{
		// Incoming parent ID
		$pid = Request::getInt('pid', 0);
		if (!$pid)
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_ID'));
			return $this->displayTask($pid);
		}

		// Incoming child ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_CHILD_ID'));
			return $this->displayTask($pid);
		}

		// Load resource info
		$resource = Resource::oneOrFail($id);

		// Set value
		$access = Request::getInt('access', 0);
		if (!in_array($access, array(0, 1)))
		{
			$access = 0;
		}

		$resource->set('access', $access);

		// Store new content
		if (!$resource->save())
		{
			$this->setError($resource->getError());
		}

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Display a list of attachments
	 *
	 * @param   integer  $id  Resource ID
	 * @return  void
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
			App::abort(404, Lang::txt('CONTRIBUTE_NO_ID'));
		}

		// Initiate a resource
		$resource = Resource::oneOrFail($id);

		$children = $resource->children()->ordered()->rows();

		// Output HTML
		$this->view
			->set('config', $this->config)
			->set('children', $children)
			->set('path', '')
			->set('id', $id)
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
	}

	/**
	 * Build the absolute path to a resource's file upload
	 *
	 * @param   string  $listdir  Primary upload directory
	 * @param   string  $subdir   Sub directory of $listdir
	 * @return  string
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
	 * @param   string  $filename  File name
	 * @return  integer
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
	 * @param   string   $date  Resource created date
	 * @param   integer  $id    Resource ID
	 * @param   string   $base  Base path to prepend
	 * @return  string
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
