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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Document;
use Pathway;
use Component;
use Request;
use Route;
use Lang;
use User;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'helper.php');
include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'tool.php');
include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php');
include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'screenshot.php');

/**
 * Controller class for contributing a tool
 */
class Screenshots extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->_authorize();

		// Load the com_resources component config
		$rconfig = Component::params('com_resources');
		$this->rconfig = $rconfig;

		parent::execute();
	}

	/**
	 * Reorder screenshots
	 *
	 * @return     void
	 */
	public function reorderTask()
	{
		// Incoming parent ID
		$pid = Request::getInt('pid', 0);
		$version = Request::getVar('version', 'dev');

		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		// get tool object
		$obj = new \Components\Tools\Tables\Tool($this->database);
		$this->_toolid = $obj->getToolIdFromResource($pid);

		// make sure user is authorized to go further
		if (!$this->check_access($this->_toolid))
		{
			App::abort(403, Lang::txt('COM_TOOLS_ALERTNOTAUTH'));
			return;
		}

		// Get version id
		$objV = new \Components\Tools\Tables\Version($this->database);
		$vid = $objV->getVersionIdFromResource($pid, $version);

		if ($vid == NULL)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_VERSION_ID_NOT_FOUND'));
			$this->displayTask($pid, $version);
			return;
		}

		// Incoming
		$file_toleft = Request::getVar('fl', '');
		$order_toleft = Request::getInt('ol', 1);
		$file_toright = Request::getVar('fr', '');
		$order_toright = Request::getInt('or', 0);

		$neworder_toleft = ($order_toleft != 0) ? $order_toleft - 1 : 0;
		$neworder_toright = $order_toright + 1;

		// Instantiate a new screenshot object
		$ss = new \Components\Resources\Tables\Screenshot($this->database);
		$shot1 = $ss->getScreenshot($file_toright, $pid, $vid);
		$shot2 = $ss->getScreenshot($file_toleft, $pid, $vid);

		// Do we have information stored?
		if ($shot1)
		{
			$ss->saveScreenshot($file_toright, $pid, $vid, $neworder_toright);
		}
		else
		{
			$ss->saveScreenshot($file_toright, $pid, $vid, $neworder_toright, true);
		}
		if ($shot1)
		{
			$ss->saveScreenshot($file_toleft, $pid, $vid, $neworder_toleft);
		}
		else
		{
			$ss->saveScreenshot($file_toleft, $pid, $vid, $neworder_toleft, true);
		}

		$this->_rid = $pid;

		// Push through to the screenshot view
		$this->displayTask($pid, $version);
	}

	/**
	 * Edit a screenshot
	 *
	 * @return     void
	 */
	public function editTask()
	{
		// Incoming parent ID
		$pid = Request::getInt('pid', 0);
		$version = Request::getVar('version', 'dev');
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		// Incoming child ID
		$this->view->file = Request::getVar('filename', '');
		if (!$this->view->file)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_CHILD_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		// Load resource info
		$row = new \Components\Resources\Tables\Resource($this->database);
		$row->load($pid);

		// Get version id
		$objV = new \Components\Tools\Tables\Version($this->database);
		$vid = $objV->getVersionIdFromResource($pid, $version);

		if ($vid == NULL)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_VERSION_ID_NOT_FOUND'));
			$this->displayTask($pid, $version);
			return;
		}

		// Build the path
		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');
		$listdir  = \Components\Resources\Helpers\Html::build_path($row->created, $pid, '') . DS . $vid;
		$this->view->wpath = DS . trim($this->rconfig->get('uploadpath'), DS) . DS . $listdir;
		$this->view->upath = $this->_buildUploadPath($listdir, '');

		// Instantiate a new screenshot object
		$ss = new \Components\Resources\Tables\Screenshot($this->database);
		$this->view->shot = $ss->getScreenshot($this->view->file, $pid, $vid);

		// Set the page title
		$this->view->title = Lang::txt(strtoupper($this->_name)) . ': ' . Lang::txt('COM_TOOLS_TASK_EDIT_SS');
		Document::setTitle($this->view->title);

		$this->view->pid = $pid;
		$this->view->version = $version;
		$this->view->vid = $vid;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Save changes to a screenshot
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Incoming parent ID
		$pid     = Request::getInt('pid', 0);
		$version = Request::getVar('version', 'dev');
		$vid     = Request::getInt('vid', 0);
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		// Incoming
		$file = Request::getVar('filename', '');
		$title = preg_replace('/\s+/', ' ', Request::getVar('title', ''));

		// Instantiate a new screenshot object
		$ss = new \Components\Resources\Tables\Screenshot($this->database);
		$shot  = $ss->getScreenshot($file, $pid, $vid);
		$files = $ss->getFiles($pid, $vid);

		if ($shot)
		{
			// update entry
			$ss->loadFromFilename($file, $pid, $vid);
		}
		else
		{
			// make new entry
			$ss->versionid = $vid;
			$ordering = $ss->getLastOrdering($pid, $vid);
			$ss->ordering = ($ordering) ? $ordering + 1 : count($files) + 1; // put in the end
			$ss->filename = $file;
			$ss->resourceid = $pid;
		}
		$ss->title = preg_replace('/"((.)*?)"/i', "&#147;\\1&#148;", $title);

		if (!$ss->store())
		{
			$this->setError($ss->getError());
		}

		// Push through to the screenshot view
		$this->displayTask($pid, $version);
	}

	/**
	 * Delete a screenshot
	 *
	 * @return     void
	 */
	public function deleteTask()
	{
		// Incoming parent ID
		$pid = Request::getInt('pid', 0);
		$version = Request::getVar('version', 'dev');
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		// Incoming child ID
		$file = Request::getVar('filename', '');
		if (!$file)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_CHILD_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		// Load resource info
		$row = new \Components\Resources\Tables\Resource($this->database);
		$row->load($pid);

		// Get version id
		$objV = new \Components\Tools\Tables\Version($this->database);
		$vid = $objV->getVersionIdFromResource($pid, $version);

		if ($vid == NULL)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_VERSION_ID_NOT_FOUND'));
			$this->displayTask($pid, $version);
			return;
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');

		// Build the path
		$listdir  = \Components\Resources\Helpers\Html::build_path($row->created, $pid, '');
		$listdir .= DS.$vid;
		$path = $this->_buildUploadPath($listdir, '');

		// Check if the folder even exists
		if (!is_dir($path) or !$path)
		{
			$this->setError(Lang::txt('COM_TOOLS_DIRECTORY_NOT_FOUND'));
			$this->displayTask($pid, $version);
			return;
		}
		else
		{
			if (!Filesystem::exists($path . DS . $file))
			{
				$this->displayTask($pid, $version);
				return;
			}

			if (!Filesystem::delete($path . DS . $file))
			{
				$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_DELETE_FILE'));
				$this->displayTask($pid, $version);
				return;
			}
			else
			{
				// Delete thumbnail
				$tn = \Components\Resources\Helpers\Html::thumbnail($file);
				Filesystem::delete($path . DS . $tn);

				// Instantiate a new screenshot object
				$ss = new \Components\Resources\Tables\Screenshot($this->database);
				$ss->deleteScreenshot($file, $pid, $vid);
			}
		}

		$this->_rid = $pid;

		// Push through to the screenshot view
		$this->displayTask($pid, $version);
	}

	/**
	 * Upload a screenshot
	 *
	 * @return     void
	 */
	public function uploadTask()
	{
		// Incoming
		$pid = Request::getInt('pid', 0);
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		$version = Request::getVar('version', 'dev');
		$title = preg_replace('/\s+/', ' ',Request::getVar('title', ''));
		$allowed = array('.gif','.jpg','.png','.bmp');
		$changing_version = Request::getInt('changing_version', 0);
		if ($changing_version)
		{
			// reload screen
			$this->displayTask($pid, $version);
			return;
		}

		// Get resource information
		$resource = new \Components\Resources\Tables\Resource($this->database);
		$resource->load($pid);

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_FILE'));
			$this->displayTask($pid, $version);
			return;
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$file['name'] = str_replace('-tn', '', $file['name']);
		$file_basename = substr($file['name'], 0, strripos($file['name'], '.')); // strip extention
		$file_ext      = substr($file['name'], strripos($file['name'], '.'));

		// Make sure we have an allowed format
		if (!in_array(strtolower($file_ext), $allowed))
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_WRONG_FILE_FORMAT'));
			$this->displayTask($pid, $version);
			return;
		}

		// Get version id
		$objV = new \Components\Tools\Tables\Version($this->database);
		$vid = $objV->getVersionIdFromResource($pid, $version);

		if ($vid == NULL)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_VERSION_ID_NOT_FOUND'));
			$this->displayTask($pid, $version);
			return;
		}

		// Instantiate a new screenshot object
		$row = new \Components\Resources\Tables\Screenshot($this->database);

		// Check if file with the same name already exists
		$files = $row->getFiles($pid, $vid);
		if (count($files) > 0)
		{
			$files = \Components\Tools\Helpers\Utils::transform($files, 'filename');
			foreach ($files as $f)
			{
				if ($f == $file['name'])
				{
					// append extra characters in the end
					$file['name'] = $file_basename . '_' . time() . $file_ext;
					$file_basename = $file_basename . '_' . time();
				}
			}
		}

		$row->title = preg_replace('/"((.)*?)"/i', "&#147;\\1&#148;", $title);
		$row->versionid = $vid;
		$ordering = $row->getLastOrdering($pid, $vid);
		$row->ordering = ($ordering) ? $ordering + 1 : count($files) + 1; // put in the end
		$row->filename = $file['name'];
		$row->resourceid = $pid;

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			$this->displayTask($pid, $version);
			return;
		}

		// Build the path
		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');
		$listdir  = \Components\Resources\Helpers\Html::build_path($resource->created, $pid, '');
		$listdir .= DS . $vid;
		$path = $this->_buildUploadPath($listdir, '');

		// Make sure the upload path exist
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_CREATE_UPLOAD_PATH') . $path);
				$this->displayTask($pid, $version);
				return;
			}
		}

		// Perform the upload
		if (!\Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_UPLOADING'));
		}
		else
		{
			// Store new content
			if (!$row->store())
			{
				$this->setError($row->getError());
				$this->displayTask($pid, $version);
				return;
			}

			if (!$row->id)
			{
				$row->id = $row->insertid();
			}

			// Create thumbnail
			$ss_height = (intval($this->config->get('screenshot_maxheight', 58)) > 30) ? intval($this->config->get('screenshot_maxheight', 58)) : 58;
			$ss_width  = (intval($this->config->get('screenshot_maxwidth', 91)) > 80)  ? intval($this->config->get('screenshot_maxwidth', 91))  : 91;

			$tn = \Components\Resources\Helpers\Html::thumbnail($file['name']);
			if ($file_ext != '.swf')
			{
				$this->_createThumb($path . DS . $file['name'], $ss_width, $ss_height, $path, $tn);
			}
			else
			{
				//$this->_createAnimThumb($path . DS . $file['name'], $ss_width, $ss_height, $path, $tn);
			}
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			$this->displayTask($pid, $version);
			return;
		}

		$this->_rid = $pid;

		// Push through to the screenshot view
		$this->displayTask($pid, $version);
	}

	/**
	 * Create a thumbnail for an animation
	 *
	 * @param      string  $tmpname   Uploaded file name
	 * @param      integer $maxwidth  Max image width
	 * @param      integer $maxheight Max image height
	 * @param      string  $save_dir  Directory to save to
	 * @param      string  $save_name Name to save file as
	 * @return     boolean False if errors, True if successful
	 */
	private function _createAnimThumb($tmpname, $maxwidth, $maxheight, $save_dir, $save_name)
	{
		$imorig = imagecreatefromjpeg(PATH_CORE . DS . 'components' . DS . $this->_option . DS . 'assets' . DS . 'img' . DS . 'anim.jpg');
		$x = imageSX($imorig);
		$y = imageSY($imorig);

		$yc = $y*1.555555;
		$d  = $x>$yc ? $x : $yc;
		$c  = $d>$maxwidth ? $maxwidth/$d : $maxwidth;
		$av = $x*$c;
		$ah = $y*$c;

		$im = imagecreate($av, $ah);
		$im = imagecreatetruecolor($av, $ah);
		if (imagecopyresampled($im, $imorig, 0, 0, 0, 0, $av, $ah, $x, $y))
		{
			if (imagegif ($im, $save_dir . $save_name))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * Create a thumbnail from a picture
	 *
	 * @param      string  $tmpname   Uploaded file name
	 * @param      integer $maxwidth  Max image width
	 * @param      integer $maxheight Max image height
	 * @param      string  $save_dir  Directory to save to
	 * @param      string  $save_name Name to save file as
	 * @return     boolean False if errors, True if successful
	 */
	private function _createThumb($tmpname, $maxwidth, $maxheight, $save_dir, $save_name)
	{
		$save_dir = rtrim($save_dir, DS) . DS;
		$gis = getimagesize($tmpname);

		switch ($gis[2])
		{
			case '1': $imorig = imagecreatefromgif ($tmpname);  break;
			case '2': $imorig = imagecreatefromjpeg($tmpname); break;
			case '3': $imorig = imagecreatefrompng($tmpname);  break;
			case '4': $imorig = imagecreatefromwbmp($tmpname); break;
			default:  $imorig = imagecreatefromjpeg($tmpname); break;
		}

		$x = imageSX($imorig);
		$y = imageSY($imorig);
		if ($gis[0] <= $maxwidth)
		{
			$av = $x;
			$ah = $y;
		}
		else
		{
			$yc = $y*1.555555;
			$d  = $x>$yc ? $x : $yc;
			$c  = $d>$maxwidth ? $maxwidth/$d : $maxwidth;
			$av = $x*$c;
			$ah = $y*$c;
		}

		$im = imagecreate($av, $ah);
		$im = imagecreatetruecolor($av, $ah);
		if (imagecopyresampled($im, $imorig, 0, 0, 0, 0, $av, $ah, $x, $y))
		{
			if (imagegif ($im, $save_dir . $save_name))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * Copy files
	 *
	 * @return     void
	 */
	public function copyTask()
	{
		$version = Request::getVar('version', 'dev');
		$rid     = Request::getInt('rid', 0);
		$from    = $version == 'dev' ? 'current' : 'dev';

		// get admin priviliges
		$this->_authorize();

		// Get version id
		$objV = new \Components\Tools\Tables\Version($this->database);
		$to   = $objV->getVersionIdFromResource($rid, $version);
		$from = $objV->getVersionIdFromResource($rid, $from);

		// get tool id
		$obj = new \Components\Tools\Tables\Tool($this->database);
		$toolid = $obj->getToolIdFromResource($rid);

		if ($from == 0 or $to == 0 or $rid == 0)
		{
			App::abort(500, Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return;
		}

		if ($toolid && $this->_checkAccess($toolid))
		{
			if ($this->transfer($from, $to, $rid))
			{
				// Push through to the screenshot view
				$this->displayTask($rid, $version);
			}
		}
	}

	/**
	 * Move files
	 *
	 * @return     void
	 */
	public function moveTask()
	{
		$from    = Request::getInt('from', 0);
		$to      = Request::getInt('to', 0);
		$rid     = Request::getInt('rid', 0);
		$version = Request::getVar('version', 'dev');

		// get admin priviliges
		$this->_authorize();

		if ($this->config->get('access-admin-component') or $this->_task == 'copy')
		{
			if ($from == 0 or $to == 0 or $rid == 0)
			{
				App::abort(500, Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
				return;
			}

			if ($this->transfer($from, $to, $rid))
			{
				if ($this->_task == 'copy')
				{
					$this->_rid = $rid;
					// Push through to the screenshot view
					$this->displayTask($rid, $version);
				}
				else
				{
					echo Lang::txt('COM_TOOLS_SUCCESS');
				}
			}
			else if ($this->_task != 'copy')
			{
				$this->setError(Lang::txt('COM_TOOLS_ERROR_COPYING_FILES'));
			}
		}
		else
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
		}
	}

	/**
	 * Transfer files from one version to another
	 *
	 * @param      string  $sourceid Source version ID
	 * @param      string  $destid   Destination version ID
	 * @param      integer $rid      Resource ID
	 * @return     boolean False if errors, True on success
	 */
	public function transfer($sourceid, $destid, $rid)
	{
		Log::debug(__FUNCTION__ . '()');

		// Get resource information
		$resource = new \Components\Resources\Tables\Resource($this->database);
		$resource->load($rid);

		// Get screenshot information
		$ss = new \Components\Resources\Tables\Screenshot($this->database);
		$shots = $ss->getFiles($rid, $sourceid);

		// Build the path
		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');

		$listdir = \Components\Resources\Helpers\Html::build_path($resource->created, $rid, '');
		$srcdir  = $listdir . DS . $sourceid;
		$destdir = $listdir . DS . $destid;
		$src     = $this->_buildUploadPath($srcdir, '');
		$dest    = $this->_buildUploadPath($destdir, '');

		// Make sure the path exist
		if (!is_dir($src))
		{
			if (!Filesystem::makeDirectory($src))
			{
				$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return false;
			}
		}
		Log::debug(__FUNCTION__ . "() $src");

		// do we have files to transfer?
		$files = Filesystem::files($src, '.', false, true, array());
		Log::debug(__FUNCTION__ . "() " . implode(',', $files));
		if (!empty($files))
		{
			// Copy directory
			Log::debug(__FUNCTION__ . "() copying $src to $dest");
			if (!Filesystem::copyDirectory($src, $dest, '', true))
			{
				return false;
			}
			else
			{
				// Update screenshot information for this resource
				$ss->updateFiles($rid, $sourceid, $destid, $copy=1);

				Log::debug(__FUNCTION__ . '() updated files');
				return true;
			}
		}

		Log::debug(__FUNCTION__ . '() done');

		return true;
	}

	/**
	 * Display a list of screenshots for this entry
	 *
	 * @param      integer $rid     Resource ID
	 * @param      string  $version Tool version
	 * @return     void
	 */
	public function displayTask($rid=NULL, $version=NULL)
	{
		$this->view->setLayout('display');

		// Incoming
		if (!$rid)
		{
			$rid = Request::getInt('rid', 0);
		}
		if (!$version)
		{
			$version = Request::getVar('version', 'dev');
		}

		// Ensure we have an ID to work with
		if (!$rid)
		{
			App::abort(500, Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return;
		}
		// Get resource information
		$resource = new \Components\Resources\Tables\Resource($this->database);
		$resource->load($rid);

		// Get version id
		$objV = new \Components\Tools\Tables\Version($this->database);
		$vid = $objV->getVersionIdFromResource($rid, $version);

		// Do we have a published tool?
		$this->view->published = $objV->getCurrentVersionProperty($resource->alias, 'id');

		// Get screenshot information for this resource
		$ss = new \Components\Resources\Tables\Screenshot($this->database);
		$this->view->shots = $ss->getScreenshots($rid, $vid);

		// Build paths
		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');

		$path = \Components\Resources\Helpers\Html::build_path($resource->created, $rid, '');
		$this->view->upath = PATH_CORE . DS . trim($this->rconfig->get('uploadpath'), DS) . $path;
		$this->view->wpath = DS . trim($this->rconfig->get('uploadpath'), DS) . $path;
		if ($vid)
		{
			$this->view->upath .= DS . $vid;
			$this->view->wpath .= DS . $vid;
		}

		// get config
		$this->view->cparams = Component::params('com_resources');
		$this->view->version = $version;
		$this->view->rid = $rid;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Construct a path to upload files to
	 *
	 * @param      string $listdir Base directory
	 * @param      string $subdir  Sub-directory
	 * @return     string
	 */
	private function _buildUploadPath($listdir, $subdir='')
	{
		if ($subdir)
		{
			$subdir = DS . trim($subdir, DS);
		}

		// Get the configured upload path
		$rconfig = Component::params('com_resources');

		$base_path = $rconfig->get('uploadpath');
		if ($base_path)
		{
			// Make sure the path doesn't end with a slash
			$base_path = DS . trim($base_path, DS);
		}

		// Make sure the path doesn't end with a slash
		$listdir = DS . trim($listdir, DS);

		// Does the beginning of the $listdir match the config path?
		if (substr($listdir, 0, strlen($base_path)) == $base_path)
		{
			// Yes - ... this really shouldn't happen
		}
		else
		{
			// No - append it
			$listdir = $base_path . $listdir;
		}

		// Build the path
		return PATH_APP . $listdir . $subdir;
	}

	/**
	 * Get the resource child type from the file extension
	 *
	 * @param      string $filename File to check
	 * @return     integer Numerical file type
	 */
	private function _getChildType($filename)
	{
		$ftype = strtolower(Filesystem::extension($filename));

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
	 * Check if the current user has access to this tool
	 *
	 * @param      unknown $toolid       Tool ID
	 * @param      integer $allowAdmins  Allow admins access?
	 * @param      boolean $allowAuthors Allow authors access?
	 * @return     boolean True if they have access
	 */
	private function _checkAccess($toolid, $allowAdmins=1, $allowAuthors=false)
	{
		// Create a Tool object
		$obj = new \Components\Tools\Tables\Tool($this->database);

		// allow to view if admin
		if ($this->config->get('access-manage-component') && $allowAdmins)
		{
			return true;
		}

		// check if user in tool dev team
		if ($developers = $obj->getToolDevelopers($toolid))
		{
			foreach ($developers as $dv)
			{
				if ($dv->uidNumber == User::get('id'))
				{
					return true;
				}
			}
		}

		// allow access to tool authors
		if ($allowAuthors)
		{
			// Nothing here?
		}

		return false;
	}

	/**
	 * Authorization checks
	 *
	 * @param      string $assetType Asset type
	 * @param      string $assetId   Asset id to check against
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (User::isGuest())
		{
			return;
		}

		// if no admin group is defined, allow superadmin to act as admin
		// otherwise superadmins can only act if they are also a member of the component admin group
		if (($admingroup = trim($this->config->get('admingroup', ''))))
		{
			// Check if they're a member of admin group
			$ugs = \Hubzero\User\Helper::getGroups(User::get('id'));
			if ($ugs && count($ugs) > 0)
			{
				$admingroup = strtolower($admingroup);
				foreach ($ugs as $ug)
				{
					if (strtolower($ug->cn) == $admingroup)
					{
						$this->config->set('access-manage-' . $assetType, true);
						$this->config->set('access-admin-' . $assetType, true);
						$this->config->set('access-create-' . $assetType, true);
						$this->config->set('access-delete-' . $assetType, true);
						$this->config->set('access-edit-' . $assetType, true);
					}
				}
			}
		}
		else
		{
			$asset  = $this->_option;
			if ($assetId)
			{
				$asset .= ($assetType != 'component') ? '.' . $assetType : '';
				$asset .= ($assetId) ? '.' . $assetId : '';
			}

			$at = '';
			if ($assetType != 'component')
			{
				$at .= '.' . $assetType;
			}

			// Admin
			$this->config->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
			// Permissions
			$this->config->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
		}
	}
}
