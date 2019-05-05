<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Resources\Models\Entry;
use Components\Resources\Models\Screenshot;
use Document;
use Pathway;
use Component;
use Request;
use Route;
use Lang;
use User;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'helper.php';
include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'tool.php';
include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php';
include_once Component::path('com_resources') . DS . 'models' . DS . 'entry.php';

/**
 * Controller class for contributing a tool
 */
class Screenshots extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->_authorize();

		// Load the com_resources component config
		$rconfig = Component::params('com_resources');
		$this->rconfig = $rconfig;

		$this->registerTask('order', 'reorder');

		parent::execute();
	}

	/**
	 * Reorder screenshots
	 *
	 * @return  void
	 */
	public function reorderTask()
	{
		// Incoming parent ID
		$pid = Request::getInt('pid', 0);
		$version = Request::getString('version', 'dev');

		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return $this->displayTask($pid, $version);
		}

		// get tool object
		$obj = new \Components\Tools\Tables\Tool($this->database);
		$this->_toolid = $obj->getToolIdFromResource($pid);

		// make sure user is authorized to go further
		if (!$this->_checkAccess($this->_toolid))
		{
			App::abort(403, Lang::txt('COM_TOOLS_ALERTNOTAUTH'));
		}

		// Get version id
		$objV = new \Components\Tools\Tables\Version($this->database);
		$vid = $objV->getVersionIdFromResource($pid, $version);

		if ($vid == null)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_VERSION_ID_NOT_FOUND'));
			return $this->displayTask($pid, $version);
		}

		// Incoming
		$file_toleft   = Request::getString('fl', '');
		$order_toleft  = Request::getInt('ol', 1);
		$file_toright  = Request::getString('fr', '');
		$order_toright = Request::getInt('or', 0);

		$neworder_toleft  = ($order_toleft != 0) ? $order_toleft - 1 : 0;
		$neworder_toright = $order_toright + 1;

		// Instantiate a new screenshot object
		$shot1 = Screenshot::oneByFilename($file_toright, $pid, $vid);
		$shot2 = Screenshot::oneByFilename($file_toleft, $pid, $vid);

		// Do we have information stored?
		if (!$shot1 || !$shot1->get('id'))
		{
			$shot1->set('filename', $file_toright);
			$shot1->set('resourceid', $pid);
			$shot1->set('versionid', $vid);
		}
		$shot1->set('ordering', $neworder_toright);
		$shot1->save();

		if (!$shot2 || !$shot2->get('id'))
		{
			$shot2->set('filename', $file_toleft);
			$shot2->set('resourceid', $pid);
			$shot2->set('versionid', $vid);
		}
		$shot2->set('ordering', $neworder_toleft);
		$shot2->save();

		$this->_rid = $pid;

		// Push through to the screenshot view
		$this->displayTask($pid, $version);
	}

	/**
	 * Edit a screenshot
	 *
	 * @return  void
	 */
	public function editTask()
	{
		// Incoming parent ID
		$pid = Request::getInt('pid', 0);
		$version = Request::getString('version', 'dev');
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return $this->displayTask($pid, $version);
		}

		// Incoming child ID
		$this->view->file = Request::getString('filename', '');
		if (!$this->view->file)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_CHILD_ID'));
			return $this->displayTask($pid, $version);
		}

		// Load resource info
		$row = Entry::oneOrFail($pid);

		// Get version id
		$objV = new \Components\Tools\Tables\Version($this->database);
		$vid = $objV->getVersionIdFromResource($pid, $version);

		if ($vid == null)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_VERSION_ID_NOT_FOUND'));
			return $this->displayTask($pid, $version);
		}

		// Build the path
		$listdir  = $row->relativepath() . DS . $vid;
		$this->view->wpath = DS . trim($this->rconfig->get('uploadpath'), DS) . DS . $listdir;

		// Make sure wpath is preceded by app
		if (substr($this->view->wpath, 0, 4) != DS . 'app')
		{
			$this->view->wpath = DS . 'app' . $this->view->wpath;
		}

		$this->view->upath = PATH_ROOT . $this->view->wpath;

		// Instantiate a new screenshot object
		$shot = Screenshot::oneByFilename($this->view->file, $pid, $vid);

		// Set the page title
		$this->view->title = Lang::txt(strtoupper($this->_name)) . ': ' . Lang::txt('COM_TOOLS_TASK_EDIT_SS');
		Document::setTitle($this->view->title);

		// Output HTML
		$this->view
			->set('shot', $shot)
			->set('pid', $pid)
			->set('version', $version)
			->set('vid', $vid)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Save changes to a screenshot
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Incoming parent ID
		$pid     = Request::getInt('pid', 0);
		$version = Request::getString('version', 'dev');
		$vid     = Request::getInt('vid', 0);
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return $this->displayTask($pid, $version);
		}

		// Incoming
		$file = Request::getString('filename', '');
		$title = preg_replace('/\s+/', ' ', Request::getString('title', ''));

		// Instantiate a new screenshot object
		$shot = Screenshot::oneByFilename($file, $pid, $vid);

		if (!$shot)
		{
			// make new entry
			$shot->set('versionid', $vid);
			$shot->set('filename', $file);
			$shot->set('resourceid', $pid);
		}

		$shot->set('title', preg_replace('/"((.)*?)"/i', "&#147;\\1&#148;", $title));

		if (!$shot->save())
		{
			$this->setError($shot->getError());
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
		$version = Request::getString('version', 'dev');
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		// Incoming child ID
		$file = Request::getString('filename', '');
		if (!$file)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_CHILD_ID'));
			$this->displayTask($pid, $version);
			return;
		}

		// Load resource info
		$row = Entry::oneOrFail($pid);

		// Get version id
		$objV = new \Components\Tools\Tables\Version($this->database);
		$vid = $objV->getVersionIdFromResource($pid, $version);

		if ($vid == null)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_VERSION_ID_NOT_FOUND'));
			return $this->displayTask($pid, $version);
		}

		// Build the path
		$listdir  = $row->relativepath();
		$listdir .= DS.$vid;
		$path = $this->_buildUploadPath($listdir, '');

		// Check if the folder even exists
		if (!is_dir($path) or !$path)
		{
			$this->setError(Lang::txt('COM_TOOLS_DIRECTORY_NOT_FOUND'));
			return $this->displayTask($pid, $version);
		}
		else
		{
			if (!Filesystem::exists($path . DS . $file))
			{
				return $this->displayTask($pid, $version);
			}

			if (!Filesystem::delete($path . DS . $file))
			{
				$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_DELETE_FILE'));
				return $this->displayTask($pid, $version);
			}
			else
			{
				// Delete thumbnail
				$tn = preg_replace('#\.[^.]*$#', '', $file) . '-tn.gif';
				Filesystem::delete($path . DS . $tn);

				// Instantiate a new screenshot object
				$ss = Screenshot::all()
					->whereEquals('filename', $file)
					->whereEquals('resourceid', $pid)
					->whereEquals('versionid', $vid)
					->row();
				$ss->destroy();
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
			return $this->displayTask($pid, $version);
		}

		$version = Request::getString('version', 'dev');
		$title = preg_replace('/\s+/', ' ', Request::getString('title', ''));
		$allowed = array('.gif', '.jpg', '.png', '.bmp');
		$changing_version = Request::getInt('changing_version', 0);
		if ($changing_version)
		{
			// reload screen
			return $this->displayTask($pid, $version);
		}

		// Get resource information
		$resource = Entry::oneOrFail($pid);

		// Incoming file
		$file = Request::getArray('upload', '', 'files');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_FILE'));
			return $this->displayTask($pid, $version);
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
			return $this->displayTask($pid, $version);
		}

		// Get version id
		$objV = new \Components\Tools\Tables\Version($this->database);
		$vid = $objV->getVersionIdFromResource($pid, $version);

		if ($vid == null)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_VERSION_ID_NOT_FOUND'));
			return $this->displayTask($pid, $version);
		}

		// Instantiate a new screenshot object
		$row = Screenshot::blank();

		// Check if file with the same name already exists
		$files = $resource->screenshots()
			->whereEquals('versionid', $vid)
			->ordered()
			->rows()
			->fieldsByKey('filename');
		if (count($files) > 0)
		{
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

		$row->set('title', preg_replace('/"((.)*?)"/i', "&#147;\\1&#148;", $title));
		$row->set('versionid', $vid);
		$row->set('filename', $file['name']);
		$row->set('resourceid', $pid);

		// Build the path
		$listdir  = $resource->relativepath();
		$listdir .= DS . $vid;
		$path = $this->_buildUploadPath($listdir, '');

		// Make sure the upload path exist
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_CREATE_UPLOAD_PATH') . $path);
				return $this->displayTask($pid, $version);
			}
		}

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_UPLOADING'));
		}
		else
		{
			// Store new content
			if (!$row->save())
			{
				$this->setError($row->getError());
				return $this->displayTask($pid, $version);
			}

			// Create thumbnail
			$ss_height = (intval($this->config->get('screenshot_maxheight', 58)) > 30) ? intval($this->config->get('screenshot_maxheight', 58)) : 58;
			$ss_width  = (intval($this->config->get('screenshot_maxwidth', 91)) > 80)  ? intval($this->config->get('screenshot_maxwidth', 91))  : 91;

			$tn = preg_replace('#\.[^.]*$#', '', $file['name']) . '-tn.gif';
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
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->displayTask($pid, $version);
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
		$imorig = imagecreatefromjpeg(dirname(__DIR__) . DS . 'assets' . DS . 'img' . DS . 'anim.jpg');
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
			if (imagegif($im, $save_dir . $save_name))
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
			case '1':
				$imorig = imagecreatefromgif($tmpname);
				break;
			case '2':
				$imorig = imagecreatefromjpeg($tmpname);
				break;
			case '3':
				$imorig = imagecreatefrompng($tmpname);
				break;
			case '4':
				$imorig = imagecreatefromwbmp($tmpname);
				break;
			default:
				$imorig = imagecreatefromjpeg($tmpname);
				break;
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
			if (imagegif($im, $save_dir . $save_name))
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
		$version = Request::getString('version', 'dev');
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
	 * @return  void
	 */
	public function moveTask()
	{
		$from    = Request::getInt('from', 0);
		$to      = Request::getInt('to', 0);
		$rid     = Request::getInt('rid', 0);
		$version = Request::getString('version', 'dev');

		// get admin priviliges
		$this->_authorize();

		if ($this->config->get('access-admin-component') or $this->_task == 'copy')
		{
			if ($from == 0 or $to == 0 or $rid == 0)
			{
				App::abort(500, Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
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
	 * @param   string   $sourceid  Source version ID
	 * @param   string   $destid    Destination version ID
	 * @param   integer  $rid       Resource ID
	 * @return  boolean  False if errors, True on success
	 */
	public function transfer($sourceid, $destid, $rid)
	{
		Log::debug(__FUNCTION__ . '()');

		// Get resource information
		$resource = Entry::oneOrFail($rid);

		// Build the path
		$listdir = $resource->relativepath();
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
				$shots = $resource->screenshots()
					->whereEquals('versionid', $sourceid)
					->ordered()
					->rows();

				foreach ($shots as $shot)
				{
					$shot->set('id', null);
					$shot->set('versionid', $destid);
					$shot->save();
				}

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
	public function displayTask($rid=null, $version=null)
	{
		$this->view->setLayout('display');

		// Incoming
		if (!$rid)
		{
			$rid = Request::getInt('rid', 0);
		}
		if (!$version)
		{
			$version = Request::getString('version', 'dev');
		}

		// Ensure we have an ID to work with
		if (!$rid)
		{
			App::abort(500, Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return;
		}
		// Get resource information
		$resource = Entry::oneOrFail($rid);

		// Get version id
		$objV = new \Components\Tools\Tables\Version($this->database);
		$vid = $objV->getVersionIdFromResource($rid, $version);

		// Do we have a published tool?
		$this->view->published = $objV->getCurrentVersionProperty($resource->alias, 'id');

		// Get screenshot information for this resource
		$this->view->shots = $resource->screenshots()
			->whereEquals('versionid', $vid)
			->ordered()
			->rows();

		// Build paths
		$path = $resource->relativepath();
		$this->view->upath = PATH_APP . DS . trim($this->rconfig->get('uploadpath'), DS) . $path;
		$this->view->wpath = DS . trim($this->rconfig->get('uploadpath'), DS) . $path;
		if ($vid)
		{
			$this->view->upath .= DS . $vid;
			$this->view->wpath .= DS . $vid;
		}

		// Make sure wpath is preceded by app
		if (substr($this->view->wpath, 0, 4) != DS . 'app')
		{
			$this->view->wpath = DS . 'app' . $this->view->wpath;
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
